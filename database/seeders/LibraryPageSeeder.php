<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class LibraryPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'library'],
            ['title' => 'Library', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Library — Prayaag International School, Panipat',
                'description' => 'Explore the well-stocked libraries at Prayaag International School, Panipat. Separate libraries for Junior and Senior wings with fiction, non-fiction, periodicals, and more.',
                'og_image'    => $b . '01KTQWYK8ET3FN4J0MM71YNAEM.jpg',
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
                                'kicker'          => 'A World of Knowledge',
                                'heading'         => 'Library',
                                'tagline'         => 'The school boasts of two well-stocked libraries, separately for Juniors and Seniors where all students feel welcome and encouraged to grow and learn.',
                                'image'           => $b . '01KTQWYK8ET3FN4J0MM71YNAEM.jpg',
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
                                    . '<span class="eyebrow">Reading & Discovery</span>'
                                    . '<h2 class="sec-title">Our Library</h2>'
                                    . '</div>'
                                    . '<div class="lib-content" data-reveal>'
                                    . '<p>The school boasts of two well-stocked libraries, separately for Juniors and Seniors where all students feel welcome and encouraged to grow and learn from the range of books with an impressive index of titles, covering fiction and non-fiction, periodicals, magazines, and newspapers. Students are encouraged to make full use of these facilities in order to inculcate a love for books and the habit of reading from an early age.</p>'
                                    . '</div>'
                                    . '<div class="lib-features" data-reveal>'
                                    . '<div class="lf-card"><h4>Junior Library</h4><p>Age-appropriate books, picture books, early readers, and activity books for young learners.</p></div>'
                                    . '<div class="lf-card"><h4>Senior Library</h4><p>Extensive collection of fiction, non-fiction, reference books, and competitive exam materials.</p></div>'
                                    . '<div class="lf-card"><h4>Digital Resources</h4><p>E-books, online encyclopedias, and digital learning resources accessible from school.</p></div>'
                                    . '<div class="lf-card"><h4>Reading Programs</h4><p>Regular reading competitions, book clubs, and author interactions to promote reading culture.</p></div>'
                                    . '</div>'
                                    . '<div class="lib-gallery" data-reveal><img src="' . $b . '01KTQWWVDF6QJR4NHJH12MASPJ.webp" alt="School Library" loading="lazy"></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Library page created with ' . count($sections) . ' sections.');
    }
}
