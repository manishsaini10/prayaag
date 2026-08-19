<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class FeeStructurePageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'fee-structure'],
            ['title' => 'Fee Structure', 'status' => 'published']
        );

        $imgBase = '/storage/media/imported/';

        $page->update([
            'title'  => $page->title ?: 'Fee Structure',
            'status' => 'published',
            'seo'    => [
                'title'       => 'Fee Structure 2026-27 | Prayaag International School, Panipat.',
                'description' => 'Explore the fee structure for the academic year 2026-27 at Prayaag International School in Panipat. Find comprehensive details about tuition fees, admission charges, and other associated costs.',
                'og_image'    => $imgBase . '01KTQWY8B3QGJ1J62RQA8XJPSN.jpg',
            ],
        ]);

        $heroImg = $imgBase . '01KTQWY8B3QGJ1J62RQA8XJPSN.jpg';

        $feeRows = '';
        $grades = [
            ['Pre Nursery – I', 7250, 87000],
            ['II – V', 7750, 93000],
            ['VI – VIII', 8000, 96000],
            ['IX – X', 8500, 102000],
            ['XI – XII', 8750, 105000],
        ];
        foreach ($grades as $i => [$grade, $monthly, $annual]) {
            $delay = ($i % 5) + 1;
            $feeRows .= '<tr data-reveal data-reveal-delay="' . $delay . '">'
                . '<td class="fee-grade">' . $grade . '</td>'
                . '<td class="fee-amt">₹' . number_format($monthly) . '</td>'
                . '<td class="fee-amt">₹' . number_format($annual) . '</td></tr>';
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
                                'kicker'          => 'Academic Year 2026-27',
                                'heading'         => 'Fee Structure',
                                'tagline'         => 'Transparent, affordable, and value-driven education at Prayaag International School, Panipat.',
                                'primary_label'   => 'Online Registration →',
                                'primary_url'     => '/registration',
                                'secondary_label' => 'Download Prospectus',
                                'secondary_url'   => '#',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Tuition Fee Table
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Fee Structure 2026-27</span>'
                                    . '<h2 class="sec-title">Tuition Fees</h2>'
                                    . '<p class="sec-sub">Affordable, transparent fee structure designed to provide quality education at every grade level.</p>'
                                    . '</div>'
                                    . '<div class="fee-table-wrap" data-reveal>'
                                    . '<table class="fee-table"><thead><tr>'
                                    . '<th>Grade</th><th>Tuition Fee Per Month</th><th>Total Annual Fee</th>'
                                    . '</tr></thead><tbody>' . $feeRows . '</tbody></table></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Other Fees
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Additional</span>'
                                    . '<h2 class="sec-title">Other Charges</h2>'
                                    . '</div>'
                                    . '<div class="fee-extra-grid" data-reveal>'
                                    . '<div class="fee-extra-card"><div class="fee-extra-label">Registration</div><div class="fee-extra-note">One Time (At Admission)</div><div class="fee-extra-amt">₹1,000</div></div>'
                                    . '<div class="fee-extra-card"><div class="fee-extra-label">Security</div><div class="fee-extra-note">Refundable</div><div class="fee-extra-amt">₹10,000</div></div>'
                                    . '<div class="fee-extra-card"><div class="fee-extra-label">Admission Charges</div><div class="fee-extra-note">One Time</div><div class="fee-extra-amt">₹20,000</div></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Note
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Important</span>'
                                    . '<h2 class="sec-title">Notes &amp; Policies</h2>'
                                    . '</div>'
                                    . '<div class="fee-notes" data-reveal>'
                                    . '<div class="fee-note-card"><p>The above fee structure is inclusive of all academic facilities and does not cover additional activities or special programs.</p></div>'
                                    . '<div class="fee-note-card"><p>Transportation, books and uniform have separate charges.</p></div>'
                                    . '<div class="fee-note-card"><p>The Security fee is refundable at the end of the student\'s tenure at the school.</p></div>'
                                    . '<div class="fee-note-card"><p>All fees are payable in advance, on a quarterly, bi-annual, or annual basis, as per the school\'s policy.</p></div>'
                                    . '<div class="fee-note-card"><p>We believe that quality education is an investment in the future. Prayaag International School, Panipat is committed to providing an enriching learning experience for your child.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 5. CTA
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'admission-cta',
                            'settings' => [
                                'heading'      => 'Ready to Enroll Your Child?',
                                'text'         => 'Give your child the best CBSE education in Panipat. Register online today.',
                                'button_label' => 'Online Registration →',
                                'button_url'   => '/registration',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Fee Structure page created with ' . count($sections) . ' sections.');
    }
}
