{{--
    Book List Widget — Ultra-Premium Academic Textbook & Syllabus Catalog
    Designed with School Design System Tokens (Navy, Gold, Playfair/Poppins)
--}}

<style>
/* ================================================================
   BOOK LIST PAGE — SCOPED ULTRA-PREMIUM FULL-WIDTH CSS
   ================================================================ */

.bkl-wrapper {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow-x: hidden;
    background: #f8fafc;
}

.pb-section:has(.bkl-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.bkl-wrapper) .pb-row,
.pb-section:has(.bkl-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.bkl-hero {
    position: relative;
    min-height: 460px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.bkl-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp');
    background-size: cover;
    background-position: center;
    opacity: .28;
    transform: scale(1.03);
    transition: transform 6s ease-out;
}

.bkl-hero:hover .bkl-hero__bg {
    transform: scale(1.08);
}

.bkl-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.95) 0%, rgba(11,37,69,0.75) 60%, rgba(197,143,39,0.2) 100%);
}

.bkl-hero__content {
    position: relative;
    z-index: 2;
    padding: 100px 4vw 70px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.bkl-eyebrow {
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

.bkl-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 18px;
}

.bkl-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.bkl-hero__sub {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.85);
    max-width: 780px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.bkl-hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.bkl-btn-primary {
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

.bkl-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

.bkl-btn-secondary {
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

.bkl-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ── Highlight Stats Bar ─────────────────────────────────────────── */
.bkl-stats-bar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px -4px rgba(0,0,0,0.06);
    position: relative;
    z-index: 10;
    width: 100%;
}

.bkl-stats-grid {
    max-width: 100%;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    padding: 0 4vw;
}

.bkl-stat-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 20px;
    border-right: 1px solid #f1f5f9;
}

.bkl-stat-item:last-child {
    border-right: none;
}

.bkl-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: grid;
    place-items: center;
    font-size: 20px;
    color: var(--navy, #0b2545);
    flex-shrink: 0;
}

.bkl-stat-info h4 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}

.bkl-stat-info p {
    margin: 2px 0 0;
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

/* ── Main Catalog Section ────────────────────────────────────────── */
.bkl-section {
    padding: 60px 4vw 80px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.bkl-header-center {
    text-align: center;
    max-width: 780px;
    margin: 0 auto 40px;
}

.bkl-header-center h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2rem, 3.5vw, 2.7rem);
    color: #0f172a;
    margin: 0 0 12px;
}

.bkl-header-center p {
    font-size: 1rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

/* ── Filter & Search Bar ─────────────────────────────────────────── */
.bkl-filter-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 40px;
    box-shadow: 0 6px 24px -6px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.bkl-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.bkl-tab-btn {
    padding: 9px 18px;
    border-radius: 999px;
    font-size: 0.88rem;
    font-weight: 600;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.2s ease;
}

.bkl-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.bkl-tab-btn.active {
    background: var(--navy, #0b2545);
    color: #ffffff;
    border-color: var(--navy, #0b2545);
    box-shadow: 0 3px 10px rgba(11,37,69,0.25);
}

.bkl-search-box {
    position: relative;
    min-width: 260px;
    flex: 1;
    max-width: 340px;
}

.bkl-search-box input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border-radius: 999px;
    border: 1px solid #cbd5e1;
    font-size: 0.88rem;
    outline: none;
    transition: border-color 0.2s ease;
}

.bkl-search-box input:focus {
    border-color: var(--gold, #d97706);
    box-shadow: 0 0 0 3px rgba(217,119,6,0.12);
}

.bkl-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

/* ── Class Book Cards Grid ───────────────────────────────────────── */
.bkl-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
    margin-bottom: 60px;
}

.bkl-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 8px 30px -6px rgba(0,0,0,0.06);
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.bkl-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px -10px rgba(11,37,69,0.12);
    border-color: #cbd5e1;
}

.bkl-card__top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 14px;
}

.bkl-wing-badge {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    padding: 4px 12px;
    border-radius: 999px;
}

.wing-primary { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.wing-middle { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
.wing-senior { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
.wing-pre { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

.bkl-card__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.45rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 6px;
}

.bkl-card__meta {
    font-size: 0.85rem;
    color: #64748b;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.bkl-card__meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.bkl-subjects-wrap {
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.bkl-subject-chip {
    font-size: 0.76rem;
    padding: 3px 10px;
    background: #f1f5f9;
    color: #334155;
    border-radius: 6px;
    font-weight: 500;
}

.bkl-card__actions {
    display: flex;
    gap: 10px;
    align-items: center;
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
    margin-top: auto;
}

.bkl-download-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #0f172a;
    color: #ffffff !important;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.bkl-download-btn:hover {
    background: var(--gold, #d97706);
}

.bkl-preview-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
}

.bkl-preview-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

/* ── Archive Download Table ──────────────────────────────────────── */
.bkl-archive-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 32px;
    box-shadow: 0 10px 36px -10px rgba(0,0,0,0.06);
    margin-bottom: 60px;
}

.bkl-archive-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.bkl-archive-header h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.6rem;
    color: #0f172a;
    margin: 0;
}

.bkl-archive-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.bkl-archive-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    color: #0f172a;
    transition: all 0.2s ease;
}

.bkl-archive-item:hover {
    background: #ffffff;
    border-color: var(--gold, #d97706);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
}

.bkl-archive-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.bkl-archive-info .pdf-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #fee2e2;
    color: #dc2626;
    display: grid;
    place-items: center;
    font-weight: 800;
    font-size: 0.75rem;
}

.bkl-archive-title {
    font-weight: 700;
    font-size: 0.95rem;
}

.bkl-archive-sub {
    font-size: 0.78rem;
    color: #64748b;
}

/* ── CBSE Compliance & Declaration Banner ────────────────────────── */
.bkl-compliance-banner {
    background: linear-gradient(135deg, #0b2545, #1e3a8a);
    border-radius: 20px;
    padding: 40px;
    color: #ffffff;
    margin-bottom: 60px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px -10px rgba(11,37,69,0.35);
}

.bkl-compliance-banner::before {
    content: '';
    position: absolute;
    right: -40px;
    bottom: -40px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(245,158,11,0.25) 0%, transparent 70%);
    pointer-events: none;
}

.bkl-compliance-grid {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 24px;
    align-items: flex-start;
}

@media(max-width: 640px) {
    .bkl-compliance-grid {
        grid-template-columns: 1fr;
    }
}

.bkl-compliance-icon {
    width: 72px;
    height: 72px;
    border-radius: 16px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    display: grid;
    place-items: center;
    font-size: 32px;
    backdrop-filter: blur(8px);
}

.bkl-compliance-content h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.55rem;
    margin: 0 0 10px;
    color: var(--gold-2, #f59e0b);
}

.bkl-compliance-content p {
    font-size: 0.95rem;
    line-height: 1.7;
    color: rgba(255,255,255,0.85);
    margin: 0 0 16px;
}

.bkl-compliance-checklist {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 12px;
    margin-top: 18px;
}

.bkl-check-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.88rem;
    font-weight: 500;
    color: rgba(255,255,255,0.92);
}

.bkl-check-item svg {
    color: #4ade80;
    flex-shrink: 0;
}

/* ── FAQ Section ─────────────────────────────────────────────────── */
.bkl-faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 60px;
}

.bkl-faq-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px -4px rgba(0,0,0,0.04);
}

.bkl-faq-card h4 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 10px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.bkl-faq-card h4 span {
    color: var(--gold, #d97706);
}

.bkl-faq-card p {
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

/* ── Quick Assistance CTA ────────────────────────────────────────── */
.bkl-cta-box {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
}

.bkl-cta-box h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.8rem;
    color: #0f172a;
    margin: 0 0 10px;
}

.bkl-cta-box p {
    font-size: 1rem;
    color: #64748b;
    max-width: 540px;
    margin: 0 auto 24px;
}
</style>

<div class="bkl-wrapper">
    {{-- 🌟 HERO BANNER --}}
    <section class="bkl-hero">
        <div class="bkl-hero__bg"></div>
        <div class="bkl-hero__overlay"></div>
        <div class="bkl-hero__content">
            <div class="bkl-eyebrow">
                <span>📚</span> Academic Curriculum &amp; Syllabus
            </div>
            <h1 class="bkl-hero__title">
                Prescribed <span>Book Lists</span> &amp; Stationery
            </h1>
            <p class="bkl-hero__sub">
                Official CBSE-compliant textbook catalogues, syllabus outlines, and stationery recommendations for Academic Session <strong>{{ $currentSession ?? '2025–26' }}</strong>.
            </p>
            <div class="bkl-hero__actions">
                <a href="#class-catalog" class="bkl-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>
                    Explore Class Book Lists
                </a>
                <a href="#archive-section" class="bkl-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download PDF Archive
                </a>
            </div>
        </div>
    </section>

    {{-- 📊 STATS & COMPLIANCE BAR --}}
    <div class="bkl-stats-bar">
        <div class="bkl-stats-grid">
            <div class="bkl-stat-item">
                <div class="bkl-stat-icon">🎓</div>
                <div class="bkl-stat-info">
                    <h4>CBSE &amp; NCERT</h4>
                    <p>Strictly Prescribed Standards</p>
                </div>
            </div>
            <div class="bkl-stat-item">
                <div class="bkl-stat-icon">🏫</div>
                <div class="bkl-stat-info">
                    <h4>Pre-Nur to XII</h4>
                    <p>Complete Wing Coverage</p>
                </div>
            </div>
            <div class="bkl-stat-item">
                <div class="bkl-stat-icon">⚖️</div>
                <div class="bkl-stat-info">
                    <h4>Rule 2.4.7 Compliant</h4>
                    <p>Open Market Vendor Choice</p>
                </div>
            </div>
            <div class="bkl-stat-item">
                <div class="bkl-stat-icon">📥</div>
                <div class="bkl-stat-info">
                    <h4>Instant PDF</h4>
                    <p>Official Digital Copies</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 📚 INTERACTIVE CATALOG SECTION --}}
    <section class="bkl-section" id="class-catalog">
        <div class="bkl-header-center">
            <h2>Select Your Class Book List</h2>
            <p>Filter by academic wing or search directly for specific grades to view prescribed books, subject syllabus, and official PDF documents.</p>
        </div>

        {{-- Filter & Search --}}
        <div class="bkl-filter-wrapper">
            <div class="bkl-tabs">
                <button type="button" class="bkl-tab-btn active" onclick="filterWing('all', this)">All Wings</button>
                <button type="button" class="bkl-tab-btn" onclick="filterWing('pre', this)">🧸 Pre-Primary</button>
                <button type="button" class="bkl-tab-btn" onclick="filterWing('primary', this)">🎒 Primary (I-V)</button>
                <button type="button" class="bkl-tab-btn" onclick="filterWing('middle', this)">🔬 Middle (VI-VIII)</button>
                <button type="button" class="bkl-tab-btn" onclick="filterWing('senior', this)">🎓 Secondary (IX-XII)</button>
            </div>
            <div class="bkl-search-box">
                <svg class="bkl-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="bklSearchInput" placeholder="Search class or subject..." onkeyup="searchBooks()">
            </div>
        </div>

        {{-- Class Cards Grid --}}
        <div class="bkl-grid" id="bookCardGrid">

            {{-- 1. Pre-Nursery & Nursery --}}
            <div class="bkl-card" data-wing="pre" data-title="pre-nursery nursery kindergarten kg playgroup">
                <div>
                    <div class="bkl-card__top">
                        <span class="bkl-wing-badge wing-pre">🧸 Pre-Primary Wing</span>
                        <span style="font-size:11px;font-weight:700;color:#15803d">Session 2025–26</span>
                    </div>
                    <h3 class="bkl-card__title">Pre-Nursery, Nursery &amp; KG</h3>
                    <div class="bkl-card__meta">
                        <span>📖 5 Prescribed Sets</span>
                        <span>🎨 Activity Based</span>
                    </div>
                    <div class="bkl-subjects-wrap">
                        <span class="bkl-subject-chip">Phonics &amp; Rhymes</span>
                        <span class="bkl-subject-chip">Early Numeracy</span>
                        <span class="bkl-subject-chip">Picture Dictionary</span>
                        <span class="bkl-subject-chip">Creative Art &amp; Craft</span>
                    </div>
                </div>
                <div class="bkl-card__actions">
                    <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-Pre-Nur-III-20-21.pdf" target="_blank" class="bkl-download-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF List
                    </a>
                </div>
            </div>

            {{-- 2. Classes I & II --}}
            <div class="bkl-card" data-wing="primary" data-title="class 1 class 2 grade 1 grade 2 first second primary">
                <div>
                    <div class="bkl-card__top">
                        <span class="bkl-wing-badge wing-primary">🎒 Primary Wing</span>
                        <span style="font-size:11px;font-weight:700;color:#1d4ed8">Session 2025–26</span>
                    </div>
                    <h3 class="bkl-card__title">Classes I &amp; II</h3>
                    <div class="bkl-card__meta">
                        <span>📖 6 Core Textbooks</span>
                        <span>🌟 NCERT Aligned</span>
                    </div>
                    <div class="bkl-subjects-wrap">
                        <span class="bkl-subject-chip">English Marigold</span>
                        <span class="bkl-subject-chip">Hindi Rimjhim</span>
                        <span class="bkl-subject-chip">Math Magic</span>
                        <span class="bkl-subject-chip">EVS / Discovery</span>
                        <span class="bkl-subject-chip">Coding &amp; AI Jr.</span>
                    </div>
                </div>
                <div class="bkl-card__actions">
                    <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-Pre-Nur-III-20-21.pdf" target="_blank" class="bkl-download-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF List
                    </a>
                </div>
            </div>

            {{-- 3. Classes III to V --}}
            <div class="bkl-card" data-wing="primary" data-title="class 3 class 4 class 5 grade 3 grade 4 grade 5 primary">
                <div>
                    <div class="bkl-card__top">
                        <span class="bkl-wing-badge wing-primary">🎒 Primary Wing</span>
                        <span style="font-size:11px;font-weight:700;color:#1d4ed8">Session 2025–26</span>
                    </div>
                    <h3 class="bkl-card__title">Classes III, IV &amp; V</h3>
                    <div class="bkl-card__meta">
                        <span>📖 8 Prescribed Books</span>
                        <span>🌱 NEP 2020 Framework</span>
                    </div>
                    <div class="bkl-subjects-wrap">
                        <span class="bkl-subject-chip">English Literature</span>
                        <span class="bkl-subject-chip">Hindi Vyakaran</span>
                        <span class="bkl-subject-chip">Mathematics</span>
                        <span class="bkl-subject-chip">Environmental Studies</span>
                        <span class="bkl-subject-chip">General Knowledge</span>
                    </div>
                </div>
                <div class="bkl-card__actions">
                    <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-4-8.pdf" target="_blank" class="bkl-download-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF List
                    </a>
                </div>
            </div>

            {{-- 4. Classes VI to VIII --}}
            <div class="bkl-card" data-wing="middle" data-title="class 6 class 7 class 8 grade 6 grade 7 grade 8 middle">
                <div>
                    <div class="bkl-card__top">
                        <span class="bkl-wing-badge wing-middle">🔬 Middle Wing</span>
                        <span style="font-size:11px;font-weight:700;color:#be185d">Session 2025–26</span>
                    </div>
                    <h3 class="bkl-card__title">Classes VI, VII &amp; VIII</h3>
                    <div class="bkl-card__meta">
                        <span>📖 10 NCERT Standard Books</span>
                        <span>🌐 3rd Language Option</span>
                    </div>
                    <div class="bkl-subjects-wrap">
                        <span class="bkl-subject-chip">NCERT Mathematics</span>
                        <span class="bkl-subject-chip">NCERT Science</span>
                        <span class="bkl-subject-chip">History / Civics / Geo</span>
                        <span class="bkl-subject-chip">Sanskrit / French</span>
                        <span class="bkl-subject-chip">Computer / Python</span>
                    </div>
                </div>
                <div class="bkl-card__actions">
                    <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-4-8.pdf" target="_blank" class="bkl-download-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF List
                    </a>
                </div>
            </div>

            {{-- 5. Classes IX & X (Secondary Board) --}}
            <div class="bkl-card" data-wing="senior" data-title="class 9 class 10 grade 9 grade 10 board secondary">
                <div>
                    <div class="bkl-card__top">
                        <span class="bkl-wing-badge wing-senior">🎓 Secondary Wing</span>
                        <span style="font-size:11px;font-weight:700;color:#a16207">Session 2025–26</span>
                    </div>
                    <h3 class="bkl-card__title">Classes IX &amp; X (CBSE Board)</h3>
                    <div class="bkl-card__meta">
                        <span>📖 NCERT Textbooks</span>
                        <span>🏆 CBSE Curriculum</span>
                    </div>
                    <div class="bkl-subjects-wrap">
                        <span class="bkl-subject-chip">Maths (Standard/Basic)</span>
                        <span class="bkl-subject-chip">Physics/Chem/Bio (NCERT)</span>
                        <span class="bkl-subject-chip">Social Science (4 Vols)</span>
                        <span class="bkl-subject-chip">Language I &amp; II</span>
                        <span class="bkl-subject-chip">Artificial Intelligence (417)</span>
                    </div>
                </div>
                <div class="bkl-card__actions">
                    <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-9-12.pdf" target="_blank" class="bkl-download-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF List
                    </a>
                </div>
            </div>

            {{-- 6. Classes XI & XII (Sr. Secondary Streams) --}}
            <div class="bkl-card" data-wing="senior" data-title="class 11 class 12 grade 11 grade 12 science commerce humanities senior secondary">
                <div>
                    <div class="bkl-card__top">
                        <span class="bkl-wing-badge wing-senior">🎓 Senior Secondary</span>
                        <span style="font-size:11px;font-weight:700;color:#a16207">Session 2025–26</span>
                    </div>
                    <h3 class="bkl-card__title">Classes XI &amp; XII (All Streams)</h3>
                    <div class="bkl-card__meta">
                        <span>📖 Medical / Non-Med / Comm / Arts</span>
                    </div>
                    <div class="bkl-subjects-wrap">
                        <span class="bkl-subject-chip">Physics &amp; Chemistry</span>
                        <span class="bkl-subject-chip">Mathematics / Biology</span>
                        <span class="bkl-subject-chip">Accountancy &amp; B.St</span>
                        <span class="bkl-subject-chip">Economics &amp; History</span>
                        <span class="bkl-subject-chip">Informatics / Physical Ed.</span>
                    </div>
                </div>
                <div class="bkl-card__actions">
                    <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-9-12.pdf" target="_blank" class="bkl-download-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF List
                    </a>
                </div>
            </div>

        </div>

        {{-- 📜 OFFICIAL ARCHIVE DOWNLOAD CENTER --}}
        <div class="bkl-archive-card" id="archive-section">
            <div class="bkl-archive-header">
                <div>
                    <h3>Official PDF Downloads &amp; Past Sessions Archive</h3>
                    <p style="font-size:0.9rem;color:#64748b;margin:4px 0 0">Direct download links for official school-issued book lists across academic sessions.</p>
                </div>
                <span class="bkl-wing-badge" style="background:#f1f5f9;color:#334155;font-size:12px;padding:6px 14px">
                    🗂️ 8 Archived Documents
                </span>
            </div>

            <div class="bkl-archive-list">
                <a href="https://prayaaginternationalschool.com/wp-content/uploads/2023/09/BOOK_LIST_PrayaagInternationalSchool.com_2023-24.pdf" target="_blank" class="bkl-archive-item">
                    <div class="bkl-archive-info">
                        <div class="pdf-icon">PDF</div>
                        <div>
                            <div class="bkl-archive-title">Complete Book List 2023–24</div>
                            <div class="bkl-archive-sub">All Classes (Pre-Nur to XII) · 1.4 MB</div>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>

                <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-Pre-Nur-III-20-21.pdf" target="_blank" class="bkl-archive-item">
                    <div class="bkl-archive-info">
                        <div class="pdf-icon">PDF</div>
                        <div>
                            <div class="bkl-archive-title">Pre-Nursery to Class III</div>
                            <div class="bkl-archive-sub">Academic Syllabus &amp; Textbooks · 820 KB</div>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>

                <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-4-8.pdf" target="_blank" class="bkl-archive-item">
                    <div class="bkl-archive-info">
                        <div class="pdf-icon">PDF</div>
                        <div>
                            <div class="bkl-archive-title">Classes IV to VIII Book List</div>
                            <div class="bkl-archive-sub">Middle Wing Prescribed List · 940 KB</div>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>

                <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Booklist-9-12.pdf" target="_blank" class="bkl-archive-item">
                    <div class="bkl-archive-info">
                        <div class="pdf-icon">PDF</div>
                        <div>
                            <div class="bkl-archive-title">Classes IX to XII Book List</div>
                            <div class="bkl-archive-sub">Secondary &amp; Senior Secondary · 1.1 MB</div>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>

                <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Book-List-2020-21.pdf" target="_blank" class="bkl-archive-item">
                    <div class="bkl-archive-info">
                        <div class="pdf-icon">PDF</div>
                        <div>
                            <div class="bkl-archive-title">Book List 2020–21 Archive</div>
                            <div class="bkl-archive-sub">Official School Archive · 1.2 MB</div>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>

                <a href="https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Book-List-2019-20.pdf" target="_blank" class="bkl-archive-item">
                    <div class="bkl-archive-info">
                        <div class="pdf-icon">PDF</div>
                        <div>
                            <div class="bkl-archive-title">Book List 2019–20 Archive</div>
                            <div class="bkl-archive-sub">Historical Reference · 980 KB</div>
                        </div>
                    </div>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        {{-- 🏛️ CBSE COMPLIANCE DECLARATION --}}
        <div class="bkl-compliance-banner">
            <div class="bkl-compliance-grid">
                <div class="bkl-compliance-icon">⚖️</div>
                <div class="bkl-compliance-content">
                    <h3>CBSE Affiliation Bye-Laws Compliance Declaration</h3>
                    <p>
                        In strict compliance with <strong>CBSE Affiliation Bye-Laws Rule 2.4.7</strong>, Prayaag International School hereby declares that the textbooks prescribed for all classes have been thoroughly scrutinized by the School Academic Advisory Committee. The contents are aligned with the National Curriculum Framework (NCF) and National Education Policy (NEP 2020).
                    </p>
                    <div class="bkl-compliance-checklist">
                        <div class="bkl-check-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>No objectionable content in prescribed books</span>
                        </div>
                        <div class="bkl-check-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Parents free to purchase from any vendor</span>
                        </div>
                        <div class="bkl-check-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Full adherence to NCERT &amp; CBSE syllabus</span>
                        </div>
                        <div class="bkl-check-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Digital E-Books supported on DIKSHA portal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ❓ PARENT GUIDELINES & FAQS --}}
        <div class="bkl-header-center" style="margin-bottom:30px">
            <h2>Frequently Asked Questions for Parents</h2>
            <p>Important guidelines regarding textbook purchases, stationery specifications, and e-learning resources.</p>
        </div>

        <div class="bkl-faq-grid">
            <div class="bkl-faq-card">
                <h4><span>Q1.</span> Is it mandatory to purchase books from a specific shop?</h4>
                <p><strong>No.</strong> Parents are completely free to purchase prescribed textbooks, notebooks, and stationery from any vendor, bookstore, or online platform of their choice.</p>
            </div>
            <div class="bkl-faq-card">
                <h4><span>Q2.</span> Are digital NCERT textbooks acceptable?</h4>
                <p><strong>Yes.</strong> NCERT e-books are freely accessible in digital format on the official NCERT portal and Government DIKSHA application for reference and homework.</p>
            </div>
            <div class="bkl-faq-card">
                <h4><span>Q3.</span> What are the specifications for notebooks?</h4>
                <p>Standard four-line notebooks for English, single-line for Hindi/EVS/Social Science, and square-grid notebooks for Mathematics with standard protective covers are recommended.</p>
            </div>
            <div class="bkl-faq-card">
                <h4><span>Q4.</span> When are new book lists updated each year?</h4>
                <p>Official book lists for the upcoming academic session are finalized by the Academic Committee and published on the school website every year in February/March.</p>
            </div>
        </div>

        {{-- 📞 ACADEMIC ASSISTANCE CTA --}}
        <div class="bkl-cta-box">
            <h3>Need Assistance with Textbooks or Curriculum?</h3>
            <p>Our Academic Coordination Cell is here to help parents with syllabus queries, book availability, and curriculum clarifications.</p>
            <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap">
                <a href="tel:+919350748851" class="bkl-btn-primary" style="background:#0f172a">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Call Academic Office: +91 93507 48851
                </a>
                <a href="https://wa.me/919350748851?text=Hello%20Prayaag%20School%20Academic%20Cell,%20I%20have%20a%20query%20regarding%20the%20prescribed%20book%20list." target="_blank" class="bkl-btn-primary" style="background:#16a34a">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>
</div>

<script>
function filterWing(wing, btn) {
    document.querySelectorAll('.bkl-tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const cards = document.querySelectorAll('.bkl-card');
    cards.forEach(card => {
        if (wing === 'all' || card.dataset.wing === wing) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function searchBooks() {
    const term = document.getElementById('bklSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.bkl-card');

    cards.forEach(card => {
        const text = (card.dataset.title || '') + ' ' + card.innerText.toLowerCase();
        if (term === '' || text.includes(term)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
