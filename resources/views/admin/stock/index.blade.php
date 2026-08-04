@extends('layouts.admin')

@section('page_title', 'Stock Management')

@section('admin_content')
<div class="row g-4">
    <!-- Header -->
    <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-boxes text-success me-2"></i>Stock Management</h1>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Monitor stock counts, track adjustment logs, and view daily order volumes.</p>
        </div>
        <div>
            <form action="{{ route('admin.stock.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control bg-white border shadow-none px-3" placeholder="Search by name or SKU..." value="{{ $search ?? '' }}" style="font-size: 0.85rem; width: 240px; border-radius: 0.5rem;">
                <button type="submit" class="btn btn-success px-3 rounded-3" style="background-color: var(--admin-accent) !important; border-color: var(--admin-accent) !important;">
                    <i class="bi bi-search"></i>
                </button>
                @if($search)
                    <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-secondary rounded-3">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="col-12">
        <div class="row g-3">
            <!-- Card 1 -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-box-seam fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-semibold">Total Products</span>
                            <h4 class="font-heading fw-bold text-dark m-0">{{ $metrics['total_products'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-semibold">Low Stock Alerts</span>
                            <h4 class="font-heading fw-bold text-warning m-0">{{ $metrics['low_stock'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-x-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-semibold">Out of Stock</span>
                            <h4 class="font-heading fw-bold text-danger m-0">{{ $metrics['out_of_stock'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 4 -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 rounded-4 shadow-sm p-3 bg-white h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-cart-check-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-semibold">Total Items Sold</span>
                            <h4 class="font-heading fw-bold text-primary m-0">{{ $metrics['total_items_sold'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product List Table -->
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm overflow-hidden bg-white">
            <div class="table-responsive">
                <table class="table align-middle m-0">
                    <thead>
                        <tr class="bg-light" style="font-size: 0.825rem; text-transform: uppercase;">
                            <th class="px-4 py-3" style="width: 80px;">Thumbnail</th>
                            <th class="px-4 py-3">Product Name</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3" style="width: 180px;">Quick Stock Adjust</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-end">Logs & Stats</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $stockClass = '';
                                $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">In Stock</span>';
                                
                                if($product->stock <= 0) {
                                    $stockClass = 'table-danger';
                                    $statusBadge = '<span class="badge bg-danger text-white rounded-pill px-2.5 py-1">Out of Stock</span>';
                                } elseif($product->stock <= 5) {
                                    $stockClass = 'table-warning';
                                    $statusBadge = '<span class="badge bg-warning text-dark rounded-pill px-2.5 py-1">Low Stock</span>';
                                }
                            @endphp
                            <tr class="{{ $stockClass }}" style="font-size: 0.85rem;">
                                <td class="px-4 py-3">
                                    <div class="rounded overflow-hidden border" style="width: 50px; height: 50px;">
                                        @if($product->primaryImage)
                                            <img src="{{ asset($product->primaryImage->image_path) }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 fw-bold text-dark">{{ $product->name }}</td>
                                <td class="px-4 py-3 text-muted">{{ $product->sku ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-muted">{{ $product->category->name ?? 'Uncategorized' }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.stock.update', $product->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="stock" class="form-control text-center bg-white" value="{{ $product->stock }}" min="0" required style="width: 60px;">
                                            <button type="submit" class="btn btn-success" title="Save Stock">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td class="px-4 py-3">{!! $statusBadge !!}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.stock.show', $product->id) }}" class="btn btn-sm btn-premium-outline rounded-3 px-3 py-1">
                                        <i class="bi bi-clock-history me-1"></i> Logs & Stats
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-boxes display-4"></i>
                                    <h6 class="mt-3">No products match this query.</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if($products->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
