@extends('admin.layout')

@section('title', 'Conversational Assistants')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.chatbot.assistant.save') }}" method="POST" class="card space-y-8">
        @csrf

        {{-- Section 1: Admission Assistant --}}
        <div class="space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold" style="color:var(--text)">Conversational Admission Assistant</h3>
                    <p class="text-sm mt-1" style="color:var(--text-muted)">Collect visitor parent details naturally and save them as prospective admission leads.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings_data[assistants][enable_admission]" value="1" 
                           {{ ($settings->settings_data['assistants']['enable_admission'] ?? false) ? 'checked' : '' }} 
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold" style="color:var(--text)">Initial Greeting Message</label>
                <textarea name="settings_data[assistants][admission_greeting]" rows="3" class="w-full text-sm rounded-lg" 
                          placeholder="I'd love to help with admissions at Prayaag School! 🌟 I'll collect a few details and our admissions team will be in touch with you shortly.">{{ $settings->settings_data['assistants']['admission_greeting'] ?? '' }}</textarea>
                <p class="text-xs" style="color:var(--text-muted)">Leave empty to use the system default greeting.</p>
            </div>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- Section 2: Job Assistant --}}
        <div class="space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold" style="color:var(--text)">Conversational Job Assistant</h3>
                    <p class="text-sm mt-1" style="color:var(--text-muted)">Pre-qualify candidates applying for teaching and administrative roles at Prayaag School.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="settings_data[assistants][enable_job]" value="1" 
                           {{ ($settings->settings_data['assistants']['enable_job'] ?? false) ? 'checked' : '' }} 
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                </label>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold" style="color:var(--text)">Initial Greeting Message</label>
                <textarea name="settings_data[assistants][job_greeting]" rows="3" class="w-full text-sm rounded-lg" 
                          placeholder="I can help you with job applications at Prayaag School! 🎓 I'll collect some basic details and our HR team will get in touch with you shortly.">{{ $settings->settings_data['assistants']['job_greeting'] ?? '' }}</textarea>
                <p class="text-xs" style="color:var(--text-muted)">Leave empty to use the system default greeting.</p>
            </div>
        </div>

        <hr style="border-top:1px solid var(--border)">

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<style>
/* Modern Tailwind-like peer toggles custom styling */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}
.peer:checked ~ div {
    background-color: #4f46e5;
}
.peer:checked ~ div::after {
    transform: translateX(100%);
}
</style>
@endsection
