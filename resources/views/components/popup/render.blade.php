<div class="popup-builder-output" style="display:none;">
    {!! $css !!}
    <div class="popup-overlay" data-popup-id="{{ $popup->id }}" style="display:none;"></div>
    <div class="popup" data-popup-id="{{ $popup->id }}"
         data-type="{{ $popup->type }}"
         data-animation="{{ $settings['animation'] ?? 'fade' }}"
         data-position="{{ $settings['position'] ?? 'center-center' }}"
         data-width="{{ $settings['width'] ?? 600 }}"
         data-delay="{{ $settings['delay'] ?? 0 }}"
         data-frequency="{{ $popup->frequency_type }}"
         role="dialog"
         aria-modal="true"
         style="display:none; {{ $popup->type === 'floating_bar' ? 'position:fixed;bottom:0;left:0;right:0;z-index:' . ($settings['z_index'] ?? 999999) . ';' : '' }}">
        <div class="popup-content" style="
            background: {{ $design['background'] ?? '#ffffff' }};
            border-radius: {{ $design['borderRadius'] ?? '12' }}px;
            box-shadow: {{ $design['boxShadow'] ?? '0 25px 50px -12px rgba(0,0,0,0.25)' }};
            max-width: {{ $settings['width'] ?? 600 }}px;
            width: 90%;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            {{ ($design['backdropBlur'] ?? false) ? 'backdrop-filter:blur(' . $design['backdropBlur'] . 'px);' : '' }}
        ">
            @if($settings['close_button'] ?? true)
                <button class="popup-close" data-popup-id="{{ $popup->id }}"
                    style="position:absolute;top:12px;right:12px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border:none;background:rgba(0,0,0,0.05);border-radius:50%;cursor:pointer;z-index:10;font-size:18px;color:#666;transition:all 0.2s;"
                    aria-label="Close popup">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            @endif
            <div class="popup-body">
                {!! $html !!}
            </div>
        </div>
    </div>
    @if($popup->custom_js)
        <script>{!! $popup->custom_js !!}</script>
    @endif
    <script>{!! $js !!}</script>
</div>
