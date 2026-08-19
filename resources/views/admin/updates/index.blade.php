@extends('admin.layout')

@section('title', 'CMS Updates & Git Deployer')
@section('subtitle', 'Auto-detect installation paths, sync code from Git, and manage version history')

@section('actions')
    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('admin.updates.backup') }}" onsubmit="return confirm('Create a full pre-update backup now?')">
            @csrf
            <button type="submit" class="btn-secondary inline-flex items-center gap-1.5">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Create Backup
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="px-4 py-3 rounded-xl text-sm font-medium" style="background:#fee2e2;color:#991b1b;border:1px solid #fecaca">
            {{ session('error') }}
        </div>
    @endif

    {{-- Deploy Execution Logs (if just run) --}}
    @if(session('deploy_logs'))
        <div class="card p-5" style="background:#0f172a;color:#f8fafc;border:1px solid #334155">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-emerald-400 font-mono">📟 Auto-Deployment Execution Output:</h4>
                <span class="text-xs text-slate-400 font-mono">{{ count(session('deploy_logs')) }} lines</span>
            </div>
            <pre class="text-xs font-mono overflow-auto max-h-60 space-y-1 p-2 rounded" style="background:#1e293b;line-height:1.6">@foreach(session('deploy_logs') as $line){{ $line }}
@endforeach</pre>
        </div>
    @endif

    {{-- NEW UPDATE AVAILABLE ALERT BANNER (If new commit on GitHub) --}}
    @if(!empty($systemInfo['update_available']))
        <div class="card p-6" style="background:linear-gradient(135deg,#fff1f2,#fef2f2);border:2px solid #f43f5e;box-shadow:0 8px 24px rgba(244,63,94,.15)">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shrink-0" style="background:#e11d48;box-shadow:0 0 16px rgba(225,29,72,.5)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-rose-900">🆕 New GitHub Release Available!</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-bold bg-rose-200 text-rose-800">
                                Commit: {{ $systemInfo['remote_commit']['sha'] ?? 'Latest' }}
                            </span>
                        </div>
                        @if(!empty($systemInfo['remote_commit']['message']))
                            <p class="text-sm font-medium text-rose-900 mt-1 font-mono bg-white/80 px-3 py-1.5 rounded-lg border border-rose-200">
                                💬 {{ $systemInfo['remote_commit']['message'] }}
                            </p>
                        @endif
                        <p class="text-xs text-rose-700 mt-1.5">
                            Pushed {{ $systemInfo['remote_commit']['date'] ?? 'recently' }} by {{ $systemInfo['remote_commit']['author'] ?? 'Developer' }}.
                            Clicking the button will <strong>take a full backup first</strong> and then install the update automatically.
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.updates.git-pull') }}"
                      x-data="{ deploying: false }"
                      @submit="deploying = true; if(!confirm('Create a full backup and apply latest update from GitHub now?')) { deploying = false; return false; }">
                    @csrf
                    <input type="hidden" name="branch" value="main">
                    <button type="submit" class="flex items-center gap-2 px-5 py-3 rounded-xl font-bold text-white transition transform hover:scale-105"
                            x-bind:disabled="deploying"
                            style="background:linear-gradient(135deg,#e11d48,#be123c);box-shadow:0 4px 14px rgba(225,29,72,.4)">
                        <span x-show="!deploying">⚡ Backup &amp; Apply Update Now</span>
                        <span x-show="deploying" x-cloak>⏳ Creating Backup &amp; Updating…</span>
                    </button>
                </form>
            </div>
        </div>
    @endif


    {{-- System Auto-Detection & Git Status Banner --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-5 md:col-span-2">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:linear-gradient(135deg,var(--primary),var(--primary-strong))">
                    <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="width:22px;height:22px"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wider mb-1" style="color:var(--text-muted)">CMS Release &amp; Git Version</div>
                    <div class="text-2xl font-bold" style="color:var(--text)">v{{ $currentVersion }}</div>
                    <div class="text-xs font-mono truncate mt-1" style="color:var(--primary-strong)" title="{{ $systemInfo['current_git_rev'] }}">
                        🔖 Commit: {{ $systemInfo['current_git_rev'] }}
                    </div>
                </div>
                <span class="badge" style="background:#dcfce7;color:#166534;font-size:12px;padding:4px 10px">✅ Auto-Detected</span>
            </div>
        </div>

        <div class="card p-5">
            <div class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:var(--text-muted)">Public Root Structure</div>
            <div class="text-sm font-bold" style="color:var(--text)">
                @if($systemInfo['is_split_public'])
                    <span class="text-amber-600">⚡ Split (public_html detected)</span>
                @else
                    <span class="text-emerald-600">🔗 Standard (Unified public)</span>
                @endif
            </div>
            <div class="text-xs mt-1" style="color:var(--text-muted)">{{ count($backups) }} saved restore points</div>
        </div>
    </div>

    {{-- SECTION 1: 1-Click Git Auto-Sync (Automated Deployment) --}}
    <div class="card p-6" style="border: 2px solid var(--primary);box-shadow: 0 4px 12px rgba(var(--primary-rgb, 14, 116, 144), 0.08)">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
            <div>
                <h3 class="text-lg font-bold flex items-center gap-2" style="color:var(--text)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:var(--primary)"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    1-Click Git Auto-Deployer
                </h3>
                <p class="text-sm mt-0.5" style="color:var(--text-muted)">
                    Pulls latest code from GitHub, auto-syncs assets to <code>public_html</code>, runs migrations, and flushes caches.
                </p>
            </div>
            <form method="POST" action="{{ route('admin.updates.git-pull') }}"
                  x-data="{ running: false }"
                  @submit="running = true; if(!confirm('Start automated Git pull and asset sync now?')) { running = false; return false; }">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="hidden" name="branch" value="main">
                    <button type="submit" class="btn-primary flex items-center gap-2" x-bind:disabled="running" style="padding:10px 20px;font-size:14px">
                        <span x-show="!running">⚡ Sync with Git Now</span>
                        <span x-show="running" x-cloak>⏳ Deploying… Please wait…</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Auto-Detected Environment Specs --}}
        <div class="rounded-xl p-4 mt-4" style="background:var(--surface-2);border:1px solid var(--border)">
            <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:var(--text-muted)">🧠 Auto-Detected Environment Paths:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-xs font-mono">
                <div class="truncate">
                    <span class="font-bold text-slate-500">Core Directory:</span>
                    <span class="text-slate-800 dark:text-slate-200" title="{{ $systemInfo['laravel_root'] }}">{{ $systemInfo['laravel_root'] }}</span>
                </div>
                <div class="truncate">
                    <span class="font-bold text-slate-500">Web Root (Public):</span>
                    <span class="text-slate-800 dark:text-slate-200" title="{{ $systemInfo['web_root'] }}">{{ $systemInfo['web_root'] }}</span>
                </div>
                <div class="truncate">
                    <span class="font-bold text-slate-500">PHP Binary:</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $systemInfo['php_binary'] }}</span>
                </div>
                <div class="truncate">
                    <span class="font-bold text-slate-500">Git Binary:</span>
                    <span class="text-slate-800 dark:text-slate-200">{{ $systemInfo['git_binary'] }}</span>
                </div>
            </div>
        </div>

        {{-- Automated GitHub Webhook URL Box --}}
        <div class="mt-4 pt-4" style="border-top:1px dashed var(--border)" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-bold" style="color:var(--text)">🔗 Automated GitHub Webhook URL (Optional for Zero-Touch Deployment):</span>
                <span x-show="copied" x-cloak class="text-xs text-emerald-600 font-bold">✓ Copied to clipboard!</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ $webhookUrl }}" class="flex-1 px-3 py-1.5 text-xs font-mono rounded-lg outline-none" style="background:var(--surface);border:1px solid var(--border);color:var(--text-muted)">
                <button type="button" class="btn-secondary text-xs px-3 py-1.5"
                        @click="navigator.clipboard.writeText('{{ $webhookUrl }}'); copied = true; setTimeout(() => copied = false, 2500)">
                    Copy Link
                </button>
            </div>
            <p class="text-[11px] mt-1" style="color:var(--text-muted)">
                Add this URL in <strong>GitHub Repo ➔ Settings ➔ Webhooks</strong> so every <code>git push</code> updates your live school website automatically!
            </p>
        </div>
    </div>

    {{-- SECTION 2: Manual ZIP Package Upload (Fallback / Offline) --}}
    <div class="card p-6">
        <h3 class="text-base font-bold mb-1" style="color:var(--text)">Manual ZIP Update Package (Offline Fallback)</h3>
        <p class="text-sm mb-5" style="color:var(--text-muted)">
            Agar internet nahi hai, toh offline banayi hui <code>.zip</code> package yahan upload kar sakte hain.
        </p>

        <form method="POST" action="{{ route('admin.updates.upload') }}" enctype="multipart/form-data"
              x-data="{ file: null, dragging: false }"
              @dragover.prevent="dragging=true" @dragleave="dragging=false"
              @drop.prevent="dragging=false; file=$event.dataTransfer.files[0]; $refs.fileInput.files=$event.dataTransfer.files">
            @csrf

            {{-- Drop zone --}}
            <label
                class="block border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors"
                :style="dragging ? 'border-color:var(--primary);background:var(--surface-2)' : 'border-color:var(--border)'"
                @click="$refs.fileInput.click()">

                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                     style="width:36px;height:36px;margin:0 auto 12px;color:var(--text-muted)">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>

                <template x-if="!file">
                    <div>
                        <p class="font-semibold text-sm" style="color:var(--text)">Drag & drop update package (.zip) here</p>
                        <p class="text-xs mt-1" style="color:var(--text-muted)">or click to browse — accepts <strong>.zip</strong> files only (max 50 MB)</p>
                    </div>
                </template>
                <template x-if="file">
                    <div>
                        <p class="font-semibold text-sm" style="color:var(--primary)" x-text="'📦 ' + file.name"></p>
                        <p class="text-xs mt-1" style="color:var(--text-muted)" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                    </div>
                </template>

                <input type="file" name="package" accept=".zip" x-ref="fileInput" class="hidden"
                       @change="file=$event.target.files[0]">
            </label>

            @error('package')
                <p class="text-sm mt-2" style="color:var(--danger)">{{ $message }}</p>
            @enderror

            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="btn-primary" x-bind:disabled="!file">
                    Validate &amp; Continue →
                </button>
                <span class="text-xs" style="color:var(--text-muted)">A full backup is taken automatically before applying.</span>
            </div>
        </form>
    </div>

    {{-- Update History Table --}}
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--border)">
            <h3 class="text-base font-bold" style="color:var(--text)">Update &amp; Deploy History</h3>
        </div>

        @if($history->isEmpty())
            <div class="text-center py-12 text-sm" style="color:var(--text-muted)">
                No manual ZIP updates recorded. Active version is v{{ $currentVersion }}.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border)">
                            <th class="text-left px-6 py-3 font-semibold text-xs uppercase tracking-wider" style="color:var(--text-muted)">Version</th>
                            <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="color:var(--text-muted)">From</th>
                            <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="color:var(--text-muted)">Status</th>
                            <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="color:var(--text-muted)">Applied By</th>
                            <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="color:var(--text-muted)">Date</th>
                            <th class="text-left px-4 py-3 font-semibold text-xs uppercase tracking-wider" style="color:var(--text-muted)">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $update)
                            <tr style="border-bottom:1px solid var(--border)">
                                <td class="px-6 py-3">
                                    <span class="font-bold" style="color:var(--text)">v{{ $update->version }}</span>
                                </td>
                                <td class="px-4 py-3" style="color:var(--text-muted)">
                                    v{{ $update->previous_version ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badges = [
                                            'success'      => ['bg' => '#dcfce7', 'color' => '#166534', 'label' => '✅ Success'],
                                            'failed'       => ['bg' => '#fee2e2', 'color' => '#991b1b', 'label' => '❌ Failed'],
                                            'rolled_back'  => ['bg' => '#fef3c7', 'color' => '#92400e', 'label' => '↩ Rolled Back'],
                                            'applying'     => ['bg' => '#dbeafe', 'color' => '#1e40af', 'label' => '⏳ Applying'],
                                            'pending'      => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'label' => '⏸ Pending'],
                                        ];
                                        $b = $badges[$update->status] ?? $badges['pending'];
                                    @endphp
                                    <span class="badge text-xs" style="background:{{ $b['bg'] }};color:{{ $b['color'] }}">
                                        {{ $b['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm" style="color:var(--text-muted)">
                                    {{ $update->applied_by ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm" style="color:var(--text-muted)">
                                    {{ $update->applied_at ? \Carbon\Carbon::parse($update->applied_at)->format('d M Y, h:i A') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($update->status === 'success' && $update->backup_path && file_exists($update->backup_path))
                                            <form method="POST" action="{{ route('admin.updates.rollback', $update->id) }}"
                                                  onsubmit="return confirm('Rollback to v{{ $update->previous_version }}? Current changes will be reverted.')">
                                                @csrf
                                                <button type="submit" class="text-xs font-medium hover:underline" style="color:var(--danger);background:none;border:none;cursor:pointer">
                                                    Rollback
                                                </button>
                                            </form>
                                        @endif
                                        @if($update->error_message)
                                            <span class="text-xs" style="color:var(--danger)" title="{{ $update->error_message }}">⚠ Error</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Saved Backups Section --}}
    @if(count($backups) > 0)
    <div class="card p-0 overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border)">
            <h3 class="text-base font-bold" style="color:var(--text)">Saved Backups</h3>
            <span class="text-xs" style="color:var(--text-muted)">Stored in storage/backups/updates/</span>
        </div>
        <div class="divide-y" style="border-color:var(--border)">
            @foreach($backups as $backup)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium" style="color:var(--text)">{{ $backup['name'] }}</p>
                        <p class="text-xs" style="color:var(--text-muted)">{{ $backup['size'] }} · {{ $backup['modified'] }}</p>
                    </div>
                    <span class="badge text-xs" style="background:#dcfce7;color:#166534">Available</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
