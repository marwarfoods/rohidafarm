@props(['product'])

@php
    $price = $product->sale_price;
    $mrp = $product->mrp;
    $discount = $product->discountPercentage();
    $imagePath = $product->primaryImage ? asset($product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
    
    // Hover image extraction
    $galleryFirst = $product->gallery->first();
    $hoverImagePath = $galleryFirst ? asset($galleryFirst->image_path) : $imagePath;
    
    // Best Price calculation with dynamic coupon
    $bestCoupon = \App\Models\Coupon::where('is_active', true)
        ->where(function($q) {
            $q->whereNull('active_until')->orWhere('active_until', '>=', now());
        })
        ->orderByDesc('discount_value')
        ->first();

    $maxDiscountAmount = 0;
    if ($bestCoupon) {
        $maxDiscountAmount = $bestCoupon->discount_type === 'percent' 
            ? $price * ($bestCoupon->discount_value / 100) 
            : $bestCoupon->discount_value;
    }
    
    $bestPrice = $price - $maxDiscountAmount;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/product-card.css') }}">
@endpush

<div class="card h-100 border-0 rounded-4 overflow-hidden position-relative product-card shadow-sm bg-white" style="border: 1px solid var(--border-color) !important;" id="prod-card-{{ $product->id }}">
    <!-- Product Image Container -->
    <div class="position-relative overflow-hidden bg-light product-image-container" style="aspect-ratio: 1/1;">
        <!-- Discount Badge on top-left of image styled as hanging tag -->
        @if($discount > 0)
            <div class="position-absolute z-3 text-white fw-bold d-flex flex-column align-items-center justify-content-center product-discount-tag" style="background-color: var(--primary-green); font-size: 0.72rem; width: 38px; height: 42px; border-radius: 0 0 18px 18px; top: 0; left: 10px; box-shadow: 0 3px 6px rgba(0,0,0,0.12); line-height: 1.1; padding-top: 2px;">
                <span style="font-family: 'DM Sans', sans-serif; font-weight: 700;">{{ round($discount) }}%</span>
                <span style="font-size: 0.5rem; font-weight: 800; letter-spacing: 0.5px; opacity: 0.95; margin-top: 1px;">OFF</span>
            </div>
        @endif

        <a href="{{ route('shop.show', $product->slug) }}" class="d-block w-100 h-100">
            <img src="{{ $imagePath }}" alt="{{ $product->name }}" width="400" height="400" class="w-100 h-100 object-fit-cover product-card-img-primary" loading="lazy" decoding="async" style="transition: var(--transition-smooth); opacity: 1;">
            @if($hoverImagePath !== $imagePath)
                <img src="{{ $hoverImagePath }}" alt="{{ $product->name }} - alternate view" width="400" height="400" class="w-100 h-100 object-fit-cover product-card-img-hover position-absolute top-0 start-0" loading="lazy" decoding="async" style="transition: var(--transition-smooth); opacity: 0; z-index: 2;">
            @endif
        </a>

        <!-- Wishlist & Quick Actions Float (Visible on hover) -->
        <div class="product-actions position-absolute bottom-2 start-0 end-0 p-2 d-flex justify-content-center gap-2 z-3" style="transform: translateY(110%); transition: var(--transition-smooth);">
            <button class="btn bg-white text-dark rounded-circle border p-1.5 shadow-sm hover-gold btn-wishlist-toggle" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" data-id="{{ $product->id }}" aria-label="Add {{ $product->name }} to wishlist">
                <i class="bi bi-heart" style="font-size: 0.8rem;" aria-hidden="true"></i>
            </button>
            <a href="{{ route('shop.show', $product->slug) }}" class="btn bg-white text-dark rounded-circle border p-1.5 shadow-sm hover-gold" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" aria-label="View {{ $product->name }} details">
                <i class="bi bi-eye" style="font-size: 0.8rem;"></i>
            </a>
        </div>
    </div>

    <!-- Selling/Trending and Best Seller Badges (Positions below image, styled small & premium, aligned with min-height) -->
    <div class="px-3 pt-2 d-flex flex-wrap gap-1 align-items-center product-badge-row" style="min-height: 26px;">
        <!-- Selling / Trending Badge -->
        @if($product->is_featured)
            <span class="badge text-dark font-heading fw-bold d-inline-flex align-items-center gap-1 py-0.5 px-2" style="background-color: #e3f2fd; color: #0d47a1 !important; font-size: 0.6rem; border-radius: 5px; text-transform: uppercase; letter-spacing: 0.3px;">
                <i class="bi bi-lightning-charge-fill"></i> Selling Fast
            </span>
        @elseif($product->is_trending)
            <span class="badge text-dark font-heading fw-bold d-inline-flex align-items-center gap-1 py-0.5 px-2" style="background-color: #e8f5e9; color: #1b5e20 !important; font-size: 0.6rem; border-radius: 5px; text-transform: uppercase; letter-spacing: 0.3px;">
                <i class="bi bi-star-fill text-warning"></i> Top Rated
            </span>
        @endif

        <!-- Best Seller Badge -->
        @if($product->is_best_seller)
            <span class="badge text-uppercase font-heading fw-bold py-0.5 px-2" style="background-color: #fff8e1; color: #f57f17 !important; font-size: 0.6rem; border-radius: 5px; letter-spacing: 0.3px;">
                <i class="bi bi-star-fill me-0.5"></i> Best Seller
            </span>
        @endif
    </div>

    <!-- Product Details -->
    <div class="card-body px-3 pb-2.5 pt-1 d-flex flex-column justify-content-between">
        <div>
            <!-- Star Ratings and Reviews Count -->
            <div class="d-flex align-items-center mb-1">
                <div class="text-warning me-1.5" style="font-size: 0.7rem;">
                    <i class="bi bi-star-fill"></i>
                </div>
                <span class="text-dark fw-bold" style="font-size: 0.72rem;">{{ number_format($product->rating, 1) }}</span>
            </div>
 
            <!-- Title -->
            <h6 class="card-title text-dark font-heading mb-1" style="font-size: 0.92rem; line-height: 1.3; min-height: 2.35rem; font-weight: 500 !important;">
                <a href="{{ route('shop.show', $product->slug) }}" class="text-dark text-decoration-none hover-gold product-card-title">{{ $product->name }}</a>
            </h6>
 
            <!-- Variant Dropdown & Price Row -->
            <div class="d-flex align-items-center justify-content-between mb-2 mt-1.5 price-select-row border-top pt-1.5" style="border-color: var(--border-color) !important;">
                <!-- Left Side: Variant Dropdown (Wider, takes 50% width) -->
                <div class="w-50">
                    @if($product->variants->count() > 1)
                        <select class="form-select form-select-sm variant-selector border rounded-pill py-0.5 px-2" style="font-size: 0.72rem; font-weight: 600; background-color: #fcfbfa; border-color: #ECE7DD; width: 100%; height: auto;" data-product-id="{{ $product->id }}">
                            @foreach($product->variants as $variant)
                                <option value="{{ $variant->id }}" 
                                        data-price="{{ $variant->sale_price }}" 
                                        data-mrp="{{ $variant->mrp }}" 
                                        data-weight="{{ $variant->weight }}"
                                        data-stock="{{ $variant->stock }}"
                                        {{ $variant->stock <= 0 ? 'disabled' : '' }}
                                        {{ $variant->weight == $product->weight && $variant->stock > 0 ? 'selected' : '' }}>
                                    {{ $variant->weight }} {{ $variant->stock <= 0 ? '(Out of Stock)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="d-flex align-items-center">
                            <span class="badge rounded-pill border px-2 py-1 text-dark" style="font-size: 0.72rem; font-weight: 600; background-color: #fcfbfa; border-color: #ECE7DD !important;">
                                {{ $product->variants->first()?->weight ?: $product->weight }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Right Side: Pricing (Takes 50% width, aligned end) -->
                <div class="w-50 d-flex flex-column align-items-end justify-content-center price-container">
                    <div class="d-flex align-items-center gap-1 justify-content-end">
                        <span class="text-muted text-decoration-line-through fw-medium card-mrp-price" style="font-size: 0.72rem;">₹{{ number_format($mrp, 0) }}</span>
                        <span class="fw-bold text-dark font-heading card-sale-price" style="font-size: 1.05rem;">₹{{ number_format($price, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <!-- ADD TO CART BUTTON (Full Width & Premium Pill Shape) -->
            <div class="d-flex align-items-center mt-1 w-100 card-cta-wrapper">
                @php
                    $defaultVariant = $product->variants->firstWhere('stock', '>', 0) ?? $product->variants->first();
                    $defaultStock = $defaultVariant ? $defaultVariant->stock : $product->stock;
                    $isInStock = $defaultStock > 0;
                @endphp
                <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form m-0 w-100 {{ !$isInStock ? 'd-none' : '' }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}" class="card-product-id">
                    <input type="hidden" name="variant_id" value="{{ $product->variants->first()?->id }}" class="card-variant-id">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-premium w-100 rounded-pill text-uppercase font-heading d-flex align-items-center justify-content-center" style="padding: 6px 10px; font-size: 0.74rem; font-weight: bold; background-color: var(--primary-green); border-color: var(--primary-green); letter-spacing: 0.3px;">
                        <i class="bi bi-bag-plus-fill me-1.5" style="font-size: 0.85rem;"></i> <span>ADD TO CART</span>
                    </button>
                </form>
                <button class="btn btn-secondary w-100 rounded-pill text-uppercase font-heading disabled m-0 card-sold-out-btn {{ $isInStock ? 'd-none' : '' }}" style="padding: 6px 10px; font-size: 0.74rem; letter-spacing: 0.5px;">
                    SOLD OUT
                </button>
            </div>

            <!-- Best Price indicator below button -->
            @if($bestCoupon && $maxDiscountAmount > 0)
                <div class="text-center mt-1.5">
                    <span class="text-success fw-semibold card-best-price" style="font-size: 0.62rem; letter-spacing: -0.1px; background: rgba(36, 132, 67, 0.05); padding: 2px 8px; border-radius: 4px; display: inline-block;">
                        <i class="bi bi-tag-fill me-1"></i>Best Price ₹{{ number_format($bestPrice, 0) }} with {{ $bestCoupon->code }}
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
