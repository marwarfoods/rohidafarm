@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0">
        <i class="bi bi-patch-check-fill text-success me-2"></i>Add Certification
    </h1>
    <a href="{{ route('admin.certifications.index') }}" class="btn btn-outline-secondary rounded-3">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <form method="POST" action="{{ route('admin.certifications.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Name --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Certification Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control rounded-3" value="{{ old('name') }}" placeholder="e.g. ISO 9001:2015" required>
            </div>

            {{-- Certificate Number --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Certificate / License Number</label>
                <input type="text" name="certificate_number" class="form-control rounded-3" value="{{ old('certificate_number') }}" placeholder="e.g. FSSAI: 10020022011985">
            </div>

            {{-- Sort Order + Active --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control rounded-3" value="{{ old('sort_order', 0) }}" min="0">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch ps-0 w-100">
                    <label class="form-label fw-bold d-block mb-2">Active on Website</label>
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked style="width: 50px; height: 26px;">
                </div>
            </div>

            {{-- ── LOGO / ICON (Media Picker + Direct Upload) ── --}}
            <div class="col-12">
                <label class="form-label fw-bold">Logo / Icon <small class="text-muted">(PNG/SVG/JPG)</small></label>
                
                <div class="row g-3 align-items-start">
                    <div class="col-md-7">
                        <div class="input-group">
                            <input type="text" name="logo_path" id="logoPathInput" class="form-control media-picker-input" 
                                   placeholder="Select from Media Library or paste image URL..." value="{{ old('logo_path') }}">
                        </div>
                        <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                            <i class="bi bi-info-circle me-1"></i>Or upload a file directly below:
                        </div>
                        <input type="file" name="logo" class="form-control rounded-3 mt-1" accept="image/*" onchange="previewDirectFile(this, '#logoPreview')">
                    </div>
                    <div class="col-md-5">
                        <div id="logoPreview" class="border rounded-3 p-2 bg-light text-center min-height-80 d-flex align-items-center justify-content-center">
                            <span class="text-muted small">No logo selected</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── CERTIFICATE IMAGES ── --}}
            <div class="col-12">
                <label class="form-label fw-bold">Certificate Document Images <small class="text-muted">(Scanned certificate pages)</small></label>

                <div class="row g-3 align-items-start">
                    <div class="col-md-7">
                        <div class="mb-2">
                            <label class="form-label text-muted small fw-semibold">Option A: Select from Media / URL</label>
                            <div class="input-group">
                                <input type="text" name="cert_media_urls" id="certMediaInput" class="form-control media-picker-input" 
                                       placeholder="Choose from media library..." value="{{ old('cert_media_urls') }}">
                            </div>
                        </div>
                        <div>
                            <label class="form-label text-muted small fw-semibold">Option B: Direct File Upload (Multiple)</label>
                            <input type="file" name="certificate_images[]" class="form-control rounded-3" accept="image/*" multiple onchange="previewMultiFiles(this, '#certFilesPreview')">
                            <small class="text-muted">You can select multiple files at once.</small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-semibold">Selected Images Preview</label>
                        <div id="certPreview" class="border rounded-3 p-2 bg-light min-height-80 d-flex flex-wrap gap-2 align-items-center">
                            <span class="text-muted small">No images selected</span>
                        </div>
                        <div id="certFilesPreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success fw-bold px-4 rounded-3">
                <i class="bi bi-save2-fill me-1"></i> Save Certification
            </button>
            <a href="{{ route('admin.certifications.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
        </div>
    </form>
</div>

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initMediaPicker === 'function') {
            window.initMediaPicker('#logoPathInput', '#logoPreview', 'image');
            window.initMediaPicker('#certMediaInput', '#certPreview', 'image');
        }
    });

    function previewDirectFile(input, previewSelector) {
        const container = document.querySelector(previewSelector);
        if (!container) return;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                container.innerHTML = `<img src="${e.target.result}" class="rounded-3 img-fluid border" style="max-height: 120px; object-fit: contain;">`;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewMultiFiles(input, previewSelector) {
        const container = document.querySelector(previewSelector);
        if (!container) return;
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    container.insertAdjacentHTML('beforeend', `<img src="${e.target.result}" class="rounded-3 border" style="width: 60px; height: 75px; object-fit: cover;">`);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endpush
@endsection
