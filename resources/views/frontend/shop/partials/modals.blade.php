<!-- ═══════════ LIGHTBOX SLIDER MODAL ═══════════ -->
<div class="modal fade" id="imageLightboxModal" tabindex="-1" aria-hidden="true" style="z-index:1070;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 90vw; width: fit-content; margin: auto;">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 position-relative d-flex justify-content-center align-items-center">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-1 z-3" data-bs-dismiss="modal" aria-label="Close" style="background-color: rgba(0,0,0,0.55); border-radius: 50%; padding: 8px; filter: invert(1) grayscale(100%) brightness(200%); transform: translate(30%, -30%);"></button>
                <!-- Lightbox Swiper -->
                <div class="swiper" id="lightboxSwiper" style="max-height:88vh; max-width:88vw;">
                    <div class="swiper-wrapper" id="lightboxSwiperWrapper">
                        <!-- Slides injected by JS -->
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ DETAILED REVIEW MODAL (photo left + full text right, like a product-review lightbox) ═══════════ -->
<div class="modal fade" id="reviewDetailModal" tabindex="-1" aria-hidden="true" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"
                    style="z-index:20;background-color:rgba(0,0,0,0.55);border-radius:50%;padding:10px;filter:invert(1) grayscale(100%) brightness(200%);"></button>

            <div class="row g-0">
                <!-- Photo column (hidden entirely when the review has no photos) -->
                <div class="col-md-6 bg-dark position-relative d-flex align-items-center justify-content-center d-none" id="modalReviewImageCol" style="min-height: 340px;">
                    <img id="modalReviewMainImg" src="" alt="Review photo" class="img-fluid" style="max-height: 70vh; max-width: 100%; object-fit: contain;">

                    <button type="button" id="modalReviewImgPrev"
                            style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;border:none;background:rgba(0,0,0,0.55);cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="15,18 9,12 15,6"/></svg>
                    </button>
                    <button type="button" id="modalReviewImgNext"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;border:none;background:rgba(0,0,0,0.55);cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><polyline points="9,18 15,12 9,6"/></svg>
                    </button>

                    <div id="modalReviewImgThumbs" class="position-absolute bottom-0 start-0 w-100 d-flex gap-2 justify-content-center p-2" style="background: rgba(0,0,0,0.35);"></div>
                </div>

                <!-- Text column (full-width when there is no photo column) -->
                <div class="col-md-6 p-4 p-md-5" id="modalReviewTextCol">
                    <div class="text-warning mb-3" id="modalReviewStars" style="font-size: 1rem;"></div>

                    <div class="text-muted mb-3" id="modalReviewDate" style="font-size: 0.8rem;">Date</div>

                    <h5 class="fw-bold font-heading text-dark mb-2" id="modalReviewTitle" style="font-size: 1.05rem;">Headline</h5>
                    <p class="text-muted mb-0" id="modalReviewText" style="font-size: 0.92rem; line-height: 1.75; white-space: pre-line;">Content...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ WRITE REVIEW MODAL ═══════════ -->
<div class="modal fade" id="writeReviewModal" tabindex="-1" aria-hidden="true" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg" style="background: #fff;">
            <div class="modal-header border-bottom" style="border-color: #f6f3eb !important;">
                <h5 class="modal-title font-heading fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-success"></i>Write a Product Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
                @endif
                <form action="{{ route('product.reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Your Name *</label>
                            <input type="text" name="guest_name" class="form-control border p-2" required placeholder="e.g. Rajendra" value="{{ auth()->check() ? auth()->user()->name : old('guest_name') }}">
                        </div>
                        @guest
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Your Email *</label>
                                <input type="email" name="guest_email" class="form-control border p-2" required placeholder="e.g. raj@example.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold text-dark">Mobile (Optional)</label>
                                <input type="text" name="guest_phone" class="form-control border p-2" placeholder="e.g. 9876543210">
                            </div>
                        @endguest
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark d-block">Overall Rating *</label>
                        <div class="d-flex gap-2 text-warning fs-2" id="interactiveRatingStars" style="cursor: pointer;">
                            <i class="bi bi-star-fill star-input-icon" data-val="1"></i>
                            <i class="bi bi-star-fill star-input-icon" data-val="2"></i>
                            <i class="bi bi-star-fill star-input-icon" data-val="3"></i>
                            <i class="bi bi-star-fill star-input-icon" data-val="4"></i>
                            <i class="bi bi-star-fill star-input-icon" data-val="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInputValue" value="5" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Review Headline *</label>
                        <input type="text" name="title" class="form-control border p-2" required placeholder="Summarize your experience...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Your Review *</label>
                        <textarea name="review" class="form-control border p-2" rows="4" required placeholder="What did you like or dislike about this product?"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">
                            Photos <span class="text-muted fw-normal">(Optional, max 2 images)</span>
                        </label>

                        {{-- Custom upload trigger --}}
                        <div id="reviewImgPickerArea"
                             class="border rounded-3 p-3 text-center"
                             style="border-style:dashed!important;border-color:#d1d5db!important;cursor:pointer;background:#fafafa;transition:background 0.2s;"
                             onclick="document.getElementById('reviewImagesInput').click()"
                             ondragover="event.preventDefault();this.style.background='#f0fdf4';"
                             ondragleave="this.style.background='#fafafa';"
                             ondrop="handleReviewImageDrop(event)">
                            <i class="bi bi-cloud-upload text-muted" style="font-size:1.6rem;"></i>
                            <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;">Click or drag images here</p>
                            <p class="text-muted mb-0" style="font-size:0.72rem;">JPG · PNG · WEBP · Max 2MB each · Up to 2 photos</p>
                        </div>

                        {{-- Hidden real file input --}}
                        <input type="file" name="review_images[]" id="reviewImagesInput"
                               class="d-none" multiple accept="image/jpeg,image/png,image/webp">

                        {{-- Preview grid --}}
                        <div id="reviewImagePreviews" class="d-flex flex-wrap gap-2 mt-3"></div>
                    <x-turnstile />

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-premium px-5 py-2 rounded-pill">Submit Review</button>
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ DIRECT BUY QUICK CHECKOUT MODAL ═══════════ -->
<div class="modal fade" id="directBuyModal" tabindex="-1" aria-hidden="true" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg" style="background: #fff;">
            <div class="modal-header border-bottom" style="border-color: #f6f3eb !important;">
                <h5 class="modal-title font-heading fw-bold text-dark d-flex align-items-center gap-2">
                    <img src="https://d6xcmfyh68wv8.cloudfront.net/newsroom-content/uploads/2024/05/Razorpay-Logo.jpg" alt="Razorpay" style="height:18px; object-fit:contain;">
                    Checkout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="directBuyForm" method="POST">
                    @csrf
                    <!-- Product data -->
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="directBuyVariantId" value="{{ $product->variants->first()?->id }}">
                    <input type="hidden" name="quantity" id="directBuyQuantity" value="1">
                    
                    @php
                        $user = Auth::user();
                        $lastOrder = $user ? $user->orders()->latest()->first() : null;
                    @endphp

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Full Name *</label>
                            <input type="text" name="name" class="form-control border p-2" required placeholder="John Doe" value="{{ $user->name ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Email *</label>
                            <input type="email" name="email" class="form-control border p-2" required placeholder="john@example.com" value="{{ $user->email ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Mobile Number *</label>
                            <input type="text" name="phone" class="form-control border p-2" required placeholder="10-digit mobile" pattern="[0-9]{10}" value="{{ $user->phone ?? ($lastOrder->shipping_phone ?? '') }}">
                        </div>
                    </div>
                    
                    <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2">Shipping Details</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Address (House No, Building, Street) *</label>
                            <input type="text" name="address_line1" class="form-control border p-2" required placeholder="123, Main Street" value="{{ $lastOrder->shipping_address_line1 ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">City / District *</label>
                            <input type="text" name="city" class="form-control border p-2" required placeholder="City" value="{{ $lastOrder->shipping_city ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark">State *</label>
                            <select name="state" class="form-select border p-2" required>
                                <option value="">Select State</option>
                                @php
                                    $states = ["Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", " तेलंगाना", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal", "Andaman and Nicobar Islands", "Chandigarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Lakshadweep", "Puducherry"];
                                @endphp
                                @foreach($states as $st)
                                    <option value="{{ $st }}" {{ ($lastOrder->shipping_state ?? '') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-dark">Pincode *</label>
                            <input type="text" name="postal_code" class="form-control border p-2" required placeholder="6 digits" pattern="[0-9]{6}" value="{{ $lastOrder->shipping_postal_code ?? '' }}">
                        </div>
                    </div>

                    <!-- Direct checkout error container -->
                    <div id="directBuyError" class="alert alert-danger d-none mt-3 rounded-3" style="font-size:0.85rem;"></div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" id="btnDirectBuySubmit" class="btn btn-premium px-5 py-3 rounded-pill w-100 d-flex align-items-center justify-content-center gap-2" style="background-color:#02042b;border-color:#02042b;color:#fff;">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="directBuySpinner"></span>
                            <svg width="18" height="18" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22.6146 1.70014C23.0142 0.360114 24.1683 -0.320473 25.1328 0.156108C26.0973 0.632689 26.5056 2.0874 26.1061 3.42742L11.4587 52.5593C11.0592 53.8994 9.90513 54.5799 8.94065 54.1034C7.97617 53.6268 7.56781 52.1721 7.96738 50.8321L22.6146 1.70014Z" fill="#3395FF"/><path d="M38.8028 17.5134C39.6373 18.0039 39.8134 19.349 39.183 20.3168L21.4391 47.5583L25.9625 32.3789L37.1352 20.155L38.8028 17.5134Z" fill="#3395FF"/><path d="M12.9814 50.8033L10.3957 59.4815C9.99613 60.8215 10.4045 62.2762 11.369 62.7528C12.3335 63.2294 13.4876 62.5488 13.8871 61.2088L15.9324 54.3444C15.4855 53.223 14.1751 51.5036 12.9814 50.8033Z" fill="#3395FF"/></svg>
                            Pay Securely
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Write Review: Photo picker (max 2 images) ──
    const REVIEW_MAX_IMAGES = 2;
    const reviewInput = document.getElementById('reviewImagesInput');
    const reviewPreviews = document.getElementById('reviewImagePreviews');
    let reviewFilesStore = new DataTransfer();

    function renderReviewPreviews() {
        if (!reviewPreviews) return;
        reviewPreviews.innerHTML = '';
        Array.from(reviewFilesStore.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.cssText = 'width:70px;height:70px;';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" class="rounded-3 border w-100 h-100" style="object-fit:cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center review-img-remove"
                            style="width:20px;height:20px;margin:2px;border-radius:50%;" data-index="${index}">
                        <i class="bi bi-x" style="font-size:0.8rem;"></i>
                    </button>
                `;
                reviewPreviews.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    function addReviewFiles(fileList) {
        const incoming = Array.from(fileList || []);
        if (incoming.length === 0) return;

        const remainingSlots = REVIEW_MAX_IMAGES - reviewFilesStore.files.length;
        if (remainingSlots <= 0) {
            alert(`You can upload a maximum of ${REVIEW_MAX_IMAGES} photos.`);
            return;
        }

        if (incoming.length > remainingSlots) {
            alert(`You can upload a maximum of ${REVIEW_MAX_IMAGES} photos. Only the first ${remainingSlots} selected file(s) were added.`);
        }

        incoming.slice(0, remainingSlots).forEach(file => reviewFilesStore.items.add(file));
        reviewInput.files = reviewFilesStore.files;
        renderReviewPreviews();
    }

    if (reviewInput) {
        reviewInput.addEventListener('change', function () {
            addReviewFiles(this.files);
        });
    }

    if (reviewPreviews) {
        reviewPreviews.addEventListener('click', function (e) {
            const btn = e.target.closest('.review-img-remove');
            if (!btn) return;
            const index = parseInt(btn.getAttribute('data-index'));
            const newStore = new DataTransfer();
            Array.from(reviewFilesStore.files).forEach((file, i) => {
                if (i !== index) newStore.items.add(file);
            });
            reviewFilesStore = newStore;
            reviewInput.files = reviewFilesStore.files;
            renderReviewPreviews();
        });
    }

    window.handleReviewImageDrop = function (event) {
        event.preventDefault();
        event.currentTarget.style.background = '#fafafa';
        addReviewFiles(event.dataTransfer.files);
    };
});
</script>
