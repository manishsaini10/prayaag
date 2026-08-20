{{-- Senior Wing School Page Widget --}}
@php
    $settings           = $settings ?? [];
    $heroTitle          = $settings['hero_title'] ?? "Senior Wing School — Shaping Tomorrow's Leaders";
    $heroSub            = $settings['hero_subtitle'] ?? '';
    $heroBg             = $settings['hero_bg'] ?? '';
    $heroEyebrow        = $settings['hero_eyebrow'] ?? 'Classes VI – XII · CBSE';
    $intro              = $settings['intro'] ?? '';
    $highlights         = (array)($settings['highlights'] ?? []);
    $features           = (array)($settings['features'] ?? []);
    $streams            = (array)($settings['streams'] ?? []);
    $ctaPrimaryLabel    = $settings['cta_primary_label'] ?? 'Admissions Open — Apply Now';
    $ctaPrimaryUrl      = $settings['cta_primary_url'] ?? '/registration';
    $ctaSecondaryLabel  = $settings['cta_secondary_label'] ?? 'Explore Junior Wing';
    $ctaSecondaryUrl    = $settings['cta_secondary_url'] ?? '/junior-wing-school-in-panipat';
@endphp

<style>
.sw-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.sw-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.sw-pg) .pb-row,.pb-section:has(.sw-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.sw-hero{position:relative;min-height:480px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.sw-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.3;}
.sw-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.88),rgba(15,56,100,.72));}
.sw-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:72px 24px;text-align:center;color:#fff;}
.sw-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.35);color:#fbbf24;padding:6px 20px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:20px;}
.sw-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.sw-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto 32px;}
.sw-hero-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;}
.sw-btn-primary{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;padding:14px 32px;border-radius:50px;font-size:.98rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.sw-btn-primary:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(217,119,6,.4);}
.sw-btn-secondary{display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);color:#fff;padding:14px 32px;border-radius:50px;font-size:.98rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.sw-btn-secondary:hover{background:rgba(255,255,255,.22);}
.sw-stats{background:#fff;border-bottom:1px solid #e2e8f0;padding:26px 24px;}
.sw-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.sw-stat{padding:14px;}.sw-stat .si{font-size:2rem;margin-bottom:8px;}
.sw-stat .sv{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:#0b2545;}
.sw-stat .sl{font-size:.8rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
.sw-intro{padding:60px 24px;background:#f8fafc;text-align:center;}
.sw-intro-inner{max-width:840px;margin:0 auto;}
.sw-intro-inner h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:18px;}
.sw-intro-inner p{font-size:1rem;color:#475569;line-height:1.85;}
.sw-features{padding:64px 24px;background:#fff;}
.sw-feat-inner{max-width:1200px;margin:0 auto;}
.sw-sec-head{text-align:center;margin-bottom:48px;}
.sw-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.sw-sec-head p{font-size:.98rem;color:#64748b;max-width:580px;margin:0 auto;}
.sw-feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;}
.sw-feat-card{background:#f8fafc;border-radius:18px;padding:28px;border:1px solid #e2e8f0;transition:all .3s ease;}
.sw-feat-card:hover{background:#fff;transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.sw-feat-icon{font-size:2.2rem;margin-bottom:14px;display:block;}
.sw-feat-card h3{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.sw-feat-card p{font-size:.88rem;color:#475569;line-height:1.7;margin:0;}
.sw-streams{background:#f8fafc;padding:56px 24px;}
.sw-streams-inner{max-width:900px;margin:0 auto;}
.sw-streams-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
.sw-stream-card{background:#fff;border-radius:14px;padding:24px;text-align:center;box-shadow:0 4px 20px -4px rgba(0,0,0,.07);border:2px solid #e2e8f0;transition:all .3s ease;}
.sw-stream-card:hover{border-color:#d97706;transform:translateY(-4px);}
.sw-stream-card h3{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.sw-stream-card p{font-size:.85rem;color:#64748b;line-height:1.6;margin:0;}
.sw-cta{background:linear-gradient(135deg,#0b2545,#0f3864);padding:64px 24px;text-align:center;}
.sw-cta h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#fff;margin-bottom:10px;}
.sw-cta p{font-size:.98rem;color:rgba(255,255,255,.75);margin-bottom:28px;max-width:520px;margin-left:auto;margin-right:auto;}
.sw-cta-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;}
@media(max-width:640px){.sw-feat-grid{grid-template-columns:1fr;}.sw-hero-btns,.sw-cta-btns{flex-direction:column;align-items:center;}.sw-hero__content{padding:52px 20px;}}
</style>

<div class="sw-pg">
    <section class="sw-hero">
        @if($heroBg)
            <div class="sw-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="sw-hero__overlay"></div>
        <div class="sw-hero__content">
            <div class="sw-eyebrow">🏛️ {{ $heroEyebrow }}</div>
            <h1 class="sw-hero__title">{{ $heroTitle }}</h1>
            <p class="sw-hero__sub">{{ $heroSub }}</p>
            <div class="sw-hero-btns">
                <a href="{{ $ctaPrimaryUrl }}" class="sw-btn-primary">🎓 {{ $ctaPrimaryLabel }}</a>
                <a href="{{ $ctaSecondaryUrl }}" class="sw-btn-secondary">{{ $ctaSecondaryLabel }} →</a>
            </div>
        </div>
    </section>

    @if(!empty($highlights))
    <div class="sw-stats">
        <div class="sw-stats-grid">
            @foreach($highlights as $h)
            <div class="sw-stat">
                <div class="si">{{ $h['icon'] ?? '' }}</div>
                <div class="sv">{{ $h['stat'] ?? '' }}</div>
                <div class="sl">{{ $h['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($intro)
    <section class="sw-intro">
        <div class="sw-intro-inner">
            <h2>Academic <span style="color:#d97706">Excellence</span> & Holistic Growth</h2>
            <p>{{ $intro }}</p>
        </div>
    </section>
    @endif

    @if(!empty($features))
    <section class="sw-features">
        <div class="sw-feat-inner">
            <div class="sw-sec-head">
                <h2>What Defines Our <span style="color:#d97706">Senior Wing</span></h2>
                <p>A CBSE-affiliated high school with world-class infrastructure, expert faculty, and a holistic development philosophy.</p>
            </div>
            <div class="sw-feat-grid">
                @foreach($features as $f)
                <div class="sw-feat-card">
                    <span class="sw-feat-icon">{{ $f['icon'] ?? '⭐' }}</span>
                    <h3>{{ $f['title'] ?? '' }}</h3>
                    <p>{{ $f['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(!empty($streams))
    <section class="sw-streams">
        <div class="sw-streams-inner">
            <div class="sw-sec-head">
                <h2>🎓 Academic <span style="color:#d97706">Streams (XI–XII)</span></h2>
                <p>Choose from three comprehensive streams aligned with CBSE curriculum and competitive examination pathways.</p>
            </div>
            <div class="sw-streams-grid">
                @foreach($streams as $s)
                <div class="sw-stream-card">
                    <h3>{{ $s['name'] ?? '' }}</h3>
                    <p>{{ $s['subs'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="sw-cta">
        <h2>Shape Your <span style="color:#fbbf24">Future</span> Here</h2>
        <p>Admissions open for Classes VI through XII. Join Prayaag's Senior Wing — where excellence meets opportunity.</p>
        <div class="sw-cta-btns">
            <a href="{{ $ctaPrimaryUrl }}" class="sw-btn-primary">🎓 {{ $ctaPrimaryLabel }}</a>
            <a href="{{ $ctaSecondaryUrl }}" class="sw-btn-secondary">{{ $ctaSecondaryLabel }}</a>
        </div>
    </section>
</div>
