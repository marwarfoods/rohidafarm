@extends('layouts.app')

@section('content')
@push('styles')
    @vite(['resources/sass/shop.scss'])
@endpush

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

        @if($activeCategory)
            <!-- Category Banner (full image, subtle overlay, no text) -->
            @if($activeCategory->banner_image)
                <div class="position-relative overflow-hidden mb-3 category-banner">
                    <img src="{{ asset($activeCategory->banner_image) }}" alt="{{ $activeCategory->name }}" class="w-100 h-100 object-fit-cover" style="display:block;">
                    <div class="position-absolute top-0 start-0 w-100 h-100 category-banner-overlay"></div>
                </div>
            @endif

            <!-- Breadcrumb (plain, below banner) -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Shop</a></li>
                    <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">{{ $activeCategory->name }}</li>
                </ol>
            </nav>
        @else
            <!-- Category Capsule Slider (Replicated from Homepage) -->
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-start justify-content-lg-center gap-2 border-0 flex-nowrap overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
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
        @endif

        <div class="row g-4">
            <!-- Product Grid Column (Full Width — Sidebar Removed) -->
            <div class="col-12">
                
            <div id="product-grid-area">
                <span id="shownCountNum" class="d-none">{{ $products->count() }}</span>

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
