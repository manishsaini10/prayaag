@extends('admin.layout')

@section('title', 'Notifications')
@section('subtitle', $unread > 0 ? $unread . ' unread' : 'All caught up')

@section('actions')
    @if ($unread > 0)
        <form method="POST" action="{{ url('/admin/notifications/read-all') }}">
            @csrf
            <button class="btn" type="submit"><x-admin.icon name="check"/> Mark all read</button>
        </form>
    @endif
@endsection

@section('content')

@php
    $levelColor = ['success' => 'var(--success)', 'warning' => 'var(--warning)', 'danger' => 'var(--danger)', 'info' => 'var(--primary-strong)'];
@endphp

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<div class="card">
    <div class="widget__body" style="padding-top:4px;padding-bottom:4px">
        @forelse ($notifications as $n)
            <div class="activity-item" style="{{ $n->isUnread() ? 'background:var(--primary-soft);margin:0 -18px;padding-left:18px;padding-right:18px;' : '' }}">
                <div class="activity-dot" style="color:{{ $levelColor[$n->level] ?? 'var(--text-soft)' }}">
                    <x-admin.icon name="{{ $n->icon ?: 'bell' }}"/>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[13.5px]" style="color:var(--text);font-weight:{{ $n->isUnread() ? '600' : '500' }}">{{ $n->title }}</div>
                    @if ($n->body)<div class="text-[13px] mt-0.5" style="color:var(--text-soft)">{{ $n->body }}</div>@endif
                    <div class="text-[12px] mt-0.5" style="color:var(--text-muted)">
                        <span class="badge">{{ $n->type }}</span>
                        <span class="ml-1">{{ $n->created_at?->diffForHumans() }}</span>
                    </div>
                </div>
                <div style="white-space:nowrap;display:flex;gap:6px;align-items:center">
                    @if ($n->url || $n->isUnread())
                        <form method="POST" action="{{ url('/admin/notifications/'.$n->id.'/read') }}">
                            @csrf
                            <button class="btn-sm" type="submit">{{ $n->url ? 'Open' : 'Mark read' }}</button>
                        </form>
                    @endif
                    @if ($n->isUnread())<span class="status-dot ok" style="background:{{ $levelColor[$n->level] ?? 'var(--primary)' }}"></span>@endif
                </div>
            </div>
        @empty
            <div class="empty">No notifications yet. New enquiries, applications, and subscribers will appear here.</div>
        @endforelse
    </div>
</div>

@if ($notifications->hasPages())
    <div class="flex items-center justify-between mt-4 text-[13px]" style="color:var(--text-muted)">
        <span>Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}</span>
        <div class="flex items-center gap-2">
            @if ($notifications->onFirstPage())<span class="btn-sm" style="opacity:.5">Previous</span>@else<a class="btn-sm" href="{{ $notifications->previousPageUrl() }}">Previous</a>@endif
            @if ($notifications->hasMorePages())<a class="btn-sm" href="{{ $notifications->nextPageUrl() }}">Next</a>@else<span class="btn-sm" style="opacity:.5">Next</span>@endif
        </div>
    </div>
@endif

@endsection
