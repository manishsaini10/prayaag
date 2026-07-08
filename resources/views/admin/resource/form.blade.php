@extends('admin.layout')

@php $isEdit = $mode === 'edit'; @endphp

@section('title', ($isEdit ? 'Edit ' : 'New ') . strtolower($def['singular'] ?? 'item'))
@section('subtitle', $def['label'])

@section('actions')
    <a href="{{ url('/admin/m/'.$resource) }}" class="btn"><x-admin.icon name="chevron-right" style="transform:rotate(180deg)"/> Back to {{ strtolower($def['label']) }}</a>
@endsection

@section('content')

<style>
    .frm{max-width:760px}
    .frm-field{margin-bottom:18px}
    .frm-label{display:block;font-size:13px;font-weight:600;color:var(--text);margin-bottom:6px}
    .frm-input,.frm-select,.frm-textarea{width:100%;padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px}
    .frm-input:focus,.frm-select:focus,.frm-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .frm-textarea{resize:vertical;min-height:90px}
    .frm-check{display:flex;align-items:center;gap:9px;font-size:14px;color:var(--text);cursor:pointer}
    .frm-check input{width:18px;height:18px;accent-color:var(--primary)}
    .frm-err{color:var(--danger);font-size:12.5px;margin-top:5px}
    .frm-actions{display:flex;gap:10px;padding-top:6px;border-top:1px solid var(--border);margin-top:4px}
</style>

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">
        Please fix the highlighted fields below.
    </div>
@endif

<div class="card frm" style="padding:22px">
    <form method="POST" action="{{ $isEdit ? url('/admin/m/'.$resource.'/'.$item->getKey()) : url('/admin/m/'.$resource) }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        @foreach ($def['fields'] as $f)
            @php
                $key = $f['key'];
                $type = $f['type'] ?? 'text';
                $attr = $item->getAttribute($key);
                $nullable = str_contains($f['rules'] ?? '', 'nullable');
            @endphp

            <div class="frm-field">
                @if ($type === 'bool')
                    <label class="frm-check">
                        <input type="hidden" name="{{ $key }}" value="0">
                        <input type="checkbox" name="{{ $key }}" value="1" @checked(old($key, $attr))>
                        {{ $f['label'] }}
                    </label>
                @else
                    <label class="frm-label" for="f_{{ $key }}">{{ $f['label'] }}</label>

                    @switch($type)
                        @case('textarea')
                            <textarea id="f_{{ $key }}" name="{{ $key }}" rows="{{ $f['rows'] ?? 4 }}" class="frm-textarea">{{ old($key, $attr) }}</textarea>
                            @break

                        @case('select')
                            <select id="f_{{ $key }}" name="{{ $key }}" class="frm-select">
                                @foreach ($f['options'] as $ov => $ol)
                                    <option value="{{ $ov }}" @selected((string) old($key, $attr) === (string) $ov)>{{ $ol }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('belongsTo')
                            <select id="f_{{ $key }}" name="{{ $key }}" class="frm-select">
                                @if ($nullable)<option value="">— None —</option>@endif
                                @foreach (($options[$key] ?? []) as $ov => $ol)
                                    <option value="{{ $ov }}" @selected((string) old($key, $attr) === (string) $ov)>{{ $ol }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('datetime')
                            <input type="datetime-local" id="f_{{ $key }}" name="{{ $key }}" class="frm-input"
                                   value="{{ old($key, $attr ? \Illuminate\Support\Carbon::parse($attr)->format('Y-m-d\TH:i') : '') }}">
                            @break

                        @case('date')
                            <input type="date" id="f_{{ $key }}" name="{{ $key }}" class="frm-input"
                                   value="{{ old($key, $attr ? \Illuminate\Support\Carbon::parse($attr)->format('Y-m-d') : '') }}">
                            @break

                        @case('number')
                            <input type="number" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" value="{{ old($key, $attr) }}">
                            @break

                        @case('password')
                            <input type="password" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" autocomplete="new-password"
                                   placeholder="{{ $isEdit ? 'Leave blank to keep current password' : '' }}">
                            @break

                        @case('email')
                            <input type="email" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" value="{{ old($key, $attr) }}">
                            @break

                        @default
                            <input type="text" id="f_{{ $key }}" name="{{ $key }}" class="frm-input" value="{{ old($key, $attr) }}"
                                   @if ($type === 'slug') data-slug-target @endif>
                    @endswitch
                @endif

                @error($key)<div class="frm-err">{{ $message }}</div>@enderror
            </div>
        @endforeach

        <div class="frm-actions">
            <button type="submit" class="btn primary">{{ $isEdit ? 'Save changes' : 'Create '.strtolower($def['singular'] ?? 'item') }}</button>
            <a href="{{ url('/admin/m/'.$resource) }}" class="btn">Cancel</a>
        </div>
    </form>
</div>

<script>
    // Auto-fill an empty slug field from the first title/name input.
    (function () {
        const slug = document.querySelector('[data-slug-target]');
        if (!slug) return;
        const source = document.querySelector('#f_title, #f_name');
        if (!source) return;
        const slugify = (s) => s.toString().toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
        source.addEventListener('blur', () => { if (!slug.value.trim()) slug.value = slugify(source.value); });
    })();
</script>

@endsection
