{{-- Video Testimonials Wall / Mosaic Grid Layout --}}
{{-- Multi-Video Grid of Simultaneous Autoplaying Muted Tiles --}}
<section class="vt-section vt-wall-section py-16 px-4" style="background: {{ $settings['section_bg'] ?? 'transparent' }}; color: {{ $settings['text_color'] ?? '#0f172a' }}; padding-top: 5rem; padding-bottom: 5rem;">
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

    <div class="vt-wall-grid max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($videos as $i => $video)
        @if($video->isPubliclyVisible())
        @php $tagList = implode(',', $video->tags->pluck('tag_value')->toArray()); @endphp
        <div class="vt-wall-card vt-card-grow relative rounded-2xl overflow-hidden shadow-md cursor-pointer aspect-[9/14] bg-slate-900 group"
             data-tags="{{ $tagList }}"
             data-embed="{{ htmlspecialchars($video->video_embed_url ?: '', ENT_QUOTES) }}"
             data-title="{{ htmlspecialchars($video->title, ENT_QUOTES) }}"
             data-student="{{ htmlspecialchars($video->student_name ?? '', ENT_QUOTES) }}"
             data-grade="{{ htmlspecialchars($video->class_grade ?? '', ENT_QUOTES) }}"
             data-cta-label="{{ htmlspecialchars($video->cta_label ?? '', ENT_QUOTES) }}"
             data-cta-url="{{ htmlspecialchars($video->cta_url ?? '', ENT_QUOTES) }}"
             onclick="vtOpenModal(this)">

            {{-- Autoplay Muted Tile --}}
            <iframe class="w-full h-full border-0 pointer-events-none scale-125 object-cover"
                    src="https://www.youtube-nocookie.com/embed/{{ $video->video_external_id }}?autoplay=1&mute=1&controls=0&loop=1&playlist={{ $video->video_external_id }}&modestbranding=1"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy"></iframe>

            {{-- Sound Badge --}}
            <div class="absolute top-2 right-2 z-10 bg-black/60 backdrop-blur-md text-white text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1">
                🔊 Click for sound
            </div>

            {{-- Bottom Gradient Info --}}
            <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent text-white z-10">
                <p class="font-extrabold text-sm text-white drop-shadow">{{ $video->student_name ?? 'Student' }}</p>
                @if($video->class_grade)
                <span class="inline-block text-[10px] font-bold bg-[#c79a3b]/30 text-[#eda52a] border border-[#c79a3b]/40 px-2 py-0.5 rounded-full mt-0.5">{{ $video->class_grade }}</span>
                @endif
                <p class="text-[11px] text-slate-300 line-clamp-1 mt-1">{{ $video->title }}</p>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</section>

@include('widgets.video-testimonial._modal')

<style>
.vt-wall-card {
    transform-origin: center center;
    transform: scale(0.92);
    opacity: 0;
    transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease;
}
.vt-wall-card.vt-in-view {
    transform: scale(1);
    opacity: 1;
}
.vt-wall-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 16px 40px rgba(14,47,94,0.3);
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

        document.querySelectorAll('.vt-wall-card').forEach(function(card) {
            observer.observe(card);
        });
    } else {
        document.querySelectorAll('.vt-wall-card').forEach(function(card) {
            card.classList.add('vt-in-view');
        });
    }
});
</script>
