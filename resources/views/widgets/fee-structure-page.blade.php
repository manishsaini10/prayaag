{{--
    Fee Structure 2026-27 Page Widget — Luxury Modern, Ultra-Clean & 100% Mobile Responsive
    Features: Grade-Wise Tuition Table, One-Time Charges, Interactive Payment Schedule, PDF Downloads, Policy Notes & Estimator
--}}

@php
    $heroEyebrow      = $settings['hero_eyebrow'] ?? 'Academic Session 2026-27 · Transparent Investment';
    $heroTitle        = $settings['hero_title'] ?? 'Fee Structure 2026-27';
    $heroSub          = $settings['hero_subtitle'] ?? 'Explore the comprehensive fee schedule for the academic year 2026-27 at Prayaag International School, Panipat.';
    $heroBg           = $settings['hero_bg_image'] ?? '/images/classrooms/classroom-main.jpg';

    $regUrl           = $settings['registration_url'] ?? 'https://pisp.accevate.com/registration/';
    $payUrl           = $settings['online_payment_url'] ?? 'https://pisp.accevate.com/online/main';
    $feePdfUrl        = $settings['pdf_fee_url'] ?? '/docs/Fee_Structure_2026-27.pdf';
    $transPdfUrl      = $settings['pdf_transport_url'] ?? '/docs/Transport_Fee_Structure-2026-27.pdf';

    $highlights       = (array) ($settings['highlights'] ?? []);
    $tuitionFees      = (array) ($settings['tuition_fees'] ?? []);
    $otherCharges     = (array) ($settings['other_charges'] ?? []);
    $notes            = (array) ($settings['notes'] ?? []);

    $closingTitle     = $settings['closing_title'] ?? 'Investing in Quality Education';
    $closingText      = $settings['closing_text'] ?? '';

    $adminPhone       = $settings['admin_phone'] ?? '+919350748851';
    $adminEmail       = $settings['admin_email'] ?? 'info@prayaagschool.com';
    $adminHours       = $settings['admin_hours'] ?? 'Mon - Sat : 08:00 AM - 03:30 PM';
@endphp

<style>
/* ================================================================
   FEE STRUCTURE 2026-27 — 100% ULTRA MOBILE RESPONSIVE & LUXURY
   ================================================================ */

.fee-wrapper {
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

.pb-section:has(.fee-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.fee-wrapper) .pb-row,
.pb-section:has(.fee-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ── Hero Banner ─────────────────────────────────────────────────── */
.fee-hero {
    position: relative;
    min-height: 460px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.fee-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('{{ $heroBg }}');
    background-size: cover;
    background-position: center;
    opacity: .28;
    transform: scale(1.02);
}

.fee-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.96) 0%, rgba(11,37,69,0.85) 60%, rgba(217,119,6,0.30) 100%);
}

.fee-hero__content {
    position: relative;
    z-index: 2;
    padding: 85px 5vw 55px;
    max-width: 1280px;
    margin: 0 auto;
    width: 100%;
}

.fee-eyebrow {
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

.fee-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2rem, 4.5vw, 3.4rem);
    font-weight: 800;
    color: #ffffff;
    line-height: 1.22;
    margin: 0 0 16px;
    max-width: 860px;
}

.fee-hero__sub {
    font-size: clamp(0.95rem, 1.8vw, 1.15rem);
    color: rgba(255,255,255,0.88);
    max-width: 820px;
    line-height: 1.7;
    margin: 0 0 28px;
}

.fee-hero__actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.fee-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: linear-gradient(135deg, #d97706, #b45309);
    color: #ffffff !important;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(217,119,6,0.35);
    transition: all 0.3s ease;
}

.fee-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

.fee-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(255,255,255,0.12);
    color: #ffffff !important;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.92rem;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(8px);
    transition: all 0.3s ease;
}

.fee-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

.fee-btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: transparent;
    color: #f59e0b !important;
    padding: 12px 20px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    border: 1px solid rgba(245,158,11,0.5);
    transition: all 0.3s ease;
}

.fee-btn-outline:hover {
    background: rgba(245,158,11,0.12);
    transform: translateY(-2px);
}

/* ── 4 Transparency Highlights ────────────────────────────────────── */
.fee-highlights-sec {
    max-width: 1280px;
    margin: -36px auto 0;
    padding: 0 5vw;
    position: relative;
    z-index: 10;
}

.fee-highlights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.fee-hl-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px 20px;
    box-shadow: 0 10px 30px -5px rgba(11,37,69,0.07);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.fee-hl-card:hover {
    transform: translateY(-4px);
    border-color: #cbd5e1;
    box-shadow: 0 16px 36px -8px rgba(11,37,69,0.12);
}

.fee-hl-icon {
    font-size: 26px;
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: #f1f5f9;
    display: grid;
    place-items: center;
}

.fee-hl-card:nth-child(1) .fee-hl-icon { background: #eff6ff; }
.fee-hl-card:nth-child(2) .fee-hl-icon { background: #fdf4ff; }
.fee-hl-card:nth-child(3) .fee-hl-icon { background: #f0fdf4; }
.fee-hl-card:nth-child(4) .fee-hl-icon { background: #fffbeb; }

.fee-hl-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}

.fee-hl-desc {
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.55;
    margin: 0;
}

/* ── Main Content Section ────────────────────────────────────────── */
.fee-main-sec {
    padding: 70px 5vw;
    max-width: 1280px;
    margin: 0 auto;
}

.fee-sec-header {
    text-align: center;
    margin-bottom: 40px;
}

.fee-sec-header h2 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(1.8rem, 3.2vw, 2.5rem);
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}

.fee-sec-header p {
    font-size: 1rem;
    color: #64748b;
    max-width: 680px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ── Fee Tables Layout ────────────────────────────────────────────── */
.fee-card-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    padding: 32px;
    box-shadow: 0 12px 36px -8px rgba(0,0,0,0.04);
    margin-bottom: 36px;
    overflow: hidden;
}

.fee-box-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f1f5f9;
}

.fee-box-title {
    display: flex;
    align-items: center;
    gap: 10px;
}

.fee-box-title h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.45rem;
    font-weight: 800;
    color: #0b2545;
    margin: 0;
}

/* Payment Cycle Switcher */
.fee-cycle-toggle {
    display: inline-flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.fee-cycle-btn {
    border: none;
    background: transparent;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
}

.fee-cycle-btn.active {
    background: #0b2545;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(11,37,69,0.2);
}

/* Responsive Table */
.fee-table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
}

.fee-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    margin: 0;
    font-size: 0.95rem;
}

.fee-table th {
    background: #0b2545;
    color: #ffffff;
    font-weight: 700;
    padding: 16px 20px;
    font-size: 0.9rem;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}

.fee-table th:nth-child(2),
.fee-table th:nth-child(3) {
    text-align: right;
}

.fee-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
}

.fee-table tr:last-child td {
    border-bottom: none;
}

.fee-table tr:hover td {
    background: #f8fafc;
}

.fee-table td:nth-child(2),
.fee-table td:nth-child(3) {
    text-align: right;
    font-weight: 700;
    font-feature-settings: "tnum";
}

.fee-grade-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 800;
    color: #0b2545;
    font-size: 1rem;
}

.fee-amount-highlight {
    color: #b45309;
    font-size: 1.05rem;
}

.fee-badge-pill {
    font-size: 0.76rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 999px;
    background: #e0f2fe;
    color: #0369a1;
}

/* ── Interactive Fee Calculator ──────────────────────────────────── */
.fee-calc-box {
    background: linear-gradient(135deg, #0b2545 0%, #173760 100%);
    border-radius: 24px;
    padding: 36px 32px;
    color: #ffffff;
    box-shadow: 0 16px 40px -10px rgba(11,37,69,0.3);
    margin-bottom: 36px;
}

.fee-calc-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 32px;
    align-items: center;
}

.fee-calc-left h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0 0 8px;
    color: #ffffff;
}

.fee-calc-left p {
    font-size: 0.92rem;
    color: rgba(255,255,255,0.8);
    margin: 0 0 20px;
    line-height: 1.6;
}

.fee-calc-select {
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.25);
    background: rgba(255,255,255,0.12);
    color: #ffffff;
    font-size: 0.95rem;
    font-weight: 600;
    outline: none;
    cursor: pointer;
}

.fee-calc-select option {
    background: #0b2545;
    color: #ffffff;
}

.fee-calc-results {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 18px;
    padding: 24px;
    backdrop-filter: blur(10px);
}

.fee-calc-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    font-size: 0.9rem;
    color: rgba(255,255,255,0.85);
}

.fee-calc-row:last-child {
    border-bottom: none;
    padding-top: 12px;
    font-size: 1.1rem;
    font-weight: 800;
    color: #f59e0b;
}

/* ── Policy Notes Section ────────────────────────────────────────── */
.fee-notes-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 20px;
    padding: 28px 30px;
    margin-bottom: 36px;
}

.fee-notes-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #92400e;
    margin: 0 0 14px;
}

.fee-notes-list {
    margin: 0;
    padding-left: 22px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.fee-notes-list li {
    font-size: 0.92rem;
    color: #78350f;
    line-height: 1.65;
}

/* ── Closing Quality Education Commitment ────────────────────────── */
.fee-closing-box {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    padding: 36px;
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 32px;
    align-items: center;
    box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
    margin-bottom: 36px;
}

.fee-closing-text h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.5rem;
    font-weight: 800;
    color: #0b2545;
    margin: 0 0 12px;
}

.fee-closing-text p {
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.7;
    margin: 0;
}

.fee-support-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.fee-support-card h4 {
    font-size: 0.95rem;
    font-weight: 800;
    color: #0b2545;
    margin: 0;
}

.fee-contact-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.88rem;
    color: #475569;
}

.fee-contact-item a {
    color: #0284c7;
    font-weight: 700;
    text-decoration: none;
}

/* ── Bottom Downloads & CTAs Bar ─────────────────────────────────── */
.fee-downloads-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 24px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    box-shadow: 0 8px 24px -4px rgba(0,0,0,0.04);
}

.fee-downloads-title {
    font-size: 1rem;
    font-weight: 800;
    color: #0b2545;
}

.fee-downloads-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.fee-pdf-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 10px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #1e293b;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.fee-pdf-btn:hover {
    background: #0b2545;
    color: #ffffff;
    border-color: #0b2545;
}

/* ── Mobile Responsive Breakpoints ───────────────────────────────── */
@media(max-width: 900px) {
    .fee-calc-grid,
    .fee-closing-box {
        grid-template-columns: 1fr;
        gap: 24px;
    }
}

@media(max-width: 640px) {
    .fee-hero {
        min-height: 380px;
    }
    .fee-hero__content {
        padding: 65px 5vw 35px;
    }
    .fee-hero__actions {
        flex-direction: column;
        width: 100%;
    }
    .fee-btn-primary, .fee-btn-secondary, .fee-btn-outline {
        width: 100%;
    }
    .fee-card-box {
        padding: 20px 16px;
    }
    .fee-highlights-sec {
        margin-top: -20px;
    }
    .fee-main-sec {
        padding: 50px 5vw;
    }
    .fee-downloads-bar {
        padding: 20px 16px;
        flex-direction: column;
        align-items: flex-start;
    }
    .fee-downloads-actions {
        width: 100%;
        flex-direction: column;
    }
    .fee-pdf-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="fee-wrapper">
    {{-- 🌟 HERO SHOWCASE BANNER --}}
    <section class="fee-hero">
        <div class="fee-hero__bg"></div>
        <div class="fee-hero__overlay"></div>
        <div class="fee-hero__content">
            @if(!empty($heroEyebrow))
            <div class="fee-eyebrow">
                <span>🏛️</span> {{ $heroEyebrow }}
            </div>
            @endif
            <h1 class="fee-hero__title">
                {{ $heroTitle }}
            </h1>
            <p class="fee-hero__sub">
                {{ $heroSub }}
            </p>
            <div class="fee-hero__actions">
                <a href="{{ $regUrl }}" target="_blank" class="fee-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    Online Registration ↗
                </a>
                <a href="{{ $payUrl }}" target="_blank" class="fee-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Online Payment Portal ↗
                </a>
                <a href="{{ $feePdfUrl }}" target="_blank" class="fee-btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download Fee PDF
                </a>
            </div>
        </div>
    </section>

    {{-- ❄️ 4 TRANSPARENCY HIGHLIGHTS --}}
    @if(count($highlights) > 0)
    <section class="fee-highlights-sec">
        <div class="fee-highlights-grid">
            @foreach($highlights as $hl)
            <div class="fee-hl-card">
                <div class="fee-hl-icon">{{ $hl['icon'] ?? '✨' }}</div>
                <h3 class="fee-hl-title">{{ $hl['title'] ?? '' }}</h3>
                <p class="fee-hl-desc">{{ $hl['desc'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 📊 MAIN CONTENT & TABLES --}}
    <section class="fee-main-sec">
        {{-- SECTION HEADER --}}
        <div class="fee-sec-header">
            <h2>Approved Fee Schedule (Session 2026-27)</h2>
            <p>Transparent fee breakup across classes. All fees are in Indian National Rupees (₹).</p>
        </div>

        {{-- TABLE 1: GRADE-WISE TUITION FEE TABLE --}}
        <div class="fee-card-box">
            <div class="fee-box-top">
                <div class="fee-box-title">
                    <span style="font-size:24px">📚</span>
                    <div>
                        <h3>Grade-Wise Tuition Fee Schedule</h3>
                        <div style="font-size:0.85rem;color:#64748b">Covers smart classroom instruction, digital teaching, sports, and library access</div>
                    </div>
                </div>
                {{-- Payment Cycle Toggle --}}
                <div class="fee-cycle-toggle">
                    <button type="button" class="fee-cycle-btn active" onclick="setPaymentCycle('monthly', this)">Monthly (₹)</button>
                    <button type="button" class="fee-cycle-btn" onclick="setPaymentCycle('quarterly', this)">Quarterly (₹)</button>
                    <button type="button" class="fee-cycle-btn" onclick="setPaymentCycle('annual', this)">Annual (₹)</button>
                </div>
            </div>

            <div class="fee-table-responsive">
                <table class="fee-table">
                    <thead>
                        <tr>
                            <th style="width:40%">Grade</th>
                            <th id="th_periodic" style="width:30%">Tuition Fee Per Month (₹)</th>
                            <th style="width:30%">Total Annual Fee (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tuitionFees as $tf)
                        @php
                            $m = (int) ($tf['monthly'] ?? 0);
                            $a = (int) ($tf['annual'] ?? 0);
                            $q = $m * 3;
                        @endphp
                        <tr data-monthly="{{ $m }}" data-quarterly="{{ $q }}" data-annual="{{ $a }}">
                            <td>
                                <div class="fee-grade-badge">
                                    <span>🏫</span> {{ $tf['grade'] ?? '' }}
                                </div>
                            </td>
                            <td class="fee-td-periodic">
                                <span class="fee-amount-highlight">₹{{ number_format($m) }}</span>
                            </td>
                            <td>
                                <strong>₹{{ number_format($a) }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABLE 2: ONE-TIME & OTHER CHARGES --}}
        <div class="fee-card-box">
            <div class="fee-box-top">
                <div class="fee-box-title">
                    <span style="font-size:24px">🏷️</span>
                    <div>
                        <h3>One-Time &amp; Other Charges</h3>
                        <div style="font-size:0.85rem;color:#64748b">Applicable at the time of admission or as per school guidelines</div>
                    </div>
                </div>
                <span class="fee-badge-pill">Admission Specific</span>
            </div>

            <div class="fee-table-responsive">
                <table class="fee-table">
                    <thead>
                        <tr>
                            <th style="width:40%">Charge</th>
                            <th style="width:35%">Type</th>
                            <th style="width:25%">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otherCharges as $oc)
                        <tr>
                            <td>
                                <strong>{{ $oc['charge'] ?? '' }}</strong>
                            </td>
                            <td>
                                <span style="color:#64748b;font-size:0.9rem">{{ $oc['type'] ?? '' }}</span>
                            </td>
                            <td>
                                <span class="fee-amount-highlight">₹{{ number_format((int)($oc['amount'] ?? 0)) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 🧮 INTERACTIVE FEE ESTIMATOR FOR PARENTS --}}
        <div class="fee-calc-box">
            <div class="fee-calc-grid">
                <div class="fee-calc-left">
                    <div style="font-size:0.78rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#f59e0b;margin-bottom:6px">Quick Estimator</div>
                    <h3>Calculate Total Academic Investment</h3>
                    <p>Select your child’s grade to preview the exact breakdown of monthly tuition, one-time admission charges, and refundable security deposit.</p>
                    
                    <select id="calcGradeSelect" class="fee-calc-select" onchange="updateFeeEstimator()">
                        @foreach($tuitionFees as $idx => $tf)
                        <option value="{{ $idx }}" data-monthly="{{ $tf['monthly'] }}" data-annual="{{ $tf['annual'] }}">
                            {{ $tf['grade'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="fee-calc-results">
                    <div class="fee-calc-row">
                        <span>Monthly Tuition Fee:</span>
                        <strong id="calcMonthly">₹7,250</strong>
                    </div>
                    <div class="fee-calc-row">
                        <span>Quarterly Advance (3 mos):</span>
                        <strong id="calcQuarterly">₹21,750</strong>
                    </div>
                    <div class="fee-calc-row">
                        <span>One-Time Admission &amp; Reg:</span>
                        <span>₹21,000</span>
                    </div>
                    <div class="fee-calc-row">
                        <span>Refundable Security Deposit:</span>
                        <span>₹10,000</span>
                    </div>
                    <div class="fee-calc-row">
                        <span>Total 1st Year Annual Investment:</span>
                        <span id="calcTotalAnnual">₹1,18,000</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📝 OFFICIAL POLICY NOTES --}}
        @if(count($notes) > 0)
        <div class="fee-notes-box">
            <div class="fee-notes-title">
                <span>📌</span> Important Notes &amp; Fee Policy Guidelines
            </div>
            <ul class="fee-notes-list">
                @foreach($notes as $note)
                <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- 🤝 CLOSING COMMITMENT & HELP DESK --}}
        <div class="fee-closing-box">
            <div class="fee-closing-text">
                <h3>{{ $closingTitle }}</h3>
                <p>{{ $closingText }}</p>
            </div>

            <div class="fee-support-card">
                <h4>📞 Fee &amp; Admission Helpline</h4>
                <div class="fee-contact-item">
                    <span>📱</span> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $adminPhone) }}">{{ $adminPhone }}</a>
                </div>
                <div class="fee-contact-item">
                    <span>✉️</span> <a href="mailto:{{ $adminEmail }}">{{ $adminEmail }}</a>
                </div>
                <div class="fee-contact-item" style="font-size:0.82rem;color:#64748b">
                    <span>⏰</span> {{ $adminHours }}
                </div>
                <div style="margin-top:6px">
                    <a href="{{ $regUrl }}" target="_blank" class="fee-btn-primary" style="width:100%;font-size:0.88rem;padding:10px">
                        Proceed to Online Registration ↗
                    </a>
                </div>
            </div>
        </div>

        {{-- 📂 DOWNLOADS BAR --}}
        <div class="fee-downloads-bar">
            <div>
                <div class="fee-downloads-title">📥 Official Fee PDF Documents (Academic Session 2026-27)</div>
                <div style="font-size:0.85rem;color:#64748b">Download official stamped circulars and transport fee charts for your records</div>
            </div>
            <div class="fee-downloads-actions">
                @if(!empty($feePdfUrl))
                <a href="{{ $feePdfUrl }}" target="_blank" class="fee-pdf-btn">
                    <span>📄</span> Download Fee Structure PDF (2026-27)
                </a>
                @endif
                @if(!empty($transPdfUrl))
                <a href="{{ $transPdfUrl }}" target="_blank" class="fee-pdf-btn">
                    <span>🚌</span> Download Transport Fee PDF (2026-27)
                </a>
                @endif
            </div>
        </div>
    </section>
</div>

<script>
// Interactive Payment Cycle Switcher
function setPaymentCycle(cycle, btn) {
    document.querySelectorAll('.fee-cycle-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const thPeriodic = document.getElementById('th_periodic');
    if (cycle === 'monthly') {
        thPeriodic.textContent = 'Tuition Fee Per Month (₹)';
    } else if (cycle === 'quarterly') {
        thPeriodic.textContent = 'Quarterly Fee (3 Months) (₹)';
    } else if (cycle === 'annual') {
        thPeriodic.textContent = 'Total Annual Fee (₹)';
    }

    document.querySelectorAll('.fee-table tbody tr[data-monthly]').forEach(row => {
        const val = row.getAttribute(`data-${cycle}`);
        const td = row.querySelector('.fee-td-periodic span');
        if (td && val) {
            td.textContent = '₹' + Number(val).toLocaleString('en-IN');
        }
    });
}

// Quick Fee Estimator
function updateFeeEstimator() {
    const sel = document.getElementById('calcGradeSelect');
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    const m = Number(opt.getAttribute('data-monthly')) || 0;
    const a = Number(opt.getAttribute('data-annual')) || 0;
    const q = m * 3;
    const total1stYear = a + 20000 + 1000 + 10000; // Annual + Admission + Reg + Security

    document.getElementById('calcMonthly').textContent = '₹' + m.toLocaleString('en-IN');
    document.getElementById('calcQuarterly').textContent = '₹' + q.toLocaleString('en-IN');
    document.getElementById('calcTotalAnnual').textContent = '₹' + total1stYear.toLocaleString('en-IN');
}

document.addEventListener('DOMContentLoaded', () => {
    updateFeeEstimator();
});
</script>
