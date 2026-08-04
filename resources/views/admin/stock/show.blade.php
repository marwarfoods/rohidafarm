@extends('layouts.admin')

@section('page_title', 'Stock Details & Analytics')

@section('admin_content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-bar-chart-steps text-success me-2"></i>Stock Logs & Stats</h1>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Detailed logs of inventory updates and daily sales volume for this product.</p>
        </div>
        <div>
            <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill font-heading" style="border-width: 2px; font-weight: 600;">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock List
            </a>
        </div>
    </div>

    <!-- Product Summary Card -->
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
            <div class="row align-items-center g-3">
                <div class="col-auto">
                    <div class="rounded overflow-hidden border" style="width: 80px; height: 80px;">
                        @if($product->image_path)
                            <img src="{{ asset($product->image_path) }}" class="w-100 h-100 object-fit-cover">
                        @else
                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1 text-uppercase fw-semibold" style="font-size: 0.7rem;">{{ $product->category->name ?? 'Uncategorized' }}</span>
                    <h3 class="font-heading fw-bold text-dark m-0 mt-1">{{ $product->name }}</h3>
                    <div class="d-flex align-items-center gap-3 mt-1" style="font-size: 0.825rem;">
                        <span class="text-muted">SKU: <strong class="text-dark">{{ $product->sku ?? 'N/A' }}</strong></span>
                        <span class="text-muted">Current Stock: <strong class="text-dark">{{ $product->stock }} units</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & logs grids -->
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
            <h5 class="fw-bold font-heading text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-clock-history text-success me-2"></i>Stock Adjustment Log
            </h5>
            
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table align-middle m-0 table-sm" style="font-size: 0.8rem;">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-2">Date & Time</th>
                            <th class="px-3 py-2">Adjusted By</th>
                            <th class="px-3 py-2 text-center">Change</th>
                            <th class="px-3 py-2 text-end">Stock Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-3 py-2.5 text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-3 py-2.5 fw-semibold text-dark">{{ $log->user->name ?? 'System' }}</td>
                                <td class="px-3 py-2.5 text-center">
                                    @if($log->change_amount > 0)
                                        <span class="badge bg-success text-white px-2 py-0.5" style="font-size: 0.7rem;">+{{ $log->change_amount }}</span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-0.5" style="font-size: 0.7rem;">{{ $log->change_amount }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-end text-muted">{{ $log->old_stock }} <i class="bi bi-arrow-right mx-1 small"></i> <strong class="text-dark">{{ $log->new_stock }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No stock adjustment entries logged for this product.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
            <h5 class="fw-bold font-heading text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-cart3 text-success me-2"></i>Daily Order Statistics
            </h5>
            
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table align-middle m-0 table-sm" style="font-size: 0.8rem;">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-3 py-2">Order Date</th>
                            <th class="px-3 py-2 text-center">Orders Count</th>
                            <th class="px-3 py-2 text-center">Quantity Sold</th>
                            <th class="px-3 py-2 text-end">Total Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyOrders as $day)
                            <tr>
                                <td class="px-3 py-2.5 text-dark fw-semibold">{{ \Carbon\Carbon::parse($day->order_date)->format('d M Y') }}</td>
                                <td class="px-3 py-2.5 text-center text-muted">{{ $day->order_count }}</td>
                                <td class="px-3 py-2.5 text-center fw-bold text-dark">{{ $day->total_qty }}</td>
                                <td class="px-3 py-2.5 text-end text-success fw-bold">₹{{ number_format($day->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No order volume stats registered for this product.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
