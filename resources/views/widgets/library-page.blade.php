{{-- Library & Learning Resource Centre Page Widget --}}
@php
    $settings   = $settings ?? [];
    $heroTitle  = $settings['hero_title'] ?? 'Library & Learning Resource Centre';
    $heroSub    = $settings['hero_subtitle'] ?? '';
    $heroBg     = $settings['hero_bg'] ?? '';
    $stats      = (array)($settings['stats'] ?? []);
    $sections   = (array)($settings['sections'] ?? []);
    $timings    = (array)($settings['timings'] ?? []);
    $ctaUrl     = $settings['cta_url'] ?? '/contact';
    $ctaLabel   = $settings['cta_label'] ?? 'Contact Library Team';
@endphp

<style>
.lib-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.lib-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.lib-pg) .pb-row,.pb-section:has(.lib-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.lib-hero{position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.lib-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.28;}
.lib-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.9),rgba(15,56,100,.7));}
.lib-hero__content{position:relative;z-index:2;max-width:800px;margin:0 auto;padding:60px 24px;text-align:center;color:#fff;}
.lib-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.lib-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.lib-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:680px;margin:0 auto;}
/* Stats */
.lib-stats-bar{background:#fff;border-bottom:1px solid #e2e8f0;padding:28px 24px;}
.lib-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.lib-stat{padding:16px;}
.lib-stat .stat-icon{font-size:2rem;margin-bottom:8px;}
.lib-stat .stat-val{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#0b2545;}
.lib-stat .stat-lbl{font-size:.82rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
/* Main content */
.lib-main{padding:70px 24px;background:#f8fafc;}
.lib-main-inner{max-width:1200px;margin:0 auto;}
.lib-sec-head{text-align:center;margin-bottom:52px;}
.lib-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.lib-sec-head p{font-size:1rem;color:#64748b;max-width:600px;margin:0 auto;}
.lib-features-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(340px,1fr));gap:28px;}
.lib-feat-card{background:#fff;border-radius:18px;padding:30px;box-shadow:0 4px 24px -4px rgba(0,0,0,.07);border:1px solid #f1f5f9;transition:all .3s ease;}
.lib-feat-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.lib-feat-icon{font-size:2rem;margin-bottom:14px;display:block;}
.lib-feat-card h3{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.lib-feat-card p{font-size:.9rem;color:#475569;line-height:1.7;margin:0;}
/* Timings */
.lib-timings-section{background:linear-gradient(135deg,#0b2545,#0f3864);padding:56px 24px;}
.lib-timings-inner{max-width:800px;margin:0 auto;text-align:center;}
.lib-timings-inner h2{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#fbbf24;margin-bottom:30px;}
.lib-timings-table{width:100%;border-collapse:collapse;background:rgba(255,255,255,.07);border-radius:14px;overflow:hidden;}
.lib-timings-table th{background:rgba(255,255,255,.1);color:#fff;font-size:.82rem;text-transform:uppercase;letter-spacing:.06em;padding:14px 20px;text-align:left;}
.lib-timings-table td{padding:14px 20px;color:rgba(255,255,255,.88);border-top:1px solid rgba(255,255,255,.08);font-size:.93rem;}
.lib-cta-section{padding:50px 24px;background:#fff;text-align:center;}
.lib-cta-btn{display:inline-flex;align-items:center;gap:10px;background:#0b2545;color:#fff;padding:15px 36px;border-radius:50px;font-size:1rem;font-weight:700;text-decoration:none;transition:all .25s ease;margin-top:10px;}
.lib-cta-btn:hover{background:#d97706;transform:translateY(-2px);}
@media(max-width:640px){.lib-features-grid{grid-template-columns:1fr;}.lib-hero__content{padding:48px 20px;}}
</style>

<div class="lib-pg">
    {{-- Hero --}}
    <section class="lib-hero">
        @if($heroBg)
            <div class="lib-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <div class="lib-eyebrow">📚 Knowledge Hub & Reading Centre</div>
            <h1 class="lib-hero__title">{{ $heroTitle }}</h1>
            <p class="lib-hero__sub">{{ $heroSub }}</p>
        </div>
    </section>

    {{-- Stats bar --}}
    @if(!empty($stats))
    <div class="lib-stats-bar">
        <div class="lib-stats-grid">
            @foreach($stats as $s)
            <div class="lib-stat">
                <div class="stat-icon">{{ $s['icon'] ?? '' }}</div>
                <div class="stat-val">{{ $s['value'] ?? '' }}</div>
                <div class="stat-lbl">{{ $s['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Features --}}
    @if(!empty($sections))
    <section class="lib-main">
        <div class="lib-main-inner">
            <div class="lib-sec-head">
                <h2>Everything a <span style="color:#d97706">Young Reader</span> Needs</h2>
                <p>A world-class library ecosystem designed to inspire lifelong learning, curiosity, and a deep love of books.</p>
            </div>
            <div class="lib-features-grid">
                @foreach($sections as $sec)
                <div class="lib-feat-card">
                    <span class="lib-feat-icon">{{ $sec['icon'] ?? '📖' }}</span>
                    <h3>{{ $sec['title'] ?? '' }}</h3>
                    <p>{{ $sec['desc'] ?? '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Timings --}}
    @if(!empty($timings))
    <section class="lib-timings-section">
        <div class="lib-timings-inner">
            <h2>📅 Library Opening Hours</h2>
            <table class="lib-timings-table">
                <thead>
                    <tr><th>Day</th><th>Timings</th></tr>
                </thead>
                <tbody>
                    @foreach($timings as $t)
                    <tr>
                        <td>{{ $t['day'] ?? '' }}</td>
                        <td><strong>{{ $t['time'] ?? '' }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- CTA --}}
    <section class="lib-cta-section">
        <p style="font-size:.95rem;color:#64748b;margin-bottom:8px;">Have questions about the library? Get in touch with our library team.</p>
        <a href="{{ $ctaUrl }}" class="lib-cta-btn">{{ $ctaLabel }} →</a>
    </section>
</div>
