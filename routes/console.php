<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();

// Instagram Feed (Meta Graph API)
Schedule::command('instagram:sync-all')->hourly()->withoutOverlapping();
Schedule::command('instagram:refresh-tokens')->daily()->withoutOverlapping();

// Popup Builder
Schedule::command('popup:cleanup-analytics --days=365')->daily()->withoutOverlapping();
