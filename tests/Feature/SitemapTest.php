<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_index_points_to_sub_sitemaps(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<sitemapindex', false)
            ->assertSee('/sitemap-pages.xml', false)
            ->assertSee('/sitemap-images.xml', false);
    }

    public function test_pages_sitemap_lists_published_pages_only(): void
    {
        Page::create(['title' => 'Home', 'slug' => 'home', 'status' => 'published']);
        Page::create(['title' => 'About', 'slug' => 'about', 'status' => 'published']);
        Page::create(['title' => 'Secret', 'slug' => 'secret-draft', 'status' => 'draft']);

        $response = $this->get('/sitemap-pages.xml');

        $response->assertOk();
        $response->assertSee('/about', false);
        $response->assertDontSee('secret-draft', false);
    }

    public function test_noindex_pages_are_excluded_from_the_sitemap(): void
    {
        Page::create(['title' => 'Hidden', 'slug' => 'hidden-page', 'status' => 'published', 'seo' => ['robots_index' => false]]);
        Page::create(['title' => 'Visible', 'slug' => 'visible-page', 'status' => 'published']);

        $this->get('/sitemap-pages.xml')
            ->assertOk()
            ->assertSee('/visible-page', false)
            ->assertDontSee('hidden-page', false);
    }

    public function test_robots_points_to_the_sitemap_and_blocks_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap:')
            ->assertSee('/sitemap.xml')
            ->assertSee('Disallow: /admin');
    }
}
