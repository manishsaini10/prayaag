<?php

namespace App\Core\Builder\Widgets;

use App\Core\Builder\AbstractWidget;

/**
 * Newsletter signup form posting to /subscribe. CSRF token and session reads
 * are guarded with rescue() so the widget renders safely anywhere.
 */
class NewsletterWidget extends AbstractWidget
{
    public function type(): string
    {
        return 'newsletter';
    }

    public function label(): string
    {
        return 'Newsletter Signup';
    }

    public function category(): string
    {
        return 'forms';
    }

    public function defaultSettings(): array
    {
        return [
            'heading' => 'Subscribe to our newsletter',
            'button'  => 'Subscribe',
            'success' => 'Thanks for subscribing!',
        ];
    }

    public function isDynamic(): bool
    {
        return true;
    }

    public function render(array $settings, array $context = []): string
    {
        $heading = $this->e($this->setting($settings, 'heading', 'Subscribe'));
        $button = $this->e($this->setting($settings, 'button', 'Subscribe'));

        $token = rescue(fn () => csrf_token(), '', false);
        $source = $this->e(rescue(fn () => request()->path(), '', false));
        $sent = rescue(fn () => session('subscribed'), false, false);
        $errors = rescue(fn () => session('errors'), null, false);

        $notice = $sent
            ? '<div class="pb-form__ok">' . $this->e($this->setting($settings, 'success', '')) . '</div>'
            : '';

        $error = ($errors && method_exists($errors, 'any') && $errors->any())
            ? '<div class="pb-form__err">' . $this->e($errors->first()) . '</div>'
            : '';

        return '<form class="pb-form pb-newsletter" method="POST" action="/subscribe">'
            . '<input type="hidden" name="_token" value="' . $token . '">'
            . '<input type="hidden" name="source" value="' . $source . '">'
            . '<input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">'
            . '<h3 class="pb-form__heading">' . $heading . '</h3>'
            . $notice . $error
            . '<input class="pb-input" type="email" name="email" placeholder="Your email" required>'
            . '<button class="pb-button" type="submit">' . $button . '</button>'
            . '</form>';
    }
}
