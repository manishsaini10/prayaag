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
    .ig-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:12px}
    .ig-field{margin-bottom:14px}
    .ig-field label{display:block;font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:4px}
    .ig-row{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--surface);border:1px solid var(--border);border-radius:10px;font-size:13px}
    .ig-row code{font-size:12px;background:var(--bg-soft);padding:2px 8px;border-radius:4px}
    .ig-log-row{font-size:12.5px;padding:8px 0;border-bottom:1px solid var(--border);color:var(--text-soft);display:flex;justify-content:space-between}
    .ig-log-row:last-child{border:0}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $errors->first() }}</div>
@endif

{{-- Environment Configuration --}}
<div class="ig-section">
    <p class="ig-h">Environment Configuration</p>
    <div style="display:grid;gap:8px">
        <div class="ig-row">
            <span>App ID</span>
            <span><code>{{ $env['app_id'] ?: 'Not configured' }}</code></span>
        </div>
        <div class="ig-row">
            <span>App Secret</span>
            <span><code>{{ $env['app_secret'] ?: 'Not configured' }}</code></span>
        </div>
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
            <span>Sync Interval</span>
            <span><code>{{ $env['sync_interval'] }} min</code></span>
        </div>
        <div class="ig-row">
            <span>Queue Jobs</span>
            <span><code>{{ $env['enable_queue'] ? 'Enabled' : 'Disabled' }}</code></span>
        </div>
        <div class="ig-row">
            <span>Local Media Cache</span>
            <span><code>{{ $env['enable_cache'] ? 'Enabled' : 'Disabled' }}</code></span>
        </div>
        <div class="ig-row">
            <span>WebP Conversion</span>
            <span><code>{{ $env['enable_webp'] ? 'Enabled' : 'Disabled' }}</code></span>
        </div>
    </div>
</div>

{{-- Setup Guide --}}
<div class="ig-section">
    <p class="ig-h">Setup Guide</p>
    <div style="background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:16px;font-size:13px;line-height:1.7;color:var(--text-soft)">

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">1</div>
            <div>
                <strong style="color:var(--text)">Create a Facebook App</strong><br>
                Go to <a href="https://developers.facebook.com" target="_blank" style="color:var(--primary)">developers.facebook.com</a> → <strong>My Apps → Create App</strong> → Choose <strong>Business</strong> type.
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">2</div>
            <div>
                <strong style="color:var(--text)">Add Instagram Graph API</strong><br>
                In App Dashboard → <strong>Add Product</strong> → Select <strong>Instagram Graph API</strong>.
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">3</div>
            <div>
                <strong style="color:var(--text)">Configure OAuth Redirect</strong><br>
                In <strong>Instagram Graph API → Configure</strong>, add this Redirect URI:<br>
                <code style="word-break:break-all">{{ $env['redirect_uri'] }}</code>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--border)">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">4</div>
            <div>
                <strong style="color:var(--text)">Add Instagram Test User</strong><br>
                <strong>App Roles → Roles → Add Instagram Tester</strong> → Add your Instagram account username. Accept the invite in the Instagram app.
            </div>
        </div>

        <div style="display:flex;gap:12px">
            <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;font-weight:700;flex-shrink:0">5</div>
            <div>
                <strong style="color:var(--text)">Set .env Variables</strong><br>
                <code>FACEBOOK_APP_ID=your_app_id</code><br>
                <code>FACEBOOK_APP_SECRET=your_app_secret</code><br>
                Then run: <code>php artisan config:clear</code>
            </div>
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
