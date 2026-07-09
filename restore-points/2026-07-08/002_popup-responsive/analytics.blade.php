@extends('admin.layout')

@section('title', 'Analytics · ' . $popup->title)

@section('actions')
    <a href="{{ url('/admin/popup-builder/' . $popup->id . '/edit') }}" class="btn-sm" style="border:1px solid var(--border)">← Back to Editor</a>
    <a href="{{ url('/admin/popup-builder') }}" class="btn-sm" style="border:1px solid var(--border)">All Popups</a>
@endsection

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card"><div class="text-2xl font-bold">{{ number_format($popup->view_count) }}</div><div class="text-sm" style="color:var(--text-muted)">Views</div></div>
        <div class="card"><div class="text-2xl font-bold">{{ number_format($popup->impression_count) }}</div><div class="text-sm" style="color:var(--text-muted)">Impressions</div></div>
        <div class="card"><div class="text-2xl font-bold">{{ number_format($popup->click_count) }}</div><div class="text-sm" style="color:var(--text-muted)">Clicks</div></div>
        <div class="card"><div class="text-2xl font-bold">{{ number_format($popup->conversion_count) }}</div><div class="text-sm" style="color:var(--text-muted)">Conversions</div></div>
    </div>

    @if($popup->analytics->count())
        <div class="card p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead><tr style="border-bottom:1px solid var(--border)">
                    <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Event</th>
                    <th class="text-left px-4 py-3 font-semibold hidden sm:table-cell" style="color:var(--text-muted)">URL</th>
                    <th class="text-left px-4 py-3 font-semibold hidden md:table-cell" style="color:var(--text-muted)">Device</th>
                    <th class="text-right px-4 py-3 font-semibold" style="color:var(--text-muted)">Time</th>
                </tr></thead>
                <tbody>
                    @foreach($popup->analytics->take(100) as $entry)
                        <tr style="border-bottom:1px solid var(--border)">
                            <td class="px-4 py-3"><span class="badge">{{ $entry->event_type }}</span></td>
                            <td class="px-4 py-3 hidden sm:table-cell text-xs" style="color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $entry->url }}</td>
                            <td class="px-4 py-3 hidden md:table-cell text-xs" style="color:var(--text-muted)">{{ $entry->device_type ?? '—' }}</td>
                            <td class="px-4 py-3 text-right text-xs" style="color:var(--text-muted)">{{ $entry->occurred_at?->diffForHumans() ?? $entry->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card empty">No analytics data yet.</div>
    @endif
</div>

<style>
.badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;background:var(--surface-2);color:var(--text-muted);text-transform:capitalize}
</style>
@endsection
