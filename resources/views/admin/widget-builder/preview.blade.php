<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview: {{ $label }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/site.css">
<style>
    /* Preview chrome reset — no header/footer, just the widget */
    body { margin: 0; padding: 0; background: #fff; }
    section, .section { padding: 60px 0; }
    .container { max-width: 1200px; margin-inline: auto; padding-inline: 24px; }

    /* Fullbleed wrapper that some hero/section widgets use */
    .fullbleed { width: 100%; }

    /* Minimal reveal fallback (no JS) */
    [data-reveal] { opacity: 1 !important; transform: none !important; }

    /* Dynamic widget note */
    .ws-dyn-note {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 10px;
        padding: 10px 16px;
        font-family: sans-serif;
        font-size: 13px;
        color: #92400e;
        margin: 16px 24px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>
</head>
<body>

@if ($isDynamic)
<div class="ws-dyn-note">
    ⚡ <strong>Live widget</strong> — This widget queries real data (gallery, testimonials, news, etc.).
    Preview shows default/placeholder output. Actual output varies based on your database content.
</div>
@endif

<div class="page-builder">
    {!! $html !!}
</div>

<script src="/site.js"></script>
<script>
// Trigger scroll reveals immediately in preview mode
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-reveal]').forEach(function(el) {
        el.style.opacity = '1';
        el.style.transform = 'none';
    });
    // Trigger counter animations if present
    document.querySelectorAll('[data-count]').forEach(function(el) {
        var target = parseFloat(el.dataset.count || 0);
        var suffix = el.dataset.suffix || '';
        el.textContent = (Number.isInteger(target) ? target : target.toFixed(1)) + suffix;
    });
});
</script>
</body>
</html>
