@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-box-seam text-success me-2"></i>Catalog Management</h1>
    @if(auth()->user()->hasPermission('products-create'))
    <a href="{{ route('admin.products.create') }}" class="btn btn-premium px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.8rem;"><i class="bi bi-plus-lg me-1"></i> Add New Product</a>
    @endif
</div>

@if(auth()->user()->hasPermission('products-edit'))
{{-- Bulk Status Action Bar --}}
<div class="d-none align-items-center gap-2 mb-3 p-3 rounded-3 border border-success-subtle bg-success-subtle" id="bulkStatusBar">
    <span class="fw-semibold text-dark" id="bulkSelectedCount" style="font-size: 0.85rem;">0 selected</span>
    <select class="form-select form-select-sm border shadow-none" id="bulkStatusAction" style="font-size: 0.8rem; width: 220px;">
        <option value="">Bulk Action...</option>
        <option value="is_active:1">Mark Active</option>
        <option value="is_active:0">Mark Inactive</option>
        <option value="show_on_home:1">Enable: Show on Home</option>
        <option value="show_on_home:0">Disable: Show on Home</option>
        <option value="show_on_shop:1">Enable: Show on Shop</option>
        <option value="show_on_shop:0">Disable: Show on Shop</option>
        <option value="show_on_category:1">Enable: Show on Category</option>
        <option value="show_on_category:0">Disable: Show on Category</option>
        <option value="is_featured:1">Mark Featured Badge</option>
        <option value="is_featured:0">Unmark Featured Badge</option>
    </select>
    <button type="button" class="btn btn-success btn-sm" id="bulkStatusApplyBtn"><i class="bi bi-check2-circle"></i> Apply</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" id="bulkStatusCancelBtn">Cancel</button>

    {{-- Real, item-by-item progress — width/count only ever advance as each product's own request finishes --}}
    <div class="d-none align-items-center gap-2 ms-2" id="bulkStatusProgressWrap" style="flex: 1; min-width: 200px;">
        <div class="progress flex-grow-1" style="height: 8px;">
            <div class="progress-bar bg-success" id="bulkStatusProgressBar" style="width: 0%;"></div>
        </div>
        <span class="text-muted fw-semibold" id="bulkStatusProgressText" style="font-size: 0.78rem; white-space: nowrap;">0 / 0</span>
    </div>
</div>
@endif

<x-admin-table
    :headers="[auth()->user()->hasPermission('products-edit') ? '<input type=\'checkbox\' id=\'selectAllProducts\'>' : '', 'ID', 'Product Name & Image', 'Category', 'SKU', 'Original / Off Price', 'Stock', 'Status & Placement', 'Actions']"
    :items="$products"
    title="Products Catalog"
    description="Manage your store products, modify pricing, stock levels, and assign categories.">

    <x-slot name="actions">
        @if(auth()->user()->hasPermission('products-create'))
        <a href="{{ route('admin.products.create') }}" class="btn btn-premium btn-sm rounded-pill text-uppercase font-heading" style="font-size: 0.75rem;"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
        @endif
    </x-slot>

    @forelse($products as $prod)
        <tr style="font-size: 0.85rem;" id="productRow_{{ $prod->id }}">
            @if(auth()->user()->hasPermission('products-edit'))
            <td class="px-4 py-3">
                <input type="checkbox" class="form-check-input product-row-checkbox" value="{{ $prod->id }}">
            </td>
            @endif
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
            <td class="px-4 py-3" style="min-width: 140px;">
                @if(auth()->user()->hasPermission('products-edit'))
                <div class="input-group input-group-sm" style="width: 130px;">
                    <input type="number" class="form-control text-center stock-inp bg-light border fw-bold" data-id="{{ $prod->id }}" value="{{ $prod->stock }}" min="0" placeholder="Qty">
                    <button type="button" class="btn btn-success btn-update-stock" data-id="{{ $prod->id }}" title="Update Stock Quantity"><i class="bi bi-check-lg"></i></button>
                </div>
                @else
                <span class="fw-bold">{{ $prod->stock }}</span>
                @endif
            </td>
            <td class="px-4 py-3">
                <span class="badge product-status-active-badge {{ $prod->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border' }} d-block mb-1" style="width: fit-content;">
                    {{ $prod->is_active ? 'Active' : 'Inactive' }}
                </span>
                <div class="d-flex flex-wrap gap-1 mt-1">
                    @if($prod->show_on_home)
                        <span class="badge bg-light text-primary border" style="font-size:0.68rem;" title="Visible on Homepage">Home</span>
                    @endif
                    @if($prod->show_on_shop)
                        <span class="badge bg-light text-success border" style="font-size:0.68rem;" title="Visible on Shop Page">Shop</span>
                    @endif
                    @if($prod->show_on_category)
                        <span class="badge bg-light text-secondary border" style="font-size:0.68rem;" title="Visible on Category Page">Cat</span>
                    @endif
                    @if($prod->is_featured)
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.68rem;">Featured</span>
                    @endif
                </div>
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
            <td colspan="9" class="text-center py-4 text-muted">No products available in database catalog.</td>
        </tr>
    @endforelse
</x-admin-table>
@endsection

@push('admin_styles')
<style>
/* Remove number spinner up/down arrows */
input.stock-inp::-webkit-outer-spin-button,
input.stock-inp::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input.stock-inp[type=number] {
    -moz-appearance: textfield;
}
.stock-inp {
    font-size: 0.9rem !important;
    letter-spacing: 0.5px;
}
</style>
@endpush

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Stock Quick Update AJAX
        const stockButtons = document.querySelectorAll('.btn-update-stock');
        stockButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const inp = document.querySelector(`.stock-inp[data-id="${id}"]`);
                const qty = inp.value;

                const origHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width: 12px; height: 12px;"></span>';

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
                    this.disabled = false;
                    this.innerHTML = origHtml;
                    if (data.status === 'success') {
                        inp.classList.add('is-valid');
                        setTimeout(() => inp.classList.remove('is-valid'), 2000);
                    } else {
                        alert('Failed to update stock quantity: ' + (data.message || 'Error'));
                    }
                })
                .catch(err => {
                    this.disabled = false;
                    this.innerHTML = origHtml;
                    alert('Network error while updating stock.');
                });
            });
        });

        // ── Bulk Product Status Update (real per-item progress) ──
        const selectAllProducts = document.getElementById('selectAllProducts');
        const rowCheckboxes = document.querySelectorAll('.product-row-checkbox');
        const bulkStatusBar = document.getElementById('bulkStatusBar');
        const bulkSelectedCount = document.getElementById('bulkSelectedCount');
        const bulkStatusAction = document.getElementById('bulkStatusAction');
        const bulkStatusApplyBtn = document.getElementById('bulkStatusApplyBtn');
        const bulkStatusCancelBtn = document.getElementById('bulkStatusCancelBtn');
        const progressWrap = document.getElementById('bulkStatusProgressWrap');
        const progressBar = document.getElementById('bulkStatusProgressBar');
        const progressText = document.getElementById('bulkStatusProgressText');

        function getCheckedIds() {
            return Array.from(rowCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        }

        function refreshBulkBar() {
            if (!bulkStatusBar) return;
            const ids = getCheckedIds();
            if (ids.length > 0) {
                bulkStatusBar.classList.remove('d-none');
                bulkStatusBar.classList.add('d-flex');
                bulkSelectedCount.textContent = `${ids.length} selected`;
            } else {
                bulkStatusBar.classList.add('d-none');
                bulkStatusBar.classList.remove('d-flex');
            }
        }

        if (selectAllProducts) {
            selectAllProducts.addEventListener('change', function () {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                refreshBulkBar();
            });
        }

        rowCheckboxes.forEach(cb => cb.addEventListener('change', refreshBulkBar));

        if (bulkStatusCancelBtn) {
            bulkStatusCancelBtn.addEventListener('click', function () {
                rowCheckboxes.forEach(cb => cb.checked = false);
                if (selectAllProducts) selectAllProducts.checked = false;
                refreshBulkBar();
            });
        }

        if (bulkStatusApplyBtn) {
            bulkStatusApplyBtn.addEventListener('click', function () {
                const raw = bulkStatusAction.value;
                if (!raw) {
                    alert('Please choose a bulk action first.');
                    return;
                }

                const ids = getCheckedIds();
                if (ids.length === 0) {
                    alert('Please select at least one product.');
                    return;
                }

                const [field, valueStr] = raw.split(':');
                const value = valueStr === '1';
                const label = bulkStatusAction.options[bulkStatusAction.selectedIndex].text;

                if (!confirm(`${label} for ${ids.length} selected product(s)?`)) return;

                bulkStatusApplyBtn.disabled = true;
                bulkStatusCancelBtn.disabled = true;
                progressWrap.classList.remove('d-none');
                progressWrap.classList.add('d-flex');
                progressBar.style.width = '0%';
                progressText.textContent = `0 / ${ids.length}`;

                let completed = 0;
                let failed = 0;

                function updateNext(index) {
                    if (index >= ids.length) {
                        bulkStatusApplyBtn.disabled = false;
                        bulkStatusCancelBtn.disabled = false;
                        if (failed > 0) {
                            alert(`${completed} product(s) updated, ${failed} failed.`);
                        }
                        if (completed > 0) {
                            window.location.reload();
                        }
                        return;
                    }

                    const id = ids[index];

                    fetch(`/admin/products/${id}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ field: field, value: value })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            completed++;
                        } else {
                            failed++;
                        }
                    })
                    .catch(() => { failed++; })
                    .finally(() => {
                        // Real progress: only advances once THIS product's own request has actually completed.
                        const done = completed + failed;
                        const percent = Math.round((done / ids.length) * 100);
                        progressBar.style.width = percent + '%';
                        progressText.textContent = `${done} / ${ids.length}`;
                        updateNext(index + 1);
                    });
                }

                updateNext(0);
            });
        }
    });
</script>
@endpush
