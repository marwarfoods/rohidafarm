@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            @include('customer.partials.sidebar')

            <!-- Panel Contents -->
            <div class="col-lg-9">

                <!-- Back Button -->
                <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-3 px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back to My Orders
                </a>

                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-3" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger rounded-3 mb-3" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <!-- Order Header -->
                <div class="bg-white p-4 rounded-4 shadow-sm border mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3" style="border-color: var(--border-color) !important;">
                    <div>
                        <h4 class="font-heading fw-bold text-dark m-0">Order #{{ $order->order_number }}</h4>
                        <span class="text-muted" style="font-size:0.85rem;">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if($order->status == 'cancellation_requested')
                            <span class="badge bg-warning text-dark px-3 py-2" style="font-size:0.85rem;">Cancellation Requested</span>
                        @else
                            <span class="badge {{ $order->status_badge_class }} px-3 py-2" style="font-size:0.85rem;">{{ ucfirst($order->status) }}</span>
                        @endif
                        <a href="{{ route('order.receipt', $order->uuid) }}" target="_blank" class="btn btn-sm btn-premium-outline px-3 rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> View Invoice
                        </a>
                        @if($order->tracking_number)
                            <a href="{{ $order->tracking_url ?: 'https://www.delhivery.com/track/package/' . $order->tracking_number }}" target="_blank" class="btn btn-sm btn-info text-white px-3 rounded-pill">
                                <i class="bi bi-truck me-1"></i> Track Shipment
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-8">

                        <!-- Order Items -->
                        <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
                            <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Items Ordered</h5>

                            @foreach($order->items as $item)
                                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->product?->thumbnail)
                                            <img src="{{ asset($item->product->thumbnail) }}" alt="{{ $item->product_name }}" class="rounded-3 border" style="width:55px;height:55px;object-fit:cover;">
                                        @else
                                            <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center" style="width:55px;height:55px;">
                                                <i class="bi bi-image text-muted fs-5"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="fw-bold text-dark m-0" style="font-size:0.95rem;">{{ $item->product_name }}</h6>
                                            <span class="text-muted" style="font-size:0.78rem;">{{ $item->variant_name ?: 'Default' }} &bull; Qty: {{ $item->quantity }} @ ₹{{ number_format($item->price, 2) }}</span>
                                        </div>
                                    </div>
                                    <strong class="text-dark">₹{{ number_format($item->total, 2) }}</strong>
                                </div>
                            @endforeach

                            <!-- Totals -->
                            <div class="pt-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size:0.9rem;">
                                    <span class="text-muted">Subtotal</span>
                                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-1 text-success" style="font-size:0.9rem;">
                                        <span>Discount @if($order->coupon_code)({{ $order->coupon_code }})@endif</span>
                                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between mb-1" style="font-size:0.9rem;">
                                    <span class="text-muted">Tax</span>
                                    <span>₹{{ number_format($order->tax, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="font-size:0.9rem;">
                                    <span class="text-muted">Shipping</span>
                                    <span>{{ $order->shipping_charges > 0 ? '₹' . number_format($order->shipping_charges, 2) : 'FREE' }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="fs-6 fw-bold font-heading text-dark">Grand Total</span>
                                    <strong class="fs-5 fw-bold font-heading text-success">₹{{ number_format($order->total, 2) }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Tracking Timeline -->
                        <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                            <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-4">Shipment Tracking</h5>

                            @if($order->trackingUpdates->count())
                                <div class="tracking-timeline">
                                    @foreach($order->trackingUpdates->sortByDesc('occurred_at') as $track)
                                        <div class="timeline-item completed">
                                            <h6 class="fw-bold m-0 text-dark" style="font-size:0.95rem;">{{ ucwords(str_replace('_', ' ', $track->status)) }}</h6>
                                            <p class="text-muted m-0" style="font-size:0.85rem;">{{ $track->description }}</p>
                                            <span class="text-muted text-uppercase fw-semibold d-block mt-1" style="font-size:0.7rem;">
                                                {{ $track->occurred_at->format('d M Y, H:i A') }}
                                                @if($track->location) &bull; {{ $track->location }} @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-truck display-5 d-block mb-2"></i>
                                    <p class="m-0" style="font-size:0.9rem;">No tracking updates yet. We'll update this as soon as your order ships!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Delivery Address -->
                        <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
                            <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Delivery Address</h5>
                            <div style="font-size:0.88rem; line-height:1.8; color:#555;">
                                <strong class="text-dark d-block">{{ $order->shipping_name ?? $order->user?->name }}</strong>
                                <span>{{ $order->shipping_address_line1 }}</span><br>
                                @if($order->shipping_address_line2)
                                    <span>{{ $order->shipping_address_line2 }}</span><br>
                                @endif
                                <span>{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}</span><br>
                                @if($order->shipping_phone)
                                    <span><i class="bi bi-phone me-1 text-success"></i>{{ $order->shipping_phone }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
                            <h5 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Payment Info</h5>
                            <div style="font-size:0.88rem;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Method</span>
                                    <strong class="text-uppercase">{{ $order->payment_method }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status</span>
                                    @if($order->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($order->payment_status ?? 'N/A') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Cancel Order -->
                        @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled', 'cancellation_requested']))
                            <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                                <h5 class="font-heading fw-bold text-danger border-bottom pb-2 mb-3">Cancel Order</h5>
                                <p class="text-muted" style="font-size:0.85rem;">Changed your mind? You can request a cancellation and our team will process it shortly.</p>
                                <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to request cancellation for this order?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100 rounded-3 py-2 text-uppercase font-heading" style="font-size:0.8rem;">
                                        <i class="bi bi-x-circle me-1"></i> Request Cancellation
                                    </button>
                                </form>
                            </div>
                        @elseif($order->status === 'cancellation_requested')
                            <div class="bg-white p-4 rounded-4 shadow-sm border border-warning" style="border-color: #ffc107 !important;">
                                <p class="text-warning fw-bold m-0" style="font-size:0.88rem;"><i class="bi bi-clock-history me-2"></i>Your cancellation request is being reviewed by our team.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
