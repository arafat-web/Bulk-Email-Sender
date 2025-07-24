<?php

namespace App\Jobs;

use App\Mail\SendMail;
use App\Models\EmailAccount;
use App\Models\OneTimeSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $mailData;

    // Job retry settings
    public $tries = 3;
    public $maxExceptions = 3;
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min
    public $timeout = 120; // 2 minutes timeout

    public function __construct($email, $mailData)
    {
        $this->email = $email;
        $this->mailData = $mailData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Validate email address
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                Log::warning("Invalid email address skipped in job: " . $this->email);
                return;
            }

            // Get the email account to use
            $emailAccount = null;

            if (isset($this->mailData['email_account_id'])) {
                $emailAccount = EmailAccount::find($this->mailData['email_account_id']);
            }

            if (!$emailAccount) {
                $emailAccount = EmailAccount::getDefault();
            }

            if (!$emailAccount || !$emailAccount->is_active) {
                throw new Exception('No active email account available for sending emails.');
            }

            // Decrypt password for use
            $decryptedPassword = decrypt($emailAccount->smtp_password);

            // Configure mail settings dynamically for this job
            Config::set('mail.mailers.smtp.host', $emailAccount->smtp_host);
            Config::set('mail.mailers.smtp.port', $emailAccount->smtp_port);
            Config::set('mail.mailers.smtp.username', $emailAccount->smtp_username);
            Config::set('mail.mailers.smtp.password', $decryptedPassword);
            Config::set('mail.mailers.smtp.encryption', $emailAccount->smtp_encryption === 'none' ? null : $emailAccount->smtp_encryption);
            Config::set('mail.from.address', $emailAccount->email);
            Config::set('mail.from.name', $emailAccount->from_name);

            // Additional SMTP settings for better delivery
            Config::set('mail.mailers.smtp.timeout', 30);
            Config::set('mail.mailers.smtp.local_domain', env('MAIL_EHLO_DOMAIN'));

            // Send the email
            Mail::to($this->email)->send(new SendMail($this->mailData));

            // Log successful send (only for debugging if needed)
            if (config('app.debug')) {
                Log::info("Email sent successfully", [
                    'to' => $this->email,
                    'subject' => $this->mailData['subject'] ?? 'No subject',
                    'account' => $emailAccount->name
                ]);
            }

        } catch (Exception $e) {
            // Log the error with context
            Log::error("Failed to send email", [
                'to' => $this->email,
                'subject' => $this->mailData['subject'] ?? 'No subject',
                'attempt' => $this->attempts(),
                'error' => $e->getMessage(),
                'account_id' => $this->mailData['email_account_id'] ?? null
            ]);

            // Update campaign status if this is the final attempt
            if ($this->attempts() >= $this->tries) {
                $this->updateCampaignStats('failed');
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
        Log::error("Email job finally failed after all retries", [
            'to' => $this->email,
            'subject' => $this->mailData['subject'] ?? 'No subject',
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $this->updateCampaignStats('failed');
    }

    /**
     * Update campaign statistics
     */
    private function updateCampaignStats(string $status): void
    {
        try {
            if (isset($this->mailData['campaign_id'])) {
                $campaign = OneTimeSender::find($this->mailData['campaign_id']);
                if ($campaign) {
                    if ($status === 'failed') {
                        $campaign->increment('failed_count');
                    } else {
                        $campaign->increment('sent_count');
                    }
                }
            }
        } catch (Exception $e) {
            Log::error("Failed to update campaign stats", [
                'campaign_id' => $this->mailData['campaign_id'] ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil()
    {
        return now()->addMinutes(30);
    }
}
