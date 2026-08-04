<!-- Coupons Offcanvas Drawer (Mobile/Desktop Premium Popup) -->
<div class="offcanvas offcanvas-bottom" tabindex="-1" id="couponsOffcanvas" aria-labelledby="couponsOffcanvasLabel" style="height: auto; max-height: 80vh; border-top-left-radius: 24px; border-top-right-radius: 24px; z-index: 1045;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title font-heading fw-bold text-dark d-flex align-items-center" id="couponsOffcanvasLabel">
            <span class="rounded-circle bg-success bg-opacity-10 p-2 d-inline-flex align-items-center justify-content-center me-2 text-success" style="width: 38px; height: 38px;">
                <i class="bi bi-ticket-perforated fs-5"></i>
            </span>
            Available Store Coupons
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 bg-light">
        <p class="text-muted" style="font-size: 0.85rem; margin-top: -10px; margin-bottom: 20px;">Click on any coupon code below to copy it and apply it at the checkout page for instant savings.</p>
        
        <div class="d-flex flex-column gap-3">
            <!-- Coupon Item 1 -->
            <div class="bg-white border rounded-4 p-3 d-flex align-items-center justify-content-between shadow-sm" style="border-color: #ECE7DD !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-style: dashed !important; border-width: 2px !important; border-color: rgba(255,255,255,0.4) !important;">
                        <span class="fw-bold font-heading" style="font-size: 0.8rem;">10%</span>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">10% Instant Discount</h6>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Applicable on all products.</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success copy-coupon-btn px-3 py-1.5 rounded-pill font-heading fw-bold" data-code="ROHIDA10" style="font-size: 0.75rem;">
                    ROHIDA10
                </button>
            </div>

            <!-- Coupon Item 2 -->
            <div class="bg-white border rounded-4 p-3 d-flex align-items-center justify-content-between shadow-sm" style="border-color: #ECE7DD !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-style: dashed !important; border-width: 2px !important; border-color: rgba(255,255,255,0.4) !important;">
                        <i class="bi bi-truck fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">Free Shipping Coupon</h6>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Free delivery on orders above ₹999.</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success copy-coupon-btn px-3 py-1.5 rounded-pill font-heading fw-bold" data-code="FREE999" style="font-size: 0.75rem;">
                    FREE999
                </button>
            </div>

            <!-- Coupon Item 3 -->
            <div class="bg-white border rounded-4 p-3 d-flex align-items-center justify-content-between shadow-sm" style="border-color: #ECE7DD !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success text-white p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-style: dashed !important; border-width: 2px !important; border-color: rgba(255,255,255,0.4) !important;">
                        <span class="fw-bold font-heading" style="font-size: 0.8rem;">₹200</span>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">Bilona Ghee Special</h6>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Flat ₹200 off on pure cow ghee.</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-success copy-coupon-btn px-3 py-1.5 rounded-pill font-heading fw-bold" data-code="BILO200" style="font-size: 0.75rem;">
                    BILO200
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.copy-coupon-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                navigator.clipboard.writeText(code).then(() => {
                    const originalText = this.innerText;
                    this.innerHTML = '<i class="bi bi-check-circle-fill"></i> Copied!';
                    this.classList.remove('btn-outline-success');
                    this.classList.add('btn-success');
                    
                    setTimeout(() => {
                        this.innerText = originalText;
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-success');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            });
        });
    });
</script>
