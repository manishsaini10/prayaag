<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageWidget extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'settings'   => 'array',
        'sort_order' => 'integer',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(PageColumn::class, 'column_id');
    }

    /** Optional normalized key/value settings (see migration note). */
    public function settingRows(): HasMany
    {
        return $this->hasMany(PageWidgetSetting::class, 'widget_id');
    }
}
