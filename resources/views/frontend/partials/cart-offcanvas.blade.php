<style>
    /* Cart Offcanvas Hover & Text Styling */
    .btn-outline-success:hover,
    .btn-success:hover {
        color: #ffffff !important;
    }
    .btn-offcanvas-remove:hover i {
        color: #dc3545 !important;
    }
</style>

<div class="offcanvas offcanvas-end border-0 shadow-lg" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 440px; background-color: #F9F8F6;">
    @php
        $cartService = app(\App\Services\CartService::class);
        $cartItems = $cartService->getItems();
        $cartTotals = $cartService->getTotals();
        $isEmpty = $cartItems->isEmpty();
        $totalItemCount = $cartItems->sum('quantity');
    @endphp

    <!-- ── Header Bar (Matching Anveshan Layout) ── -->
    <div class="offcanvas-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <button type="button" class="btn border-0 p-0 text-dark fs-4 shadow-none" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="bi bi-chevron-left"></i>
        </button>
        <h5 class="offcanvas-title font-heading fw-bold m-0 fs-4" id="cartOffcanvasLabel" style="color: #174C38;">
            Your Cart
        </h5>
        <div style="width: 24px;"></div>
    </div>

    <!-- ── Trust Banner Strip ── -->
    <div class="py-2 text-center text-white fw-semibold" style="background-color: #174C38; font-size: 0.78rem; letter-spacing: 0.5px;">
        <i class="bi bi-shield-lock-fill me-1"></i> Secure Payment • FSSAI Certified
    </div>

    <div class="offcanvas-body d-flex flex-column justify-content-between p-3 p-md-4 position-relative overflow-x-hidden">

        <!-- Skeleton Loader Overlay -->
        @include('frontend.partials.cart-skeleton')

        <!-- Empty Cart Container -->
        <div id="cartEmptyContent" class="text-center my-auto py-5 w-100 {{ !$isEmpty ? 'd-none' : '' }}">
            <img src="{{ asset('images/emtycart.png') }}" alt="Empty Cart" class="mb-3 img-fluid" style="max-width: 150px;">
            <h4 class="font-heading fw-bold mt-2" style="color: #174C38;">Your Cart is Empty</h4>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Discover our pure A2 Bilona ghee, cold-pressed oils, and organic wild honey.</p>
            <a href="{{ route('shop.index') }}" class="btn rounded-pill px-4 py-2.5 text-white fw-bold font-heading text-uppercase shadow-sm" style="background-color: #174C38; font-size: 0.8rem; color: #ffffff !important;">Explore Catalog</a>
        </div>

        <!-- Active Cart Content -->
        <div id="cartActiveContent" class="d-flex flex-column justify-content-between h-100 w-100 {{ $isEmpty ? 'd-none' : '' }}">
            
            <!-- Scrollable Middle Section -->
            <div class="cart-items-scroll flex-grow-1 overflow-y-auto pe-1 mb-2" style="max-height: calc(100vh - 250px);">
                
                <!-- Cart Details Sub-header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold font-heading d-flex align-items-center gap-1" style="color: #174C38; font-size: 0.95rem;">
                        <i class="bi bi-cart3 fs-5"></i> Cart details
                    </span>
                    <small class="text-muted fw-semibold" style="font-size: 0.82rem;">Total items: {{ $totalItemCount }}</small>
                </div>

                <!-- Items List Cards -->
                <div class="d-flex flex-column gap-3 mb-3">
                    @foreach($cartItems as $item)
                        @php
                            $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
                            
                            // Extract clean short weight text (e.g. 200 GM or 1L jar)
                            $rawWeight = $item->variant ? ($item->variant->weight ?? $item->variant->name) : $item->product->weight;
                            if (str_contains($rawWeight, '-')) {
                                $parts = explode('-', $rawWeight);
                                $weight = trim(end($parts));
                            } else {
                                $weight = $rawWeight;
                            }
                            
                            $image = $item->product->primaryImage ? asset($item->product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                        @endphp
                        <div class="p-3 bg-white rounded-3 border shadow-2xs position-relative cart-item-row" id="offcanvasRow_{{ $item->cart_id }}" data-price="{{ $price }}" style="border-color: #E8E5DF !important;">
                            <div class="d-flex gap-3 align-items-start">
                                <img src="{{ $image }}" onerror="this.onerror=null;this.src='/assets/images/products/placeholder.jpg';" class="rounded-3 border object-fit-cover bg-light" style="width: 70px; height: 70px; flex-shrink: 0;">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="fw-semibold text-dark m-0 mb-1" style="font-family: var(--font-body); font-size: 0.88rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $item->product->name }}
                                    </h6>
                                    
                                    @if($weight)
                                        <span class="badge rounded-1 px-2 py-0.5 mb-2 d-inline-block" style="background-color: #E0F2F1; color: #004D40; font-size: 0.68rem; font-weight: 600;">
                                            {{ $weight }}
                                        </span>
                                    @endif

                                    <div class="fw-bold text-dark mb-2" style="font-size: 0.95rem;">
                                        ₹{{ number_format($price * $item->quantity, 0) }}
                                    </div>

                                    <!-- Bottom Row: Pill Qty Selector Left, Trash Icon Right -->
                                    <div class="d-flex align-items-center justify-content-between w-100">
                                        <div class="quantity-input border rounded-pill d-inline-flex align-items-center bg-white px-2 py-0.5" style="border-color: #CCC !important;">
                                            <button type="button" class="btn border-0 p-0 text-dark btn-offcanvas-dec" data-id="{{ $item->cart_id }}" style="font-size: 0.85rem;"><i class="bi bi-dash"></i></button>
                                            <input type="number" class="form-control border-0 bg-transparent text-center fw-bold shadow-none p-0 offcanvas-qty text-dark" value="{{ $item->quantity }}" style="width: 28px; font-size: 0.82rem;" readonly>
                                            <button type="button" class="btn border-0 p-0 text-dark btn-offcanvas-inc" data-id="{{ $item->cart_id }}" style="font-size: 0.85rem;"><i class="bi bi-plus"></i></button>
                                        </div>

                                        <button class="btn btn-sm text-secondary border-0 p-1 btn-offcanvas-remove" data-id="{{ $item->cart_id }}" title="Remove Item">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Add More Items Button -->
                <div class="mb-3">
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-success w-100 rounded-pill py-2 fw-semibold font-heading d-flex align-items-center justify-content-center gap-1" style="color: #174C38; border-color: #174C38; font-size: 0.85rem;">
                        Add more items <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>

                <!-- Offer Announcement Strip -->
                <div class="p-2.5 rounded-3 text-center mb-3 fw-bold" style="background-color: #FEF8E7; color: #9E6C00; font-size: 0.82rem;">
                    Unlock your best offer at checkout!
                </div>

                <!-- Recommended Add-Ons (Hot Choices — Selling Right Now: IN STOCK ONLY) -->
                @php
                    $cartAddons = \App\Models\Product::with(['primaryImage', 'variants'])
                        ->where('is_active', true)
                        ->where('stock', '>', 0)
                        ->take(6)
                        ->get();
                @endphp
                @if($cartAddons->isNotEmpty())
                    <div class="pt-2 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="font-heading fw-bold text-dark m-0" style="font-size: 0.92rem; color: #174C38 !important;">
                                Hot Choices – Selling Right Now <i class="bi bi-graph-up-arrow text-danger ms-1" style="font-size: 0.85rem;"></i>
                            </h6>
                        </div>

                        <!-- Horizontal Slider Cards with Proper Gap, Padding & No Star Badges -->
                        <div class="d-flex flex-nowrap overflow-x-auto gap-3 pb-3 pt-1" style="-webkit-overflow-scrolling: touch; scrollbar-width: none;">
                            @foreach($cartAddons as $addon)
                                @php
                                    $addonPrice = $addon->sale_price;
                                    $addonImg = $addon->primaryImage ? asset($addon->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                                @endphp
                                <div class="bg-white p-3 rounded-3 border d-flex align-items-center justify-content-between shadow-2xs" style="min-width: 290px; width: 290px; flex-shrink: 0; border-color: #E8E5DF !important;">
                                    
                                    <!-- Left Column: Image, Title & Price -->
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden pe-2">
                                        <img src="{{ $addonImg }}" onerror="this.onerror=null;this.src='/assets/images/products/placeholder.jpg';" width="54" height="54" class="rounded-2 object-fit-cover border bg-light flex-shrink-0">
                                        <div class="overflow-hidden">
                                            <h6 class="fw-semibold text-dark m-0 mb-1" style="font-family: var(--font-body); font-size: 0.82rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                {{ $addon->name }}
                                            </h6>
                                            <div class="fw-bold" style="font-size: 0.88rem; color: #174C38;">
                                                ₹{{ number_format($addonPrice, 0) }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Add Button -->
                                    <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form m-0 flex-shrink-0">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $addon->id }}">
                                        <input type="hidden" name="variant_id" value="{{ $addon->variants->first()?->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 py-1.5 text-white fw-bold shadow-2xs" style="font-size: 0.8rem; background-color: #174C38; border-color: #174C38; color: #ffffff !important; min-width: 60px;">
                                            + Add
                                        </button>
                                    </form>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Sticky Footer Action Bar (Matching Anveshan Layout) -->
            <div class="border-top pt-3 bg-white p-3 rounded-4 border shadow-sm mt-auto" style="border-color: #E8E5DF !important;">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <!-- Left: Subtotal & Online Offer Subtext -->
                    <div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-bold font-heading text-dark fs-4 price-container" id="offcanvasSubtotal">₹{{ number_format($cartTotals['subtotal'], 0) }}</span>
                            <i class="bi bi-chevron-down text-dark fs-6"></i>
                        </div>
                        <div class="badge rounded-1 px-2 py-1 mt-1 text-start fw-normal" style="background-color: #E8F5E9; color: #2E7D32; font-size: 0.7rem; white-space: normal;">
                            Or ₹{{ number_format(max($cartTotals['subtotal'] * 0.95, 0), 0) }} with online payment
                        </div>
                    </div>

                    <!-- Right: Continue Button -->
                    <a href="{{ route('checkout.index') }}" class="btn btn-lg rounded-pill px-4 py-2.5 text-white fw-bold font-heading shadow-sm" style="background-color: #174C38; border-color: #174C38; font-size: 0.92rem; min-width: 140px; color: #ffffff !important;">
                        Continue
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Global Sticky Cart Widget (Mobile & PC) -->
@if(!$isEmpty)
<style>
    .sticky-cart-pill {
        bottom: 85px;
    }
    @media (min-width: 768px) {
        .sticky-cart-pill {
            bottom: 25px;
        }
    }
    .page-checkout #stickyCartWidget,
    .page-cart #stickyCartWidget {
        display: none !important;
    }
</style>
<div id="stickyCartWidget" class="sticky-cart-pill position-fixed d-flex align-items-center justify-content-between p-2 shadow-lg" style="left: 50%; transform: translateX(-50%); z-index: 1030; background-color: #174C38; border-radius: 50px; width: 92%; max-width: 400px; transition: var(--transition-smooth); cursor: pointer;" onclick="document.querySelector('[data-bs-target=\'#cartOffcanvas\']') ? document.querySelector('[data-bs-target=\'#cartOffcanvas\']').click() : new bootstrap.Offcanvas(document.getElementById('cartOffcanvas')).show();">
    <div class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center position-relative" style="margin-left: 5px;">
            @php $count = 0; @endphp
            @foreach($cartItems as $item)
                @if($count < 3)
                    @php
                        $image = $item->product->primaryImage ? asset($item->product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                    @endphp
                    <img src="{{ $image }}" onerror="this.onerror=null;this.src='/assets/images/products/placeholder.jpg';" class="rounded-circle border border-2 border-white object-fit-cover shadow-sm" style="width: 32px; height: 32px; {{ $count > 0 ? 'margin-left: -12px;' : '' }} position: relative; z-index: {{ 10 - $count }}; background: #fff;">
                @endif
                @php $count++; @endphp
            @endforeach
            @if($cartItems->count() > 3)
                <div class="rounded-circle border border-2 border-white bg-light text-dark d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; margin-left: -12px; position: relative; z-index: 5; font-size: 0.7rem; font-weight: bold;">
                    +{{ $cartItems->count() - 3 }}
                </div>
            @endif
        </div>
        <div class="text-white ms-2 lh-sm">
            <div style="font-size: 0.75rem; color: rgba(255,255,255,0.85);">{{ $cartItems->sum('quantity') }} item{{ $cartItems->sum('quantity') > 1 ? 's' : '' }}</div>
            <div class="fw-bold font-heading price-container text-white" style="font-size: 0.95rem;">₹{{ number_format($cartTotals['subtotal'], 0) }}</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-1 fw-bold rounded-pill px-3 py-1 me-1 shadow-sm" style="font-size: 0.85rem; height: 36px; background-color: #E6C875 !important; color: #ffffff !important;">
        <i class="bi bi-cart3"></i>
        <i class="bi bi-arrow-right"></i>
    </div>
</div>
@else
<div id="stickyCartWidget" class="d-none"></div>
@endif
