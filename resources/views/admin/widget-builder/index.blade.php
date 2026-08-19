@extends('admin.layout')

@section('title', 'WidgetStudio')
@section('subtitle', $customWidgets->count() . ' custom widgets · ' . collect($allWidgets)->flatten(1)->count() . ' in library')

@section('actions')
    <a href="{{ route('admin.widgets.create') }}" class="btn-sm primary">+ New Widget</a>
@endsection

@section('content')

@if (session('status'))
    <div id="ws-toast" style="position:fixed;top:20px;right:24px;z-index:9999;background:#10b981;color:#fff;padding:12px 20px;border-radius:12px;font-size:13.5px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.18);display:flex;align-items:center;gap:8px;animation:ws-slidein .3s ease">
        <span>{{ session('status') }}</span>
        <button onclick="document.getElementById('ws-toast').remove()" style="background:none;border:none;color:#fff;cursor:pointer;font-size:16px;line-height:1;margin-left:6px">×</button>
    </div>
    <script>setTimeout(()=>{ const t=document.getElementById('ws-toast'); if(t) t.style.opacity='0'; setTimeout(()=>{ if(t)t.remove(); }, 400); }, 4000);</script>
@endif

<style>
@keyframes ws-slidein { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:none} }
@keyframes ws-fade { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }
@keyframes ws-modal-in { from{opacity:0;transform:scale(.96) translateY(10px)} to{opacity:1;transform:scale(1) translateY(0)} }

/* ── Layout ── */
.ws-wrap { display:grid; grid-template-columns:220px 1fr; gap:20px; align-items:start; }
@media(max-width:900px){ .ws-wrap{ grid-template-columns:1fr; } }

/* ── Tabs ── */
.ws-tabs { display:flex; gap:4px; margin-bottom:20px; border-bottom:2px solid var(--border); }
.ws-tab  { padding:10px 20px; font-size:14px; font-weight:700; border:none; background:none; cursor:pointer;
           color:var(--text-muted); border-bottom:3px solid transparent; margin-bottom:-2px; transition:.15s; border-radius:8px 8px 0 0; }
.ws-tab:hover { background:var(--surface-2); color:var(--text); }
.ws-tab.active { color:#6366f1; border-bottom-color:#6366f1; background:rgba(99,102,241,.07); }

/* ── Sidebar ── */
.ws-sidebar { background:var(--surface); border:1px solid var(--border); border-radius:14px; padding:12px; position:sticky; top:80px; }
.ws-sidebar h4 { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.7px; color:var(--text-muted); padding:8px 8px 6px; }
.ws-cat-btn { width:100%; display:flex; align-items:center; gap:8px; padding:8px 10px; border:none; background:none;
              border-radius:8px; font-size:13px; color:var(--text-muted); cursor:pointer; text-align:left; transition:.12s; font-weight:500; }
.ws-cat-btn:hover { background:var(--surface-2); color:var(--text); }
.ws-cat-btn.active { background:rgba(99,102,241,.12); color:#6366f1; font-weight:700; }
.ws-cat-btn .cnt { margin-left:auto; font-size:11px; background:var(--surface-2); padding:1px 7px; border-radius:999px; color:var(--text-muted); }
.ws-cat-btn.active .cnt { background:rgba(99,102,241,.2); color:#6366f1; }

/* ── Search & Filter Bar ── */
.ws-top-bar { display:flex; flex-direction:column; gap:12px; margin-bottom:18px; }
.ws-search-wrap { position:relative; width:100%; }
.ws-search { width:100%; padding:10px 14px 10px 38px; border:1.5px solid var(--border); border-radius:10px;
             background:var(--surface); color:var(--text); font:inherit; font-size:13.5px; transition:.2s; }
.ws-search:focus { outline:none; border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.ws-search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:14px; pointer-events:none; }

.ws-filter-pills { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
.ws-pill-btn { padding:6px 14px; border-radius:999px; border:1px solid var(--border); background:var(--surface); color:var(--text-muted); font-size:12.5px; font-weight:600; cursor:pointer; transition:all .15s; display:flex; align-items:center; gap:6px; }
.ws-pill-btn:hover { background:var(--surface-2); color:var(--text); }
.ws-pill-btn.active { background:#6366f1; color:#ffffff; border-color:#6366f1; box-shadow:0 2px 8px rgba(99,102,241,.25); }

/* ── Widget Cards Grid ── */
.ws-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:14px; animation:ws-fade .25s ease; }
.ws-card { background:var(--surface); border:1.5px solid var(--border); border-radius:14px; overflow:hidden;
           transition:all .2s; display:flex; flex-direction:column; position:relative; }
.ws-card:hover { border-color:#6366f1; box-shadow:0 6px 24px rgba(99,102,241,.13); transform:translateY(-2px); }

.ws-card-preview { height:90px; display:flex; align-items:center; justify-content:center; font-size:36px;
                   background:linear-gradient(135deg, var(--surface-2), var(--surface)); border-bottom:1px solid var(--border); position:relative; }
.ws-dyn-badge { position:absolute;top:8px;right:8px;background:#f59e0b;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:6px;letter-spacing:.4px; }
.ws-pro-badge { position:absolute;top:8px;left:8px;background:linear-gradient(135deg,#c79a3b,#e0b94e);color:#0b2545;font-size:10px;font-weight:800;padding:2px 7px;border-radius:6px;letter-spacing:.5px;box-shadow:0 2px 6px rgba(199,154,59,.4); }
.ws-added-badge { position:absolute;bottom:8px;right:8px;background:#10b981;color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:6px; }

.ws-card-body { padding:12px 14px; flex:1; display:flex; flex-direction:column; gap:6px; }
.ws-card-name { font-size:13.5px; font-weight:700; color:var(--text); line-height:1.3; }
.ws-card-cat  { font-size:11px; color:var(--text-muted); display:flex; align-items:center; gap:4px; }
.ws-card-cat .dot { width:6px;height:6px;border-radius:50%;background:#6366f1;display:inline-block; }

.ws-card-actions { padding:8px 10px; border-top:1px solid var(--border); display:flex; gap:5px; align-items:center; }

.ws-card.is-seeded { border-color:#10b981; }
.ws-card.is-seeded .ws-card-preview { background:linear-gradient(135deg,rgba(16,185,129,.08),rgba(16,185,129,.03)); }
.ws-card.is-seeded:hover { border-color:#10b981; box-shadow:0 6px 24px rgba(16,185,129,.13); }

.ws-seed-btn { flex:1; padding:6px 8px; border-radius:7px; font-size:12px; font-weight:600; border:none; cursor:pointer;
               background:linear-gradient(135deg,#6366f1,#8b5cf6); color:#fff; transition:.15s; }
.ws-seed-btn:hover { opacity:.9; }
.ws-preview-btn { padding:6px 8px; border-radius:7px; font-size:12px; font-weight:600; border:1px solid var(--border);
                  background:var(--surface-2); color:var(--text-muted); cursor:pointer; transition:.15s; white-space:nowrap; }
.ws-preview-btn:hover { background:#6366f1; color:#fff; border-color:#6366f1; }
.ws-edit-btn { padding:6px 9px; border-radius:7px; font-size:12px; font-weight:600; border:1px solid var(--border);
               background:var(--surface-2); color:var(--text); text-decoration:none; transition:.15s; }
.ws-edit-btn:hover { background:#6366f1; color:#fff; border-color:#6366f1; }

/* ── My Widgets Cards ── */
.mw-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:14px; }
.mw-card { background:var(--surface); border:1.5px solid var(--border); border-radius:14px; padding:16px; display:flex; flex-direction:column; gap:10px; transition:.2s; }
.mw-card:hover { border-color:#6366f1; box-shadow:0 4px 18px rgba(99,102,241,.12); }
.mw-card-header { display:flex; align-items:flex-start; gap:10px; }
.mw-card-icon { width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
.mw-card-title { font-size:14px;font-weight:700;color:var(--text);line-height:1.3; }
.mw-card-slug  { font-size:11.5px;color:var(--text-muted);font-family:ui-monospace,monospace; }
.mw-card-meta  { display:flex;gap:6px;flex-wrap:wrap;align-items:center; }
.mw-badge { font-size:11px;font-weight:600;padding:2px 8px;border-radius:6px;background:var(--surface-2);color:var(--text-muted); }
.mw-badge.active { background:rgba(16,185,129,.12);color:#10b981; }
.mw-badge.inactive { background:rgba(239,68,68,.1);color:#ef4444; }
.mw-card-actions { display:flex;gap:6px;padding-top:4px;border-top:1px solid var(--border); }
.mw-act-btn { flex:1;padding:6px 10px;border-radius:7px;font-size:12px;font-weight:600;text-align:center;text-decoration:none;border:none;cursor:pointer;transition:.13s; }
.mw-act-btn.edit { background:var(--surface-2);color:var(--text);border:1px solid var(--border); }
.mw-act-btn.edit:hover { background:#6366f1;color:#fff;border-color:#6366f1; }
.mw-act-btn.del  { background:rgba(239,68,68,.08);color:#ef4444;border:1px solid rgba(239,68,68,.2); }
.mw-act-btn.del:hover { background:#ef4444;color:#fff; }

/* Empty state */
.ws-empty { text-align:center;padding:48px 20px;color:var(--text-muted); }
.ws-empty .ws-empty-icon { font-size:48px;display:block;margin-bottom:12px; }
.ws-empty h3 { font-size:16px;color:var(--text);margin-bottom:6px; }
.ws-empty p  { font-size:13.5px;max-width:40ch;margin:0 auto; }
.ws-section-head { font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--text-muted);margin:0 0 12px; padding-bottom:8px; border-bottom:1px solid var(--border); }

/* ─────────────────────────────────────────
   PREVIEW MODAL
───────────────────────────────────────── */
.ws-modal-backdrop {
    position:fixed; inset:0; z-index:10000;
    background:rgba(11,21,42,.65); backdrop-filter:blur(6px);
    display:none; align-items:center; justify-content:center;
    padding:20px;
}
.ws-modal-backdrop.open { display:flex; }
.ws-modal {
    background:var(--surface); border-radius:18px; overflow:hidden;
    box-shadow:0 32px 80px rgba(0,0,0,.35);
    display:flex; flex-direction:column;
    width:100%; max-width:1180px; max-height:90vh;
    animation:ws-modal-in .22s ease;
}
.ws-modal-header {
    padding:14px 18px; border-bottom:1px solid var(--border);
    display:flex; align-items:center; gap:12px;
    background:var(--surface-2); flex-shrink:0;
}
.ws-modal-title { font-size:14px; font-weight:700; color:var(--text); flex:1; }
.ws-device-bar {
    display:flex; align-items:center; gap:4px;
    background:var(--surface); border:1px solid var(--border);
    border-radius:10px; padding:3px;
}
.ws-dev-btn {
    display:flex; align-items:center; gap:5px;
    padding:5px 11px; border-radius:7px; border:none; background:none;
    cursor:pointer; font-size:12px; font-weight:600; color:var(--text-muted);
    transition:.13s; white-space:nowrap;
}
.ws-dev-btn:hover { background:var(--surface-2); color:var(--text); }
.ws-dev-btn.active { background:#6366f1; color:#fff; }
.ws-modal-close {
    width:30px;height:30px;border-radius:8px;border:none;
    background:var(--surface-hover);color:var(--text);cursor:pointer;
    font-size:18px;line-height:1;display:flex;align-items:center;justify-content:center;
    transition:.13s;
}
.ws-modal-close:hover { background:#ef4444; color:#fff; }
.ws-resp-badge {
    font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;
    background:rgba(16,185,129,.12);color:#10b981;
    display:flex;align-items:center;gap:4px;
}
.ws-preview-viewport {
    flex:1; overflow:hidden; background:#f0f0f0;
    display:flex; align-items:center; justify-content:center;
    padding:16px; min-height:0;
}
.ws-iframe-wrap {
    background:#fff; border-radius:10px;
    box-shadow:0 8px 32px rgba(0,0,0,.18);
    overflow:hidden; transition:all .3s ease;
    height:100%; max-height:calc(90vh - 130px);
    position:relative;
}
.ws-iframe-wrap.device-desktop { width:100%; }
.ws-iframe-wrap.device-tablet  { width:768px; max-width:100%; }
.ws-iframe-wrap.device-mobile  { width:390px; max-width:100%; }
.ws-preview-frame { width:100%; height:100%; border:none; display:block; min-height:450px; }

.ws-iframe-wrap::before {
    content:''; display:block; height:4px;
    background:linear-gradient(90deg,#6366f1,#8b5cf6,#ec4899);
}
</style>

{{-- ═══════════════════════════════════════════════════════
     PREVIEW MODAL
═══════════════════════════════════════════════════════ --}}
<div class="ws-modal-backdrop" id="ws-preview-modal" onclick="if(event.target===this)closePreview()">
    <div class="ws-modal">
        <div class="ws-modal-header">
            <span id="ws-preview-label" class="ws-modal-title">Widget Preview</span>

            <span class="ws-resp-badge">
                ✓ Fully Responsive
            </span>

            <div class="ws-device-bar">
                <button class="ws-dev-btn active" id="dev-desktop" onclick="setDevice('desktop')" title="Desktop (1200px)">
                    🖥️ Desktop
                </button>
                <button class="ws-dev-btn" id="dev-tablet" onclick="setDevice('tablet')" title="Tablet (768px)">
                    📱 Tablet
                </button>
                <button class="ws-dev-btn" id="dev-mobile" onclick="setDevice('mobile')" title="Mobile (390px)">
                    📲 Mobile
                </button>
            </div>

            <button class="ws-modal-close" onclick="closePreview()" title="Close">×</button>
        </div>
        <div class="ws-preview-viewport">
            <div class="ws-iframe-wrap device-desktop" id="ws-iframe-wrap">
                <iframe class="ws-preview-frame" id="ws-preview-frame" src="about:blank"></iframe>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="ws-tabs">
    <button class="ws-tab active" data-tab="my-widgets" id="tab-my">
        🧩 My Widgets <span style="font-size:12px;background:rgba(99,102,241,.12);color:#6366f1;padding:1px 7px;border-radius:6px;margin-left:4px">{{ $customWidgets->count() }}</span>
    </button>
    <button class="ws-tab" data-tab="studio" id="tab-studio">
        🎨 WidgetStudio Library <span style="font-size:12px;background:var(--surface-2);color:var(--text-muted);padding:1px 7px;border-radius:6px;margin-left:4px">{{ collect($allWidgets)->flatten(1)->count() }}</span>
    </button>
</div>

{{-- ══════════════════════════════════════════════════════
     TAB 1: MY WIDGETS
══════════════════════════════════════════════════════ --}}
<div id="pane-my-widgets">
    @if ($customWidgets->isEmpty())
        <div class="ws-empty">
            <span class="ws-empty-icon">🧩</span>
            <h3>No custom widgets yet</h3>
            <p>Browse the <strong>WidgetStudio Library</strong> tab to add ready-made widgets, or click <strong>+ New Widget</strong> to build one from scratch.</p>
            <button style="margin-top:16px;padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;cursor:pointer;font-weight:700" onclick="switchTab('studio')">
                Browse WidgetStudio →
            </button>
        </div>
    @else
        <div class="mw-grid">
            @foreach ($customWidgets as $w)
                @php
                    $catIcons = ['hero'=>'🏛️','school'=>'🎓','content'=>'📝','media'=>'🖼️','forms'=>'📩','dynamic'=>'⚡','general'=>'🧩','custom'=>'✨'];
                    $icon = $catIcons[$w->category] ?? '🧩';
                @endphp
                <div class="mw-card">
                    <div class="mw-card-header">
                        <div class="mw-card-icon">{{ $icon }}</div>
                        <div>
                            <div class="mw-card-title">{{ $w->name }}</div>
                            <div class="mw-card-slug">{{ $w->slug }}</div>
                        </div>
                    </div>
                    <div class="mw-card-meta">
                        <span class="mw-badge">{{ $w->category }}</span>
                        <span class="mw-badge">{{ count($w->fields ?? []) }} fields</span>
                        <span class="mw-badge {{ $w->is_active ? 'active' : 'inactive' }}">
                            {{ $w->is_active ? '✓ Active' : '✗ Inactive' }}
                        </span>
                    </div>
                    <div class="mw-card-actions">
                        <button class="mw-act-btn edit" style="border:1px solid var(--border)" onclick="openPreview('{{ $w->slug }}', '{{ addslashes($w->name) }}')">👁 Preview</button>
                        <a href="{{ route('admin.widgets.edit', $w->id) }}" class="mw-act-btn edit">✎ Edit</a>
                        <form method="POST" action="{{ route('admin.widgets.destroy', $w->id) }}" style="flex:1" onsubmit="return confirm('Delete this widget?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="mw-act-btn del" style="width:100%">🗑</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════
     TAB 2: WIDGETSTUDIO LIBRARY
══════════════════════════════════════════════════════ --}}
<div id="pane-studio" style="display:none">

    {{-- Top Search & Filter Bar --}}
    <div class="ws-top-bar">
        <div class="ws-search-wrap">
            <span class="ws-search-icon">🔍</span>
            <input type="text" class="ws-search" id="ws-search" placeholder="Search widgets — Pricing, Hero, Gallery, FAQ Accordion, Timeline..." oninput="applyFilters()">
        </div>

        {{-- Type Filter Pills --}}
        <div class="ws-filter-pills">
            <button class="ws-pill-btn active" data-filter="all" onclick="filterType('all', this)">
                🗂️ All Widgets ({{ collect($allWidgets)->flatten(1)->count() }})
            </button>
            <button class="ws-pill-btn" data-filter="pro" onclick="filterType('pro', this)">
                👑 PRO Suite (10)
            </button>
            <button class="ws-pill-btn" data-filter="live" onclick="filterType('live', this)">
                ⚡ LIVE Widgets
            </button>
        </div>
    </div>

    <div class="ws-wrap">
        {{-- Sidebar: Category Filter --}}
        <aside class="ws-sidebar">
            <h4>Categories</h4>
            <button class="ws-cat-btn active" data-cat="all" onclick="filterCat('all', this)">
                🗂️ All Categories
                <span class="cnt">{{ collect($allWidgets)->flatten(1)->count() }}</span>
            </button>
            @foreach ($allWidgets as $cat => $catWidgets)
                @php $meta = $categoryMeta[$cat] ?? ['icon'=>'🧩','label'=>ucfirst($cat)]; @endphp
                <button class="ws-cat-btn" data-cat="{{ $cat }}" onclick="filterCat('{{ $cat }}', this)">
                    {{ $meta['icon'] }} {{ $meta['label'] }}
                    <span class="cnt">{{ $catWidgets->count() }}</span>
                </button>
            @endforeach

            <div style="margin-top:16px;padding:10px 12px;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:10px">
                <div style="font-size:11px;font-weight:700;color:#10b981;margin-bottom:6px">✅ Responsive</div>
                <div style="font-size:11px;color:var(--text-muted);line-height:1.5">All widgets use <code>site.css</code> with <strong>60+ media queries</strong> — Mobile, Tablet & Desktop ready.</div>
                <div style="display:flex;gap:4px;margin-top:8px;flex-wrap:wrap">
                    <span style="font-size:10px;background:rgba(99,102,241,.1);color:#6366f1;padding:2px 6px;border-radius:5px;font-weight:600">📲 320px+</span>
                    <span style="font-size:10px;background:rgba(99,102,241,.1);color:#6366f1;padding:2px 6px;border-radius:5px;font-weight:600">📱 768px+</span>
                    <span style="font-size:10px;background:rgba(99,102,241,.1);color:#6366f1;padding:2px 6px;border-radius:5px;font-weight:600">🖥️ 1200px+</span>
                </div>
            </div>
        </aside>

        {{-- Widget Cards --}}
        <div>
            @php
                $widgetIcons = [
                    'hero' => '🏛️', 'slider' => '🎠', 'announcement-bar' => '📢', 'breadcrumb' => '🗺️',
                    'heading' => '✍️', 'text' => '📄', 'html' => '💻', 'button' => '🔘', 'image' => '🖼️',
                    'stats' => '📊', 'achievements' => '🏆', 'leadership' => '👤', 'news' => '📰',
                    'gallery' => '📸', 'videos' => '🎬', 'instagram' => '📱', 'glimpses' => '🌟',
                    'testimonials' => '💬', 'testimonials-cards' => '🃏', 'video-testimonial' => '🎥',
                    'contact-form' => '📩', 'newsletter' => '✉️', 'admission-cta' => '🎓',
                    'facilities' => '🏫', 'campus' => '🏫', 'map' => '📍', 'life' => '🌱',
                    'academic-programs' => '📚', 'admissions-page' => '📋', 'admission-process' => '⚙️',
                    'job-listings' => '💼', 'mess-menu' => '🍽️', 'downloads' => '⬇️',
                    'quick-links' => '🔗', 'notice-board' => '📌', 'upcoming-events' => '📅',
                    'latest-posts' => '📰', 'floating-action' => '💡', 'dynamic' => '⚡',
                    'pro-pricing' => '💎', 'pro-accordion' => '❓', 'pro-countdown' => '⏳',
                    'pro-team' => '👥', 'pro-flipbox' => '🔄', 'pro-timeline' => '🚩',
                    'pro-progress' => '📈', 'pro-image-comparison' => '🔀',
                    'pro-tabs' => '🗂️', 'pro-dual-button' => '🔘',
                    'pro-audio-player' => '🎵', 'pro-chart' => '📊', 'pro-circle-menu' => '☸️',
                    'pro-data-table' => '📋', 'pro-event-calendar' => '📅', 'pro-whatsapp' => '💬',
                    'pro-zoom-meeting' => '🎥', 'pro-advanced-slider' => '🎠', 'pro-advanced-toggle' => '🔀',
                    'pro-protected-content' => '🔒', 'pro-creative-button' => '🎨', 'pro-fancy-text' => '✍️',
                    'pro-image-hotspot' => '📍', 'pro-image-morphing' => '💧', 'pro-motion-text' => '📜',
                    'pro-stacked-cards' => '🃏', 'pro-unfold-content' => '📖', 'pro-glass-morphism' => '✨',
                    'pro-content-ticker' => '⚡', 'pro-coupon-code' => '🎟️', 'pro-price-menu' => '🍽️',
                    'pro-reviews-ratings' => '⭐', 'pro-back-to-top' => '⬆️', 'pro-business-hours' => '🕒',
                    'pro-icon-box' => '📦', 'pro-image-accordion' => '🖼️', 'pro-image-box' => '📷',
                    'pro-page-list' => '📑', 'pro-social-icons' => '🌐', 'pro-drop-caps' => '🔤',
                    'pro-fun-fact' => '📊', 'pro-lottie' => '🎨', 'pro-mega-menu' => '🗂️',
                    'pro-header-info' => '📞', 'pro-header-offcanvas' => '☰', 'pro-client-logo' => '🏛️',
                    'pro-social-share' => '📲', 'pro-category-list' => '📂', 'pro-post-list' => '📰',
                ];
                $catColors = [
                    'hero'=>'#6366f1','school'=>'#10b981','content'=>'#f59e0b',
                    'media'=>'#ec4899','forms'=>'#3b82f6','dynamic'=>'#f97316',
                    'general'=>'#8b5cf6','pro-general'=>'#c79a3b','pro-advanced'=>'#e0b94e',
                    'pro-creative'=>'#ec4899','pro-features'=>'#f59e0b','pro-social'=>'#10b981',
                    'custom'=>'#06b6d4',
                ];
            @endphp

            @foreach ($allWidgets as $cat => $catWidgets)
                @php $meta = $categoryMeta[$cat] ?? ['icon'=>'🧩','label'=>ucfirst($cat)]; @endphp
                <div class="ws-cat-section" data-cat="{{ $cat }}" style="margin-bottom:28px">
                    <div class="ws-section-head">{{ $meta['icon'] }} {{ $meta['label'] }} ({{ $catWidgets->count() }})</div>
                    <div class="ws-grid" id="grid-{{ $cat }}">
                        @foreach ($catWidgets as $w)
                            @php
                                $icon = $widgetIcons[$w['type']] ?? '🧩';
                                $color = $catColors[$cat] ?? '#6366f1';
                                $isPro = str_starts_with($w['type'], 'pro-') || str_starts_with($cat, 'pro-');
                            @endphp
                            <div class="ws-card {{ $w['is_seeded'] ? 'is-seeded' : '' }}"
                                 data-name="{{ strtolower($w['label']) }}"
                                 data-cat="{{ $cat }}"
                                 data-pro="{{ $isPro ? '1' : '0' }}"
                                 data-live="{{ $w['is_dynamic'] ? '1' : '0' }}">
                                <div class="ws-card-preview" style="background:linear-gradient(135deg,{{ $color }}18,{{ $color }}06)">
                                    <span style="font-size:38px">{{ $icon }}</span>
                                    @if ($isPro)
                                        <span class="ws-pro-badge">👑 PRO</span>
                                    @endif
                                    @if ($w['is_dynamic'])
                                        <span class="ws-dyn-badge">⚡ LIVE</span>
                                    @endif
                                    @if ($w['is_seeded'])
                                        <span class="ws-added-badge">✓ Added</span>
                                    @endif
                                </div>
                                <div class="ws-card-body">
                                    <div class="ws-card-name">{{ $w['label'] }}</div>
                                    <div class="ws-card-cat">
                                        <span class="dot" style="background:{{ $color }}"></span>
                                        {{ $meta['label'] }}
                                        @if ($w['field_count'] > 0)
                                            <span style="margin-left:auto;font-size:11px;color:var(--text-muted)">{{ $w['field_count'] }} settings</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ws-card-actions">
                                    <button class="ws-preview-btn" onclick="openPreview('{{ $w['type'] }}', '{{ addslashes($w['label']) }}')" title="Live Preview">👁</button>

                                    @if ($w['is_seeded'])
                                        <a href="{{ route('admin.widgets.edit', $w['seeded_id']) }}" class="ws-edit-btn" style="flex:1;text-align:center">✎ Customize</a>
                                    @else
                                        <form method="POST" action="{{ route('admin.widgets.seed', $w['type']) }}" style="flex:1">
                                            @csrf
                                            <button type="submit" class="ws-seed-btn" style="width:100%">
                                                + Add to My Widgets
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div id="ws-no-results" style="display:none" class="ws-empty">
                <span class="ws-empty-icon">🔍</span>
                <h3>No widgets found</h3>
                <p>Try selecting a different filter or search query.</p>
            </div>
        </div>
    </div>
</div>

<script>
let currentCategory = 'all';
let currentTypeFilter = 'all';

// ── Tab switching ──
function switchTab(tab) {
    document.getElementById('pane-my-widgets').style.display = tab === 'my-widgets' ? '' : 'none';
    document.getElementById('pane-studio').style.display = tab === 'studio' ? '' : 'none';
    document.getElementById('tab-my').classList.toggle('active', tab === 'my-widgets');
    document.getElementById('tab-studio').classList.toggle('active', tab === 'studio');
}
document.querySelectorAll('.ws-tab[data-tab]').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.dataset.tab));
});

// ── Category Filter ──
function filterCat(cat, btn) {
    currentCategory = cat;
    document.querySelectorAll('.ws-cat-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    applyFilters();
}

// ── Type Filter (All / PRO / LIVE) ──
function filterType(type, btn) {
    currentTypeFilter = type;
    document.querySelectorAll('.ws-pill-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    applyFilters();
}

// ── Master Filter Engine (Search + Category + Type) ──
function applyFilters() {
    const q = (document.getElementById('ws-search').value || '').toLowerCase().trim();
    let visibleCount = 0;

    document.querySelectorAll('.ws-card').forEach(card => {
        const nameMatch = !q || (card.dataset.name || '').includes(q);
        const catMatch  = currentCategory === 'all' || card.dataset.cat === currentCategory;

        let typeMatch = true;
        if (currentTypeFilter === 'pro') {
            typeMatch = card.dataset.pro === '1';
        } else if (currentTypeFilter === 'live') {
            typeMatch = card.dataset.live === '1';
        }

        const visible = nameMatch && catMatch && typeMatch;
        card.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });

    // Hide empty category sections
    document.querySelectorAll('.ws-cat-section').forEach(sec => {
        const visibleCards = [...sec.querySelectorAll('.ws-card')].some(c => c.style.display !== 'none');
        sec.style.display = visibleCards ? '' : 'none';
    });

    document.getElementById('ws-no-results').style.display = visibleCount === 0 ? '' : 'none';
}

// ── Preview Modal ──
const previewRoute = '{{ url("admin/widgets/preview") }}';

function openPreview(type, label) {
    const modal = document.getElementById('ws-preview-modal');
    const frame = document.getElementById('ws-preview-frame');
    document.getElementById('ws-preview-label').textContent = '👁 Preview — ' + label;
    frame.src = previewRoute + '/' + encodeURIComponent(type);
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    setDevice('desktop');
}

function closePreview() {
    document.getElementById('ws-preview-modal').classList.remove('open');
    document.getElementById('ws-preview-frame').src = 'about:blank';
    document.body.style.overflow = '';
}

// ── Device Switcher ──
function setDevice(device) {
    const wrap = document.getElementById('ws-iframe-wrap');
    wrap.className = 'ws-iframe-wrap device-' + device;
    ['desktop','tablet','mobile'].forEach(d => {
        document.getElementById('dev-' + d).classList.toggle('active', d === device);
    });
    const frame = document.getElementById('ws-preview-frame');
    const src = frame.src;
    if (src && src !== 'about:blank') {
        frame.src = src;
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePreview();
});

@if ($customWidgets->isEmpty())
    switchTab('studio');
@endif
</script>

@endsection
