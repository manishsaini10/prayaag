@extends('admin.layout')

@section('title', 'New Mess Menu')
@section('subtitle', 'Initialize a new weekly menu structure.')

@section('actions')
    <a href="{{ route('admin.mess-menus.index') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to List</a>
@endsection

@section('content')
<style>
    .frm{max-width:540px}
    .frm-field{margin-bottom:18px}
    .frm-label{display:block;font-size:13px;font-weight:600;color:var(--text,#e2e8f0);margin-bottom:6px}
    .frm-input{width:100%;padding:9px 12px;border:1px solid var(--border,#2a2f3e);border-radius:10px;background:var(--card,#1a1f2e);color:var(--text,#e2e8f0);font:inherit;font-size:14px}
    .frm-input:focus{outline:none;border-color:#6366f1;}
    .frm-err{color:var(--danger,#ef4444);font-size:12.5px;margin-top:5px}
    .frm-actions{display:flex;gap:10px;padding-top:6px;border-top:1px solid var(--border,#2a2f3e);margin-top:4px}
</style>

@if ($errors->any())
    <div class="card" style="border-color:var(--danger,#ef4444);background:rgba(239,68,68,.1);color:var(--danger,#ef4444);padding:12px 16px;margin-bottom:16px;font-size:13.5px">
        Please resolve the errors highlighted below.
    </div>
@endif

<div class="card frm" style="padding:22px">
    <form method="POST" action="{{ route('admin.mess-menus.store') }}">
        @csrf

        <div class="frm-field">
            <label class="frm-label">Menu Title</label>
            <input type="text" name="title" class="frm-input" placeholder="e.g. Weekly Mess Menu - July Week 4" value="{{ old('title', 'Weekly Mess Menu') }}" required>
            @error('title')<div class="frm-err">{{ $message }}</div>@enderror
        </div>

        <div class="frm-field">
            <label class="frm-label">Effective From Date</label>
            <input type="date" name="effective_from" class="frm-input" value="{{ old('effective_from') }}" required>
            @error('effective_from')<div class="frm-err">{{ $message }}</div>@enderror
        </div>

        <div class="frm-field">
            <label class="frm-label">Effective To Date (Optional)</label>
            <input type="date" name="effective_to" class="frm-input" value="{{ old('effective_to') }}">
            @error('effective_to')<div class="frm-err">{{ $message }}</div>@enderror
        </div>

        <div class="frm-actions">
            <button type="submit" class="btn-primary" style="padding: 10px 18px; border-radius: 8px; border:none; font-weight:600; cursor:pointer;">Create & Edit Meals</button>
            <a href="{{ route('admin.mess-menus.index') }}" class="btn" style="padding: 10px 14px;">Cancel</a>
        </div>
    </form>
</div>
@endsection
