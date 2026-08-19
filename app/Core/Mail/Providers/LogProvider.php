<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use Illuminate\Support\Facades\Log;

class LogProvider implements MailProviderInterface
{
    public function key(): string
    {
        return 'log';
    }

    public function label(): string
    {
        return 'Log (Local / Dev Fallback)';
    }

    public function send(EmailMessage $message): SendResult
    {
        Log::info('MailManager [LogProvider]: Sending Email', [
            'to' => $message->getToAddresses(),
            'subject' => $message->subject,
            'template' => $message->templateKey,
            'module' => $message->module,
        ]);

        return SendResult::ok('log-' . uniqid(), $this->key());
    }

    public function validateConfig(array $config): array
    {
        return [];
    }

    public function testConnection(array $config): SendResult
    {
        return SendResult::ok('log-test-ok', $this->key());
    }

    public function requiredFields(): array
    {
        return [];
    }
}
