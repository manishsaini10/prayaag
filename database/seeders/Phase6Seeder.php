<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Seeds a primary navigation menu, linking the Home item to the seeded
 * Home page.
 */
class Phase6Seeder extends Seeder
{
    public function run(): void
    {
        $home = Page::where('slug', 'home')->first();

        $menu = Menu::firstOrCreate(
            ['slug' => 'primary'],
            ['name' => 'Primary Menu', 'location' => 'primary']
        );

        if (! $menu->items()->exists()) {
            $menu->items()->create([
                'label'      => 'Home',
                'type'       => $home ? 'page' : 'url',
                'page_id'    => $home?->id,
                'url'        => '/',
                'sort_order' => 0,
            ]);
            $menu->items()->create(['label' => 'About', 'type' => 'url', 'url' => '/about', 'sort_order' => 1]);
            $menu->items()->create(['label' => 'Admissions', 'type' => 'url', 'url' => '/admissions', 'sort_order' => 2]);
            $menu->items()->create(['label' => 'Contact', 'type' => 'url', 'url' => '/contact', 'sort_order' => 3]);
        }
    }
}
