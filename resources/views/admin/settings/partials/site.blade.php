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
