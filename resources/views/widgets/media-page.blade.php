{{--
    Media ("Life at Prayaag") Page Widget — Clean & 100% Ultra Mobile Responsive
    Configurable Auto-Play (3s interval default), Smooth Animation & Lightbox
--}}

@php
    $autoplay = (bool) ($settings['autoplay'] ?? true);
    $interval = (int) ($settings['interval'] ?? 3000);
    $animSpeed = (int) ($settings['animation_speed'] ?? 600);
    $pauseHover = (bool) ($settings['pause_on_hover'] ?? true);
@endphp

<style>
/* ================================================================
   MEDIA ("LIFE AT PRAYAAG") — FULL-WIDTH & 100% MOBILE RESPONSIVE
   ================================================================ */

.med-wrapper {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow-x: hidden;
    background: #f8fafc;
}

.pb-section:has(.med-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.med-wrapper) .pb-row,
.pb-section:has(.med-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ── Hero ────────────────────────────────────────────────────────── */
.med-hero {
    position: relative;
    min-height: 440px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.med-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/media/Children-playing-at-swimimg-pool.webp');
    background-size: cover;
    background-position: center;
    opacity: .28;
    transform: scale(1.03);
    transition: transform 6s ease-out;
}

.med-hero:hover .med-hero__bg {
    transform: scale(1.08);
}

.med-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.96) 0%, rgba(11,37,69,0.80) 60%, rgba(197,143,39,0.28) 100%);
}

.med-hero__content {
    position: relative;
    z-index: 2;
    padding: 90px 4vw 60px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.med-eyebrow {
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
    padding: 6px 14px;
    border-radius: 999px;
    border: 1px solid rgba(245,158,11,0.3);
}

.med-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 16px;
}

.med-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.med-hero__sub {
    font-size: clamp(0.95rem, 2vw, 1.15rem);
    color: rgba(255,255,255,0.88);
    max-width: 780px;
    line-height: 1.7;
    margin: 0 0 28px;
}

.med-hero__actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.med-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--gold, #d97706), #b45309);
    color: #ffffff !important;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(217,119,6,0.35);
    transition: all 0.3s ease;
}

.med-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

.med-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    padding: 12px 22px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.92rem;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
}

.med-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ── Filter Tabs Bar (Horizontal Touch Scrolling) ───────────────── */
.med-filter-bar {
    position: sticky;
    top: 70px;
    z-index: 40;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 16px -4px rgba(0,0,0,0.04);
    padding: 12px 4vw;
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.med-filter-bar::-webkit-scrollbar {
    display: none;
}

.med-tab-btn {
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    white-space: nowrap;
    text-decoration: none;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.med-tab-btn:hover,
.med-tab-btn.active {
    background: #0b2545;
    color: #ffffff !important;
    border-color: #0b2545;
    box-shadow: 0 4px 12px rgba(11,37,69,0.25);
}

/* ── Gallery Section Styling ─────────────────────────────────────── */
.med-section {
    padding: 50px 4vw 60px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.med-section-header {
    margin-bottom: 28px;
    text-align: center;
}

.med-section-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.8rem, 3.5vw, 2.5rem);
    color: #0f172a;
    margin: 0 0 6px;
}

/* ── 3-Image Grid Layout ─────────────────────────────────────────── */
.med-grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.med-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 24px -6px rgba(0,0,0,0.06);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}

.med-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px -10px rgba(11,37,69,0.15);
    border-color: #cbd5e1;
}

.med-card__img-box {
    position: relative;
    width: 100%;
    padding-top: 72%;
    overflow: hidden;
    background: #0b2545;
}

.med-card__img-box img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.med-card:hover .med-card__img-box img {
    transform: scale(1.08);
}

.med-card__overlay {
    position: absolute;
    inset: 0;
    background: rgba(11,37,69,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.med-card:hover .med-card__overlay {
    opacity: 1;
}

.med-zoom-badge {
    background: rgba(255,255,255,0.95);
    color: #0b2545;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transform: translateY(10px);
    transition: transform 0.3s ease;
}

.med-card:hover .med-zoom-badge {
    transform: translateY(0);
}

/* ── News Clipping Carousel / Slider ─────────────────────────────── */
.news-slider-section {
    background: #0b2545;
    color: #ffffff;
    padding: 60px 4vw 70px;
}

.news-slider-header {
    text-align: center;
    margin-bottom: 30px;
}

.news-slider-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.9rem, 3.5vw, 2.7rem);
    color: #ffffff;
    margin: 0;
}

.news-carousel-container {
    position: relative;
    max-width: 100%;
    margin: 0 auto;
    touch-action: pan-y;
}

.news-carousel-track-wrapper {
    overflow: hidden;
    border-radius: 20px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.12);
    padding: 20px;
}

.news-carousel-track {
    display: flex;
    gap: 20px;
    transition: transform {{ $animSpeed }}ms cubic-bezier(0.25, 1, 0.5, 1);
    will-change: transform;
}

.news-slide-card {
    flex: 0 0 calc(33.333% - 14px);
    min-width: 260px;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    cursor: pointer;
    transition: transform 0.3s ease;
}

@media(max-width: 980px) {
    .news-slide-card {
        flex: 0 0 calc(50% - 10px);
        min-width: 220px;
    }
}

@media(max-width: 640px) {
    .news-slide-card {
        flex: 0 0 100%;
        min-width: 0;
    }
}

.news-slide-card:hover {
    transform: translateY(-4px);
}

.news-slide-img-box {
    position: relative;
    width: 100%;
    padding-top: 100%; /* square newspaper clipping */
    background: #e2e8f0;
    overflow: hidden;
}

.news-slide-img-box img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.news-slide-card:hover .news-slide-img-box img {
    transform: scale(1.05);
}

/* ── Carousel Nav Controls ───────────────────────────────────────── */
.news-carousel-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 20px;
    flex-wrap: wrap;
    gap: 14px;
}

.news-carousel-btn-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.news-nav-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.25);
    color: #ffffff;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.news-nav-btn:hover {
    background: var(--gold, #d97706);
    border-color: var(--gold, #d97706);
    transform: scale(1.05);
}

.news-counter-pill {
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--gold-2, #f59e0b);
}

/* ── Fullscreen Lightbox Modal ───────────────────────────────────── */
.med-lightbox-overlay {
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

.med-lightbox-overlay.active {
    display: flex;
    opacity: 1;
}

.med-lightbox-container {
    background: #0b2545;
    border: 1px solid rgba(255,255,255,0.15);
    width: 100%;
    max-width: 1000px;
    max-height: 94vh;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 30px 80px -15px rgba(0,0,0,0.7);
    transform: scale(0.95);
    transition: transform 0.3s ease;
}

.med-lightbox-overlay.active .med-lightbox-container {
    transform: scale(1);
}

.med-lightbox-header {
    background: rgba(255,255,255,0.06);
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    color: #ffffff;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.med-lightbox-close {
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

.med-lightbox-close:hover {
    background: #dc2626;
}

.med-lightbox-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: #050e1a;
    overflow: auto;
}

.med-lightbox-body img {
    max-width: 100%;
    max-height: 78vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}

/* ── Specific Mobile Breakpoints ─────────────────────────────────── */
@media(max-width: 640px) {
    .med-hero {
        min-height: 380px;
    }
    .med-hero__content {
        padding: 70px 5vw 40px;
    }
    .med-hero__actions {
        flex-direction: column;
        width: 100%;
    }
    .med-btn-primary, .med-btn-secondary {
        width: 100%;
    }
    .med-section {
        padding: 36px 4vw 40px;
    }
    .med-grid-3 {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .news-slider-section {
        padding: 40px 4vw 45px;
    }
    .news-carousel-track-wrapper {
        padding: 12px;
    }
    .news-carousel-track {
        gap: 12px;
    }
}
</style>

<div class="med-wrapper">
    {{-- 🌟 HERO BANNER --}}
    <section class="med-hero">
        <div class="med-hero__bg"></div>
        <div class="med-hero__overlay"></div>
        <div class="med-hero__content">
            <div class="med-eyebrow">
                <span>📸</span> Campus Life &amp; Media Gallery
            </div>
            <h1 class="med-hero__title">
                Life at Prayaag — <span>Media &amp; Press Gallery</span>
            </h1>
            <p class="med-hero__sub">
                Explore life at Prayaag International School — performing arts studios, championship sports arenas, fine arts ateliers, early childhood play zones, and celebrated newspaper features.
            </p>
            <div class="med-hero__actions">
                <a href="#news-section" class="med-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                    News
                </a>
                <a href="#dance-music-section" class="med-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    Campus Galleries
                </a>
            </div>
        </div>
    </section>

    {{-- 🎛️ STICKY CATEGORY FILTER TABS --}}
    <div class="med-filter-bar">
        <a href="#dance-music-section" class="med-tab-btn">Dance &amp; Music</a>
        <a href="#sports-section" class="med-tab-btn">Sports</a>
        <a href="#arts-craft-section" class="med-tab-btn">Arts &amp; Craft</a>
        <a href="#fun-activities-section" class="med-tab-btn">Fun Activities</a>
        <a href="#news-section" class="med-tab-btn" style="background:#fef3c7;color:#b45309;border-color:#fde68a">News</a>
    </div>

    {{-- 💃 DANCE & MUSIC --}}
    <section class="med-section" id="dance-music-section">
        <div class="med-section-header">
            <h2>Dance &amp; Music</h2>
        </div>

        <div class="med-grid-3">
            <div class="med-card" onclick="openLightbox('/images/media/Dance_class.jpg')">
                <div class="med-card__img-box">
                    <img src="/images/media/Dance_class.jpg" alt="Dance & Music" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/student-playing-keyboard.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/student-playing-keyboard.webp" alt="Dance & Music" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Teacher-teaching-keyboard.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Teacher-teaching-keyboard.webp" alt="Dance & Music" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ⚽ SPORTS --}}
    <section class="med-section" id="sports-section" style="background:#f1f5f9">
        <div class="med-section-header">
            <h2>Sports</h2>
        </div>

        <div class="med-grid-3">
            <div class="med-card" onclick="openLightbox('/images/media/Football.jpg')">
                <div class="med-card__img-box">
                    <img src="/images/media/Football.jpg" alt="Sports" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Shooting.jpg')">
                <div class="med-card__img-box">
                    <img src="/images/media/Shooting.jpg" alt="Sports" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Basket.jpg')">
                <div class="med-card__img-box">
                    <img src="/images/media/Basket.jpg" alt="Sports" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 🎨 ARTS & CRAFT --}}
    <section class="med-section" id="arts-craft-section">
        <div class="med-section-header">
            <h2>Arts &amp; Craft</h2>
        </div>

        <div class="med-grid-3">
            <div class="med-card" onclick="openLightbox('/images/media/Painting-practice-prayaag-student.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Painting-practice-prayaag-student.webp" alt="Arts & Craft" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Painting-at-Prayaag-International-School.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Painting-at-Prayaag-International-School.webp" alt="Arts & Craft" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Prayaag-International-School-Laibrary.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Prayaag-International-School-Laibrary.webp" alt="Arts & Craft" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 🎈 FUN ACTIVITIES --}}
    <section class="med-section" id="fun-activities-section" style="background:#f1f5f9">
        <div class="med-section-header">
            <h2>Fun Activities</h2>
        </div>

        <div class="med-grid-3">
            <div class="med-card" onclick="openLightbox('/images/media/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp" alt="Fun Activities" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Junior-children-playing.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Junior-children-playing.webp" alt="Fun Activities" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>

            <div class="med-card" onclick="openLightbox('/images/media/Children-playing-at-swimimg-pool.webp')">
                <div class="med-card__img-box">
                    <img src="/images/media/Children-playing-at-swimimg-pool.webp" alt="Fun Activities" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 📰 NEWS CAROUSEL SLIDER (TOUCH SWIPEABLE) --}}
    <section class="news-slider-section" id="news-section">
        <div class="news-slider-header">
            <h2>News</h2>
        </div>

        @php
            $newsClippings = [
                '/images/media/WhatsApp-Image-2025-08-21-at-10.50.47-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-09-30-at-10.16.22-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-10-08-at-2.28.58-PM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-10-09-at-9.41.27-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-10-18-at-8.45.26-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-11-10-at-2.24.58-PM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-11-11-at-4.53.30-PM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-11-16-at-10.00.53-AM_1350x1350.jpg',
                '/images/media/News-5.jpg',
                '/images/media/WhatsApp-Image-2026-01-19-at-12.54.27-PM-1.jpeg',
                '/images/media/WhatsApp-Image-2025-09-30-at-10.16.21-AM_1350x1350.jpg',
                '/images/media/WhatsApp-Image-2025-09-30-at-10.16.19-AM_1350x1350.jpg',
                '/images/media/News-6.jpg',
                '/images/media/News-4.jpg',
                '/images/media/News-2.jpg',
                '/images/media/News-1.jpg',
                '/images/media/news-123.jpeg',
            ];
        @endphp

        <div class="news-carousel-container" id="newsCarouselContainer">
            <div class="news-carousel-track-wrapper" id="carouselWrapper">
                <div class="news-carousel-track" id="carouselTrack">
                    @foreach($newsClippings as $idx => $imgSrc)
                        <div class="news-slide-card" onclick="openLightbox('{{ $imgSrc }}')">
                            <div class="news-slide-img-box">
                                <img src="{{ $imgSrc }}" alt="News" loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Controls --}}
            <div class="news-carousel-controls">
                <div class="news-carousel-btn-group">
                    <button type="button" class="news-nav-btn" onclick="prevSlide()" aria-label="Previous Slide">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button type="button" class="news-nav-btn" onclick="nextSlide()" aria-label="Next Slide">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <button type="button" class="news-nav-btn" id="playPauseBtn" onclick="toggleAutoPlay()" aria-label="Pause AutoPlay" style="font-size:14px">
                        ⏸️
                    </button>
                </div>
                <div class="news-counter-pill" id="slideCounter">
                    Slide 1 of 17
                </div>
            </div>
        </div>
    </section>
</div>

{{-- 🔍 FULLSCREEN LIGHTBOX MODAL --}}
<div id="mediaLightbox" class="med-lightbox-overlay" onclick="closeLightbox(event)">
    <div class="med-lightbox-container" onclick="event.stopPropagation()">
        <div class="med-lightbox-header">
            <button type="button" class="med-lightbox-close" onclick="closeLightbox()" aria-label="Close">✕</button>
        </div>
        <div class="med-lightbox-body">
            <img id="lightboxImg" src="" alt="Zoom Preview">
        </div>
    </div>
</div>

<script>
// ── CONFIGURABLE WIDGET SETTINGS ────────────────────────────────────
const CONFIG_AUTOPLAY     = {{ $autoplay ? 'true' : 'false' }};
const CONFIG_INTERVAL     = {{ $interval }}; // 3000ms = 3 sec
const CONFIG_PAUSE_HOVER  = {{ $pauseHover ? 'true' : 'false' }};

let currentSlide = 0;
const totalSlides = 17;
let autoPlayInterval = null;
let isAutoPlaying = CONFIG_AUTOPLAY;

function getVisibleSlides() {
    if (window.innerWidth <= 640) return 1;
    if (window.innerWidth <= 980) return 2;
    return 3;
}

function updateSlider() {
    const track = document.getElementById('carouselTrack');
    if (!track || !track.children.length) return;
    
    const visible = getVisibleSlides();
    const maxIndex = Math.max(0, totalSlides - visible);
    
    if (currentSlide > maxIndex) currentSlide = 0;
    if (currentSlide < 0) currentSlide = maxIndex;

    const gap = window.innerWidth <= 640 ? 12 : 20;
    const cardWidth = track.children[0].offsetWidth + gap;
    track.style.transform = `translateX(-${currentSlide * cardWidth}px)`;
    
    const counter = document.getElementById('slideCounter');
    if (counter) {
        counter.innerText = `Slide ${currentSlide + 1} of ${totalSlides}`;
    }
}

function nextSlide() {
    const visible = getVisibleSlides();
    const maxIndex = Math.max(0, totalSlides - visible);
    if (currentSlide >= maxIndex) {
        currentSlide = 0;
    } else {
        currentSlide++;
    }
    updateSlider();
}

function prevSlide() {
    const visible = getVisibleSlides();
    const maxIndex = Math.max(0, totalSlides - visible);
    if (currentSlide <= 0) {
        currentSlide = maxIndex;
    } else {
        currentSlide--;
    }
    updateSlider();
}

function startAutoPlay() {
    if (!CONFIG_AUTOPLAY && !isAutoPlaying) return;
    stopAutoPlay();
    autoPlayInterval = setInterval(nextSlide, CONFIG_INTERVAL);
    isAutoPlaying = true;
    const btn = document.getElementById('playPauseBtn');
    if (btn) btn.innerText = '⏸️';
}

function stopAutoPlay() {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
        autoPlayInterval = null;
    }
    isAutoPlaying = false;
    const btn = document.getElementById('playPauseBtn');
    if (btn) btn.innerText = '▶️';
}

function toggleAutoPlay() {
    if (isAutoPlaying) {
        stopAutoPlay();
    } else {
        startAutoPlay();
    }
}

// ── MOUSE HOVER PAUSE & RESUME ──────────────────────────────────────
if (CONFIG_PAUSE_HOVER) {
    const container = document.getElementById('newsCarouselContainer');
    if (container) {
        container.addEventListener('mouseenter', () => {
            if (isAutoPlaying) stopAutoPlay();
        });
        container.addEventListener('mouseleave', () => {
            startAutoPlay();
        });
    }
}

// ── MOBILE TOUCH SWIPE SUPPORT ──────────────────────────────────────
(function initTouchSwipe() {
    const container = document.getElementById('newsCarouselContainer');
    if (!container) return;

    let touchStartX = 0;
    let touchEndX = 0;

    container.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
        stopAutoPlay();
    }, { passive: true });

    container.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchStartX - touchEndX;
        if (Math.abs(diff) > 40) {
            if (diff > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        }
        if (CONFIG_AUTOPLAY) {
            setTimeout(startAutoPlay, 2000);
        }
    }, { passive: true });
})();

// ── LIGHTBOX LOGIC ──────────────────────────────────────────────────
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    const modal = document.getElementById('mediaLightbox');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    stopAutoPlay();
}

function closeLightbox(e) {
    if (e && e.target !== e.currentTarget && !e.target.classList.contains('med-lightbox-close')) {
        return;
    }
    const modal = document.getElementById('mediaLightbox');
    modal.classList.remove('active');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
    if (CONFIG_AUTOPLAY) {
        startAutoPlay();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

window.addEventListener('resize', updateSlider);
document.addEventListener('DOMContentLoaded', () => {
    if (CONFIG_AUTOPLAY) {
        startAutoPlay();
    }
    updateSlider();
});
</script>
