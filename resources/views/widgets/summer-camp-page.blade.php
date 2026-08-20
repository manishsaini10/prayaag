{{-- Summer Camp Page Widget --}}
@php
    $settings           = $settings ?? [];
    $heroTitle          = $settings['hero_title'] ?? 'Summer Camp Adventure Awaits!';
    $heroSub            = $settings['hero_subtitle'] ?? '';
    $heroBg             = $settings['hero_bg'] ?? '';
    $year               = $settings['year'] ?? '2025';
    $dates              = $settings['dates'] ?? '';
    $timings            = $settings['timings'] ?? '';
    $charges            = $settings['charges'] ?? '';
    $about              = $settings['about'] ?? '';
    $highlights         = (array)($settings['highlights'] ?? []);
    $activityCategories = (array)($settings['activity_categories'] ?? []);
    $schedule           = (array)($settings['schedule'] ?? []);
    $note               = $settings['note'] ?? '';
    $ctaLabel           = $settings['cta_label'] ?? 'Register for Summer Camp';
    $ctaUrl             = $settings['cta_url'] ?? '/contact-us';
@endphp

<style>
.sc-pg{width:100vw;position:relative;left:50%;margin-left:-50vw;margin-right:-50vw;overflow-x:hidden;background:#f8fafc;font-family:'Plus Jakarta Sans',system-ui,sans-serif;color:#1e293b;}
.pb-section:has(.sc-pg),.pb-section--full-width{padding:0!important;max-width:100%!important;width:100%!important;}
.pb-section:has(.sc-pg) .pb-row,.pb-section:has(.sc-pg) .pb-col{padding:0!important;margin:0!important;max-width:100%!important;width:100%!important;}
.sc-hero{position:relative;min-height:480px;display:flex;align-items:center;overflow:hidden;background:linear-gradient(135deg,#1e3a5f,#0f2040);}
.sc-hero__bg{position:absolute;inset:0;background-size:cover;background-position:center;opacity:.35;}
.sc-hero__overlay{position:absolute;inset:0;background:linear-gradient(135deg,rgba(11,37,69,.88),rgba(30,58,95,.7));}
.sc-hero__content{position:relative;z-index:2;max-width:820px;margin:0 auto;padding:72px 24px;text-align:center;color:#fff;}
.sc-eyebrow{display:inline-block;background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.4);color:#fbbf24;padding:6px 20px;border-radius:50px;font-size:.8rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:20px;}
.sc-hero__title{font-family:'Playfair Display',serif;font-size:clamp(2rem,4.5vw,3.2rem);font-weight:800;line-height:1.15;margin-bottom:18px;}
.sc-hero__sub{font-size:clamp(.95rem,1.5vw,1.1rem);line-height:1.8;opacity:.88;max-width:700px;margin:0 auto 32px;}
.sc-hero-info{display:flex;gap:24px;justify-content:center;flex-wrap:wrap;}
.sc-hero-badge{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);padding:10px 20px;border-radius:50px;font-size:.9rem;font-weight:700;color:#fff;backdrop-filter:blur(8px);}
/* Stats */
.sc-stats{background:#fff;border-bottom:1px solid #e2e8f0;padding:28px 24px;}
.sc-stats-grid{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:20px;text-align:center;}
.sc-stat{padding:16px;}.sc-stat .si{font-size:2rem;margin-bottom:8px;}
.sc-stat .sv{font-family:'Playfair Display',serif;font-size:1.6rem;font-weight:800;color:#0b2545;}
.sc-stat .sl{font-size:.8rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;}
/* About */
.sc-about{padding:60px 24px;background:#f8fafc;text-align:center;}
.sc-about-inner{max-width:820px;margin:0 auto;}
.sc-about-inner h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:18px;}
.sc-about-inner p{font-size:1rem;color:#475569;line-height:1.85;}
/* Activities */
.sc-activities{padding:64px 24px;background:#fff;}
.sc-act-inner{max-width:1200px;margin:0 auto;}
.sc-sec-head{text-align:center;margin-bottom:48px;}
.sc-sec-head h2{font-family:'Playfair Display',serif;font-size:clamp(1.5rem,3vw,2.2rem);font-weight:800;color:#0b2545;margin-bottom:12px;}
.sc-sec-head p{font-size:.98rem;color:#64748b;max-width:580px;margin:0 auto;}
.sc-act-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;}
.sc-act-card{background:#f8fafc;border-radius:18px;padding:28px;border:1px solid #e2e8f0;transition:all .3s ease;}
.sc-act-card:hover{background:#fff;transform:translateY(-4px);box-shadow:0 16px 48px -12px rgba(11,37,69,.15);}
.sc-act-icon{font-size:2.2rem;margin-bottom:12px;display:block;}
.sc-act-card h3{font-weight:800;color:#0b2545;font-size:1.05rem;margin-bottom:8px;}
.sc-act-card p{font-size:.88rem;color:#475569;line-height:1.65;margin:0 0 10px;}
.sc-act-age{display:inline-block;background:#e0f2fe;color:#0369a1;padding:3px 12px;border-radius:20px;font-size:.75rem;font-weight:700;}
/* Schedule */
.sc-schedule{background:linear-gradient(135deg,#0b2545,#0f3864);padding:64px 24px;}
.sc-sched-inner{max-width:900px;margin:0 auto;}
.sc-sched-inner h2{font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:800;color:#fbbf24;text-align:center;margin-bottom:36px;}
.sc-sched-table{width:100%;border-collapse:collapse;background:rgba(255,255,255,.06);border-radius:14px;overflow:hidden;}
.sc-sched-table th{background:rgba(255,255,255,.1);color:#fbbf24;font-size:.8rem;text-transform:uppercase;letter-spacing:.06em;padding:14px 18px;text-align:left;}
.sc-sched-table td{padding:14px 18px;color:rgba(255,255,255,.88);border-top:1px solid rgba(255,255,255,.08);font-size:.9rem;vertical-align:top;}
/* Note */
.sc-note{background:#fffbeb;border-top:3px solid #fbbf24;padding:32px 24px;}
.sc-note-inner{max-width:860px;margin:0 auto;display:flex;gap:16px;align-items:flex-start;}
.sc-note-icon{font-size:1.8rem;flex-shrink:0;}
.sc-note-body p{font-size:.92rem;color:#92400e;line-height:1.7;margin:0;font-weight:500;}
/* CTA */
.sc-cta{padding:60px 24px;background:#fff;text-align:center;}
.sc-cta h2{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:800;color:#0b2545;margin-bottom:10px;}
.sc-cta p{font-size:.95rem;color:#64748b;margin-bottom:24px;}
.sc-cta-btn{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,#d97706,#b45309);color:#fff;padding:16px 40px;border-radius:50px;font-size:1.05rem;font-weight:700;text-decoration:none;transition:all .25s ease;}
.sc-cta-btn:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(217,119,6,.4);}
@media(max-width:640px){.sc-act-grid{grid-template-columns:1fr;}.sc-hero-info{flex-direction:column;align-items:center;}.sc-hero__content{padding:52px 20px;}}
</style>

<div class="sc-pg">
    {{-- Hero --}}
    <section class="sc-hero">
        @if($heroBg)
            <div class="sc-hero__bg" style="background-image:url('{{ $heroBg }}')"></div>
        @endif
        <div class="sc-hero__overlay"></div>
        <div class="sc-hero__content">
            <div class="sc-eyebrow">🏕️ Annual Summer Camp {{ $year }}</div>
            <h1 class="sc-hero__title">{{ $heroTitle }}</h1>
            <p class="sc-hero__sub">{{ $heroSub }}</p>
            <div class="sc-hero-info">
                @if($dates)<div class="sc-hero-badge">📅 {{ $dates }}</div>@endif
                @if($timings)<div class="sc-hero-badge">⏰ {{ $timings }}</div>@endif
                @if($charges)<div class="sc-hero-badge">💰 {{ $charges }}</div>@endif
            </div>
        </div>
    </section>

    {{-- Stats --}}
    @if(!empty($highlights))
    <div class="sc-stats">
        <div class="sc-stats-grid">
            @foreach($highlights as $h)
            <div class="sc-stat">
                <div class="si">{{ $h['icon'] ?? '' }}</div>
                <div class="sv">{{ $h['value'] ?? '' }}</div>
                <div class="sl">{{ $h['label'] ?? '' }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- About --}}
    @if($about)
    <section class="sc-about">
        <div class="sc-about-inner">
            <h2>About the <span style="color:#d97706">Summer Camp</span></h2>
            <p>{{ $about }}</p>
        </div>
    </section>
    @endif

    {{-- Activities --}}
    @if(!empty($activityCategories))
    <section class="sc-activities">
        <div class="sc-act-inner">
            <div class="sc-sec-head">
                <h2>🎯 Activity <span style="color:#d97706">Modules</span></h2>
                <p>Choose from a wide range of activities designed by expert educators and coaches for all age groups.</p>
            </div>
            <div class="sc-act-grid">
                @foreach($activityCategories as $act)
                <div class="sc-act-card">
                    <span class="sc-act-icon">{{ $act['icon'] ?? '🎨' }}</span>
                    <h3>{{ $act['name'] ?? '' }}</h3>
                    <p>{{ $act['desc'] ?? '' }}</p>
                    @if(!empty($act['age_group']))<span class="sc-act-age">👦 {{ $act['age_group'] }}</span>@endif
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- Schedule --}}
    @if(!empty($schedule))
    <section class="sc-schedule">
        <div class="sc-sched-inner">
            <h2>📅 Daily Schedule</h2>
            <table class="sc-sched-table">
                <thead>
                    <tr>
                        <th>Age Group</th><th>Time Slot</th><th>Activity 1</th><th>Activity 2</th><th>Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $s)
                    <tr>
                        <td><strong>{{ $s['group'] ?? '' }}</strong></td>
                        <td>{{ $s['slot'] ?? '' }}</td>
                        <td>{{ $s['activity1'] ?? '' }}</td>
                        <td>{{ $s['activity2'] ?? '' }}</td>
                        <td>{{ $s['capacity'] ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- Note --}}
    @if($note)
    <div class="sc-note">
        <div class="sc-note-inner">
            <div class="sc-note-icon">⚠️</div>
            <div class="sc-note-body"><p>{{ $note }}</p></div>
        </div>
    </div>
    @endif

    {{-- CTA --}}
    <section class="sc-cta">
        <h2>Ready for the <span style="color:#d97706">Adventure?</span></h2>
        <p>Secure your child's spot today. Limited seats available!</p>
        <a href="{{ $ctaUrl }}" class="sc-cta-btn">🏕️ {{ $ctaLabel }}</a>
    </section>
</div>
