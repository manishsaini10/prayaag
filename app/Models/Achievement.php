<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Achievement extends BaseModel
{
    protected $casts = [
        'is_published' => 'boolean',
        'year'         => 'integer',
        'sort_order'   => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
