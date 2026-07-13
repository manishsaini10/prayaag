<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <x-popup-builder-assets />
    <link rel="stylesheet" href="{{ asset('css/chatbot/chatbot-runtime.css') }}">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/chatbot/chatbot-runtime.js') }}" defer></script>
</head>
<body>
    {!! $header !!}

    <main id="content">{!! $content !!}</main>

    {!! $footer !!}

    <x-popup-render />
    <script src="{{ asset('site.js') }}?v={{ @filemtime(public_path('site.js')) ?: '1' }}" defer></script>
    @stack('scripts')
</body>
</html>
