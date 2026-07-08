<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_builds_a_nested_tree(): void
    {
        $menu = Menu::create(['name' => 'Primary', 'slug' => 'primary', 'location' => 'primary']);
        $parent = $menu->items()->create(['label' => 'Services', 'type' => 'url', 'url' => '/services', 'sort_order' => 0]);
        $menu->items()->create([
            'label'      => 'Design',
            'type'       => 'url',
            'url'        => '/services/design',
            'parent_id'  => $parent->id,
            'sort_order' => 0,
        ]);

        $tree = $menu->tree();

        $this->assertCount(1, $tree);
        $this->assertSame('Services', $tree->first()->label);
        $this->assertCount(1, $tree->first()->children);
        $this->assertSame('Design', $tree->first()->children->first()->label);
    }

    public function test_menu_item_resolves_a_linked_page_url(): void
    {
        $page = Page::create(['title' => 'About', 'slug' => 'about', 'status' => 'published']);
        $menu = Menu::create(['name' => 'Primary', 'slug' => 'primary']);
        $item = $menu->items()->create(['label' => 'About', 'type' => 'page', 'page_id' => $page->id, 'sort_order' => 0]);

        $this->assertSame('/about', $item->fresh()->resolveUrl());
    }
}
