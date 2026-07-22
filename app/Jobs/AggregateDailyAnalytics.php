<?php

namespace App\Jobs;

use App\Core\Analytics\Aggregators\TrafficAggregator;
use App\Core\Analytics\Aggregators\SourceAggregator;
use App\Core\Analytics\Aggregators\ChatbotAggregator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Carbon\Carbon;

class AggregateDailyAnalytics implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected ?string $dateStr;

    public function __construct(?Carbon $date = null)
    {
        $this->dateStr = $date ? $date->toDateString() : null;
    }

    public function handle(
        TrafficAggregator $traffic,
        SourceAggregator $source,
        ChatbotAggregator $chatbot
    ): void {
        $targetDate = $this->dateStr ? Carbon::parse($this->dateStr) : now()->subDay()->startOfDay();

        $traffic->aggregateForDate($targetDate);
        $source->aggregateForDate($targetDate);
        $chatbot->aggregateForDate($targetDate);
    }
}
