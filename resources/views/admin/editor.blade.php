<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Page Builder — {{ $page->title }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ─── Reset & Base ─────────────────────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --navy:#0f172a; --navy-2:#1e293b; --navy-3:#334155;
  --gold:#c79a3b; --gold-2:#e0b94e;
  --blue:#3b82f6; --blue-2:#2563eb; --blue-pale:#eff6ff;
  --green:#22c55e; --red:#ef4444;
  --bg:#f1f5f9; --surface:#fff;
  --border:#e2e8f0; --border-dark:#cbd5e1;
  --text:#0f172a; --muted:#64748b; --faint:#94a3b8;
  --radius:10px; --radius-sm:6px; --radius-lg:16px;
  --shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06);
  --shadow-md:0 4px 20px rgba(0,0,0,.12),0 1px 4px rgba(0,0,0,.08);
  --ease:cubic-bezier(.4,0,.2,1);
  --ff: 'Inter', system-ui, sans-serif;
}
html,body{height:100%;overflow:hidden}
body{font:14px/1.5 var(--ff);color:var(--text);background:var(--bg);display:flex;flex-direction:column}

/* ─── Top Bar ────────────────────────────────────────────────────── */
.topbar{
  display:flex;align-items:center;gap:10px;
  height:52px;padding:0 16px;
  background:var(--navy);
  border-bottom:1px solid rgba(255,255,255,.07);
  flex-shrink:0;z-index:100;
}
.topbar__logo{
  font-size:13px;font-weight:700;color:#fff;
  display:flex;align-items:center;gap:7px;
  white-space:nowrap;
}
.topbar__logo svg{opacity:.8}
.topbar__breadcrumb{
  display:flex;align-items:center;gap:5px;
  font-size:12px;color:rgba(255,255,255,.45);
  white-space:nowrap;overflow:hidden;
}
.topbar__breadcrumb a{color:rgba(255,255,255,.45);text-decoration:none;transition:color .15s}
.topbar__breadcrumb a:hover{color:rgba(255,255,255,.8)}
.topbar__breadcrumb span{color:rgba(255,255,255,.7);font-weight:500;overflow:hidden;text-overflow:ellipsis}
.topbar__sep{opacity:.3}
.spacer{flex:1}
.topbar__status{
  font-size:12px;font-weight:500;
  padding:4px 10px;border-radius:999px;
  transition:all .3s var(--ease);
  color:rgba(255,255,255,.5);
}
.topbar__status.ok{color:#4ade80;background:rgba(74,222,128,.12)}
.topbar__status.err{color:#f87171;background:rgba(248,113,113,.12)}
.topbar__status.saving{color:#facc15;background:rgba(250,204,21,.12)}

.tb-btn{
  display:inline-flex;align-items:center;gap:5px;
  height:32px;padding:0 12px;
  font:500 12px/1 var(--ff);
  border-radius:var(--radius-sm);
  border:1px solid rgba(255,255,255,.12);
  background:rgba(255,255,255,.07);
  color:rgba(255,255,255,.8);
  cursor:pointer;white-space:nowrap;
  transition:all .15s var(--ease);
}
.tb-btn:hover{background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.2)}
.tb-btn.primary{
  background:linear-gradient(135deg,#3b82f6,#2563eb);
  border-color:#3b82f6;color:#fff;
  box-shadow:0 2px 8px rgba(59,130,246,.4);
}
.tb-btn.primary:hover{background:linear-gradient(135deg,#60a5fa,#3b82f6);box-shadow:0 4px 12px rgba(59,130,246,.5)}
.tb-btn.gold{
  background:linear-gradient(135deg,var(--gold-2),var(--gold));
  border-color:var(--gold);color:#1a0f00;
  box-shadow:0 2px 8px rgba(199,154,59,.4);
}
.tb-btn svg{width:14px;height:14px;opacity:.8}

/* ─── Body Layout ───────────────────────────────────────────────── */
.editor-body{
  display:flex;flex:1;overflow:hidden;
}

/* ─── Canvas Panel ───────────────────────────────────────────────── */
.canvas-panel{
  flex:1;overflow-y:auto;overflow-x:hidden;
  padding:24px;
  scroll-behavior:smooth;
}
.canvas-panel::-webkit-scrollbar{width:6px}
.canvas-panel::-webkit-scrollbar-thumb{background:var(--border-dark);border-radius:999px}

/* ─── Preview Panel ──────────────────────────────────────────────── */
.preview-panel{
  width:0;flex-shrink:0;overflow:hidden;
  border-left:1px solid var(--border);
  background:var(--surface);
  transition:width .35s var(--ease);
  display:flex;flex-direction:column;
}
.preview-panel.open{width:480px}
.preview-panel__head{
  display:flex;align-items:center;justify-content:space-between;
  padding:0 16px;height:44px;
  border-bottom:1px solid var(--border);
  background:var(--bg);flex-shrink:0;
  font-size:12px;font-weight:600;color:var(--muted);
  text-transform:uppercase;letter-spacing:.08em;
}
.preview-panel__head .pv-tabs{display:flex;gap:2px}
.pv-tab{
  padding:4px 10px;border-radius:var(--radius-sm);
  font-size:11px;font-weight:600;cursor:pointer;color:var(--muted);
  border:none;background:none;transition:all .15s;
}
.pv-tab.active{background:var(--surface);color:var(--text);box-shadow:var(--shadow)}
#previewFrame{flex:1;border:none;background:#fff;width:100%}

/* ─── Empty State ────────────────────────────────────────────────── */
.empty-state{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:16px;padding:80px 40px;text-align:center;
  background:var(--surface);border:2px dashed var(--border);
  border-radius:var(--radius-lg);color:var(--muted);
  animation:fadeIn .4s var(--ease);
}
.empty-state__icon{font-size:3rem;opacity:.4}
.empty-state__title{font-size:18px;font-weight:600;color:var(--text)}
.empty-state__sub{font-size:13px;max-width:320px;line-height:1.6}
.empty-state .add-btn{
  display:inline-flex;align-items:center;gap:6px;
  padding:10px 20px;border-radius:999px;
  background:linear-gradient(135deg,var(--blue),var(--blue-2));
  color:#fff;font-weight:600;font-size:13px;border:none;cursor:pointer;
  box-shadow:0 4px 12px rgba(59,130,246,.4);
  transition:transform .2s,box-shadow .2s;
}
.empty-state .add-btn:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(59,130,246,.5)}

/* ─── Section Card ───────────────────────────────────────────────── */
.section-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-lg);margin-bottom:16px;
  overflow:hidden;
  box-shadow:var(--shadow);
  animation:slideIn .25s var(--ease);
  transition:box-shadow .2s;
}
.section-card:hover{box-shadow:var(--shadow-md)}
.section-head{
  display:flex;align-items:center;gap:8px;flex-wrap:wrap;
  padding:10px 14px;
  background:linear-gradient(135deg,#f8fafc,#f1f5f9);
  border-bottom:1px solid var(--border);
}
.section-label{
  display:flex;align-items:center;gap:6px;
  font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--navy-3);
  background:var(--navy);
  color:#fff;
  padding:3px 10px;border-radius:999px;
}
.section-label svg{width:12px;height:12px;opacity:.7}
.section-body{padding:12px}
.section-select{
  font:500 12px var(--ff);padding:4px 8px;
  border:1px solid var(--border-dark);border-radius:var(--radius-sm);
  background:#fff;color:var(--text);cursor:pointer;
  transition:border-color .15s;
}
.section-select:focus{outline:none;border-color:var(--blue)}

.color-bar{height:3px;transition:background-color .2s}
.section-strip-section{background:var(--border-dark)}
.section-strip-alt{background:#3b82f6}
.section-strip-navy{background:#0f172a}
.section-strip-flush{background:#22c55e}
.section-strip-hero{background:#8b5cf6}
.section-strip-custom{background:var(--gold)}
.color-swatch-btn{width:14px;height:14px;border-radius:50%;border:1px solid rgba(0,0,0,.18);cursor:pointer;flex-shrink:0;padding:0;transition:transform .15s}
.color-swatch-btn:hover{transform:scale(1.3)}

/* ─── Row ───────────────────────────────────────────────────────── */
.row-card{
  border:1px solid var(--border);border-radius:var(--radius);
  margin-bottom:10px;overflow:hidden;
  background:#fafbfc;
  transition:border-color .15s;
}
.row-card:hover{border-color:var(--border-dark)}
.row-head{
  display:flex;align-items:center;gap:6px;flex-wrap:wrap;
  padding:7px 10px;background:#f8fafc;
  border-bottom:1px solid var(--border);
}
.row-label{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;
  color:var(--muted);padding:2px 8px;
  border:1px solid var(--border-dark);border-radius:999px;background:#fff;
}
.row-body{display:flex;gap:10px;padding:10px;flex-wrap:wrap;align-items:flex-start}

/* ─── Column ─────────────────────────────────────────────────────── */
.col-card{
  flex:1 1 180px;min-width:180px;
  background:#fff;border:1px dashed var(--border);
  border-radius:var(--radius);padding:8px;
  transition:border-color .2s,background .2s,box-shadow .2s;
  min-height:80px;position:relative;
}
.col-card.drag-over{
  border-color:var(--blue);background:var(--blue-pale);
  box-shadow:0 0 0 3px rgba(59,130,246,.15);
}
.col-head{
  display:flex;align-items:center;gap:5px;flex-wrap:wrap;
  margin-bottom:8px;
}
.col-label{
  font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;
  color:var(--faint);
}
.col-width-sel{
  font-size:11px;padding:2px 6px;border:1px solid var(--border);
  border-radius:4px;background:#fff;color:var(--muted);cursor:pointer;
}

/* ─── Widget Card ─────────────────────────────────────────────────── */
.widget-card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius-sm);margin-bottom:6px;
  overflow:hidden;
  transition:box-shadow .15s,transform .15s;
  animation:slideIn .2s var(--ease);
}
.widget-card:hover{box-shadow:0 2px 8px rgba(0,0,0,.08)}
.widget-card.dragging{opacity:.4;transform:scale(.97)}
.widget-head{
  display:flex;align-items:center;gap:6px;
  padding:6px 8px;
  background:linear-gradient(135deg,#f8fafc,#fff);
  border-bottom:1px solid transparent;
  cursor:default;
}
.widget-handle{
  cursor:grab;color:var(--faint);font-size:14px;
  line-height:1;user-select:none;flex-shrink:0;
  transition:color .15s;
}
.widget-handle:hover{color:var(--muted)}
.widget-handle:active{cursor:grabbing}
.widget-type-badge{
  flex:1;display:flex;align-items:center;gap:6px;
  font-size:12px;font-weight:600;color:var(--text);
}
.widget-type-dot{
  width:8px;height:8px;border-radius:50%;flex-shrink:0;
}
.widget-settings-panel{
  padding:10px;background:#fafbfc;border-top:1px solid var(--border);
  display:none;
}
.widget-settings-panel.open{display:block}

/* ─── Widget Add Dropdown ─────────────────────────────────────────── */
.widget-adder{margin-top:6px}
.widget-adder select{
  width:100%;padding:6px 8px;
  font:13px var(--ff);
  border:1px dashed var(--border-dark);border-radius:var(--radius-sm);
  background:#fff;color:var(--muted);cursor:pointer;
  transition:border-color .15s,color .15s;
}
.widget-adder select:focus{outline:none;border-color:var(--blue);color:var(--text)}
.widget-adder select:hover{border-color:var(--blue)}

/* ─── Small Action Buttons ───────────────────────────────────────── */
.ic-btn{
  display:inline-flex;align-items:center;justify-content:center;
  width:24px;height:24px;border-radius:5px;
  border:1px solid var(--border);background:#fff;
  color:var(--muted);cursor:pointer;font-size:11px;
  transition:all .12s var(--ease);flex-shrink:0;
}
.ic-btn:hover{background:var(--bg);color:var(--text);border-color:var(--border-dark)}
.ic-btn.danger:hover{background:#fff1f1;color:var(--red);border-color:#fecaca}
.ic-btn.ghost{border-color:transparent;background:transparent}
.ic-btn.ghost:hover{background:var(--bg)}
.ic-btn.settings-toggle{
  padding:0 8px;width:auto;font-size:11px;font-weight:600;
  color:var(--blue);border-color:rgba(59,130,246,.25);background:rgba(59,130,246,.06);
}
.ic-btn.settings-toggle:hover{background:rgba(59,130,246,.12);color:var(--blue-2)}
.ic-btn.settings-toggle.active{background:rgba(59,130,246,.15)}

/* ─── Settings Fields ─────────────────────────────────────────────── */
.field{margin-bottom:8px}
.field__label{
  display:block;font-size:11px;font-weight:600;color:var(--muted);
  margin-bottom:3px;text-transform:capitalize;
  letter-spacing:.02em;
}
.field__input{
  width:100%;padding:6px 8px;
  font:13px var(--ff);color:var(--text);
  background:#fff;border:1px solid var(--border);
  border-radius:var(--radius-sm);
  transition:border-color .15s,box-shadow .15s;
}
.field__input:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.field__input.bad{border-color:var(--red);background:#fff1f1}
textarea.field__input{font-family:ui-monospace,monospace;font-size:12px;resize:vertical;min-height:70px}

/* ─── Array Editor ────────────────────────────────────────────────── */
.arr{display:flex;flex-direction:column;gap:6px;margin-top:4px}
.arr-row{
  border:1px solid var(--border);border-radius:var(--radius-sm);
  padding:8px;background:#fff;
}
.arr-row__head{
  display:flex;align-items:center;gap:4px;margin-bottom:6px;
  padding-bottom:6px;border-bottom:1px solid var(--border);
}
.arr-row__head .idx-badge{
  font-size:10px;font-weight:700;color:var(--blue);background:rgba(59,130,246,.08);
  padding:1px 7px;border-radius:999px;
}
.arr-row__body{display:flex;flex-direction:column;gap:4px}
.subobj{padding-left:10px;border-left:2px solid var(--border);display:flex;flex-direction:column;gap:4px;margin:4px 0}

/* ─── Checkbox ───────────────────────────────────────────────────── */
.check-wrap{
  display:inline-flex;align-items:center;gap:5px;
  font-size:11px;font-weight:500;color:var(--muted);cursor:pointer;
}
.check-wrap input[type=checkbox]{accent-color:var(--blue);cursor:pointer}

/* ─── Animations ─────────────────────────────────────────────────── */
@keyframes slideIn{
  from{opacity:0;transform:translateY(6px)}
  to{opacity:1;transform:none}
}
@keyframes fadeIn{
  from{opacity:0}
  to{opacity:1}
}

/* ─── Scrollbar Global ───────────────────────────────────────────── */
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-thumb{background:var(--border-dark);border-radius:999px}

/* ─── Widget Color Dots ──────────────────────────────────────────── */
.dot-hero{background:#8b5cf6}
.dot-text{background:#3b82f6}
.dot-image{background:#10b981}
.dot-gallery{background:#f59e0b}
.dot-form{background:#ef4444}
.dot-cta{background:#06b6d4}
.dot-default{background:#94a3b8}

/* ─── Section type color strips ──────────────────────────────────── */
.section-strip-section{background:linear-gradient(90deg,#3b82f6,#6366f1)}
.section-strip-alt{background:linear-gradient(90deg,#64748b,#94a3b8)}
.section-strip-navy{background:linear-gradient(90deg,#0f172a,#1e293b)}
.section-strip-flush{background:linear-gradient(90deg,#10b981,#059669)}
.section-strip-hero{background:linear-gradient(90deg,#8b5cf6,#6d28d9)}

/* ─── Drag ghost zone at bottom of col ──────────────────────────── */
.drop-zone{
  border:2px dashed var(--border);border-radius:6px;
  padding:12px;text-align:center;
  font-size:11px;color:var(--faint);margin-top:4px;
  transition:all .15s;
}
.drop-zone.active{border-color:var(--blue);color:var(--blue);background:var(--blue-pale)}

/* ─── Section type color bar ─────────────────────────────────────── */
.color-bar{height:3px;border-radius:2px;margin-bottom:0;flex-shrink:0;transition:all .3s}
</style>
</head>
<body>

{{-- ─── TOPBAR ─────────────────────────────────────────────────────── --}}
<div class="topbar">
  <div class="topbar__logo">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
      <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
    </svg>
    Page Builder
  </div>
  <div class="topbar__breadcrumb">
    <a href="/admin">Admin</a>
    <span class="topbar__sep">/</span>
    <a href="/admin/pages">Pages</a>
    <span class="topbar__sep">/</span>
    <span title="{{ $page->title }}">{{ mb_strimwidth($page->title, 0, 31, '…') }}</span>
  </div>

  <span class="spacer"></span>

  <span id="statusEl" class="topbar__status"></span>

  <button id="addSection" class="tb-btn" title="Add Section (A)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
    Section
  </button>
  <button id="togglePreviewBtn" class="tb-btn" title="Toggle live preview (P)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
    Preview
  </button>
  <a class="tb-btn" href="{{ $page->slug === 'home' ? url('/') : url('/'.$page->slug) }}" target="_blank" rel="noopener" title="View live page">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    View
  </a>
  <button id="reloadBtn" class="tb-btn" title="Reload from server (R)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
  </button>
  <button id="saveBtn" class="tb-btn gold" title="Save (Ctrl+S)">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
    Save
  </button>
</div>

{{-- ─── BODY ────────────────────────────────────────────────────────── --}}
<div class="editor-body">
  <div class="canvas-panel" id="canvas"></div>

  <div class="preview-panel" id="previewPanel">
    <div class="preview-panel__head">
      <span>Live Preview</span>
      <div class="pv-tabs">
        <button class="pv-tab active" onclick="setPreviewWidth('100%')">Desktop</button>
        <button class="pv-tab" onclick="setPreviewWidth('768px')">Tablet</button>
        <button class="pv-tab" onclick="setPreviewWidth('375px')">Mobile</button>
      </div>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;overflow:hidden;position:relative">
      <div id="previewViewport" style="flex:1;overflow:auto;display:flex;justify-content:center;background:#e5e7eb;padding:0">
        <iframe id="previewFrame" title="Live preview" style="width:100%;height:100%;border:none;background:#fff;transition:width .3s"></iframe>
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
function mk(tag, attrs, children) {
    const e = document.createElement(tag);
    if (attrs) Object.entries(attrs).forEach(([k,v]) => { if (k === 'style') Object.assign(e.style, v); else if (k.startsWith('on')) e.addEventListener(k.slice(2), v); else e.setAttribute(k, v); });
    (children || []).forEach(c => e.appendChild(typeof c === 'string' ? document.createTextNode(c) : c));
    return e;
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
    return '';
}
function move(arr, i, dir) { const j=i+dir; if(j<0||j>=arr.length) return; [arr[i],arr[j]]=[arr[j],arr[i]]; }
const paletteFor = t => cfg.palette.find(p=>p.type===t);

/* ── Widget color dots ──────────────────────────────────────────── */
const widgetDotClass = type => {
    const t = (type||'').toLowerCase();
    if (/hero|banner|slider/.test(t)) return 'dot-hero';
    if (/text|rich|content|html/.test(t)) return 'dot-text';
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
    const addBtn = btn('+ Add item','ic-btn',()=>{ arr.push(cloneEmpty(arr[0]??'')); rerender(); });
    addBtn.style.cssText='margin-top:4px;width:auto;padding:0 10px;font-size:11px';
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
        i.style.cssText='width:auto;padding:0;margin:2px 0';
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
        const long=/body|text|quote|desc|message|html|address|embed|content/i.test(String(key))||(typeof val==='string'&&val.length>60);
        const i=document.createElement(long?'textarea':'input');
        if(long) i.rows=3; else i.type='text';
        i.value=(val==null?'':val); i.className='field__input';
        i.addEventListener('input',()=>{ obj[key]=i.value; schedulePreview(); });
        field.appendChild(i);
    }
    wrap.appendChild(field);
}

function settingsForm(widget) {
    const form = el('div','widget-settings-panel');
    function draw() {
        form.innerHTML='';
        const keys=Object.keys(widget.settings||{});
        if(!keys.length){ form.appendChild(el('em',null,'No settings for this widget.')); form.style.color='var(--muted)'; return; }
        const enums=(paletteFor(widget.type)||{}).options||{};
        keys.forEach(k=>fieldEditor(form,widget.settings,k,()=>{ draw(); schedulePreview(); },enums));
    }
    draw();
    return form;
}

/* ── RENDER ─────────────────────────────────────────────────────── */
function render() {
    canvas.innerHTML='';

    if (!state.sections.length) {
        const empty=el('div','empty-state');
        empty.innerHTML=`
          <div class="empty-state__icon">📐</div>
          <div class="empty-state__title">No sections yet</div>
          <p class="empty-state__sub">Click <strong>"+ Section"</strong> in the toolbar to start building your page.</p>
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
        bar.style.height='3px';
        if ((s.type==='custom' || s.type==='section') && s.settings && s.settings._custom_bg) {
            bar.style.backgroundColor = s.settings._custom_bg;
        }
        card.appendChild(bar);

        /* section head */
        const sHead=el('div','section-head');
        const lbl=el('div','section-label');
        lbl.innerHTML=`<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="12" height="12"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg> Section ${si+1}`;
        sHead.appendChild(lbl);

        /* style select */
        const styleSel = sel([
            ['section','⬜ Default'],['alt','🔵 Soft Grey'],
            ['navy','🌑 Navy Dark'],['flush','🟢 Flush'],['hero','🟣 Hero'],
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
            colorWrap.style.cssText = 'display:inline-flex;align-items:center;gap:4px;background:#fff;border:1px solid var(--border-dark);border-radius:var(--radius-sm);padding:2px 6px;';

            const colorDot = el('input');
            colorDot.type = 'color';
            colorDot.value = (s.settings && s.settings._custom_bg) ? s.settings._custom_bg : '#f4f7fc';
            colorDot.title = 'Pick custom section background color';
            colorDot.style.cssText = 'width:20px;height:20px;border:none;border-radius:4px;cursor:pointer;padding:0;background:none;';

            const hexInput = el('input');
            hexInput.type = 'text';
            hexInput.value = (s.settings && s.settings._custom_bg) ? s.settings._custom_bg : '#f4f7fc';
            hexInput.placeholder = '#f4f7fc';
            hexInput.title = 'Hex color code (e.g. #f4f7fc or #ff5722)';
            hexInput.style.cssText = 'width:64px;font:500 11px var(--ff);border:none;outline:none;background:transparent;color:var(--text);';

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

            const swatches = ['#ffffff', '#f4f7fc', '#eff6ff', '#fefce8', '#f0fdf4', '#fff1f2', '#0f172a'];
            swatches.forEach(hex => {
                const sw = el('button', 'color-swatch-btn');
                sw.type = 'button';
                sw.style.backgroundColor = hex;
                sw.title = 'Set color ' + hex;
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
            ['','No animation'],['fade-up','Fade ↑'],['fade-down','Fade ↓'],
            ['fade-left','Fade ←'],['fade-right','Fade →'],['zoom','Zoom']
        ], (s.settings=s.settings||{})._animation||'', v=>{s.settings._animation=v; schedulePreview();}, 'section-select'));

        sHead.appendChild(mkCheck('Hide mobile',  s.settings._hide_mobile,  v=>{s.settings._hide_mobile=v;schedulePreview();}));
        sHead.appendChild(mkCheck('Hide desktop', s.settings._hide_desktop, v=>{s.settings._hide_desktop=v;schedulePreview();}));

        const sp=el('span'); sp.style.flex='1'; sHead.appendChild(sp);

        sHead.appendChild(btn('+ Row','ic-btn',()=>{(s.rows=s.rows||[]).push({settings:{},columns:[]});render();},'Add row'));
        sHead.appendChild(btn('↑','ic-btn',()=>{move(state.sections,si,-1);render();},'Move up'));
        sHead.appendChild(btn('↓','ic-btn',()=>{move(state.sections,si,1);render();},'Move down'));
        sHead.appendChild(btn('🗑','ic-btn danger',()=>{if(confirm('Delete section?')){state.sections.splice(si,1);render();}},'Delete section'));
        card.appendChild(sHead);

        /* rows */
        const sBody=el('div','section-body');
        (s.rows||[]).forEach((r,ri)=>{
            const rCard=el('div','row-card');
            const rHead=el('div','row-head');
            rHead.appendChild(el('span','row-label','Row '+(ri+1)));
            const rSp=el('span'); rSp.style.flex='1'; rHead.appendChild(rSp);
            rHead.appendChild(btn('+ Col','ic-btn',()=>{(r.columns=r.columns||[]).push({width:12,settings:{},widgets:[]});render();},'Add column'));
            rHead.appendChild(btn('↑','ic-btn',()=>{move(s.rows,ri,-1);render();},'Move up'));
            rHead.appendChild(btn('↓','ic-btn',()=>{move(s.rows,ri,1);render();},'Move down'));
            rHead.appendChild(btn('🗑','ic-btn danger',()=>{if(confirm('Delete row?')){s.rows.splice(ri,1);render();}},'Delete row'));
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
                wSel.title='Grid width (1–12)';
                for(let i=1;i<=12;i++){const o=el('option',null,i+'/12');o.value=i;if(Number(c.width)===i)o.selected=true;wSel.appendChild(o);}
                wSel.addEventListener('change',()=>{c.width=Number(wSel.value);});
                cHead.appendChild(wSel);
                const cSp=el('span'); cSp.style.flex='1'; cHead.appendChild(cSp);
                cHead.appendChild(btn('↑','ic-btn',()=>{move(r.columns,ci,-1);render();},'Move left'));
                cHead.appendChild(btn('↓','ic-btn',()=>{move(r.columns,ci,1);render();},'Move right'));
                cHead.appendChild(btn('✕','ic-btn danger',()=>{if(confirm('Delete column?')){r.columns.splice(ci,1);render();}},'Delete column'));
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
                    handle.draggable=true; handle.title='Drag to reorder';
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
                    const toggleBtn=btn('Settings','ic-btn settings-toggle',()=>{
                        const isOpen=form.classList.toggle('open');
                        toggleBtn.classList.toggle('active',isOpen);
                        toggleBtn.textContent=isOpen?'Close':'Settings';
                    });
                    wHead.appendChild(toggleBtn);
                    wHead.appendChild(btn('↑','ic-btn',()=>{move(c.widgets,wi,-1);render();},'Move up'));
                    wHead.appendChild(btn('↓','ic-btn',()=>{move(c.widgets,wi,1);render();},'Move down'));
                    wHead.appendChild(btn('✕','ic-btn danger',()=>{c.widgets.splice(wi,1);render();},'Remove widget'));

                    wCard.appendChild(wHead);
                    wCard.appendChild(form);
                    cCard.appendChild(wCard);
                });

                /* Add widget dropdown */
                const adder=el('div','widget-adder');
                const addSel=el('select');
                addSel.appendChild(el('option',null,'＋ Add widget…'));

                /* Group palette by category */
                const cats={};
                cfg.palette.forEach(p=>{const c=p.category||'Other';(cats[c]=cats[c]||[]).push(p);});
                Object.entries(cats).forEach(([cat,items])=>{
                    const grp=document.createElement('optgroup');
                    grp.label=cat;
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
            hint.style.cssText='text-align:center;padding:20px;color:var(--faint);font-size:12px';
            hint.innerHTML='No rows yet — click <strong>+ Row</strong> to start';
            sBody.appendChild(hint);
        }

        card.appendChild(sBody);
        canvas.appendChild(card);
    });

    schedulePreview();
}

/* ── Load ───────────────────────────────────────────────────────── */
async function load() {
    setStatus('Loading…', 'saving');
    try {
        const res=await fetch(cfg.treeUrl,{headers:{'Accept':'application/json'}});
        if(!res.ok) throw new Error('HTTP '+res.status);
        const data=await res.json();
        state.sections=Array.isArray(data.sections)?data.sections:[];
        render();
        setStatus('Loaded ✓','ok');
        setTimeout(()=>setStatus(''),'2000');
    } catch(e) {
        setStatus('Failed: '+e.message,'err');
    }
}

/* ── Save ───────────────────────────────────────────────────────── */
async function save() {
    setStatus('Saving…','saving');
    document.getElementById('saveBtn').disabled=true;
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
        setStatus('Saved ✓','ok');
        setTimeout(()=>setStatus(''),'2500');
    } catch(e) {
        setStatus('Save failed: '+e.message,'err');
    } finally {
        document.getElementById('saveBtn').disabled=false;
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
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg> Preview'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Preview';
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
