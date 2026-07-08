@extends('admin.layout')

@section('title', 'Instagram Feed')
@section('subtitle', 'Enterprise Instagram Feed Manager')

@section('content')
<style>
    .ig-stat{text-align:center;padding:16px 12px;background:var(--surface);border:1px solid var(--border);border-radius:12px;flex:1;min-width:120px}
    .ig-stat .num{font-size:1.6rem;font-weight:700;color:var(--text);line-height:1.2}
    .ig-stat .lbl{font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:2px}
    .ig-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:20px}
    .ig-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);display:grid;place-items:center;color:#fff;font-weight:700;font-size:18px;flex-shrink:0;text-transform:uppercase}
    .ig-token{height:5px;border-radius:3px;background:var(--bg-soft);overflow:hidden;max-width:180px}
    .ig-token-fill{height:100%;border-radius:3px;transition:width .5s}
    .ig-log{font-size:12.5px;padding:8px 0;border-bottom:1px solid var(--border);color:var(--text-soft)}
    .ig-log:last-child{border:0}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $errors->first() }}</div>
@endif

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:16px">
    <div class="ig-stat"><div class="num">{{ $totalAccounts }}</div><div class="lbl">Accounts</div></div>
    <div class="ig-stat"><div class="num">{{ number_format($totalFollowers) }}</div><div class="lbl">Followers</div></div>
    <div class="ig-stat"><div class="num">{{ $totalMedia }}</div><div class="lbl">Posts Cached</div></div>
    <div class="ig-stat"><div class="num">{{ $latestSync ? $latestSync->diffForHumans() : '—' }}</div><div class="lbl">Last Sync</div></div>
</div>

{{-- Actions --}}
<div class="ig-card" style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <div>
        @if ($totalAccounts > 0)
            <strong style="font-size:14px">{{ $totalAccounts }} Account(s) Connected</strong>
        @else
            <strong style="font-size:14px">No accounts connected</strong>
        @endif
    </div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
        @if ($totalAccounts > 0)
            <form method="POST" action="{{ route('admin.instagram.sync.all') }}" style="display:inline">@csrf
                <button class="btn" style="font-size:12px">Sync All</button>
            </form>
            <form method="POST" action="{{ route('admin.instagram.tokens.refresh') }}" style="display:inline">@csrf
                <button class="btn" style="font-size:12px">Refresh Tokens</button>
            </form>
        @endif
        <a href="{{ route('admin.instagram.oauth.connect') }}" class="btn primary" style="font-size:12px">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="margin-right:4px"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
            Connect Instagram
        </a>
        <a href="{{ route('admin.instagram.settings') }}" class="btn" style="font-size:12px">Settings</a>
    </div>
</div>

{{-- Connected Accounts --}}
@if ($accounts->count())
<div style="display:grid;gap:10px;margin-bottom:16px">
    @foreach ($accounts as $acct)
        @php
            $expiryPct = $acct->expires_at ? max(0, min(100, round(($acct->expires_at->diffInDays(now()) / 60) * 100))) : 100;
            $expiryColor = $expiryPct < 20 ? 'var(--danger)' : ($expiryPct < 50 ? 'var(--warning)' : 'var(--success)');
        @endphp
        <div class="ig-card" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <div class="ig-avatar">{{ substr($acct->username ?: 'IG', 0, 2) }}</div>
            <div style="flex:1;min-width:180px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <strong style="font-size:14px">{{ $acct->username }}</strong>
                    <span style="font-size:11px;color:var(--text-muted);background:var(--bg-soft);padding:2px 8px;border-radius:4px">{{ $acct->name }}</span>
                </div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
                    {{ number_format($acct->followers) }} followers · {{ $acct->media()->count() }} posts
                    · Synced {{ $acct->last_sync?->diffForHumans() ?? 'never' }}
                </div>
                @if ($acct->expires_at)
                <div style="display:flex;align-items:center;gap:8px;margin-top:4px">
                    <div class="ig-token"><div class="ig-token-fill" style="width:{{ $expiryPct }}%;background:{{ $expiryColor }}"></div></div>
                    <span style="font-size:11px;color:var(--text-muted)">
                        @if ($acct->isExpired())
                            <span style="color:var(--danger)">Expired</span>
                        @else
                            Expires {{ $acct->expires_at->diffForHumans() }}
                        @endif
                    </span>
                </div>
                @endif
            </div>
            <div style="display:flex;gap:6px">
                <form method="POST" action="{{ route('admin.instagram.sync', $acct->id) }}" style="display:inline">@csrf
                    <button class="btn" type="submit" style="font-size:11px;padding:5px 10px">Sync</button>
                </form>
                <form method="POST" action="{{ route('admin.instagram.disconnect', $acct->id) }}" style="display:inline">@csrf
                    <button class="btn" type="submit" style="font-size:11px;padding:5px 10px;color:var(--danger)" onclick="return confirm('Disconnect {{ $acct->username }}? This cannot be undone.')">Disconnect</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- Expiring tokens alert --}}
@if ($expiringTokens->count())
    <div class="ig-card" style="border-color:var(--warning);background:var(--warning-soft);margin-bottom:16px">
        <strong style="color:var(--warning);font-size:13px">⚠ Token Expiry Alert</strong>
        <div style="font-size:12.5px;color:var(--text-soft);margin-top:4px">
            {{ $expiringTokens->count() }} account(s) have tokens expiring within 10 days:
            @foreach ($expiringTokens as $acct)
                <strong>{{ $acct->username }}</strong> ({{ $acct->expires_at->diffForHumans() }})@if(!$loop->last), @endif
            @endforeach
            <form method="POST" action="{{ route('admin.instagram.tokens.refresh') }}" style="display:inline">@csrf
                <button class="btn" type="submit" style="font-size:11px;padding:4px 10px;margin-left:8px">Refresh Now</button>
            </form>
        </div>
    </div>
@endif

{{-- Activity Log --}}
<div class="ig-card">
    <h3 style="font-size:14px;font-weight:600;margin:0 0 8px;color:var(--text)">Activity Log</h3>
    @forelse ($recentLogs as $log)
        <div class="ig-log">
            <span style="display:inline-block;padding:1px 7px;border-radius:4px;font-size:10px;font-weight:600;text-transform:uppercase;margin-right:6px;
                @if($log->status === 'connected' || $log->status === 'success') background:var(--success-soft);color:var(--success)
                @elseif($log->status === 'failed') background:var(--danger-soft);color:var(--danger)
                @else background:var(--bg-soft);color:var(--text-muted)@endif
            ">{{ $log->status }}</span>
            {{ $log->created_at->diffForHumans() }} — {{ $log->message }}
            @if ($log->execution_time > 0)<span style="color:var(--text-muted);font-size:11px;margin-left:6px">({{ $log->execution_time }}s)</span>@endif
        </div>
    @empty
        <div class="ig-log" style="color:var(--text-muted)">No activity yet. Connect an Instagram account to get started.</div>
    @endforelse
</div>

@endsection
