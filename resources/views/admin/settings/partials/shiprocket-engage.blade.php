@php
    $engageService = app(\App\Services\ShiprocketEngageService::class);
    $engageCreds = $engageService->credentials();
    $engageEnabled = $engageService->isEnabled();
    $engageHasSavedPassword = (bool) \App\Models\Setting::get('shiprocket_engage_password');
    $engageEnvActive = config('services.shiprocket_engage.email') && config('services.shiprocket_engage.password');
@endphp
<!-- Shiprocket Engage Tab -->
<div class="tab-pane settings-tab-pane fade" id="shiprocket-engage" role="tabpanel" aria-labelledby="shiprocket-engage-tab">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
        <h4 class="font-heading fw-bold text-success m-0"><i class="bi bi-whatsapp me-2"></i>Shiprocket Engage</h4>
        <span class="badge bg-{{ $engageEnabled && $engageCreds['email'] ? 'success' : 'secondary' }}">
            {{ $engageEnabled && $engageCreds['email'] ? 'Enabled' : 'Disabled' }}
        </span>
    </div>

    <div class="alert alert-info border-0 rounded-3" style="font-size: 0.85rem;">
        <i class="bi bi-info-circle-fill me-1"></i>
        Shiprocket does <strong>not</strong> issue a separate API key, secret or workspace ID for Engage. Engage runs inside your
        Shiprocket account and is accessed with the same <strong>API user (email + password)</strong> from
        <em>Shiprocket → Settings → API</em>. This module is separate from the Shipping integration and does not change it.
    </div>

    {{-- ── Settings ── --}}
    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-1"></i> Settings</h6>
    <div class="card border rounded-3 p-3 bg-light mb-3">
        <div class="form-check form-switch mb-1">
            <input type="hidden" name="settings[shiprocket_engage_enabled]" value="false">
            <input class="form-check-input" type="checkbox" name="settings[shiprocket_engage_enabled]" value="true" id="shiprocketEngageEnabled" {{ $engageEnabled ? 'checked' : '' }}>
            <label class="form-check-label fw-bold text-dark" for="shiprocketEngageEnabled">Enable Shiprocket Engage integration</label>
        </div>
        <small class="text-muted d-block">Turns on the Engage module in this site. WhatsApp flows themselves are switched on inside the Shiprocket panel (see Setup Guide).</small>
    </div>

    @if ($engageEnvActive)
        <div class="alert alert-warning border-0 rounded-3" style="font-size: 0.85rem;">
            <i class="bi bi-shield-lock-fill me-1"></i>
            Credentials are coming from <strong>.env</strong> (<code>SHIPROCKET_ENGAGE_EMAIL</code> / <code>SHIPROCKET_ENGAGE_PASSWORD</code>) and override the fields below.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">API User Email</label>
            <input type="email" name="settings[shiprocket_engage_email]" id="shiprocketEngageEmail" class="form-control bg-light border p-2" value="{{ \App\Models\Setting::get('shiprocket_engage_email') }}" placeholder="api-user@yourbrand.com" autocomplete="off">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">API User Password</label>
            <div class="input-group">
                <input type="password" name="settings[shiprocket_engage_password]" id="shiprocketEngagePassword" class="form-control bg-light border p-2" value="" placeholder="{{ $engageHasSavedPassword ? 'Saved — leave blank to keep' : 'Enter password' }}" autocomplete="new-password">
                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1"><i class="bi bi-eye"></i></button>
            </div>
            @if ($engageHasSavedPassword)
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="clear_secrets[shiprocket_engage_password]" value="1" id="shiprocketEngagePasswordClear">
                    <label class="form-check-label text-muted" for="shiprocketEngagePasswordClear" style="font-size: 0.8rem;">Remove saved password</label>
                </div>
            @endif
        </div>
        <div class="col-12">
            <small class="text-muted d-block">
                Leave both blank to reuse the Shiprocket credentials from the <strong>Shipping &amp; Taxes</strong> tab. The password is stored encrypted and is never shown again.
                Currently using: <strong>{{ \App\Services\ShiprocketEngageService::sourceLabel($engageCreds['source']) }}</strong>{{ $engageCreds['email'] ? ' — ' . $engageCreds['email'] : '' }}.
            </small>
        </div>
    </div>

    {{-- ── Test Connection ── --}}
    <div class="card border rounded-3 p-3 mt-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-success font-heading" id="btnTestShiprocketEngage">
                <i class="bi bi-plug me-1"></i> Test Connection
            </button>
            <small class="text-muted">Works on localhost and live. Tests the values typed above if both email and password are filled, otherwise the saved configuration.</small>
        </div>
        <div id="shiprocketEngageTestResult" class="mt-3 d-none"></div>
    </div>

    {{-- ── Setup Guide ── --}}
    <h6 class="fw-bold text-dark mt-5 mb-3"><i class="bi bi-journal-text me-1"></i> Instructions / Setup Guide</h6>
    <div class="accordion" id="engageGuide" style="font-size: 0.85rem;">
        @php
            $engageSteps = [
                ['Open Shiprocket Engage', '
                    <ol class="mb-0">
                        <li>Log in to <a href="https://app.shiprocket.in" target="_blank" rel="noopener">app.shiprocket.in</a> with the <strong>same account</strong> used for shipping.</li>
                        <li>Open <strong>Engage</strong> from the left menu (Engage 360 / buyer communication). WhatsApp order updates are under <strong>Settings → Notifications → Buyer Communication</strong>.</li>
                        <li>If Engage is not visible, ask your Shiprocket account manager / support to activate it for your account.</li>
                    </ol>'],
                ['Create API credentials', '
                    <ol class="mb-0">
                        <li>In Shiprocket go to <strong>Settings → API → Configure → Create An API User</strong>.</li>
                        <li>Enter an email that is <em>different</em> from your panel login email, set a password and save.</li>
                        <li>You may reuse the API user already used for shipping — Engage needs no separate user.</li>
                    </ol>'],
                ['Which details to copy', '
                    <p class="mb-1">Only two values are needed:</p>
                    <ul class="mb-1"><li>API user <strong>email</strong></li><li>API user <strong>password</strong></li></ul>
                    <p class="mb-0 text-muted">Shiprocket\'s official API (apidocs.shiprocket.in) has no Engage-specific API key, secret or workspace ID, so none are asked for here.</p>'],
                ['Enter them in this Admin Panel', '
                    <ol class="mb-0">
                        <li>Admin → Settings → <strong>Shiprocket Engage</strong>.</li>
                        <li>Turn on <strong>Enable Shiprocket Engage integration</strong>.</li>
                        <li>Fill API User Email + Password (or leave blank to reuse the Shipping tab credentials).</li>
                        <li>Click <strong>Save Configurations</strong>.</li>
                    </ol>'],
                ['WhatsApp / channel configuration', '
                    <ol class="mb-0">
                        <li>Shiprocket → <strong>Settings → Notifications → Buyer Communication</strong> → <strong>Activate Now</strong> to enable WhatsApp communication, choose the order statuses to notify, and save.</li>
                        <li>In the <strong>Engage</strong> section enable the flows you want (order confirmation, COD confirmation / COD-to-prepaid, address verification) and set up your WhatsApp business/branded profile if offered.</li>
                        <li>Engage acts on orders that reach Shiprocket. This site pushes orders through the custom channel set in <strong>Shipping &amp; Taxes → Shiprocket Integration</strong> (keep it enabled and the Channel ID correct).</li>
                        <li>Customer phone numbers must be valid 10-digit Indian mobiles for WhatsApp delivery.</li>
                        <li>Engage messages are billed from your <strong>Shiprocket wallet</strong> — keep it recharged. Check current pricing in the panel.</li>
                    </ol>'],
                ['Test the API connection', '
                    <p class="mb-1">Click <strong>Test Connection</strong>. The backend runs these checks:</p>
                    <ul class="mb-0">
                        <li><strong>Credentials</strong> — which source is used (.env / Engage settings / Shipping settings).</li>
                        <li><strong>API authentication</strong> — <code>POST /auth/login</code> returns a token (credentials valid, API reachable).</li>
                        <li><strong>Account / wallet access</strong> — <code>GET /account/details/wallet-balance</code>.</li>
                        <li><strong>Channels</strong> — <code>GET /channels</code>, and whether your configured channel exists.</li>
                        <li><strong>Engage status on orders</strong> — the <code>engage</code> field of your latest pushed order (<code>GET /orders/show/{id}</code>).</li>
                    </ul>'],
                ['After a successful test', '
                    <ul class="mb-0">
                        <li>New orders pushed to Shiprocket are picked up by the Engage flows you enabled in the Shiprocket panel — no extra code needed.</li>
                        <li>Place a test order and re-run the test; the latest order\'s <code>engage</code> value confirms Engage is working.</li>
                        <li>Further features (order/COD confirmation results, address updates, abandoned cart, customer messaging) will be added in this same module (<code>App\Services\ShiprocketEngageService</code>) as Shiprocket makes the corresponding APIs/webhooks available for custom websites. Abandoned-cart recovery currently needs cart data Shiprocket gets from its own store apps/checkout — confirm availability with Shiprocket before planning it.</li>
                    </ul>'],
                ['Localhost configuration', '
                    <ol class="mb-0">
                        <li>Either fill the fields on this page, or add to your local <code>.env</code>:<br><code>SHIPROCKET_ENGAGE_EMAIL=api-user@yourbrand.com</code><br><code>SHIPROCKET_ENGAGE_PASSWORD=your-password</code></li>
                        <li>Run <code>php artisan config:clear</code> after editing <code>.env</code>.</li>
                        <li>If the test shows <em>SSL certificate error (cURL 60)</em>, point <code>curl.cainfo</code> / <code>openssl.cafile</code> in php.ini to a <code>cacert.pem</code> file, or set <code>SHIPROCKET_ENGAGE_VERIFY_SSL=false</code> (only honoured when <code>APP_ENV=local</code>).</li>
                        <li>Your computer needs internet access; HTTPS on localhost is <em>not</em> required for this test.</li>
                    </ol>'],
                ['Live server configuration', '
                    <ol class="mb-0">
                        <li>Recommended: put credentials in the server <code>.env</code> (cPanel File Manager or SSH), never in code or Git:<br><code>SHIPROCKET_ENGAGE_EMAIL=...</code><br><code>SHIPROCKET_ENGAGE_PASSWORD=...</code></li>
                        <li>Run <code>php artisan config:cache</code> after changing <code>.env</code>.</li>
                        <li>Keep <code>APP_ENV=production</code> — SSL verification is always on in production.</li>
                        <li>The server must allow outgoing HTTPS (port 443) to <code>apiv2.shiprocket.in</code>.</li>
                        <li>Don\'t change <code>APP_KEY</code> — the admin-saved password is encrypted with it. If it changes, re-enter the password.</li>
                    </ol>'],
                ['Common errors & solutions', '
                    <table class="table table-sm table-bordered bg-white mb-0">
                        <thead><tr><th>Error</th><th>Solution</th></tr></thead>
                        <tbody>
                            <tr><td>HTTP 401/403 — login failed</td><td>Use the API user from Settings → API (not your panel login). Check email/password; confirm the API user is active.</td></tr>
                            <tr><td>No credentials found</td><td>Fill this tab, the Shipping tab, or the <code>.env</code> variables, then Save.</td></tr>
                            <tr><td>SSL certificate error (cURL 60)</td><td>Localhost only: configure <code>curl.cainfo</code> or set <code>SHIPROCKET_ENGAGE_VERIFY_SSL=false</code>.</td></tr>
                            <tr><td>Cannot reach apiv2.shiprocket.in / timeout</td><td>Check internet, DNS or hosting firewall; increase <code>SHIPROCKET_ENGAGE_TIMEOUT</code>.</td></tr>
                            <tr><td>HTTP 429 Too many requests</td><td>Wait a minute and test again.</td></tr>
                            <tr><td>Wallet balance ₹0 / negative</td><td>Recharge the Shiprocket wallet — Engage messages are billed from it.</td></tr>
                            <tr><td>Channel ID not found</td><td>Correct the Channel ID in Shipping &amp; Taxes using one from the list shown in the test result.</td></tr>
                            <tr><td>engage = null / NA on orders</td><td>Engage/WhatsApp flows are not active for that order — enable them in the Shiprocket panel; only new orders are affected.</td></tr>
                            <tr><td>.env change not applied</td><td>Run <code>php artisan config:clear</code> (local) or <code>config:cache</code> (live).</td></tr>
                        </tbody>
                    </table>'],
            ];
        @endphp
        @foreach ($engageSteps as $i => [$title, $body])
            <div class="accordion-item">
                <h2 class="accordion-header" id="engageGuideH{{ $i }}">
                    <button class="accordion-button collapsed py-2 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#engageGuideC{{ $i }}" aria-expanded="false" aria-controls="engageGuideC{{ $i }}" style="font-size: 0.85rem;">
                        <span class="badge bg-success rounded-pill me-2">{{ $i + 1 }}</span> {{ $title }}
                    </button>
                </h2>
                <div id="engageGuideC{{ $i }}" class="accordion-collapse collapse" aria-labelledby="engageGuideH{{ $i }}" data-bs-parent="#engageGuide">
                    <div class="accordion-body">{!! $body !!}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>
