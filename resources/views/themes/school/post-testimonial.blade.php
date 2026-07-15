<link rel="stylesheet" href="{{ asset('css/tailwind-client.css') }}?v={{ @filemtime(public_path('css/tailwind-client.css')) ?: '1' }}">
<style>
    @keyframes float-slow {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    @keyframes float-reverse {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(20px) scale(0.95); }
    }
    .animate-float-1 {
        animation: float-slow 9s ease-in-out infinite;
    }
    .animate-float-2 {
        animation: float-reverse 11s ease-in-out infinite;
    }
</style>

{{-- Page Hero Header Banner with Background Image --}}
<div class="fullbleed">
    <section class="hero">
        <div class="hero-slides">
            <div class="hero-slide is-active" style="background-image:url('https://prayaaginternationalschool.com/wp-content/uploads/2022/01/About-Prayaag-International-School.webp')"></div>
        </div>
        <div class="container">
            <span class="hero-kicker" data-reveal>Share Your Experience</span>
            <h1 data-reveal data-reveal-delay="1">Post Your Testimonial</h1>
            <p class="hero-tag" data-reveal data-reveal-delay="2">Help other parents discover the Prayaag International School community.</p>
        </div>
    </section>
</div>

{{-- Main Page Grid Content --}}
<div class="relative min-h-screen bg-slate-50/50 py-12 md:py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    {{-- Animated Premium Gradient Blobs --}}
    <div class="absolute -left-32 top-10 w-96 h-96 rounded-full bg-gradient-to-tr from-purple-400/20 to-pink-400/20 blur-3xl pointer-events-none animate-float-1"></div>
    <div class="absolute -right-32 bottom-20 w-96 h-96 rounded-full bg-gradient-to-br from-indigo-400/20 to-blue-400/20 blur-3xl pointer-events-none animate-float-2"></div>
    <div class="absolute left-1/3 top-1/2 w-80 h-80 rounded-full bg-amber-200/10 blur-3xl pointer-events-none"></div>

    {{-- Subtle Page Grid --}}
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30 pointer-events-none"></div>

    <div class="max-w-6xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            
            {{-- Left Column: Brand Showcase --}}
            <div class="lg:col-span-5 space-y-8 text-center lg:text-left">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 shadow-sm">
                        ✨ Share Your Voice
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-black text-gray-800 tracking-tight leading-none font-serif">
                        Your Story <br class="hidden lg:inline"/>
                        <span class="bg-gradient-to-r from-purple-700 via-indigo-800 to-blue-900 text-transparent bg-clip-text">Inspires Others.</span>
                    </h2>
                    <p class="text-sm sm:text-base text-gray-500 font-medium max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        At Prayaag International School, we cherish the partnership between parents and educators. Your feedback guides future families in choosing the right path for their children.
                    </p>
                </div>

                {{-- Interactive Stats badges --}}
                <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto lg:mx-0">
                    <div class="bg-white/80 backdrop-blur-md border border-gray-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="text-2xl font-black text-indigo-900">4.9 / 5</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Parent Rating</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md border border-gray-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="text-2xl font-black text-purple-900">100%</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Verified Reviews</div>
                    </div>
                </div>

                {{-- Key Features Stepper --}}
                <div class="hidden lg:block space-y-4 bg-white/40 backdrop-blur-sm p-6 rounded-3xl border border-white/50 shadow-sm">
                    <h4 class="text-xs font-bold text-indigo-950 uppercase tracking-wider">Review Submission Guide</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-900 text-white text-xs font-extrabold shadow-sm">1</span>
                            <span class="text-xs font-semibold text-gray-600">Provide parent contact and child class info</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-900 text-white text-xs font-extrabold shadow-sm">2</span>
                            <span class="text-xs font-semibold text-gray-600">Write an honest review (min 50 characters)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-900 text-white text-xs font-extrabold shadow-sm">3</span>
                            <span class="text-xs font-semibold text-gray-600">Attach an optional profile photo for cropping</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Testimonial Form Wrapper --}}
            <div class="lg:col-span-7">
                <div class="relative">
                    {{-- Soft outer card glow --}}
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-3xl blur-2xl opacity-10 pointer-events-none"></div>
                    
                    {{-- Form Component --}}
                    <div class="relative">
                        <x-testimonial-form />
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
