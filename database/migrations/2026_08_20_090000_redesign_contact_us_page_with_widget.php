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
        $slugs = ['contact-us', 'contact'];

        foreach ($slugs as $slug) {
            // 1. Find or create Page
            $page = Page::firstOrNew(['slug' => $slug]);
            $page->title = 'Contact Us';
            $page->slug = $slug;
            $page->status = 'published';
            $page->seo = [
                'meta_title' => 'Contact Us | Prayaag International School, Panipat',
                'meta_description' => 'Get in touch with Prayaag International School, Panipat. Find campus address, helpline numbers, visiting hours, Google map directions, and send online admission/general inquiries.',
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

            // 3. Create fresh full-width section with ContactUsPageWidget
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
                'widget_type' => 'contact-us-page',
                'settings'    => [
                    'phone'     => '+91 93507 48851',
                    'email'     => 'mailus@pisp.in',
                    'whatsapp'  => '919350748851',
                    'address'   => 'Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences, NH-44, Panipat-132103, Haryana',
                ],
                'sort_order'  => 0,
            ]);
        }
    }

    public function down(): void
    {
        // Keep page intact
    }
};
