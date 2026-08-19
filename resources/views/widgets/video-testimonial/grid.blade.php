{{-- Video Testimonials Grid Layout --}}
{{-- Center-Origin Scale/Fade Playback, GPU-Accelerated Animations, Category Filter Tabs --}}
<section class="vt-section vt-grid-section py-16 px-4" style="background: {{ $settings['section_bg'] ?? 'transparent' }}; color: {{ $settings['text_color'] ?? '#0f172a' }}; padding-top: 5rem; padding-bottom: 5rem;">
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

    <div class="vt-grid max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($videos as $video)
        @if($video->isPubliclyVisible())
        @php $tagList = implode(',', $video->tags->pluck('tag_value')->toArray()); @endphp
        <div class="vt-card vt-card-grow"
             data-tags="{{ $tagList }}"
             data-embed="{{ htmlspecialchars($video->video_embed_url ?: '', ENT_QUOTES) }}"
             data-title="{{ htmlspecialchars($video->title, ENT_QUOTES) }}"
             data-student="{{ htmlspecialchars($video->student_name ?? '', ENT_QUOTES) }}"
             data-grade="{{ htmlspecialchars($video->class_grade ?? '', ENT_QUOTES) }}"
             data-cta-label="{{ htmlspecialchars($video->cta_label ?? '', ENT_QUOTES) }}"
             data-cta-url="{{ htmlspecialchars($video->cta_url ?? '', ENT_QUOTES) }}"
             onclick="vtOpenModal(this)"
             tabindex="0"
             role="button"
             aria-label="Watch video testimonial from {{ $video->student_name ?? 'student' }}">

            {{-- Thumbnail --}}
            <div class="vt-thumb-wrap relative overflow-hidden" style="border-radius: {{ $settings['border_radius'] ?? '1rem' }};">
                <img src="{{ $video->resolved_thumbnail_url }}"
                     alt="Video testimonial thumbnail — {{ $video->student_name ?? 'student' }}"
                     class="vt-thumb w-full h-full object-cover transition-transform duration-500" loading="lazy">

                <div class="vt-play-overlay">
                    <div class="vt-play-btn" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>

                @if($video->is_featured)
                <span class="vt-featured-badge">⭐ Featured</span>
                @endif
            </div>

            {{-- Info --}}
            <div class="vt-info p-4">
                <p class="vt-name font-bold text-base">{{ $video->student_name ?? 'Student' }}</p>
                @if($video->class_grade)
                <span class="vt-grade text-xs font-bold text-[#0e2f5e] bg-slate-100 px-2 py-0.5 rounded-full inline-block mt-1">{{ $video->class_grade }}</span>
                @endif
                <p class="vt-card-title text-xs text-slate-500 mt-1.5 line-clamp-2">{{ \Illuminate\Support\Str::limit($video->title, 55) }}</p>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>

{{-- Shared Lightbox Modal Partial --}}
@include('widgets.video-testimonial._modal')

<style>
.vt-card-grow {
    transform-origin: center center;
    transform: scale(0.92);
    opacity: 0;
    transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease;
}
.vt-card-grow.vt-in-view {
    transform: scale(1);
    opacity: 1;
}
.vt-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 16px 40px rgba(14,47,94,0.18);
}
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
        }, { threshold: 0.1 });

        document.querySelectorAll('.vt-card-grow').forEach(function(card) {
            observer.observe(card);
        });
    } else {
        document.querySelectorAll('.vt-card-grow').forEach(function(card) {
            card.classList.add('vt-in-view');
        });
    }
});
</script>
