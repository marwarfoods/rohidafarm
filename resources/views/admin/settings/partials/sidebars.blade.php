<!-- Dynamic Diagnostic Sidebars -->
<!-- Site Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card active" id="sidebar-site">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-display text-primary me-2"></i>Site Settings Guide</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Configure your brand's core visual identity and contact information here.</p>
    <ul class="text-muted ps-3 mt-3" style="font-size: 0.8rem; line-height:1.8;">
        <li><strong>Logos:</strong> Upload high-quality PNGs with transparent backgrounds.</li>
        <li><strong>Favicon:</strong> Should be a square image (e.g. 32x32px or 64x64px).</li>
        <li><strong>Contact Info:</strong> This will be automatically reflected in the website header and footer.</li>
    </ul>
</div>

<!-- SMTP Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-smtp">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-envelope-check text-success me-2"></i>SMTP Diagnostic Tools</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Use this tool to dispatch a test email message to verify your SMTP protocol credentials in real time.</p>
    
    <form action="{{ route('admin.settings.smtp.test') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="test_email" class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Recipient Email Address *</label>
            <input type="email" name="test_email" class="form-control bg-light border p-2" placeholder="diagnostic@example.com" required>
        </div>
        <button type="submit" class="btn btn-premium-outline w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;"><i class="bi bi-send me-1"></i> Send HTML Test Template</button>
    </form>
</div>

<!-- SEO Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-seo">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-google text-primary me-2"></i>SEO Live Preview</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">This is how your homepage will likely appear on Google search results.</p>
    
    <div class="seo-preview-card mt-3">
        <div class="seo-preview-url">{{ url('/') }} <i class="bi bi-three-dots-vertical text-muted ms-1" style="font-size: 12px;"></i></div>
        <div class="seo-preview-title" id="seo_preview_title">Title</div>
        <div class="seo-preview-desc" id="seo_preview_desc">Description</div>
    </div>
</div>

<!-- Payments Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-payments">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-credit-card-2-front text-success me-2"></i>Payment Gateways</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Ensure you use your <strong>Sandbox/Test</strong> API keys here. Do not enter live production keys in these fields.</p>
    
    <div class="bg-light p-3 rounded-3 border mt-3">
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-check-circle-fill text-success me-2"></i>
            <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Razorpay Test Mode</span>
        </div>
    </div>
</div>

<!-- Shipping Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-shipping">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-truck text-success me-2"></i>Shipping & Taxes Rules</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Configure delivery locations and applicable taxes dynamically.</p>
    
    <ul class="text-muted ps-3 mt-3" style="font-size: 0.8rem; line-height:1.8;">
        <li><strong>Tax Mode:</strong> Flat tax applies equally. State-wise requires a JSON map.</li>
        <li><strong>Blocked Pincodes:</strong> Users from these pincodes will be prevented from placing orders.</li>
        <li><strong>Discounts/Charges:</strong> Add a small extra fee to incentivize Online payments, or apply a discount.</li>
    </ul>
</div>

<!-- Integrations Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-integrations">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-boxes text-success me-2"></i>Integration Status</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Manage your third-party connections. If Delhivery credentials are blank, the system will use simulated sandbox mode.</p>
    <div class="bg-light p-3 rounded-3 border mt-3 text-center">
        <span class="badge bg-{{ App\Models\Setting::get('delhivery_api_token') ? 'success' : 'warning text-dark' }} w-100 p-2 mb-2">Delhivery: {{ App\Models\Setting::get('delhivery_api_token') ? 'Connected' : 'Sandbox Mode' }}</span>
        <span class="badge bg-{{ App\Models\Setting::get('google_analytics_id') ? 'success' : 'secondary' }} w-100 p-2 mb-2">Google Analytics: {{ App\Models\Setting::get('google_analytics_id') ? 'Active' : 'Inactive' }}</span>
        <span class="badge bg-{{ App\Models\Setting::get('meta_pixel_enabled') && (App\Models\Setting::get('meta_pixel_id') || App\Models\Setting::get('meta_pixel_code')) ? 'success' : 'secondary' }} w-100 p-2 mb-2">Meta Pixel: {{ App\Models\Setting::get('meta_pixel_enabled') && (App\Models\Setting::get('meta_pixel_id') || App\Models\Setting::get('meta_pixel_code')) ? 'Active & Tracking' : 'Disabled' }}</span>
        <span class="badge bg-{{ App\Models\Setting::get('turnstile_enabled') && App\Models\Setting::get('turnstile_site_key') ? 'success' : 'secondary' }} w-100 p-2">Cloudflare Turnstile: {{ App\Models\Setting::get('turnstile_enabled') && App\Models\Setting::get('turnstile_site_key') ? 'Active & Protecting' : 'Disabled' }}</span>
    </div>
</div>

<!-- Auth & Google OAuth Panel -->
<div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-auth">
    <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-google text-danger me-2"></i>Google OAuth Guide</h5>
    <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Allow visitors to register and log in instantly with their Google Account.</p>
    <div class="bg-light p-3 rounded-3 border mb-3 text-center">
        <span class="badge bg-{{ \App\Models\Setting::get('google_login_enabled') && \App\Models\Setting::get('google_client_id') ? 'success' : 'secondary' }} w-100 p-2">
            Google Login: {{ \App\Models\Setting::get('google_login_enabled') && \App\Models\Setting::get('google_client_id') ? 'Enabled & Configured' : 'Disabled / Incomplete' }}
        </span>
    </div>
    <ul class="text-muted ps-3 mt-2" style="font-size: 0.8rem; line-height:1.8;">
        <li>Go to <a href="https://console.cloud.google.com" target="_blank" class="text-success fw-bold">Google Cloud Console</a>.</li>
        <li>Create OAuth 2.0 Credentials (Web Application).</li>
        <li>Copy the <strong>Authorized Redirect URI</strong> from the form and paste it into Google Console.</li>
        <li>Paste your <strong>Client ID</strong> and <strong>Client Secret</strong> into the fields on the left and click <strong>Test Connection</strong>.</li>
    </ul>
</div>
