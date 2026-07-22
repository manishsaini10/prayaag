@extends('admin.layout')

@section('title', 'Weekly Mess Menus')
@section('subtitle', 'Manage daily meal menus (Breakfast, Lunch, Snacks, Dinner) and holiday special meal overrides.')

@section('actions')
    <a href="{{ route('admin.mess-menus.create') }}" class="btn-primary inline-flex items-center gap-1.5">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M12 5v14M5 12h14"/></svg>
        <span>New Mess Menu</span>
    </a>
@endsection

@section('content')
<style>
.menu-table-wrap { background:var(--card,#1a1f2e); border:1px solid var(--border,#2a2f3e); border-radius:14px; overflow:hidden; }
.menu-table { width:100%; border-collapse:collapse; }
.menu-table th { padding:12px 16px; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--border,#2a2f3e); text-align:left; }
.menu-table td { padding:14px 16px; border-bottom:1px solid var(--border,#2a2f3e); font-size:14px; color:var(--text,#e2e8f0); vertical-align:middle; }
.menu-table tr:last-child td { border-bottom:none; }
.menu-table tr:hover td { background:rgba(99,102,241,.04); }

.status-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:3px 10px; border-radius:6px; font-size:12px; font-weight:700;
}
.status-badge.active { background:rgba(16,185,129,.15); color:#6ee7b7; }
.status-badge.inactive { background:rgba(100,116,139,.15); color:#94a3b8; }

.action-btn { padding:5px 10px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; text-decoration:none; display:inline-block; text-align:center; }
.btn-edit { background:rgba(59,130,246,.15); color:#93c5fd; border:1px solid rgba(59,130,246,.2); }
.btn-edit:hover { background:rgba(59,130,246,0.3); }
.btn-active { background:rgba(16,185,129,0.15); color:#6ee7b7; border:1px solid rgba(16,185,129,0.2); }
.btn-active:hover { background:rgba(16,185,129,0.3); }
.btn-clone { background:rgba(168,85,247,0.15); color:#e9d5ff; border:1px solid rgba(168,85,247,0.2); }
.btn-clone:hover { background:rgba(168,85,247,0.3); }
.btn-del { background:rgba(239,68,68,.1); color:#fca5a5; border:1px solid rgba(239,68,68,.15); }
.btn-del:hover { background:rgba(239,68,68,.25); }

/* Modal */
.clone-modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:1000; align-items:center; justify-content:center; }
.clone-modal-bg.open { display:flex; }
.clone-modal {
    background:var(--card,#1a1f2e); border:1px solid var(--border,#2a2f3e);
    border-radius:16px; padding:28px; width:440px; max-width:95vw;
    box-shadow:0 24px 60px rgba(0,0,0,.5);
    animation: modalIn .2s ease;
}
@keyframes modalIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
.clone-modal h3 { font-size:18px; font-weight:700; color:#e2e8f0; margin-bottom:20px; }
.clone-field { margin-bottom:16px; }
.clone-field label { display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase; }
.clone-field input {
    width:100%; padding:10px 14px; border:1px solid var(--border,#2a2f3e);
    border-radius:9px; background:#0f1623; color:#e2e8f0; font-size:14px; outline:none;
}
.clone-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; }
.clone-btn-save { padding:9px 20px; background:#6366f1; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer; }
.clone-btn-cancel { padding:9px 16px; background:transparent; color:#94a3b8; border:1px solid var(--border,#2a2f3e); border-radius:8px; cursor:pointer; }
</style>

<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    <div class="menu-table-wrap">
        <table class="menu-table">
            <thead>
                <tr>
                    <th>Menu Title</th>
                    <th>Effective From</th>
                    <th>Effective To</th>
                    <th>Status</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $menu)
                <tr>
                    <td>
                        <div style="font-weight:700;">{{ $menu->title }}</div>
                        <div style="font-size:12px;color:#64748b;">Created by: {{ $menu->creator->name ?? 'System' }}</div>
                    </td>
                    <td>{{ $menu->effective_from->format('M j, Y') }}</td>
                    <td>{{ $menu->effective_to ? $menu->effective_to->format('M j, Y') : 'Ongoing' }}</td>
                    <td>
                        @if($menu->is_active)
                            <span class="status-badge active">Active</span>
                        @else
                            <span class="status-badge inactive">Draft</span>
                        @endif
                    </td>
                    <td style="text-align:right; white-space:nowrap;">
                        <form action="{{ route('admin.mess-menus.toggle', $menu->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="action-btn btn-active">
                                {{ $menu->is_active ? 'Set Draft' : 'Set Active' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.mess-menus.edit', $menu->id) }}" class="action-btn btn-edit">Edit Meals</a>
                        <button class="action-btn btn-clone" onclick="openCloneModal('{{ $menu->id }}')">Duplicate</button>
                        <form action="{{ route('admin.mess-menus.destroy', $menu->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this menu and all daily meals?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn btn-del">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:#475569;">
                        No mess menus configured yet. Create one to display on the school pages!
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Duplicate Menu Modal -->
<div class="clone-modal-bg" id="clone-modal">
    <div class="clone-modal">
        <h3>Duplicate Mess Menu</h3>
        <form id="clone-form" method="POST" action="">
            @csrf
            <div class="clone-field">
                <label>New Effective Date</label>
                <input type="date" name="effective_from" required min="{{ date('Y-m-d') }}">
                <div style="font-size:11px;color:#64748b;margin-top:6px;">Copies all meals and notes from the selected menu to the new start date.</div>
            </div>
            <div class="clone-actions">
                <button type="button" class="clone-btn-cancel" onclick="closeCloneModal()">Cancel</button>
                <button type="submit" class="clone-btn-save">Clone Menu</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCloneModal(id) {
    const modal = document.getElementById('clone-modal');
    const form = document.getElementById('clone-form');
    form.action = `{{ url('/admin/mess-menus') }}/${id}/duplicate`;
    modal.classList.add('open');
}
function closeCloneModal() {
    document.getElementById('clone-modal').classList.remove('open');
}
</script>
@endsection
