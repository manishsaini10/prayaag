@extends('admin.layout')

@section('title', $menu->name)
@section('subtitle', 'Menu' . ($menu->location ? ' · ' . $menu->location : ''))

@section('actions')
    <a href="{{ url('/admin/menus') }}" class="btn">← All menus</a>
@endsection

@section('content')

<style>
    .mi-grid{display:grid;grid-template-columns:1fr;gap:16px}
    @media(min-width:1024px){.mi-grid{grid-template-columns:1.5fr 1fr}}
    .fld{display:flex;flex-direction:column;gap:5px;margin-bottom:12px}
    .fld label{font-size:12px;font-weight:600;color:var(--text-soft)}
    .fld input,.fld select{padding:8px 11px;border:1px solid var(--border-strong);border-radius:9px;background:var(--surface);color:var(--text);font:inherit;font-size:13.5px;width:100%}
    .fld input:focus,.fld select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .mi-row{display:flex;align-items:center;gap:10px;padding:11px 14px;border-top:1px solid var(--border)}
    .mi-row:first-child{border-top:0}
    .mi-row .lbl{font-weight:600;color:var(--text);font-size:14px}
    .mi-edit{padding:14px;background:var(--surface-2);border-top:1px solid var(--border)}
    .mi-2col{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    @media(max-width:560px){.mi-2col{grid-template-columns:1fr}}
    .branch{color:var(--text-muted);font-size:12px;margin-right:4px}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<div class="mi-grid">

    {{-- ---------- Items ---------- --}}
    <div>
        <div class="card" style="overflow:hidden">
            <div class="widget__head"><x-admin.icon name="rectangle-stack" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">Menu items</span><span class="widget__sub">· drag-free ordering via sort number</span></div>
            <div>
                @forelse ($flat as $entry)
                    @php $item = $entry['item']; $depth = $entry['depth']; @endphp
                    <div x-data="{ editing: false }">
                        <div class="mi-row" style="padding-left:{{ 14 + $depth * 26 }}px">
                            @if ($depth > 0)<span class="branch">↳</span>@endif
                            <span class="lbl">{{ $item->label }}</span>
                            <span class="badge">{{ $item->type }}</span>
                            @if ($item->target === '_blank')<span class="badge new">new tab</span>@endif
                            <span class="muted" style="font-size:12px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                {{ $item->type === 'page' ? '/'.ltrim(optional($item->page)->slug ?? '?', '/') : ($item->url ?: '#') }}
                            </span>
                            <button class="btn-sm" @click="editing=!editing" type="button">Edit</button>
                            <form method="POST" action="{{ url('/admin/menus/'.$menu->id.'/items/'.$item->id) }}" style="display:inline" onsubmit="return confirm('Remove this item?')">
                                @csrf @method('DELETE')
                                <button class="btn-sm" type="submit" style="color:var(--danger)">Delete</button>
                            </form>
                        </div>

                        <div x-show="editing" x-cloak class="mi-edit">
                            <form method="POST" action="{{ url('/admin/menus/'.$menu->id.'/items/'.$item->id) }}" x-data="{ type: '{{ $item->type }}' }">
                                @csrf @method('PUT')
                                <div class="mi-2col">
                                    <div class="fld"><label>Label</label><input type="text" name="label" value="{{ $item->label }}" required></div>
                                    <div class="fld"><label>Type</label>
                                        <select name="type" x-model="type">
                                            <option value="page" @selected($item->type==='page')>Linked page</option>
                                            <option value="url" @selected($item->type==='url')>Custom URL</option>
                                            <option value="custom" @selected($item->type==='custom')>Custom (no link)</option>
                                        </select>
                                    </div>
                                    <div class="fld" x-show="type==='page'"><label>Page</label>
                                        <select name="page_id">
                                            <option value="">— Select page —</option>
                                            @foreach ($pages as $p)<option value="{{ $p->id }}" @selected($item->page_id===$p->id)>{{ $p->title }}</option>@endforeach
                                        </select>
                                    </div>
                                    <div class="fld" x-show="type!=='page'"><label>URL</label><input type="text" name="url" value="{{ $item->url }}" placeholder="https:// or /path"></div>
                                    <div class="fld"><label>Parent</label>
                                        <select name="parent_id">
                                            <option value="">— Top level —</option>
                                            @foreach ($items as $opt)@if ($opt->id !== $item->id)<option value="{{ $opt->id }}" @selected($item->parent_id===$opt->id)>{{ $opt->label }}</option>@endif @endforeach
                                        </select>
                                    </div>
                                    <div class="fld"><label>Open in</label>
                                        <select name="target">
                                            <option value="_self" @selected($item->target==='_self')>Same tab</option>
                                            <option value="_blank" @selected($item->target==='_blank')>New tab</option>
                                        </select>
                                    </div>
                                    <div class="fld"><label>Sort order</label><input type="number" name="sort_order" value="{{ $item->sort_order }}"></div>
                                </div>
                                <button class="btn primary" type="submit">Save item</button>
                                <button class="btn" type="button" @click="editing=false">Cancel</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="empty">No items yet — add the first one →</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ---------- Add item + settings ---------- --}}
    <div>
        <div class="widget" style="margin-bottom:16px">
            <div class="widget__head"><x-admin.icon name="plus" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">Add item</span></div>
            <div class="widget__body">
                <form method="POST" action="{{ url('/admin/menus/'.$menu->id.'/items') }}" x-data="{ type: 'page' }">
                    @csrf
                    <div class="fld"><label>Label</label><input type="text" name="label" placeholder="About Us" required></div>
                    <div class="fld"><label>Type</label>
                        <select name="type" x-model="type">
                            <option value="page">Linked page</option>
                            <option value="url">Custom URL</option>
                            <option value="custom">Custom (no link)</option>
                        </select>
                    </div>
                    <div class="fld" x-show="type==='page'"><label>Page</label>
                        <select name="page_id">
                            <option value="">— Select page —</option>
                            @foreach ($pages as $p)<option value="{{ $p->id }}">{{ $p->title }}</option>@endforeach
                        </select>
                    </div>
                    <div class="fld" x-show="type!=='page'" x-cloak><label>URL</label><input type="text" name="url" placeholder="https:// or /path"></div>
                    <div class="fld"><label>Parent</label>
                        <select name="parent_id">
                            <option value="">— Top level —</option>
                            @foreach ($items as $opt)<option value="{{ $opt->id }}">{{ $opt->label }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mi-2col">
                        <div class="fld"><label>Open in</label>
                            <select name="target"><option value="_self">Same tab</option><option value="_blank">New tab</option></select>
                        </div>
                        <div class="fld"><label>Sort order</label><input type="number" name="sort_order" value="0"></div>
                    </div>
                    <button class="btn primary" type="submit"><x-admin.icon name="plus"/> Add item</button>
                </form>
            </div>
        </div>

        <div class="widget">
            <div class="widget__head"><x-admin.icon name="cog" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">Menu settings</span></div>
            <div class="widget__body">
                <form method="POST" action="{{ url('/admin/menus/'.$menu->id) }}">
                    @csrf @method('PUT')
                    <div class="fld"><label>Name</label><input type="text" name="name" value="{{ $menu->name }}" required></div>
                    <div class="fld"><label>Slug</label><input type="text" name="slug" value="{{ $menu->slug }}" required>@error('slug')<span style="color:var(--danger);font-size:12px">{{ $message }}</span>@enderror</div>
                    <div class="fld"><label>Location</label><input type="text" name="location" value="{{ $menu->location }}" placeholder="primary / footer / mobile"></div>
                    <button class="btn primary" type="submit">Save settings</button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
