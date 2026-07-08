<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Raw-HTML widget with a server-side sanitization pass.
 *
 * SECURITY NOTE: regex-based HTML sanitization is a defense-in-depth stopgap,
 * NOT a robust filter — it can be bypassed with malformed markup, exotic
 * vectors (SVG/MathML), or mutation XSS. For production this widget should be
 * (a) restricted to trusted/super-admin authors, AND/OR (b) run through a
 * vetted sanitizer (e.g. HTMLPurifier / voku/anti-xss), AND/OR (c) rendered
 * inside a sandboxed iframe under a strict CSP. Tracked as a known limitation.
 */
class HtmlWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'html';
    }

    public function label(): string
    {
        return 'Custom HTML';
    }

    public function category(): string
    {
        return 'advanced';
    }

    public function defaultSettings(): array
    {
        return ['html' => ''];
    }

    public function render(array $settings, array $context = []): string
    {
        return $this->sanitize((string) $this->setting($settings, 'html', ''));
    }

    protected function sanitize(string $html): string
    {
        $dangerous = 'script|style|iframe|object|embed|link|meta|base|form|svg|math|template|frame|frameset|applet';

        // Paired dangerous element blocks (incl. content).
        $html = preg_replace('#<\s*(' . $dangerous . ')[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? '';
        // Self-closing / unpaired dangerous elements.
        $html = preg_replace('#<\s*(' . $dangerous . ')[^>]*/?>#is', '', $html) ?? '';
        // Inline event handlers (onclick, onerror, onload, ...).
        $html = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html) ?? '';
        // Neutralize javascript:/vbscript:/data: URLs in href/src.
        $html = preg_replace(
            '#(href|src|xlink:href)\s*=\s*(["\']?)\s*(?:javascript|vbscript|data)\s*:[^"\'>\s]*#i',
            '$1=$2#',
            $html
        ) ?? '';

        return $html;
    }
}
