@extends('admin.layout')

@section('title', 'Video Testimonials Settings')

@section('actions')
    <a href="{{ route('admin.video-testimonials.index') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Back to List</span>
    </a>
    <a href="{{ route('admin.video-testimonials.analytics') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M3 18l4-8 4 4 4-6 4 8"/></svg>
        <span>Analytics</span>
    </a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm font-semibold" style="background:#dcfce7;color:#166534">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm font-semibold" style="background:#fee2e2;color:#991b1b">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.video-testimonials.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Section 1: Default Module Layout & Styling --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="font-extrabold text-lg" style="color:var(--text)">🎨 Layout & Design Settings</h3>
                    <p class="text-xs" style="color:var(--text-muted)">Configure default defaults for Page Builder widget instances</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Default Provider</label>
                    <select name="default_provider" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                        <option value="youtube_unlisted" {{ ($settings['default_provider'] ?? '') === 'youtube_unlisted' ? 'selected' : '' }}>YouTube (Unlisted) — Default</option>
                        <option value="instagram_reel" {{ ($settings['default_provider'] ?? '') === 'instagram_reel' ? 'selected' : '' }}>Instagram Reel</option>
                        <option value="cloudflare_stream" {{ ($settings['default_provider'] ?? '') === 'cloudflare_stream' ? 'selected' : '' }}>Cloudflare Stream</option>
                        <option value="local" {{ ($settings['default_provider'] ?? '') === 'local' ? 'selected' : '' }}>Local Storage (Dev)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Default Layout Engine</label>
                    <select name="default_layout" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                        <option value="reel_slider" {{ ($settings['default_layout'] ?? '') === 'reel_slider' ? 'selected' : '' }}>📱 Reel Slider (9:16 Next.js Portrait)</option>
                        <option value="grid" {{ ($settings['default_layout'] ?? '') === 'grid' ? 'selected' : '' }}>🔲 Responsive Grid</option>
                        <option value="carousel" {{ ($settings['default_layout'] ?? '') === 'carousel' ? 'selected' : '' }}>🎠 Horizontal Carousel</option>
                        <option value="masonry" {{ ($settings['default_layout'] ?? '') === 'masonry' ? 'selected' : '' }}>🧱 Staggered Masonry</option>
                        <option value="spotlight" {{ ($settings['default_layout'] ?? '') === 'spotlight' ? 'selected' : '' }}>🌟 Spotlight Hero</option>
                        <option value="wall_mosaic" {{ ($settings['default_layout'] ?? '') === 'wall_mosaic' ? 'selected' : '' }}>🧱 Wall / Mosaic Grid</option>
                        <option value="story_bubble" {{ ($settings['default_layout'] ?? '') === 'story_bubble' ? 'selected' : '' }}>💬 Story Floating Bubble</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Card View Style</label>
                    <select name="default_card_style" class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2">
                        <option value="shadow" {{ ($settings['default_card_style'] ?? '') === 'shadow' ? 'selected' : '' }}>Card with Soft Shadow</option>
                        <option value="minimal" {{ ($settings['default_card_style'] ?? '') === 'minimal' ? 'selected' : '' }}>Minimal Clean</option>
                        <option value="glassmorphism" {{ ($settings['default_card_style'] ?? '') === 'glassmorphism' ? 'selected' : '' }}>Glassmorphism Blur</option>
                        <option value="fullscreen_immersive" {{ ($settings['default_card_style'] ?? '') === 'fullscreen_immersive' ? 'selected' : '' }}>Fullscreen Immersive</option>
                        <option value="story_style" {{ ($settings['default_card_style'] ?? '') === 'story_style' ? 'selected' : '' }}>Instagram Story Style</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Card Border Radius</label>
                    <input type="text" name="border_radius" value="{{ $settings['border_radius'] ?? '1rem' }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2" placeholder="e.g. 1rem or 16px">
                </div>
            </div>
        </div>

        {{-- Section 2: Instagram Reels Integration --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="font-extrabold text-lg" style="color:var(--text)">📸 Instagram Account Integration</h3>
                    <p class="text-xs" style="color:var(--text-muted)">Connect Meta Graph API to auto-fetch recent Instagram Reels</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Instagram User ID</label>
                    <input type="text" name="instagram_user_id" value="{{ $settings['instagram_user_id'] ?? '' }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2" placeholder="e.g. 17841400000000000">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:var(--text)">Instagram Access Token</label>
                    <input type="password" name="instagram_access_token" value="{{ $settings['instagram_access_token'] ?? '' }}"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2" placeholder="EAAB...">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-between">
                <p class="text-xs text-slate-500">Need to manually refresh reels from Instagram Graph API right now?</p>
                <button type="submit" form="syncIgForm" class="btn-secondary text-xs py-2 px-4 inline-flex items-center gap-1.5">
                    🔄 Sync Instagram Reels Now
                </button>
            </div>
        </div>

        {{-- Section 3: Moderation & Public Form Controls --}}
        <div class="card p-6 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="font-extrabold text-lg" style="color:var(--text)">🛡️ Moderation & Consent Controls</h3>
                    <p class="text-xs" style="color:var(--text-muted)">Public student submission and parent consent guardrails</p>
                </div>
            </div>

            <div class="space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="enable_public_submissions" value="1" {{ !empty($settings['enable_public_submissions']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0e2f5e]">
                    <div>
                        <span class="font-semibold text-sm block" style="color:var(--text)">Enable Public Student Submission Form</span>
                        <span class="text-xs text-slate-500">Allows students and parents to submit video testimonials at /video-testimonials/submit</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="require_parent_consent" value="1" {{ !empty($settings['require_parent_consent']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0e2f5e]">
                    <div>
                        <span class="font-semibold text-sm block" style="color:var(--text)">Require Parent/Guardian Consent Checkbox</span>
                        <span class="text-xs text-slate-500">Submissions cannot be published without signed consent confirmation</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="auto_approve" value="1" {{ !empty($settings['auto_approve']) ? 'checked' : '' }} class="w-4 h-4 rounded text-[#0e2f5e]">
                    <div>
                        <span class="font-semibold text-sm block" style="color:var(--text)">Auto-Approve Direct Admin Uploads</span>
                        <span class="text-xs text-slate-500">Automatically set admin added videos to approved state</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.video-testimonials.index') }}" class="btn-secondary py-2.5 px-5">Cancel</a>
            <button type="submit" class="btn-primary py-2.5 px-6 font-semibold">Save Settings</button>
        </div>
    </form>

    {{-- Separate Form for Instagram Manual Sync --}}
    <form id="syncIgForm" action="{{ route('admin.video-testimonials.settings.sync-instagram') }}" method="POST" class="hidden">
        @csrf
    </form>
</div>
@endsection
