<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageRow;
use App\Models\PageColumn;
use App\Models\PageWidget;
use App\Models\Menu;
use App\Models\MenuItem;

return new class extends Migration
{
    public function up(): void
    {
        $slugs = ['media', 'life-at-prayaag'];

        foreach ($slugs as $slug) {
            // 1. Find or create Page
            $page = Page::firstOrNew(['slug' => $slug]);
            $page->title = 'Life at Prayaag (Media)';
            $page->slug = $slug;
            $page->status = 'published';
            $page->seo = [
                'meta_title' => 'Life at Prayaag — Media & Press Gallery | Prayaag International School',
                'meta_description' => 'Explore life at Prayaag International School, Panipat — dance & music studios, Olympic sports, fine arts, kindergarten play activities, and regional newspaper press clippings.',
            ];
            $page->save();

            // 2. Clean old sections for page
            $existingSections = PageSection::where('page_id', $page->id)->get();
            foreach ($existingSections as $s) {
                foreach ($s->rows as $r) {
                    foreach ($r->columns as $c) {
                        $c->widgets()->delete();
                    }
                    $r->columns()->delete();
                }
                $s->rows()->delete();
            }
            PageSection::where('page_id', $page->id)->delete();

            // 3. Create fresh full-width section with MediaPageWidget
            $section = PageSection::create([
                'page_id'      => $page->id,
                'section_type' => 'full-width',
                'sort_order'   => 0,
                'settings'     => [],
            ]);

            $row = PageRow::create([
                'section_id' => $section->id,
                'sort_order' => 0,
                'settings'   => [],
            ]);

            $column = PageColumn::create([
                'row_id'     => $row->id,
                'width'      => 12,
                'sort_order' => 0,
                'settings'   => [],
            ]);

            PageWidget::create([
                'column_id'   => $column->id,
                'widget_type' => 'media-page',
                'settings'    => [],
                'sort_order'  => 0,
            ]);
        }

        // 4. Update Navigation Menu
        $primaryMenu = Menu::whereIn('location', ['header', 'primary', 'main'])->first() ?? Menu::first();
        if ($primaryMenu) {
            $existingMedia = MenuItem::where('menu_id', $primaryMenu->id)
                ->where(function($q) {
                    $q->where('url', 'like', '%media%')
                      ->orWhere('label', 'like', '%Media%')
                      ->orWhere('label', 'like', '%Life at Prayaag%');
                })->first();

            if (!$existingMedia) {
                $maxOrder = MenuItem::where('menu_id', $primaryMenu->id)->whereNull('parent_id')->max('sort_order') ?? 0;
                MenuItem::create([
                    'menu_id'    => $primaryMenu->id,
                    'label'      => 'Media',
                    'url'        => '/media',
                    'type'       => 'url',
                    'target'     => '_self',
                    'sort_order' => $maxOrder + 1,
                ]);
            } else {
                $existingMedia->label = 'Media';
                $existingMedia->url   = '/media';
                $existingMedia->save();
            }
        }
    }

    public function down(): void
    {
        // Keep page intact
    }
};
