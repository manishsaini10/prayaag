{{--
    Contact Us Page Widget — Ultra-Premium School Contact & Campus Location Portal
    Designed with School Design System Tokens (Navy, Gold, Playfair/Poppins)
--}}

<style>
/* ================================================================
   CONTACT US PAGE — SCOPED ULTRA-PREMIUM FULL-WIDTH CSS
   ================================================================ */

.cnt-wrapper {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    overflow-x: hidden;
    background: #f8fafc;
}

.pb-section:has(.cnt-wrapper),
.pb-section--full-width {
    padding: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

.pb-section:has(.cnt-wrapper) .pb-row,
.pb-section:has(.cnt-wrapper) .pb-col {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ── Hero ────────────────────────────────────────────────────────── */
.cnt-hero {
    position: relative;
    min-height: 460px;
    display: flex;
    align-items: center;
    overflow: hidden;
    background-color: var(--navy, #0b2545);
}

.cnt-hero__bg {
    position: absolute;
    inset: 0;
    background-image: url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/Admin-Resception.webp');
    background-size: cover;
    background-position: center;
    opacity: .32;
    transform: scale(1.03);
    transition: transform 6s ease-out;
}

.cnt-hero:hover .cnt-hero__bg {
    transform: scale(1.08);
}

.cnt-hero__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(11,37,69,0.95) 0%, rgba(11,37,69,0.78) 60%, rgba(197,143,39,0.25) 100%);
}

.cnt-hero__content {
    position: relative;
    z-index: 2;
    padding: 100px 4vw 70px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.cnt-eyebrow {
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

.cnt-hero__title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: clamp(2.2rem, 5vw, 3.8rem);
    font-weight: 700;
    color: #ffffff;
    line-height: 1.2;
    margin: 0 0 18px;
}

.cnt-hero__title span {
    color: var(--gold-2, #f59e0b);
}

.cnt-hero__sub {
    font-size: 1.15rem;
    color: rgba(255,255,255,0.88);
    max-width: 760px;
    line-height: 1.7;
    margin: 0 0 32px;
}

.cnt-hero__actions {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.cnt-btn-primary {
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

.cnt-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.45);
}

.cnt-btn-secondary {
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

.cnt-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ── Top Contact Cards Bar ───────────────────────────────────────── */
.cnt-cards-bar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    box-shadow: 0 4px 20px -4px rgba(0,0,0,0.06);
    position: relative;
    z-index: 10;
    width: 100%;
}

.cnt-cards-grid {
    max-width: 100%;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    padding: 0 4vw;
}

.cnt-card-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 26px 20px;
    border-right: 1px solid #f1f5f9;
}

.cnt-card-item:last-child {
    border-right: none;
}

.cnt-card-icon {
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

.cnt-card-info h4 {
    margin: 0 0 4px;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}

.cnt-card-info p {
    margin: 0;
    font-size: 0.88rem;
    color: #64748b;
    line-height: 1.5;
}

.cnt-card-info a {
    color: #0f172a;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s ease;
}

.cnt-card-info a:hover {
    color: var(--gold, #d97706);
}

/* ── Main Split Section (Form + Campus Info) ─────────────────────── */
.cnt-section {
    padding: 70px 4vw 80px;
    max-width: 100%;
    margin: 0 auto;
    width: 100%;
}

.cnt-split-grid {
    display: grid;
    grid-template-columns: 1.15fr 0.85fr;
    gap: 40px;
    margin-bottom: 70px;
}

@media(max-width: 980px) {
    .cnt-split-grid {
        grid-template-columns: 1fr;
    }
}

/* ── Form Card ───────────────────────────────────────────────────── */
.cnt-form-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 10px 36px -10px rgba(0,0,0,0.06);
}

.cnt-form-header {
    margin-bottom: 28px;
}

.cnt-form-header h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.85rem;
    color: #0f172a;
    margin: 0 0 8px;
}

.cnt-form-header p {
    font-size: 0.95rem;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}

.cnt-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 18px;
}

@media(max-width: 600px) {
    .cnt-form-row {
        grid-template-columns: 1fr;
    }
}

.cnt-form-group {
    margin-bottom: 18px;
}

.cnt-form-group label {
    display: block;
    font-size: 0.86rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 6px;
}

.cnt-input,
.cnt-select,
.cnt-textarea {
    width: 100%;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 0.92rem;
    color: #0f172a;
    outline: none;
    transition: all 0.2s ease;
    background: #ffffff;
    font-family: inherit;
}

.cnt-input:focus,
.cnt-select:focus,
.cnt-textarea:focus {
    border-color: var(--gold, #d97706);
    box-shadow: 0 0 0 3px rgba(217,119,6,0.12);
}

.cnt-submit-btn {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: #0b2545;
    color: #ffffff !important;
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(11,37,69,0.25);
    transition: all 0.3s ease;
}

.cnt-submit-btn:hover {
    background: var(--gold, #d97706);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(217,119,6,0.35);
}

/* ── Campus Info Card ────────────────────────────────────────────── */
.cnt-info-card {
    background: linear-gradient(135deg, #0b2545, #16325c);
    border-radius: 20px;
    padding: 40px;
    color: #ffffff;
    box-shadow: 0 20px 40px -10px rgba(11,37,69,0.35);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.cnt-info-title {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.75rem;
    color: var(--gold-2, #f59e0b);
    margin: 0 0 20px;
}

.cnt-info-list {
    display: flex;
    flex-direction: column;
    gap: 22px;
    margin-bottom: 30px;
}

.cnt-info-item {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.cnt-info-item-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.2);
    display: grid;
    place-items: center;
    font-size: 20px;
    flex-shrink: 0;
}

.cnt-info-item-text h5 {
    margin: 0 0 4px;
    font-size: 0.98rem;
    font-weight: 700;
    color: #ffffff;
}

.cnt-info-item-text p {
    margin: 0;
    font-size: 0.88rem;
    color: rgba(255,255,255,0.82);
    line-height: 1.5;
}

.cnt-info-item-text a {
    color: var(--gold-2, #f59e0b);
    text-decoration: none;
    font-weight: 600;
}

.cnt-quick-ctas {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cnt-cta-link-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 0.92rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
}

.cnt-cta-wa {
    background: #16a34a;
    color: #ffffff !important;
}
.cnt-cta-wa:hover {
    background: #15803d;
    transform: translateY(-2px);
}

.cnt-cta-map {
    background: rgba(255,255,255,0.15);
    color: #ffffff !important;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(8px);
}
.cnt-cta-map:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
}

/* ── Full Width Interactive Google Map Section ───────────────────── */
.cnt-map-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 36px -10px rgba(0,0,0,0.06);
    margin-bottom: 70px;
}

.cnt-map-header {
    padding: 24px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 16px;
}

.cnt-map-header h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 1.55rem;
    color: #0f172a;
    margin: 0;
}

.cnt-map-header p {
    font-size: 0.88rem;
    color: #64748b;
    margin: 4px 0 0;
}

.cnt-map-iframe-box {
    width: 100%;
    height: 480px;
    position: relative;
}

.cnt-map-iframe-box iframe {
    width: 100%;
    height: 100%;
    border: 0;
}

/* ── Social Videos & Community Section ───────────────────────────── */
.cnt-social-section {
    margin-bottom: 70px;
}

.cnt-social-header {
    text-align: center;
    max-width: 680px;
    margin: 0 auto 36px;
}

.cnt-social-header h3 {
    font-family: var(--font-head, 'Playfair Display', serif);
    font-size: 2.2rem;
    color: #0f172a;
    margin: 0 0 10px;
}

.cnt-social-header p {
    font-size: 0.95rem;
    color: #64748b;
    margin: 0;
}

.cnt-social-links-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 14px;
    margin-bottom: 36px;
    flex-wrap: wrap;
}

.cnt-social-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 999px;
    font-size: 0.88rem;
    font-weight: 700;
    color: #ffffff !important;
    text-decoration: none;
    transition: all 0.2s ease;
}

.cnt-social-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.soc-fb { background: #1877f2; }
.soc-ig { background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); }
.soc-tw { background: #0f1419; }
.soc-li { background: #0a66c2; }
.soc-yt { background: #ff0000; }

.cnt-videos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.cnt-video-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 20px -4px rgba(0,0,0,0.06);
}

.cnt-video-card iframe {
    width: 100%;
    height: 300px;
    border: none;
}

/* ── FAQ Section ─────────────────────────────────────────────────── */
.cnt-faq-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 60px;
}

.cnt-faq-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px -4px rgba(0,0,0,0.04);
}

.cnt-faq-card h4 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 10px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.cnt-faq-card h4 span {
    color: var(--gold, #d97706);
}

.cnt-faq-card p {
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}
</style>

<div class="cnt-wrapper">
    {{-- 🌟 HERO BANNER --}}
    <section class="cnt-hero">
        <div class="cnt-hero__bg"></div>
        <div class="cnt-hero__overlay"></div>
        <div class="cnt-hero__content">
            <div class="cnt-eyebrow">
                <span>📍</span> Connect With Prayaag International School
            </div>
            <h1 class="cnt-hero__title">
                Reach Us — We'd Love to <span>Hear From You</span>
            </h1>
            <p class="cnt-hero__sub">
                Contact us for admission consultations, campus tours, transport queries, or academic assistance. Our administrative helpdesk is always ready to assist students and parents.
            </p>
            <div class="cnt-hero__actions">
                <a href="#contact-form-section" class="cnt-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    Send an Inquiry
                </a>
                <a href="#campus-map-section" class="cnt-btn-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    View Campus on Map
                </a>
            </div>
        </div>
    </section>

    {{-- 📊 TOP CONTACT CARDS BAR --}}
    <div class="cnt-cards-bar">
        <div class="cnt-cards-grid">
            <div class="cnt-card-item">
                <div class="cnt-card-icon">✉️</div>
                <div class="cnt-card-info">
                    <h4>Email Inquiries</h4>
                    <p><a href="mailto:mailus@pisp.in">mailus@pisp.in</a></p>
                </div>
            </div>
            <div class="cnt-card-item">
                <div class="cnt-card-icon">📞</div>
                <div class="cnt-card-info">
                    <h4>Direct Helpline</h4>
                    <p><a href="tel:9350748851">+91 93507 48851</a></p>
                    <p><a href="tel:01802565555" style="font-size:0.8rem;color:#64748b">+91 180-2565555, 2575555</a></p>
                </div>
            </div>
            <div class="cnt-card-item">
                <div class="cnt-card-icon">🏫</div>
                <div class="cnt-card-info">
                    <h4>Campus Address</h4>
                    <p>Opp. New Police Lines, NH-44, Panipat-132103, Haryana</p>
                </div>
            </div>
            <div class="cnt-card-item">
                <div class="cnt-card-icon">⏰</div>
                <div class="cnt-card-info">
                    <h4>Visiting Hours</h4>
                    <p>Mon – Sat: 08:00 AM – 03:30 PM</p>
                    <p style="font-size:0.8rem;color:#dc2626;font-weight:600">Sunday Closed</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 📝 MAIN SECTION: FORM + CAMPUS DETAILS --}}
    <section class="cnt-section" id="contact-form-section">
        <div class="cnt-split-grid">
            {{-- Form Column --}}
            <div class="cnt-form-card">
                <div class="cnt-form-header">
                    <h3>Send Us a Direct Message</h3>
                    <p>Fill out the form below and our counseling coordinator will get in touch with you within 24 hours.</p>
                </div>

                @if(session('success'))
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:14px 18px;border-radius:10px;margin-bottom:20px;font-size:0.92rem;font-weight:600">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:14px 18px;border-radius:10px;margin-bottom:20px;font-size:0.92rem">
                        ⚠️ Please check the required form fields.
                    </div>
                @endif

                <form action="{{ route('enquiries.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="contact">
                    <input type="hidden" name="source" value="Contact Us Page">

                    {{-- Honeypot Spam Protection --}}
                    <div style="display:none !important">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="cnt-form-row">
                        <div class="cnt-form-group" style="margin-bottom:0">
                            <label for="name">Your Name <span style="color:#dc2626">*</span></label>
                            <input type="text" id="name" name="name" class="cnt-input" placeholder="e.g. Rahul Sharma" value="{{ old('name') }}" required>
                        </div>
                        <div class="cnt-form-group" style="margin-bottom:0">
                            <label for="email">Email Address <span style="color:#dc2626">*</span></label>
                            <input type="email" id="email" name="email" class="cnt-input" placeholder="e.g. rahul@example.com" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="cnt-form-row">
                        <div class="cnt-form-group" style="margin-bottom:0">
                            <label for="phone">Mobile / WhatsApp Number <span style="color:#dc2626">*</span></label>
                            <input type="tel" id="phone" name="phone" class="cnt-input" placeholder="e.g. 9876543210" value="{{ old('phone') }}" required>
                        </div>
                        <div class="cnt-form-group" style="margin-bottom:0">
                            <label for="subject">Inquiry Subject <span style="color:#dc2626">*</span></label>
                            <select id="subject" name="subject" class="cnt-select" required>
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Admissions 2026–27">Admissions 2026–27</option>
                                <option value="Campus Visit Appointment">Campus Visit Appointment</option>
                                <option value="Transport & Bus Routes">Transport &amp; Bus Routes</option>
                                <option value="Academic Curriculum & Syllabus">Academic Curriculum &amp; Syllabus</option>
                                <option value="Fee Structure & Accounts">Fee Structure &amp; Accounts</option>
                                <option value="Career & Job Opportunities">Career &amp; Job Opportunities</option>
                            </select>
                        </div>
                    </div>

                    <div class="cnt-form-group">
                        <label for="message">Your Message / Query <span style="color:#dc2626">*</span></label>
                        <textarea id="message" name="message" rows="5" class="cnt-textarea" placeholder="Please write your detailed query or message here..." required>{{ old('message') }}</textarea>
                    </div>

                    <button type="submit" class="cnt-submit-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        Submit Contact Inquiry
                    </button>
                </form>
            </div>

            {{-- Campus Info Column --}}
            <div class="cnt-info-card">
                <div>
                    <h3 class="cnt-info-title">Visit Our Campus</h3>
                    <div class="cnt-info-list">
                        <div class="cnt-info-item">
                            <div class="cnt-info-item-icon">📍</div>
                            <div class="cnt-info-item-text">
                                <h5>Campus Location</h5>
                                <p>
                                    Prayaag International School<br>
                                    Opp. New Police Lines, Near Indraprastha Institute of Medical Sciences,<br>
                                    NH-44, Panipat-132103, Haryana
                                </p>
                            </div>
                        </div>

                        <div class="cnt-info-item">
                            <div class="cnt-info-item-icon">📞</div>
                            <div class="cnt-info-item-text">
                                <h5>Phone Lines</h5>
                                <p>
                                    Hotline: <a href="tel:919350748851">+91 93507 48851</a><br>
                                    Landlines: <a href="tel:01802565555">+91 180-2565555</a>, <a href="tel:01802575555">2575555</a>
                                </p>
                            </div>
                        </div>

                        <div class="cnt-info-item">
                            <div class="cnt-info-item-icon">✉️</div>
                            <div class="cnt-info-item-text">
                                <h5>Official Email ID</h5>
                                <p><a href="mailto:mailus@pisp.in">mailus@pisp.in</a></p>
                            </div>
                        </div>

                        <div class="cnt-info-item">
                            <div class="cnt-info-item-icon">⏰</div>
                            <div class="cnt-info-item-text">
                                <h5>Office &amp; Visitor Hours</h5>
                                <p>Monday to Saturday: 08:00 AM – 03:30 PM<br>(Closed on Sundays &amp; Public Holidays)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cnt-quick-ctas">
                    <a href="https://wa.me/919350748851?text=Hello%20Prayaag%20School,%20I%20would%20like%20to%20inquire%20about%20admissions%20and%20campus%20visits." target="_blank" class="cnt-cta-link-btn cnt-cta-wa">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        Instant WhatsApp Chat
                    </a>
                    <a href="https://maps.google.com/?q=Prayaag+International+School+Panipat" target="_blank" class="cnt-cta-link-btn cnt-cta-map">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        Open in Google Maps App
                    </a>
                </div>
            </div>
        </div>

        {{-- 🗺️ FULL-BLEED GOOGLE MAPS CARD --}}
        <div class="cnt-map-wrapper" id="campus-map-section">
            <div class="cnt-map-header">
                <div>
                    <h3>Campus Location &amp; Driving Directions</h3>
                    <p>Conveniently located on NH-44 Highway, opposite New Police Lines Panipat.</p>
                </div>
                <a href="https://maps.google.com/?q=Prayaag+International+School+Panipat" target="_blank" class="cnt-btn-primary" style="padding:10px 20px;font-size:0.88rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
                    Get GPS Driving Route
                </a>
            </div>
            <div class="cnt-map-iframe-box">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d55659.904628583856!2d76.986936!3d29.319182999999995!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xd337897af9217763!2sPrayaag%20International%20School%2C%20Panipat!5e0!3m2!1sen!2sin!4v1642017535643!5m2!1sen!2sin" width="100%" height="480" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        {{-- 📱 SOCIAL CHANNELS & VIDEO SHOWCASE --}}
        <div class="cnt-social-section">
            <div class="cnt-social-header">
                <h3>Follow Us &amp; Stay Connected</h3>
                <p>Join our thriving digital community on social media to experience daily life, events, and achievements at Prayaag.</p>
            </div>

            <div class="cnt-social-links-bar">
                <a href="https://www.facebook.com/PrayaagInternationalSchoolPanipat" target="_blank" class="cnt-social-pill soc-fb">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    Facebook
                </a>
                <a href="http://instagram.com/prayaag2016" target="_blank" class="cnt-social-pill soc-ig">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    Instagram
                </a>
                <a href="https://twitter.com/MailusIntl" target="_blank" class="cnt-social-pill soc-tw">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    Twitter / X
                </a>
                <a href="https://www.linkedin.com/company/prayaag-international-school" target="_blank" class="cnt-social-pill soc-li">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    LinkedIn
                </a>
                <a href="https://www.youtube.com/channel/UCeqR_-8SsGfMi09aX1FSzdA" target="_blank" class="cnt-social-pill soc-yt">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    YouTube
                </a>
            </div>

            <div class="cnt-videos-grid">
                <div class="cnt-video-card">
                    <iframe loading="lazy" src="https://www.facebook.com/plugins/video.php?height=314&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F253105543457607%2F&amp;show_text=false&amp;width=560&amp;t=0" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                </div>
                <div class="cnt-video-card">
                    <iframe loading="lazy" src="https://www.facebook.com/plugins/video.php?height=308&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F963896641178047%2F&amp;show_text=false&amp;width=560&amp;t=0" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                </div>
                <div class="cnt-video-card">
                    <iframe loading="lazy" src="https://www.facebook.com/plugins/video.php?height=308&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F1694315614072401%2F&amp;show_text=false&amp;width=560&amp;t=0" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                </div>
                <div class="cnt-video-card">
                    <iframe loading="lazy" src="https://www.facebook.com/plugins/video.php?height=316&amp;href=https%3A%2F%2Fwww.facebook.com%2FPrayaagInternationalSchool%2Fvideos%2F1252873791879404%2F&amp;show_text=false&amp;width=560&amp;t=0" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>
                </div>
            </div>
        </div>

        {{-- ❓ FREQUENTLY ASKED QUESTIONS --}}
        <div class="cnt-social-header" style="margin-bottom:30px">
            <h3>Visiting &amp; Consultation FAQs</h3>
            <p>Helpful information for parents planning to visit the campus or connect with the administration.</p>
        </div>

        <div class="cnt-faq-grid">
            <div class="cnt-faq-card">
                <h4><span>Q1.</span> Do I need an appointment before visiting the school?</h4>
                <p>While walk-in visits are welcome between 08:00 AM and 03:00 PM (Monday to Saturday), booking an appointment ensures our Academic Counselor is reserved exclusively for your session.</p>
            </div>
            <div class="cnt-faq-card">
                <h4><span>Q2.</span> What documents should I bring for admission enquiry?</h4>
                <p>We recommend carrying the child's previous year report card, birth certificate photocopy, and 2 passport-sized photographs for spot document verification.</p>
            </div>
            <div class="cnt-faq-card">
                <h4><span>Q3.</span> How do I check school bus routes for my sector?</h4>
                <p>Our Transport Coordination Desk can be reached at <strong>+91 93507 48851</strong> to verify exact pick-up and drop timings across Panipat, Samalkha, and adjoining areas.</p>
            </div>
            <div class="cnt-faq-card">
                <h4><span>Q4.</span> How soon will I receive a callback after submitting the enquiry?</h4>
                <p>Our admissions desk responds within 2 to 4 working hours during school office timings (08:00 AM to 03:30 PM).</p>
            </div>
        </div>
    </section>
</div>
