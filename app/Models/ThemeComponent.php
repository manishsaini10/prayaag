<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Reusable theme building block: headers, footers, sidebars, mega menus,
 * and global components. `content` holds the builder tree.
 */
class ThemeComponent extends BaseModel
{
    protected $casts = [
        'content'    => 'array',
        'settings'   => 'array',
        'is_default' => 'boolean',
    ];

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
}
