<?php

namespace App\Models\Popup;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupSchedule extends Model
{
    use HasUlids;

    protected $table = 'popup_schedules';

    protected $fillable = [
        'popup_id', 'type', 'starts_at', 'ends_at',
        'schedule_data', 'timezone', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'schedule_data' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function isActive(): bool
    {
        if (!$this->is_active) return false;
        $now = now($this->timezone);
        if ($this->starts_at && $now->lt($this->starts_at)) return false;
        if ($this->ends_at && $now->gt($this->ends_at)) return false;
        return true;
    }
}
