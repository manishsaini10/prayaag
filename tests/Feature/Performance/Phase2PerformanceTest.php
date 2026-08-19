<?php

namespace Tests\Feature\Performance;

use App\Core\Menu\MenuManager;
use App\Core\Mess\Services\MessMenuService;
use App\Models\Menu;
use App\Models\Mess\MessMenu;
use App\Notifications\MailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Phase 2 — Performance & Scalability Feature Tests.
 *
 * Tests:
 *  1. HTTP Cache-Control and ETag headers generation.
 *  2. HTTP 304 Not Modified when client provides matching If-None-Match header.
 *  3. MessMenuService query caching.
 *  4. MenuManager tree caching.
 *  5. Automatic cache invalidation on model update.
 *  6. MailNotification implements ShouldQueue.
 */
class Phase2PerformanceTest extends TestCase
{
    use RefreshDatabase;

    // ── 1. HTTP Cache Headers & ETag Validation ──────────────────────────────

    public function test_http_cache_middleware_adds_cache_control_and_etag_headers(): void
    {
        $response = $this->get('/mess-menu');

        $response->assertStatus(200);
        $response->assertHeader('Cache-Control');
        $this->assertStringContainsString('public', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=300', $response->headers->get('Cache-Control'));
        $response->assertHeader('ETag');
    }

    public function test_http_cache_middleware_returns_304_not_modified_on_matching_etag(): void
    {
        $firstResponse = $this->get('/mess-menu');
        $etag = $firstResponse->headers->get('ETag');

        $this->assertNotEmpty($etag);

        // Second request with If-None-Match header
        $secondResponse = $this->withHeaders([
            'If-None-Match' => $etag,
        ])->get('/mess-menu');

        $secondResponse->assertStatus(304);
        $this->assertEmpty($secondResponse->getContent());
    }

    // ── 2. Data Layer Caching ────────────────────────────────────────────────

    public function test_mess_menu_service_caches_active_menu_grouped(): void
    {
        Cache::forget('mess_menu:active_grouped');

        $service = app(MessMenuService::class);
        $data1 = $service->getActiveMenuGrouped();

        $this->assertTrue(Cache::has('mess_menu:active_grouped'));

        // Modify cache content directly to verify service reads from cache
        Cache::put('mess_menu:active_grouped', ['cached_stub' => true], 60);

        $data2 = $service->getActiveMenuGrouped();
        $this->assertEquals(['cached_stub' => true], $data2);
    }

    public function test_mess_menu_save_flushes_cache(): void
    {
        Cache::put('mess_menu:active_grouped', ['cached_stub' => true], 60);

        $menu = MessMenu::create([
            'title'      => 'Test Menu',
            'is_active'  => true,
            'start_date' => now(),
            'end_date'   => now()->addDays(7),
        ]);

        $this->assertFalse(Cache::has('mess_menu:active_grouped'), 'Saving MessMenu should flush mess menu cache');
    }

    public function test_menu_manager_caches_menu_tree(): void
    {
        $menu = Menu::create(['name' => 'Primary Menu', 'location' => 'primary']);

        Cache::forget('theme.menu_tree.primary');

        $manager = app(MenuManager::class);
        $tree = $manager->tree('primary');

        $this->assertTrue(Cache::has('theme.menu_tree.primary'));

        // Modifying menu should flush cache
        $menu->update(['name' => 'Updated Primary Menu']);
        $this->assertFalse(Cache::has('theme.menu_tree.primary'));
    }

    // ── 3. Queue Optimizations ───────────────────────────────────────────────

    public function test_mail_notification_implements_should_queue(): void
    {
        $mail = new MailNotification('Subject', 'Body content');

        $this->assertInstanceOf(ShouldQueue::class, $mail);
        $this->assertSame(3, $mail->tries);
    }
}
