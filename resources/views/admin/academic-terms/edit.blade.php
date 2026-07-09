@extends('admin.layout')

@section('title', 'Edit Term')
@section('subtitle', 'Modify academic term name, parent session, or boundaries.')

@section('actions')
    <a href="{{ route('admin.academic-terms.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to Terms</a>
@endsection

@section('content')

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">
        <ul style="margin:0;padding-left:16px">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card" style="padding:24px;max-width:600px">
    <form method="POST" action="{{ route('admin.academic-terms.update', $term->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom:20px">
            <label style="display:block;font-weight:600;margin-bottom:8px">Academic Session *</label>
            <select name="session_id" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px;background:#fff">
                @foreach($sessions as $s)
                    <option value="{{ $s->id }}" @selected($term->session_id === $s->id)>{{ $s->session_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom:20px">
            <label style="display:block;font-weight:600;margin-bottom:8px">Term Name *</label>
            <input type="text" name="term_name" value="{{ $term->term_name }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px">
            <div class="form-group">
                <label style="display:block;font-weight:600;margin-bottom:8px">Start Date *</label>
                <input type="date" name="start_date" value="{{ $term->start_date->toDateString() }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
            </div>

            <div class="form-group">
                <label style="display:block;font-weight:600;margin-bottom:8px">End Date *</label>
                <input type="date" name="end_date" value="{{ $term->end_date->toDateString() }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
            </div>
        </div>

        <div style="border-top:1px solid #e2e8f0;padding-top:20px;text-align:right">
            <a href="{{ route('admin.academic-terms.index') }}" class="btn" style="margin-right:8px">Cancel</a>
            <button type="submit" class="btn primary">Update Term</button>
        </div>
    </form>
</div>

@endsection
