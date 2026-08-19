<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class AdmissionsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'admissions'],
            ['title' => 'Admissions', 'status' => 'published']
        );

        $page->update([
            'title'  => $page->title ?: 'Admissions',
            'status' => 'published',
            'seo'    => [
                'title'       => 'Admissions Open for Session 2026-27 in Prayaag: Apply Now',
                'description' => 'Admissions open for 2026-27 at Prayaag International School, Panipat! From Pre-School to Secondary, unlock quality education. Apply now!',
                'og_image'    => '/storage/media/imported/01KTQWY72Y3TYZBS69V2BARWSG.webp',
            ],
        ]);

        $imgBase = '/storage/media/imported/';
        $heroImg = $imgBase . '01KTQWY72Y3TYZBS69V2BARWSG.webp';    // Admin-Prayaag-International-School
        $poolImg = $imgBase . '01KTQWX2CES8JNV36GMCVYVB7T.webp';    // Children-playing-at-swimimg-pool
        $assemblyImg = $imgBase . '01KTQWVHR3X6CR7FY4NP3WK2SH.webp'; // Morning Assembly

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
                                'kicker'          => '★ Admission Open 2026-27',
                                'heading'         => 'Best CBSE School in Panipat',
                                'tagline'         => 'Give Your Child The Best Future at Prayaag International School Panipat. Limited Seats Available – Apply Now.',
                                'primary_label'   => 'Online Registration →',
                                'primary_url'     => '/registration',
                                'secondary_label' => 'Contact Us →',
                                'secondary_url'   => '/contact-us',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Admission Process
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'admission-process',
                            'settings' => [
                                'eyebrow' => 'Admissions',
                                'heading' => 'How To Apply?',
                                'sub'     => 'Welcome to Prayaag International School, Panipat. Our admission process is meticulously designed to ensure a seamless and transparent journey.',
                                'steps'   => [
                                    ['title' => 'Campus Tour', 'text' => 'Explore our exquisite campus and experience our exceptional facilities. Visit to understand our mission and distinctive educational style.'],
                                    ['title' => 'Interview', 'text' => 'A comprehensive half-hour interview with your family. An opportunity for you to pose inquiries and share insights about your family.'],
                                    ['title' => 'Prospectus & Form', 'text' => 'Procure the prospectus and registration form from our admission counselor. Submit with attested photocopies of stipulated documents.'],
                                    ['title' => 'Document Verification', 'text' => 'Our Admission Office will verify submitted documents, followed by a significant meeting with the Principal to delve deeper into the process.'],
                                    ['title' => 'Entrance Test', 'text' => 'For Class I onward, an entrance test will be administered. Admissions based on merit and performance in the entrance test.'],
                                    ['title' => 'Admission Decision', 'text' => 'Admissions granted based on test and personal interaction involving the student and parents. Process unfolds at the onset of the academic session.'],
                                ],
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Stats
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'stats',
                            'settings' => [
                                'eyebrow' => 'Our Strength',
                                'heading' => 'Creating Intellectual Environment for Students',
                                'items'   => [
                                    ['value' => 184, 'suffix' => '+', 'label' => 'Teacher & Staff'],
                                    ['value' => 96, 'suffix' => '+', 'label' => 'Events Held'],
                                    ['value' => 1100, 'suffix' => '+', 'label' => 'Happy Parents'],
                                    ['value' => 43, 'suffix' => '+', 'label' => 'Lab Projects'],
                                ],
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Eligibility & Documents
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 6,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" style="text-align:left;margin-left:0" data-reveal>'
                                    . '<span class="eyebrow">Requirements</span>'
                                    . '<h2 class="sec-title">Eligibility Criteria</h2>'
                                    . '</div>'
                                    . '<div class="admission-section" data-reveal>'
                                    . '<p class="admission-ed-note">Registration for admission to all classes starts from December.</p>'
                                    . '<h4 style="color:var(--navy);margin-top:1.5rem">Age Criteria (as on 1st April)</h4>'
                                    . '<table class="admission-table"><thead><tr><th>Class</th><th>Min. Age</th></tr></thead><tbody>'
                                    . '<tr><td>Pre-Nursery</td><td>2.5 Years</td></tr>'
                                    . '<tr><td>Nursery</td><td>3.5 Years</td></tr>'
                                    . '<tr><td>K.G.</td><td>4.5 Years</td></tr>'
                                    . '<tr><td>Grade I</td><td>6 Years</td></tr>'
                                    . '</tbody></table>'
                                    . '<h4 style="color:var(--navy);margin-top:1.5rem">Test Pattern</h4>'
                                    . '<ul class="admission-list">'
                                    . '<li><strong>NUR – I</strong> — One on One Interaction</li>'
                                    . '<li><strong>II-IX</strong> — Written test of English, Math &amp; General Awareness</li>'
                                    . '<li><strong>XII (All streams)</strong> — Aptitude Test</li>'
                                    . '</ul>'
                                    . '</div>',
                            ],
                        ]],
                    ], [
                        'width'   => 6,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" style="text-align:left;margin-left:0" data-reveal>'
                                    . '<span class="eyebrow">Documents</span>'
                                    . '<h2 class="sec-title">Documents Required</h2>'
                                    . '</div>'
                                    . '<div class="admission-section" data-reveal>'
                                    . '<p>Duly filled in application form (attached with prospectus) to be submitted along with:</p>'
                                    . '<ul class="admission-list">'
                                    . '<li><strong>4</strong> Photographs of the student</li>'
                                    . '<li><strong>2</strong> Photographs of the parents</li>'
                                    . '<li>Original TC from previous School</li>'
                                    . '<li>Proof of Residence</li>'
                                    . '<li>Aadhar Card</li>'
                                    . '<li>Birth Certificate (issued by the civic body)</li>'
                                    . '</ul>'
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
                                'heading'      => 'Ready to Join Prayaag International School?',
                                'text'         => 'Give your child the Prayaag advantage. Register online today or visit our campus for a personal tour. Limited seats available.',
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

        $this->command?->info('Admissions page created with ' . count($sections) . ' sections.');
    }
}
