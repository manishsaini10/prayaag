@php
    $theme  = rescue(fn () => app(\App\Core\Theme\ThemeRenderer::class), null, false);
    $header = $theme ? rescue(fn () => $theme->header(), '', false) : '';
    $footer = $theme ? rescue(fn () => $theme->footer(), '', false) : '';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Access Forbidden — Prayaag International School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('site.css') }}">
</head>
<body>
    {!! $header !!}

    <main id="content">
        <section class="pb-section" style="padding: 4rem 1rem;">
            <div class="pb-row">
                <div class="pb-col pb-col--12" style="text-align:center">
                    <div style="font-family:var(--font-head);font-weight:800;color:var(--gold, #d4af37);line-height:1;font-size:clamp(4.5rem,14vw,9rem)">403</div>
                    <h1 style="margin:.1em 0 .3em">Access Forbidden</h1>
                    <p class="sec-sub" style="margin:0 auto 1.8rem; max-width: 600px;">You don't have permission to access this page or action. If you believe this is an error, please log in with appropriate credentials.</p>
                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                        <a class="btn btn-gold" href="{{ url('/') }}">Back to Home</a>
                        <a class="btn btn-outline" href="{{ route('login') }}">Log In</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {!! $footer !!}

    <script src="{{ asset('site.js') }}" defer></script>
</body>
</html>
