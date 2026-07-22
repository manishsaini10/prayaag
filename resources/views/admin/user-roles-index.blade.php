@extends('admin.layout')

@section('title', 'User Roles')
@section('subtitle', 'Select a user to manage their roles')

@section('content')

<style>
    .user-card{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-radius:12px;border:1px solid var(--border);background:var(--surface);margin-bottom:8px;transition:border-color .15s}
    .user-card:hover{border-color:var(--primary)}
    .user-card-name{font-size:15px;font-weight:600;color:var(--text)}
    .user-card-meta{font-size:12.5px;color:var(--text-muted);margin-top:2px}
    .user-role-badge{display:inline-block;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;background:var(--primary-soft);color:var(--primary-strong);margin-right:4px}
</style>

<div class="card" style="padding:18px">
    @forelse ($users as $user)
        <div class="user-card">
            <div>
                <div class="user-card-name">{{ $user->name }}</div>
                <div class="user-card-meta">
                    {{ $user->email }}
                    @foreach ($user->roles as $role)
                        <span class="user-role-badge">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('admin.user-roles.edit', $user) }}" class="btn primary" style="padding:7px 16px;font-size:13px">
                Manage Roles
            </a>
        </div>
    @empty
        <div class="empty">No users found.</div>
    @endforelse
</div>

@if ($users->lastPage() > 1)
    <div class="flex items-center justify-between mt-4 text-[13px]" style="color:var(--text-muted)">
        <span>Page {{ $users->currentPage() }} of {{ $users->lastPage() }} · {{ $users->total() }} total</span>
        <div class="flex items-center gap-2">
            @if ($users->onFirstPage())
                <span class="btn-sm" style="opacity:.5">Previous</span>
            @else
                <a class="btn-sm" href="{{ $users->previousPageUrl() }}">Previous</a>
            @endif
            @if ($users->hasMorePages())
                <a class="btn-sm" href="{{ $users->nextPageUrl() }}">Next</a>
            @else
                <span class="btn-sm" style="opacity:.5">Next</span>
            @endif
        </div>
    </div>
@endif

@endsection
