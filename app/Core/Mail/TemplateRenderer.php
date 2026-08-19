<?php

namespace App\Core\Mail;

class TemplateRenderer
{
    /**
     * Safely render subject and body with placeholder mapping.
     */
    public function render(string $templateHtml, string $subject, array $data, bool $isNewsletter = false, ?string $unsubscribeUrl = null): array
    {
        $renderedSubject = $this->replacePlaceholders($subject, $data);
        $renderedBody = $this->replacePlaceholders($templateHtml, $data);

        // Sanitize any raw PHP code tags if present
        $renderedBody = preg_replace('/<\?php[\s\S]*?\?>/i', '', $renderedBody);

        if ($isNewsletter && $unsubscribeUrl) {
            $renderedBody = $this->appendUnsubscribeFooter($renderedBody, $unsubscribeUrl);
        }

        return [
            'subject'   => $renderedSubject,
            'body_html' => $renderedBody,
            'body_text' => strip_tags(str_replace(['<br>', '<br/>', '</p>'], ["\n", "\n", "\n\n"], $renderedBody)),
        ];
    }

    public function replacePlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $val) {
            if (is_scalar($val) || (is_object($val) && method_exists($val, '__toString'))) {
                $content = str_replace('{{' . $key . '}}', (string) $val, $content);
                $content = str_replace('{{ ' . $key . ' }}', (string) $val, $content);
            }
        }
        return $content;
    }

    protected function appendUnsubscribeFooter(string $html, string $unsubscribeUrl): string
    {
        if (str_contains($html, '{{unsubscribe_url}}') || str_contains($html, '{{ unsubscribe_url }}')) {
            return str_replace(['{{unsubscribe_url}}', '{{ unsubscribe_url }}'], $unsubscribeUrl, $html);
        }

        $footer = '
        <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; text-align: center;">
            <p>You received this email because you are subscribed to our updates.</p>
            <p><a href="' . htmlspecialchars($unsubscribeUrl) . '" style="color: #4f46e5; text-decoration: underline;">Unsubscribe from these emails</a></p>
        </div>';

        if (str_contains($html, '</body>')) {
            return str_replace('</body>', $footer . '</body>', $html);
        }

        return $html . $footer;
    }
}
