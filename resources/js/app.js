// Import Premium Media Gallery Client Logic
import './media-gallery';
import './icon-picker';
import './header';

// ── GLOBAL DEBUG LOGGER WITH EMOJIS ──────────────────
window.RohidaDebug = {
    log: function(emoji, category, message, data = null) {
        const time = new Date().toLocaleTimeString();
        if (data) {
            console.log(`%c${emoji} [${time}] [Rohida ${category}] ${message}`, 'color: #8B5A2B; font-weight: bold; background-color: #FFF8E8; padding: 2px 6px; border-radius: 4px;', data);
        } else {
            console.log(`%c${emoji} [${time}] [Rohida ${category}] ${message}`, 'color: #8B5A2B; font-weight: bold; background-color: #FFF8E8; padding: 2px 6px; border-radius: 4px;');
        }
    }
};

document.addEventListener('DOMContentLoaded', function () {
    RohidaDebug.log('🚀', 'App', 'Rohida Farm Application Initialized. Real-time Debug Logger Active!');

    // Global image fallback listener for missing uploads/images
    document.addEventListener('error', function (e) {
        if (e.target.tagName === 'IMG' && !e.target.dataset.hasFallback) {
            e.target.dataset.hasFallback = 'true';
            e.target.src = '/assets/images/products/placeholder.jpg';
        }
    }, true);

    // Initialize AOS (Animate on Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 600,
            easing: 'ease-out-cubic',
            once: true,
            offset: 0
        });

        window.addEventListener('load', function() {
            setTimeout(() => AOS.refresh(), 100);
        });
    }

    // ── AUTOMATIC GLITCH CHECKER ──
    window.addEventListener('load', function() {
        console.group('🔍 [Glitch Checker] Automated Page Diagnostics');
        let glitchesFound = 0;

        // 1. Check for broken images
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            if (!img.complete || img.naturalWidth === 0) {
                console.error(`🚨 [Glitch] Broken Image found:`, img.src || img);
                glitchesFound++;
            }
        });

        // 2. Check for empty or placeholder links
        const links = document.querySelectorAll('a:not([data-bs-toggle])');
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href.trim() === '#' || href.trim() === '') {
                console.warn(`⚠️ [Glitch] Empty/Placeholder Link found:`, link.textContent.trim() || link);
                glitchesFound++;
            }
        });

        if (glitchesFound === 0) {
            console.log('✅ [Glitch Checker] No major glitches found on this page!');
        } else {
            console.log(`⚠️ [Glitch Checker] Found ${glitchesFound} potential glitches to fix.`);
        }
        console.groupEnd();
    });

    // Fixed Sticky Header logic
    const header = document.querySelector('.navbar-sticky');
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 150) {
                header.classList.add('navbar-scrolled');
            } else {
                header.classList.remove('navbar-scrolled');
            }
        });
    }

    // Delegation for offcanvas quantity updates & wishlist
    document.addEventListener('click', function(e) {
        // Cart increments
        if (e.target.closest('.btn-offcanvas-inc') || e.target.closest('.btn-offcanvas-dec')) {
            const btn = e.target.closest('button');
            const id = btn.getAttribute('data-id');
            const isInc = btn.classList.contains('btn-offcanvas-inc');
            const inp = btn.parentElement.querySelector('.offcanvas-qty');
            let val = parseInt(inp.value);
            if (isInc && val < 10) val++;
            else if (!isInc && val > 1) val--;
            inp.value = val;
            
            RohidaDebug.log('➕', 'Cart Qty', `Quantity button clicked for item #${id} -> New value: ${val}`);
            updateCartQtyOffcanvas(id, val);
        }

        // Cart remove
        if (e.target.closest('.btn-offcanvas-remove')) {
            const btn = e.target.closest('.btn-offcanvas-remove');
            const id = btn.getAttribute('data-id');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm text-danger" style="width: 14px; height: 14px;" role="status"></span>';
            btn.style.pointerEvents = 'none';
            RohidaDebug.log('🗑️', 'Cart Remove', `Remove button clicked for Cart Item #${id}`);
            removeCartItemOffcanvas(id);
        }

        // Wishlist remove
        if (e.target.closest('.btn-offcanvas-wishlist-remove')) {
            const id = e.target.closest('.btn-offcanvas-wishlist-remove').getAttribute('data-id');
            RohidaDebug.log('❤️', 'Wishlist', `Wishlist toggle triggered for Product #${id}`);
            toggleWishlistOffcanvas(id);
        }
    });

    function showCartSkeleton() {
        const skeleton = document.getElementById('cartOffcanvasSkeleton');
        if (skeleton) {
            skeleton.classList.remove('d-none');
            skeleton.classList.add('d-flex');
            skeleton.style.opacity = '1';
        }
    }

    function hideCartSkeleton() {
        const skeleton = document.getElementById('cartOffcanvasSkeleton');
        if (skeleton) {
            skeleton.style.opacity = '0';
            setTimeout(() => {
                skeleton.classList.remove('d-flex');
                skeleton.classList.add('d-none');
            }, 200);
        }
    }

    // "Hot Choices – Selling Right Now" auto-sliding cart add-ons carousel
    // (present in both the empty and active cart drawer states). Guards
    // against double-init since this also runs after every AJAX cart reload.
    function initCartAddonsSliders() {
        document.querySelectorAll('.cart-addons-slider').forEach(function (el) {
            if (el.swiper || typeof Swiper === 'undefined') return;
            new Swiper(el, {
                slidesPerView: 'auto',
                spaceBetween: 12,
                loop: el.querySelectorAll('.swiper-slide').length > 2,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                pagination: {
                    el: el.querySelector('.swiper-pagination'),
                    clickable: true,
                },
            });
        });
    }
    initCartAddonsSliders();

    window.reloadCartDrawer = function(callback) {
        const cacheBuster = new Date().getTime();
        RohidaDebug.log('🔄', 'Cart Drawer', `Fetching fresh real-time cart drawer HTML (t=${cacheBuster})...`);
        fetch('/cart/drawer?t=' + cacheBuster, {
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Update active and empty contents
                const newActive = doc.getElementById('cartActiveContent');
                const newEmpty = doc.getElementById('cartEmptyContent');
                
                const currentActive = document.getElementById('cartActiveContent');
                const currentEmpty = document.getElementById('cartEmptyContent');
                
                if (newActive && currentActive) {
                    currentActive.innerHTML = newActive.innerHTML;
                    currentActive.className = newActive.className;
                }
                
                if (newEmpty && currentEmpty) {
                    currentEmpty.innerHTML = newEmpty.innerHTML;
                    currentEmpty.className = newEmpty.className;
                }
                
                // Update Sticky Cart Widget
                const newSticky = doc.getElementById('stickyCartWidget');
                const currentSticky = document.getElementById('stickyCartWidget');
                if (newSticky && currentSticky) {
                    currentSticky.innerHTML = newSticky.innerHTML;
                    currentSticky.className = newSticky.className;
                }
                
                // Update all cart count badges across the page (header desktop, header mobile, etc)
                const newBadge = doc.querySelector('.cart-badge-count');
                const currentBadges = document.querySelectorAll('.cart-badge-count');
                let countText = '0';
                if (newBadge) {
                    countText = newBadge.innerText.trim();
                    const isHidden = newBadge.classList.contains('d-none');
                    currentBadges.forEach(badge => {
                        badge.innerText = countText;
                        if (isHidden || countText === '0') {
                            badge.classList.add('d-none');
                        } else {
                            badge.classList.remove('d-none');
                        }
                    });
                }
                RohidaDebug.log('🏷️', 'Cart Badges', `Updated ${currentBadges.length} cart badge elements across DOM -> Badge Count: ${countText}`);
                RohidaDebug.log('✅', 'Cart Drawer', `Cart Drawer successfully reloaded & synchronized in real-time!`);

                // The innerHTML swap above tears down any previously-initialized
                // "Hot Choices" Swiper instances along with their DOM, so re-init
                // on the freshly inserted sliders.
                initCartAddonsSliders();

                // If on checkout page and cart is now empty, redirect to home
                if (window.location.pathname === '/checkout' || window.location.pathname.startsWith('/checkout/')) {
                    if (newEmpty && !newEmpty.classList.contains('d-none')) {
                        RohidaDebug.log('⚠️', 'Checkout Guard', 'Cart is empty on checkout page -> Redirecting to home...');
                        window.location.href = '/';
                        return;
                    }
                }

                if (callback) callback();
            })
            .catch(err => {
                RohidaDebug.log('❌', 'Cart Drawer', 'Error refreshing cart drawer HTML:', err);
                if (callback) callback();
            });
    }

    function updateCartQtyOffcanvas(id, qty) {
        const subtotalEl = document.getElementById('offcanvasSubtotal');
        const totalEl = document.getElementById('offcanvasTotal');
        if (subtotalEl) subtotalEl.classList.add('price-loading-shimmer');
        if (totalEl) totalEl.classList.add('price-loading-shimmer');
        
        const row = document.getElementById(`offcanvasRow_${id}`);
        const qtyWrapper = row ? row.querySelector('.quantity-input') : null;
        if (qtyWrapper) qtyWrapper.style.pointerEvents = 'none';

        sessionStorage.setItem('open_cart_drawer', '1');

        fetch(`/cart/update/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ quantity: qty })
        })
        .then(res => {
            if (!res.ok) throw new Error('Stock limits reached.');
            return res.json();
        })
        .then(data => {
            RohidaDebug.log('✅', 'Cart Qty', `Quantity update confirmed on server for item #${id} -> Reloading page...`);
            window.location.reload();
        })
        .catch(err => {
            if (subtotalEl) subtotalEl.classList.remove('price-loading-shimmer');
            if (totalEl) totalEl.classList.remove('price-loading-shimmer');
            if (qtyWrapper) qtyWrapper.style.pointerEvents = 'auto';
            alert(err.message || 'Stock limits reached.');
        });
    }

    function removeCartItemOffcanvas(id) {
        showCartSkeleton();
        sessionStorage.setItem('open_cart_drawer', '1');

        fetch(`/cart/remove/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' || data.success) {
                RohidaDebug.log('🗑️', 'Cart Remove', `Item #${id} removed successfully -> Reloading page...`);
                window.location.reload();
            } else {
                hideCartSkeleton();
                alert('Error removing item.');
            }
        })
        .catch(err => {
            hideCartSkeleton();
            alert('Error removing item.');
        });
    }

    function toggleWishlistOffcanvas(id) {
        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ product_id: id })
        })
        .then(res => res.json())
        .then(data => {
            sessionStorage.setItem('open_wishlist_drawer', '1');
            window.location.reload();
        });
    }

    // Intercept Add to Cart form submissions for dynamic offcanvas & add-ons modal
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form.add-to-cart-form') || (e.target.action && e.target.action.endsWith('/cart/add') ? e.target : null);
        if (!form) return;
        
        e.preventDefault();
        const formData = new FormData(form);
        const productId = formData.get('product_id');
        const variantId = formData.get('variant_id');
        const qty       = formData.get('quantity') || 1;
        const isInsideOffcanvas = Boolean(form.closest('#cartOffcanvas'));

        if (isInsideOffcanvas) {
            RohidaDebug.log('🔥', 'Cart Drawer', `Hot Choices Add triggered inside Cart Drawer -> Product #${productId}, Variant #${variantId}`);
            sessionStorage.setItem('open_cart_drawer', '1');
        } else {
            RohidaDebug.log('🛒', 'Add To Cart', `Add to Cart triggered -> Product #${productId}, Variant #${variantId}, Qty: ${qty}`);
        }
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (res.ok) {
                if (isInsideOffcanvas) {
                    RohidaDebug.log('⚡', 'Cart Drawer', `Hot Choice item added -> Reloading page for 100% accuracy!`);
                    window.location.reload();
                    return;
                }

                reloadCartDrawer(function() {
                    hideCartSkeleton();
                });

                // Trigger Top Add-Ons Modal ("✔ Added to Cart!")
                const addonsModalEl = document.getElementById('addToCartAddonsModal');
                if (addonsModalEl) {
                    RohidaDebug.log('✨', 'Add-Ons Modal', `Opening 'Added to Cart!' Top Add-ons Modal...`);
                    const addonsModal = bootstrap.Modal.getOrCreateInstance(addonsModalEl);
                    addonsModal.show();
                } else {
                    sessionStorage.setItem('open_cart_drawer', '1');
                    window.location.reload();
                }
            } else {
                hideCartSkeleton();
                res.json().then(data => alert(data.message || 'Stock limits reached.'));
            }
        })
        .catch(err => {
            hideCartSkeleton();
            form.submit();
        });
    });

    // "Added to Cart!" Add-Ons Modal — confirm button was previously wired to
    // nothing, so checking add-ons and clicking "Add to Cart" silently did
    // nothing. Also: whether the user confirms, skips, or just closes the
    // modal, open the cart drawer afterwards so there's visible confirmation
    // of what actually ended up in the cart instead of the modal just vanishing.
    const addonsModalEl = document.getElementById('addToCartAddonsModal');
    if (addonsModalEl) {
        addonsModalEl.addEventListener('hidden.bs.modal', function () {
            const cartOffcanvasEl = document.getElementById('cartOffcanvas');
            if (cartOffcanvasEl) {
                bootstrap.Offcanvas.getOrCreateInstance(cartOffcanvasEl).show();
            }
        });

        const confirmAddonsBtn = document.getElementById('btnConfirmAddons');
        if (confirmAddonsBtn) {
            confirmAddonsBtn.addEventListener('click', function () {
                const checked = Array.from(addonsModalEl.querySelectorAll('.addon-checkbox:checked'));
                const modalInstance = bootstrap.Modal.getOrCreateInstance(addonsModalEl);

                if (checked.length === 0) {
                    modalInstance.hide();
                    return;
                }

                const originalText = confirmAddonsBtn.innerHTML;
                confirmAddonsBtn.disabled = true;
                confirmAddonsBtn.innerHTML = 'Adding...';

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const requests = checked.map(function (checkbox) {
                    const body = new FormData();
                    body.append('product_id', checkbox.value);
                    body.append('variant_id', checkbox.getAttribute('data-variant-id') || '');
                    body.append('quantity', 1);
                    return fetch('/cart/add', {
                        method: 'POST',
                        body: body,
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    });
                });

                Promise.all(requests).finally(function () {
                    reloadCartDrawer();
                    confirmAddonsBtn.disabled = false;
                    confirmAddonsBtn.innerHTML = originalText;
                    modalInstance.hide();
                });
            });
        }
    }

    // Dynamic Variant Selector Price and Hidden Inputs Update with Shimmer Loader
    // Delegated on document (not bound per-select) because Swiper's loop mode
    // clones slides — including their <select> and the duplicated
    // "prod-card-{id}" wrapper — after this script runs. A direct listener
    // attached only to the selects that existed at load time would silently
    // miss those clones, which is why the price update used to work
    // intermittently depending on which physical slide (original vs. clone)
    // the user landed on.
    document.addEventListener('change', function (e) {
        const select = e.target.closest('.variant-selector');
        if (!select) return;

        const productId = select.getAttribute('data-product-id');
        const card = select.closest('.product-card') || document.getElementById(`prod-card-${productId}`);
        if (!card) return;

        const selectedOption = select.options[select.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price'));
        const mrp = parseFloat(selectedOption.getAttribute('data-mrp'));
        const variantId = selectedOption.value;

        const formattedPrice = `₹${price.toLocaleString('en-IN', {maximumFractionDigits: 0})}`;
        const formattedMrp = `₹${mrp.toLocaleString('en-IN', {maximumFractionDigits: 0})}`;
        const bestPrice = `Best Price ₹${Math.round(price * 0.85).toLocaleString('en-IN')} with PURE15`;

        RohidaDebug.log('🏷️', 'Variant Change', `Product #${productId} variant changed -> Selected Variant #${variantId}, Price: ${formattedPrice}`);

        const salePriceEl = card.querySelector('.card-sale-price');
        const mrpPriceEl = card.querySelector('.card-mrp-price');
        const bestPriceEl = card.querySelector('.card-best-price');
        const priceContainer = card.querySelector('.price-container');

        if (priceContainer) {
            priceContainer.classList.add('price-loading-shimmer');
            setTimeout(() => {
                if (salePriceEl) salePriceEl.textContent = formattedPrice;
                if (mrpPriceEl) mrpPriceEl.textContent = formattedMrp;
                if (bestPriceEl) bestPriceEl.innerHTML = `<i class="bi bi-tag-fill me-1"></i>${bestPrice}`;
                priceContainer.classList.remove('price-loading-shimmer');
            }, 250);
        } else {
            if (salePriceEl) salePriceEl.textContent = formattedPrice;
            if (mrpPriceEl) mrpPriceEl.textContent = formattedMrp;
            if (bestPriceEl) bestPriceEl.innerHTML = `<i class="bi bi-tag-fill me-1"></i>${bestPrice}`;
        }

        const variantInput = card.querySelector('.card-variant-id');
        if (variantInput) variantInput.value = variantId;

        const stock = parseInt(selectedOption.getAttribute('data-stock') || '0');
        const cartForm = card.querySelector('.add-to-cart-form');
        const soldOutBtn = card.querySelector('.card-sold-out-btn');

        if (stock > 0) {
            if (cartForm) cartForm.classList.remove('d-none');
            if (soldOutBtn) soldOutBtn.classList.add('d-none');
        } else {
            if (cartForm) cartForm.classList.add('d-none');
            if (soldOutBtn) soldOutBtn.classList.remove('d-none');
        }
    });

    // Auto-open drawers after page reload if flag is present
    if (sessionStorage.getItem('open_cart_drawer') === '1') {
        sessionStorage.removeItem('open_cart_drawer');
        window.addEventListener('load', function() {
            const cartDrawer = new bootstrap.Offcanvas(document.getElementById('cartOffcanvas'));
            cartDrawer.show();
        });
    }

    if (sessionStorage.getItem('open_wishlist_drawer') === '1') {
        sessionStorage.removeItem('open_wishlist_drawer');
        window.addEventListener('load', function() {
            const wishlistDrawer = new bootstrap.Offcanvas(document.getElementById('wishlistOffcanvas'));
            wishlistDrawer.show();
        });
    }

    // Hide mobile sticky CTA and prevent background scroll when any offcanvas is opened
    document.addEventListener('show.bs.offcanvas', function (e) {
        const id = e.target.id || 'offcanvas';
        RohidaDebug.log('📱', 'Offcanvas', `Offcanvas '${id}' opened -> Body scroll locked.`);
        document.body.classList.add('hide-mobile-cta', 'offcanvas-active');
    });
    document.addEventListener('hidden.bs.offcanvas', function (e) {
        const id = e.target.id || 'offcanvas';
        RohidaDebug.log('📱', 'Offcanvas', `Offcanvas '${id}' closed -> Body scroll restored.`);
        if (!document.querySelector('.offcanvas.show')) {
            document.body.classList.remove('hide-mobile-cta', 'offcanvas-active');
        }
    });
});

// ── Page Load Skeleton Fade-out ──────────────────────────
window.addEventListener('load', function () {
    document.querySelectorAll('.section-skeleton-overlay').forEach(overlay => {
        overlay.style.transition = 'opacity 0.4s ease';
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        setTimeout(() => overlay.remove(), 400);
    });
});
setTimeout(function() {
    document.querySelectorAll('.section-skeleton-overlay').forEach(overlay => {
        overlay.style.transition = 'opacity 0.4s ease';
        overlay.style.opacity = '0';
        overlay.style.pointerEvents = 'none';
        setTimeout(() => overlay.remove(), 400);
    });
}, 2200);

// ── Button Click Spinner (global delegation) ──
document.addEventListener('click', function (e) {
    const btn = e.target.closest('button[type="submit"], a.btn[href]:not([href="#"]),[data-spinner]');
    if (!btn) return;
    if (btn.classList.contains('btn-loading') || btn.classList.contains('no-spinner')) return;
    if (btn.closest('.copy-coupon-btn')) return;
    btn.classList.add('btn-loading');
    setTimeout(() => btn.classList.remove('btn-loading'), 3000);
});
