@extends('admin.layout')

@section('title', 'Compose Newsletter Campaign')
@section('subtitle', 'Targeting ' . $subscriberCount . ' active subscribed parents')

@section('content')
<div class="max-w-4xl mx-auto bg-slate-900/90 rounded-2xl p-8 border border-slate-700/80 shadow-md" x-data="{ body: '' }">
    <form action="{{ route('admin.newsletter.campaigns.store') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-white mb-2">Subject Line</label>
            <input type="text" name="subject" required placeholder="e.g. Monthly Principal Update & Upcoming Sports Meet" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-3 text-sm focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-semibold text-white mb-2">Campaign Content (HTML)</label>
            <textarea name="body_html" x-model="body" rows="12" required placeholder="<h1>Dear Parents,</h1><p>We are excited to share...</p>" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white p-4 text-xs font-mono leading-relaxed focus:ring-indigo-500"></textarea>
        </div>

        <div class="p-4 rounded-xl bg-indigo-950/80 text-indigo-200 border border-indigo-800 text-xs">
            <strong>Mandatory Legal Compliance:</strong> A unique one-click unsubscribe link will be automatically appended to the footer of every campaign email sent.
        </div>

        <!-- Live Preview -->
        <div>
            <h4 class="text-sm font-bold text-white mb-2">Live Preview</h4>
            <div class="rounded-xl border border-slate-700 p-4 bg-white min-h-[200px]">
                <iframe :srcdoc="body" class="w-full min-h-[200px] border-0"></iframe>
            </div>
        </div>

        <div class="flex items-center justify-between pt-6 border-t border-slate-800">
            <a href="{{ route('admin.newsletter.campaigns.index') }}" class="btn">Cancel</a>

            <div class="flex items-center gap-3">
                <button type="submit" name="save_draft" class="btn">Save Draft</button>
                <button type="submit" name="send_now" value="1" onclick="return confirm('Send this campaign now to all {{ $subscriberCount }} subscribers?')" class="btn primary">
                    Send Campaign Now
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
