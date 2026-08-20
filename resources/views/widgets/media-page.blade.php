{{--
    Media ("Life at Prayaag") Page Widget — Ultra-Premium Campus Galleries & News Slider
    Designed with School Design System Tokens (Navy, Gold, Playfair/Poppins) + Interactive Swiper & Lightbox
--}}

<style>
/* ================================================================
   MEDIA ("LIFE AT PRAYAAG") — SCOPED FULL-WIDTH CSS
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
    min-height: 480px;
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
    padding: 100px 4vw 70px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.med-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--gold-2, #f59e0b);
    margin-bottom: 16px;
    background: rgba(245,158,11,0.12);
    padding: 6px 16px;
    border-radius: 999px;
    border: 1px solid rgba(245,158,11,0.3);
}

.med-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 18px;
}

.med-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.med-hero__sub {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.88);
    max-width: 800px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.med-hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.med-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--gold, #d97706), #b45309);
    color: #ffffff !important;
    padding: 13px 26px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.95rem;
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
    gap: 8px;
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    padding: 13px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
}

.med-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ── Media Stats Bar ─────────────────────────────────────────────── */
.med-stats-bar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px -4px rgba(0,0,0,0.06);
    position: relative;
    z-index: 10;
    width: 100%;
}

.med-stats-grid {
    max-width: 100%;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    padding: 0 4vw;
}

.med-stat-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 20px;
    border-right: 1px solid #f1f5f9;
}

.med-stat-item:last-child {
    border-right: none;
}

.med-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: grid;
    place-items: center;
    font-size: 22px;
    color: var(--navy, #0b2545);
    flex-shrink: 0;
}

.med-stat-info h4 {
    margin: 0 0 2px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}

.med-stat-info p {
    margin: 0;
    font-size: 0.84rem;
    color: #64748b;
    font-weight: 500;
}

/* ── Filter Tabs Bar ─────────────────────────────────────────────── */
.med-filter-bar {
    position: sticky;
    top: 70px;
    z-index: 40;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 16px -4px rgba(0,0,0,0.04);
    padding: 14px 4vw;
    display: flex;
    align-items: center;
    gap: 10px;
    overflow-x: auto;
}

.med-tab-btn {
    padding: 9px 20px;
    border-radius: 999px;
    font-size: 0.88rem;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    white-space: nowrap;
    text-decoration: none;
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
    padding: 60px 4vw 70px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.med-section-header {
    margin-bottom: 32px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 16px;
}

.med-section-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.8rem, 3vw, 2.3rem);
    color: #0f172a;
    margin: 0 0 6px;
}

.med-section-header p {
    font-size: 0.95rem;
    color: #64748b;
    margin: 0;
}

.med-badge {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    padding: 6px 14px;
    border-radius: 999px;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

/* ── 3-Image Grid Layout ─────────────────────────────────────────── */
.med-grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 26px;
    margin-bottom: 40px;
}

.med-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px -6px rgba(0,0,0,0.06);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
}

.med-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px -10px rgba(11,37,69,0.15);
    border-color: #cbd5e1;
}

.med-card__img-box {
    position: relative;
    width: 100%;
    padding-top: 68%;
    overflow: hidden;
    background: #0b2545;
    cursor: pointer;
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
    padding: 10px 18px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transform: translateY(10px);
    transition: transform 0.3s ease;
}

.med-card:hover .med-zoom-badge {
    transform: translateY(0);
}

.med-card__body {
    padding: 22px 24px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.med-card__title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 6px;
}

.med-card__caption {
    font-size: 0.88rem;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

/* ── News Clipping Carousel / Slider ─────────────────────────────── */
.news-slider-section {
    background: #0b2545;
    color: #ffffff;
    padding: 70px 4vw 80px;
}

.news-slider-header {
    text-align: center;
    max-width: 780px;
    margin: 0 auto 40px;
}

.news-slider-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2rem, 3.5vw, 2.7rem);
    color: #ffffff;
    margin: 0 0 10px;
}

.news-slider-header h2 span {
    color: var(--gold-2, #f59e0b);
}

.news-slider-header p {
    font-size: 1rem;
    color: rgba(255,255,255,0.85);
    margin: 0;
}

.news-carousel-container {
    position: relative;
    max-width: 100%;
    margin: 0 auto;
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
    transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
    will-change: transform;
}

.news-slide-card {
    flex: 0 0 calc(33.333% - 14px);
    min-width: 300px;
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
    }
}

@media(max-width: 640px) {
    .news-slide-card {
        flex: 0 0 100%;
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

.news-slide-caption-box {
    padding: 16px 18px;
    background: #ffffff;
}

.news-slide-caption-box h4 {
    font-size: 0.98rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px;
    line-height: 1.35;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.news-slide-caption-box p {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* ── Carousel Nav Controls ───────────────────────────────────────── */
.news-carousel-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.news-carousel-btn-group {
    display: flex;
    align-items: center;
    gap: 12px;
}

.news-nav-btn {
    width: 46px;
    height: 46px;
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
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 0.88rem;
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
    padding: 20px;
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
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #ffffff;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.med-lightbox-title h3 {
    margin: 0 0 2px;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--gold-2, #f59e0b);
}

.med-lightbox-title p {
    margin: 0;
    font-size: 0.84rem;
    color: rgba(255,255,255,0.8);
}

.med-lightbox-close {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(255,255,255,0.12);
    border: none;
    color: #ffffff;
    font-size: 20px;
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
    padding: 20px;
    background: #050e1a;
    overflow: auto;
}

.med-lightbox-body img {
    max-width: 100%;
    max-height: 72vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
</style>

<div class="med-wrapper">
    {{-- 🌟 HERO BANNER --}}
    <section class="med-hero">
        <div class="med-hero__bg"></div>
        <div class="med-hero__overlay"></div>
        <div class="med-hero__content">
            <div class="med-eyebrow">
                <span>📸</span> Campus Life, Activities &amp; Media Gallery
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
                    View Press News Slider
                </a>
                <a href="#dance-music-section" class="med-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    Explore Campus Galleries
                </a>
            </div>
        </div>
    </section>

    {{-- 📊 MEDIA STATS BAR --}}
    <div class="med-stats-bar">
        <div class="med-stats-grid">
            <div class="med-stat-item">
                <div class="med-stat-icon">💃</div>
                <div class="med-stat-info">
                    <h4>Performing Arts</h4>
                    <p>Dance &amp; Instrumental Studio</p>
                </div>
            </div>
            <div class="med-stat-item">
                <div class="med-stat-icon">⚽</div>
                <div class="med-stat-info">
                    <h4>Sports Arenas</h4>
                    <p>Shooting, Football &amp; Basketball</p>
                </div>
            </div>
            <div class="med-stat-item">
                <div class="med-stat-icon">🎨</div>
                <div class="med-stat-info">
                    <h4>Arts &amp; Library</h4>
                    <p>Fine Arts Atelier &amp; Resource Hub</p>
                </div>
            </div>
            <div class="med-stat-item">
                <div class="med-stat-icon">📰</div>
                <div class="med-stat-info">
                    <h4>17+ Press Features</h4>
                    <p>Interactive Newspaper Slider</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 🎛️ STICKY CATEGORY FILTER TABS --}}
    <div class="med-filter-bar">
        <a href="#dance-music-section" class="med-tab-btn">💃 Dance &amp; Music</a>
        <a href="#sports-section" class="med-tab-btn">⚽ Sports &amp; Athletics</a>
        <a href="#arts-craft-section" class="med-tab-btn">🎨 Arts &amp; Craft</a>
        <a href="#fun-activities-section" class="med-tab-btn">🎈 Fun Activities</a>
        <a href="#news-section" class="med-tab-btn" style="background:#fef3c7;color:#b45309;border-color:#fde68a">📰 News &amp; Press Slider</a>
    </div>

    {{-- 💃 SECTION 1: DANCE & MUSIC --}}
    <section class="med-section" id="dance-music-section">
        <div class="med-section-header">
            <div>
                <h2>Section 1: Dance &amp; Music</h2>
                <p>Cultivating rhythm, vocal harmony, and performing arts excellence across classical and contemporary genres.</p>
            </div>
            <span class="med-badge">3 Photo Showcases</span>
        </div>

        <div class="med-grid-3">
            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Dance_class.jpg', 'Classical & Contemporary Dance Studio', 'Students training in performing arts, rhythmic discipline, and expressive stage choreography.')">
                    <img src="/images/media/Dance_class.jpg" alt="Classical & Contemporary Dance Studio" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Classical &amp; Contemporary Dance Studio</h3>
                    <p class="med-card__caption">Students training in performing arts, rhythmic discipline, and expressive stage choreography.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/student-playing-keyboard.webp', 'Instrumental Keyboard Lessons', 'Hands-on synthesizer, acoustic melody, and musical keyboard practice under faculty mentorship.')">
                    <img src="/images/media/student-playing-keyboard.webp" alt="Instrumental Keyboard Lessons" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Instrumental Keyboard Lessons</h3>
                    <p class="med-card__caption">Hands-on synthesizer, acoustic melody, and musical keyboard practice under faculty mentorship.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Teacher-teaching-keyboard.webp', 'Faculty Mentorship & Theory', 'Dedicated music educators guiding foundational scales, classical compositions, and stage readiness.')">
                    <img src="/images/media/Teacher-teaching-keyboard.webp" alt="Faculty Mentorship & Theory" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Faculty Mentorship &amp; Theory</h3>
                    <p class="med-card__caption">Dedicated music educators guiding foundational scales, classical compositions, and stage readiness.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ⚽ SECTION 2: SPORTS & ATHLETICS --}}
    <section class="med-section" id="sports-section" style="background:#f1f5f9">
        <div class="med-section-header">
            <div>
                <h2>Section 2: Sports &amp; Athletics</h2>
                <p>Building resilience, championship mindset, teamwork, and Olympic-grade sporting discipline.</p>
            </div>
            <span class="med-badge" style="background:#f0fdf4;color:#15803d;border-color:#bbf7d0">3 Sports Arenas</span>
        </div>

        <div class="med-grid-3">
            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Football.jpg', 'Football Championship Turf', 'Inter-house football leagues and professional tactical agility drills.')">
                    <img src="/images/media/Football.jpg" alt="Football Championship Turf" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Football Championship Turf</h3>
                    <p class="med-card__caption">Inter-house football leagues and professional tactical agility drills on FIFA-spec natural turf.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Shooting.jpg', 'Precision 10m Shooting Range', 'Olympic-standard 10m Air Rifle and Pistol shooting arena with electronic targets and certified coaches.')">
                    <img src="/images/media/Shooting.jpg" alt="Precision 10m Shooting Range" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Precision 10m Shooting Range</h3>
                    <p class="med-card__caption">Olympic-standard 10m Air Rifle and Pistol shooting arena with electronic targets and certified coaches.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Basket.jpg', 'Standard Basketball Arena', 'All-weather hardcourt for competitive dribbling, team strategy, and CBSE zonal tournament play.')">
                    <img src="/images/media/Basket.jpg" alt="Standard Basketball Arena" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Standard Basketball Arena</h3>
                    <p class="med-card__caption">All-weather hardcourt for competitive dribbling, team strategy, and CBSE zonal tournament play.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 🎨 SECTION 3: ARTS & CRAFT --}}
    <section class="med-section" id="arts-craft-section">
        <div class="med-section-header">
            <div>
                <h2>Section 3: Arts &amp; Craft</h2>
                <p>Fostering visual imagination, fine motor creativity, and expansive knowledge discovery.</p>
            </div>
            <span class="med-badge">3 Creative Ateliers</span>
        </div>

        <div class="med-grid-3">
            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Painting-practice-prayaag-student.webp', 'Creative Painting Studio', 'Oil, acrylic, watercolor, and canvas painting workshops cultivating artistic aesthetics.')">
                    <img src="/images/media/Painting-practice-prayaag-student.webp" alt="Creative Painting Studio" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Creative Painting Studio</h3>
                    <p class="med-card__caption">Oil, acrylic, watercolor, and canvas painting workshops cultivating artistic aesthetics.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Painting-at-Prayaag-International-School.webp', 'Visual Arts Exhibition', 'Student canvas artwork displayed across school galleries during annual inter-school conclaves.')">
                    <img src="/images/media/Painting-at-Prayaag-International-School.webp" alt="Visual Arts Exhibition" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Visual Arts Exhibition</h3>
                    <p class="med-card__caption">Student canvas artwork displayed across school galleries during annual inter-school conclaves.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Prayaag-International-School-Laibrary.webp', 'Knowledge & Resource Center', 'Expansive air-conditioned library with curated reference volumes, journals, and quiet reading lounges.')">
                    <img src="/images/media/Prayaag-International-School-Laibrary.webp" alt="Knowledge & Resource Center" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Knowledge &amp; Resource Center</h3>
                    <p class="med-card__caption">Expansive air-conditioned library with curated reference volumes, journals, and quiet reading lounges.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 🎈 SECTION 4: FUN ACTIVITIES & EARLY LEARNING --}}
    <section class="med-section" id="fun-activities-section" style="background:#f1f5f9">
        <div class="med-section-header">
            <div>
                <h2>Section 4: Fun Activities &amp; Play-Way Learning</h2>
                <p>Early childhood sensory development, kindergarten play zones, and aquatic confidence.</p>
            </div>
            <span class="med-badge" style="background:#fef3c7;color:#b45309;border-color:#fde68a">3 Activity Zones</span>
        </div>

        <div class="med-grid-3">
            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp', 'Pre-Primary Sensory Play', 'Montessori sensory toys and activity play stations for cognitive and social development.')">
                    <img src="/images/media/Fun-Activity-for-Play-school-children-at-prayaag-International-School.webp" alt="Pre-Primary Sensory Play" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Pre-Primary Sensory Play</h3>
                    <p class="med-card__caption">Montessori sensory toys and activity play stations for cognitive and social development.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Junior-children-playing.webp', 'Junior Kindergarten Playzone', 'Safe outdoor play facilities with soft-fall turf, swings, and interactive group motor games.')">
                    <img src="/images/media/Junior-children-playing.webp" alt="Junior Kindergarten Playzone" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Junior Kindergarten Playzone</h3>
                    <p class="med-card__caption">Safe outdoor play facilities with soft-fall turf, swings, and interactive group motor games.</p>
                </div>
            </div>

            <div class="med-card">
                <div class="med-card__img-box" onclick="openLightbox('/images/media/Children-playing-at-swimimg-pool.webp', 'Junior Splash & Swimming Pool', 'Supervised shallow splash pool for early aquatic confidence, water safety, and recreation.')">
                    <img src="/images/media/Children-playing-at-swimimg-pool.webp" alt="Junior Splash & Swimming Pool" loading="lazy">
                    <div class="med-card__overlay">
                        <span class="med-zoom-badge">🔍 Click to Zoom</span>
                    </div>
                </div>
                <div class="med-card__body">
                    <h3 class="med-card__title">Junior Splash &amp; Swimming Pool</h3>
                    <p class="med-card__caption">Supervised shallow splash pool for early aquatic confidence, water safety, and recreation.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 📰 SECTION 5: NEWS & PRESS CLIPPINGS CAROUSEL SLIDER --}}
    <section class="news-slider-section" id="news-section">
        <div class="news-slider-header">
            <h2>Prayaag in the News — <span>Press Clippings Slider</span></h2>
            <p>17 official regional newspaper features, academic distinctions, and sports accolades published in leading media. Click any clipping to zoom &amp; read.</p>
        </div>

        @php
            $newsClippings = [
                ['src' => '/images/media/WhatsApp-Image-2025-08-21-at-10.50.47-AM_1350x1350.jpg', 'title' => 'Academic Distinction & Meritorious Awards', 'caption' => 'Prayaag students felicitated for board examination excellence in state media.'],
                ['src' => '/images/media/WhatsApp-Image-2025-09-30-at-10.16.22-AM_1350x1350.jpg', 'title' => 'District Shooting & Athletics Championship', 'caption' => 'Gold and Silver medal sweep covered by leading regional press.'],
                ['src' => '/images/media/WhatsApp-Image-2025-10-08-at-2.28.58-PM_1350x1350.jpg', 'title' => 'Annual Cultural Conclave & Grand Exhibition', 'caption' => 'Grand celebration of performing arts and robotics showcased in media.'],
                ['src' => '/images/media/WhatsApp-Image-2025-10-09-at-9.41.27-AM_1350x1350.jpg', 'title' => 'CBSE Science & Innovation Olympiad Winners', 'caption' => 'National Olympiad achievers featured in morning headlines.'],
                ['src' => '/images/media/WhatsApp-Image-2025-10-18-at-8.45.26-AM_1350x1350.jpg', 'title' => 'Inter-School Sports Carnival Trophy', 'caption' => 'Overall championship trophy awarded to Prayaag International School.'],
                ['src' => '/images/media/WhatsApp-Image-2025-11-10-at-2.24.58-PM_1350x1350.jpg', 'title' => 'State Level Yoga & Fitness Felicitation', 'caption' => 'Prayaag yoga contingent wins laurels at Haryana State Meet.'],
                ['src' => '/images/media/WhatsApp-Image-2025-11-11-at-4.53.30-PM_1350x1350.jpg', 'title' => 'Eco-Club Green Campus Initiative', 'caption' => 'Tree plantation and sustainability drive recognized by municipal authorities.'],
                ['src' => '/images/media/WhatsApp-Image-2025-11-16-at-10.00.53-AM_1350x1350.jpg', 'title' => 'Children\'s Day Grand Gala & Competitions', 'caption' => 'Vibrant carnival activities featured across Panipat editions.'],
                ['src' => '/images/media/News-5.jpg', 'title' => 'Martial Arts & Taekwondo Belt Ceremony', 'caption' => 'Black belt qualifiers and self-defense camp highlighted in press.'],
                ['src' => '/images/media/WhatsApp-Image-2026-01-19-at-12.54.27-PM-1.jpeg', 'title' => 'Republic Day March Past Honors', 'caption' => 'School marching band and NCC troop recognized at district level parade.'],
                ['src' => '/images/media/WhatsApp-Image-2025-09-30-at-10.16.21-AM_1350x1350.jpg', 'title' => 'Vedic Mathematics & Abacus Championship', 'caption' => 'Speed calculation prodigies claim top three ranks.'],
                ['src' => '/images/media/WhatsApp-Image-2025-09-30-at-10.16.19-AM_1350x1350.jpg', 'title' => 'Model United Nations (MUN) Leadership Delegation', 'caption' => 'Diplomatic debaters win best delegate accolades.'],
                ['src' => '/images/media/News-6.jpg', 'title' => 'Parent-Teacher Collaborative Forum', 'caption' => 'Holistic pedagogy and technology-driven classrooms covered in education weekly.'],
                ['src' => '/images/media/News-4.jpg', 'title' => 'National Skating Championship Accolades', 'caption' => 'Speed rollerskating medalists bring home national trophies.'],
                ['src' => '/images/media/News-2.jpg', 'title' => 'Robotics & AI Expo Showcase', 'caption' => 'Future innovators design assistive robots for rural agriculture.'],
                ['src' => '/images/media/News-1.jpg', 'title' => 'CBSE Class X & XII Meritorious List', 'caption' => '100% pass result with school toppers scoring 98%+ aggregate.'],
                ['src' => '/images/media/news-123.jpeg', 'title' => 'Special Feature: Experiential Learning Pedagogy', 'caption' => 'Prayaag recognized among top progressive CBSE schools in Haryana.'],
            ];
        @endphp

        <div class="news-carousel-container">
            <div class="news-carousel-track-wrapper" id="carouselWrapper">
                <div class="news-carousel-track" id="carouselTrack">
                    @foreach($newsClippings as $idx => $item)
                        <div class="news-slide-card" onclick="openLightbox('{{ $item['src'] }}', '{{ $item['title'] }}', '{{ $item['caption'] }}')">
                            <div class="news-slide-img-box">
                                <img src="{{ $item['src'] }}" alt="{{ $item['title'] }}" loading="lazy">
                            </div>
                            <div class="news-slide-caption-box">
                                <h4>{{ $item['title'] }}</h4>
                                <p>{{ $item['caption'] }}</p>
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
            <div class="med-lightbox-title">
                <h3 id="lightboxTitle">Media Preview</h3>
                <p id="lightboxCaption">Description</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <a id="lightboxFullBtn" href="#" target="_blank" class="med-btn-secondary" style="padding:8px 14px;font-size:0.82rem">
                    Full Resolution ↗
                </a>
                <button type="button" class="med-lightbox-close" onclick="closeLightbox()">✕</button>
            </div>
        </div>
        <div class="med-lightbox-body">
            <img id="lightboxImg" src="" alt="Zoom Preview">
        </div>
    </div>
</div>

<script>
// ── NEWS SLIDER LOGIC ───────────────────────────────────────────────
let currentSlide = 0;
const totalSlides = 17;
let autoPlayInterval = null;
let isAutoPlaying = true;

function getVisibleSlides() {
    if (window.innerWidth <= 640) return 1;
    if (window.innerWidth <= 980) return 2;
    return 3;
}

function updateSlider() {
    const track = document.getElementById('carouselTrack');
    const visible = getVisibleSlides();
    const maxIndex = Math.max(0, totalSlides - visible);
    
    if (currentSlide > maxIndex) currentSlide = 0;
    if (currentSlide < 0) currentSlide = maxIndex;

    const cardWidth = track.children[0].offsetWidth + 20; // 20px gap
    track.style.transform = `translateX(-${currentSlide * cardWidth}px)`;
    
    document.getElementById('slideCounter').innerText = `Slide ${currentSlide + 1} of ${totalSlides}`;
}

function nextSlide() {
    currentSlide++;
    updateSlider();
}

function prevSlide() {
    currentSlide--;
    updateSlider();
}

function startAutoPlay() {
    stopAutoPlay();
    autoPlayInterval = setInterval(nextSlide, 3500);
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

// ── LIGHTBOX LOGIC ──────────────────────────────────────────────────
function openLightbox(src, title, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxTitle').innerText = title || 'Media Showcase';
    document.getElementById('lightboxCaption').innerText = caption || '';
    document.getElementById('lightboxFullBtn').href = src;
    
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
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

window.addEventListener('resize', updateSlider);
document.addEventListener('DOMContentLoaded', () => {
    startAutoPlay();
});
</script>
