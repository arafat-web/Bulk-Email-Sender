<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailAccount;
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
            // Configure mail settings
            config([
                'mail.mailers.smtp.host' => $this->emailAccount->smtp_host,
                'mail.mailers.smtp.port' => $this->emailAccount->smtp_port,
                'mail.mailers.smtp.username' => $this->emailAccount->email,
                'mail.mailers.smtp.password' => $this->emailAccount->password,
                'mail.mailers.smtp.encryption' => $this->emailAccount->encryption,
                'mail.from.address' => $this->emailAccount->email,
                'mail.from.name' => $this->emailAccount->from_name ?? 'BulkMailer',
            ]);

            if ($this->isBulk && is_array($this->recipients)) {
                // Send bulk email (all recipients in one email)
                Mail::to($this->recipients[0])
                    ->cc(array_slice($this->recipients, 1))
                    ->send(new IndividualMail($this->subject, $this->body));
            } else {
                // Send individual email
                $recipient = is_array($this->recipients) ? $this->recipients[0] : $this->recipients;
                Mail::to($recipient)->send(new IndividualMail($this->subject, $this->body));
            }

        } catch (\Exception $e) {
            \Log::error('Individual email sending failed: ' . $e->getMessage());
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
}
