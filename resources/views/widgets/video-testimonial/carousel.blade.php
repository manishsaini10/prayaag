{{-- Video Testimonials Carousel Layout --}}
{{-- Horizontal scroll-snap, Default vs Spotlight style toggle, arrow + swipe support --}}
<section class="vt-section vt-carousel-section py-16 px-4" style="background: {{ $settings['section_bg'] ?? 'transparent' }}; color: {{ $settings['text_color'] ?? '#0f172a' }}; padding-top: 5rem; padding-bottom: 5rem;">
    @if($eyebrow || $heading)
    <div class="vt-head text-center mb-10">
        @if($eyebrow)
        <span class="vt-eyebrow">{{ $eyebrow }}</span>
        @endif
        @if($heading)
        <h2 class="vt-title mt-2" style="color: {{ $settings['text_color'] ?? '#0f172a' }}">{{ $heading }}</h2>
        @endif
    </div>
    @endif

    @include('widgets.video-testimonial._filter_tabs')

    <div class="vt-carousel-outer" x-data="vtCarousel({{ $videos->count() }})" x-init="init()">
        {{-- Arrow: Previous --}}
        <button class="vt-arrow vt-arrow-left" @click="prev()" :disabled="current === 0" aria-label="Previous video">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20"><path d="M15 18l-6-6 6-6"/></svg>
        </button>

        {{-- Track --}}
        <div class="vt-carousel-track" id="vtCarTrack"
             @touchstart.passive="touchStart($event)"
             @touchend.passive="touchEnd($event)">
            @foreach($videos as $i => $video)
            @if($video->isPubliclyVisible())
            @php $tagList = implode(',', $video->tags->pluck('tag_value')->toArray()); @endphp
            <div class="vt-carousel-card {{ $carouselStyle === 'spotlight' ? 'vt-spotlight-card' : '' }}"
                 :class="{ 'vt-active': current === {{ $i }}, 'vt-adjacent': Math.abs(current - {{ $i }}) === 1 }"
                 data-tags="{{ $tagList }}"
                 data-embed="{{ htmlspecialchars($video->video_embed_url ?: '', ENT_QUOTES) }}"
                 data-title="{{ htmlspecialchars($video->title, ENT_QUOTES) }}"
                 data-student="{{ htmlspecialchars($video->student_name ?? '', ENT_QUOTES) }}"
                 data-grade="{{ htmlspecialchars($video->class_grade ?? '', ENT_QUOTES) }}"
                 data-cta-label="{{ htmlspecialchars($video->cta_label ?? '', ENT_QUOTES) }}"
                 data-cta-url="{{ htmlspecialchars($video->cta_url ?? '', ENT_QUOTES) }}"
                 onclick="vtOpenModal(this)"
                 tabindex="0" role="button"
                 aria-label="Watch video: {{ $video->student_name ?? 'Student' }}">
                <div class="vt-thumb-wrap">
                    <img src="{{ $video->resolved_thumbnail_url }}"
                         alt="Video from {{ $video->student_name ?? 'student' }}"
                         class="vt-thumb" loading="lazy">
                    <div class="vt-play-overlay"><div class="vt-play-btn" aria-hidden="true"><svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32"><path d="M8 5v14l11-7z"/></svg></div></div>
                </div>
                <div class="vt-info">
                    <p class="vt-name">{{ $video->student_name ?? 'Student' }}</p>
                    @if($video->class_grade)<span class="vt-grade">{{ $video->class_grade }}</span>@endif
                    <p class="vt-card-title">{{ \Illuminate\Support\Str::limit($video->title, 65) }}</p>
                    @if($showCta && $video->cta_label && $video->cta_url)
                    <a href="{{ $video->cta_url }}" class="vt-inline-cta" target="_blank" rel="noopener" onclick="event.stopPropagation()">{{ $video->cta_label }} →</a>
                    @endif
                </div>
            </div>
            @endif
            @endforeach
        </div>

        {{-- Arrow: Next --}}
        <button class="vt-arrow vt-arrow-right" @click="next()" :disabled="current >= total - 1" aria-label="Next video">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20"><path d="M9 18l6-6-6-6"/></svg>
        </button>

        {{-- Dots --}}
        <div class="vt-dots">
            @foreach($videos as $i => $video)
            <button class="vt-dot" :class="{ 'vt-dot-active': current === {{ $i }} }" @click="go({{ $i }})" aria-label="Go to video {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
</section>

{{-- Shared modal (same as grid) --}}
@include('widgets.video-testimonial._modal')

<style>
.vt-carousel-outer { position:relative; max-width:1100px; margin:0 auto; overflow:hidden; }
.vt-carousel-track { display:flex; gap:1.5rem; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; -webkit-overflow-scrolling:touch; padding:1rem .5rem 2rem; scrollbar-width:none; }
.vt-carousel-track::-webkit-scrollbar { display:none; }
.vt-carousel-card { flex:0 0 300px; scroll-snap-align:center; border-radius:1rem; overflow:hidden; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.08); cursor:pointer; transition:transform .3s,box-shadow .3s,opacity .3s; }
.vt-spotlight-card.vt-active { transform:scale(1.06); box-shadow:0 12px 40px rgba(14,47,94,.22); z-index:1; }
.vt-spotlight-card:not(.vt-active) { opacity:.7; transform:scale(.95); }
.vt-carousel-card:hover { transform:translateY(-4px) scale(1.02); box-shadow:0 10px 32px rgba(0,0,0,.14); }
.vt-arrow { position:absolute; top:42%; transform:translateY(-50%); z-index:5; width:44px; height:44px; border-radius:50%; border:none; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.15); color:#0e2f5e; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s; }
.vt-arrow:hover { background:#0e2f5e; color:#fff; }
.vt-arrow:disabled { opacity:.35; cursor:not-allowed; }
.vt-arrow-left { left:0; }
.vt-arrow-right { right:0; }
.vt-dots { display:flex; justify-content:center; gap:.5rem; margin-top:.5rem; }
.vt-dot { width:8px; height:8px; border-radius:50%; border:none; background:#cbd5e1; cursor:pointer; transition:all .2s; }
.vt-dot-active { background:#0e2f5e; width:22px; border-radius:4px; }
.vt-inline-cta { display:inline-block; margin-top:.5rem; font-size:.75rem; font-weight:700; color:#c79a3b; text-decoration:none; }
.vt-inline-cta:hover { text-decoration:underline; }
</style>

<script>
function vtCarousel(total) {
    return {
        current: 0, total: total, touchX: 0,
        init() {
            // sync scroll position to current card
            this.$watch('current', v => {
                var track = document.getElementById('vtCarTrack');
                if (!track) return;
                var cards = track.querySelectorAll('.vt-carousel-card');
                if (cards[v]) {
                    var card = cards[v];
                    var scrollTo = card.offsetLeft - (track.clientWidth / 2) + (card.clientWidth / 2);
                    track.scrollTo({ left: scrollTo, behavior: 'smooth' });
                }
            });
        },
        next() { if (this.current < this.total - 1) this.current++; },
        prev() { if (this.current > 0) this.current--; },
        go(i)  { this.current = i; },
        touchStart(e) { this.touchX = e.changedTouches[0].clientX; },
        touchEnd(e) {
            var dx = e.changedTouches[0].clientX - this.touchX;
            if (Math.abs(dx) > 50) { dx < 0 ? this.next() : this.prev(); }
        }
    };
}
</script>
