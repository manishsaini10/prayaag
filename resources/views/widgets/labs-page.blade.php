{{-- Labs & STEM Centres Page Widget --}}
@php
    $settings     = $settings ?? [];
    $heroTitle    = $settings['hero_title'] ?? 'State-of-the-Art Laboratories';
    $heroSub      = $settings['hero_subtitle'] ?? '';
    $heroBg       = $settings['hero_bg'] ?? '';
    $labs         = (array)($settings['labs'] ?? []);
    $highlights   = (array)($settings['highlights'] ?? []);
    $safetyNote   = $settings['safety_note'] ?? '';
@endphp

<style>
.labs-pg { width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b; }
.pb-section:has(.labs-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.labs-pg) .pb-row,.pb-section:has(.labs-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
/* Hero */
.labs-hero{position:relative;min-height:420px;display:flex;align-items:center;overflow:hidden;background:#0b2545;}
.labs-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.3;}
.labs-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.9),rgba(15,56,100,.7));}
.labs-hero__content{position:relative;z-index:2;max-width:800px;margin:0 auto;padding:60px 24px;text-align:center;color:#fff;}
.labs-eyebrow{display:inline-block;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fbbf24;padding:6px 18px;border-radius:50px;font-size:.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:18px;}
.labs-hero__title{font-family:'Playfair Display',serif;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:18px;}
.labs-hero__title span{color:#fbbf24;}
.labs-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:680px;margin:0 auto;}
/* Stats bar */
.labs-stats{background:#fff;border-bottom:1px solid #e2e8f0;padding:28px 24px;}
.labs-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.labs-stat-item{padding:16px;}
.labs-stat-item .ls-icon{font-size:2rem;margin-bottom:8px;}
.labs-stat-item .ls-value{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#0b2545;}
.labs-stat-item .ls-label{font-size:.82rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
/* Labs grid */
.labs-section{padding:70px 24px;background:#f8fafc;}
.labs-section-inner{max-width:1200px;margin:0 auto;}
.labs-sec-head{text-align:center;margin-bottom:52px;}
.labs-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.labs-sec-head p{font-size:1rem;color:#64748b;max-width:580px;margin:0 auto;}
.labs-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:28px;}
.lab-card{background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 4px 24px -4px rgba(0,0,0,.08);transition:all .3s ease;border:1px solid #f1f5f9;}
.lab-card:hover{transform:translateY(-6px);box-shadow:0 16px 48px -12px rgba(11,37,69,.18);}
.lab-card-img{height:200px;overflow:hidden;position:relative;}
.lab-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .5s ease;}
.lab-card:hover .lab-card-img img{transform:scale(1.06);}
.lab-card-badge{position:absolute;top:14px;right:14px;background:rgba(11,37,69,.85);color:#fbbf24;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:700;backdrop-filter:blur(6px);}
.lab-card-body{padding:24px;}
.lab-card-icon{font-size:2rem;margin-bottom:10px;display:block;}
.lab-card-body h3{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.lab-card-body p{font-size:.9rem;color:#475569;line-height:1.7;margin:0;}
/* Safety note */
.labs-safety{background:linear-gradient(135deg,#0b2545,#0f3864);padding:50px 24px;}
.labs-safety-inner{max-width:860px;margin:0 auto;display:flex;gap:24px;align-items:flex-start;}
.labs-safety-icon{font-size:2.5rem;flex-shrink:0;}
.labs-safety-body h3{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:800;color:#fbbf24;margin-bottom:10px;}
.labs-safety-body p{font-size:.95rem;color:rgba(255,255,255,.85);line-height:1.8;margin:0;}
@media(max-width:640px){.labs-grid{grid-template-columns:1fr;}.labs-safety-inner{flex-direction:column;}.labs-hero__content{padding:48px 20px;}}
</style>

<div class="labs-pg">
    {{-- Hero --}}
    <section class="labs-hero">
        @if($heroBg)
            <div class="labs-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="labs-hero__overlay"></div>
        <div class="labs-hero__content">
            <div class="labs-eyebrow">🔬 STEM & Science Infrastructure</div>
            <h1 class="labs-hero__title">{{ $heroTitle }}</h1>
            <p class="labs-hero__sub">{{ $heroSub }}</p>
        </div>
    </section>

    {{-- Stats bar --}}
    @if(!empty($highlights))
    <div class="labs-stats">
        <div class="labs-stats-grid">
            @foreach($highlights as $h)
            <div class="labs-stat-item">
                <div class="ls-icon">{{ $h['icon'] ?? '' }}</div>
                <div class="ls-value">{{ $h['stat'] ?? '' }}</div>
                <div class="ls-label">{{ $h['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Labs Grid --}}
    @if(!empty($labs))
    <section class="labs-section">
        <div class="labs-section-inner">
            <div class="labs-sec-head">
                <h2>Our <span style="color:#d97706">World-Class</span> Laboratories</h2>
                <p>Equipped to inspire curiosity, support CBSE curriculum, and develop the next generation of scientists, engineers, and innovators.</p>
            </div>
            <div class="labs-grid">
                @foreach($labs as $lab)
                <div class="lab-card">
                    <div class="lab-card-img">
                        <img src="{{ $lab['image'] ?? '' }}" alt="{{ $lab['name'] ?? 'Lab' }}" loading="lazy">
                        <span class="lab-card-badge">{{ $lab['badge'] ?? '' }}</span>
                    </div>
                    <div class="lab-card-body">
                        <span class="lab-card-icon">{{ $lab['icon'] ?? '🔬' }}</span>
                        <h3>{{ $lab['name'] ?? '' }}</h3>
                        <p>{{ $lab['desc'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Safety note --}}
    @if($safetyNote)
    <section class="labs-safety">
        <div class="labs-safety-inner">
            <div class="labs-safety-icon">🛡️</div>
            <div class="labs-safety-body">
                <h3>Laboratory Safety Standards</h3>
                <p>{{ $safetyNote }}</p>
            </div>
        </div>
    </section>
    @endif
</div>
