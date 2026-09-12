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
                // Send bulk as individual messages to avoid exposing recipients to each other
                $recipients = is_array($this->recipients) ? $this->recipients : [$this->recipients];
                $sentCount = 0;
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
                    [$subject, $body] = $this->personalize($keywords, $recipient);
                    Mail::to($recipient)->send(new IndividualMail($subject, $body, $recipient));
                    $sentCount++;
                    $this->trackRecipient($recipient, 'sent');
                }
                $this->updateContactsLastEmailed($recipients);
                // Increment by actual number of emails sent
                if ($sentCount > 0) {
                    $this->emailAccount->increment('emails_sent', $sentCount);
                }
                $this->emailAccount->update(['last_used_at' => now()]);
            } else {
                // Send individual email
                $recipient = is_array($this->recipients) ? $this->recipients[0] : $this->recipients;
                if (\App\Models\EmailUnsubscribe::isUnsubscribed($recipient)) {
                    \Log::info('Skipped unsubscribed recipient: '.$recipient);
                    $this->trackRecipient($recipient, 'skipped', 'Unsubscribed');

                    return;
                }
                [$subject, $body] = $this->personalize($keywords, $recipient);
                Mail::to($recipient)->send(new IndividualMail($subject, $body, $recipient));
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
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Individual email job failed: '.$exception->getMessage());
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
