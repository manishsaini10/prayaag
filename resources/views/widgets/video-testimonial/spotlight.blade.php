{{-- Video Testimonials Spotlight Layout --}}
{{-- Hero Video Spotlight with Sidebar Queue Selector --}}
<section class="vt-section vt-spotlight-section py-16 px-4" style="background: {{ $settings['section_bg'] ?? 'transparent' }}; color: {{ $settings['text_color'] ?? '#0f172a' }}; padding-top: 5rem; padding-bottom: 5rem;">
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

    @php
        $first = $videos->first();
    @endphp

    @if($first)
    <div class="vt-spotlight-container max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 items-start"
         x-data="{
            activeEmbed: '{{ $first->video_embed_url }}',
            activeTitle: '{{ addslashes($first->title) }}',
            activeStudent: '{{ addslashes($first->student_name ?? '') }}',
            activeGrade: '{{ addslashes($first->class_grade ?? '') }}',
            activeThumb: '{{ $first->resolved_thumbnail_url }}',
            activeId: '{{ $first->id }}'
         }">

        {{-- Main Spotlight Hero --}}
        <div class="lg:col-span-2 vt-spotlight-hero bg-slate-900 rounded-3xl overflow-hidden shadow-2xl vt-card-grow vt-in-view">
            <div class="aspect-video relative bg-black">
                <iframe class="w-full h-full border-0"
                        :src="activeEmbed + '?autoplay=0&rel=0'"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen loading="lazy"></iframe>
            </div>
            <div class="p-6 bg-slate-900 text-white flex items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold text-[#eda52a] uppercase tracking-wider">Featured Story</span>
                    <h3 class="font-extrabold text-xl mt-1 text-white" x-text="activeTitle"></h3>
                    <p class="text-xs text-slate-400 mt-1" x-text="activeStudent + (activeGrade ? ' · ' + activeGrade : '')"></p>
                </div>
            </div>
        </div>

        {{-- Sidebar Playlist Queue --}}
        <div class="vt-spotlight-sidebar space-y-3">
            <h4 class="font-bold text-sm text-slate-500 uppercase tracking-wider px-1">More Stories</h4>
            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                @foreach($videos as $i => $video)
                @if($video->isPubliclyVisible())
                @php $tagList = implode(',', $video->tags->pluck('tag_value')->toArray()); @endphp
                <div class="vt-spotlight-thumb p-3 rounded-2xl bg-white shadow-sm hover:shadow-md transition-all cursor-pointer flex gap-3 items-center border border-slate-100"
                     :class="{ 'ring-2 ring-[#c79a3b] bg-slate-50': activeId === '{{ $video->id }}' }"
                     data-tags="{{ $tagList }}"
                     @click="
                        activeEmbed = '{{ $video->video_embed_url }}';
                        activeTitle = '{{ addslashes($video->title) }}';
                        activeStudent = '{{ addslashes($video->student_name ?? '') }}';
                        activeGrade = '{{ addslashes($video->class_grade ?? '') }}';
                        activeId = '{{ $video->id }}';
                     ">
                    <div class="w-20 h-14 rounded-xl overflow-hidden relative shrink-0 bg-slate-900">
                        <img src="{{ $video->resolved_thumbnail_url }}" alt="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center text-white text-xs">▶</div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-xs text-slate-800 truncate">{{ $video->student_name ?? 'Student' }}</p>
                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $video->title }}</p>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>

@include('widgets.video-testimonial._modal')

<style>
.vt-spotlight-hero {
    transform-origin: center center;
    transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
</style>
