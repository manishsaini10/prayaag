<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class FacilitiesPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'facilities'],
            ['title' => 'Facilities', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Facilities | Life at Prayaag International School, Panipat',
                'description' => 'Explore world-class facilities at Prayaag International School, Panipat — sports grounds, science labs, library, transport, medical, and more.',
                'og_image'    => $b . '01KTQWWPVF2VAEJSD6PZ6D1CTQ.webp',
            ],
        ]);

        $heroImg    = $b . '01KTQWWPVF2VAEJSD6PZ6D1CTQ.webp';
        $volleyImg  = $b . '01KTQWWRAX46YK4G2YCMM00R9D.webp';
        $swimImg    = $b . '01KTQWWSADT3JFAFSKVJ852ZJT.webp';
        $classImg   = $b . '01KTQWWWH5PG2EBSWSAV8ZMQ11.webp';
        $libImg     = $b . '01KTQWWVDF6QJR4NHJH12MASPJ.webp';
        $transImg   = $b . '01KTQWWXGF62TW89BPJN4T592J.webp';
        $medImg     = $b . '01KTQWWYJFQT9QT4HP1R5HJBT1.webp';
        $secImg     = $b . '01KTQWWZKFTQJC54AMHM5VD90M.webp';

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
                                'heading'         => 'Facilities',
                                'tagline'         => 'World-class infrastructure and facilities that nurture holistic development.',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Intro + Sports
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Excellence in Infrastructure</span>'
                                    . '<h2 class="sec-title">Life At Prayaag</h2>'
                                    . '</div>'
                                    . '<div class="fac-desc" data-reveal>'
                                    . '<p>Sports help to build character and educate the importance of discipline in life. It instills a respect for rules and allows the children to learn the value of self control. Keeping this in mind, we strive to provide our students with one of the best indoor and outdoor sporting infrastructures in Panipat to prepare our children for the highly competitive world of sports.</p>'
                                    . '</div>'
                                    . '<div class="fac-sports" data-reveal>'
                                    . '<div class="fac-sport"><div class="fs-img"><img src="' . $volleyImg . '" alt="Volleyball Court" loading="lazy"></div><h4>Sports Ground</h4><p>Football and hockey ground with a 5-lane track.</p></div>'
                                    . '<div class="fac-sport"><div class="fs-img"><img src="' . $swimImg . '" alt="Swimming Pool" loading="lazy"></div><h4>Swimming Pool</h4><p>Swimming pool with a splash pool for students.</p></div>'
                                    . '</div>'
                                    . '<div class="fac-sports-list" data-reveal>'
                                    . '<ul>'
                                    . '<li><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><path d="M5 13l3 3L19 7"/></svg> International standard turf courts for basketball, badminton and tennis</li>'
                                    . '<li><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><path d="M5 13l3 3L19 7"/></svg> Gym for functional training of students</li>'
                                    . '<li><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><path d="M5 13l3 3L19 7"/></svg> Cricket, table tennis, yoga, chess, rock climbing and Karate</li>'
                                    . '</ul>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Science Labs
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow" style="color:var(--gold-2)">Cutting-edge Laboratories</span>'
                                    . '<h2 class="sec-title" style="color:#fff">Science Laboratories</h2>'
                                    . '</div>'
                                    . '<p class="fac-lab-intro" data-reveal>Every student is an enthusiastic scientist in the making, and tries to explore, probe and experiment to find the truth behind the facts of life. To shape the world of tomorrow we have the best Science laboratories in Panipat for Physics, Chemistry and Biology that enable students to conduct all experiments prescribed by the CBSE syllabi.</p>'
                                    . '<div class="fac-labs" data-reveal>'
                                    . '<div class="fac-lab"><div class="fl-icon">&#9881;</div><h4>Physics Lab</h4><p>A well planned and equipped Physics lab with all the interesting sets of equipment to underpin scientific and experimental concepts and assist the children in developing investigative skills.</p></div>'
                                    . '<div class="fac-lab"><div class="fl-icon">&#9878;</div><h4>Chemistry Lab</h4><p>The Chemistry laboratory is planned while keeping all the statutory norms and safety standards. A scientific approach is developed in the students along with the ability to analyze, collate, compute, integrate and deduce.</p></div>'
                                    . '<div class="fac-lab"><div class="fl-icon">&#9763;</div><h4>Biology Lab</h4><p>A modern fact finding infrastructure which provides a broad range of biological and biochemical techniques with in-depth practical guidance offered by experienced staff.</p></div>'
                                    . '<div class="fac-lab"><div class="fl-icon">&#8776;</div><h4>Math Lab</h4><p>Designed for students to learn and explore various mathematics concepts and verify a range of mathematical facts and theorems using combination of activities.</p></div>'
                                    . '<div class="fac-lab"><div class="fl-icon">&#9000;</div><h4>Computer Lab</h4><p>A fully air conditioned, highly modernized computer lab with the latest technology and 24 hour internet access. Students are trained on various computer programs according to the demands of the modern times.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Library + Classrooms
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="fac-duo" data-reveal>'
                                    . '<div class="fac-duo-item"><div class="fdi-img"><img src="' . $libImg . '" alt="Library" loading="lazy"></div><h4>Library</h4><p>The school boasts of providing two well-stocked separate best libraries for Juniors and Seniors in Panipat with an impressive index of titles covering fiction and non-fiction, periodicals, magazines, and newspapers.</p></div>'
                                    . '<div class="fac-duo-item"><div class="fdi-img"><img src="' . $classImg . '" alt="Classrooms" loading="lazy"></div><h4>Smart Classrooms</h4><p>Prayaag International School is at par with international standards. Spacious classrooms equipped with latest infrastructure ensure students have the best resources. Fully centralized air-conditioned rooms with Smart Class (Digital Teaching System) and a strength of 25-30 students for effective learning.</p><ul class="fac-teach"><li>MCQs and Worksheets</li><li>Virtual Laboratory of simulations</li><li>Mind maps</li><li>Teaching ideas and topic synopsis</li><li>Real life applications</li><li>Web links and diagram marker</li></ul></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 5. Transport + Medical + Safety
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Safety &amp; Convenience</span>'
                                    . '<h2 class="sec-title">Support Facilities</h2>'
                                    . '</div>'
                                    . '<div class="fac-grid-3" data-reveal>'
                                    . '<div class="fac-card"><div class="fc-img"><img src="' . $transImg . '" alt="Transport" loading="lazy"></div><h4>Transport</h4><p>The school has its own best transport facility in Panipat with a fleet of outsourced school buses equipped with CCTVs, designed as per standards and manned by trained drivers. A transport attendant is on board throughout the journey for supervision.</p></div>'
                                    . '<div class="fac-card"><div class="fc-img"><img src="' . $medImg . '" alt="Medical Facility" loading="lazy"></div><h4>Medical Facility</h4><p>The school ensures the health and well-being of every student with an on-campus medical facility and trained staff to handle emergencies.</p></div>'
                                    . '<div class="fac-card"><div class="fc-img"><img src="' . $secImg . '" alt="Security" loading="lazy"></div><h4>Safety &amp; Security</h4><p>The safety and security of our students is our priority. CCTV cameras are installed in and around the school campus to ensure safety at all times. A separate play area with soft play equipment provides a variety of activities and opportunities for exploration.</p></div>'
                                    . '</div>'
                                    . '<p class="fac-tours" data-reveal>At PRAYAAG, we believe that tours and excursions are the perfect way to expand one\'s horizon. The students are persuaded to acquire knowledge and explore new things not just within the boundaries but also beyond them. Every now and then International Educational Exchange Program is organized for the global exposure for the children.</p>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 6. CTA
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'admission-cta',
                            'settings' => [
                                'heading'      => 'Experience Prayaag Facilities',
                                'text'         => 'Visit our campus and see our world-class facilities firsthand. Schedule a tour today.',
                                'button_label' => 'Book a Visit →',
                                'button_url'   => '/contact-us',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Facilities page created with ' . count($sections) . ' sections.');
    }
}
