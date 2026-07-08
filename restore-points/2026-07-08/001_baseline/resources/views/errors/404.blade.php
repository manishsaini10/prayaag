@php
    // Resolve chrome defensively — a 404 view must never throw a secondary error.
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
    <title>Page Not Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('site.css') }}">
</head>
<body>
    {!! $header !!}

    <main id="content">
        <section class="pb-section">
            <div class="pb-row">
                <div class="pb-col pb-col--12" style="text-align:center" data-reveal>
                    <div style="font-family:var(--font-head);font-weight:800;color:var(--gold);line-height:1;font-size:clamp(4.5rem,14vw,9rem)">404</div>
                    <h1 style="margin:.1em 0 .3em">Page Not Found</h1>
                    <p class="sec-sub" style="margin:0 auto 1.8rem">The page you're looking for doesn't exist or may have moved. Let's get you back on track.</p>
                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                        <a class="btn btn-gold" href="{{ url('/') }}">Back to Home</a>
                        <a class="btn btn-outline" href="{{ url('/search') }}">Search the Site</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {!! $footer !!}

    <script src="{{ asset('site.js') }}" defer></script>
</body>
</html>
