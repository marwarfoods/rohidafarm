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
                <div class="position-relative overflow-hidden mb-4 category-banner">
                    <img src="{{ asset($activeCategory->banner_image) }}" alt="{{ $activeCategory->name }}" class="w-100 h-100 object-fit-cover" style="display:block;">
                    <div class="position-absolute top-0 start-0 w-100 h-100 category-banner-overlay"></div>
                </div>
            @endif
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

        {{-- ── 1. Global FAQs Section ── --}}
        @if(isset($globalFaqs) && $globalFaqs->isNotEmpty())
            <div class="my-5">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border" style="border-color:#f6f3eb !important;">
                    <div class="text-center mb-4">
                        <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">Have Questions?</span>
                        <h3 class="font-heading fw-bold mt-1 text-dark display-6">Frequently Asked Questions</h3>
                    </div>

                    <div class="accordion mx-auto" id="shopFaqAccordion" style="max-width: 820px;">
                        @foreach($globalFaqs as $index => $faq)
                            <div class="accordion-item border-0 border-bottom" style="border-color:#f6f3eb !important;">
                                <h2 class="accordion-header" id="shopFaqHeading{{ $faq->id ?? $index }}">
                                    <button class="accordion-button collapsed fw-bold text-dark bg-transparent shadow-none px-2 py-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#shopFaqCollapse{{ $faq->id ?? $index }}"
                                            aria-expanded="false" aria-controls="shopFaqCollapse{{ $faq->id ?? $index }}" style="font-size: 0.95rem;">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="shopFaqCollapse{{ $faq->id ?? $index }}" class="accordion-collapse collapse" aria-labelledby="shopFaqHeading{{ $faq->id ?? $index }}" data-bs-parent="#shopFaqAccordion">
                                    <div class="accordion-body text-muted px-2 pb-3 pt-0" style="font-size: 0.88rem; line-height: 1.75;">
                                        {{ $faq->answer }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- ── 2. Featured Video Reviews (Homepage Component) ── --}}
        @if(isset($videoReviews) && $videoReviews->isNotEmpty())
            <div class="my-5 rounded-4 overflow-hidden shadow-sm">
                @include('frontend.home.videos')
            </div>
        @endif

        {{-- ── 3. Explore More Products (Shown only on Category pages) ── --}}
        @if($activeCategory && isset($exploreProducts) && $exploreProducts->isNotEmpty())
            <div class="my-5">
                <div class="text-center mb-4">
                    <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">More from Rohida Farm</span>
                    <h2 class="display-6 font-heading fw-bold mt-1 mb-0">Explore More Products</h2>
                </div>
                
                <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 g-3 g-md-4">
                    @foreach($exploreProducts as $prod)
                        <div class="col">
                            <x-product-card :product="$prod" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
    @vite(['resources/js/shop.js', 'resources/js/home.js'])
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
