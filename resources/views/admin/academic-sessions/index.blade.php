@extends('admin.layout')

@section('title', 'Academic Sessions')
@section('subtitle', 'Manage school academic years (e.g. 2026-2027). Only one session can be active at a time.')

@section('actions')
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.academic-calendar-entries.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to Calendar</a>
        <a href="{{ route('admin.academic-sessions.create') }}" class="btn primary"><x-admin.icon name="plus"/> New Session</a>
    </div>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        {{ session('status') }}
    </div>
@endif

<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr>
                <th>Session Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Active / Current</th>
                <th>Calendar Entries</th>
                <th style="text-align:right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sessions as $s)
                <tr>
                    <td><strong>{{ $s->session_name }}</strong></td>
                    <td>{{ $s->start_date->format('Y-m-d') }}</td>
                    <td>{{ $s->end_date->format('Y-m-d') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.academic-sessions.toggle', $s->id) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="badge {{ $s->is_current ? 'published' : 'draft' }}" style="border:none; cursor:pointer; font-family:inherit" title="Click to toggle active status">
                                {{ $s->is_current ? 'Yes (Active)' : 'No (Inactive)' }}
                            </button>
                        </form>
                    </td>
                    <td>{{ $s->entries_count }}</td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm primary" href="{{ route('admin.academic-sessions.edit', $s->id) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.academic-sessions.destroy', $s->id) }}" style="display:inline" onsubmit="return confirm('Delete this session and all its terms and entries?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm" type="submit" style="color:var(--danger);border:none;background:transparent;cursor:pointer">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">No academic sessions defined yet. Create one to get started.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
