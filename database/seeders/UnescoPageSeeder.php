<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class UnescoPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'unesco'],
            ['title' => 'UNESCO', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'UNESCO Club — Prayaag International School, Panipat',
                'description' => 'Prayaag International School proudly hosts a UNESCO club dedicated to promoting peace, cultural heritage, literacy, and global citizenship.',
                'og_image'    => $b . '01KWEC2PW5J7Y50P4YSWK7Z82G.jpg',
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
                                'kicker'          => 'Global Citizenship',
                                'heading'         => 'UNESCO',
                                'tagline'         => 'Promoting peace, education, and cultural heritage through active student participation.',
                                'image'           => $b . '01KWEC2PW5J7Y50P4YSWK7Z82G.jpg',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Intro + Mission
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">UNESCO Club</span>'
                                    . '<h2 class="sec-title">UNESCO at Prayaag</h2>'
                                    . '</div>'
                                    . '<div class="unesco-intro" data-reveal>'
                                    . '<img src="' . $b . '01KWEC2RBY2KJ5MD3MT6PEDNNT.jpg" alt="UNESCO Logo" class="unesco-logo-img" loading="lazy">'
                                    . '<p>Prayaag International School is proud to host a UNESCO club that actively engages students in the principles of the United Nations Educational, Scientific and Cultural Organization.</p>'
                                    . '</div>'
                                    . '<div class="unesco-content" data-reveal>'
                                    . '<div class="uc-image"><img src="' . $b . '01KWEC2QR3J5A7AWXEBS8FFNB2.jpg" alt="Hands United" loading="lazy"></div>'
                                    . '<div class="uc-objectives">'
                                    . '<h4>The basic work of this club is:</h4>'
                                    . '<ul>'
                                    . '<li>Disseminate the general principles as those set out in the preamble and the constitution of UNESCO, the United Nations Charter and Universal declaration of Human Rights.</li>'
                                    . '<li>Participate in the celebration of International days and years proclaimed by the General Assembly of United Nations and General Conference of UNESCO.</li>'
                                    . '<li>Promote literacy activities, the preservation and presentation of the cultural heritage — organise study camps for the students of foreign countries.</li>'
                                    . '<li>Educate children for the prevention of AIDS.</li>'
                                    . '</ul>'
                                    . '</div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Additional images
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Club Activities</span>'
                                    . '<h2 class="sec-title">UNESCO in Action</h2>'
                                    . '</div>'
                                    . '<div class="unesco-gallery" data-reveal>'
                                    . '<div class="ug-item"><img src="' . $b . '01KWEC2RZ9Q8D3TBN9SWF5AV8E.jpg" alt="UNESCO Club Activity" loading="lazy"></div>'
                                    . '<div class="ug-item"><img src="' . $b . '01KWEC2QR3J5A7AWXEBS8FFNB2.jpg" alt="Hands United" loading="lazy"></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('UNESCO page created with ' . count($sections) . ' sections.');
    }
}
