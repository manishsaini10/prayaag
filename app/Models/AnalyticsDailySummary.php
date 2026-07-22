<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class AnalyticsDailySummary extends Model
{
    use HasUlids;

    protected $table = 'analytics_daily_summary';

    protected $fillable = [
        'date',
        'total_views',
        'unique_visitors',
        'new_visitors',
        'returning_visitors',
        'avg_session_duration',
        'bounce_rate',
        'total_leads',
        'total_chatbot_conversations',
    ];

    protected $casts = [
        'date' => 'date',
        'total_views' => 'integer',
        'unique_visitors' => 'integer',
        'new_visitors' => 'integer',
        'returning_visitors' => 'integer',
        'avg_session_duration' => 'integer',
        'bounce_rate' => 'decimal:2',
        'total_leads' => 'integer',
        'total_chatbot_conversations' => 'integer',
    ];
}
