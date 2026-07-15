@props([
    'formTitle'       => 'Share Your Experience',
    'formDescription' => 'Let us know your feedback! Your values and stories help other parents discover the vibrant Prayaag International School community.',
    'buttonText'      => 'Submit Experience',
    'consentText'     => '',
])

<link rel="stylesheet" href="{{ asset('css/tailwind-client.css') }}?v={{ @filemtime(public_path('css/tailwind-client.css')) ?: '1' }}">

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<div class="relative max-w-3xl mx-auto" x-data="testimonialForm()">

    {{-- Success State Card --}}
    <div x-show="success" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-8 shadow-lg text-center space-y-6" style="display: none;" x-cloak>
        <div class="w-16 h-16 bg-emerald-50 border border-emerald-100 rounded-full flex items-center justify-center mx-auto text-emerald-600 shadow-inner">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" class="w-8 h-8"><path d="M20 6 9 17l-5-5"/></svg>
        </div>
        <div class="space-y-2">
            <h3 class="text-2xl font-black text-gray-800 tracking-tight">Thank You!</h3>
            <p class="text-xs text-gray-500 font-medium leading-relaxed max-w-md mx-auto" x-text="message"></p>
        </div>
        <div class="pt-2">
            <button type="button" @click="success = false" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-purple-700 to-indigo-800 hover:from-purple-800 hover:to-indigo-900 text-white text-xs font-bold px-6 py-2.5 rounded-full transition-all shadow-md hover:shadow-lg">
                Submit Another Testimonial
            </button>
        </div>
    </div>

    {{-- Form Container --}}
    <div x-show="!success" class="relative bg-white border border-gray-100 rounded-2xl md:rounded-3xl p-5 sm:p-6 md:p-8 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
        {{-- Decorative Background Glow --}}
        <div class="absolute -right-20 -top-20 w-48 h-48 rounded-full bg-purple-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-48 h-48 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>

        <h3 class="text-xl sm:text-2xl font-black text-gray-800 tracking-tight mb-1 flex items-center gap-2">
            <span>❤️</span> {{ $formTitle }}
        </h3>
        @if($formDescription)
        <p class="text-[11px] sm:text-xs text-gray-400 mb-6 font-medium leading-relaxed">
            {{ $formDescription }}
        </p>
        @endif

        {{-- Errors Alert --}}
        <div x-show="errors.length" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="p-4 mb-5 rounded-xl sm:rounded-2xl text-xs font-semibold text-rose-800 bg-rose-50 border border-rose-100" style="display: none;" x-cloak>
            <div class="flex gap-2 font-bold mb-1.5 text-rose-900">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="w-4 h-4"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6m0-6 6 6"/></svg>
                <span>Please correct the following errors:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                <template x-for="err in errors">
                    <li x-text="err"></li>
                </template>
            </ul>
        </div>

        <form @submit.prevent="submitForm" class="space-y-4 sm:space-y-5" enctype="multipart/form-data">
            {{-- Card 1: Submitter Info --}}
            <div class="bg-gray-50/50 p-4 rounded-xl sm:rounded-2xl border border-gray-100/80 space-y-4">
            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">1. Your Profile</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                {{-- Parent Name --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Parent Name *</label>
                    <input type="text" x-model="form.name" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white" placeholder="Full Name" required>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Phone Number *</label>
                    <input type="tel" x-model="form.phone" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white" placeholder="10-digit Mobile" required>
                </div>

                {{-- Relationship --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Relationship</label>
                    <select x-model="form.relation" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white">
                        <option value="">Relation...</option>
                        <option value="Mother">Mother</option>
                        <option value="Father">Father</option>
                        <option value="Guardian">Guardian</option>
                    </select>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Email Address</label>
                    <input type="email" x-model="form.email" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white" placeholder="email@address.com">
                </div>

                {{-- Child's Name --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Child's Name</label>
                    <input type="text" x-model="form.student_name" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white" placeholder="Student Name">
                </div>

                {{-- Child's Class --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Child's Class</label>
                    <input type="text" x-model="form.class" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white" placeholder="e.g. Grade III-A">
                </div>
            </div>
        </div>

        {{-- Card 2: Testimonial Message --}}
        <div class="bg-gray-50/50 p-4 rounded-xl sm:rounded-2xl border border-gray-100/80 space-y-4">
            <h4 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">2. Your Message & Review</h4>
            
            {{-- Title --}}
            <div>
                <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Headline / Title</label>
                <input type="text" x-model="form.title" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white" placeholder="e.g. Great academic progress and helpful staff">
            </div>

            {{-- Experience --}}
            <div>
                <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Your Detailed Experience *</label>
                <textarea x-model="form.testimonial" rows="4" class="w-full text-xs px-3 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition-all duration-200 bg-white leading-relaxed resize-none" placeholder="Describe what makes Prayaag special for your child..." required></textarea>
            </div>

            {{-- Rating and Custom uploader --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                {{-- Glowing Interactive Stars --}}
                @if(\App\Core\Settings\Settings::get('testimonial_enable_rating', true))
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Star Rating</label>
                    <div class="flex gap-1 items-center">
                        <template x-for="star in 5">
                            <button type="button" @click="form.rating = star" class="text-2xl transition-all duration-150 transform hover:scale-125 focus:outline-none" :class="star <= form.rating ? 'text-amber-500 drop-shadow-sm' : 'text-gray-200'">
                                ★
                            </button>
                        </template>
                        <span class="text-xs font-bold text-gray-600 ml-2" x-text="form.rating + '.0 Star'"></span>
                    </div>
                </div>
                @endif

                {{-- SaaS-style custom uploader --}}
                <div>
                    <label class="block text-[10px] sm:text-[11px] font-bold text-gray-500 mb-1">Upload Photo</label>
                    {{-- Native input is completely hidden --}}
                    <input type="file" x-ref="fileInput" @change="handleFile($event)" class="hidden" accept="image/*">
                    {{-- Click triggers hidden file input --}}
                    <div @click="$refs.fileInput.click()" class="relative flex items-center justify-center border-2 border-dashed border-gray-200 hover:border-purple-500 hover:bg-purple-50/20 transition-all duration-200 rounded-xl p-3 text-center cursor-pointer bg-white">
                        <div class="flex items-center gap-2">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5 text-purple-600"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            <span class="text-xs font-semibold text-gray-500 truncate max-w-[180px]" x-text="photoFile ? photoFile.name : 'Choose Parent Photo'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Consent Agreement Checkbox --}}
        @if($consentText)
        <label class="flex items-start gap-2.5 cursor-pointer">
            <input type="checkbox" required class="mt-0.5 rounded text-purple-600 focus:ring-purple-400">
            <span class="text-[10px] text-gray-400 font-medium leading-relaxed">{{ $consentText }}</span>
        </label>
        @endif

        {{-- Interactive Gradient Submit Button --}}
        <div>
            <button type="submit" class="w-full bg-gradient-to-r from-purple-700 to-indigo-800 hover:from-purple-800 hover:to-indigo-900 text-white font-extrabold py-3 px-6 rounded-xl text-sm transition-all duration-200 shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-purple-200 active:scale-98 flex items-center justify-center gap-2" :disabled="submitting">
                <template x-if="!submitting">
                    <span class="flex items-center gap-1.5">
                        {{ $buttonText }}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" class="w-3.5 h-3.5"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                    </span>
                </template>
                <template x-if="submitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    </span>
                </template>
            </button>
        </div>
    </form>
    </div>
</div>

<script>
function testimonialForm() {
    return {
        form: {
            name: '',
            student_name: '',
            relation: '',
            class: '',
            phone: '',
            email: '',
            title: '',
            testimonial: '',
            rating: 5,
        },
        photoFile: null,
        submitting: false,
        success: false,
        message: '',
        errors: [],
        handleFile(e) {
            this.photoFile = e.target.files[0];
        },
        submitForm() {
            this.submitting = true;
            this.errors = [];
            this.success = false;

            const formData = new FormData();
            Object.keys(this.form).forEach(key => {
                formData.append(key, this.form[key]);
            });

            if (this.photoFile) {
                formData.append('photo', this.photoFile);
            }

            fetch('/testimonials', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                this.submitting = false;
                if (res.status === 200 && res.body.success) {
                    this.success = true;
                    this.message = res.body.message;
                    // Reset Form
                    this.form = {
                        name: '',
                        student_name: '',
                        relation: '',
                        class: '',
                        phone: '',
                        email: '',
                        title: '',
                        testimonial: '',
                        rating: 5,
                    };
                    this.photoFile = null;
                } else {
                    if (res.body.errors && typeof res.body.errors === 'object') {
                        this.errors = Object.values(res.body.errors).flat();
                    } else {
                        this.errors = [res.body.message || 'An error occurred. Please try again.'];
                    }
                }
            })
            .catch(err => {
                this.submitting = false;
                this.errors = ['Connection lost. Please try again later.'];
            });
        }
    }
}
</script>
