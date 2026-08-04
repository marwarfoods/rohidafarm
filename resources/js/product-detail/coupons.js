// =================================================
// Coupons Module – Copy Coupon Code to Clipboard
// resources/js/product-detail/coupons.js
// =================================================

export function initCoupons() {
    document.querySelectorAll('.copy-coupon-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const code    = this.getAttribute('data-code');
            const copyBtn = this.querySelector('.coupon-copy-btn');
            navigator.clipboard.writeText(code).then(() => {
                const orig = copyBtn.textContent;
                copyBtn.textContent = 'COPIED!';
                copyBtn.classList.replace('btn-outline-success', 'btn-success');
                copyBtn.classList.add('text-white');
                setTimeout(() => {
                    copyBtn.textContent = orig;
                    copyBtn.classList.replace('btn-success', 'btn-outline-success');
                    copyBtn.classList.remove('text-white');
                }, 1600);
            });
        });
    });
}
