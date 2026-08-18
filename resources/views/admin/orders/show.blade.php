@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-2">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-cart text-success me-2"></i>Order #{{ $order->order_number }}</h1>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        {{-- Push to Delhivery button --}}
        @if(!in_array($order->status, ['cancelled', 'delivered']))
            @if(auth()->user()->hasPermission('orders-edit'))
                <form action="{{ route('admin.orders.sync-delhivery', $order->id) }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>Sending...'">
                    @csrf
                    <button type="submit" class="btn px-4 py-2 rounded-pill text-uppercase font-heading text-white fw-bold" style="font-size: 0.8rem; background: linear-gradient(135deg, #7c3aed, #4f46e5);">
                        <i class="bi bi-send-fill me-1"></i> Push to Delhivery
                    </button>
                </form>
            @endif
        @endif
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.8rem;"><i class="bi-arrow-left me-1"></i> Back to List</a>
    </div>
</div>

<div class="row g-4">
    <!-- Order items and details -->
    <div class="col-lg-8">
        <!-- Items list -->
        @if($order->status == 'cancellation_requested')
            <div class="alert alert-warning mb-4 rounded-4 shadow-sm border-warning">
                <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Cancellation Requested</h5>
                <p class="mb-0">The customer has requested to cancel this order. Please process the cancellation or update the status accordingly.</p>
                @if(auth()->user()->hasPermission('orders-edit'))
                    <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="reason" value="Requested by customer">
                        <button type="submit" class="btn btn-danger btn-sm px-3 rounded-pill">Approve Cancellation</button>
                    </form>
                @endif
            </div>
        @endif

        <!-- Order Summary (table) -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Order Summary</h4>
            <div class="table-responsive">
                <table class="table table-borderless align-middle m-0" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 220px;">Order Number</td>
                            <td class="fw-bold text-dark">#{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Order Date</td>
                            <td class="text-dark">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Order Status</td>
                            <td><span class="badge {{ $order->status_badge_class }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Payment Method</td>
                            <td class="text-uppercase text-dark">{{ $order->payment_method ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Payment Status</td>
                            <td>
                                <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success text-white' : ($order->payment_status === 'failed' ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($order->payment_status ?: 'pending') }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Order Items</h4>

            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="fw-bold text-dark">{{ $item->product_name }}</td>
                                <td class="text-muted">{{ $item->variant_name ?: 'Default' }}</td>
                                <td class="text-center text-muted">{{ $item->quantity }}</td>
                                <td class="text-end text-muted">₹{{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-bold text-dark">₹{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Calculations -->
            <div class="pt-3">
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.9rem;">
                    <span class="text-muted">Subtotal</span>
                    <span class="text-dark">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-1 text-success" style="font-size: 0.9rem;">
                        <span>Discount ({{ $order->coupon_code }})</span>
                        <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-1" style="font-size: 0.9rem;">
                    <span class="text-muted">GST Tax (5%)</span>
                    <span class="text-dark">₹{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom" style="font-size: 0.9rem;">
                    <span class="text-muted">Shipping Charges</span>
                    <span class="text-dark">{{ $order->shipping_charges > 0 ? '₹' . number_format($order->shipping_charges, 2) : 'FREE' }}</span>
                </div>
                <div class="d-flex justify-content-between pt-1">
                    <span class="fs-5 fw-bold font-heading text-dark">Grand Total</span>
                    <strong class="fs-4 fw-bold font-heading text-success">₹{{ number_format($order->total, 2) }}</strong>
                </div>
                @if($order->payment_method === 'cod' && $order->advance_amount > 0)
                    <div class="d-flex justify-content-between pt-2 mt-2 border-top">
                        <span class="text-dark fw-bold" style="font-size: 0.9rem;">Advance Paid (Online)</span>
                        <strong class="text-success fw-bold" style="font-size: 0.9rem;">₹{{ number_format($order->advance_amount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pt-1">
                        <span class="text-dark fw-bold" style="font-size: 0.9rem;">Amount Due on Delivery</span>
                        <strong class="text-danger fw-bold" style="font-size: 0.9rem;">₹{{ number_format($order->cod_due_amount, 2) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Details (from payments transactions table, if any) --}}
        @if($order->payments && $order->payments->count())
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Payment Details</h4>
            <div class="table-responsive">
                <table class="table align-middle" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Method</th>
                            <th>Transaction ID</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->payments as $payment)
                            <tr>
                                <td class="text-uppercase text-dark">{{ $payment->payment_method }}</td>
                                <td class="text-muted">{{ $payment->transaction_id ?: '—' }}</td>
                                <td class="text-end fw-bold text-dark">₹{{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $payment->status === 'success' || $payment->status === 'paid' ? 'bg-success text-white' : ($payment->status === 'failed' ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="text-muted">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Tracking Timeline status logs -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-4">Shipment Tracking History</h4>
            
            <div class="tracking-timeline">
                @foreach($order->trackingUpdates as $track)
                    <div class="timeline-item completed">
                        <h6 class="fw-bold m-0 text-dark" style="font-size: 0.95rem;">{{ ucwords(str_replace('_', ' ', $track->status)) }}</h6>
                        <p class="text-muted m-0" style="font-size: 0.85rem;">{{ $track->description }}</p>
                        <span class="text-muted text-uppercase fw-semibold d-block mt-1" style="font-size: 0.7rem;">{{ $track->occurred_at->format('d M Y, H:i A') }} @if($track->location) [{{ $track->location }}] @endif</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Management Sidebar panel -->
    <div class="col-lg-4">
        <!-- Update status Form -->
        @if(auth()->user()->hasPermission('orders-edit'))
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Update Order Status</h4>
            
            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Select Order Status</label>
                    <select name="status" class="form-select bg-light border p-2" required>
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancellation_requested" {{ $order->status === 'cancellation_requested' ? 'selected' : '' }}>Cancellation Requested</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing / Packed</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped / Dispatched</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Tracking Number / AWB Code</label>
                    <input type="text" name="tracking_number" class="form-control bg-light border p-2" value="{{ $order->tracking_number }}" placeholder="e.g. AWB1234567890">
                    <small class="text-muted">Customer will use this to track their shipment.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Courier / Carrier Name</label>
                    <input type="text" name="tracking_carrier" class="form-control bg-light border p-2" value="{{ $order->tracking_carrier }}" placeholder="e.g. Delhivery">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Tracking Comment / Location</label>
                    <input type="text" name="description" class="form-control bg-light border p-2" placeholder="e.g. Dispatched from Surat logistics center">
                </div>
                <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Update Status</button>
            </form>
        </div>
        @endif

        {{-- Delhivery Sync Status Card --}}
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4" style="background: linear-gradient(135deg, #f5f3ff, #ede9fe) !important; border: 1px solid #c4b5fd !important;">
            <h5 class="font-heading fw-bold mb-3" style="color: #4f46e5;"><i class="bi bi-truck me-2"></i>Delhivery Sync</h5>

            @if($order->shipment)
                <div class="mb-2" style="font-size: 0.82rem;">
                    <span class="badge bg-success mb-2"><i class="bi bi-check-circle me-1"></i>Synced to Delhivery</span>
                    <div class="text-muted">Order ID: <strong class="text-dark">{{ $order->shipment->delhivery_order_id }}</strong></div>
                    <div class="text-muted">Shipment ID: <strong class="text-dark">{{ $order->shipment->delhivery_shipment_id }}</strong></div>
                    <div class="text-muted">AWB (Waybill): <strong class="text-dark">{{ $order->shipment->awb_code ?: 'Not assigned yet' }}</strong></div>
                    <div class="text-muted">Courier: <strong class="text-dark">{{ $order->shipment->courier_name }}</strong></div>
                    <div class="text-muted">Status: <strong class="text-dark">{{ $order->shipment->status }}</strong></div>
                </div>
                @if($order->tracking_number)
                    <a href="https://www.delhivery.com/track/package/{{ $order->tracking_number }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-1" style="font-size: 0.78rem;">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Track on Delhivery
                    </a>
                @endif
                {{-- Re-sync button --}}
                @if(!in_array($order->status, ['cancelled', 'delivered']))
                    @if(auth()->user()->hasPermission('orders-edit'))
                        <form action="{{ route('admin.orders.sync-delhivery', $order->id) }}" method="POST" class="mt-2" onsubmit="this.querySelector('button').disabled=true;">
                            @csrf
                            <button type="submit" class="btn btn-sm rounded-pill px-3 text-white" style="font-size: 0.78rem; background:#7c3aed;">
                                <i class="bi bi-arrow-repeat me-1"></i>Re-sync to Delhivery
                            </button>
                        </form>
                    @endif
                @endif
            @else
                <p class="text-muted mb-3" style="font-size: 0.85rem;">This order has not been pushed to Delhivery yet.</p>
                @if(!in_array($order->status, ['cancelled', 'delivered']))
                    @if(auth()->user()->hasPermission('orders-edit'))
                        <form action="{{ route('admin.orders.sync-delhivery', $order->id) }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<span class=\'spinner-border spinner-border-sm me-1\'></span>Sending...'">
                            @csrf
                            <button type="submit" class="btn w-100 rounded-3 py-2 text-white fw-bold text-uppercase font-heading" style="font-size: 0.8rem; background: linear-gradient(135deg, #7c3aed, #4f46e5);">
                                <i class="bi bi-send-fill me-1"></i> Push Order to Delhivery
                            </button>
                        </form>
                    @endif
                @else
                    <span class="badge bg-secondary">Cannot sync cancelled/delivered orders</span>
                @endif
            @endif
        </div>

        <!-- Shipping and Customer Details -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Customer Details</h4>

            <div class="table-responsive">
                <table class="table table-borderless align-middle m-0" style="font-size: 0.85rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 110px;">Name</td>
                            <td class="fw-bold text-dark">{{ $order->user->name ?? $order->shipping_name ?? 'Guest Customer' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td class="text-dark">{{ $order->user->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone</td>
                            <td class="text-dark">{{ $order->shipping_phone ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted align-top">Shipping Address</td>
                            <td class="text-dark">
                                {{ $order->shipping_name }}<br>
                                {{ $order->shipping_address_line1 }}{{ $order->shipping_address_line2 ? ', ' . $order->shipping_address_line2 : '' }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_postal_code }}<br>
                                {{ $order->shipping_country }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
