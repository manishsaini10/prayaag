<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class NewsletterSubscriber extends BaseModel
{
    protected $table = 'newsletter_subscribers';

    protected $casts = [
        'email' => 'encrypted',
        'consent_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public static function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    public static function findByEmail(string $email): ?self
    {
        return static::where('email_hash', static::hashEmail($email))->first();
    }

    public static function createPending(string $email, ?string $source = null, ?string $name = null): self
    {
        $hash = static::hashEmail($email);
        $existing = static::where('email_hash', $hash)->first();

        if ($existing) {
            if ($existing->status === 'subscribed') {
                return $existing;
            }
            $existing->update([
                'status' => 'pending',
                'confirm_token' => Str::random(40),
                'consent_source' => $source ?? $existing->consent_source,
            ]);
            return $existing;
        }

        return static::create([
            'email' => $email,
            'email_hash' => $hash,
            'name' => $name,
            'status' => 'pending',
            'confirm_token' => Str::random(40),
            'consent_source' => $source,
            'consent_at' => now(),
        ]);
    }

    public function unsubscribeUrl(): string
    {
        return URL::temporarySignedRoute(
            'newsletter.unsubscribe',
            now()->addDays(30),
            ['id' => $this->id, 'hash' => $this->email_hash]
        );
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('status', 'subscribed');
    }
}
