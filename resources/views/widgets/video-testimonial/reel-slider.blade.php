{{-- Video Testimonials Reel Slider Layout — Next.js / Framer-Motion Style --}}
{{-- Center-Focused Auto-Slider, Muted Inline Card, Full Cover Thumbnails, Zero Background Audio --}}
<section class="vt-section vt-reel-section px-4" style="background: {{ $settings['section_bg'] ?? 'transparent' }}; color: {{ $settings['text_color'] ?? '#0f172a' }}; padding-top: 5rem; padding-bottom: 5rem;">
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

    <div class="vt-reel-outer"
         x-data="vtNextReelSlider({{ $videos->count() }}, 4000)"
         x-init="init()"
         @mouseenter="pauseTimer()"
         @mouseleave="startTimer()">

        {{-- Arrow: Previous --}}
        <button class="vt-reel-arrow vt-reel-arrow-left" @click="prev()" aria-label="Previous reel">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><path d="M15 18l-6-6 6-6"/></svg>
        </button>

        {{-- Slider Track --}}
        <div class="vt-reel-track" id="vtReelTrack"
             @touchstart.passive="touchStart($event)"
             @touchend.passive="touchEnd($event)">

            @foreach($videos as $i => $video)
            @if($video->isPubliclyVisible())
            @php $tagList = implode(',', $video->tags->pluck('tag_value')->toArray()); @endphp
            <div class="vt-reel-card"
                 :class="{ 'vt-reel-active': current === {{ $i }} }"
                 data-tags="{{ $tagList }}"
                 data-embed="{{ htmlspecialchars($video->video_embed_url ?: '', ENT_QUOTES) }}"
                 data-title="{{ htmlspecialchars($video->title, ENT_QUOTES) }}"
                 data-student="{{ htmlspecialchars($video->student_name ?? '', ENT_QUOTES) }}"
                 data-grade="{{ htmlspecialchars($video->class_grade ?? '', ENT_QUOTES) }}"
                 data-cta-label="{{ htmlspecialchars($video->cta_label ?? '', ENT_QUOTES) }}"
                 data-cta-url="{{ htmlspecialchars($video->cta_url ?? '', ENT_QUOTES) }}"
                 onclick="vtOpenModal(this)">

                {{-- 4s Animated Top Progress Bar (Active Center Card Only) --}}
                <div class="vt-reel-progress-wrap" x-show="current === {{ $i }}">
                    <div class="vt-reel-progress-bar" :style="'width: ' + progress + '%'"></div>
                </div>

                {{-- Card Media --}}
                <div class="vt-reel-media">
                    {{-- Center Active Card: Muted Video (Strictly mute=1, Zero Sound) --}}
                    <template x-if="current === {{ $i }}">
                        <iframe class="vt-reel-iframe"
                                src="https://www.youtube-nocookie.com/embed/{{ $video->video_external_id }}?autoplay=1&mute=1&controls=0&loop=1&playlist={{ $video->video_external_id }}&modestbranding=1&enablejsapi=1"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                loading="eager"
                                title="Reel video by {{ $video->student_name ?? 'student' }}"></iframe>
                    </template>

                    {{-- Non-Center Cards: Full-Width High-Res Thumbnail Image --}}
                    <template x-if="current !== {{ $i }}">
                        <img src="{{ $video->resolved_thumbnail_url }}"
                             alt="{{ $video->student_name ?? 'Reel' }}"
                             class="vt-reel-poster" loading="eager">
                    </template>

                    {{-- Glassmorphic Click for Sound Badge --}}
                    <div class="vt-reel-sound-badge" title="Click to watch in HD with sound">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <span>Watch with Sound 🔊</span>
                    </div>

                    @if($video->is_featured)
                    <span class="vt-featured-badge">⭐ Featured</span>
                    @endif
                </div>

                {{-- Bottom Gradient Overlay with Typography --}}
                <div class="vt-reel-overlay">
                    <div class="vt-reel-info" :class="{ 'vt-animated': current === {{ $i }} }">
                        <p class="vt-reel-name">{{ $video->student_name ?? 'Student' }}</p>
                        @if($video->class_grade)
                        <span class="vt-reel-grade">{{ $video->class_grade }}</span>
                        @endif
                        <p class="vt-reel-caption">{{ \Illuminate\Support\Str::limit($video->title, 55) }}</p>

                        @if($showCta && $video->cta_label && $video->cta_url)
                        <a href="{{ $video->cta_url }}" class="vt-reel-cta-btn" target="_blank" rel="noopener" onclick="event.stopPropagation()">
                            {{ $video->cta_label }} →
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @endforeach

        </div>

        {{-- Arrow: Next --}}
        <button class="vt-reel-arrow vt-reel-arrow-right" @click="next()" aria-label="Next reel">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><path d="M9 18l6-6-6-6"/></svg>
        </button>

        {{-- Navigation Dots --}}
        <div class="vt-reel-dots">
            @foreach($videos as $i => $video)
            <button class="vt-reel-dot" :class="{ 'vt-reel-dot-active': current === {{ $i }} }" @click="go({{ $i }})" aria-label="Go to reel {{ $i + 1 }}"></button>
            @endforeach
        </div>
    </div>
</section>

{{-- Shared Lightbox Modal Partial (Plays with sound when clicked, stops 100% when closed) --}}
@include('widgets.video-testimonial._modal')

<style>
.vt-reel-outer { position:relative; max-width:1200px; margin:0 auto; padding:0 2.5rem; }
.vt-reel-track { display:flex; gap:1.25rem; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; -webkit-overflow-scrolling:touch; padding:1.25rem .5rem 2.25rem; scrollbar-width:none; }
.vt-reel-track::-webkit-scrollbar { display:none; }

/* Next.js Modern Reel Cards (9:16 Portrait Ratio) */
.vt-reel-card { flex:0 0 270px; height:480px; scroll-snap-align:center; border-radius:1.5rem; overflow:hidden; position:relative; background:#0f172a; box-shadow:0 10px 32px rgba(0,0,0,.18); cursor:pointer; transition:transform .4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow .4s ease, opacity .4s ease, filter .4s ease; }
@media(max-width:640px) {
    .vt-reel-outer { padding:0 1rem; }
    .vt-reel-card { flex:0 0 240px; height:420px; }
}

/* Center Active Card (Focused Next.js Style) */
.vt-reel-card.vt-reel-active { transform:scale(1.05); box-shadow:0 20px 50px rgba(14,47,94,.42); z-index:2; border:2px solid #c79a3b; filter:none; opacity:1; }

/* Non-Center Cards (Thumbnail Cover Only) */
.vt-reel-card:not(.vt-reel-active) { opacity:.75; filter:brightness(.88); transform:scale(.92); }
.vt-reel-card:hover { opacity:1; filter:none; transform:translateY(-6px) scale(1.04); }

/* Progress bar at top of center card */
.vt-reel-progress-wrap { position:absolute; top:0; left:0; right:0; height:4px; background:rgba(255,255,255,.25); z-index:10; }
.vt-reel-progress-bar { height:100%; background:linear-gradient(90deg,#c79a3b,#eda52a); transition:width .1s linear; }

/* Media & Iframe */
.vt-reel-media { width:100%; height:100%; position:relative; background:#000; overflow:hidden; }
.vt-reel-iframe { width:100%; height:100%; pointer-events:none; border:none; object-fit:cover; transform:scale(1.38); }
.vt-reel-poster { width:100%; height:100%; object-fit:cover; display:block; }

.vt-reel-sound-badge { position:absolute; top:.75rem; right:.75rem; z-index:8; background:rgba(15,23,42,.75); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.18); color:#fff; font-size:.65rem; font-weight:600; padding:.3rem .65rem; border-radius:99px; display:flex; align-items:center; gap:.4rem; box-shadow:0 2px 8px rgba(0,0,0,.3); transition:background .2s; }
.vt-reel-card:hover .vt-reel-sound-badge { background:rgba(14,47,94,.92); }

/* Bottom Gradient Overlay */
.vt-reel-overlay { position:absolute; bottom:0; left:0; right:0; padding:3.5rem 1.25rem 1.25rem; background:linear-gradient(to top, rgba(15,23,42,.96) 0%, rgba(15,23,42,.65) 60%, transparent 100%); z-index:6; color:#fff; pointer-events:none; }
.vt-reel-info { display:flex; flex-direction:column; gap:.25rem; }

/* Text Animations for Active Card */
.vt-animated .vt-reel-name { animation: vtSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.vt-animated .vt-reel-grade { animation: vtSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.08s forwards; }
.vt-animated .vt-reel-caption { animation: vtSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.16s forwards; }
.vt-animated .vt-reel-cta-btn { animation: vtSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) 0.24s forwards; pointer-events:auto; }

@keyframes vtSlideUp {
    from { opacity:0; transform:translateY(12px); }
    to   { opacity:1; transform:translateY(0); }
}

.vt-reel-name { font-weight:800; font-size:1.05rem; margin:0; color:#fff; letter-spacing:-.01em; text-shadow:0 2px 6px rgba(0,0,0,.6); }
.vt-reel-grade { display:inline-block; align-self:flex-start; font-size:.68rem; font-weight:700; background:rgba(199,154,59,.3); border:1px solid rgba(199,154,59,.5); color:#eda52a; padding:.15rem .6rem; border-radius:99px; }
.vt-reel-caption { font-size:.8rem; color:#cbd5e1; margin:0; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.vt-reel-cta-btn { display:inline-block; align-self:flex-start; margin-top:.4rem; background:linear-gradient(135deg,#c79a3b,#eda52a); color:#fff; font-size:.75rem; font-weight:700; padding:.45rem 1rem; border-radius:.5rem; text-decoration:none; box-shadow:0 4px 12px rgba(199,154,59,.3); transition:transform .15s; }
.vt-reel-cta-btn:hover { transform:translateY(-2px); }

/* Navigation Arrows & Dots */
.vt-reel-arrow { position:absolute; top:50%; transform:translateY(-50%); z-index:15; width:44px; height:44px; border-radius:50%; border:none; background:#fff; box-shadow:0 4px 16px rgba(0,0,0,.2); color:#0e2f5e; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s, color .2s; }
.vt-reel-arrow:hover { background:#0e2f5e; color:#fff; }
.vt-reel-arrow-left { left:.5rem; }
.vt-reel-arrow-right { right:.5rem; }
.vt-reel-dots { display:flex; justify-content:center; gap:.5rem; margin-top:1rem; }
.vt-reel-dot { width:8px; height:8px; border-radius:50%; border:none; background:#cbd5e1; cursor:pointer; transition:all .25s; }
.vt-reel-dot-active { background:#0e2f5e; width:26px; border-radius:4px; }
</style>

<script>
function vtNextReelSlider(total, durationMs) {
    return {
        current: 0,
        total: total,
        duration: durationMs || 4000,
        progress: 0,
        progressInterval: null,
        touchX: 0,

        init() {
            this.startTimer();

            this.$nextTick(() => {
                this.scrollToCurrent();
            });

            // Watch current slide -> auto scroll track container horizontally (NEVER scroll browser window)
            this.$watch('current', () => {
                this.scrollToCurrent();
            });
        },

        scrollToCurrent() {
            var track = document.getElementById('vtReelTrack');
            if (!track) return;
            var cards = track.querySelectorAll('.vt-reel-card');
            if (cards[this.current]) {
                var card = cards[this.current];
                var maxScroll = track.scrollWidth - track.clientWidth;
                var scrollTo = card.offsetLeft - (track.clientWidth / 2) + (card.clientWidth / 2);
                scrollTo = Math.max(0, Math.min(scrollTo, maxScroll));
                track.scrollTo({ left: scrollTo, behavior: 'smooth' });
            }
        },

        startTimer() {
            this.pauseTimer();
            this.progress = 0;

            var stepMs = 100;
            var increment = (stepMs / this.duration) * 100;

            this.progressInterval = setInterval(() => {
                if (this.progress < 100) {
                    this.progress += increment;
                } else {
                    this.next();
                }
            }, stepMs);
        },

        pauseTimer() {
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
        },

        next() {
            this.current = (this.current + 1) % this.total;
            this.startTimer();
        },

        prev() {
            this.current = (this.current - 1 + this.total) % this.total;
            this.startTimer();
        },

        go(i) {
            this.current = i;
            this.startTimer();
        },

        touchStart(e) {
            this.touchX = e.changedTouches[0].clientX;
            this.pauseTimer();
        },

        touchEnd(e) {
            var dx = e.changedTouches[0].clientX - this.touchX;
            if (Math.abs(dx) > 50) {
                dx < 0 ? this.next() : this.prev();
            } else {
                this.startTimer();
            }
        }
    };
}
</script>
