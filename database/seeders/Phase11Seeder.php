<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageLayout;
use Illuminate\Database\Seeder;

/**
 * Builds a working /contact page with a contact_form widget so the enquiry
 * capture flow is usable end-to-end out of the box.
 */
class Phase11Seeder extends Seeder
{
    public function run(): void
    {
        $layout = PageLayout::where('slug', 'default')->first();

        $page = Page::firstOrCreate(
            ['slug' => 'contact'],
            ['title' => 'Contact', 'layout_id' => $layout?->id, 'status' => 'published']
        );

        if (! $page->sections()->exists()) {
            $section = $page->sections()->create(['section_type' => 'contact', 'sort_order' => 0]);
            $row = $section->rows()->create(['sort_order' => 0]);
            $column = $row->columns()->create(['width' => 12, 'sort_order' => 0]);

            $column->widgets()->create([
                'widget_type' => 'heading',
                'sort_order'  => 0,
                'settings'    => ['text' => 'Get in Touch', 'level' => 1],
            ]);
            $column->widgets()->create([
                'widget_type' => 'contact_form',
                'sort_order'  => 1,
                'settings'    => ['heading' => 'Send us a message', 'button' => 'Send', 'type' => 'contact'],
            ]);
        }
    }
}
