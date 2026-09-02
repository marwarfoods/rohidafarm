/**
 * Admin Product Create & Edit Logic
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');
    if (!form) return;

    // Read initial counts from dataset to support edit mode dynamically
    let galleryIndex = parseInt(form.dataset.galleryCount || '0');
    let variantIndex = parseInt(form.dataset.variantCount || '0');

    // 1. Initialize Choices.js for Coupons select
    const couponsSelect = document.getElementById('displayCouponsSelect');
    if (couponsSelect && typeof Choices !== 'undefined') {
        new Choices(couponsSelect, {
            removeItemButton: true,
            searchEnabled: true,
            placeholderValue: 'Select coupons to show...',
            itemSelectText: ''
        });
    }

    // 2. Initialize Media Picker
    if (window.initMediaPicker) {
        initMediaPicker('#productImageInput', '#imagePreviewContainer', 'image', false);
        initMediaPicker('#gallerySelectorInput', null, 'image', true);
    }

    // 3. Initialize CKEditor
    let editors = {};
    const editorIds = ['ShortDescription', 'Description', 'Benefits', 'Ingredients'];
    editorIds.forEach(key => {
        const el = document.querySelector(`#editor${key}`);
        if (el && typeof ClassicEditor !== 'undefined') {
            ClassicEditor.create(el)
                .then(editor => { editors[key] = editor; })
                .catch(err => console.error(err));
        }
    });

    // Sync CKEditor content on form submit
    form.addEventListener('submit', function() {
        editorIds.forEach(key => {
            const el = document.getElementById(`editor${key}`);
            if (el && editors[key]) {
                el.value = editors[key].getData();
            }
        });
        refreshGallerySortOrders();
        refreshFaqSortOrders();
    });

    // 4. MRP & Sale Price Discount Badge
    const mrpEl  = document.querySelector('[name="mrp"]');
    const saleEl = document.querySelector('[name="sale_price"]');
    const badge  = document.getElementById('discountBadge');
    
    function updateDiscount() {
        if (!mrpEl || !saleEl || !badge) return;
        const mrp = parseFloat(mrpEl.value) || 0;
        const sale = parseFloat(saleEl.value) || 0;
        badge.textContent = (mrp > 0 && sale > 0 && sale < mrp)
            ? Math.round((mrp - sale) / mrp * 100) + '% off from MRP' : '';
    }
    
    if (mrpEl && saleEl) {
        mrpEl.addEventListener('input', updateDiscount);
        saleEl.addEventListener('input', updateDiscount);
        updateDiscount(); // run once on load
    }

    // 5. Weight Variants & Variant Images
    const btnAddVariant = document.getElementById('btnAddVariant');
    const variantsContainer = document.getElementById('variantsContainer');

    // Reusable hidden input for variant gallery picker
    let variantGalleryPickerInput = document.getElementById('variantGallerySelectorInput');
    if (!variantGalleryPickerInput) {
        variantGalleryPickerInput = document.createElement('input');
        variantGalleryPickerInput.type = 'text';
        variantGalleryPickerInput.id = 'variantGallerySelectorInput';
        variantGalleryPickerInput.style.display = 'none';
        document.body.appendChild(variantGalleryPickerInput);
        if (window.initMediaPicker) {
            initMediaPicker('#variantGallerySelectorInput', null, 'image');
        }
    }

    let activeVariantGalleryTarget = null;
    let activeVariantIndex = null;

    if (variantGalleryPickerInput) {
        variantGalleryPickerInput.addEventListener('change', function() {
            const fp = this.value;
            if (!fp || !activeVariantGalleryTarget) return;

            const chip = document.createElement('div');
            chip.className = 'position-relative variant-gallery-chip';
            chip.innerHTML = `
                <input type="hidden" name="variants[${activeVariantIndex}][gallery_images][]" value="${fp}">
                <img src="${fp.startsWith('http') || fp.startsWith('/') ? fp : '/' + fp}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px; transform: translate(30%, -30%);">
                    <i class="bi bi-x"></i>
                </button>
            `;
            chip.querySelector('button').addEventListener('click', function() {
                const container = this.closest('.variant-gallery-chips-container');
                chip.remove();
                if (container && !container.querySelectorAll('.variant-gallery-chip').length) {
                    container.querySelector('.empty-variant-gallery-msg')?.classList.remove('d-none');
                }
            });

            const emptyMsg = activeVariantGalleryTarget.querySelector('.empty-variant-gallery-msg');
            if (emptyMsg) emptyMsg.classList.add('d-none');

            activeVariantGalleryTarget.appendChild(chip);
            this.value = '';
        });
    }

    function wireVariantCard(card, idx) {
        const weightInput = card.querySelector('.variant-weight-input');
        const priceInput = card.querySelector('.variant-price-input');
        const weightBadge = card.querySelector('.variant-weight-badge');
        const pricePreview = card.querySelector('.variant-price-preview');
        const mainImgInput = card.querySelector('.variant-main-img-input');
        const mainImgPreview = card.querySelector('.variant-main-img-preview');
        const headerThumbContainer = card.querySelector('.variant-header-thumb-container');

        if (weightInput && weightBadge) {
            weightInput.addEventListener('input', function() {
                weightBadge.textContent = this.value.trim() || `Variant #${idx + 1}`;
            });
        }

        if (priceInput && pricePreview) {
            priceInput.addEventListener('input', function() {
                const val = parseFloat(this.value) || 0;
                pricePreview.textContent = `₹${val.toLocaleString('en-IN')}`;
            });
        }

        if (mainImgInput) {
            if (window.initMediaPicker && !mainImgInput.parentNode.classList.contains('media-picker-group')) {
                initMediaPicker(mainImgInput, null, 'image');
            }
            mainImgInput.addEventListener('change', function() {
                const val = this.value.trim();
                const src = val ? (val.startsWith('http') || val.startsWith('/') ? val : '/' + val) : '';
                if (src) {
                    if (mainImgPreview) mainImgPreview.innerHTML = `<img src="${src}" class="w-100 h-100 object-fit-cover">`;
                    if (headerThumbContainer) headerThumbContainer.innerHTML = `<img src="${src}" class="rounded border variant-header-thumb" style="width: 28px; height: 28px; object-fit: cover;">`;
                } else {
                    if (mainImgPreview) mainImgPreview.innerHTML = `<i class="bi bi-image text-muted" style="font-size: 1.3rem;"></i>`;
                    if (headerThumbContainer) headerThumbContainer.innerHTML = '';
                }
            });
        }

        const btnAddGal = card.querySelector('.btn-add-variant-gallery');
        const galContainer = card.querySelector('.variant-gallery-chips-container');
        if (btnAddGal && galContainer) {
            btnAddGal.addEventListener('click', function(e) {
                e.preventDefault();
                const variantIndexValue = card.dataset.index || idx;
                
                if (window.openMediaPicker) {
                    window.openMediaPicker({
                        type: 'image',
                        isMultiSelect: true,
                        onSelect: function(selectedItems) {
                            if (!selectedItems || !selectedItems.length) return;
                            selectedItems.forEach(item => {
                                const fp = item.path;
                                const chip = document.createElement('div');
                                chip.className = 'position-relative variant-gallery-chip';
                                chip.innerHTML = `
                                    <input type="hidden" name="variants[${variantIndexValue}][gallery_images][]" value="${fp}">
                                    <img src="${fp.startsWith('http') || fp.startsWith('/') ? fp : '/' + fp}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px; transform: translate(30%, -30%);">
                                        <i class="bi bi-x"></i>
                                    </button>
                                `;
                                chip.querySelector('button').addEventListener('click', function() {
                                    const container = this.closest('.variant-gallery-chips-container');
                                    chip.remove();
                                    if (container && !container.querySelectorAll('.variant-gallery-chip').length) {
                                        container.querySelector('.empty-variant-gallery-msg')?.classList.remove('d-none');
                                    }
                                });

                                const emptyMsg = galContainer.querySelector('.empty-variant-gallery-msg');
                                if (emptyMsg) emptyMsg.classList.add('d-none');

                                galContainer.appendChild(chip);
                            });
                        }
                    });
                }
            });
        }

        // Wire existing chips delete button
        card.querySelectorAll('.variant-gallery-chip button').forEach(btn => {
            btn.addEventListener('click', function() {
                const chip = this.closest('.variant-gallery-chip');
                const container = this.closest('.variant-gallery-chips-container');
                if (chip) chip.remove();
                if (container && !container.querySelectorAll('.variant-gallery-chip').length) {
                    container.querySelector('.empty-variant-gallery-msg')?.classList.remove('d-none');
                }
            });
        });

        const removeBtn = card.querySelector('.btn-remove-variant');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                card.remove();
                if (variantsContainer && !variantsContainer.querySelectorAll('.variant-item').length) {
                    const empty = document.createElement('div');
                    empty.id = 'emptyVariantRow';
                    empty.className = 'text-center py-4 text-muted';
                    empty.style.fontSize = '0.85rem';
                    empty.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Click "Add Variant" to configure size/weight options and custom images.';
                    variantsContainer.appendChild(empty);
                }
            });
        }
    }

    if (variantsContainer) {
        // Wire all existing variants on page load
        variantsContainer.querySelectorAll('.variant-item').forEach((item, i) => {
            wireVariantCard(item, parseInt(item.dataset.index || i));
        });

        btnAddVariant?.addEventListener('click', () => {
            document.getElementById('emptyVariantRow')?.remove();
            const currIdx = variantIndex;
            const item = document.createElement('div');
            item.className = 'variant-item card border rounded-3 mb-3 bg-white shadow-sm overflow-hidden';
            item.dataset.index = currIdx;
            item.innerHTML = `
                {{-- Accordion Header --}}
                <div class="card-header bg-light px-3 py-2.5 d-flex justify-content-between align-items-center variant-header" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#variantCollapse_${currIdx}" aria-expanded="true">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-chevron-down variant-toggle-icon text-muted" style="transition: transform 0.2s;"></i>
                        <span class="badge bg-success font-heading variant-weight-badge">New Variant</span>
                        <span class="text-dark fw-bold variant-price-preview">₹0</span>
                        <div class="variant-header-thumb-container d-inline-block ms-1"></div>
                    </div>
                    <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation();">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5 btn-remove-variant" style="font-size: 0.75rem;">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>

                {{-- Accordion Body --}}
                <div id="variantCollapse_${currIdx}" class="collapse show">
                    <div class="card-body p-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Weight Option *</label>
                                <input type="text" name="variants[${currIdx}][weight]" class="form-control form-control-sm border variant-weight-input" required placeholder="e.g. 500ml">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">MRP (₹)</label>
                                <input type="number" step="0.01" name="variants[${currIdx}][mrp]" class="form-control form-control-sm border" placeholder="999">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Sale Price (₹) *</label>
                                <input type="number" step="0.01" name="variants[${currIdx}][sale_price]" class="form-control form-control-sm border variant-price-input" required placeholder="799">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Stock *</label>
                                <input type="number" name="variants[${currIdx}][stock]" class="form-control form-control-sm border" value="100">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Max Cart Qty</label>
                                <input type="number" name="variants[${currIdx}][max_cart_qty]" class="form-control form-control-sm border" placeholder="No limit">
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-3">
                                <div class="col-md-5 border-end">
                                    <label class="form-label text-dark fw-semibold mb-1 d-flex align-items-center gap-1" style="font-size:0.8rem;">
                                        <i class="bi bi-image text-success"></i> Variant Main Image
                                    </label>
                                    <p class="text-muted m-0 mb-2" style="font-size: 0.72rem;">Primary photo shown when this variant is selected.</p>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="variant-main-img-preview border rounded bg-white d-flex align-items-center justify-content-center overflow-hidden" style="width: 54px; height: 54px; flex-shrink: 0;">
                                            <i class="bi bi-image text-muted" style="font-size: 1.3rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="text" name="variants[${currIdx}][image_path]" class="form-control form-control-sm media-picker-input variant-main-img-input" placeholder="Pick main image...">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label text-dark fw-semibold m-0 d-flex align-items-center gap-1" style="font-size:0.8rem;">
                                            <i class="bi bi-images text-success"></i> Variant Gallery Images
                                        </label>
                                        <button type="button" class="btn btn-outline-success btn-sm py-0.5 px-2.5 btn-add-variant-gallery" data-index="${currIdx}" style="font-size: 0.72rem;">
                                            <i class="bi bi-plus-lg me-1"></i> Add Gallery Photo
                                        </button>
                                    </div>
                                    <p class="text-muted m-0 mb-2" style="font-size: 0.72rem;">Slides shown in product detail gallery when this variant is active.</p>
                                    <div class="d-flex flex-wrap gap-2 p-2 border rounded bg-white min-height-50 variant-gallery-chips-container">
                                        <span class="text-muted small align-self-center empty-variant-gallery-msg" style="font-size: 0.75rem;">
                                            No gallery photos added yet for this variant.
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            variantsContainer.appendChild(item);
            wireVariantCard(item, currIdx);
            variantIndex++;
        });
    }

    // 6. Gallery Drag-and-Drop (SortableJS)
    const galleryContainer = document.getElementById('galleryContainer');
    if (galleryContainer && typeof Sortable !== 'undefined') {
        Sortable.create(galleryContainer, {
            animation: 180,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            handle: '.card',
            onEnd: refreshGallerySortOrders
        });
    }

    function refreshGallerySortOrders() {
        if (!galleryContainer) return;
        const cards = galleryContainer.querySelectorAll('.gallery-item-card');
        cards.forEach((card, i) => {
            const imgPath = card.querySelector('.gallery-image-path');
            const vidPath = card.querySelector('.gallery-video-path');
            const sortInp = card.querySelector('.gallery-sort-order');
            if (imgPath) imgPath.name = `gallery[${i}][image_path]`;
            if (vidPath) vidPath.name = `gallery[${i}][video_path]`;
            if (sortInp) {
                sortInp.name = `gallery[${i}][sort_order]`;
                sortInp.value = i + 1;
            }
        });
    }

    // 7. Gallery Add & Remove Item
    const gallerySelector = document.getElementById('gallerySelectorInput');
    const btnOpenPicker = document.getElementById('btnOpenGalleryPicker');

    if (gallerySelector) {
        gallerySelector.addEventListener('change', function() {
            const val = this.value;
            if (!val) return;
            const paths = val.split(',');
            paths.forEach(fp => {
                if (fp && fp.trim()) appendGalleryItem(fp.trim());
            });
            this.value = '';
        });
    }

    if (btnOpenPicker) {
        btnOpenPicker.addEventListener('click', () => {
            if (window.openMediaPicker) {
                window.openMediaPicker({
                    type: 'image',
                    isMultiSelect: true,
                    onSelect: function(selectedItems) {
                        if (!selectedItems || !selectedItems.length) return;
                        selectedItems.forEach(item => {
                            if (item.path) appendGalleryItem(item.path);
                        });
                    }
                });
            } else {
                const input = document.getElementById('gallerySelectorInput');
                const wrapper = input?.closest('.media-picker-group');
                const btn = wrapper?.querySelector('button');
                if (btn) btn.click();
            }
        });
    }

    function appendGalleryItem(filePath, videoPath = '') {
        if (!galleryContainer) return;
        const emptyMsg = document.getElementById('galleryEmpty');
        if (emptyMsg) emptyMsg.remove();
        
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 gallery-item-card';
        col.innerHTML = `
            <div class="card border rounded-3 bg-white shadow-sm h-100" style="cursor:grab;">
                <div class="card-body p-2 text-center">
                    <div class="text-muted mb-1" style="font-size:0.75rem;"><i class="bi bi-grip-horizontal me-1"></i> Drag to reorder</div>
                    <img src="${filePath}" class="w-100 rounded-2 mb-2" style="height:90px;object-fit:cover;">
                    <input type="hidden" class="gallery-image-path" name="gallery[${galleryIndex}][image_path]" value="${filePath}">
                    <input type="hidden" class="gallery-sort-order" name="gallery[${galleryIndex}][sort_order]" value="${galleryIndex + 1}">
                    <input type="text" class="gallery-video-path form-control form-control-sm border mb-2"
                           name="gallery[${galleryIndex}][video_path]" placeholder="Video URL (optional)" value="${videoPath}" style="font-size:0.72rem;">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill btn-remove-gallery-item" style="font-size:0.75rem;">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        `;
        galleryContainer.appendChild(col);
        galleryIndex++;
        col.querySelector('.btn-remove-gallery-item').addEventListener('click', () => {
            col.remove();
            refreshGallerySortOrders();
        });
    }

    // Wire remove buttons for existing gallery items (Edit mode)
    if (galleryContainer) {
        galleryContainer.querySelectorAll('.btn-remove-gallery-item').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.gallery-item-card').remove();
                refreshGallerySortOrders();
            });
        });
    }

    // 7b. Product FAQs — Add / Remove / Drag-to-Reorder (SortableJS)
    let faqIndex = parseInt(form.dataset.faqCount || '0');
    const btnAddFaq = document.getElementById('btnAddFaq');
    const faqsContainer = document.getElementById('faqsContainer');

    function refreshFaqSortOrders() {
        if (!faqsContainer) return;
        const cards = faqsContainer.querySelectorAll('.faq-item-card');
        cards.forEach((card, i) => {
            const q = card.querySelector('.faq-question-input');
            const a = card.querySelector('.faq-answer-input');
            const s = card.querySelector('.faq-sort-order');
            if (q) q.name = `faqs[${i}][question]`;
            if (a) a.name = `faqs[${i}][answer]`;
            if (s) { s.name = `faqs[${i}][sort_order]`; s.value = i; }
        });
    }

    function appendFaqItem(question = '', answer = '') {
        if (!faqsContainer) return;
        document.getElementById('faqsEmpty')?.remove();

        const card = document.createElement('div');
        card.className = 'faq-item-card d-flex align-items-start gap-3 border rounded-3 p-3 bg-light';
        card.innerHTML = `
            <div class="faq-drag-handle text-muted pt-2" style="cursor: grab;"><i class="bi bi-grip-vertical fs-5"></i></div>
            <div class="flex-grow-1">
                <input type="text" name="faqs[${faqIndex}][question]" class="form-control border mb-2 faq-question-input" placeholder="Question" required value="${question}">
                <textarea name="faqs[${faqIndex}][answer]" class="form-control border faq-answer-input" rows="2" placeholder="Answer" required>${answer}</textarea>
                <input type="hidden" class="faq-sort-order" name="faqs[${faqIndex}][sort_order]" value="${faqIndex}">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border rounded-pill btn-remove-faq"><i class="bi bi-trash"></i></button>
        `;
        faqsContainer.appendChild(card);
        faqIndex++;
        card.querySelector('.btn-remove-faq').addEventListener('click', () => {
            card.remove();
            refreshFaqSortOrders();
        });
    }

    if (btnAddFaq) {
        btnAddFaq.addEventListener('click', () => appendFaqItem());
    }

    // Wire remove buttons for existing FAQ rows (Edit mode)
    if (faqsContainer) {
        faqsContainer.querySelectorAll('.btn-remove-faq').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.faq-item-card').remove();
                refreshFaqSortOrders();
            });
        });
    }

    if (faqsContainer && typeof Sortable !== 'undefined') {
        Sortable.create(faqsContainer, {
            animation: 180,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            handle: '.faq-drag-handle',
            onEnd: refreshFaqSortOrders
        });
    }

    // 8. Real-time Slug Generation
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (nameInput && slugInput) {
        let autoGenerateSlug = slugInput.value === '';

        nameInput.addEventListener('input', function() {
            if (autoGenerateSlug) {
                slugInput.value = slugify(this.value);
            }
        });

        slugInput.addEventListener('input', function() {
            autoGenerateSlug = (this.value.trim() === '');
            this.value = slugify(this.value);
        });
    }

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }
});
