<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ApplicationSubmitted extends Mailable
{
    public function __construct(
        public Application $application,
        public bool $forAdmin = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->forAdmin
                ? 'Jauns pieteikums no ' . $this->application->client_name
                : 'Saņēmām jūsu pieteikumu',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
