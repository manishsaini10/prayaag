@extends('admin.layout')
@section('title', isset($campaign) ? 'Edit Campaign' : 'New Campaign')
@section('actions')
    <a href="{{ route('admin.chatbot.campaigns.index') }}" class="btn-secondary inline-flex items-center gap-1.5">&larr; Back</a>
@endsection
@section('content')
<div class="max-w-2xl">
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">
            <ul class="list-disc list-inside">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="card p-6">
        <form method="POST" action="{{ isset($campaign) ? route('admin.chatbot.campaigns.update', $campaign) : route('admin.chatbot.campaigns.store') }}" class="space-y-4">
            @csrf
            @if(isset($campaign)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Campaign Name *</label>
                <input type="text" name="name" value="{{ old('name', $campaign->name ?? '') }}" required class="w-full">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Type *</label>
                    <select name="type" required class="w-full">
                        @foreach(['email','sms','push','in_app','whatsapp'] as $t)
                            <option value="{{ $t }}" {{ old('type', $campaign->type ?? '') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Channel</label>
                    <select name="channel" class="w-full">
                        <option value="">Select channel</option>
                        @foreach(['marketing','transactional','automation'] as $ch)
                            <option value="{{ $ch }}" {{ old('channel', $campaign->channel ?? '') === $ch ? 'selected' : '' }}>{{ ucfirst($ch) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Content</label>
                <textarea name="content" rows="6" class="w-full" placeholder="Campaign content / message body...">{{ old('content', $campaign->content ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Status</label>
                    <select name="status" class="w-full">
                        @foreach(['draft','scheduled','active'] as $s)
                            <option value="{{ $s }}" {{ old('status', $campaign->status ?? 'draft') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Scheduled At</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', isset($campaign) && $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}" class="w-full">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Targeting Rules <span class="text-xs" style="color:var(--text-muted)">(JSON)</span></label>
                <textarea name="targeting_rules" rows="3" class="w-full font-mono text-xs" placeholder='{"segments":["all"],"exclude":[]}'>{{ old('targeting_rules', isset($campaign) && $campaign->targeting_rules ? json_encode($campaign->targeting_rules) : '') }}</textarea>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ isset($campaign) ? 'Update Campaign' : 'Create Campaign' }}</button>
                <a href="{{ route('admin.chatbot.campaigns.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
