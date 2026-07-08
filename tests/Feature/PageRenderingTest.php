<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_published_page_renders_end_to_end_over_http(): void
    {
        // A primary menu so the header renders a nav link.
        $menu = Menu::create(['name' => 'Primary', 'slug' => 'primary', 'location' => 'primary']);
        $menu->items()->create(['label' => 'Home', 'type' => 'url', 'url' => '/', 'sort_order' => 0]);

        // A built Home page.
        $page = Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);
        $section = $page->sections()->create(['section_type' => 'hero', 'sort_order' => 0]);
        $row = $section->rows()->create(['sort_order' => 0]);
        $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);
        $column->widgets()->create([
            'widget_type' => 'heading',
            'sort_order'  => 0,
            'settings'    => ['text' => 'Hello Browser', 'level' => 1],
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Hello Browser', false);   // widget output
        $response->assertSee('Home', false);            // menu link in header
        $response->assertSee('pb-section--hero', false);
    }

    public function test_an_unknown_slug_returns_404(): void
    {
        // No page with this slug exists -> firstOrFail -> 404.
        $this->get('/no-such-page')->assertNotFound();
    }
}
