@extends('admin.layout')
@section('title', 'Webhooks')
@section('actions')
    <a href="{{ route('admin.chatbot.webhooks.create') }}" class="btn-primary px-4 py-2 rounded-lg text-sm font-bold inline-flex items-center gap-1.5">+ New Webhook</a>
@endsection
@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#991b1b">{{ session('error') }}</div>
    @endif

    @if($webhooks->isEmpty())
        <div class="card p-12 text-center">
            <p class="text-sm" style="color:var(--text-muted)">No webhooks configured. Create your first webhook endpoint.</p>
        </div>
    @else
        <div class="card p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Name</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">URL</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Events</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Method</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Status</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($webhooks as $webhook)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                            <td class="px-4 py-3 font-medium" style="color:var(--text)">{{ $webhook->name }}</td>
                            <td class="px-4 py-3 max-w-[200px] truncate" style="color:var(--text-muted)"><code>{{ $webhook->url }}</code></td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(json_decode($webhook->events ?? '[]', true) ?: [] as $ev)
                                        <span class="badge text-xs">{{ $ev }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-3"><span class="badge">{{ $webhook->method ?? 'POST' }}</span></td>
                            <td class="px-4 py-3">
                                @if($webhook->status === 'active')
                                    <span class="badge" style="background:#dcfce7;color:#166534">Active</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#991b1b">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.chatbot.webhooks.show', $webhook) }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">View</a>
                                    <a href="{{ route('admin.chatbot.webhooks.edit', $webhook) }}" class="text-sm font-medium hover:underline" style="color:var(--primary)">Edit</a>
                                    <form method="POST" action="{{ route('admin.chatbot.webhooks.test', $webhook) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="text-sm font-medium hover:underline" style="color:#8b5cf6">Test</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.chatbot.webhooks.destroy', $webhook) }}" onsubmit="return confirm('Delete this webhook?')" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium hover:underline" style="color:#dc2626">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($webhooks->hasPages())
            <div class="p-4">{{ $webhooks->links() }}</div>
        @endif
    @endif
</div>
@endsection
