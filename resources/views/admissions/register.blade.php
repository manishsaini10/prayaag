{{-- resources/views/admissions/register.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Registration Form — {{ config('app.school_name', 'Prayaag International School') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-100 min-h-screen py-10">

<div class="max-w-4xl mx-auto px-4">

    {{-- School Header --}}
    <div class="text-center mb-6">
        {{-- School Logo --}}
        <a href="/" class="inline-block">
            <img src="https://prayaaginternationalschool.com/wp-content/uploads/2021/12/prayaag-school-logo.png" alt="Prayaag International School Logo" class="h-24 mx-auto mb-2 object-contain" onerror="this.onerror=null; this.src='{{ asset('storage/media/imported/logo.png') }}';">
        </a>
        <h1 class="text-3xl font-bold text-gray-800 tracking-wide">{{ config('app.school_name', 'PRAYAAG INTERNATIONAL SCHOOL') }}</h1>
        <p class="text-xl text-amber-600 font-semibold mt-1">Academic Session {{ date('Y') }}-{{ date('Y') + 1 }}</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">

        {{-- Card Header --}}
        <div class="bg-gradient-to-r from-sky-100 to-indigo-100 px-6 py-4 border-b border-sky-200">
            <h2 class="text-xl font-bold text-sky-700 text-center uppercase tracking-wider">Online Registration Form</h2>
        </div>

        {{-- Success Notification --}}
        @if (session('success') || session('registration_sent'))
            <div class="mx-6 mt-6 p-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-900 rounded-r-xl shadow-md space-y-4">
                <div class="flex items-start gap-4">
                    <span class="text-3xl p-2 bg-emerald-100 rounded-full flex-shrink-0">🎉</span>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-emerald-900">Registration Submitted Successfully!</h3>
                        <p class="text-sm font-medium text-emerald-800 mt-1">
                            {{ session('success') ?: 'Thank you for submitting the registration form.' }}
                        </p>
                        <p class="text-sm text-emerald-700 mt-2 leading-relaxed">
                            Our admissions team will get in touch with you shortly. To learn more about our school, feel free to explore our official website.
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-3 border-t border-emerald-200/70 flex flex-wrap gap-3 items-center">
                    <a href="/" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow transition transform active:scale-95">
                        <span>Explore Website</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="/admissions" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-sm font-semibold shadow-sm transition">
                        <span>View Admissions Details</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- Error Summary --}}
        @if ($errors->any())
            <div class="mx-6 mt-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admissions.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-5">

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span>:</label>
                    <select name="applying_for" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-white">
                        <option value="">-Select-</option>
                        <option value="Nursery" {{ old('applying_for') == 'Nursery' ? 'selected' : '' }}>Nursery</option>
                        <option value="LKG" {{ old('applying_for') == 'LKG' ? 'selected' : '' }}>LKG</option>
                        <option value="UKG" {{ old('applying_for') == 'UKG' ? 'selected' : '' }}>UKG</option>
                        <option value="Class 1" {{ old('applying_for') == 'Class 1' ? 'selected' : '' }}>Class 1</option>
                        <option value="Class 2" {{ old('applying_for') == 'Class 2' ? 'selected' : '' }}>Class 2</option>
                        <option value="Class 3" {{ old('applying_for') == 'Class 3' ? 'selected' : '' }}>Class 3</option>
                        <option value="Class 4" {{ old('applying_for') == 'Class 4' ? 'selected' : '' }}>Class 4</option>
                        <option value="Class 5" {{ old('applying_for') == 'Class 5' ? 'selected' : '' }}>Class 5</option>
                        <option value="Class 6" {{ old('applying_for') == 'Class 6' ? 'selected' : '' }}>Class 6</option>
                        <option value="Class 7" {{ old('applying_for') == 'Class 7' ? 'selected' : '' }}>Class 7</option>
                        <option value="Class 8" {{ old('applying_for') == 'Class 8' ? 'selected' : '' }}>Class 8</option>
                        <option value="Class 9" {{ old('applying_for') == 'Class 9' ? 'selected' : '' }}>Class 9</option>
                        <option value="Class 10" {{ old('applying_for') == 'Class 10' ? 'selected' : '' }}>Class 10</option>
                        <option value="Class 11" {{ old('applying_for') == 'Class 11' ? 'selected' : '' }}>Class 11</option>
                        <option value="Class 12" {{ old('applying_for') == 'Class 12' ? 'selected' : '' }}>Class 12</option>
                    </select>
                </div>

                {{-- Student Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Name:<span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" placeholder="Student Name" required value="{{ old('full_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth :</label>
                    <div class="flex gap-2">
                        <select name="dob_day" class="w-1/3 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-white">
                            <option value="">DD</option>
                            @for ($d = 1; $d <= 31; $d++)
                                <option value="{{ $d }}" {{ old('dob_day') == $d ? 'selected' : '' }}>{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select name="dob_month" class="w-1/3 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-white">
                            <option value="">MM</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('dob_month') == $m ? 'selected' : '' }}>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <select name="dob_year" class="w-1/3 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-white">
                            <option value="">YYYY</option>
                            @for ($y = date('Y'); $y >= date('Y') - 25; $y--)
                                <option value="{{ $y }}" {{ old('dob_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender:<span class="text-red-500">*</span></label>
                    <select name="gender" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none bg-white">
                        <option value="">--GENDER --</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Current School --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current School:</label>
                    <input type="text" name="previous_school" placeholder="Current School" value="{{ old('previous_school') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

                <div></div>

                {{-- Father's Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Father's Name:<span class="text-red-500">*</span></label>
                    <input type="text" name="father_name" placeholder="Father Name" required value="{{ old('father_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

                {{-- Mother's Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mother's Name:<span class="text-red-500">*</span></label>
                    <input type="text" name="mother_name" placeholder="Mother Name" required value="{{ old('mother_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

                {{-- Mobile --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile:<span class="text-red-500">*</span></label>
                    <input type="text" name="mobile" placeholder="Mobile" required maxlength="10" value="{{ old('mobile') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address:</label>
                    <input type="text" name="address" placeholder="Address" value="{{ old('address') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

                {{-- Email Address (Optional extra field for auto-notifications) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address:</label>
                    <input type="email" name="email" placeholder="Email Address (Optional)" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-sky-400 focus:outline-none">
                </div>

            </div>

            {{-- Captcha --}}
            @php
                $sysSettings = app(\App\Core\Settings\SettingsManager::class);
                $isCaptchaOn = filter_var($sysSettings->get('recaptcha_enabled', true), FILTER_VALIDATE_BOOLEAN);
                $captchaKey  = $sysSettings->get('recaptcha_site_key') ?: config('services.recaptcha.site_key');
            @endphp

            @if ($isCaptchaOn && !empty($captchaKey))
            <div class="mt-8">
                <div class="g-recaptcha" data-sitekey="{{ $captchaKey }}"></div>
            </div>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            @endif

            {{-- Buttons --}}
            <div class="mt-8 flex gap-4 border-t border-gray-100 pt-6">
                <button type="submit"
                        class="px-8 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md transition transform active:scale-95">
                    Submit Registration
                </button>
                <button type="reset"
                        class="px-8 py-2.5 rounded-lg bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold shadow-md transition">
                    Reset Form
                </button>
                <a href="/" class="px-6 py-2.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold transition ml-auto">
                    Back to Home
                </a>
            </div>
        </form>
    </div>

    <p class="text-center text-sm text-gray-500 mt-6">
        Powered By <span class="font-semibold text-gray-700">Prayaag International School, Panipat</span>
    </p>
</div>

</body>
</html>
