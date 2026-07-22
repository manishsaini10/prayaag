<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsPageSummary extends Model
{
    use HasUlids;

    protected $table = 'analytics_page_summary';

    protected $fillable = [
        'date',
        'page_id',
        'url',
        'views',
        'unique_views',
        'avg_time_on_page',
    ];

    protected $casts = [
        'date' => 'date',
        'views' => 'integer',
        'unique_views' => 'integer',
        'avg_time_on_page' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
