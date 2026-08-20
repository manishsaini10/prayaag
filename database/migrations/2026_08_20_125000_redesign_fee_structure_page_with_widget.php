<?php

use App\Core\Builder\Widgets\FeeStructurePageWidget;
use App\Models\Page;
use App\Models\PageColumn;
use App\Models\PageRow;
use App\Models\PageSection;
use App\Models\PageWidget;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $widgetClass = new FeeStructurePageWidget();
        $defaults    = $widgetClass->defaultSettings();

        $page = Page::firstOrCreate(
            ['slug' => 'fee-structure'],
            [
                'title'   => 'Fee Structure 2026-27',
                'status'  => 'published',
                'seo'     => [
                    'title'       => 'Fee Structure 2026-27 | Prayaag International School, Panipat',
                    'description' => 'Explore the fee structure for the academic year 2026-27 at Prayaag International School in Panipat. Find comprehensive details about tuition fees, admission charges, and other associated costs, ensuring transparency and informed decision-making for parents and students.',
                ],
            ]
        );

        $page->title   = 'Fee Structure 2026-27';
        $page->status  = 'published';
        $page->seo     = [
            'title'       => 'Fee Structure 2026-27 | Prayaag International School, Panipat',
            'description' => 'Explore the fee structure for the academic year 2026-27 at Prayaag International School in Panipat. Find comprehensive details about tuition fees, admission charges, and other associated costs, ensuring transparency and informed decision-making for parents and students.',
        ];
        $page->save();

        // Clean out old sections
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

        // Create Flush Full-Width Section
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
            'widget_type' => 'fee-structure-page',
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
