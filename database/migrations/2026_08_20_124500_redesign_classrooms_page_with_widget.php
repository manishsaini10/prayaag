<?php

use App\Core\Builder\Widgets\ClassroomsPageWidget;
use App\Models\Page;
use App\Models\PageColumn;
use App\Models\PageRow;
use App\Models\PageSection;
use App\Models\PageWidget;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $widgetClass = new ClassroomsPageWidget();
        $defaults    = $widgetClass->defaultSettings();

        $page = Page::firstOrCreate(
            ['slug' => 'classrooms'],
            [
                'title'   => 'Classrooms',
                'status'  => 'published',
                'seo'     => [
                    'title'       => 'Smart Classrooms & Learning Spaces | Prayaag International School',
                    'description' => 'Explore centralized air-conditioned, digitally-enabled smart classrooms at Prayaag International School, Panipat — designed with ergonomic seating, interactive digital boards, and 1:25 student ratio.',
                ],
            ]
        );

        $page->title   = 'Classrooms';
        $page->status  = 'published';
        $page->seo     = [
            'title'       => 'Smart Classrooms & Learning Spaces | Prayaag International School',
            'description' => 'Explore centralized air-conditioned, digitally-enabled smart classrooms at Prayaag International School, Panipat — designed with ergonomic seating, interactive digital boards, and 1:25 student ratio.',
        ];
        $page->save();

        // Remove old sections and rebuild with modern widget
        foreach ($page->sections as $section) {
            foreach ($section->rows as $row) {
                foreach ($row->columns as $col) {
                    $col->widgets()->delete();
                }
                $row->columns()->delete();
            }
            $section->rows()->delete();
        }
        $page->sections()->delete();

        // 1. Create Clean Full-Width Section
        $section = PageSection::create([
            'page_id'    => $page->id,
            'type'       => 'flush',
            'sort_order' => 1,
            'settings'   => ['_full_width' => true],
        ]);

        $row = PageRow::create([
            'section_id' => $section->id,
            'sort_order' => 1,
            'settings'   => [],
        ]);

        $col = PageColumn::create([
            'row_id'     => $row->id,
            'width'      => 12,
            'sort_order' => 1,
            'settings'   => [],
        ]);

        PageWidget::create([
            'column_id'   => $col->id,
            'widget_type' => 'classrooms-page',
            'sort_order'  => 1,
            'settings'    => $defaults,
        ]);

        // Clear page cache
        app(\App\Core\Builder\PageRenderer::class)->forget($page);
    }

    public function down(): void
    {
        // Keep page intact
    }
};
