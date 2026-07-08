@extends('admin.layout')

@section('title', 'Bulk SEO')
@section('subtitle', 'Edit titles, descriptions & indexing for every page')

@section('actions')
    <a href="{{ route('admin.seo') }}" class="btn"><x-admin.icon name="chevron-left"/> SEO overview</a>
    <a href="{{ route('admin.seo.audit') }}" class="btn"><x-admin.icon name="bolt"/> Run audit</a>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<style>
    .bulk-input{width:100%;padding:7px 9px;border:1px solid var(--border-strong);border-radius:8px;background:var(--surface);color:var(--text);font:inherit;font-size:13px}
    .bulk-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .bulk-ta{min-height:38px;resize:vertical;line-height:1.4}
    .bulk-count{font-size:10.5px;color:var(--text-muted);float:right}
    .bulk-count.over{color:var(--danger)}
    table.bulk td{vertical-align:top;padding:10px 12px}
    table.bulk th{text-align:left;padding:10px 12px}
</style>

<form method="POST" action="{{ route('admin.seo.bulk.save') }}">
    @csrf
    @method('PUT')

    <div class="card" style="overflow:hidden">
        <table class="bulk">
            <thead>
                <tr>
                    <th style="width:200px">Page</th>
                    <th>Meta title <span class="muted" style="font-weight:400">(blank = auto)</span></th>
                    <th>Meta description <span class="muted" style="font-weight:400">(blank = auto)</span></th>
                    <th style="width:80px;text-align:center">No-index</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $r)
                    <tr>
                        <td>
                            <strong style="font-size:13px">{{ $r['title'] }}</strong>
                            <div class="muted" style="font-size:11px">/{{ $r['slug'] }}</div>
                            <span class="badge {{ $r['status'] }}" style="margin-top:4px;display:inline-block">{{ $r['status'] }}</span>
                        </td>
                        <td>
                            <input class="bulk-input bulk-counted" data-max="60" type="text"
                                   name="pages[{{ $r['id'] }}][title]" value="{{ $r['metaTitle'] }}"
                                   placeholder="{{ $r['autoTitle'] }}">
                            <span class="bulk-count"></span>
                        </td>
                        <td>
                            <textarea class="bulk-input bulk-ta bulk-counted" data-max="160"
                                      name="pages[{{ $r['id'] }}][description]"
                                      placeholder="Auto-generated from page content">{{ $r['metaDesc'] }}</textarea>
                            <span class="bulk-count"></span>
                        </td>
                        <td style="text-align:center">
                            <input type="hidden" name="pages[{{ $r['id'] }}][noindex]" value="0">
                            <input type="checkbox" name="pages[{{ $r['id'] }}][noindex]" value="1" {{ $r['noindex'] ? 'checked' : '' }}
                                   style="width:18px;height:18px;accent-color:var(--primary);margin-top:8px">
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No pages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="position:sticky;bottom:0;background:var(--surface);border-top:1px solid var(--border);padding:14px 0;margin-top:8px;display:flex;gap:10px;align-items:center">
        <button type="submit" class="btn primary"><x-admin.icon name="cog"/> Save all changes</button>
        <span class="muted" style="font-size:12.5px">Edits merge into each page — your social images & schema types are preserved.</span>
    </div>
</form>

<script>
(function () {
    document.querySelectorAll('.bulk-counted').forEach(function (el) {
        var max = parseInt(el.getAttribute('data-max'), 10);
        var c = el.parentElement.querySelector('.bulk-count');
        var upd = function () {
            var n = el.value.length;
            c.textContent = n + '/' + max;
            c.classList.toggle('over', n > max);
        };
        el.addEventListener('input', upd); upd();
    });
})();
</script>

@endsection
