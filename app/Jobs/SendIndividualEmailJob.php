<?php

namespace App\Jobs;

use App\Mail\IndividualMail;
use App\Models\CampaignRecipient;
use App\Models\EmailAccount;
use App\Models\EmailContact;
use App\Models\EmailKeyword;
use App\Models\OneTimeSender;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendIndividualEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailAccount;

    protected $recipients;

    protected $subject;

    protected $body;

    protected $isBulk;

    protected $campaignId;

    // Retry / throttle settings (parity with SendEmailJob).
    // Without these the worker-level --tries applies and every
    // transient SMTP 550 blasts through retries -> MaxAttemptsExceeded.
    public $tries = 3;

    public $maxExceptions = 3;

    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    public $timeout = 300; // bulk batches need longer than single sends

    /**
     * Throttle SMTP usage so the remote server never sees a burst.
     * 30 jobs/min per account, excess released back after 60s
     * instead of failing. Requires cache driver (file/database ok).
     */
    public function middleware(): array
    {
        $key = 'smtp-account-'.($this->emailAccount->id ?? 'default');

        return [(new RateLimited($key))->allow(30)->everyMinute()->releaseAfter(60)];
    }

    /**
     * Stop retrying after ~2h even if backoff keeps releasing.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(2)->toDateTime();
    }

    /**
     * Create a new job instance.
     */
    public function __construct(EmailAccount $emailAccount, $recipients, $subject, $body, $isBulk = false, $campaignId = null)
    {
        $this->emailAccount = $emailAccount;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->body = $body;
        $this->isBulk = $isBulk;
        $this->campaignId = $campaignId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Configure mail settings dynamically
            config([
                'mail.mailers.smtp.host' => $this->emailAccount->smtp_host,
                'mail.mailers.smtp.port' => $this->emailAccount->smtp_port,
                'mail.mailers.smtp.username' => $this->emailAccount->smtp_username ?: $this->emailAccount->email,
                'mail.mailers.smtp.password' => $this->emailAccount->smtp_password,
                'mail.mailers.smtp.encryption' => $this->emailAccount->smtp_encryption === 'none' ? null : $this->emailAccount->smtp_encryption,
                'mail.from.address' => $this->emailAccount->email,
                'mail.from.name' => $this->emailAccount->from_name ?? 'BulkMailer',
            ]);

            // Purge the mail manager to use new config
            app('mail.manager')->purge('smtp');

            $keywords = EmailKeyword::activeMap();

            if ($this->isBulk) {
                // Send bulk as individual messages to avoid exposing recipients to each other.
                // Each recipient is isolated: one 550 must not fail the whole batch
                // (that is what caused retry storms + MaxAttemptsExceeded).
                $recipients = is_array($this->recipients) ? $this->recipients : [$this->recipients];
                $sentCount = 0;
                $failedTransient = 0;
                $lastTransientError = null;
                $transientFailures = [];
                foreach ($recipients as $recipient) {
                    if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                        $this->trackRecipient($recipient, 'skipped', 'Invalid email address');
                        continue;
                    }
                    if (\App\Models\EmailUnsubscribe::isUnsubscribed($recipient)) {
                        \Log::info('Skipped unsubscribed recipient: '.$recipient);
                        $this->trackRecipient($recipient, 'skipped', 'Unsubscribed');
                        continue;
                    }
                    // On a retry, don't resend rows already marked sent.
                    if ($this->attempts() > 0 && $this->campaignId && $this->isAlreadySent($recipient)) {
                        $sentCount++;
                        continue;
                    }
                    [$subject, $body] = $this->personalize($keywords, $recipient);
                    try {
                        Mail::to($recipient)->send(new IndividualMail($subject, $body, $recipient));
                        $sentCount++;
                        $this->trackRecipient($recipient, 'sent');
                    } catch (\Throwable $e) {
                        // Permanent 5xx (mailbox unknown, rejected content, blocked sender):
                        // record + continue, never retry the whole batch for these.
                        if ($this->isPermanentFailure($e->getMessage())) {
                            \Log::warning('Permanent send failure, skipping recipient', ['to' => $recipient, 'error' => $e->getMessage()]);
                            $this->trackRecipient($recipient, 'failed', $e->getMessage());
                            continue;
                        }
                        // Transient (421/450/451/connection timeout/rate-limit 550):
                        // collect, do NOT mark failed yet — marking now would
                        // double-count on every retry. Thrown below for backoff.
                        $failedTransient++;
                        $lastTransientError = $e->getMessage();
                        $transientFailures[$recipient] = $e->getMessage();
                        $this->touchRecipientError($recipient, $e->getMessage());
                    }
                    // Pacing: ~1 mail / 500ms keeps us under most shared-host
                    // SMTP rate limits (avoids the 550 throttling that started this).
                    usleep(500000);
                }
                $this->updateContactsLastEmailed($recipients);
                // Increment by actual number of emails sent
                if ($sentCount > 0) {
                    $this->emailAccount->increment('emails_sent', $sentCount);
                }
                $this->emailAccount->update(['last_used_at' => now()]);
                // Partial-batch handling: only transient failures reach here.
                if ($failedTransient > 0) {
                    if ($this->isFinalAttempt()) {
                        foreach ($transientFailures as $failedEmail => $failedError) {
                            $this->trackRecipient($failedEmail, 'failed', $failedError);
                        }

                        return; // counted in tracker, don't pollute failed_jobs
                    }
                    throw new \Exception('Transient SMTP failure for '.$failedTransient.' recipient(s). Last: '.$lastTransientError);
                }
            } else {
                // Send individual email (single recipient)
                $recipient = is_array($this->recipients) ? $this->recipients[0] : $this->recipients;
                if (\App\Models\EmailUnsubscribe::isUnsubscribed($recipient)) {
                    \Log::info('Skipped unsubscribed recipient: '.$recipient);
                    $this->trackRecipient($recipient, 'skipped', 'Unsubscribed');

                    return;
                }
                try {
                    [$subject, $body] = $this->personalize($keywords, $recipient);
                    Mail::to($recipient)->send(new IndividualMail($subject, $body, $recipient));
                } catch (\Throwable $e) {
                    if ($this->isPermanentFailure($e->getMessage())) {
                        $this->trackRecipient($recipient, 'failed', $e->getMessage());

                        return; // permanent: counted, no retry
                    }
                    if ($this->isFinalAttempt()) {
                        $this->trackRecipient($recipient, 'failed', $e->getMessage());

                        return; // final transient: counted, no MaxAttemptsExceeded noise
                    }
                    $this->touchRecipientError($recipient, $e->getMessage());
                    throw $e; // transient: backoff + retry
                }
                $this->trackRecipient($recipient, 'sent');
                $this->updateContactsLastEmailed([$recipient]);
                $this->emailAccount->increment('emails_sent');
                $this->emailAccount->update(['last_used_at' => now()]);
            }

        } catch (\Exception $e) {
            \Log::error('Individual email sending failed: '.$e->getMessage(), [
                'email_account' => $this->emailAccount->email,
                'smtp_host' => $this->emailAccount->smtp_host,
                'smtp_port' => $this->emailAccount->smtp_port,
                'error' => $e->getMessage(),
            ]);
            // Mark recipients failed so the tracker shows what went wrong.
            foreach ((array) $this->recipients as $recipient) {
                $this->trackRecipient($recipient, 'failed', $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Report one recipient outcome to the realtime tracker (if linked to a campaign).
     * Never throws — tracking must not break sending.
     */
    private function trackRecipient(string $email, string $outcome, ?string $error = null): void
    {
        try {
            if (! $this->campaignId || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
            }
            $campaign = OneTimeSender::find($this->campaignId);
            if (! $campaign) {
                return;
            }
            $column = match ($outcome) {
                'failed' => 'failed_count',
                'skipped' => 'skipped_count',
                default => 'sent_count',
            };
            $campaign->increment($column);

            CampaignRecipient::updateOrCreate(
                ['campaign_id' => $this->campaignId, 'email' => strtolower(trim($email))],
                [
                    'status' => $outcome === 'sent' ? CampaignRecipient::STATUS_SENT
                        : ($outcome === 'failed' ? CampaignRecipient::STATUS_FAILED : CampaignRecipient::STATUS_SKIPPED),
                    'error' => $error ? substr($error, 0, 1000) : null,
                    'sent_at' => $outcome === 'sent' ? now() : null,
                ]
            );

            if ($error && $outcome !== 'sent') {
                $campaign->update(['last_error' => substr($error, 0, 1000)]);
            }

            $campaign->refresh();
            $campaign->refreshStatusFromCounters();
        } catch (\Throwable $t) {
            Log::warning('Tracker update failed: '.$t->getMessage());
        }
    }

    /**
     * Replace admin-defined [keywords] per recipient (contact lookup + defaults).
     * Never fails the send: falls back to the raw subject/body.
     */
    private function personalize(array $keywords, string $recipient): array
    {
        try {
            if (empty($keywords)) {
                return [$this->subject, $this->body];
            }
            $row = EmailContact::where('email', $recipient)->first() ?? ['email' => $recipient];

            return [
                EmailKeyword::replaceIn($this->subject, $row, $keywords),
                EmailKeyword::replaceIn($this->body, $row, $keywords),
            ];
        } catch (\Throwable $e) {
            \Log::warning('Keyword personalization failed, sending raw content', [
                'to' => $recipient,
                'error' => $e->getMessage(),
            ]);

            return [$this->subject, $this->body];
        }
    }

    /**
     * Whether this is the last allowed attempt (job will land in
     * failed_jobs / MaxAttemptsExceeded path after this).
     */
    private function isFinalAttempt(): bool
    {
        try {
            return $this->attempts() >= $this->tries;
        } catch (\Throwable $t) {
            return true;
        }
    }

    /**
     * Permanent SMTP failures: retrying will never succeed, so record
     * them immediately and never throw (avoids retry storms).
     * Conservative list — unknown errors are treated as transient.
     */
    private function isPermanentFailure(string $message): bool
    {
        $m = strtolower($message);
        $permanent = [
            'mailbox unavailable', 'mailbox not found', 'user unknown',
            'recipient unknown', 'recipient address rejected',
            'address unknown', 'invalid mailbox', 'no such user',
            'account does not exist', 'account unavailable',
            'message rejected', // 550 content/policy rejection (your 550 case)
            'blocked', 'blacklisted', 'blacklist', 'spam',
            'authentication failed', 'authentication unsuccessful',
            'invalid login', 'invalid credentials', 'relay denied',
            'relaying denied', 'sender rejected',
            'unrouteable address', 'domain not found',
        ];
        foreach ($permanent as $needle) {
            if (str_contains($m, $needle)) {
                // "message rejected" + "exceeded"/"limit"/"throttl"/"too many"
                // is actually a rate limit -> transient, keep retrying.
                if ($needle === 'message rejected'
                    && preg_match('/exceed|limit|throttl|too many|try again|temporary|4\.\d\.\d|421|450|451/i', $message)) {
                    return false;
                }

                return true;
            }
        }
        // Bare "550" without rate-limit wording: recipient server refused
        // delivery (policy/content). Retrying 3x in backoff won't help.
        if (preg_match('/\b55[0-9]\b/', $message)
            && ! preg_match('/exceed|limit|throttl|too many|try again|temporary|4\.\d\.\d|421|450|451/i', $message)) {
            return true;
        }

        return false;
    }

    /**
     * Record attempt/error on the recipient row without bumping the
     * campaign counters (intermediate retries must not double-count).
     */
    private function touchRecipientError(string $email, string $error): void
    {
        try {
            if (! $this->campaignId || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
            }
            $recipient = CampaignRecipient::firstOrCreate(
                ['campaign_id' => $this->campaignId, 'email' => strtolower(trim($email))],
                ['status' => CampaignRecipient::STATUS_QUEUED]
            );
            $recipient->increment('attempts');
            $recipient->update(['error' => substr($error, 0, 1000)]);
        } catch (\Throwable $t) {
            // Tracker must never break sending.
        }
    }

    /**
     * Skip recipients already marked sent (idempotent retries).
     */
    private function isAlreadySent(string $email): bool
    {
        try {
            return CampaignRecipient::where('campaign_id', $this->campaignId)
                ->where('email', strtolower(trim($email)))
                ->where('status', CampaignRecipient::STATUS_SENT)
                ->exists();
        } catch (\Throwable $t) {
            return false;
        }
    }

    /**
     * Handle a job failure (only fires when an exception escapes handle()
     * on the final attempt — all counted paths above return normally).
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Individual email job finally failed: '.$exception->getMessage());
        try {
            if (! $this->campaignId) {
                return;
            }
            foreach ((array) $this->recipients as $recipient) {
                if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $existing = CampaignRecipient::where('campaign_id', $this->campaignId)
                    ->where('email', strtolower(trim($recipient)))
                    ->first();
                // Only count if not already tracked (avoids double count
                // against the final-attempt paths in handle()).
                if (! $existing || ! in_array($existing->status, [CampaignRecipient::STATUS_FAILED, CampaignRecipient::STATUS_SENT], true)) {
                    $this->trackRecipient($recipient, 'failed', $exception->getMessage());
                }
            }
        } catch (\Throwable $t) {
            Log::warning('Failed-hook tracker update failed: '.$t->getMessage());
        }
    }

    /**
     * Update last_emailed_at for contacts.
     */
    private function updateContactsLastEmailed($recipients): void
    {
        try {
            $emails = is_array($recipients) ? $recipients : [$recipients];

            EmailContact::whereIn('email', $emails)
                ->update(['last_emailed_at' => now()]);

        } catch (\Exception $e) {
            \Log::warning('Failed to update contact last_emailed_at: '.$e->getMessage());
        }
    }
}
