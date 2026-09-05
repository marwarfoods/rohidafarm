// =================================================
// Cart Module – Quantity Controls, Buy Now Buttons & Real-Time Price Shimmer
// resources/js/product-detail/cart.js
// =================================================

export function initCart() {

    // ── Quantity Selector & Real-Time Price Shimmer ──────────────────
    const qtyInput       = document.getElementById('purchaseQuantity');
    const hiddenQty      = document.getElementById('hiddenQuantity');
    const mobileQty      = document.getElementById('mobileHiddenQuantity');
    const mobileQtyInput = document.getElementById('mobilePurchaseQuantity');
    const productPrice   = document.getElementById('productPrice');
    const productMrp     = document.getElementById('productMrp');

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
            syncQty(v);
        });
    });

    document.querySelectorAll('.qty-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const currentVal = parseInt(qtyInput?.value || mobileQtyInput?.value || 1);
            const v = Math.max(currentVal - 1, 1);
            syncQty(v);
        });
    });

    qtyInput?.addEventListener('change', function () {
        let v = parseInt(this.value) || 1;
        v = Math.min(Math.max(v, 1), 10);
        syncQty(v);
    });

    // ── Direct Buy Now (Desktop & Mobile) ────────────────────────────
    const handleDirectBuy = async function (e) {
        e.preventDefault();
        e.stopPropagation();

        const btn = e.currentTarget;
        const originalHtml = btn.innerHTML;

        // Determine which form to use based on clicked button or viewport
        const isMobileBtn = btn.id === 'mobileBtnBuyNowDirect' || window.innerWidth < 768;
        const form = isMobileBtn
            ? (document.getElementById('mobileAddToCartForm') || document.getElementById('mainAddToCartForm'))
            : (document.getElementById('mainAddToCartForm') || document.getElementById('mobileAddToCartForm'));

        if (!form) return;

        // Resolve current active variant ID
        const activeVariantCard = document.querySelector('.variant-card.active');
        let activeVariantId = activeVariantCard 
            ? activeVariantCard.getAttribute('data-id')
            : (document.getElementById('hiddenVariantId')?.value || document.getElementById('mobileHiddenVariantId')?.value || '');

        // Resolve current selected quantity
        const qtyVal = parseInt(document.getElementById('purchaseQuantity')?.value || document.getElementById('mobilePurchaseQuantity')?.value || '1') || 1;

        // Sync hidden fields across forms
        const hiddenVar = document.getElementById('hiddenVariantId');
        const mobileVar = document.getElementById('mobileHiddenVariantId');
        const hiddenQty = document.getElementById('hiddenQuantity');
        const mobileQty = document.getElementById('mobileHiddenQuantity');

        if (hiddenVar) hiddenVar.value = activeVariantId;
        if (mobileVar) mobileVar.value = activeVariantId;
        if (hiddenQty) hiddenQty.value = qtyVal;
        if (mobileQty) mobileQty.value = qtyVal;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Checkout...';

        const formData = new FormData(form);
        formData.set('variant_id', activeVariantId);
        formData.set('quantity', qtyVal);

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token') || '';
            const res = await fetch('/cart/add', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (!res.ok || data.status === 'error') {
                throw new Error(data.message || 'Could not add product to cart.');
            }

            // Prevent cart offcanvas drawer from opening on page load
            sessionStorage.removeItem('open_cart_drawer');

            window.location.href = '/checkout';
        } catch (err) {
            console.error('Direct Buy Error:', err);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            alert(err.message || 'Unable to proceed to checkout. Please try again.');
        }
    };

    document.getElementById('btnBuyNowDirect')?.addEventListener('click', handleDirectBuy);
    document.getElementById('mobileBtnBuyNowDirect')?.addEventListener('click', handleDirectBuy);

    // Also delegate click in case button is dynamically replaced/rendered
    document.addEventListener('click', function (e) {
        const buyBtn = e.target.closest('#btnBuyNowDirect, #mobileBtnBuyNowDirect');
        if (buyBtn && !buyBtn.disabled && buyBtn !== e.currentTarget) {
            // Handled by direct event listener above, but fallback ensures execution
        }
    });

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
