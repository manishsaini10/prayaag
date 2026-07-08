<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageWidgetSetting extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    public function widget(): BelongsTo
    {
        return $this->belongsTo(PageWidget::class, 'widget_id');
    }
}
