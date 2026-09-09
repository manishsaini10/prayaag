@extends('admin.layout')

@section('title', 'Instagram Feed')
@section('subtitle', '1-Click Instagram Feed & Social Media Manager')

@section('content')
<style>
    .ig-stat{text-align:center;padding:16px 12px;background:var(--surface);border:1px solid var(--border);border-radius:12px;flex:1;min-width:120px}
    .ig-stat .num{font-size:1.6rem;font-weight:700;color:var(--text);line-height:1.2}
    .ig-stat .lbl{font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:2px}
    .ig-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:22px}
    .ig-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);display:grid;place-items:center;color:#fff;font-weight:700;font-size:18px;flex-shrink:0;text-transform:uppercase;overflow:hidden}
    .ig-avatar img{width:100%;height:100%;object-fit:cover}
    .ig-token{height:5px;border-radius:3px;background:var(--bg-soft);overflow:hidden;max-width:180px}
    .ig-token-fill{height:100%;border-radius:3px;transition:width .5s}
    .ig-log{font-size:12.5px;padding:8px 0;border-bottom:1px solid var(--border);color:var(--text-soft)}
    .ig-log:last-child{border:0}
    .ig-connect-box{background:linear-gradient(135deg,rgba(221,42,123,.05),rgba(129,52,175,.05));border:1.5px dashed rgba(221,42,123,.3);border-radius:14px;padding:24px;margin-bottom:20px}
    .ig-input{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:14px;color:var(--text);width:100%;outline:none;transition:border-color .2s}
    .ig-input:focus{border-color:#dd2a7b}
    .btn-ig{background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);color:#fff;border:none;padding:10px 22px;border-radius:8px;font-weight:600;font-size:13.5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:opacity .2s;text-decoration:none}
    .btn-ig:hover{opacity:.92}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px">
        ✅ {{ session('status') }}
    </div>
@endif
@if (isset($errors) && $errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">
        ❌ {{ $errors->first() }}
    </div>
@endif

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:20px">
    <div class="ig-stat"><div class="num">{{ $totalAccounts }}</div><div class="lbl">Connected Accounts</div></div>
    <div class="ig-stat"><div class="num">{{ number_format($totalFollowers) }}</div><div class="lbl">Followers</div></div>
    <div class="ig-stat"><div class="num">{{ $totalMedia }}</div><div class="lbl">Posts Cached</div></div>
    <div class="ig-stat"><div class="num">{{ $latestSync ? $latestSync->diffForHumans() : '—' }}</div><div class="lbl">Last Sync</div></div>
</div>

{{-- 1-CLICK ZERO-SETUP CONNECT BOX --}}
<div class="ig-connect-box">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
        <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);display:grid;place-items:center;color:#fff;font-size:18px">📸</div>
        <div>
            <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text)">1-Click Connect (Zero Setup — No Meta App / No .env Required)</h3>
            <p style="margin:2px 0 0;font-size:12.5px;color:var(--text-muted)">Sirf apna Instagram handle enter karein aur live feed website par start ho jayegi.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.instagram.connect.handle') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:14px">
        @csrf
        <div style="position:relative;flex:1;min-width:220px;max-width:380px">
            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-weight:600">@</span>
            <input type="text" name="username" class="ig-input" placeholder="prayaag2016" value="{{ old('username', 'prayaag2016') }}" style="padding-left:32px" required>
        </div>
        <button type="submit" class="btn-ig">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
            ⚡ Connect & Fetch Live Feed
        </button>
        <button type="button" onclick="document.getElementById('token-advanced-box').style.display = document.getElementById('token-advanced-box').style.display === 'none' ? 'block' : 'none'" class="btn" style="font-size:12.5px">
            🔑 Manual Token Paste (Optional)
        </button>
    </form>

    {{-- Manual Token Optional Box --}}
    <div id="token-advanced-box" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid rgba(221,42,123,.2)">
        <p style="font-size:12.5px;color:var(--text-muted);margin:0 0 8px">Agar aapke paas direct Instagram / Meta User Token hai, to yaha paste karke direct database me save karein:</p>
        <form method="POST" action="{{ route('admin.instagram.save.token') }}" style="display:flex;gap:10px;flex-wrap:wrap">
            @csrf
            <input type="text" name="username" class="ig-input" placeholder="Instagram Username (e.g. prayaag2016)" style="max-width:220px" required>
            <input type="text" name="access_token" class="ig-input" placeholder="Instagram Access Token (EAAB...)" style="flex:1;min-width:240px" required>
            <button type="submit" class="btn primary" style="font-size:13px">Save Token & Sync</button>
        </form>
    </div>
</div>

{{-- Actions Bar --}}
<div class="ig-card" style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <div>
        <strong style="font-size:14px">
            @if ($totalAccounts > 0)
                {{ $totalAccounts }} Active Account(s) Connected
            @else
                No Instagram accounts connected yet.
            @endif
        </strong>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @if ($totalAccounts > 0)
            <form method="POST" action="{{ route('admin.instagram.sync.all') }}" style="display:inline">@csrf
                <button class="btn" style="font-size:12.5px">🔄 Sync All Posts</button>
            </form>
        @endif
        <a href="{{ route('admin.instagram.settings') }}" class="btn" style="font-size:12.5px">⚙️ Advanced Settings</a>
    </div>
</div>

{{-- Connected Accounts --}}
@if ($accounts->count())
<div style="display:grid;gap:12px;margin-bottom:20px">
    @foreach ($accounts as $acct)
        <div class="ig-card" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap">
            <div class="ig-avatar">
                @if ($acct->profile_picture)
                    <img src="{{ $acct->profile_picture }}" alt="{{ $acct->username }}" onerror="this.style.display='none'">
                @endif
                {{ substr($acct->username ?: 'IG', 0, 2) }}
            </div>
            <div style="flex:1;min-width:200px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <strong style="font-size:15px">{{ $acct->username }}</strong>
                    <span style="font-size:11px;color:var(--text-muted);background:var(--bg-soft);padding:2px 8px;border-radius:4px">{{ $acct->name }}</span>
                    <span style="font-size:11px;background:var(--success-soft);color:var(--success);padding:2px 8px;border-radius:4px;font-weight:600">Active Live Feed</span>
                </div>
                <div style="font-size:12.5px;color:var(--text-muted);margin-top:4px">
                    {{ number_format($acct->followers) }} followers · {{ $acct->media()->count() }} cached posts
                    · Last synced {{ $acct->last_sync?->diffForHumans() ?? 'just now' }}
                </div>
            </div>
            <div style="display:flex;gap:8px">
                <a href="https://www.instagram.com/{{ $acct->username }}/" target="_blank" rel="noopener" class="btn" style="font-size:12px;padding:6px 12px">View on IG ↗</a>
                <form method="POST" action="{{ route('admin.instagram.sync', $acct->id) }}" style="display:inline">@csrf
                    <button class="btn" type="submit" style="font-size:12px;padding:6px 12px">Sync Now</button>
                </form>
                <form method="POST" action="{{ route('admin.instagram.disconnect', $acct->id) }}" style="display:inline">@csrf
                    <button class="btn" type="submit" style="font-size:12px;padding:6px 12px;color:var(--danger)" onclick="return confirm('Disconnect @{{ $acct->username }}? This will remove posts from frontend.')">Disconnect</button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endif

{{-- Activity Log --}}
<div class="ig-card">
    <h3 style="font-size:14px;font-weight:600;margin:0 0 10px;color:var(--text)">Activity & Sync Log</h3>
    @forelse ($recentLogs as $log)
        <div class="ig-log">
            <span style="display:inline-block;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:600;text-transform:uppercase;margin-right:8px;
                @if($log->status === 'connected' || $log->status === 'success') background:var(--success-soft);color:var(--success)
                @elseif($log->status === 'failed') background:var(--danger-soft);color:var(--danger)
                @else background:var(--bg-soft);color:var(--text-muted)@endif
            ">{{ $log->status }}</span>
            <span style="font-size:12px;color:var(--text-muted);margin-right:6px">{{ $log->created_at->diffForHumans() }}</span>
            — {{ $log->message }}
        </div>
    @empty
        <div class="ig-log" style="color:var(--text-muted)">No activity logs yet. Enter your Instagram handle above to connect.</div>
    @endforelse
</div>

@endsection
