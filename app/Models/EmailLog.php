<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class EmailLog extends BaseModel
{
    protected $table = 'email_logs';

    protected $casts = [
        'to_address' => 'encrypted',
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'sent');
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        return $query->where('module', $module);
    }
}
