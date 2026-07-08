{{-- Premium school header. Data + Header Settings injected by ThemeRenderer. --}}
@php($menu = $menu ?? collect())
@php($cur = rtrim(url()->current(), '/'))
@php($hVariant = $hVariant ?? 'hv-01')

<div class="site-header {{ $hVariant }} {{ ($hSticky ?? true) ? 'is-sticky' : 'no-sticky' }} {{ ($hGlass ?? false) ? 'is-glass' : '' }} {{ ($hTransparent ?? false) ? 'is-transparent' : '' }}">

    @if($hTopbar ?? true)
    <div class="site-top">
        <div class="container">
            <div class="top-info">
                @foreach($topNotes ?? [] as $i => $note)
                    <span>{!! $icon($i === 0 ? 'shield' : 'building') !!}{{ $note }}</span>
                @endforeach
            </div>
            <div class="top-right">
                @if(($hSocial ?? true) && collect($social)->filter()->isNotEmpty())
                    <div class="top-social">
                        @foreach($social as $net => $url)
                            @if(!empty($url))<a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $net }}">{!! $icon($net) !!}</a>@endif
                        @endforeach
                    </div>
                    @if($hLogin ?? true)<span class="top-div"></span>@endif
                @endif
                @if($hLogin ?? true)
                    @foreach($topLinks ?? [] as $l)
                        @if(($l['style'] ?? 'link') === 'btn')
                            <a class="top-pay" href="{{ $l['url'] }}" target="_blank" rel="noopener">{!! $icon($l['ic'] ?? 'card') !!}<span>{{ $l['label'] }}</span></a>
                        @else
                            <a class="top-link" href="{{ $l['url'] }}" target="_blank" rel="noopener">{!! $icon($l['ic'] ?? '') !!}<span>{{ $l['label'] }}</span></a>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif

    <header class="site-head">
        <div class="container">
            <a href="{{ url('/') }}" class="brand" aria-label="{{ $siteName }} — Home">
                @if(!empty($logo))<img src="{{ $logo }}" alt="{{ $siteName }}">@endif
                <span class="brand-text">{{ $siteName }}@if(!empty($tagline))<small>{{ \Illuminate\Support\Str::limit($tagline, 40) }}</small>@endif</span>
            </a>

            <div class="head-actions">
                @if(($hCta ?? true) && !empty($ctaLabel))
                    <a href="{{ $ctaUrl }}" class="btn btn-enquire">{!! $icon('chat') !!}{{ $ctaLabel }}</a>
                @endif
                <button class="menu-toggle" aria-label="Open menu"><span></span><span></span><span></span></button>
            </div>

            <nav aria-label="Primary">
                <ul class="nav">
                    @forelse($menu as $item)
                        @php($u = rtrim($item->resolveUrl(), '/'))
                        @php($kids = $item->children)
                        <li class="{{ $kids->count() ? 'has-children' : '' }}">
                            <a href="{{ $item->resolveUrl() }}" class="{{ $u === $cur ? 'is-active' : '' }}" @if($item->target)target="{{ $item->target }}"@endif>{{ $item->label }}</a>
                            @if($kids->count())
                                <ul class="submenu {{ $kids->count() >= 5 ? 'submenu--mega' : '' }}">
                                    @foreach($kids as $child)
                                        <li><a href="{{ $child->resolveUrl() }}" @if($child->target)target="{{ $child->target }}"@endif>{{ $child->label }}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @empty
                        <li><a href="{{ url('/') }}" class="is-active">Home</a></li>
                    @endforelse
                </ul>
            </nav>
        </div>
    </header>
</div>

{{-- Mobile drawer --}}
<div class="drawer-backdrop"></div>
<aside class="drawer" aria-label="Mobile menu">
    <div class="drawer-head">
        <span class="brand-text" style="font-size:1.15rem;color:var(--navy)">{{ $siteName }}</span>
        <button class="drawer-close" aria-label="Close menu">&times;</button>
    </div>
    <nav>
        <ul>
            @forelse($menu as $item)
                <li class="{{ $item->children->count() ? 'has-children' : '' }}">
                    <a href="{{ $item->resolveUrl() }}">{{ $item->label }}</a>
                    @if($item->children->count())
                        <ul class="submenu" style="display:none;list-style:none;padding:0;margin:0">
                            @foreach($item->children as $child)
                                <li><a href="{{ $child->resolveUrl() }}">{{ $child->label }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @empty
                <li><a href="{{ url('/') }}">Home</a></li>
            @endforelse
        </ul>
        @foreach($topLinks ?? [] as $l)
            <a href="{{ $l['url'] }}" target="_blank" rel="noopener" style="display:block;padding:.75rem .4rem;border-bottom:1px solid var(--line-soft);font-weight:600;color:var(--ink)">{{ $l['label'] }}</a>
        @endforeach
        @if(!empty($ctaLabel))<a href="{{ $ctaUrl }}" class="btn btn-enquire" style="margin-top:1.2rem;width:100%;justify-content:center">{{ $ctaLabel }}</a>@endif
    </nav>
</aside>
