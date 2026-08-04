@extends('layouts.app')

@section('content')
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <h1 class="display-6 font-heading fw-bold m-0 text-dark">Shopping Cart</h1>
    </div>
</section>

<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container">
        @if($items->isEmpty())
            <div class="bg-white p-5 rounded-4 shadow-sm border text-center my-4 mx-auto" style="border-color: #ECE7DD !important; max-width: 600px;">
                <div class="mb-4">
                    <img src="{{ asset('images/emtycart.png') }}" alt="Empty Cart" class="img-fluid animate-fade-in" style="max-width: 200px; opacity: 0.9;">
                </div>
                <h3 class="font-heading fw-bold mt-2 text-dark">Your Cart is Empty</h3>
                <p class="text-muted mb-4 fs-6 px-md-4">Discover our pure traditional organic ghee, oils, and honey catalog. Handcrafted for your health.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.9rem; letter-spacing: 0.5px;">Explore Products</a>
            </div>
        @else
            <div class="row g-4">
                <!-- Cart Items List -->
                <div class="col-lg-8">
                    <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: #ECE7DD !important;">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr style="font-family: 'DM Sans', sans-serif; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;" class="text-muted border-bottom">
                                        <th scope="col" class="border-0 pb-3">Product</th>
                                        <th scope="col" class="border-0 pb-3">Price</th>
                                        <th scope="col" class="border-0 pb-3">Quantity</th>
                                        <th scope="col" class="border-0 text-end pb-3">Total</th>
                                        <th scope="col" class="border-0 text-center pb-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        @php
                                            $price = $item->variant ? $item->variant->sale_price : $item->product->sale_price;
                                            $weight = $item->variant ? $item->variant->name : $item->product->weight;
                                            $image = $item->product->primaryImage ? asset($item->product->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg');
                                        @endphp
                                        <tr id="cartRow_{{ $item->cart_id }}">
                                            <td class="py-3 border-bottom" style="min-width: 250px;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $image }}" class="rounded-3 border" style="width: 70px; height: 70px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="fw-bold font-heading text-dark m-0 fs-6">{{ $item->product->name }}</h6>
                                                        <span class="text-muted" style="font-size: 0.8rem;"><i class="bi bi-tag text-success me-1"></i>Weight: {{ $weight }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3 border-bottom fw-bold" style="font-size: 0.95rem;">
                                                ₹{{ number_format($price, 2) }}
                                            </td>
                                            <td class="py-4 border-bottom">
                                                <div class="quantity-input border rounded-pill d-inline-flex align-items-center bg-white shadow-sm" style="padding: 4px 12px; border-color: #e8e4dd !important;">
                                                    <button type="button" class="btn border-0 p-0 bg-transparent text-dark qty-dec" data-id="{{ $item->cart_id }}"><i class="bi-dash fs-5"></i></button>
                                                    <input type="number" class="form-control border-0 bg-transparent text-center fw-bold shadow-none p-0 qty-val" data-id="{{ $item->cart_id }}" value="{{ $item->quantity }}" min="1" max="10" style="width: 40px; font-size: 1rem;" readonly>
                                                    <button type="button" class="btn border-0 p-0 bg-transparent text-dark qty-inc" data-id="{{ $item->cart_id }}"><i class="bi-plus fs-5"></i></button>
                                                </div>
                                            </td>
                                            <td class="py-4 border-bottom text-end fw-bold font-heading text-dark" id="rowTotal_{{ $item->cart_id }}" style="font-size: 1.1rem;">
                                                ₹{{ number_format($price * $item->quantity, 2) }}
                                            </td>
                                            <td class="py-4 border-bottom text-center">
                                                <button class="btn btn-light btn-sm rounded-circle btn-cart-remove shadow-sm text-danger" style="width:32px; height:32px; display:inline-flex; align-items:center; justify-content:center; border: 1px solid #ECE7DD;" data-id="{{ $item->cart_id }}" aria-label="Remove item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: #ECE7DD !important;">
                        <h5 class="font-heading fw-bold text-dark border-bottom pb-3 mb-4">Order Summary</h5>
                        
                        <div class="d-flex justify-content-between mb-3 text-muted" style="font-size: 0.95rem;">
                            <span>Subtotal</span>
                            <span class="fw-bold text-dark" id="summarySubtotal">₹{{ number_format($totals['subtotal'], 2) }}</span>
                        </div>

                        <!-- Coupon Code Discount -->
                        <div class="d-flex justify-content-between mb-3 text-success" id="discountBlock" style="{{ $totals['discount'] > 0 ? '' : 'display:none !important;' }}; font-size: 0.95rem;">
                            <span>Discount ({{ $totals['coupon']?->code }})</span>
                            <span class="fw-bold" id="summaryDiscount">-₹{{ number_format($totals['discount'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-muted" style="font-size: 0.95rem;">
                            <span>Taxes (5% GST)</span>
                            <span class="fw-bold text-dark" id="summaryTax">₹{{ number_format($totals['tax'], 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 border-bottom pb-4 text-muted" style="font-size: 0.95rem;">
                            <span>Delivery Charges</span>
                            <span class="fw-bold text-dark" id="summaryShipping">
                                {{$totals['shipping'] > 0 ? '₹' . number_format($totals['shipping'], 2) : 'FREE'}}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 align-items-center">
                            <span class="fs-5 fw-bold text-dark font-heading">Total</span>
                            <span class="fs-3 fw-bold text-success font-heading" id="summaryTotal">₹{{ number_format($totals['total'], 2) }}</span>
                        </div>

                        <!-- Checkout CTA -->
                        <a href="{{ route('checkout.index') }}" class="btn btn-premium w-100 py-3 rounded-pill text-uppercase font-heading d-flex justify-content-between align-items-center px-4" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                            <span>Checkout Now</span>
                            <i class="bi bi-arrow-right fs-5"></i>
                        </a>
                    </div>

                    <!-- Coupon Apply Card -->
                    <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: #ECE7DD !important;">
                        <h6 class="fw-bold text-dark text-uppercase font-heading mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Have a Promo Coupon?</h6>
                        
                        <!-- Toggle Form / Applied view -->
                        <div class="input-group mb-0" id="couponApplyGroup" style="{{ $totals['coupon'] ? 'display:none !important;' : '' }}">
                            <input type="text" id="couponInput" class="form-control border bg-light shadow-none" placeholder="Enter Code (e.g. PURE15)" style="font-size: 0.9rem; padding: 10px 15px;">
                            <button type="button" id="btnApplyCoupon" class="btn btn-dark font-heading px-4 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Apply</button>
                        </div>

                        <div class="bg-success-subtle border border-success-subtle p-3 rounded-3 d-flex justify-content-between align-items-center" id="couponAppliedGroup" style="{{ $totals['coupon'] ? '' : 'display:none !important;' }}">
                            <div>
                                <span class="fw-bold text-success d-block" style="font-size: 0.85rem;" id="appliedCouponCode">Code: {{ $totals['coupon']?->code }}</span>
                                <span class="text-muted" style="font-size: 0.75rem;">Discount successfully applied!</span>
                            </div>
                            <button type="button" id="btnRemoveCoupon" class="btn btn-sm btn-outline-danger px-2 border-0"><i class="bi bi-x-circle-fill"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // Helper to update summary tags
        function updateSummary(totals) {
            document.getElementById('summarySubtotal').textContent = '₹' + parseFloat(totals.subtotal).toFixed(2);
            document.getElementById('summaryTax').textContent = '₹' + parseFloat(totals.tax).toFixed(2);
            document.getElementById('summaryTotal').textContent = '₹' + parseFloat(totals.total).toFixed(2);

            const shipping = parseFloat(totals.shipping);
            document.getElementById('summaryShipping').textContent = shipping > 0 ? '₹' + shipping.toFixed(2) : 'FREE';

            const discount = parseFloat(totals.discount);
            const discountBlock = document.getElementById('discountBlock');
            if (discount > 0) {
                discountBlock.style.setProperty('display', 'flex', 'important');
                document.getElementById('summaryDiscount').textContent = '-₹' + discount.toFixed(2);
            } else {
                discountBlock.style.setProperty('display', 'none', 'important');
            }

            const cartCountBadge = document.querySelector('.navbar .badge');
            if (cartCountBadge) {
                if (totals.items_count > 0) {
                    cartCountBadge.textContent = totals.items_count;
                } else {
                    cartCountBadge.remove();
                }
            }
        }

        // Add AJAX callbacks for Cart items increment / decrement
        const quantityInputs = document.querySelectorAll('.qty-val');
        
        document.querySelectorAll('.qty-inc').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const inp = document.querySelector(`.qty-val[data-id="${id}"]`);
                let val = parseInt(inp.value) || 1;
                if (val < 10) {
                    val++;
                    inp.value = val;
                    updateCartQty(id, val);
                }
            });
        });

        document.querySelectorAll('.qty-dec').forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const inp = document.querySelector(`.qty-val[data-id="${id}"]`);
                let val = parseInt(inp.value) || 1;
                if (val > 1) {
                    val--;
                    inp.value = val;
                    updateCartQty(id, val);
                }
            });
        });

        function updateCartQty(id, qty) {
            fetch(`/cart/update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ quantity: qty })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update item row total
                    const items = @json($items);
                    const item = items.find(i => i.id == id);
                    const price = item && item.variant ? parseFloat(item.variant.sale_price) : parseFloat(item.product.sale_price);
                    
                    document.getElementById(`rowTotal_${id}`).textContent = '₹' + (price * qty).toFixed(2);
                    updateSummary(data.totals);
                    
                    // Sync with offcanvas and badge globally
                    if(typeof window.reloadCartDrawer === 'function') {
                        window.reloadCartDrawer();
                    }
                } else {
                    alert(data.message);
                }
            });
        }

        // Cart Item delete
        document.querySelectorAll('.btn-cart-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const id    = this.getAttribute('data-id');
                const btnEl = this;
                btnEl.innerHTML = '<span class="spinner-border spinner-border-sm text-danger" style="width:14px;height:14px;" role="status"></span>';
                btnEl.style.pointerEvents = 'none';

                fetch(`/cart/remove/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = document.getElementById(`cartRow_${id}`);
                        if (row) row.remove();
                        
                        updateSummary(data.totals);
                        
                        // Show empty cart message if empty
                        if (data.totals.items_count === 0) {
                            const cartBody = document.querySelector('.card-body');
                            if(cartBody) {
                                cartBody.innerHTML = `
                                    <div class="text-center py-5">
                                        <img src="{{ asset('images/emtycart.png') }}" alt="Empty Cart" class="mb-4" style="max-width: 200px;">
                                        <h3 class="font-heading fw-bold">Your Cart is Empty</h3>
                                        <p class="text-muted mb-4">Looks like you haven't added anything to your cart yet.</p>
                                        <a href="{{ route('shop.index') }}" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading shadow-sm">Start Shopping</a>
                                    </div>
                                `;
                            }
                        }
                        
                        // Sync with offcanvas and badge globally
                        if(typeof window.reloadCartDrawer === 'function') {
                            window.reloadCartDrawer();
                        }
                    } else {
                        btnEl.innerHTML = '<i class="bi bi-trash"></i>';
                        btnEl.style.pointerEvents = 'auto';
                        alert(data.message || 'Could not remove item.');
                    }
                })
                .catch(() => {
                    btnEl.innerHTML = '<i class="bi bi-trash"></i>';
                    btnEl.style.pointerEvents = '';
                });
            });
        });

        // Apply Coupon Event
        const btnApply = document.getElementById('btnApplyCoupon');
        if (btnApply) {
            btnApply.addEventListener('click', function () {
                const code = document.getElementById('couponInput').value;
                if (!code) return;

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
                        // Switch views
                        document.getElementById('couponApplyGroup').style.setProperty('display', 'none', 'important');
                        document.getElementById('couponAppliedGroup').style.setProperty('display', 'flex', 'important');
                        document.getElementById('appliedCouponCode').textContent = 'Code: ' + code.toUpperCase();
                        
                        updateSummary(data.totals);
                    } else {
                        alert(data.message);
                    }
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
    });
</script>
@endpush
