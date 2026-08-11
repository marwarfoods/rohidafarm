@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-box-seam text-success me-2"></i>Catalog Management</h1>
    @if(auth()->user()->hasPermission('products-create'))
    <a href="{{ route('admin.products.create') }}" class="btn btn-premium px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.8rem;"><i class="bi bi-plus-lg me-1"></i> Add New Product</a>
    @endif
</div>

<x-admin-table 
    :headers="['ID', 'Product Name & Image', 'Category', 'SKU', 'Original / Off Price', 'Stock', 'Actions']" 
    :items="$products" 
    title="Products Catalog" 
    description="Manage your store products, modify pricing, stock levels, and assign categories.">
    
    <x-slot name="actions">
        @if(auth()->user()->hasPermission('products-create'))
        <a href="{{ route('admin.products.create') }}" class="btn btn-premium btn-sm rounded-pill text-uppercase font-heading" style="font-size: 0.75rem;"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
        @endif
    </x-slot>

    @forelse($products as $prod)
        <tr style="font-size: 0.85rem;">
            <td class="fw-semibold px-4 py-3">#{{ $prod->id }}</td>
            <td class="px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ $prod->primaryImage ? asset($prod->primaryImage->image_path) : asset('/assets/images/products/placeholder.jpg') }}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">
                    <span class="fw-bold text-dark" title="{{ $prod->name }}">{{ \Illuminate\Support\Str::words($prod->name, 4, '...') }}</span>
                </div>
            </td>
            <td class="px-4 py-3">{{ $prod->category?->name ?? 'Uncategorized' }} @if($prod->subCategory) / {{ $prod->subCategory?->name }} @endif</td>
            <td class="fw-semibold px-4 py-3">{{ $prod->sku }}</td>
            <td class="px-4 py-3">
                <span class="text-decoration-line-through text-muted small">₹{{ number_format($prod->mrp, 0) }}</span>
                <span class="fw-bold text-success ms-1">₹{{ number_format($prod->sale_price, 0) }}</span>
            </td>
            <td class="px-4 py-3">
                @if(auth()->user()->hasPermission('products-edit'))
                <div class="input-group input-group-sm" style="max-width: 100px;">
                    <input type="number" class="form-control text-center stock-inp" data-id="{{ $prod->id }}" value="{{ $prod->stock }}" min="0">
                    <button type="button" class="btn btn-success btn-update-stock" data-id="{{ $prod->id }}"><i class="bi bi-check-lg"></i></button>
                </div>
                @else
                <span class="fw-bold">{{ $prod->stock }}</span>
                @endif
            </td>
            <td class="text-end px-4 py-3">
                <div class="d-inline-flex gap-2">
                    @if(auth()->user()->hasPermission('products-edit'))
                    <a href="{{ route('admin.products.edit', $prod->id) }}" class="btn btn-sm btn-outline-success border"><i class="bi bi-pencil"></i></a>
                    @endif
                    @if(auth()->user()->hasPermission('products-delete'))
                    <form action="{{ route('admin.products.delete', $prod->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete (soft delete) this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger border"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center py-4 text-muted">No products available in database catalog.</td>
        </tr>
    @endforelse
</x-admin-table>
@endsection

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Stock Quick Update AJAX
        const stockButtons = document.querySelectorAll('.btn-update-stock');
        stockButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const qty = document.querySelector(`.stock-inp[data-id="${id}"]`).value;

                fetch(`/admin/products/${id}/stock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ stock: qty })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                    } else {
                        alert('Failed to update stock quantity.');
                    }
                });
            });
        });
    });
</script>
@endpush
