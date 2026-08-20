@extends('admin.layout')

@php $isEdit = $mode === 'edit'; @endphp

@section('title', ($isEdit ? 'Edit ' : 'New ') . strtolower($def['singular'] ?? 'item'))
@section('subtitle', $def['label'])

@section('actions')
    <a href="{{ url('/admin/m/'.$resource) }}" class="btn"><x-admin.icon name="chevron-right" style="transform:rotate(180deg)"/> Back to {{ strtolower($def['label']) }}</a>
@endsection

@section('content')

<style>
    .frm{max-width:760px}
    .frm-field{margin-bottom:18px}
    .frm-label{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:6px}
    .frm-input,.frm-select,.frm-textarea{width:100%;padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px}
    .frm-input:focus,.frm-select:focus,.frm-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .frm-textarea{resize:vertical;min-height:90px}
    .frm-check{display:flex;align-items:center;gap:9px;font-size:14px;color:var(--text);cursor:pointer}
    .frm-check input{width:18px;height:18px;accent-color:var(--primary)}
    .frm-err{color:var(--danger);font-size:12.5px;margin-top:5px}
    .frm-actions{display:flex;gap:10px;padding-top:6px;border-top:1px solid var(--border);margin-top:4px}
</style>

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">
        Please fix the highlighted fields below.
    </div>
@endif

<div class="card frm" style="padding:22px">
    <form method="POST" action="{{ $isEdit ? url('/admin/m/'.$resource.'/'.$item->getKey()) : url('/admin/m/'.$resource) }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @foreach ($def['fields'] as $f)
            @php
                $key = $f['key'];
                $type = $f['type'] ?? 'text';
                $attr = $item->getAttribute($key);
                $nullable = str_contains($f['rules'] ?? '', 'nullable');
            @endphp

            <div class="frm-field">
                @if ($type === 'bool')
                    <label class="frm-check">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $attr))>
                        {{ $f['label'] }}
                    </label>
                @else
                    <label class="frm-label" for="f_{{ $key }}">{{ $f['label'] }}</label>

                    @switch($type)
                        @case('textarea')
                            <textarea id="f_{{ $key }}" name="{{ $key }}" rows="{{ $f['rows'] ?? 4 }}" class="frm-textarea">{{ old($key, $attr) }}</textarea>
                            @break

                        @case('select')
                            <select id="f_{{ $key }}" name="{{ $key }}" class="frm-select">
                                @foreach ($f['options'] as $ov => $ol)
                                    <option value="{{ $ov }}" @selected((string) old($key, $attr) === (string) $ov)>{{ $ol }}</option>
                                @endforeach
                            </select>
                            @if ($resource === 'events' && $key === 'category')
                                <div style="font-size:12px;margin-top:6px;display:flex;align-items:center;gap:6px">
                                    <span>🏷️</span>
                                    <a href="javascript:void(0)" onclick="openCategoryModal()" style="color:var(--primary);font-weight:600;text-decoration:none">
                                        + Manage / Add New Event Category
                                    </a>
                                </div>
                            @endif
                            @break

                        @case('belongsTo')
                            <select id="f_{{ $key }}" name="{{ $key }}" class="frm-select">
                                @if ($nullable)<option value="">— None —</option>@endif
                                @foreach (($options[$key] ?? []) as $ov => $ol)
                                    <option value="{{ $ov }}" @selected((string) old($key, $attr) === (string) $ov)>{{ $ol }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('datetime')
                            <input type="datetime-local" id="f_{{ $key }}" name="{{ $key }}" class="frm-input"
                                   value="{{ old($key, $attr ? \Illuminate\Support\Carbon::parse($attr)->format('Y-m-d\TH:i') : '') }}">
                            @break

                        @case('date')
                            <input type="date" id="f_{{ $key }}" name="{{ $key }}" class="frm-input"
                                   value="{{ old($key, $attr ? \Illuminate\Support\Carbon::parse($attr)->format('Y-m-d') : '') }}">
                            @break

                        @case('number')
                            <input type="number" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" value="{{ old($key, $attr) }}">
                            @break

                        @case('password')
                            <input type="password" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" autocomplete="new-password"
                                   placeholder="{{ $isEdit ? 'Leave blank to keep current password' : '' }}">
                            @break

                        @case('email')
                            <input type="email" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" value="{{ old($key, $attr) }}">
                            @break

                        @default
                            <input type="text" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" value="{{ old($key, $attr) }}"
                                   @if ($type === 'slug') data-slug-target @endif>
                    @endswitch
                @endif

                @error($key)<div class="frm-err">{{ $message }}</div>@enderror
            </div>
        @endforeach

        <div class="frm-actions">
            <button type="submit" class="btn primary">{{ $isEdit ? 'Save changes' : 'Create '.strtolower($def['singular'] ?? 'item') }}</button>
            <a href="{{ url('/admin/m/'.$resource) }}" class="btn">Cancel</a>
        </div>
    </form>
</div>

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
                        <input type="text" id="newCatName" placeholder="e.g. Robotics Fest, Annual Sports…" required class="inline-select" style="flex:1;padding:8px 12px">
                        <input type="color" id="newCatColor" value="#2563eb" title="Badge Color" style="width:36px;height:36px;padding:0;border:1px solid var(--border);border-radius:6px;cursor:pointer;background:none">
                        <button type="submit" id="addCatBtn" class="btn primary" style="white-space:nowrap;padding:8px 14px">
                            Add Category
                        </button>
                    </form>
                    <div id="catFormMsg" style="font-size:12px;margin-top:6px;display:none"></div>
                </div>

                {{-- Categories List Table --}}
                <div style="max-height:300px;overflow-y:auto;border:1px solid var(--border);border-radius:10px">
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
    // Auto-fill an empty slug field from the first title/name input.
    (function () {
        const slug = document.querySelector('[data-slug-target]');
        if (!slug) return;
        const source = document.querySelector('#f_title, #f_name');
        if (!source) return;
        const slugify = (s) => s.toString().toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        source.addEventListener('blur', () => { if (!slug.value.trim()) slug.value = slugify(source.value); });
    })();

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
                        <strong style="color:var(--text)">${c.name}</strong>
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

            // Sync select options in the form
            const catSelect = document.getElementById('f_category');
            if (catSelect) {
                const currentVal = catSelect.value;
                catSelect.innerHTML = data.categories.map(c => 
                    `<option value="${c.name}" ${currentVal === c.name ? 'selected' : ''}>${c.name}</option>`
                ).join('');
            }
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

            // Also auto-select in form
            const catSelect = document.getElementById('f_category');
            if (catSelect) {
                const opt = document.createElement('option');
                opt.value = data.category.name;
                opt.textContent = data.category.name;
                opt.selected = true;
                catSelect.appendChild(opt);
            }

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
