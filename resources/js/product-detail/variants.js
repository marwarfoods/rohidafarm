// =================================================
// Variants Module – Variant Card Selection,
//                  Price Update & Shipping Bar
// resources/js/product-detail/variants.js
// =================================================

export function initVariants() {

    const variantCards = document.querySelectorAll('.variant-card');
    const priceTag     = document.getElementById('productPrice');
    const mrpTag       = document.getElementById('productMrp');
    const bestPriceTag = document.getElementById('productBestPrice');
    const hiddenVarId  = document.getElementById('hiddenVariantId');
    const mobileVarId  = document.getElementById('mobileHiddenVariantId');

    variantCards.forEach(card => {
        card.addEventListener('click', function () {
            variantCards.forEach(c => c.classList.remove('active', 'border-success', 'bg-success-subtle'));
            this.classList.add('active', 'border-success', 'bg-success-subtle');

            const newMrp       = this.getAttribute('data-mrp');
            const newPrice     = this.getAttribute('data-price');
            const newBestPrice = this.getAttribute('data-best-price');
            const newBestCoupon = this.getAttribute('data-best-coupon');
            const newId        = this.getAttribute('data-id');
            const newStock     = parseInt(this.getAttribute('data-stock') || '0');

            // Skeleton price animation
            if (priceTag)     priceTag.classList.add('skeleton-loading-text');
            if (mrpTag)       mrpTag.classList.add('skeleton-loading-text');
            if (bestPriceTag) bestPriceTag.classList.add('skeleton-loading-text');

            setTimeout(() => {
                if (priceTag)     priceTag.textContent = newPrice;
                if (mrpTag)       mrpTag.textContent = newMrp;
                
                if (bestPriceTag) {
                    if (newBestCoupon) {
                        bestPriceTag.innerHTML = `<i class="bi bi-tag-fill me-1"></i> Best Price ${newBestPrice} with ${newBestCoupon}`;
                        bestPriceTag.style.display = 'inline-block';
                        const wrapper = document.getElementById('productBestPriceWrapper');
                        if (wrapper) wrapper.style.setProperty('display', 'inline-block', 'important');
                    } else {
                        bestPriceTag.style.display = 'none';
                        const wrapper = document.getElementById('productBestPriceWrapper');
                        if (wrapper) wrapper.style.setProperty('display', 'none', 'important');
                    }
                }
                
                if (hiddenVarId)  hiddenVarId.value = newId;
                if (mobileVarId)  mobileVarId.value = newId;
                const directVarId = document.getElementById('directBuyVariantId');
                if (directVarId) directVarId.value = newId;

                // Toggle Add to Cart / Buy Now vs Sold Out based on variant stock
                const desktopQtyInput    = document.getElementById('desktopQtyInput');
                const mainAddToCartForm  = document.getElementById('mainAddToCartForm');
                const desktopSoldOutBtn  = document.getElementById('desktopSoldOutBtn');
                const mobileAddToCartForm = document.getElementById('mobileAddToCartForm');
                const mobileSoldOutBtn   = document.getElementById('mobileSoldOutBtn');

                if (newStock > 0) {
                    if (desktopQtyInput) desktopQtyInput.classList.remove('d-none');
                    if (mainAddToCartForm) mainAddToCartForm.classList.remove('d-none');
                    if (desktopSoldOutBtn) desktopSoldOutBtn.classList.add('d-none');
                    if (mobileAddToCartForm) {
                        mobileAddToCartForm.classList.remove('d-none');
                        mobileAddToCartForm.style.display = 'contents';
                    }
                    if (mobileSoldOutBtn) mobileSoldOutBtn.classList.add('d-none');
                } else {
                    if (desktopQtyInput) desktopQtyInput.classList.add('d-none');
                    if (mainAddToCartForm) mainAddToCartForm.classList.add('d-none');
                    if (desktopSoldOutBtn) desktopSoldOutBtn.classList.remove('d-none');
                    if (mobileAddToCartForm) {
                        mobileAddToCartForm.classList.add('d-none');
                        mobileAddToCartForm.style.display = 'none';
                    }
                    if (mobileSoldOutBtn) mobileSoldOutBtn.classList.remove('d-none');
                }

                if (priceTag)     priceTag.classList.remove('skeleton-loading-text');
                if (mrpTag)       mrpTag.classList.remove('skeleton-loading-text');
                if (bestPriceTag) bestPriceTag.classList.remove('skeleton-loading-text');
            }, 300);

            // Update free shipping progress bar
            const targetLabel = document.getElementById('deliveryTargetLabel');
            const threshold = targetLabel ? parseFloat(targetLabel.getAttribute('data-threshold')) || 0 : 0;
            
            if (threshold > 0) {
                const price     = parseFloat(newPrice.replace(/[^0-9.]/g, ''));
                const percent   = Math.min((price / threshold) * 100, 100);
                const needed    = Math.max(threshold - price, 0);
                const barEl     = document.getElementById('deliveryProgressBar');
                const msgEl     = document.getElementById('deliveryMessage');

                if (barEl) { barEl.style.width = percent + '%'; barEl.setAttribute('aria-valuenow', percent); }
                if (msgEl) {
                    msgEl.innerHTML = needed > 0
                        ? `Add <span class="text-success fw-bold">₹${needed}</span> more to unlock <span class="text-success fw-bold">FREE Delivery</span>!`
                        : `🎉 <span class="text-success fw-bold">FREE Delivery</span> unlocked!`;
                }
            }
        });
    });
}
