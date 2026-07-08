<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Menu extends BaseModel
{
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id');
    }

    public function scopeLocation(Builder $query, string $location): Builder
    {
        return $query->where('location', $location);
    }

    /**
     * Build the nested item tree (each item gets a `children` relation).
     *
     * @return Collection<int, MenuItem>
     */
    public function tree(): Collection
    {
        $all = $this->items()->get();

        $build = function ($parentId) use (&$build, $all): Collection {
            return $all->where('parent_id', $parentId)
                ->sortBy('sort_order')
                ->map(function (MenuItem $item) use (&$build) {
                    $item->setRelation('children', $build($item->id));

                    return $item;
                })
                ->values();
        };

        return $build(null);
    }
}
