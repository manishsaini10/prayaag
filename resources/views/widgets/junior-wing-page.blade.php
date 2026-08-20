{{-- Junior Wing School Page Widget --}}
@php
    $settings           = $settings ?? [];
    $heroTitle          = $settings['hero_title'] ?? 'Junior Wing School — Nurturing Young Minds';
    $heroSub            = $settings['hero_subtitle'] ?? '';
    $heroBg             = $settings['hero_bg'] ?? '';
    $heroEyebrow        = $settings['hero_eyebrow'] ?? 'Pre-Nursery to Class V';
    $intro              = $settings['intro'] ?? '';
    $highlights         = (array)($settings['highlights'] ?? []);
    $features           = (array)($settings['features'] ?? []);
    $classes            = $settings['classes'] ?? '';
    $ctaPrimaryLabel    = $settings['cta_primary_label'] ?? 'Admissions Open — Apply Now';
    $ctaPrimaryUrl      = $settings['cta_primary_url'] ?? '/registration';
    $ctaSecondaryLabel  = $settings['cta_secondary_label'] ?? 'Explore Senior Wing';
    $ctaSecondaryUrl    = $settings['cta_secondary_url'] ?? '/senior-wing-school-in-panipat';
@endphp

<style>
.jw-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.jw-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.jw-pg) .pb-row,.pb-section:has(.jw-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.jw-hero{position:relative;min-height:480px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.jw-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.32;}
.jw-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.88),rgba(15,56,100,.72));}
.jw-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:72px 24px;text-align:center;color:#fff;}
.jw-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.35);color:#fbbf24;padding:6px 20px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:20px;}
.jw-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.jw-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto 32px;}
.jw-hero-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;}
.jw-btn-primary{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;padding:14px 32px;border-radius:50px;font-size:.98rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.jw-btn-primary:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(217,119,6,.4);}
.jw-btn-secondary{display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);color:#fff;padding:14px 32px;border-radius:50px;font-size:.98rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.jw-btn-secondary:hover{background:rgba(255,255,255,.22);}
/* Stats bar */
.jw-stats{background:#fff;border-bottom:1px solid #e2e8f0;padding:26px 24px;}
.jw-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.jw-stat{padding:14px;}.jw-stat .ji{font-size:2rem;margin-bottom:8px;}
.jw-stat .jv{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:#0b2545;}
.jw-stat .jl{font-size:.8rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
/* Intro */
.jw-intro{padding:60px 24px;background:#f8fafc;text-align:center;}
.jw-intro-inner{max-width:840px;margin:0 auto;}
.jw-intro-inner h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:18px;}
.jw-intro-inner p{font-size:1rem;color:#475569;line-height:1.85;}
/* Features */
.jw-features{padding:64px 24px;background:#fff;}
.jw-feat-inner{max-width:1200px;margin:0 auto;}
.jw-sec-head{text-align:center;margin-bottom:48px;}
.jw-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.jw-sec-head p{font-size:.98rem;color:#64748b;max-width:580px;margin:0 auto;}
.jw-feat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;}
.jw-feat-card{background:#f8fafc;border-radius:18px;padding:28px;border:1px solid #e2e8f0;transition:all .3s ease;}
.jw-feat-card:hover{background:#fff;transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.jw-feat-icon{font-size:2.2rem;margin-bottom:14px;display:block;}
.jw-feat-card h3{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.jw-feat-card p{font-size:.88rem;color:#475569;line-height:1.7;margin:0;}
/* CTA */
.jw-cta{background:linear-gradient(135deg,#0b2545,#0f3864);padding:64px 24px;text-align:center;}
.jw-cta h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#fff;margin-bottom:10px;}
.jw-cta p{font-size:.98rem;color:rgba(255,255,255,.75);margin-bottom:28px;max-width:520px;margin-left:auto;margin-right:auto;}
@if($classes).jw-classes-badge{display:inline-block;background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.8rem;font-weight:700;margin-bottom:20px;}@endif
.jw-cta-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;}
@media(max-width:640px){.jw-feat-grid{grid-template-columns:1fr;}.jw-hero-btns{flex-direction:column;align-items:center;}.jw-hero__content{padding:52px 20px;}}
</style>

<div class="jw-pg">
    {{-- Hero --}}
    <section class="jw-hero">
        @if($heroBg)
            <div class="jw-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="jw-hero__overlay"></div>
        <div class="jw-hero__content">
            <div class="jw-eyebrow">🏫 {{ $heroEyebrow }}</div>
            <h1 class="jw-hero__title">{{ $heroTitle }}</h1>
            <p class="jw-hero__sub">{{ $heroSub }}</p>
            <div class="jw-hero-btns">
                <a href="{{ $ctaPrimaryUrl }}" class="jw-btn-primary">🎓 {{ $ctaPrimaryLabel }}</a>
                <a href="{{ $ctaSecondaryUrl }}" class="jw-btn-secondary">{{ $ctaSecondaryLabel }} →</a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    @if(!empty($highlights))
    <div class="jw-stats">
        <div class="jw-stats-grid">
            @foreach($highlights as $h)
            <div class="jw-stat">
                <div class="ji">{{ $h['icon'] ?? '' }}</div>
                <div class="jv">{{ $h['stat'] ?? '' }}</div>
                <div class="jl">{{ $h['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Intro --}}
    @if($intro)
    <section class="jw-intro">
        <div class="jw-intro-inner">
            <h2>Nurturing <span style="color:#d97706">Young Minds</span> Since Day One</h2>
            <p>{{ $intro }}</p>
        </div>
    </section>
    @endif

    {{-- Features --}}
    @if(!empty($features))
    <section class="jw-features">
        <div class="jw-feat-inner">
            <div class="jw-sec-head">
                <h2>What Makes Our <span style="color:#d97706">Junior Wing</span> Special</h2>
                <p>A self-contained, purpose-built environment that ensures every young learner thrives academically, creatively, and socially.</p>
            </div>
            <div class="jw-feat-grid">
                @foreach($features as $f)
                <div class="jw-feat-card">
                    <span class="jw-feat-icon">{{ $f['icon'] ?? '⭐' }}</span>
                    <h3>{{ $f['title'] ?? '' }}</h3>
                    <p>{{ $f['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA --}}
    <section class="jw-cta">
        @if($classes)<div class="jw-classes-badge">📚 {{ $classes }}</div>@endif
        <h2>Give Your Child the <span style="color:#fbbf24">Best Start</span></h2>
        <p>Admissions open for Pre-Nursery through Class V. Join the Prayaag family today.</p>
        <div class="jw-cta-btns">
            <a href="{{ $ctaPrimaryUrl }}" class="jw-btn-primary">🎓 {{ $ctaPrimaryLabel }}</a>
            <a href="{{ $ctaSecondaryUrl }}" class="jw-btn-secondary">{{ $ctaSecondaryLabel }}</a>
        </div>
    </section>
</div>
