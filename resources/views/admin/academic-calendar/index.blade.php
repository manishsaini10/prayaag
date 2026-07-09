@extends('admin.layout')

@section('title', 'Academic Calendar Manager')
@section('subtitle', 'Configure school sessions, terms, exam schedules, holidays, and other academic events.')

@section('actions')
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.academic-sessions.index') }}" class="btn"><x-admin.icon name="cog"/> Sessions</a>
        <a href="{{ route('admin.academic-calendar-entries.import') }}" class="btn"><x-admin.icon name="arrow-up-on-square"/> Import Utility</a>
        <a href="{{ route('admin.academic-calendar-entries.create') }}" class="btn primary"><x-admin.icon name="plus"/> New Entry</a>
    </div>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

@if (session('warning'))
    <div class="card" style="border-color:var(--warning);background:var(--warning-soft);color:var(--warning);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('warning') }}
    </div>
@endif

<div class="card" style="padding:16px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px">
    <form method="GET" action="{{ route('admin.academic-calendar-entries.index') }}" style="display:flex; align-items:center; gap:10px">
        <label style="font-weight:600; font-size:13.5px">Select Session:</label>
        <select name="session_id" onchange="this.form.submit()" style="padding:6px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13.5px; background:#fff">
            <option value="">— All Sessions —</option>
            @foreach($sessions as $s)
                <option value="{{ $s->id }}" @selected($selectedSessionId == $s->id)>{{ $s->session_name }} @if($s->is_current)(Active)@endif</option>
            @endforeach
        </select>
    </form>
    <div class="muted font-semibold" style="font-size:13px">
        Showing entries for: 
        <strong>
            @php $selSess = $sessions->firstWhere('id', $selectedSessionId); @endphp
            {{ $selSess ? $selSess->session_name : 'All Sessions' }}
        </strong>
    </div>
</div>

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Session</th>
                <th>Dates</th>
                <th>Relevance</th>
                <th>Working Day</th>
                <th>Status</th>
                <th style="text-align:right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>
                        <strong>{{ $entry->title }}</strong>
                        @if($entry->sub_type)
                            <span class="muted" style="font-size:11px">({{ $entry->sub_type }})</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $entry->category }}" style="font-size:10px;text-transform:uppercase;padding:2px 8px;border-radius:4px;font-weight:600;
                            @if($entry->category === 'exam') background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;
                            @elseif($entry->category === 'holiday') background:#fef3c7;color:#92400e;border:1px solid #fcd34d;
                            @elseif($entry->category === 'important_date') background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;
                            @else background:#f3f4f6;color:#374151;border:1px solid #e5e7eb; @endif
                        ">
                            {{ str_replace('_', ' ', $entry->category) }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:500">{{ $entry->session->session_name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div>{{ $entry->start_date->format('Y-m-d') }}</div>
                        @if ($entry->end_date)
                            <div class="muted" style="font-size:11px">to {{ $entry->end_date->format('Y-m-d') }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $entry->class ? $entry->class->class_name : 'All Classes' }}
                    </td>
                    <td>
                        <span class="badge {{ $entry->is_working_day ? 'published' : 'draft' }}">
                            {{ $entry->is_working_day ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $entry->status === 'published' ? 'published' : 'draft' }}">
                            {{ ucfirst($entry->status) }}
                        </span>
                    </td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm primary" href="{{ route('admin.academic-calendar-entries.edit', $entry->id) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.academic-calendar-entries.destroy', $entry->id) }}" style="display:inline" onsubmit="return confirm('Delete this calendar entry?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm" type="submit" style="color:var(--danger);border:none;background:transparent;cursor:pointer">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty">No academic calendar entries created yet. Click "New Entry" to configure.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px">
    {{ $entries->links() }}
</div>

@endsection
