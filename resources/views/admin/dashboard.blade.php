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

@php
    /* ── Greeting ── */
    $hour = now()->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $userName = explode(' ', auth()->user()->name ?? 'Admin')[0];

    /* ── KPI colour palette ── */
    $kpiPalette = [
        ['from' => '#6366f1', 'to' => '#8b5cf6', 'soft' => '#f0f0ff', 'icon_color' => '#6366f1'],
        ['from' => '#3b82f6', 'to' => '#0ea5e9', 'soft' => '#eff8ff', 'icon_color' => '#3b82f6'],
        ['from' => '#10b981', 'to' => '#059669', 'soft' => '#f0fdf4', 'icon_color' => '#10b981'],
        ['from' => '#f59e0b', 'to' => '#f97316', 'soft' => '#fff7ed', 'icon_color' => '#f59e0b'],
        ['from' => '#ec4899', 'to' => '#f43f5e', 'soft' => '#fff0f5', 'icon_color' => '#ec4899'],
        ['from' => '#8b5cf6', 'to' => '#a855f7', 'soft' => '#faf0ff', 'icon_color' => '#8b5cf6'],
        ['from' => '#14b8a6', 'to' => '#06b6d4', 'soft' => '#f0fdfa', 'icon_color' => '#14b8a6'],
        ['from' => '#ef4444', 'to' => '#f97316', 'soft' => '#fff1f1', 'icon_color' => '#ef4444'],
        ['from' => '#6366f1', 'to' => '#3b82f6', 'soft' => '#eef4ff', 'icon_color' => '#6366f1'],
        ['from' => '#22c55e', 'to' => '#10b981', 'soft' => '#f0fdf4', 'icon_color' => '#22c55e'],
    ];
@endphp

{{-- ══════════════════════════════════════════════════════
     HERO GREETING BAR
═══════════════════════════════════════════════════════ --}}
<div class="dash-hero mb-6 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
     style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#ec4899 100%)">
    <div>
        <p class="text-indigo-200 text-sm font-medium mb-0.5">{{ $greeting }}, {{ $userName }} 👋</p>
        <h2 class="text-white text-2xl sm:text-3xl font-bold tracking-tight">Dashboard Overview</h2>
        <p class="text-indigo-200 text-xs mt-1">{{ now()->format('l, d F Y') }} · All systems operational</p>
    </div>
    <div class="flex items-center gap-3 flex-shrink-0">
        <a href="{{ url('/') }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/15 hover:bg-white/25 text-white text-sm font-semibold border border-white/20 transition-all">
            <x-admin.icon name="globe" style="width:15px;height:15px"/>
            View Site
        </a>
        <a href="{{ url('/admin/pages') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-indigo-700 text-sm font-bold hover:bg-indigo-50 transition-all shadow-md">
            <x-admin.icon name="plus" style="width:15px;height:15px"/>
            New Page
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 1 · KPI CARDS (2 cols mobile → 3 sm → 5 lg)
═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-5">
    @foreach ($kpis as $i => $kpi)
        @php
            $pal   = $kpiPalette[$i % count($kpiPalette)];
            $spark = $kpi['spark'] ?? [];
            $n     = max(count($spark), 1);
            $max   = max($spark ?: [0]) ?: 1;
            $pts   = [];
            foreach ($spark as $si => $v) {
                $x    = $n > 1 ? round($si / ($n - 1) * 100, 2) : 0;
                $y    = round(26 - ($v / $max) * 20 - 3, 2);
                $pts[] = "$x,$y";
            }
            $poly  = implode(' ', $pts);
            $href  = $kpi['href'] ?? null;
            $tag   = $href ? 'a' : 'div';
            $trend = $kpi['trend'];
        @endphp
        <{{ $tag }}
            @if($href) href="{{ $href }}" @endif
            class="dash-kpi group relative overflow-hidden rounded-2xl p-4 flex flex-col gap-3 transition-all duration-200"
            style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06);text-decoration:none;">

            {{-- Gradient accent bar top --}}
            <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl"
                 style="background:linear-gradient(90deg,{{ $pal['from'] }},{{ $pal['to'] }})"></div>

            {{-- Icon + Trend --}}
            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                     style="background:{{ $pal['soft'] }};color:{{ $pal['icon_color'] }}">
                    <x-admin.icon name="{{ $kpi['icon'] }}" style="width:19px;height:19px"/>
                </div>
                @if ($kpi['delta'] != 0)
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $trend === 'up' ? 'text-emerald-700 bg-emerald-50' : 'text-red-600 bg-red-50' }}">
                        {{ $trend === 'up' ? '▲' : '▼' }} {{ abs($kpi['delta']) }}%
                    </span>
                @else
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full text-slate-400 bg-slate-100">—</span>
                @endif
            </div>

            {{-- Value + Label --}}
            <div>
                <div class="text-[26px] font-black tracking-tight leading-none" style="color:#0f172a">
                    @if ($kpi['total'] > 0)
                        <span x-data="{n:0}"
                              x-init="const t={{ $kpi['total'] }};const s=Math.max(1,Math.ceil(t/24));const iv=setInterval(()=>{n=Math.min(t,n+s);if(n>=t)clearInterval(iv)},28)"
                              x-text="n.toLocaleString()">0</span>
                    @else
                        0
                    @endif
                </div>
                <div class="text-[11.5px] font-semibold mt-0.5" style="color:#64748b">{{ $kpi['label'] }}</div>
            </div>

            {{-- Sparkline --}}
            @if(count($spark) > 1)
            <svg viewBox="0 0 100 28" preserveAspectRatio="none" style="width:100%;height:28px;opacity:.85">
                <polyline points="{{ $poly }}"
                          fill="none"
                          stroke="{{ $trend === 'down' ? '#ef4444' : $pal['from'] }}"
                          stroke-width="2.2"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          vector-effect="non-scaling-stroke"/>
            </svg>
            @endif
        </{{ $tag }}>
    @endforeach
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 2 · CHARTS (Engagement 2/3 + Visitors 1/3)
═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Engagement Chart --}}
    <div class="dash-card lg:col-span-2 rounded-2xl" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden">
        <div class="flex flex-wrap items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#eef2ff;color:#6366f1">
                <x-admin.icon name="chart-line" style="width:16px;height:16px"/>
            </div>
            <div>
                <span class="font-bold text-[14px]" style="color:#0f172a">Engagement</span>
                <span class="text-[12px] ml-1" style="color:#94a3b8">· last 30 days</span>
            </div>
            <div class="flex-1"></div>
            <div class="flex flex-wrap items-center gap-3 text-[11.5px] font-semibold" style="color:#64748b">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#6366f1"></span>Enquiries</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#22c55e"></span>Applications</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm inline-block" style="background:#f59e0b"></span>Admissions</span>
            </div>
        </div>
        <div class="p-5"><div style="height:240px"><canvas id="chartEngagement"></canvas></div></div>
    </div>

    {{-- Visitors Chart --}}
    <div class="dash-card rounded-2xl" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden">
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0f9ff;color:#0ea5e9">
                <x-admin.icon name="chart-bar" style="width:16px;height:16px"/>
            </div>
            <div>
                <span class="font-bold text-[14px]" style="color:#0f172a">Visitors</span>
                <span class="text-[12px] ml-1" style="color:#94a3b8">· 30 days</span>
            </div>
        </div>
        <div class="p-5"><div style="height:240px"><canvas id="chartVisitors"></canvas></div></div>
    </div>
</div>

{{-- Content Publishing Trend --}}
<div class="dash-card rounded-2xl mb-5" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden">
    <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0fdf4;color:#16a34a">
            <x-admin.icon name="pencil" style="width:16px;height:16px"/>
        </div>
        <div>
            <span class="font-bold text-[14px]" style="color:#0f172a">Content publishing</span>
            <span class="text-[12px] ml-1" style="color:#94a3b8">· posts published, last 30 days</span>
        </div>
    </div>
    <div class="p-5"><div style="height:130px"><canvas id="chartContent"></canvas></div></div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 3 · QUICK ACTIONS
═══════════════════════════════════════════════════════ --}}
@php
    $quickActions = [
        ['icon'=>'plus',           'label'=>'Create Page',      'href'=>url('/admin/pages'),         'from'=>'#6366f1','to'=>'#8b5cf6','soft'=>'#f0f0ff','ic'=>'#6366f1'],
        ['icon'=>'rectangle-stack','label'=>'Page Builder',     'href'=>url('/admin/pages/builder'), 'from'=>'#3b82f6','to'=>'#0ea5e9','soft'=>'#eff8ff','ic'=>'#3b82f6'],
        ['icon'=>'inbox',          'label'=>'Enquiries',        'href'=>url('/admin/enquiries'),     'from'=>'#f59e0b','to'=>'#f97316','soft'=>'#fff7ed','ic'=>'#f59e0b'],
        ['icon'=>'briefcase',      'label'=>'Applications',     'href'=>url('/admin/applications'),  'from'=>'#10b981','to'=>'#059669','soft'=>'#f0fdf4','ic'=>'#10b981'],
        ['icon'=>'envelope',       'label'=>'Subscribers',      'href'=>url('/admin/subscribers'),   'from'=>'#ec4899','to'=>'#f43f5e','soft'=>'#fff0f5','ic'=>'#ec4899'],
        ['icon'=>'chart-bar',      'label'=>'Analytics',        'href'=>url('/admin/analytics'),     'from'=>'#8b5cf6','to'=>'#a855f7','soft'=>'#faf0ff','ic'=>'#8b5cf6'],
        ['icon'=>'globe',          'label'=>'View Site',        'href'=>url('/'),                    'from'=>'#14b8a6','to'=>'#06b6d4','soft'=>'#f0fdfa','ic'=>'#14b8a6'],
        ['icon'=>'cog',            'label'=>'Settings',         'href'=>url('/admin/settings'),      'from'=>'#64748b','to'=>'#475569','soft'=>'#f8fafc','ic'=>'#64748b'],
    ];
@endphp

<div class="mb-5">
    <h2 class="text-[11.5px] font-bold uppercase tracking-widest mb-3" style="color:#94a3b8">Quick Actions</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3">
        @foreach($quickActions as $qa)
        <a href="{{ $qa['href'] }}" @if($qa['href']==='#') style="opacity:.5;pointer-events:none" @endif
           class="dash-quick group flex flex-col items-center gap-2.5 p-4 rounded-2xl text-center transition-all duration-200"
           style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06);text-decoration:none">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center transition-transform duration-200 group-hover:scale-110"
                 style="background:{{ $qa['soft'] }};color:{{ $qa['ic'] }}">
                <x-admin.icon name="{{ $qa['icon'] }}" style="width:20px;height:20px"/>
            </div>
            <span class="text-[11.5px] font-semibold leading-tight" style="color:#374151">{{ $qa['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 4 · ACTIVITY FEED + SYSTEM HEALTH
═══════════════════════════════════════════════════════ --}}
@php
    $humanize = function ($desc) {
        $parts = explode(' ', $desc, 2);
        $name = preg_replace('/(?<!^)([A-Z])/', ' $1', $parts[0]);
        $name = ucfirst(strtolower($name));
        return trim($name . ' ' . ($parts[1] ?? ''));
    };

    $activityIconMap = [
        'pages'=>'document','posts'=>'pencil','media'=>'photo','enquiries'=>'inbox',
        'job_applications'=>'briefcase','subscribers'=>'envelope','events'=>'calendar',
        'notices'=>'megaphone','users'=>'users','settings'=>'cog','sliders'=>'photo',
        'galleries'=>'collection','testimonials'=>'star','achievements'=>'star',
        'academic_calendar'=>'calendar','mess_menus'=>'utensils'
    ];

    $activityColors = [
        'pages'=>['#eef2ff','#6366f1'],'posts'=>['#f0fdf4','#22c55e'],'media'=>['#f0f9ff','#0ea5e9'],
        'enquiries'=>['#fff7ed','#f59e0b'],'job_applications'=>['#f0fdf4','#10b981'],
        'subscribers'=>['#fff0f5','#ec4899'],'events'=>['#eff8ff','#3b82f6'],
        'notices'=>['#faf0ff','#8b5cf6'],'users'=>['#fff1f1','#ef4444'],
        'mess_menus'=>['#f0fdfa','#14b8a6'],'academic_calendar'=>['#eff8ff','#3b82f6'],
    ];

    $healthColors = ['ok'=>['#f0fdf4','#22c55e'],'warn'=>['#fff7ed','#f59e0b'],'down'=>['#fff1f1','#ef4444']];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">

    {{-- Activity Feed --}}
    <div class="dash-card lg:col-span-2 rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06)">
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fff7ed;color:#f59e0b">
                <x-admin.icon name="bolt" style="width:16px;height:16px"/>
            </div>
            <span class="font-bold text-[14px]" style="color:#0f172a">Recent Activity</span>
            <span class="ml-auto text-[11px] font-semibold px-2.5 py-1 rounded-full" style="background:#f8fafc;color:#64748b;border:1px solid #e2e8f0">Live</span>
        </div>
        <div class="divide-y" style="divide-color:#f8fafc">
            @forelse ($activity as $entry)
                @php
                    $ic  = $activityIconMap[$entry->log_name] ?? 'bolt';
                    $col = $activityColors[$entry->log_name] ?? ['#f8fafc','#64748b'];
                @endphp
                <div class="flex items-start gap-3.5 px-5 py-3.5 hover:bg-slate-50/60 transition-colors">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5"
                         style="background:{{ $col[0] }};color:{{ $col[1] }}">
                        <x-admin.icon name="{{ $ic }}" style="width:15px;height:15px"/>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[13px] font-medium leading-snug" style="color:#1e293b">{{ $humanize($entry->description) }}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10.5px] font-semibold px-2 py-0.5 rounded-full" style="background:#f1f5f9;color:#64748b">
                                {{ str_replace('_', ' ', $entry->log_name) }}
                            </span>
                            <span class="text-[11px]" style="color:#94a3b8">{{ $entry->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-[13px]" style="color:#94a3b8">
                    <div class="text-3xl mb-2">📋</div>
                    No activity recorded yet.
                </div>
            @endforelse
        </div>
    </div>

    {{-- System Health --}}
    <div class="dash-card rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06)">
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0fdf4;color:#22c55e">
                <x-admin.icon name="server" style="width:16px;height:16px"/>
            </div>
            <span class="font-bold text-[14px]" style="color:#0f172a">System Health</span>
        </div>
        <div class="p-4 flex flex-col gap-2">
            @foreach ($health as $h)
                @php $hc = $healthColors[$h['status']] ?? ['#f8fafc','#64748b']; @endphp
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl" style="background:#fafafa;border:1px solid #f1f5f9">
                    <div class="w-2 h-2 rounded-full shrink-0"
                         style="background:{{ $hc[1] }};box-shadow:0 0 0 3px {{ $hc[0] }}"></div>
                    <span class="text-[12.5px] font-semibold flex-1" style="color:#1e293b">{{ $h['label'] }}</span>
                    <span class="text-[11px] text-right leading-tight" style="color:#94a3b8;max-width:120px">{{ $h['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 5 · OVERVIEWS: Pages · Media · SEO
═══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    {{-- Page Builder Overview --}}
    <div class="dash-card rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06)">
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#eef2ff;color:#6366f1">
                <x-admin.icon name="rectangle-stack" style="width:16px;height:16px"/>
            </div>
            <span class="font-bold text-[14px]" style="color:#0f172a">Pages</span>
        </div>
        <div class="p-5">
            <div class="flex gap-4 mb-4">
                <div class="flex-1 rounded-xl px-4 py-3 text-center" style="background:#f0fdf4;border:1px solid #dcfce7">
                    <div class="text-[24px] font-black" style="color:#16a34a">{{ $builder['published'] }}</div>
                    <div class="text-[11px] font-semibold mt-0.5" style="color:#16a34a">Published</div>
                </div>
                <div class="flex-1 rounded-xl px-4 py-3 text-center" style="background:#fff7ed;border:1px solid #fed7aa">
                    <div class="text-[24px] font-black" style="color:#ea580c">{{ $builder['drafts'] }}</div>
                    <div class="text-[11px] font-semibold mt-0.5" style="color:#ea580c">Drafts</div>
                </div>
            </div>
            <div class="space-y-1">
                @foreach ($builder['recent'] as $p)
                    <a href="{{ url('/admin/pages/'.$p->id.'/edit') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-50 transition-colors group"
                       style="text-decoration:none">
                        <x-admin.icon name="document" style="width:13px;height:13px;color:#94a3b8;flex-shrink:0"/>
                        <span class="flex-1 truncate text-[12.5px] font-medium" style="color:#374151">{{ $p->title }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                            {{ $p->status === 'published' ? 'text-emerald-700 bg-emerald-50' : 'text-amber-700 bg-amber-50' }}">
                            {{ $p->status }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Media Library Overview --}}
    <div class="dash-card rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06)">
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0f9ff;color:#0ea5e9">
                <x-admin.icon name="photo" style="width:16px;height:16px"/>
            </div>
            <span class="font-bold text-[14px]" style="color:#0f172a">Media Library</span>
        </div>
        <div class="p-5">
            <div class="flex gap-4 mb-5">
                <div class="flex-1 rounded-xl px-4 py-3 text-center" style="background:#f0f9ff;border:1px solid #bae6fd">
                    <div class="text-[24px] font-black" style="color:#0284c7">{{ $media['images'] }}</div>
                    <div class="text-[11px] font-semibold mt-0.5" style="color:#0284c7">Images</div>
                </div>
                <div class="flex-1 rounded-xl px-4 py-3 text-center" style="background:#faf0ff;border:1px solid #e9d5ff">
                    <div class="text-[24px] font-black" style="color:#7c3aed">{{ $media['docs'] }}</div>
                    <div class="text-[11px] font-semibold mt-0.5" style="color:#7c3aed">Docs</div>
                </div>
            </div>
            @php
                $mb = $media['bytes']; $units = ['B','KB','MB','GB']; $uk = 0;
                while ($mb >= 1024 && $uk < count($units)-1) { $mb /= 1024; $uk++; }
            @endphp
            <div class="rounded-xl p-4" style="background:#f8fafc;border:1px solid #f1f5f9">
                <div class="text-[11px] font-semibold uppercase tracking-wide mb-1" style="color:#94a3b8">Storage Used</div>
                <div class="text-[22px] font-black" style="color:#0f172a">{{ round($mb, 1) }} <span class="text-[14px] font-semibold" style="color:#64748b">{{ $units[$uk] }}</span></div>
                <div class="mt-2 h-1.5 rounded-full overflow-hidden" style="background:#e2e8f0">
                    <div class="h-full rounded-full" style="width:{{ min(100, round($media['bytes'] / (1024*1024*1024) * 10)) }}%;background:linear-gradient(90deg,#0ea5e9,#6366f1)"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- SEO Coverage --}}
    <div class="dash-card rounded-2xl overflow-hidden" style="background:#fff;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06)">
        <div class="flex items-center gap-3 px-5 py-4" style="border-bottom:1px solid #f1f5f9">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0fdfa;color:#14b8a6">
                <x-admin.icon name="globe" style="width:16px;height:16px"/>
            </div>
            <span class="font-bold text-[14px]" style="color:#0f172a">SEO Coverage</span>
        </div>
        <div class="p-5">
            @php $cov = $seo['published'] > 0 ? round($seo['covered']/$seo['published']*100) : 0; @endphp

            {{-- Circular progress visual --}}
            <div class="flex flex-col items-center py-3">
                <div class="relative w-28 h-28">
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="10"/>
                        <circle cx="50" cy="50" r="40" fill="none"
                                stroke="url(#seoGrad)"
                                stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round(2.51327 * 40 * $cov / 100, 1) }} 251.327"/>
                        <defs>
                            <linearGradient id="seoGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#14b8a6"/>
                                <stop offset="100%" stop-color="#6366f1"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[26px] font-black" style="color:#0f172a">{{ $cov }}</span>
                        <span class="text-[10px] font-bold" style="color:#94a3b8">%</span>
                    </div>
                </div>
                <p class="text-[12px] font-medium text-center mt-3" style="color:#64748b">
                    <span class="font-bold" style="color:#0f172a">{{ $seo['covered'] }}</span> of <span class="font-bold" style="color:#0f172a">{{ $seo['published'] }}</span> pages optimized
                </p>
                @if ($seo['missingDesc'] > 0)
                    <div class="mt-3 w-full flex items-center gap-2 px-3 py-2 rounded-xl" style="background:#fff7ed;border:1px solid #fed7aa">
                        <span class="text-base">⚠️</span>
                        <span class="text-[12px] font-semibold" style="color:#c2410c">{{ $seo['missingDesc'] }} pages missing meta description</span>
                    </div>
                @else
                    <div class="mt-3 w-full flex items-center gap-2 px-3 py-2 rounded-xl" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <span class="text-base">✅</span>
                        <span class="text-[12px] font-semibold" style="color:#15803d">All pages are fully optimized</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    if (typeof Chart === 'undefined') return;

    const isDark = document.documentElement.classList.contains('dark');
    const grid   = isDark ? 'rgba(148,163,184,.10)' : 'rgba(148,163,184,.12)';
    const tick   = isDark ? 'rgba(148,163,184,.70)' : 'rgba(100,116,139,.80)';

    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = tick;

    const charts = @json($charts);

    const area = (ctx, c1, c2) => {
        const g = ctx.createLinearGradient(0, 0, 0, 250);
        g.addColorStop(0, (c2 || c1) + '40');
        g.addColorStop(1, c1 + '00');
        return g;
    };

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#f1f5f9',
                bodyColor: '#cbd5e1',
                borderColor: '#334155',
                borderWidth: 1,
                padding: 10,
                cornerRadius: 10,
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { maxTicksLimit: 7, color: tick, font: { size: 11 } } },
            y: { beginAtZero: true, grid: { color: grid }, ticks: { precision: 0, maxTicksLimit: 5, color: tick, font: { size: 11 } } },
        },
        elements: { point: { radius: 0, hoverRadius: 5 }, line: { tension: .42, borderWidth: 2.5 } },
    };

    const emptyHTML = '<div style="display:grid;place-items:center;height:100%;font-size:13px;color:#94a3b8">No data in this period yet</div>';

    function mount(id, build, series) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!series.some(v => v > 0)) { el.parentElement.innerHTML = emptyHTML; return; }
        new Chart(el, build(el));
    }

    mount('chartEngagement', (el) => ({
        type: 'line',
        data: {
            labels: charts.enquiries.labels,
            datasets: [
                { label: 'Enquiries',    data: charts.enquiries.data,    borderColor: '#6366f1', backgroundColor: area(el.getContext('2d'), '#6366f1'), fill: true },
                { label: 'Applications', data: charts.applications.data, borderColor: '#22c55e', backgroundColor: 'transparent', fill: false },
                { label: 'Admissions',   data: charts.admissions.data,   borderColor: '#f59e0b', backgroundColor: 'transparent', fill: false, borderDash: [5,3] },
            ]
        },
        options: baseOpts,
    }), [...charts.enquiries.data, ...charts.applications.data, ...charts.admissions.data]);

    mount('chartVisitors', (el) => ({
        type: 'line',
        data: {
            labels: charts.visitors.labels,
            datasets: [
                { label: 'Visitors', data: charts.visitors.data, borderColor: '#0ea5e9', backgroundColor: area(el.getContext('2d'), '#0ea5e9', '#6366f1'), fill: true },
            ]
        },
        options: baseOpts,
    }), charts.visitors.data);

    mount('chartContent', (el) => ({
        type: 'bar',
        data: {
            labels: charts.content.labels,
            datasets: [
                {
                    label: 'Posts',
                    data: charts.content.data,
                    backgroundColor: (ctx) => {
                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 150);
                        g.addColorStop(0, '#22c55e');
                        g.addColorStop(1, '#10b98150');
                        return g;
                    },
                    borderRadius: 5,
                    borderSkipped: false,
                }
            ]
        },
        options: { ...baseOpts, elements: { ...baseOpts.elements } },
    }), charts.content.data);
})();
</script>

<style>
/* ── Dashboard card hover lift ── */
.dash-kpi:hover,
.dash-quick:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,.10) !important;
    border-color: #e0e7ff !important;
}
.dash-card {
    transition: box-shadow .2s ease;
}
.dash-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.08) !important;
}

/* ── Dark mode overrides ── */
.dark .dash-hero     { filter: brightness(.92); }
.dark .dash-kpi,
.dark .dash-card,
.dark .dash-quick    { background: var(--surface) !important; border-color: var(--border) !important; }
.dark .dash-kpi > div[style*="#0f172a"],
.dark .dash-card > div,
.dark [style*="color:#0f172a"] { color: var(--text) !important; }
.dark [style*="color:#1e293b"] { color: var(--text) !important; }
.dark [style*="color:#374151"] { color: var(--text-soft) !important; }
.dark [style*="color:#64748b"] { color: var(--text-muted) !important; }
.dark [style*="color:#94a3b8"] { color: var(--text-muted) !important; }
.dark [style*="background:#fff"],
.dark [style*="background:#fafafa"],
.dark [style*="background:#f8fafc"] { background: var(--surface-2) !important; }
.dark [style*="background:#f1f5f9"],
.dark [style*="background:#f0f9ff"],
.dark [style*="background:#eef2ff"] { background: var(--surface-hover) !important; }
.dark [style*="border:1px solid #f1f5f9"],
.dark [style*="border-bottom:1px solid #f1f5f9"],
.dark [style*="divide-color:#f8fafc"] { border-color: var(--border) !important; }
</style>
@endpush

@endsection
