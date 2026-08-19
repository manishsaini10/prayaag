@extends('admin.layout')

@section('title', 'Edit Video Testimonial')

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

    {{-- Video Preview --}}
    @if($video->video_embed_url)
    <div class="card p-4">
        <h3 class="font-bold text-sm mb-3" style="color:var(--text)">Current Video Preview</h3>
        <div class="aspect-video max-w-xl rounded-lg overflow-hidden bg-black">
            <iframe src="{{ $video->video_embed_url }}" width="100%" height="100%" frameborder="0"
                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen loading="lazy" title="Video preview"></iframe>
        </div>
        <div class="mt-2 flex items-center gap-2">
            <span class="text-xs" style="color:var(--text-muted)">Provider: <strong>{{ str_replace('_', ' ', $video->video_provider) }}</strong></span>
            <span class="text-xs" style="color:var(--text-muted)">ID: <code>{{ $video->video_external_id }}</code></span>
            <a href="{{ $video->video_embed_url }}" target="_blank" rel="noopener" class="text-xs underline" style="color:var(--primary)">Open in new tab</a>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.video-testimonials.update', $video->id) }}">
        @csrf @method('PATCH')

        {{-- Basic Info --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-bold text-base" style="color:var(--text)">📹 Video Information</h2>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $video->title) }}" required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Update Video Link / ID (Optional — leave blank to keep current)</label>
                <input type="text" name="video_source" value="{{ old('video_source') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                       placeholder="https://youtu.be/... or 11-character video ID (Current ID: {{ $video->video_external_id }})">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Orientation</label>
                    <select name="orientation" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                        <option value="landscape" {{ $video->orientation === 'landscape' ? 'selected' : '' }}>Landscape (16:9)</option>
                        <option value="portrait" {{ $video->orientation === 'portrait' ? 'selected' : '' }}>Portrait (9:16)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $video->sort_order) }}" min="0"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
            </div>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $video->is_featured) ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium" style="color:var(--text)">⭐ Mark as Featured</span>
            </label>
        </div>

        {{-- Student --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-bold text-base" style="color:var(--text)">🎓 Student Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Student Name</label>
                    <input type="text" name="student_name" value="{{ old('student_name', $video->student_name) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Class / Grade</label>
                    <input type="text" name="class_grade" value="{{ old('class_grade', $video->class_grade) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Submitter --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-bold text-base" style="color:var(--text)">👤 Submitted By</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Name</label>
                    <input type="text" name="submitted_by_name" value="{{ old('submitted_by_name', $video->submitted_by_name) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Email</label>
                    <input type="email" name="submitted_by_email" value="{{ old('submitted_by_email', $video->submitted_by_email) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Phone</label>
                    <input type="text" name="submitted_by_phone" value="{{ old('submitted_by_phone', $video->submitted_by_phone) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
                </div>
            </div>
        </div>

        {{-- Consent --}}
        <div class="card p-6 space-y-4 border-2 border-amber-200">
            <h2 class="font-bold text-base" style="color:var(--text)">⚖️ Consent</h2>
            @if($video->consent_confirmed)
            <div class="flex items-center gap-2 p-3 rounded-lg bg-green-50 border border-green-200">
                <svg viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="2" width="18" height="18"><path d="M20 6L9 17l-5-5"/></svg>
                <span class="text-sm font-semibold text-green-800">Consent confirmed by: {{ $video->consent_signed_by }}</span>
                <span class="text-xs text-green-600">on {{ $video->consent_signed_at?->format('M j, Y') }}</span>
            </div>
            @else
            <div class="flex items-center gap-2 p-3 rounded-lg bg-red-50 border border-red-200">
                <svg viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2" width="18" height="18"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span class="text-sm font-semibold text-red-800">⚠ No consent recorded — cannot approve until consent is confirmed.</span>
            </div>
            @endif
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Consent Signed By</label>
                <input type="text" name="consent_signed_by" value="{{ old('consent_signed_by', $video->consent_signed_by) }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none">
            </div>
            <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg bg-amber-50 border border-amber-200">
                <input type="checkbox" name="consent_confirmed" value="1"
                       {{ old('consent_confirmed', $video->consent_confirmed) ? 'checked' : '' }} class="mt-0.5 rounded shrink-0">
                <span class="text-sm" style="color:var(--text)">Consent has been obtained from the parent/guardian named above.</span>
            </label>
        </div>

        {{-- CTA --}}
        <div class="card p-6 space-y-4">
            <h2 class="font-bold text-base" style="color:var(--text)">🔗 Call-to-Action</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Button Label</label>
                    <input type="text" name="cta_label" value="{{ old('cta_label', $video->cta_label) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none" placeholder="Apply Now">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Button URL</label>
                    <input type="url" name="cta_url" value="{{ old('cta_url', $video->cta_url) }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none" placeholder="https://...">
                </div>
            </div>
        </div>

        {{-- Tags --}}
        <div class="card p-6 space-y-4"
             x-data="{ tags: {{ json_encode(old('tags', $video->tags->map(fn($t) => ['tag_type' => $t->tag_type, 'tag_value' => $t->tag_value])->values()->toArray())) }} }">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-base" style="color:var(--text)">🏷️ Tags</h2>
                <button type="button" @click="tags.push({tag_type:'program',tag_value:''})"
                        class="btn-secondary py-1 px-3 text-xs">+ Add Tag</button>
            </div>
            <template x-for="(tag, i) in tags" :key="i">
                <div class="flex gap-2 items-center">
                    <select :name="'tags[' + i + '][tag_type]'" x-model="tag.tag_type" class="border rounded-lg px-3 py-2 text-sm">
                        <option value="program">Program</option>
                        <option value="event">Event</option>
                        <option value="class">Class</option>
                        <option value="department">Department</option>
                        <option value="custom">Custom</option>
                    </select>
                    <input :name="'tags[' + i + '][tag_value]'" x-model="tag.tag_value" type="text"
                           class="flex-1 border rounded-lg px-3 py-2 text-sm focus:outline-none"
                           placeholder="e.g. Admissions 2026-27">
                    <button type="button" @click="tags.splice(i,1)" class="text-red-500 hover:text-red-700">✕</button>
                </div>
            </template>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center gap-3">
            <button type="submit" form="delete-video-form" class="text-sm text-red-600 hover:underline"
                    onclick="return confirm('Delete this video testimonial permanently?')">Delete this video</button>
            <div class="flex gap-3">
                <a href="{{ route('admin.video-testimonials.index') }}" class="btn-secondary py-2 px-5 text-sm">Cancel</a>
                <button type="submit" class="btn-primary py-2 px-6 text-sm font-semibold">Save Changes</button>
            </div>
        </div>
    </form>

    <form id="delete-video-form" method="POST" action="{{ route('admin.video-testimonials.destroy', $video->id) }}" class="hidden">
        @csrf @method('DELETE')
    </form>
</div>
@endsection
