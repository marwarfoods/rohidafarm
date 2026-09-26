<!-- Integrations Tab -->
<div class="tab-pane settings-tab-pane fade" id="integrations" role="tabpanel" aria-labelledby="integrations-tab">
    <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Third-Party Integrations</h4>
    
    <!-- Google Analytics Section -->
    <div>
        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
            <img src="https://play-lh.googleusercontent.com/-CrW3g5AXNgw4DxSqCC46AMFQ1JRTz5YjOSn1xfllkxMml6KpR3enY7MfVhcq8vDGVSUsr76Ca-89y3GocaS" height="24" class="me-2" alt="Google Analytics"> Google Analytics (GA4)
            <i class="bi bi-question-circle text-primary ms-2 cursor-pointer" data-bs-toggle="modal" data-bs-target="#gaHelpModal" style="font-size: 1.1rem;" title="How to get Google Analytics ID?"></i>
        </h6>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Measurement ID (G-XXXXXXXXXX)</label>
                <input type="text" name="settings[google_analytics_id]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('google_analytics_id') }}">
            </div>
        </div>
        <small class="text-muted d-block mt-2">Enter your GA4 Measurement ID to start tracking website traffic.</small>
    </div>

    <hr class="my-4">

    <!-- Google reCAPTCHA v3 Protection -->
    @php
        $recaptchaOn = \App\Models\Setting::get('recaptcha_enabled') && \App\Models\Setting::get('recaptcha_site_key');
    @endphp
    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark d-flex align-items-center m-0">
                <i class="bi bi-shield-lock-fill text-warning fs-5 me-2"></i> Google reCAPTCHA v3 (Bot & Spam Protection)
            </h6>
            <span class="badge bg-{{ $recaptchaOn ? 'success' : 'secondary' }}">
                {{ $recaptchaOn ? 'Enabled' : 'Disabled' }}
            </span>
        </div>

        <div class="card border rounded-3 p-3 bg-light mb-3">
            <div class="form-check form-switch mb-1">
                <input type="hidden" name="settings[recaptcha_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[recaptcha_enabled]" value="1" id="recaptchaSwitch" {{ \App\Models\Setting::get('recaptcha_enabled') ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark fs-6" for="recaptchaSwitch">Enable Google reCAPTCHA v3 on Public Forms</label>
            </div>
            <small class="text-muted d-block">Invisible background check (no checkbox) protecting Login, Registration, Contact Form, Reviews, and Password Reset. Only the small reCAPTCHA badge shows at the bottom-left.</small>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">reCAPTCHA Site Key</label>
                <input type="text" name="settings[recaptcha_site_key]" id="inputRecaptchaSiteKey" class="form-control bg-light border p-2 font-monospace" placeholder="e.g. 6Lc..." value="{{ \App\Models\Setting::get('recaptcha_site_key') }}">
                <small class="text-muted">Public Site Key (reCAPTCHA v3) from the Google reCAPTCHA Admin Console</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">reCAPTCHA Secret Key</label>
                <div class="input-group">
                    <input type="password" name="settings[recaptcha_secret_key]" id="inputRecaptchaSecretKey" class="form-control bg-light border p-2 font-monospace" placeholder="e.g. 6Lc..." value="{{ \App\Models\Setting::get('recaptcha_secret_key') }}">
                    <button class="btn btn-outline-secondary toggle-password" type="button"><i class="bi bi-eye"></i></button>
                </div>
                <small class="text-muted">Server-side Secret Key used for verification API</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Minimum Score</label>
                <input type="number" name="settings[recaptcha_min_score]" class="form-control bg-light border p-2" min="0.1" max="1" step="0.1" value="{{ \App\Models\Setting::get('recaptcha_min_score', '0.5') }}">
                <small class="text-muted">0.1 (lenient) – 1.0 (strict). Submissions scoring below this are blocked. Google recommends 0.5.</small>
            </div>
        </div>

        <div class="alert alert-info border-0 rounded-3 py-2 px-3 mt-3 d-flex align-items-center gap-2" style="font-size:0.85rem;">
            <i class="bi bi-info-circle-fill text-primary fs-5"></i>
            <span>Create the keys as <strong>Score based (v3)</strong> in the Google console and add your domain there. v2 checkbox keys will not work.</span>
        </div>

        <div class="mt-3 d-flex align-items-center gap-3">
            <button type="button" class="btn btn-outline-success fw-bold rounded-pill px-4" id="btnTestRecaptcha">
                <i class="bi bi-shield-check me-1"></i> Test Connection
            </button>
            <a href="https://www.google.com/recaptcha/admin" target="_blank" class="btn btn-link text-decoration-none text-muted p-0" style="font-size: 0.85rem;">
                <i class="bi bi-box-arrow-up-right me-1"></i> Open reCAPTCHA Admin Console
            </a>
        </div>

        <div id="recaptchaTestResult" class="mt-3 d-none"></div>
    </div>

    <hr class="my-4">

    <!-- Meta Pixel (Facebook Pixel) Section -->
    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold text-dark d-flex align-items-center m-0">
                <i class="fa-brands fa-meta text-primary fs-5 me-2" style="color: #0668E1 !important;"></i> Meta Pixel (Facebook Pixel)
                <i class="bi bi-question-circle text-primary ms-2 cursor-pointer" data-bs-toggle="modal" data-bs-target="#metaPixelHelpModal" style="font-size: 1.1rem;" title="How to get Meta Pixel ID?"></i>
            </h6>
            <span class="badge bg-{{ \App\Models\Setting::get('meta_pixel_enabled') && (\App\Models\Setting::get('meta_pixel_id') || \App\Models\Setting::get('meta_pixel_code')) ? 'success' : 'secondary' }}">
                {{ \App\Models\Setting::get('meta_pixel_enabled') && (\App\Models\Setting::get('meta_pixel_id') || \App\Models\Setting::get('meta_pixel_code')) ? 'Active & Tracking' : 'Disabled' }}
            </span>
        </div>

        <div class="card border rounded-3 p-3 bg-light mb-3">
            <div class="form-check form-switch mb-1">
                <input type="hidden" name="settings[meta_pixel_enabled]" value="0">
                <input class="form-check-input" type="checkbox" name="settings[meta_pixel_enabled]" value="1" id="metaPixelSwitch" {{ \App\Models\Setting::get('meta_pixel_enabled') ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark fs-6" for="metaPixelSwitch">Enable Meta Pixel Tracking on Website</label>
            </div>
            <small class="text-muted d-block">Track conversions, ad performance, page views, and optimize Facebook/Instagram ad campaigns across your store.</small>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Meta Pixel ID / Dataset ID</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="fa-brands fa-facebook-f"></i></span>
                    <input type="text" name="settings[meta_pixel_id]" id="inputMetaPixelId" class="form-control bg-light border p-2 font-monospace" placeholder="e.g. 1234567890123456" value="{{ \App\Models\Setting::get('meta_pixel_id') }}">
                </div>
                <small class="text-muted">Numeric Pixel ID from Meta Events Manager (auto-generates standard tracking code).</small>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold text-dark d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                    <span>Custom Meta Code / Script (Optional)</span>
                    <span class="badge bg-light text-muted border">Advanced</span>
                </label>
                <textarea name="settings[meta_pixel_code]" id="inputMetaPixelCode" rows="2" class="form-control bg-light border p-2 font-monospace" style="font-size: 0.8rem;" placeholder="<!-- Optional: Paste custom Facebook tracking script or standard event snippet -->">{{ \App\Models\Setting::get('meta_pixel_code') }}</textarea>
                <small class="text-muted">Optional: Paste custom snippet or manual base code if you prefer not using just the Pixel ID.</small>
            </div>
        </div>

        <div class="alert alert-light border rounded-3 py-2 px-3 mt-3 d-flex align-items-center justify-content-between" style="font-size:0.85rem;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <span>When enabled, standard <code>PageView</code> and pixel scripts will automatically load across the store.</span>
            </div>
            <a href="https://business.facebook.com/events_manager2" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 text-nowrap">
                <i class="bi bi-box-arrow-up-right me-1"></i> Meta Events Manager
            </a>
        </div>
    </div>
</div>
