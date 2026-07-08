<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editing: {{ $page->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font: 14px/1.5 system-ui, sans-serif; color: #1f2937; background: #f3f4f6; }
        .bar { position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 8px;
               padding: 10px 16px; background: #111827; color: #fff; }
        .bar h1 { font-size: 15px; margin: 0 12px 0 0; font-weight: 600; }
        .bar .spacer { flex: 1; }
        .status { font-size: 13px; opacity: .85; min-width: 120px; }
        .status.ok { color: #6ee7b7; }
        .status.err { color: #fca5a5; }
        .btn { border: 1px solid #d1d5db; background: #fff; color: #1f2937; border-radius: 6px;
               padding: 6px 10px; cursor: pointer; font-size: 13px; }
        .btn:hover { background: #f9fafb; }
        .btn.sm { padding: 2px 7px; font-size: 12px; }
        .btn.danger { color: #b91c1c; border-color: #fecaca; }
        .btn.primary { background: #2563eb; color: #fff; border-color: #2563eb; }
        .bar .btn { background: #1f2937; color: #fff; border-color: #374151; }
        .bar .btn.primary { background: #2563eb; border-color: #2563eb; }
        .wrap { max-width: 1000px; margin: 20px auto; padding: 0 16px; }
        .sec { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; margin-bottom: 16px; }
        .sec__head, .row__head, .col__head, .wg__head { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
        .row { border: 1px dashed #d1d5db; border-radius: 8px; padding: 10px; margin-top: 10px; }
        .cols { display: flex; gap: 10px; margin-top: 8px; flex-wrap: wrap; }
        .col { flex: 1 1 160px; min-width: 160px; background: #f9fafb; border: 1px solid #e5e7eb;
               border-radius: 8px; padding: 8px; }
        .wg { background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 8px; margin: 6px 0; }
        .wg__type { font-weight: 600; flex: 1; }
        .wg__handle { cursor: grab; color: #9ca3af; user-select: none; margin-right: 4px; font-size: 13px; }
        .wg.dragging { opacity: .45; }
        .col.drag-over { outline: 2px dashed #2563eb; outline-offset: 2px; }
        .tag { font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; font-weight: 600; flex: 1; }
        .width { font-size: 12px; padding: 2px; }
        .adder select { width: 100%; margin-top: 6px; padding: 5px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; }
        .settings { margin-top: 8px; padding-top: 8px; border-top: 1px solid #f3f4f6; }
        .field { display: block; margin-bottom: 6px; }
        .field__label { display: block; font-size: 11px; color: #6b7280; margin-bottom: 2px; }
        .field__input { width: 100%; padding: 5px 7px; border: 1px solid #d1d5db; border-radius: 6px; font: inherit; }
        textarea.field__input { font-family: ui-monospace, monospace; font-size: 12px; }
        .field__input.bad { border-color: #ef4444; background: #fef2f2; }
        .muted { color: #9ca3af; }
        .empty { text-align: center; color: #9ca3af; padding: 40px; background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; }
        .arr { display: flex; flex-direction: column; gap: 6px; margin-top: 4px; }
        .arr-row { border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px; background: #fafafa; }
        .arr-row__head { display: flex; align-items: center; gap: 4px; margin-bottom: 4px; }
        .arr-row__body { display: flex; flex-direction: column; gap: 4px; }
        .subobj { padding-left: 8px; border-left: 2px solid #eef1f6; display: flex; flex-direction: column; gap: 4px; }
    </style>
</head>
<body>
    <div class="bar">
        <h1>{{ $page->title }}</h1>
        <button id="addSection" class="btn">+ Section</button>
        <button id="reload" class="btn">Reload</button>
        <button id="livePreview" class="btn">Live Preview</button>
        <span class="spacer"></span>
        <span id="status" class="status"></span>
        <a class="btn" href="{{ $page->slug === 'home' ? url('/') : url('/'.$page->slug) }}" target="_blank" rel="noopener">Preview</a>
        <button id="save" class="btn primary">Save</button>
    </div>
    <div class="wrap"><div id="canvas"></div></div>
    <div class="wrap" id="previewWrap" style="display:none">
        <iframe id="previewFrame" title="Live preview" style="width:100%;height:640px;border:1px solid #e5e7eb;border-radius:10px;background:#fff"></iframe>
    </div>

    <script>
        window.__EDITOR__ = {
            palette: @json($palette),
            treeUrl: @json(url('/admin/pages/'.$page->id.'/tree')),
            previewUrl: @json(url('/admin/pages/'.$page->id.'/preview')),
            csrf: @json(csrf_token()),
        };
    </script>
    @verbatim
    <script>
    (function () {
        const cfg = window.__EDITOR__;
        const state = { sections: [] };
        const canvas = document.getElementById('canvas');
        const statusEl = document.getElementById('status');

        let dragSrc = null;
        function moveWidget(src, dest, destIndex) {
            const from = state.sections[src.si].rows[src.ri].columns[src.ci].widgets;
            const to = state.sections[dest.si].rows[dest.ri].columns[dest.ci].widgets;
            if (!from || !to) return;
            const [w] = from.splice(src.wi, 1);
            let idx = destIndex;
            if (src.si === dest.si && src.ri === dest.ri && src.ci === dest.ci && src.wi < destIndex) idx = destIndex - 1;
            to.splice(Math.max(0, idx), 0, w);
            render();
        }

        const setStatus = (msg, kind) => { statusEl.textContent = msg; statusEl.className = 'status ' + (kind || ''); };
        let _pvTimer = null;
        const schedulePreview = () => { clearTimeout(_pvTimer); _pvTimer = setTimeout(() => updatePreview(), 350); };
        const paletteFor = (type) => cfg.palette.find(p => p.type === type);

        // Widgets seeded with empty settings ({}) would otherwise show "No settings".
        // Fill any missing keys from the palette defaults so every widget becomes
        // fully editable (content + images) straight from the builder.
        function hydrateDefaults(widget) {
            const p = paletteFor(widget.type);
            const defs = (p && p.defaults) ? p.defaults : {};
            widget.settings = widget.settings || {};
            Object.keys(defs).forEach(function (k) {
                if (!(k in widget.settings)) {
                    widget.settings[k] = JSON.parse(JSON.stringify(defs[k]));
                }
            });
        }

        function move(arr, i, dir) {
            const j = i + dir;
            if (j < 0 || j >= arr.length) return;
            [arr[i], arr[j]] = [arr[j], arr[i]];
        }

        function el(tag, cls, text) {
            const e = document.createElement(tag);
            if (cls) e.className = cls;
            if (text != null) e.textContent = text;
            return e;
        }
        function btn(label, cls, onClick) {
            const b = el('button', 'btn ' + (cls || ''), label);
            b.type = 'button';
            b.addEventListener('click', onClick);
            return b;
        }

        function mkCheck(label, checked, onChange) {
            const wrap = el('label');
            wrap.style.cssText = 'display:inline-flex;align-items:center;gap:3px;font-size:11px;color:#6b7280';
            const c = document.createElement('input');
            c.type = 'checkbox'; c.checked = !!checked;
            c.addEventListener('change', () => onChange(c.checked));
            wrap.appendChild(c); wrap.appendChild(document.createTextNode(label));
            return wrap;
        }

        function humanize(k) {
            return String(k).replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        }

        function cloneEmpty(v) {
            if (typeof v === 'boolean') return false;
            if (typeof v === 'number') return 0;
            if (Array.isArray(v)) return [];
            if (v !== null && typeof v === 'object') { const o = {}; Object.keys(v).forEach(k => o[k] = cloneEmpty(v[k])); return o; }
            return '';
        }
        function cloneTemplate(sample) {
            return sample === undefined ? '' : cloneEmpty(sample);
        }

        // Repeater editor for an array value (of primitives or objects).
        function arrayEditor(arr, rerender) {
            const box = el('div', 'arr');
            arr.forEach((item, idx) => {
                const rowEl = el('div', 'arr-row');
                const head = el('div', 'arr-row__head');
                head.appendChild(el('span', 'tag', '#' + (idx + 1)));
                head.appendChild(btn('\u2191', 'sm', () => { if (idx > 0) { [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]]; rerender(); } }));
                head.appendChild(btn('\u2193', 'sm', () => { if (idx < arr.length - 1) { [arr[idx + 1], arr[idx]] = [arr[idx], arr[idx + 1]]; rerender(); } }));
                head.appendChild(btn('\u2715', 'sm danger', () => { arr.splice(idx, 1); rerender(); }));
                rowEl.appendChild(head);
                const body = el('div', 'arr-row__body');
                if (item !== null && typeof item === 'object' && !Array.isArray(item)) {
                    Object.keys(item).forEach(k => fieldEditor(body, item, k, rerender));
                } else if (Array.isArray(item)) {
                    body.appendChild(arrayEditor(item, rerender));
                } else {
                    fieldEditor(body, arr, idx, rerender);
                }
                rowEl.appendChild(body);
                box.appendChild(rowEl);
            });
            box.appendChild(btn('+ Add item', 'sm', () => { arr.push(cloneTemplate(arr[0])); rerender(); }));
            return box;
        }

        // Recursive single-field editor; mutates obj[key] in place.
        function fieldEditor(wrap, obj, key, rerender, enums) {
            const val = obj[key];
            const field = el('div', 'field');
            if (typeof key !== 'number') field.appendChild(el('span', 'field__label', humanize(key)));

            if (enums && Array.isArray(enums[key]) && enums[key].length) {
                const sel = document.createElement('select');
                sel.classList.add('field__input');
                enums[key].forEach(function (opt) {
                    const o = el('option', null, opt); o.value = opt;
                    if (String(val) === String(opt)) o.selected = true;
                    sel.appendChild(o);
                });
                sel.addEventListener('change', () => { obj[key] = sel.value; schedulePreview(); });
                field.appendChild(sel);
                wrap.appendChild(field);
                return;
            }

            if (typeof val === 'boolean') {
                const i = document.createElement('input'); i.type = 'checkbox'; i.checked = val;
                i.classList.add('field__input');
                i.addEventListener('change', () => { obj[key] = i.checked; schedulePreview(); });
                field.appendChild(i);
            } else if (typeof val === 'number') {
                const i = document.createElement('input'); i.type = 'number'; i.value = val;
                i.classList.add('field__input');
                i.addEventListener('input', () => { obj[key] = i.value === '' ? null : Number(i.value); schedulePreview(); });
                field.appendChild(i);
            } else if (Array.isArray(val)) {
                field.appendChild(arrayEditor(val, rerender));
            } else if (val !== null && typeof val === 'object') {
                const sub = el('div', 'subobj');
                Object.keys(val).forEach(k => fieldEditor(sub, val, k, rerender));
                field.appendChild(sub);
            } else {
                const long = /body|text|quote|desc|message|html|address|embed/i.test(String(key)) || (typeof val === 'string' && val.length > 60);
                const i = document.createElement(long ? 'textarea' : 'input');
                if (long) i.rows = 3; else i.type = 'text';
                i.value = (val == null ? '' : val);
                i.classList.add('field__input');
                i.addEventListener('input', () => { obj[key] = i.value; schedulePreview(); });
                field.appendChild(i);
            }
            wrap.appendChild(field);
        }

        function settingsForm(widget) {
            const form = el('div', 'settings');
            function draw() {
                form.innerHTML = '';
                const keys = Object.keys(widget.settings || {});
                if (!keys.length) { form.appendChild(el('em', 'muted', 'No settings')); return; }
                const enums = (paletteFor(widget.type) || {}).options || {};
                keys.forEach(k => fieldEditor(form, widget.settings, k, rerender, enums));
            }
            function rerender() { draw(); schedulePreview(); }
            draw();
            return form;
        }

        function render() {
            canvas.innerHTML = '';
            if (!state.sections.length) {
                canvas.appendChild(el('div', 'empty', 'No sections yet — click "+ Section" to start building.'));
            }
            state.sections.forEach((s, si) => {
                const sec = el('div', 'sec');
                const sh = el('div', 'sec__head');
                sh.appendChild(el('span', 'tag', 'Section'));
                const styleSel = document.createElement('select');
                styleSel.className = 'width';
                [['section','Default (white)'],['alt','Soft grey'],['navy','Navy'],['flush','Flush (no padding)'],['hero','Hero (full-bleed)']].forEach(([v, lbl]) => {
                    const o = el('option', null, lbl); o.value = v;
                    if ((s.type || 'section') === v) o.selected = true;
                    styleSel.appendChild(o);
                });
                styleSel.title = 'Section background / style';
                styleSel.addEventListener('change', () => { s.type = styleSel.value; updatePreview(); });
                sh.appendChild(styleSel);

                s.settings = s.settings || {};
                const animSel = document.createElement('select');
                animSel.className = 'width';
                animSel.title = 'Entrance animation';
                [['', 'No animation'], ['fade-up', 'Fade \u2191'], ['fade-down', 'Fade \u2193'], ['fade-left', 'Fade \u2190'], ['fade-right', 'Fade \u2192'], ['zoom', 'Zoom']].forEach(([v, lbl]) => {
                    const o = el('option', null, lbl); o.value = v;
                    if ((s.settings._animation || '') === v) o.selected = true;
                    animSel.appendChild(o);
                });
                animSel.addEventListener('change', () => { s.settings._animation = animSel.value; schedulePreview(); });
                sh.appendChild(animSel);
                sh.appendChild(mkCheck('Hide mobile', s.settings._hide_mobile, v => { s.settings._hide_mobile = v; schedulePreview(); }));
                sh.appendChild(mkCheck('Hide desktop', s.settings._hide_desktop, v => { s.settings._hide_desktop = v; schedulePreview(); }));
                sh.appendChild(mkCheck('Hover FX', !s.settings._no_hover, v => { s.settings._no_hover = !v; schedulePreview(); }));

                sh.appendChild(btn('+ Row', 'sm', () => { (s.rows = s.rows || []).push({ settings: {}, columns: [] }); render(); }));
                sh.appendChild(btn('↑', 'sm', () => { move(state.sections, si, -1); render(); }));
                sh.appendChild(btn('↓', 'sm', () => { move(state.sections, si, 1); render(); }));
                sh.appendChild(btn('Delete', 'sm danger', () => { state.sections.splice(si, 1); render(); }));
                sec.appendChild(sh);

                (s.rows || []).forEach((r, ri) => {
                    const row = el('div', 'row');
                    const rh = el('div', 'row__head');
                    rh.appendChild(el('span', 'tag', 'Row'));
                    rh.appendChild(btn('+ Column', 'sm', () => { (r.columns = r.columns || []).push({ width: 12, settings: {}, widgets: [] }); render(); }));
                    rh.appendChild(btn('↑', 'sm', () => { move(s.rows, ri, -1); render(); }));
                    rh.appendChild(btn('↓', 'sm', () => { move(s.rows, ri, 1); render(); }));
                    rh.appendChild(btn('Delete', 'sm danger', () => { s.rows.splice(ri, 1); render(); }));
                    row.appendChild(rh);

                    const cols = el('div', 'cols');
                    (r.columns || []).forEach((c, ci) => {
                        const col = el('div', 'col');
                        col.addEventListener('dragover', (e) => { if (dragSrc) { e.preventDefault(); col.classList.add('drag-over'); } });
                        col.addEventListener('dragleave', () => col.classList.remove('drag-over'));
                        col.addEventListener('drop', (e) => {
                            if (!dragSrc) return;
                            e.preventDefault();
                            col.classList.remove('drag-over');
                            moveWidget(dragSrc, { si, ri, ci }, (c.widgets || []).length);
                        });
                        const ch = el('div', 'col__head');
                        ch.appendChild(el('span', 'tag', 'Col'));
                        const w = document.createElement('select'); w.className = 'width';
                        for (let i = 1; i <= 12; i++) {
                            const o = el('option', null, String(i)); o.value = i;
                            if (Number(c.width) === i) o.selected = true;
                            w.appendChild(o);
                        }
                        w.addEventListener('change', () => { c.width = Number(w.value); });
                        ch.appendChild(w);
                        ch.appendChild(btn('↑', 'sm', () => { move(r.columns, ci, -1); render(); }));
                        ch.appendChild(btn('↓', 'sm', () => { move(r.columns, ci, 1); render(); }));
                        ch.appendChild(btn('Delete', 'sm danger', () => { r.columns.splice(ci, 1); render(); }));
                        col.appendChild(ch);

                        (c.widgets || []).forEach((wg, wi) => {
                            hydrateDefaults(wg);
                            const box = el('div', 'wg');
                            box.addEventListener('dragover', (e) => { if (dragSrc) e.preventDefault(); });
                            box.addEventListener('drop', (e) => {
                                if (!dragSrc) return;
                                e.preventDefault();
                                e.stopPropagation();
                                moveWidget(dragSrc, { si, ri, ci }, wi);
                            });
                            const wh = el('div', 'wg__head');
                            const handle = el('span', 'wg__handle', '\u283F');
                            handle.draggable = true;
                            handle.title = 'Drag to reorder';
                            handle.addEventListener('dragstart', (e) => {
                                dragSrc = { si, ri, ci, wi };
                                box.classList.add('dragging');
                                if (e.dataTransfer) { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', String(wi)); }
                            });
                            handle.addEventListener('dragend', () => { dragSrc = null; box.classList.remove('dragging'); });
                            wh.appendChild(handle);
                            const p = paletteFor(wg.type);
                            wh.appendChild(el('span', 'wg__type', p ? p.label : wg.type));
                            const form = settingsForm(wg);
                            form.style.display = 'none';
                            wh.appendChild(btn('Settings', 'sm', () => {
                                form.style.display = form.style.display === 'none' ? 'block' : 'none';
                            }));
                            wh.appendChild(btn('↑', 'sm', () => { move(c.widgets, wi, -1); render(); }));
                            wh.appendChild(btn('↓', 'sm', () => { move(c.widgets, wi, 1); render(); }));
                            wh.appendChild(btn('Delete', 'sm danger', () => { c.widgets.splice(wi, 1); render(); }));
                            box.appendChild(wh);
                            box.appendChild(form);
                            col.appendChild(box);
                        });

                        const adder = el('div', 'adder');
                        const sel = document.createElement('select');
                        sel.appendChild(el('option', null, '+ Add widget…'));
                        cfg.palette.forEach(p => {
                            const o = el('option', null, p.label + ' · ' + p.category); o.value = p.type;
                            sel.appendChild(o);
                        });
                        sel.addEventListener('change', () => {
                            if (!sel.value) return;
                            const p = paletteFor(sel.value);
                            const defaults = p ? JSON.parse(JSON.stringify(p.defaults || {})) : {};
                            (c.widgets = c.widgets || []).push({ type: sel.value, settings: defaults });
                            render();
                        });
                        adder.appendChild(sel);
                        col.appendChild(adder);

                        cols.appendChild(col);
                    });
                    row.appendChild(cols);
                    sec.appendChild(row);
                });
                canvas.appendChild(sec);
            });

            updatePreview();
        }

        async function load() {
            setStatus('Loading…');
            try {
                const res = await fetch(cfg.treeUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                state.sections = Array.isArray(data.sections) ? data.sections : [];
                render();
                setStatus('Loaded', 'ok');
            } catch (e) {
                setStatus('Failed to load: ' + e.message, 'err');
            }
        }

        async function save() {
            setStatus('Saving…');
            try {
                const res = await fetch(cfg.treeUrl, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                    body: JSON.stringify({ sections: state.sections }),
                });
                if (!res.ok) {
                    let msg = 'HTTP ' + res.status;
                    try { const j = await res.json(); if (j.message) msg = j.message; } catch (e) {}
                    throw new Error(msg);
                }
                setStatus('Saved ✓', 'ok');
            } catch (e) {
                setStatus('Save failed: ' + e.message, 'err');
            }
        }

        let previewOn = false;

        async function updatePreview() {
            if (!previewOn) return;
            try {
                const res = await fetch(cfg.previewUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
                    body: JSON.stringify({ sections: state.sections }),
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const data = await res.json();
                const cssHref = location.origin + '/site.css?v=' + Date.now();
                const doc = '<!doctype html><html><head><meta charset="utf-8">'
                    + '<meta name="viewport" content="width=device-width, initial-scale=1">'
                    + '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">'
                    + '<link rel="stylesheet" href="' + cssHref + '">'
                    + '<style>body{margin:0;background:#fff}[data-reveal]{opacity:1!important;transform:none!important}</style></head><body>'
                    + (data.html || '<p style="color:#9ca3af;padding:2rem">Nothing to preview yet.</p>')
                    + '</body></html>';
                document.getElementById('previewFrame').srcdoc = doc;
            } catch (e) {
                setStatus('Preview failed: ' + e.message, 'err');
            }
        }

        function togglePreview() {
            previewOn = !previewOn;
            document.getElementById('previewWrap').style.display = previewOn ? 'block' : 'none';
            document.getElementById('livePreview').textContent = previewOn ? 'Hide Preview' : 'Live Preview';
            updatePreview();
        }

        document.getElementById('livePreview').addEventListener('click', togglePreview);
        document.getElementById('addSection').addEventListener('click', () => { state.sections.push({ type: 'section', settings: {}, rows: [] }); render(); });
        document.getElementById('save').addEventListener('click', save);
        document.getElementById('reload').addEventListener('click', load);
        load();
    })();
    </script>
    @endverbatim
</body>
</html>
