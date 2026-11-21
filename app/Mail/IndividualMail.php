<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class IndividualMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailSubject;

    public $emailBody;

    public $recipientEmail;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $recipientEmail = null)
    {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        $this->recipientEmail = $recipientEmail;
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
                'unsubscribeUrl' => $this->unsubscribeUrl(),
            ],
        );
    }

    /**
     * Signed one-click + footer unsubscribe headers (RFC 8058).
     * Powers the native "Unsubscribe" button in Gmail / Yahoo / Outlook.
     * List-Unsubscribe points at the POST one-click endpoint; the footer
     * button links to the GET confirmation page (unsubscribeUrl).
     */
    public function headers(): Headers
    {
        $url = $this->oneClickUrl();

        if (! $url) {
            return new Headers;
        }

        return new Headers(
            text: [
                'List-Unsubscribe' => "<{$url}>",
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
            ],
        );
    }

    protected function oneClickUrl(): ?string
    {
        if (empty($this->recipientEmail) || ! filter_var($this->recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return URL::signedRoute('unsubscribe.one-click', ['email' => strtolower(trim($this->recipientEmail))]);
    }

    protected function unsubscribeUrl(): ?string
    {
        if (empty($this->recipientEmail) || ! filter_var($this->recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return URL::signedRoute('unsubscribe.show', ['email' => strtolower(trim($this->recipientEmail))]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
