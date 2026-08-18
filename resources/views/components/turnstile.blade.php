@if(\App\Services\TurnstileService::isEnabled())
    @php
        $siteKey = \App\Services\TurnstileService::getSiteKey();
    @endphp
    @if($siteKey)
        <div class="my-3 d-flex flex-column align-items-center justify-content-center">
            <div class="cf-turnstile" data-sitekey="{{ $siteKey }}"></div>
            @error('cf-turnstile-response')
                <div class="text-danger small mt-1 fw-semibold text-center">{{ $message }}</div>
            @enderror
        </div>

        @pushOnce('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endPushOnce
    @endif
@endif
