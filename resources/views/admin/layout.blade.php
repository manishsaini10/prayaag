<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'CMS') }}</title>

    {{-- Apply saved theme before paint to avoid a flash of the wrong mode. --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('admin-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    @php
        $viteManifestPath = public_path('build/manifest.json');
        $viteManifest = file_exists($viteManifestPath)
            ? json_decode(file_get_contents($viteManifestPath), true)
            : [];
        $viteCss = $viteManifest['resources/css/app.css']['file'] ?? null;
        $viteJs = $viteManifest['resources/js/app.js']['file'] ?? null;
    @endphp
    @if ($viteCss)
        <link rel="stylesheet" href="{{ asset('build/'.$viteCss) }}">
    @else
        {{-- No Vite build present - load the self-contained admin stylesheet. --}}
        <link rel="stylesheet" href="{{ asset('admin.css') }}">
    @endif
    @if ($viteJs)
        <script type="module" src="{{ asset('build/'.$viteJs) }}"></script>
    @endif
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
</head>
<body x-data="adminShell()" class="min-h-screen">

<div class="flex min-h-screen">

    {{-- ============================ SIDEBAR ============================ --}}
    <div x-show="mobileNav" x-cloak class="fixed inset-0 z-30 bg-slate-900/50 md:hidden" @click="mobileNav=false"></div>

    <aside
        class="admin-sidebar fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-200 md:static md:translate-x-0"
        :class="{ '-translate-x-full': !mobileNav, 'translate-x-0': mobileNav, 'w-64': !collapsed, 'md:w-[72px] w-64': collapsed }">

        {{-- Brand --}}
        <div class="flex items-center gap-2.5 h-16 px-4 shrink-0" style="border-bottom:1px solid var(--border)">
            <div class="w-9 h-9 rounded-xl grid place-items-center text-white shrink-0" style="background:linear-gradient(135deg,var(--primary),var(--primary-strong))">
                <x-admin.icon name="academic-cap" style="width:20px;height:20px"/>
            </div>
            <div x-show="!collapsed" class="leading-tight min-w-0">
                <div class="font-bold text-[15px] truncate" style="color:var(--text)">{{ config('app.name', 'School CMS') }}</div>
                <div class="text-[11px] truncate" style="color:var(--text-muted)">Control Center</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto py-2">
            @php($is = fn ($p) => request()->is($p) ? 'active' : '')

            <a href="{{ url('/admin') }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                <x-admin.icon name="dashboard"/><span x-show="!collapsed">Dashboard</span>
            </a>

            <div x-show="!collapsed" class="nav-section">Content</div>
            <a href="{{ url('/admin/pages') }}" class="nav-link {{ $is('admin/pages*') }}"><x-admin.icon name="document"/><span x-show="!collapsed">Pages</span></a>
            <a href="{{ url('/admin/pages/builder') }}" class="nav-link {{ $is('admin/pages/builder') }}"><x-admin.icon name="rectangle-stack"/><span x-show="!collapsed">Page Builder</span></a>
            <a href="{{ url('/admin/widgets') }}" class="nav-link {{ $is('admin/widgets*') }}"><x-admin.icon name="rectangle-stack"/><span x-show="!collapsed">Widget Builder</span></a>
            <a href="{{ url('/admin/menus') }}" class="nav-link {{ $is('admin/menus*') }}"><x-admin.icon name="menu"/><span x-show="!collapsed">Menus</span></a>
            @foreach ([['pencil','Posts','posts'],['tag','Categories','categories'],['megaphone','Notices','notices'],['calendar','Events','events'],['download','Downloads','downloads'],['star','Testimonials','testimonials'],['star','Achievements','achievements'],['collection','Gallery','galleries'],['photo','Sliders','sliders'],['calendar','Academic Calendar','academic_calendar']] as [$ic,$lbl,$key])
                @if ($key === 'academic_calendar')
                    <a href="{{ route('admin.academic-calendar-entries.index') }}" class="nav-link {{ request()->is('admin/academic-calendar-entries*') || request()->is('admin/academic-sessions*') ? 'active' : '' }}"><x-admin.icon name="{{ $ic }}"/><span x-show="!collapsed">{{ $lbl }}</span></a>
                @elseif ($key === 'testimonials')
                    <a href="{{ route('admin.testimonials-console.index') }}" class="nav-link {{ request()->is('admin/testimonials-console*') ? 'active' : '' }}"><x-admin.icon name="{{ $ic }}"/><span x-show="!collapsed">{{ $lbl }}</span></a>
                @else
                    <a href="{{ url('/admin/m/'.$key) }}" class="nav-link {{ $is('admin/m/'.$key.'*') }}"><x-admin.icon name="{{ $ic }}"/><span x-show="!collapsed">{{ $lbl }}</span></a>
                @endif
            @endforeach

            <div x-show="!collapsed" class="nav-section">Admissions</div>
            <a href="{{ url('/admin/leads') }}" class="nav-link {{ $is('admin/leads*') }}"><x-admin.icon name="users"/><span x-show="!collapsed">Leads</span></a>
            <a href="{{ url('/admin/enquiries') }}" class="nav-link {{ $is('admin/enquiries*') }}"><x-admin.icon name="inbox"/><span x-show="!collapsed">Enquiries</span></a>
            <a href="{{ url('/admin/applications') }}" class="nav-link {{ $is('admin/applications*') }}"><x-admin.icon name="briefcase"/><span x-show="!collapsed">Applications</span></a>
            <a href="{{ url('/admin/forms') }}" class="nav-link {{ $is('admin/forms*') }}"><x-admin.icon name="document"/><span x-show="!collapsed">Admission Forms</span></a>

            <div x-show="!collapsed" class="nav-section">Media</div>
            <a href="{{ url('/admin/m/media') }}" class="nav-link {{ $is('admin/m/media*') }}"><x-admin.icon name="folder"/><span x-show="!collapsed">Media Library</span></a>
            <a href="{{ url('/admin/m/folders') }}" class="nav-link {{ $is('admin/m/folders*') }}"><x-admin.icon name="folder"/><span x-show="!collapsed">Folders</span></a>
            <a href="{{ url('/admin/upload') }}" class="nav-link {{ $is('admin/upload*') }}"><x-admin.icon name="upload"/><span x-show="!collapsed">Upload Center</span></a>

            @can('users.view')
            <div x-show="!collapsed" class="nav-section">Users</div>
            @foreach ([['users','Users','users'],['shield','Roles','roles'],['shield','Permissions','permissions']] as [$ic,$lbl,$key])
                <a href="{{ url('/admin/m/'.$key) }}" class="nav-link {{ $is('admin/m/'.$key.'*') }}"><x-admin.icon name="{{ $ic }}"/><span x-show="!collapsed">{{ $lbl }}</span></a>
            @endforeach
            @endcan

            <div x-data="{ marketingOpen: localStorage.getItem('mktg') !== '0' }" x-effect="localStorage.setItem('mktg', marketingOpen ? '1' : '0')" class="mb-1">
                <button @click="marketingOpen = !marketingOpen" x-show="!collapsed" class="nav-section w-full flex items-center justify-between cursor-pointer" style="background:none;border:none;font:inherit;color:inherit;padding:6px 16px">
                    <span>Marketing</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;transition:transform .2s" :class="{'rotate-180': marketingOpen}"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div x-show="marketingOpen || collapsed">
                    <a href="{{ url('/admin/analytics') }}" class="nav-link {{ $is('admin/analytics*') }}"><x-admin.icon name="chart-bar"/><span x-show="!collapsed">Analytics</span></a>
                    <a href="{{ url('/admin/subscribers') }}" class="nav-link {{ $is('admin/subscribers*') }}"><x-admin.icon name="envelope"/><span x-show="!collapsed">Subscribers</span></a>
                    <a href="{{ url('/admin/seo') }}" class="nav-link {{ $is('admin/seo*') }}"><x-admin.icon name="globe"/><span x-show="!collapsed">SEO</span></a>
                    <a href="{{ url('/admin/m/redirects') }}" class="nav-link {{ $is('admin/m/redirects*') }}"><x-admin.icon name="globe"/><span x-show="!collapsed">Redirects</span></a>
                    <a href="{{ url('/admin/instagram') }}" class="nav-link {{ $is('admin/instagram*') }}"><x-admin.icon name="photo"/><span x-show="!collapsed">Instagram Feed</span></a>
                    @can('popup.view')
                    <a href="{{ url('/admin/popup-builder') }}" class="nav-link {{ $is('admin/popup-builder*') }}"><x-admin.icon name="collection"/><span x-show="!collapsed">Popup Manager</span></a>
                    @endcan
                    <a href="{{ url('/admin/chatbot') }}" class="nav-link {{ $is('admin/chatbot') }}"><x-admin.icon name="inbox"/><span x-show="!collapsed">Chatbot</span></a>
                    <a href="{{ url('/admin/chatbot/form-fields') }}" class="nav-link {{ $is('admin/chatbot/form-fields*') }}"><x-admin.icon name="collection"/><span x-show="!collapsed">Pre-Chat Form</span></a>
                    <a href="{{ url('/admin/chatbot/campaigns') }}" class="nav-link {{ $is('admin/chatbot/campaigns*') }}"><x-admin.icon name="megaphone"/><span x-show="!collapsed">Campaigns</span></a>
                    <a href="{{ url('/admin/chatbot/webhooks') }}" class="nav-link {{ $is('admin/chatbot/webhooks*') }}"><x-admin.icon name="globe"/><span x-show="!collapsed">Webhooks</span></a>
                    <a href="{{ url('/admin/chatbot/analytics') }}" class="nav-link {{ $is('admin/chatbot/analytics') }}"><x-admin.icon name="chart-bar"/><span x-show="!collapsed">Chatbot Analytics</span></a>
                    <a href="{{ url('/admin/funnel') }}" class="nav-link {{ $is('admin/funnel*') }}"><x-admin.icon name="trending-up"/><span x-show="!collapsed">Funnel Analytics</span></a>
                </div>
            </div>

            <div x-show="!collapsed" class="nav-section">System</div>
            <a href="{{ url('/admin/settings') }}" class="nav-link {{ $is('admin/settings*') }}"><x-admin.icon name="cog"/><span x-show="!collapsed">Settings</span></a>
            <a href="{{ url('/admin/theme') }}" class="nav-link {{ $is('admin/theme*') }}"><x-admin.icon name="rectangle-stack"/><span x-show="!collapsed">Theme Builder</span></a>
            <a href="{{ url('/admin/m/activitylog') }}" class="nav-link {{ $is('admin/m/activitylog*') }}"><x-admin.icon name="bolt"/><span x-show="!collapsed">Activity Logs</span></a>
            <a href="{{ url('/admin/notifications') }}" class="nav-link {{ $is('admin/notifications*') }}"><x-admin.icon name="bell"/><span x-show="!collapsed">Notifications</span></a>
            <a href="{{ url('/admin/system-health') }}" class="nav-link {{ $is('admin/system-health*') }}"><x-admin.icon name="server"/><span x-show="!collapsed">System Health</span></a>
            <a href="{{ url('/admin/api-tokens') }}" class="nav-link {{ $is('admin/api-tokens*') }}"><x-admin.icon name="lock"/><span x-show="!collapsed">API Tokens</span></a>
            <a href="{{ url('/2fa/setup') }}" class="nav-link {{ $is('2fa/setup') }}"><x-admin.icon name="shield"/><span x-show="!collapsed">2FA</span></a>
        </nav>

        {{-- Collapse toggle (desktop) --}}
        <button @click="toggleCollapse()" class="hidden md:flex items-center gap-2 px-4 h-12 shrink-0 text-[13px]" style="border-top:1px solid var(--border);color:var(--text-muted)">
            <span :class="collapsed ? '' : 'rotate-180'" class="transition-transform"><x-admin.icon name="chevron-right" style="width:18px;height:18px"/></span>
            <span x-show="!collapsed">Collapse</span>
        </button>
    </aside>

    {{-- ============================ MAIN ============================ --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Header --}}
        <header class="admin-header sticky top-0 z-20 flex items-center gap-3 h-16 px-4 md:px-6">
            <button @click="mobileNav=true" class="md:hidden btn-sm" style="border:none;background:transparent"><x-admin.icon name="menu" style="width:22px;height:22px"/></button>

            {{-- Command palette trigger --}}
            <button @click="$dispatch('open-palette')" class="flex items-center gap-2.5 text-[13px] rounded-xl px-3 h-9 w-full max-w-md transition" style="background:var(--surface-2);border:1px solid var(--border);color:var(--text-muted)">
                <x-admin.icon name="search" style="width:17px;height:17px"/>
                <span class="flex-1 text-left">Search or jump to…</span>
                <span class="kbd hidden sm:inline">⌘K</span>
            </button>

            <div class="flex-1"></div>

            {{-- Theme switcher --}}
            <button @click="toggleTheme()" class="btn-sm" style="border:1px solid var(--border);width:38px;height:38px;display:grid;place-items:center" title="Toggle theme">
                <span x-show="!dark"><x-admin.icon name="moon" style="width:18px;height:18px"/></span>
                <span x-show="dark" x-cloak><x-admin.icon name="sun" style="width:18px;height:18px"/></span>
            </button>

            {{-- Notifications --}}
            <div class="relative" x-data="{ open:false }" @click.outside="open=false">
                <button @click="open=!open" class="btn-sm relative" style="border:1px solid var(--border);width:38px;height:38px;display:grid;place-items:center">
                    <x-admin.icon name="bell" style="width:18px;height:18px"/>
                    @if (($navUnreadCount ?? 0) > 0)
                        <span style="position:absolute;top:-4px;right:-4px;min-width:17px;height:17px;padding:0 4px;border-radius:999px;background:var(--danger);color:#fff;font-size:10px;font-weight:700;display:grid;place-items:center">{{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}</span>
                    @endif
                </button>
                <div x-show="open" x-cloak x-transition.origin.top.right class="pop absolute right-0 mt-2 w-80 z-30" style="overflow:hidden">
                    <div class="px-3 py-2.5 flex items-center justify-between" style="border-bottom:1px solid var(--border)">
                        <span class="font-semibold text-[14px]" style="color:var(--text)">Notifications</span>
                        @if (($navUnreadCount ?? 0) > 0)
                            <form method="POST" action="{{ url('/admin/notifications/read-all') }}">@csrf
                                <button type="submit" class="text-[12px]" style="background:none;border:none;color:var(--primary-strong);cursor:pointer">Mark all read</button>
                            </form>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto p-1">
                        @forelse (($navNotifications ?? collect()) as $n)
                            <form method="POST" action="{{ url('/admin/notifications/'.$n->id.'/read') }}">@csrf
                                <button type="submit" class="cmdk-item w-full text-left" style="{{ $n->isUnread() ? '' : 'opacity:.65' }}">
                                    <x-admin.icon name="{{ $n->icon ?: 'bell' }}"/>
                                    <span class="flex-1 min-w-0">
                                        <span class="block truncate text-[13px]" style="color:var(--text);font-weight:{{ $n->isUnread() ? '600' : '500' }}">{{ $n->title }}</span>
                                        <span class="block text-[11px]" style="color:var(--text-muted)">{{ $n->created_at?->diffForHumans() }}</span>
                                    </span>
                                    @if ($n->isUnread())<span class="status-dot ok" style="background:var(--primary)"></span>@endif
                                </button>
                            </form>
                        @empty
                            <div class="empty text-[13px]">You're all caught up.</div>
                        @endforelse
                    </div>
                    <a href="{{ url('/admin/notifications') }}" class="block text-center py-2.5 text-[13px]" style="border-top:1px solid var(--border);color:var(--primary-strong)">View all notifications</a>
                </div>
            </div>

            {{-- User menu --}}
            <div class="relative" x-data="{ open:false }" @click.outside="open=false">
                <button @click="open=!open" class="flex items-center gap-2 rounded-xl pl-1 pr-2 h-10" style="border:1px solid var(--border);background:var(--surface)">
                    <span class="w-7 h-7 rounded-lg grid place-items-center text-white text-[12px] font-bold" style="background:linear-gradient(135deg,var(--primary),var(--primary-strong))">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </span>
                    <span class="hidden sm:block text-[13px] font-semibold max-w-[120px] truncate" style="color:var(--text)">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <x-admin.icon name="chevron-down" style="width:16px;height:16px;color:var(--text-muted)"/>
                </button>
                <div x-show="open" x-cloak x-transition.origin.top.right class="pop absolute right-0 mt-2 w-60 p-2 z-30">
                    <div class="px-3 py-2">
                        <div class="font-semibold text-[14px] truncate" style="color:var(--text)">{{ auth()->user()->name ?? 'Admin' }}</div>
                        <div class="text-[12px] truncate" style="color:var(--text-muted)">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                    <div style="height:1px;background:var(--border);margin:6px 0"></div>
                    <a href="{{ url('/') }}" target="_blank" class="cmdk-item"><x-admin.icon name="globe"/> View site</a>
                    <button @click="toggleTheme()" class="cmdk-item w-full text-left"><x-admin.icon name="moon"/> Toggle theme</button>
                    <form method="POST" action="{{ url('/logout') }}">@csrf
                        <button type="submit" class="cmdk-item w-full text-left" style="color:var(--danger)"><x-admin.icon name="logout"/> Sign out</button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Page title bar --}}
        @hasSection('title')
        <div class="px-4 md:px-6 pt-6 pb-1 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h1 class="text-[22px] font-bold tracking-tight" style="color:var(--text)">@yield('title')</h1>
                @hasSection('subtitle')<p class="text-[13px] mt-0.5" style="color:var(--text-muted)">@yield('subtitle')</p>@endif
            </div>
            @yield('actions')
        </div>
        @endif

        <main class="flex-1 px-4 md:px-6 py-5">
            @yield('content')
        </main>
    </div>
</div>

{{-- ============================ COMMAND PALETTE ============================ --}}
<div x-data="cmdPalette()" x-show="open" x-cloak @open-palette.window="show()" @keydown.window="hotkey($event)" class="fixed inset-0 z-50 flex items-start justify-center pt-[12vh] px-4">
    <div class="cmdk-backdrop absolute inset-0" @click="open=false"></div>
    <div class="cmdk-panel relative w-full max-w-xl overflow-hidden" @click.stop>
        <div class="flex items-center gap-3 px-4 h-14" style="border-bottom:1px solid var(--border)">
            <x-admin.icon name="search" style="width:19px;height:19px;color:var(--text-muted)"/>
            <input x-ref="q" x-model="query" @input="onInput()" type="text" placeholder="Search pages, posts, media, users, actions…"
                   class="flex-1 bg-transparent outline-none text-[15px]" style="color:var(--text)">
            <span class="kbd">esc</span>
        </div>
        <div class="max-h-80 overflow-y-auto p-2">
            <template x-for="(item, i) in combined" :key="item.group + item.label + i">
                <a :href="item.href" class="cmdk-item" :class="{ 'active': i === active }" @mouseenter="active=i">
                    <span x-html="item.svg"></span>
                    <span class="flex-1 truncate" x-text="item.label"></span>
                    <span class="text-[11px] truncate" style="color:var(--text-muted)" x-text="item.sub || item.group"></span>
                </a>
            </template>
            <div x-show="loading" class="empty text-[13px]">Searching…</div>
            <div x-show="!loading && combined.length === 0" class="empty text-[13px]">No results for "<span x-text="query"></span>"</div>
        </div>
    </div>
</div>

<script>
    function adminShell() {
        return {
            mobileNav: false,
            collapsed: localStorage.getItem('admin-collapsed') === '1',
            dark: document.documentElement.classList.contains('dark'),
            toggleTheme() {
                this.dark = !this.dark;
                document.documentElement.classList.toggle('dark', this.dark);
                localStorage.setItem('admin-theme', this.dark ? 'dark' : 'light');
            },
            toggleCollapse() {
                this.collapsed = !this.collapsed;
                localStorage.setItem('admin-collapsed', this.collapsed ? '1' : '0');
            },
        };
    }

    function cmdPalette() {
        const ico = (p) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;opacity:.8">' + p + '</svg>';
        const docIco = ico('<path d="M6 2.5h7l5 5V21H6Z"/><path d="M13 2.5V8h5"/>');
        const items = [
            { label: 'Dashboard', href: '{{ url('/admin') }}', group: 'Go', svg: ico('<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>') },
            { label: 'Pages', href: '{{ url('/admin/pages') }}', group: 'Go', svg: docIco },
            { label: 'Page Builder', href: '{{ url('/admin/pages/builder') }}', group: 'Go', svg: ico('<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>') },
            { label: 'Menus', href: '{{ url('/admin/menus') }}', group: 'Go', svg: ico('<path d="M4 6h16M4 12h16M4 18h16"/>') },
            { label: 'Posts', href: '{{ url('/admin/m/posts') }}', group: 'Go', svg: ico('<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>') },
            { label: 'Media Library', href: '{{ url('/admin/m/media') }}', group: 'Go', svg: ico('<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 15 5-5 4 4 3-3 6 6"/>') },
            { label: 'Enquiries', href: '{{ url('/admin/enquiries') }}', group: 'Go', svg: ico('<path d="M4 13l2-8h12l2 8v6H4Z"/>') },
            { label: 'Analytics', href: '{{ url('/admin/analytics') }}', group: 'Go', svg: ico('<path d="M4 20V4M4 20h16"/><rect x="7" y="12" width="3" height="5"/><rect x="12" y="8" width="3" height="9"/>') },
            { label: 'Instagram Feed', href: '{{ url('/admin/instagram') }}', group: 'Go', svg: ico('<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/>') },
            @can('popup.view'){ label: 'Popup Manager', href: '{{ url('/admin/popup-builder') }}', group: 'Go', svg: ico('<path d="M4 4h16v16H4z"/><path d="M9 4v16"/>') },@endcan
            { label: 'Settings', href: '{{ url('/admin/settings') }}', group: 'Go', svg: ico('<circle cx="12" cy="12" r="3"/><path d="M19.4 13a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-2.82 1.17V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15H4a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 6 9.4a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 11 4.6V4a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 2.82 1.17l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 11H21a2 2 0 0 1 0 4h-.09Z"/>') },
            { label: 'Create new page', href: '{{ url('/admin/pages/builder') }}', group: 'Action', svg: ico('<path d="M12 5v14M5 12h14"/>') },
            { label: 'View public site', href: '{{ url('/') }}', group: 'Action', svg: ico('<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17"/>') },
        ];
        return {
            open: false, query: '', active: 0, items, remote: [], loading: false, timer: null,
            searchUrl: '{{ url('/admin/search') }}', docIco,
            get staticMatches() {
                const q = this.query.toLowerCase().trim();
                if (!q) return this.items;
                return this.items.filter(i => (i.label + ' ' + i.group).toLowerCase().includes(q));
            },
            get combined() { return [...this.staticMatches, ...this.remote]; },
            onInput() {
                this.active = 0;
                clearTimeout(this.timer);
                const q = this.query.trim();
                if (q.length < 2) { this.remote = []; this.loading = false; return; }
                this.loading = true;
                this.timer = setTimeout(() => this.fetchRemote(q), 220);
            },
            async fetchRemote(q) {
                try {
                    const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    this.remote = (data.results || []).map(r => ({ label: r.label, href: r.href, group: r.group, sub: r.sub || '', svg: this.docIco }));
                } catch (e) { this.remote = []; }
                this.loading = false;
            },
            show() { this.open = true; this.query = ''; this.active = 0; this.remote = []; this.loading = false; this.$nextTick(() => this.$refs.q.focus()); },
            hotkey(e) {
                if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); this.open ? (this.open = false) : this.show(); return; }
                if (!this.open) return;
                if (e.key === 'Escape') { this.open = false; }
                if (e.key === 'ArrowDown') { e.preventDefault(); this.active = Math.min(this.active + 1, this.combined.length - 1); }
                if (e.key === 'ArrowUp') { e.preventDefault(); this.active = Math.max(this.active - 1, 0); }
                if (e.key === 'Enter') { const it = this.combined[this.active]; if (it) window.location.href = it.href; }
            },
        };
    }
</script>
@stack('scripts')
</body>
</html>
