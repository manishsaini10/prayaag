<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageRow;
use App\Models\PageColumn;
use App\Models\PageWidget;
use App\Models\Download;
use App\Models\Menu;
use App\Models\MenuItem;

return new class extends Migration
{
    public function up(): void
    {
        $slugs = ['mandatory-public-disclosure', 'disclosure'];

        foreach ($slugs as $slug) {
            // 1. Find or create Page
            $page = Page::firstOrNew(['slug' => $slug]);
            $page->title = 'Mandatory Public Disclosure';
            $page->slug = $slug;
            $page->status = 'published';
            $page->seo = [
                'meta_title' => 'Mandatory Public Disclosure | Prayaag International School, Panipat',
                'meta_description' => 'Official CBSE Appendix IX Mandatory Public Disclosure for Prayaag International School, Panipat. Access school fee structure, safety certificates, affiliation status, and governance dossiers.',
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

            // 3. Create fresh full-width section with DisclosurePageWidget
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
                'widget_type' => 'disclosure-page',
                'settings'    => [],
                'sort_order'  => 0,
            ]);
        }

        // 4. Seed all Mandatory Disclosure documents into `downloads` table
        $docs = [
            // Key Documents
            ['title' => 'Fee Structure (2026–27)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Fee_Structure_2026-27.pdf', 'description' => 'Official Annual Fee Structure 2026-27'],
            ['title' => 'Transport Fee Structure (2026–27)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Transport_Fee_Structure-2026-27.pdf', 'description' => 'Transportation Charges 2026-27'],
            ['title' => 'Mandatory Public Disclosure (Appendix IX)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Mandatory-Public-Disclosure.pdf', 'description' => 'CBSE Appendix IX Complete Compliance Dossier'],
            ['title' => 'Building Safety Certificate (BSC)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/BSC.pdf', 'description' => 'PWD Structural Safety Certificate 2024'],
            ['title' => 'Transport Safety Certificate (TSC)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/TSC.pdf', 'description' => 'School Bus Fleet Safety Certificate 2024'],
            ['title' => 'Fire Safety Certificate (FSC)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/FSC.pdf', 'description' => 'Fire & Emergency Services Clearance 2024'],

            // All Disclosures
            ['title' => 'Affiliation Certificate', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Afflitation-Certificate.pdf', 'description' => 'CBSE Affiliation Grant Letter'],
            ['title' => 'Drinking Water & Sanitary Certificate', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Drinking-Water-And-Sanitary-Certificate-2022-23.pdf', 'description' => 'Health & Hygiene Sanitation Clearance'],
            ['title' => 'CBSE Mandatory Disclosure General Details', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/CBSE-MANDATORY-DISCLOSURE.pdf', 'description' => 'General Information & Affiliation Info'],
            ['title' => 'Fire Safety Certificate (2022–23)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Fire-safety-22-23.pdf', 'description' => 'Fire Safety Certificate Archive'],
            ['title' => 'NOC by DSE Haryana', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/NOC-BY-DSE.pdf', 'description' => 'No Objection Certificate by Directorate of School Education'],
            ['title' => 'Recognition Certificate', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/RecognitionCertificate.pdf', 'description' => 'Haryana Education Dept. Recognition Certificate'],
            ['title' => 'Building Safety Certificate (Archive)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Building-Safety-certificate.pdf', 'description' => 'Building Stability Certificate Archive'],
            ['title' => 'Activity Calendar (2022–23)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Activity-Calander-2022-23.pdf', 'description' => 'Academic & Co-Curricular Calendar'],
            ['title' => 'Trust Deed', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Trust-Deed.pdf', 'description' => 'Educational Society Trust Deed'],
            ['title' => 'Certificate by DEO for Affiliation', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Certificate-By-DEO-for-affliation.pdf', 'description' => 'District Education Officer Affiliation Certificate'],
            ['title' => 'Food Menu & Mess Nutrition', 'category' => 'Mess & Dining', 'file' => '/docs/FOOD-MENU-FROM-9TH-MAY-TO-22-MAY-2022.pdf', 'description' => 'School Mess Dining Menu'],
            ['title' => 'School Management Committee (SMC)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/School-Management-Committee.pdf', 'description' => 'SMC Members & Governance List'],
            ['title' => 'School Details & Infrastructure', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/School-Detail.pdf', 'description' => 'Campus Land & Infrastructure Overview'],
            ['title' => 'Activities & Academic Calendar Overview', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Activity-Academic-Calendar.pdf', 'description' => 'Curriculum Planning & Activities Calendar'],
            ['title' => 'General Information Dossier', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/GENERAL-INFORMATION.pdf', 'description' => 'Institutional Profile & General Information'],
            ['title' => 'Transport Safety Certificate (Archive)', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Transport-Safety-Certificate.pdf', 'description' => 'Vehicle Fleet Safety Archive'],
            ['title' => 'Non-Proprietary Character Affidavit', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/Non-Proprietry-affidavit.pdf', 'description' => 'Legal Affidavit on Non-Proprietary Character'],
            ['title' => 'Hygienic Certificate', 'category' => 'Mandatory Public Disclosure', 'file' => '/docs/HygenicCertificate.pdf', 'description' => 'Sanitation & Health Inspection Certificate'],
        ];

        foreach ($docs as $d) {
            Download::updateOrCreate(
                ['title' => $d['title']],
                [
                    'category'    => $d['category'],
                    'file'        => $d['file'],
                    'description' => $d['description'],
                    'is_active'   => true,
                ]
            );
        }

        // 5. Update primary/header menu if available
        $primaryMenu = Menu::whereIn('location', ['header', 'primary', 'main'])->first() ?? Menu::first();
        if ($primaryMenu) {
            $existingParent = MenuItem::where('menu_id', $primaryMenu->id)
                ->where(function($q) {
                    $q->where('url', 'like', '%disclosure%')
                      ->orWhere('label', 'like', '%Disclosure%');
                })->first();

            if (!$existingParent) {
                $maxOrder = MenuItem::where('menu_id', $primaryMenu->id)->whereNull('parent_id')->max('sort_order') ?? 0;
                $parentItem = MenuItem::create([
                    'menu_id'    => $primaryMenu->id,
                    'label'      => 'Mandatory Public Disclosure',
                    'url'        => '/mandatory-public-disclosure',
                    'type'       => 'url',
                    'target'     => '_self',
                    'sort_order' => $maxOrder + 1,
                ]);

                // Sub-items
                $subItems = [
                    ['label' => 'Fee Structure', 'url' => '/docs/Fee_Structure_2026-27.pdf'],
                    ['label' => 'Transport Fee', 'url' => '/docs/Transport_Fee_Structure-2026-27.pdf'],
                    ['label' => 'Mandatory Public Disclosure', 'url' => '/mandatory-public-disclosure'],
                    ['label' => 'Building Safety Certificate', 'url' => '/docs/BSC.pdf'],
                    ['label' => 'Transport Safety Certificate', 'url' => '/docs/TSC.pdf'],
                    ['label' => 'Fire Safety Certificate', 'url' => '/docs/FSC.pdf'],
                ];

                foreach ($subItems as $idx => $sub) {
                    MenuItem::create([
                        'menu_id'    => $primaryMenu->id,
                        'parent_id'  => $parentItem->id,
                        'label'      => $sub['label'],
                        'url'        => $sub['url'],
                        'type'       => 'url',
                        'target'     => str_ends_with($sub['url'], '.pdf') ? '_blank' : '_self',
                        'sort_order' => $idx + 1,
                    ]);
                }
            } else {
                $existingParent->label = 'Mandatory Public Disclosure';
                $existingParent->url   = '/mandatory-public-disclosure';
                $existingParent->save();
            }
        }
    }

    public function down(): void
    {
        // Keep page intact
    }
};
