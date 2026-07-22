@extends('admin.layout')

@section('title', 'Permissions for ' . $role->name)
@section('subtitle', 'Assign or remove permissions for this role')

@section('actions')
    <a href="{{ url('/admin/m/roles') }}" class="btn"><x-admin.icon name="chevron-right" style="transform:rotate(180deg)"/> Back to Roles</a>
@endsection

@section('content')

<style>
    .perm-group{margin-bottom:24px}
    .perm-group-title{font-size:14px;font-weight:700;color:var(--text);padding:8px 12px;border-radius:8px;background:var(--surface-2);margin-bottom:8px;text-transform:capitalize}
    .perm-group-title .group-path{font-weight:400;opacity:.6;font-size:12px}
    .perm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:4px}
    .perm-item{display:flex;align-items:center;gap:10px;padding:6px 10px;border-radius:8px;transition:background .12s;cursor:pointer;font-size:13.5px;color:var(--text)}
    .perm-item:hover{background:var(--surface-hover)}
    .perm-item input[type=checkbox]{width:17px;height:17px;accent-color:var(--primary);cursor:pointer;flex-shrink:0}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

<div class="card" style="padding:22px">
    <form method="POST" action="{{ route('admin.role-permissions.update', $role) }}">
        @csrf
        @method('PUT')

        @foreach ($groups as $group => $perms)
            <div class="perm-group">
                <div class="perm-group-title">
                    <label style="cursor:pointer;display:flex;align-items:center;gap:8px">
                        <input type="checkbox" class="group-toggle" data-group="{{ $group }}"
                               @if ($perms->every(fn ($p) => in_array($p->id, $rolePermIds))) checked @endif>
                        @php $parts = explode('/', $group); @endphp
                        @if (count($parts) > 1)
                            <span>{{ $parts[1] }} <span class="group-path">{{ $parts[0] }}</span></span>
                        @else
                            {{ $group }}
                        @endif
                    </label>
                </div>
                <div class="perm-grid">
                    @foreach ($perms as $perm)
                        <label class="perm-item">
                            <input type="checkbox" name="permissions[{{ $perm->id }}]" value="1"
                                   @checked(in_array($perm->id, $rolePermIds))>
                            {{ $perm->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="frm-actions" style="padding-top:16px;margin-top:8px">
            <button type="submit" class="btn primary">Save Permissions</button>
            <a href="{{ url('/admin/m/roles') }}" class="btn">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.querySelectorAll('.group-toggle').forEach(function (cb) {
        cb.addEventListener('change', function () {
            var checked = this.checked;
            this.closest('.perm-group').querySelectorAll('.perm-item input[type=checkbox]').forEach(function (item) {
                item.checked = checked;
            });
        });
    });
</script>

@endsection
