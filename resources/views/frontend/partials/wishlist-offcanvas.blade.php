<div class="offcanvas offcanvas-end" tabindex="-1" id="wishlistOffcanvas" aria-labelledby="wishlistOffcanvasLabel" style="width: 420px; border-left: 1px solid var(--border-color);">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title font-heading fw-bold text-dark d-flex align-items-center" id="wishlistOffcanvasLabel">
            <i class="bi bi-heart text-success me-2 fs-4"></i>My Wishlist
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-4 bg-light">
        @php
            $wishlistItems = collect();
            if (Auth::check()) {
                $wishlistItems = \App\Models\Wishlist::with(['product.images'])->where('user_id', Auth::id())->get();
            } else {
                $sessionWishlist = session('wishlist', []);
                foreach ($sessionWishlist as $prodId) {
                    $prod = \App\Models\Product::with('images')->find($prodId);
                    if ($prod) {
                        $wishlistItems->push((object)[
                            'product' => $prod
                        ]);
                    }
                }
            }
        @endphp

        @if($wishlistItems->isEmpty())
            <div class="text-center my-auto py-5">
                <i class="bi bi-heartbreak text-muted" style="font-size: 4rem;"></i>
                <h4 class="font-heading fw-bold mt-4">Your Wishlist is Empty</h4>
                <p class="text-muted" style="font-size: 0.85rem;">Save your favorite organic items here to purchase them later.</p>
            </div>
        @else
            <!-- Scrollable Items List -->
            <div class="wishlist-scroll flex-grow-1 overflow-y-auto mb-3" style="max-height: 80vh;">
                @foreach($wishlistItems as $item)
                    @php
                        $prod = $item->product;
                        $price = $prod->sale_price;
                        $image = $prod->primaryImage ? asset($prod->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                    @endphp
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom align-items-center" id="offcanvasWishlistRow_{{ $prod->id }}">
                        <img src="{{ $image }}" class="rounded-3 border object-fit-cover" style="width: 65px; height: 65px;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold font-heading text-dark m-0 fs-6">{{ $prod->name }}</h6>
                            <span class="fw-bold text-success d-block mb-2" style="font-size: 0.9rem;">₹{{ number_format($price, 2) }}</span>
                            
                            <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $prod->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-premium px-3 py-1 rounded-pill font-heading" style="font-size: 0.7rem;"><i class="bi bi-cart-plus me-1"></i> Add to Cart</button>
                            </form>
                        </div>
                        <button class="btn btn-sm btn-outline-danger border-0 p-1 btn-offcanvas-wishlist-remove" data-id="{{ $prod->id }}"><i class="bi bi-trash"></i></button>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
