<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageSection extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'settings'   => 'array',
        'sort_order' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(PageRow::class, 'section_id')->orderBy('sort_order');
    }
}
