@extends('admin.layout')

@section('title', 'SEO')
@section('subtitle', 'Search-engine health across the site')

@section('actions')
    <a href="{{ route('admin.seo.audit') }}" class="btn primary"><x-admin.icon name="bolt"/> Run full audit</a>
    <a href="{{ route('admin.seo.bulk') }}" class="btn"><x-admin.icon name="pencil"/> Bulk edit</a>
    <a href="{{ route('admin.notfound') }}" class="btn"><x-admin.icon name="bolt"/> 404 monitor</a>
    <a href="{{ url('/admin/m/redirects') }}" class="btn"><x-admin.icon name="globe"/> Manage redirects</a>
@endsection

@section('content')

@php
    $scoreColor = $score >= 80 ? 'var(--success)' : ($score >= 50 ? 'var(--warning)' : 'var(--danger)');
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-4" style="margin-bottom:16px">
    <div class="kpi" style="display:flex;align-items:center;gap:16px">
        <div style="width:64px;height:64px;border-radius:50%;display:grid;place-items:center;flex:none;background:conic-gradient({{ $scoreColor }} {{ $score * 3.6 }}deg, var(--surface-hover) 0)">
            <div style="width:50px;height:50px;border-radius:50%;background:var(--surface);display:grid;place-items:center;font-weight:700;font-size:16px;color:{{ $scoreColor }}">{{ $score }}</div>
        </div>
        <div><div class="kpi__label">SEO score</div><div class="text-[13px]" style="color:var(--text-muted)">meta titles + descriptions</div></div>
    </div>
    <div class="kpi"><div class="kpi__label">Published pages</div><div class="kpi__value" style="margin-top:4px">{{ $published }}</div></div>
    <div class="kpi"><div class="kpi__label">Missing description</div><div class="kpi__value" style="margin-top:4px;color:{{ $missing > 0 ? 'var(--warning)' : 'var(--text)' }}">{{ $missing }}</div></div>
    <div class="kpi"><div class="kpi__label">Active redirects</div><div class="kpi__value" style="margin-top:4px">{{ $redirectsActive }}</div></div>
</div>

{{-- Sitemap / indexing status --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4" style="margin-bottom:16px">
    <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px">
        <span class="status-dot ok"></span>
        <div style="flex:1"><div style="font-weight:600;color:var(--text)">Sitemap</div><div class="muted" style="font-size:13px">{{ $sitemapUrls }} URLs · <a class="link" href="{{ url('/sitemap.xml') }}" target="_blank">/sitemap.xml</a></div></div>
    </div>
    <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px">
        <span class="status-dot ok"></span>
        <div style="flex:1"><div style="font-weight:600;color:var(--text)">Robots</div><div class="muted" style="font-size:13px"><a class="link" href="{{ url('/robots.txt') }}" target="_blank">/robots.txt</a></div></div>
    </div>
    <div class="card" style="padding:16px;display:flex;align-items:center;gap:12px">
        <span class="status-dot {{ $missing > 0 ? 'warn' : 'ok' }}"></span>
        <div style="flex:1"><div style="font-weight:600;color:var(--text)">Meta coverage</div><div class="muted" style="font-size:13px">{{ $covered }}/{{ $published }} pages have a description</div></div>
    </div>
</div>

{{-- Broken link scan results --}}
@if (is_array($broken))
    <div class="widget" style="margin-bottom:16px">
        <div class="widget__head">
            <x-admin.icon name="bolt" style="width:18px;height:18px;color:{{ count($broken) ? 'var(--warning)' : 'var(--success)' }}"/>
            <span class="widget__title">Link scan</span>
            <span class="widget__sub">· {{ count($broken) }} internal {{ \Illuminate\Support\Str::plural('link', count($broken)) }} with no matching page</span>
        </div>
        <div>
            @forelse ($broken as $b)
                <div class="health-row" style="padding-left:18px;padding-right:18px">
                    <span class="status-dot warn"></span>
                    <span style="flex:1"><code style="color:var(--danger)">{{ $b['link'] }}</code> <span class="muted">on</span> <strong>{{ $b['page'] }}</strong></span>
                </div>
            @empty
                <div class="empty">No broken internal links found. 🎉</div>
            @endforelse
        </div>
    </div>
@else
    <p class="muted" style="font-size:12.5px;margin-bottom:16px">Tip: run a link scan to find internal links pointing to pages that don't exist.</p>
@endif

{{-- Per-page coverage --}}
<div class="card" style="overflow:hidden">
    <table>
        <thead><tr><th>Page</th><th>Status</th><th>Meta title</th><th>Meta description</th><th style="text-align:right">Edit</th></tr></thead>
        <tbody>
            @forelse ($rows as $r)
                <tr>
                    <td><strong>{{ $r['title'] }}</strong> <span class="muted">/{{ $r['slug'] }}</span></td>
                    <td><span class="badge {{ $r['status'] }}">{{ $r['status'] }}</span></td>
                    <td>@if ($r['hasTitle'])<span class="badge published">Set</span>@else<span class="badge archived">Missing</span>@endif</td>
                    <td>@if ($r['hasDesc'])<span class="badge published">Set</span>@else<span class="badge {{ $r['status'] === 'published' ? 'new' : 'archived' }}">Missing</span>@endif</td>
                    <td style="text-align:right"><a class="btn-sm" href="{{ route('admin.seo.edit', $r['id']) }}">Edit SEO</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No pages yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="muted" style="font-size:12.5px;margin-top:12px">Each page auto-generates complete metadata; use <strong>Edit SEO</strong> to override the title, description, social image, or indexing. Search-ranking metrics (impressions, position) require a Search Console connection, which isn't wired up yet.</p>

@endsection
