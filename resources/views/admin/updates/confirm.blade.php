@extends('admin.layout')

@section('title', 'Confirm Update')
@section('subtitle', 'Review the update details before applying')

@section('actions')
    <a href="{{ route('admin.updates.index') }}" class="btn-secondary inline-flex items-center gap-1.5">
        ← Back to Updates
    </a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Warning Banner --}}
    <div class="rounded-xl p-4 flex gap-3" style="background:#fef3c7;border:1px solid #fde68a">
        <svg viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" style="width:20px;height:20px;shrink-0;margin-top:1px">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
            <p class="text-sm font-bold" style="color:#92400e">Before You Continue</p>
            <p class="text-sm mt-0.5" style="color:#92400e">
                A backup will be created automatically before any changes are made.
                The update process cannot be interrupted once started.
            </p>
        </div>
    </div>

    {{-- Update Details --}}
    <div class="card p-6">
        <h3 class="text-base font-bold mb-4" style="color:var(--text)">Update Details</h3>

        <div class="space-y-4">
            <div class="flex items-center justify-between py-3" style="border-bottom:1px solid var(--border)">
                <span class="text-sm font-medium" style="color:var(--text-muted)">Current Version</span>
                <span class="font-bold text-sm" style="color:var(--text)">v{{ $currentVersion }}</span>
            </div>
            <div class="flex items-center justify-between py-3" style="border-bottom:1px solid var(--border)">
                <span class="text-sm font-medium" style="color:var(--text-muted)">New Version</span>
                <span class="font-bold text-sm" style="color:var(--primary)">v{{ $manifest['version'] }}</span>
            </div>
            @if(!empty($manifest['requires']))
            <div class="flex items-center justify-between py-3" style="border-bottom:1px solid var(--border)">
                <span class="text-sm font-medium" style="color:var(--text-muted)">Requires</span>
                <span class="text-sm" style="color:var(--text)">v{{ $manifest['requires'] }}+</span>
            </div>
            @endif
            <div class="flex items-center justify-between py-3" style="border-bottom:1px solid var(--border)">
                <span class="text-sm font-medium" style="color:var(--text-muted)">Database Migrations</span>
                @if(!empty($manifest['has_migrations']))
                    <span class="badge text-xs" style="background:#fef3c7;color:#92400e">⚠ Yes — migrations will run</span>
                @else
                    <span class="badge text-xs" style="background:#f3f4f6;color:#6b7280">No</span>
                @endif
            </div>
        </div>

        {{-- Changelog --}}
        @if(!empty($manifest['changelog']))
        <div class="mt-5">
            <p class="text-sm font-bold mb-2" style="color:var(--text)">What's New</p>
            <div class="rounded-xl p-4 text-sm" style="background:var(--surface-2);color:var(--text);white-space:pre-line;line-height:1.8">{{ $manifest['changelog'] }}</div>
        </div>
        @endif
    </div>

    {{-- Update Process Steps --}}
    <div class="card p-5">
        <p class="text-sm font-bold mb-3" style="color:var(--text)">Update Process — What will happen:</p>
        <ol class="space-y-2 text-sm" style="color:var(--text-muted)">
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 rounded-full text-xs flex items-center justify-center shrink-0 font-bold" style="background:var(--primary);color:#fff;margin-top:1px">1</span>
                <span>Create a full backup of all app files (stored safely in storage/backups/)</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 rounded-full text-xs flex items-center justify-center shrink-0 font-bold" style="background:var(--primary);color:#fff;margin-top:1px">2</span>
                <span>Extract and apply changed files from the update package</span>
            </li>
            @if(!empty($manifest['has_migrations']))
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 rounded-full text-xs flex items-center justify-center shrink-0 font-bold" style="background:var(--primary);color:#fff;margin-top:1px">3</span>
                <span>Run new database migrations</span>
            </li>
            @endif
            <li class="flex items-start gap-2">
                <span class="w-5 h-5 rounded-full text-xs flex items-center justify-center shrink-0 font-bold" style="background:var(--primary);color:#fff;margin-top:1px">{{ !empty($manifest['has_migrations']) ? 4 : 3 }}</span>
                <span>Clear all caches and mark update as complete</span>
            </li>
        </ol>
    </div>

    {{-- Confirm Button --}}
    <form method="POST" action="{{ route('admin.updates.apply') }}"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary flex items-center gap-2" x-bind:disabled="loading">
                <span x-show="!loading">⚡ Apply Update Now</span>
                <span x-show="loading" x-cloak>⏳ Applying… Please wait…</span>
            </button>
            <a href="{{ route('admin.updates.index') }}" class="btn-secondary">Cancel</a>
        </div>
        <p class="text-xs mt-3" style="color:var(--text-muted)">
            If anything goes wrong, you can rollback from the Update History table.
        </p>
    </form>

</div>
@endsection
