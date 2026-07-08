<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'settings'   => 'array',
        'sort_order' => 'integer',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /** Resolve the href: a linked page's slug, or the explicit URL. */
    public function resolveUrl(): string
    {
        if ($this->type === 'page' && $this->page) {
            return '/' . ltrim($this->page->slug, '/');
        }

        return $this->url ?: '#';
    }
}
