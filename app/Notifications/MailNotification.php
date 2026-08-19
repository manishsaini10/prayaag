<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** Number of times the job may be attempted. */
    public int $tries = 3;

    /** Backoff delays in seconds between retries. */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public string $subjectLine,
        public string $body,
        public ?string $actionUrl = null,
        public ?string $actionText = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
        );
    }
}
