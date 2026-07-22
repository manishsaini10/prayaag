@extends('admin.layout')

@section('title', 'Canned Responses')

@section('actions')
    <button id="btn-new-canned" class="btn-primary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
        <span>New Canned Response</span>
    </button>
@endsection

@section('content')
<style>
/* ── Canned Responses Premium UI ── */
.canned-header { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
.canned-search { flex:1; position:relative; }
.canned-search input {
    width:100%; padding:10px 16px 10px 40px;
    border:1px solid var(--border,#2a2f3e); border-radius:10px;
    background:var(--card,#1a1f2e); color:var(--text,#e2e8f0);
    font-size:14px; outline:none; transition:border-color .2s;
}
.canned-search input:focus { border-color:#6366f1; }
.canned-search svg { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#6b7280; width:16px; height:16px; }

.canned-tabs { display:flex; gap:8px; flex-wrap:wrap; }
.canned-tab {
    padding:6px 14px; border-radius:20px; font-size:12px; font-weight:600; cursor:pointer;
    border:1px solid transparent; transition:all .2s;
    background:var(--card,#1a1f2e); color:#94a3b8;
}
.canned-tab.active, .canned-tab:hover { background:#6366f1; color:#fff; }

.canned-table-wrap { background:var(--card,#1a1f2e); border:1px solid var(--border,#2a2f3e); border-radius:14px; overflow:hidden; }
.canned-table { width:100%; border-collapse:collapse; }
.canned-table th { padding:12px 16px; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--border,#2a2f3e); text-align:left; }
.canned-table td { padding:14px 16px; border-bottom:1px solid var(--border,#2a2f3e); font-size:14px; color:var(--text,#e2e8f0); vertical-align:top; }
.canned-table tr:last-child td { border-bottom:none; }
.canned-table tr:hover td { background:rgba(99,102,241,.04); }

.shortcut-badge {
    display:inline-flex; align-items:center; gap:4px;
    background:rgba(99,102,241,.15); color:#a5b4fc;
    padding:3px 10px; border-radius:6px; font-family:monospace; font-size:13px; font-weight:700;
}
.category-badge {
    background:rgba(16,185,129,.15); color:#6ee7b7;
    padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.4px;
}
.body-preview { color:#94a3b8; font-size:13px; max-width:400px; line-height:1.5; white-space:pre-wrap; }

.action-btn { padding:5px 10px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
.btn-edit { background:rgba(59,130,246,.15); color:#93c5fd; border:1px solid rgba(59,130,246,.2); }
.btn-edit:hover { background:rgba(59,130,246,.3); }
.btn-del  { background:rgba(239,68,68,.1); color:#fca5a5; border:1px solid rgba(239,68,68,.15); }
.btn-del:hover  { background:rgba(239,68,68,.25); }

.empty-canned { text-align:center; padding:60px 20px; color:#475569; }
.empty-canned svg { width:48px; height:48px; margin:0 auto 12px; opacity:.4; }

/* Modal */
.cr-modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:1000; align-items:center; justify-content:center; }
.cr-modal-bg.open { display:flex; }
.cr-modal {
    background:var(--card,#1a1f2e); border:1px solid var(--border,#2a2f3e);
    border-radius:16px; padding:28px; width:520px; max-width:95vw;
    box-shadow:0 24px 60px rgba(0,0,0,.5);
    animation: modalIn .2s ease;
}
@keyframes modalIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
.cr-modal h3 { font-size:18px; font-weight:700; color:#e2e8f0; margin-bottom:20px; }
.cr-field { margin-bottom:16px; }
.cr-field label { display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
.cr-field input, .cr-field select, .cr-field textarea {
    width:100%; padding:10px 14px; border:1px solid var(--border,#2a2f3e);
    border-radius:9px; background:#0f1623; color:#e2e8f0; font-size:14px; outline:none; transition:border-color .2s; resize:vertical;
}
.cr-field input:focus, .cr-field select:focus, .cr-field textarea:focus { border-color:#6366f1; }
.cr-field textarea { min-height:100px; }
.cr-hint { font-size:11px; color:#64748b; margin-top:4px; }
.cr-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
.cr-btn-save { padding:9px 20px; background:#6366f1; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer; transition:background .2s; }
.cr-btn-save:hover { background:#4f46e5; }
.cr-btn-cancel { padding:9px 16px; background:transparent; color:#94a3b8; border:1px solid var(--border,#2a2f3e); border-radius:8px; cursor:pointer; }
</style>

<div class="canned-header">
    <div class="canned-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="canned-search-input" placeholder="Search by shortcut or content…" />
    </div>
</div>

<div class="canned-tabs" id="canned-tabs" style="margin-bottom:20px;">
    <div class="canned-tab active" data-cat="">All</div>
    @foreach($categories as $cat)
        <div class="canned-tab" data-cat="{{ $cat }}">{{ $cat }}</div>
    @endforeach
</div>

<div class="canned-table-wrap">
    <table class="canned-table" id="canned-table">
        <thead>
            <tr>
                <th>Shortcut</th>
                <th>Response Body</th>
                <th>Category</th>
                <th style="text-align:right">Actions</th>
            </tr>
        </thead>
        <tbody id="canned-tbody">
            @forelse($responses as $cr)
            <tr data-id="{{ $cr->id }}" data-cat="{{ $cr->category }}">
                <td><span class="shortcut-badge">/{{ $cr->shortcut }}</span></td>
                <td><div class="body-preview">{{ Str::limit($cr->body, 120) }}</div></td>
                <td>
                    @if($cr->category)
                        <span class="category-badge">{{ $cr->category }}</span>
                    @else
                        <span style="color:#475569">—</span>
                    @endif
                </td>
                <td style="text-align:right; white-space:nowrap;">
                    <button class="action-btn btn-edit" onclick="editCanned('{{ $cr->id }}','{{ addslashes($cr->shortcut) }}',{{ json_encode($cr->body) }},'{{ $cr->category }}')">Edit</button>
                    <button class="action-btn btn-del" onclick="deleteCanned('{{ $cr->id }}',this)">Delete</button>
                </td>
            </tr>
            @empty
            <tr id="empty-row">
                <td colspan="4">
                    <div class="empty-canned">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p>No canned responses yet. Create your first one!</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="cr-modal-bg" id="cr-modal">
    <div class="cr-modal">
        <h3 id="cr-modal-title">New Canned Response</h3>
        <input type="hidden" id="cr-edit-id" value="">
        <div class="cr-field">
            <label>Shortcut <span style="color:#ef4444">*</span></label>
            <input type="text" id="cr-shortcut" placeholder="e.g. welcome, fee-info, admission" maxlength="50">
            <div class="cr-hint">Agents type /<strong>shortcut</strong> in the reply box to insert this response instantly.</div>
        </div>
        <div class="cr-field">
            <label>Category</label>
            <input type="text" id="cr-category" placeholder="e.g. admission, fee, general" maxlength="50">
        </div>
        <div class="cr-field">
            <label>Response Body <span style="color:#ef4444">*</span></label>
            <textarea id="cr-body" placeholder="Type the full response here…"></textarea>
        </div>
        <div class="cr-actions">
            <button class="cr-btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="cr-btn-save" onclick="saveCanned()">Save Response</button>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const BASE = '{{ url("/admin/chatbot/canned") }}';

document.getElementById('btn-new-canned').addEventListener('click', () => openModal());

document.getElementById('canned-search-input').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#canned-tbody tr[data-id]').forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
});

document.querySelectorAll('.canned-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.canned-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const cat = this.dataset.cat;
        document.querySelectorAll('#canned-tbody tr[data-id]').forEach(row => {
            row.style.display = (!cat || row.dataset.cat === cat) ? '' : 'none';
        });
    });
});

function openModal(id='', shortcut='', body='', category='') {
    document.getElementById('cr-edit-id').value = id;
    document.getElementById('cr-shortcut').value = shortcut;
    document.getElementById('cr-body').value = body;
    document.getElementById('cr-category').value = category;
    document.getElementById('cr-modal-title').textContent = id ? 'Edit Canned Response' : 'New Canned Response';
    document.getElementById('cr-modal').classList.add('open');
}
function closeModal() {
    document.getElementById('cr-modal').classList.remove('open');
}
document.getElementById('cr-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
function editCanned(id, shortcut, body, category) {
    openModal(id, shortcut, body, category || '');
}

async function saveCanned() {
    const id       = document.getElementById('cr-edit-id').value;
    const shortcut = document.getElementById('cr-shortcut').value.trim();
    const body     = document.getElementById('cr-body').value.trim();
    const category = document.getElementById('cr-category').value.trim();

    if (!shortcut || !body) { alert('Shortcut and body are required.'); return; }

    const url    = id ? `${BASE}/${id}` : BASE;
    const method = id ? 'PUT' : 'POST';

    const res = await fetch(url, {
        method,
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
        body: JSON.stringify({ shortcut, body, category }),
    });
    if (res.ok) {
        closeModal();
        location.reload();
    } else {
        const err = await res.json();
        alert(Object.values(err.errors || { e: err.message }).join('\n'));
    }
}

async function deleteCanned(id, btn) {
    if (!confirm('Delete this canned response?')) return;
    const res = await fetch(`${BASE}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept':'application/json' },
    });
    if (res.ok) {
        btn.closest('tr').remove();
    } else {
        alert('Failed to delete.');
    }
}
</script>
@endsection
