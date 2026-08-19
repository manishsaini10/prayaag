{{-- Spotlight Modal Layout — lightbox with prev/next navigation, ESC/backdrop close, CTA pinned below --}}
<section class="vt-section vt-spotlight-section py-16 px-4">
    @if($eyebrow || $heading)
    <div class="vt-head text-center mb-10">
        @if($eyebrow)<span class="vt-eyebrow">{{ $eyebrow }}</span>@endif
        @if($heading)<h2 class="vt-title mt-2">{{ $heading }}</h2>@endif
    </div>
    @endif

    {{-- Thumbnail grid triggering spotlight modal --}}
    <div class="vt-spotlight-grid" x-data="vtSpotlight({{ $videos->count() }})">
        @foreach($videos as $i => $video)
        @if($video->isPubliclyVisible())
        <div class="vt-sp-thumb-card" @click="open({{ $i }})" tabindex="0" role="button"
             aria-label="Watch: {{ $video->student_name ?? 'Student' }}">
            <div class="vt-thumb-wrap">
                <img src="{{ $video->resolved_thumbnail_url }}"
                     alt="{{ $video->student_name ?? 'student' }}" class="vt-thumb" loading="lazy">
                <div class="vt-play-overlay"><div class="vt-play-btn" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="36" height="36"><path d="M8 5v14l11-7z"/></svg>
                </div></div>
                @if($video->is_featured)<span class="vt-featured-badge">⭐ Featured</span>@endif
            </div>
            <div class="vt-info">
                <p class="vt-name">{{ $video->student_name ?? 'Student' }}</p>
                @if($video->class_grade)<span class="vt-grade">{{ $video->class_grade }}</span>@endif
                <p class="vt-card-title">{{ \Illuminate\Support\Str::limit($video->title, 55) }}</p>
            </div>
        </div>
        @endif
        @endforeach

        {{-- Spotlight Lightbox --}}
        <div class="vt-spotlight-modal" x-show="isOpen" x-cloak
             role="dialog" aria-modal="true" aria-label="Video spotlight"
             @keydown.escape.window="close()"
             @keydown.arrow-right.window="next()"
             @keydown.arrow-left.window="prev()">

            <div class="vt-sp-backdrop" @click="close()"></div>

            <div class="vt-sp-box">
                <button class="vt-sp-close" @click="close()" aria-label="Close">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="20" height="20"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </button>

                {{-- Prev / Next --}}
                <button class="vt-sp-nav vt-sp-prev" @click="prev()" :disabled="currentIdx === 0" aria-label="Previous video">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
                <button class="vt-sp-nav vt-sp-next" @click="next()" :disabled="currentIdx >= total-1" aria-label="Next video">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="22" height="22"><path d="M9 18l6-6-6-6"/></svg>
                </button>

                {{-- Videos (only active one loads iframe) --}}
                @foreach($videos as $i => $video)
                @if($video->isPubliclyVisible())
                <div class="vt-sp-slide" x-show="currentIdx === {{ $i }}">
                    <div class="vt-sp-video-wrap">
                        <iframe class="vt-sp-iframe" frameborder="0"
                            :src="currentIdx === {{ $i }} ? '{{ $video->video_embed_url ?: '' }}' : 'about:blank'"
                            allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen loading="lazy"
                            title="Video testimonial from {{ $video->student_name ?? 'student' }}"></iframe>
                    </div>
                    <div class="vt-sp-meta">
                        <div class="vt-sp-info">
                            <p class="vt-sp-student">{{ $video->student_name ?? 'Student' }}</p>
                            @if($video->class_grade)<span class="vt-grade">{{ $video->class_grade }}</span>@endif
                            <p class="vt-sp-title">{{ $video->title }}</p>
                        </div>
                        @if($showCta && $video->cta_label && $video->cta_url)
                        <a href="{{ $video->cta_url }}" class="vt-cta-btn" target="_blank" rel="noopener">{{ $video->cta_label }}</a>
                        @endif
                    </div>
                </div>
                @endif
                @endforeach

                {{-- Counter --}}
                <div class="vt-sp-counter" x-text="(currentIdx+1) + ' / ' + total"></div>
            </div>
        </div>
    </div>
</section>

<style>
.vt-spotlight-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1.25rem; }
@media(min-width:640px){ .vt-spotlight-grid{ grid-template-columns:repeat(3,1fr); } }
@media(min-width:1024px){ .vt-spotlight-grid{ grid-template-columns:repeat(4,1fr); } }
.vt-sp-thumb-card { border-radius:1rem; overflow:hidden; cursor:pointer; background:#fff; box-shadow:0 2px 12px rgba(0,0,0,.08); transition:transform .2s,box-shadow .2s; }
.vt-sp-thumb-card:hover { transform:translateY(-4px); box-shadow:0 10px 30px rgba(0,0,0,.14); }

/* Modal */
.vt-spotlight-modal { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem; }
.vt-sp-backdrop { position:absolute; inset:0; background:rgba(0,0,0,.88); backdrop-filter:blur(6px); }
.vt-sp-box { position:relative; width:100%; max-width:840px; background:#0f172a; border-radius:1.25rem; overflow:hidden; box-shadow:0 24px 64px rgba(0,0,0,.7); animation:vtSlideUp .25s ease; }
.vt-sp-close { position:absolute; top:.75rem; right:.75rem; z-index:20; width:36px; height:36px; border-radius:50%; border:none; background:rgba(255,255,255,.15); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.vt-sp-close:hover { background:rgba(255,255,255,.3); }
.vt-sp-nav { position:absolute; top:38%; transform:translateY(-50%); z-index:15; width:42px; height:42px; border-radius:50%; border:none; background:rgba(255,255,255,.12); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s; }
.vt-sp-nav:hover { background:rgba(255,255,255,.28); }
.vt-sp-nav:disabled { opacity:.3; cursor:not-allowed; }
.vt-sp-prev { left:.75rem; }
.vt-sp-next { right:.75rem; }
.vt-sp-slide { display:flex; flex-direction:column; }
.vt-sp-video-wrap { aspect-ratio:16/9; background:#000; }
.vt-sp-iframe { width:100%; height:100%; display:block; border:none; }
.vt-sp-meta { padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.vt-sp-student { font-weight:700; color:#f1f5f9; font-size:1rem; margin:0; }
.vt-sp-title { color:#94a3b8; font-size:.8rem; margin:.25rem 0 0; }
.vt-sp-counter { position:absolute; bottom:.75rem; right:1rem; color:rgba(255,255,255,.5); font-size:.7rem; font-weight:600; }
</style>

<script>
function vtSpotlight(total) {
    return {
        isOpen: false, currentIdx: 0, total: total,
        open(i) { this.currentIdx = i; this.isOpen = true; document.body.style.overflow = 'hidden'; },
        close() { this.isOpen = false; document.body.style.overflow = ''; },
        next() { if (this.currentIdx < this.total - 1) this.currentIdx++; },
        prev() { if (this.currentIdx > 0) this.currentIdx--; }
    };
}
</script>
