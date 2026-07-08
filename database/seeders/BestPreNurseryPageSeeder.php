<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class BestPreNurseryPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'best-pre-nursery-school-in-panipat'],
            ['title' => 'Best Pre Nursery School in Panipat', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Best Pre Nursery School in Panipat | Prayaag International School',
                'description' => 'Looking for the best pre nursery school in Panipat? Prayaag International School offers excellent early childhood education with play-based learning, caring teachers, and a safe environment.',
                'og_image'    => $b . '01KTQWV3M7KWGRV6KSD23KN9YD.webp',
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
                                'kicker'          => 'Best Pre Nursery School in Panipat',
                                'heading'         => 'Prayaag International School',
                                'tagline'         => 'Where little learners take their first steps towards a bright future. Play-based learning, experienced teachers, and a nurturing environment for children aged 2-5 years.',
                                'image'           => $b . '01KTQWV3M7KWGRV6KSD23KN9YD.webp',
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
                                    . '<span class="eyebrow">Early Childhood Education</span>'
                                    . '<h2 class="sec-title">Best Pre Nursery School in Panipat</h2>'
                                    . '</div>'
                                    . '<div class="seo-content" data-reveal>'
                                    . '<p>When it comes to early childhood education, choosing the right pre nursery school is one of the most important decisions parents make. At Prayaag International School, we understand the significance of these formative years and have designed our pre nursery program to provide the perfect foundation for lifelong learning.</p>'
                                    . '<p>As the <strong>best pre nursery school in Panipat</strong>, we offer a warm, stimulating, and safe environment where children aged 2 to 5 years can explore, discover, and grow. Our curriculum is carefully crafted to balance structured learning with free play, ensuring that each child develops at their own pace while building essential social, emotional, and cognitive skills.</p>'
                                    . '<p>What sets us apart as a <strong>top pre nursery school in Panipat</strong> is our dedicated team of early childhood educators who are not just teachers but nurturers. They understand the unique needs of young learners and create a classroom atmosphere filled with warmth, encouragement, and joy. Every child is celebrated for their individuality and encouraged to express themselves freely.</p>'
                                    . '<p>Our pre nursery program focuses on:</p>'
                                    . '<ul><li><strong>Play-Based Learning:</strong> Children learn best through play. Our activities are designed to make learning fun and engaging.</li>'
                                    . '<li><strong>Language Development:</strong> Storytelling, rhymes, and conversations help build strong communication skills.</li>'
                                    . '<li><strong>Fine &amp; Gross Motor Skills:</strong> Art, crafts, outdoor play, and movement activities support physical development.</li>'
                                    . '<li><strong>Social &amp; Emotional Growth:</strong> Group activities teach sharing, cooperation, and empathy.</li>'
                                    . '<li><strong>Creative Expression:</strong> Music, dance, and art allow children to explore their creativity.</li></ul>'
                                    . '<p>The <strong>best pre-primary school in Panipat</strong> recognizes that parents are partners in education. We maintain open communication with families through regular updates, parent-teacher meetings, and special events that bring the school community together. Our safe and secure campus, with child-friendly infrastructure and constant supervision, gives parents complete peace of mind.</p>'
                                    . '<p>If you are searching for the <strong>best nursery school in Panipat</strong>, look no further than Prayaag International School. Our junior wing is designed specifically for young learners, with colorful classrooms, age-appropriate learning materials, and a sprawling outdoor play area where children can run, jump, and explore nature.</p>'
                                    . '<p>We invite you to visit our campus and see for yourself why Prayaag International School is recognized as the <strong>best pre nursery school in Panipat</strong>. Meet our teachers, explore our facilities, and experience the warm, nurturing atmosphere that makes our school a home away from home for your little one.</p>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Why Choose Us
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
                                    . '<h2 class="sec-title">Why We Are the Best Pre Nursery School in Panipat</h2>'
                                    . '</div>'
                                    . '<div class="best-features" data-reveal>'
                                    . '<div class="bf-card"><div class="bf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></div><h4>Experienced Teachers</h4><p>Our early childhood educators are highly trained and passionate about teaching young children.</p></div>'
                                    . '<div class="bf-card"><div class="bf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div><h4>Child-Centric Approach</h4><p>Every child is unique. Our curriculum adapts to individual learning styles and paces.</p></div>'
                                    . '<div class="bf-card"><div class="bf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h4>Safe &amp; Secure Campus</h4><p>Child-safe infrastructure with CCTV surveillance and constant supervision ensures complete safety.</p></div>'
                                    . '<div class="bf-card"><div class="bf-icon" style="background:#f0e8fc;color:#6b3fa0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><h4>Play-Based Curriculum</h4><p>Research-backed play-based learning that makes education fun and effective.</p></div>'
                                    . '<div class="bf-card"><div class="bf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg></div><h4>Holistic Development</h4><p>Focus on social, emotional, physical, and cognitive development through diverse activities.</p></div>'
                                    . '<div class="bf-card"><div class="bf-icon" style="background:#e8fcf0;color:#1a7a4a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div><h4>Small Class Sizes</h4><p>Individual attention ensures every child gets the support they need to thrive.</p></div>'
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
                                    . '<span class="eyebrow">From the Principal</span>'
                                    . '<h2 class="sec-title">A Message from Our Principal</h2>'
                                    . '</div>'
                                    . '<div class="best-principal" data-reveal>'
                                    . '<div class="bp-img"><img src="' . $b . '01KTQWW217PTPXXT14ATFA7F48.webp" alt="Principal" loading="lazy"><h4>Mrs. Mamta Sachdeva</h4><span>Principal</span></div>'
                                    . '<div class="bp-msg"><p>At Prayaag International School, we believe that the early years of a child\'s life are the most crucial for laying the foundation of lifelong learning. Our pre nursery program is designed with love, care, and scientific understanding of how young children learn best.</p><p>We create a nurturing environment where every child feels safe, valued, and excited to come to school each day. Our teachers are not just educators — they are nurturers who understand the unique needs of every little learner. Through play, exploration, and guided discovery, we help children develop confidence, curiosity, and a love for learning that will stay with them throughout their lives.</p><p>Choosing the right pre nursery school is the first step in your child\'s educational journey. We invite you to visit our campus, meet our team, and experience the Prayaag difference. Welcome to the Prayaag family!</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 5. CTA
            [
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="best-cta" data-reveal><h3>Admissions Open 2026-27</h3><p>Give your child the best start at Prayaag International School — the best pre nursery school in Panipat.</p><a class="btn" href="/admissions">Enroll Now →</a></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Best Pre Nursery School page created with ' . count($sections) . ' sections.');
    }
}
