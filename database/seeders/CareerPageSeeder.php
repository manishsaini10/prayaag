<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class CareerPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'career'],
            ['title' => 'Career', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Apply For Job | Career Opportunities at Prayaag International School, Panipat',
                'description' => 'Explore rewarding career opportunities at Prayaag International School in Panipat. Join a passionate and dedicated team committed to shaping the future of education.',
                'og_image'    => $b . '01KTQWX1DBEWN6XZKTWHYKAEB0.webp',
            ],
        ]);

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
                                'kicker'          => 'Join Our Team',
                                'heading'         => 'Career',
                                'tagline'         => 'Be a Part of the Team that Inspires the Next Generation.',
                                'image'           => $b . '01KTQWX1DBEWN6XZKTWHYKAEB0.webp',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Qualifications
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Work With Us</span>'
                                    . '<h2 class="sec-title">Essential Qualifications</h2>'
                                    . '</div>'
                                    . '<div class="career-quals" data-reveal>'
                                    . '<div class="cq-item"><span class="cq-label">Qualification (Teaching)</span><span class="cq-value">Minimum Post Graduation</span></div>'
                                    . '<div class="cq-item"><span class="cq-label">Qualification (F.O.E.)</span><span class="cq-value">Minimum Graduation in any stream with effective communication skills</span></div>'
                                    . '<div class="cq-item"><span class="cq-label">Experience (Teaching)</span><span class="cq-value">Minimum 5 yrs</span></div>'
                                    . '<div class="cq-item"><span class="cq-label">Experience (F.O.E.)</span><span class="cq-value">Minimum 3 yrs</span></div>'
                                    . '<div class="cq-item"><span class="cq-label">Preferred Age</span><span class="cq-value">As per CBSE norms</span></div>'
                                    . '<div class="cq-item"><span class="cq-label">Salary</span><span class="cq-value">No constraints for deserving candidates</span></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Current Openings
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'job-listings',
                            'settings' => [
                                'heading' => 'Current Openings',
                                'limit'   => 10,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. How to Apply
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow" style="color:var(--gold-2)">Take the Next Step</span>'
                                    . '<h2 class="sec-title" style="color:#fff">How to Apply</h2>'
                                    . '</div>'
                                    . '<div class="career-apply" data-reveal>'
                                    . '<div class="ca-card"><div class="ca-icon" style="background:rgba(255,255,255,.1);color:var(--gold-2)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v12H4zM4 6l8 5 8-5"/></svg></div><h4>Send Resume</h4><p>Email your resume to <a href="mailto:hr@pisp.in">hr@pisp.in</a></p></div>'
                                    . '<div class="ca-card"><div class="ca-icon" style="background:rgba(255,255,255,.1);color:var(--gold-2)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div><h4>Visit Us</h4><p><a href="https://prayaaginternationalschool.com">prayaaginternationalschool.com</a></p></div>'
                                    . '<div class="ca-card"><div class="ca-icon" style="background:rgba(255,255,255,.1);color:var(--gold-2)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.08-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.96.37 1.9.74 2.78a2 2 0 0 1-.45 2.11l-1.27 1.27a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.88.37 1.82.62 2.78.74A2 2 0 0 1 22 16.92z"/></svg></div><h4>Call Us</h4><p><a href="tel:0180-2565555">0180-2565555</a>, <a href="tel:0180-2575555">2575555</a>, <a href="tel:9350748851">+91 9350748851</a><br><span style="font-size:.85rem;color:rgba(255,255,255,.6)">Between 09:00 a.m – 03:00 p.m., Mon–Sat</span></p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Career page created with ' . count($sections) . ' sections.');
    }
}
