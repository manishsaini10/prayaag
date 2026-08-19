<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageRow;
use App\Models\PageColumn;
use App\Models\PageWidget;
use App\Models\Download;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Seed or Update Downloads Table with all files from live website
        $items = [
            // PT-1 Syllabus
            [
                'title' => 'PT1 XII Commerce Syllabus Compiled',
                'description' => 'Class XII Commerce Academic Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/PT1-XII-COMMERCE-SYLLABUS-COMPILED.pdf',
                'file_type' => 'pdf',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'PT-1 Syllabus XII (Science)',
                'description' => 'Class XII Science Academic Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/PT-1-SYLLABUS-XII-SCIENCE.pdf',
                'file_type' => 'pdf',
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'PT1 XII Humanities Syllabus Compiled',
                'description' => 'Class XII Humanities Academic Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/PT1-XII-HUMANITIES-SYLLABUS-COMPILED.pdf',
                'file_type' => 'pdf',
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'title' => '10th PT-1 Syllabus',
                'description' => 'Class X CBSE Board Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/10TH-PT-1-SYLLABUS.pdf',
                'file_type' => 'pdf',
                'sort_order' => 4,
                'is_published' => true,
            ],
            [
                'title' => '9th PT-1 Syllabus',
                'description' => 'Class IX Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/9TH-PT-1-SYLLABUS.docx',
                'file_type' => 'docx',
                'sort_order' => 5,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 8 PT-1 Syllabus',
                'description' => 'Class VIII Middle Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/grade-8-pt1-syllabus-22-23.pdf',
                'file_type' => 'pdf',
                'sort_order' => 6,
                'is_published' => true,
            ],
            [
                'title' => 'Class 7 PT-1 Syllabus',
                'description' => 'Class VII Middle Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/cl7-pt1-syllabus.pdf',
                'file_type' => 'pdf',
                'sort_order' => 7,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 5 PT-1 Syllabus',
                'description' => 'Class V Primary Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/Grade-5-pt-1-syllabus.pdf',
                'file_type' => 'pdf',
                'sort_order' => 8,
                'is_published' => true,
            ],
            [
                'title' => 'PT-1 Grade 4 Syllabus',
                'description' => 'Class IV Primary Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/PT-1-Grade-4-SYLLABUS.pdf',
                'file_type' => 'pdf',
                'sort_order' => 9,
                'is_published' => true,
            ],
            [
                'title' => 'PT-1 Grade 3 Syllabus',
                'description' => 'Class III Primary Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/PT-1-Grade-3-SYLLABUS.pdf',
                'file_type' => 'pdf',
                'sort_order' => 10,
                'is_published' => true,
            ],
            [
                'title' => 'PT-1 Grade 2 Syllabus',
                'description' => 'Class II Primary Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/PT-1-Grade-2-SYLLABUS.pdf',
                'file_type' => 'pdf',
                'sort_order' => 11,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 1 PT1 Syllabus',
                'description' => 'Class I Primary Wing Periodic Test 1 Syllabus',
                'category' => 'PT-1 Syllabus',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/06/Grade-1-PT1-Syllabus-2022-23.pdf',
                'file_type' => 'pdf',
                'sort_order' => 12,
                'is_published' => true,
            ],

            // Holiday Homework
            [
                'title' => 'Grade 12 Science HHW',
                'description' => 'Class XII Science Vacation Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/GRADE-12-SCIENCE-HHW-COMPILED.pdf',
                'file_type' => 'pdf',
                'sort_order' => 13,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 12 Humanities HHW',
                'description' => 'Class XII Arts / Humanities Vacation Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/GRADE-12-HUMANITIES-HHW-COMPLIED.pdf',
                'file_type' => 'pdf',
                'sort_order' => 14,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 12 Commerce HHW',
                'description' => 'Class XII Commerce Vacation Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/GRADE-12-COMMERCE-HHW-COMPILED.pdf',
                'file_type' => 'pdf',
                'sort_order' => 15,
                'is_published' => true,
            ],
            [
                'title' => 'Compiled Holiday Homework Grade 10',
                'description' => 'Class X CBSE Board Holiday Homework Assignment',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/Compiled-Holiday-Homework-Grade-10.pdf',
                'file_type' => 'pdf',
                'sort_order' => 16,
                'is_published' => true,
            ],
            [
                'title' => 'Compiled Holiday Homework IX',
                'description' => 'Class IX Holiday Homework Assignment',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/Compiled-holiday-homework-IX.pdf',
                'file_type' => 'pdf',
                'sort_order' => 17,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 8 Holiday Homework',
                'description' => 'Class VIII Middle Wing Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/Grade-8-Holiday-Homework-2022-23.pdf',
                'file_type' => 'pdf',
                'sort_order' => 18,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 7 Holiday Homework',
                'description' => 'Class VII Middle Wing Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/Grade-7-Holiday-Homework-2022-23.pdf',
                'file_type' => 'pdf',
                'sort_order' => 19,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 6 HHW',
                'description' => 'Class VI Middle Wing Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/6-hhw-1.pdf',
                'file_type' => 'pdf',
                'sort_order' => 20,
                'is_published' => true,
            ],
            [
                'title' => 'Grade V HHW',
                'description' => 'Class V Primary Wing Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/5-hhw-copy-copy.pdf',
                'file_type' => 'pdf',
                'sort_order' => 21,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 4 Holiday Homework',
                'description' => 'Class IV Primary Wing Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/Grade-4-HOLIDAY-HOMEWORK.pdf',
                'file_type' => 'pdf',
                'sort_order' => 22,
                'is_published' => true,
            ],
            [
                'title' => 'Grade 3 Holiday Homework',
                'description' => 'Class III Primary Wing Holiday Homework Dossier',
                'category' => 'Holiday Homework',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/Grade-3-HOLIDAY-HOMEWORK-Copy.pdf',
                'file_type' => 'pdf',
                'sort_order' => 23,
                'is_published' => true,
            ],

            // Mess & Dining
            [
                'title' => 'School Food & Mess Menu',
                'description' => 'Nutritional Student Mess & Dining Menu Plan',
                'category' => 'Mess & Dining',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2022/05/FOOD-MENU-FROM-9TH-MAY-TO-22-MAY-2022.pdf',
                'file_type' => 'pdf',
                'sort_order' => 24,
                'is_published' => true,
            ],

            // Mandatory Public Disclosure
            [
                'title' => 'School Fee Structure 2026–27',
                'description' => 'Official Annual School Fee Schedule',
                'category' => 'Mandatory Disclosure',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2026/03/Fee_Structure_2026-27.pdf',
                'file_type' => 'pdf',
                'sort_order' => 25,
                'is_published' => true,
            ],
            [
                'title' => 'Transport Fee Structure 2026–27',
                'description' => 'Official Transport & Bus Fee Schedule',
                'category' => 'Mandatory Disclosure',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2026/03/Transport_Fee_Structure-2026-27.pdf',
                'file_type' => 'pdf',
                'sort_order' => 26,
                'is_published' => true,
            ],
            [
                'title' => 'Mandatory Public Disclosure (Appendix IX)',
                'description' => 'CBSE Appendix IX Complete Compliance Disclosure',
                'category' => 'Mandatory Disclosure',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2026/07/Mandatory-Public-Disclosure.pdf',
                'file_type' => 'pdf',
                'sort_order' => 27,
                'is_published' => true,
            ],
            [
                'title' => 'Building Safety Certificate (BSC)',
                'description' => 'PWD Structural Safety Certificate',
                'category' => 'Mandatory Disclosure',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2024/06/BSC.pdf',
                'file_type' => 'pdf',
                'sort_order' => 28,
                'is_published' => true,
            ],
            [
                'title' => 'Transport Safety Certificate (TSC)',
                'description' => 'School Bus & Transport Safety Fitness Certificate',
                'category' => 'Mandatory Disclosure',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2024/06/TSC.pdf',
                'file_type' => 'pdf',
                'sort_order' => 29,
                'is_published' => true,
            ],
            [
                'title' => 'Fire Safety Certificate (FSC)',
                'description' => 'Haryana Fire & Emergency Services NOC',
                'category' => 'Mandatory Disclosure',
                'file' => 'https://prayaaginternationalschool.com/wp-content/uploads/2024/06/FSC.pdf',
                'file_type' => 'pdf',
                'sort_order' => 30,
                'is_published' => true,
            ],
        ];

        foreach ($items as $item) {
            Download::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }

        // 2. Find or create the downloads Page
        $page = Page::firstOrNew(['slug' => 'downloads']);
        $page->title = 'Academic Downloads & Resources';
        $page->slug = 'downloads';
        $page->status = 'published';
        $page->seo = [
            'meta_title' => 'Academic Downloads, Syllabus & Circulars | Prayaag International School',
            'meta_description' => 'Download official CBSE syllabus, Periodic Test 1 documents, holiday homework, mess menu, and mandatory public disclosure certificates from Prayaag International School Panipat.',
        ];
        $page->save();

        // 3. Clean old sections for downloads page
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

        // 4. Create fresh full-width section with DownloadsPageWidget
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
            'widget_type' => 'downloads-page',
            'settings'    => [
                'session'         => '2025–26',
                'whatsapp_number' => '919350748851',
                'phone'           => '+919350748851',
            ],
            'sort_order'  => 0,
        ]);
    }

    public function down(): void
    {
        // Keep data intact
    }
};
