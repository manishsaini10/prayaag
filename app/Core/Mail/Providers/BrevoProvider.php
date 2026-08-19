<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use Illuminate\Support\Facades\Http;

class BrevoProvider implements MailProviderInterface
{
    public function __construct(protected array $config = []) {}

    public function key(): string
    {
        return 'brevo';
    }

    public function label(): string
    {
        return 'Brevo (Sendinblue API)';
    }

    public function send(EmailMessage $message): SendResult
    {
        $apiKey = $this->config['api_key'] ?? '';
        if (empty($apiKey)) {
            return SendResult::fail('Brevo API key missing', $this->key());
        }

        $fromName = $message->fromName ?? $this->config['from_name'] ?? config('app.name');
        $fromEmail = $message->fromEmail ?? $this->config['from_email'] ?? '';

        $toPayload = array_map(fn ($email) => ['email' => $email], $message->getToAddresses());

        $payload = [
            'sender' => ['name' => $fromName, 'email' => $fromEmail],
            'to' => $toPayload,
            'subject' => $message->subject,
            'htmlContent' => $message->bodyHtml,
        ];

        if ($message->bodyText) {
            $payload['textContent'] = $message->bodyText;
        }

        if ($message->replyTo) {
            $payload['replyTo'] = ['email' => $message->replyTo];
        }

        try {
            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', $payload);

            if ($response->successful()) {
                $msgId = $response->json('messageId') ?? ('brevo-' . uniqid());
                return SendResult::ok($msgId, $this->key());
            }

            return SendResult::fail('Brevo API error: ' . $response->body(), $this->key());
        } catch (\Throwable $e) {
            return SendResult::fail($e->getMessage(), $this->key());
        }
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        if (empty($config['api_key'])) $errors['api_key'] = 'API Key is required';
        if (empty($config['from_email'])) $errors['from_email'] = 'From Email is required';
        return $errors;
    }

    public function testConnection(array $config): SendResult
    {
        $oldConfig = $this->config;
        $this->config = $config;

        $testMsg = new EmailMessage(
            to: $config['from_email'] ?? 'test@example.com',
            subject: 'Prayaag CMS - Brevo Connection Test',
            bodyHtml: '<p>Your Brevo API connection is working!</p>'
        );

        $res = $this->send($testMsg);
        $this->config = $oldConfig;
        return $res;
    }

    public function requiredFields(): array
    {
        return [
            'api_key' => ['type' => 'password', 'label' => 'Brevo v3 API Key', 'placeholder' => 'xkeysib-...'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag International School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email (Verified in Brevo)', 'placeholder' => 'info@prayaag.edu.in'],
        ];
    }
}
