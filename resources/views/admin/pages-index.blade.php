@extends('admin.layout')

@section('title', 'Pages')
@section('subtitle', $pages->count() . ' ' . \Illuminate\Support\Str::plural('page', $pages->count()) . ' configured')

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success, #16a34a);background:rgba(22,163,74,0.08);color:#15803d;padding:14px 18px;margin-bottom:20px;font-size:14px;font-weight:600;border-radius:12px;display:flex;align-items:center;gap:10px">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="card" style="border-color:var(--danger, #dc2626);background:rgba(220,38,38,0.08);color:#b91c1c;padding:14px 18px;margin-bottom:20px;font-size:14px;font-weight:600;border-radius:12px;display:flex;align-items:center;gap:10px">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="card" style="border-color:var(--danger, #dc2626);background:rgba(220,38,38,0.08);color:#b91c1c;padding:14px 18px;margin-bottom:20px;font-size:14px;border-radius:12px">
        <ul style="margin:0;padding-left:20px">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Top Toolbar --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;gap:16px;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:12px;flex:1;max-width:400px">
        <div style="position:relative;width:100%">
            <input type="text" id="pageSearch" placeholder="Search pages by title or slug..." 
                style="width:100%;padding:10px 16px 10px 38px;border-radius:10px;border:1px solid var(--border);background:var(--bg-card);color:var(--text);font-size:13.5px;outline:none"
                onkeyup="filterPages()">
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </div>
    </div>

    <button type="button" class="btn primary" onclick="openCreateModal()" style="display:inline-flex;align-items:center;gap:8px;font-weight:600;padding:10px 20px;border-radius:10px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add New Page
    </button>
</div>

{{-- Pages Table Card --}}
<div class="card" style="overflow:hidden;border-radius:16px;border:1px solid var(--border);box-shadow:0 4px 20px -4px rgba(0,0,0,0.05)">
    <table style="width:100%;border-collapse:collapse" id="pagesTable">
        <thead>
            <tr style="background:var(--bg-subtle, rgba(0,0,0,0.02));border-bottom:1px solid var(--border)">
                <th style="padding:14px 20px;text-align:left;font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted)">Page Title</th>
                <th style="padding:14px 20px;text-align:left;font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted)">Slug / URL</th>
                <th style="padding:14px 20px;text-align:left;font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted)">Status</th>
                <th style="padding:14px 20px;text-align:right;font-size:12.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted)">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pages as $page)
                @php
                    $isHome = in_array($page->slug, ['home', 'index']);
                    $pageUrl = $isHome ? url('/') : url('/' . ltrim($page->slug, '/'));
                    $metaTitle = $page->seo['meta_title'] ?? $page->title;
                    $metaDesc = $page->seo['meta_description'] ?? '';
                @endphp
                <tr class="page-row" style="border-bottom:1px solid var(--border);transition:background 0.15s ease">
                    <td style="padding:16px 20px;vertical-align:middle">
                        <div style="font-weight:700;font-size:14.5px;color:var(--text)">
                            {{ $page->title }}
                            @if($isHome)
                                <span style="font-size:11px;font-weight:700;background:rgba(217,119,6,0.15);color:#d97706;padding:2px 8px;border-radius:999px;margin-left:6px;border:1px solid rgba(217,119,6,0.3)">HOME</span>
                            @endif
                        </div>
                        @if(!empty($metaDesc))
                            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $metaDesc }}
                            </div>
                        @endif
                    </td>
                    <td style="padding:16px 20px;vertical-align:middle">
                        <a href="{{ $pageUrl }}" target="_blank" style="font-family:monospace;font-size:13px;color:var(--primary, #2563eb);text-decoration:none;display:inline-flex;align-items:center;gap:4px">
                            <span>/{{ $page->slug }}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        </a>
                    </td>
                    <td style="padding:16px 20px;vertical-align:middle">
                        @if($page->status === 'published')
                            <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(22,163,74,0.12);color:#16a34a;font-weight:700;font-size:12px;padding:4px 12px;border-radius:999px;border:1px solid rgba(22,163,74,0.25)">
                                <span style="width:6px;height:6px;border-radius:50%;background:#16a34a"></span>
                                Published
                            </span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(100,116,139,0.12);color:#64748b;font-weight:700;font-size:12px;padding:4px 12px;border-radius:999px;border:1px solid rgba(100,116,139,0.25)">
                                <span style="width:6px;height:6px;border-radius:50%;background:#64748b"></span>
                                Draft
                            </span>
                        @endif
                    </td>
                    <td style="padding:16px 20px;text-align:right;vertical-align:middle;white-space:nowrap">
                        <div style="display:inline-flex;align-items:center;gap:6px">
                            {{-- 👁️ View Live --}}
                            <a class="btn-sm" href="{{ $pageUrl }}" target="_blank" rel="noopener" title="View Public Page"
                               style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:8px;text-decoration:none">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                View
                            </a>

                            {{-- 🎨 Page Builder --}}
                            <a class="btn-sm primary" href="{{ url('/admin/pages/'.$page->id.'/edit') }}" title="Open Visual Page Builder"
                               style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;border-radius:8px;text-decoration:none;font-weight:700">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                Builder
                            </a>

                            {{-- ⚙️ Settings / Edit Meta --}}
                            <button type="button" class="btn-sm" title="Edit Page Settings"
                                onclick="openEditModal('{{ $page->id }}', '{{ addslashes($page->title) }}', '{{ $page->slug }}', '{{ $page->status }}', '{{ addslashes($metaTitle) }}', '{{ addslashes($metaDesc) }}')"
                                style="display:inline-flex;align-items:center;gap:4px;padding:6px 10px;border-radius:8px;cursor:pointer">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            </button>

                            {{-- 🗑️ Delete --}}
                            @if(!$isHome)
                                <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete page &quot;{{ addslashes($page->title) }}&quot;? This will remove all its sections and widgets.');" style="display:inline;margin:0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm" title="Delete Page"
                                        style="display:inline-flex;align-items:center;padding:6px 10px;border-radius:8px;color:#dc2626;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2);cursor:pointer">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="empty" style="text-align:center;padding:40px;color:var(--text-muted)">No pages found. Click "+ Add New Page" to create one.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ➕ MODAL: ADD NEW PAGE --}}
<div id="createPageModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:20px">
    <div style="background:var(--bg-card, #ffffff);border:1px solid var(--border);width:100%;max-width:550px;border-radius:18px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.3)">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h3 style="margin:0;font-size:17px;font-weight:700;color:var(--text)">Create New Page</h3>
            <button type="button" onclick="closeCreateModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted)">✕</button>
        </div>

        <form action="{{ route('admin.pages.store') }}" method="POST" style="padding:24px">
            @csrf
            
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Page Title <span style="color:#dc2626">*</span></label>
                <input type="text" name="title" id="createTitle" required placeholder="e.g. Science Labs & Innovation Center"
                    oninput="autoGenerateSlug(this.value)"
                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">URL Slug <span style="color:#dc2626">*</span></label>
                <div style="display:flex;align-items:center">
                    <span style="padding:10px 12px;background:var(--bg-subtle, rgba(0,0,0,0.04));border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;font-size:13px;color:var(--text-muted)">/</span>
                    <input type="text" name="slug" id="createSlug" placeholder="science-labs"
                        style="flex:1;padding:10px 14px;border-radius:0 8px 8px 0;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Publication Status</label>
                <select name="status" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
                    <option value="published" selected>Published (Publicly Viewable)</option>
                    <option value="draft">Draft (Private in Builder Only)</option>
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Meta Title (SEO)</label>
                <input type="text" name="meta_title" id="createMetaTitle" placeholder="Page Title for Google Search"
                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
            </div>

            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Meta Description (SEO)</label>
                <textarea name="meta_description" rows="2" placeholder="Brief summary for search engines..."
                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13.5px;outline:none"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:12px">
                <button type="button" class="btn" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" class="btn primary" style="font-weight:700">Create &amp; Open Builder →</button>
            </div>
        </form>
    </div>
</div>

{{-- ⚙️ MODAL: EDIT PAGE SETTINGS --}}
<div id="editPageModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.7);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:20px">
    <div style="background:var(--bg-card, #ffffff);border:1px solid var(--border);width:100%;max-width:550px;border-radius:18px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.3)">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <h3 style="margin:0;font-size:17px;font-weight:700;color:var(--text)">Edit Page Settings</h3>
            <button type="button" onclick="closeEditModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--text-muted)">✕</button>
        </div>

        <form id="editPageForm" method="POST" style="padding:24px">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Page Title <span style="color:#dc2626">*</span></label>
                <input type="text" name="title" id="editTitle" required
                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">URL Slug <span style="color:#dc2626">*</span></label>
                <div style="display:flex;align-items:center">
                    <span style="padding:10px 12px;background:var(--bg-subtle, rgba(0,0,0,0.04));border:1px solid var(--border);border-right:none;border-radius:8px 0 0 8px;font-size:13px;color:var(--text-muted)">/</span>
                    <input type="text" name="slug" id="editSlug" required
                        style="flex:1;padding:10px 14px;border-radius:0 8px 8px 0;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Publication Status</label>
                <select name="status" id="editStatus" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Meta Title (SEO)</label>
                <input type="text" name="meta_title" id="editMetaTitle"
                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:14px;outline:none">
            </div>

            <div style="margin-bottom:24px">
                <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px;color:var(--text)">Meta Description (SEO)</label>
                <textarea name="meta_description" id="editMetaDesc" rows="2"
                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--text);font-size:13.5px;outline:none"></textarea>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:12px">
                <button type="button" class="btn" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn primary" style="font-weight:700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function filterPages() {
    const query = document.getElementById('pageSearch').value.toLowerCase().trim();
    const rows = document.querySelectorAll('.page-row');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (query === '' || text.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function autoGenerateSlug(title) {
    const slugInput = document.getElementById('createSlug');
    const metaTitleInput = document.getElementById('createMetaTitle');
    
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
        
    slugInput.value = slug;
    metaTitleInput.value = title;
}

function openCreateModal() {
    document.getElementById('createPageModal').style.display = 'flex';
    document.getElementById('createTitle').focus();
}

function closeCreateModal() {
    document.getElementById('createPageModal').style.display = 'none';
}

function openEditModal(id, title, slug, status, metaTitle, metaDesc) {
    const form = document.getElementById('editPageForm');
    form.action = `/admin/pages/${id}/meta`;
    
    document.getElementById('editTitle').value = title;
    document.getElementById('editSlug').value = slug;
    document.getElementById('editStatus').value = status;
    document.getElementById('editMetaTitle').value = metaTitle;
    document.getElementById('editMetaDesc').value = metaDesc;
    
    document.getElementById('editPageModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editPageModal').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
    }
});
</script>

@endsection
