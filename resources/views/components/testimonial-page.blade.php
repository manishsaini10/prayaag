@props([
    'settings' => [],
])

@php
    $accent       = $settings['hero_accent_text'] ?? 'Share Your Voice';
    $heading      = $settings['hero_heading'] ?? 'Your Story <br class="hidden lg:inline"/> <span class="bg-gradient-to-r from-purple-700 via-indigo-800 to-blue-900 text-transparent bg-clip-text">Inspires Others.</span>';
    $desc         = $settings['hero_description'] ?? 'At Prayaag International School, we cherish the partnership between parents and educators. Your feedback guides future families in choosing the right path for their children.';
    $showRating   = $settings['show_rating_cards'] ?? true;
    $ratingVal    = $settings['rating_value'] ?? '4.9 / 5';
    $ratingLabel  = $settings['rating_label'] ?? 'Parent Rating';
    $verifiedVal  = $settings['verified_value'] ?? '100%';
    $verifiedLabel= $settings['verified_label'] ?? 'Verified Reviews';
    $showGuide    = $settings['show_guide'] ?? true;
    $guideTitle   = $settings['guide_title'] ?? 'Review Submission Guide';
    $step1        = $settings['guide_step_1'] ?? 'Provide parent contact and child class info';
    $step2        = $settings['guide_step_2'] ?? 'Write an honest review (min 50 characters)';
    $step3        = $settings['guide_step_3'] ?? 'Attach an optional profile photo for cropping';
    $bgStyle      = $settings['background_style'] ?? 'default';
    $formTitle    = $settings['form_title'] ?? 'Share Your Experience';
    $formDesc     = $settings['form_description'] ?? 'Let us know your feedback! Your values and stories help other parents discover the vibrant Prayaag International School community.';
    $btnText      = $settings['form_button_text'] ?? 'Submit Experience';
    $consentText  = $settings['consent_text'] ?? '';
@endphp

<style>
    @keyframes tf-float-slow {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(-20px) scale(1.05); }
    }
    @keyframes tf-float-reverse {
        0%, 100% { transform: translateY(0px) scale(1); }
        50% { transform: translateY(20px) scale(0.95); }
    }
    .tf-float-1 { animation: tf-float-slow 9s ease-in-out infinite; }
    .tf-float-2 { animation: tf-float-reverse 11s ease-in-out infinite; }
</style>

<div class="relative min-h-screen bg-slate-50/50 py-12 md:py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
    @if($bgStyle === 'default')
    <div class="absolute -left-32 top-10 w-96 h-96 rounded-full bg-gradient-to-tr from-purple-400/20 to-pink-400/20 blur-3xl pointer-events-none tf-float-1"></div>
    <div class="absolute -right-32 bottom-20 w-96 h-96 rounded-full bg-gradient-to-br from-indigo-400/20 to-blue-400/20 blur-3xl pointer-events-none tf-float-2"></div>
    <div class="absolute left-1/3 top-1/2 w-80 h-80 rounded-full bg-amber-200/10 blur-3xl pointer-events-none"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30 pointer-events-none"></div>
    @elseif($bgStyle === 'vibrant')
    <div class="absolute -left-20 top-10 w-80 h-80 rounded-full bg-fuchsia-300/30 blur-3xl pointer-events-none tf-float-1"></div>
    <div class="absolute -right-20 bottom-10 w-80 h-80 rounded-full bg-cyan-300/30 blur-3xl pointer-events-none tf-float-2"></div>
    <div class="absolute inset-0 bg-gradient-to-br from-fuchsia-50/40 via-transparent to-cyan-50/40 pointer-events-none"></div>
    @endif

    <div class="max-w-6xl mx-auto relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            <div class="lg:col-span-5 space-y-8 text-center lg:text-left">
                <div class="space-y-4">
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 shadow-sm">&#10024; {{ $accent }}</span>
                    <h2 class="text-4xl sm:text-5xl font-black text-gray-800 tracking-tight leading-none font-serif">{!! $heading !!}</h2>
                    @if($desc)
                    <p class="text-sm sm:text-base text-gray-500 font-medium max-w-xl mx-auto lg:mx-0 leading-relaxed">{{ $desc }}</p>
                    @endif
                </div>

                @if($showRating)
                <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto lg:mx-0">
                    <div class="bg-white/80 backdrop-blur-md border border-gray-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="text-2xl font-black text-indigo-900">{{ $ratingVal }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $ratingLabel }}</div>
                    </div>
                    <div class="bg-white/80 backdrop-blur-md border border-gray-100 p-4 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="text-2xl font-black text-purple-900">{{ $verifiedVal }}</div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $verifiedLabel }}</div>
                    </div>
                </div>
                @endif

                @if($showGuide)
                <div class="hidden lg:block space-y-4 bg-white/40 backdrop-blur-sm p-6 rounded-3xl border border-white/50 shadow-sm">
                    <h4 class="text-xs font-bold text-indigo-950 uppercase tracking-wider">{{ $guideTitle }}</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-900 text-white text-xs font-extrabold shadow-sm">1</span>
                            <span class="text-xs font-semibold text-gray-600">{{ $step1 }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-900 text-white text-xs font-extrabold shadow-sm">2</span>
                            <span class="text-xs font-semibold text-gray-600">{{ $step2 }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-indigo-900 text-white text-xs font-extrabold shadow-sm">3</span>
                            <span class="text-xs font-semibold text-gray-600">{{ $step3 }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-7">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-3xl blur-2xl opacity-10 pointer-events-none"></div>
                    <div class="relative">
                        <x-testimonial-form
                            :form-title="$formTitle"
                            :form-description="$formDesc"
                            :button-text="$btnText"
                            :consent-text="$consentText"
                        />
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
