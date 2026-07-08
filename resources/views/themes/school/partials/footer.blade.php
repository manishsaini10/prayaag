{{-- Premium school footer. Data injected by App\Core\Theme\ThemeRenderer. --}}
@php($links = ($footerMenu ?? collect())->isNotEmpty() ? $footerMenu : ($menu ?? collect()))
<footer class="site-foot {{ $fVariant ?? 'fv-01' }}">
    <div class="container">
        <div class="foot-grid">
            {{-- Brand --}}
            <div class="foot-col">
                <div class="brand-text" style="font-family:var(--font-head);font-weight:700;font-size:1.5rem;margin-bottom:.8rem">{{ $siteName }}</div>
                <p style="font-size:var(--fs-sm);max-width:34ch">{{ $about ?: $tagline }}</p>
                <div class="foot-social">
                    @foreach($social as $net => $url)
                        @if(!empty($url))<a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ $net }}">{!! $icon($net) !!}</a>@endif
                    @endforeach
                </div>
            </div>

            {{-- Quick links --}}
            <div class="foot-col">
                <h4>Quick Links</h4>
                <ul>
                    @forelse($links as $item)
                        <li><a href="{{ $item->resolveUrl() }}">{{ $item->label }}</a></li>
                    @empty
                        <li><a href="{{ url('/') }}">Home</a></li>
                    @endforelse
                </ul>
            </div>

            {{-- Contact --}}
            <div class="foot-col">
                <h4>Get in Touch</h4>
                <div class="foot-contact">
                    @if(!empty($address))<span>{!! $icon('pin') !!}<span>{{ $address }}</span></span>@endif
                    @if(!empty($phone))<span>{!! $icon('phone') !!}<a href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}">{{ $phone }}</a></span>@endif
                    @if(!empty($email))<span>{!! $icon('mail') !!}<a href="mailto:{{ $email }}">{{ $email }}</a></span>@endif
                </div>
            </div>

            {{-- Newsletter + map --}}
            <div class="foot-col">
                <h4>Stay Updated</h4>
                <form class="foot-news" method="POST" action="{{ url('/subscribe') }}">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" style="display:none" aria-hidden="true">
                    <input type="email" name="email" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center">Subscribe</button>
                </form>
                @if(!empty($mapEmbed))
                    <div class="foot-map">{!! $mapEmbed !!}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="foot-bottom">
        <div class="container" style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.6rem;width:100%">
            <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
            <span>Managed via the school CMS.</span>
        </div>
    </div>
</footer>
