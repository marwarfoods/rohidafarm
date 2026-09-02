@extends('layouts.admin')

@push('admin_styles')
    @vite(['resources/sass/admin/products.scss'])
@endpush

@section('admin_content')

<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h1 class="fs-4 font-heading fw-bold m-0"><i class="bi bi-pencil-fill text-success me-2"></i>Edit Product</h1>
        <p class="text-muted m-0 mt-1" style="font-size:0.82rem;">Editing: <strong>{{ $product->name }}</strong> · SKU: {{ $product->sku }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('shop.show', $product->slug) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-eye me-1"></i> View Live
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger rounded-3 mb-4">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="productForm" data-gallery-count="{{ $product->images->where('is_primary', false)->count() }}" data-variant-count="{{ $product->variants->count() }}" data-faq-count="{{ $product->faqs->count() }}">
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
                                   value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-muted" style="font-size: 0.85rem;">Product Slug (URL identifier)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted" style="font-size: 0.8rem;">/product/</span>
                                <input type="text" name="slug" class="form-control border shadow-none" style="font-size: 0.85rem;"
                                       placeholder="auto-generated-from-title" value="{{ old('slug', $product->slug) }}">
                            </div>
                            <div class="form-text text-muted" style="font-size: 0.75rem; margin-top: 2px;">This defines the clean URL of the product. e.g. /product/a2-desi-cow-ghee</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">SKU Code *</label>
                            <input type="text" name="sku" class="form-control border p-2" value="{{ old('sku', $product->sku) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Default Weight</label>
                            <input type="text" name="weight" class="form-control border p-2" value="{{ old('weight', $product->weight) }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Short Description --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-card-text text-success me-2"></i>Short Description *</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Shown below product title on shop & product page.</p>
                </div>
                <div class="card-body p-4">
                    <textarea name="short_description" id="editorShortDescription" class="form-control border p-2" rows="3">{!! old('short_description', $product->short_description) !!}</textarea>
                </div>
            </div>

            {{-- Long Description --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-file-text text-success me-2"></i>Long Description (Description Tab)</h6>
                </div>
                <div class="card-body p-4">
                    <textarea name="description" id="editorDescription" class="form-control border p-2" rows="6">{!! old('description', $product->description) !!}</textarea>
                </div>
            </div>

            {{-- ── BENEFITS (Separate Card) ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-check2-circle text-success me-2"></i>Benefits</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Shown in the "Benefits" tab on the product detail page. Supports rich text, bullet lists, tables.</p>
                </div>
                <div class="card-body p-4">
                    <textarea name="benefits" id="editorBenefits" class="form-control border p-2" rows="6">{!! old('benefits', $product->benefits) !!}</textarea>
                </div>
            </div>

            {{-- ── INGREDIENTS / NUTRITION (Separate Card) ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-leaf text-success me-2"></i>Ingredients &amp; Nutrition</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Shown in the "Ingredients" tab. You can paste a nutrition table or ingredient list here.</p>
                </div>
                <div class="card-body p-4">
                    <textarea name="ingredients" id="editorIngredients" class="form-control border p-2" rows="6">{!! old('ingredients', $product->ingredients) !!}</textarea>
                </div>
            </div>

            {{-- Weight Variants --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-box-seam text-success me-2"></i>Weight Variants &amp; Variant Images</h6>
                        <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">Configure size/weight options, prices, stock, and individual variant images/galleries.</p>
                    </div>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="btnAddVariant">
                        <i class="bi bi-plus-lg me-1"></i> Add Variant
                    </button>
                </div>
                <div class="card-body p-3">
                    <div id="variantsContainer">
                        @foreach($product->variants as $index => $v)
                            <div class="variant-item card border rounded-3 mb-3 bg-white shadow-sm overflow-hidden" data-index="{{ $index }}">
                                {{-- Accordion Header --}}
                                <div class="card-header bg-light px-3 py-2.5 d-flex justify-content-between align-items-center variant-header" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#variantCollapse_{{ $index }}" aria-expanded="true">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-chevron-down variant-toggle-icon text-muted" style="transition: transform 0.2s;"></i>
                                        <span class="badge bg-success font-heading variant-weight-badge">{{ $v->weight ?: 'New Variant' }}</span>
                                        <span class="text-dark fw-bold variant-price-preview">₹{{ number_format($v->sale_price, 0) }}</span>
                                        <div class="variant-header-thumb-container d-inline-block ms-1">
                                            @if($v->image_path)
                                                <img src="{{ asset($v->image_path) }}" class="rounded border variant-header-thumb" style="width: 28px; height: 28px; object-fit: cover;">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5 btn-remove-variant" style="font-size: 0.75rem;">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>

                                {{-- Accordion Body --}}
                                <div id="variantCollapse_{{ $index }}" class="collapse show">
                                    <div class="card-body p-3">
                                        {{-- Row 1: Weight, MRP, Sale Price, Stock, Max Cart Qty --}}
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Weight Option *</label>
                                                <input type="text" name="variants[{{ $index }}][weight]" class="form-control form-control-sm border variant-weight-input" required value="{{ $v->weight }}" placeholder="e.g. 500ml or 1kg">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">MRP (₹)</label>
                                                <input type="number" step="0.01" name="variants[{{ $index }}][mrp]" class="form-control form-control-sm border" value="{{ $v->mrp }}" placeholder="999">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Sale Price (₹) *</label>
                                                <input type="number" step="0.01" name="variants[{{ $index }}][sale_price]" class="form-control form-control-sm border variant-price-input" value="{{ $v->sale_price }}" placeholder="799" required>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Stock *</label>
                                                <input type="number" name="variants[{{ $index }}][stock]" class="form-control form-control-sm border" value="{{ $v->stock }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Max Cart Qty</label>
                                                <input type="number" name="variants[{{ $index }}][max_cart_qty]" class="form-control form-control-sm border" value="{{ $v->max_cart_qty }}" placeholder="No limit">
                                            </div>
                                        </div>

                                        {{-- Row 2: Variant Main Image & Variant Gallery Images --}}
                                        <div class="p-3 bg-light rounded-3 border">
                                            <div class="row g-3">
                                                {{-- Variant Main Thumbnail --}}
                                                <div class="col-md-5 border-end">
                                                    <label class="form-label text-dark fw-semibold mb-1 d-flex align-items-center gap-1" style="font-size:0.8rem;">
                                                        <i class="bi bi-image text-success"></i> Variant Main Image
                                                    </label>
                                                    <p class="text-muted m-0 mb-2" style="font-size: 0.72rem;">Primary photo shown when this variant is selected.</p>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="variant-main-img-preview position-relative border rounded bg-white d-flex align-items-center justify-content-center overflow-visible" style="width: 54px; height: 54px; flex-shrink: 0;">
                                                            @if($v->image_path)
                                                                <img src="{{ asset($v->image_path) }}" class="w-100 h-100 object-fit-cover rounded">
                                                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 p-0 btn-clear-variant-main-img d-flex align-items-center justify-content-center" title="Remove main image" style="width: 18px; height: 18px; font-size: 10px; transform: translate(30%, -30%); z-index: 5;">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            @else
                                                                <i class="bi bi-image text-muted" style="font-size: 1.3rem;"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <input type="text" name="variants[{{ $index }}][image_path]" class="form-control form-control-sm media-picker-input variant-main-img-input" value="{{ $v->image_path }}" placeholder="Pick main image...">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Variant Additional Gallery Images --}}
                                                <div class="col-md-7">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-label text-dark fw-semibold m-0 d-flex align-items-center gap-1" style="font-size:0.8rem;">
                                                            <i class="bi bi-images text-success"></i> Variant Gallery Images
                                                        </label>
                                                        <button type="button" class="btn btn-outline-success btn-sm py-0.5 px-2.5 btn-add-variant-gallery" data-index="{{ $index }}" style="font-size: 0.72rem;">
                                                            <i class="bi bi-plus-lg me-1"></i> Add Gallery Photo
                                                        </button>
                                                    </div>
                                                    <p class="text-muted m-0 mb-2" style="font-size: 0.72rem;">Slides shown in product detail gallery when this variant is active.</p>
                                                    
                                                    <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-white min-height-50 variant-gallery-chips-container">
                                                        @php $vGalleries = is_array($v->gallery_images) ? $v->gallery_images : []; @endphp
                                                        @foreach($vGalleries as $gIdx => $gPath)
                                                            @if(!empty($gPath))
                                                                <div class="position-relative variant-gallery-chip">
                                                                    <input type="hidden" name="variants[{{ $index }}][gallery_images][]" value="{{ $gPath }}">
                                                                    <img src="{{ asset($gPath) }}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                                                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px; transform: translate(30%, -30%);" onclick="this.closest('.variant-gallery-chip').remove();">
                                                                        <i class="bi bi-x"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                        <span class="text-muted small align-self-center empty-variant-gallery-msg {{ count($vGalleries) ? 'd-none' : '' }}" style="font-size: 0.75rem;">
                                                            No gallery photos added yet for this variant.
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if($product->variants->isEmpty())
                            <div class="text-center py-4 text-muted" id="emptyVariantRow" style="font-size:0.85rem;">
                                <i class="bi bi-plus-circle me-1"></i> Click "Add Variant" to configure size/weight options.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── PRODUCT FAQs with Drag-and-Drop ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-question-circle text-success me-2"></i>Product FAQs</h6>
                        <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">
                            <i class="bi bi-grip-vertical me-1"></i>Drag the handle on the left to reorder · Order is saved on submit.
                        </p>
                    </div>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="btnAddFaq">
                        <i class="bi bi-plus-lg me-1"></i> Add FAQ
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="faqsContainer" class="d-flex flex-column gap-3">
                        @foreach($product->faqs as $index => $faq)
                            <div class="faq-item-card d-flex align-items-start gap-3 border rounded-3 p-3 bg-light">
                                <div class="faq-drag-handle text-muted pt-2" style="cursor: grab;"><i class="bi bi-grip-vertical fs-5"></i></div>
                                <div class="flex-grow-1">
                                    <input type="text" name="faqs[{{ $index }}][question]" class="form-control border mb-2 faq-question-input" placeholder="Question" required value="{{ $faq->question }}">
                                    <textarea name="faqs[{{ $index }}][answer]" class="form-control border faq-answer-input" rows="2" placeholder="Answer" required>{{ $faq->answer }}</textarea>
                                    <input type="hidden" class="faq-sort-order" name="faqs[{{ $index }}][sort_order]" value="{{ $index }}">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger border rounded-pill btn-remove-faq"><i class="bi bi-trash"></i></button>
                            </div>
                        @endforeach
                        <div class="text-center py-3 text-muted" id="faqsEmpty" style="font-size:0.85rem; {{ $product->faqs->count() > 0 ? 'display:none;' : '' }}">
                            <i class="bi bi-question-circle me-1"></i> No FAQs added yet.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── GALLERY with Drag-and-Drop ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark m-0"><i class="bi bi-images text-success me-2"></i>Gallery Images</h6>
                        <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">
                            <i class="bi bi-grip-vertical me-1 text-muted"></i>Drag cards to reorder · Order is saved on submit.
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
                        @php $galleryIdx = 0; @endphp
                        @foreach($product->images->where('is_primary', false) as $img)
                            <div class="col-6 col-md-4 gallery-item-card" data-order="{{ $galleryIdx }}">
                                <div class="card border rounded-3 bg-white shadow-sm h-100" style="cursor:grab;">
                                    <div class="card-body p-2 text-center">
                                        <div class="drag-handle text-muted mb-1" style="font-size:0.75rem;cursor:grab;">
                                            <i class="bi bi-grip-horizontal me-1"></i> Drag to reorder
                                        </div>
                                        <img src="{{ asset($img->image_path) }}" class="w-100 rounded-2 mb-2" style="height:90px;object-fit:cover;">
                                        <input type="hidden" class="gallery-image-path" name="gallery[{{ $galleryIdx }}][image_path]" value="{{ $img->image_path }}">
                                        <input type="hidden" class="gallery-sort-order" name="gallery[{{ $galleryIdx }}][sort_order]" value="{{ $galleryIdx + 1 }}">
                                        <input type="text" class="gallery-video-path form-control form-control-sm border mb-2"
                                               name="gallery[{{ $galleryIdx }}][video_path]"
                                               placeholder="Video URL (optional)" value="{{ $img->video_path }}" style="font-size:0.72rem;">
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill btn-remove-gallery-item" style="font-size:0.75rem;">
                                            <i class="bi bi-trash me-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @php $galleryIdx++; @endphp
                        @endforeach
                        @if($galleryIdx === 0)
                            <div class="col-12 text-center py-4 text-muted" id="galleryEmpty" style="font-size:0.85rem;">
                                <i class="bi bi-images me-1"></i> No gallery images added yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── PRODUCT STORY / INFOGRAPHIC BANNERS ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-file-image text-success me-2"></i>Product Story &amp; Infographic Banners</h6>
                    <p class="text-muted m-0 mt-1" style="font-size:0.78rem;">
                        Upload vertical promotional banners to display below description section on product page. Click "x" on any banner card to remove it.
                    </p>
                </div>
                <div class="card-body p-4">
                    <input type="hidden" name="infographic_form_submitted" value="1">
                    @php $existingInfographics = $product->infographic_images ?? []; @endphp

                    <label class="form-label text-dark fw-semibold" style="font-size:0.85rem;">Active Story Banners:</label>
                    <div class="d-flex flex-wrap gap-3 p-3 border rounded-4 bg-light mb-4" id="infographicGrid">
                        @forelse($existingInfographics as $idx => $img)
                            @php $imgPath = is_array($img) ? ($img['image_path'] ?? $img['url'] ?? reset($img)) : $img; @endphp
                            @if(is_string($imgPath) && !empty($imgPath))
                                <div class="position-relative infographic-card-item">
                                    <input type="hidden" name="existing_infographics[]" value="{{ $imgPath }}">
                                    <img src="{{ asset($imgPath) }}" alt="story banner" style="height: 120px; width: 90px; object-fit: cover; border-radius: 10px; border: 2px solid #ddd;">
                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center"
                                            style="width: 22px; height: 22px; font-size: 12px; line-height: 1;"
                                            onclick="this.closest('.infographic-card-item').remove()"
                                            title="Remove image">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            @endif
                        @empty
                            <span class="text-muted small py-2" id="emptyInfographicsMsg">No story banners added yet.</span>
                        @endforelse
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Option A: Pick from Media Library</label>
                            <div class="input-group">
                                <input type="text" name="infographic_urls" id="infographicMediaInput" class="form-control media-picker-input"
                                       placeholder="Choose from media library..." value="{{ old('infographic_urls') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Option B: Upload Files Directly (Multiple)</label>
                            <input type="file" name="infographic_images[]" class="form-control border p-2" accept="image/*" multiple onchange="previewInfographicFiles(this)">
                        </div>
                    </div>
                    <div id="infographicPreview" class="d-flex flex-wrap gap-2 mt-3 p-2 border rounded-3 bg-light min-height-80 d-none">
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
                        <input type="text" name="meta_title" id="seoTitleInput" class="form-control border p-2" value="{{ old('meta_title', $product->meta_title) }}" placeholder="e.g. Premium A2 Desi Cow Ghee | RohidaFarm">
                        <small class="text-muted d-block mt-1">Recommended length: 50-60 characters. Max 70.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">SEO Description</label>
                        <textarea name="meta_description" id="seoDescInput" class="form-control border p-2" rows="3" placeholder="Write a short summary to entice searchers to click on your link...">{{ old('meta_description', $product->meta_description) }}</textarea>
                        <small class="text-muted d-block mt-1">Recommended length: 150-160 characters. Max 160.</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">SEO Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control border p-2" value="{{ old('meta_keywords', $product->meta_keywords) }}" placeholder="e.g. cow ghee, a2 ghee, organic bilona ghee">
                        <small class="text-muted d-block mt-1">Comma separated list of keywords. e.g. organic, ghee, native.</small>
                    </div>

                    {{-- Google Search Result Snippet Preview --}}
                    <div class="border rounded-4 p-3 bg-light">
                        <h6 class="fw-bold text-muted mb-3" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">Google Search Preview</h6>
                        <div class="google-snippet-preview" style="font-family: Arial, sans-serif; max-width: 600px;">
                            <div class="google-url text-truncate mb-1" style="font-size: 0.85rem; color: #202124;">
                                https://rohidafarm.com <span style="color: #5f6368;">&rsaquo; product &rsaquo; <span id="googleSlugPreview">{{ $product->slug }}</span></span>
                            </div>
                            <h5 class="google-title mb-1" id="googleTitlePreview" style="font-size: 1.25rem; color: #1a0dab; cursor: pointer; text-decoration: none; font-weight: normal; margin: 0; line-height: 1.3;">
                                {{ $product->meta_title ?: ($product->name . ' | RohidaFarm') }}
                            </h5>
                            <p class="google-desc text-muted m-0" id="googleDescPreview" style="font-size: 0.875rem; color: #4d5156; line-height: 1.5;">
                                {{ $product->meta_description ?: ($product->short_description ? strip_tags($product->short_description) : 'No SEO description set. Edit the fields above to configure your search result snippet.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ═══ RIGHT SIDEBAR ═══ --}}
        <div class="col-lg-4">

            {{-- Update Panel --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-send text-success me-2"></i>Update</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2" style="font-size:0.72rem;">
                        <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i> Published
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="text-muted mb-3" style="font-size:0.82rem;">
                        <i class="bi bi-clock me-1"></i> Last updated: {{ $product->updated_at->format('d M Y') }}
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold mb-2">
                        <i class="bi bi-check-lg me-1"></i> Update Product
                    </button>
                    <a href="{{ route('shop.show', $product->slug) }}" target="_blank" class="btn btn-outline-success w-100 rounded-pill py-2 mb-2" style="font-size:0.85rem;">
                        <i class="bi bi-eye me-1"></i> View Live Page
                    </a>
                    <button type="button" class="btn btn-outline-danger w-100 rounded-pill py-2" style="font-size:0.82rem;"
                            onclick="if(confirm('Permanently delete this product?')) { document.getElementById('deleteProductForm').submit(); }">
                        <i class="bi bi-trash me-1"></i> Delete Product
                    </button>
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
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Subcategory</label>
                        <select name="sub_category_id" class="form-select border">
                            <option value="">— None —</option>
                            @foreach($subcategories as $sub)
                                <option value="{{ $sub->id }}" {{ $product->sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Brand</label>
                        <select name="brand_id" class="form-select border">
                            <option value="">— None —</option>
                            @foreach($brands as $br)
                                <option value="{{ $br->id }}" {{ $product->brand_id == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
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
                            <option value="all" {{ (is_array(old('display_coupons', $product->display_coupons)) && in_array('all', old('display_coupons', $product->display_coupons ?? []))) ? 'selected' : '' }}>ALL Active Coupons</option>
                            @foreach($coupons as $coupon)
                                <option value="{{ $coupon->code }}" {{ (is_array(old('display_coupons', $product->display_coupons)) && in_array($coupon->code, old('display_coupons', $product->display_coupons ?? []))) ? 'selected' : '' }}>{{ $coupon->code }}</option>
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
                               value="{{ old('mrp', $product->mrp) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Sale Price (₹) *</label>
                        <input type="number" step="0.01" name="sale_price" class="form-control border p-2"
                               value="{{ old('sale_price', $product->sale_price) }}" required>
                        <div class="text-success fw-semibold mt-1" id="discountBadge" style="font-size:0.78rem;">
                            @if($product->mrp > 0 && $product->sale_price < $product->mrp)
                                {{ round(($product->mrp - $product->sale_price) / $product->mrp * 100) }}% off from MRP
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Base Stock *</label>
                        <input type="number" name="stock" class="form-control border p-2"
                               value="{{ old('stock', $product->stock) }}" required>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold text-dark" style="font-size:0.85rem;">Free Shipping Unlock Target (₹)</label>
                        <input type="number" step="0.01" name="free_shipping_threshold" class="form-control border p-2"
                               placeholder="e.g. 500" value="{{ old('free_shipping_threshold', $product->free_shipping_threshold) }}">
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
                           value="{{ $product->primaryImage ? $product->primaryImage->image_path : '' }}"
                           placeholder="/uploads/products/image.jpg or URL">
                    <div id="imagePreviewContainer">
                        @if($product->primaryImage)
                            <img src="{{ asset($product->primaryImage->image_path) }}"
                                 class="img-fluid rounded-3 border" style="max-height:140px;object-fit:cover;width:100%;">
                        @endif
                    </div>
                </div>
            </div>

            {{-- Product Labels --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-flag text-success me-2"></i>Product Labels</h6>
                </div>
                <div class="card-body p-4">
                    @foreach([
                        ['is_featured',    'is_featured',    'Feature on Home Page',    $product->is_featured],
                        ['is_organic',     'is_organic',     '100% Organic Certified',  $product->is_organic],
                        ['is_bilona',      'is_bilona',      'Bilona Method Churned',   $product->is_bilona],
                        ['is_best_seller', 'is_best_seller', 'Best Seller Badge',       $product->is_best_seller],
                        ['is_trending',    'is_trending',    'Trending Badge',          $product->is_trending],
                        ['is_new_arrival', 'is_new_arrival', 'New Arrival Badge',       $product->is_new_arrival],
                    ] as [$name, $id, $label, $val])
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="{{ $name }}" id="{{ $id }}" value="1" {{ $val ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="{{ $id }}" style="font-size:0.85rem;">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Real Reviews Summary ── --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white rounded-top-4 px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark m-0"><i class="bi bi-star text-warning me-2"></i>Reviews Summary</h6>
                    <a href="{{ route('admin.reviews.index', ['product_id' => $product->id]) }}" class="text-success btn btn-sm btn-outline-success rounded-pill px-3" style="font-size:0.78rem;">
                        Manage All
                    </a>
                </div>
                <div class="card-body p-4">
                    {{-- Average Rating --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="display-6 fw-bold text-dark font-heading">{{ $avgRating ? number_format($avgRating, 1) : '–' }}</span>
                        <div>
                            <div class="text-warning" style="font-size:0.9rem;">
                                @for($i=1;$i<=5;$i++)
                                    <i class="bi bi-star{{ $i <= round($avgRating ?? 0) ? '-fill' : ($i - 0.5 <= ($avgRating ?? 0) ? '-half' : '') }}"></i>
                                @endfor
                            </div>
                            <div class="text-muted" style="font-size:0.78rem;">Based on approved reviews</div>
                        </div>
                    </div>
                    {{-- Stats Grid --}}
                    <div class="row g-2 text-center mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-success-subtle">
                                <div class="fw-bold text-success fs-5">{{ $approvedReviewsCount }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">Approved</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 bg-warning-subtle">
                                <div class="fw-bold text-warning fs-5">{{ $pendingReviewsCount }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">Pending</div>
                            </div>
                        </div>
                    </div>
                    @if($pendingReviewsCount > 0)
                        <a href="{{ route('admin.reviews.index', ['product_id' => $product->id, 'status' => 'pending']) }}"
                           class="btn btn-warning btn-sm rounded-pill w-100">
                            <i class="bi bi-hourglass-split me-1"></i> Review {{ $pendingReviewsCount }} Pending
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>

</form>

{{-- Separate Delete Form --}}
<form id="deleteProductForm" action="{{ route('admin.products.delete', $product->id) }}" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
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
                googleTitlePreview.textContent = this.value.trim() || '{{ $product->name }} | RohidaFarm';
            });
        }

        if (seoDescInput && googleDescPreview) {
            seoDescInput.addEventListener('input', function() {
                googleDescPreview.textContent = this.value.trim() || 'No SEO description set. Edit the fields above to configure your search result snippet.';
            });
        }

        if (typeof window.initMediaPicker === 'function') {
            window.initMediaPicker('#infographicMediaInput', '#infographicPreview', 'image');
        }
    });

    function previewInfographicFiles(input) {
        const container = document.getElementById('infographicGrid');
        const emptyMsg = document.getElementById('emptyInfographicsMsg');
        if (emptyMsg) emptyMsg.remove();
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    container.insertAdjacentHTML('beforeend', `
                        <div class="position-relative infographic-card-item">
                            <img src="${e.target.result}" class="rounded-3 border" style="height: 120px; width: 90px; object-fit: cover; border: 2px solid #5C3D2E;">
                            <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center"
                                    style="width: 22px; height: 22px; font-size: 12px; line-height: 1;"
                                    onclick="this.closest('.infographic-card-item').remove()"
                                    title="Remove image">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    `);
                };
                reader.readAsDataURL(file);
            });
        }
    }
</script>
@endpush
