<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slider extends BaseModel
{
    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class)->orderBy('sort_order');
    }

    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', $location);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
