@php
$settings = $popup->settings ?? [];
$design = $popup->design ?? [];
$animation = $settings['animation'] ?? 'fade';
$position = $settings['position'] ?? 'center-center';
$width = $settings['width'] ?? 700;
$bgColor = $design['background'] ?? '#ffffff';
$borderRadius = ($design['borderRadius'] ?? 12) . 'px';
$overlayOpacity = $settings['overlay_opacity'] ?? 0.5;
$zIndex = $settings['z_index'] ?? 999999;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Popup Preview</title>
<link rel="stylesheet" href="{{ asset('css/popup-builder/popup-runtime.css') }}">
<style>
body{margin:0;min-height:400px;display:flex;align-items:center;justify-content:center;background:#f1f5f9;font-family:system-ui,sans-serif}
.popup-preview-wrapper{width:100%;min-height:400px;position:relative}
.popup-overlay-preview{position:absolute;inset:0;background:rgba(0,0,0,{{ $overlayOpacity }});z-index:1;border-radius:8px}
.popup-content-preview{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:{{ $bgColor }};border-radius:{{ $borderRadius }};box-shadow:0 25px 50px -12px rgba(0,0,0,.25);max-width:{{ $width }}px;width:90%;z-index:2;overflow:hidden;max-height:80vh;overflow-y:auto;animation:popup-fade-in .3s ease-out}
.popup-body-preview{padding:32px}
.preview-close{position:absolute;top:12px;right:12px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:none;background:rgba(0,0,0,.05);border-radius:50%;cursor:pointer;z-index:10;font-size:18px;color:#666}
.popup-preview-heading{font-size:24px;font-weight:700;margin:0 0 12px;color:#1e293b}
.popup-preview-text{font-size:15px;line-height:1.6;color:#475569;margin:0 0 20px}
.popup-preview-btn{display:inline-block;padding:12px 28px;background:#6366f1;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:all .2s}
.popup-preview-btn:hover{background:#4f46e5;transform:translateY(-1px)}
</style>
</head>
<body>
<div class="popup-preview-wrapper">
    <div class="popup-overlay-preview"></div>
    <div class="popup-content-preview">
        <button class="preview-close" disabled aria-label="Close">&times;</button>
        <div class="popup-body-preview">
            @php $blocks = $popup->structure['blocks'] ?? []; @endphp
            @if(count($blocks) > 0)
                @foreach($blocks as $block)
                    @php $type = $block['type'] ?? 'text'; @endphp
                    @if($type === 'text')
                        @if(!empty($block['heading']))<{{ $block['heading_size'] ?? 'h2' }} style="font-size:{{ (($block['heading_size'] ?? 'h2') === 'h2') ? '24px' : (($block['heading_size'] ?? '') === 'h3' ? '20px' : '18px') }};font-weight:700;margin:0 0 8px;color:#1e293b;text-align:{{ $block['align'] ?? 'left' }}">{{ e($block['heading']) }}</{{ $block['heading_size'] ?? 'h2' }}>@endif
                        @if(!empty($block['content']))<div style="font-size:15px;line-height:1.7;color:#475569;text-align:{{ $block['align'] ?? 'left' }}">{{ nl2br(e($block['content'])) }}</div>@endif
                    @elseif($type === 'image' && !empty($block['src']))
                        <div style="text-align:{{ $block['align'] ?? 'center' }};margin-bottom:16px">
                            <img src="{{ e($block['src']) }}" alt="{{ e($block['alt'] ?? '') }}" style="width:{{ $block['width'] ?? 'auto' }};max-width:{{ $block['max_width'] ?? '100%' }};border-radius:{{ ($block['border_radius'] ?? 8) }}px;height:auto" loading="lazy">
                        </div>
                    @elseif($type === 'button')
                        <div style="text-align:center;margin-bottom:16px">
                            <a href="#" class="popup-preview-btn" style="background:{{ $block['bg_color'] ?? '#6366f1' }};color:{{ $block['text_color'] ?? '#fff' }};{{ ($block['full_width'] ?? '0') === '1' ? 'display:block' : '' }}">{{ e($block['text'] ?? 'Button') }}</a>
                        </div>
                    @elseif($type === 'html' && !empty($block['html']))
                        <div style="margin-bottom:16px">{!! $block['html'] !!}</div>
                    @elseif($type === 'divider')
                        <hr style="border:none;border-top:{{ ($block['thickness'] ?? 1) }}px solid {{ $block['color'] ?? '#e5e7eb' }};margin:{{ ($block['margin'] ?? 16) }}px 0">
                    @endif
                @endforeach
            @else
                <h2 class="popup-preview-heading">Sample Popup Heading</h2>
                <p class="popup-preview-text">This is a sample popup content area. Customize the content in the popup editor to see your own content here. You can add text, images, buttons, and more.</p>
                <button class="popup-preview-btn">Get Started</button>
            @endif
        </div>
    </div>
</div>
</body>
</html>
