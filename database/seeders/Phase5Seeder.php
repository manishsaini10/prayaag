<?php

namespace Database\Seeders;

use App\Core\Settings\SettingsManager;
use App\Models\Page;
use App\Models\PageLayout;
use App\Models\ThemeComponent;
use Illuminate\Database\Seeder;

/**
 * Seeds a default layout, a built-out Home page (hero heading + intro text),
 * default header/footer theme components, and a few theme settings. Idempotent.
 */
class Phase5Seeder extends Seeder
{
    public function run(): void
    {
        $layout = PageLayout::firstOrCreate(
            ['slug' => 'default'],
            ['name' => 'Default', 'type' => 'default']
        );

        $page = Page::firstOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home', 'layout_id' => $layout->id, 'status' => 'published']
        );

        if (! $page->sections()->exists()) {
            $section = $page->sections()->create(['section_type' => 'hero', 'sort_order' => 0]);
            $row = $section->rows()->create(['sort_order' => 0]);
            $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);

            $column->widgets()->create([
                'widget_type' => 'heading',
                'sort_order'  => 0,
                'settings'    => ['text' => 'Welcome to Demo School', 'level' => 1],
            ]);

            $column->widgets()->create([
                'widget_type' => 'text',
                'sort_order'  => 1,
                'settings'    => ['content' => 'Excellence in education, built on a fully dynamic CMS.'],
            ]);
        }

        ThemeComponent::firstOrCreate(
            ['type' => 'header', 'slug' => 'main'],
            ['name' => 'Main Header', 'is_default' => true, 'content' => []]
        );

        ThemeComponent::firstOrCreate(
            ['type' => 'footer', 'slug' => 'main'],
            ['name' => 'Main Footer', 'is_default' => true, 'content' => []]
        );

        $settings = app(SettingsManager::class);
        $settings->set('theme_primary_color', '#1d4ed8', 'string', 'general');
        $settings->set('theme_font_family', 'Inter, sans-serif', 'string', 'general');
        $settings->set('theme_container_width', 1200, 'integer', 'general');
    }
}
