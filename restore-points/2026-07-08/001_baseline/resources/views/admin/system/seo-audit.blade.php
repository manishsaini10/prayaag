@extends('admin.layout')

@section('title', 'SEO Audit')
@section('subtitle', 'On-page health across all published pages')

@section('actions')
    <a href="{{ route('admin.seo') }}" class="btn"><x-admin.icon name="chevron-left"/> SEO overview</a>
    <a href="{{ route('admin.seo.audit') }}" class="btn primary"><x-admin.icon name="bolt"/> Re-run audit</a>
@endsection

@section('content')

@php
    $score = $report['score'];
    $scoreColor = $score >= 80 ? 'var(--success)' : ($score >= 50 ? 'var(--warning)' : 'var(--danger)');
    $crit = $report['critical']; $warn = $report['warnings']; $pass = $report['passed'];
    $statusMeta = [
        'crit' => ['dot' => 'down', 'color' => 'var(--danger)', 'label' => 'Critical'],
        'warn' => ['dot' => 'warn', 'color' => 'var(--warning)', 'label' => 'Warning'],
        'pass' => ['dot' => 'ok',   'color' => 'var(--success)', 'label' => 'Passed'],
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-4 gap-4" style="margin-bottom:18px">
    <div class="kpi" style="display:flex;align-items:center;gap:16px">
        <div style="width:66px;height:66px;border-radius:50%;display:grid;place-items:center;flex:none;background:conic-gradient({{ $scoreColor }} {{ $score * 3.6 }}deg, var(--surface-hover) 0)">
            <div style="width:52px;height:52px;border-radius:50%;background:var(--surface);display:grid;place-items:center;font-weight:700;font-size:17px;color:{{ $scoreColor }}">{{ $score }}</div>
        </div>
        <div><div class="kpi__label">SEO score</div><div class="text-[13px]" style="color:var(--text-muted)">{{ $report['pages'] }} pages audited</div></div>
    </div>
    <div class="kpi"><div class="kpi__label">Critical issues</div><div class="kpi__value" style="margin-top:4px;color:{{ count($crit) ? 'var(--danger)' : 'var(--text)' }}">{{ count($crit) }}</div></div>
    <div class="kpi"><div class="kpi__label">Warnings</div><div class="kpi__value" style="margin-top:4px;color:{{ count($warn) ? 'var(--warning)' : 'var(--text)' }}">{{ count($warn) }}</div></div>
    <div class="kpi"><div class="kpi__label">Passed</div><div class="kpi__value" style="margin-top:4px;color:var(--success)">{{ count($pass) }}</div></div>
</div>

@php
    $renderGroup = function ($title, $checks, $meta) {
        if (! count($checks)) return;
        echo '<div class="seo-h" style="font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:'.$meta['color'].';margin:22px 0 10px">'.$title.' · '.count($checks).'</div>';
        foreach ($checks as $c) {
            echo '<div class="card" style="padding:0;margin-bottom:10px;overflow:hidden">';
            echo '<div style="display:flex;align-items:center;gap:10px;padding:13px 16px">';
            echo '<span class="status-dot '.$meta['dot'].'"></span>';
            echo '<span style="flex:1;font-weight:600;color:var(--text)">'.e($c['label']).'</span>';
            echo '<span class="muted" style="font-size:12.5px">'.e($c['summary']).'</span>';
            echo '</div>';
            if (! empty($c['items'])) {
                echo '<div style="border-top:1px solid var(--border);padding:10px 16px;background:var(--surface-hover)">';
                foreach ($c['items'] as $it) {
                    echo '<div style="display:flex;gap:10px;align-items:center;padding:3px 0;font-size:12.5px">';
                    echo '<a class="link" href="'.e($it['path']).'" target="_blank" style="min-width:160px">'.e($it['title']).'</a>';
                    echo '<code class="muted" style="font-size:11px">'.e($it['path']).'</code>';
                    if (! empty($it['note'])) echo '<span class="muted" style="margin-left:auto;font-size:11px">'.e($it['note']).'</span>';
                    echo '</div>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
    };
    $renderGroup('Critical', $crit, $statusMeta['crit']);
    $renderGroup('Warnings', $warn, $statusMeta['warn']);
    $renderGroup('Passed', $pass, $statusMeta['pass']);
@endphp

@if (count($report['checks']) === 0)
    <div class="card"><div class="empty">No published pages to audit yet.</div></div>
@endif

<p class="muted" style="font-size:12.5px;margin-top:14px">The audit reflects the <strong>effective</strong> metadata each page ships (per-page overrides + auto-generation). Fix issues via <a class="link" href="{{ route('admin.seo') }}">Edit SEO</a> on each page, or set site-wide defaults in <a class="link" href="{{ url('/admin/settings') }}">Settings → SEO</a>.</p>

@endsection
