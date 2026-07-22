@extends('admin.layout')

@section('title', 'Roles for ' . $user->name)
@section('subtitle', $user->email)

@section('actions')
    <a href="{{ url('/admin/m/users') }}" class="btn"><x-admin.icon name="chevron-right" style="transform:rotate(180deg)"/> Back to Users</a>
@endsection

@section('content')

<style>
    .role-list{display:flex;flex-direction:column;gap:6px}
    .role-item{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;transition:background .12s;cursor:pointer;font-size:14px;color:var(--text)}
    .role-item:hover{background:var(--surface-hover)}
    .role-item input[type=checkbox]{width:18px;height:18px;accent-color:var(--primary);cursor:pointer;flex-shrink:0}
    .role-name{font-weight:600}
    .role-meta{font-size:12px;color:var(--text-muted);margin-left:auto}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

<div class="card" style="padding:22px;max-width:560px">
    <form method="POST" action="{{ route('admin.user-roles.update', $user) }}">
        @csrf
        @method('PUT')

        <p style="font-size:13.5px;color:var(--text-muted);margin-bottom:16px">
            Select the roles to assign to this user. A user inherits all permissions from their assigned roles.
        </p>

        <div class="role-list">
            @foreach ($roles as $role)
                <label class="role-item">
                    <input type="checkbox" name="roles[{{ $role->id }}]" value="1"
                           @checked(in_array($role->id, $userRoleIds))>
                    <span class="role-name">{{ $role->name }}</span>
                    <span class="role-meta">{{ $role->permissions->count() }} permissions</span>
                </label>
            @endforeach
        </div>

        <div class="frm-actions" style="padding-top:16px;margin-top:20px">
            <button type="submit" class="btn primary">Save Roles</button>
            <a href="{{ url('/admin/m/users') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>

@endsection
