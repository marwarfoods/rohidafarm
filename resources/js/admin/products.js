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
        initMediaPicker('#productImageInput', '#imagePreviewContainer', 'image');
        initMediaPicker('#gallerySelectorInput', null, 'image');
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

    // 5. Weight Variants
    const btnAddVariant = document.getElementById('btnAddVariant');
    const variantsTable = document.querySelector('#variantsTable tbody');

    if (btnAddVariant && variantsTable) {
        // Wire existing delete buttons
        variantsTable.querySelectorAll('.btn-remove-variant').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('tr').remove();
            });
        });

        btnAddVariant.addEventListener('click', () => {
            document.getElementById('emptyVariantRow')?.remove();
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-2"><input type="text" name="variants[${variantIndex}][weight]" class="form-control border" required placeholder="e.g. 500ml"></td>
                <td class="px-4 py-2"><input type="number" step="0.01" name="variants[${variantIndex}][mrp]" class="form-control border" placeholder="999"></td>
                <td class="px-4 py-2"><input type="number" step="0.01" name="variants[${variantIndex}][sale_price]" class="form-control border" placeholder="799"></td>
                <td class="px-4 py-2"><input type="number" name="variants[${variantIndex}][stock]" class="form-control border" value="100"></td>
                <td class="px-4 py-2"><input type="number" name="variants[${variantIndex}][max_cart_qty]" class="form-control border" placeholder="No limit"></td>
                <td class="px-4 py-2 text-center"><button type="button" class="btn btn-sm btn-outline-danger border rounded-pill btn-remove-variant"><i class="bi bi-trash"></i></button></td>
            `;
            variantsTable.appendChild(row);
            variantIndex++;
            row.querySelector('.btn-remove-variant').addEventListener('click', () => row.remove());
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
            const fp = this.value;
            if (!fp) return;
            appendGalleryItem(fp);
            this.value = '';
        });
    }

    if (btnOpenPicker) {
        btnOpenPicker.addEventListener('click', () => {
            const input = document.getElementById('gallerySelectorInput');
            const wrapper = input?.closest('.media-picker-group');
            if (wrapper) {
                const btn = wrapper.querySelector('button');
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
