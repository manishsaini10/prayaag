{{--
    Mandatory Public Disclosure Page Widget — Ultra-Premium Regulatory & CBSE Appendix IX Portal
    Designed with School Design System Tokens (Navy, Gold, Playfair/Poppins)
--}}

<style>
/* ================================================================
   MANDATORY PUBLIC DISCLOSURE — SCOPED FULL-WIDTH CSS
   ================================================================ */

.dsc-wrapper {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow-x: hidden;
    background: #f8fafc;
}

.pb-section:has(.dsc-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.dsc-wrapper) .pb-row,
.pb-section:has(.dsc-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ── Hero ────────────────────────────────────────────────────────── */
.dsc-hero {
    position: relative;
    min-height: 460px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.dsc-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Laibrary-prayaag-International-school.webp');
    background-size: cover;
    background-position: center;
    opacity: .28;
    transform: scale(1.03);
    transition: transform 6s ease-out;
}

.dsc-hero:hover .dsc-hero__bg {
    transform: scale(1.08);
}

.dsc-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.95) 0%, rgba(11,37,69,0.78) 60%, rgba(197,143,39,0.25) 100%);
}

.dsc-hero__content {
    position: relative;
    z-index: 2;
    padding: 100px 4vw 70px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.dsc-eyebrow {
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

.dsc-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 18px;
}

.dsc-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.dsc-hero__sub {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.88);
    max-width: 780px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.dsc-hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.dsc-btn-primary {
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

.dsc-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

.dsc-btn-secondary {
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

.dsc-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ── Statutory Summary Bar ───────────────────────────────────────── */
.dsc-stats-bar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px -4px rgba(0,0,0,0.06);
    position: relative;
    z-index: 10;
    width: 100%;
}

.dsc-stats-grid {
    max-width: 100%;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    padding: 0 4vw;
}

.dsc-stat-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 20px;
    border-right: 1px solid #f1f5f9;
}

.dsc-stat-item:last-child {
    border-right: none;
}

.dsc-stat-icon {
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

.dsc-stat-info h4 {
    margin: 0 0 2px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}

.dsc-stat-info p {
    margin: 0;
    font-size: 0.84rem;
    color: #64748b;
    font-weight: 500;
}

/* ── Content Sections ────────────────────────────────────────────── */
.dsc-section {
    padding: 60px 4vw 80px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.dsc-header-box {
    margin-bottom: 32px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 16px;
}

.dsc-header-box h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.8rem, 3vw, 2.3rem);
    color: #0f172a;
    margin: 0 0 6px;
}

.dsc-header-box p {
    font-size: 0.95rem;
    color: #64748b;
    margin: 0;
}

.dsc-section-tag {
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

/* ── Key Cards Grid ──────────────────────────────────────────────── */
.dsc-key-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 22px;
    margin-bottom: 60px;
}

.dsc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 8px 30px -6px rgba(0,0,0,0.06);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.dsc-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px -10px rgba(11,37,69,0.12);
    border-color: #cbd5e1;
}

.dsc-card__top {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 18px;
}

.dsc-doc-badge {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: #fee2e2;
    color: #dc2626;
    display: grid;
    place-items: center;
    font-size: 14px;
    font-weight: 800;
    flex-shrink: 0;
}

.dsc-doc-badge.gold {
    background: #fef3c7;
    color: #d97706;
}

.dsc-card__title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    margin: 0 0 4px;
}

.dsc-card__sub {
    font-size: 0.84rem;
    color: #64748b;
}

.dsc-card__action {
    border-top: 1px solid #f1f5f9;
    padding-top: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dsc-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #0b2545;
    color: #ffffff !important;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.dsc-action-btn:hover {
    background: var(--gold, #d97706);
    transform: translateY(-2px);
}

/* ── All Disclosures Table & Filter ──────────────────────────────── */
.dsc-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 30px;
    box-shadow: 0 6px 24px -6px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}

.dsc-search-box {
    position: relative;
    min-width: 280px;
    flex: 1;
    max-width: 400px;
}

.dsc-search-box input {
    width: 100%;
    padding: 11px 16px 11px 42px;
    border-radius: 999px;
    border: 1px solid #cbd5e1;
    font-size: 0.9rem;
    outline: none;
    transition: all 0.2s ease;
}

.dsc-search-box input:focus {
    border-color: var(--gold, #d97706);
    box-shadow: 0 0 0 3px rgba(217,119,6,0.12);
}

.dsc-search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

.dsc-dossiers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 18px;
    margin-bottom: 60px;
}

.dsc-dossier-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    text-decoration: none;
    color: #0f172a;
    transition: all 0.25s ease;
}

.dsc-dossier-item:hover {
    border-color: var(--gold, #d97706);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px -4px rgba(0,0,0,0.08);
}

.dsc-dossier-info {
    display: flex;
    align-items: center;
    gap: 14px;
}

.dsc-dossier-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #475569;
    display: grid;
    place-items: center;
    font-size: 18px;
    flex-shrink: 0;
}

.dsc-dossier-item:hover .dsc-dossier-icon {
    background: #fee2e2;
    color: #dc2626;
}

.dsc-dossier-text h4 {
    margin: 0 0 2px;
    font-size: 0.98rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
}

.dsc-dossier-text p {
    margin: 0;
    font-size: 0.78rem;
    color: #64748b;
}

.dsc-download-arrow {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    display: grid;
    place-items: center;
    color: #0f172a;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

.dsc-dossier-item:hover .dsc-download-arrow {
    background: #0b2545;
    color: #ffffff;
    border-color: #0b2545;
}

/* ── CBSE Compliance Formal Declaration Banner ───────────────────── */
.dsc-compliance-banner {
    background: linear-gradient(135deg, #0b2545, #1e3a8a);
    border-radius: 20px;
    padding: 40px;
    color: #ffffff;
    box-shadow: 0 20px 40px -10px rgba(11,37,69,0.35);
    margin-bottom: 60px;
}

.dsc-compliance-grid {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 24px;
    align-items: flex-start;
}

@media(max-width: 640px) {
    .dsc-compliance-grid {
        grid-template-columns: 1fr;
    }
}

.dsc-compliance-icon {
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

.dsc-compliance-content h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.6rem;
    margin: 0 0 10px;
    color: var(--gold-2, #f59e0b);
}

.dsc-compliance-content p {
    font-size: 0.95rem;
    line-height: 1.7;
    color: rgba(255,255,255,0.85);
    margin: 0 0 18px;
}

.dsc-compliance-points {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 12px;
}

.dsc-c-point {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.88rem;
    font-weight: 500;
    color: rgba(255,255,255,0.92);
}

.dsc-c-point svg {
    color: #4ade80;
    flex-shrink: 0;
}
</style>

<div class="dsc-wrapper">
    {{-- 🌟 HERO BANNER --}}
    <section class="dsc-hero">
        <div class="dsc-hero__bg"></div>
        <div class="dsc-hero__overlay"></div>
        <div class="dsc-hero__content">
            <div class="dsc-eyebrow">
                <span>⚖️</span> CBSE Affiliation &amp; Regulatory Directives
            </div>
            <h1 class="dsc-hero__title">
                Mandatory Public <span>Disclosure</span>
            </h1>
            <p class="dsc-hero__sub">
                In strict adherence to the Central Board of Secondary Education (CBSE) Appendix IX mandate, Prayaag International School publicly discloses all statutory affiliation, infrastructure safety, financial, and governance certificates.
            </p>
            <div class="dsc-hero__actions">
                <a href="#key-documents" class="dsc-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    View Key Documents
                </a>
                <a href="#all-disclosures" class="dsc-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Browse All 18 Dossiers
                </a>
            </div>
        </div>
    </section>

    {{-- 📊 STATUTORY SUMMARY BAR --}}
    <div class="dsc-stats-bar">
        <div class="dsc-stats-grid">
            <div class="dsc-stat-item">
                <div class="dsc-stat-icon">🎓</div>
                <div class="dsc-stat-info">
                    <h4>Affiliation No. 531592</h4>
                    <p>CBSE New Delhi</p>
                </div>
            </div>
            <div class="dsc-stat-item">
                <div class="dsc-stat-icon">🏫</div>
                <div class="dsc-stat-info">
                    <h4>School Code: 41568</h4>
                    <p>Co-Ed Senior Secondary</p>
                </div>
            </div>
            <div class="dsc-stat-item">
                <div class="dsc-stat-icon">🛡️</div>
                <div class="dsc-stat-info">
                    <h4>Appendix IX Compliant</h4>
                    <p>Full Statutory Transparency</p>
                </div>
            </div>
            <div class="dsc-stat-item">
                <div class="dsc-stat-icon">📥</div>
                <div class="dsc-stat-info">
                    <h4>Local PDF Storage</h4>
                    <p>Instant Direct Downloads</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 🌟 SECTION 1: KEY DOCUMENTS (CURRENT 2024–2027) --}}
    <section class="dsc-section" id="key-documents">
        <div class="dsc-header-box">
            <div>
                <h2>Section 1: Key Documents &amp; Current Compliance</h2>
                <p>Essential statutory disclosure documents, fee structures, and current building/fire safety certificates.</p>
            </div>
            <span class="dsc-section-tag">Primary Certificates</span>
        </div>

        <div class="dsc-key-grid">
            {{-- 1. Fee Structure --}}
            <div class="dsc-card">
                <div>
                    <div class="dsc-card__top">
                        <div class="dsc-doc-badge">PDF</div>
                        <div>
                            <h3 class="dsc-card__title">Fee Structure (2026–27)</h3>
                            <div class="dsc-card__sub">Official Annual School Fee Schedule</div>
                        </div>
                    </div>
                </div>
                <div class="dsc-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Current Session</span>
                    <a href="/docs/Fee_Structure_2026-27.pdf" target="_blank" class="dsc-action-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 2. Transport Fee --}}
            <div class="dsc-card">
                <div>
                    <div class="dsc-card__top">
                        <div class="dsc-doc-badge">PDF</div>
                        <div>
                            <h3 class="dsc-card__title">Transport Fee Structure (2026–27)</h3>
                            <div class="dsc-card__sub">Route-Wise Transportation Charges</div>
                        </div>
                    </div>
                </div>
                <div class="dsc-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">Current Session</span>
                    <a href="/docs/Transport_Fee_Structure-2026-27.pdf" target="_blank" class="dsc-action-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 3. Mandatory Public Disclosure --}}
            <div class="dsc-card">
                <div>
                    <div class="dsc-card__top">
                        <div class="dsc-doc-badge gold">PDF</div>
                        <div>
                            <h3 class="dsc-card__title">Mandatory Public Disclosure</h3>
                            <div class="dsc-card__sub">Complete CBSE Appendix IX Disclosure</div>
                        </div>
                    </div>
                </div>
                <div class="dsc-card__action">
                    <span style="font-size:12px;color:#64748b;font-weight:600">CBSE Mandate</span>
                    <a href="/docs/Mandatory-Public-Disclosure.pdf" target="_blank" class="dsc-action-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 4. Building Safety Certificate --}}
            <div class="dsc-card">
                <div>
                    <div class="dsc-card__top">
                        <div class="dsc-doc-badge">PDF</div>
                        <div>
                            <h3 class="dsc-card__title">Building Safety Certificate (BSC)</h3>
                            <div class="dsc-card__sub">PWD Structural Safety Certificate</div>
                        </div>
                    </div>
                </div>
                <div class="dsc-card__action">
                    <span style="font-size:12px;color:#16a34a;font-weight:700">✓ Validated 2024</span>
                    <a href="/docs/BSC.pdf" target="_blank" class="dsc-action-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 5. Transport Safety Certificate --}}
            <div class="dsc-card">
                <div>
                    <div class="dsc-card__top">
                        <div class="dsc-doc-badge">PDF</div>
                        <div>
                            <h3 class="dsc-card__title">Transport Safety Certificate (TSC)</h3>
                            <div class="dsc-card__sub">School Bus Fleet Fitness &amp; Safety</div>
                        </div>
                    </div>
                </div>
                <div class="dsc-card__action">
                    <span style="font-size:12px;color:#16a34a;font-weight:700">✓ Validated 2024</span>
                    <a href="/docs/TSC.pdf" target="_blank" class="dsc-action-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>

            {{-- 6. Fire Safety Certificate --}}
            <div class="dsc-card">
                <div>
                    <div class="dsc-card__top">
                        <div class="dsc-doc-badge">PDF</div>
                        <div>
                            <h3 class="dsc-card__title">Fire Safety Certificate (FSC)</h3>
                            <div class="dsc-card__sub">Haryana Fire &amp; Emergency Services NOC</div>
                        </div>
                    </div>
                </div>
                <div class="dsc-card__action">
                    <span style="font-size:12px;color:#16a34a;font-weight:700">✓ Validated 2024</span>
                    <a href="/docs/FSC.pdf" target="_blank" class="dsc-action-btn">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- 📂 SECTION 2: ALL DISCLOSURES (18 CERTIFICATES & DOSSIERS) --}}
        <div class="dsc-header-box" id="all-disclosures" style="margin-top:20px">
            <div>
                <h2>Section 2: All Disclosures &amp; Statutory Archives</h2>
                <p>Complete official archive of affiliation decrees, recognition certificates, trust deeds, and hygiene reports.</p>
            </div>
            <span class="dsc-section-tag" style="background:#fef3c7;color:#b45309;border-color:#fde68a">18 Statutory Documents</span>
        </div>

        {{-- Search filter --}}
        <div class="dsc-filter-bar">
            <div style="font-size:0.92rem;font-weight:700;color:#0f172a">
                📁 Document Archive List
            </div>
            <div class="dsc-search-box">
                <svg class="dsc-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="dscSearchInput" placeholder="Search disclosure certificate..." onkeyup="searchDossiers()">
            </div>
        </div>

        <div class="dsc-dossiers-grid" id="dossiersGrid">
            {{-- 1. Affiliation Certificate --}}
            <a href="/docs/Afflitation-Certificate.pdf" target="_blank" class="dsc-dossier-item" data-title="affiliation certificate cbse decree">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">📜</div>
                    <div class="dsc-dossier-text">
                        <h4>Affiliation Certificate</h4>
                        <p>CBSE Official Affiliation Decree</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 2. Drinking Water & Sanitary --}}
            <a href="/docs/Drinking-Water-And-Sanitary-Certificate-2022-23.pdf" target="_blank" class="dsc-dossier-item" data-title="drinking water and sanitary certificate hygiene health">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">💧</div>
                    <div class="dsc-dossier-text">
                        <h4>Drinking Water &amp; Sanitary Certificate</h4>
                        <p>Public Health &amp; Hygiene Inspection</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 3. CBSE Mandatory Disclosure --}}
            <a href="/docs/CBSE-MANDATORY-DISCLOSURE.pdf" target="_blank" class="dsc-dossier-item" data-title="cbse mandatory disclosure general details">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🏛️</div>
                    <div class="dsc-dossier-text">
                        <h4>CBSE Mandatory Disclosure</h4>
                        <p>General Information &amp; Documents</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 4. Fire Safety 22-23 --}}
            <a href="/docs/Fire-safety-22-23.pdf" target="_blank" class="dsc-dossier-item" data-title="fire safety 22-23 certificate noc">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🚒</div>
                    <div class="dsc-dossier-text">
                        <h4>Fire Safety (2022–23 Archive)</h4>
                        <p>Fire Safety Compliance NOC</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 5. NOC by DSE --}}
            <a href="/docs/NOC-BY-DSE.pdf" target="_blank" class="dsc-dossier-item" data-title="noc by dse directorate school education">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">📑</div>
                    <div class="dsc-dossier-text">
                        <h4>NOC by DSE</h4>
                        <p>Directorate of School Education Haryana</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 6. Recognition Certificate --}}
            <a href="/docs/RecognitionCertificate.pdf" target="_blank" class="dsc-dossier-item" data-title="recognition certificate government approval">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🎓</div>
                    <div class="dsc-dossier-text">
                        <h4>Recognition Certificate</h4>
                        <p>Haryana State Education Dept. Approval</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 7. Building Safety Certificate (Archival) --}}
            <a href="/docs/Building-Safety-certificate.pdf" target="_blank" class="dsc-dossier-item" data-title="building safety certificate archival structure">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🏢</div>
                    <div class="dsc-dossier-text">
                        <h4>Building Safety Certificate (Archive)</h4>
                        <p>Structural Stability Certificate Archive</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 8. Activity Calendar 2022-23 --}}
            <a href="/docs/Activity-Calander-2022-23.pdf" target="_blank" class="dsc-dossier-item" data-title="activity calendar 2022-23 events schedule">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">📅</div>
                    <div class="dsc-dossier-text">
                        <h4>Activity Calendar (2022–23)</h4>
                        <p>Academic &amp; Co-Curricular Schedule</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 9. Trust Deed --}}
            <a href="/docs/Trust-Deed.pdf" target="_blank" class="dsc-dossier-item" data-title="trust deed society registration constitution">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">⚖️</div>
                    <div class="dsc-dossier-text">
                        <h4>Trust Deed</h4>
                        <p>Educational Trust Constitution</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 10. Certificate By DEO for Affiliation --}}
            <a href="/docs/Certificate-By-DEO-for-affliation.pdf" target="_blank" class="dsc-dossier-item" data-title="certificate by deo for affiliation district education office">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🏅</div>
                    <div class="dsc-dossier-text">
                        <h4>Certificate by DEO for Affiliation</h4>
                        <p>District Education Officer Certificate</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 11. Food Menu --}}
            <a href="/docs/FOOD-MENU-FROM-9TH-MAY-TO-22-MAY-2022.pdf" target="_blank" class="dsc-dossier-item" data-title="food menu mess dining nutrition">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🍱</div>
                    <div class="dsc-dossier-text">
                        <h4>Food &amp; Mess Menu</h4>
                        <p>School Dining Nutrition Chart</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 12. School Management Committee --}}
            <a href="/docs/School-Management-Committee.pdf" target="_blank" class="dsc-dossier-item" data-title="school management committee smc members governance">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">👥</div>
                    <div class="dsc-dossier-text">
                        <h4>School Management Committee (SMC)</h4>
                        <p>Governing Body &amp; Member List</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 13. School Details --}}
            <a href="/docs/School-Detail.pdf" target="_blank" class="dsc-dossier-item" data-title="school details campus land infrastructure">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">📐</div>
                    <div class="dsc-dossier-text">
                        <h4>School Details &amp; Infrastructure</h4>
                        <p>Land Area, Facilities &amp; Classrooms</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 14. Activities & Academic Calendar --}}
            <a href="/docs/Activity-Academic-Calendar.pdf" target="_blank" class="dsc-dossier-item" data-title="activities and academic calendar term schedule">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🗓️</div>
                    <div class="dsc-dossier-text">
                        <h4>Activities &amp; Academic Calendar</h4>
                        <p>Annual Academic Curriculum Overview</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 15. General Information --}}
            <a href="/docs/GENERAL-INFORMATION.pdf" target="_blank" class="dsc-dossier-item" data-title="general information school profile">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">ℹ️</div>
                    <div class="dsc-dossier-text">
                        <h4>General Information</h4>
                        <p>Official Institutional Profile</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 16. Transport Safety Certificate (Archival) --}}
            <a href="/docs/Transport-Safety-Certificate.pdf" target="_blank" class="dsc-dossier-item" data-title="transport safety certificate archival bus fleet">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🚌</div>
                    <div class="dsc-dossier-text">
                        <h4>Transport Safety Certificate (Archive)</h4>
                        <p>Historical Vehicle Fitness Archive</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 17. Non Property Affidavit --}}
            <a href="/docs/Non-Proprietry-affidavit.pdf" target="_blank" class="dsc-dossier-item" data-title="non property affidavit non proprietary character">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">⚖️</div>
                    <div class="dsc-dossier-text">
                        <h4>Non-Proprietary Character Affidavit</h4>
                        <p>Notarized Legal Affidavit</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>

            {{-- 18. Hygienic Certificate --}}
            <a href="/docs/HygenicCertificate.pdf" target="_blank" class="dsc-dossier-item" data-title="hygienic certificate health clean campus sanitation">
                <div class="dsc-dossier-info">
                    <div class="dsc-dossier-icon">🧼</div>
                    <div class="dsc-dossier-text">
                        <h4>Hygienic Certificate</h4>
                        <p>Campus Sanitation &amp; Health Clearance</p>
                    </div>
                </div>
                <div class="dsc-download-arrow">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </div>
            </a>
        </div>

        {{-- 🏛️ FORMAL COMPLIANCE DECLARATION --}}
        <div class="dsc-compliance-banner">
            <div class="dsc-compliance-grid">
                <div class="dsc-compliance-icon">⚖️</div>
                <div class="dsc-compliance-content">
                    <h3>CBSE Appendix IX Statutory Declaration</h3>
                    <p>
                        Prayaag International School, Panipat operates in complete alignment with Central Board of Secondary Education (CBSE) Affiliation Bye-Laws. All safety certificates, fee structures, curriculum details, and society constitutions provided above are verified periodically by the District Administration and Education authorities.
                    </p>
                    <div class="dsc-compliance-points">
                        <div class="dsc-c-point">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>100% Certified Structural Stability</span>
                        </div>
                        <div class="dsc-c-point">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Active Fire Safety NOC Verified</span>
                        </div>
                        <div class="dsc-c-point">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Clean Drinking Water &amp; Sanitation Clearance</span>
                        </div>
                        <div class="dsc-c-point">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>Safe School Bus Transportation Fleet</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📞 RTI & COMPLIANCE DESK CTA --}}
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:20px;padding:36px;text-align:center;box-shadow:0 8px 24px -6px rgba(0,0,0,0.05)">
            <h3 style="font-family:var(--font-head,'Playfair Display',serif);font-size:1.65rem;color:#0f172a;margin:0 0 8px">
                Statutory Compliance &amp; Public Records Office
            </h3>
            <p style="font-size:0.95rem;color:#64748b;max-width:580px;margin:0 auto 20px">
                For physical document verifications or Right to Information (RTI) records, please contact the School Administrative Office.
            </p>
            <div style="display:flex;justify-content:center;gap:14px;flex-wrap:wrap">
                <a href="tel:+919350748851" class="dsc-action-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Call Compliance Desk: +91 93507 48851
                </a>
                <a href="mailto:mailus@pisp.in" class="dsc-action-btn" style="background:#f1f5f9;color:#0f172a !important;border:1px solid #cbd5e1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    Email: mailus@pisp.in
                </a>
            </div>
        </div>
    </section>
</div>

<script>
function searchDossiers() {
    const term = document.getElementById('dscSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.dsc-dossier-item');

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
