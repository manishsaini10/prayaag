@props([
    'trail'    => [],
    'settings' => [],
])

@php
    $style       = $settings['style'] ?? 'simple';
    $separator   = $settings['separator'] ?? 'chevron';
    $showMobile  = $settings['show_mobile'] ?? true;
    $align       = $settings['alignment'] ?? 'left';
    $bgColor     = $settings['background_color'] ?? '#ffffff';
    $txtColor    = $settings['text_color'] ?? '#374151';
    $accentColor = $settings['accent_color'] ?? '#4f46e5';
    $overlay     = min(100, max(0, (int) ($settings['overlay_opacity'] ?? 40)));
    $padding     = $settings['padding_y'] ?? 'py-4';
    $bgImage     = $settings['background_image'] ?? '';
    $bgVideo     = $settings['background_video'] ?? '';
    $minHeight   = $settings['min_height'] ?? '80px';
    $maxWidth    = $settings['max_width'] ?? 'full';
    $widthStyle  = $settings['width_style'] ?? 'full';

    $sepIcon = match($separator) {
        'slash'   => '/',
        'dot'     => '&#8226;',
        'arrow'   => '&#8594;',
        default   => '<svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>',
    };
    $alignClass = $align === 'center' ? 'justify-center' : 'justify-start';
    $mobileClass = $showMobile ? 'flex' : 'hidden sm:flex';
    $widthClass = $widthStyle === 'box'
        ? ($maxWidth !== 'full' ? "container mx-auto max-w-{$maxWidth}" : 'container mx-auto')
        : 'w-full';

    $hasMedia = null;
    if ($style === 'with-video' && $bgVideo) {
        $hasMedia = 'video';
    } elseif ($bgImage) {
        $hasMedia = 'image';
    }
@endphp

@if (count($trail) > 0)
    @if ($style === 'simple')
        <nav class="{{ $mobileClass }} {{ $alignClass }} {{ $padding }} {{ $widthClass }} flex-wrap items-center gap-1 text-sm font-medium" style="color: {{ $txtColor }};">
            @foreach ($trail as $i => $crumb)
                @if ($i > 0)
                    <span class="inline-flex items-center text-gray-300 mx-0.5">{!! $sepIcon !!}</span>
                @endif
                @if ($crumb['url'])
                    <a href="{{ $crumb['url'] }}" class="hover:text-indigo-600 transition-colors duration-150 underline-offset-2 hover:underline">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-gray-800 font-semibold">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>

    @elseif ($style === 'gradient')
        <nav class="{{ $widthClass }} {{ $padding }}">
            <div class="{{ $mobileClass }} {{ $alignClass }} items-center gap-1.5 px-5 py-2.5 rounded-full text-white text-sm font-medium shadow-md" style="background: linear-gradient(135deg, {{ $accentColor }}, #7c3aed);">
                @foreach ($trail as $i => $crumb)
                    @if ($i > 0)
                        <span class="inline-flex items-center text-white/50 mx-0.5">{!! $sepIcon !!}</span>
                    @endif
                    @if ($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="hover:text-white/80 transition-colors duration-150">{{ $crumb['label'] }}</a>
                    @else
                        <span class="text-white font-bold">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </div>
        </nav>

    @elseif ($style === 'modern')
        <nav class="{{ $widthClass }} {{ $padding }}">
            <div class="{{ $mobileClass }} {{ $alignClass }} items-center gap-1 px-5 py-3 rounded-xl text-sm font-medium" style="background-color: #1e293b; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                @foreach ($trail as $i => $crumb)
                    @if ($i > 0)
                        <span class="inline-flex items-center text-gray-500 mx-0.5">{!! $sepIcon !!}</span>
                    @endif
                    @if ($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="hover:text-white transition-colors duration-150" style="color: #94a3b8;">{{ $crumb['label'] }}</a>
                    @else
                        <span class="font-bold text-white">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </div>
        </nav>

    @elseif ($style === 'minimal')
        <nav class="{{ $mobileClass }} {{ $alignClass }} {{ $padding }} {{ $widthClass }} items-center gap-0.5 text-xs font-medium tracking-wide uppercase" style="color: #9ca3af;">
            @foreach ($trail as $i => $crumb)
                @if ($i > 0)
                    <span class="inline-flex items-center text-gray-300 mx-1">{!! $sepIcon !!}</span>
                @endif
                @if ($crumb['url'])
                    <a href="{{ $crumb['url'] }}" class="hover:text-gray-600 transition-colors duration-150">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-gray-800 font-bold">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>

    @elseif ($style === 'with-image' || $style === 'with-video')
        <nav class="relative {{ $widthClass }} overflow-hidden rounded-xl {{ $padding }}" style="min-height: {{ $minHeight }};">
            @if ($hasMedia === 'video')
            <div class="absolute inset-0 w-full h-full pointer-events-none overflow-hidden">
                @php
                    $videoId = '';
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w-]+)/', $bgVideo, $m)) {
                        $videoId = $m[1];
                    } elseif (preg_match('/vimeo\.com\/(\d+)/', $bgVideo, $m)) {
                        $videoId = $m[1];
                    }
                @endphp
                @if ($videoId)
                    <iframe class="absolute top-1/2 left-1/2 w-[200%] h-[200%] -translate-x-1/2 -translate-y-1/2" src="https://www.youtube.com/embed/{{ $videoId }}?autoplay=1&mute=1&loop=1&playlist={{ $videoId }}&controls=0&showinfo=0" frameborder="0" allow="autoplay; muted" style="pointer-events: none;"></iframe>
                @else
                    <video class="absolute top-1/2 left-1/2 w-full h-full object-cover -translate-x-1/2 -translate-y-1/2" autoplay muted loop playsinline style="pointer-events: none;">
                        <source src="{{ $bgVideo }}" type="video/mp4">
                    </video>
                @endif
            </div>
            @elseif ($hasMedia === 'image')
            <div class="absolute inset-0 w-full h-full bg-cover bg-center" style="background-image: url('{{ $bgImage }}');"></div>
            @endif

            @if ($hasMedia)
            <div class="absolute inset-0 w-full h-full" style="background-color: rgba(0,0,0,{{ $overlay / 100 }});"></div>
            @endif

            <div class="relative {{ $mobileClass }} {{ $alignClass }} items-center gap-1 px-6 py-5 text-sm font-medium {{ $hasMedia ? 'text-white' : 'text-gray-800' }}" style="min-height: {{ $minHeight }};">
                @foreach ($trail as $i => $crumb)
                    @if ($i > 0)
                        <span class="inline-flex items-center {{ $hasMedia ? 'text-white/40' : 'text-gray-300' }} mx-1">{!! $sepIcon !!}</span>
                    @endif
                    @if ($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="transition-colors duration-150 {{ $hasMedia ? 'text-white/80 hover:text-white' : 'text-gray-500 hover:text-indigo-600' }}">{{ $crumb['label'] }}</a>
                    @else
                        <span class="font-bold {{ $hasMedia ? 'text-white' : 'text-gray-800' }}">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </div>
        </nav>
    @endif
@endif
