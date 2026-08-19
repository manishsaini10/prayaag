{{--
    Admissions Page Widget — Premium Design
    Uses school design system tokens from site.css (navy, gold, Playfair/Poppins)
--}}

{{-- ═══════════════════════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════════════════════ --}}
<section class="adm-hero" aria-labelledby="adm-hero-title">
    <div class="adm-hero__bg"></div>
    <div class="container adm-hero__content" data-reveal>
        <span class="adm-eyebrow">Prayaag International School, Panipat</span>
        <h1 id="adm-hero-title" class="adm-hero__title">
            Admissions Open<br>
            <span class="adm-hero__session">{{ $session }}</span>
        </h1>
        <p class="adm-hero__sub">Give Your Child The Best Future at Prayaag International School.<br>
            Limited Seats Available — Apply Now.</p>
        <div class="adm-hero__actions">
            <a href="{{ $applyUrl }}" class="btn btn-gold" target="_blank" rel="noopener">
                Online Registration →
            </a>
            <a href="{{ $waUrl }}" class="btn adm-btn-wa" target="_blank" rel="noopener">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12.004 2C6.477 2 2 6.477 2 12.004c0 1.77.467 3.432 1.278 4.876L2 22l5.234-1.27A9.95 9.95 0 0012.004 22C17.527 22 22 17.527 22 12.004 22 6.477 17.527 2 12.004 2z"/></svg>
                WhatsApp Now
            </a>
        </div>
    </div>
    <div class="adm-hero__images" aria-hidden="true">
        <div class="adm-hero__img" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Children-playing-at-swimimg-pool.webp')"></div>
        <div class="adm-hero__img" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp')"></div>
        <div class="adm-hero__img" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Painting-practice-prayaag-student.webp')"></div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     STATS BAND
════════════════════════════════════════════════════════════════ --}}
<section class="adm-stats" aria-label="School statistics">
    <div class="container adm-stats__grid">
        @php
            $stats = [
                ['184', 'Teachers & Staff', '👨‍🏫'],
                ['96',  'Events Held',      '🎪'],
                ['1100','Happy Parents',    '❤️'],
                ['43',  'Lab Projects',     '🔬'],
            ];
        @endphp
        @foreach($stats as [$num, $label, $emoji])
        <div class="adm-stat" data-reveal>
            <div class="adm-stat__emoji">{{ $emoji }}</div>
            <div class="adm-stat__num" data-count="{{ $num }}">{{ $num }}</div>
            <div class="adm-stat__label">{{ $label }} +</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     HOW TO APPLY — PROCESS STEPS
════════════════════════════════════════════════════════════════ --}}
<section class="adm-section" aria-labelledby="adm-process-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Simple Steps</span>
            <h2 class="sec-title" id="adm-process-title">How To Apply?</h2>
            <p class="sec-sub">A transparent journey for parents and students, from enquiry to confirmation.</p>
        </div>

        <div class="adm-steps">
            @php
                $steps = [
                    ['🏫', 'Campus Tour',
                     'We warmly invite parents to explore our exquisite campus and experience our exceptional facilities. This visit provides a deeper understanding of our school\'s mission and distinctive educational style.'],
                    ['💬', 'Family Interview',
                     'Following the tour, we conduct a comprehensive half-hour interview with your family — an opportunity for you to ask questions and share insights in a personal manner.'],
                    ['📋', 'Registration Form',
                     'Procure the prospectus and registration form from our admission counselor. Submit the completed form with attested copies of the required documents within the stipulated timeframe.'],
                    ['✅', 'Document Verification',
                     'Our admission office verifies all submitted documents, followed by a meeting with the Principal to discuss the admission process in detail.'],
                    ['📝', 'Entrance Test',
                     'For Class I onwards: NUR–I = One-on-One Interaction · II–IX = Written test (English, Math, General Awareness) · XII = Aptitude Test.'],
                    ['🎓', 'Admission Confirmation',
                     'Admissions are granted based on the entrance test and personal interaction. The process begins at the start of the academic session in April.'],
                ];
            @endphp
            @foreach($steps as $i => [$icon, $title, $text])
            <div class="adm-step" data-reveal data-reveal-delay="{{ ($i % 3) + 1 }}">
                <div class="adm-step__num">{{ $i + 1 }}</div>
                <div class="adm-step__icon">{{ $icon }}</div>
                <h3 class="adm-step__title">{{ $title }}</h3>
                <p class="adm-step__text">{{ $text }}</p>
            </div>
            @endforeach
        </div>

        <div class="adm-apply-cta" data-reveal>
            <a href="{{ $applyUrl }}" class="btn btn-gold" target="_blank" rel="noopener">Apply For Admission →</a>
            <a href="{{ $waUrl }}"   class="btn btn-navy" target="_blank" rel="noopener">Chat on WhatsApp</a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     ELIGIBILITY & AGE CRITERIA
════════════════════════════════════════════════════════════════ --}}
<section class="adm-section adm-section--soft" aria-labelledby="adm-eligibility-title">
    <div class="container">
        <div class="sec-head" data-reveal>
            <span class="eyebrow">Who Can Apply</span>
            <h2 class="sec-title" id="adm-eligibility-title">Eligibility Criteria</h2>
            <p class="sec-sub">Registration for all classes starts from December. Admissions are merit-based.</p>
        </div>

        <div class="adm-elig-grid">
            {{-- Age criteria card --}}
            <div class="adm-card" data-reveal>
                <div class="adm-card__head">
                    <span class="adm-card__icon">🎂</span>
                    <h3 class="adm-card__title">Age Criteria <small>(as on 1st April)</small></h3>
                </div>
                <div class="adm-elig-table">
                    @php
                        $ages = [
                            ['Pre-Nursery', '2.5 Years'],
                            ['Nursery',     '3.5 Years'],
                            ['K.G.',        '4.5 Years'],
                            ['Grade I',     '6 Years'],
                        ];
                    @endphp
                    @foreach($ages as [$cls, $age])
                    <div class="adm-elig-row">
                        <span class="adm-elig-class">{{ $cls }}</span>
                        <span class="adm-elig-age">{{ $age }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Test type card --}}
            <div class="adm-card" data-reveal data-reveal-delay="2">
                <div class="adm-card__head">
                    <span class="adm-card__icon">📝</span>
                    <h3 class="adm-card__title">Selection Test Format</h3>
                </div>
                <div class="adm-test-list">
                    @php
                        $tests = [
                            ['NUR – I',        'One-on-One Interaction',                    '#16a34a'],
                            ['II – IX',        'Written Test (English, Math, GK)',           '#2563eb'],
                            ['XII (All)',      'Aptitude Test',                             '#7c3aed'],
                        ];
                    @endphp
                    @foreach($tests as [$grade, $format, $color])
                    <div class="adm-test-row">
                        <span class="adm-test-badge" style="background:{{ $color }}1a;color:{{ $color }};border-color:{{ $color }}40">{{ $grade }}</span>
                        <span class="adm-test-format">{{ $format }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Documents card --}}
            <div class="adm-card adm-card--full" data-reveal data-reveal-delay="3">
                <div class="adm-card__head">
                    <span class="adm-card__icon">📂</span>
                    <h3 class="adm-card__title">Documents Required</h3>
                </div>
                <div class="adm-docs-grid">
                    @php
                        $docs = [
                            '🖼️ 4 Photographs of student',
                            '👥 2 Photographs of parents',
                            '🎓 Original TC from previous school',
                            '🏠 Proof of Residence',
                            '🪪 Aadhaar Card',
                            '📄 Birth Certificate (civic body)',
                            '💊 Medical fitness certificate',
                            '📊 Latest report card / fee receipt',
                        ];
                    @endphp
                    @foreach($docs as $doc)
                    <div class="adm-doc-item">{{ $doc }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     ADMISSION ENQUIRY FORM
════════════════════════════════════════════════════════════════ --}}
<section class="adm-section adm-section--form" id="apply-now" aria-labelledby="adm-form-title">
    <div class="container">
        <div class="adm-form-layout">

            {{-- Left Info Panel --}}
            <div class="adm-form-info" data-reveal>
                <span class="adm-eyebrow" style="color:var(--gold-2);border-color:rgba(224,185,78,.4);background:rgba(224,185,78,.1)">Apply Today</span>
                <h2 class="adm-form-info__title" id="adm-form-title">Start Your<br><span style="color:var(--gold-2)">Admission Journey</span></h2>
                <p class="adm-form-info__sub">Fill in the details below and our admissions team will reach out within 24 hours.</p>

                <div class="adm-form-contacts">
                    <a href="tel:+919350748851" class="adm-contact-item">
                        <div class="adm-contact-icon">📞</div>
                        <div>
                            <div class="adm-contact-label">Call Us</div>
                            <div class="adm-contact-val">+91 93507 48851</div>
                        </div>
                    </a>
                    <a href="{{ $waUrl }}" class="adm-contact-item" target="_blank" rel="noopener">
                        <div class="adm-contact-icon">💬</div>
                        <div>
                            <div class="adm-contact-label">WhatsApp</div>
                            <div class="adm-contact-val">Chat with Admissions</div>
                        </div>
                    </a>
                    <a href="{{ $applyUrl }}" class="adm-contact-item" target="_blank" rel="noopener">
                        <div class="adm-contact-icon">🌐</div>
                        <div>
                            <div class="adm-contact-label">Online Portal</div>
                            <div class="adm-contact-val">Online Registration</div>
                        </div>
                    </a>
                </div>

                <div class="adm-form-badge">
                    <span class="adm-form-badge__dot"></span>
                    Admissions Open · Session {{ $session }}
                </div>
            </div>

            {{-- Right: The Form --}}
            <div class="adm-form-wrap" data-reveal data-reveal-delay="2">

                @if(session('enquiry_sent'))
                <div class="adm-form-success">
                    <div class="adm-form-success__icon">🎉</div>
                    <h3 class="adm-form-success__title">Application Received!</h3>
                    <p class="adm-form-success__text">Thank you for applying to Prayaag International School. Our admissions team will contact you within 24 hours.</p>
                    <a href="{{ $applyUrl }}" class="btn btn-gold" target="_blank">Complete Online Registration →</a>
                </div>
                @else

                @if(isset($errors) && $errors->any())
                <div class="adm-form-error">
                    ⚠️ {{ $errors->first() }}
                </div>
                @endif

                <form class="adm-form" method="POST" action="/enquiries" novalidate id="admission-form">
                    @csrf
                    <input type="hidden" name="type" value="admission">
                    <input type="hidden" name="source" value="admissions">
                    {{-- Honeypot --}}
                    <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;opacity:0">

                    {{-- Section: Student Details --}}
                    <div class="adm-form-section-label">
                        <span class="adm-form-section-num">01</span>
                        Student Details
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label class="adm-label" for="student_name">Student's Full Name <span class="adm-req">*</span></label>
                            <input class="adm-input" type="text" id="student_name" name="student_name" placeholder="e.g. Arjun Sharma" required autocomplete="name">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label" for="gender">Gender <span class="adm-req">*</span></label>
                            <select class="adm-input adm-select" id="gender" name="gender" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label class="adm-label" for="dob">Date of Birth <span class="adm-req">*</span></label>
                            <input class="adm-input" type="date" id="dob" name="dob" required>
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label" for="class_applying">Class Applying For <span class="adm-req">*</span></label>
                            <select class="adm-input adm-select" id="class_applying" name="class_applying" required>
                                <option value="" disabled selected>Select class</option>
                                <option>Pre-Nursery</option>
                                <option>Nursery</option>
                                <option>K.G.</option>
                                <option>Grade I</option>
                                <option>Grade II</option>
                                <option>Grade III</option>
                                <option>Grade IV</option>
                                <option>Grade V</option>
                                <option>Grade VI</option>
                                <option>Grade VII</option>
                                <option>Grade VIII</option>
                                <option>Grade IX</option>
                                <option>Grade X</option>
                                <option>Grade XI</option>
                                <option>Grade XII</option>
                            </select>
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-label" for="previous_school">Previous School (if any)</label>
                        <input class="adm-input" type="text" id="previous_school" name="previous_school" placeholder="Name of previous school">
                    </div>

                    {{-- Section: Parent/Guardian --}}
                    <div class="adm-form-section-label">
                        <span class="adm-form-section-num">02</span>
                        Parent / Guardian Details
                    </div>

                    <div class="adm-form-row">
                        <div class="adm-form-group">
                            <label class="adm-label" for="adm_name">Parent's Full Name <span class="adm-req">*</span></label>
                            <input class="adm-input" type="text" id="adm_name" name="name" placeholder="e.g. Ramesh Sharma" required autocomplete="name">
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-label" for="adm_phone">Mobile Number <span class="adm-req">*</span></label>
                            <input class="adm-input" type="tel" id="adm_phone" name="phone" placeholder="+91 98765 43210" required autocomplete="tel">
                        </div>
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-label" for="adm_email">Email Address <span class="adm-req">*</span></label>
                        <input class="adm-input" type="email" id="adm_email" name="email" placeholder="parent@email.com" required autocomplete="email">
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-label" for="address">Residential Address</label>
                        <input class="adm-input" type="text" id="address" name="address" placeholder="Street, City, PIN Code">
                    </div>

                    {{-- Section: Message --}}
                    <div class="adm-form-section-label">
                        <span class="adm-form-section-num">03</span>
                        Additional Message
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-label" for="adm_message">Any questions or special requirements?</label>
                        <textarea class="adm-input adm-textarea" id="adm_message" name="message" placeholder="Share any additional information or questions you may have..."></textarea>
                    </div>

                    <button class="adm-submit" type="submit" id="adm-submit-btn">
                        <span class="adm-submit__text">Submit Admission Enquiry</span>
                        <svg class="adm-submit__arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>

                    <p class="adm-form-privacy">🔒 Your information is secure and will only be used for admission purposes.</p>
                </form>

                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     NOTE & FINAL CTA
════════════════════════════════════════════════════════════════ --}}
<section class="adm-section" aria-labelledby="adm-note-title">
    <div class="container">
        <div class="adm-note" data-reveal>
            <span class="adm-note__icon">📌</span>
            <div>
                <h3 id="adm-note-title" class="adm-note__title">Important Note</h3>
                <p class="adm-note__text">The ultimate authority on admissions rests with the school's Principal. All decisions pertaining to admissions remain within the purview of the school's discretion. We are eagerly anticipating the prospect of welcoming you into the Prayaag International School fold.</p>
            </div>
        </div>
    </div>
</section>

<div class="fullbleed">
    <section class="admit-cta">
        <div class="container" data-reveal>
            <h2>Ready to join the Prayaag family?</h2>
            <p>Admissions open for session {{ $session }}. Limited seats — secure your child's future today.</p>
            <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;margin-top:1.5rem">
                <a class="btn btn-gold" href="{{ $applyUrl }}" target="_blank" rel="noopener">Online Registration →</a>
                <a class="btn" href="{{ $waUrl }}" target="_blank" rel="noopener"
                   style="background:rgba(255,255,255,.15);color:#fff;border-color:rgba(255,255,255,.3)">
                    WhatsApp Us
                </a>
            </div>
        </div>
    </section>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     SCOPED STYLES
════════════════════════════════════════════════════════════════ --}}
<style>
/* ── Hero ── */
.adm-hero {
    position: relative;
    display: grid;
    grid-template-columns: 1fr;
    min-height: 520px;
    overflow: hidden;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-3) 60%, #1a3c7a 100%);
}
@media (min-width: 900px) {
    .adm-hero { grid-template-columns: 1fr 1fr; min-height: 560px; }
}
.adm-hero__bg {
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.adm-hero__content {
    position: relative;
    z-index: 2;
    padding-top: 72px;
    padding-bottom: 72px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 1.5rem;
}
.adm-eyebrow {
    display: inline-block;
    font-size: var(--fs-xs);
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--gold-2);
    border: 1px solid rgba(224,185,78,.3);
    background: rgba(224,185,78,.08);
    padding: .35rem 1rem;
    border-radius: 999px;
    width: fit-content;
}
.adm-hero__title {
    font-family: var(--font-head);
    font-size: clamp(2rem, 4.5vw, 3.2rem);
    font-weight: 800;
    line-height: 1.15;
    color: #fff;
    margin: 0;
}
.adm-hero__session {
    color: var(--gold-2);
    display: block;
}
.adm-hero__sub {
    font-size: var(--fs-base);
    color: rgba(255,255,255,.80);
    line-height: 1.7;
    max-width: 500px;
    margin: 0;
}
.adm-hero__actions {
    display: flex;
    flex-wrap: wrap;
    gap: .875rem;
    align-items: center;
}
.adm-btn-wa {
    background: #25d366;
    color: #fff;
    border-color: #25d366;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
}
.adm-btn-wa:hover { background: #22be5b; color: #fff; transform: translateY(-2px); }
.adm-btn-wa svg { fill: currentColor; }
.adm-hero__images {
    display: none;
}
@media (min-width: 900px) {
    .adm-hero__images {
        display: grid;
        grid-template-rows: 1fr 1fr 1fr;
        gap: 3px;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
}
.adm-hero__img {
    background-size: cover;
    background-position: center;
    transition: transform .6s var(--ease);
}
.adm-hero__img:hover { transform: scale(1.04); }

/* ── Stats Band ── */
.adm-stats {
    background: var(--navy-2);
    padding: 2.5rem 0;
}
.adm-stats__grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    text-align: center;
}
@media (min-width: 640px) { .adm-stats__grid { grid-template-columns: repeat(4, 1fr); } }
.adm-stat {
    padding: 1.25rem 1rem;
    border-right: 1px solid rgba(255,255,255,.1);
}
.adm-stat:last-child { border-right: none; }
.adm-stat__emoji { font-size: 1.75rem; margin-bottom: .25rem; }
.adm-stat__num {
    font-family: var(--font-head);
    font-size: clamp(1.8rem, 3vw, 2.5rem);
    font-weight: 800;
    color: var(--gold-2);
    line-height: 1;
}
.adm-stat__label {
    font-size: var(--fs-sm);
    color: rgba(255,255,255,.65);
    margin-top: .25rem;
}

/* ── Sections ── */
.adm-section {
    padding: 5rem 0;
}
.adm-section--soft {
    background: var(--bg-soft);
}

/* ── Steps ── */
.adm-steps {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin: 3rem 0 2.5rem;
}
@media (min-width: 600px) { .adm-steps { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1000px) { .adm-steps { grid-template-columns: repeat(3, 1fr); } }

.adm-step {
    position: relative;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 2rem 1.5rem 1.75rem;
    transition: box-shadow .2s var(--ease), transform .2s var(--ease), border-color .2s;
}
.adm-step:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow);
    border-color: var(--gold-soft);
}
.adm-step__num {
    position: absolute;
    top: -14px; left: 1.5rem;
    width: 30px; height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-2), var(--gold));
    color: #2a1f05;
    font-weight: 800;
    font-size: .8rem;
    display: grid;
    place-items: center;
    box-shadow: 0 4px 10px rgba(199,154,59,.4);
}
.adm-step__icon {
    font-size: 2rem;
    margin-bottom: .75rem;
}
.adm-step__title {
    font-family: var(--font-head);
    font-size: var(--fs-lg);
    font-weight: 700;
    color: var(--navy);
    margin: 0 0 .5rem;
}
.adm-step__text {
    font-size: var(--fs-sm);
    color: var(--body);
    line-height: 1.7;
    margin: 0;
}
.adm-apply-cta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
    margin-top: 1rem;
}

/* ── Eligibility Grid ── */
.adm-elig-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-top: 3rem;
}
@media (min-width: 720px) { .adm-elig-grid { grid-template-columns: 1fr 1fr; } }

.adm-card {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}
.adm-card--full { grid-column: 1 / -1; }
.adm-card__head {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--line-soft);
    background: linear-gradient(135deg, var(--navy), var(--navy-3));
}
.adm-card__icon { font-size: 1.5rem; }
.adm-card__title {
    font-family: var(--font-head);
    font-size: var(--fs-lg);
    color: #fff;
    margin: 0;
    font-weight: 700;
}
.adm-card__title small {
    font-size: var(--fs-xs);
    color: rgba(255,255,255,.6);
    font-weight: 400;
    display: block;
    font-family: var(--font-body);
    margin-top: .1rem;
}

/* Age table */
.adm-elig-table { padding: 1rem 1.5rem; }
.adm-elig-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: .75rem 0;
    border-bottom: 1px solid var(--line-soft);
    font-size: var(--fs-sm);
}
.adm-elig-row:last-child { border-bottom: none; }
.adm-elig-class { font-weight: 700; color: var(--navy); }
.adm-elig-age {
    background: var(--gold-soft);
    color: var(--gold);
    font-weight: 700;
    font-size: var(--fs-xs);
    padding: .2rem .75rem;
    border-radius: 999px;
    border: 1px solid rgba(199,154,59,.3);
}

/* Test type */
.adm-test-list { padding: 1rem 1.5rem; display: flex; flex-direction: column; gap: .75rem; }
.adm-test-row {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .75rem;
    border-radius: var(--radius-sm);
    background: var(--bg-soft);
    border: 1px solid var(--line-soft);
}
.adm-test-badge {
    font-size: var(--fs-xs);
    font-weight: 700;
    padding: .25rem .75rem;
    border-radius: 999px;
    border: 1px solid;
    white-space: nowrap;
    flex-shrink: 0;
}
.adm-test-format {
    font-size: var(--fs-sm);
    color: var(--ink);
    font-weight: 500;
}

/* Documents */
.adm-docs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: .625rem;
    padding: 1.25rem 1.5rem;
}
.adm-doc-item {
    display: flex;
    align-items: center;
    gap: .625rem;
    padding: .625rem 1rem;
    background: var(--bg-soft);
    border: 1px solid var(--line-soft);
    border-radius: var(--radius-sm);
    font-size: var(--fs-sm);
    color: var(--body);
    font-weight: 500;
    transition: background .18s, border-color .18s;
}
.adm-doc-item:hover { background: var(--gold-soft); border-color: rgba(199,154,59,.3); }

/* ── Note ── */
.adm-note {
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
    background: var(--bg-soft);
    border: 1px solid var(--line);
    border-left: 4px solid var(--gold);
    border-radius: var(--radius);
    padding: 1.75rem 2rem;
}
.adm-note__icon { font-size: 1.75rem; flex-shrink: 0; margin-top: .1rem; }
.adm-note__title {
    font-family: var(--font-head);
    font-size: var(--fs-lg);
    color: var(--navy);
    margin: 0 0 .5rem;
}
.adm-note__text {
    font-size: var(--fs-sm);
    color: var(--body);
    line-height: 1.75;
    margin: 0;
}

/* ── Admission Form Section ── */
.adm-section--form {
    background: linear-gradient(160deg, var(--bg-soft) 0%, #eef3fb 100%);
    position: relative;
    overflow: hidden;
}
.adm-section--form::before {
    content: '';
    position: absolute;
    top: -120px; right: -120px;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(199,154,59,.08) 0%, transparent 70%);
    pointer-events: none;
}
.adm-form-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    align-items: start;
}
@media (min-width: 900px) {
    .adm-form-layout { grid-template-columns: 1fr 1.6fr; gap: 4rem; }
}

/* Info Panel */
.adm-form-info {
    position: sticky;
    top: 100px;
}
.adm-form-info__title {
    font-family: var(--font-head);
    font-size: clamp(1.75rem, 3vw, 2.5rem);
    color: var(--navy);
    line-height: 1.2;
    margin: 1rem 0 1rem;
}
.adm-form-info__sub {
    font-size: var(--fs-base);
    color: var(--body);
    line-height: 1.7;
    margin: 0 0 2rem;
}
.adm-form-contacts {
    display: flex;
    flex-direction: column;
    gap: .75rem;
    margin-bottom: 2rem;
}
.adm-contact-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .875rem 1.125rem;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius);
    text-decoration: none;
    transition: box-shadow .2s var(--ease), transform .2s var(--ease), border-color .2s;
}
.adm-contact-item:hover {
    transform: translateX(4px);
    box-shadow: var(--shadow-sm);
    border-color: rgba(199,154,59,.4);
}
.adm-contact-icon { font-size: 1.5rem; flex-shrink: 0; }
.adm-contact-label {
    font-size: var(--fs-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
}
.adm-contact-val {
    font-size: var(--fs-sm);
    font-weight: 600;
    color: var(--navy);
    margin-top: .1rem;
}
.adm-form-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    font-size: var(--fs-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #16a34a;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: .4rem 1rem;
    border-radius: 999px;
}
.adm-form-badge__dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
    animation: adm-pulse 2s infinite;
}
@keyframes adm-pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }
    50%       { box-shadow: 0 0 0 6px rgba(34,197,94,.10); }
}

/* Form Card */
.adm-form-wrap {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: var(--radius-lg);
    padding: 2.5rem;
    box-shadow: var(--shadow);
}
@media (max-width: 500px) { .adm-form-wrap { padding: 1.5rem; } }

/* Section label */
.adm-form-section-label {
    display: flex;
    align-items: center;
    gap: .75rem;
    font-size: var(--fs-xs);
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--navy);
    margin: 1.75rem 0 1.25rem;
    padding-bottom: .75rem;
    border-bottom: 2px solid var(--line-soft);
}
.adm-form-section-label:first-of-type { margin-top: 0; }
.adm-form-section-num {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-2), var(--gold));
    color: #2a1f05;
    font-size: .7rem;
    font-weight: 900;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

/* Row / Group */
.adm-form-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}
@media (min-width: 520px) { .adm-form-row { grid-template-columns: 1fr 1fr; } }
.adm-form-group {
    display: flex;
    flex-direction: column;
    gap: .4rem;
    margin-bottom: .25rem;
}
.adm-label {
    font-size: var(--fs-xs);
    font-weight: 700;
    color: var(--ink);
    letter-spacing: .03em;
}
.adm-req { color: #ef4444; }

/* Inputs */
.adm-input {
    width: 100%;
    padding: .75rem 1rem;
    font-family: var(--font-body);
    font-size: var(--fs-sm);
    color: var(--ink);
    background: var(--bg-soft);
    border: 1.5px solid var(--line);
    border-radius: var(--radius-sm);
    outline: none;
    transition: border-color .18s var(--ease), box-shadow .18s var(--ease), background .18s;
    -webkit-appearance: none;
    appearance: none;
}
.adm-input:focus {
    border-color: var(--navy-3);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(28,58,110,.10);
}
.adm-input::placeholder { color: var(--muted); }
.adm-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7588' stroke-width='2.5'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .9rem center;
    padding-right: 2.5rem;
    cursor: pointer;
}
.adm-textarea {
    min-height: 110px;
    resize: vertical;
    margin-bottom: 1.5rem;
}

/* Submit */
.adm-submit {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .75rem;
    padding: .95rem 2rem;
    font-family: var(--font-body);
    font-size: var(--fs-base);
    font-weight: 700;
    color: #2a1f05;
    background: linear-gradient(135deg, var(--gold-2), var(--gold));
    border: none;
    border-radius: 999px;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(199,154,59,.35);
    transition: transform .22s var(--ease), box-shadow .22s var(--ease), opacity .2s;
    position: relative;
    overflow: hidden;
}
.adm-submit::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,.18), transparent);
    pointer-events: none;
}
.adm-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(199,154,59,.45);
}
.adm-submit:active { transform: translateY(0); }
.adm-submit__arrow {
    transition: transform .2s var(--ease);
    flex-shrink: 0;
}
.adm-submit:hover .adm-submit__arrow { transform: translateX(4px); }

.adm-form-privacy {
    text-align: center;
    font-size: var(--fs-xs);
    color: var(--muted);
    margin: .875rem 0 0;
}

/* Error / Success */
.adm-form-error {
    background: #fff1f1;
    border: 1px solid #fecaca;
    color: #991b1b;
    border-radius: var(--radius-sm);
    padding: .875rem 1rem;
    font-size: var(--fs-sm);
    font-weight: 500;
    margin-bottom: 1.25rem;
}
.adm-form-success {
    text-align: center;
    padding: 3rem 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}
.adm-form-success__icon { font-size: 3.5rem; }
.adm-form-success__title {
    font-family: var(--font-head);
    font-size: var(--fs-2xl);
    color: var(--navy);
    margin: 0;
}
.adm-form-success__text {
    font-size: var(--fs-base);
    color: var(--body);
    max-width: 380px;
    margin: 0;
    line-height: 1.7;
}
</style>
