@extends('admin.layout')

@section('title', 'Pre-Chat Form Builder')

@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ url('/admin/chatbot/form-fields/submissions') }}" class="btn-secondary inline-flex items-center gap-1.5">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Submissions ({{ $submissionsCount }})</span>
        </a>
        <a href="{{ url('/admin/chatbot') }}" class="btn-secondary inline-flex items-center gap-1.5">
            &larr; Back to Chatbot Settings
        </a>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    {{-- Add New Field --}}
    <div class="card p-6">
        <h3 class="text-lg font-semibold mb-4" style="color:var(--text)">Add New Field</h3>
        <form method="POST" action="{{ url('/admin/chatbot/form-fields') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Label *</label>
                <input type="text" name="label" placeholder="e.g. Full Name" required class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Field Key *</label>
                <input type="text" name="field_key" placeholder="e.g. full_name" required class="w-full">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Type *</label>
                <select name="field_type" required class="w-full">
                    <option value="text">Text</option>
                    <option value="email">Email</option>
                    <option value="tel">Phone</option>
                    <option value="select">Select / Dropdown</option>
                    <option value="textarea">Textarea</option>
                    <option value="number">Number</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Placeholder</label>
                <input type="text" name="placeholder" placeholder="Optional" class="w-full">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1" style="color:var(--text)">
                    Options <span class="text-xs" style="color:var(--text-muted)">(comma separated, for select type)</span>
                </label>
                <input type="text" name="options" placeholder="Option 1, Option 2, Option 3" class="w-full">
            </div>
            <div class="flex items-end gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1">
                    <span class="text-sm font-medium" style="color:var(--text)">Required</span>
                </label>
                <button type="submit" class="btn-primary">Add Field</button>
            </div>
        </form>
    </div>

    {{-- Fields List --}}
    <div class="card p-0 overflow-hidden">
        @if($fields->isEmpty())
            <div class="text-center py-12 text-sm" style="color:var(--text-muted)">
                <p class="mb-2">No form fields created yet.</p>
                <p>Add fields above to build your pre-chat form. The widget will render them in order.</p>
            </div>
        @else
            <table class="w-full text-sm" id="fields-table">
                <thead>
                    <tr style="border-bottom:1px solid var(--border)">
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted);width:40px">#</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Label</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Key</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Type</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Required</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Active</th>
                        <th class="text-left px-4 py-3 font-semibold" style="color:var(--text-muted)">Actions</th>
                    </tr>
                </thead>
                <tbody id="sortable-body">
                    @foreach($fields as $field)
                        <tr style="border-bottom:1px solid var(--border)" class="hover:bg-surface-2/50 transition" data-id="{{ $field->id }}">
                            <td class="px-4 py-3 cursor-grab" style="color:var(--text-muted)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M8 6h.01M16 6h.01M8 12h.01M16 12h.01M8 18h.01M16 18h.01"/></svg>
                            </td>
                            <td class="px-4 py-3 font-medium" style="color:var(--text)">{{ $field->label }}</td>
                            <td class="px-4 py-3" style="color:var(--text-muted)"><code>{{ $field->field_key }}</code></td>
                            <td class="px-4 py-3">
                                <span class="badge">{{ $field->field_type }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($field->is_required)
                                    <span class="badge" style="background:#fee2e2;color:#991b1b">Required</span>
                                @else
                                    <span class="badge" style="background:#f3f4f6;color:#6b7280">Optional</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ url('/admin/chatbot/form-fields/' . $field->id . '/toggle') }}" style="display:inline">
                                    @csrf
                                    <button type="submit" style="background:none;border:none;cursor:pointer">
                                        @if($field->is_active)
                                            <span class="badge" style="background:#dcfce7;color:#166534">Active</span>
                                        @else
                                            <span class="badge" style="background:#fef2f2;color:#991b1b">Inactive</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="text-sm font-medium hover:underline" style="color:var(--primary)" onclick="editField('{{ $field->id }}', '{{ addslashes($field->label) }}', '{{ $field->field_key }}', '{{ $field->field_type }}', '{{ addslashes($field->placeholder ?? '') }}', '{{ addslashes(json_encode($field->options ?? [])) }}', {{ $field->is_required ? 'true' : 'false' }}, {{ $field->is_active ? 'true' : 'false' }})">Edit</button>
                                    <form method="POST" action="{{ url('/admin/chatbot/form-fields/' . $field->id) }}" onsubmit="return confirm('Delete this field?')" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium hover:underline" style="color:#dc2626">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden" style="background:rgba(0,0,0,0.4);backdrop-filter:blur(2px)">
    <div class="flex items-center justify-center min-h-full p-4">
        <div class="card p-6 w-full max-w-lg" style="max-height:90vh;overflow-y:auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold" style="color:var(--text)">Edit Form Field</h3>
                <button type="button" class="text-xl leading-none" style="color:var(--text-muted)" onclick="closeEdit()">&times;</button>
            </div>
            <form method="POST" id="edit-form" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Label *</label>
                    <input type="text" name="label" id="edit-label" required class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Field Key *</label>
                    <input type="text" name="field_key" id="edit-field-key" required class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Type *</label>
                    <select name="field_type" id="edit-field-type" required class="w-full">
                        <option value="text">Text</option>
                        <option value="email">Email</option>
                        <option value="tel">Phone</option>
                        <option value="select">Select / Dropdown</option>
                        <option value="textarea">Textarea</option>
                        <option value="number">Number</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">Placeholder</label>
                    <input type="text" name="placeholder" id="edit-placeholder" class="w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:var(--text)">
                        Options <span class="text-xs" style="color:var(--text-muted)">(comma separated, for select type)</span>
                    </label>
                    <input type="text" name="options" id="edit-options" placeholder="Option 1, Option 2, Option 3" class="w-full">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" id="edit-is-required" value="1">
                        <span class="text-sm font-medium" style="color:var(--text)">Required</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit-is-active" value="1">
                        <span class="text-sm font-medium" style="color:var(--text)">Active</span>
                    </label>
                </div>
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary">Update Field</button>
                    <button type="button" class="btn-secondary" onclick="closeEdit()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editField(id, label, key, type, placeholder, optionsJson, isRequired, isActive) {
    document.getElementById('edit-form').action = '/admin/chatbot/form-fields/' + id;
    document.getElementById('edit-label').value = label;
    document.getElementById('edit-field-key').value = key;
    document.getElementById('edit-field-type').value = type;
    document.getElementById('edit-placeholder').value = placeholder;
    try {
        const opts = JSON.parse(optionsJson);
        document.getElementById('edit-options').value = Array.isArray(opts) ? opts.join(', ') : '';
    } catch(e) {
        document.getElementById('edit-options').value = '';
    }
    document.getElementById('edit-is-required').checked = isRequired;
    document.getElementById('edit-is-active').checked = isActive;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEdit() {
    document.getElementById('edit-modal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEdit();
});
</script>

{{-- Sortable Drag & Drop --}}
<script>
(function() {
    const tbody = document.getElementById('sortable-body');
    if (!tbody) return;
    let dragRow = null;

    tbody.addEventListener('dragstart', function(e) {
        dragRow = e.target.closest('tr');
        if (dragRow) e.dataTransfer.effectAllowed = 'move';
    });

    tbody.addEventListener('dragover', function(e) {
        e.preventDefault();
        const target = e.target.closest('tr');
        if (!target || target === dragRow) return;
        const rect = target.getBoundingClientRect();
        const mid = rect.top + rect.height / 2;
        if (e.clientY < mid) {
            target.parentNode.insertBefore(dragRow, target);
        } else {
            target.parentNode.insertBefore(dragRow, target.nextSibling);
        }
    });

    tbody.addEventListener('dragend', function() {
        if (!dragRow) return;
        const order = Array.from(tbody.querySelectorAll('tr[data-id]')).map(tr => tr.getAttribute('data-id'));
        fetch('/admin/chatbot/form-fields/reorder', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
            body: JSON.stringify({ order: order })
        }).catch(() => {});
        dragRow = null;
    });

    tbody.querySelectorAll('tr[data-id]').forEach(tr => tr.draggable = true);
})();
</script>
@endsection
