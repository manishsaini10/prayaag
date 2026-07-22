<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSourceSummary extends Model
{
    use HasUlids;

    protected $table = 'analytics_source_summary';

    protected $fillable = [
        'date',
        'source',
        'medium',
        'campaign',
        'visits',
        'leads_generated',
    ];

    protected $casts = [
        'date' => 'date',
        'visits' => 'integer',
        'leads_generated' => 'integer',
    ];
}
