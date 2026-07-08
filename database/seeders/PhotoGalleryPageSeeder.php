<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class PhotoGalleryPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'photo-gallery'],
            ['title' => 'Photo Gallery', 'status' => 'published']
        );

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Photo Gallery — Prayaag International School, Panipat',
                'description' => 'Browse the photo gallery of Prayaag International School, Panipat. See campus life, events, sports, and academic moments.',
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
                                'kicker'          => 'Moments at Prayaag',
                                'heading'         => 'Photo Gallery',
                                'tagline'         => 'Browse through moments captured at Prayaag International School — events, sports, campus life, and academic celebrations.',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Gallery
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'gallery',
                            'settings' => [
                                'slug'  => 'campus-life',
                                'limit' => 50,
                                'layout' => 'grid',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Photo Gallery page created with ' . count($sections) . ' sections.');
    }
}
