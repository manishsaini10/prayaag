@extends('admin.layout')

@section('title', 'Widget Builder')
@section('subtitle', $widgets->count().' custom '.\Illuminate\Support\Str::plural('widget', $widgets->count()))

@section('actions')
    <a href="{{ route('admin.widgets.create') }}" class="btn-sm primary">+ New Widget</a>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<div class="card" style="padding:14px 16px;margin-bottom:16px;font-size:13px;color:var(--text-muted)">
    Build your own page-builder widgets here — give it a name, define editable fields, and write an HTML template using <code>@{{ field_key }}</code> placeholders. Saved widgets appear in the Page Builder palette automatically — no code, no restart.
</div>

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr><th>Name</th><th>Slug</th><th>Category</th><th>Fields</th><th>Status</th><th style="text-align:right">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($widgets as $w)
                <tr>
                    <td><strong>{{ $w->name }}</strong></td>
                    <td><span class="muted">{{ $w->slug }}</span></td>
                    <td>{{ $w->category }}</td>
                    <td>{{ count($w->fields ?? []) }}</td>
                    <td><span class="badge {{ $w->is_active ? 'published' : 'draft' }}">{{ $w->is_active ? 'active' : 'inactive' }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm primary" href="{{ route('admin.widgets.edit', $w->id) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.widgets.destroy', $w->id) }}" style="display:inline" onsubmit="return confirm('Delete this widget? Pages using it will lose this section.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm" style="color:var(--danger)">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">No custom widgets yet. Click “+ New Widget” to create one.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
