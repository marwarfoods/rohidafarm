<!-- ═══════════ MOBILE STICKY CTA ═══════════ -->
<div class="mobile-sticky-cta p-2 bg-white border-top shadow-lg" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1030;">
    @php
        $defaultVariant = $product->variants->firstWhere('stock', '>', 0) ?? $product->variants->first();
        $defaultStock = $defaultVariant ? $defaultVariant->stock : $product->stock;
        $isInStock = $defaultStock > 0;
    @endphp

    <form action="{{ route('cart.add') }}" method="POST" id="mobileAddToCartForm" class="m-0 w-100 {{ !$isInStock ? 'd-none' : '' }}">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="variant_id" id="mobileHiddenVariantId" value="{{ $product->variants->first()?->id }}">
        <input type="hidden" name="quantity" id="mobileHiddenQuantity" value="1">

        <div class="d-flex align-items-center justify-content-between gap-1.5 px-1">
            {{-- 1. ADD TO CART --}}
            <button type="submit" class="btn btn-success flex-grow-1 rounded-3 py-2 px-1 fw-bold font-heading text-uppercase text-white d-flex align-items-center justify-content-center" style="font-size: 0.78rem; background-color: var(--dark-green) !important; border-color: var(--dark-green) !important; height: 42px; white-space: nowrap;">
                Add to Cart
            </button>

            {{-- 2. QUANTITY SELECTOR (- 1 +) --}}
            <div id="mobileQtyInput" class="quantity-input border rounded-3 d-flex align-items-center justify-content-between bg-white px-2 shadow-sm" style="border-color: #dcd6c9 !important; width: 95px; height: 42px; flex-shrink: 0;">
                <button type="button" class="btn border-0 p-0 text-dark fw-bold qty-minus fs-5" style="line-height: 1;"><i class="bi bi-dash"></i></button>
                <input type="number" id="mobilePurchaseQuantity" class="form-control border-0 bg-transparent text-center fw-bold shadow-none p-0 text-dark" value="1" min="1" max="10" readonly style="width: 28px; font-size: 0.9rem;">
                <button type="button" class="btn border-0 p-0 text-dark fw-bold qty-plus fs-5" style="line-height: 1;"><i class="bi bi-plus"></i></button>
            </div>

            {{-- 3. BUY NOW --}}
            <button type="button" id="mobileBtnBuyNowDirect" class="btn btn-warning flex-grow-1 rounded-3 py-2 px-1 fw-bold font-heading text-uppercase text-dark d-flex align-items-center justify-content-center" style="font-size: 0.78rem; background-color: var(--gold-accent) !important; border-color: var(--gold-accent) !important; height: 42px; white-space: nowrap;">
                Buy Now
            </button>
        </div>
    </form>

    {{-- SOLD OUT BUTTON --}}
    <button type="button" id="mobileSoldOutBtn" class="btn btn-secondary w-100 py-3 rounded-3 text-uppercase font-heading {{ $isInStock ? 'd-none' : '' }}" disabled style="font-size:0.85rem;font-weight:bold;background-color:#95a5a6;border-color:#95a5a6;color:#ffffff;cursor:not-allowed;">
        Sold Out
    </button>
</div>
