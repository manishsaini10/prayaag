<?php

namespace App\Core\Mail\Contracts;

use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;

interface MailProviderInterface
{
    public function key(): string;
    public function label(): string;
    public function send(EmailMessage $message): SendResult;
    public function validateConfig(array $config): array;
    public function testConnection(array $config): SendResult;
    public function requiredFields(): array;
}
