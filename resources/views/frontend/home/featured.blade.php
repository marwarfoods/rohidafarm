<section class="py-md-5 py-3 position-relative overflow-hidden" id="featured-products"
    style="background: url('{{ asset('images/vectors/bg5.png') }}') center center / cover no-repeat;">

    {{-- Soft warm overlay for crisp UI contrast --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(250, 247, 238, 0.35); z-index: 0; pointer-events: none;"></div>

    <div class="container-fluid px-md-4 px-2 position-relative" style="max-width: 100%; z-index: 1;">
        <div class="text-center mb-md-5 mb-2" data-aos="fade-up">
            <h2 class="display-5 font-heading fw-bold mt-1 mb-1">Featured Products</h2>
            <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">Shop By Categories</span>
            
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
            <!-- Category Navigation Tabs styled as capsules -->
            <ul class="nav nav-pills justify-content-md-center justify-content-start mt-md-4 mt-2 gap-2 border-0 flex-nowrap overflow-x-auto pb-2" id="productTabs" role="tablist" style="scrollbar-width: none; -ms-overflow-style: none;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active capsule-tab" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-products" type="button" role="tab" aria-controls="all-products" aria-selected="true">
                        <span class="icon-circle"><i class="bi bi-hand-thumbs-up-fill"></i></span>
                        <span class="tab-text">Bestseller</span>
                    </button>
                </li>
                @foreach($categories as $category)
                    @if(!is_object($category)) @continue @endif
                    @php
                        $iconClass = $iconMap[$category->slug] ?? 'bi-basket-fill';
                        // Friendly display names
                        $displayName = $category->name;
                        if ($category->slug == 'cold-pressed-oil') $displayName = 'Oils';
                    @endphp
                    <li class="nav-item" role="presentation">
                        <button class="nav-link capsule-tab" id="{{ $category->slug }}-tab" data-bs-toggle="tab" data-bs-target="#cat-{{ $category->slug }}" type="button" role="tab" aria-controls="cat-{{ $category->slug }}" aria-selected="false">
                            <span class="icon-circle"><i class="bi {{ $iconClass }}"></i></span>
                            <span class="tab-text">{{ $displayName }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content" id="productTabsContent" data-aos="fade-up">
            <!-- All products -->
            <div class="tab-pane fade show active" id="all-products" role="tabpanel" aria-labelledby="all-tab">
                <div class="tab-pane-content-wrapper products-slider-wrapper position-relative px-2">
                    <!-- Section Skeleton Overlay -->
                    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none; padding: 15px 8px;">
                        <div class="row w-100 g-4 flex-nowrap overflow-hidden mx-auto">
                            @for($i = 0; $i < 4; $i++)
                                <div class="col-lg-3 col-md-4 col-6" style="flex-shrink: 0;">
                                    <div class="card h-100 border-0 bg-white p-3 rounded-4 border shadow-sm" style="border-color: var(--border-color) !important;">
                                        <!-- Product Card Image skeleton -->
                                        <div class="skeleton-block mb-3 w-100" style="aspect-ratio: 1/1;"></div>
                                        <!-- Product Title skeleton -->
                                        <div class="skeleton-block mb-2 w-100" style="height: 14px;"></div>
                                        <!-- Weight capsule skeleton -->
                                        <div class="skeleton-block mb-3" style="height: 10px; width: 45%;"></div>
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <!-- Price skeleton -->
                                            <div class="skeleton-block" style="height: 14px; width: 50px;"></div>
                                            <!-- Add to Cart button skeleton -->
                                            <div class="skeleton-block" style="height: 32px; width: 80px; border-radius: 20px;"></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <div class="swiper products-slider" style="overflow: hidden;">
                        <div class="swiper-wrapper">
                            @forelse($featuredProducts as $product)
                                <div class="swiper-slide py-3 h-auto">
                                    <x-product-card :product="$product" />
                                </div>
                            @empty
                                <div class="swiper-slide text-center text-muted py-5 w-100">No products available.</div>
                            @endforelse
                        </div>
                        <!-- Scrollbar (Desktop only) -->
                        <div class="swiper-scrollbar mt-4 d-none d-md-block"></div>
                        <!-- Pagination (Mobile only) -->
                        <div class="swiper-pagination d-block d-md-none mt-2"></div>
                    </div>
                </div>
                <!-- See All button aligned to right of the slider bounds -->
                <div class="d-flex justify-content-end mt-2 px-md-5 px-3">
                    <a href="/shop" class="text-decoration-none fw-bold font-heading text-success" style="font-size: 0.9rem;" aria-label="See all products in shop">
                        See All <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Category Specific Tab Panes -->
            @foreach($categories as $category)
                @if(!is_object($category)) @continue @endif
                <div class="tab-pane fade" id="cat-{{ $category->slug }}" role="tabpanel" aria-labelledby="{{ $category->slug }}-tab">
                    <div class="tab-pane-content-wrapper products-slider-wrapper position-relative px-2">
                        <!-- Section Skeleton Overlay -->
                        <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none; padding: 15px 8px;">
                            <div class="row w-100 g-4 flex-nowrap overflow-hidden mx-auto">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="col-lg-3 col-md-4 col-6" style="flex-shrink: 0;">
                                        <div class="card h-100 border-0 bg-white p-3 rounded-4 border shadow-sm" style="border-color: var(--border-color) !important;">
                                            <!-- Product Card Image skeleton -->
                                            <div class="skeleton-block mb-3 w-100" style="aspect-ratio: 1/1;"></div>
                                            <!-- Product Title skeleton -->
                                            <div class="skeleton-block mb-2 w-100" style="height: 14px;"></div>
                                            <!-- Weight capsule skeleton -->
                                            <div class="skeleton-block mb-3" style="height: 10px; width: 45%;"></div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <!-- Price skeleton -->
                                                <div class="skeleton-block" style="height: 14px; width: 50px;"></div>
                                                <!-- Add to Cart button skeleton -->
                                                <div class="skeleton-block" style="height: 32px; width: 80px; border-radius: 20px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="swiper products-slider" style="overflow: hidden;">
                            <div class="swiper-wrapper">
                                @forelse($category->products as $product)
                                    <div class="swiper-slide py-3 h-auto">
                                        <x-product-card :product="$product" />
                                    </div>
                                @empty
                                    <div class="swiper-slide text-center text-muted py-5 w-100">No products available in {{ $category->name }} category.</div>
                                @endforelse
                            </div>
                            <!-- Scrollbar (Desktop only) -->
                            <div class="swiper-scrollbar mt-4 d-none d-md-block"></div>
                            <!-- Pagination (Mobile only) -->
                            <div class="swiper-pagination d-block d-md-none mt-2"></div>
                        </div>
                    </div>
                    <!-- See All button aligned to right of the slider bounds -->
                    <div class="d-flex justify-content-end mt-2 px-md-5 px-3">
                        <a href="/shop?category={{ $category->slug }}" class="text-decoration-none fw-bold font-heading text-success" style="font-size: 0.9rem;">
                            See All <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
