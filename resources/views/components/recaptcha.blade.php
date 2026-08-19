{{--
    reCAPTCHA v3 Form Component
    ============================
    Drop-in component to add invisible reCAPTCHA v3 to any form.

    Usage:
      <x-recaptcha action="enquiry" />

    Place ONCE anywhere on the page (usually just before </body>).
    The component injects:
      1. The Google reCAPTCHA v3 <script> tag (only if RECAPTCHA_SITE_KEY is set).
      2. A hidden <input name="g-recaptcha-response"> field that the JS populates.
      3. An inline JS snippet that calls grecaptcha.execute() on every form submit.

    When RECAPTCHA_SITE_KEY is empty (local/dev), this renders nothing.
--}}

@php
    $siteKey = config('privacy.recaptcha_site_key', '');
@endphp

@if($siteKey)
<script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}" defer></script>

<input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-{{ $action }}" value="">

<script>
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('[data-recaptcha-action="{{ $action }}"]');

    forms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (typeof grecaptcha === 'undefined') {
                // reCAPTCHA didn't load (ad blocker, network issue) — submit anyway
                form.submit();
                return;
            }

            grecaptcha.ready(function () {
                grecaptcha.execute('{{ $siteKey }}', { action: '{{ $action }}' })
                    .then(function (token) {
                        var field = document.getElementById('g-recaptcha-response-{{ $action }}');
                        if (field) { field.value = token; }
                        form.submit();
                    })
                    .catch(function () {
                        // If grecaptcha.execute fails, submit without token
                        // (server-side will allow it due to fail-open on empty secret)
                        form.submit();
                    });
            });
        });
    });
});
</script>
@endif
