<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageColumn extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'settings'   => 'array',
        'width'      => 'integer',
        'sort_order' => 'integer',
    ];

    public function row(): BelongsTo
    {
        return $this->belongsTo(PageRow::class, 'row_id');
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(PageWidget::class, 'column_id')->orderBy('sort_order');
    }
}
