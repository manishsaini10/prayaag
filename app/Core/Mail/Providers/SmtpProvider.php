<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SmtpProvider implements MailProviderInterface
{
    public function __construct(protected array $config = []) {}

    public function key(): string
    {
        return 'smtp';
    }

    public function label(): string
    {
        return 'Custom SMTP Server';
    }

    public function send(EmailMessage $message): SendResult
    {
        $validation = $this->validateConfig($this->config);
        if (!empty($validation)) {
            return SendResult::fail('Invalid SMTP configuration: ' . implode(', ', $validation), $this->key());
        }

        try {
            $encryption = strtolower($this->config['encryption'] ?? 'tls');
            $tls = in_array($encryption, ['tls', 'ssl']);
            
            $transport = new EsmtpTransport(
                $this->config['host'],
                (int) ($this->config['port'] ?? 587),
                $tls
            );

            if (!empty($this->config['username'])) {
                $transport->setUsername($this->config['username']);
                $transport->setPassword($this->config['password'] ?? '');
            }

            $mailer = new Mailer($transport);

            $fromEmail = $message->fromEmail ?? $this->config['from_email'] ?? $this->config['username'];
            $fromName = $message->fromName ?? $this->config['from_name'] ?? config('app.name');

            $email = (new Email())
                ->from(new Address($fromEmail, $fromName))
                ->subject($message->subject)
                ->html($message->bodyHtml);

            if ($message->bodyText) {
                $email->text($message->bodyText);
            }

            foreach ($message->getToAddresses() as $to) {
                $email->addTo($to);
            }

            if ($message->replyTo) {
                $email->replyTo($message->replyTo);
            }

            $mailer->send($email);

            return SendResult::ok('smtp-' . uniqid(), $this->key());
        } catch (\Throwable $e) {
            return SendResult::fail($e->getMessage(), $this->key());
        }
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        if (empty($config['host'])) $errors['host'] = 'Host is required';
        if (empty($config['port'])) $errors['port'] = 'Port is required';
        if (empty($config['from_email'])) $errors['from_email'] = 'From email is required';
        return $errors;
    }

    public function testConnection(array $config): SendResult
    {
        $oldConfig = $this->config;
        $this->config = $config;

        $testMsg = new EmailMessage(
            to: $config['from_email'] ?? 'test@example.com',
            subject: 'Prayaag CMS - SMTP Connection Test',
            bodyHtml: '<p>Your SMTP connection is working correctly!</p>'
        );

        $result = $this->send($testMsg);
        $this->config = $oldConfig;
        return $result;
    }

    public function requiredFields(): array
    {
        return [
            'host' => ['type' => 'text', 'label' => 'SMTP Host', 'placeholder' => 'smtp.mailtrap.io'],
            'port' => ['type' => 'number', 'label' => 'Port', 'placeholder' => '587'],
            'encryption' => ['type' => 'select', 'label' => 'Encryption', 'options' => ['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None']],
            'username' => ['type' => 'text', 'label' => 'Username', 'placeholder' => 'user@domain.com'],
            'password' => ['type' => 'password', 'label' => 'Password', 'placeholder' => '••••••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email', 'placeholder' => 'noreply@prayaag.edu.in'],
        ];
    }
}
