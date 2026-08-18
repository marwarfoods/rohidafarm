<!-- Authentication & Google OAuth Group Tab -->
<div class="tab-pane settings-tab-pane fade" id="auth" role="tabpanel" aria-labelledby="auth-tab">
    <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Google OAuth & Social Authentication</h4>
    <p class="text-muted fs-6 mb-4">Configure Google Social Login credentials to allow customers to log in using their Google accounts seamlessly.</p>

    <div class="card border rounded-3 p-3 bg-light mb-4">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="settings[google_login_enabled]" value="0">
            <input class="form-check-input" type="checkbox" name="settings[google_login_enabled]" value="1" id="googleLoginSwitch" {{ \App\Models\Setting::get('google_login_enabled') ? 'checked' : '' }}>
            <label class="form-check-label fw-bold text-dark fs-6" for="googleLoginSwitch">Enable Google Social Login</label>
        </div>
        <small class="text-muted d-block">When enabled, a "Continue with Google" button will be displayed on the login and register pages.</small>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold text-dark">Google Client ID *</label>
        <input type="text" name="settings[google_client_id]" id="inputGoogleClientId" class="form-control bg-light border p-2" placeholder="e.g. 123456789-abc.apps.googleusercontent.com" value="{{ \App\Models\Setting::get('google_client_id') }}">
        <small class="text-muted">Obtained from Google Cloud Console -> APIs & Services -> Credentials</small>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold text-dark">Google Client Secret *</label>
        <div class="input-group">
            <input type="password" name="settings[google_client_secret]" id="inputGoogleClientSecret" class="form-control bg-light border p-2" placeholder="Enter Google Client Secret key" value="{{ \App\Models\Setting::get('google_client_secret') }}">
            <button class="btn btn-outline-secondary toggle-password" type="button"><i class="bi bi-eye"></i></button>
        </div>
        <small class="text-muted">Keep this secret safe. Do not share it publicly.</small>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold text-dark">Authorized Redirect URI (Callback URL)</label>
        <div class="input-group">
            <input type="text" class="form-control bg-light font-monospace text-muted" value="{{ route('auth.google.callback') }}" readonly id="googleCallbackUrl">
            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('googleCallbackUrl').value); alert('Callback URL copied to clipboard!');">
                <i class="bi bi-clipboard me-1"></i> Copy
            </button>
        </div>
        <small class="text-muted">Add this URL to Google Cloud Console under "Authorized redirect URIs".</small>
    </div>

    <div class="pt-2">
        <button type="button" class="btn btn-outline-success fw-bold rounded-pill px-4" id="btnTestGoogleOAuth">
            <i class="bi bi-patch-check me-1"></i> Test Connection
        </button>
        <div id="googleTestResult" class="mt-3 d-none"></div>
    </div>
</div>
