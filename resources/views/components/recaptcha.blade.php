{{--
    Google reCAPTCHA v3 (invisible). No checkbox: the form gets a fresh token at
    submit time and the only UI is Google's badge, pinned to the bottom-left.
--}}
@if(\App\Services\RecaptchaService::isEnabled())
    @php
        $siteKey = \App\Services\RecaptchaService::getSiteKey();
    @endphp
    @if($siteKey)
        <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-token" value="">
        @error('g-recaptcha-response')
            <div class="text-danger small my-2 fw-semibold text-center">{{ $message }}</div>
        @enderror

        @pushOnce('scripts')
            <div id="recaptchaBadge"></div>
            <style>
                /* Below lg the fixed mobile bottom nav would cover the badge — lift it above the nav. */
                @media (max-width: 991.98px) {
                    .grecaptcha-badge { bottom: calc(84px + env(safe-area-inset-bottom, 0px)) !important; z-index: 1030; }
                }
            </style>
            <script>
                (function () {
                    const siteKey = @json($siteKey);
                    let widgetId = null;

                    // Explicit render so the badge can sit bottom-left instead of Google's default bottom-right.
                    window.onRecaptchaLoad = function () {
                        widgetId = grecaptcha.render('recaptchaBadge', { sitekey: siteKey, badge: 'bottomleft', size: 'invisible' });
                    };

                    // Capture phase: fetch a token before any other submit handler (native or AJAX) sees the
                    // form, then replay the submit with the token filled in. Tokens expire after 2 minutes,
                    // so one is requested per submission rather than on page load.
                    document.addEventListener('submit', function (e) {
                        const form = e.target;
                        const input = form.querySelector && form.querySelector('.g-recaptcha-token');
                        if (!input) return;
                        if (form.dataset.recaptchaReady === '1') {
                            delete form.dataset.recaptchaReady;
                            return;
                        }
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        if (form.dataset.recaptchaBusy === '1') return;
                        form.dataset.recaptchaBusy = '1';

                        const submitter = e.submitter && e.submitter.form === form ? e.submitter : null;
                        // Action = form's path, e.g. /password/email → "password_email" (letters, digits, _ only).
                        const action = new URL(form.action, location.href).pathname
                            .replace(/[^A-Za-z0-9]+/g, '_').replace(/^_+|_+$/g, '') || 'submit';

                        const finish = function (token) {
                            input.value = token || '';
                            delete form.dataset.recaptchaBusy;
                            form.dataset.recaptchaReady = '1';
                            if (form.requestSubmit) form.requestSubmit(submitter || undefined);
                            else form.submit();
                        };

                        if (typeof grecaptcha === 'undefined' || widgetId === null) {
                            finish(''); // script blocked/not loaded — the server decides
                            return;
                        }
                        grecaptcha.ready(function () {
                            grecaptcha.execute(widgetId, { action: action }).then(finish, function () { finish(''); });
                        });
                    }, true);
                })();
            </script>
            <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
        @endPushOnce
    @endif
@endif
