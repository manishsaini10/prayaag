{{-- Safety & Security Page Widget --}}
@php
    $settings   = $settings ?? [];
    $heroTitle  = $settings['hero_title'] ?? 'Campus Safety & Security';
    $heroSub    = $settings['hero_subtitle'] ?? '';
    $heroBg     = $settings['hero_bg'] ?? '';
    $stats      = (array)($settings['stats'] ?? []);
    $features   = (array)($settings['features'] ?? []);
    $policyNote = $settings['policy_note'] ?? '';
@endphp

<style>
.ss-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.ss-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.ss-pg) .pb-row,.pb-section:has(.ss-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.ss-hero{position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.ss-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.28;}
.ss-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.9),rgba(15,56,100,.7));}
.ss-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:70px 24px;text-align:center;color:#fff;}
.ss-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.ss-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.ss-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto;}
.ss-stats-bar{background:#fff;border-bottom:1px solid #e2e8f0;padding:28px 24px;}
.ss-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.ss-stat{padding:16px;}.ss-stat .si{font-size:2rem;margin-bottom:8px;}
.ss-stat .sv{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#0b2545;}
.ss-stat .sl{font-size:.82rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
.ss-features{padding:70px 24px;background:#f8fafc;}
.ss-feat-inner{max-width:1200px;margin:0 auto;}
.ss-sec-head{text-align:center;margin-bottom:50px;}
.ss-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.ss-sec-head p{font-size:1rem;color:#64748b;max-width:600px;margin:0 auto;}
.ss-feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;}
.ss-feat-card{background:#fff;border-radius:18px;padding:30px;box-shadow:0 4px 24px -4px rgba(0,0,0,.07);border:1px solid #f1f5f9;transition:all .3s ease;}
.ss-feat-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.ss-feat-icon{font-size:2.2rem;margin-bottom:14px;display:block;}
.ss-feat-card h3{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.ss-feat-card p{font-size:.9rem;color:#475569;line-height:1.7;margin:0;}
.ss-policy{background:linear-gradient(135deg,#0b2545,#0f3864);padding:56px 24px;}
.ss-policy-inner{max-width:860px;margin:0 auto;display:flex;gap:24px;align-items:flex-start;}
.ss-policy-icon{font-size:2.5rem;flex-shrink:0;}
.ss-policy-body h3{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:#fbbf24;margin-bottom:12px;}
.ss-policy-body p{font-size:.95rem;color:rgba(255,255,255,.85);line-height:1.8;margin:0;}
@media(max-width:640px){.ss-feat-grid{grid-template-columns:1fr;}.ss-policy-inner{flex-direction:column;}.ss-hero__content{padding:52px 20px;}}
</style>

<div class="ss-pg">
    <section class="ss-hero">
        @if($heroBg)
            <div class="ss-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="ss-hero__overlay"></div>
        <div class="ss-hero__content">
            <div class="ss-eyebrow">🛡️ CCTV · Secure Perimeter · Medical Care</div>
            <h1 class="ss-hero__title">{{ $heroTitle }}</h1>
            <p class="ss-hero__sub">{{ $heroSub }}</p>
        </div>
    </section>

    @if(!empty($stats))
    <div class="ss-stats-bar">
        <div class="ss-stats-grid">
            @foreach($stats as $s)
            <div class="ss-stat">
                <div class="si">{{ $s['icon'] ?? '' }}</div>
                <div class="sv">{{ $s['value'] ?? '' }}</div>
                <div class="sl">{{ $s['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if(!empty($features))
    <section class="ss-features">
        <div class="ss-feat-inner">
            <div class="ss-sec-head">
                <h2>Multi-Layer <span style="color:#d97706">Security Ecosystem</span></h2>
                <p>Every element of our campus security system is thoughtfully designed to protect every child, teacher, and staff member — every day.</p>
            </div>
            <div class="ss-feat-grid">
                @foreach($features as $f)
                <div class="ss-feat-card">
                    <span class="ss-feat-icon">{{ $f['icon'] ?? '🛡️' }}</span>
                    <h3>{{ $f['title'] ?? '' }}</h3>
                    <p>{{ $f['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($policyNote)
    <section class="ss-policy">
        <div class="ss-policy-inner">
            <div class="ss-policy-icon">⚖️</div>
            <div class="ss-policy-body">
                <h3>Regulatory Compliance & Child Safety Policy</h3>
                <p>{{ $policyNote }}</p>
            </div>
        </div>
    </section>
    @endif
</div>
