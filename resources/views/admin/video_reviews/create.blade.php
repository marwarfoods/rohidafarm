@extends('layouts.admin')

@section('admin_content')
<div class="mb-4">
    <a href="{{ route('admin.video-reviews.index') }}" class="btn btn-link text-success p-0 text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Listing</a>
    <h2 class="font-heading fw-bold text-dark mt-2">Add New Video Review</h2>
    <p class="text-muted m-0">Upload a video review file and link it to an existing product.</p>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
    <form action="{{ route('admin.video-reviews.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="reviewer_name" class="form-label fw-bold text-dark">Reviewer Name</label>
                    <input type="text" class="form-control rounded-3 @error('reviewer_name') is-invalid @enderror" id="reviewer_name" name="reviewer_name" value="{{ old('reviewer_name') }}" required placeholder="e.g. Ramesh Kumar">
                    @error('reviewer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="product_id" class="form-label fw-bold text-dark">Link to Product</label>
                    <select class="form-select rounded-3 @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
                        <option value="">-- Do Not Link (Generic Review) --</option>
                        @foreach($products as $prod)
                            <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="videoInput" class="form-label fw-bold text-dark">Video Review Asset <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-3 @error('video') is-invalid @enderror media-picker-input" id="videoInput" name="video" value="{{ old('video') }}" required placeholder="Select from Media Library or enter video URL...">
                    <div class="mt-2 text-muted" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i>Or upload a video file directly:</div>
                    <input type="file" name="video_file" class="form-control rounded-3 mt-1" accept="video/mp4,video/webm,video/quicktime" onchange="previewVideoFile(this)">
                    <div id="videoPreviewContainer" class="mt-2"></div>
                    @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="sort_order" class="form-label fw-bold text-dark">Sort Order</label>
                    <input type="number" class="form-control rounded-3 @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">
                        Thumbnail / Poster Image <small class="text-muted fw-normal">(Optional — shown before video plays & returns on mouse leave, just like Instagram)</small>
                    </label>
                    <div class="row g-3 align-items-start">
                        <div class="col-md-7">
                            <input type="text" class="form-control rounded-3 @error('thumbnail_path') is-invalid @enderror media-picker-input" id="thumbnailInput" name="thumbnail_path" value="{{ old('thumbnail_path') }}" placeholder="Select from Media Library or paste image URL...">
                            <div class="mt-2 text-muted" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i>Or upload an image file directly (JPEG, PNG, WEBP max 5MB):</div>
                            <input type="file" name="thumbnail" class="form-control rounded-3 mt-1" accept="image/*" onchange="previewThumbnail(this)">
                            @error('thumbnail')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @error('thumbnail_path')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <div id="thumbnailPreviewContainer" class="border rounded-3 p-2 bg-light text-center d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                <span class="text-muted small">No thumbnail selected (will fallback to video frame)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked value="1">
                    <label class="form-check-label fw-semibold" for="is_active">Make Active (Visible on Homepage)</label>
                </div>
            </div>

            <div class="col-12 mt-4 border-top pt-4">
                <button type="submit" class="btn btn-success px-5 py-3 rounded-pill text-uppercase fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">Save Video Review</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initMediaPicker === 'function') {
            initMediaPicker('#videoInput', '#videoPreviewContainer', 'video');
            initMediaPicker('#thumbnailInput', '#thumbnailPreviewContainer', 'image');
        }

        const videoInput = document.getElementById('videoInput');
        if (videoInput) {
            videoInput.addEventListener('input', function() {
                renderVideoPreview(this.value);
            });
            videoInput.addEventListener('change', function() {
                renderVideoPreview(this.value);
            });
            videoInput.addEventListener('media-picker:selected', function(e) {
                if (e.detail && e.detail.paths && e.detail.paths[0]) {
                    renderVideoPreview(e.detail.paths[0]);
                }
            });
            if (videoInput.value) {
                renderVideoPreview(videoInput.value);
            }
        }
    });

    function renderVideoPreview(url) {
        const container = document.getElementById('videoPreviewContainer');
        if (!container) return;
        if (!url || !url.trim()) {
            container.innerHTML = '';
            return;
        }
        const cleanUrl = url.trim();
        const src = cleanUrl.startsWith('http') || cleanUrl.startsWith('blob:')
            ? cleanUrl
            : (window.location.origin + (cleanUrl.startsWith('/') ? '' : '/') + cleanUrl);

        container.innerHTML = `
            <div class="video-preview-card position-relative rounded-3 overflow-hidden border shadow-sm mt-2 bg-black" style="max-width: 320px;">
                <video src="${src}#t=0.1" class="w-100" style="max-height: 180px; display: block;" controls preload="metadata" muted playsinline></video>
                <div class="small text-white-50 p-1 text-center bg-dark" style="font-size: 0.72rem;">
                    <i class="bi bi-play-circle me-1"></i>Hover or click to play preview
                </div>
            </div>
        `;

        const v = container.querySelector('video');
        const card = container.querySelector('.video-preview-card');
        if (v && card) {
            card.addEventListener('mouseenter', () => {
                v.muted = true;
                const p = v.play();
                if (p) p.catch(() => {});
            });
            card.addEventListener('mouseleave', () => {
                v.pause();
                try { v.currentTime = 0.1; } catch (e) {}
            });
        }
    }

    function previewVideoFile(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const fileUrl = URL.createObjectURL(file);
        renderVideoPreview(fileUrl);
        const textInput = document.getElementById('videoInput');
        if (textInput && !textInput.value) {
            textInput.value = '/uploads/videos/' + file.name;
        }
    }

    function previewThumbnail(input) {
        const container = document.querySelector('#thumbnailPreviewContainer');
        if (!container || !input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            container.innerHTML = `<img src="${e.target.result}" class="rounded-3 img-fluid border" style="max-height: 150px; object-fit: contain;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>
@endpush
