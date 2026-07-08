<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class SummerCampPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'summer-camp'],
            ['title' => 'Summer Camp', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Summer Camp 2025 | Prayaag International School, Panipat',
                'description' => 'Enroll your child in Prayaag International School\'s exciting Summer Camp. Adventure, creativity, sports, and STEM activities for young explorers.',
                'og_image'    => $b . '01KTQX13TDVNX7JCAX9PTCMMQT.webp',
            ],
        ]);

        $moonImg  = $b . '01KTQX13TDVNX7JCAX9PTCMMQT.webp';
        $musicImg = $b . '01KTQX14S0B05ZG5NZQZQAJR0X.png';
        $natureIc = $b . '01KTQX15YHS0Y920W2T5GQDVW2.svg';
        $paintIc  = $b . '01KTQX17250EZ0RJZH2A0M2QV2.svg';
        $golfIc   = $b . '01KTQX187AGKJCYXKTAWQ3WT6R.svg';
        $robotIc  = $b . '01KTQX19BV6FFK3S743GFSSHPQ.svg';

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
                                'heading'         => 'Summer Camp Adventure Awaits!',
                                'tagline'         => 'Enriching, Exciting, and Educational Camp Experiences for Young Explorers!',
                                'image'           => $moonImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. About
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Summer Fun at Prayaag</span>'
                                    . '<h2 class="sec-title">About Us</h2>'
                                    . '</div>'
                                    . '<div class="camp-about" data-reveal>'
                                    . '<p>Prayaag International School, located in the heart of Panipat, is more than just an educational institution; it\'s a nurturing ground for young minds, a place where aspirations are nurtured and potential is shaped. Since its inception in 2016, the school has been dedicated to providing holistic education that goes beyond textbooks, fostering a stimulating environment where students can excel academically, emotionally, and socially.</p>'
                                    . '<div class="camp-music-icon"><img src="' . $musicImg . '" alt="Music" loading="lazy"></div>'
                                    . '<p class="camp-tagline"><strong>Exciting Adventures Await</strong></p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Activities
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow" style="color:var(--gold-2)">Choose Your Adventure</span>'
                                    . '<h2 class="sec-title" style="color:#fff">From Outdoor Exploration To Creative Workshops</h2>'
                                    . '</div>'
                                    . '<div class="camp-activities" data-reveal>'
                                    . '<div class="camp-act"><img src="' . $natureIc . '" alt="Nature" class="ca-icon"><h4>Nature Exploration</h4><ul><li>Guided nature walks</li><li>Birdwatching sessions</li><li>Outdoor scavenger hunts</li><li>Environmental education</li></ul></div>'
                                    . '<div class="camp-act"><img src="' . $paintIc . '" alt="Arts" class="ca-icon"><h4>Arts &amp; Crafts</h4><ul><li>Painting and drawing</li><li>DIY craft projects</li><li>Pottery and ceramics</li><li>Collaborative mural</li></ul></div>'
                                    . '<div class="camp-act"><img src="' . $golfIc . '" alt="Sports" class="ca-icon"><h4>Sports &amp; Games</h4><ul><li>Fun games</li><li>Team Athletics</li><li>Relay Fun</li><li>Group Challenges</li></ul></div>'
                                    . '<div class="camp-act"><img src="' . $robotIc . '" alt="STEM" class="ca-icon"><h4>STEM Exploration</h4><ul><li>Robotics workshops</li><li>Science experiments</li><li>Hands-on learning</li><li>Problem solving</li></ul></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Schedule & Details
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Plan Your Summer</span>'
                                    . '<h2 class="sec-title">Plan Your Child\'s Perfect Summer Adventure</h2>'
                                    . '</div>'
                                    . '<div class="camp-details" data-reveal>'
                                    . '<div class="cd-item"><span class="cd-label">Dates</span><span class="cd-value">22nd May to 31st May</span></div>'
                                    . '<div class="cd-item"><span class="cd-label">Timing</span><span class="cd-value">7:45 AM to 10:30 AM</span></div>'
                                    . '<div class="cd-item"><span class="cd-label">Charges</span><span class="cd-value">Rs. 1500/- Per Student</span></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 5. Activity 1 Table (Pre-Nursery - II)
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head-sm" data-reveal>'
                                    . '<h3 class="camp-table-title">Summer Camp Schedule For Classes Pre-Nursery - II</h3>'
                                    . '<p class="camp-table-sub">Choose Any One From Activity 1 &amp; Activity 2</p>'
                                    . '</div>'
                                    . '<div class="camp-tables" data-reveal>'
                                    . '<table class="camp-table"><thead><tr><th>Activity 1</th><th>Capacity</th><th>Transport</th></tr></thead><tbody>'
                                    . '<tr><td>Cricket</td><td>35</td><td>Yes</td></tr>'
                                    . '<tr><td>Martial Arts</td><td>35</td><td>Yes</td></tr>'
                                    . '<tr><td>Football</td><td>35</td><td>Yes</td></tr>'
                                    . '<tr><td>Skating</td><td>35</td><td>Yes</td></tr>'
                                    . '<tr><td>Clay</td><td>30</td><td>Yes</td></tr>'
                                    . '<tr><td>Phonics</td><td>30</td><td>Yes</td></tr>'
                                    . '</tbody></table>'
                                    . '<table class="camp-table"><thead><tr><th>Activity 2</th><th>Capacity</th><th>Transport</th></tr></thead><tbody>'
                                    . '<tr><td>Art &amp; Craft</td><td>30</td><td>Yes</td></tr>'
                                    . '<tr><td>Dance - Classical/Western</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Swimming</td><td>60</td><td>Yes</td></tr>'
                                    . '<tr><td>Music - Instrumental</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Calligraphy</td><td>60</td><td>Yes</td></tr>'
                                    . '</tbody></table>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 6. Activity Table (III - VIII)
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head-sm" data-reveal>'
                                    . '<h3 class="camp-table-title">Summer Camp Schedule For Classes III - VIII</h3>'
                                    . '<p class="camp-table-sub">Choose Any One From Activity 1 &amp; Activity 2</p>'
                                    . '</div>'
                                    . '<div class="camp-tables" data-reveal>'
                                    . '<table class="camp-table"><thead><tr><th>Activity 1</th><th>Capacity</th><th>Transport</th></tr></thead><tbody>'
                                    . '<tr><td>Art &amp; Craft</td><td>30</td><td>Yes</td></tr>'
                                    . '<tr><td>Dance - Classic/Western</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Budding Scientists</td><td>30</td><td>Yes</td></tr>'
                                    . '<tr><td>Music Instrumental</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Coding (Only for VI-VIII)</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Fireless Cooking</td><td>25</td><td>Yes</td></tr>'
                                    . '</tbody></table>'
                                    . '<table class="camp-table"><thead><tr><th>Activity 2</th><th>Capacity</th><th>Transport</th></tr></thead><tbody>'
                                    . '<tr><td>Skating</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Swimming</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Football</td><td>30</td><td>Yes</td></tr>'
                                    . '<tr><td>Calligraphy</td><td>25</td><td>Yes</td></tr>'
                                    . '<tr><td>Yoga</td><td>30</td><td>Yes</td></tr>'
                                    . '<tr><td>Chess</td><td>30</td><td>Yes</td></tr>'
                                    . '</tbody></table>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 7. CTA
            [
                'type' => 'flush',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'admission-cta',
                            'settings' => [
                                'heading'      => 'Enroll for Summer Camp',
                                'text'         => 'Give your child an unforgettable summer experience at Prayaag International School.',
                                'button_label' => 'Enquire Now →',
                                'button_url'   => '/contact-us',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Summer Camp page created with ' . count($sections) . ' sections.');
    }
}
