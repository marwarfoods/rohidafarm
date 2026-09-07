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
        
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">GSTIN Number</label>
            <input type="text" name="settings[gst_number]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('gst_number', '08FPRPR1916D2ZJ') }}" placeholder="e.g. 08FPRPR1916D2ZJ">
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
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Advance Payment Required for COD (₹)</label>
            <input type="number" step="0.01" name="settings[cod_advance_amount]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('cod_advance_amount', 0) }}">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Discount for Online Payment (%)</label>
            <input type="number" step="0.01" name="settings[online_discount_percent]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('online_discount_percent', 0) }}">
        </div>
    </div>

    {{-- ── Shiprocket Courier Integration ── --}}
    <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3 mt-4">
        <i class="bi bi-truck me-1"></i> Shiprocket Integration
    </h4>
    <div class="row g-3">
        <div class="col-md-12 mb-1">
            <div class="form-check form-switch mt-1">
                <input type="hidden" name="settings[shiprocket_enabled]" value="false">
                <input class="form-check-input" type="checkbox" name="settings[shiprocket_enabled]" value="true" id="shiprocketEnabled" {{ filter_var(App\Models\Setting::get('shiprocket_enabled', 'false'), FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold text-dark" for="shiprocketEnabled">Enable Shiprocket (push new orders to Shiprocket)</label>
            </div>
            <small class="text-muted d-block mt-1">Use your Shiprocket API user credentials (Settings → API → Configure in Shiprocket). Testing requires HTTPS — run it on the live site.</small>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Shiprocket Account Email</label>
            <input type="email" name="settings[shiprocket_email]" id="shiprocketEmail" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_email') }}" placeholder="api-user@yourbrand.com">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Shiprocket Password</label>
            <input type="password" name="settings[shiprocket_password]" id="shiprocketPassword" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_password') }}" placeholder="••••••••" autocomplete="new-password">
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Pickup Location Nickname</label>
            <input type="text" name="settings[shiprocket_pickup_location]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_pickup_location', 'Primary') }}" placeholder="Exactly as saved in Shiprocket (e.g. Primary)">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Channel ID <span class="text-muted">(optional)</span></label>
            <input type="text" name="settings[shiprocket_channel_id]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_channel_id') }}" placeholder="Custom / API channel ID">
        </div>

        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Default Weight (kg)</label>
            <input type="number" step="0.01" name="settings[shiprocket_default_weight]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_default_weight', 0.5) }}">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Package L (cm)</label>
            <input type="number" step="0.1" name="settings[shiprocket_pkg_length]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_pkg_length', 15) }}">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Package B (cm)</label>
            <input type="number" step="0.1" name="settings[shiprocket_pkg_breadth]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_pkg_breadth', 12) }}">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Package H (cm)</label>
            <input type="number" step="0.1" name="settings[shiprocket_pkg_height]" class="form-control bg-light border p-2" value="{{ App\Models\Setting::get('shiprocket_pkg_height', 8) }}">
        </div>

        <div class="col-12">
            <button type="button" class="btn btn-outline-success font-heading" id="btnTestShiprocket">
                <i class="bi bi-plug me-1"></i> Test Configuration
            </button>
            <small class="text-muted d-block mt-2">Save your settings first. This authenticates with Shiprocket and checks the pickup location so you know orders will go through.</small>
            <div id="shiprocketTestResult" class="mt-3 alert d-none"></div>
        </div>
    </div>
</div>
