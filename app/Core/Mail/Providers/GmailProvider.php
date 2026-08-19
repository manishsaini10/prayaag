<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;

class GmailProvider extends SmtpProvider
{
    public function key(): string
    {
        return 'gmail';
    }

    public function label(): string
    {
        return 'Google Gmail (SMTP App Password)';
    }

    public function send(EmailMessage $message): SendResult
    {
        $this->config['host'] = 'smtp.gmail.com';
        $this->config['port'] = 587;
        $this->config['encryption'] = 'tls';

        return parent::send($message);
    }

    public function requiredFields(): array
    {
        return [
            'username' => ['type' => 'email', 'label' => 'Gmail / Google Workspace Email', 'placeholder' => 'yourname@gmail.com'],
            'password' => ['type' => 'password', 'label' => 'Google 16-character App Password', 'placeholder' => '•••• •••• •••• ••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag International School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email', 'placeholder' => 'yourname@gmail.com'],
        ];
    }
}
