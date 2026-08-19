{{-- Next.js / Framer-Motion Style Ultra-Premium Careers Page & Apply Portal Widget --}}
{{-- 100% Full-Width Fullbleed Layout with About Us Style Hero Banner --}}
<div class="cp-fullbleed">
    {{-- Full-Bleed Hero Section (Matching About Us Page Hero Style) --}}
    <section class="cp-about-hero" style="background-image: url('{{ asset('images/career-hero-banner.webp') }}');">
        <div class="cp-about-hero-overlay"></div>
        <div class="cp-about-hero-container">
            <span class="cp-hero-kicker">★ Join Our Team</span>
            <h1 class="cp-hero-heading">Be a Part of the Team that Inspires the Next Generation.</h1>
            <p class="cp-hero-tagline">Life begins here… Explore rewarding career opportunities at Prayaag International School.</p>
            <div class="cp-hero-cta">
                <a href="#open-positions" class="cp-btn-gold">Explore Vacancies ↓</a>
                <a href="#apply-form" class="cp-btn-ghost">Apply Form Below ↓</a>
            </div>
        </div>
    </section>
</div>

<section class="cp-wrapper">
    {{-- Stats / Perks Grid --}}
    <div class="cp-stats-container">
        <div class="cp-stats-grid">
            <div class="cp-stat-card">
                <p class="cp-stat-value">100+</p>
                <p class="cp-stat-label">Faculty & Staff</p>
            </div>
            <div class="cp-stat-card">
                <p class="cp-stat-value cp-gold">Global</p>
                <p class="cp-stat-label">Teaching Standards</p>
            </div>
            <div class="cp-stat-card">
                <p class="cp-stat-value">100%</p>
                <p class="cp-stat-label">Collaborative Culture</p>
            </div>
            <div class="cp-stat-card">
                <p class="cp-stat-value cp-gold">Competitive</p>
                <p class="cp-stat-label">Growth & Remuneration</p>
            </div>
        </div>
    </div>

    {{-- Success Alert Notification --}}
    @if(session('application_sent'))
    <div class="cp-alert-success">
        <div class="cp-alert-icon">✓</div>
        <div>
            <h4 class="cp-alert-title">Application Submitted Successfully!</h4>
            <p class="cp-alert-desc">Thank you for applying to Prayaag International School. Our HR team will review your profile and reach out shorty.</p>
        </div>
    </div>
    @endif

    {{-- Category Filter Tabs & Job Grid Section --}}
    <div class="cp-container" id="open-positions" x-data="{ activeCategory: 'all' }">
        <div class="cp-section-head">
            <span class="cp-section-tag">Current Openings</span>
            <h2 class="cp-section-title">Explore Current Vacancies</h2>
        </div>

        <div class="cp-tabs-wrap">
            <button type="button"
                    class="cp-tab-btn"
                    :class="activeCategory === 'all' ? 'cp-tab-active' : ''"
                    @click="activeCategory = 'all'; filterJobs('all')">
                🌐 All Vacancies ({{ $jobs->count() }})
            </button>

            @foreach($categories as $cat)
            <button type="button"
                    class="cp-tab-btn"
                    :class="activeCategory === '{{ strtolower($cat) }}' ? 'cp-tab-active' : ''"
                    @click="activeCategory = '{{ strtolower($cat) }}'; filterJobs('{{ strtolower($cat) }}')">
                {{ $cat }}
            </button>
            @endforeach
        </div>

        {{-- Job Cards Grid --}}
        <div class="cp-job-grid">
            @forelse($jobs as $job)
            <div class="cp-job-card" data-department="{{ strtolower($job->department ?? 'general') }}">
                <div class="cp-card-head">
                    <div class="cp-card-badges">
                        <span class="cp-dept-badge">{{ $job->department ?? 'General' }}</span>
                        <span class="cp-type-badge">{{ ucfirst(str_replace('_', ' ', $job->employment_type ?? 'Full-time')) }}</span>
                    </div>

                    <h3 class="cp-job-title">{{ $job->title }}</h3>

                    <div class="cp-job-meta">
                        <span>📍 {{ $job->location ?? 'Main Campus, Panipat' }}</span>
                        @if($job->closes_at)
                        <span>⏳ Closes: {{ $job->closes_at->format('M d, Y') }}</span>
                        @endif
                    </div>

                    <p class="cp-job-desc">
                        {{ $job->description ?: 'Join our vibrant academic team at Prayaag International School. We offer competitive remuneration, professional growth opportunities, and an inspiring environment.' }}
                    </p>
                </div>

                <div class="cp-card-foot">
                    <span class="cp-ref-code">Ref: #{{ substr($job->id, -6) }}</span>
                    <button type="button"
                            class="cp-apply-btn"
                            onclick="openApplyModal('{{ $job->id }}', '{{ addslashes($job->title) }}')">
                        Apply Position →
                    </button>
                </div>
            </div>
            @empty
            <div class="cp-empty-box">
                <p class="cp-empty-icon">🎓</p>
                <h3 class="cp-empty-title">No Open Positions At Present</h3>
                <p class="cp-empty-desc">We are always eager to connect with outstanding educators! Send us your unsolicited CV using the form below.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Permanent Inline Application Form Section (At Very Bottom) --}}
    <div class="cp-bottom-form-wrap" id="apply-form">
        <div class="cp-bottom-form-card">
            <div class="cp-bottom-form-head">
                <span class="cp-badge-pill-dark">HR Portal</span>
                <h2 class="cp-bottom-form-title">Direct Job Application Form</h2>
                <p class="cp-bottom-form-sub">
                    Fill out your details below and upload your CV/Resume. Our recruitment committee will review your candidate profile.
                </p>
            </div>

            <form action="{{ route('jobs.apply') }}" method="POST" enctype="multipart/form-data" class="cp-inline-form">
                @csrf
                {{-- Honeypot --}}
                <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

                <div class="cp-form-group">
                    <label class="cp-label">Select Vacancy / Position <span class="cp-req">*</span></label>
                    <select name="job_listing_id" id="inlineJobSelect" required class="cp-input cp-select">
                        @foreach($jobs as $j)
                        <option value="{{ $j->id }}">{{ $j->title }} ({{ $j->department ?? 'General' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="cp-form-row">
                    <div class="cp-form-group">
                        <label class="cp-label">Full Name <span class="cp-req">*</span></label>
                        <input type="text" name="name" required placeholder="e.g. Dr. Ananya Sharma" class="cp-input">
                    </div>
                    <div class="cp-form-group">
                        <label class="cp-label">Email Address <span class="cp-req">*</span></label>
                        <input type="email" name="email" required placeholder="e.g. ananya@example.com" class="cp-input">
                    </div>
                </div>

                <div class="cp-form-group">
                    <label class="cp-label">Phone / WhatsApp Number <span class="cp-req">*</span></label>
                    <input type="text" name="phone" required placeholder="e.g. +91 98765 43210" class="cp-input">
                </div>

                <div class="cp-form-group">
                    <label class="cp-label">Cover Letter / Statement of Purpose</label>
                    <textarea name="cover_letter" rows="3" placeholder="Briefly describe your teaching philosophy and relevant experience..." class="cp-input cp-textarea"></textarea>
                </div>

                <div class="cp-form-group">
                    <label class="cp-label">Upload CV / Resume (PDF, DOC, DOCX up to 5MB) <span class="cp-req">*</span></label>
                    <div class="cp-file-dropzone">
                        <input type="file" name="resume" required accept=".pdf,.doc,.docx" class="cp-file-input" onchange="updateInlineFileName(this)">
                        <div class="cp-file-content">
                            <span class="cp-file-link">Click to browse file</span> or drag & drop resume here
                            <p class="cp-file-hint" id="inlineFileName">Supported formats: PDF, DOC, DOCX (Max 5MB)</p>
                        </div>
                    </div>
                </div>

                <div class="cp-form-submit-row">
                    <button type="submit" class="cp-submit-btn-lg">
                        Submit Application Now 🚀
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Lightbox Modal Popup --}}
<div id="vtApplyModal" class="cp-modal-overlay" style="display:none">
    <div class="cp-modal-box">
        <div class="cp-modal-head">
            <button type="button" onclick="closeApplyModal()" class="cp-modal-close">✕</button>
            <span class="cp-modal-tag">Job Application Form</span>
            <h3 class="cp-modal-title" id="vtModalJobTitle">Apply for Position</h3>
            <p class="cp-modal-sub">Prayaag International School HR Portal</p>
        </div>

        <form action="{{ route('jobs.apply') }}" method="POST" enctype="multipart/form-data" class="cp-modal-body">
            @csrf
            <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">

            <div class="cp-form-group">
                <label class="cp-label">Select Vacancy <span class="cp-req">*</span></label>
                <select name="job_listing_id" id="vtJobSelect" required class="cp-input cp-select">
                    @foreach($jobs as $j)
                    <option value="{{ $j->id }}">{{ $j->title }} ({{ $j->department ?? 'General' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="cp-form-row">
                <div class="cp-form-group">
                    <label class="cp-label">Full Name <span class="cp-req">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Dr. Ananya Sharma" class="cp-input">
                </div>
                <div class="cp-form-group">
                    <label class="cp-label">Email Address <span class="cp-req">*</span></label>
                    <input type="email" name="email" required placeholder="e.g. ananya@example.com" class="cp-input">
                </div>
            </div>

            <div class="cp-form-group">
                <label class="cp-label">Phone / WhatsApp Number <span class="cp-req">*</span></label>
                <input type="text" name="phone" required placeholder="e.g. +91 98765 43210" class="cp-input">
            </div>

            <div class="cp-form-group">
                <label class="cp-label">Cover Letter / Statement of Purpose</label>
                <textarea name="cover_letter" rows="3" placeholder="Briefly describe your teaching experience..." class="cp-input cp-textarea"></textarea>
            </div>

            <div class="cp-form-group">
                <label class="cp-label">Upload CV / Resume (PDF, DOC, DOCX up to 5MB) <span class="cp-req">*</span></label>
                <div class="cp-file-dropzone">
                    <input type="file" name="resume" required accept=".pdf,.doc,.docx" class="cp-file-input" onchange="updateModalFileName(this)">
                    <div class="cp-file-content">
                        <span class="cp-file-link">Click to browse file</span> or drag & drop resume here
                        <p class="cp-file-hint" id="modalFileName">Supported formats: PDF, DOC, DOCX (Max 5MB)</p>
                    </div>
                </div>
            </div>

            <div class="cp-modal-actions">
                <button type="button" onclick="closeApplyModal()" class="cp-btn-secondary">Cancel</button>
                <button type="submit" class="cp-btn-primary">Submit Application 🚀</button>
            </div>
        </form>
    </div>
</div>

<style>
/* 100% Full-Width Fullbleed Wrapper */
.cp-fullbleed {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    box-sizing: border-box;
}

/* About Us Style Fullbleed Hero Banner */
.cp-about-hero {
    position: relative;
    min-height: 520px;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    color: #ffffff;
    padding: 5rem 1.5rem;
    box-sizing: border-box;
}

.cp-about-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(14, 47, 94, 0.94) 0%, rgba(14, 47, 94, 0.78) 55%, rgba(15, 23, 42, 0.55) 100%);
    z-index: 1;
}

.cp-about-hero-container {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
    padding: 0 1.5rem;
}

.cp-hero-kicker {
    display: inline-block;
    color: #eda52a;
    font-size: 0.85rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 0.75rem;
}

.cp-hero-heading {
    font-size: 3.25rem;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.18;
    letter-spacing: -0.02em;
    max-width: 850px;
    margin: 0 0 1rem;
    text-shadow: 0 4px 16px rgba(0, 0, 0, 0.5);
}

@media(max-width: 768px) {
    .cp-hero-heading { font-size: 2.25rem; }
    .cp-about-hero { min-height: 420px; padding: 3.5rem 1rem; }
}

.cp-hero-tagline {
    font-size: 1.15rem;
    color: #e2e8f0;
    max-width: 650px;
    margin: 0 0 2rem;
    line-height: 1.6;
    font-weight: 400;
}

.cp-hero-cta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.cp-btn-gold {
    background: linear-gradient(135deg, #c79a3b 0%, #eda52a 100%);
    color: #ffffff;
    font-weight: 800;
    font-size: 0.85rem;
    padding: 0.85rem 1.75rem;
    border-radius: 0.75rem;
    text-decoration: none;
    box-shadow: 0 6px 20px rgba(199, 154, 59, 0.4);
    transition: transform 0.2s, box-shadow 0.2s;
}

.cp-btn-gold:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(199, 154, 59, 0.5);
}

.cp-btn-ghost {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    font-weight: 800;
    font-size: 0.85rem;
    padding: 0.85rem 1.75rem;
    border-radius: 0.75rem;
    text-decoration: none;
    transition: background 0.2s;
}

.cp-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.28);
}

/* Full Standalone Scoped Next.js & Framer-Motion Styles */
.cp-wrapper { width:100%; min-height:100vh; background:#f8fafc; padding:3rem 1.5rem 5rem; font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color:#0f172a; box-sizing:border-box; }
.cp-wrapper * { box-sizing:border-box; }

/* Stats Container */
.cp-stats-container { max-width: 1200px; margin: 0 auto 3.5rem; }
.cp-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
@media(max-width: 768px) { .cp-stats-grid { grid-template-columns: repeat(2, 1fr); } }

.cp-stat-card { background: #ffffff; padding: 1.25rem 1rem; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04); text-align: center; transition: transform 0.3s ease; }
.cp-stat-card:hover { transform: translateY(-4px); }
.cp-stat-value { font-size: 1.75rem; font-weight: 900; color: #0e2f5e; margin: 0; }
.cp-stat-value.cp-gold { color: #c79a3b; }
.cp-stat-label { font-size: 0.75rem; font-weight: 700; color: #64748b; margin: 0.2rem 0 0; }

/* Section Title */
.cp-section-head { text-align: center; margin-bottom: 2rem; }
.cp-section-tag { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #c79a3b; display: block; margin-bottom: 0.3rem; }
.cp-section-title { font-size: 2rem; font-weight: 900; color: #0f172a; margin: 0; }

/* Filter Tabs */
.cp-container { max-width: 1200px; margin: 0 auto 5rem; }
.cp-tabs-wrap { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.6rem; margin-bottom: 2.5rem; }
.cp-tab-btn { background: #ffffff; border: 1px solid #cbd5e1; color: #475569; font-size: 0.8rem; font-weight: 700; padding: 0.65rem 1.3rem; border-radius: 99px; cursor: pointer; transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
.cp-tab-btn:hover { background: #f1f5f9; color: #0f172a; }
.cp-tab-active { background: #0e2f5e !important; color: #ffffff !important; border-color: #0e2f5e !important; box-shadow: 0 6px 20px rgba(14, 47, 94, 0.25); transform: scale(1.04); }

/* Job Cards Grid */
.cp-job-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.75rem; }
@media(max-width: 1024px) { .cp-job-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 640px) { .cp-job-grid { grid-template-columns: 1fr; } }

.cp-job-card { background: #ffffff; border-radius: 1.5rem; padding: 1.75rem; border: 1px solid #e2e8f0; box-shadow: 0 6px 24px rgba(15, 23, 42, 0.05); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); animation: cpFadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.cp-job-card:hover { transform: translateY(-8px) scale(1.02); border-color: #c79a3b; box-shadow: 0 20px 45px rgba(14, 47, 94, 0.16); }

.cp-card-head { display: flex; flex-direction: column; gap: 0.6rem; }
.cp-card-badges { display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; }
.cp-dept-badge { background: rgba(14, 47, 94, 0.08); color: #0e2f5e; font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; padding: 0.25rem 0.75rem; border-radius: 99px; }
.cp-type-badge { background: #fef3c7; color: #92400e; font-size: 0.65rem; font-weight: 700; border: 1px solid #fde68a; padding: 0.2rem 0.6rem; border-radius: 99px; }

.cp-job-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0.25rem 0 0; line-height: 1.3; }
.cp-job-meta { display: flex; gap: 1rem; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-top: 0.25rem; }

.cp-job-desc { font-size: 0.8rem; color: #475569; margin: 0.5rem 0 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

.cp-card-foot { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #f1f5f9; padding-top: 1.25rem; margin-top: 1.5rem; }
.cp-ref-code { font-size: 0.7rem; font-weight: 700; color: #94a3b8; }

.cp-apply-btn { background: linear-gradient(135deg, #0e2f5e 0%, #1e40af 100%); color: #ffffff; font-size: 0.78rem; font-weight: 800; border: none; padding: 0.65rem 1.25rem; border-radius: 0.75rem; cursor: pointer; box-shadow: 0 4px 14px rgba(14, 47, 94, 0.25); transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
.cp-apply-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(14, 47, 94, 0.35); background: linear-gradient(135deg, #c79a3b 0%, #eda52a 100%); }

/* Bottom Permanent Form Section */
.cp-bottom-form-wrap { max-width: 900px; margin: 0 auto; }
.cp-bottom-form-card { background: #ffffff; border-radius: 2rem; padding: 3rem 2.5rem; border: 1px solid #e2e8f0; box-shadow: 0 16px 45px rgba(15, 23, 42, 0.08); }
@media(max-width: 640px) { .cp-bottom-form-card { padding: 2rem 1.25rem; } }

.cp-badge-pill-dark { display: inline-block; background: rgba(14, 47, 94, 0.1); color: #0e2f5e; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; padding: 0.3rem 0.9rem; border-radius: 99px; }
.cp-bottom-form-head { text-align: center; margin-bottom: 2rem; }
.cp-bottom-form-title { font-size: 2.25rem; font-weight: 900; color: #0f172a; margin: 0.75rem 0 0; }
.cp-bottom-form-sub { font-size: 0.95rem; color: #64748b; max-width: 600px; margin: 0.5rem auto 0; line-height: 1.6; }

.cp-inline-form { display: flex; flex-direction: column; gap: 1.25rem; }
.cp-submit-btn-lg { width: 100%; background: linear-gradient(135deg, #0e2f5e 0%, #1e40af 100%); color: #ffffff; font-size: 1rem; font-weight: 900; border: none; padding: 1rem; border-radius: 1rem; cursor: pointer; box-shadow: 0 8px 25px rgba(14, 47, 94, 0.3); transition: transform 0.2s, box-shadow 0.2s; }
.cp-submit-btn-lg:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(14, 47, 94, 0.4); background: linear-gradient(135deg, #c79a3b 0%, #eda52a 100%); }

/* Empty state */
.cp-empty-box { grid-column: 1 / -1; text-align: center; background: #ffffff; padding: 4rem 2rem; border-radius: 2rem; border: 1px solid #e2e8f0; }
.cp-empty-icon { font-size: 3rem; margin: 0; }
.cp-empty-title { font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0.5rem 0 0; }
.cp-empty-desc { font-size: 0.85rem; color: #64748b; max-width: 480px; margin: 0.5rem auto 0; }

/* Alert */
.cp-alert-success { max-width: 800px; margin: 0 auto 2.5rem; background: #dcfce7; border: 1px solid #86efac; color: #14532d; padding: 1.25rem 1.5rem; border-radius: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 16px rgba(22, 101, 52, 0.1); }
.cp-alert-icon { width: 36px; height: 36px; border-radius: 50%; background: #22c55e; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.1rem; shrink: 0; }
.cp-alert-title { font-weight: 800; font-size: 1rem; margin: 0; }
.cp-alert-desc { font-size: 0.8rem; margin: 0.2rem 0 0; opacity: 0.9; }

/* Application Modal Drawer */
.cp-modal-overlay { position: fixed; inset: 0; z-index: 9999; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(12px); display: flex; align-items: center; justify-content: center; padding: 1rem; }
.cp-modal-box { background: #ffffff; max-width: 580px; width: 100%; border-radius: 2rem; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.3); border: 1px solid #e2e8f0; animation: cpModalZoom 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.cp-modal-head { background: linear-gradient(135deg, #0e2f5e 0%, #1e40af 100%); padding: 1.75rem 2rem; color: #ffffff; position: relative; }
.cp-modal-close { position: absolute; top: 1.25rem; right: 1.25rem; background: rgba(255,255,255,0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
.cp-modal-close:hover { background: rgba(255,255,255,0.3); }
.cp-modal-tag { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #eda52a; }
.cp-modal-title { font-size: 1.5rem; font-weight: 900; margin: 0.2rem 0 0; }
.cp-modal-sub { font-size: 0.75rem; color: #93c5fd; margin: 0.2rem 0 0; }

.cp-modal-body { padding: 1.75rem 2rem; display: flex; flex-direction: column; gap: 1rem; }
.cp-form-group { display: flex; flex-direction: column; gap: 0.4rem; }
.cp-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
@media(max-width: 640px) { .cp-form-row { grid-template-columns: 1fr; } }

.cp-label { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #475569; }
.cp-req { color: #ef4444; }

.cp-input { width: 100%; border: 1px solid #cbd5e1; border-radius: 0.85rem; padding: 0.75rem 1rem; font-size: 0.85rem; font-weight: 600; color: #0f172a; background: #ffffff; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
.cp-input:focus { border-color: #0e2f5e; box-shadow: 0 0 0 3px rgba(14, 47, 94, 0.15); }
.cp-select { background: #f8fafc; }
.cp-textarea { resize: vertical; }

.cp-file-dropzone { border: 2px dashed #cbd5e1; border-radius: 1rem; padding: 1.25rem; text-align: center; background: #f8fafc; position: relative; cursor: pointer; transition: border-color 0.2s; }
.cp-file-dropzone:hover { border-color: #0e2f5e; }
.cp-file-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.cp-file-content { font-size: 0.8rem; color: #64748b; font-weight: 500; }
.cp-file-link { color: #0e2f5e; font-weight: 800; text-decoration: underline; }
.cp-file-hint { font-size: 0.7rem; color: #94a3b8; margin: 0.4rem 0 0; }

.cp-modal-actions { display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 1.25rem; margin-top: 0.5rem; }
.cp-btn-secondary { background: #f1f5f9; border: none; color: #475569; font-size: 0.8rem; font-weight: 700; padding: 0.65rem 1.25rem; border-radius: 0.75rem; cursor: pointer; }
.cp-btn-primary { background: linear-gradient(135deg, #0e2f5e 0%, #1e40af 100%); border: none; color: #ffffff; font-size: 0.8rem; font-weight: 800; padding: 0.65rem 1.5rem; border-radius: 0.75rem; cursor: pointer; box-shadow: 0 4px 14px rgba(14, 47, 94, 0.25); transition: transform 0.2s; }
.cp-btn-primary:hover { transform: scale(1.03); }

/* Animations */
@keyframes cpFadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes cpModalZoom {
    from { opacity: 0; transform: scale(0.92); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
function filterJobs(category) {
    var cards = document.querySelectorAll('.cp-job-card');
    cards.forEach(function(card) {
        var dept = card.getAttribute('data-department') || '';
        if (category === 'all' || dept.includes(category)) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}

function openApplyModal(jobId, jobTitle) {
    var m = document.getElementById('vtApplyModal');
    var select = document.getElementById('vtJobSelect');
    var titleElem = document.getElementById('vtModalJobTitle');

    if (jobTitle) {
        titleElem.textContent = 'Apply: ' + jobTitle;
    }
    if (jobId && select) {
        select.value = jobId;
    }
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeApplyModal() {
    var m = document.getElementById('vtApplyModal');
    m.style.display = 'none';
    document.body.style.overflow = '';
}

function updateModalFileName(input) {
    var label = document.getElementById('modalFileName');
    if (input.files && input.files[0]) {
        label.textContent = 'Selected: ' + input.files[0].name + ' (' + Math.round(input.files[0].size / 1024) + ' KB)';
        label.style.color = '#166534';
        label.style.fontWeight = 'bold';
    }
}

function updateInlineFileName(input) {
    var label = document.getElementById('inlineFileName');
    if (input.files && input.files[0]) {
        label.textContent = 'Selected: ' + input.files[0].name + ' (' + Math.round(input.files[0].size / 1024) + ' KB)';
        label.style.color = '#166534';
        label.style.fontWeight = 'bold';
    }
}
</script>
