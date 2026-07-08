<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class ClassroomsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'classrooms'],
            ['title' => 'Classrooms', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Classrooms — Prayaag International School, Panipat',
                'description' => 'Explore smart classrooms at Prayaag International School, Panipat. AC rooms with Smart Class systems, 25-30 students per class for effective learning.',
                'og_image'    => $b . '01KTQWYFXJJAEXGQJ429XTFM79.webp',
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
                                'kicker'          => 'Modern Learning Spaces',
                                'heading'         => 'Classrooms',
                                'tagline'         => 'Prayaag International School has spacious classrooms equipped with the latest infrastructure to support the academic program.',
                                'image'           => $b . '01KTQWYFXJJAEXGQJ429XTFM79.webp',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Content
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Where Learning Comes Alive</span>'
                                    . '<h2 class="sec-title">Our Classrooms</h2>'
                                    . '</div>'
                                    . '<div class="class-intro" data-reveal>'
                                    . '<p>Prayaag International School, Panipat has spacious classrooms equipped with latest infrastructure which ensure that our students have the best resources to support the academic program. The school has centralized air-conditioned class rooms equipped with Smart Class (Digital Teaching System) and a strength of 25-30 students for effective learning.</p>'
                                    . '</div>'
                                    . '<div class="class-features" data-reveal>'
                                    . '<div class="cf-card"><div class="cf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18"/></svg></div><h4>Smart Classrooms</h4><p>Centralized AC rooms with Digital Teaching System for interactive learning.</p></div>'
                                    . '<div class="cf-card"><div class="cf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div><h4>Optimal Size</h4><p>25-30 students per class ensures personalized attention and effective learning.</p></div>'
                                    . '<div class="cf-card"><div class="cf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div><h4>Spacious &amp; Airy</h4><p>Well-designed spacious rooms that create a conducive learning environment.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Teaching Methodologies
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Innovative Pedagogy</span>'
                                    . '<h2 class="sec-title">Teaching Methodologies</h2>'
                                    . '</div>'
                                    . '<div class="class-methods" data-reveal>'
                                    . '<div class="cm-item"><span class="cm-num">01</span><span>MCQ\'s and Worksheets</span></div>'
                                    . '<div class="cm-item"><span class="cm-num">02</span><span>Virtual Laboratory of simulations</span></div>'
                                    . '<div class="cm-item"><span class="cm-num">03</span><span>Mind maps</span></div>'
                                    . '<div class="cm-item"><span class="cm-num">04</span><span>Teaching ideas and topic synopsis</span></div>'
                                    . '<div class="cm-item"><span class="cm-num">05</span><span>Real life applications</span></div>'
                                    . '<div class="cm-item"><span class="cm-num">06</span><span>Web links and diagram marker</span></div>'
                                    . '</div>'
                                    . '<div class="class-gallery" data-reveal><img src="' . $b . '01KTQWYH7Y9B1NSRQX0BFKFCSJ.jpg" alt="Classrooms" loading="lazy"></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Classrooms page created with ' . count($sections) . ' sections.');
    }
}
