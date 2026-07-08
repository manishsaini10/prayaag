<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class DisclosurePageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'disclosure'],
            ['title' => 'Disclosure', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Mandatory Public Disclosure — Prayaag International School, Panipat',
                'description' => 'View mandatory public disclosure documents for Prayaag International School, Panipat — affiliation, safety certificates, and more.',
                'og_image'    => $b . '01KTQWVF2TH3ES88Y987P5E4QM.webp',
            ],
        ]);

        $heroImg = $b . '01KTQWVF2TH3ES88Y987P5E4QM.webp';

        $baseDocs = 'https://prayaaginternationalschool.com/wp-content/uploads/';

        $groups = [
            [
                'title' => 'Affiliation & Recognition',
                'docs'  => [
                    ['Affiliation Certificate', $baseDocs . '2022/01/Affiliation-Certificate_watermark.pdf'],
                    ['Drinking Water And Sanitary Certificate 2022-23', $baseDocs . '2022/08/Drinking-Water-And-Sanitary-Certificate_watermark-1.pdf'],
                    ['CBSE Mandatory Disclosure', $baseDocs . '2022/01/CBSE-MANDATORY-DISCLOSURE_watermark.pdf'],
                    ['Fire Safety 22-23', $baseDocs . '2022/08/Fire-Safety-22-23_watermark.pdf'],
                    ['NOC by DSE', $baseDocs . '2022/01/NOC-by-DSE_watermark.pdf'],
                    ['Recognition Certificate', $baseDocs . '2022/01/Recognition-Certificates_watermark.pdf'],
                ],
            ],
            [
                'title' => 'Safety & Building',
                'docs'  => [
                    ['Building Safety Certificate', $baseDocs . '2022/08/Building-Safety-Certificate_watermark.pdf'],
                    ['Activity Calendar 2022-23', $baseDocs . '2022/08/Activity-Calender-2022-23_watermark.pdf'],
                    ['Trust Deed', $baseDocs . '2022/08/Trust-Deed_watermark.pdf'],
                    ['Certificate By DEO for Affiliation', $baseDocs . '2022/08/Certificate-by-DEO-for-affiliation_watermark.pdf'],
                ],
            ],
            [
                'title' => 'School Operations',
                'docs'  => [
                    ['Food Menu', $baseDocs . '2022/08/Food-Menu_watermark.pdf'],
                    ['School Management Committee', $baseDocs . '2022/08/School-Management-Committee_watermark.pdf'],
                    ['School Details', $baseDocs . '2022/08/School-Details_watermark.pdf'],
                    ['Activities & Academic Calendar', $baseDocs . '2022/08/Activities-Academic-Calendar_watermark.pdf'],
                    ['General Information', $baseDocs . '2022/08/General-Information_watermark.pdf'],
                    ['Transport Safety Certificate', $baseDocs . '2022/08/Transport-Safety-Certificate_watermark.pdf'],
                ],
            ],
            [
                'title' => 'Other Certificates',
                'docs'  => [
                    ['Non Property Affidavit', $baseDocs . '2022/08/Non-Property-Affidavit_watermark.pdf'],
                    ['Hygienic Certificate', $baseDocs . '2022/08/Hygienic-Certificate_watermark.pdf'],
                ],
            ],
        ];

        $cardsHtml = '';
        foreach ($groups as $group) {
            $cardsHtml .= '<div class="disc-group"><h4 class="disc-group-title">' . $group['title'] . '</h4><div class="disc-list">';
            foreach ($group['docs'] as $doc) {
                $cardsHtml .= '<a href="' . $doc[1] . '" target="_blank" class="disc-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg><span>' . $doc[0] . '</span></a>';
            }
            $cardsHtml .= '</div></div>';
        }

        $sections = [
            // 1. Hero
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'hero',
                            'settings' => [
                                'kicker'          => 'Transparency & Compliance',
                                'heading'         => 'Disclosure',
                                'tagline'         => 'Mandatory public disclosure documents as per CBSE regulations.',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Documents
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Documents</span>'
                                    . '<h2 class="sec-title">Mandatory Public Disclosure</h2>'
                                    . '</div>'
                                    . '<div class="disc-grid" data-reveal>' . $cardsHtml . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Disclosure page created with ' . count($sections) . ' sections.');
    }
}
