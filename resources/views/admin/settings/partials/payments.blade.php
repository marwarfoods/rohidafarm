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
