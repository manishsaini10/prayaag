<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class InstagramAccount extends BaseModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'followers'   => 'integer',
        'media_count' => 'integer',
        'expires_at'  => 'datetime',
        'last_sync'   => 'datetime',
        'settings'    => 'array',
    ];

    public function scopeConnected(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 7): Builder
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays($days));
    }

    public function media(): HasMany
    {
        return $this->hasMany(InstagramMedia::class, 'instagram_account_id');
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(InstagramSyncLog::class, 'account_id');
    }

    public function setTokenAttribute(string $value): void
    {
        $this->attributes['token'] = Crypt::encryptString($value);
    }

    public function getTokenAttribute(?string $value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function setRefreshTokenAttribute(?string $value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getRefreshTokenAttribute(?string $value): ?string
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExpiringSoon(int $days = 7): bool
    {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= $days;
    }

    public function markAsDisconnected(): void
    {
        $this->update(['status' => 'disconnected']);
    }
}
