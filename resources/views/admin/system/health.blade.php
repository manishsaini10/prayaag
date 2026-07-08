@extends('admin.layout')

@section('title', 'System Health')
@section('subtitle', 'Live infrastructure status')

@section('actions')
    <a href="{{ url('/admin/system-health') }}" class="btn"><x-admin.icon name="bolt"/> Refresh</a>
@endsection

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach ($health as $h)
        <div class="card" style="padding:16px;display:flex;align-items:center;gap:14px">
            <span class="status-dot {{ $h['status'] }}" style="width:12px;height:12px"></span>
            <div style="flex:1">
                <div style="font-weight:600;color:var(--text)">{{ $h['label'] }}</div>
                <div class="muted" style="font-size:13px;margin-top:2px">{{ $h['value'] }}</div>
            </div>
            <span class="badge {{ $h['status'] === 'ok' ? 'published' : ($h['status'] === 'warn' ? 'new' : 'archived') }}">
                {{ strtoupper($h['status']) }}
            </span>
        </div>
    @endforeach
</div>

@endsection
