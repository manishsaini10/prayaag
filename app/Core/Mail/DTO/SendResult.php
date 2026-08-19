<?php

namespace App\Core\Mail\DTO;

class SendResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $messageId = null,
        public readonly ?string $error = null,
        public readonly ?string $providerKey = null
    ) {}

    public static function ok(?string $messageId = null, ?string $providerKey = null): self
    {
        return new self(true, $messageId, null, $providerKey);
    }

    public static function fail(string $error, ?string $providerKey = null): self
    {
        return new self(false, null, $error, $providerKey);
    }
}
