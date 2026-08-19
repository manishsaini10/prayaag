<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Mail\Contracts\MailProviderInterface;
use App\Core\Mail\Providers\BrevoProvider;
use App\Core\Mail\Providers\ElasticEmailProvider;
use App\Core\Mail\Providers\HostingerCpanelProvider;
use App\Core\Mail\Providers\LogProvider;
use App\Core\Mail\Providers\MailjetProvider;
use App\Core\Mail\Providers\SesProvider;
use App\Core\Mail\Providers\SmtpProvider;
use App\Core\Mail\Providers\ZohoMailProvider;
use Illuminate\Database\Eloquent\Builder;

class EmailProviderConfig extends BaseModel
{
    protected $table = 'email_providers_config';

    protected $casts = [
        'credentials' => 'encrypted:json',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('priority_order', 'asc');
    }

    public function getProviderInstance(): MailProviderInterface
    {
        $creds = $this->credentials ?? [];

        return match ($this->provider_key) {
            'hostinger' => new HostingerCpanelProvider($creds),
            'gmail' => new \App\Core\Mail\Providers\GmailProvider($creds),
            'zoho' => new ZohoMailProvider($creds),
            'brevo' => new BrevoProvider($creds),
            'elastic_email' => new ElasticEmailProvider($creds),
            'mailjet' => new MailjetProvider($creds),
            'ses' => new SesProvider($creds),
            'smtp' => new SmtpProvider($creds),
            default => new LogProvider(),
        };
    }
}
