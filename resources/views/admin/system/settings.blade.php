@extends('admin.layout')

@section('title', 'Settings')
@section('subtitle', 'Site configuration')

@section('content')

<style>
    .set-row{margin-bottom:16px}
    .set-label{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:5px}
    .set-key{font-size:11px;color:var(--text-muted);font-family:ui-monospace,monospace}
    .set-input,.set-textarea{width:100%;max-width:520px;padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px}
    .set-input:focus,.set-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .set-textarea{min-height:90px;resize:vertical;font-family:ui-monospace,monospace;font-size:13px}
    .set-check{display:flex;align-items:center;gap:9px;font-size:14px;cursor:pointer}
    .set-check input{width:18px;height:18px;accent-color:var(--primary)}

    /* Tabs */
    .set-tabs{display:flex;flex-wrap:wrap;gap:6px;border-bottom:1px solid var(--border);margin-bottom:18px;padding-bottom:0}
    .set-tab{appearance:none;background:none;border:0;border-bottom:2px solid transparent;padding:9px 14px;font:inherit;font-size:13.5px;font-weight:600;color:var(--text-muted);cursor:pointer;border-radius:8px 8px 0 0;transition:.15s ease;white-space:nowrap;display:inline-flex;align-items:center;gap:7px}
    .set-tab:hover{color:var(--text);background:var(--surface-2,rgba(0,0,0,.03))}
    .set-tab.is-active{color:var(--primary-strong);border-bottom-color:var(--primary)}
    .set-tab .set-tab-count{font-size:11px;font-weight:700;background:var(--surface-2,rgba(0,0,0,.06));color:var(--text-muted);border-radius:999px;padding:1px 7px}
    .set-tab.is-active .set-tab-count{background:var(--ring);color:var(--primary-strong)}
    .set-panel{display:none}
    .set-panel.is-active{display:block}
</style>

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

@php
    // Settings whose value should be picked from a dropdown instead of typed.
    $selectOptions = [
        'header_variant' => [
            'hv-01' => 'Header 01 · Modern Clean Corporate',
            'hv-02' => 'Header 02 · Glassmorphism Floating',
            'hv-03' => 'Header 03 · Luxury Premium (Navy + Gold)',
            'hv-04' => 'Header 04 · Enterprise Mega Menu',
            'hv-05' => 'Header 05 · Next.js / Vercel Minimal',
        ],
        'footer_variant' => [
            'fv-01' => 'Footer 01 · Classic Navy (default)',
            'fv-02' => 'Footer 02 · Centered Minimal',
            'fv-03' => 'Footer 03 · Light',
            'fv-04' => 'Footer 04 · Gradient Accent',
            'fv-05' => 'Footer 05 · Compact',
        ],
        'recaptcha_version' => [
            'v2' => 'Google reCAPTCHA v2 (Checkbox "I am not a robot")',
            'v3' => 'Google reCAPTCHA v3 (Invisible Score Verification)',
        ],
    ];

    $render = function ($s) use ($selectOptions) {
        $name = "settings[{$s->id}]";
        $label = \Illuminate\Support\Str::headline($s->key);
        echo '<div class="set-row">';
        if (isset($selectOptions[$s->key])) {
            echo '<label class="set-label">'.$label.' <span class="set-key">· '.$s->key.'</span></label>';
            echo '<select class="set-input" name="'.$name.'">';
            foreach ($selectOptions[$s->key] as $val => $optLabel) {
                $sel = ((string) $s->value === (string) $val) ? ' selected' : '';
                echo '<option value="'.e($val).'"'.$sel.'>'.e($optLabel).'</option>';
            }
            echo '</select>';
        } elseif ($s->type === 'boolean') {
            $on = filter_var($s->value, FILTER_VALIDATE_BOOLEAN);
            echo '<label class="set-check"><input type="hidden" name="'.$name.'" value="0"><input type="checkbox" name="'.$name.'" value="1" '.($on ? 'checked' : '').'> <span><span style="font-weight:600">'.$label.'</span> <span class="set-key">'.$s->key.'</span></span></label>';
        } else {
            echo '<label class="set-label">'.$label.' <span class="set-key">· '.$s->key.'</span></label>';
            if (in_array($s->type, ['json', 'array', 'text'])) {
                echo '<textarea class="set-textarea" name="'.$name.'">'.e($s->value).'</textarea>';
            } else {
                $t = in_array($s->type, ['integer', 'float']) ? 'number' : 'text';
                echo '<input class="set-input" type="'.$t.'" name="'.$name.'" value="'.e($s->value).'">';
            }
        }
        echo '</div>';
    };

    // Build the list of tabs: only groups that actually have settings, plus "Other".
    $tabs = [];
    foreach ($groups as $group) {
        $rows = $grouped->get($group->id, collect());
        if ($rows->count()) {
            $tabs[] = ['id' => 'g'.$group->id, 'name' => $group->name, 'desc' => $group->description, 'rows' => $rows];
        }
    }
    $ungrouped = $settings->whereNull('group_id');
    if ($ungrouped->count()) {
        $tabs[] = ['id' => 'other', 'name' => 'Other', 'desc' => null, 'rows' => $ungrouped];
    }
@endphp

@if ($settings->isEmpty())
    <div class="card"><div class="empty">No settings defined yet.</div></div>
@else
<form method="POST" action="{{ url('/admin/settings') }}">
    @csrf

    {{-- Tab buttons --}}
    <div class="set-tabs" role="tablist">
        @foreach ($tabs as $i => $tab)
            <button type="button" class="set-tab {{ $i === 0 ? 'is-active' : '' }}" data-tab="{{ $tab['id'] }}" role="tab">
                {{ $tab['name'] }}
                <span class="set-tab-count">{{ $tab['rows']->count() }}</span>
            </button>
        @endforeach
    </div>

    {{-- Tab panels --}}
    @foreach ($tabs as $i => $tab)
        <div class="set-panel {{ $i === 0 ? 'is-active' : '' }}" data-panel="{{ $tab['id'] }}">
            <div class="widget" style="margin-bottom:16px">
                <div class="widget__head">
                    <x-admin.icon name="cog" style="width:18px;height:18px;color:var(--primary-strong)"/>
                    <span class="widget__title">{{ $tab['name'] }}</span>
                    @if ($tab['desc'])<span class="widget__sub">· {{ $tab['desc'] }}</span>@endif
                </div>
                <div class="widget__body">
                    @foreach ($tab['rows'] as $s) @php $render($s) @endphp @endforeach
                </div>
            </div>
        </div>
    @endforeach

    <button type="submit" class="btn primary"><x-admin.icon name="cog"/> Save settings</button>
</form>

<script>
(function () {
    var tabs = document.querySelectorAll('.set-tab');
    var panels = document.querySelectorAll('.set-panel');
    tabs.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-tab');
            tabs.forEach(function (t) { t.classList.toggle('is-active', t === btn); });
            panels.forEach(function (p) { p.classList.toggle('is-active', p.getAttribute('data-panel') === id); });
            try { localStorage.setItem('adminSettingsTab', id); } catch (e) {}
        });
    });
    // Restore last-opened tab
    try {
        var saved = localStorage.getItem('adminSettingsTab');
        if (saved) {
            var btn = document.querySelector('.set-tab[data-tab="' + saved + '"]');
            if (btn) btn.click();
        }
    } catch (e) {}
})();
</script>
@endif

@endsection
