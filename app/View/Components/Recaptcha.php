<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Blade component: <x-recaptcha action="enquiry" />
 *
 * Injects reCAPTCHA v3 JS + hidden input into any form.
 * Renders nothing when RECAPTCHA_SITE_KEY is not configured (dev/local bypass).
 */
class Recaptcha extends Component
{
    /**
     * @param  string  $action  The reCAPTCHA action name (e.g. "enquiry", "job_apply").
     */
    public function __construct(public string $action = 'submit')
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.recaptcha');
    }
}
