<?php

namespace App\Console\Commands\Popup;

use App\Models\Popup\PopupAnalytics;
use Illuminate\Console\Command;

class CleanupAnalytics extends Command
{
    protected $signature = 'popup:cleanup-analytics {--days=365 : Retention days}';
    protected $description = 'Clean up old popup analytics data';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deleted = PopupAnalytics::where('occurred_at', '<', $cutoff)->delete();

        $this->info("Deleted {$deleted} old analytics records (before {$cutoff->format('Y-m-d')}).");
        return Command::SUCCESS;
    }
}
