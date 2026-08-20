{{-- UNESCO & Global Citizenship Page Widget --}}
@php
    $settings    = $settings ?? [];
    $heroTitle   = $settings['hero_title'] ?? 'UNESCO & Global Citizenship';
    $heroSub     = $settings['hero_subtitle'] ?? '';
    $heroBg      = $settings['hero_bg'] ?? '';
    $intro       = $settings['intro'] ?? '';
    $objectives  = (array)($settings['objectives'] ?? []);
    $activities  = (array)($settings['activities'] ?? []);
    $networkNote = $settings['network_note'] ?? '';
@endphp

<style>
.unsc-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.unsc-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.unsc-pg) .pb-row,.pb-section:has(.unsc-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.unsc-hero{position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.unsc-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.28;}
.unsc-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.9),rgba(15,56,100,.7));}
.unsc-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:70px 24px;text-align:center;color:#fff;}
.unsc-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.unsc-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.unsc-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto;}
.unsc-intro-section{padding:64px 24px;background:#fff;}
.unsc-intro-inner{max-width:860px;margin:0 auto;text-align:center;}
.unsc-intro-inner p{font-size:1.05rem;color:#1e293b;line-height:1.8;}
.unsc-objectives{padding:64px 24px;background:#f8fafc;}
.unsc-obj-inner{max-width:1000px;margin:0 auto;}
.unsc-sec-head{text-align:center;margin-bottom:48px;}
.unsc-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.unsc-sec-head p{font-size:.98rem;color:#64748b;max-width:600px;margin:0 auto;}
.unsc-obj-list{display:flex;flex-direction:column;gap:16px;}
.unsc-obj-item{display:flex;gap:16px;align-items:flex-start;background:#fff;border-radius:14px;padding:20px 24px;box-shadow:0 4px 20px -4px rgba(0,0,0,.06);border:1px solid #f1f5f9;}
.unsc-obj-num{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0b2545,#1e4080);color:#fbbf24;font-weight:800;font-size:.9rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.unsc-obj-item p{font-size:.92rem;color:#334155;line-height:1.65;margin:0;}
.unsc-activities{background:linear-gradient(135deg,#0b2545,#0f3864);padding:64px 24px;}
.unsc-act-inner{max-width:1100px;margin:0 auto;}
.unsc-act-inner .unsc-sec-head h2{color:#fbbf24;}
.unsc-act-inner .unsc-sec-head p{color:rgba(255,255,255,.7);}
.unsc-act-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:24px;}
.unsc-act-card{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:28px;backdrop-filter:blur(8px);}
.unsc-act-icon{font-size:2rem;margin-bottom:12px;display:block;}
.unsc-act-card h3{font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:800;color:#fbbf24;margin-bottom:10px;}
.unsc-act-card p{font-size:.88rem;color:rgba(255,255,255,.82);line-height:1.65;margin:0;}
.unsc-network{padding:56px 24px;background:#fff;text-align:center;}
.unsc-network-badge{display:inline-flex;align-items:center;gap:12px;background:linear-gradient(135deg,#0b2545,#1e4080);color:#fff;padding:20px 36px;border-radius:16px;font-size:.98rem;font-weight:700;margin-bottom:20px;}
.unsc-network p{font-size:.95rem;color:#475569;line-height:1.7;max-width:680px;margin:0 auto;}
@media(max-width:640px){.unsc-act-grid{grid-template-columns:1fr;}.unsc-hero__content{padding:52px 20px;}.unsc-obj-item{flex-direction:column;}}
</style>

<div class="unsc-pg">
    <section class="unsc-hero">
        @if($heroBg)
            <div class="unsc-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="unsc-hero__overlay"></div>
        <div class="unsc-hero__content">
            <div class="unsc-eyebrow">🕊️ UNESCO ASPNet · Global Citizenship</div>
            <h1 class="unsc-hero__title">{{ $heroTitle }}</h1>
            <p class="unsc-hero__sub">{{ $heroSub }}</p>
        </div>
    </section>

    @if($intro)
    <section class="unsc-intro-section">
        <div class="unsc-intro-inner"><p>{{ $intro }}</p></div>
    </section>
    @endif

    @if(!empty($objectives))
    <section class="unsc-objectives">
        <div class="unsc-obj-inner">
            <div class="unsc-sec-head">
                <h2>Our UNESCO <span style="color:#d97706">Club Objectives</span></h2>
                <p>Core principles and mission activities that guide our participation in the UNESCO Associated Schools Programme Network.</p>
            </div>
            <div class="unsc-obj-list">
                @foreach($objectives as $i => $obj)
                <div class="unsc-obj-item">
                    <div class="unsc-obj-num">{{ $i + 1 }}</div>
                    <p>{{ $obj }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(!empty($activities))
    <section class="unsc-activities">
        <div class="unsc-act-inner">
            <div class="unsc-sec-head">
                <h2>UNESCO in Action</h2>
                <p>Programs and initiatives where our students actively contribute to UNESCO's global mission.</p>
            </div>
            <div class="unsc-act-grid">
                @foreach($activities as $a)
                <div class="unsc-act-card">
                    <span class="unsc-act-icon">{{ $a['icon'] ?? '🌍' }}</span>
                    <h3>{{ $a['title'] ?? '' }}</h3>
                    <p>{{ $a['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($networkNote)
    <section class="unsc-network">
        <div class="unsc-network-badge">🌐 UNESCO ASPNet Member</div>
        <p>{{ $networkNote }}</p>
    </section>
    @endif
</div>
