<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateProcessor;
use App\Models\EmailContact;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    
    public $mailData;
    public $contact;
    public $processedBody;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData, EmailContact $contact = null)
    {
        $this->mailData = $mailData;
        $this->contact = $contact;
        
        // Process the email body with custom fields
        $processor = new EmailTemplateProcessor();
        
        if ($contact) {
            $this->processedBody = $processor->processTemplate($mailData['body'], $contact);
        } else {
            // For instant campaigns without specific contacts, use plain processing
            $recipient = $mailData['recipient'] ?? '';
            $this->processedBody = $processor->processPlainTemplate($mailData['body'], $recipient);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailData['subject'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.template',
            with: [
                'mailData' => array_merge($this->mailData, ['body' => $this->processedBody])
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
