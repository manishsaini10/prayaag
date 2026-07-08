<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class ContactUsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'contact-us'],
            ['title' => 'Contact Us', 'status' => 'published']
        );

        $b = '/storage/media/imported/';

        $page->update([
            'status' => 'published',
            'seo'    => [
                'title'       => 'Contact Us — Prayaag International School, Panipat',
                'description' => 'Get in touch with Prayaag International School, Panipat. Call, email, or visit our campus. We\'d love to hear from you.',
                'og_image'    => $b . '01KTQWWFY5KNKSQT56C9VDM4PE.webp',
            ],
        ]);

        $heroImg    = $b . '01KTQWWFY5KNKSQT56C9VDM4PE.webp';
        $mailIcon   = $b . '01KTQWWGSMHS4ZJ5PHSMVT665S.png';
        $mapIcon    = $b . '01KTQWWHZHH61MX243DTPS7WH9.png';
        $phoneIcon  = $b . '01KTQWWJW3M6QBW00TB43PH07Z.png';

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
                                'kicker'          => 'We\'d Love to Hear From You',
                                'heading'         => 'Reach Us',
                                'tagline'         => 'Contact us for any of your queries. Our team is here to help.',
                                'image'           => $heroImg,
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 2. Contact Cards
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Get in Touch</span>'
                                    . '<h2 class="sec-title">Contact</h2>'
                                    . '</div>'
                                    . '<div class="contact-cards" data-reveal>'
                                    . '<div class="contact-card"><div class="cc-icon"><img src="' . $mailIcon . '" alt="Email"></div><h4>Send us an email</h4><p><a href="mailto:mailus@pisp.in">mailus@pisp.in</a></p></div>'
                                    . '<div class="contact-card"><div class="cc-icon"><img src="' . $mapIcon . '" alt="Address"></div><h4>Visit our School</h4><p>Opp. New Police Lines<br>Near Indraprastha Institute of Medical Sciences<br>NH-44, Panipat-132103, Haryana</p></div>'
                                    . '<div class="contact-card"><div class="cc-icon"><img src="' . $phoneIcon . '" alt="Phone"></div><h4>Call us</h4><p><a href="tel:919350748851">+91 9350748851</a>, <a href="tel:01802565555">+91 180-2565555</a>, <a href="tel:01802575555">2575555</a></p></div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 3. Contact Form + Map
            [
                'type' => 'alt',
                'rows' => [[
                    'columns' => [[
                        'width'   => 6,
                        'widgets' => [[
                            'type' => 'contact-form',
                            'settings' => [
                                'heading' => 'Send us a Message',
                            ],
                        ]],
                    ], [
                        'width'   => 6,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="contact-map-wrap" data-reveal><h4 style="margin-bottom:1rem;color:var(--ink)">Find Us</h4><div class="contact-map"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d55659.904628583856!2d76.986936!3d29.319182999999995!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd337897af9217763!2sPrayaag%20International%20School%2C%20Panipat!5e0!3m2!1sen!2sin!4v1642017535643!5m2!1sen!2sin" width="100%" height="400" style="border:0;border-radius:12px" allowfullscreen="" loading="lazy"></iframe></div></div>',
                            ],
                        ]],
                    ]],
                ]],
            ],

            // 4. Social / Follow Us
            [
                'type' => 'section',
                'rows' => [[
                    'columns' => [[
                        'width'   => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => [
                                'html' => '<div class="sec-head" data-reveal>'
                                    . '<span class="eyebrow">Stay Connected</span>'
                                    . '<h2 class="sec-title">Follow us</h2>'
                                    . '</div>'
                                    . '<div class="contact-social" data-reveal>'
                                    . '<p>Follow Prayaag International School on social media for the latest updates, events, and achievements.</p>'
                                    . '<div class="contact-social-links">'
                                    . '<a href="https://www.facebook.com/PrayaagInternationalSchool" target="_blank" rel="noopener" class="cs-link" style="background:#1877f2">Facebook</a>'
                                    . '<a href="https://www.instagram.com/prayaag_international_school/" target="_blank" rel="noopener" class="cs-link" style="background:#e4405f">Instagram</a>'
                                    . '<a href="https://www.youtube.com/channel/PrayaagInternationalSchool" target="_blank" rel="noopener" class="cs-link" style="background:#ff0000">YouTube</a>'
                                    . '<a href="https://www.linkedin.com/school/prayaag-international-school/" target="_blank" rel="noopener" class="cs-link" style="background:#0a66c2">LinkedIn</a>'
                                    . '</div>'
                                    . '</div>',
                            ],
                        ]],
                    ]],
                ]],
            ],
        ];

        app(PageTreeService::class)->sync($page, $sections);
        app(PageRenderer::class)->forget($page);

        $this->command?->info('Contact Us page created with ' . count($sections) . ' sections.');
    }
}
