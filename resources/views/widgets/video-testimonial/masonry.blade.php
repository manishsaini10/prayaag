{{-- Video Testimonials Masonry Layout --}}
{{-- Staggered Columns, Center-Grow Animations, Lazy Observer --}}
<section class="vt-section vt-masonry-section py-16 px-4" style="background: {{ $settings['section_bg'] ?? 'transparent' }}; color: {{ $settings['text_color'] ?? '#0f172a' }}; padding-top: 5rem; padding-bottom: 5rem;">
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

    {{-- Category Filter Tabs --}}
    @include('widgets.video-testimonial._filter_tabs')

    <div class="vt-masonry-grid max-w-7xl mx-auto">
        @foreach($videos as $i => $video)
        @if($video->isPubliclyVisible())
        @php
            $tagList = implode(',', $video->tags->pluck('tag_value')->toArray());
            $isPortrait = $video->orientation === 'portrait';
        @endphp
        <div class="vt-masonry-card vt-card-grow {{ $isPortrait ? 'vt-tall' : 'vt-wide' }} {{ $cardStyleClass ?? 'vt-style-shadow' }}"
             data-tags="{{ $tagList }}"
             data-embed="{{ htmlspecialchars($video->video_embed_url ?: '', ENT_QUOTES) }}"
             data-title="{{ htmlspecialchars($video->title, ENT_QUOTES) }}"
             data-student="{{ htmlspecialchars($video->student_name ?? '', ENT_QUOTES) }}"
             data-grade="{{ htmlspecialchars($video->class_grade ?? '', ENT_QUOTES) }}"
             data-cta-label="{{ htmlspecialchars($video->cta_label ?? '', ENT_QUOTES) }}"
             data-cta-url="{{ htmlspecialchars($video->cta_url ?? '', ENT_QUOTES) }}"
             onclick="vtOpenModal(this)">

            <div class="vt-thumb-wrap relative overflow-hidden" style="border-radius: {{ $settings['border_radius'] ?? '1rem' }};">
                <img src="{{ $video->resolved_thumbnail_url }}"
                     alt="{{ $video->student_name ?? 'Testimonial' }}"
                     class="vt-thumb w-full h-full object-cover transition-transform duration-500" loading="lazy">

                <div class="vt-play-overlay">
                    <div class="vt-play-btn" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>

                @if($video->is_featured)
                <span class="vt-featured-badge">⭐ Featured</span>
                @endif
            </div>

            <div class="vt-info p-4">
                <p class="vt-name font-bold text-base">{{ $video->student_name ?? 'Student' }}</p>
                @if($video->class_grade)
                <span class="vt-grade font-semibold text-xs text-[#0e2f5e] bg-slate-100 px-2 py-0.5 rounded-full inline-block mt-1">{{ $video->class_grade }}</span>
                @endif
                <p class="vt-card-title text-xs text-slate-500 mt-1.5 leading-relaxed">{{ \Illuminate\Support\Str::limit($video->title, 60) }}</p>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>

@include('widgets.video-testimonial._modal')

<style>
.vt-masonry-grid {
    column-count: 1;
    column-gap: 1.5rem;
}
@media(min-width:640px) { .vt-masonry-grid { column-count: 2; } }
@media(min-width:1024px) { .vt-masonry-grid { column-count: 3; } }

.vt-masonry-card {
    break-inside: avoid;
    margin-bottom: 1.5rem;
    background: #fff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    cursor: pointer;
    transform-origin: center center;
    transform: scale(0.92);
    opacity: 0;
    transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease;
}
.vt-masonry-card.vt-in-view {
    transform: scale(1);
    opacity: 1;
}
.vt-masonry-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 16px 40px rgba(14,47,94,0.18);
}
.vt-tall .vt-thumb-wrap { aspect-ratio: 9/16; }
.vt-wide .vt-thumb-wrap { aspect-ratio: 16/9; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('vt-in-view');
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.vt-masonry-card').forEach(function(card) {
            observer.observe(card);
        });
    } else {
        document.querySelectorAll('.vt-masonry-card').forEach(function(card) {
            card.classList.add('vt-in-view');
        });
    }
});
</script>
