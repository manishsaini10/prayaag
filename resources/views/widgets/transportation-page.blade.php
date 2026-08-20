{{-- Transportation & Fleet Safety Page Widget --}}
@php
    $settings          = $settings ?? [];
    $heroTitle         = $settings['hero_title'] ?? 'Safe & Reliable School Transportation';
    $heroSub           = $settings['hero_subtitle'] ?? '';
    $heroBg            = $settings['hero_bg'] ?? '';
    $highlights        = (array)($settings['highlights'] ?? []);
    $features          = (array)($settings['features'] ?? []);
    $coverageNote      = $settings['coverage_note'] ?? '';
    $safetyMeasures    = (array)($settings['safety_measures'] ?? []);
    $transportContact  = $settings['transport_contact'] ?? '+91 93507 48851';
    $pdfUrl            = $settings['pdf_url'] ?? '';
@endphp

<style>
.trn-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.trn-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.trn-pg) .pb-row,.pb-section:has(.trn-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.trn-hero{position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.trn-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.28;}
.trn-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.9),rgba(15,56,100,.7));}
.trn-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:70px 24px;text-align:center;color:#fff;}
.trn-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.trn-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.trn-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto;}
/* Highlights */
.trn-stats{background:#fff;border-bottom:1px solid #e2e8f0;padding:28px 24px;}
.trn-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.trn-stat{padding:16px;}.trn-stat .ti{font-size:2rem;margin-bottom:8px;}
.trn-stat .tv{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#0b2545;}
.trn-stat .tl{font-size:.82rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
/* Features */
.trn-features{padding:70px 24px;background:#f8fafc;}
.trn-features-inner{max-width:1200px;margin:0 auto;}
.trn-sec-head{text-align:center;margin-bottom:50px;}
.trn-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.trn-sec-head p{font-size:1rem;color:#64748b;max-width:600px;margin:0 auto;}
.trn-feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;}
.trn-feat-card{background:#fff;border-radius:18px;padding:30px;box-shadow:0 4px 24px -4px rgba(0,0,0,.07);border:1px solid #f1f5f9;transition:all .3s ease;}
.trn-feat-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.trn-feat-icon{font-size:2.2rem;margin-bottom:14px;display:block;}
.trn-feat-card h3{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.trn-feat-card p{font-size:.9rem;color:#475569;line-height:1.7;margin:0;}
/* Safety measures */
.trn-safety{background:linear-gradient(135deg,#0b2545,#0f3864);padding:60px 24px;}
.trn-safety-inner{max-width:860px;margin:0 auto;}
.trn-safety-inner h2{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#fbbf24;text-align:center;margin-bottom:32px;}
.trn-safety-list{display:flex;flex-direction:column;gap:14px;}
.trn-safety-item{display:flex;align-items:flex-start;gap:14px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:16px 20px;}
.trn-safety-item::before{content:"✅";flex-shrink:0;font-size:1.1rem;}
.trn-safety-item span{font-size:.93rem;color:rgba(255,255,255,.9);line-height:1.6;}
/* Coverage & CTA */
.trn-bottom{padding:56px 24px;background:#fff;}
.trn-bottom-inner{max-width:900px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:stretch;}
.trn-coverage{background:#f0f9ff;border:1px solid #bae6fd;border-radius:18px;padding:28px;}
.trn-coverage h3{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:800;color:#0369a1;margin-bottom:12px;}
.trn-coverage p{font-size:.9rem;color:#0c4a6e;line-height:1.7;margin:0;}
.trn-contact-card{background:linear-gradient(135deg,#0b2545,#1e4080);border-radius:18px;padding:28px;text-align:center;color:#fff;}
.trn-contact-card h3{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:800;color:#fbbf24;margin-bottom:16px;}
.trn-contact-phone{font-size:1.4rem;font-weight:800;color:#fff;text-decoration:none;display:block;margin-bottom:16px;}
.trn-pdf-btn{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;padding:12px 24px;border-radius:50px;font-size:.88rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.trn-pdf-btn:hover{background:rgba(255,255,255,.25);}
@media(max-width:640px){.trn-bottom-inner{grid-template-columns:1fr;}.trn-hero__content{padding:52px 20px;}.trn-feat-grid{grid-template-columns:1fr;}}
</style>

<div class="trn-pg">
    {{-- Hero --}}
    <section class="trn-hero">
        @if($heroBg)
            <div class="trn-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="trn-hero__overlay"></div>
        <div class="trn-hero__content">
            <div class="trn-eyebrow">🚌 Safe Commute · GPS Fleet · Trained Staff</div>
            <h1 class="trn-hero__title">{{ $heroTitle }}</h1>
            <p class="trn-hero__sub">{{ $heroSub }}</p>
        </div>
    </section>

    {{-- Stats --}}
    @if(!empty($highlights))
    <div class="trn-stats">
        <div class="trn-stats-grid">
            @foreach($highlights as $h)
            <div class="trn-stat">
                <div class="ti">{{ $h['icon'] ?? '' }}</div>
                <div class="tv">{{ $h['stat'] ?? '' }}</div>
                <div class="tl">{{ $h['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Features --}}
    @if(!empty($features))
    <section class="trn-features">
        <div class="trn-features-inner">
            <div class="trn-sec-head">
                <h2>Our <span style="color:#d97706">Safety-First</span> Transport System</h2>
                <p>Every aspect of our transportation system is designed with child safety, comfort, and reliability at its core.</p>
            </div>
            <div class="trn-feat-grid">
                @foreach($features as $f)
                <div class="trn-feat-card">
                    <span class="trn-feat-icon">{{ $f['icon'] ?? '🚌' }}</span>
                    <h3>{{ $f['title'] ?? '' }}</h3>
                    <p>{{ $f['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Safety Measures --}}
    @if(!empty($safetyMeasures))
    <section class="trn-safety">
        <div class="trn-safety-inner">
            <h2>🛡️ Strict Safety Protocols</h2>
            <div class="trn-safety-list">
                @foreach($safetyMeasures as $m)
                <div class="trn-safety-item"><span>{{ $m }}</span></div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Coverage + Contact --}}
    <section class="trn-bottom">
        <div class="trn-bottom-inner">
            @if($coverageNote)
            <div class="trn-coverage">
                <h3>🗺️ Route Coverage</h3>
                <p>{{ $coverageNote }}</p>
            </div>
            @endif
            <div class="trn-contact-card">
                <h3>📞 Transport Office</h3>
                <a href="tel:{{ preg_replace('/\s+/','',$transportContact) }}" class="trn-contact-phone">{{ $transportContact }}</a>
                @if($pdfUrl)
                    <a href="{{ $pdfUrl }}" target="_blank" class="trn-pdf-btn">📄 View Transport Fee PDF</a>
                @endif
            </div>
        </div>
    </section>
</div>
