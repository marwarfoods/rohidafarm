@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container" style="max-width: 800px;">
        <!-- Success Banner -->
        <div class="bg-white p-5 rounded-4 shadow-sm border text-center mb-4" style="border-color: var(--border-color) !important;">
            <div class="display-3 text-success mb-3"><i class="bi bi-patch-check-fill"></i></div>
            <h2 class="font-heading fw-bold text-dark mb-2">Order Confirmed!</h2>
            <p class="text-muted mb-4">Thank you for your order. We are preparing to dispatch your organic ghee using the traditional Bilona method.</p>
            
            <div class="row g-3 justify-content-center">
                <div class="col-6 col-sm-4 text-center">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Order Number</span>
                    <strong class="text-dark fs-5 font-heading">#{{ $order->order_number }}</strong>
                </div>
                <div class="col-6 col-sm-4 text-center">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Total Paid</span>
                    <strong class="text-success fs-5 font-heading">₹{{ number_format($order->total, 2) }}</strong>
                </div>
                <div class="col-12 col-sm-4 text-center mt-3 mt-sm-0">
                    <span class="text-muted d-block" style="font-size: 0.8rem;">Payment Method</span>
                    <strong class="text-dark fs-5 font-heading text-uppercase">{{ $order->payment_method }}</strong>
                </div>
            </div>
        </div>

        <!-- Tracking Timeline Box -->
        <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-4"><i class="bi-geo-alt text-success me-2"></i>Estimated Delivery</h4>
            
            <div class="tracking-timeline">
                <div class="timeline-item completed">
                    <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">Order Placed Successfully</h6>
                    <p class="text-muted m-0" style="font-size: 0.85rem;">Your order #{{ $order->order_number }} has been processed in our warehouse system.</p>
                    <span class="text-muted text-uppercase fw-semibold d-block mt-1" style="font-size: 0.7rem;">{{ $order->created_at->format('d M Y, H:i A') }}</span>
                </div>

                <div class="timeline-item">
                    <h6 class="fw-bold m-0 text-muted" style="font-size: 0.95rem;">Packed & Dispatched</h6>
                    <p class="text-muted m-0" style="font-size: 0.85rem;">Package seal verification and manifest creation via Delhivery/BlueDart shipping hubs.</p>
                    <span class="text-muted d-block mt-1" style="font-size: 0.7rem;">Pending warehouse verification</span>
                </div>

                <div class="timeline-item">
                    <h6 class="fw-bold m-0 text-muted" style="font-size: 0.95rem;">Estimated Delivery Date</h6>
                    <p class="text-muted m-0" style="font-size: 0.85rem;">Your package is expected to arrive at your shipping address by <strong>{{ $order->estimated_delivery ? $order->estimated_delivery->format('d M, Y') : now()->addDays(5)->format('d M, Y') }}</strong>.</p>
                </div>
            </div>

            @if($order->tracking_number)
                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between mt-3 border">
                    <div>
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Delhivery Waybill AWB</span>
                        <strong class="text-dark" style="font-size: 0.85rem;">{{ $order->tracking_number }} ({{ $order->tracking_carrier }})</strong>
                    </div>
                    <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-sm btn-premium px-3 py-1 rounded-pill text-uppercase font-heading" style="font-size: 0.7rem;"><i class="bi bi-box-seam me-1"></i> Track Live</a>
                </div>
            @endif
        </div>

        <!-- Order Items Detail list -->
        <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Invoice Details</h4>
            
            @foreach($order->items as $item)
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                    <div>
                        <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">{{ $item->product_name }}</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">Qty: {{ $item->quantity }} @if($item->variant_name) | {{ $item->variant_name }} @endif</span>
                    </div>
                    <strong class="text-dark" style="font-size: 0.9rem;">₹{{ number_format($item->total, 2) }}</strong>
                </div>
            @endforeach

            <!-- Summary Calculations -->
            <div class="pt-3">
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                    <span class="text-muted">Subtotal</span>
                    <span class="text-dark">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-1 text-success" style="font-size: 0.85rem;">
                        <span>Coupon Discount ({{ $order->coupon_code }})</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                    <span class="text-muted">GST Tax (5%)</span>
                    <span class="text-dark">₹{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="font-size: 0.85rem;">
                    <span class="text-muted">Shipping Charges</span>
                    <span class="text-dark">{{ $order->shipping_charges > 0 ? '₹' . number_format($order->shipping_charges, 2) : 'FREE' }}</span>
                </div>
                <div class="d-flex justify-content-between pt-1">
                    <span class="fs-5 fw-bold font-heading text-dark">Grand Total</span>
                    <strong class="fs-4 fw-bold font-heading text-success">₹{{ number_format($order->total, 2) }}</strong>
                </div>
                
                @if($order->payment_method === 'cod' && $order->advance_amount > 0)
                    <div class="d-flex justify-content-between pt-2 mt-2 border-top">
                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">Advance Paid (Online)</span>
                        <strong class="fw-bold text-success" style="font-size: 0.9rem;">₹{{ number_format($order->advance_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pt-1">
                        <span class="fw-bold text-dark" style="font-size: 0.9rem;">Amount Due on Delivery</span>
                        <strong class="fw-bold text-danger" style="font-size: 0.9rem;">₹{{ number_format($order->cod_due_amount, 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="text-center d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('shop.index') }}" class="btn btn-premium-outline px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-arrow-left me-2"></i> Continue Shopping</a>
            <a href="{{ route('order.receipt', $order->uuid) }}" target="_blank" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.85rem; letter-spacing: 0.5px;"><i class="bi bi-download me-2"></i> Download Invoice Receipt</a>
        </div>
    </div>
</section>
@endsection
