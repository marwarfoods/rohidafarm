<div class="card border-0 rounded-4 shadow-sm bg-white">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-heading fw-bold text-dark"><i class="bi bi-image text-success me-2"></i>Core Values Section Image</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.homepage.section-image.update') }}" method="POST">
            @csrf
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Desktop Section Image (e.g. 1920x400) <span class="text-danger">*</span></label>
                    <input type="text" name="home_section_image_pc" id="sectionImagePcInput" class="form-control @error('home_section_image_pc') is-invalid @enderror" value="{{ old('home_section_image_pc', \App\Models\Setting::get('home_section_image_pc')) }}" placeholder="Choose desktop image..." readonly required>
                    @error('home_section_image_pc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2 border rounded-3 p-2 bg-light text-center" style="min-height: 150px;">
                        <img id="sectionImagePcPreview" src="{{ old('home_section_image_pc', \App\Models\Setting::get('home_section_image_pc')) ? asset(old('home_section_image_pc', \App\Models\Setting::get('home_section_image_pc'))) : '' }}" class="img-fluid rounded-2 mx-auto" style="max-height: 150px; {{ old('home_section_image_pc', \App\Models\Setting::get('home_section_image_pc')) ? '' : 'display: none;' }} object-fit: contain;">
                        <small id="sectionImagePcPlaceholder" class="text-muted d-block py-5" style="{{ old('home_section_image_pc', \App\Models\Setting::get('home_section_image_pc')) ? 'display: none;' : '' }}">Click input to select image</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Mobile Section Image (e.g. 600x600)</label>
                    <input type="text" name="home_section_image_mobile" id="sectionImageMobileInput" class="form-control @error('home_section_image_mobile') is-invalid @enderror" value="{{ old('home_section_image_mobile', \App\Models\Setting::get('home_section_image_mobile')) }}" placeholder="Choose mobile image..." readonly>
                    @error('home_section_image_mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2 border rounded-3 p-2 bg-light text-center" style="min-height: 150px;">
                        <img id="sectionImageMobilePreview" src="{{ old('home_section_image_mobile', \App\Models\Setting::get('home_section_image_mobile')) ? asset(old('home_section_image_mobile', \App\Models\Setting::get('home_section_image_mobile'))) : '' }}" class="img-fluid rounded-2 mx-auto" style="max-height: 150px; {{ old('home_section_image_mobile', \App\Models\Setting::get('home_section_image_mobile')) ? '' : 'display: none;' }} object-fit: contain;">
                        <small id="sectionImageMobilePlaceholder" class="text-muted d-block py-5" style="{{ old('home_section_image_mobile', \App\Models\Setting::get('home_section_image_mobile')) ? 'display: none;' : '' }}">Click input to select image</small>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Optional Click Link</label>
                <input type="text" name="home_section_image_link" class="form-control @error('home_section_image_link') is-invalid @enderror" value="{{ old('home_section_image_link', \App\Models\Setting::get('home_section_image_link')) }}" placeholder="e.g. /shop">
                @error('home_section_image_link')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">If provided, the image will act as a clickable banner.</small>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-premium px-5 rounded-pill text-uppercase fw-bold" style="font-size: 0.85rem;">Save Section Images</button>
            </div>
        </form>
    </div>
</div>
