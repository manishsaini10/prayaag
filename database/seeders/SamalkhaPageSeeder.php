<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class SamalkhaPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'best-schools-in-samalkha'],
            ['title' => 'Best Schools in Samalkha', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Best Schools in Samalkha for Quality Education 2025-26',
                'description' => 'Are you trying to find the best Schools in Samalkha? Prayaag International School provides your child with an excellent education and a caring atmosphere.',
                'og_image'    => $b . '01KTQWZ4ZY7Z2TFZRGKTY93JDV.jpg',
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
                                'kicker'          => 'Best Schools in Samalkha',
                                'heading'         => 'Best School in Samalkha',
                                'tagline'         => 'Prayaag International School — a beacon of excellence among the best schools in Samalkha.',
                                'image'           => $b . '01KTQWZ4ZY7Z2TFZRGKTY93JDV.jpg',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Content
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Excellence in Education</span>'
                                    . '<h2 class="sec-title">Best School in Samalkha</h2>'
                                    . '</div>'
                                    . '<div class="sam-content" data-reveal>'
                                    . '<p class="sam-subtitle"><strong><a href="https://prayaaginternationalschool.com/">Prayaag International School</a>, A Beacon of Excellence Among the Best Schools in Samalkha</strong></p>'
                                    . '<img src="' . $b . '01KTQWZ6EBVA4AB399Q547M7Y4.jpg" alt="Best CBSE School in Samalkha" class="sam-feat-img" loading="lazy">'
                                    . '<p>Prayaag International School: Preserving Quality as the Best School In Samalkha, Prayaag International School is the epitome of academic brilliance, representing a dedication to developing students\' whole selves. As the greatest school in Samalkha, we aim to provide a transformative learning environment that gives kids the tools they need to succeed intellectually, emotionally, and socially. We go beyond conventional educational paradigms.</p>'
                                    . '<p>The foundation of our educational philosophy is the conviction that learning is a journey that takes place outside of the classroom. We take pride in going above and beyond the strict requirements imposed by school boards, making sure that our curriculum is painstakingly designed to offer a thorough education that equips kids for the challenges of the future.</p>'
                                    . '<p>Prayaag International School is unique because of our steadfast commitment to fostering an environment that is vibrant and engaging outside of the classroom. Our faculty, which is made up of seasoned instructors and subject matter experts, is incredibly passionate about encouraging students to embrace studying. By utilizing interactive teaching approaches, we foster critical thinking, active questioning, and deep engagement with the subjects that students study.</p>'
                                    . '<p>Our infrastructure demonstrates our dedication to offering a favorable environment for learning. Our cutting-edge learning environments, well-stocked labs, extensive library, and cutting-edge sports facilities all support students\' intellectual, physical, and emotional development.</p>'
                                    . '<p>Prayaag International School\'s success can be attributed to its focus on character development and values. Our mission is to prepare our students to be morally upright and responsible global citizens by teaching them values such as integrity, compassion, and social responsibility. In addition to academics, we promote extracurricular involvement among students to guarantee the development of critical life skills like resilience, leadership, and teamwork.</p>'
                                    . '<p>Our use of the newest instructional tools is just one more way that we demonstrate our dedication to providing high-quality education. We understand how critical it is to keep up with technological developments and incorporate them into our curriculum in a smooth manner. This progressive approach guarantees that our students have the information and abilities necessary to prosper in a world that is changing quickly.</p>'
                                    . '<p>Prayaag International School, regarded as one of Samalkha\'s Best Schools, is proud of its students\' achievements. In addition to their academic prowess, our alumni are self-assured people who significantly impact society. We take great satisfaction in raising up the next generation of innovators, leaders, and thinkers who are ready to contribute to society on a worldwide scale.</p>'
                                    . '<p>Selecting Prayaag International School is a commitment to developing a well-rounded, successful, and moral person as much as a choice of educational institution. We cordially invite you to accompany us on this voyage of excellence and exploration, where each student is equipped to realize their greatest potential and turn into a lighthouse for the globe.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Principal's Message
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">From the Principal</span>'
                                    . '<h2 class="sec-title">Message from Our Principal</h2>'
                                    . '</div>'
                                    . '<div class="sam-principal" data-reveal>'
                                    . '<div class="sp-img"><img src="' . $b . '01KTQWYAVYB4AE4KG519C0XK68.webp" alt="Principal" loading="lazy"></div>'
                                    . '<div class="sp-msg"><p>The distinguishing feature of Prayaag International, Panipat is its unique blend of Indian ethos and culture with contemporary teaching learning pedagogies. It is a school where the children can grow into confident and well-balanced youngsters. To unleash the latent powers of the child, the school provides opportunities, support and challenges at all stages of growth and development.</p><p>We believe that &mdash; <strong>IF A CHILD CANNOT LEARN THE WAY WE TEACH, TEACH HIM THE WAY HE CAN LEARN</strong>. Skill and activity based learning together with technology have replaced rote learning. Prayaag International, Panipat provides a conducive learning environment where every student is respected for his potential and is encouraged to learn at a pace he can cope with and stimulated to excel according to individual aptitudes.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. CTA
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sam-cta" data-reveal><h3>Ready to Enroll?</h3><p>Begin your child\'s journey at Prayaag International School today.</p><a class="btn" href="/admissions">Admission Process →</a></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Best Schools in Samalkha page created with ' . count($sections) . ' sections.');
    }
}
