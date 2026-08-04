// =================================================
// Cart Module – Quantity Controls, Buy Now Buttons & Real-Time Price Shimmer
// resources/js/product-detail/cart.js
// =================================================

export function initCart() {

    // ── Quantity Selector & Real-Time Price Shimmer ──────────────────
    const qtyInput     = document.getElementById('purchaseQuantity');
    const hiddenQty    = document.getElementById('hiddenQuantity');
    const mobileQty    = document.getElementById('mobileHiddenQuantity');
    const productPrice = document.getElementById('productPrice');
    const productMrp   = document.getElementById('productMrp');

    const updatePriceDisplay = (qty) => {
        const activeVariant = document.querySelector('.variant-card.active');
        let unitPrice = 0;
        let unitMrp = 0;

        if (activeVariant) {
            unitPrice = parseFloat(activeVariant.getAttribute('data-price').replace(/[^0-9.]/g, '')) || 0;
            unitMrp   = parseFloat(activeVariant.getAttribute('data-mrp').replace(/[^0-9.]/g, '')) || 0;
        } else if (productPrice) {
            unitPrice = parseFloat(productPrice.getAttribute('data-unit-price') || productPrice.textContent.replace(/[^0-9.]/g, '')) || 0;
            unitMrp   = productMrp ? (parseFloat(productMrp.getAttribute('data-unit-mrp') || productMrp.textContent.replace(/[^0-9.]/g, '')) || 0) : 0;
        }

        if (unitPrice > 0 && productPrice) {
            const totalPrice = unitPrice * qty;
            const totalMrp   = unitMrp * qty;

            if (typeof window.RohidaDebug === 'object') {
                window.RohidaDebug.log('💰', 'Price Calculate', `Quantity = ${qty} -> Total Price: ₹${totalPrice}`);
            }

            productPrice.classList.add('price-loading-shimmer');
            if (productMrp) productMrp.classList.add('price-loading-shimmer');

            setTimeout(() => {
                productPrice.textContent = `₹${totalPrice.toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
                if (productMrp && totalMrp > 0) {
                    productMrp.textContent = `₹${totalMrp.toLocaleString('en-IN', { maximumFractionDigits: 0 })}`;
                }
                productPrice.classList.remove('price-loading-shimmer');
                if (productMrp) productMrp.classList.remove('price-loading-shimmer');
            }, 250);
        }
    };

    const syncQty = val => {
        if (hiddenQty) hiddenQty.value = val;
        if (mobileQty) mobileQty.value = val;
        if (qtyInput)  qtyInput.value  = val;
        if (mobileQtyInput) mobileQtyInput.value = val;
        updatePriceDisplay(val);
    };

    document.querySelectorAll('.qty-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const currentVal = parseInt(qtyInput?.value || mobileQtyInput?.value || 1);
            const v = Math.min(currentVal + 1, 10);
            if (typeof window.RohidaDebug === 'object') {
                window.RohidaDebug.log('➕', 'Single Product Qty', `Quantity incremented to ${v}`);
            }
            syncQty(v);
        });
    });

    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const currentVal = parseInt(qtyInput?.value || mobileQtyInput?.value || 1);
            const v = Math.max(currentVal - 1, 1);
            if (typeof window.RohidaDebug === 'object') {
                window.RohidaDebug.log('➖', 'Single Product Qty', `Quantity decremented to ${v}`);
            }
            syncQty(v);
        });
    });

    qtyInput?.addEventListener('change', function () {
        let v = parseInt(this.value) || 1;
        v = Math.min(Math.max(v, 1), 10);
        if (typeof window.RohidaDebug === 'object') {
            window.RohidaDebug.log('🔢', 'Single Product Qty', `Quantity input changed to ${v}`);
        }
        syncQty(v);
    });

    // ── Direct Buy Now (Desktop & Mobile) ────────────────────────────
    const handleDirectBuy = function (e) {
        e.preventDefault();
        const form = document.getElementById('mainAddToCartForm') || document.getElementById('mobileAddToCartForm');
        if (!form) return;

        if (typeof window.RohidaDebug === 'object') {
            window.RohidaDebug.log('🚀', 'Direct Buy', 'Buy Now button clicked! Adding item to cart and redirecting to /checkout...');
        }

        const btn = e.currentTarget;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Redirecting to Checkout...';

        const formData = new FormData(form);
        fetch('/cart/add', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (typeof window.RohidaDebug === 'object') {
                window.RohidaDebug.log('✅', 'Direct Buy', 'Item added successfully -> Redirecting to /checkout now!');
            }
            window.location.href = '/checkout';
        })
        .catch(err => {
            window.location.href = '/checkout';
        });
    };

    document.getElementById('btnBuyNowDirect')?.addEventListener('click', handleDirectBuy);
    document.getElementById('mobileBtnBuyNowDirect')?.addEventListener('click', handleDirectBuy);

    // ── Short Description Read More / Read Less ───────
    const descText   = document.getElementById('shortDescText');
    const descToggle = document.getElementById('shortDescToggle');
    const descIcon   = document.getElementById('shortDescIcon');
    const descLabel  = document.getElementById('shortDescLabel');

    if (descText && descToggle) {
        const checkOverflow = () => {
            const lineH   = parseFloat(getComputedStyle(descText).lineHeight) || 27;
            const maxH    = lineH * 4;
            if (descText.scrollHeight <= maxH + 4) {
                descToggle.style.display = 'none';
            } else {
                descToggle.style.display = '';
            }
        };
        checkOverflow();

        let expanded = false;
        descToggle.addEventListener('click', () => {
            expanded = !expanded;
            descText.classList.toggle('expanded', expanded);
            descIcon.className  = expanded ? 'bi bi-chevron-up me-1' : 'bi bi-chevron-down me-1';
            descLabel.textContent = expanded ? 'Read Less' : 'Read More';
        });
    }
}
