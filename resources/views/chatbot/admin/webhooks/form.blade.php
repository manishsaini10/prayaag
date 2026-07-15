@extends('admin.layout')
@section('title', isset($webhook) ? 'Edit Webhook' : 'New Webhook')
@section('actions')
    <a href="{{ route('admin.chatbot.webhooks.index') }}" class="btn-secondary inline-flex items-center gap-1.5">&larr; Back</a>
@endsection
@section('content')
<div class="max-w-2xl">
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">
            <ul class="list-disc list-inside">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="card p-6">
        <form method="POST" action="{{ isset($webhook) ? route('admin.chatbot.webhooks.update', $webhook) : route('admin.chatbot.webhooks.store') }}" class="space-y-4">
            @csrf
            @if(isset($webhook)) @method('PUT') @endif

            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Name *</label>
                <input type="text" name="name" value="{{ old('name', $webhook->name ?? '') }}" required class="w-full" placeholder="e.g. Slack Notifications">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Webhook URL *</label>
                <input type="url" name="url" value="{{ old('url', $webhook->url ?? '') }}" required class="w-full" placeholder="https://hooks.slack.com/services/...">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Method</label>
                    <select name="method" class="w-full">
                        @foreach(['POST','GET','PUT','PATCH','DELETE'] as $m)
                            <option value="{{ $m }}" {{ old('method', $webhook->method ?? 'POST') === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Status</label>
                    <select name="status" class="w-full">
                        <option value="active" {{ old('status', $webhook->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $webhook->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Events *</label>
                <div class="grid grid-cols-2 gap-2 p-3 rounded-lg" style="background:var(--surface-2)">
                    @php $events = ['lead.created','lead.updated','conversation.created','conversation.closed','message.sent','ticket.created','ticket.updated','contact.created','deal.moved','campaign.sent']; @endphp
                    @foreach($events as $ev)
                        @php $selectedEvents = old('events', isset($webhook) ? (is_array($webhook->events) ? $webhook->events : (json_decode($webhook->events ?? '[]', true) ?: [])) : []); @endphp
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="events[]" value="{{ $ev }}" {{ in_array($ev, $selectedEvents) ? 'checked' : '' }}>
                            <span class="text-sm">{{ $ev }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Secret <span class="text-xs" style="color:var(--text-muted)">(for signature verification)</span></label>
                    <input type="text" name="secret" value="{{ old('secret', $webhook->secret ?? '') }}" class="w-full" placeholder="Optional shared secret">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Timeout (seconds)</label>
                    <input type="number" name="timeout_seconds" value="{{ old('timeout_seconds', $webhook->timeout_seconds ?? 15) }}" min="1" max="60" class="w-full">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Retry Count</label>
                    <input type="number" name="retry_count" value="{{ old('retry_count', $webhook->retry_count ?? 3) }}" min="0" max="10" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Custom Headers <span class="text-xs" style="color:var(--text-muted)">(JSON)</span></label>
                    <input type="text" name="headers" value="{{ old('headers', isset($webhook) && $webhook->headers ? json_encode($webhook->headers) : '') }}" class="w-full font-mono text-xs" placeholder='{"Authorization":"Bearer ..."}'>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary">{{ isset($webhook) ? 'Update Webhook' : 'Create Webhook' }}</button>
                <a href="{{ route('admin.chatbot.webhooks.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
