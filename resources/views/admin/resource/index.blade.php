@extends('admin.layout')

@php
    $actions = $def['actions'] ?? ['index', 'create', 'store', 'edit', 'update', 'destroy'];
    $canCreate = in_array('create', $actions, true);
    $canEdit = in_array('edit', $actions, true);
    $canDelete = in_array('destroy', $actions, true);
@endphp

@section('title', $def['label'])
@section('subtitle', $items->total() . ' ' . \Illuminate\Support\Str::plural('record', $items->total()))

@section('actions')
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $catField = collect($def['fields'] ?? [])->firstWhere('key', 'category');
        @endphp
        <form method="GET" action="{{ url('/admin/m/'.$resource) }}" class="flex items-center gap-2 flex-wrap">
            @if ($catField && !empty($catField['options']))
                <select name="category" class="inline-select" onchange="this.form.submit()" style="padding:8px 12px">
                    <option value="">All Categories</option>
                    @foreach ($catField['options'] as $ckey => $clabel)
                        <option value="{{ $ckey }}" @selected(($category ?? '') === (string)$ckey)>{{ $clabel }}</option>
                    @endforeach
                </select>
            @endif

            @if (! empty($def['search']))
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Search…" class="inline-select" style="min-width:180px;padding:8px 12px">
                <button class="btn-sm" type="submit">Filter</button>
            @endif

            @if (!empty($q) || !empty($category))
                <a class="btn-sm" href="{{ url('/admin/m/'.$resource) }}">Clear</a>
            @endif
        </form>
        @if ($resource === 'events')
            <button type="button" class="btn" onclick="openCategoryModal()" style="display:inline-flex;align-items:center;gap:6px">
                <x-admin.icon name="tag"/> Manage Categories
            </button>
        @endif
        @if ($canCreate)
            <a href="{{ url('/admin/m/'.$resource.'/create') }}" class="btn primary"><x-admin.icon name="plus"/> New {{ strtolower($def['singular'] ?? 'item') }}</a>
        @endif
    </div>
@endsection

@section('content')

<style>
    .media-thumb-wrap{width:44px;height:44px;border-radius:8px;overflow:hidden;background:var(--surface-hover);display:grid;place-items:center}
    .media-thumb{width:100%;height:100%;object-fit:cover}
    .media-ext{font-weight:700;font-size:11px;color:var(--text-muted);text-transform:uppercase}
    .copy-url-btn{display:inline-flex;align-items:center;gap:4px;padding:4px 8px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);font-size:11.5px;cursor:pointer;transition:.15s;line-height:1}
    .copy-url-btn:hover{border-color:var(--primary);color:var(--primary)}
    .copy-url-btn.copied{background:var(--success-soft);border-color:var(--success);color:var(--success)}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr>
                @foreach ($def['columns'] as $col)
                    <th>{{ $col['label'] }}</th>
                @endforeach
                @if ($canEdit || $canDelete)<th style="text-align:right">Actions</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    @foreach ($def['columns'] as $col)
                        @php
                            $type = $col['type'] ?? 'text';
                            $key = $col['key'];
                            $val = $type === 'relation' ? null : data_get($item, $key);
                        @endphp
                        <td>
                            @switch($type)
                                @case('relation')
                                    {{ optional($item->{$key})->{$col['attr'] ?? 'name'} ?? '—' }}
                                    @break
                                @case('badge')
                                    @if ($val !== null && $val !== '')
                                        <span class="badge {{ \Illuminate\Support\Str::slug((string) $val) }}">{{ str_replace('_', ' ', $val) }}</span>
                                    @else — @endif
                                    @break
                                @case('bool')
                                    <span class="badge {{ $val ? 'published' : 'archived' }}">{{ $val ? 'Yes' : 'No' }}</span>
                                    @break
                                @case('image')
                                    @php
                                        $disk = $col['disk'] ?? 'public';
                                        $path = $item->path ?? '';
                                        $imgUrl = $path ? '/storage/' . ltrim($path, '/') : '';
                                    @endphp
                                    @if ($imgUrl && str_starts_with((string) $item->mime_type, 'image/'))
                                        <div class="media-thumb-wrap"><img class="media-thumb" src="{{ $imgUrl }}" alt="" loading="lazy" onerror="this.style.display='none'"></div>
                                    @else
                                        <span class="media-ext">{{ $item->extension ?: 'file' }}</span>
                                    @endif
                                    @break
                                @case('datetime')
                                    <span class="muted">{{ $val ? \Illuminate\Support\Carbon::parse($val)->diffForHumans() : '—' }}</span>
                                    @break
                                @case('date')
                                    <span class="muted">{{ $val ? \Illuminate\Support\Carbon::parse($val)->toFormattedDateString() : '—' }}</span>
                                    @break
                                @case('bytes')
                                    @php $b=(int) $val; $u=['B','KB','MB','GB']; $k=0; while($b>=1024 && $k<3){$b/=1024;$k++;} @endphp
                                    {{ $val ? round($b,1).' '.$u[$k] : '0 B' }}
                                    @break
                                @default
                                    {{ ($val !== null && $val !== '') ? \Illuminate\Support\Str::limit((string) $val, 70) : '—' }}
                            @endswitch
                        </td>
                    @endforeach
                    @if ($canEdit || $canDelete || isset($item->path))
                        <td style="text-align:right;white-space:nowrap">
                            @if (isset($item->path))
                                @php $copyUrl = '/storage/' . ltrim($item->path, '/'); @endphp
                                <button class="copy-url-btn" onclick="copyMediaUrl(this, '{{ $copyUrl }}')" title="Copy URL"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy URL</button>
                            @endif
                            @if ($canEdit)
                                <a class="btn-sm" href="{{ url('/admin/m/'.$resource.'/'.$item->getKey().'/edit') }}">Edit</a>
                            @endif
                            @if ($canDelete)
                                <form method="POST" action="{{ url('/admin/m/'.$resource.'/'.$item->getKey()) }}" style="display:inline" onsubmit="return confirm('Delete this {{ strtolower($def['singular'] ?? 'item') }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-sm" type="submit" style="color:var(--danger)">Delete</button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ count($def['columns']) + 1 }}" class="empty">No {{ strtolower($def['label']) }} yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($items->lastPage() > 1)
    <div class="flex items-center justify-between mt-4 text-[13px]" style="color:var(--text-muted)">
        <span>Page {{ $items->currentPage() }} of {{ $items->lastPage() }} · {{ $items->total() }} total</span>
        <div class="flex items-center gap-2">
            @if ($items->onFirstPage())
                <span class="btn-sm" style="opacity:.5">Previous</span>
            @else
                <a class="btn-sm" href="{{ $items->previousPageUrl() }}">Previous</a>
            @endif
            @if ($items->hasMorePages())
                <a class="btn-sm" href="{{ $items->nextPageUrl() }}">Next</a>
            @else
                <span class="btn-sm" style="opacity:.5">Next</span>
            @endif
        </div>
    </div>
@endif

@if ($resource === 'events')
    {{-- 🏷️ EVENT CATEGORY MANAGEMENT MODAL --}}
    <div id="categoryModal" class="cat-modal-backdrop" onclick="closeCategoryModal(event)">
        <div class="cat-modal-dialog" onclick="event.stopPropagation()">
            <div class="cat-modal-header">
                <div style="display:flex;align-items:center;gap:8px">
                    <span style="font-size:18px">🏷️</span>
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text)">Manage Event Categories</h3>
                </div>
                <button type="button" class="btn-sm" onclick="closeCategoryModal()" style="border:none;background:none;font-size:16px;cursor:pointer">✕</button>
            </div>

            <div class="cat-modal-body">
                {{-- Quick Add Form --}}
                <div style="background:var(--surface-hover);padding:14px;border-radius:10px;margin-bottom:16px;border:1px solid var(--border)">
                    <div style="font-size:12px;font-weight:700;color:var(--text-muted);text-transform:uppercase;margin-bottom:8px">
                        + Add New Category
                    </div>
                    <form id="addCategoryForm" onsubmit="submitNewCategory(event)" style="display:flex;gap:8px;align-items:center">
                        <input type="text" id="newCatName" placeholder="e.g. Science Fair, Robothon…" required class="inline-select" style="flex:1;padding:8px 12px">
                        <input type="color" id="newCatColor" value="#2563eb" title="Badge Color" style="width:36px;height:36px;padding:0;border:1px solid var(--border);border-radius:6px;cursor:pointer;background:none">
                        <button type="submit" id="addCatBtn" class="btn primary" style="white-space:nowrap;padding:8px 14px">
                            Add Category
                        </button>
                    </form>
                    <div id="catFormMsg" style="font-size:12px;margin-top:6px;display:none"></div>
                </div>

                {{-- Categories List Table --}}
                <div style="max-height:360px;overflow-y:auto;border:1px solid var(--border);border-radius:10px">
                    <table style="margin:0;font-size:13px">
                        <thead>
                            <tr style="background:var(--surface)">
                                <th style="padding:10px 14px">Category</th>
                                <th style="padding:10px 14px;text-align:center">Events</th>
                                <th style="padding:10px 14px;text-align:right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody">
                            <tr>
                                <td colspan="3" style="text-align:center;padding:20px;color:var(--text-muted)">Loading categories…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="cat-modal-footer">
                <button type="button" class="btn" onclick="closeCategoryModal()">Done</button>
            </div>
        </div>
    </div>

    <style>
        .cat-modal-backdrop {
            position: fixed; inset: 0; background: rgba(11,25,44,0.65);
            backdrop-filter: blur(6px); z-index: 9999;
            display: none; align-items: center; justify-content: center; padding: 16px;
        }
        .cat-modal-backdrop.open { display: flex; }
        .cat-modal-dialog {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 16px; width: 100%; max-width: 540px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;
            animation: catModalIn .2s ease-out;
        }
        @keyframes catModalIn { from { opacity: 0; transform: scale(.95); } to { opacity: 1; transform: scale(1); } }
        .cat-modal-header {
            padding: 14px 18px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .cat-modal-body { padding: 18px; }
        .cat-modal-footer {
            padding: 12px 18px; border-top: 1px solid var(--border);
            background: var(--surface-hover); display: flex; justify-content: flex-end;
        }
        .cat-color-dot {
            width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px;
        }
    </style>
@endif

<script>
    function copyMediaUrl(btn, url) {
        navigator.clipboard.writeText(window.location.origin + url).then(() => {
            btn.classList.add('copied');
            const orig = btn.innerHTML;
            btn.innerHTML = 'Copied!';
            setTimeout(() => { btn.classList.remove('copied'); btn.innerHTML = orig; }, 1500);
        }).catch(() => {
            btn.innerHTML = 'Failed';
            setTimeout(() => { btn.innerHTML = orig; }, 1500);
        });
    }

    // 🏷️ Category Management Modal AJAX Logic
    function openCategoryModal() {
        const m = document.getElementById('categoryModal');
        if (m) {
            m.classList.add('open');
            loadCategoryList();
        }
    }

    function closeCategoryModal(e) {
        if (e && e.target !== e.currentTarget) return;
        const m = document.getElementById('categoryModal');
        if (m) m.classList.remove('open');
    }

    async function loadCategoryList() {
        const tbody = document.getElementById('categoryTableBody');
        if (!tbody) return;

        try {
            const res = await fetch('/admin/events/categories', {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (!data.success) throw new Error('Failed to load categories');

            if (!data.categories.length) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:16px;color:var(--text-muted)">No categories found.</td></tr>';
                return;
            }

            tbody.innerHTML = data.categories.map(c => `
                <tr>
                    <td style="padding:10px 14px">
                        <span class="cat-color-dot" style="background:${c.color || '#2563eb'}"></span>
                        <strong style="color:var(--text)" id="cat_name_${c.id}">${c.name}</strong>
                    </td>
                    <td style="padding:10px 14px;text-align:center">
                        <span class="badge" style="font-size:11px">${c.events_count || 0} events</span>
                    </td>
                    <td style="padding:10px 14px;text-align:right;white-space:nowrap">
                        <button type="button" class="btn-sm" onclick="editCategoryPrompt('${c.id}', '${c.name.replace(/'/g, "\\'")}', '${c.color || '#2563eb'}')" style="margin-right:4px">Edit</button>
                        <button type="button" class="btn-sm" onclick="deleteCategory('${c.id}', '${c.name.replace(/'/g, "\\'")}', ${c.events_count || 0})" style="color:var(--danger)">Delete</button>
                    </td>
                </tr>
            `).join('');
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="3" style="text-align:center;padding:16px;color:var(--danger)">Error: ${err.message}</td></tr>`;
        }
    }

    async function submitNewCategory(e) {
        e.preventDefault();
        const nameInput = document.getElementById('newCatName');
        const colorInput = document.getElementById('newCatColor');
        const btn = document.getElementById('addCatBtn');
        const msg = document.getElementById('catFormMsg');

        const name = nameInput.value.trim();
        const color = colorInput.value;
        if (!name) return;

        btn.disabled = true;
        msg.style.display = 'none';

        try {
            const res = await fetch('/admin/events/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, color })
            });

            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error creating category');

            nameInput.value = '';
            msg.innerText = `✓ Category "${data.category.name}" added successfully.`;
            msg.style.color = 'var(--success)';
            msg.style.display = 'block';

            loadCategoryList();
        } catch (err) {
            msg.innerText = '✕ ' + err.message;
            msg.style.color = 'var(--danger)';
            msg.style.display = 'block';
        } finally {
            btn.disabled = false;
        }
    }

    async function editCategoryPrompt(id, oldName, oldColor) {
        const newName = prompt(`Rename category "${oldName}" to:`, oldName);
        if (!newName || newName.trim() === '' || newName.trim() === oldName) return;

        try {
            const res = await fetch(`/admin/events/categories/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name: newName.trim(), color: oldColor })
            });

            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error updating category');

            loadCategoryList();
        } catch (err) {
            alert('Failed: ' + err.message);
        }
    }

    async function deleteCategory(id, name, eventCount) {
        const promptMsg = eventCount > 0 
            ? `Delete category "${name}"? ${eventCount} existing event(s) in this category will be reassigned to "General".`
            : `Delete category "${name}"?`;

        if (!confirm(promptMsg)) return;

        try {
            const res = await fetch(`/admin/events/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Error deleting category');

            loadCategoryList();
        } catch (err) {
            alert('Failed: ' + err.message);
        }
    }
</script>

@endsection
