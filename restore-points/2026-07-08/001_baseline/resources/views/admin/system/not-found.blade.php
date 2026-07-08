@extends('admin.layout')

@section('title', '404 Monitor')
@section('subtitle', 'Missing URLs hit by visitors & search engines')

@section('actions')
    <a href="{{ route('admin.seo') }}" class="btn"><x-admin.icon name="chevron-left"/> SEO</a>
    <a href="{{ url('/admin/m/redirects') }}" class="btn"><x-admin.icon name="globe"/> All redirects</a>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-4" style="margin-bottom:16px">
    <div class="kpi"><div class="kpi__label">Unresolved 404s</div><div class="kpi__value" style="margin-top:4px;color:{{ count($logs) ? 'var(--warning)' : 'var(--text)' }}">{{ count($logs) }}</div></div>
    <div class="kpi"><div class="kpi__label">Total hits (unresolved)</div><div class="kpi__value" style="margin-top:4px">{{ number_format($totalHits) }}</div></div>
    <div class="kpi"><div class="kpi__label">Resolved</div><div class="kpi__value" style="margin-top:4px;color:var(--success)">{{ $resolvedNum }}</div></div>
</div>

@if (count($logs) === 0)
    <div class="card"><div class="empty">No unresolved 404s. 🎉 Either nothing has 404'd yet, or you've redirected them all.</div></div>
@else
<div class="card" style="overflow:hidden">
    <table>
        <thead>
            <tr>
                <th>Missing URL</th>
                <th style="width:70px">Hits</th>
                <th style="width:130px">Last seen</th>
                <th style="width:420px">Fix → redirect to</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>
                        <code style="color:var(--danger)">/{{ ltrim($log->path, '/') }}</code>
                        @if ($log->referrer)<div class="muted" style="font-size:11px;margin-top:3px">ref: {{ \Illuminate\Support\Str::limit($log->referrer, 60) }}</div>@endif
                    </td>
                    <td><strong>{{ $log->hits }}</strong></td>
                    <td class="muted" style="font-size:12px">{{ optional($log->last_seen_at)->diffForHumans() ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.notfound.redirect', $log) }}" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">
                            @csrf
                            <input list="page-targets" name="to_path" required placeholder="/target-page or full URL"
                                   style="flex:1;min-width:170px;padding:7px 10px;border:1px solid var(--border-strong);border-radius:8px;background:var(--surface);color:var(--text);font:inherit;font-size:13px">
                            <select name="status_code" style="padding:7px 8px;border:1px solid var(--border-strong);border-radius:8px;background:var(--surface);color:var(--text);font-size:12px">
                                <option value="301">301</option>
                                <option value="302">302</option>
                            </select>
                            <button type="submit" class="btn-sm primary">Redirect</button>
                        </form>
                        <form method="POST" action="{{ route('admin.notfound.ignore', $log) }}" style="margin-top:4px">
                            @csrf
                            <button type="submit" class="btn-sm" style="font-size:11px;color:var(--text-muted)">Dismiss</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Autocomplete of published page slugs for the redirect target --}}
<datalist id="page-targets">
    <option value="/"></option>
    @foreach ($pages as $p)
        <option value="/{{ $p->slug === 'home' ? '' : $p->slug }}">{{ $p->title }}</option>
    @endforeach
</datalist>
@endif

<p class="muted" style="font-size:12.5px;margin-top:12px">404s are recorded automatically (bots & asset requests are filtered out). Creating a redirect here adds an active 301/302 that takes effect immediately and resolves the entry.</p>

@endsection
