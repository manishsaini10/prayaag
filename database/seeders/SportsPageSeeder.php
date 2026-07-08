<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class SportsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'sports'],
            ['title' => 'Sports', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Sports Facilities at Prayaag International School, Panipat',
                'description' => 'Explore world-class sports facilities at Prayaag International School, Panipat. Encouraging fitness, teamwork, discipline, and all-round student development.',
                'og_image'    => $b . '01KTQWYM5VD2N9A76SW3VZQJVF.jpg',
            ],
        ]);

        $heroImg   = $b . '01KTQWYM5VD2N9A76SW3VZQJVF.jpg';
        $shootImg  = $b . '01KTQWX7SDBEP94AWKQVJH2953.jpg';
        $basketImg = $b . '01KTQWX9BVVAN25SKC3GH0EAGA.jpg';
        $tennisImg = $b . '01KTQWYNBFN91ZSGQNMF85PEC1.jpg';
        $cricketImg = $b . '01KTQWYPBN2GYVFHZ1FQC42K9V.jpg';
        $ttImg     = $b . '01KTQWYQEQJBNHKRH2WVJ8D6FV.jpg';
        $badmImg   = $b . '01KTQWYRFZDA57GBVHQTMS7ARC.jpg';
        $swimImg   = $b . '01KTQWYSFZRB4V36269SSB2WKW.jpg';
        $skateImg  = $b . '01KTQWYTJ4Z0SCZ4KPAB5GZE6R.jpg';
        $volleyImg = $b . '01KTQWYVS04HXEJ2QXDHMAPES5.jpg';

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
                                'kicker'          => 'Fitness · Teamwork · Discipline',
                                'heading'         => 'Sports',
                                'tagline'         => 'Sports help to build character and educate the importance of discipline in life. It instills respect for rules and allows children to learn the value of self control.',
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
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Building Champions</span>'
                                    . '<h2 class="sec-title">Sports at Prayaag</h2>'
                                    . '</div>'
                                    . '<div class="sports-desc" data-reveal>'
                                    . '<p>We strive to provide our students with one of the best indoor and outdoor sporting infrastructures in Panipat to prepare our children for the highly competitive world of sports.</p>'
                                    . '</div>'
                                    . '<div class="sports-list" data-reveal>'
                                    . '<div class="sl-item"><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span>Shooting Range, Football and Hockey ground with a 5-lane track</span></div>'
                                    . '<div class="sl-item"><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span>International standard turf courts for Volleyball, Basketball, Badminton and Lawn Tennis</span></div>'
                                    . '<div class="sl-item"><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span>Swimming Pool with a Splash Pool</span></div>'
                                    . '<div class="sl-item"><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span>Gym for functional training of students</span></div>'
                                    . '<div class="sl-item"><svg viewBox="0 0 24 24" fill="none" stroke="#c79a3b" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span>Cricket, Table Tennis, Yoga, Chess and Karate</span></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Gallery Grid
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Our Facilities</span>'
                                    . '<h2 class="sec-title">Sports Gallery</h2>'
                                    . '</div>'
                                    . '<div class="sports-grid" data-reveal>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $shootImg . '" alt="Shooting Range" loading="lazy"></div><div class="sgc-label">Shooting Range</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $basketImg . '" alt="Basketball" loading="lazy"></div><div class="sgc-label">Basketball Court</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $tennisImg . '" alt="Lawn Tennis" loading="lazy"></div><div class="sgc-label">Lawn Tennis</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $cricketImg . '" alt="Cricket" loading="lazy"></div><div class="sgc-label">Cricket Ground</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $badmImg . '" alt="Badminton" loading="lazy"></div><div class="sgc-label">Badminton</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $swimImg . '" alt="Swimming" loading="lazy"></div><div class="sgc-label">Swimming Pool</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $skateImg . '" alt="Skating" loading="lazy"></div><div class="sgc-label">Skating Rink</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $ttImg . '" alt="Table Tennis" loading="lazy"></div><div class="sgc-label">Table Tennis</div></div>'
                                    . '<div class="sg-card"><div class="sgc-img"><img src="' . $volleyImg . '" alt="Volleyball" loading="lazy"></div><div class="sgc-label">Volleyball</div></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. CTA
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'admission-cta',
                            'settings' => [
                                'heading'      => 'Join the Champions',
                                'text'         => 'Give your child access to world-class sports facilities and coaching at Prayaag International School.',
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

        $this->command?->info('Sports page created with ' . count($sections) . ' sections.');
    }
}
