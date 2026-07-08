<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A custom form. The field schema is a JSON array of definitions; each entry
 * is {key, label, type, required, options, placeholder}.
 */
class Form extends BaseModel
{
    protected $casts = [
        'fields'       => 'array',
        'is_published' => 'boolean',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
