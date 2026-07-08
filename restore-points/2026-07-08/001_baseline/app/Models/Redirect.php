<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Redirect extends BaseModel
{
    protected $casts = [
        'is_active'   => 'boolean',
        'status_code' => 'integer',
        'hits'        => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
