@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar Navigation -->
            @include('customer.partials.sidebar')

            <!-- Panel Contents -->
            <div class="col-lg-9">
                <div class="bg-white p-4 rounded-4 shadow-sm border" style="border-color: var(--border-color) !important;">
                    <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-4">My Order History</h4>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Payment Mode</th>
                                    <th scope="col">Order Status</th>
                                    <th scope="col" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $ord)
                                    <tr style="font-size: 0.85rem;">
                                        <td class="fw-bold">#{{ $ord->order_number }}</td>
                                        <td>{{ $ord->created_at->format('d M, Y') }}</td>
                                        <td class="fw-bold">₹{{ number_format($ord->total, 2) }}</td>
                                        <td class="text-uppercase">{{ $ord->payment_method }}</td>
                                        <td>
                                            @if($ord->status == 'cancellation_requested')
                                                <span class="badge bg-warning text-dark">Cancel Requested</span>
                                            @else
                                                <span class="badge {{ $ord->status_badge_class }}">{{ ucfirst($ord->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex flex-column gap-2 align-items-end">
                                                <a href="{{ route('customer.orders.show', $ord->id) }}" class="btn btn-sm btn-success px-3 py-1 rounded-3"><i class="bi bi-eye me-1"></i>Details</a>
                                                <a href="{{ route('order.receipt', $ord->uuid) }}" target="_blank" class="btn btn-sm btn-premium-outline px-3 py-1 rounded-3">Invoice</a>
                                                
                                                @if($ord->tracking_number)
                                                    <a href="{{ $ord->tracking_url ?: 'https://www.delhivery.com/track/package/' . $ord->tracking_number }}" target="_blank" class="btn btn-sm btn-info text-white px-3 py-1 rounded-3"><i class="bi bi-truck me-1"></i> Track</a>
                                                @endif
                                                
                                                @if(!in_array($ord->status, ['shipped', 'delivered', 'cancelled', 'cancellation_requested']))
                                                    <form action="{{ route('customer.orders.cancel', $ord->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to request cancellation for this order?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-danger px-3 py-1 rounded-3">Request Cancel</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">You have not placed any orders yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
