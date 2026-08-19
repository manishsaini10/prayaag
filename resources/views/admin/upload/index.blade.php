@extends('admin.layout')

@section('title', 'Upload Center')
@section('subtitle', 'Add files to the media library')

@section('actions')
    <a href="{{ url('/admin/m/media') }}" class="btn"><x-admin.icon name="folder"/> Media Library</a>
@endsection

@section('content')

<style>
    .dz{border:2px dashed var(--border-strong);border-radius:16px;background:var(--surface-2);padding:42px 24px;text-align:center;transition:.18s;cursor:pointer}
    .dz:hover,.dz.over{border-color:var(--primary);background:var(--primary-soft)}
    .dz .ic{width:56px;height:56px;border-radius:16px;margin:0 auto 14px;display:grid;place-items:center;background:linear-gradient(135deg,var(--primary),var(--primary-strong));color:#fff}
    .dz .ic svg{width:28px;height:28px}
    .dz h3{font-size:16px;color:var(--text);font-weight:600}
    .dz p{color:var(--text-muted);font-size:13.5px;margin-top:4px}
    .picked{margin-top:16px;display:flex;flex-direction:column;gap:6px}
    .picked .row{display:flex;align-items:center;gap:10px;font-size:13px;background:var(--surface);border:1px solid var(--border);border-radius:9px;padding:8px 12px}
    .picked .row .nm{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text)}
    .grid-media{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-top:16px}
    @media(max-width:1024px){.grid-media{grid-template-columns:repeat(4,1fr)}}
    @media(max-width:620px){.grid-media{grid-template-columns:repeat(2,1fr)}}
    .mtile{border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--surface);position:relative}
    .mtile .thumb{aspect-ratio:1;display:grid;place-items:center;background:var(--surface-hover);overflow:hidden;position:relative}
    .mtile .thumb img{width:100%;height:100%;object-fit:cover;transition:transform .25s}
    .mtile:hover .thumb img{transform:scale(1.08)}
    .mtile .ext{font-weight:700;color:var(--text-muted);font-size:15px;text-transform:uppercase}
    .mtile .meta{padding:8px 10px}
    .mtile .meta .n{font-size:12px;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .mtile .meta .s{font-size:11px;color:var(--text-muted)}
    .mtile .actions-overlay{position:absolute;top:6px;right:6px;display:flex;gap:4px;opacity:0;transition:opacity .18s;z-index:2}
    .mtile:hover .actions-overlay{opacity:1}
    .mtile .action-btn{width:28px;height:28px;border-radius:7px;background:rgba(15,23,42,.75);backdrop-filter:blur(6px);border:none;color:#fff;cursor:pointer;display:grid;place-items:center;transition:background .15s,transform .15s}
    .mtile .action-btn:hover{background:rgba(15,23,42,.95);transform:scale(1.06)}
    .mtile .action-btn svg{width:13px;height:13px}
    .mtile .action-btn.del:hover{background:#ef4444}
    .mtile .action-btn.copied{background:var(--success)}
    .toast-copy{position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#1a1a2e;color:#fff;padding:10px 22px;border-radius:10px;font-size:13.5px;z-index:999;box-shadow:0 8px 30px rgba(0,0,0,.3);opacity:0;transition:opacity .25s;pointer-events:none}
    .toast-copy.show{opacity:1}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif
@error('files.*')<div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $message }}</div>@enderror

<div class="card" style="padding:20px;margin-bottom:22px">
    <form id="upForm" method="POST" action="{{ url('/admin/upload') }}" enctype="multipart/form-data">
        @csrf
        <label class="dz" id="dz" for="fileInput">
            <span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4m0 0 4 4m-4-4-4 4"/><path d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg></span>
            <h3>Drag &amp; drop files here</h3>
            <p>or click to browse — images, PDFs and documents, up to 20&nbsp;MB each</p>
            <input id="fileInput" type="file" name="files[]" multiple style="display:none">
        </label>

        <div class="picked" id="picked"></div>

        <div style="margin-top:16px;display:flex;gap:10px">
            <button type="submit" id="upBtn" class="btn primary" disabled><x-admin.icon name="upload"/> Upload</button>
            <button type="button" id="clearBtn" class="btn" style="display:none">Clear</button>
        </div>
    </form>
</div>

<div class="flex items-center justify-between mb-2">
    <span class="font-semibold" style="color:var(--text)">Recent uploads</span>
    <a class="link text-[13px]" href="{{ url('/admin/m/media') }}">View all in Media Library →</a>
</div>

<div class="toast-copy" id="toastCopy">Link copied</div>

<div class="grid-media">
    @forelse ($recent as $m)
        @php $url = \Illuminate\Support\Facades\Storage::disk($m->disk ?? 'public')->url($m->path); $rel = '/storage/' . ltrim($m->path, '/'); @endphp
        <div class="mtile" data-url="{{ $rel }}">
            <div class="actions-overlay">
                <button class="action-btn" onclick="copyMediaLink(this)" title="Copy link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                </button>
                <form method="POST" action="{{ route('admin.upload.destroy', $m->id) }}" onsubmit="return confirm('Are you sure you want to permanently delete this file: {{ addslashes($m->original_name) }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn del" title="Delete file">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </form>
            </div>
            <div class="thumb">
                @if (str_starts_with((string) $m->mime_type, 'image/'))
                    <img src="{{ $rel }}" alt="{{ $m->original_name }}" loading="lazy" onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'ext\'>{{ $m->extension ?: 'img' }}</span>'">
                @else
                    <span class="ext">{{ $m->extension ?: 'file' }}</span>
                @endif
            </div>
            <div class="meta">
                <div class="n" title="{{ $m->original_name }}">{{ $m->original_name }}</div>
                @php $b=(int)$m->size;$u=['B','KB','MB','GB'];$k=0;while($b>=1024&&$k<3){$b/=1024;$k++;} @endphp
                <div class="s">{{ round($b,1).' '.$u[$k] }}{{ $m->width ? ' · '.$m->width.'×'.$m->height : '' }}</div>
            </div>
        </div>
    @empty
        <div class="empty" style="grid-column:1/-1">No uploads yet.</div>
    @endforelse
</div>


<script>
    (function () {
        const dz = document.getElementById('dz');
        const input = document.getElementById('fileInput');
        const picked = document.getElementById('picked');
        const upBtn = document.getElementById('upBtn');
        const clearBtn = document.getElementById('clearBtn');
        const toast = document.getElementById('toastCopy');
        let toastTimer;

        function human(b){const u=['B','KB','MB','GB'];let k=0;while(b>=1024&&k<3){b/=1024;k++;}return (Math.round(b*10)/10)+' '+u[k];}

        function render() {
            const files = input.files;
            picked.innerHTML = '';
            if (!files || !files.length) { upBtn.disabled = true; clearBtn.style.display='none'; return; }
            upBtn.disabled = false; clearBtn.style.display='inline-flex';
            Array.from(files).forEach(f => {
                const row = document.createElement('div');
                row.className = 'row';
                row.innerHTML = '<span class="nm"></span><span style="color:var(--text-muted)"></span>';
                row.children[0].textContent = f.name;
                row.children[1].textContent = human(f.size);
                picked.appendChild(row);
            });
        }

        input.addEventListener('change', render);
        clearBtn.addEventListener('click', () => { input.value = ''; render(); });

        ['dragenter','dragover'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.add('over'); }));
        ['dragleave','drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); dz.classList.remove('over'); }));
        dz.addEventListener('drop', e => {
            const dt = new DataTransfer();
            Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
            input.files = dt.files;
            render();
        });
    })();

    function copyMediaLink(btn) {
        const url = btn.closest('.mtile').dataset.url;
        if (!url) return;
        navigator.clipboard.writeText(url).then(() => {
            btn.classList.add('copied');
            const toast = document.getElementById('toastCopy');
            toast.textContent = 'Link copied';
            toast.classList.add('show');
            clearTimeout(toast._t);
            toast._t = setTimeout(() => toast.classList.remove('show'), 1800);
            setTimeout(() => btn.classList.remove('copied'), 1200);
        }).catch(() => {
            const toast = document.getElementById('toastCopy');
            toast.textContent = 'Failed to copy';
            toast.classList.add('show');
            clearTimeout(toast._t);
            toast._t = setTimeout(() => toast.classList.remove('show'), 1800);
        });
    }
</script>

@endsection
