@extends('admin.layout')

@section('title', 'Theme Builder')
@section('subtitle', 'Header, footer, colors & fonts — applies live')

@section('content')

<style>
    .tb-section{margin-bottom:26px}
    .tb-h{font-size:13px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:var(--text-muted);margin:0 0 12px}
    .tb-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px}
    .tb-card{position:relative;display:block;cursor:pointer;border:2px solid var(--border);border-radius:14px;background:var(--surface);padding:0;overflow:hidden;transition:.15s ease}
    .tb-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:var(--shadow,0 6px 18px rgba(0,0,0,.06))}
    .tb-card input{position:absolute;opacity:0;pointer-events:none}
    .tb-card:has(input:checked){border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .tb-card:has(input:checked) .tb-tick{opacity:1;transform:scale(1)}
    .tb-prev{height:74px;display:block;border-bottom:1px solid var(--border)}
    .tb-body{padding:10px 12px}
    .tb-name{font-size:13.5px;font-weight:600;color:var(--text)}
    .tb-desc{font-size:11.5px;color:var(--text-muted);margin-top:2px;line-height:1.4}
    .tb-tick{position:absolute;top:8px;right:8px;width:22px;height:22px;border-radius:50%;background:var(--primary);color:#fff;display:grid;place-items:center;opacity:0;transform:scale(.6);transition:.15s ease;z-index:2}
    .tb-tick svg{width:13px;height:13px}

    .tb-toggles{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px}
    .tb-toggle{display:flex;align-items:center;gap:10px;border:1px solid var(--border);border-radius:11px;padding:11px 14px;background:var(--surface);font-size:13.5px;color:var(--text);cursor:pointer}
    .tb-toggle input{width:18px;height:18px;accent-color:var(--primary)}

    .tb-colors{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:14px}
    .tb-color{border:1px solid var(--border);border-radius:12px;padding:12px;background:var(--surface)}
    .tb-color label{display:block;font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:8px}
    .tb-color .row{display:flex;align-items:center;gap:8px}
    .tb-color input[type=color]{width:42px;height:38px;border:1px solid var(--border);border-radius:8px;background:none;padding:2px;cursor:pointer}
    .tb-color input[type=text]{flex:1;min-width:0;padding:8px 10px;border:1px solid var(--border-strong);border-radius:8px;background:var(--surface);color:var(--text);font:inherit;font-size:13px;font-family:ui-monospace,monospace}
    .tb-color .reset{font-size:11px;color:var(--text-muted);background:none;border:0;cursor:pointer;margin-top:6px}
    .tb-color .reset:hover{color:var(--primary)}

    .tb-field{margin-bottom:14px}
    .tb-field label{display:block;font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:5px}
    .tb-field .hint{font-size:11.5px;color:var(--text-muted);margin-top:4px}
    .tb-input{width:100%;max-width:520px;padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px}
    .tb-input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}

    .tb-fontcard{display:flex;align-items:center;gap:12px;border:1px solid var(--border);border-radius:12px;padding:12px 14px;background:var(--surface);flex-wrap:wrap}
    .tb-fontcard .fam{font-size:14px;font-weight:600;color:var(--text)}
    .tb-bar{position:sticky;bottom:0;background:var(--surface);border-top:1px solid var(--border);padding:14px 0;margin-top:8px;display:flex;gap:10px;align-items:center;z-index:5}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $errors->first() }}</div>
@endif

@php
    // Tiny inline preview gradients per variant (just a visual hint on the card).
    $hPrev = [
        'hv-01' => 'background:linear-gradient(180deg,#0b2545 0 30%,#fff 30%)',
        'hv-02' => 'background:linear-gradient(135deg,#dfe7f5,#fff);',
        'hv-03' => 'background:linear-gradient(180deg,#0a1830 0 55%,#0b2545 55%);border-bottom:3px solid #c79a3b',
        'hv-04' => 'background:linear-gradient(180deg,#0b2545 0 45%,#f6f8fc 45%)',
        'hv-05' => 'background:linear-gradient(180deg,#000 0 30%,#fff 30%)',
    ];
    $fPrev = [
        'fv-01' => 'background:#0b2545',
        'fv-02' => 'background:#0a0f1a',
        'fv-03' => 'background:#f6f8fc;border:1px solid var(--border)',
        'fv-04' => 'background:linear-gradient(135deg,#0a1830,#1c3a6e)',
        'fv-05' => 'background:linear-gradient(90deg,#0b2545 60%,#13294b)',
    ];
    $tick = '<span class="tb-tick"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>';
    $toggleLabels = [
        'header_sticky'=>'Sticky header','header_topbar'=>'Top info bar','header_social'=>'Social icons',
        'header_cta'=>'CTA button','header_login'=>'Login + Pay links','header_glass'=>'Glass effect','header_transparent'=>'Transparent (over hero)',
    ];
@endphp

<form method="POST" action="{{ route('admin.theme.save') }}">
    @csrf

    {{-- HEADER PICKER --}}
    <div class="tb-section">
        <p class="tb-h">Header layout</p>
        <div class="tb-cards">
            @foreach ($headers as $key => $h)
                <label class="tb-card">
                    <input type="radio" name="header_variant" value="{{ $key }}" {{ $current['header_variant'] === $key ? 'checked' : '' }}>
                    {!! $tick !!}
                    <span class="tb-prev" style="{{ $hPrev[$key] ?? '' }}"></span>
                    <span class="tb-body"><span class="tb-name">{{ $h['name'] }}</span><span class="tb-desc">{{ $h['desc'] }}</span></span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- FOOTER PICKER --}}
    <div class="tb-section">
        <p class="tb-h">Footer layout</p>
        <div class="tb-cards">
            @foreach ($footers as $key => $f)
                <label class="tb-card">
                    <input type="radio" name="footer_variant" value="{{ $key }}" {{ $current['footer_variant'] === $key ? 'checked' : '' }}>
                    {!! $tick !!}
                    <span class="tb-prev" style="{{ $fPrev[$key] ?? '' }}"></span>
                    <span class="tb-body"><span class="tb-name">{{ $f['name'] }}</span><span class="tb-desc">{{ $f['desc'] }}</span></span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- HEADER TOGGLES --}}
    <div class="tb-section">
        <p class="tb-h">Header elements</p>
        <div class="tb-toggles">
            @foreach ($toggleLabels as $key => $label)
                <label class="tb-toggle">
                    <input type="hidden" name="{{ $key }}" value="0">
                    <input type="checkbox" name="{{ $key }}" value="1" {{ !empty($current[$key]) ? 'checked' : '' }}>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    {{-- COLORS --}}
    <div class="tb-section">
        <p class="tb-h">Brand colors</p>
        <div class="tb-colors">
            @foreach ($colors as $key => $label)
                @php $val = $current[$key]; $def = $colorDefaults[$key]; @endphp
                <div class="tb-color">
                    <label>{{ $label }}</label>
                    <div class="row">
                        <input type="color" value="{{ $val }}" data-color-for="{{ $key }}">
                        <input type="text" name="{{ $key }}" id="hex-{{ $key }}" value="{{ $val }}" placeholder="{{ $def }}">
                    </div>
                    <button type="button" class="reset" data-reset="{{ $key }}" data-default="{{ $def }}">Reset to default</button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- BUTTON STYLING --}}
    <div class="tb-section">
        <p class="tb-h">Header button styling</p>
        <div class="tb-colors" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
            @foreach ($btnColors as $key => $label)
                @php $val = $current[$key] ?? ''; $def = $colorDefaults[$key] ?? ''; @endphp
                <div class="tb-color">
                    <label>{{ $label }}</label>
                    <div class="row">
                        <input type="color" value="{{ $val ?: $def }}" data-color-for="{{ $key }}">
                        <input type="text" name="{{ $key }}" id="hex-{{ $key }}" value="{{ $val }}" placeholder="{{ $def }}" style="font-family:ui-monospace,monospace">
                    </div>
                    <button type="button" class="reset" data-reset="{{ $key }}" data-default="{{ $def }}">Reset</button>
                </div>
            @endforeach
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-top:12px">
            @foreach ($btnText as $key => $label)
                <div class="tb-field" style="margin:0">
                    <label>{{ $label }}</label>
                    <input class="tb-input" type="text" name="{{ $key }}" value="{{ $current[$key] ?? '' }}" placeholder="e.g. 8px" style="max-width:100%">
                </div>
            @endforeach
        </div>
    </div>

    {{-- MENU STYLING --}}
    <div class="tb-section">
        <p class="tb-h">Menu styling</p>
        <div class="tb-colors" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
            @foreach ($menuColors as $key => $label)
                @php $val = $current[$key] ?? ''; $def = $colorDefaults[$key] ?? ''; @endphp
                <div class="tb-color">
                    <label>{{ $label }}</label>
                    <div class="row">
                        <input type="color" value="{{ $val ?: $def }}" data-color-for="{{ $key }}">
                        <input type="text" name="{{ $key }}" id="hex-{{ $key }}" value="{{ $val }}" placeholder="{{ $def }}" style="font-family:ui-monospace,monospace">
                    </div>
                    <button type="button" class="reset" data-reset="{{ $key }}" data-default="{{ $def }}">Reset</button>
                </div>
            @endforeach
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-top:12px">
            @foreach ($menuText as $key => $label)
                <div class="tb-field" style="margin:0">
                    <label>{{ $label }}</label>
                    <input class="tb-input" type="text" name="{{ $key }}" value="{{ $current[$key] ?? '' }}" placeholder="e.g. .92rem" style="max-width:100%">
                </div>
            @endforeach
        </div>
    </div>

    {{-- FONTS --}}
    <div class="tb-section">
        <p class="tb-h">Typography</p>

        <div class="tb-field">
            <label>Heading font (CSS font-family)</label>
            <input class="tb-input" type="text" name="theme_heading_font" value="{{ $current['heading_font'] }}" placeholder='e.g. "Playfair Display", serif'>
        </div>
        <div class="tb-field">
            <label>Body font (CSS font-family)</label>
            <input class="tb-input" type="text" name="theme_body_font" value="{{ $current['body_font'] }}" placeholder='e.g. "Poppins", sans-serif'>
        </div>
        <div class="tb-field">
            <label>Google Fonts &lt;link&gt; URL (optional)</label>
            <input class="tb-input" type="text" name="theme_google_fonts_url" value="{{ $current['google_fonts'] }}" placeholder="https://fonts.googleapis.com/css2?family=...">
            <div class="hint">Paste the full stylesheet URL from fonts.google.com, then use its family name above.</div>
        </div>
    </div>

    <div class="tb-bar">
        <button type="submit" class="btn primary"><x-admin.icon name="cog"/> Save &amp; apply theme</button>
        <span style="font-size:12.5px;color:var(--text-muted)">Changes go live immediately.</span>
    </div>
</form>

{{-- CUSTOM FONT UPLOAD (separate multipart form) --}}
<div class="tb-section" style="margin-top:26px">
    <p class="tb-h">Custom font upload</p>
    @if (!empty($current['custom_url']) && !empty($current['custom_family']))
        <div class="tb-fontcard" style="margin-bottom:12px">
            <span class="fam" style="font-family:'{{ $current['custom_family'] }}'">{{ $current['custom_family'] }}</span>
            <span style="font-size:12px;color:var(--text-muted)">— installed. Set it as Heading/Body font above (use the name <code>{{ $current['custom_family'] }}</code>).</span>
            <form method="POST" action="{{ route('admin.theme.font.remove') }}" style="margin-left:auto">@csrf<button class="btn" type="submit" style="font-size:12px">Remove</button></form>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.theme.font') }}" enctype="multipart/form-data" class="card" style="padding:16px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
        @csrf
        <div class="tb-field" style="margin:0">
            <label>Font file (.woff2 / .woff / .ttf / .otf)</label>
            <input class="tb-input" type="file" name="font" accept=".woff2,.woff,.ttf,.otf" required>
        </div>
        <div class="tb-field" style="margin:0">
            <label>Font family name</label>
            <input class="tb-input" type="text" name="font_family" placeholder="e.g. My Brand Font" style="max-width:260px">
        </div>
        <button type="submit" class="btn primary"><x-admin.icon name="upload"/> Upload font</button>
    </form>
    <div class="hint" style="font-size:11.5px;color:var(--text-muted);margin-top:8px">.woff2 is best for web. After upload, type the family name into Heading or Body font above and Save.</div>
</div>

<script>
(function () {
    // Sync color picker ↔ hex text
    document.querySelectorAll('input[type=color][data-color-for]').forEach(function (cp) {
        var key = cp.getAttribute('data-color-for');
        var hex = document.getElementById('hex-' + key);
        cp.addEventListener('input', function () { hex.value = cp.value; });
        hex.addEventListener('input', function () { if (/^#[0-9a-fA-F]{6}$/.test(hex.value)) cp.value = hex.value; });
    });
    document.querySelectorAll('.reset[data-reset]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var key = btn.getAttribute('data-reset');
            var def = btn.getAttribute('data-default');
            var hex = document.getElementById('hex-' + key);
            var cp = document.querySelector('input[type=color][data-color-for="' + key + '"]');
            hex.value = '';            // empty → falls back to default on the site
            if (cp) cp.value = def;
            hex.setAttribute('placeholder', def);
        });
    });
})();
</script>

@endsection
