@extends('admin.layout')

@section('title', 'Notifications')
@section('subtitle', 'Recent system events')

@section('content')

@php
    $humanize = function ($desc) {
        $parts = explode(' ', $desc, 2);
        $name = preg_replace('/(?<!^)([A-Z])/', ' $1', $parts[0]);
        return trim(ucfirst(strtolower($name)) . ' ' . ($parts[1] ?? ''));
    };
    $map = ['pages'=>'document','posts'=>'pencil','media'=>'photo','enquiries'=>'inbox','job_applications'=>'briefcase','subscribers'=>'envelope','events'=>'calendar','notices'=>'megaphone','users'=>'users','settings'=>'cog','sliders'=>'photo','galleries'=>'collection','testimonials'=>'star','achievements'=>'star','academic_calendar'=>'calendar'];
@endphp

<div class="card">
    <div class="widget__body" style="padding-top:6px;padding-bottom:6px">
        @forelse ($items as $entry)
            <div class="activity-item">
                <div class="activity-dot"><x-admin.icon name="{{ $map[$entry->log_name] ?? 'bell' }}"/></div>
                <div class="min-w-0 flex-1">
                    <div class="text-[13.5px]" style="color:var(--text)">{{ $humanize($entry->description) }}</div>
                    <div class="text-[12px] mt-0.5" style="color:var(--text-muted)">
                        <span class="badge">{{ str_replace('_', ' ', $entry->log_name) }}</span>
                        <span class="ml-1">{{ $entry->created_at?->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty">No notifications yet.</div>
        @endforelse
    </div>
</div>

@endsection
