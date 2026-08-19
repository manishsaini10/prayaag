{{-- Story Bubble Layout — fixed floating circle, pulse animation, tap-to-advance --}}
@php
    $posClass = ($storyPosition === 'bottom-left') ? 'vt-sb-left' : 'vt-sb-right';
    $firstVideo = $videos->first();
@endphp

@if($firstVideo && $firstVideo->isPubliclyVisible())
<div class="vt-story-bubble {{ $posClass }}"
     x-data="vtStoryBubble({{ $videos->count() }})"
     x-init="init()"
     role="complementary"
     aria-label="Video testimonials">

    {{-- Floating bubble --}}
    <div class="vt-sb-ring" @click="openStory()">
        <div class="vt-sb-pulse"></div>
        <div class="vt-sb-avatar" role="button" tabindex="0" aria-label="Watch student video testimonials"
             @keydown.enter="openStory()">
            <img src="{{ $firstVideo->resolved_thumbnail_url }}"
                 alt="{{ $firstVideo->student_name ?? 'Video testimonial' }}" loading="lazy">
            <div class="vt-sb-play" aria-hidden="true">▶</div>
        </div>
        <span class="vt-sb-label">Stories</span>
    </div>

    {{-- Fullscreen story viewer --}}
    <div class="vt-story-viewer" x-show="open" x-cloak
         @keydown.escape.window="closeStory()"
         @keydown.arrow-right.window="nextStory()"
         @keydown.arrow-left.window="prevStory()">

        <div class="vt-sv-backdrop" @click="closeStory()"></div>

        <div class="vt-sv-box">
            {{-- Progress bars --}}
            <div class="vt-sv-progress">
                @foreach($videos as $i => $video)
                <div class="vt-sv-prog-bar">
                    <div class="vt-sv-prog-fill"
                         :style="'width:' + (current > {{ $i }} ? 100 : (current === {{ $i }} ? progress : 0)) + '%'"></div>
                </div>
                @endforeach
            </div>

            {{-- Header --}}
            <div class="vt-sv-header">
                <div class="vt-sv-who">
                    @foreach($videos as $i => $video)
                    <span x-show="current === {{ $i }}" class="vt-sv-name">
                        {{ $video->student_name ?? 'Student' }}
                        @if($video->class_grade) — {{ $video->class_grade }} @endif
                    </span>
                    @endforeach
                </div>
                <button class="vt-sv-close" @click="closeStory()" aria-label="Close stories">✕</button>
            </div>

            {{-- Videos --}}
            @foreach($videos as $i => $video)
            @if($video->isPubliclyVisible())
            <div class="vt-sv-slide" x-show="current === {{ $i }}" x-cloak>
                <iframe class="vt-sv-iframe" frameborder="0"
                    :src="current === {{ $i }} ? '{{ $video->video_embed_url ?: '' }}?autoplay=0' : 'about:blank'"
                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy"
                    title="Video testimonial from {{ $video->student_name ?? 'student' }}"></iframe>
            </div>
            @endif
            @endforeach

            {{-- Tap zones --}}
            <div class="vt-sv-tap-left" @click="prevStory()" aria-label="Previous story"></div>
            <div class="vt-sv-tap-right" @click="nextStory()" aria-label="Next story"></div>

            {{-- CTA --}}
            @foreach($videos as $i => $video)
            @if($showCta && $video->cta_label && $video->cta_url)
            <div class="vt-sv-cta" x-show="current === {{ $i }}">
                <a href="{{ $video->cta_url }}" class="vt-cta-btn" target="_blank" rel="noopener">{{ $video->cta_label }}</a>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

<style>
[x-cloak]{display:none!important}
.vt-story-bubble { position:fixed; bottom:5.5rem; z-index:8000; display:flex; flex-direction:column; align-items:center; gap:.35rem; }
.vt-sb-right { right:1.25rem; }
.vt-sb-left  { left:1.25rem; }
.vt-sb-ring { position:relative; cursor:pointer; }
.vt-sb-pulse { position:absolute; inset:-6px; border-radius:50%; border:3px solid #c79a3b; animation:vtPulse 2s ease-in-out infinite; }
@keyframes vtPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.08)} }
.vt-sb-avatar { width:72px; height:72px; border-radius:50%; overflow:hidden; border:3px solid #fff; box-shadow:0 4px 16px rgba(0,0,0,.25); position:relative; }
.vt-sb-avatar img { width:100%; height:100%; object-fit:cover; }
.vt-sb-play { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(14,47,94,.45); color:#fff; font-size:.9rem; }
.vt-sb-label { font-size:.65rem; font-weight:700; color:#0e2f5e; background:#fff; padding:.15rem .5rem; border-radius:99px; box-shadow:0 2px 6px rgba(0,0,0,.12); }

/* Story viewer */
.vt-story-viewer { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; }
.vt-sv-backdrop { position:absolute; inset:0; background:rgba(0,0,0,.92); backdrop-filter:blur(6px); }
.vt-sv-box { position:relative; width:min(420px,100vw); height:min(750px,100svh); background:#000; border-radius:1rem; overflow:hidden; }
.vt-sv-progress { position:absolute; top:.75rem; left:.75rem; right:.75rem; z-index:10; display:flex; gap:4px; }
.vt-sv-prog-bar { flex:1; height:3px; background:rgba(255,255,255,.3); border-radius:2px; overflow:hidden; }
.vt-sv-prog-fill { height:100%; background:#fff; border-radius:2px; transition:width .1s linear; }
.vt-sv-header { position:absolute; top:1.5rem; left:.75rem; right:2.5rem; z-index:10; }
.vt-sv-name { color:#fff; font-size:.8rem; font-weight:700; text-shadow:0 1px 4px rgba(0,0,0,.5); }
.vt-sv-close { position:absolute; top:1.5rem; right:.75rem; z-index:10; background:none; border:none; color:#fff; font-size:1.25rem; cursor:pointer; }
.vt-sv-slide { position:absolute; inset:0; }
.vt-sv-iframe { width:100%; height:100%; border:none; }
.vt-sv-tap-left { position:absolute; left:0; top:0; bottom:0; width:35%; z-index:5; cursor:pointer; }
.vt-sv-tap-right { position:absolute; right:0; top:0; bottom:0; width:35%; z-index:5; cursor:pointer; }
.vt-sv-cta { position:absolute; bottom:1.5rem; left:50%; transform:translateX(-50%); z-index:10; }
</style>

<script>
function vtStoryBubble(total) {
    return {
        open: false, current: 0, total: total, progress: 0, timer: null,
        init() {},
        openStory() { this.open = true; this.current = 0; document.body.style.overflow = 'hidden'; },
        closeStory() { this.open = false; clearInterval(this.timer); document.body.style.overflow = ''; },
        nextStory() { this.current < this.total - 1 ? this.current++ : this.closeStory(); },
        prevStory() { if (this.current > 0) this.current--; }
    };
}
</script>
@endif
