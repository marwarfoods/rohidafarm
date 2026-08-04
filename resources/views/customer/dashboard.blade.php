@extends('layouts.app')

@section('content')
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container">
        <!-- Success Alerts -->
        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row g-4">
            <!-- Sidebar Navigation -->
            @include('customer.partials.sidebar')

            <!-- Panel Contents -->
            <div class="col-lg-9">
                <!-- Welcome & Wallet Banner -->
                <div class="bg-white p-4 rounded-4 shadow-sm border mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3" style="border-color: var(--border-color) !important;">
                    <div>
                        <h3 class="font-heading fw-bold text-dark m-0">Namaste, {{ $user->name }}!</h3>
                        <p class="text-muted m-0" style="font-size: 0.9rem;">Manage your orders, addresses, and account details here.</p>
                    </div>
                </div>

                <!-- Recent Orders Grid -->
                <div class="bg-white p-4 rounded-4 shadow-sm border mb-4" style="border-color: var(--border-color) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="font-heading fw-bold text-dark m-0">Recent Orders</h4>
                        <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-link text-success fw-bold text-decoration-none">View All</a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th scope="col">Order Number</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Payment</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $ord)
                                    <tr style="font-size: 0.85rem;">
                                        <td class="fw-bold">#{{ $ord->order_number }}</td>
                                        <td>{{ $ord->created_at->format('d M, Y') }}</td>
                                        <td class="fw-bold">₹{{ number_format($ord->total, 2) }}</td>
                                        <td class="text-uppercase">{{ $ord->payment_method }}</td>
                                        <td><span class="badge {{ $ord->status_badge_class }}">{{ ucfirst($ord->status) }}</span></td>
                                        <td class="text-end">
                                            <a href="{{ route('order.receipt', $ord->uuid) }}" target="_blank" class="btn btn-sm btn-premium-outline px-3 py-1 rounded-3">View Invoice</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No recent orders placed.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Saved Address Grid & Wallet Transactions -->
                <div class="row g-4">
                    <!-- Short Address Summary -->
                    <div class="col-md-12">
                        <div class="bg-white p-4 rounded-4 shadow-sm border h-100" style="border-color: var(--border-color) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="font-heading fw-bold text-dark m-0">Default Address</h4>
                                <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-link text-success fw-bold text-decoration-none">Manage</a>
                            </div>

                            @php
                                $def = $addresses->where('is_default', true)->first();
                            @endphp
                            @if($def)
                                <div class="p-3 bg-light rounded-3 border" style="font-size: 0.85rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>{{ $def->name }}</strong>
                                        <span class="badge bg-secondary text-uppercase" style="font-size: 0.6rem;">{{ $def->type }}</span>
                                    </div>
                                    <p class="text-muted m-0">{{ $def->address_line1 }}</p>
                                    @if($def->address_line2)<p class="text-muted m-0">{{ $def->address_line2 }}</p>@endif
                                    <p class="text-muted m-0">{{ $def->city }}, {{ $def->state }} - {{ $def->postal_code }}</p>
                                    <p class="text-muted m-0 mt-2"><i class="bi bi-phone me-1 text-success"></i>Phone: {{ $def->phone }}</p>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted border border-dashed rounded-3">
                                    <i class="bi bi-geo-alt display-6 text-muted"></i>
                                    <p class="m-0 mt-2" style="font-size: 0.85rem;">No address configured yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
