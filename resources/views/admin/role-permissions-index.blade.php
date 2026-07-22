@extends('admin.layout')

@section('title', 'Role Permissions')
@section('subtitle', 'Select a role to manage its permissions')

@section('content')

<style>
    .role-card{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-radius:12px;border:1px solid var(--border);background:var(--surface);margin-bottom:8px;transition:border-color .15s}
    .role-card:hover{border-color:var(--primary)}
    .role-card-name{font-size:15px;font-weight:600;color:var(--text)}
    .role-card-meta{font-size:12.5px;color:var(--text-muted);margin-top:2px}
</style>

<div class="card" style="padding:18px">
    @forelse ($roles as $role)
        <div class="role-card">
            <div>
                <div class="role-card-name">{{ $role->name }}</div>
                <div class="role-card-meta">{{ $role->permissions_count }} permissions · {{ $role->guard_name }}</div>
            </div>
            <a href="{{ route('admin.role-permissions.edit', $role) }}" class="btn primary" style="padding:7px 16px;font-size:13px">
                Manage Permissions
            </a>
        </div>
    @empty
        <div class="empty">No roles found.</div>
    @endforelse
</div>

@endsection
