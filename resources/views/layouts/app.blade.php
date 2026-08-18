<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- -- Dynamic SEO Tags -- --}}
    <title>{{ $seo['title'] ?? 'RohidaFarm - Pure and Traditional Organic Ghee' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'Premium luxury organic ghee and farm fresh products.' }}">
    <meta name="keywords" content="{{ $seo['keywords'] ?? 'ghee, bilona ghee, organic' }}">
    <link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#248443">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    {{-- -- Open Graph / Facebook -- --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $seo['og_url'] ?? url()->current() }}">
    <meta property="og:title" content="{{ $seo['og_title'] ?? 'RohidaFarm' }}">
    <meta property="og:description" content="{{ $seo['og_description'] ?? 'Premium luxury organic ghee' }}">
    <meta property="og:image" content="{{ $seo['og_image'] ?? asset('/assets/images/logo.png') }}">

    {{-- -- Twitter Card -- --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $seo['og_url'] ?? url()->current() }}">
    <meta name="twitter:title" content="{{ $seo['og_title'] ?? 'RohidaFarm' }}">
    <meta name="twitter:description" content="{{ $seo['og_description'] ?? 'Premium luxury organic ghee' }}">
    <meta name="twitter:image" content="{{ $seo['og_image'] ?? asset('/assets/images/logo.png') }}">

    {{-- -- Favicon -- --}}
    @if(\App\Models\Setting::get('favicon'))
        <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::get('favicon')) }}">
    @endif

    {{-- -- JSON-LD Structured Data (LocalBusiness schema for SEO) -- --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "LocalBusiness",
        "name": "RohidaFarm",
        "description": "{{ $seo['description'] ?? 'Premium organic ghee, wood pressed oils and natural products' }}",
        "url": "{{ url('/') }}",
        "logo": "{{ $seo['og_image'] ?? asset('/assets/images/logo.png') }}",
        "image": "{{ $seo['og_image'] ?? asset('/assets/images/logo.png') }}",
        "priceRange": "₹₹",
        "address": {
            "@@type": "PostalAddress",
            "addressLocality": "{{ \App\Models\Setting::get('contact_city') ?? 'Pune' }}",
            "addressRegion": "Maharashtra",
            "addressCountry": "IN"
        },
        "telephone": "{{ \App\Models\Setting::get('contact_mobile_1') ?? '+91 9876543210' }}"
    }
    </script>

    {{-- -- Resource Hints: Preconnect -- --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://unpkg.com">

    {{-- -- Font Awesome & Bootstrap Icons -- --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- -- Google Fonts - Outfit (Headings & Badges) + Inter (Body & UI) -- --}}
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- -- Preload Primary Font -- --}}
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700&family=Inter:wght@400;600&display=swap">

    {{-- -- 3rd-party scripts - deferred -- --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11.1.4/swiper-bundle.min.js" defer></script>

    {{-- -- Instant Page Prefetching for SPA-like Instant Navigation -- --}}
    <script src="https://cdn.jsdelivr.net/npm/instant.page@5.2.0/instantpage.js" type="module"></script>

    {{-- -- Swiper CSS -- --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.1.4/swiper-bundle.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.1.4/swiper-bundle.min.css"></noscript>

    {{-- -- App styles & scripts via Vite -- --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    @stack('preload')
    @if((request()->is('/') || request()->routeIs('home') || request()->path() === '/') && \App\Models\Setting::get('lucky_wheel_enabled'))
        @vite(['resources/sass/lucky-wheel.scss', 'resources/js/lucky-wheel.js'])
    @endif

    <style>
        :root {
            --primary-brown: #8B5A2B;
            --gold-accent: #D4A017;
            --cream-bg: #FFF8E8;
            --white: #FFFFFF;
            --dark-green: #1B5E20;
            --primary-green: #1B5E20;
            --text-dark: #2C1D11;
            --text-muted: #6C5B4C;
            --border-color: #E8DCC4;
            --shadow-soft: 0 10px 30px rgba(139, 90, 43, 0.08);
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            --font-heading: 'Outfit', system-ui, -apple-system, sans-serif;
            --font-body: 'Inter', system-ui, -apple-system, sans-serif;
            --section-padding-desktop: 4.5rem 0;
            --section-padding-mobile: 2.5rem 0;
        }
        body {
            font-family: var(--font-body);
            background-color: var(--cream-bg);
            color: var(--text-dark);
            letter-spacing: -0.01em;
        }
        h1, h2, h3, h4, h5, h6, .font-heading {
            font-family: var(--font-heading);
            letter-spacing: -0.025em;
        }
        .eyebrow-badge {
            font-family: var(--font-heading);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary-green);
        }
        /* -- Hover Gold & Custom Button Accents -- */
        .hover-gold:hover {
            color: var(--gold-accent) !important;
        }
        .btn-premium {
            background-color: var(--primary-brown) !important;
            border-color: var(--primary-brown) !important;
            color: var(--white) !important;
            font-family: var(--font-heading);
            font-weight: 600;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 0.7rem 1.75rem;
            border-radius: 50px;
            transition: var(--transition-smooth);
        }
        .btn-premium:hover {
            background-color: #724720 !important;
            border-color: #724720 !important;
            color: var(--white) !important;
            box-shadow: 0 8px 20px rgba(139, 90, 43, 0.25);
        }
        .btn-gold {
            background-color: var(--gold-accent) !important;
            border-color: var(--gold-accent) !important;
            color: var(--text-dark) !important;
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 0.7rem 1.75rem;
            border-radius: 50px;
        }
        .btn-gold:hover {
            background-color: #b58710 !important;
            color: var(--white) !important;
        }
        /* -- Section Lazy-reveal -- */
        .lazy-section {
            opacity: 1;
        }
        /* -- Image lazy loading blur-up & CLS prevention -- */
        img[loading="lazy"] {
            image-rendering: auto;
        }
        .img-fluid-aspect {
            width: 100%;
            height: auto;
            object-fit: cover;
        }
    </style>
</head>
<body class="{{ request()->is('checkout*') ? 'page-checkout' : '' }} {{ request()->is('cart*') ? 'page-cart' : '' }}">

    @php
        $topTickers = \App\Models\HeaderTicker::where('is_active', true)->orderBy('sort_order')->get();
    @endphp
    @if($topTickers->isNotEmpty())
        <div class="announcement-bar py-2 text-white" role="marquee" aria-label="Announcements" style="background-color: var(--dark-green); font-size: 0.82rem; font-family: 'DM Sans', sans-serif; overflow: hidden; white-space: nowrap;">
            <div class="announcement-ticker-container d-inline-block" aria-hidden="true">
                @foreach($topTickers as $ticker)
                    <span class="mx-4 fw-semibold"><i class="{{ $ticker->icon_class }} me-2 text-white" aria-hidden="true"></i> {{ $ticker->text }}</span>
                @endforeach
                @foreach($topTickers as $ticker)
                    <span class="mx-4 fw-semibold"><i class="{{ $ticker->icon_class }} me-2 text-white" aria-hidden="true"></i> {{ $ticker->text }}</span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Header / Navigation --}}
    @include('frontend.partials.header')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('frontend.partials.footer')

    {{-- Offcanvas Drawers --}}
    @include('frontend.partials.cart-offcanvas')
    @include('frontend.partials.wishlist-offcanvas')
    @include('frontend.partials.search-offcanvas')
    @include('frontend.partials.coupons-offcanvas')
    @include('frontend.partials.mobile-bottom-nav')
    @include('frontend.partials.lab-reports-modal')
    @include('frontend.partials.cart-add-addons-modal')
    <x-lucky-wheel />

    {{-- Script: Lock Background Scroll on Open Offcanvas / Modal & Handle Add-Ons --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Prevent background scrolling when any Bootstrap Offcanvas drawer or Modal is open
        const lockBodyScroll = function () {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            document.body.style.touchAction = 'none';
        };
        const unlockBodyScroll = function () {
            setTimeout(function() {
                const activeOffcanvas = document.querySelectorAll('.offcanvas.show');
                const activeModals = document.querySelectorAll('.modal.show');
                if (activeOffcanvas.length === 0 && activeModals.length === 0) {
                    document.body.style.overflow = '';
                    document.documentElement.style.overflow = '';
                    document.body.style.touchAction = '';
                }
            }, 150);
        };

        document.addEventListener('show.bs.offcanvas', lockBodyScroll);
        document.addEventListener('hidden.bs.offcanvas', unlockBodyScroll);
        document.addEventListener('show.bs.modal', lockBodyScroll);
        document.addEventListener('hidden.bs.modal', unlockBodyScroll);

        // Confirm Addons Button Action inside Top Addons Modal
        const btnConfirmAddons = document.getElementById('btnConfirmAddons');
        if (btnConfirmAddons) {
            btnConfirmAddons.addEventListener('click', function () {
                const checkboxes = document.querySelectorAll('.addon-checkbox:checked');
                
                if (checkboxes.length === 0) {
                    const modalEl = document.getElementById('addToCartAddonsModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.hide();
                    }
                    sessionStorage.setItem('open_cart_drawer', '1');
                    window.location.reload();
                    return;
                }

                btnConfirmAddons.disabled = true;
                btnConfirmAddons.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Adding...';

                if (typeof window.RohidaDebug === 'object') {
                    window.RohidaDebug.log('✨', 'Add-Ons Modal', `Add-Ons modal confirmed! User selected ${checkboxes.length} add-on item(s)...`);
                }

                const promises = [];
                checkboxes.forEach(function (cb) {
                    const productId = cb.value;
                    const variantId = cb.getAttribute('data-variant-id');
                    const formData = new FormData();
                    formData.append('product_id', productId);
                    if (variantId && variantId !== 'null' && variantId !== '') {
                        formData.append('variant_id', variantId);
                    }
                    formData.append('quantity', 1);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    promises.push(
                        fetch('{{ route("cart.add") }}', {
                            method: 'POST',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            body: formData
                        })
                    );
                });

                sessionStorage.setItem('open_cart_drawer', '1');

                Promise.all(promises).then(function () {
                    window.location.reload();
                }).catch(function() {
                    window.location.reload();
                });
            });
        }
    });
    </script>

    {{-- Floating WhatsApp Widget (PC Only) --}}
    @php $waNumber = '+919664223314'; @endphp
    @if($waNumber)
    <style>
        @keyframes pulse-wa {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(37, 211, 102, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }
        .wa-float-btn {
            animation: pulse-wa 2s infinite;
        }
        .wa-float-btn:hover {
            animation: none;
            transform: scale(1.1) !important;
        }
    </style>
    <div class="d-none d-md-block position-fixed" style="bottom: 30px; right: 30px; z-index: 1035;">
        <!-- Chat Box -->
        <div id="wa-chat-box" class="bg-white rounded-4 shadow-lg mb-3 d-none overflow-hidden" style="width: 320px; transition: all 0.3s; border: 1px solid var(--border-color);">
            <div class="bg-success text-white p-3 d-flex align-items-center justify-content-between" style="background-color: #075E54 !important;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-whatsapp fs-3 text-white"></i>
                    <div class="text-white">
                        <h6 class="m-0 fw-bold text-white">RohidaFarm Support</h6>
                        <small style="font-size: 0.75rem; color: rgba(255,255,255,0.9);">Typically replies instantly</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('wa-chat-box').classList.add('d-none')"></button>
            </div>
            <div class="p-3 bg-light" style="min-height: 120px;">
                <div class="bg-white p-2 rounded-3 shadow-sm d-inline-block position-relative mb-3" style="max-width: 85%; font-size: 0.85rem; border-top-left-radius: 0;">
                    Hello! 👋<br>Welcome to RohidaFarm. How can we help you today?
                    <div class="position-absolute top-0 start-0 translate-middle-x" style="width: 0; height: 0; border-top: 10px solid white; border-left: 10px solid transparent; margin-top: 0px; margin-left: 5px;"></div>
                </div>
                <textarea id="wa_user_message" class="form-control" rows="2" placeholder="Type your message here..." style="font-size: 0.85rem; resize: none;"></textarea>
            </div>
            <div class="p-3 text-center border-top">
                <button type="button" onclick="sendWhatsAppMessage('{{ preg_replace('/[^0-9]/', '', $waNumber) }}')" class="btn btn-success rounded-pill w-100 fw-bold" style="background-color: #25D366; border: none;">
                    <i class="bi bi-send me-2"></i> Send Message
                </button>
            </div>
        </div>
        <!-- Floating Button -->
        <button type="button" class="btn rounded-circle shadow p-0 d-flex align-items-center justify-content-center ms-auto wa-float-btn" 
                style="width: 60px; height: 60px; background-color: #25D366; color: white; transition: transform 0.3s;"
                onclick="document.getElementById('wa-chat-box').classList.toggle('d-none')">
            <i class="bi bi-whatsapp" style="font-size: 32px;"></i>
        </button>
    </div>
    <script>
        function sendWhatsAppMessage(number) {
            let text = document.getElementById('wa_user_message').value.trim();
            if(!text) text = "Hello!";
            let url = "https://wa.me/" + number + "?text=" + encodeURIComponent(text);
            window.open(url, "_blank");
        }
    </script>
    @endif

    @stack('scripts')
    @stack('page-scripts')

    {{-- -- Intersection Observer: lazy section reveal -- --}}
    <script>
    (function(){
        if (!('IntersectionObserver' in window)) return;
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(e){
                if(e.isIntersecting){
                    e.target.classList.add('is-visible');
                    io.unobserve(e.target);
                }
            });
        }, { rootMargin: '0px 0px -60px 0px', threshold: 0.08 });
        document.querySelectorAll('.lazy-section').forEach(function(el){ io.observe(el); });
    })();
    </script>
</body>
</html>
