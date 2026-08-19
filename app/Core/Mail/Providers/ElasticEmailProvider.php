<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use Illuminate\Support\Facades\Http;

class ElasticEmailProvider implements MailProviderInterface
{
    public function __construct(protected array $config = []) {}

    public function key(): string
    {
        return 'elastic_email';
    }

    public function label(): string
    {
        return 'Elastic Email (API)';
    }

    public function send(EmailMessage $message): SendResult
    {
        $apiKey = $this->config['api_key'] ?? '';
        if (empty($apiKey)) {
            return SendResult::fail('Elastic Email API key missing', $this->key());
        }

        $fromName = $message->fromName ?? $this->config['from_name'] ?? config('app.name');
        $fromEmail = $message->fromEmail ?? $this->config['from_email'] ?? '';

        $recipients = array_map(fn ($email) => ['Email' => $email], $message->getToAddresses());

        $payload = [
            'Recipients' => $recipients,
            'Content' => [
                'Body' => [
                    ['ContentType' => 'HTML', 'Content' => $message->bodyHtml],
                ],
                'Subject' => $message->subject,
                'From' => $fromEmail,
                'FromName' => $fromName,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'X-ElasticEmail-ApiKey' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.elasticemail.com/v4/emails/transactional', $payload);

            if ($response->successful()) {
                $msgId = $response->json('TransactionID') ?? ('elastic-' . uniqid());
                return SendResult::ok($msgId, $this->key());
            }

            return SendResult::fail('Elastic Email API error: ' . $response->body(), $this->key());
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
            subject: 'Prayaag CMS - Elastic Email Connection Test',
            bodyHtml: '<p>Your Elastic Email API connection is working!</p>'
        );

        $res = $this->send($testMsg);
        $this->config = $oldConfig;
        return $res;
    }

    public function requiredFields(): array
    {
        return [
            'api_key' => ['type' => 'password', 'label' => 'Elastic Email API Key', 'placeholder' => '••••••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag International School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email', 'placeholder' => 'info@prayaag.edu.in'],
        ];
    }
}
