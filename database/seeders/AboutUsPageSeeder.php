<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class AboutUsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'about-us'],
            ['title' => 'About Us', 'status' => 'published']
        );

        $page->update([
            'title'  => $page->title ?: 'About Us',
            'status' => 'published',
            'seo'    => [
                'title'       => 'About PISP | Best CBSE Schools in Panipat | Top 5 Schools Panipat',
                'description' => 'Discover PISP, the best schools in Panipat committed to providing top-quality education and nurturing young minds for a bright future.',
                'og_image'    => '/storage/media/imported/01KTQWV3M7KWGRV6KSD23KN9YD.webp',
            ],
        ]);

        $imgBase = '/storage/media/imported/';
        $heroImg = $imgBase . '01KTQWV3M7KWGRV6KSD23KN9YD.webp';   // About-Prayaag-International-School
        $principalImg = $imgBase . '01KTQWYAVYB4AE4KG519C0XK68.webp'; // principal-prayaag-International-school
        $selfDefenceImg = $imgBase . '01KTQWYA6MVTX8XJMBZWH8X1P8.webp'; // Self-defence
        $footballImg = $imgBase . '01KTQWYC0GNDR9FVSWVBFHWAJ2.webp'; // student-playing-football-1
        $yogaImg = $imgBase . '01KTQWYD1P3FZZ73JFTTXET7KQ.webp';    // Yoga-at-Prayaag-International-School
        $checkIcon = $imgBase . '01KTQWYDSM9J22GN5NHEYT20P0.png';   // check-line

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
                                'kicker'          => 'Our School Story',
                                'heading'         => 'About Us',
                                'tagline'         => 'Prayaag International School, Panipat — nurturing young minds and shaping the leaders of tomorrow since 2016.',
                                'primary_label'   => 'Explore Our Campus →',
                                'primary_url'     => '/campus/',
                                'secondary_label' => 'Visit Our Facilities →',
                                'secondary_url'   => '/facilities/',
                                'image'           => $heroImg,
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
                                'html' => '<div class="about-intro" data-reveal>'
                                    . '<p class="about-intro-text">Prayaag International School, Panipat located in the heart of the city, is more than just an educational institution; it\'s a nurturing ground for young minds, a place where aspirations are nurtured and potential is shaped. Since its inception in 2016, the school has been dedicated to providing holistic education that goes beyond textbooks, fostering a stimulating environment where students can excel academically, emotionally, and socially.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Our Vision
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Our Guiding Light</span>'
                                    . '<h2 class="sec-title">Our Vision</h2>'
                                    . '</div>'
                                    . '<div class="about-vision" data-reveal>'
                                    . '<div class="about-vision-content">'
                                    . '<p>Prayaag International School has as its vision and mission <strong>"Character Building and Man-Making"</strong> and its motto is <strong>"Discipline and Excellence"</strong>. Its belief is <strong>"Co-operation over Competition"</strong>.</p>'
                                    . '<p>The goals of Prayaag International School, Panipat are defined by the verses of the Sthithaprajna from the <strong>Bhagvad Gita</strong> — namely true wisdom that transcends all text books.</p>'
                                    . '</div>'
                                    . '<div class="about-vision-image">'
                                    . '<img src="' . $selfDefenceImg . '" alt="Self-defence practice at Prayaag International School" loading="lazy">'
                                    . '</div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Principal's Message
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">From the Principal\'s Desk</span>'
                                    . '<h2 class="sec-title">Message from Our Principal</h2>'
                                    . '</div>'
                                    . '<div class="about-principal" data-reveal>'
                                    . '<div class="about-principal-image">'
                                    . '<img src="' . $principalImg . '" alt="Principal - Prayaag International School" loading="lazy">'
                                    . '</div>'
                                    . '<div class="about-principal-content">'
                                    . '<p>The distinguishing feature of Prayaag International, Panipat is its unique blend of Indian ethos and culture with contemporary teaching learning pedagogies. It is a school where the children can grow into confident and well-balanced youngsters. To unleash the latent powers of the child, the school provides opportunities, support and challenges at all stages of growth and development.</p>'
                                    . '<p>We believe that — <strong>If a child cannot learn the way we teach, teach him the way he can learn</strong>. Skill and activity based learning together with technology have replaced rote learning. Prayaag International, Panipat provides a conducive learning environment where every student is respected for his potential and is encouraged to learn at a pace he can cope with and stimulated to excel according to individual aptitudes.</p>'
                                    . '</div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 5. Our Mission
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Our Purpose</span>'
                                    . '<h2 class="sec-title">Our Mission</h2>'
                                    . '</div>'
                                    . '<div class="about-mission" data-reveal>'
                                    . '<div class="about-mission-content">'
                                    . '<p>Our Mission is to provide a comprehensive and future-oriented education that empowers students to become lifelong learners, confident decision-makers, and compassionate individuals. We aim to foster an atmosphere of inclusivity and collaboration, where every student\'s unique talents are recognized and nurtured.</p>'
                                    . '</div>'
                                    . '<div class="about-mission-images">'
                                    . '<div class="about-mission-img"><img src="' . $footballImg . '" alt="Student playing football" loading="lazy"></div>'
                                    . '<div class="about-mission-img"><img src="' . $yogaImg . '" alt="Yoga at Prayaag International School" loading="lazy"></div>'
                                    . '</div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 6. Our Values
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">What We Stand For</span>'
                                    . '<h2 class="sec-title">Our Values</h2>'
                                    . '</div>'
                                    . '<div class="about-values" data-reveal>'
                                    . '<blockquote class="about-values-quote">"Pursuing Excellence and Embrace Responsibility"</blockquote>'
                                    . '<p>We raise intellectual standard of our children by promoting a school ethos that is underpinned by the core value — growing by learning.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 7. Why Prayaag?
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Why Choose Us</span>'
                                    . '<h2 class="sec-title">Why Prayaag?</h2>'
                                    . '</div>'
                                    . '<div class="about-features" data-reveal>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span>Awarded the prestigious <strong>British Council International School Award, 2020-23</strong></span></div>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span><strong>100% results</strong> in academics, sports and co-scholastic events</span></div>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span>Highly educated and experienced teachers that imbibe a contemporary, all-around education</span></div>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span>Superior indoor and outdoor sports infrastructure with highly-experienced trainers</span></div>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span>World-class infrastructure equipped with state-of-the-art digital facilities</span></div>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span>Special periodic sessions for personal well-being, ethics and personality development</span></div>'
                                    . '<div class="about-feature"><img src="' . $checkIcon . '" alt="" class="about-feature-icon" aria-hidden="true"><span>GPS and security cameras equipped, fully-air-conditioned infrastructure and transport</span></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 8. Our Achievements
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'achievements',
                            'settings' => [
                                'eyebrow' => 'Our Achievements',
                                'heading' => 'Milestones of Our Journey',
                                'sub'     => 'Discover excellence in education at Prayaag International School, Panipat. Our students consistently excel in various academic competitions, sports events, and cultural activities.',
                                'rows'    => [
                                    ['year' => '2016', 'items' => ['Laid the foundation stone of the school']],
                                    ['year' => '2019', 'items' => [
                                        'District Level Karate Competition',
                                        'Wrestling Competition (Block level)',
                                        'Capacity Building Programme By CBSE',
                                        'Annual Function – Let Me Fly',
                                    ]],
                                    ['year' => '2020', 'items' => [
                                        'Vidya Mandir Quest – Biggest National Level Quiz',
                                        'British Council International School Award',
                                        'Go Green Initiative',
                                    ]],
                                    ['year' => '2021', 'items' => [
                                        'National Level Karate Championship',
                                        'Building Resilience – A virtue in Covid Times',
                                        'Dhammika KAT Cup Championship',
                                        'Sports Tournament – District Level',
                                        'Faculty / Staff Sports Tournament',
                                        'State Level Painting Competition',
                                    ]],
                                ],
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 9. Governing Body
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Our Leadership</span>'
                                    . '<h2 class="sec-title">Our Governing Body</h2>'
                                    . '<p class="sec-sub">Meet the visionary leaders who guide Prayaag International School towards excellence.</p>'
                                    . '</div>'
                                    . '<div class="about-gov-grid" data-reveal>'
                                    . '<div class="about-gov-card"><div class="about-gov-avatar" style="background:var(--gold)"><span>DG</span></div><h4>Dr. Dinesh Gupta</h4><p>Chairman</p></div>'
                                    . '<div class="about-gov-card"><div class="about-gov-avatar" style="background:var(--navy-3);overflow:hidden;width:80px;height:80px;border-radius:50%;margin:0 auto 10px"><img src="' . $imgBase . '01KTQWW0KDMYKAGQS1XNT1R7V7.webp" alt="Mrs. Anju Gupta" style="width:100%;height:100%;object-fit:cover;display:block"></div><h4>Mrs. Anju Gupta</h4><p>Director</p></div>'
                                    . '<div class="about-gov-card"><div class="about-gov-avatar" style="background:var(--gold);overflow:hidden;width:80px;height:80px;border-radius:50%;margin:0 auto 10px"><img src="' . $imgBase . '01KTQWW217PTPXXT14ATFA7F48.webp" alt="Mrs. Mamta Sachdeva" style="width:100%;height:100%;object-fit:cover;display:block"></div><h4>Mrs. Mamta Sachdeva</h4><p>Principal</p></div>'
                                    . '<div class="about-gov-card"><div class="about-gov-avatar" style="background:var(--navy-3)"><span>SK</span></div><h4>Mr. Sanjay Kumar</h4><p>Vice Principal</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('About Us page created with ' . count($sections) . ' sections.');
    }
}
