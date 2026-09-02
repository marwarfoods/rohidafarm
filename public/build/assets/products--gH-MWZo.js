document.addEventListener(`DOMContentLoaded`,function(){let e=document.getElementById(`productForm`);if(!e)return;let t=parseInt(e.dataset.galleryCount||`0`),n=parseInt(e.dataset.variantCount||`0`),r=document.getElementById(`displayCouponsSelect`);r&&typeof Choices<`u`&&new Choices(r,{removeItemButton:!0,searchEnabled:!0,placeholderValue:`Select coupons to show...`,itemSelectText:``}),window.initMediaPicker&&(initMediaPicker(`#productImageInput`,`#imagePreviewContainer`,`image`),initMediaPicker(`#gallerySelectorInput`,null,`image`));let i={},a=[`ShortDescription`,`Description`,`Benefits`,`Ingredients`];a.forEach(e=>{let t=document.querySelector(`#editor${e}`);t&&typeof ClassicEditor<`u`&&ClassicEditor.create(t).then(t=>{i[e]=t}).catch(e=>console.error(e))}),e.addEventListener(`submit`,function(){a.forEach(e=>{let t=document.getElementById(`editor${e}`);t&&i[e]&&(t.value=i[e].getData())}),_(),w()});let o=document.querySelector(`[name="mrp"]`),s=document.querySelector(`[name="sale_price"]`),c=document.getElementById(`discountBadge`);function l(){if(!o||!s||!c)return;let e=parseFloat(o.value)||0,t=parseFloat(s.value)||0;c.textContent=e>0&&t>0&&t<e?Math.round((e-t)/e*100)+`% off from MRP`:``}o&&s&&(o.addEventListener(`input`,l),s.addEventListener(`input`,l),l());let u=document.getElementById(`btnAddVariant`),d=document.getElementById(`variantsContainer`),f=document.getElementById(`variantGallerySelectorInput`);f||(f=document.createElement(`input`),f.type=`text`,f.id=`variantGallerySelectorInput`,f.style.display=`none`,document.body.appendChild(f),window.initMediaPicker&&initMediaPicker(`#variantGallerySelectorInput`,null,`image`));let p=null,m=null;f&&f.addEventListener(`change`,function(){let e=this.value;if(!e||!p)return;let t=document.createElement(`div`);t.className=`position-relative variant-gallery-chip`,t.innerHTML=`
                <input type="hidden" name="variants[${m}][gallery_images][]" value="${e}">
                <img src="${e.startsWith(`http`)||e.startsWith(`/`)?e:`/`+e}" class="rounded border" style="width: 48px; height: 48px; object-fit: cover;">
                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px; font-size: 10px; transform: translate(30%, -30%);">
                    <i class="bi bi-x"></i>
                </button>
            `,t.querySelector(`button`).addEventListener(`click`,function(){let e=this.closest(`.variant-gallery-chips-container`);t.remove(),e&&!e.querySelectorAll(`.variant-gallery-chip`).length&&e.querySelector(`.empty-variant-gallery-msg`)?.classList.remove(`d-none`)});let n=p.querySelector(`.empty-variant-gallery-msg`);n&&n.classList.add(`d-none`),p.appendChild(t),this.value=``});function h(e,t){let n=e.querySelector(`.variant-weight-input`),r=e.querySelector(`.variant-price-input`),i=e.querySelector(`.variant-weight-badge`),a=e.querySelector(`.variant-price-preview`),o=e.querySelector(`.variant-main-img-input`),s=e.querySelector(`.variant-main-img-preview`),c=e.querySelector(`.variant-header-thumb-container`);n&&i&&n.addEventListener(`input`,function(){i.textContent=this.value.trim()||`Variant #${t+1}`}),r&&a&&r.addEventListener(`input`,function(){let e=parseFloat(this.value)||0;a.textContent=`₹${e.toLocaleString(`en-IN`)}`}),o&&(window.initMediaPicker&&!o.parentNode.classList.contains(`media-picker-group`)&&initMediaPicker(o,null,`image`),o.addEventListener(`change`,function(){let e=this.value.trim(),t=e?e.startsWith(`http`)||e.startsWith(`/`)?e:`/`+e:``;t?(s&&(s.innerHTML=`<img src="${t}" class="w-100 h-100 object-fit-cover">`),c&&(c.innerHTML=`<img src="${t}" class="rounded border variant-header-thumb" style="width: 28px; height: 28px; object-fit: cover;">`)):(s&&(s.innerHTML=`<i class="bi bi-image text-muted" style="font-size: 1.3rem;"></i>`),c&&(c.innerHTML=``))}));let l=e.querySelector(`.btn-add-variant-gallery`),u=e.querySelector(`.variant-gallery-chips-container`);l&&u&&l.addEventListener(`click`,function(n){n.preventDefault(),p=u,m=e.dataset.index||t;let r=f?.closest(`.media-picker-group`),i=r?r.querySelector(`button`):null;i&&i.click()});let h=e.querySelector(`.btn-remove-variant`);h&&h.addEventListener(`click`,function(t){if(t.stopPropagation(),e.remove(),d&&!d.querySelectorAll(`.variant-item`).length){let e=document.createElement(`div`);e.id=`emptyVariantRow`,e.className=`text-center py-4 text-muted`,e.style.fontSize=`0.85rem`,e.innerHTML=`<i class="bi bi-plus-circle me-1"></i> Click "Add Variant" to configure size/weight options and custom images.`,d.appendChild(e)}})}d&&(d.querySelectorAll(`.variant-item`).forEach((e,t)=>{h(e,parseInt(e.dataset.index||t))}),u?.addEventListener(`click`,()=>{document.getElementById(`emptyVariantRow`)?.remove();let e=n,t=document.createElement(`div`);t.className=`variant-item card border rounded-3 mb-3 bg-white shadow-sm overflow-hidden`,t.dataset.index=e,t.innerHTML=`
                {{-- Accordion Header --}}
                <div class="card-header bg-light px-3 py-2.5 d-flex justify-content-between align-items-center variant-header" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#variantCollapse_${e}" aria-expanded="true">
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
                <div id="variantCollapse_${e}" class="collapse show">
                    <div class="card-body p-3">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Weight Option *</label>
                                <input type="text" name="variants[${e}][weight]" class="form-control form-control-sm border variant-weight-input" required placeholder="e.g. 500ml">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">MRP (₹)</label>
                                <input type="number" step="0.01" name="variants[${e}][mrp]" class="form-control form-control-sm border" placeholder="999">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Sale Price (₹) *</label>
                                <input type="number" step="0.01" name="variants[${e}][sale_price]" class="form-control form-control-sm border variant-price-input" required placeholder="799">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Stock *</label>
                                <input type="number" name="variants[${e}][stock]" class="form-control form-control-sm border" value="100">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-dark fw-semibold mb-1" style="font-size:0.8rem;">Max Cart Qty</label>
                                <input type="number" name="variants[${e}][max_cart_qty]" class="form-control form-control-sm border" placeholder="No limit">
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
                                            <input type="text" name="variants[${e}][image_path]" class="form-control form-control-sm media-picker-input variant-main-img-input" placeholder="Pick main image...">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label text-dark fw-semibold m-0 d-flex align-items-center gap-1" style="font-size:0.8rem;">
                                            <i class="bi bi-images text-success"></i> Variant Gallery Images
                                        </label>
                                        <button type="button" class="btn btn-outline-success btn-sm py-0.5 px-2.5 btn-add-variant-gallery" data-index="${e}" style="font-size: 0.72rem;">
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
            `,d.appendChild(t),h(t,e),n++}));let g=document.getElementById(`galleryContainer`);g&&typeof Sortable<`u`&&Sortable.create(g,{animation:180,ghostClass:`sortable-ghost`,chosenClass:`sortable-chosen`,handle:`.card`,onEnd:_});function _(){g&&g.querySelectorAll(`.gallery-item-card`).forEach((e,t)=>{let n=e.querySelector(`.gallery-image-path`),r=e.querySelector(`.gallery-video-path`),i=e.querySelector(`.gallery-sort-order`);n&&(n.name=`gallery[${t}][image_path]`),r&&(r.name=`gallery[${t}][video_path]`),i&&(i.name=`gallery[${t}][sort_order]`,i.value=t+1)})}let v=document.getElementById(`gallerySelectorInput`),y=document.getElementById(`btnOpenGalleryPicker`);v&&v.addEventListener(`change`,function(){let e=this.value;e&&(b(e),this.value=``)}),y&&y.addEventListener(`click`,()=>{let e=document.getElementById(`gallerySelectorInput`)?.closest(`.media-picker-group`);if(e){let t=e.querySelector(`button`);t&&t.click()}});function b(e,n=``){if(!g)return;let r=document.getElementById(`galleryEmpty`);r&&r.remove();let i=document.createElement(`div`);i.className=`col-6 col-md-4 gallery-item-card`,i.innerHTML=`
            <div class="card border rounded-3 bg-white shadow-sm h-100" style="cursor:grab;">
                <div class="card-body p-2 text-center">
                    <div class="text-muted mb-1" style="font-size:0.75rem;"><i class="bi bi-grip-horizontal me-1"></i> Drag to reorder</div>
                    <img src="${e}" class="w-100 rounded-2 mb-2" style="height:90px;object-fit:cover;">
                    <input type="hidden" class="gallery-image-path" name="gallery[${t}][image_path]" value="${e}">
                    <input type="hidden" class="gallery-sort-order" name="gallery[${t}][sort_order]" value="${t+1}">
                    <input type="text" class="gallery-video-path form-control form-control-sm border mb-2"
                           name="gallery[${t}][video_path]" placeholder="Video URL (optional)" value="${n}" style="font-size:0.72rem;">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100 rounded-pill btn-remove-gallery-item" style="font-size:0.75rem;">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        `,g.appendChild(i),t++,i.querySelector(`.btn-remove-gallery-item`).addEventListener(`click`,()=>{i.remove(),_()})}g&&g.querySelectorAll(`.btn-remove-gallery-item`).forEach(e=>{e.addEventListener(`click`,function(){this.closest(`.gallery-item-card`).remove(),_()})});let x=parseInt(e.dataset.faqCount||`0`),S=document.getElementById(`btnAddFaq`),C=document.getElementById(`faqsContainer`);function w(){C&&C.querySelectorAll(`.faq-item-card`).forEach((e,t)=>{let n=e.querySelector(`.faq-question-input`),r=e.querySelector(`.faq-answer-input`),i=e.querySelector(`.faq-sort-order`);n&&(n.name=`faqs[${t}][question]`),r&&(r.name=`faqs[${t}][answer]`),i&&(i.name=`faqs[${t}][sort_order]`,i.value=t)})}function T(e=``,t=``){if(!C)return;document.getElementById(`faqsEmpty`)?.remove();let n=document.createElement(`div`);n.className=`faq-item-card d-flex align-items-start gap-3 border rounded-3 p-3 bg-light`,n.innerHTML=`
            <div class="faq-drag-handle text-muted pt-2" style="cursor: grab;"><i class="bi bi-grip-vertical fs-5"></i></div>
            <div class="flex-grow-1">
                <input type="text" name="faqs[${x}][question]" class="form-control border mb-2 faq-question-input" placeholder="Question" required value="${e}">
                <textarea name="faqs[${x}][answer]" class="form-control border faq-answer-input" rows="2" placeholder="Answer" required>${t}</textarea>
                <input type="hidden" class="faq-sort-order" name="faqs[${x}][sort_order]" value="${x}">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border rounded-pill btn-remove-faq"><i class="bi bi-trash"></i></button>
        `,C.appendChild(n),x++,n.querySelector(`.btn-remove-faq`).addEventListener(`click`,()=>{n.remove(),w()})}S&&S.addEventListener(`click`,()=>T()),C&&C.querySelectorAll(`.btn-remove-faq`).forEach(e=>{e.addEventListener(`click`,function(){this.closest(`.faq-item-card`).remove(),w()})}),C&&typeof Sortable<`u`&&Sortable.create(C,{animation:180,ghostClass:`sortable-ghost`,chosenClass:`sortable-chosen`,handle:`.faq-drag-handle`,onEnd:w});let E=document.querySelector(`input[name="name"]`),D=document.querySelector(`input[name="slug"]`);if(E&&D){let e=D.value===``;E.addEventListener(`input`,function(){e&&(D.value=O(this.value))}),D.addEventListener(`input`,function(){e=this.value.trim()===``,this.value=O(this.value)})}function O(e){return e.toString().toLowerCase().replace(/\s+/g,`-`).replace(/[^\w\-]+/g,``).replace(/\-\-+/g,`-`).replace(/^-+/,``).replace(/-+$/,``)}});