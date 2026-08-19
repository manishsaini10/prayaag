@extends('admin.layout')

@section('title', $widget->exists ? 'Edit Widget' : 'New Widget')
@section('subtitle', 'Custom page-builder widget')

@section('content')

<style>
    .wb label { display:flex; flex-direction:column; gap:4px; font-size:13px; color:var(--text); }
    .wb input, .wb select, .wb textarea { padding:8px 10px; border:1px solid var(--border); border-radius:8px; background:var(--surface); color:var(--text); font:inherit; }
    .wb textarea { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
    .wb code, .wb-help code { background:var(--surface-2); padding:1px 5px; border-radius:4px; font-size:12px; }
    .wb .frow { display:grid; grid-template-columns:1fr 1fr 120px 1fr 36px; gap:6px; align-items:center; }
    @media (max-width:720px){ .wb .frow{ grid-template-columns:1fr 1fr; } }

    /* tabs */
    .wb-tabs { display:flex; gap:4px; margin-bottom:18px; border-bottom:1px solid var(--border); }
    .wb-tab { padding:9px 16px; font-size:13.5px; font-weight:600; border:0; background:none; color:var(--text-muted); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; }
    .wb-tab.active { color:var(--primary-strong,#4f46e5); border-bottom-color:var(--primary-strong,#4f46e5); }

    /* help panel */
    .wb-help { font-size:13.5px; color:var(--text); line-height:1.7; max-width:920px; }
    .wb-help h3 { font-size:15px; margin:24px 0 8px; color:var(--text); }
    .wb-help h3:first-child { margin-top:0; }
    .wb-help h4 { font-size:13.5px; margin:14px 0 6px; color:var(--text); }
    .wb-help p { margin:0 0 10px; color:var(--text-muted); }
    .wb-help ul { margin:0 0 12px; padding-left:20px; color:var(--text-muted); }
    .wb-help li { margin:3px 0; }
    .wb-help pre { background:var(--surface-2); border:1px solid var(--border); border-radius:8px; padding:12px 14px; overflow:auto; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:12.5px; line-height:1.55; color:var(--text); margin:0 0 16px; }
    .wb-help table { width:100%; border-collapse:collapse; margin:0 0 16px; font-size:13px; }
    .wb-help th, .wb-help td { text-align:left; padding:7px 10px; border-bottom:1px solid var(--border); vertical-align:top; color:var(--text-muted); }
    .wb-help th { color:var(--text); }
    .wb-help .tag { display:inline-block; font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; background:var(--surface-2); color:var(--text-muted); margin-bottom:8px; }
</style>

<div class="wb-tabs">
    <button type="button" class="wb-tab active" data-tab="builder">✎ Builder</button>
    <button type="button" class="wb-tab" data-tab="help">❓ Help &amp; Examples</button>
</div>

<div id="tab-builder">

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft,#fee2e2);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">
        <ul style="margin:0;padding-left:18px">
            @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<form class="wb" method="POST" action="{{ $widget->exists ? route('admin.widgets.update', $widget->id) : route('admin.widgets.store') }}">
    @csrf
    @if ($widget->exists) @method('PUT') @endif

    <div class="card" style="padding:18px;margin-bottom:16px;display:grid;gap:14px;grid-template-columns:1fr 1fr">
        <label>
            <span>Name</span>
            <input name="name" value="{{ old('name', $widget->name) }}" placeholder="e.g. FAQ Accordion" required>
        </label>
        <label>
            <span>Slug <small style="color:var(--text-muted)">(auto from name if blank)</small></span>
            <input name="slug" value="{{ old('slug', $widget->slug) }}" placeholder="faq-accordion">
        </label>
        <label>
            <span>Category</span>
            <input name="category" value="{{ old('category', $widget->category ?: 'custom') }}" placeholder="custom">
        </label>
        <label style="flex-direction:row;align-items:center;gap:8px;align-self:end">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $widget->is_active ? 1 : 0) ? 'checked' : '' }} style="width:18px;height:18px;padding:0">
            <span>Active (show in Page Builder palette)</span>
        </label>
    </div>

    <div class="card" style="padding:18px;margin-bottom:16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <strong style="font-size:14px;color:var(--text)">Editable Fields</strong>
            <button type="button" class="btn-sm" id="addField">+ Add field</button>
        </div>
        <p style="font-size:12.5px;color:var(--text-muted);margin:0 0 12px">Each field becomes an editable input in the Page Builder. Use its <em>key</em> in the template below as <code>@{{ key }}</code>. Need help? Open the <strong>Help &amp; Examples</strong> tab above.</p>
        <div id="fieldRows" style="display:flex;flex-direction:column;gap:8px"></div>
    </div>

    <div class="card" style="padding:18px;margin-bottom:16px">
        <strong style="font-size:14px;color:var(--text)">HTML Template</strong>
        <p style="font-size:12.5px;color:var(--text-muted);margin:6px 0 10px">
            <code>@{{ key }}</code> = escaped text · <code>@{{{ key }}}</code> = raw HTML (for embeds) · add <code>data-reveal</code> for scroll animation. Tip: write the <em>inner</em> content only — the Page Builder wraps each widget in a section + container automatically.
        </p>
        <textarea name="template" rows="12" style="width:100%" required>{{ old('template', $widget->template) }}</textarea>
    </div>

    <div style="display:flex;gap:10px">
        <button type="submit" class="btn-sm primary">{{ $widget->exists ? 'Save changes' : 'Create widget' }}</button>
        <a href="{{ route('admin.widgets.index') }}" class="btn-sm">Cancel</a>
    </div>
</form>

</div>{{-- /tab-builder --}}

<div id="tab-help" class="wb-help" style="display:none">
@verbatim
<div class="card" style="padding:22px 24px">

<h3>1. What is a custom widget?</h3>
<p>A widget is a reusable block you can drop into any page from the Page Builder. You define: a <strong>name</strong>, some <strong>editable fields</strong>, and an <strong>HTML template</strong>. Whatever fields you add become editable inputs inside the Page Builder, and your template decides how they look on the page.</p>

<h3>2. Step by step</h3>
<ul>
  <li><strong>Name</strong> — shown in the palette (e.g. "FAQ Accordion").</li>
  <li><strong>Slug</strong> — the internal id (leave blank to auto-generate). Must be unique.</li>
  <li><strong>Category</strong> — how it's grouped in the palette (e.g. custom, school, marketing).</li>
  <li><strong>Active</strong> — tick to show it in the Page Builder. Untick to hide without deleting.</li>
  <li><strong>Editable Fields</strong> — add one row per editable value (key + label + type + default).</li>
  <li><strong>HTML Template</strong> — your markup, with field values dropped in via placeholders.</li>
  <li><strong>Save</strong> — done! It appears in the Page Builder palette instantly under its category.</li>
</ul>

<h3>3. Field types</h3>
<table>
  <thead><tr><th>Type</th><th>Use for</th></tr></thead>
  <tbody>
    <tr><td><code>text</code></td><td>Short single-line text (titles, labels, links).</td></tr>
    <tr><td><code>textarea</code></td><td>Longer text / paragraphs.</td></tr>
    <tr><td><code>image</code></td><td>An image URL (paste a link or an /images/... path).</td></tr>
    <tr><td><code>url</code></td><td>A link URL (for buttons, etc.).</td></tr>
    <tr><td><code>number</code></td><td>A number (counts, prices).</td></tr>
  </tbody>
</table>
<p>The <em>key</em> is what you use in the template. The <em>label</em> is just what the editor shows. The <em>default</em> is the starting value.</p>

<h3>4. Template syntax</h3>
<table>
  <thead><tr><th>Placeholder</th><th>What it does</th></tr></thead>
  <tbody>
    <tr><td><code>{{ heading }}</code></td><td>Prints the <code>heading</code> field as <strong>safe text</strong> (recommended for everything).</td></tr>
    <tr><td><code>{{{ embed }}}</code></td><td>Prints the <code>embed</code> field as <strong>raw HTML</strong>. Only use for trusted code like YouTube/Google-Maps iframes.</td></tr>
    <tr><td><code>data-reveal</code></td><td>Add to any element to fade it in on scroll. Variants: <code>data-reveal="left"</code>, <code>"right"</code>, <code>"zoom"</code>, <code>"down"</code>.</td></tr>
    <tr><td><code>data-reveal-delay="1"</code></td><td>Stagger the animation (1–6). Great for cards.</td></tr>
  </tbody>
</table>

<h3>5. Ready-to-use CSS classes</h3>
<p>Your template can reuse the whole site design system — no need to write CSS:</p>
<table>
  <thead><tr><th>Class</th><th>What it gives you</th></tr></thead>
  <tbody>
    <tr><td><code>sec-head</code> + <code>sec-title</code> + <code>sec-sub</code></td><td>Centered section heading block.</td></tr>
    <tr><td><code>eyebrow</code></td><td>Small gold label above a heading.</td></tr>
    <tr><td><code>btn btn-gold</code></td><td>Gold button. Also <code>btn-navy</code>, <code>btn-ghost</code>, <code>btn-outline</code>.</td></tr>
    <tr><td><code>fac-grid</code> + <code>fac</code></td><td>Responsive card grid (4→2→1 cols).</td></tr>
    <tr><td><code>prog-grid</code> + <code>prog</code></td><td>3-column program/stage cards.</td></tr>
    <tr><td><code>steps</code> + <code>step</code></td><td>Numbered step boxes.</td></tr>
    <tr><td><code>map-embed</code></td><td>Rounded, responsive wrapper for an iframe.</td></tr>
    <tr><td><code>fullbleed</code></td><td>Break out to full screen width (for banners).</td></tr>
    <tr><td><code>hide-mobile</code> / <code>hide-desktop</code></td><td>Hide an element on phones / on desktops.</td></tr>
  </tbody>
</table>

<h3>6. Examples (copy &amp; paste)</h3>
<p><strong>Important:</strong> write the <em>inner</em> content only. The Page Builder already wraps every widget in a section + centered container, and you pick the background (white / grey / navy / flush) from the <strong>Section dropdown</strong> in the builder.</p>

<span class="tag">Example A · Call-to-action box</span>
<p>Fields: <code>heading</code> (text), <code>text</code> (textarea), <code>button_label</code> (text), <code>button_url</code> (url). Put it in a <em>Navy</em> section for a banner look.</p>
<pre>&lt;div data-reveal style="text-align:center"&gt;
  &lt;h2&gt;{{ heading }}&lt;/h2&gt;
  &lt;p style="max-width:60ch;margin:0 auto 24px"&gt;{{ text }}&lt;/p&gt;
  &lt;a class="btn btn-gold" href="{{ button_url }}"&gt;{{ button_label }}&lt;/a&gt;
&lt;/div&gt;</pre>

<span class="tag">Example B · Image + text (two columns)</span>
<p>Fields: <code>image</code> (image), <code>heading</code> (text), <code>body</code> (textarea).</p>
<pre>&lt;div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:center"&gt;
  &lt;img src="{{ image }}" alt="" data-reveal style="border-radius:14px"&gt;
  &lt;div data-reveal&gt;
    &lt;h2&gt;{{ heading }}&lt;/h2&gt;
    &lt;p&gt;{{ body }}&lt;/p&gt;
  &lt;/div&gt;
&lt;/div&gt;</pre>

<span class="tag">Example C · Feature cards (staggered animation)</span>
<p>Fields: <code>heading</code>, <code>title1</code>, <code>text1</code>, <code>title2</code>, <code>text2</code>, <code>title3</code>, <code>text3</code>.</p>
<pre>&lt;div class="sec-head" data-reveal&gt;&lt;h2 class="sec-title"&gt;{{ heading }}&lt;/h2&gt;&lt;/div&gt;
&lt;div class="fac-grid"&gt;
  &lt;div class="fac" data-reveal&gt;&lt;h4&gt;{{ title1 }}&lt;/h4&gt;&lt;p&gt;{{ text1 }}&lt;/p&gt;&lt;/div&gt;
  &lt;div class="fac" data-reveal data-reveal-delay="1"&gt;&lt;h4&gt;{{ title2 }}&lt;/h4&gt;&lt;p&gt;{{ text2 }}&lt;/p&gt;&lt;/div&gt;
  &lt;div class="fac" data-reveal data-reveal-delay="2"&gt;&lt;h4&gt;{{ title3 }}&lt;/h4&gt;&lt;p&gt;{{ text3 }}&lt;/p&gt;&lt;/div&gt;
&lt;/div&gt;</pre>

<span class="tag">Example D · YouTube / Google-Maps embed</span>
<p>Fields: <code>heading</code> (text), <code>embed</code> (textarea — paste the full &lt;iframe&gt; code). Note the <strong>triple braces</strong> for raw HTML.</p>
<pre>&lt;div class="sec-head" data-reveal&gt;&lt;h2 class="sec-title"&gt;{{ heading }}&lt;/h2&gt;&lt;/div&gt;
&lt;div class="map-embed"&gt;{{{ embed }}}&lt;/div&gt;</pre>

<span class="tag">Example E · FAQ accordion (no JavaScript)</span>
<p>Fields: <code>heading</code>, <code>q1</code>, <code>a1</code>, <code>q2</code>, <code>a2</code>, <code>q3</code>, <code>a3</code>. Uses the native &lt;details&gt; tag — click a question to expand.</p>
<pre>&lt;div class="sec-head" data-reveal&gt;&lt;h2 class="sec-title"&gt;{{ heading }}&lt;/h2&gt;&lt;/div&gt;
&lt;div style="max-width:760px;margin:0 auto"&gt;
  &lt;details style="background:#fff;border:1px solid #e6e9f0;border-radius:10px;padding:14px 18px;margin-bottom:10px"&gt;
    &lt;summary style="font-weight:600;cursor:pointer"&gt;{{ q1 }}&lt;/summary&gt;
    &lt;p style="margin:10px 0 0"&gt;{{ a1 }}&lt;/p&gt;
  &lt;/details&gt;
  &lt;details style="background:#fff;border:1px solid #e6e9f0;border-radius:10px;padding:14px 18px;margin-bottom:10px"&gt;
    &lt;summary style="font-weight:600;cursor:pointer"&gt;{{ q2 }}&lt;/summary&gt;
    &lt;p style="margin:10px 0 0"&gt;{{ a2 }}&lt;/p&gt;
  &lt;/details&gt;
  &lt;details style="background:#fff;border:1px solid #e6e9f0;border-radius:10px;padding:14px 18px"&gt;
    &lt;summary style="font-weight:600;cursor:pointer"&gt;{{ q3 }}&lt;/summary&gt;
    &lt;p style="margin:10px 0 0"&gt;{{ a3 }}&lt;/p&gt;
  &lt;/details&gt;
&lt;/div&gt;</pre>

<h3>7. Advanced tips</h3>
<ul>
  <li><strong>Full-width banner:</strong> wrap your template in <code>&lt;div class="fullbleed"&gt;...&lt;/div&gt;</code> and set the section to <em>Flush (no padding)</em> in the Page Builder.</li>
  <li><strong>Backgrounds:</strong> don't hard-code section background — choose white / grey / navy / flush from the Section dropdown. On a navy section, headings turn white automatically.</li>
  <li><strong>Inline styles are fine</strong> for one-off spacing/colors. For anything reused, prefer the ready-made classes above.</li>
  <li><strong>Animations:</strong> add <code>data-reveal</code> to each block; use <code>data-reveal-delay="1..6"</code> to stagger cards.</li>
  <li><strong>Raw HTML safety:</strong> use <code>{{ key }}</code> (double) for any text a user types; only use <code>{{{ key }}}</code> (triple) for embed code you trust.</li>
</ul>

<h3>8. Good to know (current limits)</h3>
<ul>
  <li>Each field holds <strong>one value</strong>. For repeating lists (like 6 testimonials or 8 facilities), either use fixed numbered fields (<code>q1, q2, q3…</code>) or use the built-in repeating widgets (Facilities, Testimonials, Glimpses, etc.).</li>
  <li>Placeholders are simple keys — no logic/if/loops inside the template (yet).</li>
  <li>After saving, changes go live immediately. If a page doesn't update, open it in the Page Builder and re-save, or run <code>php artisan optimize:clear</code>.</li>
</ul>

</div>
@endverbatim
</div>{{-- /tab-help --}}

@endsection

@push('scripts')
<script>
(function () {
    var rows = document.getElementById('fieldRows');
    var TYPES = ['text', 'textarea', 'color', 'image', 'url', 'number'];
    var existing = @json(old('fields', $widget->fields ?? []));

    function esc(v) { return String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;'); }

    function addRow(f) {
        f = f || { key: '', label: '', type: 'text', default: '' };
        var i = rows.children.length;
        var div = document.createElement('div');
        div.className = 'frow';
        div.innerHTML =
            '<input placeholder="key" name="fields[' + i + '][key]" value="' + esc(f.key) + '">' +
            '<input placeholder="Label" name="fields[' + i + '][label]" value="' + esc(f.label) + '">' +
            '<select name="fields[' + i + '][type]">' + TYPES.map(function (t) { return '<option ' + (f.type === t ? 'selected' : '') + '>' + t + '</option>'; }).join('') + '</select>' +
            '<input placeholder="default value" name="fields[' + i + '][default]" value="' + esc(f.default) + '">' +
            '<button type="button" class="btn-sm" style="color:var(--danger)">✕</button>';
        div.querySelector('button').addEventListener('click', function () { div.remove(); });
        rows.appendChild(div);
    }

    (Array.isArray(existing) ? existing : []).forEach(addRow);
    if (!rows.children.length) addRow();
    document.getElementById('addField').addEventListener('click', function () { addRow(); });
})();
</script>
<script>
(function () {
    var tabs = document.querySelectorAll('.wb-tab');
    var panels = { builder: document.getElementById('tab-builder'), help: document.getElementById('tab-help') };
    tabs.forEach(function (t) {
        t.addEventListener('click', function () {
            tabs.forEach(function (x) { x.classList.remove('active'); });
            t.classList.add('active');
            Object.keys(panels).forEach(function (k) {
                if (panels[k]) panels[k].style.display = (k === t.dataset.tab) ? '' : 'none';
            });
        });
    });
})();
</script>
@endpush
