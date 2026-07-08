<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class Top10PageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'top-10-schools-in-panipat'],
            ['title' => 'Top 10 Schools in Panipat', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Top 10 Schools in Panipat for Quality Education 2025-26',
                'description' => 'List of top 10 schools in Panipat. Unlock a World of Quality Education for Your Child. PISP proudly listed among the top 10 schools in Panipat.',
                'og_image'    => $b . '01KTQWZ3TGTT1QF6R9K8J1ZH1C.jpg',
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
                                'kicker'          => 'Top 10 Schools in Panipat',
                                'heading'         => 'Top 10 Schools in Panipat',
                                'tagline'         => 'Prayaag International School — a standout institution among Panipat\'s top ten schools.',
                                'image'           => $b . '01KTQWY8B3QGJ1J62RQA8XJPSN.jpg',
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
                                    . '<span class="eyebrow">Quality Education</span>'
                                    . '<h2 class="sec-title">Prayaag International School</h2>'
                                    . '</div>'
                                    . '<div class="top10-content" data-reveal>'
                                    . '<p class="top10-subtitle"><strong><a href="https://prayaaginternationalschool.com/">Prayaag International School</a> is a Standout Institution Among Panipat\'s Top Ten Schools.</strong></p>'
                                    . '<img src="' . $b . '01KTQWZ3TGTT1QF6R9K8J1ZH1C.jpg" alt="Top 10 Schools in Panipat" class="top10-feat-img" loading="lazy">'
                                    . '<p>At Prayaag International School, the educational philosophy transcends traditional boundaries. Its curriculum is meticulously crafted not just to meet but exceed the stringent standards set by educational boards, ensuring a comprehensive learning experience. The school\'s approach is rooted in the belief that education is a journey shaping character, instilling values, and nurturing creativity.</p>'
                                    . '<p>What sets Prayaag International School apart is its dedication to creating a dynamic and stimulating environment beyond the classroom. The faculty, comprised of seasoned educators and experts, passionately fosters a love for learning. Through interactive teaching methodologies, students are encouraged to think critically, question actively, and engage deeply with subjects.</p>'
                                    . '<p>The school\'s infrastructure reflects its commitment to a conducive learning atmosphere. State-of-the-art classrooms, well-equipped laboratories, a rich library, and modern sports facilities contribute to a holistic educational experience. Prayaag International School understands that a well-rounded education includes not only academic pursuits but also physical fitness, artistic expression, and personality development.</p>'
                                    . '<p>At the heart of the institution\'s success is its emphasis on character building and values. The school\'s ethos revolves around instilling qualities of integrity, compassion, and social responsibility in students. Beyond academics, Prayaag International School encourages participation in extracurricular activities, ensuring the development of essential life skills such as leadership, teamwork, and resilience.</p>'
                                    . '<p>Prayaag International School\'s commitment to quality education is further exemplified by its engagement with the latest educational technologies. The school recognizes the importance of staying abreast of advancements and seamlessly integrating them into the curriculum. This forward-thinking approach prepares students for the challenges of the future, equipping them with the skills and knowledge needed to thrive in a rapidly evolving world.</p>'
                                    . '<p>The school\'s recognition among the Top 10 Schools in Panipat is a testament to the success of its Students. Prayaag International School alumni are not only academically proficient but also confident individuals contributing meaningfully to society. The institution takes pride in nurturing future leaders, thinkers, and innovators prepared to make a positive impact on the global stage.</p>'
                                    . '<p>Prayaag International School stands tall among the Top 10 Schools in Panipat, distinguished by its unwavering commitment to providing quality education. With a holistic approach, state-of-the-art facilities, a dedicated faculty, and a focus on values, the school creates an environment where students thrive academically, emotionally, and socially. Choosing Prayaag International School is not just a selection of an educational institution but a commitment to nurturing a well-rounded, accomplished, and ethical individual ready to face the world with confidence and competence.</p>'
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
                                    . '<div class="top10-principal" data-reveal>'
                                    . '<div class="tp-img"><img src="' . $b . '01KTQWYAVYB4AE4KG519C0XK68.webp" alt="Principal" loading="lazy"></div>'
                                    . '<div class="tp-msg"><p>The distinguishing feature of Prayaag International, Panipat is its unique blend of Indian ethos and culture with contemporary teaching learning pedagogies. It is a school where the children can grow into confident and well-balanced youngsters. To unleash the latent powers of the child, the school provides opportunities, support and challenges at all stages of growth and development.</p><p>We believe that &mdash; <strong>IF A CHILD CANNOT LEARN THE WAY WE TEACH, TEACH HIM THE WAY HE CAN LEARN</strong>. Skill and activity based learning together with technology have replaced rote learning. Prayaag International, Panipat provides a conducive learning environment where every student is respected for his potential and is encouraged to learn at a pace he can cope with and stimulated to excel according to individual aptitudes.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Top 10 Schools page created with ' . count($sections) . ' sections.');
    }
}
