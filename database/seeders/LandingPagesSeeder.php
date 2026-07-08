<?php

namespace Database\Seeders;

use App\Core\Builder\PageRenderer;
use App\Core\Builder\PageTreeService;
use App\Models\Page;
use Illuminate\Database\Seeder;

class LandingPagesSeeder extends Seeder
{
    public function run(): void
    {
        $b = '/storage/media/imported/';

        $pages = [];

        // 1. Thank You (junior)
        $pages[] = [
            'slug' => 'thank-you',
            'title' => 'Thank You',
            'seo' => ['title' => 'Thank You — Prayaag International School, Panipat', 'description' => 'Thank you for your enquiry. Our team will contact you shortly.'],
            'sections' => [[
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="ty-wrap" data-reveal>
                                    <h1 class="ty-title">Thank You for Submitting</h1>
                                    <p class="ty-lead">Thank you for joining our Junior Program at Prayaag International School, Panipat. We value your interest and look forward to guiding your child towards academic excellence and personal growth.</p>
                                    <p>Please feel free to reach out to us at <a href="tel:+919350748851" style="color:#f99b1c;">+91 93507 48851</a> or +91 180 256 5555 / 257 5555.</p>
                                    <div class="ty-btns">
                                        <a class="btn" href="/">Back to Home</a>
                                        <a class="btn btn-outline" href="tel:+919350748851">Call Now</a>
                                        <a class="btn btn-outline" href="https://wa.me/919350748851">WhatsApp</a>
                                    </div>
                                </div>
                            '],
                        ]],
                    ]],
                ]],
            ]],
        ];

        // 2. Thank You for Senior
        $pages[] = [
            'slug' => 'thank-you-for-senior',
            'title' => 'Thank You for Senior',
            'seo' => ['title' => 'Thank You — Senior Wing Enquiry | Prayaag International School', 'description' => 'Your senior wing admission enquiry has been submitted. We will contact you shortly.'],
            'sections' => [[
                'type' => 'navy',
                'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="ty-wrap" data-reveal>
                                    <h1 class="ty-title">Thank You for Your Enquiry</h1>
                                    <p class="ty-lead">Your admission enquiry has been successfully submitted.</p>
                                    <p>Our admission team will contact you shortly to guide you through the admission process for the 2026-27 academic session. If you need immediate assistance, please call or WhatsApp us.</p>
                                    <div class="ty-btns">
                                        <a class="btn" href="/">Learn More</a>
                                        <a class="btn btn-outline" href="tel:+919350748851">Call Now</a>
                                        <a class="btn btn-outline" href="https://wa.me/919350748851">WhatsApp</a>
                                    </div>
                                </div>
                            '],
                        ]],
                    ]],
                ]],
            ]],
        ];

        // 3. Thank You for Summer Camp
        $pages[] = [
            'slug' => 'thank-you-for-summer-camp',
            'title' => 'Thank You for Summer Camp',
            'seo' => ['title' => 'Thank You — Summer Camp Registration | Prayaag International School', 'description' => 'Thank you for registering your child for Summer Camp at Prayaag International School.', 'og_image' => $b . '01KTQWZ4ZY7Z2TFZRGKTY93JDV.jpg'],
            'sections' => [
                ['type' => 'flush', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'hero',
                            'settings' => ['kicker' => 'Summer Camp', 'heading' => 'Thank You for Submitting', 'tagline' => 'Thank you for registering your child for Summer Camp! We are thrilled to have them join us for an exciting and unforgettable experience.', 'image' => $b . '01KTQWZ4ZY7Z2TFZRGKTY93JDV.jpg'],
                        ]],
                    ]],
                ]]],
                ['type' => 'section', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '<div class="ty-btns" style="text-align:center;margin-top:1.5rem" data-reveal><a class="btn" href="/summer-camp">Back to Summer Camp</a> <a class="btn btn-outline" href="/">Home</a></div>'],
                        ]],
                    ]],
                ]]],
            ],
        ];

        // 4. Junior Landing Page
        $pages[] = [
            'slug' => 'junior-landing-page',
            'title' => 'Junior Wing — Admissions',
            'seo' => ['title' => 'Junior Wing Admission — Prayaag International School, Panipat', 'description' => 'Welcome your little learner to Prayaag International School\'s Junior Wing. Pre/primary school admissions open.', 'og_image' => $b . '01KTQWVZBC5D4K49NNHK08JQ69.jpg'],
            'sections' => [
                ['type' => 'flush', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'hero',
                            'settings' => ['kicker' => 'Junior Wing', 'heading' => 'Welcoming Our Little Learners,<br>To Your Favorite Pre/Primary School!', 'tagline' => 'As you begin your child\'s educational journey, choosing the right school is important. Our team of highly skilled teachers is committed to building a strong foundation.', 'image' => $b . '01KTQWVZBC5D4K49NNHK08JQ69.jpg'],
                        ]],
                    ]],
                ]]],
                ['type' => 'section', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [
                            ['type' => 'html', 'settings' => ['html' => '<div class="sec-head" data-reveal><span class="eyebrow">Admissions</span><h2 class="sec-title">Junior Wing Admission Form</h2></div><p style="text-align:center;max-width:600px;margin:0 auto 1.5rem" data-reveal>Fill in the form below, and we will get in touch with you to resolve the pre/primary school admission queries at the earliest.</p>']],
                            ['type' => 'contact_form', 'settings' => []],
                        ],
                    ]],
                ]]],
                ['type' => 'alt', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="sec-head" data-reveal><span class="eyebrow">How to Enroll</span><h2 class="sec-title">Admission Process</h2></div>
                                <p style="text-align:center;max-width:600px;margin:0 auto 1.5rem" data-reveal>We at Prayaag, believe that initial days at school bring the best out of young learners. It\'s as simple as ABC to enroll.</p>
                                <div class="jr-steps" data-reveal>
                                    <div class="js-card"><div class="js-num">1</div><h4 style="font-size:.95rem;margin-bottom:.35rem;color:var(--ink)">Fill Form</h4><p>Fill in the form below and we will get in touch.</p><small>NUR-I: One on One Interaction<br>II-V: Admission Test &amp; Interaction</small></div>
                                    <div class="js-card"><div class="js-num">2</div><h4 style="font-size:.95rem;margin-bottom:.35rem;color:var(--ink)">Counselling</h4><p>Our Admission Counsellors will help you with the entire process.</p></div>
                                    <div class="js-card"><div class="js-num">3</div><h4 style="font-size:.95rem;margin-bottom:.35rem;color:var(--ink)">Campus Visit</h4><p>Visit Prayaag International School for a guided tour.</p></div>
                                    <div class="js-card"><div class="js-num">4</div><h4 style="font-size:.95rem;margin-bottom:.35rem;color:var(--ink)">Enrolment</h4><p>Enrolment process completion with assistance from our counsellors.</p></div>
                                </div>
                            '],
                        ]],
                    ]],
                ]]],
                ['type' => 'section', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="sec-head" data-reveal><span class="eyebrow">Programs</span><h2 class="sec-title">Curriculum &amp; Programs</h2></div>
                                <div class="lp-programs" data-reveal>
                                    <div class="lp-prog" style="border-top:4px solid #e77151"><h3 style="color:#e77151">Program 1: From Tiny Sprout to Vibrant Blossom <small>(3-4 yrs)</small></h3><p>Nurturing the whole child.</p></div>
                                    <div class="lp-prog" style="border-top:4px solid #f99b1c"><h3 style="color:#f99b1c">Program 2: Seedlings of Knowledge <small>(4-5 yrs)</small></h3><p>Reading, writing, arithmetic, social skills, creative thinking.</p></div>
                                    <div class="lp-prog" style="border-top:4px solid #1f5aa8"><h3 style="color:#1f5aa8">Program 3: Flower Blooming in Breeze <small>(5-6 yrs)</small></h3><p>Mastering fundamental skills for academic foundation.</p></div>
                                </div>
                            '],
                        ]],
                    ]],
                ]]],
                ['type' => 'navy', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '<div class="lp-cta" data-reveal><h3>Join Us in Shaping the Leaders of Tomorrow</h3><a class="btn" href="/admissions">Apply Now →</a></div>'],
                        ]],
                    ]],
                ]]],
            ],
        ];

        // 5. Senior Landing Page
        $pages[] = [
            'slug' => 'senior-landing-page',
            'title' => 'Senior Wing — Admissions',
            'seo' => ['title' => 'Senior Wing Admission — Prayaag International School, Panipat', 'description' => 'Empowering young minds for a brighter tomorrow. Enroll at Prayaag International School Senior Wing.', 'og_image' => $b . '01KTQX20J1V9QRSAD6N7JEN7QN.jpg'],
            'sections' => [
                ['type' => 'flush', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'hero',
                            'settings' => ['kicker' => 'Senior Wing', 'heading' => 'Empowering Young Minds<br>for a Brighter Tomorrow', 'tagline' => 'Enroll at Prayaag International School — where academic excellence meets holistic development.', 'image' => $b . '01KTQX20J1V9QRSAD6N7JEN7QN.jpg'],
                        ]],
                    ]],
                ]]],
                ['type' => 'section', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [
                            ['type' => 'html', 'settings' => ['html' => '<div class="sec-head" data-reveal><span class="eyebrow">Admissions</span><h2 class="sec-title">Start Online Admission Process Now</h2></div><p style="text-align:center;max-width:650px;margin:0 auto 1.5rem" data-reveal>Prayaag International School is committed to providing a seamless and open admission process.</p>']],
                            ['type' => 'contact_form', 'settings' => []],
                        ],
                    ]],
                ]]],
                ['type' => 'alt', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="sec-head" data-reveal><span class="eyebrow">Simple Steps</span><h2 class="sec-title">Admission Process</h2></div>
                                <div class="lp-steps" data-reveal>
                                    <div class="ls-card"><div class="ls-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><h4>1. Fill Application Form</h4><p>Submit your application online.</p></div>
                                    <div class="ls-card"><div class="ls-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div><h4>2. Attend Counselling</h4><p>Campus tour, entrance test, interaction.</p></div>
                                    <div class="ls-card"><div class="ls-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div><h4>3. Register Admission</h4><p>Complete the registration formalities.</p></div>
                                    <div class="ls-card"><div class="ls-icon" style="background:#f0e8fc;color:#6b3fa0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><h4>4. Confirmation</h4><p>Welcome! Your child is now a Prayaagian!</p></div>
                                </div>
                                <div class="lp-docs" data-reveal>
                                    <h3>Documents Required for Admission</h3>
                                    <ul>
                                        <li>6 Passport size photographs of the student</li>
                                        <li>3 Passport size photographs of the mother</li>
                                        <li>3 Passport size photographs of the father</li>
                                        <li>1 Passport size photograph of guardian (if any)</li>
                                        <li>Birth certificate of child</li>
                                        <li>Copy of Aadhar cards &mdash; Student, Father, Mother &amp; Guardian</li>
                                        <li>Report card of previous class</li>
                                        <li>Online SLC of previous class (Grade 1 onwards) / Manual SLC (Nursery-KG)</li>
                                        <li>Family ID</li>
                                    </ul>
                                </div>
                            '],
                        ]],
                    ]],
                ]]],
                ['type' => 'section', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="sec-head" data-reveal><span class="eyebrow">Facilities</span><h2 class="sec-title">State-of-the-Art Facilities</h2></div>
                                <div class="lp-facilities" data-reveal>
                                    <div class="lf-card"><h4>Classrooms</h4><p>Vibrant, interactive spaces with modern technology.</p></div>
                                    <div class="lf-card"><h4>Playgrounds</h4><p>Expansive grounds for soccer, basketball, cricket, tennis, volleyball.</p></div>
                                    <div class="lf-card"><h4>Swimming Pool</h4><p>State-of-the-art pool with professional coaching.</p></div>
                                    <div class="lf-card"><h4>Shooting Range</h4><p>Secure environment for precision and discipline.</p></div>
                                    <div class="lf-card"><h4>Library</h4><p>Hub of knowledge with diverse books and research materials.</p></div>
                                    <div class="lf-card"><h4>Laboratories</h4><p>Cutting-edge labs for hands-on science experiments.</p></div>
                                    <div class="lf-card"><h4>Transportation</h4><p>Fleet of well-maintained AC vehicles with experienced drivers.</p></div>
                                </div>
                            '],
                        ]],
                    ]],
                ]]],
                ['type' => 'navy', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '<div class="lp-cta" data-reveal><h3>Ready to Begin?</h3><a class="btn" href="/admissions">Apply Now →</a></div>'],
                        ]],
                    ]],
                ]]],
            ],
        ];

        // 6. Google Ads Landing Page
        $pages[] = [
            'slug' => 'landing-page-google-ads',
            'title' => 'Best CBSE School — Admissions Open',
            'seo' => ['title' => 'Best CBSE School in Panipat | Admissions Open 2026-27', 'description' => 'Top Rated School Near Samalkha. Smart Classes, Expert Faculty, Safe Campus, Holistic Development. Admissions Open 2026-27.', 'og_image' => $b . '01KTQWVF2TH3ES88Y987P5E4QM.webp'],
            'sections' => [
                ['type' => 'flush', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [
                            ['type' => 'html', 'settings' => ['html' => '<div class="google-lp" data-reveal><div class="gl-hero"><div class="gh-badge">Admissions Open 2026-27</div><h1>Best CBSE School in Panipat</h1><p>Top Rated School Near Samalkha | Smart Classes &bull; Expert Faculty &bull; Safe Campus &bull; Holistic Development</p><div class="gh-ctas"><a class="gh-btn gh-btn-primary" href="tel:+919350748851">📞 Call Now</a><a class="gh-btn gh-btn-whatsapp" href="https://wa.me/919350748851">💬 WhatsApp</a></div><div class="gh-form-wrap"><h3>📅 Book Free Campus Visit</h3><p>Fill in the form below and we\'ll get back to you.</p></div></div></div>']],
                            ['type' => 'contact_form', 'settings' => []],
                        ],
                    ]],
                ]]],
                ['type' => 'section', 'rows' => [[
                    'columns' => [[
                        'width' => 12,
                        'widgets' => [[
                            'type' => 'html',
                            'settings' => ['html' => '
                                <div class="sec-head" data-reveal><span class="eyebrow">Why Choose Us</span><h2 class="sec-title">Why Prayaag International School?</h2></div>
                                <div class="gl-features" data-reveal>
                                    <div class="glf-card"><div class="glf-icon" style="background:#e8f4fd;color:#1f5aa8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div><h4>CBSE Affiliated</h4><p>Officially affiliated with CBSE (No. 531592).</p></div>
                                    <div class="glf-card"><div class="glf-icon" style="background:#fef3e2;color:#c79a3b"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div><h4>Smart Classrooms</h4><p>Tech-enabled classrooms with digital teaching systems.</p></div>
                                    <div class="glf-card"><div class="glf-icon" style="background:#e8fce8;color:#1a8a1a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><h4>Expert Faculty</h4><p>Highly qualified teachers dedicated to student success.</p></div>
                                    <div class="glf-card"><div class="glf-icon" style="background:#fce8f0;color:#c0397a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div><h4>Safe Campus</h4><p>360&deg; CCTV surveillance and trained security.</p></div>
                                    <div class="glf-card"><div class="glf-icon" style="background:#f0e8fc;color:#6b3fa0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div><h4>Holistic Development</h4><p>Sports, arts, music, clubs, and extracurriculars.</p></div>
                                    <div class="glf-card"><div class="glf-icon" style="background:#e8fcf0;color:#1a7a4a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="10" r="3"/><path d="M12 21.7C17.3 17 20 13 20 10a8 8 0 1 0-16 0c0 3 2.7 7 8 11.7z"/></svg></div><h4>Transport Facility</h4><p>AC buses with CCTV, trained drivers, attendants.</p></div>
                                </div>
                                <div class="gl-cta" style="text-align:center;margin-top:1.5rem" data-reveal><a class="btn" href="tel:+919350748851">Call Now: +91 93507 48851</a> <a class="btn btn-outline" href="/admissions">View Admission Process</a></div>
                            '],
                        ]],
                    ]],
                ]]],
            ],
        ];

        // Build all pages
        foreach ($pages as $p) {
            $page = Page::firstOrCreate(['slug' => $p['slug']], ['title' => $p['title'], 'status' => 'published']);
            $page->update(['status' => 'published', 'seo' => $p['seo']]);
            app(PageTreeService::class)->sync($page, $p['sections']);
            app(PageRenderer::class)->forget($page);
        }

        $this->command?->info('All 6 landing/thank-you pages created.');
    }
}
