{{--
    Downloads Page Widget — Full-Page Academic & School Resource Center
    Designed with School Design System Tokens (Navy, Gold, Playfair/Poppins)
--}}

<style>
/* ================================================================
   DOWNLOADS PAGE — SCOPED ULTRA-PREMIUM FULL-WIDTH CSS
   ================================================================ */

.dwn-wrapper {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow-x: hidden;
    background: #f8fafc;
}

.pb-section:has(.dwn-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.dwn-wrapper) .pb-row,
.pb-section:has(.dwn-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ── Hero ────────────────────────────────────────────────────────── */
.dwn-hero {
    position: relative;
    min-height: 440px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.dwn-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp');
    background-size: cover;
    background-position: center;
    opacity: .28;
    transform: scale(1.03);
    transition: transform 6s ease-out;
}

.dwn-hero:hover .dwn-hero__bg {
    transform: scale(1.08);
}

.dwn-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.95) 0%, rgba(11,37,69,0.75) 60%, rgba(197,143,39,0.2) 100%);
}

.dwn-hero__content {
    position: relative;
    z-index: 2;
    padding: 100px 4vw 70px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.dwn-eyebrow {
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

.dwn-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 18px;
}

.dwn-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.dwn-hero__sub {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.85);
    max-width: 780px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.dwn-hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.dwn-btn-primary {
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

.dwn-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

/* ── Stats Bar ───────────────────────────────────────────────────── */
.dwn-stats-bar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px -4px rgba(0,0,0,0.06);
    position: relative;
    z-index: 10;
    width: 100%;
}

.dwn-stats-grid {
    max-width: 100%;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    padding: 0 4vw;
}

.dwn-stat-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 20px;
    border-right: 1px solid #f1f5f9;
}

.dwn-stat-item:last-child {
    border-right: none;
}

.dwn-stat-icon {
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

.dwn-stat-info h4 {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}

.dwn-stat-info p {
    margin: 2px 0 0;
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

/* ── Main Catalog Section ────────────────────────────────────────── */
.dwn-section {
    padding: 60px 4vw 80px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.dwn-header-center {
    text-align: center;
    max-width: 780px;
    margin: 0 auto 40px;
}

.dwn-header-center h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2rem, 3.5vw, 2.7rem);
    color: #0f172a;
    margin: 0 0 12px;
}

.dwn-header-center p {
    font-size: 1rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

/* ── Filter & Search Bar ─────────────────────────────────────────── */
.dwn-filter-wrapper {
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

.dwn-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.dwn-tab-btn {
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

.dwn-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.dwn-tab-btn.active {
    background: var(--navy, #0b2545);
    color: #ffffff;
    border-color: var(--navy, #0b2545);
    box-shadow: 0 3px 10px rgba(11,37,69,0.25);
}

.dwn-search-box {
    position: relative;
    min-width: 260px;
    flex: 1;
    max-width: 340px;
}

.dwn-search-box input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border-radius: 999px;
    border: 1px solid #cbd5e1;
    font-size: 0.88rem;
    outline: none;
    transition: border-color 0.2s ease;
}

.dwn-search-box input:focus {
    border-color: var(--gold, #d97706);
    box-shadow: 0 0 0 3px rgba(217,119,6,0.12);
}

.dwn-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

/* ── Downloads Cards Grid ────────────────────────────────────────── */
.dwn-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 60px;
}

.dwn-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 6px 20px -4px rgba(0,0,0,0.05);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.dwn-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 32px -8px rgba(11,37,69,0.12);
    border-color: #cbd5e1;
}

.dwn-card__top {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
}

.dwn-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #fee2e2;
    color: #dc2626;
    display: grid;
    place-items: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
}

.dwn-card__icon.docx {
    background: #e0f2fe;
    color: #0284c7;
}

.dwn-card__icon.general {
    background: #fef3c7;
    color: #d97706;
}

.dwn-card__meta {
    flex: 1;
}

.dwn-category-badge {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 3px 10px;
    border-radius: 999px;
    display: inline-block;
    margin-bottom: 6px;
}

.cat-pt1 { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
.cat-hhw { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
.cat-menu { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.cat-disclosure { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }

.dwn-card__title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    margin: 0;
}

.dwn-card__sub {
    font-size: 0.82rem;
    color: #64748b;
    margin-top: 4px;
}

.dwn-card__action {
    border-top: 1px solid #f1f5f9;
    padding-top: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dwn-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #0f172a;
    color: #ffffff !important;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.dwn-action-btn:hover {
    background: var(--gold, #d97706);
}

/* ── Help / Contact CTA Box ──────────────────────────────────────── */
.dwn-cta-box {
    background: linear-gradient(135deg, #0b2545, #1e3a8a);
    border-radius: 20px;
    padding: 40px;
    color: #ffffff;
    text-align: center;
    box-shadow: 0 20px 40px -10px rgba(11,37,69,0.35);
}

.dwn-cta-box h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.85rem;
    color: var(--gold-2, #f59e0b);
    margin: 0 0 10px;
}

.dwn-cta-box p {
    font-size: 1rem;
    color: rgba(255,255,255,0.85);
    max-width: 560px;
    margin: 0 auto 24px;
}
</style>

<div class="dwn-wrapper">
    {{-- 🌟 HERO BANNER --}}
    <section class="dwn-hero">
        <div class="dwn-hero__bg"></div>
        <div class="dwn-hero__overlay"></div>
        <div class="dwn-hero__content">
            <div class="dwn-eyebrow">
                <span>📥</span> Academic Resource Center
            </div>
            <h1 class="dwn-hero__title">
                Academic &amp; Student <span>Downloads</span>
            </h1>
            <p class="dwn-hero__sub">
                Access official curriculum syllabus, Periodic Test schedules, Holiday Homework dossiers, mess menus, and mandatory school disclosure certificates.
            </p>
            <div class="dwn-hero__actions">
                <a href="#downloads-catalog" class="dwn-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Browse All Documents
                </a>
            </div>
        </div>
    </section>

    {{-- 📊 STATS & SUMMARY BAR --}}
    <div class="dwn-stats-bar">
        <div class="dwn-stats-grid">
            <div class="dwn-stat-item">
                <div class="dwn-stat-icon">📄</div>
                <div class="dwn-stat-info">
                    <h4>PT-1 Syllabus</h4>
                    <p>All Classes Pre-Nur to XII</p>
                </div>
            </div>
            <div class="dwn-stat-item">
                <div class="dwn-stat-icon">🏖️</div>
                <div class="dwn-stat-info">
                    <h4>Holiday Homework</h4>
                    <p>Compiled Class Assignments</p>
                </div>
            </div>
            <div class="dwn-stat-item">
                <div class="dwn-stat-icon">🍱</div>
                <div class="dwn-stat-info">
                    <h4>Mess &amp; Exam Menus</h4>
                    <p>Nutritional &amp; Date Sheets</p>
                </div>
            </div>
            <div class="dwn-stat-item">
                <div class="dwn-stat-icon">🏛️</div>
                <div class="dwn-stat-info">
                    <h4>Mandatory Disclosures</h4>
                    <p>Safety &amp; Affiliation Docs</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 📚 MAIN DOWNLOADS CATALOG --}}
    <section class="dwn-section" id="downloads-catalog">
        <div class="dwn-header-center">
            <h2>Official School Document Center</h2>
            <p>Select a category or use the instant search bar to find and download syllabus sheets, worksheets, and circulars.</p>
        </div>

        {{-- Filter & Search Bar --}}
        <div class="dwn-filter-wrapper">
            <div class="dwn-tabs">
                <button type="button" class="dwn-tab-btn active" onclick="filterCategory('all', this)">All Documents</button>
                <button type="button" class="dwn-tab-btn" onclick="filterCategory('pt1', this)">📝 PT-1 Syllabus</button>
                <button type="button" class="dwn-tab-btn" onclick="filterCategory('hhw', this)">🏖️ Holiday Homework</button>
                <button type="button" class="dwn-tab-btn" onclick="filterCategory('menu', this)">🍱 Menus &amp; Schedules</button>
                <button type="button" class="dwn-tab-btn" onclick="filterCategory('disclosure', this)">🏛️ Mandatory Public Disclosure</button>
            </div>
            <div class="dwn-search-box">
                <svg class="dwn-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="dwnSearchInput" placeholder="Search download by title..." onkeyup="searchDownloads()">
            </div>
        </div>

        {{-- Grid of All Download Items --}}
        <div class="dwn-grid" id="downloadsGrid">

            {{-- 1. PT-1 Syllabus Items --}}
            <div class="dwn-card" data-cat="pt1" data-title="pt1 xii commerce syllabus compiled class 12">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">PT1 XII Commerce Syllabus Compiled</h3>
                            <div class="dwn-card__sub">Class XII Commerce · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/PT1-XII-COMMERCE-SYLLABUS-COMPILED.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="pt 1 syllabus xii science class 12">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">PT-1 Syllabus XII (Science)</h3>
                            <div class="dwn-card__sub">Class XII Science · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/PT-1-SYLLABUS-XII-SCIENCE.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="pt1 xii humanities syllabus compiled class 12">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">PT1 XII Humanities Syllabus Compiled</h3>
                            <div class="dwn-card__sub">Class XII Arts / Humanities · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/PT1-XII-HUMANITIES-SYLLABUS-COMPILED.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="10th pt 1 syllabus class 10 grade 10">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">10th PT-1 Syllabus</h3>
                            <div class="dwn-card__sub">Class X Board · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/10TH-PT-1-SYLLABUS.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="9th pt 1 syllabus class 9 grade 9">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon docx">DOC</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">9th PT-1 Syllabus</h3>
                            <div class="dwn-card__sub">Class IX · Word Document / Syllabus</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/9TH-PT-1-SYLLABUS.docx" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download DOCX
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="grade 8 pt1 syllabus class 8">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">Grade 8 PT-1 Syllabus</h3>
                            <div class="dwn-card__sub">Class VIII Middle Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/grade-8-pt1-syllabus-22-23.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="cl7 pt1 syllabus class 7 grade 7">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">Class 7 PT-1 Syllabus</h3>
                            <div class="dwn-card__sub">Class VII Middle Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/cl7-pt1-syllabus.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="grade 5 pt 1 syllabus class 5">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">Grade 5 PT-1 Syllabus</h3>
                            <div class="dwn-card__sub">Class V Primary Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/Grade-5-pt-1-syllabus.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="pt 1 grade 4 syllabus class 4">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">PT-1 Grade 4 Syllabus</h3>
                            <div class="dwn-card__sub">Class IV Primary Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/PT-1-Grade-4-SYLLABUS.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="pt 1 grade 3 syllabus class 3">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">PT-1 Grade 3 Syllabus</h3>
                            <div class="dwn-card__sub">Class III Primary Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/PT-1-Grade-3-SYLLABUS.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="pt 1 grade 2 syllabus class 2">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">PT-1 Grade 2 Syllabus</h3>
                            <div class="dwn-card__sub">Class II Primary Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/PT-1-Grade-2-SYLLABUS.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="pt1" data-title="grade 1 pt1 syllabus class 1">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-pt1">PT-1 Syllabus</span>
                            <h3 class="dwn-card__title">Grade 1 PT1 Syllabus</h3>
                            <div class="dwn-card__sub">Class I Primary Wing · Official PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Official Document</span>
                    <a href="/docs/Grade-1-PT1-Syllabus-2022-23.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 2. Holiday Homework Items --}}
            <div class="dwn-card" data-cat="hhw" data-title="grade 12 science hhw holiday homework class 12">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 12 Science HHW</h3>
                            <div class="dwn-card__sub">Class XII Science · Compiled Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/GRADE-12-SCIENCE-HHW-COMPILED.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 12 humanities hhw holiday homework class 12">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 12 Humanities HHW</h3>
                            <div class="dwn-card__sub">Class XII Arts · Compiled Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/GRADE-12-HUMANITIES-HHW-COMPLIED.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 12 commerce hhw holiday homework class 12">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 12 Commerce HHW</h3>
                            <div class="dwn-card__sub">Class XII Commerce · Compiled Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/GRADE-12-COMMERCE-HHW-COMPILED.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="compiled holiday homework grade 10 class 10">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Compiled Holiday Homework Grade 10</h3>
                            <div class="dwn-card__sub">Class X Board · Vacation Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/Compiled-Holiday-Homework-Grade-10.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="compiled holiday homework ix class 9">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Compiled Holiday Homework IX</h3>
                            <div class="dwn-card__sub">Class IX · Vacation Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/Compiled-holiday-homework-IX.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 8 holiday homework class 8">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 8 Holiday Homework</h3>
                            <div class="dwn-card__sub">Class VIII Middle Wing · Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/Grade-8-Holiday-Homework-2022-23.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 7 holiday homework class 7">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 7 Holiday Homework</h3>
                            <div class="dwn-card__sub">Class VII Middle Wing · Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/Grade-7-Holiday-Homework-2022-23.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 6 hhw holiday homework class 6">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 6 HHW</h3>
                            <div class="dwn-card__sub">Class VI Middle Wing · Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/6-hhw-1.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 5 hhw grade v holiday homework class 5">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade V HHW</h3>
                            <div class="dwn-card__sub">Class V Primary Wing · Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/5-hhw-copy-copy.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 4 holiday homework class 4">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 4 Holiday Homework</h3>
                            <div class="dwn-card__sub">Class IV Primary Wing · Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/Grade-4-HOLIDAY-HOMEWORK.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="hhw" data-title="grade 3 holiday homework class 3">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-hhw">Holiday Homework</span>
                            <h3 class="dwn-card__title">Grade 3 Holiday Homework</h3>
                            <div class="dwn-card__sub">Class III Primary Wing · Assignment</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Vacation Dossier</span>
                    <a href="/docs/Grade-3-HOLIDAY-HOMEWORK-Copy.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 3. Menus & Schedules --}}
            <div class="dwn-card" data-cat="menu" data-title="food menu mess menu dining">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon general">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-menu">Mess &amp; Dining</span>
                            <h3 class="dwn-card__title">School Food &amp; Mess Menu</h3>
                            <div class="dwn-card__sub">Official Nutritional Meal Plan · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Dining Chart</span>
                    <a href="/docs/FOOD-MENU-FROM-9TH-MAY-TO-22-MAY-2022.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 4. Mandatory Public Disclosures & Safety Certificates --}}
            <div class="dwn-card" data-cat="disclosure" data-title="fee structure 2026-27 fees tuition admission">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-disclosure">Mandatory Disclosure</span>
                            <h3 class="dwn-card__title">School Fee Structure 2026–27</h3>
                            <div class="dwn-card__sub">Official Annual Fee Schedule · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Affiliation Document</span>
                    <a href="/docs/Fee_Structure_2026-27.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="disclosure" data-title="transport fee structure 2026-27 bus transportation">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-disclosure">Mandatory Disclosure</span>
                            <h3 class="dwn-card__title">Transport Fee Structure 2026–27</h3>
                            <div class="dwn-card__sub">Route-wise Bus Fee Schedule · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Affiliation Document</span>
                    <a href="/docs/Transport_Fee_Structure-2026-27.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="disclosure" data-title="cbse mandatory public disclosure compliance appendix ix">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-disclosure">Mandatory Disclosure</span>
                            <h3 class="dwn-card__title">Mandatory Public Disclosure</h3>
                            <div class="dwn-card__sub">CBSE Appendix IX Complete Compliance · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Affiliation Document</span>
                    <a href="/docs/Mandatory-Public-Disclosure.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="disclosure" data-title="building safety certificate bsc pwd structure">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-disclosure">Mandatory Disclosure</span>
                            <h3 class="dwn-card__title">Building Safety Certificate (BSC)</h3>
                            <div class="dwn-card__sub">PWD Structural Safety Compliance · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Safety Certificate</span>
                    <a href="/docs/BSC.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="disclosure" data-title="transport safety certificate tsc school bus fitness">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-disclosure">Mandatory Disclosure</span>
                            <h3 class="dwn-card__title">Transport Safety Certificate (TSC)</h3>
                            <div class="dwn-card__sub">Regional Transport Authority Fitness · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Safety Certificate</span>
                    <a href="/docs/TSC.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            <div class="dwn-card" data-cat="disclosure" data-title="fire safety certificate fsc noc fire station">
                <div>
                    <div class="dwn-card__top">
                        <div class="dwn-card__icon">PDF</div>
                        <div class="dwn-card__meta">
                            <span class="dwn-category-badge cat-disclosure">Mandatory Disclosure</span>
                            <h3 class="dwn-card__title">Fire Safety Certificate (FSC)</h3>
                            <div class="dwn-card__sub">Haryana Fire &amp; Emergency Services NOC · PDF</div>
                        </div>
                    </div>
                </div>
                <div class="dwn-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Safety Certificate</span>
                    <a href="/docs/FSC.pdf" target="_blank" class="dwn-action-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

        </div>

        {{-- 📞 CONTACT & ASSISTANCE CTA --}}
        <div class="dwn-cta-box">
            <h3>Need Help Finding a Document?</h3>
            <p>Our School Academic &amp; Administrative Office is available to provide any official documentation, past examination papers, or admission brochures.</p>
            <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap">
                <a href="tel:+919350748851" class="dwn-btn-primary" style="background:#ffffff;color:#0b2545 !important">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0b2545" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Call School Office: +91 93507 48851
                </a>
                <a href="https://wa.me/919350748851?text=Hello%20Prayaag%20School,%20I%20need%20assistance%20with%20school%20downloads%20and%20documents." target="_blank" class="dwn-btn-primary" style="background:#16a34a">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>
</div>

<script>
function filterCategory(cat, btn) {
    document.querySelectorAll('.dwn-tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const cards = document.querySelectorAll('.dwn-card');
    cards.forEach(card => {
        if (cat === 'all' || card.dataset.cat === cat) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function searchDownloads() {
    const term = document.getElementById('dwnSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.dwn-card');

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
