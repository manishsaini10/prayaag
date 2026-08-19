<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageRow;
use App\Models\PageColumn;
use App\Models\PageWidget;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Find or create the book-list Page
        $page = Page::firstOrNew(['slug' => 'book-list']);
        $page->title = 'Book List & Curriculum Syllabus';
        $page->slug = 'book-list';
        $page->status = 'published';
        $page->seo = [
            'meta_title' => 'Prescribed Book Lists & Syllabus | Prayaag International School',
            'meta_description' => 'Official CBSE compliant prescribed textbook lists, subject syllabus, and stationery guidelines for Academic Session 2025-26 at Prayaag International School Panipat.',
        ];
        $page->save();

        // 2. Clean old sections for book-list
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

        // 3. Create fresh PageSection with BookListWidget
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
            'widget_type' => 'book-list',
            'settings'    => [
                'current_session' => '2025–26',
                'whatsapp_number' => '919350748851',
                'academic_phone'  => '+919350748851',
            ],
            'sort_order'  => 0,
        ]);
    }

    public function down(): void
    {
        // Intentionally keep page intact
    }
};
