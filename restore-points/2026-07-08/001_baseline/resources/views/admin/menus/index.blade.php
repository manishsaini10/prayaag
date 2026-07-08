@extends('admin.layout')

@section('title', 'Menus')
@section('subtitle', $menus->count() . ' ' . \Illuminate\Support\Str::plural('menu', $menus->count()))

@section('content')

<style>
    .mfrm{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end}
    .mfrm .f{display:flex;flex-direction:column;gap:5px}
    .mfrm label{font-size:12px;font-weight:600;color:var(--text-soft)}
    .mfrm input{padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px;min-width:200px}
    .mfrm input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<div class="widget" style="margin-bottom:16px">
    <div class="widget__head"><x-admin.icon name="rectangle-stack" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">Create a menu</span></div>
    <div class="widget__body">
        <form method="POST" action="{{ url('/admin/menus') }}" class="mfrm">
            @csrf
            <div class="f">
                <label for="name">Menu name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Primary navigation" required>
                @error('name')<span style="color:var(--danger);font-size:12px">{{ $message }}</span>@enderror
            </div>
            <div class="f">
                <label for="location">Location <span class="muted" style="font-weight:400">(optional)</span></label>
                <input id="location" type="text" name="location" value="{{ old('location') }}" placeholder="primary / footer / mobile">
            </div>
            <button class="btn primary" type="submit"><x-admin.icon name="plus"/> Create menu</button>
        </form>
    </div>
</div>

<div class="card" style="overflow:hidden">
    <table>
        <thead><tr><th>Menu</th><th>Location</th><th>Items</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
            @forelse ($menus as $menu)
                <tr>
                    <td><strong>{{ $menu->name }}</strong> <span class="muted">/{{ $menu->slug }}</span></td>
                    <td>@if ($menu->location)<span class="badge">{{ $menu->location }}</span>@else<span class="muted">—</span>@endif</td>
                    <td>{{ $menu->items_count }}</td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm primary" href="{{ url('/admin/menus/'.$menu->id) }}">Manage items</a>
                        <form method="POST" action="{{ url('/admin/menus/'.$menu->id) }}" style="display:inline" onsubmit="return confirm('Delete this menu and all its items?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm" type="submit" style="color:var(--danger)">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty">No menus yet — create your first one above.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
