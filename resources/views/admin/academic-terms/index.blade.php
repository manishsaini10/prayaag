@extends('admin.layout')

@section('title', 'Academic Terms')
@section('subtitle', 'Configure semesters or term divisions (e.g. Term 1, Term 2) inside academic sessions.')

@section('actions')
    <div style="display:flex;gap:10px">
        <a href="{{ route('admin.academic-calendar-entries.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to Calendar</a>
        <a href="{{ route('admin.academic-terms.create') }}" class="btn primary"><x-admin.icon name="plus"/> New Term</a>
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
                <th>Term Name</th>
                <th>Academic Session</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th style="text-align:right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($terms as $t)
                <tr>
                    <td><strong>{{ $t->term_name }}</strong></td>
                    <td>{{ $t->session->session_name ?? 'N/A' }}</td>
                    <td>{{ $t->start_date->format('Y-m-d') }}</td>
                    <td>{{ $t->end_date->format('Y-m-d') }}</td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm primary" href="{{ route('admin.academic-terms.edit', $t->id) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.academic-terms.destroy', $t->id) }}" style="display:inline" onsubmit="return confirm('Delete this term and all its calendar entries?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm" type="submit" style="color:var(--danger);border:none;background:transparent;cursor:pointer">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">No academic terms defined yet. Click "New Term" to configure.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
