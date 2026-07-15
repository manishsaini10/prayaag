@extends('admin.layout')
@section('title', 'Webhook: ' . $webhook->name)
@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.chatbot.webhooks.edit', $webhook) }}" class="btn-primary inline-flex items-center gap-1.5">Edit Webhook</a>
        <a href="{{ route('admin.chatbot.webhooks.index') }}" class="btn-secondary inline-flex items-center gap-1.5">&larr; Back</a>
    </div>
@endsection
@section('content')
<div class="space-y-6">
    <div class="card p-6">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Name</span>
                <p class="mt-1 font-medium" style="color:var(--text)">{{ $webhook->name }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Status</span>
                <p class="mt-1">
                    @if($webhook->status === 'active')
                        <span class="badge" style="background:#dcfce7;color:#166534">Active</span>
                    @else
                        <span class="badge" style="background:#fee2e2;color:#991b1b">{{ $webhook->status }}</span>
                    @endif
                </p>
            </div>
            <div class="col-span-2">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Webhook URL</span>
                <p class="mt-1 font-mono text-sm break-all" style="color:var(--text)">{{ $webhook->url }}</p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Method</span>
                <p class="mt-1"><span class="badge">{{ $webhook->method ?? 'POST' }}</span></p>
            </div>
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Timeout</span>
                <p class="mt-1" style="color:var(--text)">{{ $webhook->timeout_seconds ?? 15 }}s</p>
            </div>
            <div class="col-span-2">
                <span class="text-xs font-semibold uppercase tracking-wider" style="color:var(--text-muted)">Events</span>
                <div class="mt-1 flex flex-wrap gap-1">
                    @foreach(json_decode($webhook->events ?? '[]', true) ?: [] as $ev)
                        <span class="badge">{{ $ev }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="px-4 py-3 font-semibold text-sm border-b" style="border-color:var(--border);color:var(--text)">Delivery Log</div>
        @if($webhook->logs->isEmpty())
            <div class="text-center py-8 text-sm" style="color:var(--text-muted)">No delivery logs yet.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Event</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Status</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Response</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Duration</th>
                        <th class="text-left px-4 py-2 font-semibold" style="color:var(--text-muted)">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($webhook->logs as $log)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50">
                            <td class="px-4 py-2" style="color:var(--text)">{{ $log->event }}</td>
                            <td class="px-4 py-2">
                                @if($log->status === 'success')
                                    <span class="badge" style="background:#dcfce7;color:#166534">Success</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#991b1b">Failed</span>
                                @endif
                            </td>
                            <td class="px-4 py-2" style="color:var(--text-muted)">{{ $log->response_status ?? '—' }}</td>
                            <td class="px-4 py-2" style="color:var(--text-muted)">{{ $log->duration_ms ? $log->duration_ms . 'ms' : '—' }}</td>
                            <td class="px-4 py-2" style="color:var(--text-muted)">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
