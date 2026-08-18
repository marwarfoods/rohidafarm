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
