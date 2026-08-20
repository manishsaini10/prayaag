{{--
    Classrooms Page Widget — Luxury Modern, Ultra-Clean & 100% Mobile Responsive
    Features: Smart Classrooms, Junior/Senior Wings, Pedagogical Excellence & Lightbox Gallery
--}}

@php
    $heroEyebrow      = $settings['hero_eyebrow'] ?? 'Modern Campus Infrastructure & Learning Spaces';
    $heroTitle        = $settings['hero_title'] ?? 'Smart Classrooms & Innovative Learning Environments';
    $heroSub          = $settings['hero_subtitle'] ?? 'Spacious, centralized air-conditioned, and digitally-enabled smart classrooms at Prayaag International School, Panipat.';
    $heroBg           = $settings['hero_bg_image'] ?? '/images/classrooms/junior-classroom.webp';

    $highlights       = (array) ($settings['highlights'] ?? []);
    $juniorTitle      = $settings['junior_wing_title'] ?? 'Junior Wing Classrooms (Pre-Nursery – Grade II)';
    $juniorDesc       = $settings['junior_wing_desc'] ?? '';
    $juniorImage      = $settings['junior_wing_image'] ?? '/images/classrooms/junior-classroom.webp';
    $juniorTags       = (array) ($settings['junior_wing_tags'] ?? []);

    $seniorTitle      = $settings['senior_wing_title'] ?? 'Senior Wing Smart Classrooms (Grade III – Grade XII)';
    $seniorDesc       = $settings['senior_wing_desc'] ?? '';
    $seniorImage      = $settings['senior_wing_image'] ?? '/images/classrooms/classroom-main.jpg';
    $seniorTags       = (array) ($settings['senior_wing_tags'] ?? []);

    $methodTitle      = $settings['methodologies_title'] ?? 'Teaching Methodologies in Classrooms';
    $methodSub        = $settings['methodologies_sub'] ?? '';
    $methodologies    = (array) ($settings['methodologies'] ?? []);

    $galleryTitle     = $settings['gallery_title'] ?? 'Classrooms & Learning Zones Gallery';
    $galleryImages    = (array) ($settings['gallery_images'] ?? []);

    $standards        = (array) ($settings['standards'] ?? []);

    $ctaTitle         = $settings['cta_title'] ?? 'Experience the Classrooms of Tomorrow at Prayaag';
    $ctaSub           = $settings['cta_sub'] ?? '';
    $ctaPrimary       = $settings['cta_btn_primary'] ?? 'Apply for Admission';
    $ctaLink          = $settings['cta_btn_link'] ?? '/admissions';
    $ctaSecondary     = $settings['cta_btn_secondary'] ?? 'Contact Us';
    $ctaSecLink       = $settings['cta_btn_sec_link'] ?? '/contact-us';
@endphp

<style>
/* ================================================================
   CLASSROOMS SHOWCASE — 100% ULTRA MOBILE RESPONSIVE & LUXURY MODERN
   ================================================================ */

.cls-wrapper {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow-x: hidden;
    background: #f8fafc;
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: #1e293b;
}

.pb-section:has(.cls-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.cls-wrapper) .pb-row,
.pb-section:has(.cls-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ── Hero Showcase ───────────────────────────────────────────────── */
.cls-hero {
    position: relative;
    min-height: 480px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.cls-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('{{ $heroBg }}');
    background-size: cover;
    background-position: center;
    opacity: .30;
    transform: scale(1.03);
    transition: transform 6s ease-out;
}

.cls-hero:hover .cls-hero__bg {
    transform: scale(1.08);
}

.cls-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.96) 0%, rgba(11,37,69,0.84) 60%, rgba(197,143,39,0.30) 100%);
}

.cls-hero__content {
    position: relative;
    z-index: 2;
    padding: 90px 5vw 60px;
    max-width: 1280px;
    margin: 0 auto;
    width: 100%;
}

.cls-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--gold-2, #f59e0b);
    margin-bottom: 14px;
    background: rgba(245,158,11,0.12);
    padding: 6px 16px;
    border-radius: 999px;
    border: 1px solid rgba(245,158,11,0.3);
}

.cls-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2rem, 4.5vw, 3.4rem);
    font-weight: 800;
    color: #ffffff;
    line-height: 1.22;
    margin: 0 0 16px;
    max-width: 900px;
}

.cls-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.cls-hero__sub {
    font-size: clamp(0.95rem, 1.8vw, 1.15rem);
    color: rgba(255,255,255,0.88);
    max-width: 820px;
    line-height: 1.7;
    margin: 0 0 28px;
}

.cls-hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.cls-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--gold, #d97706), #b45309);
    color: #ffffff !important;
    padding: 12px 26px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(217,119,6,0.35);
    transition: all 0.3s ease;
}

.cls-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

.cls-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    padding: 12px 22px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
}

.cls-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ── 4 Smart Highlights Grid ──────────────────────────────────────── */
.cls-highlights-sec {
    max-width: 1280px;
    margin: -36px auto 0;
    padding: 0 5vw;
    position: relative;
    z-index: 10;
}

.cls-highlights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 18px;
}

.cls-hl-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 24px 20px;
    box-shadow: 0 10px 30px -5px rgba(11,37,69,0.08);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cls-hl-card:hover {
    transform: translateY(-5px);
    border-color: #cbd5e1;
    box-shadow: 0 16px 36px -8px rgba(11,37,69,0.14);
}

.cls-hl-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f1f5f9;
    display: grid;
    place-items: center;
    font-size: 24px;
}

.cls-hl-card:nth-child(1) .cls-hl-icon { background: #eff6ff; }
.cls-hl-card:nth-child(2) .cls-hl-icon { background: #fdf4ff; }
.cls-hl-card:nth-child(3) .cls-hl-icon { background: #f0fdf4; }
.cls-hl-card:nth-child(4) .cls-hl-icon { background: #fffbeb; }

.cls-hl-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.cls-hl-desc {
    font-size: 0.88rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

/* ── Junior & Senior Wings Detailed Showcase ──────────────────────── */
.cls-wings-sec {
    padding: 70px 5vw 40px;
    max-width: 1280px;
    margin: 0 auto;
}

.cls-sec-header {
    text-align: center;
    margin-bottom: 48px;
}

.cls-sec-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.8rem, 3.2vw, 2.5rem);
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}

.cls-sec-header p {
    font-size: 1rem;
    color: #64748b;
    max-width: 680px;
    margin: 0 auto;
    line-height: 1.6;
}

.cls-wing-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
    margin-bottom: 50px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    padding: 36px;
    box-shadow: 0 12px 36px -8px rgba(0,0,0,0.04);
}

.cls-wing-row.reverse {
    direction: rtl;
}

.cls-wing-row.reverse > * {
    direction: ltr;
}

.cls-wing-content h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.65rem;
    font-weight: 800;
    color: #0b2545;
    margin: 0 0 14px;
    line-height: 1.3;
}

.cls-wing-content p {
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.7;
    margin: 0 0 20px;
}

.cls-tag-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 24px;
}

.cls-tag-pill {
    font-size: 0.8rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
}

.cls-wing-img-box {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 16px 40px -10px rgba(11,37,69,0.18);
    cursor: pointer;
    background: #0b2545;
}

.cls-wing-img-box img {
    width: 100%;
    height: 380px;
    object-fit: cover;
    display: block;
    transition: transform 0.6s ease;
}

.cls-wing-img-box:hover img {
    transform: scale(1.06);
}

.cls-wing-img-badge {
    position: absolute;
    bottom: 14px;
    right: 14px;
    background: rgba(11,37,69,0.85);
    color: #ffffff;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 700;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.2);
}

/* ── 6 Pedagogical Pillars Grid ──────────────────────────────────── */
.cls-method-sec {
    background: #0b2545;
    color: #ffffff;
    padding: 80px 5vw;
}

.cls-method-container {
    max-width: 1280px;
    margin: 0 auto;
}

.cls-method-header {
    text-align: center;
    margin-bottom: 50px;
}

.cls-method-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.9rem, 3.2vw, 2.6rem);
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 12px;
}

.cls-method-header p {
    font-size: 1rem;
    color: rgba(255,255,255,0.8);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}

.cls-method-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 22px;
}

.cls-method-card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.14);
    border-radius: 20px;
    padding: 28px 24px;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
    backdrop-filter: blur(10px);
}

.cls-method-card:hover {
    background: rgba(255,255,255,0.12);
    transform: translateY(-5px);
    border-color: var(--gold, #d97706);
    box-shadow: 0 16px 40px rgba(0,0,0,0.3);
}

.cls-method-icon {
    font-size: 30px;
}

.cls-method-card h4 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
}

.cls-method-card p {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.78);
    line-height: 1.65;
    margin: 0;
}

/* ── Lightbox Gallery Section ────────────────────────────────────── */
.cls-gallery-sec {
    padding: 80px 5vw 60px;
    max-width: 1280px;
    margin: 0 auto;
}

.cls-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.cls-gal-item {
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    background: #0b2545;
    box-shadow: 0 10px 26px -6px rgba(0,0,0,0.1);
    cursor: pointer;
}

.cls-gal-img-box {
    position: relative;
    width: 100%;
    padding-top: 70%;
    overflow: hidden;
}

.cls-gal-img-box img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.cls-gal-item:hover .cls-gal-img-box img {
    transform: scale(1.08);
}

.cls-gal-caption {
    position: absolute;
    bottom: 0;
    inset-x: 0;
    background: linear-gradient(0deg, rgba(11,37,69,0.92) 0%, transparent 100%);
    padding: 24px 16px 12px;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 700;
}

/* ── Safety & Standards ──────────────────────────────────────────── */
.cls-standards-sec {
    padding: 0 5vw 70px;
    max-width: 1280px;
    margin: 0 auto;
}

.cls-standards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.cls-std-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 24px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
}

.cls-std-icon {
    font-size: 28px;
    flex-shrink: 0;
}

.cls-std-card h4 {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.cls-std-card p {
    font-size: 0.88rem;
    color: #64748b;
    line-height: 1.55;
    margin: 0;
}

/* ── Call to Action Card ─────────────────────────────────────────── */
.cls-cta-sec {
    padding: 0 5vw 80px;
    max-width: 1280px;
    margin: 0 auto;
}

.cls-cta-card {
    background: linear-gradient(135deg, #0b2545 0%, #1e3a6e 100%);
    border-radius: 28px;
    padding: 60px 40px;
    text-align: center;
    color: #ffffff;
    box-shadow: 0 24px 60px -12px rgba(11,37,69,0.35);
    position: relative;
    overflow: hidden;
}

.cls-cta-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(245,158,11,0.25) 0%, transparent 70%);
    border-radius: 50%;
}

.cls-cta-card h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.8rem, 3.5vw, 2.7rem);
    font-weight: 800;
    margin: 0 0 14px;
    position: relative;
    z-index: 2;
}

.cls-cta-card p {
    font-size: 1.05rem;
    color: rgba(255,255,255,0.88);
    max-width: 760px;
    margin: 0 auto 30px;
    line-height: 1.7;
    position: relative;
    z-index: 2;
}

.cls-cta-actions {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}

/* ── Lightbox Modal ──────────────────────────────────────────────── */
.cls-lightbox-overlay {
    position: fixed;
    inset: 0;
    background: rgba(5, 15, 29, 0.94);
    backdrop-filter: blur(12px);
    z-index: 999999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.cls-lightbox-overlay.active {
    display: flex;
    opacity: 1;
}

.cls-lightbox-container {
    background: #0b2545;
    border: 1px solid rgba(255,255,255,0.15);
    width: 100%;
    max-width: 960px;
    max-height: 94vh;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 30px 80px -15px rgba(0,0,0,0.7);
}

.cls-lightbox-header {
    background: rgba(255,255,255,0.06);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.cls-lightbox-close {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(255,255,255,0.12);
    border: none;
    color: #ffffff;
    font-size: 18px;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cls-lightbox-close:hover {
    background: #dc2626;
}

.cls-lightbox-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: #050e1a;
    overflow: auto;
}

.cls-lightbox-body img {
    max-width: 100%;
    max-height: 78vh;
    object-fit: contain;
    border-radius: 10px;
}

/* ── Mobile Breakpoints ──────────────────────────────────────────── */
@media(max-width: 900px) {
    .cls-wing-row {
        grid-template-columns: 1fr;
        padding: 24px;
        gap: 24px;
    }
    .cls-wing-row.reverse {
        direction: ltr;
    }
    .cls-wing-img-box img {
        height: 280px;
    }
}

@media(max-width: 640px) {
    .cls-hero {
        min-height: 400px;
    }
    .cls-hero__content {
        padding: 70px 5vw 40px;
    }
    .cls-hero__actions {
        flex-direction: column;
        width: 100%;
    }
    .cls-btn-primary, .cls-btn-secondary {
        width: 100%;
    }
    .cls-highlights-sec {
        margin-top: -20px;
    }
    .cls-method-sec {
        padding: 60px 5vw;
    }
    .cls-cta-card {
        padding: 40px 20px;
    }
    .cls-cta-actions {
        flex-direction: column;
        width: 100%;
    }
}
</style>

<div class="cls-wrapper">
    {{-- 🌟 HERO SHOWCASE --}}
    <section class="cls-hero">
        <div class="cls-hero__bg"></div>
        <div class="cls-hero__overlay"></div>
        <div class="cls-hero__content">
            @if(!empty($heroEyebrow))
            <div class="cls-eyebrow">
                <span>🏫</span> {{ $heroEyebrow }}
            </div>
            @endif
            <h1 class="cls-hero__title">
                {{ $heroTitle }}
            </h1>
            <p class="cls-hero__sub">
                {{ $heroSub }}
            </p>
            <div class="cls-hero__actions">
                <a href="#wings-section" class="cls-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    Explore Junior &amp; Senior Wings
                </a>
                <a href="#methodologies-section" class="cls-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    Teaching Methodologies
                </a>
            </div>
        </div>
    </section>

    {{-- ❄️ 4 KEY SMART INFRASTRUCTURE HIGHLIGHTS --}}
    @if(count($highlights) > 0)
    <section class="cls-highlights-sec">
        <div class="cls-highlights-grid">
            @foreach($highlights as $hl)
            <div class="cls-hl-card">
                <div class="cls-hl-icon">{{ $hl['icon'] ?? '✨' }}</div>
                <h3 class="cls-hl-title">{{ $hl['title'] ?? '' }}</h3>
                <p class="cls-hl-desc">{{ $hl['desc'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 🏫 JUNIOR & SENIOR WINGS DETAILED TOUR --}}
    <section class="cls-wings-sec" id="wings-section">
        <div class="cls-sec-header">
            <h2>Campus Wings &amp; Age-Appropriate Learning Zones</h2>
            <p>Every classroom is thoughtfully designed according to developmental age psychology, ensuring optimal focus, safety, and creative freedom.</p>
        </div>

        {{-- Junior Wing Card --}}
        <div class="cls-wing-row">
            <div class="cls-wing-content">
                <span class="cls-eyebrow" style="background:#fef3c7;color:#b45309;border-color:#fde68a;margin-bottom:10px">Early Childhood Excellence</span>
                <h3>{{ $juniorTitle }}</h3>
                <p>{{ $juniorDesc }}</p>
                @if(count($juniorTags) > 0)
                <div class="cls-tag-list">
                    @foreach($juniorTags as $tag)
                    <span class="cls-tag-pill">✓ {{ $tag }}</span>
                    @endforeach
                </div>
                @endif
                <a href="/admissions" class="cls-btn-primary" style="padding:10px 20px;font-size:0.9rem">
                    Junior Wing Admissions ↗
                </a>
            </div>
            <div class="cls-wing-img-box" onclick="openClassroomLightbox('{{ $juniorImage }}')">
                <img src="{{ $juniorImage }}" alt="{{ $juniorTitle }}" loading="lazy">
                <span class="cls-wing-img-badge">🔍 Click to Zoom</span>
            </div>
        </div>

        {{-- Senior Wing Card --}}
        <div class="cls-wing-row reverse">
            <div class="cls-wing-content">
                <span class="cls-eyebrow" style="background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;margin-bottom:10px">CBSE &amp; Competitive Readiness</span>
                <h3>{{ $seniorTitle }}</h3>
                <p>{{ $seniorDesc }}</p>
                @if(count($seniorTags) > 0)
                <div class="cls-tag-list">
                    @foreach($seniorTags as $tag)
                    <span class="cls-tag-pill">✓ {{ $tag }}</span>
                    @endforeach
                </div>
                @endif
                <a href="/admissions" class="cls-btn-primary" style="padding:10px 20px;font-size:0.9rem">
                    Senior Wing Admissions ↗
                </a>
            </div>
            <div class="cls-wing-img-box" onclick="openClassroomLightbox('{{ $seniorImage }}')">
                <img src="{{ $seniorImage }}" alt="{{ $seniorTitle }}" loading="lazy">
                <span class="cls-wing-img-badge">🔍 Click to Zoom</span>
            </div>
        </div>
    </section>

    {{-- 🧠 6 PEDAGOGICAL PILLARS & TEACHING METHODOLOGIES --}}
    @if(count($methodologies) > 0)
    <section class="cls-method-sec" id="methodologies-section">
        <div class="cls-method-container">
            <div class="cls-method-header">
                <h2>{{ $methodTitle }}</h2>
                <p>{{ $methodSub }}</p>
            </div>

            <div class="cls-method-grid">
                @foreach($methodologies as $m)
                <div class="cls-method-card">
                    <div class="cls-method-icon">{{ $m['icon'] ?? '💡' }}</div>
                    <h4>{{ $m['title'] ?? '' }}</h4>
                    <p>{{ $m['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 📸 HIGH-RESOLUTION CLASSROOMS GALLERY --}}
    @if(count($galleryImages) > 0)
    <section class="cls-gallery-sec">
        <div class="cls-sec-header">
            <h2>{{ $galleryTitle }}</h2>
            <p>A glimpse inside our smart, spacious, and naturally lit classroom environments.</p>
        </div>

        <div class="cls-gallery-grid">
            @foreach($galleryImages as $g)
            <div class="cls-gal-item" onclick="openClassroomLightbox('{{ $g['image'] ?? '' }}')">
                <div class="cls-gal-img-box">
                    <img src="{{ $g['image'] ?? '' }}" alt="{{ $g['caption'] ?? 'Classroom' }}" loading="lazy">
                </div>
                @if(!empty($g['caption']))
                <div class="cls-gal-caption">{{ $g['caption'] }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 🛡️ SAFETY & HYGIENE STANDARDS --}}
    @if(count($standards) > 0)
    <section class="cls-standards-sec">
        <div class="cls-standards-grid">
            @foreach($standards as $std)
            <div class="cls-std-card">
                <div class="cls-std-icon">{{ $std['icon'] ?? '🛡️' }}</div>
                <div>
                    <h4>{{ $std['title'] ?? '' }}</h4>
                    <p>{{ $std['desc'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 🚀 ADMISSIONS CALL TO ACTION --}}
    <section class="cls-cta-sec">
        <div class="cls-cta-card">
            <h2>{{ $ctaTitle }}</h2>
            <p>{{ $ctaSub }}</p>
            <div class="cls-cta-actions">
                <a href="{{ $ctaLink }}" class="cls-btn-primary" style="font-size:1rem;padding:14px 32px">
                    {{ $ctaPrimary }} ↗
                </a>
                <a href="{{ $ctaSecLink }}" class="cls-btn-secondary" style="font-size:1rem;padding:14px 28px">
                    {{ $ctaSecondary }}
                </a>
            </div>
        </div>
    </section>
</div>

{{-- 🔍 LIGHTBOX MODAL --}}
<div id="classroomLightbox" class="cls-lightbox-overlay" onclick="closeClassroomLightbox(event)">
    <div class="cls-lightbox-container" onclick="event.stopPropagation()">
        <div class="cls-lightbox-header">
            <button type="button" class="cls-lightbox-close" onclick="closeClassroomLightbox()" aria-label="Close">✕</button>
        </div>
        <div class="cls-lightbox-body">
            <img id="clsLightboxImg" src="" alt="Classroom Zoom Preview">
        </div>
    </div>
</div>

<script>
function openClassroomLightbox(src) {
    if (!src) return;
    document.getElementById('clsLightboxImg').src = src;
    const modal = document.getElementById('classroomLightbox');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeClassroomLightbox(e) {
    if (e && e.target !== e.currentTarget && !e.target.classList.contains('cls-lightbox-close')) {
        return;
    }
    const modal = document.getElementById('classroomLightbox');
    modal.classList.remove('active');
    document.getElementById('clsLightboxImg').src = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeClassroomLightbox();
    }
});
</script>
