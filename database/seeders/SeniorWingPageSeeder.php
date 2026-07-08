<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class SeniorWingPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'senior-wing-school-in-panipat'],
            ['title' => 'Senior Wing', 'status' => 'published']
        );

        $base = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Senior Wing School | Senior Secondary School in Panipat',
                'description' => 'Senior Wing School, Best Senior Secondary School, Senior Secondary School in Panipat, Best CBSE Schools in Samalkha, Top CBSE School In Panipat',
                'og_image'    => $base . '01KTQWWB8D8FEC6S9QTGZQ9BNN.webp',
            ],
        ]);

        $heroImg   = $base . '01KTQWWB8D8FEC6S9QTGZQ9BNN.webp';
        $chessImg  = $base . '01KTQWWCA7SJJQ6XM2TKCA1PBM.webp';
        $cricketImg = $base . '01KTQWWDAK4WBZ16716ZDNP7A5.webp';
        $bballImg  = $base . '01KTQWWEMX03QNFJNWKAK6G6QK.webp';

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
                                'kicker'          => 'Best place to grow',
                                'heading'         => 'Senior Wing School',
                                'tagline'         => 'Teenage life plays a crucial role in shaping a person\'s character, vision and personality. And that\'s what our goal is, to provide students with every possible instrument that will help them in becoming a better human.',
                                'primary_label'   => 'Explore Junior Wing →',
                                'primary_url'     => '/junior-wing-school-in-panipat',
                                'secondary_label' => 'Admissions Open →',
                                'secondary_url'   => '/admissions',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. About Senior Wing
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Shaping Tomorrow\'s Leaders</span>'
                                    . '<h2 class="sec-title">Senior Wing School</h2>'
                                    . '</div>'
                                    . '<div class="senior-content" data-reveal>'
                                    . '<p>Education is imparted through Tech-enabled Classrooms, well-enhanced and upgraded Science, Computer and Language Laboratories. An amalgamation of modernity, refinement, culture, and discipline is what we impart to our students.</p>'
                                    . '<p>Our highly-accomplished teaching staff thrives to metamorphose the students into fervent global citizens who are confident, responsible and fearless. Our holistic pedagogy aims at unleashing each student\'s potential to enkindle his/her originality and nurture the zeal for achieving what one is focused upon.</p>'
                                    . '<p>By providing the opportunities of practical and experiential learning, we make certain that our students are driven beyond the rigid structures of classroom learning and present new and sustainable ideas.</p>'
                                    . '<p>At Prayaag International School, Panipat, we don\'t only focus on the outstanding results in various disciplines, but also focusses on fostering the overall well-being and holistic success of students. Prayaag International School\'s Senior Wing stands as a beacon of academic excellence in Panipat.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Key Highlights
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Excellence in Education</span>'
                                    . '<h2 class="sec-title">Key Highlights</h2>'
                                    . '</div>'
                                    . '<div class="senior-highlights" data-reveal>'
                                    . '<div class="senior-highlight"><div class="sh-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 5h18v11H3zM3 16l-1 3h20l-1-3M9 9h6"/></svg></div><h4>Tech-enabled Classrooms</h4><p>Smart classrooms with digital teaching systems for interactive learning.</p></div>'
                                    . '<div class="senior-highlight"><div class="sh-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 3v16h16M18 11l-4-4-4 4M14 7v10"/></svg></div><h4>Advanced Laboratories</h4><p>Well-equipped Science, Computer and Language Labs for practical learning.</p></div>'
                                    . '<div class="senior-highlight"><div class="sh-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h4>Holistic Development</h4><p>Focus on academic excellence along with sports, arts and life skills.</p></div>'
                                    . '<div class="senior-highlight"><div class="sh-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h4>Expert Faculty</h4><p>Highly accomplished teaching staff dedicated to student success.</p></div>'
                                    . '<div class="senior-highlight"><div class="sh-icon" style="background:#ede8fc;color:#6b3fc7"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18"/></svg></div><h4>Experiential Learning</h4><p>Practical learning opportunities beyond rigid classroom structures.</p></div>'
                                    . '<div class="senior-highlight"><div class="sh-icon" style="background:#fce8e8;color:#c0392b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 21h8M12 17v4M6 3h12l2 10H4L6 3z"/></svg></div><h4>CBSE Curriculum</h4><p>Rigorous academic program following CBSE curriculum with outstanding results.</p></div>'
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
                                    . '<h2 class="sec-title">Senior Wing Gallery</h2>'
                                    . '</div>'
                                    . '<div class="senior-gallery" data-reveal>'
                                    . '<div class="senior-gallery-img sg-tall"><img src="' . $chessImg . '" alt="Chess Match" loading="lazy"></div>'
                                    . '<div class="senior-gallery-img sg-wide"><img src="' . $cricketImg . '" alt="Cricket Practice" loading="lazy"></div>'
                                    . '<div class="senior-gallery-img"><img src="' . $bballImg . '" alt="Basketball Practice" loading="lazy"></div>'
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
                                'heading'      => 'Enroll in Senior Wing',
                                'text'         => 'Give your child the best secondary education at Prayaag International School. CBSE curriculum with holistic development.',
                                'button_label' => 'Apply Now →',
                                'button_url'   => 'https://pisp.accevate.com/registration/',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Senior Wing page created with ' . count($sections) . ' sections.');
    }
}
