@extends('admin.layout')

@section('title', 'CMS Updates & Git Deployer')
@section('subtitle', 'Auto-detect installation paths, sync code from Git, and manage version history')

@section('actions')
    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('admin.updates.backup') }}" onsubmit="return confirm('Create a full pre-update backup now?')">
            @csrf
            <button type="submit" class="btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;margin-right:6px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Create Backup
            </button>
        </form>
    </div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="card" style="border-color:#86efac;background:#f0fdf4;color:#166534;padding:14px 18px;font-size:13.5px;font-weight:500;border-radius:12px">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="card" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;padding:14px 18px;font-size:13.5px;font-weight:500;border-radius:12px">
            {{ session('error') }}
        </div>
    @endif

    {{-- Execution Logs Modal / Accordion --}}
    @if(session('deploy_logs'))
        <div class="card" style="background:#0f172a;color:#f8fafc;border:1px solid #334155;padding:18px;border-radius:14px">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold font-mono text-sm" style="color:#4ade80">📟 Deployment Log Output:</span>
                <span class="text-xs font-mono" style="color:#94a3b8">{{ count(session('deploy_logs')) }} steps executed</span>
            </div>
            <pre class="font-mono text-xs overflow-auto max-h-64 p-3 rounded-lg" style="background:#1e293b;color:#e2e8f0;line-height:1.7">@foreach(session('deploy_logs') as $line){{ $line }}
@endforeach</pre>
        </div>
    @endif

    {{-- 🌟 NEW UPDATE AVAILABLE BANNER (If new commit on GitHub) --}}
    @if(!empty($systemInfo['update_available']))
        <div class="card" style="background:linear-gradient(135deg,#fff1f2,var(--surface));border:2px solid #f43f5e;padding:22px;border-radius:16px;box-shadow:0 8px 24px rgba(244,63,94,0.12)">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-start gap-4">
                    <div style="width:48px;height:48px;border-radius:14px;background:#e11d48;display:grid;place-items:center;color:#fff;box-shadow:0 4px 14px rgba(225,29,72,0.4);flex-shrink:0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 style="font-size:16px;font-weight:700;color:#9f1239;margin:0">🆕 New GitHub Release Available!</h3>
                            <span style="font-size:11px;font-weight:700;font-family:monospace;background:#ffe4e6;color:#be123c;padding:2px 8px;border-radius:6px">
                                Commit: {{ $systemInfo['remote_commit']['sha'] ?? 'Latest' }}
                            </span>
                        </div>
                        @if(!empty($systemInfo['remote_commit']['message']))
                            <div style="margin-top:6px;font-size:13px;font-family:monospace;background:rgba(255,255,255,0.85);color:#881337;padding:8px 12px;border-radius:8px;border:1px solid #fecdd3">
                                💬 {{ $systemInfo['remote_commit']['message'] }}
                            </div>
                        @endif
                        <p style="font-size:12px;color:#be123c;margin-top:6px">
                            Pushed {{ $systemInfo['remote_commit']['date'] ?? 'recently' }} by {{ $systemInfo['remote_commit']['author'] ?? 'Developer' }}.
                            The system will <strong>take a full backup first</strong> and then apply the update automatically.
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.updates.git-pull') }}"
                      x-data="{ deploying: false }"
                      @submit="deploying = true; if(!confirm('Create a full backup and apply latest update from GitHub now?')) { deploying = false; return false; }">
                    @csrf
                    <input type="hidden" name="branch" value="main">
                    <button type="submit" class="btn" style="background:#e11d48;color:#fff;border:none;padding:12px 22px;font-size:13.5px;font-weight:700;border-radius:10px;box-shadow:0 4px 14px rgba(225,29,72,0.35);cursor:pointer" x-bind:disabled="deploying">
                        <span x-show="!deploying">⚡ Backup &amp; Apply Update Now</span>
                        <span x-show="deploying" x-cloak>⏳ Creating Backup &amp; Updating…</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- System Status Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card md:col-span-2" style="padding:20px;border-radius:14px">
            <div class="flex items-start gap-4">
                <div style="width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--primary),var(--primary-strong));display:grid;place-items:center;color:#fff;flex-shrink:0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:24px;height:24px"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted)">Installed Release &amp; Build</div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);margin-top:2px">v{{ $currentVersion }}</div>
                    <div style="font-size:12px;font-family:monospace;color:var(--primary);margin-top:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $systemInfo['current_git_rev'] }}">
                        🔖 {{ $systemInfo['current_git_rev'] }}
                    </div>
                </div>
                <span class="badge" style="background:#dcfce7;color:#166534;font-size:12px;font-weight:600;padding:4px 10px;border-radius:8px">
                    ● Active Release
                </span>
            </div>
        </div>

        <div class="card" style="padding:20px;border-radius:14px">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted)">Public Root Structure</div>
            <div style="font-size:14px;font-weight:700;color:var(--text);margin-top:6px">
                @if($systemInfo['is_split_public'])
                    <span style="color:#d97706">⚡ Split (public_html detected)</span>
                @else
                    <span style="color:#059669">🔗 Unified Public Root</span>
                @endif
            </div>
            <div style="font-size:12px;color:var(--text-muted);margin-top:6px">
                <strong>{{ count($backups) }}</strong> restore points stored in backup
            </div>
        </div>
    </div>

    {{-- SECTION 1: 1-Click Git Auto-Sync (Automated Deployment) --}}
    <div class="card" style="border:2px solid var(--primary);padding:24px;border-radius:16px;background:var(--surface)">
        <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
            <div>
                <h3 style="font-size:17px;font-weight:700;color:var(--text);margin:0;display:flex;align-items:center;gap:8px">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;color:var(--primary)"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                    1-Click Git Auto-Deployer
                </h3>
                <p style="font-size:13.5px;color:var(--text-muted);margin:4px 0 0 0">
                    Takes a full backup, pulls latest code from GitHub, auto-syncs assets to <code>public_html</code>, runs migrations, and clears caches.
                </p>
            </div>
            <form method="POST" action="{{ route('admin.updates.git-pull') }}"
                  x-data="{ running: false }"
                  @submit="running = true; if(!confirm('Create full backup and sync latest update from Git now?')) { running = false; return false; }">
                @csrf
                <div class="flex items-center gap-2">
                    <input type="hidden" name="branch" value="main">
                    <button type="submit" class="btn primary" x-bind:disabled="running" style="padding:10px 22px;font-size:13.5px;font-weight:700;border-radius:10px">
                        <span x-show="!running">⚡ Sync with Git Now</span>
                        <span x-show="running" x-cloak>⏳ Backup &amp; Deploying…</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Auto-Detected Environment Specs --}}
        <div style="background:var(--surface-2);border:1px solid var(--border);padding:14px;border-radius:12px;margin-top:16px">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted);margin-bottom:8px">🧠 Auto-Detected Environment Paths:</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 font-mono" style="font-size:11.5px">
                <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    <span style="font-weight:700;color:var(--text-muted)">Core Directory:</span>
                    <span style="color:var(--text)" title="{{ $systemInfo['laravel_root'] }}">{{ $systemInfo['laravel_root'] }}</span>
                </div>
                <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    <span style="font-weight:700;color:var(--text-muted)">Web Root:</span>
                    <span style="color:var(--text)" title="{{ $systemInfo['web_root'] }}">{{ $systemInfo['web_root'] }}</span>
                </div>
                <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    <span style="font-weight:700;color:var(--text-muted)">PHP Binary:</span>
                    <span style="color:var(--text)">{{ $systemInfo['php_binary'] }}</span>
                </div>
                <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    <span style="font-weight:700;color:var(--text-muted)">Git Binary:</span>
                    <span style="color:var(--text)">{{ $systemInfo['git_binary'] }}</span>
                </div>
            </div>
        </div>

        {{-- Automated GitHub Webhook URL Box --}}
        <div style="margin-top:16px;padding-top:14px;border-top:1px dashed var(--border)" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-1.5">
                <span style="font-size:12px;font-weight:700;color:var(--text)">🔗 Automated GitHub Webhook URL (Optional for Zero-Touch Deployment):</span>
                <span x-show="copied" x-cloak style="font-size:12px;color:var(--success);font-weight:700">✓ Copied to clipboard!</span>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" readonly value="{{ $webhookUrl }}" class="flex-1 font-mono outline-none" style="background:var(--surface);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:8px;font-size:12px">
                <button type="button" class="btn secondary" style="padding:8px 14px;font-size:12px"
                        @click="navigator.clipboard.writeText('{{ $webhookUrl }}'); copied = true; setTimeout(() => copied = false, 2500)">
                    Copy Link
                </button>
            </div>
            <p style="font-size:11.5px;color:var(--text-muted);margin-top:6px">
                Add this URL in <strong>GitHub Repo ➔ Settings ➔ Webhooks</strong> so every <code>git push</code> updates your live school website automatically!
            </p>
        </div>
    </div>

    {{-- SECTION 2: Manual ZIP Package Upload (Fallback / Offline) --}}
    <div class="card" style="padding:22px;border-radius:16px">
        <h3 style="font-size:16px;font-weight:700;color:var(--text);margin:0 0 4px 0">Manual ZIP Update Package (Offline Fallback)</h3>
        <p style="font-size:13px;color:var(--text-muted);margin:0 0 16px 0">
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

            @if(!empty($errors) && $errors->has('package'))
                <p class="text-sm mt-2" style="color:var(--danger)">{{ $errors->first('package') }}</p>
            @endif

            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="btn primary" x-bind:disabled="!file">
                    Validate &amp; Continue →
                </button>
                <span class="text-xs" style="color:var(--text-muted)">A full backup is taken automatically before applying.</span>
            </div>
        </form>
    </div>

    {{-- Update History Table --}}
    <div class="card p-0 overflow-hidden" style="border-radius:16px">
        <div class="px-6 py-4" style="border-bottom:1px solid var(--border)">
            <h3 style="font-size:15px;font-weight:700;color:var(--text);margin:0">Update &amp; Deploy History</h3>
        </div>

        @if($history->isEmpty())
            <div class="text-center py-12 text-sm" style="color:var(--text-muted)">
                No previous update logs recorded. Active version is v{{ $currentVersion }}.
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
                                <td class="px-6 py-3 font-bold" style="color:var(--text)">
                                    v{{ $update->version }}
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
                                    <span class="badge text-xs" style="background:{{ $b['bg'] }};color:{{ $b['color'] }};padding:3px 8px;border-radius:6px">
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
                                                  onsubmit="return confirm('Rollback to this backup? Current changes will be reverted.')">
                                                @csrf
                                                <button type="submit" class="text-xs font-bold hover:underline" style="color:var(--danger);background:none;border:none;cursor:pointer">
                                                    ↩ Rollback
                                                </button>
                                            </form>
                                        @endif
                                        @if($update->error_message)
                                            <span class="text-xs" style="color:var(--danger)" title="{{ $update->error_message }}">⚠ Error Details</span>
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
    <div class="card p-0 overflow-hidden" style="border-radius:16px">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border)">
            <h3 style="font-size:15px;font-weight:700;color:var(--text);margin:0">Saved Backups</h3>
            <span class="text-xs font-mono" style="color:var(--text-muted)">storage/backups/updates/</span>
        </div>
        <div class="divide-y" style="border-color:var(--border)">
            @foreach($backups as $backup)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold font-mono" style="color:var(--text)">📦 {{ $backup['name'] }}</p>
                        <p class="text-xs" style="color:var(--text-muted)">{{ $backup['size'] }} · {{ $backup['modified'] }}</p>
                    </div>
                    <span class="badge text-xs" style="background:#dcfce7;color:#166534;padding:3px 8px;border-radius:6px">Verified</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
