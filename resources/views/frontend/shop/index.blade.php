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
            <div class="d-flex align-items-center gap-2 border-0 flex-nowrap overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
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
            <!-- Sidebar Filters (Visible on Desktop Only) -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="shop-sidebar-wrapper">
                    <form action="{{ request()->url() }}" method="GET" id="filterForm">
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        <!-- 1. Search Box -->
                        <div class="mb-4">
                            <h6 class="sidebar-heading">Search</h6>
                            <div class="input-group border rounded overflow-hidden">
                                <input type="text" name="search" class="form-control border-0 shadow-none bg-light p-2" placeholder="Search products..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                                <button type="submit" class="btn bg-light text-muted border-0"><i class="bi bi-search"></i></button>
                            </div>
                        </div>

                        <!-- 2. Price Range Filter (Dual Range) -->
                        <div class="mb-4">
                            <h6 class="sidebar-heading">Price Range (₹)</h6>
                            <div class="dual-range-track-container">
                                <div class="dual-range-track-fill" id="PriceTrackFill"></div>
                                <input type="range" class="dual-range-input" id="MinPriceRange" min="0" max="5000" step="50" value="{{ request('min_price', 0) }}">
                                <input type="range" class="dual-range-input" id="MaxPriceRange" min="0" max="5000" step="50" value="{{ request('max_price', 5000) }}">
                            </div>
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted fw-semibold">₹</span>
                                    <input type="number" name="min_price" id="MinPriceInput" class="form-control text-center fw-bold text-success" min="0" max="5000" value="{{ request('min_price', 0) }}">
                                </div>
                                <span class="text-muted fw-bold">-</span>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light text-muted fw-semibold">₹</span>
                                    <input type="number" name="max_price" id="MaxPriceInput" class="form-control text-center fw-bold text-success" min="0" max="5000" value="{{ request('max_price', 5000) }}">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Categories Menu Sidebar Block -->
                        <div class="mb-4">
                            <h6 class="sidebar-heading">Categories Menu</h6>
                            <div class="category-menu-list">
                                <a href="{{ route('shop.index') }}" class="category-menu-item {{ !request('category') ? 'active' : '' }}">
                                    <i class="bi bi-grid-fill me-2"></i> All Categories
                                </a>
                                @foreach($categories as $cat)
                                    @php
                                        $iconClass = $iconMap[$cat->slug] ?? 'bi-tag-fill';
                                        $isCatActive = request('category') === $cat->slug;
                                    @endphp
                                    <div class="category-menu-group">
                                        <a href="{{ route('shop.category', ['category' => $cat->slug]) }}" class="category-menu-item {{ $isCatActive && !request('subcategory') ? 'active' : '' }}">
                                            <i class="bi {{ $iconClass }} me-2"></i>
                                            <span>{{ $cat->name }}</span>
                                        </a>
                                        @if($cat->subcategories && $cat->subcategories->isNotEmpty())
                                            <div class="subcategory-menu-list ps-3 my-1">
                                                @foreach($cat->subcategories as $subcat)
                                                    <a href="{{ route('shop.subcategory', ['category' => $cat->slug, 'subcategory' => $subcat->slug]) }}" class="subcategory-menu-item {{ request('subcategory') === $subcat->slug ? 'active' : '' }}">
                                                        <i class="bi bi-dash me-1"></i> {{ $subcat->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 4. Rating Filter -->
                        <div class="mb-4">
                            <h6 class="sidebar-heading">Minimum Rating</h6>
                            <div class="d-flex flex-column gap-2" style="font-size: 0.9rem;">
                                @for($star = 5; $star >= 3; $star--)
                                    <div class="form-check">
                                        <input class="form-check-input filter-trigger" type="radio" name="rating" id="rating_{{ $star }}" value="{{ $star }}" {{ request('rating') == $star ? 'checked' : '' }}>
                                        <label class="form-check-label text-warning" for="rating_{{ $star }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star-fill{{ $i > $star ? '-null text-muted' : '' }}"></i>
                                            @endfor
                                            <span class="text-muted ms-1" style="font-size: 0.8rem;">& Up</span>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- 5. Stock Status -->
                        <div class="mb-4">
                            <h6 class="sidebar-heading">Availability</h6>
                            <div class="form-check">
                                <input class="form-check-input filter-trigger" type="checkbox" name="in_stock" id="in_stock" value="true" {{ request('in_stock') === 'true' ? 'checked' : '' }}>
                                <label class="form-check-label text-dark fw-medium" for="in_stock">In Stock Only</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.75rem;">Apply</button>
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.75rem;">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Product Grid Column -->
            <div class="col-lg-9 col-12">
                
                <!-- Mobile Filters & Sort row (Visible on Mobile Only) -->
                <div class="d-flex d-lg-none justify-content-between align-items-center bg-white p-3 rounded-4 border mb-4 shadow-sm" style="border-color: var(--border-color) !important;">
                    <button class="btn btn-outline-success d-flex align-items-center gap-2 px-3 py-2 rounded-pill fw-bold" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFiltersOffcanvas" aria-controls="mobileFiltersOffcanvas" style="font-size: 0.75rem; border-color: #ECE7DD; color: #248443;">
                        <i class="bi bi-funnel"></i> Filters
                    </button>
                    
                    <div class="d-flex align-items-center gap-1">
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
                    <span class="text-muted fw-semibold" style="font-size: 0.85rem;"><i class="bi bi-grid-fill text-success me-2"></i>Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</span>
                    
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

                <!-- Products Grid (Row is 2-column on mobile, 3-column on desktop) -->
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4 mb-5">
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

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Filters Offcanvas Drawer -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFiltersOffcanvas" aria-labelledby="mobileFiltersOffcanvasLabel" style="width: 320px; z-index: 1045;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title font-heading fw-bold text-dark d-flex align-items-center" id="mobileFiltersOffcanvasLabel">
            <i class="bi bi-funnel text-success me-2"></i> Filter Products
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 bg-light">
        <form action="{{ request()->url() }}" method="GET" id="mobileFilterForm">
            @if(request('sort'))
                <input type="hidden" name="sort" id="mobileHiddenSort" value="{{ request('sort') }}">
            @endif

            <!-- 1. Mobile Search Bar -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Search</h6>
                <div class="input-group border rounded overflow-hidden">
                    <input type="text" name="search" class="form-control border-0 shadow-none bg-light p-2" placeholder="Search..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                    <button type="submit" class="btn bg-light text-muted border-0"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <!-- 2. Mobile Price Range Filter -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Price Range (₹)</h6>
                <div class="dual-range-track-container">
                    <div class="dual-range-track-fill" id="mobilePriceTrackFill"></div>
                    <input type="range" class="dual-range-input" id="mobileMinPriceRange" min="0" max="5000" step="50" value="{{ request('min_price', 0) }}">
                    <input type="range" class="dual-range-input" id="mobileMaxPriceRange" min="0" max="5000" step="50" value="{{ request('max_price', 5000) }}">
                </div>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted fw-semibold">₹</span>
                        <input type="number" name="min_price" id="mobileMinPriceInput" class="form-control text-center fw-bold text-success" min="0" max="5000" value="{{ request('min_price', 0) }}">
                    </div>
                    <span class="text-muted fw-bold">-</span>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted fw-semibold">₹</span>
                        <input type="number" name="max_price" id="mobileMaxPriceInput" class="form-control text-center fw-bold text-success" min="0" max="5000" value="{{ request('max_price', 5000) }}">
                    </div>
                </div>
            </div>

            <!-- 3. Mobile Categories Menu -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Categories Menu</h6>
                <div class="category-menu-list">
                    <a href="{{ route('shop.index') }}" class="category-menu-item {{ !request('category') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill me-2"></i> All Categories
                    </a>
                    @foreach($categories as $cat)
                        @php
                            $iconClass = $iconMap[$cat->slug] ?? 'bi-tag-fill';
                            $isCatActive = request('category') === $cat->slug;
                        @endphp
                        <div class="category-menu-group">
                            <a href="{{ route('shop.category', ['category' => $cat->slug]) }}" class="category-menu-item {{ $isCatActive && !request('subcategory') ? 'active' : '' }}">
                                <i class="bi {{ $iconClass }} me-2"></i>
                                <span>{{ $cat->name }}</span>
                            </a>
                            @if($cat->subcategories && $cat->subcategories->isNotEmpty())
                                <div class="subcategory-menu-list ps-3 my-1">
                                    @foreach($cat->subcategories as $subcat)
                                        <a href="{{ route('shop.subcategory', ['category' => $cat->slug, 'subcategory' => $subcat->slug]) }}" class="subcategory-menu-item {{ request('subcategory') === $subcat->slug ? 'active' : '' }}">
                                            <i class="bi bi-dash me-1"></i> {{ $subcat->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Search</h6>
                <div class="input-group border rounded overflow-hidden">
                    <input type="text" name="search" class="form-control border-0 shadow-none bg-light p-2" placeholder="Search..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                    <button type="submit" class="btn bg-light text-muted border-0"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <!-- Mobile Dual Price Filter Slider -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Price Range (₹)</h6>
                <div class="dual-range-track-container">
                    <div class="dual-range-track-fill" id="mobilePriceTrackFill"></div>
                    <input type="range" class="dual-range-input" id="mobileMinPriceRange" min="0" max="5000" step="50" value="{{ request('min_price', 0) }}">
                    <input type="range" class="dual-range-input" id="mobileMaxPriceRange" min="0" max="5000" step="50" value="{{ request('max_price', 5000) }}">
                </div>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted fw-semibold">₹</span>
                        <input type="number" name="min_price" id="mobileMinPriceInput" class="form-control text-center fw-bold text-success" min="0" max="5000" value="{{ request('min_price', 0) }}">
                    </div>
                    <span class="text-muted fw-bold">-</span>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted fw-semibold">₹</span>
                        <input type="number" name="max_price" id="mobileMaxPriceInput" class="form-control text-center fw-bold text-success" min="0" max="5000" value="{{ request('max_price', 5000) }}">
                    </div>
                </div>
            </div>

            <!-- Mobile Rating Filter -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Minimum Rating</h6>
                <div class="d-flex flex-column gap-2" style="font-size: 0.9rem;">
                    @for($star = 5; $star >= 3; $star--)
                        <div class="form-check">
                            <input class="form-check-input filter-trigger" type="radio" name="rating" id="m_rating_{{ $star }}" value="{{ $star }}" {{ request('rating') == $star ? 'checked' : '' }}>
                            <label class="form-check-label text-warning" for="m_rating_{{ $star }}">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star-fill{{ $i > $star ? '-null text-muted' : '' }}"></i>
                                @endfor
                                <span class="text-muted ms-1" style="font-size: 0.8rem;">& Up</span>
                            </label>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Mobile Stock status -->
            <div class="mb-3 border-bottom pb-3">
                <h6 class="fw-bold font-heading text-dark text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Availability</h6>
                <div class="form-check">
                    <input class="form-check-input filter-trigger" type="checkbox" name="in_stock" id="m_in_stock" value="true" {{ request('in_stock') === 'true' ? 'checked' : '' }}>
                    <label class="form-check-label text-dark fw-medium" for="m_in_stock">In Stock Only</label>
                </div>
            </div>

            <!-- Mobile Actions -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.75rem;">Apply</button>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.75rem;">Reset</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/shop.js'])
@endpush
