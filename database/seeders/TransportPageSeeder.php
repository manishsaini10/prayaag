<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class TransportPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'transportations'],
            ['title' => 'Transportations', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Transportation — Prayaag International School, Panipat',
                'description' => 'Safe and reliable school transport at Prayaag International School, Panipat. AC buses with CCTV, trained drivers, and attendants.',
                'og_image'    => $b . '01KTQWWXGF62TW89BPJN4T592J.webp',
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
                                'kicker'          => 'Safe & Reliable Commute',
                                'heading'         => 'Transportation',
                                'tagline'         => 'The need for safe passage of each and every child to school and back home is of utmost importance to us.',
                                'image'           => $b . '01KTQWWXGF62TW89BPJN4T592J.webp',
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
                                    . '<span class="eyebrow">Safe Journeys</span>'
                                    . '<h2 class="sec-title">School Transport</h2>'
                                    . '</div>'
                                    . '<div class="transpo-content" data-reveal>'
                                    . '<p>To ensure safe travel, the school has its own transport facility which includes a fleet of Air Conditioned school buses that are equipped with CCTVs and are designed as per standards and are manned by trained drivers. To supervise and monitor a Transport Attendant is on board throughout the journey.</p>'
                                    . '</div>'
                                    . '<div class="transpo-features" data-reveal>'
                                    . '<div class="tf-card"><div class="tf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="8" rx="2"/><path d="M7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM17 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></div><h4>AC Buses</h4><p>Fleet of air-conditioned buses for comfortable travel.</p></div>'
                                    . '<div class="tf-card"><div class="tf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h4>CCTV Equipped</h4><p>All buses equipped with CCTV cameras for monitoring.</p></div>'
                                    . '<div class="tf-card"><div class="tf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h4>Trained Drivers</h4><p>Professional drivers with extensive training and experience.</p></div>'
                                    . '<div class="tf-card"><div class="tf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></div><h4>Bus Attendant</h4><p>Attendant on board for supervision throughout the journey.</p></div>'
                                    . '</div>'
                                    . '<div class="transpo-gallery" data-reveal><img src="' . $b . '01KTQWWXGF62TW89BPJN4T592J.webp" alt="School Bus" loading="lazy"></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Transport page created with ' . count($sections) . ' sections.');
    }
}
