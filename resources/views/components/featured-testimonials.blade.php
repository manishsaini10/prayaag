@php
    $testimonials = \App\Models\Testimonial::published()
        ->featured()
        ->forLocation('home')
        ->orderBy('sort_order')
        ->get();
@endphp

@if($testimonials->isNotEmpty())
<div class="py-12 bg-gray-50 border-y border-gray-100" x-data="testimonialSlider()">
    <div class="max-w-6xl mx-auto px-4 md:px-6 space-y-8">
        {{-- Section Head --}}
        <div class="text-center max-w-xl mx-auto space-y-2">
            <span class="text-xs font-bold text-purple-700 tracking-widest uppercase">In Their Words</span>
            <h2 class="text-3xl font-extrabold text-gray-800">Parents Testimonials</h2>
            <p class="text-sm text-gray-500">Read what parents say about our teaching methodologies and community support.</p>
        </div>

        {{-- Testimonials Carousel Slider --}}
        <div class="relative overflow-hidden w-full py-4">
            <div class="flex gap-6 transition-transform duration-500 ease-out" 
                 :style="'transform: translateX(-' + (activeSlide * (100 / slidesCount)) + '%)'" 
                 style="width: 100%;">
                
                @foreach($testimonials as $t)
                    <div class="w-full md:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] shrink-0 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between space-y-4">
                        {{-- Stars Rating --}}
                        <div class="text-amber-500 text-sm font-bold">
                            {!! str_repeat('★', $t->rating) !!}{!! str_repeat('☆', 5 - $t->rating) !!}
                        </div>
                        
                        {{-- Testimonial Quote --}}
                        <div class="flex-grow">
                            @if($t->title)
                                <h4 class="font-bold text-sm text-gray-800 mb-1">"{{ $t->title }}"</h4>
                            @endif
                            <p class="text-gray-600 text-xs italic leading-relaxed">
                                "{{ Str::limit($t->testimonial, 160) }}"
                                @if(strlen($t->testimonial) > 160)
                                    <a href="{{ route('testimonials.index') }}" class="text-purple-700 font-semibold hover:underline">Read More</a>
                                @endif
                            </p>
                        </div>

                        {{-- Parent Info --}}
                        <div class="flex items-center gap-3 border-t pt-3 border-gray-100">
                            @if($t->image)
                                <img src="{{ asset($t->image) }}" class="w-9 h-9 rounded-full object-cover border">
                            @else
                                <div class="w-9 h-9 rounded-full bg-purple-50 text-purple-700 font-bold flex items-center justify-center text-xs uppercase border border-purple-100">
                                    {{ substr($t->name, 0, 2) }}
                                </div>
                            @endif
                            <div>
                                <h5 class="font-bold text-gray-800 text-xs">{{ $t->name }}</h5>
                                <span class="text-[10px] text-gray-400 block font-semibold">{{ $t->role }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- Slider Navigation Controls --}}
        @if($testimonials->count() > 1)
        <div class="flex justify-center gap-2 items-center">
            <button @click="prev()" class="p-2 bg-white rounded-full border shadow-sm hover:bg-gray-100 text-gray-600 focus:outline-none">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-4 h-4"><path d="M15 19l-7-7 7-7"/></svg>
            </button>
            
            <div class="flex gap-1.5">
                <template x-for="idx in maxDots">
                    <button @click="goTo(idx - 1)" 
                            class="w-2 h-2 rounded-full transition-all focus:outline-none" 
                            :class="activeSlide === (idx - 1) ? 'bg-purple-700 scale-125' : 'bg-gray-300'"></button>
                </template>
            </div>

            <button @click="next()" class="p-2 bg-white rounded-full border shadow-sm hover:bg-gray-100 text-gray-600 focus:outline-none">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-4 h-4"><path d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        @endif
    </div>
</div>

<script>
function testimonialSlider() {
    return {
        activeSlide: 0,
        slidesCount: {{ $testimonials->count() }},
        get maxDots() {
            // Adjust dot counts based on responsive columns
            if (window.innerWidth >= 1024) {
                return Math.max(1, this.slidesCount - 2);
            } else if (window.innerWidth >= 768) {
                return Math.max(1, this.slidesCount - 1);
            }
            return this.slidesCount;
        },
        init() {
            // Auto slide every 5 seconds
            setInterval(() => {
                this.next();
            }, 6000);
        },
        next() {
            if (this.activeSlide >= this.maxDots - 1) {
                this.activeSlide = 0;
            } else {
                this.activeSlide++;
            }
        },
        prev() {
            if (this.activeSlide <= 0) {
                this.activeSlide = this.maxDots - 1;
            } else {
                this.activeSlide--;
            }
        },
        goTo(idx) {
            this.activeSlide = idx;
        }
    }
}
</script>
@endif
