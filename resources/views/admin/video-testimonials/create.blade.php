@extends('admin.layout')

@section('title', 'Add Video Testimonial')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.video-testimonials.index') }}" class="text-sm font-medium" style="color:var(--text-muted)">← Video Testimonials</a>
    </div>

    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif
    @if(session('error'))
    <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.video-testimonials.store') }}" x-data="vtForm()" enctype="multipart/form-data">
        @csrf

        {{-- Video Info --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-bold text-base" style="color:var(--text)">📹 Video Information</h2>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                       placeholder="e.g. My experience at Prayaag International School">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Video Source <span class="text-red-500">*</span></label>
                <p class="text-xs mb-2" style="color:var(--text-muted)">Paste a YouTube URL, YouTube video ID, or any embed URL.</p>
                <input type="text" name="video_source" value="{{ old('video_source') }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                       placeholder="https://youtu.be/dQw4w9WgXcQ  or  dQw4w9WgXcQ">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Video Provider</label>
                <select name="video_provider" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                    <option value="youtube_unlisted" {{ config('video.default_provider') === 'youtube_unlisted' ? 'selected' : '' }}>YouTube (Unlisted) — default</option>
                    <option value="instagram_reel">Instagram Reel</option>
                    <option value="cloudflare_stream" {{ config('video.default_provider') === 'cloudflare_stream' ? 'selected' : '' }}>Cloudflare Stream</option>
                    <option value="local">Local Storage (dev only)</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Orientation</label>
                    <select name="orientation" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                        <option value="landscape">Landscape (16:9)</option>
                        <option value="portrait">Portrait (9:16)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium" style="color:var(--text)">⭐ Mark as Featured</span>
            </label>
        </div>

        {{-- Student Info --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-bold text-base" style="color:var(--text)">🎓 Student Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Student Name</label>
                    <input type="text" name="student_name" value="{{ old('student_name') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2" placeholder="Arjun Sharma">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Class / Grade</label>
                    <input type="text" name="class_grade" value="{{ old('class_grade') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2" placeholder="Grade 10 / Class XII">
                </div>
            </div>
        </div>

        {{-- Submitter Info --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-bold text-base" style="color:var(--text)">👤 Submitted By</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Name</label>
                    <input type="text" name="submitted_by_name" value="{{ old('submitted_by_name') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Email</label>
                    <input type="email" name="submitted_by_email" value="{{ old('submitted_by_email') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Phone</label>
                    <input type="text" name="submitted_by_phone" value="{{ old('submitted_by_phone') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                </div>
            </div>
        </div>

        {{-- Consent (mandatory) --}}
        <div class="card p-6 space-y-4 border-2 border-amber-200">
            <h2 class="font-bold text-base" style="color:var(--text)">⚖️ Consent (Legal — Mandatory for Minors)</h2>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Consent Signed By (Parent/Guardian Name)</label>
                <input type="text" name="consent_signed_by" value="{{ old('consent_signed_by') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                       placeholder="Full name of parent or guardian">
            </div>
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg bg-amber-50 border border-amber-200">
                <input type="checkbox" name="consent_confirmed" value="1" {{ old('consent_confirmed') ? 'checked' : '' }} class="mt-0.5 rounded shrink-0">
                <span class="text-sm" style="color:var(--text)">
                    I confirm that the parent/legal guardian named above has provided written consent for this video to be used on the school website.
                    <strong>The Approve button will remain disabled until this box is checked.</strong>
                </span>
            </label>
        </div>

        {{-- CTA --}}
        <div class="card p-6 space-y-4">
            <h2 class="font-bold text-base" style="color:var(--text)">🔗 Call-to-Action Button (optional)</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Button Label</label>
                    <input type="text" name="cta_label" value="{{ old('cta_label') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                           placeholder="Apply Now / Book a Tour">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Button URL</label>
                    <input type="url" name="cta_url" value="{{ old('cta_url') }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                           placeholder="https://...">
                </div>
            </div>
        </div>

        {{-- Tags --}}
        <div class="card p-6 space-y-4" x-data="{ tags: {{ json_encode(old('tags', [])) }} }">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-base" style="color:var(--text)">🏷️ Tags (for filtering in widget)</h2>
                <button type="button" @click="tags.push({tag_type:'program',tag_value:''})"
                        class="btn-secondary py-1 px-3 text-xs">+ Add Tag</button>
            </div>
            <template x-for="(tag, i) in tags" :key="i">
                <div class="flex gap-2 items-center">
                    <select :name="'tags[' + i + '][tag_type]'" x-model="tag.tag_type"
                            class="border rounded-lg px-3 py-2 text-sm focus:outline-none">
                        <option value="program">Program</option>
                        <option value="event">Event</option>
                        <option value="class">Class</option>
                        <option value="department">Department</option>
                        <option value="custom">Custom</option>
                    </select>
                    <input :name="'tags[' + i + '][tag_value]'" x-model="tag.tag_value" type="text"
                           class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none"
                           placeholder="e.g. Admissions 2026-27, Sports Day">
                    <button type="button" @click="tags.splice(i,1)" class="text-red-500 hover:text-red-700">✕</button>
                </div>
            </template>
            <p x-show="tags.length === 0" class="text-sm" style="color:var(--text-muted)">No tags added. Tags help filter this video in the Page Builder widget.</p>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.video-testimonials.index') }}" class="btn-secondary py-2 px-5 text-sm">Cancel</a>
            <button type="submit" class="btn-primary py-2 px-6 text-sm font-semibold">Save Video Testimonial</button>
        </div>
    </form>
</div>
@endsection
