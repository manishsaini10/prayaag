<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Dynamic meta engine (SeoManager) --}}
    @include('themes.school.partials.seo-head')

    {{-- Structured data (JSON-LD) --}}
    {!! $schema ?? '' !!}

    {{-- Fonts + design system --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('site.css') }}?v={{ @filemtime(public_path('site.css')) ?: '1' }}">
    <style>:root{ --primary: {{ $primaryColor ?? '#0b2545' }}; }</style>

    {{-- Theme customisations (custom fonts + colors from settings) --}}
    {!! $themeHead ?? '' !!}

    @stack('head')
</head>
<body>
    {!! $header !!}

    <main id="content">{!! $content !!}</main>

    {!! $footer !!}

    <script src="{{ asset('site.js') }}?v={{ @filemtime(public_path('site.js')) ?: '1' }}" defer></script>
    @stack('scripts')
</body>
</html>
