<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class JuniorPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'junior'],
            ['title' => 'Junior', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Junior Wing — Little Millennium | Prayaag International School, Panipat',
                'description' => 'Welcoming our children back to their favorite preschool. Little Millennium at Prayaag International School — best preschool in Panipat.',
                'og_image'    => $b . '01KTQWVZBC5D4K49NNHK08JQ69.jpg',
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
                                'kicker'          => 'Little Millennium Preschool',
                                'heading'         => 'Welcoming Our Children,<br>Back To Their Favorite Preschool',
                                'tagline'         => 'We all want to select the best preschool for our kid\'s primitive educational years and build a strong foundation for the child\'s future.',
                                'image'           => $b . '01KTQWVZBC5D4K49NNHK08JQ69.jpg',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Intro
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Little Millennium</span>'
                                    . '<h2 class="sec-title">Best Preschool in Panipat</h2>'
                                    . '</div>'
                                    . '<div class="jr-intro" data-reveal>'
                                    . '<p>We all want to select the best preschool for our kid\'s primitive educational years and build a strong foundation for the child\'s future.</p>'
                                    . '<p>Little Millennium Preschool is one of the best preschools in India to lay the first step into the educational journey of your child.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Steps
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Simple Process</span>'
                                    . '<h2 class="sec-title">Admission Process</h2>'
                                    . '</div>'
                                    . '<div class="jr-steps" data-reveal>'
                                    . '<div class="js-card"><div class="js-num">1</div><p>Our admission counsellors will get in touch to understand the requirement</p></div>'
                                    . '<div class="js-card"><div class="js-num">2</div><p>Schedule a visit to our campus for a personal tour</p></div>'
                                    . '<div class="js-card"><div class="js-num">3</div><p>Complete the admission formalities and document submission</p></div>'
                                    . '<div class="js-card"><div class="js-num">4</div><p>Welcome your child to their new learning journey!</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Enquiry form
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [
                        ['width' => 12, 'widgets' => [
                            ['type' => 'html', 'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Get in Touch</span>'
                                    . '<h2 class="sec-title">Preschool Admission Form</h2>'
                                    . '</div>'
                                    . '<p style="text-align:center;max-width:600px;margin:0 auto 1.5rem" data-reveal>Fill in the form below, and we will get in touch with you to resolve the preschool admission queries at the earliest.</p>',
                            ]],
                            ['type' => 'contact_form', 'settings' => []],
                        ]],
                    ],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Junior landing page created with ' . count($sections) . ' sections.');
    }
}
