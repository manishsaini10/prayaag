<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use Illuminate\Support\Facades\Http;

class MailjetProvider implements MailProviderInterface
{
    public function __construct(protected array $config = []) {}

    public function key(): string
    {
        return 'mailjet';
    }

    public function label(): string
    {
        return 'Mailjet (API)';
    }

    public function send(EmailMessage $message): SendResult
    {
        $apiKey = $this->config['api_key'] ?? '';
        $secretKey = $this->config['secret_key'] ?? '';

        if (empty($apiKey) || empty($secretKey)) {
            return SendResult::fail('Mailjet API key/secret missing', $this->key());
        }

        $fromName = $message->fromName ?? $this->config['from_name'] ?? config('app.name');
        $fromEmail = $message->fromEmail ?? $this->config['from_email'] ?? '';

        $toPayload = array_map(fn ($email) => ['Email' => $email], $message->getToAddresses());

        $payload = [
            'Messages' => [
                [
                    'From' => ['Email' => $fromEmail, 'Name' => $fromName],
                    'To' => $toPayload,
                    'Subject' => $message->subject,
                    'HTMLPart' => $message->bodyHtml,
                    'TextPart' => $message->bodyText ?? strip_tags($message->bodyHtml),
                ]
            ]
        ];

        try {
            $response = Http::withBasicAuth($apiKey, $secretKey)
                ->post('https://api.mailjet.com/v3.1/send', $payload);

            if ($response->successful()) {
                $status = $response->json('Messages.0.Status');
                if ($status === 'success') {
                    return SendResult::ok('mailjet-' . uniqid(), $this->key());
                }
            }

            return SendResult::fail('Mailjet API error: ' . $response->body(), $this->key());
        } catch (\Throwable $e) {
            return SendResult::fail($e->getMessage(), $this->key());
        }
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        if (empty($config['api_key'])) $errors['api_key'] = 'API Key is required';
        if (empty($config['secret_key'])) $errors['secret_key'] = 'Secret Key is required';
        if (empty($config['from_email'])) $errors['from_email'] = 'From Email is required';
        return $errors;
    }

    public function testConnection(array $config): SendResult
    {
        $oldConfig = $this->config;
        $this->config = $config;

        $testMsg = new EmailMessage(
            to: $config['from_email'] ?? 'test@example.com',
            subject: 'Prayaag CMS - Mailjet Connection Test',
            bodyHtml: '<p>Your Mailjet API connection is working!</p>'
        );

        $res = $this->send($testMsg);
        $this->config = $oldConfig;
        return $res;
    }

    public function requiredFields(): array
    {
        return [
            'api_key' => ['type' => 'text', 'label' => 'Mailjet API Key', 'placeholder' => '••••••••'],
            'secret_key' => ['type' => 'password', 'label' => 'Mailjet Secret Key', 'placeholder' => '••••••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag International School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email (Sender)', 'placeholder' => 'info@prayaag.edu.in'],
        ];
    }
}
