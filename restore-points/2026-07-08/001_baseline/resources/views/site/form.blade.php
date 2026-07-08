{{-- Public render of a custom form (e.g. admission enquiry). Self-contained. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--navy:#0e2f5e;--blue:#1f5aa8;--gold:#eda52a;--ink:#1f2937;--muted:#6b7280;--line:#e3e8f0;--soft:#f4f7fc}
        *{box-sizing:border-box;margin:0}
        body{font-family:'Poppins',system-ui,sans-serif;background:var(--soft);color:var(--ink);line-height:1.6;padding:40px 16px}
        .card{max-width:640px;margin:0 auto;background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden;box-shadow:0 20px 50px rgba(14,47,94,.10)}
        .head{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;padding:30px 32px}
        .head h1{font-size:24px;font-weight:700}
        .head p{color:#d7e2f4;margin-top:6px;font-size:14.5px}
        .body{padding:28px 32px 32px}
        .fld{margin-bottom:18px}
        .fld label{display:block;font-size:13.5px;font-weight:600;margin-bottom:6px}
        .req{color:var(--gold)}
        .fld input,.fld textarea,.fld select{width:100%;padding:11px 13px;border:1px solid #cdd6e4;border-radius:10px;font:inherit;font-size:15px;background:#fff}
        .fld input:focus,.fld textarea:focus,.fld select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(31,90,168,.15)}
        .fld textarea{min-height:110px;resize:vertical}
        .err{color:#dc2626;font-size:12.5px;margin-top:5px}
        .btn{display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:#3a2400;font-weight:600;padding:13px 26px;border:none;border-radius:999px;font-size:15px;cursor:pointer;transition:.2s}
        .btn:hover{background:#f7b733}
        .ok{background:#dcfce7;border:1px solid #16a34a;color:#065f46;padding:14px 16px;border-radius:12px;margin-bottom:20px;font-size:14px;font-weight:500}
        .hp{position:absolute;left:-9999px}
        .foot{text-align:center;color:var(--muted);font-size:12.5px;margin-top:22px}
        .foot a{color:var(--blue);text-decoration:none}
    </style>
</head>
<body>
    <div class="card">
        <div class="head">
            <h1>{{ $form->title }}</h1>
            @if ($form->description)<p>{{ $form->description }}</p>@endif
        </div>
        <div class="body">
            @if (session('form_success'))
                <div class="ok">{{ session('form_success') }}</div>
            @endif

            <form method="POST" action="{{ url('/forms/'.$form->slug) }}">
                @csrf
                <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">

                @foreach ($form->fields ?? [] as $field)
                    @php $key = $field['key']; $type = $field['type']; $req = $field['required'] ?? false; @endphp
                    <div class="fld">
                        <label for="f_{{ $key }}">{{ $field['label'] }}@if ($req) <span class="req">*</span>@endif</label>

                        @if ($type === 'textarea')
                            <textarea id="f_{{ $key }}" name="{{ $key }}" placeholder="{{ $field['placeholder'] ?? '' }}" @if($req) required @endif>{{ old($key) }}</textarea>
                        @elseif ($type === 'select')
                            <select id="f_{{ $key }}" name="{{ $key }}" @if($req) required @endif>
                                <option value="">— Select —</option>
                                @foreach (($field['options'] ?? []) as $opt)
                                    <option value="{{ $opt }}" @selected(old($key) === $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="f_{{ $key }}" type="{{ in_array($type, ['email','tel','number','date']) ? $type : 'text' }}" name="{{ $key }}" value="{{ old($key) }}" placeholder="{{ $field['placeholder'] ?? '' }}" @if($req) required @endif>
                        @endif

                        @error($key)<div class="err">{{ $message }}</div>@enderror
                    </div>
                @endforeach

                <button type="submit" class="btn">Submit →</button>
            </form>
        </div>
    </div>
    <div class="foot"><a href="{{ url('/') }}">← Back to website</a></div>
</body>
</html>
