@extends('layouts.admin')

@push('admin_styles')
    <link rel="stylesheet" href="{{ asset('admin/css/settings.css') }}">
    <style>
        .settings-tab-link { color: #333 !important; font-weight: 500; }
        .settings-tab-link.active { color: #000 !important; background-color: #e8f5e9 !important; font-weight: 600; }
        .settings-tab-link i { color: #555 !important; }
        .settings-tab-link.active i { color: #000 !important; }
    </style>
@endpush

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-gear text-success me-2"></i>Dynamic Configurations</h1>
</div>

<div class="row g-4">
    <!-- Left Column: Navigation Sidebar -->
    <div class="col-lg-3">
        <div class="position-sticky bg-white p-3 rounded-4 shadow-sm border mb-4" style="top: 30px; border-color: var(--border-color) !important;">
            <h5 class="font-heading fw-bold text-dark border-bottom pb-3 mb-3 ps-2">Settings Menu</h5>
            <ul class="nav nav-pills flex-column gap-2" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link active" id="site-tab" href="#site" role="tab" aria-controls="site" aria-selected="true">
                        <i class="bi bi-display me-2"></i>Site Settings
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="smtp-tab" href="#smtp" role="tab" aria-controls="smtp" aria-selected="false">
                        <i class="bi bi-envelope-at me-2"></i>SMTP Credentials
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="seo-tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">
                        <i class="bi bi-search me-2"></i>SEO Parameters
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="payments-tab" href="#payments" role="tab" aria-controls="payments" aria-selected="false">
                        <i class="bi bi-credit-card-2-front me-2"></i>Payment Gateways
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="shipping-tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="false">
                        <i class="bi bi-truck me-2"></i>Shipping & Taxes
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="integrations-tab" href="#integrations" role="tab" aria-controls="integrations" aria-selected="false">
                        <i class="bi bi-boxes me-2"></i>Integrations
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link settings-tab-link" id="auth-tab" href="#auth" role="tab" aria-controls="auth" aria-selected="false">
                        <i class="bi bi-shield-lock me-2"></i>Authentication & Google OAuth
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Middle Column: Tabbed Forms -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <form action="{{ route('admin.settings.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="tab-content" id="settingsTabContent">
                    
                    <!-- Site Settings Group Tab -->
                    <div class="tab-pane settings-tab-pane fade show active" id="site" role="tabpanel" aria-labelledby="site-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Site Branding & Contacts</h4>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Main Header Logo</label>
                                <input type="text" name="settings[main_logo]" id="mainLogoInput" class="form-control bg-light border p-2 mb-2 cursor-pointer" placeholder="Choose from gallery..." value="{{ \App\Models\Setting::get('main_logo') }}" readonly>
                                <div class="border rounded-3 p-2 bg-white text-center d-flex align-items-center justify-content-center" style="height: 80px;">
                                    <img id="mainLogoPreview" src="{{ \App\Models\Setting::get('main_logo') ? asset(\App\Models\Setting::get('main_logo')) : '' }}" class="img-fluid rounded-2 mx-auto" style="max-height: 60px; object-fit: contain; {{ \App\Models\Setting::get('main_logo') ? '' : 'display: none;' }}">
                                    @if(!\App\Models\Setting::get('main_logo'))
                                        <small class="text-muted placeholder-text">No logo selected</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Footer Logo</label>
                                <input type="text" name="settings[footer_logo]" id="footerLogoInput" class="form-control bg-light border p-2 mb-2 cursor-pointer" placeholder="Choose from gallery..." value="{{ \App\Models\Setting::get('footer_logo') }}" readonly>
                                <div class="border rounded-3 p-2 bg-dark text-center d-flex align-items-center justify-content-center" style="height: 80px;">
                                    <img id="footerLogoPreview" src="{{ \App\Models\Setting::get('footer_logo') ? asset(\App\Models\Setting::get('footer_logo')) : '' }}" class="img-fluid rounded-2 mx-auto" style="max-height: 60px; object-fit: contain; filter: brightness(0) invert(1); {{ \App\Models\Setting::get('footer_logo') ? '' : 'display: none;' }}">
                                    @if(!\App\Models\Setting::get('footer_logo'))
                                        <small class="text-muted placeholder-text">No logo selected</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Favicon</label>
                                <input type="text" name="settings[favicon]" id="faviconInput" class="form-control bg-light border p-2 mb-2 cursor-pointer" placeholder="Choose from gallery..." value="{{ \App\Models\Setting::get('favicon') }}" readonly>
                                <div class="border rounded-3 p-2 bg-white text-center d-flex align-items-center justify-content-center" style="height: 80px;">
                                    <img id="faviconPreview" src="{{ \App\Models\Setting::get('favicon') ? asset(\App\Models\Setting::get('favicon')) : '' }}" class="img-fluid rounded-2 mx-auto" style="max-height: 32px; object-fit: contain; {{ \App\Models\Setting::get('favicon') ? '' : 'display: none;' }}">
                                    @if(!\App\Models\Setting::get('favicon'))
                                        <small class="text-muted placeholder-text">No favicon</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Primary Mobile</label>
                                <input type="text" name="settings[contact_mobile_1]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('contact_mobile_1') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Secondary Mobile</label>
                                <input type="text" name="settings[contact_mobile_2]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('contact_mobile_2') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Primary Email</label>
                                <input type="email" name="settings[contact_email_1]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('contact_email_1') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Secondary Email</label>
                                <input type="email" name="settings[contact_email_2]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('contact_email_2') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Physical Address</label>
                                <textarea name="settings[contact_address]" class="form-control bg-light border p-2" rows="2">{{ App\Models\Setting::get('contact_address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Facebook URL</label>
                                <input type="url" name="settings[social_facebook]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('social_facebook') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Instagram URL</label>
                                <input type="url" name="settings[social_instagram]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('social_instagram') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">YouTube URL</label>
                                <input type="url" name="settings[social_youtube]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('social_youtube') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Twitter URL</label>
                                <input type="url" name="settings[social_twitter]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('social_twitter') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">LinkedIn URL</label>
                                <input type="url" name="settings[social_linkedin]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('social_linkedin') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- SMTP Group Tab -->
                    <div class="tab-pane settings-tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">SMTP Configuration</h4>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Protocol Mailer</label>
                                <input type="text" name="settings[mail_mailer]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_mailer') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">SMTP Host</label>
                                <input type="text" name="settings[mail_host]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_host') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Port</label>
                                <input type="text" name="settings[mail_port]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_port') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Username</label>
                                <input type="text" name="settings[mail_username]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_username') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Password</label>
                                <div class="input-group">
                                    <input type="password" name="settings[mail_password]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_password') }}">
                                    <button class="btn bg-white border border-start-0 toggle-password text-muted" type="button" style="border-color: #dee2e6 !important;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Encryption Scheme</label>
                                <input type="text" name="settings[mail_encryption]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_encryption') }}">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Sender Email (From Address)</label>
                                <input type="text" name="settings[mail_from_address]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('mail_from_address') }}">
                            </div>
                        </div>
                    </div>

                    <!-- SEO Group Tab -->
                    <div class="tab-pane settings-tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">SEO Parameters</h4>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Meta Title Header Tag</label>
                                <input type="text" id="meta_title_input" name="settings[meta_title]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('meta_title') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Meta Keywords (Comma separated)</label>
                                <input type="text" name="settings[meta_keywords]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('meta_keywords') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Meta Description Tag</label>
                                <textarea id="meta_desc_input" name="settings[meta_description]" class="form-control bg-light border p-2" rows="4">{{ App\Models\Setting::get('meta_description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Gateways Group Tab -->
                    <div class="tab-pane settings-tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Payment Gateways Sandbox Mode</h4>
                        <div class="row g-3">
                            <div class="col-12 mb-2">
                                <div class="form-check form-switch mt-2">
                                    <input type="hidden" name="settings[enable_razorpay]" value="false">
                                    <input class="form-check-input" type="checkbox" name="settings[enable_razorpay]" value="true" id="enableRazorpay" {{ App\Models\Setting::get('enable_razorpay') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="enableRazorpay">Enable Razorpay Checkout Option</label>
                                </div>
                                <div class="form-check form-switch mt-2">
                                    <input type="hidden" name="settings[enable_cod]" value="false">
                                    <input class="form-check-input" type="checkbox" name="settings[enable_cod]" value="true" id="enableCod" {{ App\Models\Setting::get('enable_cod', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="enableCod">Enable Cash On Delivery (COD)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark d-flex align-items-center" style="font-size: 0.85rem;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/89/Razorpay_logo.svg" height="14" class="me-2" alt="Razorpay"> Sandbox Key ID
                                </label>
                                <div class="input-group">
                                    <input type="text" name="settings[razorpay_key_id]" id="rzp_key_input" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('razorpay_key_id') }}">
                                    <button type="button" class="btn btn-outline-success font-heading" data-bs-toggle="modal" data-bs-target="#razorpayTestModal">
                                        <i class="bi bi-play-circle me-1"></i> Test
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark d-flex align-items-center" style="font-size: 0.85rem;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/89/Razorpay_logo.svg" height="14" class="me-2" alt="Razorpay"> Secret Key
                                </label>
                                <div class="input-group">
                                    <input type="password" name="settings[razorpay_secret_key]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('razorpay_secret_key') }}">
                                    <button class="btn bg-white border border-start-0 toggle-password text-muted" type="button" style="border-color: #dee2e6 !important;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping & Taxes Tab -->
                    <div class="tab-pane settings-tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Shipping & Taxes Configuration</h4>
                        <div class="row g-3">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch mt-2">
                                    <input type="hidden" name="settings[gst_enabled]" value="false">
                                    <input class="form-check-input" type="checkbox" name="settings[gst_enabled]" value="true" id="gstEnabled" {{ filter_var(App\Models\Setting::get('gst_enabled', 'false'), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark" for="gstEnabled">Enable GST Calculation</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Tax Mode</label>
                                <select name="settings[tax_mode]" class="form-select bg-light border p-2" id="taxModeSelect">
                                    <option value="all_india" {{ App\Models\Setting::get('tax_mode', 'all_india') == 'all_india' ? 'selected' : '' }}>All over India (Flat)</option>
                                    <option value="state_wise" {{ App\Models\Setting::get('tax_mode') == 'state_wise' ? 'selected' : '' }}>State-wise (JSON Map)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3" id="taxAllIndiaContainer" style="{{ App\Models\Setting::get('tax_mode', 'all_india') == 'all_india' ? '' : 'display: none;' }}">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">All India GST Percentage (%)</label>
                                <input type="number" step="0.1" name="settings[tax_all_india_percent]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('tax_all_india_percent', 5) }}">
                            </div>

                            <div class="col-md-12 mb-3" id="taxStateWiseContainer" style="{{ App\Models\Setting::get('tax_mode') == 'state_wise' ? '' : 'display: none;' }}">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">State-wise GST Percentage (JSON Map)</label>
                                <textarea name="settings[tax_state_wise]" class="form-control bg-light border p-2" rows="3" placeholder='{"Maharashtra": 5, "Gujarat": 12, "Rajasthan": 5}'>{{ is_array(App\Models\Setting::get('tax_state_wise')) ? json_encode(App\Models\Setting::get('tax_state_wise')) : App\Models\Setting::get('tax_state_wise') }}</textarea>
                                <small class="text-muted d-block mt-1">Example: <code>{"Maharashtra": 5, "Gujarat": 12}</code></small>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Blocked Pincodes (Comma separated)</label>
                                <textarea name="settings[blocked_pincodes]" class="form-control bg-light border p-2" rows="2" placeholder="e.g. 302012, 400001">{{ App\Models\Setting::get('blocked_pincodes') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Extra Charge for COD (₹)</label>
                                <input type="number" step="0.01" name="settings[cod_extra_charge]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('cod_extra_charge', 0) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Discount for Online Payment (%)</label>
                                <input type="number" step="0.01" name="settings[online_discount_percent]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('online_discount_percent', 0) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Integrations Tab -->
                    <div class="tab-pane settings-tab-pane fade" id="integrations" role="tabpanel" aria-labelledby="integrations-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Third-Party Integrations</h4>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                                <i class="bi bi-truck text-danger me-2" style="font-size: 1.3rem;"></i> Delhivery Settings
                                <i class="bi bi-question-circle text-primary ms-2 cursor-pointer" data-bs-toggle="modal" data-bs-target="#delhiveryHelpModal" style="font-size: 1.1rem;" title="How to get Delhivery API details?"></i>
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Delhivery API Token</label>
                                    <div class="input-group">
                                        <input type="password" name="settings[delhivery_api_token]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('delhivery_api_token') }}">
                                        <button class="btn bg-white border border-start-0 toggle-password text-muted" type="button" style="border-color: #dee2e6 !important;">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Pickup Location Name</label>
                                    <input type="text" name="settings[delhivery_pickup_location]" class="form-control bg-light border p-2" placeholder="e.g. RohidaFarm Warehouse" value="{{ App\Models\Setting::get('delhivery_pickup_location') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Client Name (Registered on Delhivery)</label>
                                    <input type="text" name="settings[delhivery_client_name]" class="form-control bg-light border p-2" placeholder="Defaults to Pickup Location Name if blank" value="{{ App\Models\Setting::get('delhivery_client_name') }}">
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">Connecting your account allows automatic syncing of real orders to Delhivery for fulfillment.</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div>
                            <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                                <img src="https://play-lh.googleusercontent.com/-CrW3g5AXNgw4DxSqCC46AMFQ1JRTz5YjOSn1xfllkxMml6KpR3enY7MfVhcq8vDGVSUsr76Ca-89y3GocaS" height="24" class="me-2" alt="Google Analytics"> 
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
                    </div>

                    <!-- Authentication & Google OAuth Group Tab -->
                    <div class="tab-pane settings-tab-pane fade" id="auth" role="tabpanel" aria-labelledby="auth-tab">
                        <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Google OAuth & Social Authentication</h4>
                        <p class="text-muted fs-6 mb-4">Configure Google Social Login credentials to allow customers to log in using their Google accounts seamlessly.</p>

                        <div class="card border rounded-3 p-3 bg-light mb-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="settings[google_login_enabled]" value="true" id="googleLoginSwitch" {{ \App\Models\Setting::get('google_login_enabled') ? 'checked' : '' }}>
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

                </div>

                <div class="border-top pt-4 mt-5">
                    <button type="submit" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-save me-2"></i>Save Configurations</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Dynamic Diagnostic Panels -->
    <div class="col-lg-3">
        <div class="position-sticky" style="top: 30px;">
            
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
            </div>

            <!-- Integrations Panel -->
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white dynamic-sidebar-card" id="sidebar-integrations">
                <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-boxes text-success me-2"></i>Integration Status</h5>
                <p class="text-muted" style="font-size: 0.85rem; line-height:1.6;">Manage your third-party connections. If Delhivery credentials are blank, the system will use simulated sandbox mode.</p>
                <div class="bg-light p-3 rounded-3 border mt-3 text-center">
                    <span class="badge bg-{{ App\Models\Setting::get('delhivery_api_token') ? 'success' : 'warning text-dark' }} w-100 p-2 mb-2">Delhivery: {{ App\Models\Setting::get('delhivery_api_token') ? 'Connected' : 'Sandbox Mode' }}</span>
                    <span class="badge bg-{{ App\Models\Setting::get('google_analytics_id') ? 'success' : 'secondary' }} w-100 p-2">Google Analytics: {{ App\Models\Setting::get('google_analytics_id') ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Delhivery Help Modal -->
<div class="modal fade" id="delhiveryHelpModal" tabindex="-1" aria-labelledby="delhiveryHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="delhiveryHelpModalLabel">
                    <i class="bi bi-box-seam text-primary me-2"></i>How to connect Delhivery?
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ol class="text-muted" style="font-size: 0.9rem; line-height: 1.8;">
                    <li>Log in to your <strong>Delhivery One / Partner Portal</strong> account.</li>
                    <li>Go to <strong>Settings &gt; API Center</strong> (or contact your Delhivery account manager to enable API access).</li>
                    <li>Generate/copy your <strong>API Token</strong> (a long alphanumeric key).</li>
                    <li>Note the <strong>Pickup Location Name</strong> you registered with Delhivery for your warehouse.</li>
                    <li>Note your registered <strong>Client Name</strong> (used to generate waybills); leave blank to reuse the Pickup Location Name.</li>
                    <li>Paste the Token, Pickup Location Name and Client Name into the settings fields here.</li>
                    <li>Click <strong>Save Configurations</strong>. Future orders will automatically sync!</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Google Analytics Help Modal -->
<div class="modal fade" id="gaHelpModal" tabindex="-1" aria-labelledby="gaHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="gaHelpModalLabel">
                    <i class="bi bi-graph-up text-primary me-2"></i>How to get GA4 Measurement ID?
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <ol class="text-muted" style="font-size: 0.9rem; line-height: 1.8;">
                    <li>Log in to your <strong>Google Analytics</strong> account.</li>
                    <li>Click on <strong>Admin</strong> (gear icon) at the bottom left.</li>
                    <li>Under the Property column, click on <strong>Data Streams</strong>.</li>
                    <li>Select your website's data stream. (Create one if none exists).</li>
                    <li>Look for the <strong>Measurement ID</strong> at the top right. It starts with <code>G-</code> (e.g., <code>G-1234567890</code>).</li>
                    <li>Copy that ID and paste it here in the settings.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Test Modal -->
<div class="modal fade" id="razorpayTestModal" tabindex="-1" aria-labelledby="razorpayTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="razorpayTestModalLabel">
                    <i class="bi bi-play-circle text-success me-2"></i>Test Razorpay
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light text-center">
                <p class="text-muted mb-3" style="font-size: 0.85rem;">Enter a test amount in INR to verify the Sandbox Key.</p>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white">₹</span>
                    <input type="number" id="rzp_test_amount" class="form-control" placeholder="Amount" value="100">
                </div>
                <button type="button" id="btn_rzp_test_pay" class="btn btn-success w-100 rounded-pill fw-bold text-uppercase" style="background-color: var(--primary-green) !important; border-color: var(--primary-green) !important;">
                    Pay Now
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin_scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });
            const taxModeSelect = document.getElementById('taxModeSelect');
            if (taxModeSelect) {
                taxModeSelect.addEventListener('change', function() {
                    if (this.value === 'all_india') {
                        document.getElementById('taxAllIndiaContainer').style.display = 'block';
                        document.getElementById('taxStateWiseContainer').style.display = 'none';
                    } else {
                        document.getElementById('taxAllIndiaContainer').style.display = 'none';
                        document.getElementById('taxStateWiseContainer').style.display = 'block';
                    }
                });
            }

            const btnPay = document.getElementById('btn_rzp_test_pay');
            if(btnPay) {
                btnPay.addEventListener('click', function() {
                    const keyInput = document.getElementById('rzp_key_input').value;
                    const amountInput = document.getElementById('rzp_test_amount').value;
                    
                    if(!keyInput) {
                        alert('Please enter a Razorpay Sandbox Key ID first!');
                        return;
                    }
                    if(!amountInput || amountInput <= 0) {
                        alert('Please enter a valid amount.');
                        return;
                    }

                    var options = {
                        "key": keyInput,
                        "amount": amountInput * 100, // Amount is in currency subunits (paise)
                        "currency": "INR",
                        "name": "RohidaFarm Sandbox",
                        "description": "Test Transaction",
                        "image": "{{ asset(\App\Models\Setting::get('favicon') ?: 'favicon.ico') }}",
                        "handler": function (response){
                            alert('Success! Payment ID: ' + response.razorpay_payment_id);
                            const modalEl = document.getElementById('razorpayTestModal');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if(modal) modal.hide();
                        },
                        "prefill": {
                            "name": "Admin Tester",
                            "email": "admin@rohidafarm.com",
                            "contact": "9999999999"
                        },
                        "theme": {
                            "color": "#2c6e49"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.on('payment.failed', function (response){
                        alert('Payment Failed: ' + response.error.description);
                    });
                    rzp1.open();
                });
            }

            const btnTestGoogle = document.getElementById('btnTestGoogleOAuth');
            if (btnTestGoogle) {
                btnTestGoogle.addEventListener('click', function () {
                    const clientId = document.getElementById('inputGoogleClientId').value.trim();
                    const clientSecret = document.getElementById('inputGoogleClientSecret').value.trim();
                    const resultDiv = document.getElementById('googleTestResult');

                    btnTestGoogle.disabled = true;
                    btnTestGoogle.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';
                    resultDiv.className = 'mt-3 alert alert-info';
                    resultDiv.classList.remove('d-none');
                    resultDiv.textContent = 'Validating configuration...';

                    fetch('{{ route("admin.settings.google.test") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            google_client_id: clientId,
                            google_client_secret: clientSecret
                        })
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(res => {
                        btnTestGoogle.disabled = false;
                        btnTestGoogle.innerHTML = '<i class="bi bi-patch-check me-1"></i> Test Connection';
                        if (res.status === 200 && res.body.status === 'success') {
                            resultDiv.className = 'mt-3 alert alert-success fw-bold';
                            resultDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>' + res.body.message;
                        } else {
                            resultDiv.className = 'mt-3 alert alert-danger fw-bold';
                            resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i>' + (res.body.message || 'Validation failed.');
                        }
                    })
                    .catch(err => {
                        btnTestGoogle.disabled = false;
                        btnTestGoogle.innerHTML = '<i class="bi bi-patch-check me-1"></i> Test Connection';
                        resultDiv.className = 'mt-3 alert alert-danger fw-bold';
                        resultDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i> Connection test failed.';
                    });
                });
            }
        });
    </script>
@endpush
