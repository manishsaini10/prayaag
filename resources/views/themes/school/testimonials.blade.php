<div class="max-w-6xl mx-auto px-4 md:px-6 py-12">
    {{-- Schema.org structured data --}}
    @php
        $totalCount = $ratingStats['count'] ?? 0;
        $avgRating = $ratingStats['avg'] ?? 5.0;
    @endphp
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "Prayaag International School - Parent Reviews",
        "review": [
            @foreach($testimonials as $i => $t)
            {
                "@type": "Review",
                "reviewRating": {
                    "@type": "Rating",
                    "ratingValue": "{{ $t->rating }}",
                    "bestRating": "5"
                },
                "author": {
                    "@type": "Person",
                    "name": "{{ $t->name }}"
                },
                "reviewBody": {{ json_encode($t->testimonial) }}
            }@if(!$loop->last),@endif
            @endforeach
        ],
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "{{ $avgRating }}",
            "bestRating": "5",
            "ratingCount": "{{ $totalCount }}"
        }
    }
    </script>

    {{-- Page Header --}}
    <div class="border-b pb-8 mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800">Parents Testimonials</h1>
        <p class="text-sm text-gray-500 mt-2">See what our parent community says about academics, safety, and student growth at Prayaag.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left 2 Columns: Filters & Testimonial List --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Filter Panel --}}
            <form action="{{ route('testimonials.index') }}" method="GET" class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2 items-center">
                    {{-- Search --}}
                    <input type="text" name="q" value="{{ $search }}" placeholder="Search reviews..." class="text-xs px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 focus:ring-primary bg-white w-40">
                    
                    {{-- Rating --}}
                    <select name="rating" class="text-xs px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 focus:ring-primary bg-white">
                        <option value="">All Ratings</option>
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}" {{ $rating == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                        @endfor
                    </select>

                    {{-- Class --}}
                    <input type="text" name="class" value="{{ $class }}" placeholder="Filter by Class" class="text-xs px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 focus:ring-primary bg-white w-32">
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('testimonials.index') }}" class="px-3 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border rounded-lg">Clear</a>
                    <button type="submit" class="btn-primary px-4 py-2 text-xs font-bold rounded-lg">Apply</button>
                </div>
            </form>

            {{-- Testimonial Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($testimonials as $t)
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between space-y-4">
                        <div>
                            {{-- Star Rating --}}
                            <div class="text-amber-500 text-xs font-bold mb-2">
                                {!! str_repeat('★', $t->rating) !!}{!! str_repeat('☆', 5 - $t->rating) !!}
                            </div>
                            
                            @if($t->title)
                                <h4 class="font-bold text-gray-800 text-sm mb-1">"{{ $t->title }}"</h4>
                            @endif
                            
                            <p class="text-gray-600 text-xs italic leading-relaxed">
                                "{{ $t->testimonial }}"
                            </p>
                        </div>

                        {{-- Metadata --}}
                        <div class="flex items-center gap-3 border-t pt-3 border-gray-50">
                            @if($t->image)
                                <img src="{{ asset($t->image) }}" class="w-8 h-8 rounded-full object-cover border">
                            @else
                                <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-700 font-bold flex items-center justify-center text-xs uppercase border border-purple-100">
                                    {{ substr($t->name, 0, 2) }}
                                </div>
                            @endif
                            <div>
                                <h5 class="font-bold text-gray-800 text-xs flex items-center gap-1">
                                    <span>{{ $t->name }}</span>
                                    @if($t->is_verified)
                                    <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1 py-0.2 rounded border border-emerald-100 uppercase" title="Verified Admission Record">
                                        ✓ Verified
                                    </span>
                                    @endif
                                </h5>
                                <span class="text-[9px] text-gray-400 block font-semibold">{{ $t->role }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-12 text-center text-gray-400 border border-dashed rounded-2xl bg-gray-50">
                        No published testimonials matching the criteria were found.
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="pt-4">
                {{ $testimonials->appends(request()->query())->links() }}
            </div>
        </div>

        {{-- Right Column: Form Submit Component --}}
        <div>
            <x-testimonial-form />
        </div>
    </div>
</div>
