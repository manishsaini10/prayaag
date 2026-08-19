<?php

namespace App\Core\Mail\Providers;

class HostingerCpanelProvider extends SmtpProvider
{
    public function key(): string
    {
        return 'hostinger';
    }

    public function label(): string
    {
        return 'Hostinger / cPanel Webmail';
    }

    public function requiredFields(): array
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST) ?? 'yourdomain.com';
        $suggestedHost = 'smtp.' . preg_replace('/^www\./i', '', $domain);

        return [
            'host' => ['type' => 'text', 'label' => 'SMTP Host (cPanel/Hostinger)', 'placeholder' => $suggestedHost, 'default' => $suggestedHost],
            'port' => ['type' => 'number', 'label' => 'Port (465 SSL or 587 TLS)', 'placeholder' => '465', 'default' => '465'],
            'encryption' => ['type' => 'select', 'label' => 'Encryption', 'options' => ['ssl' => 'SSL (Port 465)', 'tls' => 'TLS (Port 587)']],
            'username' => ['type' => 'text', 'label' => 'Webmail Address / User', 'placeholder' => 'info@' . $domain],
            'password' => ['type' => 'password', 'label' => 'Webmail Password', 'placeholder' => '••••••••'],
            'from_name' => ['type' => 'text', 'label' => 'From Name', 'placeholder' => 'Prayaag School'],
            'from_email' => ['type' => 'email', 'label' => 'From Email', 'placeholder' => 'info@' . $domain],
        ];
    }
}
