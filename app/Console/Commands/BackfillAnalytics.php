<?php

namespace App\Console\Commands;

use App\Core\Analytics\Aggregators\TrafficAggregator;
use App\Core\Analytics\Aggregators\SourceAggregator;
use App\Core\Analytics\Aggregators\ChatbotAggregator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BackfillAnalytics extends Command
{
    protected $signature = 'analytics:backfill {--from= : Start date YYYY-MM-DD} {--to= : End date YYYY-MM-DD}';
    protected $description = 'Backfill daily analytics aggregates for a given date range';

    public function handle(): int
    {
        $fromStr = $this->option('from');
        $toStr = $this->option('to');

        $startDate = $fromStr ? Carbon::parse($fromStr) : now()->subDays(30);
        $endDate = $toStr ? Carbon::parse($toStr) : now();

        $currentDate = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        if ($currentDate->gt($end)) {
            $this->error('The start date (--from) must be before or equal to the end date (--to).');
            return self::FAILURE;
        }

        $this->info("Starting backfill from {$currentDate->toDateString()} to {$end->toDateString()}...");

        $traffic = app(TrafficAggregator::class);
        $source = app(SourceAggregator::class);
        $chatbot = app(ChatbotAggregator::class);

        $bar = $this->output->createProgressBar((int) $currentDate->diffInDays($end) + 1);
        $bar->start();

        while ($currentDate->lte($end)) {
            $traffic->aggregateForDate($currentDate);
            $source->aggregateForDate($currentDate);
            $chatbot->aggregateForDate($currentDate);

            $bar->advance();
            $currentDate->addDay();
        }

        $bar->finish();
        $this->newLine(2);

        // Bust all analytics caches to ensure stats update immediately
        Cache::flush();

        $this->info('Backfill completed successfully!');
        return self::SUCCESS;
    }
}
