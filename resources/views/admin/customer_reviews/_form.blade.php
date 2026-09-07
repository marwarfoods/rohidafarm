@php $cr = $customerReview ?? null; @endphp

<div class="row g-4">
    <div class="col-md-8">
        <label class="form-label fw-bold text-dark">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control rounded-3" value="{{ old('title', $cr->title ?? '') }}" required placeholder="e.g. Old-school fuel. Real results.">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-dark">Star Rating <span class="text-danger">*</span></label>
        <select name="rating" class="form-select rounded-3" required>
            @for($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" {{ (int) old('rating', $cr->rating ?? 5) === $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
            @endfor
        </select>
    </div>

    <div class="col-12">
        <label class="form-label fw-bold text-dark">Review / Description <span class="text-danger">*</span></label>
        <textarea name="review" class="form-control rounded-3" rows="4" required placeholder="Full review text as it should appear on the website...">{{ old('review', $cr->review ?? '') }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-dark">Customer Name <span class="text-danger">*</span></label>
        <input type="text" name="customer_name" class="form-control rounded-3" value="{{ old('customer_name', $cr->customer_name ?? '') }}" required placeholder="e.g. Sumit Suhag">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-dark">Link to Product</label>
        <select name="product_id" class="form-select rounded-3">
            <option value="">-- Do Not Link --</option>
            @foreach($products as $prod)
                <option value="{{ $prod->id }}" {{ (int) old('product_id', $cr->product_id ?? 0) === $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
            @endforeach
        </select>
        <small class="text-muted">The product name shows as a clickable link under the customer's name.</small>
    </div>

    <div class="col-12">
        <label class="form-label fw-bold text-dark">Customer Photo <small class="text-muted">(shown on one side of the slide)</small></label>
        <div class="row g-3 align-items-start">
            <div class="col-md-7">
                <input type="text" name="image_path" id="imagePathInput" class="form-control media-picker-input" placeholder="Select from Media Library or paste image URL..." value="{{ old('image_path', $cr->image_path ?? '') }}">
                <div class="mt-2 text-muted" style="font-size: 0.78rem;"><i class="bi bi-info-circle me-1"></i>Or upload a file directly:</div>
                <input type="file" name="image" class="form-control rounded-3 mt-1" accept="image/*" onchange="previewCustomerReviewImage(this)">
            </div>
            <div class="col-md-5">
                <div id="imagePreview" class="border rounded-3 p-2 bg-light text-center d-flex align-items-center justify-content-center" style="min-height: 100px;">
                    @if($cr && $cr->image_path)
                        <img src="{{ asset($cr->image_path) }}" class="rounded-3 img-fluid border" style="max-height: 160px; object-fit: contain;">
                    @else
                        <span class="text-muted small">No image selected</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-dark">Sort Order</label>
        <input type="number" name="sort_order" class="form-control rounded-3" value="{{ old('sort_order', $cr->sort_order ?? 0) }}" min="0">
    </div>

    <div class="col-md-8 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $cr->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label fw-semibold" for="is_active">Active (visible on homepage)</label>
        </div>
    </div>
</div>
