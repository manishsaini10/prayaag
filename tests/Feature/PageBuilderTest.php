<?php

namespace Tests\Feature;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\WidgetRegistry;
use App\Models\Page;
use App\Models\PageLayout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_registry_renders_a_registered_widget(): void
    {
        $html = app(WidgetRegistry::class)->render('heading', ['text' => 'Hello', 'level' => 2]);

        $this->assertStringContainsString('<h2', $html);
        $this->assertStringContainsString('Hello', $html);
    }

    public function test_renderer_outputs_the_full_widget_tree(): void
    {
        $layout = PageLayout::create(['name' => 'Default', 'slug' => 'default', 'type' => 'default']);
        $page = Page::create([
            'title'     => 'Home',
            'slug'      => 'home',
            'layout_id' => $layout->id,
            'status'    => 'published',
        ]);

        $section = $page->sections()->create(['section_type' => 'hero', 'sort_order' => 0]);
        $row = $section->rows()->create(['sort_order' => 0]);
        $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);
        $column->widgets()->create([
            'widget_type' => 'heading',
            'sort_order'  => 0,
            'settings'    => ['text' => 'Hello World', 'level' => 1],
        ]);

        $html = app(PageRenderer::class)->render($page->fresh());

        $this->assertStringContainsString('Hello World', $html);
        $this->assertStringContainsString('pb-section--hero', $html);
        $this->assertStringContainsString('pb-col--12', $html);
    }

    public function test_html_widget_strips_scripts(): void
    {
        $html = app(WidgetRegistry::class)->render('html', [
            'html' => '<script>alert(1)</script><p>safe content</p>',
        ]);

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString('safe content', $html);
    }
}
