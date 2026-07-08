<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoMetaTest extends TestCase
{
    use RefreshDatabase;

    private function buildAboutPage(): void
    {
        $menu = Menu::create(['name' => 'Primary', 'slug' => 'primary', 'location' => 'primary']);
        $menu->items()->create(['label' => 'Home', 'type' => 'url', 'url' => '/', 'sort_order' => 0]);

        $page = Page::create(['title' => 'About', 'slug' => 'about', 'status' => 'published']);
        $section = $page->sections()->create(['section_type' => 'section', 'sort_order' => 0]);
        $row = $section->rows()->create(['sort_order' => 0]);
        $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);
        $column->widgets()->create(['widget_type' => 'heading', 'sort_order' => 0, 'settings' => ['text' => 'About Our School', 'level' => 1]]);
        $column->widgets()->create(['widget_type' => 'text', 'sort_order' => 1, 'settings' => ['content' => 'We are a leading CBSE school in Panipat focused on holistic education.']]);
    }

    public function test_page_emits_complete_non_empty_meta_tags(): void
    {
        $this->buildAboutPage();

        $res = $this->get('/about');

        $res->assertOk();
        $res->assertSee('About | Prayaag International School', false);   // auto title
        $res->assertSee('name="description"', false);
        $res->assertSee('leading CBSE school in Panipat', false);        // auto description from content
        $res->assertSee('rel="canonical"', false);
        $res->assertSee('name="robots" content="index, follow"', false);
        $res->assertSee('property="og:title"', false);
        $res->assertSee('name="twitter:card"', false);
    }

    public function test_page_emits_structured_data_graph(): void
    {
        $this->buildAboutPage();

        $res = $this->get('/about');

        $res->assertOk();
        $res->assertSee('application/ld+json', false);
        $res->assertSee('EducationalOrganization', false);
        $res->assertSee('"WebSite"', false);
        $res->assertSee('"WebPage"', false);
        $res->assertSee('BreadcrumbList', false);
        $res->assertSee('SearchAction', false);
    }

    public function test_noindex_override_changes_the_robots_tag(): void
    {
        $this->buildAboutPage();
        Page::where('slug', 'about')->update(['seo' => json_encode(['robots_index' => false])]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('content="noindex, follow"', false);
    }
}
