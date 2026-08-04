@extends('layouts.admin')

@push('admin_styles')
    @vite(['resources/sass/admin/products.scss'])
@endpush

@section('admin_content')

<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h1 class="fs-4 font-heading fw-bold m-0"><i class="bi bi-plus-circle-fill text-success me-2"></i>Add New Product</h1>
        <p class="text-muted m-0 mt-1" style="font-size:0.82rem;">Fill in the product details below. Fields marked * are required.</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
        <i class="bi bi-arrow-left me-1"></i> Back to Products
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm" data-gallery-count="0" data-variant-count="0">
    @csrf

    <div class="row g-4 align-items-start">

        {{-- ═══ LEFT MAIN COLUMN ═══ --}}
        <div class="col-lg-8">

            {{-- Title --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Product Title *</label>
                            <input type="text" name="name" class="form-control border p-2" style="font-size:1.05rem;"
                                   placeholder="e.g. A2 Vedic Bilona Cow Ghee" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted" style="font-size: 0.85rem;">Product Slug (URL identifier)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted" style="font-size: 0.8rem;">/product/</span>
                                <input type="text" name="slug" class="form-control border shadow-none" style="font-size: 0.85rem;"
                                       placeholder="auto-generated-from-title" value="{{ old('slug') }}">
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.75rem; margin-top: 2px;">This defines the clean URL of the product. e.g. /product/a2-desi-cow-ghee</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">SKU Code *</label>
                            <input type="text" name="sku" class="form-control border p-2"
                                   placeholder="e.g. RF-A2-GHEE-1L" value="{{ old('sku') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Default Weight</label>
                            <input type="text" name="weight" class="form-control border p-2"
                                   placeholder="e.g. 1L or 500g" value="{{ old('weight') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Short Description --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-card-text text-success me-2"></i>Short Description *</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Shown below product title on shop listing & product page.</p>
                </div>
                <div class="card-body p-4">
                    <textarea name="short_description" id="editorShortDescription" class="form-control border p-2" rows="3"
                              placeholder="Brief, compelling summary of the product...">{{ old('short_description') }}</textarea>
                </div>
            </div>

            {{-- Long Description --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-file-text text-success me-2"></i>Long Description (Description Tab)</h6>
                </div>
                <div class="card-body p-4">
                    <textarea name="description" id="editorDescription" class="form-control border p-2" rows="6"
                              placeholder="Detailed product description with formatting...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- ── BENEFITS (Separate Card) ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-check2-circle text-success me-2"></i>Benefits</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Shown in the "Benefits" tab on the product detail page. Supports rich text, bullet lists, tables.</p>
                </div>
                <div class="card-body p-4">
                    <textarea name="benefits" id="editorBenefits" class="form-control border p-2" rows="6"
                              placeholder="List product benefits (supports HTML/table format)...">{{ old('benefits') }}</textarea>
                </div>
            </div>

            {{-- ── INGREDIENTS / NUTRITION (Separate Card) ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-leaf text-success me-2"></i>Ingredients &amp; Nutrition</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Shown in the "Ingredients" tab. You can paste a nutrition table or ingredient list here.</p>
                </div>
                <div class="card-body p-4">
                    <textarea name="ingredients" id="editorIngredients" class="form-control border p-2" rows="6"
                              placeholder="Ingredient list or nutrition table...">{{ old('ingredients') }}</textarea>
                </div>
            </div>

            {{-- Weight Variants --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-box-seam text-success me-2"></i>Weight Variants</h6>
                        <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Add different size/weight options with their own prices.</p>
                    </div>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="btnAddVariant">
                        <i class="bi bi-plus-lg me-1"></i> Add Option
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle m-0" id="variantsTable">
                            <thead class="bg-light text-muted" style="font-size:0.8rem;">
                                <tr>
                                    <th class="px-4 py-3">Weight *</th>
                                    <th class="px-4 py-3">MRP (₹)</th>
                                    <th class="px-4 py-3">Sale Price (₹)</th>
                                    <th class="px-4 py-3">Stock</th>
                                    <th class="px-4 py-3">Max Cart Qty</th>
                                    <th class="px-4 py-3 text-center">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="emptyVariantRow">
                                    <td colspan="5" class="text-center py-4 text-muted" style="font-size:0.85rem;">
                                        <i class="bi bi-plus-circle me-1"></i> Click "Add Option" to add weight variants.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── GALLERY with Drag-and-Drop ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-images text-success me-2"></i>Gallery Images</h6>
                        <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">
                            <i class="bi bi-grip-vertical me-1"></i>Drag cards to reorder · Order is saved on submit.
                        </p>
                    </div>
                    <div>
                        <input type="text" id="gallerySelectorInput" style="display:none;">
                        <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="btnOpenGalleryPicker">
                            <i class="bi bi-plus-lg me-1"></i> Add Image
                        </button>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3" id="galleryContainer" style="min-height:80px;">
                        <div class="col-12 text-center py-3 text-muted" id="galleryEmpty" style="font-size:0.85rem;">
                            <i class="bi bi-images me-1"></i> No gallery images added yet.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── SEO CONFIGURATIONS CARD ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-search text-success me-2"></i>SEO Search Engine Optimization</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Define how this product appears on Google search results.</p>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">SEO Title</label>
                        <input type="text" name="meta_title" id="seoTitleInput" class="form-control border p-2" value="{{ old('meta_title') }}" placeholder="e.g. Premium A2 Desi Cow Ghee | RohidaFarm">
                        <small class="text-muted d-block mt-1">Recommended length: 50-60 characters. Max 70.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">SEO Description</label>
                        <textarea name="meta_description" id="seoDescInput" class="form-control border p-2" rows="3" placeholder="Write a short summary to entice searchers to click on your link...">{{ old('meta_description') }}</textarea>
                        <small class="text-muted d-block mt-1">Recommended length: 150-160 characters. Max 160.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">SEO Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control border p-2" value="{{ old('meta_keywords') }}" placeholder="e.g. cow ghee, a2 ghee, organic bilona ghee">
                        <small class="text-muted d-block mt-1">Comma separated list of keywords. e.g. organic, ghee, native.</small>
                    </div>

                    {{-- Google Search Result Snippet Preview --}}
                    <div class="border rounded-4 p-3 bg-light">
                        <h6 class="fw-bold text-muted mb-3" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Google Search Preview</h6>
                        <div class="google-snippet-preview" style="font-family: Arial, sans-serif; max-width: 600px;">
                            <div class="google-url text-truncate mb-1" style="font-size: 0.85rem; color: #202124;">
                                https://rohidafarm.com <span style="color: #5f6368;">&rsaquo; product &rsaquo; <span id="googleSlugPreview">product-slug-url</span></span>
                            </div>
                            <h5 class="google-title mb-1" id="googleTitlePreview" style="font-size: 1.25rem; color: #1a0dab; cursor: pointer; text-decoration: none; font-weight: normal; margin: 0; line-height: 1.3;">
                                New Product | RohidaFarm
                            </h5>
                            <p class="google-desc text-muted m-0" id="googleDescPreview" style="font-size: 0.875rem; color: #4d5156; line-height: 1.5;">
                                No SEO description set. Edit the fields above to configure your search result snippet.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ RIGHT SIDEBAR ═══ --}}
        <div class="col-lg-4">

            {{-- Publish Panel --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-send text-success me-2"></i>Publish</h6>
                </div>
                <div class="card-body p-4">
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold mb-2">
                        <i class="bi bi-check-lg me-1"></i> Save &amp; Publish Product
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100 rounded-pill py-2" style="font-size:0.85rem;">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- Classification --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-tag text-success me-2"></i>Classification</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Category *</label>
                        <select name="category_id" class="form-select border" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Subcategory</label>
                        <select name="sub_category_id" class="form-select border">
                            <option value="">— Select Subcategory —</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}" {{ old('sub_category_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Brand</label>
                        <select name="brand_id" class="form-select border">
                            <option value="">— Select Brand —</option>
                            @foreach($brands as $br)
                                <option value="{{ $br->id }}" {{ old('brand_id') == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Offers & Coupons --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-tags text-success me-2"></i>Offers & Coupons</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Display Coupons</label>
                        <select name="display_coupons[]" id="displayCouponsSelect" class="form-select border" multiple>
                            <option value="all" {{ (is_array(old('display_coupons')) && in_array('all', old('display_coupons'))) ? 'selected' : '' }}>ALL Active Coupons</option>
                            @foreach($coupons as $coupon)
                                <option value="{{ $coupon->code }}" {{ (is_array(old('display_coupons')) && in_array($coupon->code, old('display_coupons'))) ? 'selected' : '' }}>{{ $coupon->code }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Pricing & Stock --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-currency-rupee text-success me-2"></i>Pricing &amp; Stock</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">MRP Price (₹) *</label>
                        <input type="number" step="0.01" name="mrp" class="form-control border p-2"
                               placeholder="e.g. 1999" value="{{ old('mrp') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Sale Price (₹) *</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control border p-2"
                               placeholder="e.g. 1799" value="{{ old('sale_price') }}" required>
                        <div class="text-success fw-semibold mt-1" id="discountBadge" style="font-size:0.78rem;"></div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Initial Stock *</label>
                        <input type="number" name="stock" class="form-control border p-2"
                               placeholder="e.g. 100" value="{{ old('stock', 100) }}" required>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Free Shipping Unlock Target (₹)</label>
                        <input type="number" step="0.01" name="free_shipping_threshold" class="form-control border p-2"
                               placeholder="e.g. 500" value="{{ old('free_shipping_threshold') }}">
                        <div class="form-text mt-1" style="font-size: 0.75rem;">If cart value for this product meets this target, order ships free!</div>
                    </div>
                </div>
            </div>

            {{-- Primary Image --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-image text-success me-2"></i>Primary Image</h6>
                </div>
                <div class="card-body p-4">
                    <input type="text" name="image" id="productImageInput"
                           class="form-control border p-2 shadow-none mb-3"
                           placeholder="/uploads/products/image.jpg or URL">
                    <div id="imagePreviewContainer"></div>
                </div>
            </div>

            {{-- Product Labels --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-flag text-success me-2"></i>Product Labels</h6>
                </div>
                <div class="card-body p-4">
                    @foreach([
                        ['is_featured',    'is_featured',    'Feature on Home Page',    true],
                        ['is_organic',     'is_organic',     '100% Organic Certified',  true],
                        ['is_bilona',      'is_bilona',      'Bilona Method Churned',   false],
                        ['is_best_seller', 'is_best_seller', 'Best Seller Badge',       false],
                        ['is_trending',    'is_trending',    'Trending Badge',          false],
                        ['is_new_arrival', 'is_new_arrival', 'New Arrival Badge',       false],
                    ] as [$name, $id, $label, $default])
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="{{ $name }}" id="{{ $id }}" value="1"
                                   {{ old($name, $default ? '1' : '') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="{{ $id }}" style="font-size:0.85rem;">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</form>
@endsection

@push('admin_scripts')
{{-- CDNs required by products.js --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

{{-- Compiled Products Script Bundle --}}
@vite(['resources/js/admin/products.js'])
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const seoTitleInput = document.getElementById('seoTitleInput');
        const seoDescInput = document.getElementById('seoDescInput');
        const googleTitlePreview = document.getElementById('googleTitlePreview');
        const googleDescPreview = document.getElementById('googleDescPreview');

        if (seoTitleInput && googleTitlePreview) {
            seoTitleInput.addEventListener('input', function() {
                googleTitlePreview.textContent = this.value.trim() || 'New Product | RohidaFarm';
            });
        }

        if (seoDescInput && googleDescPreview) {
            seoDescInput.addEventListener('input', function() {
                googleDescPreview.textContent = this.value.trim() || 'No SEO description set. Edit the fields above to configure your search result snippet.';
            });
        }
    });
</script>
@endpush
