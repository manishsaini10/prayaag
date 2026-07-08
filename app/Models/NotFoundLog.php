<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class NotFoundLog extends BaseModel
{
    protected $casts = [
        'hits'         => 'integer',
        'resolved'     => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('resolved', false);
    }
}
