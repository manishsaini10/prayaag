<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class AlumniPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'alumni'],
            ['title' => 'Alumni', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Prayaag International School, Panipat Alumni | Shaping Futures, Inspiring Excellence',
                'description' => 'Explore the accomplished alumni of Prayaag International School in Panipat. Discover their inspiring stories of success and how they continue to shape the world around them.',
                'og_image'    => $b . '01KTQWV3M7KWGRV6KSD23KN9YD.webp',
            ],
        ]);

        $heroImg  = $b . '01KTQWV3M7KWGRV6KSD23KN9YD.webp';
        $alumniImg = $b . '01KTQWYEWSC6Y3231B17VYD2A7.jpg';

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
                                'kicker'          => 'Shaping Futures, Inspiring Excellence',
                                'heading'         => 'Alumni',
                                'tagline'         => 'Welcome to the Prayaag International School, Panipat Alumni Page — a network of individuals who have excelled in their chosen fields and carried the values of our institution to every corner of the world.',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Welcome
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Our Alumni Community</span>'
                                    . '<h2 class="sec-title">Welcome Alumni</h2>'
                                    . '</div>'
                                    . '<div class="alumni-content" data-reveal>'
                                    . '<p>At Prayaag International School, we take immense pride in our alumni community — a network of individuals who have not only excelled in their chosen fields but have also carried the values and ethos of our institution to every corner of the world. This page is a dedicated space to celebrate your achievements, reconnect with old friends, and continue being a part of the vibrant Prayaag family.</p>'
                                    . '</div>'
                                    . '<div class="alumni-img-wrap" data-reveal>'
                                    . '<img src="' . $alumniImg . '" alt="Prayaag International School Alumni" loading="lazy">'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Alumni pillars
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Stay Connected · Share · Give Back</span>'
                                    . '<h2 class="sec-title">Alumni Pillars</h2>'
                                    . '</div>'
                                    . '<div class="alumni-pillars" data-reveal>'
                                    . '<div class="ap-card"><div class="ap-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v12H4zM4 6l8 5 8-5"/></svg></div><h4>Stay Connected</h4><p>We believe that the bond between the school and its alumni is everlasting. Stay connected with us to keep up with the latest happenings, events, and developments at Prayaag International School. Update your contact information and follow us on social media.</p></div>'
                                    . '<div class="ap-card"><div class="ap-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3 6 6 .5-4.5 4.5L18 20l-6-3.5L6 20l1.5-7L3 8.5 9 8l3-6z"/></svg></div><h4>Share Your Journey</h4><p>Your journey since leaving Prayaag is a story worth sharing. We invite you to share your experiences, accomplishments, and milestones with us. Whether it\'s a groundbreaking project, a new business venture, or a personal achievement, your story can inspire the current students and fellow alumni.</p></div>'
                                    . '<div class="ap-card"><div class="ap-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div><h4>Giving Back</h4><p>As alumni, you are an integral part of our school\'s legacy. Your support can make a difference in the lives of current students. Whether through scholarships, guest lectures, or mentoring programs, your contribution can shape the next generation of leaders and thinkers.</p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Reunions + Contact
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="alumni-bottom" data-reveal>'
                                    . '<div class="alumni-bottom-card"><h4>Reunions &amp; Events</h4><p>Reunions are a perfect opportunity to relive the memories, create new ones, and reconnect with classmates and teachers. Keep an eye on this section for updates about upcoming reunions and events. Don\'t miss the chance to come back to where it all began.</p><p>To ensure you don\'t miss out on any updates, please keep your contact information updated. Let us know about your achievements and milestones so we can celebrate them together.</p></div>'
                                    . '<div class="alumni-bottom-card navy"><h4>Get in Touch</h4><p>For any queries, suggestions, or to share your updates, please contact our Alumni Relations team.</p><p class="alumni-email"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16v12H4zM4 6l8 5 8-5"/></svg> alumni@pisp.in</p><a class="alumni-btn" href="/contact-us">Mail Us →</a></div>'
                                    . '</div>'
                                    . '<p class="alumni-thanks" data-reveal>Thank you for being an integral part of the Prayaag International School family. Your journey is an inspiration to us all, and we look forward to celebrating your continued success. Stay connected, stay engaged, and keep the Prayaag spirit alive!</p>',
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
                                'heading'      => 'Share Your Story',
                                'text'         => 'We\'d love to hear from our alumni. Share your journey and achievements with us.',
                                'button_label' => 'Contact Alumni Team →',
                                'button_url'   => '/contact-us',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Alumni page created with ' . count($sections) . ' sections.');
    }
}
