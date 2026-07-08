@extends('admin.layout')

@section('title', 'Media Library')
@section('subtitle', $total . ' files')

@section('actions')
    <div class="flex items-center gap-2 flex-wrap">
        <form method="GET" action="{{ url('/admin/m/media') }}" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search files…" class="inline-select" style="min-width:200px;padding:8px 12px">
            <button class="btn-sm" type="submit">Search</button>
            @if ($q)<a class="btn-sm" href="{{ url('/admin/m/media') }}">Clear</a>@endif
        </form>
        <a href="{{ url('/admin/upload') }}" class="btn primary"><x-admin.icon name="upload"/> Upload</a>
    </div>
@endsection

@section('content')

<style>
    .media-view-toggle{display:flex;gap:4px;margin-bottom:16px;background:var(--surface-2);border-radius:10px;padding:3px;width:fit-content;border:1px solid var(--border)}
    .media-view-toggle button{display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;border:none;background:transparent;font-size:12.5px;font-weight:500;color:var(--text-muted);cursor:pointer;transition:.15s}
    .media-view-toggle button:hover{color:var(--text)}
    .media-view-toggle button.active{background:var(--surface);color:var(--text);border:1px solid var(--border);box-shadow:0 1px 3px rgba(0,0,0,.06)}
    .media-view-toggle button svg{width:16px;height:16px}

    .media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:14px}
    .media-list{display:flex;flex-direction:column;gap:4px}
    .media-large{display:flex;flex-direction:column;gap:20px}

    .media-grid .media-item{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--surface);position:relative;transition:box-shadow .2s}
    .media-grid .media-item:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
    .media-grid .media-thumb{aspect-ratio:1;background:var(--surface-hover);display:grid;place-items:center;overflow:hidden;position:relative}
    .media-grid .media-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .3s}
    .media-grid .media-item:hover .media-thumb img{transform:scale(1.06)}
    .media-grid .media-info{padding:10px 12px}
    .media-grid .media-name{font-size:12.5px;font-weight:500;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .media-grid .media-meta{font-size:11px;color:var(--text-muted);margin-top:2px}
    .media-grid .media-ext{font-weight:700;font-size:14px;color:var(--text-muted);text-transform:uppercase}
    .media-grid .media-actions{position:absolute;top:6px;right:6px;display:flex;gap:4px;opacity:0;transition:opacity .18s;z-index:2}
    .media-grid .media-item:hover .media-actions{opacity:1}
    .media-grid .media-actions .ma-btn{width:30px;height:30px;border-radius:7px;border:none;background:rgba(0,0,0,.55);backdrop-filter:blur(6px);color:#fff;cursor:pointer;display:grid;place-items:center;font-size:13px;transition:.15s}
    .media-grid .media-actions .ma-btn:hover{background:rgba(0,0,0,.8)}
    .media-grid .media-actions .ma-btn.del:hover{background:var(--danger)}

    .media-list .media-item{display:flex;align-items:center;gap:12px;padding:8px 12px;border-radius:10px;border:1px solid transparent;transition:.15s}
    .media-list .media-item:hover{border-color:var(--border);background:var(--surface)}
    .media-list .media-thumb{width:44px;height:44px;border-radius:8px;overflow:hidden;background:var(--surface-hover);flex-shrink:0;display:grid;place-items:center}
    .media-list .media-thumb img{width:100%;height:100%;object-fit:cover}
    .media-list .media-ext{font-weight:700;font-size:11px;color:var(--text-muted);text-transform:uppercase}
    .media-list .media-name{flex:1;min-width:0;font-size:13px;font-weight:500;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .media-list .media-meta{display:flex;gap:14px;font-size:11.5px;color:var(--text-muted);flex-shrink:0;align-items:center}
    .media-list .media-meta span{white-space:nowrap}
    .media-list .media-actions{display:flex;gap:4px;flex-shrink:0}
    .media-list .media-actions .ma-btn{display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);font-size:11.5px;cursor:pointer;transition:.15s;line-height:1}
    .media-list .media-actions .ma-btn:hover{border-color:var(--primary);color:var(--primary)}
    .media-list .media-actions .ma-btn.copied{background:var(--success-soft);border-color:var(--success);color:var(--success)}
    .media-list .media-actions .ma-btn.del:hover{border-color:var(--danger);color:var(--danger)}

    .media-large .media-item{border:1px solid var(--border);border-radius:14px;overflow:hidden;background:var(--surface)}
    .media-large .media-thumb{width:100%;max-height:340px;background:var(--surface-hover);display:grid;place-items:center;overflow:hidden}
    .media-large .media-thumb img{width:100%;height:100%;object-fit:contain;max-height:340px}
    .media-large .media-ext{font-weight:700;font-size:18px;color:var(--text-muted);text-transform:uppercase;padding:48px 0}
    .media-large .media-info{display:flex;align-items:center;gap:16px;padding:14px 18px;flex-wrap:wrap}
    .media-large .media-name{font-size:14px;font-weight:500;color:var(--text);flex:1;min-width:0}
    .media-large .media-meta{display:flex;gap:14px;font-size:12px;color:var(--text-muted)}
    .media-large .media-actions{display:flex;gap:6px}
    .media-large .media-actions .ma-btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:7px;border:1px solid var(--border);background:var(--surface);color:var(--text-muted);font-size:12px;cursor:pointer;transition:.15s}
    .media-large .media-actions .ma-btn:hover{border-color:var(--primary);color:var(--primary)}
    .media-large .media-actions .ma-btn.copied{background:var(--success-soft);border-color:var(--success);color:var(--success)}
    .media-large .media-actions .ma-btn.del:hover{border-color:var(--danger);color:var(--danger)}

    .media-view{display:none}
    .media-view.active{display:block}

    .scroll-sentinel{height:1px;margin-top:20px}
    .scroll-loading{text-align:center;padding:20px;color:var(--text-muted);font-size:13px}
    .scroll-loading .sp{display:inline-block;width:20px;height:20px;border:2px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:sp .7s linear infinite;vertical-align:middle;margin-right:8px}
    @keyframes sp{to{transform:rotate(360deg)}}

    .media-empty{text-align:center;padding:48px 20px;color:var(--text-muted)}
    .media-empty svg{width:48px;height:48px;margin:0 auto 12px;display:block;opacity:.4}

    .toast-cp{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1a1a2e;color:#fff;padding:10px 22px;border-radius:10px;font-size:13.5px;z-index:999;box-shadow:0 8px 30px rgba(0,0,0,.3);opacity:0;transition:opacity .25s;pointer-events:none}
    .toast-cp.show{opacity:1}
</style>

{{-- View toggle --}}
<div class="media-view-toggle">
    <button data-view="grid" class="active" onclick="setView('grid')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
        Grid
    </button>
    <button data-view="list" onclick="setView('list')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        List
    </button>
    <button data-view="large" onclick="setView('large')">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
        Large
    </button>
</div>

{{-- View containers --}}
<div id="mediaViewGrid" class="media-view media-grid active">@include('admin.media._items', ['mode' => 'grid'])</div>
<div id="mediaViewList" class="media-view media-list">@include('admin.media._items', ['mode' => 'list'])</div>
<div id="mediaViewLarge" class="media-view media-large">@include('admin.media._items', ['mode' => 'large'])</div>

{{-- Scroll sentinel --}}
<div class="scroll-sentinel" id="scrollSentinel"></div>
<div class="scroll-loading" id="scrollLoading" style="display:none">
    <span class="sp"></span>Loading more…
</div>

<div class="toast-cp" id="toastCopy">Link copied</div>

<script>
    var VIEW_KEY = 'media-view-mode';
    var curView = localStorage.getItem(VIEW_KEY) || 'grid';
    var nextPg = {{ $items->currentPage() + 1 }};
    var hasMore = {{ $items->hasMorePages() ? 'true' : 'false' }};
    var loading = false;

    function setView(v) {
        curView = v;
        localStorage.setItem(VIEW_KEY, v);
        document.querySelectorAll('.media-view').forEach(function (el) { el.classList.remove('active'); });
        document.getElementById('mediaView' + v.charAt(0).toUpperCase() + v.slice(1)).classList.add('active');
        document.querySelectorAll('.media-view-toggle button').forEach(function (b) { b.classList.toggle('active', b.dataset.view === v); });
    }
    setView(curView);

    function showToast(msg) {
        var t = document.getElementById('toastCopy');
        t.textContent = msg;
        t.classList.add('show');
        clearTimeout(t._t);
        t._t = setTimeout(function () { t.classList.remove('show'); }, 1800);
    }

    function copyUrl(path) {
        navigator.clipboard.writeText(window.location.origin + path).then(function () { showToast('Link copied'); });
    }

    function deleteItem(id) {
        if (!confirm('Delete this file?')) return;
        fetch('/admin/m/media/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content, 'X-Requested-With': 'XMLHttpRequest' }, body: new URLSearchParams({ _method: 'DELETE' }) })
            .then(function (r) {
                if (r.ok) {
                    document.querySelectorAll('[data-media-id="' + id + '"]').forEach(function (el) { el.remove(); });
                    showToast('File deleted');
                } else { showToast('Delete failed'); }
            }).catch(function () { showToast('Delete failed'); });
    }

    // Infinite scroll
    (function () {
        var sentinel = document.getElementById('scrollSentinel');
        if (!sentinel) return;
        var observer = new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting && hasMore && !loading) {
                loading = true;
                document.getElementById('scrollLoading').style.display = 'block';
                var params = 'page=' + nextPg;
                var q = new URLSearchParams(window.location.search).get('q');
                if (q) params += '&q=' + encodeURIComponent(q);
                fetch('{{ url('/admin/m/media/more') }}?' + params)
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (data.html) {
                            document.getElementById('mediaViewGrid').insertAdjacentHTML('beforeend', data.html.grid || '');
                            document.getElementById('mediaViewList').insertAdjacentHTML('beforeend', data.html.list || '');
                            document.getElementById('mediaViewLarge').insertAdjacentHTML('beforeend', data.html.large || '');
                        }
                        hasMore = data.hasMore;
                        nextPg = data.nextPage;
                        loading = false;
                        document.getElementById('scrollLoading').style.display = 'none';
                    }).catch(function () {
                        loading = false;
                        document.getElementById('scrollLoading').style.display = 'none';
                    });
            }
        }, { rootMargin: '200px' });
        observer.observe(sentinel);
    })();
</script>

@endsection
