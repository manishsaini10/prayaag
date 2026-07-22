@extends('admin.layout')

@section('title', 'Configure Mess Menu')
@section('subtitle', "Weekly grid structure for {$menu->title}")

@section('actions')
    <a href="{{ route('admin.mess-menus.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to List</a>
@endsection

@section('content')
<style>
.grid-table { width:100%; border-collapse:collapse; background:var(--card,#1a1f2e); border:1px solid var(--border,#2a2f3e); border-radius:12px; overflow:hidden; }
.grid-table th { padding:14px 16px; background:#1e2435; font-size:13px; font-weight:700; color:#94a3b8; text-transform:uppercase; border-bottom:2px solid var(--border,#2a2f3e); text-align:left; }
.grid-table td { padding:12px 14px; border-bottom:1px solid var(--border,#2a2f3e); font-size:13.5px; vertical-align:top; }
.grid-table tr:last-child td { border-bottom:none; }

.day-header { font-weight:700; color:#f1f5f9; text-transform:capitalize; padding-top:16px; font-size:14px; }

.cell-wrap { display:flex; flex-direction:column; gap:6px; }
.cell-input {
    width:100%; padding:8px 10px; border:1px solid var(--border,#2a2f3e);
    border-radius:7px; background:#0f1623; color:#f1f5f9; font-size:13px; outline:none; transition:border-color .15s;
}
.cell-input:focus { border-color:#6366f1; }
.cell-note {
    width:100%; padding:5px 8px; border:1px solid var(--border,#2a2f3e);
    border-radius:6px; background:rgba(15,22,35,0.4); color:#94a3b8; font-size:11px; outline:none; transition:border-color .15s;
}
.cell-note:focus { border-color:#10b981; }

.section-card { background:var(--card,#1a1f2e); border:1px solid var(--border,#2a2f3e); border-radius:12px; padding:20px; margin-top:24px; }
.section-title { font-size:16px; font-weight:700; color:#f1f5f9; margin-bottom:16px; display:flex; align-items:center; gap:8px; }

.override-table { width:100%; border-collapse:collapse; margin-top:12px; }
.override-table th { padding:10px; text-align:left; font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; border-bottom:1px solid var(--border,#2a2f3e); }
.override-table td { padding:12px 10px; border-bottom:1px solid var(--border,#2a2f3e); font-size:13px; }
.override-table tr:last-child td { border-bottom:none; }
</style>

<div class="space-y-6">
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.mess-menus.update', $menu->id) }}">
        @csrf
        @method('PUT')

        {{-- Section 1: Basic settings --}}
        <div class="card" style="padding:20px; margin-bottom:20px; display:flex; gap:16px; flex-wrap:wrap; background:var(--card,#1a1f2e); border-color:var(--border,#2a2f3e);">
            <div style="flex:2; min-width:240px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Menu Title</label>
                <input type="text" name="title" value="{{ old('title', $menu->title) }}" class="cell-input" style="font-size:14px; padding:10px;" required>
            </div>
            <div style="flex:1; min-width:140px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Effective From</label>
                <input type="date" name="effective_from" value="{{ old('effective_from', $menu->effective_from->format('Y-m-d')) }}" class="cell-input" style="font-size:14px; padding:10px;" required>
            </div>
            <div style="flex:1; min-width:140px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#94a3b8; margin-bottom:6px; text-transform:uppercase;">Effective To (Optional)</label>
                <input type="date" name="effective_to" value="{{ old('effective_to', $menu->effective_to ? $menu->effective_to->format('Y-m-d') : '') }}" class="cell-input" style="font-size:14px; padding:10px;">
            </div>
        </div>

        {{-- Section 2: Weekly Grid --}}
        <div style="overflow-x:auto;">
            <table class="grid-table">
                <thead>
                    <tr>
                        <th style="width:120px;">Day</th>
                        <th>Breakfast</th>
                        <th>Lunch</th>
                        <th>Snacks</th>
                        <th>Dinner</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        $meals = ['breakfast', 'lunch', 'snacks', 'dinner'];
                    @endphp

                    @foreach($days as $day)
                    <tr>
                        <td class="day-header">{{ $day }}</td>
                        @foreach($meals as $meal)
                        <td>
                            <div class="cell-wrap">
                                <input type="text" 
                                       name="grid[{{ $day }}][{{ $meal }}][items]" 
                                       value="{{ old("grid.{$day}.{$meal}.items", $grid[$day][$meal]['items_str'] ?? '') }}" 
                                       class="cell-input" 
                                       placeholder="e.g. Poha, Boiled Eggs, Tea">
                                <input type="text" 
                                       name="grid[{{ $day }}][{{ $meal }}][notes]" 
                                       value="{{ old("grid.{$day}.{$meal}.notes", $grid[$day][$meal]['notes'] ?? '') }}" 
                                       class="cell-note" 
                                       placeholder="Add note (optional)">
                            </div>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
            <button type="submit" class="btn-primary" style="padding:10px 24px; border-radius:8px; border:none; font-weight:600; cursor:pointer;">Save Menu Grid</button>
            <a href="{{ route('admin.mess-menus.index') }}" class="btn" style="padding:10px 18px;">Cancel</a>
        </div>
    </form>

    {{-- Section 3: Special Override Days --}}
    <div class="section-card">
        <h3 class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;color:#f59e0b;"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Special Override Days</span>
        </h3>
        <p class="text-xs" style="color:#64748b; margin-top:-10px; margin-bottom:16px;">Configure single-day meal overrides for holidays, festivals, or special dining events.</p>

        <form method="POST" action="{{ route('admin.mess-menus.special.store', $menu->id) }}" style="display:grid; grid-template-cols:1fr 1fr 1fr 2fr auto; gap:12px; align-items:flex-end; background:#141b27; padding:16px; border-radius:10px; border:1px solid var(--border,#2a2f3e);">
            @csrf
            <div>
                <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">Date</label>
                <input type="date" name="date" class="cell-input" required>
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">Override Label</label>
                <input type="text" name="label" class="cell-input" placeholder="e.g. Independence Day Special">
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">Meal Type</label>
                <select name="meal_type" class="cell-input">
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="snacks">Snacks</option>
                    <option value="dinner">Dinner</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:11px; font-weight:600; color:#94a3b8; margin-bottom:4px;">Override Menu Items</label>
                <input type="text" name="items" class="cell-input" placeholder="e.g. Special Kheer, Poori, Chana" required>
            </div>
            <div>
                <button type="submit" class="btn-primary" style="padding:10px 16px; border-radius:8px; border:none; font-weight:600; cursor:pointer;">Add Override</button>
            </div>
        </form>

        <table class="override-table">
            <thead>
                <tr>
                    <th style="width:120px;">Date</th>
                    <th style="width:180px;">Label</th>
                    <th style="width:100px;">Meal</th>
                    <th>Menu Items</th>
                    <th style="text-align:right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($specials as $special)
                <tr>
                    <td style="font-weight:600;">{{ $special->date->format('M j, Y') }}</td>
                    <td><span style="background:rgba(245,158,11,0.15); color:#f59e0b; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700;">{{ $special->label ?: 'Special Day' }}</span></td>
                    <td style="text-transform:capitalize;">{{ $special->meal_type }}</td>
                    <td>{{ implode(', ', $special->items) }}</td>
                    <td style="text-align:right;">
                        <form action="{{ route('admin.mess-menus.special.destroy', [$menu->id, $special->id]) }}" method="POST" onsubmit="return confirm('Delete this special override?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn btn-del">Remove</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px; color:#475569;">No special override days configured for this menu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
