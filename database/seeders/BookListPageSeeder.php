<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class BookListPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'book-list'],
            ['title' => 'Book List', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Book List — Prayaag International School, Panipat',
                'description' => 'Download book lists for academic sessions at Prayaag International School, Panipat. PDFs available for all classes.',
                'og_image'    => $b . '01KTQWWVDF6QJR4NHJH12MASPJ.webp',
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
                                'kicker'          => 'Academic Resources',
                                'heading'         => 'Book Lists',
                                'tagline'         => 'Download prescribed book lists for all classes and academic sessions.',
                                'image'           => $b . '01KTQWWVDF6QJR4NHJH12MASPJ.webp',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Yearly book lists
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Download PDFs</span>'
                                    . '<h2 class="sec-title">Book List 2023-24</h2>'
                                    . '</div>'
                                    . '<div class="bl-grid" data-reveal>'
                                    . '<a href="' . $b . '01KWEC7TJ1TTT07DZ7V53ZJDNZ.pdf" class="bl-card" target="_blank" rel="noopener"><div class="bl-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><h4>Book List 2023-24</h4><span class="bl-meta">PDF &middot; Complete List</span></a>'
                                    . '<a href="' . $b . '01KWEC7XX8ZKAZZBFMDABDW2Y8.pdf" class="bl-card" target="_blank" rel="noopener"><div class="bl-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><h4>Book List 2019-20</h4><span class="bl-meta">PDF &middot; Complete List</span></a>'
                                    . '<a href="' . $b . '01KWEC7ZJDGXGZEK0GD6DFG80H.pdf" class="bl-card" target="_blank" rel="noopener"><div class="bl-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div><h4>Book List 2020-21</h4><span class="bl-meta">PDF &middot; Complete List</span></a>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Class-wise book lists
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">By Class</span>'
                                    . '<h2 class="sec-title">Book List 2021-22</h2>'
                                    . '</div>'
                                    . '<div class="bl-grid" data-reveal>'
                                    . '<a href="' . $b . '01KWEC80K4JEA39S0Y46M56M1C.pdf" class="bl-card" target="_blank" rel="noopener"><div class="bl-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><h4>Pre-Nursery to III</h4><span class="bl-meta">PDF &middot; 2021-22</span></a>'
                                    . '<a href="' . $b . '01KWEC81Z1MABY3TPKFQYA5SSW.pdf" class="bl-card" target="_blank" rel="noopener"><div class="bl-icon" style="background:#f0e8fc;color:#6b3fa0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><h4>Classes IV &ndash; VIII</h4><span class="bl-meta">PDF &middot; 2021-22</span></a>'
                                    . '<a href="' . $b . '01KWEC838TE2TX71Y8M4BFK8MH.pdf" class="bl-card" target="_blank" rel="noopener"><div class="bl-icon" style="background:#e8fcf0;color:#1a7a4a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><h4>Classes IX &ndash; XII</h4><span class="bl-meta">PDF &middot; 2021-22</span></a>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Book List page created with ' . count($sections) . ' sections.');
    }
}
