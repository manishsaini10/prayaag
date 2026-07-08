<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class BestSchoolPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'best-school-in-panipat'],
            ['title' => 'Best CBSE School in Panipat', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'PISP, Best CBSE School in Panipat | Top Schools in Samalkha',
                'description' => 'Top School in Panipat 2025-26. Best CBSE Affiliated Play/Preschool, Secondary and Senior Sec. Schools in Panipat. Top Schools in Samalkha.',
                'og_image'    => $b . '01KTQWVF2TH3ES88Y987P5E4QM.webp',
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
                                'kicker'          => 'Best CBSE School in Panipat',
                                'heading'         => 'Prayaag International School',
                                'tagline'         => 'Top School in Panipat 2025-26. Best CBSE Affiliated Play/Preschool, Secondary and Senior Secondary School.',
                                'image'           => $b . '01KTQWVF2TH3ES88Y987P5E4QM.webp',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Welcome message from Director
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Welcome</span>'
                                    . '<h2 class="sec-title">Welcome to Prayaag</h2>'
                                    . '</div>'
                                    . '<div class="best-intro" data-reveal>'
                                    . '<div class="bi-grid">'
                                    . '<div class="bi-dir"><img src="' . $b . '01KTQWW0KDMYKAGQS1XNT1R7V7.webp" alt="Director" loading="lazy"><h4>Mrs. Anju Gupta</h4><span>Director</span></div>'
                                    . '<div class="bi-msg"><p>As we continue our journey to nurture young minds and shape the leaders of tomorrow, we are delighted to have this opportunity to connect with you and share our vision.</p><p>At Prayaag International School, we believe that education is a shared commitment between dedicated teachers, motivated students and supportive parents. Our mission is to provide a dynamic and nurturing environment where every child can discover his unique potential and develop into a confident, responsible and well-rounded individual.</p><p>We are committed to fostering a love for learning by blending academic excellence with a focus on creativity, critical thinking and a strong sense of community.</p></div>'
                                    . '</div>'
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
                                    . '<h2 class="sec-title">Principal\'s Message</h2>'
                                    . '</div>'
                                    . '<div class="best-principal" data-reveal>'
                                    . '<div class="bp-img"><img src="' . $b . '01KTQWW217PTPXXT14ATFA7F48.webp" alt="Principal" loading="lazy"><h4>Mrs. Mamta Sachdeva</h4><span>Principal</span></div>'
                                    . '<div class="bp-msg"><p>At Prayaag International School, education is treated as a serious trust, not a transaction. The School is committed to deep, disciplined learning rooted in Indian cultural values while equipping students with essential 21st century competencies.</p><p>In a world shaped by artificial intelligence, the school adopts a measured and vigilant approach with guidance to treat AI as an aid, not a substitute; to question outputs, uphold academic honesty, build resilience to think, write and solve independently in a technology-rich environment.</p><p>This commitment is implemented through an academic leadership, a rigorously selected and professionally developed faculty, structured and transparent academic systems, and classrooms that prioritise understanding over rote, application over display, and most importantly character over convenience.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Features grid
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Our Campus</span>'
                                    . '<h2 class="sec-title">Life at Prayaag</h2>'
                                    . '</div>'
                                    . '<div class="best-features" data-reveal>'
                                    . '<a class="bf-card" href="/school-trip"><div class="bf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/></svg></div><h4>School Trip</h4></a>'
                                    . '<a class="bf-card" href="/labs"><div class="bf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"/><path d="M8.5 2h7"/><line x1="12" y1="2" x2="12" y2="9.5"/></svg></div><h4>Labs</h4></a>'
                                    . '<a class="bf-card" href="/sports"><div class="bf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg></div><h4>Sports</h4></a>'
                                    . '<a class="bf-card" href="/library"><div class="bf-icon" style="background:#f0e8fc;color:#6b3fa0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><h4>Library</h4></a>'
                                    . '<a class="bf-card" href="/classrooms"><div class="bf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 10v6M2 10l10-5 10 5"/><path d="M6 12v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-5"/></svg></div><h4>Classrooms</h4></a>'
                                    . '<a class="bf-card" href="/safety-security"><div class="bf-icon" style="background:#e8fcf0;color:#1a7a4a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h4>Safety &amp; Security</h4></a>'
                                    . '<a class="bf-card" href="/transportations"><div class="bf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="8" rx="2"/><path d="M7 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM17 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg></div><h4>Transportation</h4></a>'
                                    . '<a class="bf-card" href="/unesco"><div class="bf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg></div><h4>UNESCO</h4></a>'
                                    . '<a class="bf-card" href="/events"><div class="bf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><h4>Events</h4></a>'
                                    . '<a class="bf-card" href="/photo-gallery"><div class="bf-icon" style="background:#f0e8fc;color:#6b3fa0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div><h4>Photo Gallery</h4></a>'
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
                                'html' => '<div class="best-cta" data-reveal><h3>Admissions Open 2026-27</h3><p>Begin your journey at the best CBSE school in Panipat.</p><a class="btn" href="/admissions">Apply Now →</a></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Best School in Panipat page created with ' . count($sections) . ' sections.');
    }
}
