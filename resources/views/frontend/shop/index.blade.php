@extends('layouts.app')

@section('content')
@push('styles')
    @vite(['resources/sass/shop.scss'])
@endpush

<!-- Page Header -->
<section class="py-4 bg-light border-bottom">
    <div class="container-shop">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.85rem; font-family: 'DM Sans', sans-serif;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-dark" aria-current="page">Shop Catalog</li>
            </ol>
        </nav>
        <h1 class="display-6 font-heading fw-bold m-0">Our Organic Farm Products</h1>
    </div>
</section>

<!-- Shop Body Section -->
<section class="py-4" style="background-color: var(--cream-bg);">
    <div class="container-shop">

        @php
            $iconMap = [
                'ghee' => 'bi-droplet-fill',
                'cold-pressed-oil' => 'bi-funnel-fill',
                'honey' => 'bi-flower2',
                'spices' => 'bi-fire',
                'pickles' => 'bi-archive-fill',
                'dry-fruits' => 'bi-box2-heart-fill'
            ];
        @endphp

        <!-- Category Capsule Slider (Replicated from Homepage) -->
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-center gap-2 border-0 flex-nowrap overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
                <a href="{{ route('shop.index') }}" class="capsule-tab {{ !request('category') ? 'active' : '' }}">
                    <span class="icon-circle"><i class="bi bi-hand-thumbs-up-fill"></i></span>
                    <span class="tab-text">Bestseller</span>
                </a>
                @foreach($categories as $category)
                    @php
                        $iconClass = $iconMap[$category->slug] ?? 'bi-basket-fill';
                        $displayName = $category->name;
                        if ($category->slug == 'cold-pressed-oil') $displayName = 'Oils';
                    @endphp
                    <a href="{{ route('shop.category', ['category' => $category->slug]) }}" class="capsule-tab {{ request('category') === $category->slug ? 'active' : '' }}">
                        <span class="icon-circle"><i class="bi {{ $iconClass }}"></i></span>
                        <span class="tab-text">{{ $displayName }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="row g-4">
            <!-- Product Grid Column (Full Width — Sidebar Removed) -->
            <div class="col-12">
                
                <!-- Mobile Search & Sort row (Visible on Mobile Only, Sidebar Removed) -->
                <div class="d-flex d-lg-none flex-column gap-2 bg-white p-3 rounded-4 border mb-4 shadow-sm" style="border-color: var(--border-color) !important;">
                    <div class="input-group border rounded-pill overflow-hidden">
                        <input type="text" id="mobileSearchInput" class="form-control border-0 shadow-none bg-light p-2 ps-3" placeholder="Search products..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                        <button type="button" id="mobileSearchBtn" class="btn bg-light text-muted border-0 px-3"><i class="bi bi-search"></i></button>
                    </div>
                    <div class="d-flex align-items-center gap-1 justify-content-end">
                        <label for="mobileSortSelect" class="text-muted fw-bold mb-0 me-1" style="font-size: 0.75rem; white-space: nowrap;">Sort By:</label>
                        <select class="form-select border shadow-none bg-light p-2 py-1.5 rounded-pill" id="mobileSortSelect" style="font-size: 0.75rem; width: 140px;">
                            <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="price_low_high" {{ request('sort') === 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high_low" {{ request('sort') === 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Customer Rating</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        </select>
                    </div>
                </div>

            <div id="product-grid-area">
                <!-- Sorting & Top Bar (Visible on Desktop Only) -->
                <div class="bg-white p-3 rounded-4 shadow-sm border mb-4 d-none d-lg-flex justify-content-between align-items-center flex-wrap gap-2" style="border-color: var(--border-color) !important;">
                    <span class="text-muted fw-semibold" style="font-size: 0.85rem;"><i class="bi bi-grid-fill text-success me-2"></i>Showing <span id="shownCountNum">{{ $products->count() }}</span> of {{ $products->total() }} products</span>

                    <form id="desktopSearchForm" action="{{ request()->url() }}" method="GET" class="flex-grow-1 mx-3" style="max-width: 340px;">
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        <div class="input-group border rounded-pill overflow-hidden">
                            <input type="text" name="search" id="desktopSearchInput" class="form-control border-0 shadow-none bg-light p-2 ps-3" placeholder="Search products..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                            <button type="submit" class="btn bg-light text-muted border-0 px-3"><i class="bi bi-search"></i></button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center gap-2">
                        <label for="sort" class="text-muted fw-semibold" style="font-size: 0.85rem; white-space: nowrap;">Sort By:</label>
                        <select class="form-select border shadow-none bg-light p-2" id="sortSelect" style="font-size: 0.85rem; width: 180px;">
                            <option value="featured" {{ request('sort') === 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="price_low_high" {{ request('sort') === 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high_low" {{ request('sort') === 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Customer Rating</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid (2 per row on mobile, 5 per row on desktop) -->
                <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-3 g-md-4 mb-4" id="shopProductsGrid">
                    @forelse($products as $prod)
                        <div class="col">
                            <x-product-card :product="$prod" />
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 bg-white rounded-4 border">
                            <i class="bi bi-search display-1 text-muted"></i>
                            <h3 class="font-heading fw-bold mt-3">No Products Found</h3>
                            <p class="text-muted mb-0">Try clearing some filters or searching for other items.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Load More -->
                <div class="text-center mb-5" id="loadMoreWrapper" data-next-url="{{ $products->nextPageUrl() }}" style="{{ $products->hasMorePages() ? '' : 'display:none;' }}">
                    <button type="button" id="btnLoadMoreShop" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.9rem;">
                        Load More Products
                    </button>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
    @vite(['resources/js/shop.js'])
    <script>
        // Event delegation is required here: the Category Capsule / mobile filter AJAX
        // (shop.js) replaces #product-grid-area's innerHTML, which would detach a
        // directly-bound click listener on the Load More button.
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#btnLoadMoreShop');
            if (!btn) return;

            const wrapper = document.getElementById('loadMoreWrapper');
            const grid = document.getElementById('shopProductsGrid');
            const shownCountEl = document.getElementById('shownCountNum');
            if (!wrapper || !grid) return;

            const nextUrl = wrapper.getAttribute('data-next-url');
            if (!nextUrl) return;

            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Loading...';

            fetch(nextUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('shopProductsGrid');
                    const newItems = newGrid ? newGrid.querySelectorAll(':scope > .col') : [];
                    let addedCount = 0;
                    newItems.forEach(item => {
                        grid.appendChild(item);
                        addedCount++;
                    });

                    if (shownCountEl) {
                        shownCountEl.textContent = (parseInt(shownCountEl.textContent, 10) || 0) + addedCount;
                    }

                    const newWrapper = doc.getElementById('loadMoreWrapper');
                    const newNextUrl = newWrapper ? newWrapper.getAttribute('data-next-url') : null;

                    if (newNextUrl && newNextUrl !== 'null' && newNextUrl !== '') {
                        wrapper.setAttribute('data-next-url', newNextUrl);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    } else {
                        wrapper.style.display = 'none';
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
        });
    </script>
@endpush
