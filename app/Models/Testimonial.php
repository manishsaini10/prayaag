<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Testimonial extends BaseModel
{
    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    protected static function booted(): void
    {
        parent::booted();

        // sort_order is NOT NULL with no DB default — auto-assign the next
        // value (and default published) so the admin form can leave them blank.
        static::creating(function (self $t) {
            if ($t->sort_order === null || $t->sort_order === '') {
                $t->sort_order = (int) (static::query()->max('sort_order') ?? 0) + 1;
            }
            if ($t->is_published === null) {
                $t->is_published = true;
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
