@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0">
        <i class="bi bi-pencil-square text-primary me-2"></i>Edit Instagram Post
    </h1>
    <a href="{{ route('admin.instagram-feed.index') }}" class="btn btn-outline-secondary rounded-3">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-4 border-0 shadow-sm">
        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success rounded-4 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
@endif

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <form method="POST" action="{{ route('admin.instagram-feed.update', $post->id) }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            {{-- Caption --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Caption</label>
                <input type="text" name="caption" class="form-control rounded-3" value="{{ old('caption', $post->caption) }}" maxlength="255">
            </div>

            {{-- Sort Order --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Sort Order</label>
                <input type="number" name="sort_order" class="form-control rounded-3" value="{{ old('sort_order', $post->sort_order) }}" min="0">
            </div>

            {{-- Row --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Marquee Row <span class="text-danger">*</span></label>
                <select name="row" class="form-select rounded-3" required>
                    <option value="1" {{ old('row', $post->row) == 1 ? 'selected' : '' }}>Row 1 (Top — scrolls left)</option>
                    <option value="2" {{ old('row', $post->row) == 2 ? 'selected' : '' }}>Row 2 (Bottom — scrolls right)</option>
                </select>
                <small class="text-muted">Choose which marquee row this post appears in on the homepage.</small>
            </div>

            {{-- Link URL --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Redirect URL <small class="text-muted">(optional)</small></label>
                <input type="url" name="link_url" class="form-control rounded-3" value="{{ old('link_url', $post->link_url) }}" placeholder="https://instagram.com/p/...">
            </div>

            {{-- Active --}}
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch ps-0 w-100">
                    <label class="form-label fw-bold d-block mb-2">Active on Website</label>
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" {{ $post->is_active ? 'checked' : '' }} style="width: 50px; height: 26px;">
                </div>
            </div>

            {{-- Image --}}
            <div class="col-12">
                <label class="form-label fw-bold">Image</label>

                <div class="row g-3 align-items-start">
                    <div class="col-md-7">
                        <div class="input-group">
                            <input type="text" name="image_path" id="imagePathInput" class="form-control media-picker-input"
                                   placeholder="Select from Media Library or paste image URL..." value="{{ old('image_path') }}">
                        </div>
                        <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                            <i class="bi bi-info-circle me-1"></i>Or upload a file directly below to replace the current image:
                        </div>
                        <input type="file" name="image" class="form-control rounded-3 mt-1" accept="image/*" onchange="previewDirectFile(this, '#imagePreview')">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-semibold">Current Image</label>
                        <div id="imagePreview" class="border rounded-3 p-2 bg-light text-center min-height-80 d-flex align-items-center justify-content-center">
                            @if($post->image_path)
                                <img src="{{ asset($post->image_path) }}" class="rounded-3 img-fluid border" style="max-height: 120px; object-fit: contain;">
                            @else
                                <span class="text-muted small">No image set</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary fw-bold px-4 rounded-3">
                <i class="bi bi-save2-fill me-1"></i> Update Post
            </button>
            <a href="{{ route('admin.instagram-feed.index') }}" class="btn btn-outline-secondary rounded-3">Cancel</a>
        </div>
    </form>
</div>

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.initMediaPicker === 'function') {
            window.initMediaPicker('#imagePathInput', '#imagePreview', 'image');
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
</script>
@endpush
@endsection
