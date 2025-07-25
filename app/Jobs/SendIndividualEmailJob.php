<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailAccount;
use App\Models\EmailContact;
use App\Mail\IndividualMail;

class SendIndividualEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailAccount;
    protected $recipients;
    protected $subject;
    protected $body;
    protected $isBulk;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailAccount $emailAccount, $recipients, $subject, $body, $isBulk = false)
    {
        $this->emailAccount = $emailAccount;
        $this->recipients = $recipients;
        $this->subject = $subject;
        $this->body = $body;
        $this->isBulk = $isBulk;
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
                'mail.mailers.smtp.encryption' => $this->emailAccount->smtp_encryption,
                'mail.from.address' => $this->emailAccount->email,
                'mail.from.name' => $this->emailAccount->from_name ?? 'BulkMailer',
            ]);

            // Purge the mail manager to use new config
            app('mail.manager')->purge('smtp');

            if ($this->sendType === 'bulk') {
                // Send bulk email
                Mail::to($this->recipients)
                    ->send(new IndividualMail($this->subject, $this->body));

                // Update last_emailed_at for all recipients
                $this->updateContactsLastEmailed($this->recipients);
            } else {
                // Send individual email
                $recipient = is_array($this->recipients) ? $this->recipients[0] : $this->recipients;
                Mail::to($recipient)->send(new IndividualMail($this->subject, $this->body));

                // Update last_emailed_at for the recipient
                $this->updateContactsLastEmailed([$recipient]);
            }

        } catch (\Exception $e) {
            \Log::error('Individual email sending failed: ' . $e->getMessage(), [
                'email_account' => $this->emailAccount->email,
                'smtp_host' => $this->emailAccount->smtp_host,
                'smtp_port' => $this->emailAccount->smtp_port,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Individual email job failed: ' . $exception->getMessage());
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
            \Log::warning('Failed to update contact last_emailed_at: ' . $e->getMessage());
        }
    }
}
