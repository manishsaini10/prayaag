<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * A single admin notification. Global to staff (no per-user fan-out) with a
 * single read_at marker — appropriate for a small admin team. Generated from
 * incoming events (enquiries, applications, subscribers) and system messages.
 */
class AdminNotification extends Model
{
    use HasUlids;

    protected $table = 'admin_notifications';
    protected $guarded = ['id'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /** Convenience factory. Never lets a logging failure break the caller. */
    public static function record(string $type, string $title, array $attributes = []): ?self
    {
        return rescue(fn () => static::create(array_merge([
            'type'  => $type,
            'level' => 'info',
            'title' => $title,
        ], $attributes)), null, false);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
