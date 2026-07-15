<?php

namespace App\Console\Commands;

use App\Models\NotFoundLog;
use App\Models\Popup\PopupAnalytics;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CmsMaintenance extends Command
{
    protected $signature = 'cms:maintenance
        {--days=90 : Retention window used for report and pruning}
        {--prune-analytics : Delete popup analytics older than the retention window}
        {--clear-views : Clear compiled Blade views}';

    protected $description = 'Report and optionally prune generated CMS runtime data';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $cutoff = now()->subDays($days);

        $oldPopupAnalytics = PopupAnalytics::where('occurred_at', '<', $cutoff)->count();
        $oldResolved404s = NotFoundLog::where('resolved', true)
            ->where('last_seen_at', '<', $cutoff)
            ->count();

        $this->line("Retention window: {$days} days");
        $this->line("Popup analytics older than {$cutoff->toDateString()}: {$oldPopupAnalytics}");
        $this->line("Resolved 404 logs older than {$cutoff->toDateString()}: {$oldResolved404s}");

        if ($this->option('prune-analytics')) {
            $deleted = PopupAnalytics::where('occurred_at', '<', $cutoff)->delete();
            $this->info("Deleted {$deleted} old popup analytics rows.");
        } else {
            $this->comment('Analytics pruning skipped. Add --prune-analytics to delete old popup analytics.');
        }

        if ($this->option('clear-views')) {
            Artisan::call('view:clear');
            $this->info(trim(Artisan::output()) ?: 'Compiled Blade views cleared.');
        } else {
            $this->comment('Compiled view cleanup skipped. Add --clear-views to refresh Blade cache.');
        }

        return self::SUCCESS;
    }
}
