<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MediaPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'media'],
            ['title' => 'Media', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Media — Prayaag International School, Panipat',
                'description' => 'Explore photos and media from Prayaag International School, Panipat — dance, music, sports, arts, fun activities, and news.',
                'og_image'    => $b . '01KTQWX2CES8JNV36GMCVYVB7T.webp',
            ],
        ]);

        $groups = [
            'Dance & Music' => [
                $b . '01KTQWX3EVS831JCSW2YWTB0VN.jpg',
                $b . '01KTQWX4CYZC1DRGC0N63T67ZV.webp',
                $b . '01KTQWX5HQKBR5Z0Y8HZYAY5CT.webp',
            ],
            'Sports' => [
                $b . '01KTQWX6NZBN954M3QK6GBK5W0.jpg',
                $b . '01KTQWX7SDBEP94AWKQVJH2953.jpg',
                $b . '01KTQWX9BVVAN25SKC3GH0EAGA.jpg',
                $b . '01KTQWVJZHDNW6MVSWZBEW36F5.webp',
            ],
            'Arts & Craft' => [
                $b . '01KTQWXANTT10YVJ0SKD5PQ9JN.webp',
                $b . '01KTQWXBHDWWV81EYDC9N82VV3.webp',
                $b . '01KTQWWA1SNVC0YC6VC757W6J6.webp',
            ],
            'Fun Activities' => [
                $b . '01KTQWXCK1DNJT9B5P0Q3AMG1G.webp',
                $b . '01KTQWXDR0YV73YK1ECWM6FA70.webp',
                $b . '01KTQWX2CES8JNV36GMCVYVB7T.webp',
            ],
        ];

        $catsHtml = '';
        foreach ($groups as $cat => $imgs) {
            $imgsHtml = '';
            foreach ($imgs as $img) {
                $imgsHtml .= '<div class="media-img"><img src="' . $img . '" alt="' . $cat . '" loading="lazy"></div>';
            }
            $catsHtml .= '<div class="media-cat" data-reveal><h3 class="media-cat-title">' . $cat . '</h3><div class="media-grid">' . $imgsHtml . '</div></div>';
        }

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
                                'kicker'          => 'Life at Prayaag',
                                'heading'         => 'Media',
                                'tagline'         => 'Moments captured at Prayaag International School — dance, music, sports, arts, activities, and beyond.',
                                'image'           => $b . '01KTQWX2CES8JNV36GMCVYVB7T.webp',
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
                            'type' => 'html',
                            'settings' => [
                                'html' => $catsHtml,
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Media page created with ' . count($sections) . ' sections.');
    }
}
