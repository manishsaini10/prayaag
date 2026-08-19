<?php

namespace App\Core\Mail\DTO;

class EmailMessage
{
    public function __construct(
        public readonly array $to, // string or array of emails
        public readonly string $subject,
        public readonly string $bodyHtml,
        public readonly ?string $bodyText = null,
        public readonly ?string $fromName = null,
        public readonly ?string $fromEmail = null,
        public readonly ?string $replyTo = null,
        public readonly ?string $templateKey = null,
        public readonly ?string $module = null,
        public readonly array $metadata = []
    ) {}

    public function getToAddresses(): array
    {
        return is_array($this->to) ? $this->to : [$this->to];
    }
}
