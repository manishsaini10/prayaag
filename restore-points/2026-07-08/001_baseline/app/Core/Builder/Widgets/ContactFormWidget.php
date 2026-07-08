<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Renders a contact form posting to /enquiries. Includes a CSRF token, a
 * hidden honeypot, the originating page as source, and shows a success notice
 * after submission. Safe to render outside a request (token/session guarded).
 */
class ContactFormWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'contact_form';
    }

    public function label(): string
    {
        return 'Contact Form';
    }

    public function category(): string
    {
        return 'forms';
    }

    public function defaultSettings(): array
    {
        return [
            'heading' => 'Contact Us',
            'button'  => 'Send',
            'success' => 'Thanks! We will be in touch shortly.',
            'type'    => 'contact',
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $heading = $this->e($this->setting($settings, 'heading', 'Contact Us'));
        $button = $this->e($this->setting($settings, 'button', 'Send'));
        $type = $this->e($this->setting($settings, 'type', 'contact'));

        $token = rescue(fn () => csrf_token(), '', false);
        $source = $this->e(rescue(fn () => request()->path(), '', false));
        $sent = rescue(fn () => session('enquiry_sent'), false, false);
        $errors = rescue(fn () => session('errors'), null, false);

        $notice = $sent
            ? '<div class="pb-form__ok">' . $this->e($this->setting($settings, 'success', '')) . '</div>'
            : '';

        $error = ($errors && method_exists($errors, 'any') && $errors->any())
            ? '<div class="pb-form__err">' . $this->e($errors->first()) . '</div>'
            : '';

        return '<form class="pb-form" method="POST" action="/enquiries">'
            . '<input type="hidden" name="_token" value="' . $token . '">'
            . '<input type="hidden" name="type" value="' . $type . '">'
            . '<input type="hidden" name="source" value="' . $source . '">'
            . '<input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">'
            . '<h3 class="pb-form__heading">' . $heading . '</h3>'
            . $notice . $error
            . '<input class="pb-input" type="text" name="name" placeholder="Your name" required>'
            . '<input class="pb-input" type="email" name="email" placeholder="Email" required>'
            . '<input class="pb-input" type="text" name="phone" placeholder="Phone (optional)">'
            . '<textarea class="pb-input" name="message" placeholder="Message" required></textarea>'
            . '<button class="pb-button" type="submit">' . $button . '</button>'
            . '</form>';
    }
}
