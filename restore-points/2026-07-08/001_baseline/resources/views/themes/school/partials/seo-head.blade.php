{{--
    Dynamic meta engine output (Phase 5). Every tag is rendered from the
    fully-resolved $seo array produced by SeoManager — no field is ever empty,
    and there are no duplicate tags. Included by the theme layout <head>.
--}}
@php($seo = $seo ?? [])
<title>{{ $seo['title'] ?? ($title ?? '') }}</title>
<meta name="description" content="{{ $seo['description'] ?? '' }}">
@if(!empty($seo['keywords']))<meta name="keywords" content="{{ $seo['keywords'] }}">@endif
<meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
<link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
<meta property="og:title" content="{{ $seo['og_title'] ?? ($seo['title'] ?? '') }}">
<meta property="og:description" content="{{ $seo['og_description'] ?? ($seo['description'] ?? '') }}">
<meta property="og:url" content="{{ $seo['og_url'] ?? ($seo['canonical'] ?? url()->current()) }}">
@if(!empty($seo['site_name']))<meta property="og:site_name" content="{{ $seo['site_name'] }}">@endif
<meta property="og:locale" content="{{ $seo['locale'] ?? 'en_IN' }}">
@if(!empty($seo['og_image']))
<meta property="og:image" content="{{ $seo['og_image'] }}">
<meta property="og:image:alt" content="{{ $seo['og_title'] ?? ($seo['title'] ?? '') }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary' }}">
<meta name="twitter:title" content="{{ $seo['twitter_title'] ?? ($seo['title'] ?? '') }}">
<meta name="twitter:description" content="{{ $seo['twitter_description'] ?? ($seo['description'] ?? '') }}">
@if(!empty($seo['twitter_image']))<meta name="twitter:image" content="{{ $seo['twitter_image'] }}">@endif
@if(!empty($seo['twitter_site']))<meta name="twitter:site" content="{{ $seo['twitter_site'] }}">@endif
