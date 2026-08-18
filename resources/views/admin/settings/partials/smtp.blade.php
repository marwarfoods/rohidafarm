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
