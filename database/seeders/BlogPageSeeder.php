<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class BlogPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'blog'],
            ['title' => 'Blog', 'status' => 'published']
        );

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Blog — Prayaag International School, Panipat',
                'description' => 'Read the latest news, updates, and stories from Prayaag International School, Panipat.',
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
                                'kicker'          => 'Stay Updated',
                                'heading'         => 'Blog',
                                'tagline'         => 'Read the latest news, updates, events, and stories from Prayaag International School.',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Blog posts
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'latest_posts',
                            'settings' => [
                                'layout' => 'grid',
                                'limit'  => 12,
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Blog page created with ' . count($sections) . ' sections.');
    }
}
