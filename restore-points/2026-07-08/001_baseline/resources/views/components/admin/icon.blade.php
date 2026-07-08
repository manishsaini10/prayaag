@props(['name' => 'dot'])

{{--
    Inline line-icon set for the admin shell. 24x24, stroke=currentColor so icons
    inherit text color and the parent CSS controls size. Heroicons-style, hand
    authored to guarantee valid path data with no external dependency.
--}}

<svg {{ $attributes->merge(['viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.7', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('dashboard')
            <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
            @break
        @case('document')
            <path d="M6 2.5h7l5 5V21a.5.5 0 0 1-.5.5h-11A.5.5 0 0 1 6 21V3a.5.5 0 0 1 .5-.5Z"/><path d="M13 2.5V8h5"/><path d="M9 13h6M9 16.5h6"/>
            @break
        @case('pencil')
            <path d="M4 20l1-4L16 5l3 3L8 19l-4 1Z"/><path d="M14 7l3 3"/>
            @break
        @case('pencil-square')
            <path d="M4 13.5V20h6.5L20 10.5 13.5 4 4 13.5Z"/><path d="M12.5 5l6.5 6.5"/>
            @break
        @case('photo')
            <rect x="3" y="4.5" width="18" height="15" rx="2"/><circle cx="8.5" cy="9.5" r="1.6"/><path d="M21 16l-5-5-9 8.5"/>
            @break
        @case('academic-cap')
            <path d="M12 4 2.5 9 12 14l9.5-5L12 4Z"/><path d="M6 11v4.5c0 1 2.7 2.5 6 2.5s6-1.5 6-2.5V11"/><path d="M21.5 9v4.5"/>
            @break
        @case('briefcase')
            <rect x="3" y="7.5" width="18" height="12" rx="2"/><path d="M8.5 7.5V6a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v1.5"/><path d="M3 12.5h18"/>
            @break
        @case('inbox')
            <path d="M4 13l2-8h12l2 8v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6Z"/><path d="M4 13h4l1.5 2.5h5L16 13h4"/>
            @break
        @case('envelope')
            <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="15.5" rx="2"/><path d="M3.5 9.5h17M8 3v4M16 3v4"/><path d="M7.5 13h3M7.5 16.5h3M13.5 13h3"/>
            @break
        @case('megaphone')
            <path d="M4 10v4a1 1 0 0 0 1 1h3l8 4V5L8 9H5a1 1 0 0 0-1 1Z"/><path d="M8 15v3.5a1.5 1.5 0 0 0 3 0V15"/><path d="M19 9a3 3 0 0 1 0 6"/>
            @break
        @case('users')
            <circle cx="9" cy="8" r="3.2"/><path d="M3.5 19.5a5.5 5.5 0 0 1 11 0"/><path d="M16 5.5a3 3 0 0 1 0 5.8M16.5 19.5a5.5 5.5 0 0 0-2-4.3"/>
            @break
        @case('folder')
            <path d="M3 6.5A1.5 1.5 0 0 1 4.5 5h4l2 2.5h7A1.5 1.5 0 0 1 19 9v8.5a1.5 1.5 0 0 1-1.5 1.5H4.5A1.5 1.5 0 0 1 3 17.5V6.5Z"/>
            @break
        @case('upload')
            <path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/><path d="M12 15V4M8 8l4-4 4 4"/>
            @break
        @case('download')
            <path d="M4 16v2.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V16"/><path d="M12 4v11M8 11l4 4 4-4"/>
            @break
        @case('tag')
            <path d="M3.5 11.5 11 4h7.5v7.5L11 19l-7.5-7.5Z"/><circle cx="15" cy="9" r="1.3"/>
            @break
        @case('star')
            <path d="m12 4 2.4 5 5.6.7-4 3.9 1 5.4L12 16.4 6.9 19l1-5.4-4-3.9 5.6-.7L12 4Z"/>
            @break
        @case('collection')
            <rect x="3" y="8" width="18" height="12" rx="2"/><path d="M6 5h12M8 2.5h8"/>
            @break
        @case('rectangle-stack')
            <rect x="3" y="11" width="18" height="9" rx="2"/><path d="M6 8h12M8 5h8"/>
            @break
        @case('chart-bar')
            <path d="M4 20V4"/><path d="M4 20h16"/><rect x="7" y="12" width="3" height="5" rx="1"/><rect x="12" y="8" width="3" height="9" rx="1"/><rect x="17" y="14" width="3" height="3" rx="1"/>
            @break
        @case('chart-line')
            <path d="M4 4v16h16"/><path d="m7 14 3-4 3 2 4-6"/>
            @break
        @case('globe')
            <circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.3 2.5 14.7 0 17M12 3.5c-2.5 2.3-2.5 14.7 0 17"/>
            @break
        @case('cog')
            <circle cx="12" cy="12" r="3"/><path d="M12 3v2.5M12 18.5V21M3 12h2.5M18.5 12H21M5.6 5.6l1.8 1.8M16.6 16.6l1.8 1.8M18.4 5.6l-1.8 1.8M7.4 16.6l-1.8 1.8"/>
            @break
        @case('shield')
            <path d="M12 3 5 6v5c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('bolt')
            <path d="M13 3 5 13h5l-1 8 8-10h-5l1-8Z"/>
            @break
        @case('bell')
            <path d="M6 9a6 6 0 0 1 12 0c0 5 1.5 6.5 1.5 6.5h-15S6 14 6 9Z"/><path d="M10 19a2 2 0 0 0 4 0"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>
            @break
        @case('command')
            <path d="M9 6.5A2.5 2.5 0 1 0 6.5 9H9V6.5Z"/><path d="M15 6.5A2.5 2.5 0 1 1 17.5 9H15V6.5Z"/><path d="M9 17.5A2.5 2.5 0 1 1 6.5 15H9v2.5Z"/><path d="M15 17.5a2.5 2.5 0 1 0 2.5-2.5H15v2.5Z"/><rect x="9" y="9" width="6" height="6"/>
            @break
        @case('sun')
            <circle cx="12" cy="12" r="4"/><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M19.1 4.9l-1.8 1.8M6.7 17.3l-1.8 1.8"/>
            @break
        @case('moon')
            <path d="M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5Z"/>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6"/>
            @break
        @case('chevron-right')
            <path d="m9 6 6 6-6 6"/>
            @break
        @case('logout')
            <path d="M15 5h3a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-3"/><path d="M11 8l-4 4 4 4M7 12h11"/>
            @break
        @case('server')
            <rect x="3.5" y="4.5" width="17" height="6" rx="1.5"/><rect x="3.5" y="13.5" width="17" height="6" rx="1.5"/><path d="M7 7.5h.01M7 16.5h.01"/>
            @break
        @default
            <circle cx="12" cy="12" r="3.5"/>
    @endswitch
</svg>
