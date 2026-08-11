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
        <p class="text-muted" style="font-size: 0.85rem; margin-top: -10px; margin-bottom: 20px;">Click on any coupon below to copy its code and jump straight to the offer.</p>

        <div class="d-flex flex-column gap-3">
            @forelse($coupons as $coupon)
                <a href="{{ $coupon->target_link }}"
                   class="coupon-card-link bg-white border rounded-4 p-3 d-flex align-items-center justify-content-between shadow-sm text-decoration-none"
                   data-code="{{ $coupon->code }}"
                   style="border-color: #ECE7DD !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white p-3 rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-style: dashed !important; border-width: 2px !important; border-color: rgba(255,255,255,0.4) !important;">
                            <span class="fw-bold font-heading" style="font-size: 0.8rem;">
                                {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '%' : '₹' . number_format($coupon->discount_value, 0) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">
                                {{ $coupon->discount_type === 'percentage' ? $coupon->discount_value . '% Instant Discount' : '₹' . number_format($coupon->discount_value, 0) . ' Off' }}
                            </h6>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                @if($coupon->target_type === 'products')
                                    Applicable on selected product.
                                @elseif($coupon->target_type === 'categories')
                                    Applicable on selected category.
                                @else
                                    Applicable on all products.
                                @endif
                                @if($coupon->min_amount > 0)
                                    Min order ₹{{ number_format($coupon->min_amount, 0) }}.
                                @endif
                            </small>
                        </div>
                    </div>
                    <span class="btn btn-sm btn-outline-success copy-coupon-btn px-3 py-1.5 rounded-pill font-heading fw-bold" style="font-size: 0.75rem; pointer-events: none;">
                        {{ $coupon->code }}
                    </span>
                </a>
            @empty
                <p class="text-muted text-center py-4 mb-0">No active coupons available right now. Check back soon!</p>
            @endforelse
        </div>
    </div>
</div>

<style>
    .coupon-toast {
        position: fixed;
        bottom: 90px;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: #1c301c;
        color: #fff;
        padding: 10px 20px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        z-index: 2000;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease, transform 0.25s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .coupon-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function showCouponToast(message) {
            let toast = document.querySelector('.coupon-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.className = 'coupon-toast';
                document.body.appendChild(toast);
            }
            toast.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + message;
            requestAnimationFrame(() => toast.classList.add('show'));
        }

        document.querySelectorAll('.coupon-card-link').forEach(card => {
            card.addEventListener('click', function(e) {
                e.preventDefault();
                const code = this.getAttribute('data-code');
                const destination = this.getAttribute('href');
                const badge = this.querySelector('.copy-coupon-btn');
                const originalText = badge ? badge.innerText : '';

                navigator.clipboard.writeText(code).then(() => {
                    showCouponToast('Offer Copied: ' + code);
                    if (badge) {
                        badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Copied!';
                        badge.classList.remove('btn-outline-success');
                        badge.classList.add('btn-success');
                    }
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });

                setTimeout(() => {
                    window.location.href = destination;
                }, 900);
            });
        });
    });
</script>
