<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupAnalytics extends Model
{
    use HasFactory;
    protected $table = 'popup_analytics';

    protected $fillable = [
        'popup_id', 'variation_id', 'event_type', 'session_id',
        'ip_address', 'user_agent', 'url', 'referrer',
        'country', 'device_type', 'browser', 'os',
        'utm_source', 'utm_medium', 'utm_campaign',
        'extra_data', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'extra_data' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function scopeByEvent($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('occurred_at', today());
    }

    public function scopeBetween($query, $from, $to)
    {
        return $query->whereBetween('occurred_at', [$from, $to]);
    }
}
