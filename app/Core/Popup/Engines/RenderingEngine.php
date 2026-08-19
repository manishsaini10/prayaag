<?php

namespace App\Core\Popup\Engines;

use App\Models\Popup\Popup;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class RenderingEngine
{
    private array $widgetRenderers = [];

    public function __construct()
    {
        $this->registerDefaultWidgets();
    }

    public function render(Popup $popup): string
    {
        $structure = $popup->structure;
        $settings = $popup->settings ?? [];
        $design = $popup->design ?? [];
        $styles = $popup->styles ?? [];
        $customCss = $popup->custom_css ?? '';

        if (isset($structure['blocks']) && is_array($structure['blocks'])) {
            $html = $this->renderBlocks($structure['blocks']);
        } else {
            $html = $this->renderContainer($structure, $structure['rows'] ?? []);
        }
        $css = $this->buildStyles($popup, $design, $styles, $customCss);
        $js = $this->buildJavaScript($popup);

        return view('components.popup.render', compact(
            'popup', 'html', 'css', 'js', 'settings', 'design'
        ))->render();
    }

    public function renderWidget(string $type, array $widget): string
    {
        $renderer = $this->widgetRenderers[$type] ?? null;
        if ($renderer) {
            return $renderer($widget);
        }
        return $this->renderDefaultWidget($type, $widget);
    }

    public function registerWidget(string $type, callable $renderer): void
    {
        $this->widgetRenderers[$type] = $renderer;
    }

    private function renderContainer(array $container, array $rows): string
    {
        $styles = $this->parseStyles($container['styles'] ?? []);
        $rowsHtml = '';
        foreach ($rows as $row) {
            $rowsHtml .= $this->renderRow($row);
        }
        return "<div class=\"popup-container\" style=\"{$styles}\">{$rowsHtml}</div>";
    }

    private function renderRow(array $row): string
    {
        $columns = $row['columns'] ?? [];
        $colsHtml = '';
        foreach ($columns as $col) {
            $colsHtml .= $this->renderColumn($col);
        }
        return "<div class=\"popup-row\">{$colsHtml}</div>";
    }

    private function renderColumn(array $column): string
    {
        $width = $column['width'] ?? 12;
        $widgets = $column['widgets'] ?? [];
        $widgetsHtml = '';
        foreach ($widgets as $widget) {
            $widgetsHtml .= $this->renderWidget($widget['type'] ?? 'paragraph', $widget);
        }
        $colWidth = round(($width / 12) * 100, 2);
        return "<div class=\"popup-col\" style=\"width:{$colWidth}%;display:inline-block;vertical-align:top;\">{$widgetsHtml}</div>";
    }

    private function renderDefaultWidget(string $type, array $widget): string
    {
        $settings = $widget['settings'] ?? [];
        $content = $widget['content'] ?? '';
        $styles = $this->parseStyles($settings);

        return match ($type) {
            'heading' => "<{$this->getTag($settings)} class=\"popup-widget popup-heading\" style=\"{$styles}\">" . e($content) . "</{$this->getTag($settings)}>",
            'paragraph' => "<p class=\"popup-widget popup-paragraph\" style=\"{$styles}\">" . e($content) . "</p>",
            'button' => $this->renderButton($widget),
            'image' => $this->renderImage($widget),
            'spacer' => "<div class=\"popup-widget popup-spacer\" style=\"height:{$this->getSetting($settings, 'height', '20')}px\"></div>",
            'divider' => "<hr class=\"popup-widget popup-divider\" style=\"{$styles}\">",
            'icon' => $this->renderIcon($widget),
            'countdown' => $this->renderCountdown($widget),
            'video' => $this->renderVideo($widget),
            'newsletter_form' => $this->renderNewsletterForm($settings),
            'registration_form' => $this->renderRegistrationForm($settings),
            'contact_form' => $this->renderContactForm($settings),
            'html' => "<div class=\"popup-widget popup-html\" style=\"{$styles}\">{$content}</div>",
            'text' => "<div class=\"popup-widget popup-text\" style=\"{$styles}\">" . nl2br(e($content)) . "</div>",
            default => "<div class=\"popup-widget popup-{$type}\" style=\"{$styles}\">" . e($content) . "</div>",
        };
    }

    private function renderButton(array $widget): string
    {
        $settings = $widget['settings'] ?? [];
        $content = e($widget['content'] ?? 'Button');
        $url = $this->safeUrl($settings['url'] ?? '#');
        $align = $settings['align'] ?? 'left';
        $styles = $this->parseStyles($settings);

        return "<div class=\"popup-button-wrapper\" style=\"text-align:{$align}\">
            <a href=\"{$url}\" class=\"popup-widget popup-button\" style=\"{$styles}display:inline-block;text-decoration:none;\">{$content}</a>
        </div>";
    }

    private function renderImage(array $widget): string
    {
        $settings = $widget['settings'] ?? [];
        $src = $this->safeUrl($settings['src'] ?? '');
        $alt = e($settings['alt'] ?? '');
        $align = $settings['align'] ?? 'center';
        $valign = $settings['valign'] ?? 'middle';
        
        $width = $settings['width'] ?? 'auto';
        $widthStyle = is_numeric($width) ? $width . 'px' : $width;
        
        $maxWidth = $settings['max_width'] ?? '100%';
        $maxWidthStyle = is_numeric($maxWidth) ? $maxWidth . 'px' : $maxWidth;
        
        $fit = $settings['fit'] ?? 'contain';
        $radius = ($settings['border_radius'] ?? 8) . 'px';
        
        if (! $src) return '';
        
        $srcsetAttr = $this->getImageSrcset($src);
        
        $imgStyles = [
            "width:{$widthStyle}",
            "max-width:{$maxWidthStyle}",
            "height:auto",
            "border-radius:{$radius}",
            "display:inline-block",
        ];
        
        if ($fit !== 'none') {
            $imgStyles[] = "object-fit:{$fit}";
        }
        
        $wrapperStyles = [
            "display:flex",
            "justify-content:" . match($align) {
                'left' => 'flex-start',
                'right' => 'flex-end',
                default => 'center',
            },
            "align-items:" . match($valign) {
                'top' => 'flex-start',
                'bottom' => 'flex-end',
                default => 'center',
            },
            "width:100%"
        ];
        
        $imgStyleStr = implode(';', $imgStyles);
        $wrapperStyleStr = implode(';', $wrapperStyles);
        
        $linkUrl = isset($settings['link_url']) ? $this->safeUrl($settings['link_url']) : '';
        $imgHtml = "<img src=\"{$src}\"{$srcsetAttr} alt=\"{$alt}\" class=\"popup-widget popup-image popup-image-ultra\" style=\"{$imgStyleStr}\" loading=\"lazy\" decoding=\"async\" onload=\"this.classList.add('is-loaded')\">";
        
        if ($linkUrl) {
            $imgHtml = "<a href=\"{$linkUrl}\" target=\"_blank\" style=\"display:inline-block;max-width:100%;text-align:inherit;\">{$imgHtml}</a>";
        }
        
        return "<div class=\"popup-image-wrapper popup-image-container\" style=\"{$wrapperStyleStr}\">"
            . $imgHtml
            . "</div>";
    }

    private function renderIcon(array $widget): string
    {
        $settings = $widget['settings'] ?? [];
        $icon = $settings['icon'] ?? 'star';
        $color = $settings['color'] ?? '#6366f1';
        $size = $settings['size'] ?? '24';
        $align = $settings['align'] ?? 'left';

        $svgIcons = [
            'bell' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>',
            'sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>',
            'star' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="' . $color . '" stroke="' . $color . '" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
            'mail' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
            'clock' => '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
        ];

        $svg = $svgIcons[$icon] ?? $svgIcons['star'];
        return "<div class=\"popup-icon-wrapper\" style=\"text-align:{$align}\">{$svg}</div>";
    }

    private function renderCountdown(array $widget): string
    {
        $settings = $widget['settings'] ?? [];
        $targetDate = $settings['targetDate'] ?? '';
        $label = $settings['label'] ?? '';

        return "<div class=\"popup-widget popup-countdown\" data-target=\"{$targetDate}\">
            " . ($label ? "<div class=\"countdown-label\">{$label}</div>" : '') . "
            <div class=\"countdown-timer\">
                <span class=\"countdown-days\">00</span>d
                <span class=\"countdown-hours\">00</span>h
                <span class=\"countdown-minutes\">00</span>m
                <span class=\"countdown-seconds\">00</span>s
            </div>
        </div>";
    }

    private function renderVideo(array $widget): string
    {
        $settings = $widget['settings'] ?? [];
        $src = $settings['src'] ?? '';
        $type = $settings['videoType'] ?? 'youtube';

        if ($type === 'youtube') {
            $embedSrc = str_replace(['watch?v=', 'youtu.be/'], ['embed/', 'youtube.com/embed/'], $src);
            return "<div class=\"popup-video-wrapper\" style=\"position:relative;padding-bottom:56.25%;height:0;\">
                <iframe src=\"{$embedSrc}\" style=\"position:absolute;top:0;left:0;width:100%;height:100%;\" frameborder=\"0\" allowfullscreen></iframe>
            </div>";
        }
        return "<video class=\"popup-widget popup-video\" controls style=\"width:100%;\"><source src=\"{$src}\"></video>";
    }

    private function renderNewsletterForm(array $settings): string
    {
        $buttonText = $settings['buttonText'] ?? 'Subscribe';
        $placeholder = $settings['placeholder'] ?? 'Enter your email';
        $buttonColor = $settings['buttonColor'] ?? '#6366f1';

        return "<form class=\"popup-newsletter-form\" data-popup-form=\"newsletter\" style=\"display:flex;gap:8px;\">
            <input type=\"email\" name=\"email\" placeholder=\"{$placeholder}\" required
                style=\"flex:1;padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;\">
            <button type=\"submit\" style=\"padding:10px 24px;background:{$buttonColor};color:#fff;border:none;border-radius:6px;font-size:14px;cursor:pointer;\">{$buttonText}</button>
        </form>";
    }

    private function renderRegistrationForm(array $settings): string
    {
        $buttonText = $settings['buttonText'] ?? 'Register';
        $buttonColor = $settings['buttonColor'] ?? '#0284c7';
        $fields = $settings['fields'] ?? 'name,email,phone';

        $fieldHtml = '';
        foreach (explode(',', $fields) as $field) {
            $field = trim($field);
            $type = match ($field) { 'email' => 'email', 'phone' => 'tel', _ => 'text' };
            $fieldHtml .= "<input type=\"{$type}\" name=\"{$field}\" placeholder=\"Your " . ucfirst($field) . "\" required
                style=\"width:100%;padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;margin-bottom:8px;box-sizing:border-box;\">";
        }

        return "<form class=\"popup-registration-form\" data-popup-form=\"registration\">
            {$fieldHtml}
            <button type=\"submit\" style=\"width:100%;padding:12px 24px;background:{$buttonColor};color:#fff;border:none;border-radius:6px;font-size:16px;cursor:pointer;\">{$buttonText}</button>
        </form>";
    }

    private function renderContactForm(array $settings): string
    {
        $buttonText = $settings['buttonText'] ?? 'Send Message';
        return "<form class=\"popup-contact-form\" data-popup-form=\"contact\">
            <input type=\"text\" name=\"name\" placeholder=\"Your Name\" required
                style=\"width:100%;padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;margin-bottom:8px;box-sizing:border-box;\">
            <input type=\"email\" name=\"email\" placeholder=\"Your Email\" required
                style=\"width:100%;padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;margin-bottom:8px;box-sizing:border-box;\">
            <textarea name=\"message\" placeholder=\"Your Message\" rows=\"3\" required
                style=\"width:100%;padding:10px 16px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;margin-bottom:8px;box-sizing:border-box;resize:vertical;\"></textarea>
            <button type=\"submit\" style=\"width:100%;padding:12px 24px;background:#6366f1;color:#fff;border:none;border-radius:6px;font-size:16px;cursor:pointer;\">{$buttonText}</button>
        </form>";
    }

    private function buildStyles(Popup $popup, array $design, array $styles, string $customCss): string
    {
        $css = '';

        if ($overlayColor = $design['overlay_color'] ?? null) {
            $css .= ".popup-overlay[data-popup-id=\"{$popup->id}\"]{background:{$overlayColor};}";
        }

        if ($customCss) {
            $css .= $customCss;
        }

        return $css;
    }

    private function buildJavaScript(Popup $popup): string
    {
        $settings = $popup->settings ?? [];
        $animation = $settings['animation'] ?? 'fade';
        $delay = $settings['delay'] ?? 0;

        return "window.addEventListener('load', function() {
            setTimeout(function() {
                window.dispatchEvent(new CustomEvent('popup:show', { detail: { id: '{$popup->id}' } }));
            }, {$delay});
        });";
    }

    private function parseStyles(array $styles): string
    {
        $map = [
            'fontSize' => 'font-size', 'fontWeight' => 'font-weight', 'color' => 'color',
            'backgroundColor' => 'background-color', 'background' => 'background',
            'padding' => 'padding', 'margin' => 'margin', 'borderRadius' => 'border-radius',
            'border' => 'border', 'textAlign' => 'text-align', 'align' => 'text-align',
            'width' => 'width', 'height' => 'height', 'opacity' => 'opacity',
            'boxShadow' => 'box-shadow', 'maxWidth' => 'max-width', 'minWidth' => 'min-width',
            'lineHeight' => 'line-height', 'letterSpacing' => 'letter-spacing',
        ];

        $result = '';
        foreach ($styles as $key => $value) {
            if ($value === '' || $value === null) continue;
            $prop = $map[$key] ?? Str::kebab($key);
            $result .= "{$prop}:{$value};";
        }
        return $result;
    }

    private function getTag(array $settings): string
    {
        return $settings['tag'] ?? 'h2';
    }

    private function getSetting(array $settings, string $key, mixed $default = null): mixed
    {
        return $settings[$key] ?? $default;
    }

    private function safeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') return '#';
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme && ! in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel', 'ftp'], true)) {
            return '#';
        }
        return e($url);
    }

    private function renderBlocks(array $blocks): string
    {
        $html = '';
        foreach ($blocks as $block) {
            $type = $block['type'] ?? 'text';
            $html .= $this->renderSingleBlock($type, $block);
        }
        return $html;
    }

    private function renderSingleBlock(string $type, array $block): string
    {
        return match ($type) {
            'text' => $this->renderTextBlock($block),
            'image' => $this->renderImageBlock($block),
            'button' => $this->renderButtonBlock($block),
            'html' => '<div class="popup-widget popup-html">' . ($block['html'] ?? '') . '</div>',
            'divider' => $this->renderDividerBlock($block),
            'video' => $this->renderSingleVideo($block),
            default => '<div>' . ($block['content'] ?? '') . '</div>',
        };
    }

    private function renderTextBlock(array $block): string
    {
        $align = $block['align'] ?? 'left';
        $html = '';
        if (! empty($block['heading'])) {
            $tag = $block['heading_size'] ?? 'h2';
            $size = match ($tag) { 'h2' => '24px', 'h3' => '20px', 'h4' => '18px', default => '20px' };
            $html .= "<{$tag} style=\"font-size:{$size};font-weight:700;margin:0 0 8px 0;color:#1e293b;text-align:{$align}\">" . e($block['heading']) . "</{$tag}>";
        }
        if (! empty($block['content'])) {
            $html .= '<div style="font-size:15px;line-height:1.7;color:#475569;text-align:' . $align . '">' . nl2br(e($block['content'])) . '</div>';
        }
        return $html;
    }

    private function renderImageBlock(array $block): string
    {
        $align = $block['align'] ?? 'center';
        $valign = $block['valign'] ?? 'middle';
        $src = $this->safeUrl($block['src'] ?? '');
        $alt = e($block['alt'] ?? '');
        
        $width = $block['width'] ?? 'auto';
        $widthStyle = is_numeric($width) ? $width . 'px' : $width;
        
        $maxWidth = $block['max_width'] ?? '100%';
        $maxWidthStyle = is_numeric($maxWidth) ? $maxWidth . 'px' : $maxWidth;
        
        $fit = $block['fit'] ?? 'contain';
        $radius = ($block['border_radius'] ?? 8) . 'px';
        
        if (! $src) return '';
        
        $srcsetAttr = $this->getImageSrcset($src);
        
        $imgStyles = [
            "width:{$widthStyle}",
            "max-width:{$maxWidthStyle}",
            "height:auto",
            "border-radius:{$radius}",
            "display:inline-block",
        ];
        
        if ($fit !== 'none') {
            $imgStyles[] = "object-fit:{$fit}";
        }
        
        $wrapperStyles = [
            "display:flex",
            "justify-content:" . match($align) {
                'left' => 'flex-start',
                'right' => 'flex-end',
                default => 'center',
            },
            "align-items:" . match($valign) {
                'top' => 'flex-start',
                'bottom' => 'flex-end',
                default => 'center',
            },
            "margin-bottom:16px",
            "width:100%"
        ];
        
        $imgStyleStr = implode(';', $imgStyles);
        $wrapperStyleStr = implode(';', $wrapperStyles);
        
        $linkUrl = isset($block['link_url']) ? $this->safeUrl($block['link_url']) : '';
        $imgHtml = "<img src=\"{$src}\"{$srcsetAttr} alt=\"{$alt}\" class=\"popup-widget popup-image popup-image-ultra\" style=\"{$imgStyleStr}\" loading=\"lazy\" decoding=\"async\" onload=\"this.classList.add('is-loaded')\">";
        
        if ($linkUrl) {
            $imgHtml = "<a href=\"{$linkUrl}\" target=\"_blank\" style=\"display:inline-block;max-width:100%;text-align:inherit;\">{$imgHtml}</a>";
        }
        
        return "<div class=\"popup-image-wrapper popup-image-container\" style=\"{$wrapperStyleStr}\">"
            . $imgHtml
            . "</div>";
    }

    private function getImageSrcset(string $src): string
    {
        $srcset = '';
        if (str_contains($src, '/uploads/')) {
            $pathInfo = pathinfo($src);
            $dir = $pathInfo['dirname'] ?? '';
            $filename = $pathInfo['filename'];
            $ext = $pathInfo['extension'] ?? '';
            
            if ($ext && !preg_match('/-(thumb|medium|large)$/', $filename)) {
                $disk = 'public';
                $mediumPath = 'uploads/' . $filename . '-medium.' . $ext;
                $largePath = 'uploads/' . $filename . '-large.' . $ext;
                
                $mediumUrl = $dir . '/' . $filename . '-medium.' . $ext;
                $largeUrl = $dir . '/' . $filename . '-large.' . $ext;
                
                $srcsetArray = [];
                if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($mediumPath)) {
                    $srcsetArray[] = "{$mediumUrl} 600w";
                }
                if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($largePath)) {
                    $srcsetArray[] = "{$largeUrl} 1200w";
                }
                
                if (!empty($srcsetArray)) {
                    $srcset = implode(', ', $srcsetArray);
                }
            }
        }
        return $srcset ? ' srcset="' . $srcset . '" sizes="(max-width: 600px) 100vw, 1200px"' : '';
    }

    private function renderButtonBlock(array $block): string
    {
        $text = e($block['text'] ?? 'Button');
        $url = $this->safeUrl($block['url'] ?? '#');
        $bg = $block['bg_color'] ?? '#6366f1';
        $fg = $block['text_color'] ?? '#ffffff';
        $full = ($block['full_width'] ?? '0') === '1';
        $display = $full ? 'block' : 'inline-block';
        return '<div class="popup-button-wrapper" style="text-align:center;margin-bottom:16px">'
            . '<a href="' . $url . '" class="popup-widget popup-button" style="display:' . $display . ';padding:12px 28px;background:' . $bg . ';color:' . $fg . ';border-radius:8px;font-weight:600;font-size:15px;text-decoration:none;transition:all .2s">' . $text . '</a>'
            . '</div>';
    }

    private function renderDividerBlock(array $block): string
    {
        $color = $block['color'] ?? '#e5e7eb';
        $thickness = ($block['thickness'] ?? 1) . 'px';
        $margin = ($block['margin'] ?? 16) . 'px';
        return '<hr class="popup-widget popup-divider" style="border:none;border-top:' . $thickness . ' solid ' . $color . ';margin:' . $margin . ' 0">';
    }

    private function renderSingleVideo(array $block): string
    {
        $src = $block['src'] ?? '';
        $maxWidth = $block['max_width'] ?? '100%';
        if (! $src) return '';
        if (str_contains($src, 'youtube')) {
            $embedSrc = str_replace(['watch?v=', 'youtu.be/'], ['embed/', 'youtube.com/embed/'], $src);
            return '<div class="popup-video-wrapper" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;max-width:' . $maxWidth . ';margin-bottom:16px">'
                . '<iframe src="' . $embedSrc . '" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;border-radius:8px" allowfullscreen></iframe>'
                . '</div>';
        }
        return '<video class="popup-widget popup-video" controls style="width:100%;max-width:' . $maxWidth . ';margin-bottom:16px"><source src="' . $src . '"></video>';
    }

    private function registerDefaultWidgets(): void
    {
        // Default widgets are handled via renderDefaultWidget
    }
}
