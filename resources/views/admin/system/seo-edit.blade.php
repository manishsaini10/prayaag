@extends('admin.layout')

@section('title', 'Edit SEO')
@section('subtitle', $page->title)

@section('actions')
    <a href="{{ route('admin.seo') }}" class="btn"><x-admin.icon name="chevron-left"/> Back to SEO</a>
    <a href="{{ url('/'.($page->slug === 'home' ? '' : $page->slug)) }}" target="_blank" class="btn"><x-admin.icon name="globe"/> View page</a>
@endsection

@section('content')

<style>
    .seo-wrap{display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start}
    @media (max-width:1000px){.seo-wrap{grid-template-columns:1fr}}
    .seo-field{margin-bottom:16px}
    .seo-field label{display:block;font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:5px}
    .seo-field .hint{font-size:11.5px;color:var(--text-muted);margin-top:4px}
    .seo-input,.seo-textarea{width:100%;padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px}
    .seo-input:focus,.seo-textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .seo-textarea{min-height:70px;resize:vertical}
    .seo-count{float:right;font-size:11px;color:var(--text-muted)}
    .seo-count.over{color:var(--danger)}
    .seo-check{display:inline-flex;align-items:center;gap:8px;font-size:13.5px;cursor:pointer;margin-right:18px}
    .seo-check input{width:18px;height:18px;accent-color:var(--primary)}
    .seo-h{font-size:12px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:var(--text-muted);margin:22px 0 12px;padding-bottom:6px;border-bottom:1px solid var(--border)}
    .seo-h:first-child{margin-top:0}

    /* Preview cards */
    .seo-prev{position:sticky;top:16px}
    .pv-card{border:1px solid var(--border);border-radius:14px;background:var(--surface);overflow:hidden;margin-bottom:16px}
    .pv-card h4{margin:0;padding:10px 14px;font-size:11px;letter-spacing:.05em;text-transform:uppercase;color:var(--text-muted);border-bottom:1px solid var(--border)}
    .pv-body{padding:14px}
    .g-url{font-size:12px;color:#202124;}
    .g-title{font-size:18px;color:#1a0dab;line-height:1.3;margin:2px 0 3px}
    .g-desc{font-size:13px;color:#4d5156;line-height:1.5}
    .fb-img{width:100%;height:170px;object-fit:cover;background:var(--surface-hover);display:block}
    .fb-meta{padding:10px 12px;background:#f2f3f5}
    .fb-domain{font-size:11px;text-transform:uppercase;color:#606770}
    .fb-title{font-size:15px;font-weight:600;color:#1d2129;line-height:1.3;margin:3px 0}
    .fb-desc{font-size:12.5px;color:#606770;line-height:1.4}
    [data-theme="dark"] .g-url{color:#bdc1c6}[data-theme="dark"] .g-title{color:#8ab4f8}[data-theme="dark"] .g-desc{color:#bdc1c6}
</style>

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.seo.update', $page) }}">
    @csrf
    @method('PUT')

    <div class="seo-wrap">
        {{-- LEFT: form --}}
        <div class="card" style="padding:20px">
            <div class="seo-h">Basic SEO</div>

            <div class="seo-field">
                <label>Meta title <span class="seo-count" id="c-title">0</span></label>
                <input class="seo-input" type="text" name="seo[title]" id="f-title"
                       value="{{ $current['title'] ?? '' }}" placeholder="{{ $auto['title'] }}"
                       data-auto="{{ $auto['title'] }}" maxlength="255">
                <div class="hint">Leave blank to auto-use: <em>{{ $auto['title'] }}</em></div>
            </div>

            <div class="seo-field">
                <label>Meta description <span class="seo-count" id="c-desc">0</span></label>
                <textarea class="seo-textarea" name="seo[description]" id="f-desc"
                          placeholder="{{ $auto['description'] }}" data-auto="{{ $auto['description'] }}" maxlength="320">{{ $current['description'] ?? '' }}</textarea>
                <div class="hint">Aim for 150–160 characters. Blank = auto-generated from page content.</div>
            </div>

            <div class="seo-field">
                <label>Focus keywords</label>
                <input class="seo-input" type="text" name="seo[keywords]" value="{{ $current['keywords'] ?? '' }}" placeholder="{{ $auto['keywords'] }}">
            </div>

            <div class="seo-field">
                <label>Canonical URL</label>
                <input class="seo-input" type="url" name="seo[canonical]" value="{{ $current['canonical'] ?? '' }}" placeholder="{{ $auto['canonical'] }}">
            </div>

            <div class="seo-field">
                <label>Indexing</label>
                <label class="seo-check"><input type="hidden" name="seo[robots_index]" value="0"><input type="checkbox" name="seo[robots_index]" value="1" {{ ($current['robots_index'] ?? true) ? 'checked' : '' }}> Allow indexing (index)</label>
                <label class="seo-check"><input type="hidden" name="seo[robots_follow]" value="0"><input type="checkbox" name="seo[robots_follow]" value="1" {{ ($current['robots_follow'] ?? true) ? 'checked' : '' }}> Follow links (follow)</label>
            </div>

            <div class="seo-h">Social (Open Graph / Twitter)</div>

            <div class="seo-field">
                <label>OG type</label>
                <select class="seo-input" name="seo[og_type]">
                    @foreach (['website' => 'Website', 'article' => 'Article', 'profile' => 'Profile'] as $v => $l)
                        <option value="{{ $v }}" {{ ($current['og_type'] ?? $auto['og_type']) === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="seo-field">
                <label>Schema.org page type</label>
                <select class="seo-input" name="seo[schema_type]">
                    @foreach ($schemaTypes as $v => $l)
                        <option value="{{ $v }}" {{ ($current['schema_type'] ?? 'WebPage') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                <div class="hint">Controls the JSON-LD type (e.g. set <em>Contact Page</em> for /contact-us, <em>About Page</em> for /about-us).</div>
            </div>

            <div class="seo-field">
                <label>Social title</label>
                <input class="seo-input" type="text" name="seo[og_title]" id="f-ogtitle" value="{{ $current['og_title'] ?? '' }}" placeholder="{{ $auto['og_title'] }}" data-auto="{{ $auto['og_title'] }}">
            </div>
            <div class="seo-field">
                <label>Social description</label>
                <textarea class="seo-textarea" name="seo[og_description]" id="f-ogdesc" placeholder="{{ $auto['og_description'] }}" data-auto="{{ $auto['og_description'] }}">{{ $current['og_description'] ?? '' }}</textarea>
            </div>
            <div class="seo-field">
                <label>Social image URL</label>
                <input class="seo-input" type="url" name="seo[og_image]" id="f-ogimg" value="{{ $current['og_image'] ?? '' }}" placeholder="{{ $auto['og_image'] }}" data-auto="{{ $auto['og_image'] }}">
                <div class="hint">1200×630px recommended. Blank = first image on the page, or the site default.</div>
            </div>

            <details style="margin-top:8px">
                <summary style="cursor:pointer;font-size:12.5px;color:var(--text-muted);font-weight:600">Twitter overrides + advanced</summary>
                <div style="padding-top:14px">
                    <div class="seo-field"><label>Twitter title</label><input class="seo-input" type="text" name="seo[twitter_title]" value="{{ $current['twitter_title'] ?? '' }}" placeholder="{{ $auto['twitter_title'] }}"></div>
                    <div class="seo-field"><label>Twitter description</label><textarea class="seo-textarea" name="seo[twitter_description]" placeholder="{{ $auto['twitter_description'] }}">{{ $current['twitter_description'] ?? '' }}</textarea></div>
                    <div class="seo-field"><label>Twitter image URL</label><input class="seo-input" type="url" name="seo[twitter_image]" value="{{ $current['twitter_image'] ?? '' }}" placeholder="{{ $auto['twitter_image'] }}"></div>
                    <div class="seo-field"><label>Raw robots directive (overrides the checkboxes)</label><input class="seo-input" type="text" name="seo[robots_raw]" value="{{ $current['robots'] ?? '' }}" placeholder="e.g. noindex, nofollow, max-snippet:-1"></div>
                </div>
            </details>

            <div style="margin-top:20px;display:flex;gap:10px;align-items:center">
                <button type="submit" class="btn primary"><x-admin.icon name="cog"/> Save SEO</button>
                <span class="muted" style="font-size:12.5px">Applies to the live page immediately.</span>
            </div>
        </div>

        {{-- RIGHT: live previews --}}
        <div class="seo-prev">
            <div class="pv-card">
                <h4>Google preview</h4>
                <div class="pv-body">
                    <div class="g-url">{{ rtrim(config('app.url'), '/') }}/{{ $page->slug === 'home' ? '' : $page->slug }}</div>
                    <div class="g-title" id="pv-g-title">{{ $auto['title'] }}</div>
                    <div class="g-desc" id="pv-g-desc">{{ $auto['description'] }}</div>
                </div>
            </div>
            <div class="pv-card">
                <h4>Facebook / LinkedIn preview</h4>
                <img class="fb-img" id="pv-fb-img" src="{{ $current['og_image'] ?? $auto['og_image'] }}" alt="" onerror="this.style.display='none'">
                <div class="fb-meta">
                    <div class="fb-domain">{{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'website' }}</div>
                    <div class="fb-title" id="pv-fb-title">{{ $auto['og_title'] }}</div>
                    <div class="fb-desc" id="pv-fb-desc">{{ $auto['og_description'] }}</div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    var $ = function (id) { return document.getElementById(id); };
    function val(el) { return (el && el.value.trim()) || (el && el.getAttribute('data-auto')) || ''; }

    function bind(inputId, previewId, counterId, max) {
        var inp = $(inputId), pv = $(previewId), c = counterId ? $(counterId) : null;
        if (!inp) return;
        var upd = function () {
            if (pv) pv.textContent = val(inp);
            if (c) {
                var n = inp.value.length;
                c.textContent = n + (max ? '/' + max : '');
                c.classList.toggle('over', max && n > max);
            }
        };
        inp.addEventListener('input', upd); upd();
    }
    bind('f-title', 'pv-g-title', 'c-title', 60);
    bind('f-desc', 'pv-g-desc', 'c-desc', 160);
    bind('f-ogtitle', 'pv-fb-title');
    bind('f-ogdesc', 'pv-fb-desc');

    var img = $('f-ogimg'), fbimg = $('pv-fb-img');
    if (img && fbimg) {
        img.addEventListener('input', function () {
            var src = val(img);
            if (src) { fbimg.src = src; fbimg.style.display = 'block'; }
        });
    }
    // Social title/desc fall back to basic title/desc when empty
    var ogt = $('f-ogtitle'), ft = $('f-title');
    if (ogt && ft) ft.addEventListener('input', function () { if (!ogt.value.trim()) $('pv-fb-title').textContent = val(ft); });
})();
</script>

@endsection
