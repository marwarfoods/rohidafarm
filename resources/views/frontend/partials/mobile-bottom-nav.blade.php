@if(!Route::is('shop.show'))
<!-- Mobile Bottom Navigation Bar (Docked at footer of screen, persistently sticky) -->
<div class="mobile-bottom-nav d-lg-none" id="mobileBottomNav">
    <div class="mobile-nav-wrapper d-flex align-items-center justify-content-around shadow-lg">
        <!-- Home Link -->
        <a href="{{ route('home') }}" class="mobile-nav-item {{ Route::is('home') ? 'active' : '' }}">
            <i class="bi bi-house-door{{ Route::is('home') ? '-fill' : '' }}"></i>
            <span>Home</span>
        </a>

        <!-- Shop Link -->
        <a href="{{ route('shop.index') }}" class="mobile-nav-item {{ Route::is('shop.index') ? 'active' : '' }}">
            <i class="bi bi-bag{{ Route::is('shop.index') ? '-fill' : '' }}"></i>
            <span>Shop</span>
        </a>

        <!-- Coupons Button (Offcanvas Trigger) -->
        <button type="button" class="mobile-nav-item btn-trigger-coupons border-0 bg-transparent p-0" data-bs-toggle="offcanvas" data-bs-target="#couponsOffcanvas" aria-controls="couponsOffcanvas">
            <i class="bi bi-tags{{ Route::is('shop.index') ? '-fill' : '' }}"></i>
            <span>Offers</span>
        </button>

        <!-- Account Link -->
        @auth
            <a href="{{ (Auth::user()->isAdmin() || Auth::user()->roles()->exists()) ? route('admin.dashboard') : route('customer.dashboard') }}" class="mobile-nav-item {{ Route::is('admin.dashboard') || Route::is('customer.dashboard') ? 'active' : '' }}">
                <i class="bi bi-person{{ Route::is('admin.dashboard') || Route::is('customer.dashboard') ? '-fill' : '' }}"></i>
                <span>Account</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="mobile-nav-item {{ Route::is('login') ? 'active' : '' }}">
                <i class="bi bi-person{{ Route::is('login') ? '-fill' : '' }}"></i>
                <span>Account</span>
            </a>
        @endauth

        <!-- Help (WhatsApp Link) -->
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', App\Models\Setting::get('contact_mobile_1') ?: '919876543210') }}" class="mobile-nav-item help-whatsapp-link" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-whatsapp"></i>
            <span>Help</span>
        </a>
    </div>
</div>

<style>
    @media (max-width: 991px) {
        body {
            padding-bottom: 78px !important; /* Perfectly balances increased nav bar height + safe area */
        }
    }

    /* Styling for mobile bottom nav bar */
    #mobileBottomNav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 1030;
        padding: 0; /* flat, edge-to-edge docking */
    }

    .mobile-nav-wrapper {
        background: #ffffff;
        border-top: 1px solid rgba(236, 231, 221, 0.8);
        width: 100%;
        box-shadow: 0 -5px 25px rgba(0,0,0,0.06) !important;
        padding-top: 12px !important;
        padding-bottom: calc(10px + env(safe-area-inset-bottom, 10px)) !important; /* Shfit icons up comfortable distance from screen edge & home bar */
    }

    .mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #6C757D;
        font-family: 'DM Sans', sans-serif;
        font-size: 0.65rem;
        font-weight: 600;
        transition: all 0.2s ease-in-out;
        gap: 2px;
        min-width: 60px;
        padding: 6px 10px;
        border-radius: 14px;
        background: none;
        border: none;
        outline: none;
        position: relative;
    }

    .mobile-nav-item i {
        font-size: 1.25rem;
        transition: transform 0.2s ease-in-out;
    }

    .mobile-nav-item:hover,
    .mobile-nav-item.active {
        color: #248443 !important;
    }

    .mobile-nav-item.active {
        background-color: #FAF5EC !important; /* Soft cream brand accent highlight background */
    }

    .mobile-nav-item.active i {
        transform: scale(1.05);
    }
</style>
@endif
