<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class JuniorWingPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'junior-wing-school-in-panipat'],
            ['title' => 'Junior Wing', 'status' => 'published']
        );

        $imgBase = '/storage/media/imported/';

        $page->update([
            'title'  => $page->title ?: 'Junior Wing',
            'status' => 'published',
            'seo'    => [
                'title'       => 'Junior Wing school, Nursery School, Kindergarten in Panipat',
                'description' => 'Discover excellence in early education at PISP Junior Wing School, Panipat premier nursery school, kindergarten, and preschool. Enroll today!',
                'og_image'    => $imgBase . '01KTQWW6KKXCCRMFVKNRDED21M.webp',
            ],
        ]);

        $heroImg = $imgBase . '01KTQWW6KKXCCRMFVKNRDED21M.webp';
        $yogaImg = $imgBase . '01KTQWW7MH1TPB1T9H7W0F0VWG.webp';
        $yogaTeachImg = $imgBase . '01KTQWW8REKM2BRQD4PJD2JZ2S.webp';
        $libraryImg = $imgBase . '01KTQWWA1SNVC0YC6VC757W6J6.webp';

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
                                'kicker'          => 'Best place to start',
                                'heading'         => 'Junior Wing School',
                                'tagline'         => 'We at Prayaag, believe that initial days at school bring the best out of young learners. Therefore we try to emphasize the overall growth of a child.',
                                'primary_label'   => 'Explore Senior Wing →',
                                'primary_url'     => '/senior-wing-school-in-panipat',
                                'secondary_label' => 'Admissions Open →',
                                'secondary_url'   => '/admissions',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. About Junior Wing
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Nurturing Young Minds</span>'
                                    . '<h2 class="sec-title">Junior Wing School</h2>'
                                    . '</div>'
                                    . '<div class="wing-content" data-reveal>'
                                    . '<p>To sow and nurture the seeds of wisdom in the formative years of education, the Junior Wing of Prayaag International School, Panipat consists of highly-educated and experienced teachers to inculcate the foundation of contemporary education. We strive to provide an environment that helps in building a child\'s body, mind and soul.</p>'
                                    . '<p>Therefore, the junior wing has its own self-contained building with Smart Classrooms, well-equipped Library, Play-Area, Music Room and Activity Room. Every child\'s well-being and safety is ensured by our 360 degree surveillance and well-trained non-teaching staff.</p>'
                                    . '<p>A world class teaching pedagogy is used to cater to different intelligences, such as hands-on activities, group projects, visuals aids, story telling, role play and logical reasoning tasks.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Features grid
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Why Choose Our Junior Wing</span>'
                                    . '<h2 class="sec-title">Key Features</h2>'
                                    . '</div>'
                                    . '<div class="wing-features" data-reveal>'
                                    . '<div class="wing-feature"><div class="wf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 5h18v11H3zM3 16l-1 3h20l-1-3M9 9h6"/></svg></div><h4>Smart Classrooms</h4><p>Tech-enabled, airy and engaging learning spaces for effective education.</p></div>'
                                    . '<div class="wing-feature"><div class="wf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5a2 2 0 0 1 2-2h6v16H6a2 2 0 0 0-2 2V5zM20 5a2 2 0 0 0-2-2h-6v16h6a2 2 0 0 1 2 2V5z"/></svg></div><h4>Well-equipped Library</h4><p>Age-appropriate books and digital resources to foster reading habits.</p></div>'
                                    . '<div class="wing-feature"><div class="wf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 18V5l12-2v13M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM21 16a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg></div><h4>Music &amp; Activity Room</h4><p>Dedicated spaces for music, dance, art and creative exploration.</p></div>'
                                    . '<div class="wing-feature"><div class="wf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4"/></svg></div><h4>360° Safety</h4><p>CCTV surveillance and trained staff ensure complete child safety.</p></div>'
                                    . '<div class="wing-feature"><div class="wf-icon" style="background:#ede8fc;color:#6b3fc7"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/></svg></div><h4>Play Area</h4><p>Creative tools, toys and soft floor for kinesthetic skill development.</p></div>'
                                    . '<div class="wing-feature"><div class="wf-icon" style="background:#fce8e8;color:#c0392b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h4>Low Student-Teacher Ratio</h4><p>Personalized attention with a low teacher-to-student ratio.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Gallery
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Campus Life</span>'
                                    . '<h2 class="sec-title">Junior Wing Gallery</h2>'
                                    . '</div>'
                                    . '<div class="wing-gallery" data-reveal>'
                                    . '<div class="wing-gallery-img"><img src="' . $yogaImg . '" alt="Yoga Practice" loading="lazy"></div>'
                                    . '<div class="wing-gallery-img"><img src="' . $yogaTeachImg . '" alt="Yoga Teaching" loading="lazy"></div>'
                                    . '<div class="wing-gallery-img"><img src="' . $libraryImg . '" alt="School Library" loading="lazy"></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 5. CTA
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'admission-cta',
                            'settings' => [
                                'heading'      => 'Enroll Your Child in Junior Wing',
                                'text'         => 'Give your child the best start in life. Nursery, Kindergarten and Primary education at Prayaag International School.',
                                'button_label' => 'Apply Now →',
                                'button_url'   => '/registration',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Junior Wing page created with ' . count($sections) . ' sections.');
    }
}
