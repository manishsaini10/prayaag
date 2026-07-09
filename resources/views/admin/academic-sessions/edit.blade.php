@extends('admin.layout')

@section('title', 'Edit Session')
@section('subtitle', 'Modify academic session name, dates, or toggle current status.')

@section('actions')
    <a href="{{ route('admin.academic-sessions.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to Sessions</a>
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
    <form method="POST" action="{{ route('admin.academic-sessions.update', $session->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group" style="margin-bottom:20px">
            <label style="display:block;font-weight:600;margin-bottom:8px">Session Name *</label>
            <input type="text" name="session_name" value="{{ $session->session_name }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div class="form-group">
                <label style="display:block;font-weight:600;margin-bottom:8px">Start Date *</label>
                <input type="date" name="start_date" value="{{ $session->start_date->toDateString() }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
            </div>

            <div class="form-group">
                <label style="display:block;font-weight:600;margin-bottom:8px">End Date *</label>
                <input type="date" name="end_date" value="{{ $session->end_date->toDateString() }}" required style="width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:6px;font-size:14px">
            </div>
        </div>

        <div class="form-group" style="margin-bottom:24px">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" name="is_current" value="1" @checked($session->is_current) style="width:16px;height:16px">
                <span style="font-size:14px;font-weight:600">Set this session as Active/Current?</span>
            </label>
        </div>

        <div style="border-top:1px solid #e2e8f0;padding-top:20px;text-align:right">
            <a href="{{ route('admin.academic-sessions.index') }}" class="btn" style="margin-right:8px">Cancel</a>
            <button type="submit" class="btn primary">Update Session</button>
        </div>
    </form>
</div>

@endsection
