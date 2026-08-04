@extends('layouts.admin')

@push('admin_styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    .choices__inner { background-color: #f8f9fa; border-radius: 0.375rem; border-color: #dee2e6; padding: 5.5px 7.5px; }
</style>
@endpush

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0">Edit Coupon: {{ $coupon->code }}</h1>
    <a href="{{ route('admin.coupons.index') }}" class="btn btn-light border rounded-3 fw-medium">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card border-0 rounded-4 shadow-sm bg-white">
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
            @csrf
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Coupon Code *</label>
                    <div class="input-group">
                        <input type="text" name="code" id="couponCodeInput" value="{{ $coupon->code }}" class="form-control bg-light border p-3 text-uppercase" required style="border-top-left-radius: 8px; border-bottom-left-radius: 8px;">
                        <button class="btn btn-outline-success px-3" type="button" id="btnGenerateCode" title="Generate Code" style="border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
                            <i class="bi bi-magic me-1"></i> Generate
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Discount Type *</label>
                    <select name="discount_type" class="form-select bg-light border p-3" required>
                        <option value="percentage" {{ $coupon->discount_type == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ $coupon->discount_type == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Discount Value *</label>
                    <input type="number" step="0.01" name="discount_value" value="{{ $coupon->discount_value }}" class="form-control bg-light border p-3" required>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Minimum Order Amount</label>
                    <input type="number" step="0.01" name="min_amount" value="{{ $coupon->min_amount }}" class="form-control bg-light border p-3">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Maximum Discount (if %)</label>
                    <input type="number" step="0.01" name="max_discount" value="{{ $coupon->max_discount }}" class="form-control bg-light border p-3">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Total Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ $coupon->usage_limit }}" class="form-control bg-light border p-3">
                </div>
            </div>

            <div class="row g-4 mb-5 border-bottom pb-5">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Valid From Date</label>
                    <input type="datetime-local" name="active_from" value="{{ $coupon->active_from ? $coupon->active_from->format('Y-m-d\TH:i') : '' }}" class="form-control bg-light border p-3">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Valid Until Date</label>
                    <input type="datetime-local" name="active_until" value="{{ $coupon->active_until ? $coupon->active_until->format('Y-m-d\TH:i') : '' }}" class="form-control bg-light border p-3">
                </div>
            </div>

            <h5 class="fw-bold mb-3"><i class="bi bi-bullseye text-success me-2"></i>Coupon Targets</h5>
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Apply Coupon To *</label>
                    <select name="target_type" id="targetTypeSelect" class="form-select bg-light border p-3" required>
                        <option value="all" {{ $coupon->target_type == 'all' ? 'selected' : '' }}>All Products</option>
                        <option value="products" {{ $coupon->target_type == 'products' ? 'selected' : '' }}>Specific Products</option>
                        <option value="categories" {{ $coupon->target_type == 'categories' ? 'selected' : '' }}>Specific Categories</option>
                    </select>
                </div>
                
                <div class="col-md-8" id="targetIdsContainer" style="display: none;">
                    <label class="form-label fw-bold" id="targetIdsLabel">Select Targets</label>
                    <!-- Products Select -->
                    <div id="productSelectWrapper" style="display: none;">
                        <select name="target_ids[]" id="targetProducts" class="form-select" multiple>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ is_array($coupon->target_ids) && in_array($product->id, $coupon->target_ids) ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Categories Select -->
                    <div id="categorySelectWrapper" style="display: none;">
                        <select name="target_ids[]" id="targetCategories" class="form-select" multiple>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ is_array($coupon->target_ids) && in_array($category->id, $coupon->target_ids) ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveSwitch" {{ $coupon->is_active ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                <label class="form-check-label fw-bold ms-2 pt-1" for="isActiveSwitch">Coupon is Active</label>
            </div>

            <button type="submit" class="btn btn-success fw-bold text-uppercase w-100 py-3" style="border-radius: 12px; font-size: 0.95rem;">
                Save Changes
            </button>
        </form>
    </div>
</div>

@push('admin_scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Choices('#targetProducts', { removeItemButton: true, searchEnabled: true, placeholderValue: 'Search & select products...', itemSelectText: '' });
    new Choices('#targetCategories', { removeItemButton: true, searchEnabled: true, placeholderValue: 'Search & select categories...', itemSelectText: '' });

    const targetType = document.getElementById('targetTypeSelect');
    const container = document.getElementById('targetIdsContainer');
    const productWrapper = document.getElementById('productSelectWrapper');
    const categoryWrapper = document.getElementById('categorySelectWrapper');
    const label = document.getElementById('targetIdsLabel');

    function toggleFields() {
        productWrapper.style.display = 'none';
        categoryWrapper.style.display = 'none';

        if (targetType.value === 'all') {
            container.style.display = 'none';
        } else if (targetType.value === 'products') {
            container.style.display = 'block';
            productWrapper.style.display = 'block';
            label.textContent = 'Select Specific Products';
        } else if (targetType.value === 'categories') {
            container.style.display = 'block';
            categoryWrapper.style.display = 'block';
            label.textContent = 'Select Specific Categories';
        }
    }

    targetType.addEventListener('change', toggleFields);
    toggleFields(); // initialize state on load

    // Coupon Code Generator
    const btnGen = document.getElementById('btnGenerateCode');
    const inputGen = document.getElementById('couponCodeInput');
    if (btnGen && inputGen) {
        btnGen.addEventListener('click', function() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = 'RF-';
            for (let i = 0; i < 8; i++) {
                code += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            inputGen.value = code;
        });
    }
});
</script>
@endpush
@endsection
