{{-- Sports Arena & Athletic Excellence Page Widget --}}
@php
    $settings      = $settings ?? [];
    $heroTitle     = $settings['hero_title'] ?? 'Sports Arena & Athletic Excellence';
    $heroSub       = $settings['hero_subtitle'] ?? '';
    $heroBg        = $settings['hero_bg'] ?? '';
    $sports        = (array)($settings['sports'] ?? []);
    $achievements  = (array)($settings['achievements'] ?? []);
    $coaches       = (array)($settings['coaches'] ?? []);
    $sportsDayNote = $settings['sports_day_note'] ?? '';
    $ctaUrl        = $settings['cta_url'] ?? '/admissions';
    $ctaLabel      = $settings['cta_label'] ?? 'Enroll Your Champion';
@endphp

<style>
.spt-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.spt-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.spt-pg) .pb-row,.pb-section:has(.spt-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.spt-hero{position:relative;min-height:460px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.spt-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center top;opacity:.32;}
.spt-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.88),rgba(15,56,100,.7));}
.spt-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:70px 24px;text-align:center;color:#fff;}
.spt-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.spt-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.spt-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto 32px;}
.spt-cta-btn{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;padding:14px 34px;border-radius:50px;font-size:1rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.spt-cta-btn:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(217,119,6,.4);}
/* Sports grid */
.spt-sports-section{padding:70px 24px;background:#f8fafc;}
.spt-sports-inner{max-width:1200px;margin:0 auto;}
.spt-sec-head{text-align:center;margin-bottom:50px;}
.spt-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.spt-sec-head p{font-size:1rem;color:#64748b;max-width:600px;margin:0 auto;}
.spt-sports-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;}
.spt-sport-card{background:#fff;border-radius:16px;padding:26px;box-shadow:0 4px 20px -4px rgba(0,0,0,.07);border:1px solid #f1f5f9;transition:all .3s ease;display:flex;gap:16px;align-items:flex-start;}
.spt-sport-card:hover{transform:translateY(-4px);box-shadow:0 14px 40px -10px rgba(11,37,69,.15);}
.spt-sport-icon{font-size:2.2rem;flex-shrink:0;}
.spt-sport-body h3{font-weight:800;color:#0b2545;font-size:1.05rem;margin-bottom:8px;}
.spt-sport-body p{font-size:.88rem;color:#475569;line-height:1.65;margin:0;}
/* Achievements */
.spt-achiev-section{background:linear-gradient(135deg,#0b2545,#0f3864);padding:64px 24px;}
.spt-achiev-inner{max-width:900px;margin:0 auto;text-align:center;}
.spt-achiev-inner h2{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:800;color:#fbbf24;margin-bottom:36px;}
.spt-achiev-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;text-align:left;}
.spt-achiev-item{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:16px 20px;font-size:.93rem;color:rgba(255,255,255,.9);font-weight:600;backdrop-filter:blur(6px);}
/* Coaches */
.spt-coaches-section{padding:64px 24px;background:#f8fafc;}
.spt-coaches-inner{max-width:900px;margin:0 auto;}
.spt-coaches-inner .spt-sec-head{margin-bottom:40px;}
.spt-coaches-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;}
.spt-coach-card{background:#fff;border-radius:14px;padding:24px;text-align:center;box-shadow:0 4px 20px -4px rgba(0,0,0,.07);border:1px solid #f1f5f9;}
.spt-coach-avatar{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#0b2545,#1e4080);display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin:0 auto 14px;}
.spt-coach-card h4{font-weight:800;color:#0b2545;font-size:.98rem;margin-bottom:4px;}
.spt-coach-card .sport{font-size:.82rem;color:#d97706;font-weight:700;}
.spt-coach-card .exp{font-size:.78rem;color:#64748b;margin-top:4px;}
/* Sports day */
.spt-day-section{background:#fff;padding:50px 24px;border-top:1px solid #f1f5f9;}
.spt-day-inner{max-width:800px;margin:0 auto;display:flex;gap:24px;align-items:flex-start;background:linear-gradient(135deg,#fef9ec,#fef3c7);border:1px solid #fde68a;border-radius:18px;padding:32px;}
.spt-day-icon{font-size:3rem;flex-shrink:0;}
.spt-day-body h3{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:#92400e;margin-bottom:10px;}
.spt-day-body p{font-size:.95rem;color:#78350f;line-height:1.7;margin:0;}
@media(max-width:640px){.spt-sports-grid{grid-template-columns:1fr;}.spt-sport-card{flex-direction:column;}.spt-day-inner{flex-direction:column;}.spt-hero__content{padding:52px 20px;}}
</style>

<div class="spt-pg">
    {{-- Hero --}}
    <section class="spt-hero">
        @if($heroBg)
            <div class="spt-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="spt-hero__overlay"></div>
        <div class="spt-hero__content">
            <div class="spt-eyebrow">⚽ Sports · Fitness · Championships</div>
            <h1 class="spt-hero__title">{{ $heroTitle }}</h1>
            <p class="spt-hero__sub">{{ $heroSub }}</p>
            @if($ctaLabel && $ctaUrl)
                <a href="{{ $ctaUrl }}" class="spt-cta-btn">🏆 {{ $ctaLabel }}</a>
            @endif
        </div>
    </section>

    {{-- Sports Grid --}}
    @if(!empty($sports))
    <section class="spt-sports-section">
        <div class="spt-sports-inner">
            <div class="spt-sec-head">
                <h2>World-Class <span style="color:#d97706">Sports Infrastructure</span></h2>
                <p>State-of-the-art outdoor and indoor sporting facilities built to develop champions and nurture a sporting culture from an early age.</p>
            </div>
            <div class="spt-sports-grid">
                @foreach($sports as $s)
                <div class="spt-sport-card">
                    <div class="spt-sport-icon">{{ $s['icon'] ?? '🏅' }}</div>
                    <div class="spt-sport-body">
                        <h3>{{ $s['name'] ?? '' }}</h3>
                        <p>{{ $s['desc'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Achievements --}}
    @if(!empty($achievements))
    <section class="spt-achiev-section">
        <div class="spt-achiev-inner">
            <h2>🏆 Our Championship Achievements</h2>
            <div class="spt-achiev-list">
                @foreach($achievements as $a)
                <div class="spt-achiev-item">{{ $a }}</div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Coaching Staff --}}
    @if(!empty($coaches))
    <section class="spt-coaches-section">
        <div class="spt-coaches-inner">
            <div class="spt-sec-head">
                <h2>Meet Our <span style="color:#d97706">Coaching Staff</span></h2>
                <p>Experienced, passionate, and dedicated coaches committed to unlocking every student's athletic potential.</p>
            </div>
            <div class="spt-coaches-grid">
                @foreach($coaches as $c)
                <div class="spt-coach-card">
                    <div class="spt-coach-avatar">👨‍🏫</div>
                    <h4>{{ $c['name'] ?? '' }}</h4>
                    <div class="sport">{{ $c['sport'] ?? '' }}</div>
                    <div class="exp">Experience: {{ $c['exp'] ?? '' }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Sports Day --}}
    @if($sportsDayNote)
    <section class="spt-day-section">
        <div class="spt-day-inner">
            <div class="spt-day-icon">🎉</div>
            <div class="spt-day-body">
                <h3>Annual Sports Day</h3>
                <p>{{ $sportsDayNote }}</p>
            </div>
        </div>
    </section>
    @endif
</div>
