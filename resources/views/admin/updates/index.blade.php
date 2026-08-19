@extends('admin.layout')

@section('title', 'CMS Updates & Transactional Deployer')
@section('subtitle', 'Fail-safe deployment pipeline with pre-update backups, multi-tier health checks, and automated rollbacks')

@section('actions')
    <div class="flex items-center gap-2">
        <button type="button" class="btn secondary" onclick="runLiveHealthCheck()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;margin-right:6px"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
            Live Health Check
        </button>
        <form method="POST" action="{{ route('admin.updates.backup') }}" onsubmit="return confirm('Create a full manual backup snapshot now?')">
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
        <div class="card" style="border-color:#86efac;background:#f0fdf4;color:#166534;padding:16px 20px;font-size:13.5px;font-weight:600;border-radius:12px;box-shadow:0 4px 12px rgba(22,101,52,0.08)">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="card" style="border-color:#fca5a5;background:#fef2f2;color:#991b1b;padding:16px 20px;font-size:13.5px;font-weight:600;border-radius:12px;box-shadow:0 4px 12px rgba(153,27,27,0.08);white-space:pre-line">
            {{ session('error') }}
        </div>
    @endif

    {{-- Health Check Results Breakdown (if present in session) --}}
    @if(session('health_result'))
        @php($hr = session('health_result'))
        <div class="card" style="border:1px solid #86efac;background:#f0fdf4;padding:18px;border-radius:14px">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-sm" style="color:#166534">🩺 Multi-Tier Post-Deployment Health Check Results:</span>
                <span class="badge" style="background:#22c55e;color:#fff;font-weight:700;padding:3px 10px;border-radius:8px">STATUS: HEALTHY</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 text-center text-xs font-mono">
                <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #bbf7d0">
                    <div style="color:#166534;font-weight:700">Backend Boot</div>
                    <div style="color:#22c55e">✔ {{ strtoupper($hr['checks']['backend'] ?? 'PASSED') }}</div>
                </div>
                <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #bbf7d0">
                    <div style="color:#166534;font-weight:700">Database</div>
                    <div style="color:#22c55e">✔ {{ strtoupper($hr['checks']['database'] ?? 'PASSED') }}</div>
                </div>
                <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #bbf7d0">
                    <div style="color:#166534;font-weight:700">Vite Assets</div>
                    <div style="color:#22c55e">✔ {{ strtoupper($hr['checks']['assets'] ?? 'PASSED') }}</div>
                </div>
                <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #bbf7d0">
                    <div style="color:#166534;font-weight:700">Storage Wr.</div>
                    <div style="color:#22c55e">✔ {{ strtoupper($hr['checks']['storage'] ?? 'PASSED') }}</div>
                </div>
                <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #bbf7d0">
                    <div style="color:#166534;font-weight:700">Cache Engine</div>
                    <div style="color:#22c55e">✔ {{ strtoupper($hr['checks']['cache'] ?? 'PASSED') }}</div>
                </div>
                <div style="background:#fff;padding:8px;border-radius:8px;border:1px solid #bbf7d0">
                    <div style="color:#166534;font-weight:700">Frontend HTTP</div>
                    <div style="color:#22c55e">✔ {{ strtoupper($hr['checks']['frontend'] ?? 'PASSED') }}</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Execution Logs Terminal --}}
    @if(session('deploy_logs'))
        <div class="card" style="background:#0f172a;color:#f8fafc;border:1px solid #334155;padding:18px;border-radius:14px">
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold font-mono text-sm" style="color:#4ade80">📟 Transactional Deployment Execution Log:</span>
                <span class="text-xs font-mono" style="color:#94a3b8">{{ count(session('deploy_logs')) }} steps executed</span>
            </div>
            <pre class="font-mono text-xs overflow-auto max-h-64 p-3 rounded-lg" style="background:#1e293b;color:#e2e8f0;line-height:1.7">@foreach(session('deploy_logs') as $line){{ $line }}
@endforeach</pre>
        </div>
    @endif

    {{-- Live Health Modal / Result Container --}}
    <div id="liveHealthContainer" class="card" style="display:none;padding:18px;border-radius:14px;border:1px solid var(--border)">
        <div class="flex items-center justify-between mb-3">
            <h4 style="margin:0;font-size:14px;font-weight:700">🩺 Live Production Health Suite</h4>
            <button type="button" onclick="document.getElementById('liveHealthContainer').style.display='none'" style="background:none;border:none;cursor:pointer;color:var(--text-muted)">✕</button>
        </div>
        <div id="liveHealthBody" class="text-xs font-mono">Running health checks...</div>
    </div>

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
                            <strong>Safety Guarantee:</strong> A verified restore point is created first. If any health check fails, the previous version is restored automatically.
                        </p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.updates.git-pull') }}"
                      x-data="{ deploying: false }"
                      @submit="deploying = true; if(!confirm('Apply protected transactional update from GitHub now?')) { deploying = false; return false; }">
                    @csrf
                    <input type="hidden" name="branch" value="main">
                    <button type="submit" class="btn" style="background:#e11d48;color:#fff;border:none;padding:12px 22px;font-size:13.5px;font-weight:700;border-radius:10px;box-shadow:0 4px 14px rgba(225,29,72,0.35);cursor:pointer" x-bind:disabled="deploying">
                        <span x-show="!deploying">⚡ Backup &amp; Apply Update Now ({{ $systemInfo['remote_commit']['version'] ?? ($systemInfo['remote_commit']['sha'] ?? 'New') }})</span>
                        <span x-show="deploying" x-cloak>⏳ Creating Backup, Updating &amp; Verifying Health…</span>
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4 pt-4" style="border-top:1px solid var(--border)">
                <div>
                    <span style="font-size:11px;color:var(--text-muted)">PHP Version:</span>
                    <div style="font-size:12.5px;font-weight:700;font-family:monospace;color:var(--text)">PHP {{ PHP_VERSION }}</div>
                </div>
                <div>
                    <span style="font-size:11px;color:var(--text-muted)">Laravel Version:</span>
                    <div style="font-size:12.5px;font-weight:700;font-family:monospace;color:var(--text)">{{ app()->version() }}</div>
                </div>
                <div>
                    <span style="font-size:11px;color:var(--text-muted)">Environment:</span>
                    <div style="font-size:12.5px;font-weight:700;color:var(--text)">{{ app()->environment() }}</div>
                </div>
                <div>
                    <span style="font-size:11px;color:var(--text-muted)">Concurrency Lock:</span>
                    <div style="font-size:12.5px;font-weight:700;color:{{ $systemInfo['is_locked'] ? '#e11d48' : '#16a34a' }}">
                        {{ $systemInfo['is_locked'] ? '🔒 Locked (Running)' : '🔓 Free' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="padding:20px;border-radius:14px;display:flex;flex-direction:column;justify-content:space-between">
            <div>
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--text-muted)">1-Click Git Auto-Sync</div>
                <div style="font-size:13px;color:var(--text-muted);margin-top:4px">
                    Pull latest commit from <code>origin/main</code> with automatic backup, database migrations, and health verification.
                </div>
            </div>
            <form method="POST" action="{{ route('admin.updates.git-pull') }}"
                  x-data="{ syncing: false }"
                  @submit="syncing = true; if(!confirm('Execute transactional deployment from origin/main now?')) { syncing = false; return false; }"
                  class="mt-4">
                @csrf
                <input type="hidden" name="branch" value="main">
                <button type="submit" class="btn primary w-full justify-center" style="font-weight:700;border-radius:10px" x-bind:disabled="syncing">
                    <span x-show="!syncing">⚡ Backup &amp; Apply Update Now @if(!empty($systemInfo['update_available'])) ({{ $systemInfo['remote_commit']['version'] ?? 'New' }}) @endif</span>
                    <span x-show="syncing" x-cloak>⏳ Deploying &amp; Verifying…</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Deployment History Table --}}
    <div class="card" style="padding:20px;border-radius:14px">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:var(--text);margin:0">📜 Deployment History &amp; Restore Points</h3>
                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0">Complete transactional history with commit hashes, duration, and 1-click rollback.</p>
            </div>
        </div>

        @if($history->isEmpty())
            <div class="text-center py-8 text-sm" style="color:var(--text-muted)">
                No previous update deployments recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table w-full text-xs">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);color:var(--text-muted)">
                            <th class="py-2.5 text-left font-bold">DEPLOYMENT ID</th>
                            <th class="py-2.5 text-left font-bold">VERSION / COMMITS</th>
                            <th class="py-2.5 text-left font-bold">STATUS &amp; STAGE</th>
                            <th class="py-2.5 text-left font-bold">DURATION</th>
                            <th class="py-2.5 text-left font-bold">APPLIED BY</th>
                            <th class="py-2.5 text-left font-bold">TIMESTAMP</th>
                            <th class="py-2.5 text-right font-bold">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $item)
                            <tr style="border-bottom:1px solid var(--border)">
                                <td class="py-3 font-mono font-bold" style="color:var(--primary)">
                                    {{ $item->deployment_id ?? 'DEPLOY-' . $item->id }}
                                </td>
                                <td class="py-3">
                                    <div class="font-bold">v{{ $item->version }}</div>
                                    @if(!empty($item->previous_commit) || !empty($item->new_commit))
                                        <div class="font-mono text-muted" style="font-size:11px">
                                            {{ $item->previous_commit ? substr($item->previous_commit,0,7) : 'prev' }} ➔ {{ $item->new_commit ? substr($item->new_commit,0,7) : 'curr' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($item->status === 'success')
                                        <span class="badge" style="background:#dcfce7;color:#166534;font-weight:700">✔ Success</span>
                                    @elseif($item->status === 'rolled_back')
                                        <span class="badge" style="background:#fef3c7;color:#92400e;font-weight:700">↩ Rolled Back</span>
                                    @else
                                        <span class="badge" style="background:#fee2e2;color:#991b1b;font-weight:700">✖ {{ ucfirst($item->status) }}</span>
                                    @endif
                                    <span style="font-size:10px;color:var(--text-muted);display:block;margin-top:2px">[{{ $item->stage ?? 'finished' }}]</span>
                                </td>
                                <td class="py-3 font-mono">
                                    {{ $item->duration ? $item->duration . 's' : '—' }}
                                </td>
                                <td class="py-3">{{ $item->applied_by ?? 'System' }}</td>
                                <td class="py-3 font-mono text-muted">{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') : '—' }}</td>
                                <td class="py-3 text-right">
                                    @if($item->backup_path && file_exists($item->backup_path))
                                        <form method="POST" action="{{ route('admin.updates.rollback', $item->id) }}" onsubmit="return confirm('Are you sure you want to rollback to this restore point? Database and files will be restored.')">
                                            @csrf
                                            <button type="submit" class="btn secondary" style="font-size:11px;padding:3px 8px;border-radius:6px">
                                                ↩ Rollback
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size:11px">Snapshot archived</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<script>
function runLiveHealthCheck() {
    const container = document.getElementById('liveHealthContainer');
    const body = document.getElementById('liveHealthBody');
    container.style.display = 'block';
    body.innerHTML = '⏳ Executing 6-tier health verification suite...';

    fetch('{{ route("admin.updates.health-check") }}')
        .then(res => res.json())
        .then(data => {
            const isHealthy = data.status === 'healthy';
            let html = `<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                <strong style="color:${isHealthy ? '#16a34a' : '#dc2626'}">${isHealthy ? '✅ OVERALL STATUS: HEALTHY' : '❌ OVERALL STATUS: UNHEALTHY'}</strong>
                <span style="color:var(--text-muted)">Checked at: ${data.checked_at}</span>
            </div>`;

            html += '<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(130px, 1fr));gap:8px;margin-top:10px">';
            for (const [key, val] of Object.entries(data.checks || {})) {
                const passed = val === 'passed';
                html += `<div style="background:var(--surface-muted);padding:8px;border-radius:6px;border:1px solid ${passed ? '#bbf7d0' : '#fecdd3'}">
                    <div style="font-weight:700;text-transform:capitalize">${key}</div>
                    <div style="color:${passed ? '#16a34a' : '#dc2626'}">${passed ? '✔ PASSED' : '✖ FAILED'}</div>
                </div>`;
            }
            html += '</div>';

            if (data.errors && Object.keys(data.errors).length > 0) {
                html += '<div style="margin-top:10px;padding:8px;background:#fef2f2;border:1px solid #fecdd3;border-radius:6px;color:#991b1b">';
                html += '<strong>Detected Issues:</strong><ul style="margin:4px 0 0 16px;padding:0">';
                for (const [k, err] of Object.entries(data.errors)) {
                    html += `<li><strong>${k}:</strong> ${err}</li>`;
                }
                html += '</ul></div>';
            }

            body.innerHTML = html;
        })
        .catch(err => {
            body.innerHTML = `<span style="color:#dc2626">❌ Failed to execute health checks: ${err.message}</span>`;
        });
}
</script>
@endsection
