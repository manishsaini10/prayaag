<?php

namespace App\Core\Analytics\Aggregators;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChatbotAggregator
{
    public function aggregateForDate(Carbon $date): void
    {
        // Currently chatbot metrics are queried dynamically or rolled up via TrafficAggregator.
        // This is a placeholder for future custom chatbot metrics (e.g. intent analysis, sentiment scores).
    }
}
