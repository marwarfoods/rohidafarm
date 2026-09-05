@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-tags text-success me-2"></i>Taxonomy Management</h1>
</div>

<div class="row g-4">
    <!-- Forms Column (Left) -->
    <div class="col-md-5">
        <!-- Create Unified Category Form -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Add New Category</h4>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Name *</label>
                    <input type="text" name="name" class="form-control bg-light border p-2" placeholder="e.g. Cold Pressed Oils" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Parent Category</label>
                    <select name="parent_id" id="parentCategorySelect" class="form-select bg-light border p-2">
                        <option value="">None (Parent Category)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Assign a parent category to make this a subcategory.</small>
                </div>

                <!-- Custom Icon Picker -->
                <div class="mb-3" id="iconSelectorWrapper">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Icon</label>
                    <input type="text" name="icon" id="categoryIconInput" class="form-control bg-light" value="bi-tags" readonly>
                    <div class="mt-2" id="selectedIconDisplay">
                        <i class="bi bi-tags text-success fs-5 me-2"></i> Selected Preview
                    </div>
                </div>

                <!-- Category Image (Media Gallery Picker) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Image</label>
                    <div class="row g-2">
                        <div class="col-8">
                            <input type="text" name="image" id="categoryImageInput" class="form-control bg-light border p-2 media-picker-input"
                                   placeholder="Choose from media gallery...">
                        </div>
                        <div class="col-4">
                            <div id="categoryImagePreview" class="border rounded-3 p-1 bg-light text-center d-flex align-items-center justify-content-center" style="height: 42px;">
                                <span class="text-muted" style="font-size: 0.68rem;">No image</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Banner (Full Width Image for Shop Category Page) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Banner (Full Width)</label>
                    <input type="text" name="banner_image" id="categoryBannerInput" class="form-control bg-light border p-2 media-picker-input mb-2"
                           placeholder="Choose from media gallery...">
                    <div id="categoryBannerPreview" class="border rounded-3 p-1 bg-light text-center d-flex align-items-center justify-content-center overflow-hidden" style="height: 90px;">
                        <span class="text-muted" style="font-size: 0.75rem;">No banner selected</span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Shown at 100% width × 400px height (cropped to fit) on the category page — recommended size 1600×400px.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Description</label>
                    <textarea name="description" class="form-control bg-light border p-2" rows="3" placeholder="Brief details..."></textarea>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Save Category</button>
            </form>
        </div>
    </div>

    <!-- List Column (Right) -->
    <div class="col-md-7">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">Categories & Subcategories</h4>
            
            <div class="accordion border-0" id="categoriesAccordion">
                @forelse($categories as $cat)
                    <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden bg-light" style="border: 1px solid var(--border-color) !important;">
                        <h2 class="accordion-header" id="headingCat{{ $cat->id }}">
                            <div class="accordion-button collapsed d-flex align-items-center justify-content-between p-3 bg-white border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat{{ $cat->id }}" aria-expanded="false" aria-controls="collapseCat{{ $cat->id }}">
                                <!-- Left: Icon, Name & Product Count -->
                                <div class="d-flex align-items-center gap-2 flex-grow-1 text-start">
                                    @if($cat->image)
                                        <img src="{{ asset($cat->image) }}" alt="{{ $cat->name }}" class="rounded-3 border me-2" style="width: 38px; height: 38px; object-fit: cover; flex-shrink: 0;" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.classList.remove('d-none');">
                                        <i class="bi {{ $cat->icon ?? 'bi-tags' }} text-success fs-4 me-2 d-none"></i>
                                    @else
                                        <i class="bi {{ $cat->icon ?? 'bi-tags' }} text-success fs-4 me-2"></i>
                                    @endif
                                    <div>
                                        <span class="fw-bold text-dark fs-5 d-block font-heading" style="line-height: 1.2;">{{ $cat->name }}</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill mt-1" style="font-size: 0.72rem; font-weight: 600;">
                                            {{ $cat->products_count }} {{ Str::plural('Product', $cat->products_count) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Right: FAQ, Edit, Delete, Chevron -->
                                <div class="d-flex align-items-center gap-2 me-3" onclick="event.stopPropagation();">
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0 faq-cat-btn" 
                                            data-id="{{ $cat->id }}" 
                                            data-name="{{ $cat->name }}" 
                                            title="Manage Category FAQs">
                                        <i class="bi bi-question-circle fs-6"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success border-0 edit-cat-btn" 
                                            data-id="{{ $cat->id }}" 
                                            data-name="{{ $cat->name }}" 
                                            data-description="{{ $cat->description }}" 
                                            data-icon="{{ $cat->icon ?? 'bi-tags' }}"
                                            data-image="{{ $cat->image }}"
                                            data-banner-image="{{ $cat->banner_image }}"
                                            title="Edit Category">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.delete', $cat->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? All subcategories will also be affected.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Delete Category">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <i class="bi bi-chevron-down text-muted ms-2 transition-transform duration-200"></i>
                                </div>
                            </div>
                        </h2>
                        
                        <div id="collapseCat{{ $cat->id }}" class="accordion-collapse collapse" aria-labelledby="headingCat{{ $cat->id }}" data-bs-parent="#categoriesAccordion">
                            <div class="accordion-body bg-white border-top p-3">
                                @if($cat->description)
                                    <p class="text-muted mb-3" style="font-size: 0.88rem;">{{ $cat->description }}</p>
                                @endif
                                
                                <h6 class="text-muted text-uppercase fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">Subcategories:</h6>
                                
                                @if($cat->subCategories->isNotEmpty())
                                    <ul class="list-group list-group-flush border-0">
                                        @foreach($cat->subCategories as $sub)
                                            <li class="list-group-item py-2 px-0 bg-transparent d-flex justify-content-between align-items-center border-0">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-arrow-return-right text-success me-2" style="font-size: 0.85rem;"></i>
                                                    <div>
                                                        <span class="text-dark fw-medium" style="font-size: 0.92rem;">{{ $sub->name }}</span>
                                                        <span class="badge bg-light text-muted border rounded-pill ms-2" style="font-size: 0.68rem; font-weight: 600;">
                                                            {{ $sub->products_count }} {{ Str::plural('Product', $sub->products_count) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-success border-0 edit-sub-btn py-0 px-1" 
                                                            data-id="{{ $sub->id }}" 
                                                            data-name="{{ $sub->name }}" 
                                                            data-description="{{ $sub->description }}" 
                                                            data-category="{{ $cat->id }}"
                                                            title="Edit Subcategory">
                                                        <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                                    </button>
                                                    <form action="{{ route('admin.categories.subcategory.delete', $sub->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subcategory?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 py-0 px-1" title="Delete Subcategory">
                                                            <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted" style="font-size: 0.82rem;">No subcategories.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5 bg-white rounded-4 border">
                        <i class="bi bi-tags display-4 text-muted mb-2 d-block"></i>
                        No categories exist yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ── Edit Category Modal ── -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel"><i class="bi bi-pencil-square text-success me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Name *</label>
                        <input type="text" name="name" id="editCatName" class="form-control bg-light border p-2" required>
                    </div>

                    <!-- Custom Icon Picker inside modal -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Icon</label>
                        <input type="text" name="icon" id="editCatIconInput" class="form-control bg-light" value="bi-tags" readonly>
                        <div class="mt-2" id="editSelectedIconDisplay">
                            <i class="bi bi-tags text-success fs-5 me-2"></i> Selected Preview
                        </div>
                    </div>

                    <!-- Category Image (Media Gallery Picker) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Image</label>
                        <div class="row g-2">
                            <div class="col-8">
                                <input type="text" name="image" id="editCatImageInput" class="form-control bg-light border p-2 media-picker-input"
                                       placeholder="Choose from media gallery...">
                            </div>
                            <div class="col-4">
                                <div id="editCatImagePreview" class="border rounded-3 p-1 bg-light text-center d-flex align-items-center justify-content-center" style="height: 42px;">
                                    <span class="text-muted" style="font-size: 0.68rem;">No image</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Banner (Full Width Image for Shop Category Page) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Banner (Full Width)</label>
                        <input type="text" name="banner_image" id="editCatBannerInput" class="form-control bg-light border p-2 media-picker-input mb-2"
                               placeholder="Choose from media gallery...">
                        <div id="editCatBannerPreview" class="border rounded-3 p-1 bg-light text-center d-flex align-items-center justify-content-center overflow-hidden" style="height: 90px;">
                            <span class="text-muted" style="font-size: 0.75rem;">No banner selected</span>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Shown at 100% width × 400px height (cropped to fit) on the category page — recommended size 1600×400px.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Description</label>
                        <textarea name="description" id="editCatDescription" class="form-control bg-light border p-2" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 bg-success text-white border-0">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Edit Subcategory Modal ── -->
<div class="modal fade" id="editSubcategoryModal" tabindex="-1" aria-labelledby="editSubcategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="editSubcategoryModalLabel"><i class="bi bi-pencil-square text-success me-2"></i>Edit Subcategory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSubcategoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Subcategory Name *</label>
                        <input type="text" name="name" id="editSubName" class="form-control bg-light border p-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Parent Category *</label>
                        <select name="category_id" id="editSubParentId" class="form-select bg-light border p-2" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Description</label>
                        <textarea name="description" id="editSubDescription" class="form-control bg-light border p-2" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 bg-success text-white border-0">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Manage Category FAQs Modal ── -->
<div class="modal fade" id="categoryFaqsModal" tabindex="-1" aria-labelledby="categoryFaqsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom px-4 py-3">
                <div>
                    <h5 class="modal-title fw-bold text-dark font-heading m-0" id="categoryFaqsModalLabel">
                        <i class="bi bi-question-circle-fill text-success me-2"></i>Manage Category FAQs
                    </h5>
                    <small class="text-muted" id="categoryFaqsCategoryName">Configure FAQs for category</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryFaqsForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div id="categoryFaqsLoading" class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2">Loading FAQs...</p>
                    </div>

                    <div id="categoryFaqsBody" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small">FAQs will appear on this Category's page on the store.</span>
                            <button type="button" class="btn btn-outline-success btn-sm rounded-pill font-heading fw-semibold" id="btnAddCategoryFaq">
                                <i class="bi bi-plus-lg me-1"></i>Add FAQ Question
                            </button>
                        </div>

                        <div id="categoryFaqsContainer" class="d-flex flex-column gap-3" style="max-height: 55vh; overflow-y: auto;">
                            <!-- Dynamic FAQ Items -->
                        </div>

                        <div id="categoryFaqsEmpty" class="text-center py-4 border rounded-3 bg-light d-none">
                            <i class="bi bi-question-circle text-muted fs-3 d-block mb-1"></i>
                            <p class="text-muted small m-0">No FAQs added for this category yet.</p>
                            <button type="button" class="btn btn-success btn-sm rounded-pill mt-2" onclick="document.getElementById('btnAddCategoryFaq').click()">
                                <i class="bi bi-plus-lg me-1"></i>Add First FAQ
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 bg-success text-white border-0" id="btnSaveCategoryFaqs">
                        <i class="bi bi-check2-circle me-1"></i>Save FAQs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Accordion styling overrides */
.accordion-button::after {
    display: none !important; /* Hide bootstrap default chevron */
}
.accordion-button:not(.collapsed) .bi-chevron-down {
    transform: rotate(180deg);
}
.accordion-button .bi-chevron-down {
    transition: transform 0.2s ease-in-out;
}
.cat-faq-item-card {
    border: 1px solid #ECE7DD;
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    transition: all 0.2s ease;
}
.cat-faq-item-card:hover {
    border-color: #248443;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Global Icon Pickers
    if (window.initIconPicker) {
        window.initIconPicker('#categoryIconInput', '#selectedIconDisplay');
        window.initIconPicker('#editCatIconInput', '#editSelectedIconDisplay');
    }

    // Initialize Media Gallery Pickers for Category Images
    if (window.initMediaPicker) {
        window.initMediaPicker('#categoryImageInput', '#categoryImagePreview', 'image');
        window.initMediaPicker('#editCatImageInput', '#editCatImagePreview', 'image');
        window.initMediaPicker('#categoryBannerInput', '#categoryBannerPreview', 'image');
        window.initMediaPicker('#editCatBannerInput', '#editCatBannerPreview', 'image');
    }

    // ── Create category logic ──
    const parentSelect = document.getElementById('parentCategorySelect');
    const iconWrapper = document.getElementById('iconSelectorWrapper');

    if (parentSelect) {
        parentSelect.addEventListener('change', function() {
            if (this.value) {
                iconWrapper.classList.add('d-none');
            } else {
                iconWrapper.classList.remove('d-none');
            }
        });
    }

    // ── Edit Category Modal Logic ──
    const editCatButtons = document.querySelectorAll('.edit-cat-btn');
    const editCatForm = document.getElementById('editCategoryForm');
    const editCatName = document.getElementById('editCatName');
    const editCatDescription = document.getElementById('editCatDescription');
    const editCatIconInput = document.getElementById('editCatIconInput');
    const editSelectedIconDisplay = document.getElementById('editSelectedIconDisplay');
    const editCatImageInput = document.getElementById('editCatImageInput');
    const editCatImagePreview = document.getElementById('editCatImagePreview');
    const editCatBannerInput = document.getElementById('editCatBannerInput');
    const editCatBannerPreview = document.getElementById('editCatBannerPreview');
    const editCatModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));

    editCatButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-description');
            const icon = this.getAttribute('data-icon') || 'bi-tags';
            const image = this.getAttribute('data-image') || '';
            const bannerImage = this.getAttribute('data-banner-image') || '';

            editCatForm.action = `/admin/categories/${id}/update`;
            editCatName.value = name;
            editCatDescription.value = desc || '';
            editCatIconInput.value = icon;

            editSelectedIconDisplay.innerHTML = `<i class="bi ${icon} text-success me-2 fs-5"></i> Selected Preview`;

            editCatImageInput.value = image;
            if (image) {
                const previewSrc = image.startsWith('http') ? image : (window.location.origin + '/' + image.replace(/^\//, ''));
                editCatImagePreview.innerHTML = `<img src="${previewSrc}" class="rounded-3 img-fluid border" style="max-height: 40px; object-fit: cover;">`;
            } else {
                editCatImagePreview.innerHTML = `<span class="text-muted" style="font-size: 0.68rem;">No image</span>`;
            }

            editCatBannerInput.value = bannerImage;
            if (bannerImage) {
                const bannerPreviewSrc = bannerImage.startsWith('http') ? bannerImage : (window.location.origin + '/' + bannerImage.replace(/^\//, ''));
                editCatBannerPreview.innerHTML = `<img src="${bannerPreviewSrc}" class="img-fluid" style="max-height: 88px; object-fit: cover;">`;
            } else {
                editCatBannerPreview.innerHTML = `<span class="text-muted" style="font-size: 0.75rem;">No banner selected</span>`;
            }

            editCatModal.show();
        });
    });

    // ── Edit Subcategory Modal Logic ──
    const editSubButtons = document.querySelectorAll('.edit-sub-btn');
    const editSubForm = document.getElementById('editSubcategoryForm');
    const editSubName = document.getElementById('editSubName');
    const editSubDescription = document.getElementById('editSubDescription');
    const editSubParentId = document.getElementById('editSubParentId');
    const editSubModal = new bootstrap.Modal(document.getElementById('editSubcategoryModal'));

    editSubButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-description');
            const catId = this.getAttribute('data-category');

            editSubForm.action = `/admin/categories/subcategory/${id}/update`;
            editSubName.value = name;
            editSubDescription.value = desc || '';
            editSubParentId.value = catId;

            editSubModal.show();
        });
    });

    // ── Manage Category FAQs Modal Logic ──
    const categoryFaqsModalEl = document.getElementById('categoryFaqsModal');
    const categoryFaqsModal = new bootstrap.Modal(categoryFaqsModalEl);
    const categoryFaqsForm = document.getElementById('categoryFaqsForm');
    const categoryFaqsContainer = document.getElementById('categoryFaqsContainer');
    const categoryFaqsLoading = document.getElementById('categoryFaqsLoading');
    const categoryFaqsBody = document.getElementById('categoryFaqsBody');
    const categoryFaqsEmpty = document.getElementById('categoryFaqsEmpty');
    const categoryFaqsCategoryName = document.getElementById('categoryFaqsCategoryName');
    const btnAddCategoryFaq = document.getElementById('btnAddCategoryFaq');
    const btnSaveCategoryFaqs = document.getElementById('btnSaveCategoryFaqs');

    let currentCategoryId = null;

    // Initialize Sortable on category FAQs container
    if (categoryFaqsContainer && typeof Sortable !== 'undefined') {
        Sortable.create(categoryFaqsContainer, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                reindexFaqRows();
            }
        });
    }

    function reindexFaqRows() {
        const rows = categoryFaqsContainer.querySelectorAll('.cat-faq-item-card');
        rows.forEach((row, index) => {
            const qInput = row.querySelector('.faq-question-input');
            const aInput = row.querySelector('.faq-answer-input');
            const numBadge = row.querySelector('.faq-num-badge');
            if (qInput) qInput.name = `faqs[${index}][question]`;
            if (aInput) aInput.name = `faqs[${index}][answer]`;
            if (numBadge) numBadge.textContent = `#${index + 1}`;
        });
        if (rows.length === 0) {
            categoryFaqsEmpty.classList.remove('d-none');
        } else {
            categoryFaqsEmpty.classList.add('d-none');
        }
    }

    function createFaqRow(question = '', answer = '', index = 0) {
        const card = document.createElement('div');
        card.className = 'cat-faq-item-card position-relative';
        card.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="drag-handle text-muted" style="cursor: grab;" title="Drag to reorder"><i class="bi bi-grip-vertical"></i></span>
                    <span class="badge bg-light text-dark border faq-num-badge font-heading">#${index + 1}</span>
                    <strong class="text-dark small">FAQ Item</strong>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center btn-remove-cat-faq" style="width: 26px; height: 26px;" title="Remove FAQ">
                    <i class="bi bi-trash" style="font-size: 0.75rem;"></i>
                </button>
            </div>
            <div class="mb-2">
                <label class="form-label text-muted small mb-1 fw-semibold">Question</label>
                <input type="text" name="faqs[${index}][question]" class="form-control form-control-sm bg-light border faq-question-input" value="${escapeHtml(question)}" placeholder="e.g. Is this Ghee prepared via Vedic Bilona method?" required>
            </div>
            <div>
                <label class="form-label text-muted small mb-1 fw-semibold">Answer</label>
                <textarea name="faqs[${index}][answer]" class="form-control form-control-sm bg-light border faq-answer-input" rows="2" placeholder="Write a clear, helpful answer..." required>${escapeHtml(answer)}</textarea>
            </div>
        `;
        card.querySelector('.btn-remove-cat-faq').addEventListener('click', function() {
            card.remove();
            reindexFaqRows();
        });
        return card;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    document.querySelectorAll('.faq-cat-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            currentCategoryId = this.getAttribute('data-id');
            const catName = this.getAttribute('data-name');
            categoryFaqsCategoryName.textContent = `Configure FAQs for ${catName}`;
            categoryFaqsForm.action = `/admin/categories/${currentCategoryId}/faqs`;

            // Reset & show loading
            categoryFaqsLoading.classList.remove('d-none');
            categoryFaqsBody.classList.add('d-none');
            categoryFaqsContainer.innerHTML = '';
            categoryFaqsModal.show();

            // Fetch existing FAQs
            fetch(`/admin/categories/${currentCategoryId}/faqs`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                categoryFaqsLoading.classList.add('d-none');
                categoryFaqsBody.classList.remove('d-none');
                if (data.faqs && data.faqs.length > 0) {
                    data.faqs.forEach((faq, idx) => {
                        categoryFaqsContainer.appendChild(createFaqRow(faq.question, faq.answer, idx));
                    });
                    categoryFaqsEmpty.classList.add('d-none');
                } else {
                    categoryFaqsEmpty.classList.remove('d-none');
                }
            })
            .catch(err => {
                categoryFaqsLoading.classList.add('d-none');
                categoryFaqsBody.classList.remove('d-none');
                categoryFaqsEmpty.classList.remove('d-none');
            });
        });
    });

    if (btnAddCategoryFaq) {
        btnAddCategoryFaq.addEventListener('click', function() {
            const currentCount = categoryFaqsContainer.querySelectorAll('.cat-faq-item-card').length;
            const newRow = createFaqRow('', '', currentCount);
            categoryFaqsContainer.appendChild(newRow);
            categoryFaqsEmpty.classList.add('d-none');
            newRow.querySelector('.faq-question-input').focus();
        });
    }

    if (categoryFaqsForm) {
        categoryFaqsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            btnSaveCategoryFaqs.disabled = true;
            btnSaveCategoryFaqs.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnSaveCategoryFaqs.disabled = false;
                btnSaveCategoryFaqs.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Save FAQs';
                if (data.success) {
                    categoryFaqsModal.hide();
                    alert(data.message || 'Category FAQs updated successfully!');
                }
            })
            .catch(err => {
                btnSaveCategoryFaqs.disabled = false;
                btnSaveCategoryFaqs.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Save FAQs';
                alert('Error saving FAQs. Please try again.');
            });
        });
    }
});
</script>
@endsection
