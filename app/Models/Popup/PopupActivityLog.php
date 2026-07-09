<?php

namespace App\Models\Popup;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PopupActivityLog extends Model
{
    use HasUlids;

    protected $table = 'popup_activity_logs';

    protected $fillable = [
        'popup_id', 'action', 'description',
        'old_values', 'new_values',
        'ip_address', 'user_agent', 'causer_id',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function popup(): BelongsTo
    {
        return $this->belongsTo(Popup::class);
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
