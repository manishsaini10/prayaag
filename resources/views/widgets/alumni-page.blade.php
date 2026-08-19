{{--
    Alumni Page Widget — Premium Design
    Uses school design system tokens from site.css (navy, gold, Playfair/Poppins)
--}}

<style>
/* ================================================================
   ALUMNI PAGE — Scoped CSS
   ================================================================ */

/* ── Hero ────────────────────────────────────────────────────── */
.alm-hero {
    position: relative;
    min-height: 480px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy);
}
.alm-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Panipat-Alumni.jpg');
    background-size: cover;
    background-position: center 30%;
    opacity: .22;
}
.alm-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, var(--navy) 45%, rgba(11,37,69,.55) 100%);
}
.alm-hero__content {
    position: relative;
    z-index: 2;
    padding: 100px var(--gutter) 80px;
    max-width: var(--container);
    margin: 0 auto;
    width: 100%;
}
.alm-eyebrow {
    display: inline-block;
    font-family: var(--font-body);
    font-size: var(--fs-sm);
    font-weight: 600;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--gold-2);
    margin-bottom: 18px;
}
.alm-hero__title {
    font-family: var(--font-head);
    font-size: clamp(2.4rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.18;
    margin: 0 0 20px;
}
.alm-hero__title span { color: var(--gold-2); }
.alm-hero__sub {
    font-size: var(--fs-lg);
    color: rgba(255,255,255,.78);
    max-width: 560px;
    line-height: 1.7;
    margin: 0 0 36px;
}
.alm-hero__actions { display: flex; gap: 14px; flex-wrap: wrap; }

/* ── Stats Bar ───────────────────────────────────────────────── */
.alm-stats {
    background: var(--gold);
    padding: 0;
}
.alm-stats__inner {
    max-width: var(--container);
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    padding: 0 var(--gutter);
}
.alm-stat {
    padding: 30px 20px;
    text-align: center;
    border-right: 1px solid rgba(255,255,255,.25);
}
.alm-stat:last-child { border-right: none; }
.alm-stat__num {
    display: block;
    font-family: var(--font-head);
    font-size: 2.4rem;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 6px;
}
.alm-stat__label {
    font-size: var(--fs-sm);
    color: rgba(255,255,255,.85);
    font-weight: 500;
    letter-spacing: .04em;
}

/* ── Section layout ──────────────────────────────────────────── */
.alm-section {
    padding: 84px var(--gutter);
    max-width: var(--container);
    margin: 0 auto;
}
.alm-section--grey {
    background: var(--bg-soft);
    max-width: 100%;
    padding: 84px 0;
}
.alm-section--grey .alm-section { padding-top: 0; padding-bottom: 0; }
.alm-label {
    font-family: var(--font-body);
    font-size: var(--fs-sm);
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 10px;
}
.alm-title {
    font-family: var(--font-head);
    font-size: clamp(1.8rem, 3.2vw, 2.6rem);
    font-weight: 700;
    color: var(--navy);
    line-height: 1.25;
    margin: 0 0 18px;
}
.alm-body {
    font-size: var(--fs-base);
    color: var(--body);
    line-height: 1.85;
    max-width: 680px;
    margin-bottom: 36px;
}

/* ── Two-col split ───────────────────────────────────────────── */
.alm-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 56px;
    align-items: center;
}
.alm-split--reverse { direction: rtl; }
.alm-split--reverse > * { direction: ltr; }
.alm-split__img {
    border-radius: var(--radius-lg);
    overflow: hidden;
    aspect-ratio: 4/3;
    box-shadow: 0 20px 60px rgba(11,37,69,.16);
}
.alm-split__img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .6s ease;
}
.alm-split__img:hover img { transform: scale(1.04); }

/* ── Feature Cards ───────────────────────────────────────────── */
.alm-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.alm-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 34px 28px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--line-soft);
    transition: transform .25s ease, box-shadow .25s ease;
}
.alm-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 48px rgba(11,37,69,.12);
}
.alm-card__icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-3) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    font-size: 22px;
}
.alm-card__title {
    font-family: var(--font-head);
    font-size: var(--fs-xl);
    font-weight: 700;
    color: var(--navy);
    margin-bottom: 10px;
}
.alm-card__text {
    font-size: var(--fs-sm);
    color: var(--body);
    line-height: 1.75;
}

/* ── CTA Band ────────────────────────────────────────────────── */
.alm-cta {
    background: linear-gradient(125deg, var(--navy) 0%, var(--navy-3) 60%, #1a4a8a 100%);
    padding: 80px var(--gutter);
    text-align: center;
    position: relative;
    overflow: hidden;
}
.alm-cta::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 120% at 80% 50%, rgba(199,154,59,.18) 0%, transparent 70%);
    pointer-events: none;
}
.alm-cta__inner { position: relative; max-width: 680px; margin: 0 auto; }
.alm-cta__title {
    font-family: var(--font-head);
    font-size: clamp(1.8rem, 3vw, 2.4rem);
    font-weight: 700;
    color: #fff;
    margin-bottom: 16px;
}
.alm-cta__text {
    font-size: var(--fs-lg);
    color: rgba(255,255,255,.8);
    margin-bottom: 34px;
    line-height: 1.7;
}
.alm-cta__btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 900px) {
    .alm-stats__inner { grid-template-columns: repeat(2, 1fr); }
    .alm-stat { border-right: none; border-bottom: 1px solid rgba(255,255,255,.2); }
    .alm-stat:nth-child(2n) { border-bottom: 1px solid rgba(255,255,255,.2); }
    .alm-split { grid-template-columns: 1fr; gap: 32px; }
    .alm-split--reverse { direction: ltr; }
    .alm-cards { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .alm-stats__inner { grid-template-columns: repeat(2, 1fr); }
    .alm-hero__content { padding: 80px var(--gutter) 60px; }
    .alm-section { padding: 56px var(--gutter); }
}
</style>

{{-- ══ HERO ══════════════════════════════════════════════════════ --}}
<section class="alm-hero">
    <div class="alm-hero__bg"></div>
    <div class="alm-hero__overlay"></div>
    <div class="alm-hero__content" data-reveal>
        <span class="alm-eyebrow">Prayaag International School, Panipat</span>
        <h1 class="alm-hero__title">
            Always a Prayaagian,<br>
            <span>Forever Connected</span>
        </h1>
        <p class="alm-hero__sub">
            Celebrating the extraordinary journeys of our graduates — a legacy built on excellence, values, and lifelong bonds.
        </p>
        <div class="alm-hero__actions">
            <a href="mailto:{{ $email }}" class="btn btn-gold">Connect with Alumni Cell</a>
            <a href="{{ $waUrl }}" class="btn" style="background:rgba(255,255,255,.12);color:#fff;border:1.5px solid rgba(255,255,255,.3);" target="_blank" rel="noopener">WhatsApp Us</a>
        </div>
    </div>
</section>

{{-- ══ STATS BAR ════════════════════════════════════════════════ --}}
<div class="alm-stats">
    <div class="alm-stats__inner">
        <div class="alm-stat" data-reveal>
            <span class="alm-stat__num">25+</span>
            <span class="alm-stat__label">Years of Excellence</span>
        </div>
        <div class="alm-stat" data-reveal>
            <span class="alm-stat__num">5000+</span>
            <span class="alm-stat__label">Alumni Worldwide</span>
        </div>
        <div class="alm-stat" data-reveal>
            <span class="alm-stat__num">100+</span>
            <span class="alm-stat__label">Cities Represented</span>
        </div>
        <div class="alm-stat" data-reveal>
            <span class="alm-stat__num">∞</span>
            <span class="alm-stat__label">The Prayaag Bond</span>
        </div>
    </div>
</div>

{{-- ══ WELCOME SECTION ══════════════════════════════════════════ --}}
<div class="alm-section">
    <div class="alm-split" data-reveal>
        <div class="alm-split__text">
            <p class="alm-label">Welcome Back</p>
            <h2 class="alm-title">Welcome to the Prayaag Alumni Family</h2>
            <p class="alm-body">
                At Prayaag International School, we take immense pride in our alumni community — a global network of individuals who have excelled in their chosen fields while carrying the values and ethos of our institution to every corner of the world.
            </p>
            <p class="alm-body" style="margin-top:-18px">
                This page is your dedicated space to celebrate achievements, reconnect with old friends, and continue being a vibrant part of the Prayaag family — because once a Prayaagian, always a Prayaagian.
            </p>
            <a href="mailto:{{ $email }}" class="btn btn-navy">Get in Touch</a>
        </div>
        <div class="alm-split__img">
            <img src="https://prayaaginternationalschool.com/wp-content/uploads/2023/08/Prayaag-International-School-Panipat-Alumni.jpg"
                 alt="Prayaag International School Alumni"
                 loading="lazy">
        </div>
    </div>
</div>

{{-- ══ CARDS — OPPORTUNITIES ════════════════════════════════════ --}}
<div class="alm-section--grey">
    <div class="alm-section">
        <p class="alm-label" style="text-align:center">What We Offer</p>
        <h2 class="alm-title" style="text-align:center;max-width:560px;margin:0 auto 48px">Be a Part of Something Bigger</h2>
        <div class="alm-cards" data-reveal>
            <div class="alm-card">
                <div class="alm-card__icon">🔗</div>
                <h3 class="alm-card__title">Stay Connected</h3>
                <p class="alm-card__text">Update your contact information and stay connected with your batchmates, teachers, and the school through reunions, workshops, and exclusive alumni events.</p>
            </div>
            <div class="alm-card">
                <div class="alm-card__icon">🌟</div>
                <h3 class="alm-card__title">Share Your Journey</h3>
                <p class="alm-card__text">Your achievements inspire our current students. Share your story — whether it's a career milestone, a social venture, or a personal triumph — to motivate the next generation.</p>
            </div>
            <div class="alm-card">
                <div class="alm-card__icon">🤝</div>
                <h3 class="alm-card__title">Give Back</h3>
                <p class="alm-card__text">Mentor current students, offer guest lectures, support scholarships, or simply share industry insights. Your contribution shapes the leaders of tomorrow.</p>
            </div>
        </div>
    </div>
</div>

{{-- ══ REUNIONS & EVENTS ════════════════════════════════════════ --}}
<div class="alm-section">
    <div class="alm-split alm-split--reverse" data-reveal>
        <div class="alm-split__img">
            <img src="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp"
                 alt="Alumni Reunion Event"
                 loading="lazy">
        </div>
        <div class="alm-split__text">
            <p class="alm-label">Events & Reunions</p>
            <h2 class="alm-title">Relive the Memories, Create New Ones</h2>
            <p class="alm-body">
                Reunions are the perfect opportunity to come back to where it all began — to relive cherished memories, reconnect with classmates, and meet teachers who shaped your journey.
            </p>
            <p class="alm-body" style="margin-top:-18px">
                Keep an eye on this section for updates about upcoming reunions, cultural events, and exclusive alumni gatherings. Make sure your contact information is always up to date so you never miss an invite.
            </p>
            <a href="mailto:{{ $email }}" class="btn btn-navy">RSVP for Next Reunion</a>
        </div>
    </div>
</div>

{{-- ══ CTA BAND ═════════════════════════════════════════════════ --}}
<section class="alm-cta">
    <div class="alm-cta__inner" data-reveal>
        <h2 class="alm-cta__title">Carry the Prayaag Spirit Forward</h2>
        <p class="alm-cta__text">
            Thank you for being an integral part of the Prayaag International School family. Your journey is an inspiration to us all. Stay connected, stay engaged, and keep the Prayaag spirit alive.
        </p>
        <div class="alm-cta__btns">
            <a href="mailto:{{ $email }}" class="btn btn-gold">Email Alumni Cell</a>
            <a href="{{ $waUrl }}" class="btn" style="background:rgba(255,255,255,.13);color:#fff;border:1.5px solid rgba(255,255,255,.3);" target="_blank" rel="noopener">WhatsApp Us</a>
        </div>
    </div>
</section>
