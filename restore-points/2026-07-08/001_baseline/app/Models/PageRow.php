<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageRow extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'settings'   => 'array',
        'sort_order' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'section_id');
    }

    public function columns(): HasMany
    {
        return $this->hasMany(PageColumn::class, 'row_id')->orderBy('sort_order');
    }
}
