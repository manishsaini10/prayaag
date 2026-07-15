@extends('admin.layout')

@section('title', 'Parent Testimonial Settings')

@section('actions')
    <a href="{{ route('admin.testimonials-console.index') }}" class="btn-secondary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span>Back to Moderation</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.testimonials-console.settings.update') }}" method="POST" class="card space-y-6">
        @csrf
        
        <div>
            <h3 class="text-lg font-bold text-gray-800">Moderation & Submission Flow</h3>
            <p class="text-sm text-gray-500 mt-1">Configure moderation criteria and form constraints.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="auto_approve" value="1" {{ $config['auto_approve'] ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block text-gray-800">Auto Approve Testimonials</span>
                    <span class="text-xs text-gray-500">Bypass moderation queue and publish immediately</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="require_image" value="1" {{ $config['require_image'] ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block text-gray-800">Require Parent Photo</span>
                    <span class="text-xs text-gray-500">Require parents to upload a photo to submit</span>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="enable_rating" value="1" {{ $config['enable_rating'] ? 'checked' : '' }} class="mt-1">
                <div>
                    <span class="font-semibold block text-gray-800">Enable Star Ratings</span>
                    <span class="text-xs text-gray-500">Allow parents to select a 1-5 star rating</span>
                </div>
            </label>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- Character limits --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800">Validation Limits</h3>
            <p class="text-sm text-gray-500 mt-1">Set text length restrictions for the testimonial quote.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Minimum Character Count</label>
                <input type="number" name="min_chars" value="{{ $config['min_chars'] }}" min="10" max="500" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                <p class="text-xs text-gray-400 mt-1">Default is 50 characters.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Maximum Character Count</label>
                <input type="number" name="max_chars" value="{{ $config['max_chars'] }}" min="100" max="10000" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                <p class="text-xs text-gray-400 mt-1">Default is 500 characters.</p>
            </div>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- Display options --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800">Display & Layout Configurations</h3>
            <p class="text-sm text-gray-500 mt-1">Configure layout, limit, and autoplay interval for public pages.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Default Display Style</label>
                <select name="display_style" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                    <option value="slider" {{ $config['display_style'] === 'slider' ? 'selected' : '' }}>Slider (Carousel)</option>
                    <option value="grid" {{ $config['display_style'] === 'grid' ? 'selected' : '' }}>Grid Layout</option>
                    <option value="list" {{ $config['display_style'] === 'list' ? 'selected' : '' }}>Vertical List</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Used on the homepage components.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Display Limit (Count)</label>
                <input type="number" name="display_limit" value="{{ $config['display_limit'] }}" min="1" max="100" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                <p class="text-xs text-gray-400 mt-1">Maximum number of testimonials to load.</p>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1 text-gray-700">Slider Autoplay (Seconds)</label>
                <input type="number" name="slider_autoplay_interval" value="{{ $config['slider_autoplay_interval'] }}" min="1" max="60" class="w-full text-sm p-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary" required>
                <p class="text-xs text-gray-400 mt-1">Time between auto-sliding in seconds.</p>
            </div>
        </div>

        <hr style="border-top:1px solid var(--border)">

        {{-- Spam words --}}
        <div>
            <h3 class="text-lg font-bold text-gray-800">Spam Prevention & Content Moderation</h3>
            <p class="text-sm text-gray-500 mt-1">Block submissions containing unwanted, abusive, or competitor keywords.</p>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1 text-gray-700">Profanity / Blocked Words List</label>
            <textarea name="blocked_words" rows="4" class="w-full text-sm p-3 border rounded focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Enter keywords separated by commas...">{{ $config['blocked_words'] }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Submissions containing these words (case-insensitive) will be blocked instantly with a warning.</p>
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <button type="submit" class="btn-primary py-2 px-6 text-xs font-bold">Save Settings</button>
        </div>
    </form>
</div>
@endsection
