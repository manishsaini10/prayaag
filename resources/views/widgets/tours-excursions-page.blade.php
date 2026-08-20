{{-- Tours & Excursions Page Widget --}}
@php
    $settings          = $settings ?? [];
    $heroTitle         = $settings['hero_title'] ?? 'Tours & Educational Excursions';
    $heroSub           = $settings['hero_subtitle'] ?? '';
    $heroBg            = $settings['hero_bg'] ?? '';
    $programs          = (array)($settings['programs'] ?? []);
    $pastDestinations  = (array)($settings['past_destinations'] ?? []);
    $ctaUrl            = $settings['cta_url'] ?? '/contact';
    $ctaLabel          = $settings['cta_label'] ?? 'Enquire About Upcoming Excursions';
@endphp

<style>
.tx-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.tx-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.tx-pg) .pb-row,.pb-section:has(.tx-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.tx-hero{position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.tx-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.3;}
.tx-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.9),rgba(15,56,100,.7));}
.tx-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:70px 24px;text-align:center;color:#fff;}
.tx-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.tx-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.tx-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto;}
.tx-programs{padding:70px 24px;background:#f8fafc;}
.tx-programs-inner{max-width:1200px;margin:0 auto;}
.tx-sec-head{text-align:center;margin-bottom:50px;}
.tx-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.tx-sec-head p{font-size:1rem;color:#64748b;max-width:600px;margin:0 auto;}
.tx-prog-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;}
.tx-prog-card{background:#fff;border-radius:18px;padding:30px;box-shadow:0 4px 24px -4px rgba(0,0,0,.07);border:1px solid #f1f5f9;transition:all .3s ease;}
.tx-prog-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.tx-prog-icon{font-size:2.2rem;margin-bottom:14px;display:block;}
.tx-prog-card h3{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.tx-prog-card p{font-size:.9rem;color:#475569;line-height:1.7;margin:0;}
.tx-dest-section{background:linear-gradient(135deg,#0b2545,#0f3864);padding:64px 24px;}
.tx-dest-inner{max-width:900px;margin:0 auto;text-align:center;}
.tx-dest-inner h2{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:800;color:#fbbf24;margin-bottom:36px;}
.tx-dest-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;text-align:left;}
.tx-dest-item{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:16px 20px;font-size:.93rem;color:rgba(255,255,255,.9);font-weight:600;}
.tx-cta{padding:60px 24px;background:#fff;text-align:center;}
.tx-cta h2{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.tx-cta p{font-size:.95rem;color:#64748b;max-width:500px;margin:0 auto 24px;}
.tx-cta-btn{display:inline-flex;align-items:center;gap:10px;background:#0b2545;color:#fff;padding:15px 36px;border-radius:50px;font-size:1rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.tx-cta-btn:hover{background:#d97706;transform:translateY(-2px);}
@media(max-width:640px){.tx-prog-grid{grid-template-columns:1fr;}.tx-hero__content{padding:52px 20px;}}
</style>

<div class="tx-pg">
    <section class="tx-hero">
        @if($heroBg)
            <div class="tx-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="tx-hero__overlay"></div>
        <div class="tx-hero__content">
            <div class="tx-eyebrow">🌍 Beyond Classroom Boundaries</div>
            <h1 class="tx-hero__title">{{ $heroTitle }}</h1>
            <p class="tx-hero__sub">{{ $heroSub }}</p>
        </div>
    </section>

    @if(!empty($programs))
    <section class="tx-programs">
        <div class="tx-programs-inner">
            <div class="tx-sec-head">
                <h2>Our <span style="color:#d97706">Excursion Programs</span></h2>
                <p>Carefully curated journeys that complement classroom learning and build real-world awareness, cultural sensitivity, and global citizenship.</p>
            </div>
            <div class="tx-prog-grid">
                @foreach($programs as $p)
                <div class="tx-prog-card">
                    <span class="tx-prog-icon">{{ $p['icon'] ?? '🌍' }}</span>
                    <h3>{{ $p['title'] ?? '' }}</h3>
                    <p>{{ $p['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if(!empty($pastDestinations))
    <section class="tx-dest-section">
        <div class="tx-dest-inner">
            <h2>🗺️ Past Excursion Destinations</h2>
            <div class="tx-dest-grid">
                @foreach($pastDestinations as $d)
                <div class="tx-dest-item">{{ $d }}</div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="tx-cta">
        <h2>Plan Your Next <span style="color:#d97706">Adventure</span></h2>
        <p>Interested in upcoming excursions or international exchange programs? Get in touch with our activity coordinator.</p>
        <a href="{{ $ctaUrl }}" class="tx-cta-btn">{{ $ctaLabel }} →</a>
    </section>
</div>
