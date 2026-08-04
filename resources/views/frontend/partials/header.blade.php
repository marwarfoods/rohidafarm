@push('styles')
    @vite(['resources/sass/header.scss'])
@endpush
@push('scripts')
    @vite(['resources/js/header.js'])
@endpush

<header class="navbar-sticky bg-white border-bottom header-main-container shadow-sm">
    <nav class="navbar navbar-expand-lg py-1">
        <div class="container-fluid px-lg-5">
            
            <!-- ── Mobile View Layout (Hamburger Left, Centered Logo, Search & Cart Right) ── -->
            <div class="d-flex d-lg-none justify-content-between align-items-center w-100 px-1 py-1">
                <!-- Left: Hamburger Drawer Toggler -->
                <button class="navbar-toggler border-0 p-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenuOffcanvas" aria-controls="mobileMenuOffcanvas" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-1 text-dark"></i>
                </button>
                
                <!-- Center: Logo -->
                <a class="navbar-brand d-flex flex-column align-items-center m-0 mx-auto" href="{{ route('home') }}">
                    @if(\App\Models\Setting::get('main_logo'))
                        <img src="{{ asset(\App\Models\Setting::get('main_logo')) }}" alt="RohidaFarm Logo" width="180" height="52" style="height: 52px; max-width: 180px; object-fit: contain;">
                    @else
                        <span class="fs-3 fw-bold text-uppercase tracking-wider m-0 font-heading" style="color: var(--primary-green); line-height: 1; letter-spacing: -0.5px;">RohidaFarm</span>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.52rem; letter-spacing: 1.2px;">Pure & Traditional</span>
                    @endif
                </a>

                <!-- Right: Search & Cart Triggers -->
                @php
                    $cartService = app(\App\Services\CartService::class);
                    $cartCount = $cartService->getQtyCount();
                @endphp
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="text-dark hover-gold fs-4 p-0 d-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#searchOffcanvas" aria-controls="searchOffcanvas" aria-label="Open search">
                        <i class="bi bi-search text-dark" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="text-dark hover-gold position-relative fs-4 p-0 d-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas" aria-label="Open shopping cart">
                        <i class="bi bi-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-success cart-badge-count {{ $cartCount > 0 ? '' : 'd-none' }}" style="font-size: 0.55rem; padding: 0.25em 0.5em; background-color: var(--dark-green) !important;">
                            {{ $cartCount }}
                        </span>
                    </a>
                </div>
            </div>

            <!-- ── Desktop View Layout (Sleek Centered Logo with 4 Menu Links Left & 4 Menu Links Right) ── -->
            <div class="d-none d-lg-flex align-items-center justify-content-between w-100">
                
                <!-- Left Menu Items (4 Links: Home, Shop, Our Story, Why Tharparkar) -->
                <div class="d-flex align-items-center flex-grow-1" style="flex-basis: 0;">
                    <ul class="navbar-nav me-auto fw-semibold gap-3.5" style="font-size: 1.05rem; font-family: var(--font-heading); letter-spacing: 0.3px;">
                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="{{ route('home') }}">Home</a>
                        </li>
                        
                        <!-- Shop Dropdown / Mega Menu -->
                        <li class="nav-item dropdown dropdown-hover position-static">
                            <a class="nav-link text-dark hover-gold dropdown-toggle fw-semibold py-1" href="{{ route('shop.index') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Shop
                            </a>
                            <div class="dropdown-menu border-0 p-4 rounded-0 shadow-lg w-100 start-0 end-0 mt-0" style="background-color: var(--white); border-top: 3px solid var(--primary-green) !important;">
                                <div class="container position-relative px-5">
                                    @php
                                        $megaMenuProducts = \App\Models\Product::with('images')->where('is_active', true)->take(15)->get();
                                    @endphp
                                    
                                    <div class="swiper mega-menu-slider overflow-hidden py-3">
                                        <div class="swiper-wrapper">
                                            @foreach($megaMenuProducts as $product)
                                                @php 
                                                    $img = $product->primaryImage ? asset($product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg'); 
                                                @endphp
                                                <div class="swiper-slide text-center">
                                                    <a href="{{ route('shop.show', $product->slug) }}" class="d-block text-decoration-none">
                                                        <div class="mega-menu-img-wrap rounded overflow-hidden shadow-sm border mb-2 mx-auto" style="aspect-ratio: 1/1; max-width: 140px;">
                                                            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover hover-scale" style="transition: transform 0.4s ease;">
                                                        </div>
                                                        <h6 class="text-dark font-heading fw-bold m-0" style="font-size: 0.8rem; line-height: 1.2;">{{ Str::limit($product->name, 25) }}</h6>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <!-- Slider Navigation -->
                                    <div class="swiper-button-next mega-menu-next text-success shadow-sm rounded-circle bg-white" style="width: 35px; height: 35px; top: 50%; right: 5px;"></div>
                                    <div class="swiper-button-prev mega-menu-prev text-success shadow-sm rounded-circle bg-white" style="width: 35px; height: 35px; top: 50%; left: 5px;"></div>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="{{ route('about') }}">Our Story</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="{{ url('/#why-tharparkar') }}">Why Tharparkar</a>
                        </li>
                    </ul>
                </div>

                <!-- Center Logo (Sleek Header Height with Larger Clear Logo Image) -->
                <div class="text-center px-3" style="flex-shrink: 0;">
                    <a class="navbar-brand d-flex flex-column align-items-center m-0" href="{{ route('home') }}">
                        @if(\App\Models\Setting::get('main_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('main_logo')) }}" alt="RohidaFarm Logo" width="220" height="65" style="height: 65px; max-width: 220px; object-fit: contain;">
                        @else
                            <span class="fs-3 fw-bold text-uppercase tracking-wider m-0 font-heading" style="color: var(--primary-green); line-height: 1;">RohidaFarm</span>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.6rem; letter-spacing: 1.8px;">Pure & Traditional</span>
                        @endif
                    </a>
                </div>

                <!-- Right Menu Items (4 Links: Bilona Process, Lab Reports, Blogs, Contact) & Utility Icons -->
                <div class="d-flex align-items-center justify-content-end flex-grow-1" style="flex-basis: 0;">
                    <ul class="navbar-nav ms-auto fw-semibold gap-3.5 me-4" style="font-size: 1.05rem; font-family: var(--font-heading); letter-spacing: 0.3px;">
                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="{{ url('/#bilona-process') }}">Bilona Process</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="#" data-bs-toggle="modal" data-bs-target="#labReportsModal">Lab Reports</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="{{ route('blogs.index') }}">Blogs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark hover-gold fw-semibold py-1" href="{{ route('contact') }}">Contact</a>
                        </li>
                    </ul>

                    <!-- Utility Icons (Search, Account, Cart) -->
                    <div class="d-flex align-items-center gap-3 text-dark fs-5">
                        <!-- Search Trigger -->
                        <a href="#" class="text-dark hover-gold p-1" data-bs-toggle="offcanvas" data-bs-target="#searchOffcanvas" aria-controls="searchOffcanvas" aria-label="Open search">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </a>
                        
                        <!-- Account -->
                        @auth
                            @php
                                $accountLabel = Auth::user()->isAdmin() 
                                    ? 'Admin Account' 
                                    : (Auth::user()->roles->first() 
                                        ? Auth::user()->roles->first()->display_name . ' Account' 
                                        : 'Customer Account');
                            @endphp
                            <div class="dropdown">
                                <a href="#" class="text-dark hover-gold dropdown-toggle p-1" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ $accountLabel }}">
                                    <i class="bi bi-person"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded">
                                    <li><h6 class="dropdown-header text-dark fw-bold border-bottom pb-2 mb-2">{{ Auth::user()->name }}<small class="text-muted d-block mt-0.5" style="font-size: 0.72rem;">{{ $accountLabel }}</small></h6></li>
                                    @if(Auth::user()->isAdmin() || Auth::user()->roles()->exists())
                                        @php
                                            $dashboardLabel = Auth::user()->isAdmin() ? 'Admin Dashboard' : 'Staff Dashboard';
                                        @endphp
                                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-success"></i> {{ $dashboardLabel }}</a></li>
                                    @else
                                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('customer.dashboard') }}"><i class="bi bi-house-door me-2 text-success"></i> My Dashboard</a></li>
                                        <li><a class="dropdown-item py-2 fw-medium" href="{{ route('customer.orders') }}"><i class="bi bi-bag me-2 text-success"></i> My Orders</a></li>
                                    @endif
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-2 fw-medium text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-dark hover-gold p-1" title="Login"><i class="bi bi-person"></i></a>
                        @endauth

                        <!-- Cart Trigger -->
                        <a href="#" class="text-dark hover-gold position-relative p-1" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" aria-controls="cartOffcanvas" aria-label="Open shopping cart">
                            <i class="bi bi-cart"></i>
                            @php
                                $cartCount = app(\App\Services\CartService::class)->getItems()->sum('quantity');
                            @endphp
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-success cart-badge-count {{ $cartCount > 0 ? '' : 'd-none' }}" style="font-size: 0.55rem; padding: 0.25em 0.5em; background-color: var(--dark-green) !important;">
                                {{ $cartCount }}
                            </span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </nav>
</header>

<!-- Mobile Navigation Drawer / Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuOffcanvasLabel">
    <div class="offcanvas-header border-bottom">
        <div class="d-flex flex-column align-items-start">
            @if(\App\Models\Setting::get('main_logo'))
                <img src="{{ asset(\App\Models\Setting::get('main_logo')) }}" alt="RohidaFarm Logo" width="180" height="50" style="height: 50px; max-width: 180px; object-fit: contain;">
            @else
                <span class="fs-4 fw-bold text-uppercase tracking-wider font-heading m-0" style="color: var(--primary-green);">RohidaFarm</span>
                <span class="text-muted text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">Pure and Traditional</span>
            @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-0 overflow-hidden" style="height: 100%;">
        <!-- Scrollable Links Content wrapper -->
        <div class="flex-grow-1 overflow-y-auto p-4" style="height: calc(100% - 85px);">

            <!-- Tab Headers -->
            <ul class="nav nav-tabs nav-justified mb-4 mobile-menu-tabs border-0" id="mobileMenuTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-3 text-uppercase" id="mobile-categories-tab" data-bs-toggle="tab" data-bs-target="#tab-mobile-categories" type="button" role="tab" aria-controls="tab-mobile-categories" aria-selected="true" style="font-size: 0.82rem; border: none; border-radius: 0; background-color: #f5f3ef; color: #6c757d; border-bottom: 3px solid transparent; transition: all 0.2s;">
                        Categories
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3 text-uppercase" id="mobile-menu-tab" data-bs-toggle="tab" data-bs-target="#tab-mobile-menu" type="button" role="tab" aria-controls="tab-mobile-menu" aria-selected="false" style="font-size: 0.82rem; border: none; border-radius: 0; background-color: #f5f3ef; color: #6c757d; border-bottom: 3px solid transparent; transition: all 0.2s;">
                        Menu
                    </button>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content" id="mobileMenuTabsContent">
                <!-- Categories Tab -->
                <div class="tab-pane fade show active" id="tab-mobile-categories" role="tabpanel" aria-labelledby="mobile-categories-tab">
                    <div class="mb-3">
                        <a href="{{ route('shop.index') }}" class="btn btn-success w-100 rounded-pill py-2 fw-semibold" style="font-size: 0.85rem; background-color: var(--primary-green) !important; border-color: var(--primary-green) !important;">
                            <i class="bi bi-shop me-1"></i> Shop All Products
                        </a>
                    </div>
                    @php
                        $mobileCats = \App\Models\Category::where('is_active', true)->with('subCategories')->get();
                    @endphp
                    <ul class="list-unstyled d-flex flex-column gap-2 fw-semibold">
                        @foreach($mobileCats as $cat)
                            <li class="border-bottom pb-2 mb-2">
                                @if($cat->subCategories->isNotEmpty())
                                    <div class="d-flex justify-content-between align-items-center py-1">
                                        <a href="{{ route('shop.category', ['category' => $cat->slug]) }}" class="text-dark text-decoration-none d-flex align-items-center">
                                            <i class="bi {{ $cat->icon ?? 'bi-tags' }} text-success me-2 fs-5"></i>
                                            <span>{{ $cat->name }}</span>
                                        </a>
                                        <button class="btn btn-sm border-0 p-1 shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#subCatCollapse{{ $cat->id }}" aria-expanded="false" aria-controls="subCatCollapse{{ $cat->id }}">
                                            <i class="bi bi-chevron-down text-muted" style="font-size: 0.88rem; transition: transform 0.2s;"></i>
                                        </button>
                                    </div>
                                    <div class="collapse" id="subCatCollapse{{ $cat->id }}">
                                        <ul class="list-unstyled ps-4 mt-2 d-flex flex-column gap-1 fw-normal" style="font-size: 0.85rem; border-left: 2px solid var(--border-color);">
                                            @foreach($cat->subCategories as $sub)
                                                <li>
                                                    <a href="{{ route('shop.subcategory', ['category' => $cat->slug, 'subcategory' => $sub->slug]) }}" class="text-muted d-block py-1">
                                                        {{ $sub->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <a href="{{ route('shop.category', ['category' => $cat->slug]) }}" class="text-dark d-flex justify-content-between align-items-center py-1 text-decoration-none">
                                        <span class="d-flex align-items-center">
                                            <i class="bi {{ $cat->icon ?? 'bi-tags' }} text-success me-2 fs-5"></i>
                                            {{ $cat->name }}
                                        </span>
                                        <i class="bi bi-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Menu Links Tab -->
                <div class="tab-pane fade" id="tab-mobile-menu" role="tabpanel" aria-labelledby="mobile-menu-tab">
                    <ul class="list-unstyled d-flex flex-column gap-3 fw-semibold">
                        <li><a href="{{ route('home') }}" class="text-dark d-block py-1">Home</a></li>
                        <li><a href="{{ route('shop.index') }}" class="text-dark d-block py-1">Shop Catalog</a></li>
                        <li><a href="{{ route('about') }}" class="text-dark d-block py-1">Our Story</a></li>
                        <li><a href="{{ url('/#why-tharparkar') }}" class="text-dark d-block py-1">Why Tharparkar</a></li>
                        <li><a href="{{ url('/#bilona-process') }}" class="text-dark d-block py-1">Bilona Process</a></li>
                        <li><a href="#" data-bs-toggle="modal" data-bs-target="#labReportsModal" class="text-dark d-block py-1"><i class="bi bi-file-earmark-check text-warning me-2"></i>Lab Reports</a></li>
                        <li><a href="{{ route('blogs.index') }}" class="text-dark d-block py-1">Healthy Living Blogs</a></li>
                        <li><a href="{{ route('contact') }}" class="text-dark d-block py-1">Contact Us</a></li>
                        <li><a href="{{ route('track-order') }}" class="text-dark d-block py-1"><i class="bi bi-geo-alt text-success me-2"></i>Track Order</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Sticky Footer -->
        <div class="mobile-menu-footer border-top p-4 bg-white mt-auto" style="flex-shrink: 0; z-index: 10;">
            @auth
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="d-block fw-bold text-dark" style="font-size: 0.9rem;">{{ Auth::user()->name }}</span>
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('customer.dashboard') }}" class="text-success fw-medium" style="font-size: 0.8rem;">My Account</a>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-premium w-100 py-2">Account Login</a>
            @endauth
        </div>
    </div>
</div>
