<?php

namespace Tests\Feature;

use App\Core\Seo\SeoManager;
use App\Core\Settings\SettingsManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_generates_title_description_canonical_and_robots(): void
    {
        $seo = app(SeoManager::class)->resolve(
            title: 'About',
            slug: 'about',
            seo: [],
            html: '<p>Hello world from the about page.</p>',
        );

        $this->assertSame('About | Prayaag International School', $seo->title);
        $this->assertStringContainsString('Hello world', $seo->description);
        $this->assertSame(url('/about'), $seo->canonical);
        $this->assertSame('index, follow', $seo->robots);
        $this->assertSame('article', $seo->ogType);
    }

    public function test_per_page_overrides_win_over_auto(): void
    {
        $seo = app(SeoManager::class)->resolve(
            title: 'Ignored',
            slug: 'x',
            seo: ['title' => 'Custom Title', 'description' => 'Custom desc', 'robots_index' => false],
            html: '<p>Body text.</p>',
        );

        $this->assertSame('Custom Title', $seo->title);
        $this->assertSame('Custom desc', $seo->description);
        $this->assertSame('noindex, follow', $seo->robots);
    }

    public function test_first_content_image_becomes_the_og_image(): void
    {
        $seo = app(SeoManager::class)->resolve(
            title: 'Gallery',
            slug: 'gallery',
            seo: [],
            html: '<p>x</p><img src="https://example.com/a.jpg"><img src="https://example.com/b.jpg">',
        );

        $this->assertSame('https://example.com/a.jpg', $seo->ogImage);
        $this->assertSame('summary_large_image', $seo->twitterCard);
    }

    public function test_home_uses_site_name_and_tagline(): void
    {
        app(SettingsManager::class)->set('site_tagline', 'Life begins here');

        $seo = app(SeoManager::class)->resolve(title: 'Home', slug: '', seo: [], html: '', isHome: true);

        $this->assertStringContainsString('Prayaag International School', $seo->title);
        $this->assertStringContainsString('Life begins here', $seo->title);
        $this->assertSame('website', $seo->ogType);
    }

    public function test_no_field_is_ever_empty_on_the_core_meta(): void
    {
        $seo = app(SeoManager::class)->resolve(title: 'Bare', slug: 'bare', seo: [], html: '');

        $this->assertNotSame('', $seo->title);
        $this->assertNotSame('', $seo->canonical);
        $this->assertNotSame('', $seo->robots);
        $this->assertNotSame('', $seo->ogTitle);
    }
}
