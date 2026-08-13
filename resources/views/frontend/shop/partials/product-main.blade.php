<!-- ═══════════════ SKELETON LOADER ═══════════════ -->
<div id="productSkeleton" class="position-fixed top-0 start-0 w-100 h-100 bg-white d-flex align-items-center justify-content-center" style="z-index: 9999; transition: opacity 0.5s ease;">
    <div class="product-detail-container py-5">
        <div class="row g-4">
            <!-- Left: image skeleton -->
            <div class="col-md-6">
                <div class="skeleton-block mb-3" style="height: 440px; width: 100%;"></div>
                <div class="d-flex gap-2">
                    <div class="skeleton-block" style="width:70px;height:70px;"></div>
                    <div class="skeleton-block" style="width:70px;height:70px;"></div>
                    <div class="skeleton-block" style="width:70px;height:70px;"></div>
                    <div class="skeleton-block" style="width:70px;height:70px;"></div>
                </div>
            </div>
            <!-- Right: info skeleton -->
            <div class="col-md-6 d-flex flex-column gap-3 pt-2">
                <div class="skeleton-block" style="height:18px;width:30%;"></div>
                <div class="skeleton-block" style="height:42px;width:80%;"></div>
                <div class="skeleton-block" style="height:18px;width:45%;"></div>
                <div class="skeleton-block" style="height:52px;width:55%;"></div>
                <div class="skeleton-block" style="height:14px;width:100%;"></div>
                <div class="skeleton-block" style="height:14px;width:90%;"></div>
                <div class="skeleton-block" style="height:14px;width:75%;"></div>
                <!-- Variant cards -->
                <div class="skeleton-block" style="height:18px;width:35%;margin-top:8px;"></div>
                <div class="d-flex gap-2 mt-1">
                    <div class="skeleton-block" style="width:110px;height:90px;"></div>
                    <div class="skeleton-block" style="width:110px;height:90px;"></div>
                    <div class="skeleton-block" style="width:110px;height:90px;"></div>
                </div>
                <!-- CTA -->
                <div class="d-flex gap-2 mt-2">
                    <div class="skeleton-block" style="flex:1;height:52px;"></div>
                    <div class="skeleton-block" style="flex:1;height:52px;"></div>
                </div>
                <!-- Trust -->
                <div class="d-flex gap-3 mt-2">
                    <div class="skeleton-block" style="width:60px;height:60px;border-radius:50%;"></div>
                    <div class="skeleton-block" style="width:60px;height:60px;border-radius:50%;"></div>
                    <div class="skeleton-block" style="width:60px;height:60px;border-radius:50%;"></div>
                    <div class="skeleton-block" style="width:60px;height:60px;border-radius:50%;"></div>
                </div>
            </div>
        </div>
        <!-- Tabs skeleton -->
        <div class="mt-5">
            <div class="d-flex gap-3 mb-4">
                <div class="skeleton-block" style="width:120px;height:38px;"></div>
                <div class="skeleton-block" style="width:100px;height:38px;"></div>
                <div class="skeleton-block" style="width:160px;height:38px;"></div>
            </div>
            <div class="skeleton-block mb-2" style="height:14px;width:100%;"></div>
            <div class="skeleton-block mb-2" style="height:14px;width:95%;"></div>
            <div class="skeleton-block mb-2" style="height:14px;width:88%;"></div>
            <div class="skeleton-block" style="height:14px;width:70%;"></div>
        </div>
    </div>
</div>

<!-- ── Main Product Card ── -->
<div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-5 main-detail-card" style="border-color: #f6f3eb !important;">
    <div class="row g-5">

        <!-- ── LEFT: Gallery ── -->
        <div class="col-md-6 col-12">
            <div class="product-gallery-sticky" style="position: sticky; top: 90px;">
                <div class="row g-2">
                    <!-- Vertical Thumbnails (Desktop) -->
                    <div class="col-2 d-none d-md-flex flex-column align-items-center" style="gap:0;">
                        <!-- Up Arrow -->
                        <button type="button" class="thumb-scroll-btn scroll-up">
                            <i class="bi bi-chevron-up"></i>
                        </button>

                        <!-- Thumbnails — height set by JS to show exactly 5 -->
                        <div class="thumb-slider-col" style="width:100%;">
                            <div class="thumb-img-wrapper active" data-slide-index="0">
                                <img src="{{ $product->primaryImage ? asset($product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg') }}" alt="{{ $product->name }}">
                            </div>
                            @foreach($product->gallery as $index => $gal)
                                <div class="thumb-img-wrapper" data-slide-index="{{ $index + 1 }}">
                                    <img src="{{ asset($gal->image_path) }}" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>

                        <!-- Down Arrow -->
                        <button type="button" class="thumb-scroll-btn scroll-down">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Main Slider -->
                    <div class="col-12 col-md-10">
                        <div class="swiper main-product-slider border rounded-4 overflow-hidden position-relative" style="background-color:#ffffff;aspect-ratio:1/1;">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide main-image-zoom-frame" style="cursor:zoom-in;">
                                    <img src="{{ $product->primaryImage ? asset($product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg') }}"
                                         alt="{{ $product->name }}"
                                         class="w-100 h-100 object-fit-cover img-main-slide"
                                         data-full="{{ $product->primaryImage ? asset($product->primaryImage->image_path) : '' }}">
                                </div>
                                @foreach($product->gallery as $gal)
                                    <div class="swiper-slide main-image-zoom-frame" style="cursor:zoom-in;">
                                        <img src="{{ asset($gal->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-100 h-100 object-fit-cover img-main-slide"
                                             data-full="{{ asset($gal->image_path) }}">
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination d-md-none"></div>
                        </div>

                        <!-- Horizontal Thumbnails (Mobile) -->
                        <div class="mobile-thumbs-container d-flex d-md-none mt-2 py-1">
                            <div class="thumb-img-wrapper border rounded-3 overflow-hidden active cursor-pointer" data-slide-index="0">
                                <img src="{{ $product->primaryImage ? asset($product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg') }}" class="w-100 h-100 object-fit-cover">
                            </div>
                            @foreach($product->gallery as $index => $gal)
                                <div class="thumb-img-wrapper border rounded-3 overflow-hidden cursor-pointer" data-slide-index="{{ $index + 1 }}">
                                    <img src="{{ asset($gal->image_path) }}" class="w-100 h-100 object-fit-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── RIGHT: Product Details ── -->
        <div class="col-md-6 col-12 d-flex flex-column justify-content-between">
            <div>
                <h1 class="font-heading text-dark mb-2 fs-3 fs-md-2" style="font-weight: 500 !important;">{{ $product->name }}</h1>

                <!-- Review Stars -->
                <div class="d-flex align-items-center mb-3 fs-6">
                    <div class="text-warning me-2" style="font-size:0.95rem;">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <span class="text-muted fw-semibold">({{ $product->reviews_count ?? 0 }} verified reviews)</span>
                </div>

                <!-- Pricing -->
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom flex-wrap gap-2" style="border-color:#f6f3eb !important;">
                    <div class="d-flex align-items-baseline gap-3">
                        <span class="display-5 fw-bold text-dark font-heading" id="productPrice">₹{{ number_format($product->sale_price, 0) }}</span>
                        <span class="fs-4 text-muted text-decoration-line-through fw-medium" id="productMrp">₹{{ number_format($product->mrp, 0) }}</span>
                    </div>
                    
                    @php
                        $bestCoupon = null;
                        $maxDiscountAmount = 0;
                        
                        if (isset($productCoupons) && $productCoupons->count() > 0) {
                            foreach($productCoupons as $coupon) {
                                $discount = $coupon->discount_type === 'percent' 
                                    ? $product->sale_price * ($coupon->discount_value / 100) 
                                    : $coupon->discount_value;
                                if ($discount > $maxDiscountAmount) {
                                    $maxDiscountAmount = $discount;
                                    $bestCoupon = $coupon;
                                }
                            }
                        }
                        $bestPrice = $product->sale_price - $maxDiscountAmount;
                    @endphp

                    @if($bestCoupon && $maxDiscountAmount > 0)
                        <div class="ms-md-2 mt-2 mt-md-0 d-inline-block">
                            <span class="badge px-3 py-2 text-success fw-semibold border border-success-subtle bg-success-subtle text-uppercase" style="font-size:0.75rem;" id="productBestPrice">
                                <i class="bi bi-tag-fill me-1"></i> Best Price ₹{{ number_format($bestPrice, 0) }} with {{ $bestCoupon->code }}
                            </span>
                        </div>
                    @else
                        <div class="ms-md-2 mt-2 mt-md-0 d-inline-block" style="display:none !important;" id="productBestPriceWrapper">
                            <span class="badge px-3 py-2 text-success fw-semibold border border-success-subtle bg-success-subtle text-uppercase" style="font-size:0.75rem; display:none;" id="productBestPrice"></span>
                        </div>
                    @endif
                </div>

                <!-- ── Variant Selector ── -->
                @if($product->variants->isNotEmpty())
                    @php
                        $defaultVariant = $product->variants->firstWhere('stock', '>', 0) ?? $product->variants->first();
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex flex-nowrap overflow-x-auto gap-2 pb-2" style="-webkit-overflow-scrolling:touch;scrollbar-width:none;">
                            @foreach($product->variants as $variant)
                                @php
                                    $varDiscount = $variant->mrp > $variant->sale_price ? round((($variant->mrp - $variant->sale_price) / $variant->mrp) * 100) : 0;
                                    $isVarDefault = ($defaultVariant->id === $variant->id);
                                @endphp
                                <div class="variant-card border p-2 rounded-3 text-center position-relative cursor-pointer d-flex flex-column justify-content-between {{ $isVarDefault ? 'active border-success bg-success-subtle' : '' }} {{ $variant->stock <= 0 ? 'opacity-50' : '' }}"
                                     data-id="{{ $variant->id }}"
                                    @php
                                        $variantMaxDiscount = 0;
                                        if (isset($bestCoupon) && $bestCoupon) {
                                            $variantMaxDiscount = $bestCoupon->discount_type === 'percent' 
                                                ? $variant->sale_price * ($bestCoupon->discount_value / 100) 
                                                : $bestCoupon->discount_value;
                                        }
                                        $variantBestPrice = $variant->sale_price - $variantMaxDiscount;
                                    @endphp
                                    
                                     data-mrp="₹{{ number_format($variant->mrp, 0) }}"
                                     data-price="₹{{ number_format($variant->sale_price, 0) }}"
                                     data-best-price="₹{{ number_format($variantBestPrice, 0) }}"
                                     data-best-coupon="{{ isset($bestCoupon) && $bestCoupon ? $bestCoupon->code : '' }}"
                                     data-stock="{{ $variant->stock }}"
                                     style="cursor:pointer;min-width:110px;width:auto;padding:8px 14px;min-height:82px;flex-shrink:0;">
                                    @if($varDiscount > 0)
                                        <span class="position-absolute badge bg-danger text-white px-1 rounded-1" style="font-size:0.55rem;top:4px;right:4px;color:#fff !important;">{{ $varDiscount }}% off</span>
                                    @endif
                                    @if($variant->stock <= 0)
                                        <span class="position-absolute badge bg-secondary px-1 rounded-1 w-100" style="font-size:0.55rem;top:20px;left:0;right:0;text-transform:uppercase;color:#fff !important;">Out of Stock</span>
                                    @endif
                                    <div class="d-flex align-items-center justify-content-center mt-1">
                                        <span class="fw-bold font-heading" style="font-size:0.85rem;">{{ $variant->weight }}</span>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="fw-bold text-success" style="font-size:0.9rem;">₹{{ number_format($variant->sale_price, 0) }}</span>
                                            @if($variant->mrp > $variant->sale_price)
                                                <span class="text-muted text-decoration-line-through" style="font-size:0.7rem;">₹{{ number_format($variant->mrp, 0) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ── Free Shipping Progress Bar ── -->
                @php
                    $initialPrice = $product->sale_price;
                    $freeShippingThreshold = $product->free_shipping_threshold ?? 0;
                    $percent = $freeShippingThreshold > 0 ? min(($initialPrice / $freeShippingThreshold) * 100, 100) : 100;
                    $needed = $freeShippingThreshold > 0 ? max($freeShippingThreshold - $initialPrice, 0) : 0;
                @endphp
                @if($freeShippingThreshold > 0)
                <div class="mb-4 p-3 bg-light rounded-4 border" style="border-color:#f6f3eb !important;" id="deliveryUnlockBox">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:0.8rem;">
                        <span class="fw-bold text-dark" id="deliveryMessage">
                            @if($needed > 0)
                                Add <span class="text-success fw-bold">₹<span id="deliveryNeededAmt">{{ number_format($needed, 0) }}</span></span> more to unlock <span class="text-success fw-bold">FREE Delivery</span>!
                            @else
                                🎉 <span class="text-success fw-bold">FREE Delivery</span> unlocked!
                            @endif
                        </span>
                        <span class="text-muted" data-threshold="{{ $freeShippingThreshold }}" id="deliveryTargetLabel">Target ₹{{ number_format($freeShippingThreshold, 0) }}</span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:3px;">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="deliveryProgressBar" role="progressbar" style="width:{{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                @endif

                <!-- ── Available Offers Swiper Slider ── -->
                @if(isset($productCoupons) && $productCoupons->isNotEmpty())
                <div class="mb-4 overflow-hidden">
                    <h6 class="fw-bold text-dark text-uppercase font-heading mb-3" style="font-size:0.8rem;letter-spacing:0.5px;"><i class="bi bi-gift-fill text-success me-1"></i>Available Offers:</h6>
                    <div class="swiper offers-slider overflow-visible" style="padding-bottom: 10px;">
                        <div class="swiper-wrapper">
                            @foreach($productCoupons as $coupon)
                            <div class="swiper-slide">
                                <div class="p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center copy-coupon-btn w-100" data-code="{{ $coupon->code }}" style="cursor:pointer;border-color:#f6f3eb !important;transition:var(--transition-smooth);">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-success font-heading py-2 px-2 text-uppercase text-white" style="color: #fff !important;">{{ $coupon->code }}</span>
                                        <div style="font-size:0.75rem;">
                                            <div class="fw-bold text-dark">
                                                @if($coupon->discount_type === 'percentage')
                                                    Get {{ (int)$coupon->discount_value }}% OFF
                                                @else
                                                    Get ₹{{ (int)$coupon->discount_value }} OFF
                                                @endif
                                            </div>
                                            <span class="text-muted">
                                                @if($coupon->min_amount > 0)
                                                    Above ₹{{ (int)$coupon->min_amount }}
                                                @else
                                                    No min value
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 coupon-copy-btn" style="font-size:0.7rem;font-weight:bold;pointer-events:none;">COPY</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="offers-slider-pagination text-center mt-2 swiper-pagination-clickable"></div>
                    </div>
                </div>
                @endif
            </div>

            <!-- ── CTA Buttons (Desktop only) ── -->
            <div class="desktop-cart-controls d-flex flex-wrap gap-3 align-items-center border-top pt-4" style="border-color:#f6f3eb !important;">
                @php
                    $defaultVariant = $product->variants->firstWhere('stock', '>', 0) ?? $product->variants->first();
                    $defaultStock = $defaultVariant ? $defaultVariant->stock : $product->stock;
                    $isInStock = $defaultStock > 0;
                @endphp
                <div id="desktopQtyInput" class="quantity-input border rounded-pill d-flex align-items-center bg-light {{ !$isInStock ? 'd-none' : '' }}" style="padding:5px 15px;">
                    <button type="button" class="btn border-0 p-1 bg-transparent text-dark fw-bold qty-minus"><i class="bi-dash-lg"></i></button>
                    <input type="number" id="purchaseQuantity" class="form-control border-0 bg-transparent text-center fw-bold shadow-none p-0" value="1" min="1" max="10" style="width:45px;font-size:1rem;">
                    <button type="button" class="btn border-0 p-1 bg-transparent text-dark fw-bold qty-plus"><i class="bi-plus-lg"></i></button>
                </div>

                <form action="{{ route('cart.add') }}" method="POST" id="mainAddToCartForm" class="flex-grow-1 m-0 {{ !$isInStock ? 'd-none' : '' }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="hiddenVariantId" value="{{ $product->variants->first()?->id }}">
                    <input type="hidden" name="quantity" id="hiddenQuantity" value="1">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-premium-outline btn-lg w-100 rounded-pill py-3 text-uppercase font-heading" style="font-size:0.85rem;height:52px;font-weight:bold;border-width:2px;">
                            <i class="bi bi-cart-plus me-2"></i> Add to Cart
                        </button>
                        <button type="button" id="btnBuyNowDirect" class="btn btn-premium w-100 rounded-pill py-3 text-uppercase font-heading" style="font-size:0.85rem;height:52px;font-weight:bold;background-color: var(--dark-green) !important; color:#ffffff !important; border-color: var(--dark-green) !important;">
                            Buy Now
                        </button>
                    </div>
                </form>

                <button type="button" id="desktopSoldOutBtn" class="btn btn-secondary btn-lg w-100 rounded-pill py-3 text-uppercase font-heading {{ $isInStock ? 'd-none' : '' }}" disabled style="font-size:0.85rem;height:52px;font-weight:bold;background-color:#95a5a6;border-color:#95a5a6;color:#ffffff;cursor:not-allowed;">
                    Sold Out
                </button>
            </div>

            <!-- Product Short Description (Hidden on Mobile) -->
            <div class="mt-3 pb-1 d-none d-md-block">
                <div id="shortDescText" class="text-muted fs-6 mb-0 short-desc-clamp" style="line-height:1.7;">{!! $product->short_description !!}</div>
                <button type="button" id="shortDescToggle" class="btn btn-link text-success p-0 mt-1 fw-semibold" style="font-size:0.82rem;text-decoration:none;">
                    <i class="bi bi-chevron-down me-1" id="shortDescIcon"></i><span id="shortDescLabel">Read More</span>
                </button>
            </div>

            <!-- Trust Badges (Single Row on Mobile) -->
            <div class="d-flex flex-nowrap justify-content-around align-items-center py-3 mt-2 border-top text-center overflow-x-auto" style="border-color:#f6f3eb !important; gap: 8px; scrollbar-width: none;">
                <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width: 75px;">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-1" style="width:34px;height:34px;"><i class="bi bi-truck fs-6"></i></span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.68rem;">Free Shipping</h6>
                    <span class="text-muted" style="font-size:0.6rem;">Above ₹499</span>
                </div>
                <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width: 75px;">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-1" style="width:34px;height:34px;"><i class="bi bi-headset fs-6"></i></span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.68rem;">360° Help</h6>
                    <span class="text-muted" style="font-size:0.6rem;">Instant Support</span>
                </div>
                <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width: 75px;">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-1" style="width:34px;height:34px;"><i class="bi bi-arrow-left-right fs-6"></i></span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.68rem;">7 Day Return</h6>
                    <span class="text-muted" style="font-size:0.6rem;">Hassle-Free</span>
                </div>
                <div class="d-flex flex-column align-items-center flex-shrink-0" style="min-width: 75px;">
                    <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-1" style="width:34px;height:34px;"><i class="bi bi-shield-check fs-6"></i></span>
                    <h6 class="fw-bold text-dark mb-0" style="font-size:0.68rem;">70+ Checks</h6>
                    <span class="text-muted" style="font-size:0.6rem;">Certified Pure</span>
                </div>
            </div>
        </div>
    </div>
</div>
