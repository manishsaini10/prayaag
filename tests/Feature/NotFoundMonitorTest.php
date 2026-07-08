<?php

namespace Tests\Feature;

use App\Core\Seo\NotFoundLogger;
use App\Models\NotFoundLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class NotFoundMonitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_a_missing_path_and_increments_hits(): void
    {
        $logger = app(NotFoundLogger::class);

        $logger->log(Request::create('/ghost-page', 'GET'));
        $logger->log(Request::create('/ghost-page', 'GET'));

        $row = NotFoundLog::where('path', 'ghost-page')->first();
        $this->assertNotNull($row);
        $this->assertSame(2, $row->hits);
    }

    public function test_ignores_asset_and_admin_probes(): void
    {
        $logger = app(NotFoundLogger::class);

        $logger->log(Request::create('/styles/app.css', 'GET'));
        $logger->log(Request::create('/admin/secret', 'GET'));
        $logger->log(Request::create('/wp-login.php', 'GET'));

        $this->assertSame(0, NotFoundLog::count());
    }

    public function test_unknown_url_returns_404_over_http(): void
    {
        $this->get('/this-page-does-not-exist')->assertNotFound();
    }
}
