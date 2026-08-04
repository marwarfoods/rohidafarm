<!-- ── Top Add-Ons Pop-up Modal (Triggered on Add To Cart) ── -->
@php
    $suggestedAddons = \App\Models\Product::with(['images', 'variants'])
        ->where('is_active', true)
        ->take(4)
        ->get();
@endphp

<div class="modal fade" id="addToCartAddonsModal" tabindex="-1" aria-labelledby="addToCartAddonsModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden" style="background-color: var(--white);">
            <!-- Modal Header -->
            <div class="modal-header border-bottom py-3 px-4" style="background-color: #F4F7F4;">
                <div class="d-flex align-items-center gap-2 text-success">
                    <i class="bi bi-check-circle-fill fs-5"></i>
                    <h6 class="modal-title font-heading fw-bold text-dark m-0" id="addToCartAddonsModalLabel" style="font-size: 1rem;">Added to Cart!</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="mb-3">
                    <h6 class="fw-bold font-heading text-dark mb-1" style="font-size: 0.95rem;">Top Add-Ons for This Product</h6>
                    <small class="text-muted d-block" style="font-size: 0.78rem;">These are usually added together — don't miss out</small>
                </div>

                <!-- Add-Ons List -->
                <div class="d-flex flex-column gap-2 mb-4" id="addonsItemsContainer">
                    @foreach($suggestedAddons as $addon)
                        @php
                            $price = $addon->sale_price;
                            $mrp = $addon->mrp;
                            $discount = $addon->discountPercentage();
                            $img = $addon->primaryImage ? asset($addon->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                        @endphp
                        <label class="d-flex align-items-center justify-content-between p-3 rounded-3 border cursor-pointer hover-bg-light transition-smooth" style="border-color: var(--border-color) !important; background-color: var(--cream-bg);">
                            <div class="d-flex align-items-center gap-3">
                                <input type="checkbox" class="form-check-input addon-checkbox fs-5 m-0" value="{{ $addon->id }}" data-variant-id="{{ $addon->variants->first()?->id }}">
                                <img src="{{ $img }}" alt="{{ $addon->name }}" width="48" height="48" class="rounded-2 object-fit-cover border bg-white" loading="lazy">
                                <div>
                                    <h6 class="fw-bold font-heading text-dark m-0" style="font-size: 0.85rem; line-height: 1.2;">{{ $addon->name }}</h6>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">₹{{ number_format($price, 0) }}</span>
                                        @if($mrp > $price)
                                            <small class="text-muted text-decoration-line-through" style="font-size: 0.75rem;">₹{{ number_format($mrp, 0) }}</small>
                                        @endif
                                        @if($discount > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-1.5 rounded-1" style="font-size: 0.65rem;">{{ round($discount) }}% OFF</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Modal Actions -->
                <div class="row g-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary w-100 py-2.5 rounded-3 fw-bold font-heading text-dark" data-bs-dismiss="modal" style="font-size: 0.88rem; border-color: var(--border-color);">
                            Skip
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" id="btnConfirmAddons" class="btn btn-premium w-100 py-2.5 rounded-3 fw-bold font-heading text-white" style="font-size: 0.88rem; background-color: var(--dark-green) !important; border-color: var(--dark-green) !important;">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
