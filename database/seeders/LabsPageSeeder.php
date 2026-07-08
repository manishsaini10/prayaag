<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class LabsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'labs'],
            ['title' => 'Labs', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Labs — Prayaag International School, Panipat',
                'description' => 'Explore state-of-the-art labs at Prayaag International School, Panipat — Physics, Chemistry, Biology, Math, Computer, and Robotics.',
                'og_image'    => $b . '01KTQWYJ9C2YEECMGGP1JZWPZT.jpg',
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
                                'kicker'          => 'Where Science Comes Alive',
                                'heading'         => 'Our Labs',
                                'tagline'         => 'Every student is an enthusiastic scientist in the making, and tries to explore, probe and experiment to find the truth behind the facts of life.',
                                'image'           => $b . '01KTQWYJ9C2YEECMGGP1JZWPZT.jpg',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Science Labs intro
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Hands-on Learning</span>'
                                    . '<h2 class="sec-title">Science Laboratories</h2>'
                                    . '</div>'
                                    . '<div class="labs-intro" data-reveal>'
                                    . '<p>To shape the world of tomorrow we have the best Science laboratories in Panipat for Physics, Chemistry and Biology that enable students to conduct all experiments prescribed by the CBSE.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Lab cards
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="labs-grid" data-reveal>'
                                    . '<div class="lab-card"><div class="lc-icon">&#9881;</div><h4>Physics Lab</h4><p>We have a well planned and well equipped Physics lab with all the interesting sets of equipment to underpin scientific and experimental concepts and assist the children in developing investigative skills.</p></div>'
                                    . '<div class="lab-card"><div class="lc-icon">&#9878;</div><h4>Chemistry Lab</h4><p>The Chemistry laboratory is planned while keeping all the statutory norms and safety standards. A scientific approach is developed in the students along with the ability to analyze, collate, compute, integrate and deduce.</p></div>'
                                    . '<div class="lab-card"><div class="lc-icon">&#9763;</div><h4>Biology Lab</h4><p>The Biology laboratory is a modern fact finding infrastructure which provides a broad range of biological and biochemical techniques with in-depth practical guidance offered by experienced staff.</p></div>'
                                    . '<div class="lab-card"><div class="lc-icon">&#8776;</div><h4>Math Lab</h4><p>Maths lab is designed in a way where students can learn and explore various mathematics concepts and verify a range of mathematical facts and theorems using combination of activities.</p></div>'
                                    . '<div class="lab-card"><div class="lc-icon">&#9000;</div><h4>Computer Lab</h4><p>We have a fully air conditioned, highly modernized computer lab with the latest technology and 24 hour internet access. Students are trained on various computer programs.</p></div>'
                                    . '<div class="lab-card"><div class="lc-icon">&#129302;</div><h4>Robotics Lab</h4><p>Our Robotics Lab serves as a dynamic hub for innovation, fostering creativity and hands-on learning that empowers students to delve into the captivating realm of robotics and automation.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Labs page created with ' . count($sections) . ' sections.');
    }
}
