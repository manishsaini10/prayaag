<?php

namespace Tests\Feature;

use App\Models\NotFoundLog;
use App\Models\Popup\Popup;
use App\Models\Popup\PopupAnalytics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_maintenance_reports_without_deleting_by_default(): void
    {
        $popup = Popup::create([
            'title' => 'Maintenance Popup',
            'slug' => 'maintenance-popup',
            'type' => 'modal',
            'status' => 'active',
            'structure' => ['blocks' => []],
        ]);

        PopupAnalytics::create([
            'popup_id' => $popup->id,
            'event_type' => 'view',
            'occurred_at' => now()->subDays(120),
        ]);

        NotFoundLog::create([
            'path' => 'old-page',
            'hits' => 3,
            'resolved' => true,
            'last_seen_at' => now()->subDays(120),
        ]);

        $this->artisan('cms:maintenance --days=90')
            ->expectsOutput('Retention window: 90 days')
            ->expectsOutputToContain('Popup analytics older than')
            ->expectsOutputToContain('Resolved 404 logs older than')
            ->assertExitCode(0);

        $this->assertDatabaseCount('popup_analytics', 1);
        $this->assertDatabaseCount('not_found_logs', 1);
    }

    public function test_maintenance_prunes_popup_analytics_only_when_requested(): void
    {
        $popup = Popup::create([
            'title' => 'Prune Popup',
            'slug' => 'prune-popup',
            'type' => 'modal',
            'status' => 'active',
            'structure' => ['blocks' => []],
        ]);

        PopupAnalytics::create([
            'popup_id' => $popup->id,
            'event_type' => 'view',
            'occurred_at' => now()->subDays(120),
        ]);

        $this->artisan('cms:maintenance --days=90 --prune-analytics')
            ->expectsOutput('Deleted 1 old popup analytics rows.')
            ->assertExitCode(0);

        $this->assertDatabaseCount('popup_analytics', 0);
    }
}
