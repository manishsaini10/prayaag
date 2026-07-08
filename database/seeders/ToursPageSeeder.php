<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class ToursPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'tours-and-excursions'],
            ['title' => 'Tours and Excursions', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Tours and Excursions — Prayaag International School, Panipat',
                'description' => 'Prayaag International School organizes tours and excursions to expand horizons. International educational exchange programs for global exposure.',
                'og_image'    => $b . '01KWEBD5DV5S888ZVB19SP12ZD.jpg',
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
                                'kicker'          => 'Explore Beyond Boundaries',
                                'heading'         => 'Tours and Excursions',
                                'tagline'         => 'Perfect way to expand one\'s horizon and explore new things beyond boundaries.',
                                'image'           => $b . '01KWEBD5DV5S888ZVB19SP12ZD.jpg',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Intro + 3 Pillars
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Learning Beyond Classrooms</span>'
                                    . '<h2 class="sec-title">Tours and Excursions</h2>'
                                    . '</div>'
                                    . '<div class="tours-intro" data-reveal>'
                                    . '<p>At PRAYAAG, we believe that tours and excursions are the perfect way to expand one\'s horizon. The students are persuaded to acquire knowledge and explore new things not just within the boundaries but also beyond them. Every now and then International Educational Exchange Program is organized for the global exposure for the children.</p>'
                                    . '</div>'
                                    . '<div class="tours-pillars" data-reveal>'
                                    . '<div class="tp-card"><div class="tp-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/></svg></div><h4>Educational Tours</h4><p>Students explore historical, scientific, and cultural destinations that complement classroom learning with real-world experiences.</p></div>'
                                    . '<div class="tp-card"><div class="tp-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div><h4>Cultural Exchange</h4><p>International Educational Exchange Programs provide global exposure, helping students understand diverse cultures and perspectives.</p></div>'
                                    . '<div class="tp-card"><div class="tp-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h4>Adventure & Team Building</h4><p>Outdoor excursions build camaraderie, leadership, and resilience through challenging yet fun group activities.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Gallery
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Memories</span>'
                                    . '<h2 class="sec-title">Excursion Gallery</h2>'
                                    . '</div>'
                                    . '<div class="tours-gallery" data-reveal>'
                                    . '<div class="tg-item"><img src="' . $b . '01KWEBD680R1ZPBKS9Y4JQ7DXC.jpg" alt="School Trip" loading="lazy"></div>'
                                    . '<div class="tg-item"><img src="' . $b . '01KWEBD7P9SXVKQPANJBHV263Y.jpg" alt="School Trip" loading="lazy"></div>'
                                    . '<div class="tg-item"><img src="' . $b . '01KWEBD6ZHPVT6BSTXGGBA7ZM0.jpg" alt="School Trip" loading="lazy"></div>'
                                    . '<div class="tg-item"><img src="' . $b . '01KWEBD8DR6KGPSDS3ZFTKKNNV.jpg" alt="School Trip" loading="lazy"></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Tours & Excursions page created with ' . count($sections) . ' sections.');
    }
}
