@extends('admin.layout')

@section('title', 'Instagram Settings')
@section('subtitle', 'Enterprise Instagram Feed Configuration')

@section('actions')
<a href="{{ route('admin.instagram.dashboard') }}" class="btn" style="font-size:13px">← Dashboard</a>
@endsection

@section('content')

<style>
    .ig-section{margin-bottom:24px}
    .ig-h{font-size:13px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--text-muted);margin:0 0 12px}
    .ig-field{margin-bottom:14px}
    .ig-field label{display:block;font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:5px}
    .ig-field input{width:100%;padding:10px 14px;font-size:13.5px;background:var(--surface);border:1px solid var(--border);border-radius:8px;color:var(--text);outline:none;transition:border-color .2s}
    .ig-field input:focus{border-color:#0866ff}
    .ig-row{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px}
    .ig-row code{font-size:12px;background:var(--bg-soft);padding:2px 8px;border-radius:4px}
    .ig-log-row{font-size:12.5px;padding:8px 0;border-bottom:1px solid var(--border);color:var(--text-soft);display:flex;justify-content:space-between}
    .ig-log-row:last-child{border:0}
    .ig-badge-ok{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;background:var(--success-soft);color:var(--success);padding:3px 10px;border-radius:5px}
    .ig-badge-miss{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;background:var(--danger-soft);color:var(--danger);padding:3px 10px;border-radius:5px}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ session('status') }}</div>
@endif
@if (isset($errors) && $errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $errors->first() }}</div>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     STEP 1: Enter Meta App Credentials (WordPress Plugin Style — No .env)
════════════════════════════════════════════════════════════════════════════════ --}}
<div class="ig-section">
    <p class="ig-h">🔑 Step 1 — Meta App Credentials (Stored in Database — No .env editing needed)</p>
    <div style="background:var(--surface);border:1.5px solid var(--border);border-radius:14px;padding:22px">

        {{-- Status badges --}}
        <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap">
            <span class="{{ $env['app_id_set'] ? 'ig-badge-ok' : 'ig-badge-miss' }}">
                {{ $env['app_id_set'] ? '✅' : '❌' }} App ID {{ $env['app_id_set'] ? 'Saved: '.$env['app_id'] : 'Not Set' }}
            </span>
            <span class="{{ $env['app_secret_set'] ? 'ig-badge-ok' : 'ig-badge-miss' }}">
                {{ $env['app_secret_set'] ? '✅' : '❌' }} App Secret {{ $env['app_secret_set'] ? 'Saved' : 'Not Set' }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.instagram.settings.save.credentials') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div class="ig-field">
                    <label>Facebook / Meta App ID</label>
                    <input type="text" name="facebook_app_id" placeholder="e.g. 1234567890123456" autocomplete="off"
                           style="font-family:monospace" required>
                    <p style="font-size:11.5px;color:var(--text-muted);margin:4px 0 0">developers.facebook.com → Your App → Settings → Basic → App ID</p>
                </div>
                <div class="ig-field">
                    <label>Facebook / Meta App Secret</label>
                    <input type="password" name="facebook_app_secret" placeholder="Enter App Secret" autocomplete="off"
                           style="font-family:monospace" required>
                    <p style="font-size:11.5px;color:var(--text-muted);margin:4px 0 0">developers.facebook.com → Settings → Basic → App Secret → Show</p>
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:4px;align-items:center;flex-wrap:wrap">
                <button type="submit" class="btn primary" style="font-size:13px;padding:9px 22px">💾 Save Credentials</button>
                @if ($env['app_id_set'] && $env['app_secret_set'])
                    <a href="{{ route('admin.instagram.oauth.connect') }}" class="btn" style="font-size:13px;padding:9px 22px;background:linear-gradient(135deg,#0866ff,#0d47f0);color:#fff;border:none">
                        🔗 Connect Instagram via OAuth Popup
                    </a>
                @endif
                <span style="font-size:12px;color:var(--text-muted)">Credentials are saved directly to the database without modifying your .env file.</span>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     STEP 2: How to get App ID/Secret — Inline Guide
════════════════════════════════════════════════════════════════════════════════ --}}
<div class="ig-section">
    <p class="ig-h">📖 Step 2 — How to Create a Meta Developer App (5-Minute Setup Guide)</p>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px;font-size:13px;line-height:1.7;color:var(--text-soft)">

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">1</div>
            <div>
                <strong style="color:var(--text)">Open Meta Developer Console</strong><br>
                Visit <a href="https://developers.facebook.com/apps" target="_blank" style="color:var(--primary)">developers.facebook.com/apps</a> → Click <strong>Create App</strong> → Select <strong>Business</strong> type → Enter your App Name.
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">2</div>
            <div>
                <strong style="color:var(--text)">Add Instagram Graph API</strong><br>
                In App Dashboard → Click <strong>Add Product</strong> → Choose <strong>Instagram Graph API</strong> → Click Set Up.
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">3</div>
            <div>
                <strong style="color:var(--text)">Configure OAuth Redirect URI</strong><br>
                Under <strong>Instagram Graph API → Settings</strong>, enter the following authorized Redirect URI:<br>
                <code style="word-break:break-all;background:var(--bg-soft);padding:3px 8px;border-radius:4px;font-size:12px">{{ $env['redirect_uri'] }}</code>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">4</div>
            <div>
                <strong style="color:var(--text)">Copy App ID and App Secret</strong><br>
                Navigate to <strong>Settings → Basic</strong> → Copy your <strong>App ID</strong> and click Show to copy your <strong>App Secret</strong>.
            </div>
        </div>

        <div style="display:flex;gap:12px">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">5</div>
            <div>
                <strong style="color:var(--text)">Paste into Step 1 Above</strong><br>
                Paste the App ID and Secret into Step 1 form above and click Save. You can then connect via the official Meta popup!
            </div>
        </div>
    </div>
</div>

{{-- Current Config Status --}}
<div class="ig-section">
    <p class="ig-h">⚙️ Current Configuration Status</p>
    <div style="display:grid;gap:8px">
        <div class="ig-row">
            <span>OAuth Redirect URI</span>
            <span><code>{{ $env['redirect_uri'] }}</code></span>
        </div>
        <div class="ig-row">
            <span>Graph API Version</span>
            <span><code>{{ $env['graph_version'] }}</code></span>
        </div>
        <div class="ig-row">
            <span>Cache Duration</span>
            <span><code>{{ $env['cache_duration'] }}s</code></span>
        </div>
        <div class="ig-row">
            <span>Queue Jobs</span>
            <span><code>{{ $env['enable_queue'] ? 'Enabled' : 'Disabled' }}</code></span>
        </div>
        <div class="ig-row">
            <span>Local Media Cache</span>
            <span><code>{{ $env['enable_cache'] ? 'Enabled' : 'Disabled' }}</code></span>
        </div>
    </div>
</div>

{{-- Sync Logs --}}
<div class="ig-section">
    <p class="ig-h">Sync Activity Logs</p>
    <div class="ig-card" style="padding:12px 16px">
        @forelse ($logs as $log)
            <div class="ig-log-row">
                <span>
                    <span style="display:inline-block;padding:1px 7px;border-radius:4px;font-size:10px;font-weight:600;text-transform:uppercase;margin-right:6px;
                        @if(in_array($log->status, ['connected','success','token_refreshed'])) background:var(--success-soft);color:var(--success)
                        @elseif(in_array($log->status, ['failed','disconnected'])) background:var(--danger-soft);color:var(--danger)
                        @else background:var(--bg-soft);color:var(--text-muted)@endif
                    ">{{ $log->status }}</span>
                    {{ $log->created_at->diffForHumans() }} — {{ $log->message }}
                </span>
                <span style="color:var(--text-muted);font-size:11px">{{ $log->execution_time > 0 ? $log->execution_time . 's' : '' }}</span>
            </div>
        @empty
            <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">No logs yet.</div>
        @endforelse
    </div>
</div>

@endsection
