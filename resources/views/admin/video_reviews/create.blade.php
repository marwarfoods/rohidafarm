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
                    <label for="videoInput" class="form-label fw-bold text-dark">Video Review Asset</label>
                    <input type="text" class="form-control rounded-3 @error('video') is-invalid @enderror" id="videoInput" name="video" required placeholder="/uploads/videos/review.mp4">
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
        initMediaPicker('#videoInput', '#videoPreviewContainer', 'video');
    });
</script>
@endpush
