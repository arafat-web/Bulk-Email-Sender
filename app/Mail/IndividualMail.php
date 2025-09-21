<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateProcessor;
use App\Models\EmailContact;

class IndividualMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailSubject;
    public $emailBody;
    public $processedBody;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $recipientEmail = null)
    {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        
        // Process the email body with custom fields if we have a recipient
        $processor = new EmailTemplateProcessor();
        
        if ($recipientEmail) {
            $contact = EmailContact::where('email', $recipientEmail)->first();
            
            if ($contact) {
                $this->processedBody = $processor->processTemplate($body, $contact);
            } else {
                $this->processedBody = $processor->processPlainTemplate($body, $recipientEmail);
            }
        } else {
            $this->processedBody = $body;
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.individual',
            with: [
                'emailBody' => $this->processedBody
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
