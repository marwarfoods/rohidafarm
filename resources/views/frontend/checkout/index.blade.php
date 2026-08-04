@extends('layouts.app')

@section('content')
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <h1 class="display-6 font-heading fw-bold m-0">Secure Checkout</h1>
    </div>
</section>

<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container">
        <!-- Error Alerts -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
            @csrf
            
            <div class="row g-4">
                <!-- Shipping Address Form -->
                <div class="col-lg-7">
                    <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
                        <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Shipping Address</h4>
                        
                        <!-- Saved Addresses Picker -->
                        @if($addresses->isNotEmpty())
                            <div class="mb-4">
                                <h6 class="fw-bold text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Select Saved Address:</h6>
                                <div class="row g-2">
                                    @foreach($addresses as $addr)
                                        <div class="col-md-6">
                                            <div class="card p-3 rounded-3 border address-card cursor-pointer {{ $addr->is_default ? 'border-success bg-success-subtle' : '' }}" 
                                                data-name="{{ $addr->name }}"
                                                data-phone="{{ $addr->phone }}"
                                                data-line1="{{ $addr->address_line1 }}"
                                                data-line2="{{ $addr->address_line2 }}"
                                                data-city="{{ $addr->city }}"
                                                data-state="{{ $addr->state }}"
                                                data-zip="{{ $addr->postal_code }}"
                                                style="font-size: 0.85rem; cursor: pointer;">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong>{{ $addr->name }}</strong>
                                                    <span class="badge text-uppercase bg-secondary" style="font-size: 0.6rem;">{{ $addr->type }}</span>
                                                </div>
                                                <p class="text-muted m-0 text-truncate">{{ $addr->address_line1 }}</p>
                                                <p class="text-muted m-0">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Manual Form -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" name="name" id="shippingName" class="form-control bg-light border p-2 @error('name') is-invalid @enderror" value="{{ old('name', $defaultAddress?->name ?? Auth::user()?->name) }}" placeholder="Full Name *" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <input type="text" name="phone" id="shippingPhone" class="form-control bg-light border p-2 @error('phone') is-invalid @enderror" value="{{ old('phone', $defaultAddress?->phone ?? Auth::user()?->phone) }}" placeholder="Mobile Number *" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <input type="email" name="email" id="shippingEmail" class="form-control bg-light border p-2 @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()?->email) }}" placeholder="Email Address *" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <input type="text" name="address_line1" id="shippingLine1" class="form-control bg-light border p-2 @error('address_line1') is-invalid @enderror" value="{{ old('address_line1', $defaultAddress?->address_line1) }}" placeholder="Street Address / House No. *" required>
                                @error('address_line1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <input type="text" name="address_line2" id="shippingLine2" class="form-control bg-light border p-2" value="{{ old('address_line2', $defaultAddress?->address_line2) }}" placeholder="Landmark / Area (Optional)">
                            </div>

                            <div class="col-md-4">
                                <div class="position-relative">
                                    <input type="text" name="postal_code" id="shippingZip" class="form-control bg-light border p-2 pe-5 @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $defaultAddress?->postal_code) }}" placeholder="Pin Code *" required maxlength="6" pattern="\d{6}">
                                    <button type="button" id="btnFetchLocation" class="btn btn-sm btn-outline-success position-absolute top-50 end-0 translate-middle-y me-1 border-0" title="Fetch my location" style="z-index: 5;">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </button>
                                </div>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small id="pincodeMessage" class="mt-1 d-block fw-bold" style="display: none;"></small>
                            </div>

                            <div class="col-md-4">
                                <input type="text" name="city" id="shippingCity" class="form-control bg-light border p-2 @error('city') is-invalid @enderror" value="{{ old('city', $defaultAddress?->city) }}" placeholder="City *" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <input type="text" name="state" id="shippingState" class="form-control bg-light border p-2 @error('state') is-invalid @enderror" value="{{ old('state', $defaultAddress?->state) }}" placeholder="State *" required>
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Save Address flag -->
                        @auth
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="save_address" id="save_address" value="1" checked>
                                <label class="form-check-label text-muted" for="save_address" style="font-size: 0.85rem;">Save this address to my profile</label>
                            </div>
                        @endauth
                    </div>

                    <!-- Payment Gateways Section -->
                    <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                        <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Select Payment Method</h4>
                        
                        <div class="d-flex flex-column gap-3">
                            @php
                                $codEnabled = \App\Models\Setting::get('enable_cod', true);
                                $rzpEnabled = \App\Models\Setting::get('enable_razorpay', false);
                            @endphp

                            <!-- Cash On Delivery Option -->
                            @if($codEnabled)
                                <div class="p-3 border rounded-3 d-flex align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
                                        <label class="form-check-label fw-bold text-dark" for="pay_cod" style="font-size: 0.95rem;">
                                            <i class="bi bi-cash text-success me-2"></i> Cash On Delivery (COD)
                                        </label>
                                        <span class="d-block text-muted" style="font-size: 0.75rem;">Pay in cash or UPI when the package is delivered.</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Razorpay / Online option -->
                            @if($rzpEnabled)
                                <div class="p-3 border rounded-3 d-flex align-items-center">
                                    <div class="form-check w-100">
                                        <input class="form-check-input mt-2" type="radio" name="payment_method" id="pay_razorpay" value="razorpay" {{ !$codEnabled ? 'checked' : '' }}>
                                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between w-100">
                                            <div>
                                                <label class="form-check-label fw-bold text-dark d-flex align-items-center mb-1" for="pay_razorpay" style="font-size: 0.95rem;">
                                                    <i class="bi bi-credit-card-2-front text-success me-2"></i> Pay via Razorpay
                                                </label>
                                                <span class="d-block text-muted" style="font-size: 0.75rem;">Pay securely using Cards, NetBanking, or UPI.</span>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1 mt-2 mt-sm-0 align-items-center">
                                                <img src="https://cashfreelogo.cashfree.com/assets_images/pg/upi/32/gpay.png" alt="GPay" style="height: 22px;" class="border rounded-1 bg-white p-1">
                                                <img src="https://cashfreelogo.cashfree.com/assets_images/pg/wallet/32/phonepe.png" alt="PhonePe" style="height: 22px;" class="border rounded-1 bg-white p-1">
                                                <img src="https://cashfreelogo.cashfree.com/assets_images/pg/wallet/32/paytm.png" alt="Paytm" style="height: 22px;" class="border rounded-1 bg-white p-1">
                                                <img src="https://cashfreelogo.cashfree.com/assets_images/pg/upi/32/bhim.png" alt="BHIM" style="height: 22px;" class="border rounded-1 bg-white p-1">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" alt="Mastercard" style="height: 22px;" class="border rounded-1 bg-white p-1">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/57/Visa_Inc._logo_%282014%E2%80%932021%29.svg" alt="Visa" style="height: 22px;" class="border rounded-1 bg-white p-1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="col-lg-5">
                    <div class="bg-white p-4 rounded-4 shadow-sm border position-sticky" style="top: 100px; border-color: var(--border-color) !important;">
                        <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Order Details</h4>
                        
                        <!-- Mini Cart Items list -->
                        <div class="mini-cart-review mb-4" style="max-height: 350px; overflow-y: auto;">
                            @foreach($items as $item)
                                @php
                                    $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
                                    $weight = $item->variant ? $item->variant->name : $item->product->weight;
                                    $img = $item->product->primaryImage ? asset($item->product->primaryImage->image_path) : asset('/assets/images/placeholder.jpg');
                                @endphp
                                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3" id="checkoutRow_{{ $item->cart_id }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $img }}" alt="{{ $item->product->name }}" class="rounded object-fit-cover border" style="width: 55px; height: 55px;">
                                        <div>
                                            <h6 class="fw-bold font-heading text-dark m-0" style="font-size: 0.85rem; line-height: 1.3;">{{ Str::limit($item->product->name, 40) }}</h6>
                                            <span class="text-muted d-block mt-1" style="font-size: 0.75rem;">Var: {{ $weight }}</span>
                                            
                                            <!-- Quantity Controls -->
                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                <b style="font-size: 0.9rem;" class="text-dark">Qty:</b>
                                                <div class="quantity-input border rounded-pill d-inline-flex align-items-center bg-white" style="padding: 1px 6px; scale: 0.85; transform-origin: left;">
                                                    <button type="button" class="btn border-0 p-0 bg-transparent text-dark btn-checkout-dec" data-id="{{ $item->cart_id }}"><i class="bi-dash"></i></button>
                                                    <input type="number" class="form-control border-0 bg-transparent text-center fw-bold shadow-none p-0 checkout-qty" value="{{ $item->quantity }}" style="width: 30px; font-size: 0.9rem;" readonly>
                                                    <button type="button" class="btn border-0 p-0 bg-transparent text-dark btn-checkout-inc" data-id="{{ $item->cart_id }}"><i class="bi-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-column align-items-end gap-2">
                                        <button type="button" class="btn btn-sm text-danger p-0 border-0 btn-checkout-remove" data-id="{{ $item->cart_id }}" aria-label="Remove item"><i class="bi bi-trash"></i></button>
                                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">₹{{ number_format($price * $item->quantity, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Coupon Accordion/Dropdown -->
                        <div class="accordion mb-4 border rounded-3 overflow-hidden" id="couponAccordion" style="border-color: #ECE7DD !important;">
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed py-2 px-3 bg-light shadow-none fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCoupon" aria-expanded="false" aria-controls="collapseCoupon" style="font-size: 0.85rem;">
                                        <i class="bi bi-tag-fill text-success me-2"></i> Have a coupon code?
                                    </button>
                                </h2>
                                <div id="collapseCoupon" class="accordion-collapse collapse {{ $totals['coupon'] ? 'show' : '' }}" data-bs-parent="#couponAccordion">
                                    <div class="accordion-body p-3 bg-white">
                                        <!-- Apply Group -->
                                        <div class="input-group mb-0" id="couponApplyGroup" style="{{ $totals['coupon'] ? 'display:none !important;' : '' }}">
                                            <input type="text" id="couponInput" class="form-control border shadow-none" placeholder="Enter Code (e.g. PURE15)" style="font-size: 0.85rem; padding: 8px 12px;">
                                            <button type="button" id="btnApplyCoupon" class="btn btn-dark fw-bold px-3" style="font-size: 0.8rem;">APPLY</button>
                                        </div>

                                        <!-- Applied Group -->
                                        <div class="bg-success-subtle border border-success-subtle p-2 rounded-2 d-flex justify-content-between align-items-center" id="couponAppliedGroup" style="{{ $totals['coupon'] ? '' : 'display:none !important;' }}">
                                            <div>
                                                <span class="fw-bold text-success d-block" style="font-size: 0.8rem;" id="appliedCouponCode">Code: {{ $totals['coupon']?->code }}</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Applied!</span>
                                            </div>
                                            <button type="button" id="btnRemoveCoupon" class="btn btn-sm text-danger p-0 border-0"><i class="bi bi-x-circle-fill fs-6"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Calculations -->
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-medium" style="font-size: 0.9rem;">Subtotal</span>
                            <span class="fw-bold text-dark" id="summarySubtotal" style="font-size: 0.9rem;">₹{{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 text-success" id="discountBlock" style="{{ $totals['discount'] > 0 ? '' : 'display:none !important;' }}">
                            <span class="fw-medium" style="font-size: 0.9rem;">Discount <span id="discountCodeText">({{ $totals['coupon']?->code }})</span></span>
                            <span class="fw-bold" id="summaryDiscount" style="font-size: 0.9rem;">-₹{{ number_format($totals['discount'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-medium" style="font-size: 0.9rem;">Taxes</span>
                            <span class="fw-bold text-dark" id="summaryTax" style="font-size: 0.9rem;">₹{{ number_format($totals['tax'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2 text-primary" id="paymentAdjBlock" style="{{ (isset($totals['payment_adj']) && $totals['payment_adj'] != 0) ? '' : 'display:none !important;' }}">
                            <span class="fw-medium" style="font-size: 0.9rem;" id="paymentAdjLabel">{{ (isset($totals['payment_adj']) && $totals['payment_adj'] > 0) ? 'COD Charge' : 'Online Discount' }}</span>
                            <span class="fw-bold" id="summaryPaymentAdj" style="font-size: 0.9rem;">{{ (isset($totals['payment_adj']) && $totals['payment_adj'] > 0) ? '+₹' . number_format($totals['payment_adj'], 2) : '-₹' . number_format(abs($totals['payment_adj'] ?? 0), 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                            <span class="text-muted fw-medium" style="font-size: 0.9rem;">Delivery Charges</span>
                            <span class="fw-bold text-dark" id="summaryShipping" style="font-size: 0.9rem;">{{$totals['shipping'] > 0 ? '₹' . number_format($totals['shipping'], 2) : 'FREE'}}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5 fw-bold text-dark font-heading">Total Amount</span>
                            <span class="fs-4 fw-bold text-success font-heading" id="summaryTotal">₹{{ number_format($totals['total'], 2) }}</span>
                        </div>

                        <button type="button" id="placeOrderBtn" class="btn btn-premium w-100 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.85rem; letter-spacing: 0.5px;">Place Order Now</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle active styling on saved address click
        const addressCards = document.querySelectorAll('.address-card');
        
        addressCards.forEach(card => {
            card.addEventListener('click', function () {
                addressCards.forEach(c => {
                    c.classList.remove('border-success', 'bg-success-subtle');
                });
                this.classList.add('border-success', 'bg-success-subtle');

                // Autoload values into the form fields
                document.getElementById('shippingName').value = this.getAttribute('data-name');
                document.getElementById('shippingPhone').value = this.getAttribute('data-phone');
                document.getElementById('shippingLine1').value = this.getAttribute('data-line1');
                document.getElementById('shippingLine2').value = this.getAttribute('data-line2') || '';
                document.getElementById('shippingCity').value = this.getAttribute('data-city');
                document.getElementById('shippingState').value = this.getAttribute('data-state');
                document.getElementById('shippingZip').value = this.getAttribute('data-zip');
            });
        });

        // Format Currency Helper
        const formatCurrency = (val) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(val).replace('₹', '₹');

        // Update Summary DOM
        function updateSummary(totals) {
            document.getElementById('summarySubtotal').textContent = formatCurrency(totals.subtotal);
            document.getElementById('summaryTax').textContent = formatCurrency(totals.tax);
            document.getElementById('summaryShipping').textContent = totals.shipping > 0 ? formatCurrency(totals.shipping) : 'FREE';
            document.getElementById('summaryTotal').textContent = formatCurrency(totals.total);

            const discountBlock = document.getElementById('discountBlock');
            if (totals.discount > 0) {
                discountBlock.style.setProperty('display', 'flex', 'important');
                document.getElementById('summaryDiscount').textContent = '-' + formatCurrency(totals.discount);
                if(totals.coupon) document.getElementById('discountCodeText').textContent = '(' + totals.coupon.code + ')';
            } else {
                discountBlock.style.setProperty('display', 'none', 'important');
            }

            const paymentAdjBlock = document.getElementById('paymentAdjBlock');
            if (totals.payment_adj && totals.payment_adj !== 0) {
                paymentAdjBlock.style.setProperty('display', 'flex', 'important');
                document.getElementById('paymentAdjLabel').textContent = totals.payment_adj > 0 ? 'COD Charge' : 'Online Discount';
                document.getElementById('summaryPaymentAdj').textContent = totals.payment_adj > 0 ? '+' + formatCurrency(totals.payment_adj) : '-' + formatCurrency(Math.abs(totals.payment_adj));
            } else {
                paymentAdjBlock.style.setProperty('display', 'none', 'important');
            }
        }

        // Fetch Dynamic Totals
        function recalculateTotals() {
            const state = document.getElementById('shippingState').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value || '';

            fetch('/checkout/calculate-totals', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ state, payment_method: paymentMethod })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateSummary(data.totals);
                }
            });
        }

        // Apply Coupon Event
        const btnApply = document.getElementById('btnApplyCoupon');
        if (btnApply) {
            btnApply.addEventListener('click', function () {
                const code = document.getElementById('couponInput').value;
                if (!code) return;
                
                const btnOriginalText = btnApply.innerHTML;
                btnApply.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                btnApply.disabled = true;

                fetch('/cart/coupon/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ code: code })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('couponApplyGroup').style.setProperty('display', 'none', 'important');
                        document.getElementById('couponAppliedGroup').style.setProperty('display', 'flex', 'important');
                        document.getElementById('appliedCouponCode').textContent = 'Code: ' + code.toUpperCase();
                        updateSummary(data.totals);
                    } else {
                        alert(data.message);
                    }
                })
                .finally(() => {
                    btnApply.innerHTML = btnOriginalText;
                    btnApply.disabled = false;
                });
            });
        }

        // Remove Coupon Event
        const btnRemove = document.getElementById('btnRemoveCoupon');
        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                fetch('/cart/coupon/remove', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('couponApplyGroup').style.setProperty('display', 'flex', 'important');
                        document.getElementById('couponAppliedGroup').style.setProperty('display', 'none', 'important');
                        document.getElementById('couponInput').value = '';
                        updateSummary(data.totals);
                    }
                });
            });
        }

        // Place Order Form Submission
        const checkoutForm = document.getElementById('checkoutForm');
        const placeOrderBtn = document.getElementById('placeOrderBtn');

        placeOrderBtn.addEventListener('click', function(e) {
            // Check form validity before submitting
            if (!checkoutForm.checkValidity()) {
                checkoutForm.reportValidity();
                return;
            }

            const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
            
            placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
            placeOrderBtn.disabled = true;

            // Submit form for backend processing (which will handle COD logic or redirect to Razorpay logic)
            checkoutForm.submit();
        });

        // Checkout Item Controls (reload page to ensure secure totals)
        document.querySelectorAll('.btn-checkout-inc, .btn-checkout-dec').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const isInc = this.classList.contains('btn-checkout-inc');
                const inp = this.parentElement.querySelector('.checkout-qty');
                let val = parseInt(inp.value);
                
                if (isInc && val < 10) val++;
                else if (!isInc && val > 1) val--;
                else return; // no change

                this.disabled = true;
                
                fetch(`/cart/update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ quantity: val })
                })
                .then(res => {
                    if (res.ok) window.location.reload();
                    else {
                        this.disabled = false;
                        res.json().then(data => alert(data.message || 'Stock limits reached.'));
                    }
                })
                .catch(err => window.location.reload());
            });
        });

        document.querySelectorAll('.btn-checkout-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                this.innerHTML = '<span class="spinner-border spinner-border-sm text-danger" style="width:14px;height:14px;"></span>';
                this.style.pointerEvents = 'none';

                fetch(`/cart/remove/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(res => {
                    return res.json();
                })
                .then(data => {
                    window.location.reload();
                })
                .catch(err => {
                    window.location.reload();
                });
            });
        });
        // Pincode Auto-fetch logic
        const zipInput = document.getElementById('shippingZip');
        const cityInput = document.getElementById('shippingCity');
        const stateInput = document.getElementById('shippingState');
        const pinMessage = document.getElementById('pincodeMessage');

        zipInput.addEventListener('keyup', function() {
            let pin = this.value.trim();
            if (pin.length === 6 && !isNaN(pin)) {
                pinMessage.style.display = 'block';
                pinMessage.className = 'text-muted mt-1 d-block';
                pinMessage.textContent = 'Fetching details...';
                
                fetch(`https://api.postalpincode.in/pincode/${pin}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data[0].Status === 'Success') {
                            const postOffice = data[0].PostOffice[0];
                            cityInput.value = postOffice.District;
                            stateInput.value = postOffice.State;
                            pinMessage.className = 'text-success mt-1 d-block';
                            pinMessage.textContent = 'City and State fetched successfully!';
                            setTimeout(() => pinMessage.style.display = 'none', 3000);
                            recalculateTotals();
                        } else {
                            pinMessage.className = 'text-danger mt-1 d-block';
                            pinMessage.textContent = 'Invalid Pincode.';
                        }
                    })
                    .catch(() => {
                        pinMessage.className = 'text-danger mt-1 d-block';
                        pinMessage.textContent = 'Error fetching pincode details.';
                    });
            }
        });

        // Trigger recalculate on State or Payment Method change
        stateInput.addEventListener('change', recalculateTotals);
        stateInput.addEventListener('blur', recalculateTotals);
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', recalculateTotals);
        });

        // Initial Calculation
        if(stateInput.value) {
            recalculateTotals();
        }
        
        // Pincode Validation Block
        const BLOCKED_PINCODES = {!! json_encode(array_map('trim', explode(',', \App\Models\Setting::get('blocked_pincodes') ?? ''))) !!};
        
        function validateBlockedPincode() {
            const zip = zipInput.value.trim();
            if (zip.length === 6) {
                if (BLOCKED_PINCODES.includes(zip)) {
                    pinMessage.style.display = 'block';
                    pinMessage.textContent = 'Your pincode has been blocked, please try another location.';
                    pinMessage.className = 'text-danger mt-1 d-block fw-bold small';
                    placeOrderBtn.disabled = true;
                } else {
                    placeOrderBtn.disabled = false;
                }
            } else {
                placeOrderBtn.disabled = false;
            }
        }
        
        zipInput.addEventListener('keyup', validateBlockedPincode);
        zipInput.addEventListener('change', validateBlockedPincode);
        window.addEventListener('load', validateBlockedPincode);

        // Fetch Location via Geolocation API
        const btnFetchLocation = document.getElementById('btnFetchLocation');
        if(btnFetchLocation) {
            btnFetchLocation.addEventListener('click', function() {
                if ("geolocation" in navigator) {
                    const btn = this;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm text-success" role="status"></span>';
                    navigator.geolocation.getCurrentPosition(function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`)
                            .then(res => res.json())
                            .then(data => {
                                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
                                if (data && data.address) {
                                    if (data.address.postcode) {
                                        zipInput.value = data.address.postcode;
                                        // trigger keyup to fetch city/state via postal api and validate blocked
                                        zipInput.dispatchEvent(new Event('keyup'));
                                    }
                                }
                            })
                            .catch(() => {
                                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
                                alert("Could not fetch location data automatically.");
                            });
                    }, function(error) {
                        btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
                        alert("Location permission denied or unavailable. Please enter manually.");
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000
                    });
                } else {
                    alert("Geolocation is not supported by your browser.");
                }
            });
        }
        // Strip leading zero from phone
        const shippingPhone = document.getElementById('shippingPhone');
        if (shippingPhone) {
            shippingPhone.addEventListener('input', function() {
                if (this.value.startsWith('0')) {
                    this.value = this.value.replace(/^0+/, '');
                }
            });
        }
    });
</script>
@endpush
