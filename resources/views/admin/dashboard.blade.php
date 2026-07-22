@extends('admin.layout')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of content, admissions and site activity')

@section('actions')
    <div class="flex items-center gap-2">
        <a href="{{ url('/') }}" target="_blank" class="btn"><x-admin.icon name="globe"/> View site</a>
        <a href="{{ url('/admin/pages') }}" class="btn primary"><x-admin.icon name="plus"/> New page</a>
    </div>
@endsection

@section('content')

{{-- ===================== ROW 1 · KPI CARDS ===================== --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4">
    @foreach ($kpis as $kpi)
        @php
            $spark = $kpi['spark'] ?? [];
            $n = max(count($spark), 1);
            $max = max($spark ?: [0]) ?: 1;
            $pts = [];
            foreach ($spark as $i => $v) {
                $x = $n > 1 ? round($i / ($n - 1) * 100, 2) : 0;
                $y = round(28 - ($v / $max) * 22 - 3, 2);
                $pts[] = "$x,$y";
            }
            $poly = implode(' ', $pts);
            $stroke = $kpi['trend'] === 'down' ? 'var(--danger)' : ($kpi['trend'] === 'up' ? 'var(--success)' : 'var(--text-muted)');
            $href = $kpi['href'] ?? null;
            $tag = $href ? 'a' : 'div';
        @endphp
        <{{ $tag }} @if ($href) href="{{ $href }}" @endif class="kpi animate-in" style="text-decoration:none;display:block">
            <div class="flex items-start justify-between">
                <div class="kpi__icon"><x-admin.icon name="{{ $kpi['icon'] }}"/></div>
                @if ($kpi['delta'] != 0)
                    <span class="trend {{ $kpi['trend'] }}">{{ $kpi['trend'] === 'up' ? '▲' : '▼' }} {{ abs($kpi['delta']) }}%</span>
                @else
                    <span class="trend flat">—</span>
                @endif
            </div>
            <div class="mt-3 kpi__value">
                @if ($kpi['total'] > 0)
                    <span x-data="{n:0}" x-init="const target={{ $kpi['total'] }};const step=Math.max(1,Math.ceil(target/24));const t=setInterval(()=>{n=Math.min(target,n+step);if(n>=target)clearInterval(t)},28)" x-text="n.toLocaleString()">0</span>
                @else
                    0
                @endif
            </div>
            <div class="flex items-end justify-between gap-2 mt-1">
                <div class="kpi__label">{{ $kpi['label'] }}</div>
                <svg viewBox="0 0 100 28" preserveAspectRatio="none" style="width:64px;height:24px;overflow:visible">
                    <polyline points="{{ $poly }}" fill="none" stroke="{{ $stroke }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
                </svg>
            </div>
        </{{ $tag }}>
    @endforeach
</div>

{{-- ===================== ROW 2 · ANALYTICS ===================== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
    <div class="widget lg:col-span-2">
        <div class="widget__head">
            <x-admin.icon name="chart-line" style="width:18px;height:18px;color:var(--primary-strong)"/>
            <span class="widget__title">Engagement</span>
            <span class="widget__sub">· last 30 days</span>
            <div class="flex-1"></div>
            <div class="flex items-center gap-3 text-[12px]" style="color:var(--text-muted)">
                <span class="flex items-center gap-1.5"><span style="width:9px;height:9px;border-radius:3px;background:#6366f1"></span>Enquiries</span>
                <span class="flex items-center gap-1.5"><span style="width:9px;height:9px;border-radius:3px;background:#22c55e"></span>Applications</span>
                <span class="flex items-center gap-1.5"><span style="width:9px;height:9px;border-radius:3px;background:#f59e0b"></span>Admissions</span>
            </div>
        </div>
        <div class="widget__body"><div style="height:260px"><canvas id="chartEngagement"></canvas></div></div>
    </div>

    <div class="widget">
        <div class="widget__head">
            <x-admin.icon name="chart-bar" style="width:18px;height:18px;color:var(--primary-strong)"/>
            <span class="widget__title">Visitors</span>
            <span class="widget__sub">· 30 days</span>
        </div>
        <div class="widget__body"><div style="height:260px"><canvas id="chartVisitors"></canvas></div></div>
    </div>
</div>

{{-- Content publishing trend --}}
<div class="widget mt-4">
    <div class="widget__head">
        <x-admin.icon name="pencil" style="width:18px;height:18px;color:var(--primary-strong)"/>
        <span class="widget__title">Content publishing</span>
        <span class="widget__sub">· posts published, last 30 days</span>
    </div>
    <div class="widget__body"><div style="height:150px"><canvas id="chartContent"></canvas></div></div>
</div>

{{-- ===================== ROW 3 · ACTIVITY + HEALTH ===================== --}}
@php
    $humanize = function ($desc) {
        $parts = explode(' ', $desc, 2);
        $name = preg_replace('/(?<!^)([A-Z])/', ' $1', $parts[0]);
        $name = ucfirst(strtolower($name));
        return trim($name . ' ' . ($parts[1] ?? ''));
    };
@endphp
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
    <div class="widget lg:col-span-2">
        <div class="widget__head">
            <x-admin.icon name="bolt" style="width:18px;height:18px;color:var(--primary-strong)"/>
            <span class="widget__title">Recent activity</span>
        </div>
        <div class="widget__body" style="padding-top:6px;padding-bottom:6px">
            @forelse ($activity as $entry)
                @php
                    $map = ['pages'=>'document','posts'=>'pencil','media'=>'photo','enquiries'=>'inbox','job_applications'=>'briefcase','subscribers'=>'envelope','events'=>'calendar','notices'=>'megaphone','users'=>'users','settings'=>'cog','sliders'=>'photo','galleries'=>'collection','testimonials'=>'star','achievements'=>'star','academic_calendar'=>'calendar','mess_menus'=>'utensils'];
                    $ic = $map[$entry->log_name] ?? 'bolt';
                @endphp
                <div class="activity-item">
                    <div class="activity-dot"><x-admin.icon name="{{ $ic }}"/></div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[13.5px]" style="color:var(--text)">{{ $humanize($entry->description) }}</div>
                        <div class="text-[12px] mt-0.5" style="color:var(--text-muted)">
                            <span class="badge">{{ str_replace('_', ' ', $entry->log_name) }}</span>
                            <span class="ml-1">{{ $entry->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty text-[13px]">No activity recorded yet. Actions across the CMS will appear here.</div>
            @endforelse
        </div>
    </div>

    <div class="widget">
        <div class="widget__head">
            <x-admin.icon name="server" style="width:18px;height:18px;color:var(--primary-strong)"/>
            <span class="widget__title">System health</span>
        </div>
        <div class="widget__body" style="padding-top:4px;padding-bottom:8px">
            @foreach ($health as $h)
                <div class="health-row">
                    <span class="status-dot {{ $h['status'] }}"></span>
                    <span class="font-medium" style="color:var(--text)">{{ $h['label'] }}</span>
                    <span class="flex-1 text-right text-[12.5px]" style="color:var(--text-muted)">{{ $h['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ===================== ROW 4 · QUICK ACTIONS ===================== --}}
<h2 class="text-[13px] font-semibold uppercase tracking-wide mt-6 mb-3" style="color:var(--text-muted)">Quick actions</h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
    @php
        $actions = [
            ['plus','Create page', url('/admin/pages')],
            ['rectangle-stack','Page builder', url('/admin/pages')],
            ['inbox','Review enquiries', url('/admin/enquiries')],
            ['briefcase','Applications', url('/admin/applications')],
            ['envelope','Subscribers', url('/admin/subscribers')],
            ['chart-bar','Analytics', url('/admin/analytics')],
            ['globe','View site', url('/')],
            ['cog','Settings', '#'],
        ];
    @endphp
    @foreach ($actions as [$ic,$label,$href])
        <a href="{{ $href }}" class="quick-action" @if($href === '#') style="opacity:.55" @endif>
            <span class="quick-action__icon"><x-admin.icon name="{{ $ic }}"/></span>
            <span class="font-semibold text-[14px]" style="color:var(--text)">{{ $label }}</span>
        </a>
    @endforeach
</div>

{{-- ===================== ROW 5 · OVERVIEWS ===================== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
    {{-- Page builder --}}
    <div class="widget">
        <div class="widget__head"><x-admin.icon name="rectangle-stack" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">Page builder</span></div>
        <div class="widget__body">
            <div class="flex gap-4 mb-3">
                <div><div class="text-[22px] font-bold" style="color:var(--text)">{{ $builder['published'] }}</div><div class="text-[12px]" style="color:var(--text-muted)">Published</div></div>
                <div><div class="text-[22px] font-bold" style="color:var(--text)">{{ $builder['drafts'] }}</div><div class="text-[12px]" style="color:var(--text-muted)">Drafts</div></div>
            </div>
            @foreach ($builder['recent'] as $p)
                <a href="{{ url('/admin/pages/'.$p->id.'/edit') }}" class="flex items-center gap-2 py-1.5 text-[13px]" style="color:var(--text)">
                    <x-admin.icon name="document" style="width:15px;height:15px;color:var(--text-muted)"/>
                    <span class="flex-1 truncate">{{ $p->title }}</span>
                    <span class="badge {{ $p->status }}">{{ $p->status }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Media --}}
    <div class="widget">
        <div class="widget__head"><x-admin.icon name="photo" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">Media library</span></div>
        <div class="widget__body">
            <div class="flex gap-4 mb-3">
                <div><div class="text-[22px] font-bold" style="color:var(--text)">{{ $media['images'] }}</div><div class="text-[12px]" style="color:var(--text-muted)">Images</div></div>
                <div><div class="text-[22px] font-bold" style="color:var(--text)">{{ $media['docs'] }}</div><div class="text-[12px]" style="color:var(--text-muted)">Documents</div></div>
            </div>
            @php
                $b = $media['bytes']; $u=['B','KB','MB','GB']; $k=0;
                while($b>=1024 && $k<count($u)-1){ $b/=1024; $k++; }
            @endphp
            <div class="text-[13px]" style="color:var(--text-muted)">Storage used: <strong style="color:var(--text)">{{ round($b,1).' '.$u[$k] }}</strong></div>
        </div>
    </div>

    {{-- SEO --}}
    <div class="widget">
        <div class="widget__head"><x-admin.icon name="globe" style="width:18px;height:18px;color:var(--primary-strong)"/><span class="widget__title">SEO coverage</span></div>
        <div class="widget__body">
            @php $cov = $seo['published'] > 0 ? round($seo['covered']/$seo['published']*100) : 0; @endphp
            <div class="flex items-end gap-2"><div class="text-[28px] font-bold" style="color:var(--text)">{{ $cov }}%</div><div class="text-[12px] mb-1.5" style="color:var(--text-muted)">pages with meta description</div></div>
            <div style="height:8px;border-radius:999px;background:var(--surface-hover);overflow:hidden" class="mt-2">
                <div style="height:100%;width:{{ $cov }}%;background:linear-gradient(90deg,var(--primary),var(--primary-strong))"></div>
            </div>
            <div class="text-[13px] mt-3" style="color:var(--text-muted)">
                {{ $seo['covered'] }} of {{ $seo['published'] }} published pages optimized
                @if ($seo['missingDesc'] > 0) · <span style="color:var(--warning)">{{ $seo['missingDesc'] }} missing</span> @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        if (typeof Chart === 'undefined') return;
        const grid = 'rgba(148,163,184,.16)';
        const tick = 'rgba(148,163,184,.85)';
        Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
        Chart.defaults.color = tick;

        const charts = @json($charts);
        const area = (ctx, color) => { const g = ctx.createLinearGradient(0,0,0,240); g.addColorStop(0, color+'44'); g.addColorStop(1, color+'00'); return g; };
        const baseOpts = {
            responsive: true, maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 7, color: tick } },
                y: { beginAtZero: true, grid: { color: grid }, ticks: { precision: 0, maxTicksLimit: 5, color: tick } },
            },
            elements: { point: { radius: 0, hoverRadius: 4 }, line: { tension: .38, borderWidth: 2 } },
        };
        const emptyHTML = '<div class="empty" style="display:grid;place-items:center;height:100%;font-size:13px">No data in this period yet</div>';

        // Render a chart, or an honest empty-state when every value is zero.
        function mount(id, build, series) {
            const el = document.getElementById(id);
            if (!el) return;
            if (!series.some(v => v > 0)) { el.parentElement.innerHTML = emptyHTML; return; }
            new Chart(el, build(el));
        }

        mount('chartEngagement', (el) => ({
            type: 'line',
            data: { labels: charts.enquiries.labels, datasets: [
                { label: 'Enquiries', data: charts.enquiries.data, borderColor: '#6366f1', backgroundColor: area(el.getContext('2d'), '#6366f1'), fill: true },
                { label: 'Applications', data: charts.applications.data, borderColor: '#22c55e', backgroundColor: 'transparent', fill: false },
                { label: 'Admissions', data: charts.admissions.data, borderColor: '#f59e0b', backgroundColor: 'transparent', fill: false },
            ] },
            options: baseOpts,
        }), [...charts.enquiries.data, ...charts.applications.data, ...charts.admissions.data]);

        mount('chartVisitors', (el) => ({
            type: 'line',
            data: { labels: charts.visitors.labels, datasets: [
                { label: 'Visitors', data: charts.visitors.data, borderColor: '#4f46e5', backgroundColor: area(el.getContext('2d'), '#4f46e5'), fill: true },
            ] },
            options: baseOpts,
        }), charts.visitors.data);

        mount('chartContent', (el) => ({
            type: 'line',
            data: { labels: charts.content.labels, datasets: [
                { label: 'Posts', data: charts.content.data, borderColor: '#16a34a', backgroundColor: area(el.getContext('2d'), '#16a34a'), fill: true },
            ] },
            options: baseOpts,
        }), charts.content.data);
    })();
</script>
@endpush
@endsection
