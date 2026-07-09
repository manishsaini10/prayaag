@php use App\Models\Popup\Popup; @endphp
@extends('admin.layout')

@section('title', 'Popup Manager')

@section('actions')
    <a href="{{ url('/admin/popup-builder/create') }}" class="btn-primary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
        <span>Create Popup</span>
    </a>
@endsection

@section('content')
<div class="space-y-6">

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="stat-card"><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Total Popups</div></div>
        <div class="stat-card"><div class="stat-value" style="color:var(--primary)">{{ $stats['active'] }}</div><div class="stat-label">Active</div></div>
        <div class="stat-card"><div class="stat-value" style="color:#f59e0b">{{ $stats['draft'] }}</div><div class="stat-label">Drafts</div></div>
        <div class="stat-card"><div class="stat-value">{{ number_format($stats['views']) }}</div><div class="stat-label">Total Views</div></div>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Title</th>
                        <th class="text-left px-4 py-3 font-semibold hidden md:table-cell" style="color:var(--text-muted)">Type</th>
                        <th class="text-left px-4 py-3 font-semibold hidden lg:table-cell" style="color:var(--text-muted)">Category</th>
                        <th class="text-left px-4 py-3 font-semibold hidden sm:table-cell" style="color:var(--text-muted)">Status</th>
                        <th class="text-right px-4 py-3 font-semibold" style="color:var(--text-muted)">Views</th>
                        <th class="text-right px-4 py-3 font-semibold hidden md:table-cell" style="color:var(--text-muted)">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($popups as $popup)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ url('/admin/popup-builder/' . $popup->id . '/edit') }}" class="font-medium" style="color:var(--text)">{{ $popup->title }}</a>
                                <div class="text-xs mt-0.5" style="color:var(--text-muted)">{{ $popup->created_at->format('M j, Y') }}</div>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell"><span class="badge">{{ str_replace('_', ' ', $popup->type) }}</span></td>
                            <td class="px-4 py-3 hidden lg:table-cell" style="color:var(--text-muted)">{{ $popup->category?->name ?? '—' }}</td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                @if($popup->status === 'active')
                                    <span style="color:#16a34a" class="font-medium">Active</span>
                                @elseif($popup->status === 'draft')
                                    <span style="color:#f59e0b" class="font-medium">Draft</span>
                                @else
                                    <span style="color:#6b7280" class="font-medium">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right" style="color:var(--text-muted)">{{ number_format($popup->view_count) }}</td>
                            <td class="px-4 py-3 text-right hidden md:table-cell">
                                <div class="inline-flex items-center gap-1">
                                    @can('update', $popup)
                                    <a href="{{ url('/admin/popup-builder/' . $popup->id . '/edit') }}" class="btn-sm" style="border:1px solid var(--border)">Edit</a>
                                    @endcan
                                    @can('publish', $popup)
                                    @if($popup->status === 'draft')
                                        <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id . '/publish') }}" class="inline">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="border:1px solid var(--border);color:var(--primary)">Publish</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id . '/unpublish') }}" class="inline">
                                            @csrf
                                            <button type="submit" class="btn-sm" style="border:1px solid var(--border);color:#f59e0b">Unpublish</button>
                                        </form>
                                    @endif
                                    @endcan
                                    @can('duplicate', $popup)
                                    <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id . '/duplicate') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-sm" style="border:1px solid var(--border)" title="Duplicate">Dup</button>
                                    </form>
                                    @endcan
                                    @can('delete', $popup)
                                    <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id) }}" class="inline" onsubmit="return confirm('Delete this popup?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-sm" style="border:1px solid var(--border);color:var(--danger)">Del</button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty py-12">No popups yet. <a href="{{ url('/admin/popup-builder/create') }}" style="color:var(--primary-strong)">Create your first popup</a>.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($popups->hasPages())
            <div class="px-4 py-3" style="border-top:1px solid var(--border)">{{ $popups->links() }}</div>
        @endif
    </div>
</div>

<style>
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px 20px}
.stat-value{font-size:28px;font-weight:800;line-height:1.2;color:var(--text)}
.stat-label{font-size:13px;color:var(--text-muted);margin-top:4px}
.badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:12px;background:var(--surface-2);color:var(--text-muted);text-transform:capitalize}
</style>
@endsection
