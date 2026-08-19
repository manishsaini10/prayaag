<?php

namespace App\Core\Mail\Providers;

use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\DTO\SendResult;

class ZohoMailProvider extends SmtpProvider
{
    public function key(): string
    {
        return 'zoho';
    }

    public function label(): string
    {
        return 'Zoho Mail (SMTP)';
    }

    public function send(EmailMessage $message): SendResult
    {
        $regionHosts = [
            'in' => 'smtp.zoho.in',
            'eu' => 'smtp.zoho.eu',
            'global' => 'smtp.zoho.com',
        ];

        $region = $this->config['region'] ?? 'in';
        $this->config['host'] = $regionHosts[$region] ?? 'smtp.zoho.in';
        $this->config['port'] = $this->config['port'] ?? 465;
        $this->config['encryption'] = $this->config['encryption'] ?? 'ssl';

        return parent::send($message);
    }

    public function requiredFields(): array
    {
        return [
            'region' => ['type' => 'select', 'label' => 'Zoho Region', 'options' => ['in' => 'India (smtp.zoho.in)', 'global' => 'Global/US (smtp.zoho.com)', 'eu' => 'Europe (smtp.zoho.eu)']],
            'username' => ['type' => 'text', 'label' => 'Zoho Email Address', 'placeholder' => 'admin@prayaag.edu.in'],
            'password' => ['type' => 'password', 'label' => 'Zoho App Password', 'placeholder' => '••••••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag International School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email', 'placeholder' => 'admin@prayaag.edu.in'],
        ];
    }
}
