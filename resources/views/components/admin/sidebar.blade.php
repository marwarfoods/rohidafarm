{{--
  Admin Sidebar Component
  Usage: <x-admin.sidebar />
--}}
<aside class="admin-sidebar" id="adminSidebar">

    {{-- Logo --}}
    <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="bi bi-leaf-fill"></i>
        </div>
        <div class="sidebar-logo-text">
            <strong>RohidaFarm</strong>
            <small>Control Panel</small>
        </div>
    </a>

    {{-- Nav scroll area --}}
    <div class="sidebar-scroll">

        {{-- Main --}}
        <p class="sidebar-section-title">Main</p>
        <ul class="sidebar-nav">
            @if(auth()->user()->hasPermission('media-view'))
            <li>
                <a href="{{ route('admin.homepage.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.homepage.*') ? 'active' : '' }}">
                    <i class="bi bi-layout-text-window-reverse sidebar-icon"></i>
                    <span class="sidebar-label">Homepage</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('admin.dashboard') }}"
                   class="sidebar-nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 sidebar-icon"></i>
                    <span class="sidebar-label">Analytics</span>
                </a>
            </li>
            @if(auth()->user()->hasPermission('orders-view'))
            <li>
                <a href="{{ route('admin.orders.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check sidebar-icon"></i>
                    <span class="sidebar-label">Orders</span>
                    @if(!empty($newOrdersCount) && $newOrdersCount > 0)
                        <span class="sidebar-badge badge-order-count">{{ $newOrdersCount }}</span>
                    @endif
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('customers-view'))
            <li>
                <a href="{{ route('admin.customers.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people sidebar-icon"></i>
                    <span class="sidebar-label">Customers</span>
                    @if(!empty($newCustomersCount) && $newCustomersCount > 0)
                        <span class="sidebar-badge badge-customer-count">{{ $newCustomersCount }}</span>
                    @endif
                </a>
            </li>
            @endif
        </ul>

        {{-- Catalog --}}
        @if(auth()->user()->hasPermission('products-view') || auth()->user()->hasPermission('categories-view') || auth()->user()->hasPermission('coupons-view') || auth()->user()->hasPermission('stock-view') || auth()->user()->hasPermission('wheel-entries-view') || auth()->user()->hasPermission('contact-inquiries-view'))
        <p class="sidebar-section-title">Catalog</p>
        <ul class="sidebar-nav">
            @if(auth()->user()->hasPermission('products-view'))
            <li>
                <a href="{{ route('admin.products.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam sidebar-icon"></i>
                    <span class="sidebar-label">Products</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('categories-view'))
            <li>
                <a href="{{ route('admin.categories.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags sidebar-icon"></i>
                    <span class="sidebar-label">Categories</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('coupons-view'))
            <li>
                <a href="{{ route('admin.coupons.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.coupons.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated sidebar-icon"></i>
                    <span class="sidebar-label">Coupons</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('stock-view'))
            <li>
                <a href="{{ route('admin.stock.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.stock.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes sidebar-icon"></i>
                    <span class="sidebar-label">Stock Management</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('wheel-entries-view'))
            <li>
                <a href="{{ route('admin.wheel-entries.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.wheel-entries.*') ? 'active' : '' }}">
                    <i class="bi bi-life-preserver sidebar-icon"></i>
                    <span class="sidebar-label">Wheel Popup Entries</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('contact-inquiries-view'))
            <li>
                <a href="{{ route('admin.contact-inquiries.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.contact-inquiries.*') ? 'active' : '' }}">
                    <i class="bi bi-envelope-paper sidebar-icon"></i>
                    <span class="sidebar-label">Contact Form Entries</span>
                    @if(!empty($newContactInquiriesCount) && $newContactInquiriesCount > 0)
                        <span class="sidebar-badge badge-contact-count">{{ $newContactInquiriesCount }}</span>
                    @endif
                </a>
            </li>
            @endif
        </ul>
        @endif

        {{-- Content --}}
        @if(auth()->user()->hasPermission('reviews-view') || auth()->user()->hasPermission('video-reviews-view') || auth()->user()->hasPermission('blogs-view') || auth()->user()->hasPermission('pages-view'))
        <p class="sidebar-section-title">Content</p>
        <ul class="sidebar-nav">
            @if(auth()->user()->hasPermission('reviews-view'))
            <li>
                <a href="{{ route('admin.reviews.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-star sidebar-icon"></i>
                    <span class="sidebar-label">Reviews</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('video-reviews-view'))
            <li>
                <a href="{{ route('admin.video-reviews.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.video-reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-play-btn sidebar-icon"></i>
                    <span class="sidebar-label">Video Reviews</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('blogs-view'))
            <li>
                <a href="{{ route('admin.blogs.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.blogs.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text sidebar-icon"></i>
                    <span class="sidebar-label">Blogs</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.blog-categories.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.blog-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags sidebar-icon"></i>
                    <span class="sidebar-label">Blog Categories</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('pages-view'))
            <li>
                <a href="{{ route('admin.pages.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.pages.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text sidebar-icon"></i>
                    <span class="sidebar-label">Pages</span>
                </a>
            </li>
            @endif
            <li>
                <a href="{{ route('admin.certifications.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.certifications.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check sidebar-icon"></i>
                    <span class="sidebar-label">Certifications</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.instagram-feed.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.instagram-feed.*') ? 'active' : '' }}">
                    <i class="bi bi-instagram sidebar-icon"></i>
                    <span class="sidebar-label">Instagram Feed</span>
                </a>
            </li>
        </ul>
        @endif

        {{-- Media & Settings --}}
        <p class="sidebar-section-title">System</p>
        <ul class="sidebar-nav">
            @if(auth()->user()->hasPermission('media-view'))
            <li>
                <a href="{{ route('admin.media.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.media.*') ? 'active' : '' }}">
                    <i class="bi bi-images sidebar-icon"></i>
                    <span class="sidebar-label">Media Gallery</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('roles-view'))
            <li>
                <a href="{{ route('admin.roles.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock sidebar-icon"></i>
                    <span class="sidebar-label">Roles & Staff</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('settings-view'))
            <li>
                <a href="{{ route('admin.settings.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear sidebar-icon"></i>
                    <span class="sidebar-label">Settings</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('logs-view'))
            <li>
                <a href="{{ route('admin.logs.index') }}"
                   class="sidebar-nav-link {{ Route::is('admin.logs.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check sidebar-icon"></i>
                    <span class="sidebar-label">Audit Logs</span>
                </a>
            </li>
            @endif
        </ul>

    </div>

    {{-- Footer actions --}}
    <div class="sidebar-footer">
        <form action="{{ route('admin.cache.clear') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-warning no-spinner">
                <i class="bi bi-arrow-clockwise"></i>
                <span class="sidebar-label">Flush Cache</span>
            </button>
        </form>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger no-spinner">
                <i class="bi bi-box-arrow-right"></i>
                <span class="sidebar-label">Log Out</span>
            </button>
        </form>
    </div>

</aside>
