{{--
    Available coupons list for the cart page and checkout. "Apply" fills the page's
    #couponInput and clicks its #btnApplyCoupon, so the page's own apply/remove logic
    (and totals update) is reused. $coupons comes from the view composer in AppServiceProvider.
--}}
@if($coupons->isNotEmpty())
    <div class="available-coupons mt-3">
        <div class="text-uppercase fw-bold text-muted mb-2" style="font-size: 0.7rem; letter-spacing: 1px;">
            <i class="bi bi-ticket-perforated me-1"></i> Available Coupons
        </div>
        <div class="d-flex flex-column gap-2">
            @foreach($coupons as $coupon)
                @php
                    $isPercent = $coupon->discount_type === 'percentage';
                    $valueLabel = $isPercent ? rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') . '% OFF' : '₹' . number_format($coupon->discount_value, 0) . ' OFF';
                @endphp
                <div class="available-coupon d-flex align-items-center justify-content-between gap-2 p-2 rounded-3 border" data-code="{{ strtoupper($coupon->code) }}">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <span class="available-coupon-code font-heading fw-bold text-success text-nowrap">{{ strtoupper($coupon->code) }}</span>
                        <div class="min-w-0">
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">{{ $valueLabel }}</div>
                            <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.3;">
                                @if($coupon->target_type === 'products')
                                    On selected products.
                                @elseif($coupon->target_type === 'categories')
                                    On selected categories.
                                @else
                                    On all products.
                                @endif
                                @if($coupon->min_amount > 0)
                                    Min order ₹{{ number_format($coupon->min_amount, 0) }}.
                                @endif
                                @if($isPercent && $coupon->max_discount > 0)
                                    Up to ₹{{ number_format($coupon->max_discount, 0) }}.
                                @endif
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-coupon-black btn-sm rounded-pill px-3 fw-bold flex-shrink-0 btn-apply-listed-coupon" style="font-size: 0.72rem;">APPLY</button>
                </div>
            @endforeach
        </div>
    </div>

    @pushOnce('scripts')
        <style>
            .available-coupon { background: #fcfbfa; border-color: #ECE7DD !important; }
            .available-coupon.is-applied { background: #f0fdf4; border-color: #86efac !important; }
            .available-coupon-code {
                font-size: 0.72rem; letter-spacing: 0.5px; padding: 4px 8px; border-radius: 6px;
                border: 1.5px dashed #198754; background: #fff;
            }
            .available-coupons .min-w-0 { min-width: 0; }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const input = document.getElementById('couponInput');
                const applyBtn = document.getElementById('btnApplyCoupon');
                const appliedGroup = document.getElementById('couponAppliedGroup');
                const appliedCode = document.getElementById('appliedCouponCode');
                if (!input || !applyBtn) return;

                // Mark the listed coupon that is currently applied.
                function syncApplied() {
                    const isApplied = appliedGroup && appliedGroup.style.display !== 'none';
                    const code = isApplied && appliedCode ? appliedCode.textContent.replace(/^Code:\s*/i, '').trim().toUpperCase() : '';
                    document.querySelectorAll('.available-coupon').forEach(row => {
                        const on = code !== '' && row.dataset.code === code;
                        row.classList.toggle('is-applied', on);
                        const btn = row.querySelector('.btn-apply-listed-coupon');
                        btn.textContent = on ? 'APPLIED' : 'APPLY';
                        btn.disabled = on;
                    });
                }

                document.querySelectorAll('.btn-apply-listed-coupon').forEach(btn => {
                    btn.addEventListener('click', function () {
                        input.value = this.closest('.available-coupon').dataset.code;
                        applyBtn.click();
                    });
                });

                if (appliedGroup) new MutationObserver(syncApplied).observe(appliedGroup, { attributes: true, attributeFilter: ['style'] });
                if (appliedCode) new MutationObserver(syncApplied).observe(appliedCode, { childList: true, characterData: true, subtree: true });
                syncApplied();
            });
        </script>
    @endPushOnce
@endif
