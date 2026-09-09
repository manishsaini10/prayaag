@extends('admin.layout')

@section('title', 'Instagram Feed')
@section('subtitle', 'Smash Balloon Style Social Photo & Reels Feed Manager')

@section('content')
<style>
    /* Smash Balloon Design Tokens */
    .sb-tabs{display:flex;gap:8px;border-bottom:1px solid var(--border);margin-bottom:24px;padding-bottom:2px}
    .sb-tab-btn{padding:10px 18px;border-radius:8px 8px 0 0;font-size:13.5px;font-weight:600;color:var(--text-muted);background:transparent;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .2s;border-bottom:2px solid transparent}
    .sb-tab-btn:hover{color:var(--text)}
    .sb-tab-btn.active{color:#dd2a7b;border-bottom-color:#dd2a7b;background:rgba(221,42,123,.04)}

    .ig-stat{text-align:center;padding:16px 12px;background:var(--surface);border:1px solid var(--border);border-radius:12px;flex:1;min-width:130px}
    .ig-stat .num{font-size:1.6rem;font-weight:700;color:var(--text);line-height:1.2}
    .ig-stat .lbl{font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-top:2px}

    .ig-card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:22px;position:relative}
    .ig-avatar-ring{width:56px;height:56px;border-radius:50%;padding:2px;background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ig-avatar{width:100%;height:100%;border-radius:50%;border:2px solid #fff;overflow:hidden;background:#1e293b;display:grid;place-items:center;color:#fff;font-weight:700;font-size:18px}
    .ig-avatar img{width:100%;height:100%;object-fit:cover}

    .btn-smash{background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);color:#fff;border:none;padding:11px 22px;border-radius:10px;font-weight:600;font-size:13.5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .2s;box-shadow:0 4px 14px rgba(221,42,123,.25);text-decoration:none}
    .btn-smash:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(221,42,123,.35);color:#fff}

    .ig-input{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:14px;color:var(--text);width:100%;outline:none;transition:border-color .2s}
    .ig-input:focus{border-color:#dd2a7b}

    /* Smash Balloon Modal */
    .sb-modal-backdrop{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,.65);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:16px}
    .sb-modal-card{background:var(--surface);border:1px solid var(--border);border-radius:18px;max-width:580px;width:100%;padding:28px;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);position:relative;animation:sbPop .25s cubic-bezier(0.16,1,0.3,1)}
    @keyframes sbPop{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}

    .sb-connect-option{border:1.5px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:12px;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:16px}
    .sb-connect-option:hover{border-color:#dd2a7b;background:rgba(221,42,123,.03)}
    .sb-connect-option.active{border-color:#dd2a7b;background:rgba(221,42,123,.05)}

    /* Feed Grid Visualizer (Smash Balloon Live Preview) */
    .sb-feed-preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-top:14px}
    .sb-preview-item{position:relative;aspect-ratio:1;border-radius:10px;overflow:hidden;background:#1e293b;cursor:pointer}
    .sb-preview-item img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .3s}
    .sb-preview-item:hover img{transform:scale(1.06)}
    .sb-preview-overlay{position:absolute;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;gap:12px;color:#fff;font-size:12px;font-weight:600;opacity:0;transition:opacity .2s}
    .sb-preview-item:hover .sb-preview-overlay{opacity:1}

    .ig-log{font-size:12.5px;padding:8px 0;border-bottom:1px solid var(--border);color:var(--text-soft)}
    .ig-log:last-child{border:0}
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

{{-- Summary Stats Bar --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:20px">
    <div class="ig-stat"><div class="num">{{ $totalAccounts }}</div><div class="lbl">Connected Sources</div></div>
    <div class="ig-stat"><div class="num">{{ number_format($totalFollowers) }}</div><div class="lbl">Total Followers</div></div>
    <div class="ig-stat"><div class="num">{{ $totalMedia }}</div><div class="lbl">Posts in Feed</div></div>
    <div class="ig-stat"><div class="num">{{ $latestSync ? $latestSync->diffForHumans() : 'Just now' }}</div><div class="lbl">Last Sync</div></div>
</div>

{{-- Smash Balloon Tab Bar --}}
<div class="sb-tabs">
    <button class="sb-tab-btn active" onclick="switchSbTab('tab-sources', this)">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H8V4h12v12z"/></svg>
        Sources & Accounts
    </button>
    <button class="sb-tab-btn" onclick="switchSbTab('tab-preview', this)">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
        All Feeds & Live Preview
    </button>
    <button class="sb-tab-btn" onclick="switchSbTab('tab-shortcode', this)">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg>
        Embed Shortcode
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     TAB 1: SOURCES & ACCOUNTS (Smash Balloon Style)
════════════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-sources" class="sb-tab-content">

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px">
        <div>
            <h3 style="margin:0;font-size:17px;font-weight:700;color:var(--text)">Instagram Sources</h3>
            <p style="margin:3px 0 0;font-size:12.5px;color:var(--text-muted)">Manage accounts connected to your website's social feeds.</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center">
            @if ($totalAccounts > 0)
                <form method="POST" action="{{ route('admin.instagram.sync.all') }}" style="display:inline">@csrf
                    <button class="btn" style="font-size:13px;padding:9px 16px">🔄 Sync All Feeds</button>
                </form>
            @endif
            <button type="button" class="btn-smash" onclick="openAddSourceModal()">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                ➕ Add New Source
            </button>
        </div>
    </div>

    {{-- Connected Accounts Cards --}}
    @if ($accounts->count())
        <div style="display:grid;gap:14px;margin-bottom:24px">
            @foreach ($accounts as $acct)
                <div class="ig-card" style="display:flex;align-items:center;gap:18px;flex-wrap:wrap">
                    <div class="ig-avatar-ring">
                        <div class="ig-avatar">
                            @if ($acct->profile_picture)
                                <img src="{{ $acct->profile_picture }}" alt="{{ $acct->username }}" onerror="this.style.display='none'">
                            @endif
                            {{ substr($acct->username ?: 'IG', 0, 2) }}
                        </div>
                    </div>

                    <div style="flex:1;min-width:220px">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <strong style="font-size:16px;color:var(--text)">{{ $acct->username }}</strong>
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="#0095f6"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            <span style="font-size:11px;background:var(--success-soft);color:var(--success);padding:2px 8px;border-radius:4px;font-weight:600">● Active Source</span>
                            @if (str_starts_with($acct->instagram_business_id, 'public_'))
                                <span style="font-size:11px;background:var(--bg-soft);color:var(--text-muted);padding:2px 8px;border-radius:4px">1-Click Connected</span>
                            @else
                                <span style="font-size:11px;background:rgba(8,102,255,.1);color:#0866ff;padding:2px 8px;border-radius:4px;font-weight:600">Business OAuth</span>
                            @endif
                        </div>

                        <div style="font-size:13px;color:var(--text-muted);margin-top:4px">
                            {{ number_format($acct->followers) }} followers · {{ $acct->media()->count() }} cached posts
                            · Synced {{ $acct->last_sync?->diffForHumans() ?? 'just now' }}
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;align-items:center">
                        <a href="https://www.instagram.com/{{ $acct->username }}/" target="_blank" rel="noopener" class="btn" style="font-size:12.5px;padding:7px 14px">
                            View on IG ↗
                        </a>
                        <form method="POST" action="{{ route('admin.instagram.sync', $acct->id) }}" style="display:inline">@csrf
                            <button class="btn" type="submit" style="font-size:12.5px;padding:7px 14px">🔄 Sync Now</button>
                        </form>
                        <form method="POST" action="{{ route('admin.instagram.disconnect', $acct->id) }}" style="display:inline">@csrf
                            <button class="btn" type="submit" style="font-size:12.5px;padding:7px 14px;color:var(--danger)" onclick="return confirm('Disconnect @{{ $acct->username }}? This will remove posts from the live feed.')">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="ig-card" style="text-align:center;padding:48px 24px;margin-bottom:24px">
            <div style="font-size:48px;margin-bottom:12px">📸</div>
            <h3 style="margin:0 0 6px;font-size:18px;font-weight:700">No Instagram Sources Connected Yet</h3>
            <p style="margin:0 0 20px;font-size:13.5px;color:var(--text-muted)">Connect your Instagram account to show photos and reels automatically on your school website.</p>
            <button type="button" class="btn-smash" onclick="openAddSourceModal()">➕ Connect Instagram Account</button>
        </div>
    @endif

    {{-- Activity & Sync Log --}}
    <div class="ig-card">
        <h3 style="font-size:14px;font-weight:600;margin:0 0 12px;color:var(--text)">Sync Activity Log</h3>
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
            <div class="ig-log" style="color:var(--text-muted)">No activity logs yet.</div>
        @endforelse
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     TAB 2: LIVE FEED PREVIEW (Smash Balloon Visualizer)
════════════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-preview" class="sb-tab-content" style="display:none">
    <div class="ig-card" style="margin-bottom:20px">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:700">Live Feed Visualizer</h3>
                <p style="margin:2px 0 0;font-size:12px;color:var(--text-muted)">Preview how your Instagram posts appear to website visitors.</p>
            </div>
            <div style="display:flex;gap:8px">
                <a href="{{ route('admin.instagram.settings') }}" class="btn" style="font-size:12px">⚙️ Customize Appearance</a>
            </div>
        </div>

        @if ($latestPosts->count())
            <div class="sb-feed-preview-grid">
                @foreach ($latestPosts as $post)
                    <div class="sb-preview-item" onclick="openSbLightbox('{{ $post->media_url }}', '{{ $post->media_type }}', '{{ $post->permalink }}', '{{ addslashes($post->caption ?? '') }}', '{{ $post->likes }}', '{{ $post->comments }}')">
                        <img src="{{ $post->thumbnail_url ?: $post->media_url }}" alt="" loading="lazy">
                        <div class="sb-preview-overlay">
                            <span>❤️ {{ $post->likes }}</span>
                            <span>💬 {{ $post->comments }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:40px;color:var(--text-muted)">
                <p>No cached posts found in database. Connect an account above to sync posts!</p>
            </div>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     TAB 3: EMBED SHORTCODE (Smash Balloon Style)
════════════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-shortcode" class="sb-tab-content" style="display:none">
    <div class="ig-card" style="margin-bottom:20px">
        <h3 style="margin:0 0 8px;font-size:16px;font-weight:700">Display Feed on Any Page</h3>
        <p style="margin:0 0 16px;font-size:13px;color:var(--text-muted)">Add your Instagram feed to the Homepage, Footer, or any custom page using this widget shortcode:</p>

        <div style="background:var(--bg-soft);border:1px solid var(--border);border-radius:10px;padding:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px">
            <code style="font-size:14px;color:var(--text);font-family:monospace">&lt;x-instagram-feed limit="12" columns-desktop="4" layout="grid" popup="true" /&gt;</code>
            <button type="button" class="btn" onclick="copyShortcode('&lt;x-instagram-feed limit=&quot;12&quot; columns-desktop=&quot;4&quot; layout=&quot;grid&quot; popup=&quot;true&quot; /&gt;')" style="font-size:12.5px;white-space:nowrap">
                📋 Copy Code
            </button>
        </div>

        <h4 style="font-size:14px;margin:20px 0 10px">Available Layout Options:</h4>
        <ul style="font-size:13px;color:var(--text-muted);line-height:1.8;padding-left:20px">
            <li><strong>Grid:</strong> <code>layout="grid" columns-desktop="4"</code> — Standard high-density photo grid.</li>
            <li><strong>Carousel:</strong> <code>layout="carousel"</code> — Horizontal scroll slider with touch gestures.</li>
            <li><strong>Masonry:</strong> <code>layout="masonry"</code> — Pinterest-style staggered waterfall layout.</li>
            <li><strong>Lightbox Popup:</strong> <code>popup="true"</code> — Fullscreen interactive popup on photo click.</li>
        </ul>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     SMASH BALLOON STYLE "ADD SOURCE" MODAL DIALOG
════════════════════════════════════════════════════════════════════════════════ --}}
<div id="sb-add-source-modal" class="sb-modal-backdrop" onclick="if(event.target===this)closeAddSourceModal()">
    <div class="sb-modal-card" onclick="event.stopPropagation()">
        <button onclick="closeAddSourceModal()" style="position:absolute;top:16px;right:16px;background:none;border:none;color:var(--text-muted);font-size:20px;cursor:pointer">✕</button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
            <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#f58529,#dd2a7b,#8134af);display:grid;place-items:center;color:#fff;font-size:20px">📸</div>
            <div>
                <h3 style="margin:0;font-size:18px;font-weight:700;color:var(--text)">Add Instagram Source</h3>
                <p style="margin:2px 0 0;font-size:12.5px;color:var(--text-muted)">Choose how you would like to connect your Instagram account:</p>
            </div>
        </div>

        {{-- Choice 1: Personal (1-Click Zero-Setup) --}}
        <div class="sb-connect-option active" id="opt-personal" onclick="selectConnectOption('personal')">
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(221,42,123,.1);color:#dd2a7b;display:grid;place-items:center;font-size:18px;flex-shrink:0">⚡</div>
            <div style="flex:1">
                <strong style="font-size:14px;color:var(--text)">Personal Account (1-Click Instant)</strong>
                <p style="margin:2px 0 0;font-size:12px;color:var(--text-muted)">Zero setup. Enter your Instagram handle (@prayaag2016) — no Facebook developer app required.</p>
            </div>
            <input type="radio" name="connect_type" checked>
        </div>

        {{-- Choice 2: Business (Meta Login Popup) --}}
        <div class="sb-connect-option" id="opt-business" onclick="selectConnectOption('business')">
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(8,102,255,.1);color:#0866ff;display:grid;place-items:center;font-size:18px;flex-shrink:0">🔗</div>
            <div style="flex:1">
                <strong style="font-size:14px;color:var(--text)">Business / Creator Account (Meta Login Popup)</strong>
                <p style="margin:2px 0 0;font-size:12px;color:var(--text-muted)">Official Meta popup authorization dialog for business pages.</p>
            </div>
            <input type="radio" name="connect_type">
        </div>

        {{-- Choice 3: Add Post URLs (oEmbed) --}}
        <div class="sb-connect-option" id="opt-oembed" onclick="selectConnectOption('oembed')">
            <div style="width:36px;height:36px;border-radius:50%;background:rgba(16,185,129,.1);color:#10b981;display:grid;place-items:center;font-size:18px;flex-shrink:0">📎</div>
            <div style="flex:1">
                <strong style="font-size:14px;color:var(--text)">Direct Post / Reel Links (oEmbed)</strong>
                <p style="margin:2px 0 0;font-size:12px;color:var(--text-muted)">Paste specific Instagram post or reel links directly without any login.</p>
            </div>
            <input type="radio" name="connect_type">
        </div>

        {{-- Form 1: Personal Form --}}
        <div id="form-personal" style="margin-top:18px">
            <form method="POST" action="{{ route('admin.instagram.connect.handle') }}">
                @csrf
                <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:6px;color:var(--text)">Instagram Username / Handle:</label>
                <div style="position:relative;margin-bottom:14px">
                    <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-weight:600">@</span>
                    <input type="text" name="username" class="ig-input" value="prayaag2016" style="padding-left:32px" required>
                </div>
                <button type="submit" class="btn-smash" style="width:100%;justify-content:center">⚡ Connect & Sync Live Posts</button>
            </form>
        </div>

        {{-- Form 2: Business Popup Form --}}
        <div id="form-business" style="margin-top:18px;display:none">
            @if ($appIdSet)
                <p style="font-size:12.5px;color:var(--text-muted);margin:0 0 14px">
                    Clicking the button below will open the official Meta Login dialog in a popup window to connect your Business Account.
                </p>
                <button type="button" class="btn-smash" style="width:100%;justify-content:center;background:linear-gradient(135deg,#0866ff,#0052cc)" onclick="openMetaOAuthPopup()">
                    🌐 Open Meta Login Popup
                </button>
            @else
                <div style="padding:14px;background:rgba(245,133,41,.08);border:1px solid rgba(245,133,41,.2);border-radius:10px;font-size:12.5px;color:var(--text-muted);line-height:1.6;margin-bottom:14px">
                    ⚠️ <strong>Meta App Credentials Not Set:</strong> Business OAuth popup requires Meta App ID & Secret. You can save them in <a href="{{ route('admin.instagram.settings') }}" style="color:#0866ff;font-weight:600">Settings</a>, or use the <strong>Personal Account (1-Click)</strong> option above for zero-setup.
                </div>
                <a href="{{ route('admin.instagram.settings') }}" class="btn primary" style="display:block;text-align:center;padding:10px">⚙️ Go to Settings to Save App ID</a>
            @endif
        </div>

        {{-- Form 3: oEmbed URL Form --}}
        <div id="form-oembed" style="margin-top:18px;display:none">
            <form method="POST" action="{{ route('admin.instagram.add.posts') }}">
                @csrf
                @if ($accounts->count() > 0)
                    <input type="hidden" name="account_id" value="{{ $accounts->first()->id }}">
                @endif
                <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:6px;color:var(--text)">Paste Instagram Post / Reel URLs (one per line):</label>
                <textarea name="post_urls" class="ig-input" rows="4" placeholder="https://www.instagram.com/p/C...&#10;https://www.instagram.com/reel/D..." style="font-family:monospace;font-size:12.5px;margin-bottom:14px" required></textarea>
                <button type="submit" class="btn-smash" style="width:100%;justify-content:center">⚡ Fetch & Live Embed</button>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     SMASH BALLOON LIGHTBOX MODAL
════════════════════════════════════════════════════════════════════════════════ --}}
<div id="sb-lightbox" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.9);z-index:99999;align-items:center;justify-content:center;padding:16px" onclick="if(event.target===this)closeSbLightbox()">
    <div style="background:#0f172a;border-radius:14px;overflow:hidden;max-width:540px;width:100%;color:#fff;position:relative" onclick="event.stopPropagation()">
        <button onclick="closeSbLightbox()" style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,.6);border:none;color:#fff;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:18px;z-index:10">✕</button>
        <div style="background:#000;text-align:center;max-height:450px;overflow:hidden">
            <img id="sb-lightbox-img" src="" alt="" style="max-width:100%;max-height:450px;object-fit:contain;display:block;margin:auto">
            <video id="sb-lightbox-video" src="" controls style="max-width:100%;max-height:450px;display:none;margin:auto"></video>
        </div>
        <div style="padding:16px 18px">
            <p id="sb-lightbox-caption" style="font-size:13px;line-height:1.5;color:#e2e8f0;margin:0 0 10px"></p>
            <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#94a3b8">
                <span id="sb-lightbox-stats"></span>
                <a id="sb-lightbox-link" href="#" target="_blank" rel="noopener" style="color:#38bdf8;text-decoration:none;font-weight:600">View on Instagram →</a>
            </div>
        </div>
    </div>
</div>

<script>
function switchSbTab(tabId, btn) {
    document.querySelectorAll('.sb-tab-content').forEach(function(el){ el.style.display = 'none'; });
    document.querySelectorAll('.sb-tab-btn').forEach(function(el){ el.classList.remove('active'); });
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
}

function openAddSourceModal() {
    document.getElementById('sb-add-source-modal').style.display = 'flex';
}

function closeAddSourceModal() {
    document.getElementById('sb-add-source-modal').style.display = 'none';
}

function selectConnectOption(type) {
    ['personal', 'business', 'oembed'].forEach(function(t) {
        document.getElementById('opt-' + t).classList.toggle('active', t === type);
        document.getElementById('form-' + t).style.display = t === type ? 'block' : 'none';
        document.querySelector('#opt-' + t + ' input').checked = (t === type);
    });
}

function openMetaOAuthPopup() {
    var width = 650, height = 750;
    var left = (screen.width - width) / 2;
    var top = (screen.height - height) / 2;
    var popup = window.open(
        '{{ route('admin.instagram.oauth.connect') }}',
        'ig_oauth_popup',
        'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',scrollbars=yes,status=no'
    );
    if (window.focus) popup.focus();
}

window.addEventListener('message', function(event) {
    if (event.data && event.data.type === 'IG_OAUTH_SUCCESS') {
        window.location.reload();
    }
});

function openSbLightbox(url, type, link, caption, likes, comments) {
    var modal = document.getElementById('sb-lightbox');
    var img = document.getElementById('sb-lightbox-img');
    var video = document.getElementById('sb-lightbox-video');

    if (type === 'VIDEO' || type === 'REEL') {
        img.style.display = 'none';
        video.style.display = 'block';
        video.src = url;
    } else {
        video.style.display = 'none';
        img.style.display = 'block';
        img.src = url;
    }

    document.getElementById('sb-lightbox-caption').textContent = caption || '';
    document.getElementById('sb-lightbox-stats').textContent = '❤️ ' + (likes || 0) + '   💬 ' + (comments || 0);
    document.getElementById('sb-lightbox-link').href = link || '#';
    modal.style.display = 'flex';
}

function closeSbLightbox() {
    var modal = document.getElementById('sb-lightbox');
    var video = document.getElementById('sb-lightbox-video');
    modal.style.display = 'none';
    video.pause();
}

function copyShortcode(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied shortcode to clipboard!');
    });
}
</script>

@endsection
