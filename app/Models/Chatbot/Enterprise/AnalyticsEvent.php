<?php

namespace App\Models\Chatbot\Enterprise;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'event_type',
        'session_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
