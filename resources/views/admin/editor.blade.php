<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Page Builder — {{ $page->title }} · {{ config('app.name', 'CMS') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ─── Modern SaaS Design System Tokens ────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0b192c; --navy-2:#1e2e46; --navy-3:#2e4364;
  --gold:#d97706; --gold-2:#f59e0b; --gold-soft:rgba(245,158,11,0.12);
  --blue:#2563eb; --blue-2:#1d4ed8; --blue-soft:rgba(37,99,235,0.08);
  --green:#16a34a; --green-soft:rgba(22,163,74,0.1);
  --red:#dc2626; --red-soft:rgba(220,38,38,0.1);
  --purple:#7c3aed; --purple-soft:rgba(124,58,237,0.1);
  --bg:#f8fafc; --surface:#ffffff; --surface-2:#f1f5f9;
  --border:#e2e8f0; --border-dark:#cbd5e1;
  --text:#0f172a; --muted:#64748b; --faint:#94a3b8;
  --radius:12px; --radius-sm:8px; --radius-lg:18px;
  --shadow-sm:0 1px 3px rgba(0,0,0,.06),0 1px 2px rgba(0,0,0,.04);
  --shadow:0 4px 20px -4px rgba(11,25,44,.08);
  --shadow-lg:0 12px 36px -6px rgba(11,25,44,.14),0 4px 12px -2px rgba(0,0,0,.05);
  --ease:cubic-bezier(0.16, 1, 0.3, 1);
  --ff:'Plus Jakarta Sans', system-ui, sans-serif;
  --mono:'JetBrains Mono', monospace;
}
html,body{height:100%;overflow:hidden}
body{font:13.5px/1.5 var(--ff);color:var(--text);background:var(--bg);display:flex;flex-direction:column}

/* ─── Top Bar (Ultra-Premium Frosted Glass) ───────────────────────── */
.topbar{
  display:flex;align-items:center;gap:12px;
  height:58px;padding:0 20px;
  background:rgba(11,25,44,0.98);
  backdrop-filter:blur(16px);
  border-bottom:1px solid rgba(255,255,255,0.1);
  flex-shrink:0;z-index:100;
  box-shadow:0 4px 20px rgba(0,0,0,0.2);
}
.topbar__logo{
  font-size:13.5px;font-weight:800;color:#fff;
  display:flex;align-items:center;gap:9px;
  letter-spacing:-0.01em;
  white-space:nowrap;
}
.topbar__logo-badge{
  background:linear-gradient(135deg,var(--gold),#b45309);
  color:#fff;font-size:10px;font-weight:800;padding:2px 7px;
  border-radius:6px;letter-spacing:0.04em;text-transform:uppercase;
}
.topbar__sep{color:rgba(255,255,255,0.2);font-weight:300}
.topbar__breadcrumb{
  display:flex;align-items:center;gap:8px;
  font-size:12.5px;color:rgba(255,255,255,0.6);
  white-space:nowrap;overflow:hidden;
}
.topbar__breadcrumb a{color:rgba(255,255,255,0.6);text-decoration:none;transition:color .15s}
.topbar__breadcrumb a:hover{color:#fff}
.topbar__page-title{
  color:#fff;font-weight:700;background:rgba(255,255,255,0.1);
  padding:3px 10px;border-radius:6px;border:1px solid rgba(255,255,255,0.15);
  max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
}
.spacer{flex:1}

.topbar__status{
  font-size:12px;font-weight:700;
  padding:4px 12px;border-radius:999px;
  transition:all .3s var(--ease);
  display:inline-flex;align-items:center;gap:6px;
}
.topbar__status.ok{color:#4ade80;background:rgba(74,222,128,0.15);border:1px solid rgba(74,222,128,0.3)}
.topbar__status.err{color:#f87171;background:rgba(248,113,113,0.15);border:1px solid rgba(248,113,113,0.3)}
.topbar__status.saving{color:#facc15;background:rgba(250,204,21,0.15);border:1px solid rgba(250,204,21,0.3)}

.tb-btn{
  display:inline-flex;align-items:center;gap:6px;
  height:35px;padding:0 14px;
  font:600 12.5px/1 var(--ff);
  border-radius:var(--radius-sm);
  border:1px solid rgba(255,255,255,0.14);
  background:rgba(255,255,255,0.08);
  color:rgba(255,255,255,0.9);
  cursor:pointer;white-space:nowrap;
  text-decoration:none;
  transition:all .2s var(--ease);
}
.tb-btn:hover{background:rgba(255,255,255,0.16);color:#fff;border-color:rgba(255,255,255,0.25);transform:translateY(-1px)}
.tb-btn.primary{
  background:linear-gradient(135deg,#3b82f6,#2563eb);
  border-color:#3b82f6;color:#fff;
  box-shadow:0 4px 14px rgba(37,99,235,0.4);
}
.tb-btn.primary:hover{background:linear-gradient(135deg,#60a5fa,#3b82f6);box-shadow:0 6px 18px rgba(37,99,235,0.5)}
.tb-btn.gold{
  background:linear-gradient(135deg,var(--gold-2),var(--gold));
  border-color:var(--gold);color:#ffffff;
  font-weight:700;
  box-shadow:0 4px 14px rgba(217,119,6,0.45);
}
.tb-btn.gold:hover{background:linear-gradient(135deg,#fbbf24,var(--gold-2));box-shadow:0 6px 20px rgba(217,119,6,0.6)}
.tb-btn svg{width:15px;height:15px}

/* ─── Body Layout ───────────────────────────────────────────────── */
.editor-body{
  display:flex;flex:1;overflow:hidden;position:relative;
}

/* ─── Canvas Panel (Blueprint Grid Background) ─────────────────── */
.canvas-panel{
  flex:1;overflow-y:auto;overflow-x:hidden;
  padding:24px 3vw 80px;
  scroll-behavior:smooth;
  background-color: #f8fafc;
  background-image: radial-gradient(#cbd5e1 1.2px, transparent 1.2px);
  background-size: 24px 24px;
}
.canvas-container{
  max-width: 1120px;
  margin: 0 auto;
}

/* ─── Page Info Header Banner ───────────────────────────────────── */
.canvas-page-banner{
  background:#ffffff;
  border:1px solid #e2e8f0;
  border-radius:var(--radius);
  padding:14px 20px;
  margin-bottom:20px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  box-shadow:var(--shadow-sm);
  flex-wrap:wrap;
  gap:12px;
}
.cpb-info{
  display:flex;
  align-items:center;
  gap:12px;
}
.cpb-icon{
  width:38px;
  height:38px;
  border-radius:10px;
  background:var(--blue-soft);
  color:var(--blue);
  display:grid;
  place-items:center;
  font-size:18px;
}
.cpb-title{
  font-size:15px;
  font-weight:800;
  color:#0f172a;
}
.cpb-slug{
  font-size:12px;
  color:#64748b;
  font-family:var(--mono);
}
.cpb-actions{
  display:flex;
  align-items:center;
  gap:10px;
}
.cpb-badge{
  font-size:11px;
  font-weight:700;
  padding:3px 10px;
  border-radius:999px;
  background:#dcfce7;
  color:#166534;
}

/* ─── Split Screen Preview Panel ────────────────────────────────── */
.preview-panel{
  width:0;flex-shrink:0;overflow:hidden;
  border-left:1px solid var(--border);
  background:#0f172a;
  transition:width .35s var(--ease);
  display:flex;flex-direction:column;
  box-shadow:-6px 0 24px rgba(0,0,0,0.15);
  z-index:30;
}
.preview-panel.open{width:560px}
.preview-panel__head{
  display:flex;align-items:center;justify-content:space-between;
  padding:0 16px;height:48px;
  border-bottom:1px solid rgba(255,255,255,0.1);
  background:rgba(15,23,42,0.95);flex-shrink:0;
  font-size:12px;font-weight:700;color:rgba(255,255,255,0.7);
  letter-spacing:.04em;
}
.pv-tabs{display:flex;background:rgba(255,255,255,0.08);padding:3px;border-radius:8px;gap:2px}
.pv-tab{
  padding:5px 12px;border-radius:6px;
  font-size:11.5px;font-weight:700;cursor:pointer;color:rgba(255,255,255,0.6);
  border:none;background:none;transition:all .15s;
}
.pv-tab.active{background:#ffffff;color:#0f172a;box-shadow:0 2px 6px rgba(0,0,0,0.2)}
#previewViewport{
  flex:1;overflow:auto;display:flex;justify-content:center;
  background:#1e293b;padding:16px 0;
}
#previewFrame{
  border:none;background:#fff;border-radius:12px;
  box-shadow:0 20px 50px rgba(0,0,0,0.4);
  transition:width .3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* ─── Empty State ────────────────────────────────────────────────── */
.empty-state{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:16px;padding:80px 40px;text-align:center;
  background:rgba(255,255,255,0.85);backdrop-filter:blur(8px);
  border:2px dashed var(--border-dark);
  border-radius:var(--radius-lg);color:var(--muted);
  box-shadow:var(--shadow);
  animation:fadeIn .4s var(--ease);
}
.empty-state__icon{font-size:3.5rem;opacity:.5}
.empty-state__title{font-size:20px;font-weight:800;color:var(--text)}
.empty-state__sub{font-size:14px;max-width:360px;line-height:1.6}
.empty-state .add-btn{
  display:inline-flex;align-items:center;gap:8px;
  padding:12px 26px;border-radius:999px;
  background:linear-gradient(135deg,var(--blue),var(--blue-2));
  color:#fff;font-weight:700;font-size:13.5px;border:none;cursor:pointer;
  box-shadow:0 6px 18px rgba(37,99,235,.4);
  transition:transform .2s,box-shadow .2s;
}
.empty-state .add-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(37,99,235,.5)}

/* ─── Section Card ───────────────────────────────────────────────── */
.section-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-lg);margin-bottom:22px;
  overflow:hidden;
  box-shadow:var(--shadow);
  animation:slideIn .25s var(--ease);
  transition:box-shadow .2s,border-color .2s;
}
.section-card:hover{box-shadow:var(--shadow-lg);border-color:#cbd5e1}
.section-head{
  display:flex;align-items:center;gap:10px;flex-wrap:wrap;
  padding:12px 18px;
  background:linear-gradient(135deg,#f8fafc,#f1f5f9);
  border-bottom:1px solid var(--border);
}
.section-label{
  display:flex;align-items:center;gap:6px;
  font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;
  background:var(--navy);color:#fff;
  padding:4px 12px;border-radius:999px;
}
.section-body{padding:16px}
.section-select{
  font:600 12px var(--ff);padding:6px 10px;
  border:1px solid var(--border-dark);border-radius:var(--radius-sm);
  background:#fff;color:var(--text);cursor:pointer;
  outline:none;transition:border-color .15s,box-shadow .15s;
}
.section-select:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-soft)}

.color-bar{height:4px;transition:background-color .2s}
.section-strip-section{background:linear-gradient(90deg,#3b82f6,#6366f1)}
.section-strip-alt{background:linear-gradient(90deg,#64748b,#94a3b8)}
.section-strip-navy{background:linear-gradient(90deg,#0f172a,#1e293b)}
.section-strip-flush{background:linear-gradient(90deg,#16a34a,#059669)}
.section-strip-hero{background:linear-gradient(90deg,#8b5cf6,#6d28d9)}
.section-strip-custom{background:var(--gold)}

.color-swatch-btn{width:16px;height:16px;border-radius:50%;border:1px solid rgba(0,0,0,.15);cursor:pointer;flex-shrink:0;padding:0;transition:transform .15s}
.color-swatch-btn:hover{transform:scale(1.35)}

/* ─── Row Card ───────────────────────────────────────────────────── */
.row-card{
  border:1px solid var(--border);border-radius:var(--radius);
  margin-bottom:12px;overflow:hidden;
  background:#ffffff;
  box-shadow:var(--shadow-sm);
  transition:border-color .15s;
}
.row-card:hover{border-color:var(--border-dark)}
.row-head{
  display:flex;align-items:center;gap:8px;flex-wrap:wrap;
  padding:8px 12px;background:#f8fafc;
  border-bottom:1px solid var(--border);
}
.row-label{
  font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;
  color:var(--muted);padding:3px 10px;
  border:1px solid var(--border);border-radius:999px;background:#fff;
}
.row-body{display:flex;gap:12px;padding:12px;flex-wrap:wrap;align-items:flex-start}

/* ─── Column Card ─────────────────────────────────────────────────── */
.col-card{
  flex:1 1 200px;min-width:200px;
  background:#f8fafc;border:1px dashed var(--border-dark);
  border-radius:var(--radius);padding:10px;
  transition:border-color .2s,background .2s,box-shadow .2s;
  min-height:90px;position:relative;
}
.col-card.drag-over{
  border-color:var(--blue);background:var(--blue-soft);
  box-shadow:0 0 0 3px rgba(37,99,235,.18);
}
.col-head{
  display:flex;align-items:center;gap:6px;flex-wrap:wrap;
  margin-bottom:10px;
}
.col-label{
  font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;
  color:var(--faint);
}
.col-width-sel{
  font-size:11.5px;font-weight:700;padding:3px 8px;border:1px solid var(--border);
  border-radius:6px;background:#fff;color:var(--muted);cursor:pointer;outline:none;
}
.col-width-sel:focus{border-color:var(--blue)}

/* ─── Widget Card ─────────────────────────────────────────────────── */
.widget-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-sm);margin-bottom:10px;
  overflow:hidden;
  box-shadow:var(--shadow-sm);
  transition:box-shadow .15s,transform .15s,border-color .15s;
  animation:slideIn .2s var(--ease);
}
.widget-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08);border-color:#cbd5e1}
.widget-card.dragging{opacity:.4;transform:scale(.97)}
.widget-head{
  display:flex;align-items:center;gap:8px;
  padding:10px 12px;
  background:linear-gradient(135deg,#ffffff,#f8fafc);
  border-bottom:1px solid #e2e8f0;
  cursor:default;
}
.widget-handle{
  cursor:grab;color:var(--faint);font-size:16px;
  line-height:1;user-select:none;flex-shrink:0;
  transition:color .15s;
}
.widget-handle:hover{color:var(--text)}
.widget-handle:active{cursor:grabbing}
.widget-type-badge{
  flex:1;display:flex;align-items:center;gap:8px;
  font-size:13px;font-weight:800;color:var(--text);
}
.widget-type-dot{
  width:10px;height:10px;border-radius:50%;flex-shrink:0;
}
.widget-settings-panel{
  padding:18px;background:#f8fafc;border-top:1px solid var(--border);
  display:none;
}
.widget-settings-panel.open{display:block}

/* ─── Rich Visual Widget Preview Card Inside Canvas ──────────────── */
.widget-preview-box{
  padding:14px 16px;
  background:#ffffff;
  border-top:1px solid #f1f5f9;
}
.wpb-title{
  font-size:13px;
  font-weight:700;
  color:#0f172a;
  margin-bottom:8px;
  display:flex;
  align-items:center;
  gap:6px;
}
.wpb-desc{
  font-size:12px;
  color:#64748b;
  line-height:1.5;
  margin-bottom:10px;
}
.wpb-galleries-grid{
  display:flex;
  flex-wrap:wrap;
  gap:6px;
  margin-bottom:12px;
}
.wpb-pill{
  font-size:11px;
  font-weight:700;
  padding:4px 10px;
  border-radius:999px;
  background:#f1f5f9;
  color:#334155;
  border:1px solid #e2e8f0;
}
.wpb-pill.gold{
  background:#fef3c7;
  color:#b45309;
  border-color:#fde68a;
}
.wpb-thumbs{
  display:flex;
  gap:8px;
  overflow-x:auto;
  padding:6px 0;
  margin-bottom:10px;
}
.wpb-thumbs img{
  width:72px;
  height:54px;
  object-fit:cover;
  border-radius:6px;
  border:1px solid #cbd5e1;
  box-shadow:0 2px 6px rgba(0,0,0,0.06);
}
.wpb-footer{
  display:flex;
  align-items:center;
  justify-content:space-between;
  font-size:11.5px;
  color:#64748b;
  padding-top:10px;
  border-top:1px dashed #e2e8f0;
}
.wpb-link{
  color:var(--blue);
  font-weight:700;
  text-decoration:none;
}
.wpb-link:hover{
  text-decoration:underline;
}

/* ─── Settings Field Groups & Image Inputs ───────────────────────── */
.settings-section-divider{
  margin:16px 0 10px;
  padding-bottom:6px;
  border-bottom:2px solid #e2e8f0;
  font-size:12px;
  font-weight:800;
  text-transform:uppercase;
  letter-spacing:0.04em;
  color:var(--navy);
  display:flex;
  align-items:center;
  gap:6px;
}
.field{margin-bottom:12px}
.field__label{
  display:block;font-size:11.5px;font-weight:700;color:var(--muted);
  margin-bottom:4px;text-transform:capitalize;letter-spacing:.02em;
}
.field__input{
  width:100%;padding:8px 10px;
  font:13px var(--ff);color:var(--text);
  background:#fff;border:1px solid var(--border);
  border-radius:var(--radius-sm);outline:none;
  transition:border-color .15s,box-shadow .15s;
}
.field__input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-soft)}
.field__input.bad{border-color:var(--red);background:var(--red-soft)}
textarea.field__input{font-family:var(--mono);font-size:12px;resize:vertical;min-height:75px}

/* ─── Array Editor ────────────────────────────────────────────────── */
.arr{display:flex;flex-direction:column;gap:8px;margin-top:6px}
.arr-row{
  border:1px solid #cbd5e1;border-radius:var(--radius-sm);
  padding:10px;background:#fff;box-shadow:var(--shadow-sm);
}
.arr-row__head{
  display:flex;align-items:center;gap:6px;margin-bottom:8px;
  padding-bottom:6px;border-bottom:1px solid #f1f5f9;
}
.arr-row__head .idx-badge{
  font-size:10px;font-weight:800;color:var(--blue);background:var(--blue-soft);
  padding:2px 8px;border-radius:999px;
}
.arr-row__body{display:flex;flex-direction:column;gap:6px}
.subobj{padding-left:12px;border-left:2px solid var(--border-dark);display:flex;flex-direction:column;gap:6px;margin:6px 0}

/* ─── Checkbox ───────────────────────────────────────────────────── */
.check-wrap{
  display:inline-flex;align-items:center;gap:6px;
  font-size:11.5px;font-weight:600;color:var(--muted);cursor:pointer;
}
.check-wrap input[type=checkbox]{accent-color:var(--blue);cursor:pointer}

/* ─── Widget Add Dropdown ─────────────────────────────────────────── */
.widget-adder{margin-top:8px}
.widget-adder select{
  width:100%;padding:8px 12px;
  font:600 12.5px var(--ff);
  border:1px dashed var(--border-dark);border-radius:var(--radius-sm);
  background:#fff;color:var(--muted);cursor:pointer;
  outline:none;
  transition:border-color .15s,color .15s,background .15s;
}
.widget-adder select:focus{border-color:var(--blue);color:var(--text);background:var(--blue-soft)}
.widget-adder select:hover{border-color:var(--blue);color:var(--text)}

/* ─── Small Action Buttons ───────────────────────────────────────── */
.ic-btn{
  display:inline-flex;align-items:center;justify-content:center;
  height:28px;padding:0 8px;border-radius:6px;
  border:1px solid var(--border);background:#fff;
  color:var(--muted);cursor:pointer;font-size:11.5px;font-weight:700;
  transition:all .12s var(--ease);flex-shrink:0;
}
.ic-btn:hover{background:var(--bg);color:var(--text);border-color:var(--border-dark)}
.ic-btn.danger:hover{background:var(--red-soft);color:var(--red);border-color:#fca5a5}
.ic-btn.ghost{border-color:transparent;background:transparent}
.ic-btn.ghost:hover{background:var(--bg)}
.ic-btn.settings-toggle{
  padding:0 10px;font-size:11.5px;font-weight:700;
  color:var(--blue);border-color:rgba(37,99,235,.3);background:var(--blue-soft);
}
.ic-btn.settings-toggle:hover{background:rgba(37,99,235,.15);color:var(--blue-2)}
.ic-btn.settings-toggle.active{background:rgba(37,99,235,.2);color:var(--blue-2)}

/* ─── Animations ─────────────────────────────────────────────────── */
@keyframes slideIn{
  from{opacity:0;transform:translateY(8px)}
  to{opacity:1;transform:none}
}
@keyframes fadeIn{
  from{opacity:0}
  to{opacity:1}
}

/* ─── Widget Color Dots ──────────────────────────────────────────── */
.dot-hero{background:#8b5cf6}
.dot-text{background:#3b82f6}
.dot-image{background:#10b981}
.dot-gallery{background:#f59e0b}
.dot-form{background:#ef4444}
.dot-cta{background:#06b6d4}
.dot-default{background:#94a3b8}

/* ─── Scrollbar Global ───────────────────────────────────────────── */
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-thumb{background:var(--border-dark);border-radius:999px}
</style>
</head>
<body>

{{-- ─── TOPBAR ─────────────────────────────────────────────────────── --}}
<div class="topbar">
  <div class="topbar__logo">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/>
      <rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>
    </svg>
    <span>Page Builder</span>
    <span class="topbar__logo-badge">Pro 2.0</span>
  </div>

  <span class="topbar__sep">|</span>

  <div class="topbar__breadcrumb">
    <a href="/admin">Admin</a>
    <span class="topbar__sep">/</span>
    <a href="/admin/pages">Pages</a>
    <span class="topbar__sep">/</span>
    <span class="topbar__page-title" title="{{ $page->title }}">{{ $page->title }}</span>
  </div>

  <span class="spacer"></span>

  <span id="statusEl" class="topbar__status"></span>

  <button id="addSection" class="tb-btn" title="Add Section (Hotkey: A)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
    + Section
  </button>
  <button id="togglePreviewBtn" class="tb-btn" title="Toggle live responsive preview (Hotkey: P)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
    Live Preview
  </button>
  <a class="tb-btn" href="{{ $page->slug === 'home' ? url('/') : url('/'.$page->slug) }}" target="_blank" rel="noopener" title="View live published page">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    View Page
  </a>
  <button id="reloadBtn" class="tb-btn" title="Reload from server (Hotkey: R)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
  </button>
  <button id="saveBtn" class="tb-btn gold" title="Save Changes (Ctrl+S)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
    Save Changes
  </button>
</div>

{{-- ─── BODY ────────────────────────────────────────────────────────── --}}
<div class="editor-body">
  <div class="canvas-panel" id="canvasScroll">
    <div class="canvas-container" id="canvas"></div>
  </div>

  <div class="preview-panel" id="previewPanel">
    <div class="preview-panel__head">
      <div style="display:flex;align-items:center;gap:6px">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <span>Interactive Live Preview</span>
      </div>
      <div class="pv-tabs">
        <button class="pv-tab active" onclick="setPreviewWidth('100%')">Desktop</button>
        <button class="pv-tab" onclick="setPreviewWidth('768px')">Tablet</button>
        <button class="pv-tab" onclick="setPreviewWidth('375px')">Mobile</button>
      </div>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;position:relative">
      <div id="previewViewport">
        <iframe id="previewFrame" title="Live preview" style="width:100%;height:100%;border:none;background:#fff;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
window.__EDITOR__ = {
    palette: @json($palette),
    treeUrl: @json(url('/admin/pages/'.$page->id.'/tree')),
    previewUrl: @json(url('/admin/pages/'.$page->id.'/preview')),
    csrf: @json(csrf_token()),
    pageTitle: @json($page->title),
    pageSlug: @json($page->slug),
};
</script>
@verbatim
<script>
(function () {
'use strict';

/* ── Config & State ─────────────────────────────────────────────── */
const cfg   = window.__EDITOR__;
const state = { sections: [] };
let dragSrc = null, previewOn = false, _pvTimer = null;

/* ── DOM refs ───────────────────────────────────────────────────── */
const canvas       = document.getElementById('canvas');
const statusEl     = document.getElementById('statusEl');
const previewPanel = document.getElementById('previewPanel');
const previewFrame = document.getElementById('previewFrame');
const pvViewport   = document.getElementById('previewViewport');

/* ── Helpers ────────────────────────────────────────────────────── */
function el(tag, cls, txt) {
    const e = document.createElement(tag);
    if (cls) e.className = cls;
    if (txt != null) e.textContent = txt;
    return e;
}
function btn(label, cls, onClick, title) {
    const b = el('button', 'ic-btn ' + (cls || ''), label);
    b.type = 'button';
    if (title) b.title = title;
    b.addEventListener('click', onClick);
    return b;
}
function mkCheck(label, checked, onChange) {
    const wrap = el('label', 'check-wrap');
    const c = document.createElement('input');
    c.type = 'checkbox'; c.checked = !!checked;
    c.addEventListener('change', () => onChange(c.checked));
    wrap.appendChild(c);
    wrap.appendChild(document.createTextNode(' ' + label));
    return wrap;
}
function sel(options, current, onChange, cls) {
    const s = el('select', cls || 'section-select');
    options.forEach(([v, l]) => {
        const o = el('option', null, l); o.value = v;
        if (current === v) o.selected = true;
        s.appendChild(o);
    });
    s.addEventListener('change', () => onChange(s.value));
    return s;
}
function humanize(k) { return String(k).replace(/_/g,' ').replace(/\b\w/g,c=>c.toUpperCase()); }
function cloneEmpty(v) {
    if (typeof v === 'boolean') return false;
    if (typeof v === 'number')  return 0;
    if (Array.isArray(v)) return [];
    if (v !== null && typeof v === 'object') { const o={}; Object.keys(v).forEach(k=>o[k]=cloneEmpty(v[k])); return o; }
    return '/images/media/Dance_class.jpg';
}
function move(arr, i, dir) { const j=i+dir; if(j<0||j>=arr.length) return; [arr[i],arr[j]]=[arr[j],arr[i]]; }
const paletteFor = t => cfg.palette.find(p=>p.type===t);

/* ── Widget color dots ──────────────────────────────────────────── */
const widgetDotClass = type => {
    const t = (type||'').toLowerCase();
    if (/hero|banner|slider/.test(t)) return 'dot-hero';
    if (/text|rich|content|html|heading/.test(t)) return 'dot-text';
    if (/image|photo|media/.test(t)) return 'dot-image';
    if (/gallery|grid/.test(t)) return 'dot-gallery';
    if (/form|enquiry|contact/.test(t)) return 'dot-form';
    if (/cta|button/.test(t)) return 'dot-cta';
    return 'dot-default';
};
const sectionStripClass = type => {
    const t = type || 'section';
    return 'color-bar section-strip-' + t;
};

/* ── Hydrate defaults ───────────────────────────────────────────── */
function hydrateDefaults(w) {
    const p = paletteFor(w.type);
    const defs = (p && p.defaults) ? p.defaults : {};
    w.settings = w.settings || {};
    Object.keys(defs).forEach(k => { if (!(k in w.settings)) w.settings[k] = JSON.parse(JSON.stringify(defs[k])); });
}

/* ── Status ─────────────────────────────────────────────────────── */
const setStatus = (msg, kind) => {
    statusEl.textContent = msg;
    statusEl.className   = 'topbar__status ' + (kind||'');
};

/* ── Preview scheduling ─────────────────────────────────────────── */
const schedulePreview = () => { clearTimeout(_pvTimer); _pvTimer = setTimeout(updatePreview, 400); };

/* ── Drag-drop widget move ──────────────────────────────────────── */
function moveWidget(src, dest, destIdx) {
    const from = state.sections[src.si].rows[src.ri].columns[src.ci].widgets;
    const to   = state.sections[dest.si].rows[dest.ri].columns[dest.ci].widgets;
    if (!from||!to) return;
    const [w] = from.splice(src.wi, 1);
    let idx = destIdx;
    if (src.si===dest.si && src.ri===dest.ri && src.ci===dest.ci && src.wi<destIdx) idx = destIdx-1;
    to.splice(Math.max(0,idx), 0, w);
    render();
}

/* ── Field / Array editors ──────────────────────────────────────── */
function arrayEditor(arr, rerender) {
    const box = el('div','arr');
    arr.forEach((item, idx) => {
        const row = el('div','arr-row');
        const head = el('div','arr-row__head');
        head.appendChild(el('span','idx-badge','#'+(idx+1)));
        const spacer = el('span',null); spacer.style.flex='1'; head.appendChild(spacer);
        head.appendChild(btn('↑','ghost',()=>{ if(idx>0){[arr[idx-1],arr[idx]]=[arr[idx],arr[idx-1]]; rerender();} },'Move up'));
        head.appendChild(btn('↓','ghost',()=>{ if(idx<arr.length-1){[arr[idx+1],arr[idx]]=[arr[idx],arr[idx+1]]; rerender();} },'Move down'));
        head.appendChild(btn('✕','ghost danger',()=>{ arr.splice(idx,1); rerender(); },'Remove'));
        row.appendChild(head);
        const body = el('div','arr-row__body');
        if (item!==null && typeof item==='object' && !Array.isArray(item)) {
            Object.keys(item).forEach(k => fieldEditor(body,item,k,rerender));
        } else if (Array.isArray(item)) {
            body.appendChild(arrayEditor(item,rerender));
        } else {
            fieldEditor(body,arr,idx,rerender);
        }
        row.appendChild(body); box.appendChild(row);
    });
    const addBtn = btn('+ Add Image / Item','ic-btn',()=>{ arr.push(cloneEmpty(arr[0]??'/images/media/Dance_class.jpg')); rerender(); });
    addBtn.style.cssText='margin-top:6px;width:auto;padding:5px 14px;font-size:12px;background:#fff;color:var(--blue);border-color:var(--blue);font-weight:700;border-radius:6px;cursor:pointer;';
    box.appendChild(addBtn);
    return box;
}

function fieldEditor(wrap, obj, key, rerender, enums) {
    const val = obj[key];
    const field = el('div','field');
    if (typeof key !== 'number') field.appendChild(el('span','field__label', humanize(key)));

    if (enums && Array.isArray(enums[key]) && enums[key].length) {
        const s = el('select','field__input');
        enums[key].forEach(opt => {
            const o=el('option',null,opt); o.value=opt;
            if(String(val)===String(opt)) o.selected=true;
            s.appendChild(o);
        });
        s.addEventListener('change',()=>{ obj[key]=s.value; schedulePreview(); });
        field.appendChild(s); wrap.appendChild(field); return;
    }

    if (typeof val==='boolean') {
        const i=document.createElement('input'); i.type='checkbox'; i.checked=val; i.className='field__input';
        i.style.cssText='width:auto;padding:0;margin:4px 0;accent-color:var(--blue);';
        i.addEventListener('change',()=>{ obj[key]=i.checked; schedulePreview(); });
        field.appendChild(i);
    } else if (typeof val==='number') {
        const i=document.createElement('input'); i.type='number'; i.value=val; i.className='field__input';
        i.addEventListener('input',()=>{ obj[key]=i.value===''?null:Number(i.value); schedulePreview(); });
        field.appendChild(i);
    } else if (Array.isArray(val)) {
        field.appendChild(arrayEditor(val,rerender));
    } else if (val!==null && typeof val==='object') {
        const sub=el('div','subobj');
        Object.keys(val).forEach(k=>fieldEditor(sub,val,k,rerender));
        field.appendChild(sub);
    } else {
        const isImg = typeof val === 'string' && (/\.(jpg|jpeg|png|webp|svg|gif)$/i.test(val) || val.startsWith('/images/'));
        const long = /body|text|quote|desc|message|html|address|embed|content/i.test(String(key)) || (typeof val==='string' && val.length>80);

        if (isImg) {
            const rowBox = el('div', null);
            rowBox.style.cssText = 'display:flex;align-items:center;gap:10px';
            
            const thumb = el('img', null);
            thumb.src = val || '/images/media/Dance_class.jpg';
            thumb.style.cssText = 'width:44px;height:44px;object-fit:cover;border-radius:6px;border:1px solid #cbd5e1;background:#e2e8f0;flex-shrink:0';
            thumb.onerror = () => { thumb.style.display = 'none'; };
            
            const i = document.createElement('input');
            i.type = 'text';
            i.value = (val == null ? '' : val);
            i.placeholder = '/images/media/...';
            i.className = 'field__input';
            i.style.flex = '1';
            i.addEventListener('input', () => {
                obj[key] = i.value;
                thumb.src = i.value;
                thumb.style.display = 'block';
                schedulePreview();
            });
            
            rowBox.appendChild(thumb);
            rowBox.appendChild(i);
            field.appendChild(rowBox);
        } else {
            const i = document.createElement(long ? 'textarea' : 'input');
            if (long) i.rows = 3; else i.type = 'text';
            i.value = (val == null ? '' : val);
            i.className = 'field__input';
            i.addEventListener('input', () => { obj[key] = i.value; schedulePreview(); });
            field.appendChild(i);
        }
    }
    wrap.appendChild(field);
}

function settingsForm(widget) {
    const form = el('div','widget-settings-panel');
    function draw() {
        form.innerHTML='';
        const keys=Object.keys(widget.settings||{});
        if(!keys.length){ 
            form.innerHTML='<div style="font-size:12px;color:var(--muted);font-style:italic;padding:6px 0">No configurable settings for this widget.</div>'; 
            return; 
        }
        const enums=(paletteFor(widget.type)||{}).options||{};

        // If media-page, add nice section dividers
        if (widget.type === 'media-page') {
            const sectionsMap = [
                { divider: '🌟 Hero Banner Settings', keys: ['hero_eyebrow', 'hero_title', 'hero_subtitle'] },
                { divider: '💃 Dance & Music Gallery', keys: ['dance_music_title', 'dance_music_images'] },
                { divider: '⚽ Sports Arena Gallery', keys: ['sports_title', 'sports_images'] },
                { divider: '🎨 Arts & Craft Studio', keys: ['arts_craft_title', 'arts_craft_images'] },
                { divider: '🎈 Fun Activities Zone', keys: ['fun_activities_title', 'fun_activities_images'] },
                { divider: '📰 News Press Clippings & Slider', keys: ['news_title', 'news_images', 'autoplay', 'interval', 'animation_speed', 'pause_on_hover'] },
            ];

            sectionsMap.forEach(group => {
                const div = el('div', 'settings-section-divider', group.divider);
                form.appendChild(div);
                group.keys.forEach(k => {
                    if (k in widget.settings) {
                        fieldEditor(form, widget.settings, k, () => { draw(); schedulePreview(); }, enums);
                    }
                });
            });
        } else {
            keys.forEach(k=>fieldEditor(form,widget.settings,k,()=>{ draw(); schedulePreview(); },enums));
        }
    }
    draw();
    return form;
}

/* ── Rich Visual Widget Preview Renderer Inside Canvas ──────────── */
function createWidgetVisualPreview(widget, toggleCallback) {
    const box = el('div', 'widget-preview-box');
    const t = widget.type;

    if (t === 'media-page' || t === 'media-gallery' || t === 'news-slider') {
        const s = widget.settings || {};
        const danceCount = Array.isArray(s.dance_music_images) ? s.dance_music_images.length : 3;
        const sportsCount = Array.isArray(s.sports_images) ? s.sports_images.length : 3;
        const artsCount = Array.isArray(s.arts_craft_images) ? s.arts_craft_images.length : 3;
        const funCount = Array.isArray(s.fun_activities_images) ? s.fun_activities_images.length : 3;
        const newsCount = Array.isArray(s.news_images) ? s.news_images.length : 17;

        box.innerHTML = `
            <div class="wpb-title">📸 Life at Prayaag Campus Galleries &amp; News Slider Suite</div>
            <div class="wpb-desc">Renders complete campus performing arts studios, sports arenas, fine arts ateliers, splash pool activities, and news clippings carousel slider.</div>
            <div class="wpb-galleries-grid">
                <span class="wpb-pill">💃 Dance &amp; Music (${danceCount})</span>
                <span class="wpb-pill">⚽ Sports Arena (${sportsCount})</span>
                <span class="wpb-pill">🎨 Arts &amp; Craft (${artsCount})</span>
                <span class="wpb-pill">🎈 Fun Activities (${funCount})</span>
                <span class="wpb-pill gold">📰 News Slider (${newsCount} Clippings · 3s AutoPlay)</span>
            </div>
            <div class="wpb-thumbs">
                <img src="${(s.dance_music_images && s.dance_music_images[0]) || '/images/media/Dance_class.jpg'}" alt="Dance" title="Dance & Music">
                <img src="${(s.sports_images && s.sports_images[0]) || '/images/media/Football.jpg'}" alt="Sports" title="Sports">
                <img src="${(s.arts_craft_images && s.arts_craft_images[0]) || '/images/media/Painting-practice-prayaag-student.webp'}" alt="Arts" title="Arts & Craft">
                <img src="${(s.fun_activities_images && s.fun_activities_images[0]) || '/images/media/Junior-children-playing.webp'}" alt="Activities" title="Fun Activities">
                <img src="${(s.news_images && s.news_images[0]) || '/images/media/News-5.jpg'}" alt="News" title="News Slider">
            </div>
            <div class="wpb-footer">
                <button type="button" class="tb-btn primary edit-content-btn" style="height:28px;padding:0 12px;font-size:11.5px">
                    ✏️ Add / Edit Images &amp; Galleries
                </button>
                <a href="/media" target="_blank" class="wpb-link">👁️ View Public /media ↗</a>
            </div>
        `;

        const editBtn = box.querySelector('.edit-content-btn');
        if (editBtn && typeof toggleCallback === 'function') {
            editBtn.addEventListener('click', toggleCallback);
        }
    } else if (t === 'disclosure-page') {
        box.innerHTML = `
            <div class="wpb-title">📋 Mandatory Public Disclosure Hub</div>
            <div class="wpb-desc">Displays 36+ Government &amp; CBSE Compliance Certificates, Fee Structure 2026-27, Building Safety, Fire Safety with inline PDF previews.</div>
            <div class="wpb-footer">
                <span>📁 Hosted from Local Secure Storage</span>
                <a href="/disclosure" target="_blank" class="wpb-link">👁️ View Public /disclosure ↗</a>
            </div>
        `;
    } else if (t === 'hero') {
        box.innerHTML = `
            <div class="wpb-title">🌟 Hero Banner Showcase</div>
            <div class="wpb-desc">Full-screen visual hero with background video/image slider, CTA buttons, and entrance typography.</div>
        `;
    } else if (t === 'video-testimonial') {
        box.innerHTML = `
            <div class="wpb-title">🎥 Video Testimonials &amp; Parent Reviews</div>
            <div class="wpb-desc">Interactive video modal player showcasing student distinctions and parent video stories.</div>
        `;
    } else if (t === 'contact-us-page' || t === 'contact-form') {
        box.innerHTML = `
            <div class="wpb-title">📞 Contact Us &amp; Campus Map Suite</div>
            <div class="wpb-desc">Direct contact information, admission enquiry form, and interactive Google Map embed.</div>
        `;
    } else if (t === 'book-list') {
        box.innerHTML = `
            <div class="wpb-title">📚 Academic Book List &amp; Stationery Hub</div>
            <div class="wpb-desc">Class-by-class syllabus, NCERT prescribed booklists, and direct download links.</div>
        `;
    } else if (t === 'mess-menu') {
        box.innerHTML = `
            <div class="wpb-title">🍽️ Weekly Campus Mess Menu Digest</div>
            <div class="wpb-desc">Daily nutritious breakfast, lunch, and snack menus with PDF export option.</div>
        `;
    } else {
        const p = paletteFor(t);
        box.innerHTML = `
            <div class="wpb-title">📦 ${p ? p.label : t}</div>
            <div class="wpb-desc" style="margin-bottom:0">Click <strong>Settings ⚙</strong> above to customize options and layout values.</div>
        `;
    }

    return box;
}

/* ── RENDER ─────────────────────────────────────────────────────── */
function render() {
    canvas.innerHTML='';

    /* Add Page Meta Information Banner */
    const banner = el('div', 'canvas-page-banner');
    banner.innerHTML = `
        <div class="cpb-info">
            <div class="cpb-icon">📄</div>
            <div>
                <div class="cpb-title">${cfg.pageTitle}</div>
                <div class="cpb-slug">Slug: /${cfg.pageSlug}</div>
            </div>
        </div>
        <div class="cpb-actions">
            <span class="cpb-badge">● Published</span>
            <a href="/${cfg.pageSlug === 'home' ? '' : cfg.pageSlug}" target="_blank" class="tb-btn" style="height:30px;padding:0 10px;font-size:11.5px;color:#0f172a;background:#f1f5f9;border-color:#cbd5e1">
                View Public Page ↗
            </a>
        </div>
    `;
    canvas.appendChild(banner);

    if (!state.sections.length) {
        const empty=el('div','empty-state');
        empty.innerHTML=`
          <div class="empty-state__icon">📐</div>
          <div class="empty-state__title">Empty Page Canvas</div>
          <p class="empty-state__sub">Start designing your page by adding a new section, row, and widgets.</p>
          <button class="add-btn" id="emptyAddBtn">+ Add First Section</button>`;
        canvas.appendChild(empty);
        document.getElementById('emptyAddBtn').addEventListener('click',()=>{
            state.sections.push({type:'section',settings:{},rows:[]});
            render();
        });
        schedulePreview();
        return;
    }

    state.sections.forEach((s,si)=>{
        const card=el('div','section-card');

        /* color bar */
        const bar=el('div', sectionStripClass(s.type||'section'));
        if ((s.type==='custom' || s.type==='section') && s.settings && s.settings._custom_bg) {
            bar.style.backgroundColor = s.settings._custom_bg;
        }
        card.appendChild(bar);

        /* section head */
        const sHead=el('div','section-head');
        const lbl=el('div','section-label');
        lbl.innerHTML=`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="13" height="13"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg> Section ${si+1}`;
        sHead.appendChild(lbl);

        /* style select */
        const styleSel = sel([
            ['section','⬜ Default Container'],['alt','🔵 Soft Grey'],
            ['navy','🌑 Dark Navy'],['flush','🟢 Full Flush'],['hero','🟣 Gradient Hero'],
            ['custom','🎨 Custom Color']
        ], s.type||'section', v=>{
            s.type=v;
            if (v==='custom') {
                s.settings = s.settings || {};
                if (!s.settings._custom_bg) s.settings._custom_bg = '#f4f7fc';
            }
            render();
        }, 'section-select');
        sHead.appendChild(styleSel);

        /* Custom color picker inline control */
        if (s.type === 'custom' || (s.settings && s.settings._custom_bg)) {
            const colorWrap = el('div', 'custom-color-picker-wrap');
            colorWrap.style.cssText = 'display:inline-flex;align-items:center;gap:6px;background:#fff;border:1px solid var(--border-dark);border-radius:var(--radius-sm);padding:3px 8px;';

            const colorDot = el('input');
            colorDot.type = 'color';
            colorDot.value = (s.settings && s.settings._custom_bg) ? s.settings._custom_bg : '#f4f7fc';
            colorDot.title = 'Pick custom section background color';
            colorDot.style.cssText = 'width:22px;height:22px;border:none;border-radius:4px;cursor:pointer;padding:0;background:none;';

            const hexInput = el('input');
            hexInput.type = 'text';
            hexInput.value = (s.settings && s.settings._custom_bg) ? s.settings._custom_bg : '#f4f7fc';
            hexInput.placeholder = '#f4f7fc';
            hexInput.title = 'Hex color code';
            hexInput.style.cssText = 'width:68px;font:700 11px var(--mono);border:none;outline:none;background:transparent;color:var(--text);';

            const updateBg = (val) => {
                s.settings = s.settings || {};
                s.settings._custom_bg = val;
                if (bar) bar.style.backgroundColor = val;
                schedulePreview();
            };

            colorDot.addEventListener('input', () => {
                hexInput.value = colorDot.value;
                updateBg(colorDot.value);
            });

            hexInput.addEventListener('input', () => {
                const val = hexInput.value.trim();
                if (/^#([0-9A-F]{3}){1,2}$/i.test(val)) {
                    colorDot.value = val;
                }
                updateBg(val);
            });

            colorWrap.appendChild(colorDot);
            colorWrap.appendChild(hexInput);

            const swatches = ['#ffffff', '#f8fafc', '#eff6ff', '#fefce8', '#f0fdf4', '#fff1f2', '#0b192c'];
            swatches.forEach(hex => {
                const sw = el('button', 'color-swatch-btn');
                sw.type = 'button';
                sw.style.backgroundColor = hex;
                sw.title = 'Set ' + hex;
                sw.addEventListener('click', () => {
                    colorDot.value = hex;
                    hexInput.value = hex;
                    updateBg(hex);
                });
                colorWrap.appendChild(sw);
            });

            sHead.appendChild(colorWrap);

            if (bar && s.settings && s.settings._custom_bg) {
                bar.style.backgroundColor = s.settings._custom_bg;
            }
        }

        /* animation */
        sHead.appendChild(sel([
            ['','No Animation'],['fade-up','Fade ↑'],['fade-down','Fade ↓'],
            ['fade-left','Fade ←'],['fade-right','Fade →'],['zoom','Zoom In']
        ], (s.settings=s.settings||{})._animation||'', v=>{s.settings._animation=v; schedulePreview();}, 'section-select'));

        sHead.appendChild(mkCheck('Hide Mobile',  s.settings._hide_mobile,  v=>{s.settings._hide_mobile=v;schedulePreview();}));
        sHead.appendChild(mkCheck('Hide Desktop', s.settings._hide_desktop, v=>{s.settings._hide_desktop=v;schedulePreview();}));

        const sp=el('span'); sp.style.flex='1'; sHead.appendChild(sp);

        sHead.appendChild(btn('+ Row','ic-btn',()=>{(s.rows=s.rows||[]).push({settings:{},columns:[]});render();},'Add Row'));
        sHead.appendChild(btn('↑','ic-btn',()=>{move(state.sections,si,-1);render();},'Move Section Up'));
        sHead.appendChild(btn('↓','ic-btn',()=>{move(state.sections,si,1);render();},'Move Section Down'));
        sHead.appendChild(btn('🗑','ic-btn danger',()=>{if(confirm('Delete this section?')){state.sections.splice(si,1);render();}},'Delete Section'));
        card.appendChild(sHead);

        /* rows */
        const sBody=el('div','section-body');
        (s.rows||[]).forEach((r,ri)=>{
            const rCard=el('div','row-card');
            const rHead=el('div','row-head');
            rHead.appendChild(el('span','row-label','Row '+(ri+1)));
            const rSp=el('span'); rSp.style.flex='1'; rHead.appendChild(rSp);
            rHead.appendChild(btn('+ Column','ic-btn',()=>{(r.columns=r.columns||[]).push({width:12,settings:{},widgets:[]});render();},'Add Column'));
            rHead.appendChild(btn('↑','ic-btn',()=>{move(s.rows,ri,-1);render();},'Move Row Up'));
            rHead.appendChild(btn('↓','ic-btn',()=>{move(s.rows,ri,1);render();},'Move Row Down'));
            rHead.appendChild(btn('🗑','ic-btn danger',()=>{if(confirm('Delete row?')){s.rows.splice(ri,1);render();}},'Delete Row'));
            rCard.appendChild(rHead);

            const rBody=el('div','row-body');
            (r.columns||[]).forEach((c,ci)=>{
                const cCard=el('div','col-card');
                cCard.addEventListener('dragover',e=>{if(dragSrc){e.preventDefault();cCard.classList.add('drag-over');}});
                cCard.addEventListener('dragleave',()=>cCard.classList.remove('drag-over'));
                cCard.addEventListener('drop',e=>{
                    if(!dragSrc) return;
                    e.preventDefault(); cCard.classList.remove('drag-over');
                    moveWidget(dragSrc,{si,ri,ci},(c.widgets||[]).length);
                });

                const cHead=el('div','col-head');
                cHead.appendChild(el('span','col-label','Column'));
                const wSel=el('select','col-width-sel');
                wSel.title='Grid Width (1–12)';
                for(let i=1;i<=12;i++){const o=el('option',null,i+'/12 Width');o.value=i;if(Number(c.width)===i)o.selected=true;wSel.appendChild(o);}
                wSel.addEventListener('change',()=>{c.width=Number(wSel.value);});
                cHead.appendChild(wSel);
                const cSp=el('span'); cSp.style.flex='1'; cHead.appendChild(cSp);
                cHead.appendChild(btn('↑','ic-btn',()=>{move(r.columns,ci,-1);render();},'Move Column Left'));
                cHead.appendChild(btn('↓','ic-btn',()=>{move(r.columns,ci,1);render();},'Move Column Right'));
                cHead.appendChild(btn('✕','ic-btn danger',()=>{if(confirm('Delete column?')){r.columns.splice(ci,1);render();}},'Delete Column'));
                cCard.appendChild(cHead);

                /* widgets */
                (c.widgets||[]).forEach((wg,wi)=>{
                    hydrateDefaults(wg);
                    const wCard=el('div','widget-card');

                    wCard.addEventListener('dragover',e=>{if(dragSrc)e.preventDefault();});
                    wCard.addEventListener('drop',e=>{
                        if(!dragSrc) return;
                        e.preventDefault(); e.stopPropagation();
                        moveWidget(dragSrc,{si,ri,ci},wi);
                    });

                    const wHead=el('div','widget-head');

                    /* drag handle */
                    const handle=el('span','widget-handle','⠿');
                    handle.draggable=true; handle.title='Drag to reorder widget';
                    handle.addEventListener('dragstart',e=>{
                        dragSrc={si,ri,ci,wi};
                        wCard.classList.add('dragging');
                        if(e.dataTransfer){e.dataTransfer.effectAllowed='move';e.dataTransfer.setData('text/plain',String(wi));}
                    });
                    handle.addEventListener('dragend',()=>{dragSrc=null;wCard.classList.remove('dragging');});
                    wHead.appendChild(handle);

                    /* type badge */
                    const badge=el('div','widget-type-badge');
                    const dot=el('span','widget-type-dot '+widgetDotClass(wg.type));
                    badge.appendChild(dot);
                    const p=paletteFor(wg.type);
                    badge.appendChild(document.createTextNode(p?p.label:wg.type));
                    wHead.appendChild(badge);

                    /* settings toggle */
                    const form=settingsForm(wg);
                    const toggleBtn=btn('Settings ⚙','ic-btn settings-toggle',()=>{
                        const isOpen=form.classList.toggle('open');
                        toggleBtn.classList.toggle('active',isOpen);
                        toggleBtn.textContent=isOpen?'Close ✕':'Settings ⚙';
                    });
                    wHead.appendChild(toggleBtn);
                    wHead.appendChild(btn('↑','ic-btn',()=>{move(c.widgets,wi,-1);render();},'Move Widget Up'));
                    wHead.appendChild(btn('↓','ic-btn',()=>{move(c.widgets,wi,1);render();},'Move Widget Down'));
                    wHead.appendChild(btn('✕','ic-btn danger',()=>{c.widgets.splice(wi,1);render();},'Delete Widget'));

                    wCard.appendChild(wHead);

                    /* Append Visual Content Preview Box with Edit Trigger callback */
                    const previewBox = createWidgetVisualPreview(wg, () => {
                        const isOpen = form.classList.toggle('open');
                        toggleBtn.classList.toggle('active', isOpen);
                        toggleBtn.textContent = isOpen ? 'Close ✕' : 'Settings ⚙';
                    });
                    wCard.appendChild(previewBox);

                    /* Append Settings form */
                    wCard.appendChild(form);
                    cCard.appendChild(wCard);
                });

                /* Add widget dropdown */
                const adder=el('div','widget-adder');
                const addSel=el('select');
                addSel.appendChild(el('option',null,'＋ Insert Widget…'));

                /* Group palette by category */
                const cats={};
                cfg.palette.forEach(p=>{const cat=p.category||'Other';(cats[cat]=cats[cat]||[]).push(p);});
                Object.entries(cats).forEach(([cat,items])=>{
                    const grp=document.createElement('optgroup');
                    grp.label='── ' + humanize(cat) + ' ──';
                    items.forEach(p=>{
                        const o=el('option',null,p.label); o.value=p.type; grp.appendChild(o);
                    });
                    addSel.appendChild(grp);
                });
                addSel.addEventListener('change',()=>{
                    if(!addSel.value) return;
                    const p=paletteFor(addSel.value);
                    const defaults=p?JSON.parse(JSON.stringify(p.defaults||{})):{};
                    (c.widgets=c.widgets||[]).push({type:addSel.value,settings:defaults});
                    render();
                });
                adder.appendChild(addSel);
                cCard.appendChild(adder);
                rBody.appendChild(cCard);
            });
            rCard.appendChild(rBody);
            sBody.appendChild(rCard);
        });

        /* Add row shortcut at bottom of section */
        if (!(s.rows||[]).length) {
            const hint=el('div',null);
            hint.style.cssText='text-align:center;padding:24px;color:var(--faint);font-size:13px;font-weight:600';
            hint.innerHTML='No rows in this section yet — click <strong>+ Row</strong> above to begin.';
            sBody.appendChild(hint);
        }

        card.appendChild(sBody);
        canvas.appendChild(card);
    });

    schedulePreview();
}

/* ── Load ───────────────────────────────────────────────────────── */
async function load() {
    setStatus('Loading Canvas…', 'saving');
    try {
        const res=await fetch(cfg.treeUrl,{headers:{'Accept':'application/json'}});
        if(!res.ok) throw new Error('HTTP '+res.status);
        const data=await res.json();
        state.sections=Array.isArray(data.sections)?data.sections:[];
        render();
        setStatus('Ready ✓','ok');
        setTimeout(()=>setStatus(''), 2500);
    } catch(e) {
        setStatus('Failed: '+e.message,'err');
    }
}

/* ── Save ───────────────────────────────────────────────────────── */
async function save() {
    setStatus('Saving Changes…','saving');
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled=true;
    try {
        const res=await fetch(cfg.treeUrl,{
            method:'PUT',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':cfg.csrf},
            body:JSON.stringify({sections:state.sections}),
        });
        if(!res.ok){
            let msg='HTTP '+res.status;
            try{const j=await res.json();if(j.message)msg=j.message;}catch(e){}
            throw new Error(msg);
        }
        setStatus('Saved Successfully ✓','ok');
        setTimeout(()=>setStatus(''), 2500);
    } catch(e) {
        setStatus('Save failed: '+e.message,'err');
    } finally {
        saveBtn.disabled=false;
    }
}

/* ── Preview ─────────────────────────────────────────────────────── */
async function updatePreview() {
    if(!previewOn) return;
    try {
        const res=await fetch(cfg.previewUrl,{
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':cfg.csrf},
            body:JSON.stringify({sections:state.sections}),
        });
        if(!res.ok) throw new Error('HTTP '+res.status);
        const data=await res.json();
        const cssHref=location.origin+'/site.css?v='+Date.now();
        const doc='<!doctype html><html><head><meta charset="utf-8">'
            +'<meta name="viewport" content="width=device-width, initial-scale=1">'
            +'<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">'
            +'<link rel="stylesheet" href="'+cssHref+'">'
            +'<style>body{margin:0;background:#fff}[data-reveal]{opacity:1!important;transform:none!important}</style></head><body>'
            +(data.html||'<p style="color:#9ca3af;padding:2rem;font-family:system-ui">Nothing to preview yet.</p>')
            +'</body></html>';
        previewFrame.srcdoc=doc;
    } catch(e) {
        setStatus('Preview failed: '+e.message,'err');
    }
}

function togglePreview() {
    previewOn=!previewOn;
    previewPanel.classList.toggle('open', previewOn);
    document.getElementById('togglePreviewBtn').innerHTML=previewOn
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg> Close Preview'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Live Preview';
    updatePreview();
}

window.setPreviewWidth = function(w) {
    previewFrame.style.width=w;
    document.querySelectorAll('.pv-tab').forEach(t=>{
        const map={'100%':'Desktop','768px':'Tablet','375px':'Mobile'};
        t.classList.toggle('active',t.textContent===map[w]);
    });
};

/* ── Keyboard shortcuts ─────────────────────────────────────────── */
document.addEventListener('keydown', e=>{
    if ((e.ctrlKey||e.metaKey) && e.key==='s') { e.preventDefault(); save(); }
    if (e.key==='p' && e.target===document.body) togglePreview();
    if (e.key==='a' && e.target===document.body) { state.sections.push({type:'section',settings:{},rows:[]}); render(); }
    if (e.key==='r' && e.target===document.body) { e.preventDefault(); load(); }
});

/* ── Wire buttons ───────────────────────────────────────────────── */
document.getElementById('addSection').addEventListener('click',()=>{
    state.sections.push({type:'section',settings:{},rows:[]});
    render();
});
document.getElementById('saveBtn').addEventListener('click', save);
document.getElementById('reloadBtn').addEventListener('click', load);
document.getElementById('togglePreviewBtn').addEventListener('click', togglePreview);

/* ── Boot ───────────────────────────────────────────────────────── */
load();
})();
</script>
@endverbatim
@include('admin.partials.ai-content-assistant')
</body>
</html>
