@extends('admin.layout')

@section('title', 'Leads · ' . $popup->title)

@section('actions')
    <a href="{{ url('/admin/popup-builder/' . $popup->id . '/edit') }}" class="btn-sm" style="border:1px solid var(--border)">← Back to Editor</a>
    <a href="{{ url('/admin/popup-builder') }}" class="btn-sm" style="border:1px solid var(--border)">All Popups</a>
@endsection

@section('content')
<div class="card p-0 overflow-hidden">
    @if($popup->leads->count())
        <table class="w-full text-sm">
            <thead><tr style="border-bottom:1px solid var(--border)">
                <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Name</th>
                <th class="text-left px-4 py-3 font-semibold hidden sm:table-cell" style="color:var(--text-muted)">Email</th>
                <th class="text-left px-4 py-3 font-semibold hidden md:table-cell" style="color:var(--text-muted)">Phone</th>
                <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Status</th>
                <th class="text-right px-4 py-3 font-semibold" style="color:var(--text-muted)">Date</th>
            </tr></thead>
            <tbody>
                @foreach($popup->leads as $lead)
                    <tr style="border-bottom:1px solid var(--border)">
                        <td class="px-4 py-3 font-medium" style="color:var(--text)">{{ $lead->name ?? '—' }}</td>
                        <td class="px-4 py-3 hidden sm:table-cell" style="color:var(--text-muted)">{{ $lead->email ?? '—' }}</td>
                        <td class="px-4 py-3 hidden md:table-cell" style="color:var(--text-muted)">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="badge">{{ $lead->status ?? 'new' }}</span></td>
                        <td class="px-4 py-3 text-right text-xs" style="color:var(--text-muted)">{{ $lead->created_at->format('M j, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty py-12">No leads captured yet.</div>
    @endif
</div>

<style>
.badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;background:var(--surface-2);color:var(--text-muted);text-transform:capitalize}
</style>
@endsection
