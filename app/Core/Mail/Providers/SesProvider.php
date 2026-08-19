<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;

class SesProvider extends SmtpProvider
{
    public function key(): string
    {
        return 'ses';
    }

    public function label(): string
    {
        return 'Amazon SES (SMTP)';
    }

    public function send(EmailMessage $message): SendResult
    {
        $region = $this->config['region'] ?? 'us-east-1';
        $this->config['host'] = "email-smtp.{$region}.amazonaws.com";
        $this->config['port'] = 587;
        $this->config['encryption'] = 'tls';

        return parent::send($message);
    }

    public function requiredFields(): array
    {
        return [
            'region' => ['type' => 'text', 'label' => 'AWS Region', 'placeholder' => 'us-east-1', 'default' => 'us-east-1'],
            'username' => ['type' => 'text', 'label' => 'SES SMTP Username', 'placeholder' => 'AKIA...'],
            'password' => ['type' => 'password', 'label' => 'SES SMTP Password', 'placeholder' => '••••••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag International School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email (Verified in SES)', 'placeholder' => 'info@prayaag.edu.in'],
        ];
    }
}
