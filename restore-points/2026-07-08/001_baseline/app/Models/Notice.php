<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Notice extends BaseModel
{
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_pinned' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
