@php
    $src = app(\App\Services\ShiprocketCheckoutService::class);
    $srcEnabled = $src->isEnabled();
    $srcHasCreds = $src->hasCredentials();
    $srcEnvCreds = $src->credentialSource() === 'env';
    $srcHasSavedKey = (bool) \App\Models\Setting::get('shiprocket_checkout_api_key');
    $srcHasSavedSecret = (bool) \App\Models\Setting::get('shiprocket_checkout_secret_key');
    $srcLastTestAt = \App\Models\Setting::get('shiprocket_checkout_last_test_at');
    $srcLastTestOk = filter_var(\App\Models\Setting::get('shiprocket_checkout_last_test_ok', 'false'), FILTER_VALIDATE_BOOLEAN);
    $srcLastError = \App\Models\Setting::get('shiprocket_checkout_last_error');
    $srcCatalogAt = \App\Models\Setting::get('shiprocket_checkout_last_catalog_fetch_at');
    $srcWebhookAt = \App\Models\Setting::get('shiprocket_checkout_last_webhook_at');
    $srcEnv = $src->environment();
    $srcAppHost = parse_url(config('app.url'), PHP_URL_HOST) ?: '';
    $srcIsPrivate = !str_starts_with((string) config('app.url'), 'https://') || in_array($srcAppHost, ['localhost', '127.0.0.1', '::1']) || str_ends_with($srcAppHost, '.test') || str_ends_with($srcAppHost, '.local');
    $srcConnected = $srcHasCreds && $srcLastTestOk;
    $srcOn = fn ($key) => filter_var(\App\Models\Setting::get($key, 'true'), FILTER_VALIDATE_BOOLEAN);
@endphp
<!-- Shiprocket Checkout Tab -->
<div class="tab-pane settings-tab-pane fade" id="shiprocket-checkout" role="tabpanel" aria-labelledby="shiprocket-checkout-tab">
    <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
        <h4 class="font-heading fw-bold text-success m-0"><i class="bi bi-lightning-charge me-2"></i>Shiprocket Checkout</h4>
        <span class="badge bg-{{ $srcEnabled && $srcHasCreds ? 'success' : 'secondary' }}">{{ $srcEnabled && $srcHasCreds ? 'ON' : 'OFF' }}</span>
    </div>

    <div class="alert alert-info border-0 rounded-3" style="font-size: 0.85rem;">
        <i class="bi bi-info-circle-fill me-1"></i>
        Fastrr one-click checkout. This is a <strong>separate integration</strong> from Shiprocket Shipping (Shipping &amp; Taxes tab):
        different credentials, different API. Orders placed here are created in this store and then handed to Shiprocket Shipping as usual.
    </div>

    {{-- ── Status ── --}}
    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-activity me-1"></i> Status</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.85rem;">
            <tbody>
                <tr><th class="bg-light w-50">Integration Status</th>
                    <td>{!! $srcConnected ? '<span class="text-success fw-bold">✓ Connected</span>' : '<span class="text-danger fw-bold">✗ Not Connected</span>' !!}</td></tr>
                <tr><th class="bg-light">Checkout Status</th>
                    <td>{!! $srcEnabled && $srcHasCreds ? '<span class="badge bg-success">ON</span>' : '<span class="badge bg-secondary">OFF</span>' !!}
                        @if ($srcEnabled && !$srcHasCreds) <small class="text-danger ms-1">Enabled, but API Key/Secret missing — native checkout is used.</small> @endif</td></tr>
                <tr><th class="bg-light">Catalog Status</th>
                    <td>{!! $srcCatalogAt ? '<span class="text-success fw-bold">Configured</span> <small class="text-muted">— last fetched by Shiprocket ' . e($srcCatalogAt) . '</small>' : '<span class="text-muted fw-bold">Not Configured</span> <small class="text-muted">— Shiprocket has not called the catalog API yet</small>' !!}</td></tr>
                <tr><th class="bg-light">Catalog Pushed</th>
                    <td>{{ \App\Models\Setting::get('shiprocket_checkout_last_catalog_push_at') ?: 'Never' }} <small class="text-muted">— via "Sync catalog to Shiprocket now"</small></td></tr>
                <tr><th class="bg-light">Webhook Status</th>
                    <td>{!! $srcWebhookAt ? '<span class="text-success fw-bold">Configured</span> <small class="text-muted">— last order webhook ' . e($srcWebhookAt) . '</small>' : '<span class="text-muted fw-bold">Not Configured</span> <small class="text-muted">— no order webhook received yet</small>' !!}</td></tr>
                <tr><th class="bg-light">Environment</th><td>{{ ucfirst($srcEnv) }} — <code>{{ $src->baseUrl() }}</code></td></tr>
                <tr><th class="bg-light">Last Connection Test</th>
                    <td>{{ $srcLastTestAt ?: 'Never' }} @if ($srcLastTestAt) {!! $srcLastTestOk ? '<span class="text-success">(passed)</span>' : '<span class="text-danger">(failed)</span>' !!} @endif</td></tr>
                <tr><th class="bg-light">Last Error</th><td class="text-break"><small>{{ $srcLastError ?: '—' }}</small></td></tr>
            </tbody>
        </table>
    </div>

    {{-- ── Settings ── --}}
    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-sliders me-1"></i> Settings</h6>
    <div class="card border rounded-3 p-3 bg-light mb-3">
        <div class="form-check form-switch mb-1">
            <input type="hidden" name="settings[shiprocket_checkout_enabled]" value="false">
            <input class="form-check-input" type="checkbox" name="settings[shiprocket_checkout_enabled]" value="true" id="shiprocketCheckoutEnabled" {{ $srcEnabled ? 'checked' : '' }}>
            <label class="form-check-label fw-bold text-dark" for="shiprocketCheckoutEnabled">Enable Shiprocket Checkout</label>
        </div>
        <small class="text-muted d-block mb-2">OFF = the native checkout works exactly as before.</small>
        <div class="d-flex flex-wrap gap-4 ps-1">
            <div class="form-check">
                <input type="hidden" name="settings[shiprocket_checkout_on_buy_now]" value="false">
                <input class="form-check-input" type="checkbox" name="settings[shiprocket_checkout_on_buy_now]" value="true" id="srcOnBuyNow" {{ $srcOn('shiprocket_checkout_on_buy_now') ? 'checked' : '' }}>
                <label class="form-check-label" for="srcOnBuyNow" style="font-size: 0.85rem;">Use on product "Buy Now"</label>
            </div>
            <div class="form-check">
                <input type="hidden" name="settings[shiprocket_checkout_on_cart]" value="false">
                <input class="form-check-input" type="checkbox" name="settings[shiprocket_checkout_on_cart]" value="true" id="srcOnCart" {{ $srcOn('shiprocket_checkout_on_cart') ? 'checked' : '' }}>
                <label class="form-check-label" for="srcOnCart" style="font-size: 0.85rem;">Use on cart "Checkout"</label>
            </div>
            <div class="form-check">
                <input type="hidden" name="settings[shiprocket_checkout_push_to_shipping]" value="false">
                <input class="form-check-input" type="checkbox" name="settings[shiprocket_checkout_push_to_shipping]" value="true" id="srcPushShipping" {{ $srcOn('shiprocket_checkout_push_to_shipping') ? 'checked' : '' }}>
                <label class="form-check-label" for="srcPushShipping" style="font-size: 0.85rem;">Send orders to Shiprocket Shipping</label>
            </div>
        </div>
    </div>

    @if ($srcEnvCreds)
        <div class="alert alert-warning border-0 rounded-3" style="font-size: 0.85rem;">
            <i class="bi bi-shield-lock-fill me-1"></i> API Key / Secret Key are coming from <strong>.env</strong> (<code>SHIPROCKET_CHECKOUT_API_KEY</code> / <code>SHIPROCKET_CHECKOUT_SECRET_KEY</code>) and override the fields below.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">API Key</label>
            <div class="input-group">
                <input type="password" name="settings[shiprocket_checkout_api_key]" id="srcApiKey" class="form-control bg-light border p-2" value="" autocomplete="new-password"
                       placeholder="{{ $srcHasSavedKey ? 'Saved (' . $src->maskedApiKey() . ') — leave blank to keep' : 'X-Api-Key from Shiprocket' }}">
                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1"><i class="bi bi-eye"></i></button>
            </div>
            @if ($srcHasSavedKey)
                <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="clear_secrets[shiprocket_checkout_api_key]" value="1" id="srcClearKey">
                    <label class="form-check-label text-muted" for="srcClearKey" style="font-size: 0.8rem;">Remove saved API Key</label></div>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Secret Key</label>
            <div class="input-group">
                <input type="password" name="settings[shiprocket_checkout_secret_key]" id="srcSecretKey" class="form-control bg-light border p-2" value="" autocomplete="new-password"
                       placeholder="{{ $srcHasSavedSecret ? 'Saved — leave blank to keep' : 'Secret used for HMAC signing' }}">
                <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1"><i class="bi bi-eye"></i></button>
            </div>
            @if ($srcHasSavedSecret)
                <div class="form-check mt-1"><input class="form-check-input" type="checkbox" name="clear_secrets[shiprocket_checkout_secret_key]" value="1" id="srcClearSecret">
                    <label class="form-check-label text-muted" for="srcClearSecret" style="font-size: 0.8rem;">Remove saved Secret Key</label></div>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Environment</label>
            <select name="settings[shiprocket_checkout_environment]" class="form-select bg-light border p-2">
                <option value="production" {{ \App\Models\Setting::get('shiprocket_checkout_environment', 'production') === 'production' ? 'selected' : '' }}>Production (checkout-api.shiprocket.com)</option>
                <option value="staging" {{ \App\Models\Setting::get('shiprocket_checkout_environment') === 'staging' ? 'selected' : '' }}>Staging (fastrr-api-dev.pickrr.com)</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Base URL <span class="text-muted fw-normal">(optional override)</span></label>
            <input type="url" name="settings[shiprocket_checkout_base_url]" class="form-control bg-light border p-2" value="{{ \App\Models\Setting::get('shiprocket_checkout_base_url') }}" placeholder="{{ config('services.shiprocket_checkout.environments.' . $srcEnv . '.base_url') }}">
            <small class="text-muted">Leave blank to use the Environment's official URL. Must be https.</small>
        </div>
        <div class="col-12">
            <small class="text-muted">Keys are stored encrypted and never shown again or sent to the browser. All HMAC signing happens on the server.</small>
        </div>
    </div>

    {{-- ── Test Connection ── --}}
    <div class="card border rounded-3 p-3 mt-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-success font-heading" id="btnTestShiprocketCheckout">
                <i class="bi bi-plug me-1"></i> Test Connection
            </button>
            <small class="text-muted">Makes a real signed call (Order List API). Uses the keys typed above if both are filled, otherwise the saved ones.</small>
        </div>
        <div id="shiprocketCheckoutTestResult" class="mt-3 d-none"></div>
    </div>

    {{-- ── Custom Endpoints ── --}}
    <h6 class="fw-bold text-dark mt-5 mb-2"><i class="bi bi-link-45deg me-1"></i> Custom Endpoints</h6>
    <p class="text-muted mb-2" style="font-size: 0.82rem;">Share these with Shiprocket (or enter them in the Shiprocket Checkout dashboard). Generated from <code>APP_URL</code> = <code>{{ config('app.url') }}</code>.</p>
    @if ($srcIsPrivate)
        <div class="alert alert-warning border-0 rounded-3 py-2" style="font-size: 0.82rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>APP_URL is not a public HTTPS address.</strong> Shiprocket's servers cannot reach localhost. For local testing, run a tunnel (e.g. <code>ngrok http 8000</code>), set <code>APP_URL</code> to the https tunnel URL, run <code>php artisan config:clear</code>, and use the URLs shown then.
        </div>
    @endif
    <div class="d-flex flex-column gap-2 mb-2">
        @foreach ($src->customEndpoints() as $label => $url)
            <div>
                <label class="form-label fw-semibold text-dark mb-1" style="font-size: 0.8rem;">{{ $label }}</label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control bg-light border font-monospace" value="{{ $url }}" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.innerHTML='<i class=&quot;bi bi-check2&quot;></i>'; setTimeout(() => this.innerHTML='<i class=&quot;bi bi-clipboard&quot;></i>', 1500);" title="Copy"><i class="bi bi-clipboard"></i></button>
                </div>
            </div>
        @endforeach
    </div>
    <button type="submit" form="srcSyncCatalogForm" class="btn btn-sm btn-success me-2" onclick="this.innerHTML='<span class=&quot;spinner-border spinner-border-sm me-1&quot;></span> Syncing...';">
        <i class="bi bi-cloud-upload me-1"></i> Sync catalog to Shiprocket now
    </button>
    <button type="submit" form="srcRegenerateWebhookForm" class="btn btn-sm btn-outline-danger" onclick="return confirm('Generate a new webhook URL? The old URL stops working immediately — you must update it with Shiprocket.');">
        <i class="bi bi-arrow-repeat me-1"></i> Regenerate webhook URL
    </button>

    {{-- ── Instructions ── --}}
    <h6 class="fw-bold text-dark mt-5 mb-3"><i class="bi bi-journal-text me-1"></i> Instructions / Setup Guide</h6>
    @php
        $srcSteps = [
            ['How Shiprocket Checkout is enabled', '
                <ol class="mb-0">
                    <li>Shiprocket Checkout for custom websites is activated by Shiprocket\'s team. Contact Shiprocket (your account manager / checkout sales) to onboard this website for API-based checkout.</li>
                    <li>Share the <strong>Catalog endpoints</strong> and your website domain (below) with them.</li>
                    <li>Shiprocket syncs your catalog, then issues your <strong>API Key</strong> and <strong>Secret Key</strong>.</li>
                </ol>'],
            ['Where API credentials come from', '
                <ul class="mb-0">
                    <li>The <strong>API Key</strong> (sent as <code>X-Api-Key</code>) and <strong>Secret Key</strong> (used to calculate <code>X-Api-HMAC-SHA256</code>) are generated and shared by Shiprocket after catalog sync.</li>
                    <li>Staging and Production keys are different — pick the matching <strong>Environment</strong>.</li>
                    <li>These are <em>not</em> your Shiprocket Shipping email/password.</li>
                </ul>'],
            ['Enter them in this Admin Panel', '
                <ol class="mb-0">
                    <li>Admin → Settings → <strong>Shiprocket Checkout</strong>.</li>
                    <li>Paste the API Key and Secret Key, choose the Environment, leave Base URL blank.</li>
                    <li>Click <strong>Save Configurations</strong>. Keys are encrypted; the fields stay blank afterwards ("Saved").</li>
                    <li>Alternatively set <code>SHIPROCKET_CHECKOUT_API_KEY</code> / <code>SHIPROCKET_CHECKOUT_SECRET_KEY</code> in the server <code>.env</code>.</li>
                </ol>'],
            ['How the catalog APIs work', '
                <ul class="mb-0">
                    <li>Shiprocket calls <strong>Fetch Products</strong>, <strong>Fetch Products by Collection</strong> and <strong>Fetch Collections</strong> (paginated with <code>page</code> &amp; <code>limit</code>) to import your catalog.</li>
                    <li>Data comes live from your existing products, variants, categories, prices, stock and images — nothing is duplicated.</li>
                    <li>Categories are sent as collections. Each product variant gets a stable Shiprocket <code>variant_id</code> (products without variants get one default variant).</li>
                    <li>When a product, variant, price, stock or category changes, this site automatically sends Shiprocket the documented product/collection update webhook (only while the integration is ON).</li>
                </ul>'],
            ['Custom Endpoints in the Shiprocket dashboard', '
                <p class="mb-1">Copy the URLs from <strong>Custom Endpoints</strong> above and give them to Shiprocket (or enter them wherever the Shiprocket Checkout dashboard asks for seller catalog APIs / order webhook). The redirect URL is sent automatically with every checkout.</p>
                <p class="mb-0 text-muted">They must be public HTTPS URLs — see Localhost requirements.</p>'],
            ['Configure the Order Webhook', '
                <ul class="mb-0">
                    <li>Register the <strong>Order Webhook</strong> URL above with Shiprocket. It contains a secret token — keep it private; use <em>Regenerate</em> if it leaks.</li>
                    <li>Every webhook is verified by fetching the order from Shiprocket\'s signed <strong>Order Details API</strong> before an order is created, and duplicates are ignored.</li>
                    <li>If a webhook is missed, the scheduled job <code>shiprocket-checkout:reconcile</code> (every 10 min) picks the order up. It needs the Laravel scheduler cron (see Production).</li>
                </ul>'],
            ['Test the connection', '
                <p class="mb-0">Click <strong>Test Connection</strong>. It sends a signed request to the Order List API. <span class="text-success">✓ Shiprocket Checkout connection successful.</span> means the API Key, Secret Key (HMAC) and Environment are correct. HTTP 511/401 means wrong keys or wrong environment.</p>'],
            ['Test a checkout', '
                <ol class="mb-0">
                    <li>Use <strong>Staging</strong> keys first if Shiprocket provided them.</li>
                    <li>Turn ON, save, then clear page cache (Dashboard → Clear Cache) so product pages load the checkout script.</li>
                    <li>Open a product → <strong>Buy Now</strong> (or Cart → <strong>Checkout Now</strong>). The Shiprocket Checkout popup should open.</li>
                    <li>Complete a COD order. You are redirected back to the order success page, the order appears in Admin → Orders (payment method <em>cod</em> or <em>shiprocket_checkout</em>) and is pushed to Shiprocket Shipping.</li>
                    <li>The <strong>Webhook Status</strong> above turns "Configured" once Shiprocket sends the first order webhook.</li>
                </ol>'],
            ['Localhost requirements', '
                <ul class="mb-0">
                    <li>Test Connection and the checkout popup work on <code>http://127.0.0.1:8000</code> (outgoing calls only).</li>
                    <li>Catalog APIs and the order webhook are called <em>by Shiprocket\'s servers</em>, which cannot reach localhost. Use a public HTTPS tunnel: <code>ngrok http 8000</code> → set <code>APP_URL=https://xxxx.ngrok-free.app</code> → <code>php artisan config:clear</code> → share the new Custom Endpoints.</li>
                    <li>Without a tunnel, orders are still created when you return to the site (the redirect verifies the order with the API).</li>
                    <li>If you see an SSL (cURL 60) error, set <code>curl.cainfo</code> in php.ini to a <code>cacert.pem</code>.</li>
                </ul>'],
            ['Production requirements', '
                <ul class="mb-0">
                    <li><code>APP_URL</code> must be your real <code>https://</code> domain; run <code>php artisan config:cache</code> after changing <code>.env</code>.</li>
                    <li>Run <code>php artisan migrate</code> once to create the Shiprocket Checkout tables.</li>
                    <li>Add the Laravel scheduler cron: <code>* * * * * php /path/to/artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code> (for the reconcile failsafe).</li>
                    <li>Outgoing HTTPS to <code>checkout-api.shiprocket.com</code> must be allowed. Keep <code>APP_KEY</code> unchanged (saved keys are encrypted with it).</li>
                    <li>Never put the keys in <code>VITE_*</code> variables or commit <code>.env</code>.</li>
                </ul>'],
            ['Troubleshooting', '
                <table class="table table-sm table-bordered bg-white mb-0">
                    <thead><tr><th>Problem</th><th>Solution</th></tr></thead>
                    <tbody>
                        <tr><td>HTTP 511 / 401</td><td>Wrong API Key or Secret Key, or staging keys used on Production (or vice-versa).</td></tr>
                        <tr><td>HTTP 404</td><td>Wrong Base URL — clear the override and use the Environment default.</td></tr>
                        <tr><td>Popup doesn\'t open, native checkout opens</td><td>Integration OFF / keys missing / token error (see Last Error). Clear page cache after turning ON.</td></tr>
                        <tr><td>Catalog Status "Not Configured"</td><td>Shiprocket hasn\'t fetched your catalog — confirm the URLs are public HTTPS and shared with Shiprocket.</td></tr>
                        <tr><td>Order paid but not in Admin</td><td>Check Last Error; make sure the webhook URL is registered and the scheduler cron runs; run <code>php artisan shiprocket-checkout:reconcile</code>.</td></tr>
                        <tr><td>"unknown variant_id" in log</td><td>Shiprocket has an old catalog — ask them to re-sync from the catalog APIs.</td></tr>
                        <tr><td>Duplicate shipments</td><td>If Shiprocket already creates the shipment on its side, untick "Send orders to Shiprocket Shipping".</td></tr>
                        <tr><td>Keys stopped working after deploy</td><td><code>APP_KEY</code> changed — re-enter the keys.</td></tr>
                    </tbody>
                </table>'],
            ['Wallet / balance', '
                <p class="mb-0">Shiprocket Checkout charges (and Shiprocket Shipping freight for the shipments created afterwards) are billed by Shiprocket to your account. Keep your Shiprocket wallet recharged and confirm the checkout pricing/billing with Shiprocket — a low balance can block shipment creation.</p>'],
            ['Switch back to the native checkout', '
                <p class="mb-0">Untick <strong>Enable Shiprocket Checkout</strong> (or just "Buy Now" / "Checkout") and Save. The server stops issuing checkout tokens immediately, so every button falls back to the native checkout even on cached pages. Orders already placed through Shiprocket Checkout keep syncing via the webhook.</p>'],
        ];
    @endphp
    <div class="accordion" id="srcGuide" style="font-size: 0.85rem;">
        @foreach ($srcSteps as $i => [$title, $body])
            <div class="accordion-item">
                <h2 class="accordion-header" id="srcGuideH{{ $i }}">
                    <button class="accordion-button collapsed py-2 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#srcGuideC{{ $i }}" aria-expanded="false" aria-controls="srcGuideC{{ $i }}" style="font-size: 0.85rem;">
                        <span class="badge bg-success rounded-pill me-2">{{ $i + 1 }}</span> {{ $title }}
                    </button>
                </h2>
                <div id="srcGuideC{{ $i }}" class="accordion-collapse collapse" aria-labelledby="srcGuideH{{ $i }}" data-bs-parent="#srcGuide">
                    <div class="accordion-body">{!! $body !!}</div>
                </div>
            </div>
        @endforeach
    </div>
    <p class="text-muted mt-3 mb-0" style="font-size: 0.78rem;">Based on the official Shiprocket Checkout integration guide and API documentation (documenter.getpostman.com/view/25617008/2sB34bL3ig). Developer notes: <code>docs/shiprocket-checkout.md</code>.</p>
</div>
