<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @php($seo = $seo ?? [])
    @if (!empty($seo['description']))
        <meta name="description" content="{{ $seo['description'] }}">
    @endif
    @if (!empty($seo['keywords']))
        <meta name="keywords" content="{{ is_array($seo['keywords']) ? implode(', ', $seo['keywords']) : $seo['keywords'] }}">
    @endif
    <meta property="og:title" content="{{ $seo['og_title'] ?? $title }}">
    @if (!empty($seo['description']))
        <meta property="og:description" content="{{ $seo['description'] }}">
    @endif
    @if (!empty($seo['og_image']))
        <meta property="og:image" content="{{ $seo['og_image'] }}">
    @endif
    <style>
        :root { --primary: {{ $primaryColor }}; }
        * { box-sizing: border-box; }
        body { font-family: {{ $fontFamily }}; margin: 0; color: #111; line-height: 1.6; }
        .site-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem; border-bottom: 1px solid #eee; }
        .site-header .brand { font-weight: 600; font-size: 1.25rem; }
        .site-header nav ul { display: flex; gap: 1.25rem; list-style: none; margin: 0; padding: 0; }
        .site-header nav a { text-decoration: none; color: #333; }
        .site-header nav a:hover { color: var(--primary); }
        main { max-width: 1100px; margin: 0 auto; }
        .pb-section { padding: 2.5rem 2rem; }
        .pb-row { display: flex; flex-wrap: wrap; gap: 1.5rem; }
        .pb-col { flex: 1 1 0; }
        .pb-heading { margin: 0 0 .5rem; }
        .pb-button { display: inline-block; background: var(--primary); color: #fff; padding: .5rem 1rem; border-radius: 6px; text-decoration: none; }
        .site-footer { padding: 1.5rem 2rem; border-top: 1px solid #eee; color: #666; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/chatbot/chatbot-runtime.css') }}">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="{{ asset('js/chatbot/chatbot-runtime.js') }}" defer></script>
</head>
<body>
    {!! $header !!}
    <main>{!! $content !!}</main>
    {!! $footer !!}
</body>
</html>
