<?php

namespace Tests\Feature;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Core\Builder\WidgetRegistry;
use App\Models\Page;
use App\Models\PageLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_breadcrumb_with_context_renders_trail(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'simple',
            'separator' => 'chevron',
            'home_text' => 'Home',
            'show_home' => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About Us', $html);
        $this->assertStringContainsString('<svg', $html);
    }

    public function test_breadcrumb_without_home_link(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'simple',
            'separator' => 'chevron',
            'home_text' => 'Home',
            'show_home' => false,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringNotContainsString('Home', $html);
        $this->assertStringContainsString('About Us', $html);
    }

    public function test_breadcrumb_gradient_style_renders(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'gradient',
            'separator' => 'slash',
            'home_text' => 'Home',
            'show_home' => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About Us', $html);
        $this->assertStringContainsString('/', $html);
    }

    public function test_breadcrumb_modern_style_renders(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'modern',
            'separator' => 'dot',
            'home_text' => 'Home',
            'show_home' => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About Us', $html);
        $this->assertStringContainsString('&#8226;', $html);
    }

    public function test_breadcrumb_minimal_style_renders(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'minimal',
            'separator' => 'arrow',
            'home_text' => 'Home',
            'show_home' => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About Us', $html);
        $this->assertStringContainsString('&#8594;', $html);
    }

    public function test_breadcrumb_with_image_style_has_image_background(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'            => 'with-image',
            'background_image' => 'https://example.com/bg.jpg',
            'overlay_opacity'  => 30,
            'home_text'        => 'Home',
            'show_home'        => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('background-image', $html);
        $this->assertStringContainsString('https://example.com/bg.jpg', $html);
        $this->assertStringContainsString('rgba(0,0,0,0.3)', $html);
        $this->assertStringContainsString('About Us', $html);
    }

    public function test_breadcrumb_with_video_style_has_video_background(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'           => 'with-video',
            'background_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'overlay_opacity' => 50,
            'home_text'       => 'Home',
            'show_home'       => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('youtube.com/embed', $html);
        $this->assertStringContainsString('dQw4w9WgXcQ', $html);
        $this->assertStringContainsString('rgba(0,0,0,0.5)', $html);
        $this->assertStringContainsString('About Us', $html);
    }

    public function test_breadcrumb_with_video_falls_back_to_image_when_no_video(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'            => 'with-video',
            'background_image' => 'https://example.com/fallback.jpg',
            'background_video' => '',
            'overlay_opacity'  => 40,
            'home_text'        => 'Home',
            'show_home'        => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('background-image', $html);
        $this->assertStringContainsString('fallback.jpg', $html);
        $this->assertStringContainsString('About Us', $html);
    }

    public function test_breadcrumb_renders_with_real_page_via_renderer(): void
    {
        $layout = PageLayout::create(['name' => 'Default', 'slug' => 'default', 'type' => 'default']);
        $page = Page::create([
            'title'     => 'About Us',
            'slug'      => 'about-us',
            'layout_id' => $layout->id,
            'status'    => 'published',
        ]);
        app(PageTreeService::class)->sync($page, [
            [
                'type' => 'flush',
                'rows' => [
                    [
                        'columns' => [
                            [
                                'width' => 12,
                                'widgets' => [
                                    [
                                        'type' => 'breadcrumb',
                                        'settings' => [
                                            'style'     => 'simple',
                                            'separator' => 'chevron',
                                            'home_text' => 'Home',
                                            'show_home' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $html = app(PageRenderer::class)->render($page->fresh());

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About Us', $html);
    }

    public function test_breadcrumb_home_page_shows_only_home(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'simple',
            'separator' => 'chevron',
            'home_text' => 'Home',
            'show_home' => true,
        ], [
            'page_slug'  => 'home',
            'page_title' => 'Home',
        ]);

        $this->assertStringContainsString('Home', $html);
    }

    public function test_breadcrumb_hidden_on_mobile(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'       => 'simple',
            'show_mobile' => false,
            'home_text'   => 'Home',
            'show_home'   => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('hidden sm:flex', $html);
    }

    public function test_breadcrumb_center_alignment(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'       => 'simple',
            'alignment'   => 'center',
            'home_text'   => 'Home',
            'show_home'   => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('justify-center', $html);
    }

    public function test_breadcrumb_returns_empty_for_no_context(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'simple',
            'home_text' => 'Home',
            'show_home' => false,
        ], []);

        $this->assertSame('', $html);
    }

    public function test_breadcrumb_all_separator_styles(): void
    {
        $chevron = app(WidgetRegistry::class)->render('breadcrumb', [
            'style' => 'simple', 'separator' => 'chevron', 'home_text' => 'Home', 'show_home' => true,
        ], ['page_slug' => 'a', 'page_title' => 'A']);
        $this->assertStringContainsString('<svg', $chevron);

        $slash = app(WidgetRegistry::class)->render('breadcrumb', [
            'style' => 'simple', 'separator' => 'slash', 'home_text' => 'Home', 'show_home' => true,
        ], ['page_slug' => 'a', 'page_title' => 'A']);
        $this->assertStringContainsString('/</span>', $slash);
    }

    public function test_breadcrumb_custom_min_height(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'            => 'with-image',
            'background_image' => 'https://example.com/bg.jpg',
            'min_height'       => '150px',
            'home_text'        => 'Home',
            'show_home'        => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('min-height: 150px', $html);
    }

    public function test_breadcrumb_max_width_full(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'     => 'with-image',
            'background_image' => 'https://example.com/bg.jpg',
            'max_width' => 'full',
            'home_text' => 'Home',
            'show_home' => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('w-full', $html);
    }

    public function test_breadcrumb_max_width_contained(): void
    {
        $html = app(WidgetRegistry::class)->render('breadcrumb', [
            'style'            => 'with-image',
            'background_image' => 'https://example.com/bg.jpg',
            'max_width'        => '5xl',
            'width_style'      => 'box',
            'home_text'        => 'Home',
            'show_home'        => true,
        ], [
            'page_slug'  => 'about-us',
            'page_title' => 'About Us',
        ]);

        $this->assertStringContainsString('max-w-5xl', $html);
        $this->assertStringContainsString('mx-auto', $html);
        $this->assertStringContainsString('container', $html);
    }
}
