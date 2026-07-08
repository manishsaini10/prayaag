<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class SafetyPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'safety-security'],
            ['title' => 'Safety & Security', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Safety & Security — Prayaag International School, Panipat',
                'description' => 'Prayaag International School ensures a safe and secure campus with CCTV surveillance, trained security personnel, and emergency systems.',
                'og_image'    => $b . '01KWEAM88E5A97A8HC1H95YAX8.webp',
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
                                'kicker'          => 'Safe & Secure Campus',
                                'heading'         => 'Safety & Security',
                                'tagline'         => 'A protected environment ensuring the well-being of everyone on campus.',
                                'image'           => $b . '01KWEAM88E5A97A8HC1H95YAX8.webp',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Content & Features
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Campus Safety</span>'
                                    . '<h2 class="sec-title">Safety & Security</h2>'
                                    . '</div>'
                                    . '<div class="safety-intro" data-reveal>'
                                    . '<p>The school has created a safe and secure environment, ensuring the well-being of everyone on campus. Fences and secure gates are in place around the school perimeter to control access. Entry points to school are monitored and controlled.</p>'
                                    . '</div>'
                                    . '<div class="safety-features" data-reveal>'
                                    . '<div class="sf-card"><div class="sf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div><h4>CCTV Surveillance</h4><p>Cameras installed in key areas such as entrances, classrooms, amphitheatre, swimming pool, common areas, and parking lots.</p></div>'
                                    . '<div class="sf-card"><div class="sf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><h4>Secure Perimeter</h4><p>Fences and secure gates control access around the school perimeter. All entry points are monitored and controlled.</p></div>'
                                    . '<div class="sf-card"><div class="sf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h4>Security Personnel</h4><p>Trained security personnel (male and female) patrol the campus and respond to incidents promptly.</p></div>'
                                    . '<div class="sf-card"><div class="sf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div><h4>Emergency Systems</h4><p>Intercom, PA systems, and emergency notification systems are in place for effective communication during emergencies.</p></div>'
                                    . '</div>'
                                    . '<div class="safety-gallery" data-reveal>'
                                    . '<div class="sg-img"><img src="' . $b . '01KWEAMAJHVDJ4S4ACTK8HA7RP.webp" alt="Medical Facility" loading="lazy"></div>'
                                    . '<div class="sg-img"><img src="' . $b . '01KWEAMCMAE5VB6KHQ7EY6VG9W.jpg" alt="Security Team" loading="lazy"></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Safety & Security page created with ' . count($sections) . ' sections.');
    }
}
