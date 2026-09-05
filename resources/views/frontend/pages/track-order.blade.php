@extends('layouts.app')

@section('content')
{{-- Page Hero --}}
<section class="py-4 text-center position-relative overflow-hidden"
    style="background: linear-gradient(135deg, #f0f9f4 0%, #ffffff 60%, #f7fdf9 100%); border-bottom: 1px solid #e8f5e9;">
    <div class="container py-3" data-aos="fade-up">
        <span class="text-uppercase fw-bold text-success" style="font-size:0.75rem;letter-spacing:2.5px;">
            <i class="bi bi-truck me-1"></i>Shipment Tracking
        </span>
        <h1 class="display-5 font-heading fw-bold text-dark mt-2 mb-0">Track Your Order</h1>
    </div>
</section>

{{-- Track Order Form & Results --}}
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-xl-6">

                {{-- Flash messages --}}
                @if(session('error'))
                    <div class="alert alert-danger rounded-3 border-0 mb-4 d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Search Form --}}
                <div class="card border-0 rounded-4 shadow-sm mb-4" data-aos="fade-up">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold text-dark mb-1"><i class="bi bi-search me-2 text-success"></i>Enter Order Number</h5>
                        <p class="text-muted mb-4" style="font-size:0.88rem;">Your order number starts with <strong>#RF</strong> and was sent to your email after placing the order.</p>

                        <form action="{{ route('track-order') }}" method="GET" class="d-flex gap-2">
                            <input type="text"
                                   name="order_number"
                                   id="order_number"
                                   class="form-control rounded-pill px-4 py-3"
                                   placeholder="e.g. RF2024072001"
                                   value="{{ request('order_number') }}"
                                   required
                                   style="font-size:0.95rem;">
                            <button type="submit" class="btn btn-success px-4 py-3 rounded-pill fw-semibold flex-shrink-0">
                                <i class="bi bi-search me-1 d-none d-sm-inline"></i>Track
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Order Found — Details --}}
                @if($order)
                    <div class="card border-0 rounded-4 shadow-sm mb-4" data-aos="fade-up">
                        <div class="card-header d-flex justify-content-between align-items-center p-4">
                            <div>
                                <h5 class="fw-bold text-dark m-0"><i class="bi bi-receipt me-2 text-success"></i>Order #{{ $order->order_number }}</h5>
                                <small class="text-muted">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</small>
                            </div>
                            @php
                                $statusColors = [
                                    'pending'    => 'warning',
                                    'confirmed'  => 'info',
                                    'processing' => 'primary',
                                    'shipped'    => 'success',
                                    'delivered'  => 'success',
                                    'cancelled'  => 'danger',
                                    'returned'   => 'secondary',
                                ];
                                $statusColor = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $statusColor }} px-3 py-2 text-capitalize" style="font-size:0.82rem;">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="card-body p-4">

                            {{-- Order Summary --}}
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3 text-center">
                                    <div class="fw-bold text-dark" style="font-size:1.1rem;">₹{{ number_format($order->total_amount, 2) }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Order Total</div>
                                </div>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="fw-bold text-dark">{{ $order->items->count() }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Items</div>
                                </div>
                                <div class="col-6 col-md-3 text-center">
                                    <div class="fw-bold text-dark text-capitalize">{{ $order->payment_method ?? 'COD' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Payment</div>
                                </div>
                                <div class="col-6 col-md-3 text-center">
                                    @if($order->shipment && $order->shipment->awb_code)
                                        <div class="fw-bold text-success" style="font-size:0.9rem;">{{ $order->shipment->awb_code }}</div>
                                    @else
                                        <div class="text-muted fw-semibold">—</div>
                                    @endif
                                    <div class="text-muted" style="font-size:0.75rem;">AWB Code</div>
                                </div>
                            </div>

                            {{-- Shipping Address --}}
                            @if($order->shipping_address)
                                @php $addr = is_string($order->shipping_address) ? json_decode($order->shipping_address, true) : (array)$order->shipping_address; @endphp
                                <div class="rounded-3 p-3 mb-4" style="background:#f8fdf9;border:1px solid #e8f5e9;">
                                    <div class="fw-semibold text-success mb-1" style="font-size:0.8rem;letter-spacing:1px;text-transform:uppercase;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>Shipping To
                                    </div>
                                    <div class="text-dark" style="font-size:0.88rem;line-height:1.6;">
                                        {{ $addr['name'] ?? '' }}<br>
                                        {{ $addr['address_line1'] ?? $addr['address'] ?? '' }}<br>
                                        @if(!empty($addr['city'])){{ $addr['city'] }}, @endif
                                        @if(!empty($addr['state'])){{ $addr['state'] }}@endif
                                        @if(!empty($addr['pincode'])) — {{ $addr['pincode'] }}@endif
                                    </div>
                                </div>
                            @endif

                            {{-- Tracking Timeline --}}
                            @if($timeline && count($timeline) > 0)
                                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Tracking Timeline</h6>
                                <div class="position-relative" style="padding-left: 28px;">
                                    <div class="position-absolute start-0 top-0 bottom-0" style="width:2px;background:#e8f5e9;left:10px;"></div>
                                    @foreach($timeline as $event)
                                        <div class="position-relative mb-3 pb-3" style="border-bottom:1px dashed #e8f5e9;">
                                            <div class="position-absolute rounded-circle bg-success" style="width:12px;height:12px;left:-24px;top:4px;border:2px solid #fff;box-shadow:0 0 0 2px #a8d5a2;"></div>
                                            <div class="fw-semibold text-dark" style="font-size:0.88rem;">{{ $event['activity'] ?? $event['status'] ?? 'Status Update' }}</div>
                                            <div class="text-muted" style="font-size:0.78rem;">
                                                @if(!empty($event['date'])){{ $event['date'] }}@endif
                                                @if(!empty($event['location'])) — {{ $event['location'] }}@endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($order->status !== 'pending')
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-hourglass-split fs-4 d-block mb-2 opacity-50"></i>
                                    <span style="font-size:0.88rem;">Detailed tracking updates will appear here once your shipment is dispatched.</span>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- Contact support --}}
                    <div class="text-center mt-2 mb-4">
                        <p class="text-muted mb-2" style="font-size:0.85rem;">Need help with your order?</p>
                        <a href="{{ route('contact') }}" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-headset me-2"></i>Contact Support
                        </a>
                    </div>
                @endif

                {{-- Help Tips --}}
                @if(!$order)
                    <div class="card border-0 rounded-4 bg-light mt-4" data-aos="fade-up">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>How to find your Order Number?</h6>
                            <ul class="list-unstyled d-flex flex-column gap-2 mb-0" style="font-size:0.88rem;color:#555;">
                                <li><i class="bi bi-envelope-check text-success me-2"></i>Check your email inbox for a confirmation email from RohidaFarm.</li>
                                <li><i class="bi bi-person-circle text-success me-2"></i>Log in to your account and visit <a href="{{ route('customer.orders') }}" class="text-success text-decoration-none fw-semibold">My Orders</a>.</li>
                                <li><i class="bi bi-telephone text-success me-2"></i>Call or WhatsApp us — we'll help you locate your order.</li>
                            </ul>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>
@endsection
