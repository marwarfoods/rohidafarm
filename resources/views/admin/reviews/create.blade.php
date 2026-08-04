@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-star-fill text-success me-2"></i>Add Manual Review</h1>
    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill"><i class="bi bi-arrow-left me-2"></i>Back to Reviews</a>
</div>

<form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-lines-fill me-2"></i>Customer Details</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Product <span class="text-danger">*</span></label>
                    <select name="product_id" class="form-select border p-2" required>
                        <option value="">-- Choose Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control border p-2" required placeholder="e.g. John Doe">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer Email <span class="text-danger">*</span></label>
                        <input type="email" name="customer_email" class="form-control border p-2" required placeholder="e.g. john@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Customer Phone</label>
                        <input type="text" name="customer_phone" class="form-control border p-2" placeholder="e.g. 9876543210">
                    </div>
                </div>
            </div>

            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-card-text me-2"></i>Review Content</h5>

                <div class="mb-3">
                    <label class="form-label fw-bold d-block">Overall Rating <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 text-warning fs-3" id="interactiveRatingStars" style="cursor: pointer;">
                        <i class="bi bi-star-fill star-input-icon" data-val="1"></i>
                        <i class="bi bi-star-fill star-input-icon" data-val="2"></i>
                        <i class="bi bi-star-fill star-input-icon" data-val="3"></i>
                        <i class="bi bi-star-fill star-input-icon" data-val="4"></i>
                        <i class="bi bi-star-fill star-input-icon" data-val="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="ratingInputValue" value="5" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Review Headline <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control border p-2" required placeholder="Summarize the experience...">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Full Review Text <span class="text-danger">*</span></label>
                    <textarea name="review" class="form-control border p-2" rows="5" required placeholder="What did they like or dislike about this product?"></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar / Right Column Options -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-images me-2"></i>Attached Photos</h5>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-dark">
                        Upload Photos <span class="text-muted fw-normal">(Optional, max 4 images)</span>
                    </label>

                    {{-- Custom upload trigger --}}
                    <div id="reviewImgPickerArea"
                         class="border rounded-3 p-3 text-center"
                         style="border-style:dashed!important;border-color:#d1d5db!important;cursor:pointer;background:#fafafa;transition:background 0.2s;"
                         onclick="document.getElementById('reviewImagesInput').click()">
                        <i class="bi bi-cloud-upload text-muted" style="font-size:1.6rem;"></i>
                        <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;">Click to upload images</p>
                        <p class="text-muted mb-0" style="font-size:0.72rem;">JPG · PNG · WEBP · Max 2MB each</p>
                    </div>

                    {{-- Hidden real file input --}}
                    <input type="file" name="review_images[]" id="reviewImagesInput"
                           class="d-none" multiple accept="image/jpeg,image/png,image/webp">

                    {{-- Preview grid --}}
                    <div id="reviewImagePreviews" class="d-flex flex-wrap gap-2 mt-3"></div>
                </div>
                
                <div class="alert alert-success border-0 rounded-3 mb-4 p-3 d-flex gap-2">
                    <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
                    <small>Reviews added manually by the admin are automatically approved and displayed instantly.</small>
                </div>

                <div class="d-grid mt-2">
                    <button type="submit" class="btn btn-premium py-2.5 rounded-pill fw-bold text-uppercase"><i class="bi bi-check-circle me-2"></i>Publish Review</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('admin_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Interactive Rating Stars logic
    const stars = document.querySelectorAll('.star-input-icon');
    const ratingInput = document.getElementById('ratingInputValue');

    function fillStars(rating) {
        stars.forEach(s => {
            const val = parseInt(s.getAttribute('data-val'));
            if (val <= rating) {
                s.classList.remove('bi-star');
                s.classList.add('bi-star-fill');
            } else {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            }
        });
    }

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const val = parseInt(this.getAttribute('data-val'));
            ratingInput.value = val;
            fillStars(val);
        });

        star.addEventListener('mouseenter', function() {
            const val = parseInt(this.getAttribute('data-val'));
            fillStars(val);
        });
    });

    document.getElementById('interactiveRatingStars').addEventListener('mouseleave', function() {
        fillStars(parseInt(ratingInput.value));
    });

    // Image Preview logic
    const fileInput = document.getElementById('reviewImagesInput');
    const previewContainer = document.getElementById('reviewImagePreviews');
    let dataTransfer = new DataTransfer();

    fileInput.addEventListener('change', function(e) {
        // Keep existing files, add new ones (up to 4 total)
        Array.from(this.files).forEach(file => {
            if (dataTransfer.items.length < 4) {
                dataTransfer.items.add(file);
            } else {
                alert('You can only upload a maximum of 4 images.');
            }
        });
        
        fileInput.files = dataTransfer.files;
        renderPreviews();
    });

    function renderPreviews() {
        previewContainer.innerHTML = '';
        Array.from(fileInput.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'position-relative border rounded-3 overflow-hidden';
                div.style.width = '70px';
                div.style.height = '70px';
                div.innerHTML = `
                    <img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center remove-btn" 
                            style="width:20px;height:20px;margin:2px;" data-index="${index}">
                        <i class="bi bi-x" style="font-size:0.8rem;"></i>
                    </button>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    previewContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-btn')) {
            const btn = e.target.closest('.remove-btn');
            const index = parseInt(btn.getAttribute('data-index'));
            
            const newDt = new DataTransfer();
            Array.from(fileInput.files).forEach((file, i) => {
                if (i !== index) newDt.items.add(file);
            });
            
            dataTransfer = newDt;
            fileInput.files = dataTransfer.files;
            renderPreviews();
        }
    });
});
</script>
@endpush
