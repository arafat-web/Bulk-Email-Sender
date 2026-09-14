<?php

namespace App\Jobs;

use App\Mail\SendMail;
use App\Models\CampaignRecipient;
use App\Models\EmailAccount;
use App\Models\EmailContact;
use App\Models\EmailKeyword;
use App\Models\OneTimeSender;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    protected $mailData;

    protected $recipientData;

    // Job retry settings
    public $tries = 3;

    public $maxExceptions = 3;

    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    public $timeout = 120; // 2 minutes timeout

    /**
     * Same `smtp-account` limiter as SendIndividualEmailJob: 6/min per
     * account. Without this, instant campaigns bypass throttling entirely.
     */
    public function middleware(): array
    {
        return [new RateLimited('smtp-account')];
    }

    /**
     * Account id for the `smtp-account` rate limiter.
     */
    public function getEmailAccountId(): string|int|null
    {
        try {
            return $this->mailData['email_account_id'] ?? 'default';
        } catch (\Throwable $t) {
            return 'default';
        }
    }

    public function __construct($email, $mailData, $recipientData = null)
    {
        $this->email = $email;
        $this->mailData = $mailData;
        $this->recipientData = $recipientData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Validate email address
            if (! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                Log::warning('Invalid email address skipped in job: '.$this->email);
                $this->updateCampaignStats('skipped', 'Invalid email address');

                return;
            }

            // Skip unsubscribed recipients (global suppression list + contact status)
            if (\App\Models\EmailUnsubscribe::isUnsubscribed($this->email)) {
                Log::info('Skipped unsubscribed recipient: '.$this->email);
                $this->updateCampaignStats('skipped', 'Unsubscribed');

                return;
            }

            // Get the email account to use
            $emailAccount = null;

            if (isset($this->mailData['email_account_id'])) {
                $emailAccount = EmailAccount::find($this->mailData['email_account_id']);
            }

            if (! $emailAccount) {
                $emailAccount = EmailAccount::getDefault();
            }

            if (! $emailAccount || ! $emailAccount->is_active) {
                throw new Exception('No active email account available for sending emails.');
            }

            // Configure mail settings dynamically for this job
            // Note: smtp_password is already decrypted by the model accessor
            Config::set('mail.mailers.smtp.host', $emailAccount->smtp_host);
            Config::set('mail.mailers.smtp.port', $emailAccount->smtp_port);
            Config::set('mail.mailers.smtp.username', $emailAccount->smtp_username);
            Config::set('mail.mailers.smtp.password', $emailAccount->smtp_password);
            Config::set('mail.mailers.smtp.encryption', $emailAccount->smtp_encryption === 'none' ? null : $emailAccount->smtp_encryption);
            Config::set('mail.from.address', $emailAccount->email);
            Config::set('mail.from.name', $emailAccount->from_name);

            // Additional SMTP settings for better delivery
            Config::set('mail.mailers.smtp.timeout', 30);
            Config::set('mail.mailers.smtp.local_domain', env('MAIL_EHLO_DOMAIN'));

            // Purge the mail manager to ensure fresh configuration
            app('mail.manager')->purge('smtp');

            // Personalize subject/body with admin-defined dynamic keywords (e.g. [company_name], [name])
            $personalizedMailData = $this->personalizeMailData();

            // Send the email
            Mail::to($this->email)->send(new SendMail($personalizedMailData, $this->email));

            // Update contact last_emailed_at if contact exists
            EmailContact::where('email', $this->email)
                ->update(['last_emailed_at' => now()]);

            // Increment emails_sent counter
            $emailAccount->increment('emails_sent');
            $emailAccount->update(['last_used_at' => now()]);

            // Track sent in campaign
            $this->updateCampaignStats('sent');

            // Log successful send (only for debugging if needed)
            if (config('app.debug')) {
                Log::info('Email sent successfully', [
                    'to' => $this->email,
                    'subject' => $personalizedMailData['subject'] ?? $this->mailData['subject'] ?? 'No subject',
                    'account' => $emailAccount->name,
                ]);
            }

        } catch (Exception $e) {
            // Log the error with context
            Log::error('Failed to send email', [
                'to' => $this->email,
                'subject' => $this->mailData['subject'] ?? 'No subject',
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'account_id' => $this->mailData['email_account_id'] ?? null,
            ]);

            // Bump per-recipient attempt counter so the tracker shows retries.
            $this->touchRecipientAttempt($e->getMessage());

            // Count a failure only on the final attempt (retries don't double-count).
            $isFinalAttempt = false;
            try {
                $isFinalAttempt = $this->attempts() >= $this->tries;
            } catch (\Throwable $t) {
                $isFinalAttempt = true;
            }
            if ($isFinalAttempt) {
                // failed() hook also fires — guard there via status check.
                $this->updateCampaignStats('failed', $e->getMessage());
            }

            // Re-throw the exception to trigger retry logic
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Email job finally failed after all retries', [
            'to' => $this->email,
            'subject' => $this->mailData['subject'] ?? 'No subject',
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Guard against double counting: handle() already counts the final
        // attempt failure before re-throwing, so only count here if the
        // recipient row is not already marked failed.
        try {
            $campaignId = $this->mailData['campaign_id'] ?? null;
            if ($campaignId) {
                $existing = CampaignRecipient::where('campaign_id', $campaignId)
                    ->where('email', strtolower(trim($this->email)))
                    ->first();
                if (! $existing || $existing->status !== CampaignRecipient::STATUS_FAILED) {
                    $this->updateCampaignStats('failed', $exception->getMessage());
                } else {
                    $this->touchRecipientAttempt($exception->getMessage());
                }
            }
        } catch (\Throwable $t) {
            $this->updateCampaignStats('failed', $exception->getMessage());
        }
    }

    /**
     * Record an attempt on the recipient row without changing counters.
     */
    private function touchRecipientAttempt(?string $error = null): void
    {
        try {
            $campaignId = $this->mailData['campaign_id'] ?? null;
            if (! $campaignId) {
                return;
            }
            $recipient = CampaignRecipient::firstOrCreate(
                ['campaign_id' => $campaignId, 'email' => strtolower(trim($this->email))],
                ['status' => CampaignRecipient::STATUS_QUEUED]
            );
            $recipient->increment('attempts');
            if ($error) {
                $recipient->update(['error' => substr($error, 0, 1000)]);
            }
        } catch (\Throwable $t) {
            // Tracker must never break sending.
        }
    }

    /**
     * Replace admin-defined [keywords] in subject/body using per-recipient data.
     * Falls back to the EmailContact record, then to keyword default values.
     */
    private function personalizeMailData(): array
    {
        try {
            $keywords = EmailKeyword::activeMap();
            if (empty($keywords)) {
                return $this->mailData;
            }

            $row = $this->recipientData;
            if (empty($row)) {
                $row = EmailContact::where('email', $this->email)->first();
            }
            if (empty($row)) {
                $row = ['email' => $this->email];
            }

            $data = $this->mailData;
            if (isset($data['subject'])) {
                $data['subject'] = EmailKeyword::replaceIn($data['subject'], $row, $keywords);
            }
            if (isset($data['body'])) {
                $data['body'] = EmailKeyword::replaceIn($data['body'], $row, $keywords);
            }

            return $data;
        } catch (\Throwable $e) {
            Log::warning('Keyword personalization failed, sending raw template', [
                'to' => $this->email,
                'error' => $e->getMessage(),
            ]);

            return $this->mailData;
        }
    }

    /**
     * Update campaign statistics (realtime counters + per-recipient row).
     * Uses atomic increments so parallel queue workers stay accurate.
     */
    private function updateCampaignStats(string $status, ?string $error = null): void
    {
        try {
            $campaignId = $this->mailData['campaign_id'] ?? null;
            if (! $campaignId) {
                return;
            }

            $campaign = OneTimeSender::find($campaignId);
            if (! $campaign) {
                return;
            }

            $recipientStatus = match ($status) {
                'failed' => CampaignRecipient::STATUS_FAILED,
                'skipped' => CampaignRecipient::STATUS_SKIPPED,
                default => CampaignRecipient::STATUS_SENT,
            };

            // Transition-aware counters: only bump the NEW status column and
            // decrement the OLD one. Fixes the realtime stall where retries
            // pushed processed > total and the tracker froze at 100% early.
            $email = strtolower(trim($this->email));
            $existing = CampaignRecipient::where('campaign_id', $campaignId)->where('email', $email)->first();
            $previous = $existing?->status;
            if ($previous === $recipientStatus) {
                // Same-state retry (e.g. double delivery callback): touch
                // timestamps but don't inflate counters.
                $existing->update([
                    'error' => $error ? substr($error, 0, 1000) : null,
                    'sent_at' => $recipientStatus === CampaignRecipient::STATUS_SENT ? now() : $existing->sent_at,
                ]);
            } else {
                CampaignRecipient::updateOrCreate(
                    ['campaign_id' => $campaignId, 'email' => $email],
                    [
                        'status' => $recipientStatus,
                        'error' => $error ? substr($error, 0, 1000) : null,
                        'sent_at' => $recipientStatus === CampaignRecipient::STATUS_SENT ? now() : null,
                    ]
                );
                $column = match ($recipientStatus) {
                    CampaignRecipient::STATUS_FAILED => 'failed_count',
                    CampaignRecipient::STATUS_SKIPPED => 'skipped_count',
                    default => 'sent_count',
                };
                $campaign->increment($column);
                $prevColumn = match ($previous) {
                    CampaignRecipient::STATUS_FAILED => 'failed_count',
                    CampaignRecipient::STATUS_SKIPPED => 'skipped_count',
                    CampaignRecipient::STATUS_SENT => 'sent_count',
                    default => null,
                };
                if ($prevColumn && $prevColumn !== $column) {
                    $campaign->decrement($prevColumn);
                }
            }

            if ($error && $recipientStatus !== CampaignRecipient::STATUS_SENT) {
                $campaign->update(['last_error' => substr($error, 0, 1000)]);
            }

            // Refresh + auto-complete when every recipient is processed.
            $campaign->refresh();
            $campaign->refreshStatusFromCounters();
        } catch (Exception $e) {
            Log::error('Failed to update campaign stats', [
                'campaign_id' => $this->mailData['campaign_id'] ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Long window so back-of-queue jobs survive the drain.
     * 10k recipients at 360/hr ≈ 28h queue time — 30 min expired jobs
     * still waiting behind the rate limiter.
     */
    public function retryUntil()
    {
        return now()->addHours(48)->toDateTime();
    }
}
