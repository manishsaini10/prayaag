<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class DownloadsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'downloads'],
            ['title' => 'Downloads', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Downloads — Prayaag International School, Panipat',
                'description' => 'Download syllabus, holiday homework, date sheets, and academic resources from Prayaag International School, Panipat.',
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
                                'heading'         => 'Download',
                                'tagline'         => 'Access syllabus, holiday homework, date sheets, and other academic downloads.',
                                'image'           => $b . '01KTQWWVDF6QJR4NHJH12MASPJ.webp',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Downloads widget
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'downloads',
                            'settings' => [
                                'limit' => 50,
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Downloads page created with ' . count($sections) . ' sections.');
    }
}
