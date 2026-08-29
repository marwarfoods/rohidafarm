document.addEventListener(`DOMContentLoaded`,function(){let e=document.getElementById(`productForm`);if(!e)return;let t=parseInt(e.dataset.galleryCount||`0`),n=parseInt(e.dataset.variantCount||`0`),r=document.getElementById(`displayCouponsSelect`);r&&typeof Choices<`u`&&new Choices(r,{removeItemButton:!0,searchEnabled:!0,placeholderValue:`Select coupons to show...`,itemSelectText:``}),window.initMediaPicker&&(initMediaPicker(`#productImageInput`,`#imagePreviewContainer`,`image`),initMediaPicker(`#gallerySelectorInput`,null,`image`));let i={},a=[`ShortDescription`,`Description`,`Benefits`,`Ingredients`];a.forEach(e=>{let t=document.querySelector(`#editor${e}`);t&&typeof ClassicEditor<`u`&&ClassicEditor.create(t).then(t=>{i[e]=t}).catch(e=>console.error(e))}),e.addEventListener(`submit`,function(){a.forEach(e=>{let t=document.getElementById(`editor${e}`);t&&i[e]&&(t.value=i[e].getData())}),p(),b()});let o=document.querySelector(`[name="mrp"]`),s=document.querySelector(`[name="sale_price"]`),c=document.getElementById(`discountBadge`);function l(){if(!o||!s||!c)return;let e=parseFloat(o.value)||0,t=parseFloat(s.value)||0;c.textContent=e>0&&t>0&&t<e?Math.round((e-t)/e*100)+`% off from MRP`:``}o&&s&&(o.addEventListener(`input`,l),s.addEventListener(`input`,l),l());let u=document.getElementById(`btnAddVariant`),d=document.getElementById(`variantsContainer`);u&&d&&(d.querySelectorAll(`.btn-remove-variant`).forEach(e=>{e.addEventListener(`click`,function(){this.closest(`.variant-item`).remove()})}),u.addEventListener(`click`,()=>{document.getElementById(`emptyVariantRow`)?.remove();let e=document.createElement(`div`);e.className=`variant-item px-4 py-3 border-bottom`,e.innerHTML=`
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1" style="font-size:0.8rem;">Weight *</label>
                        <input type="text" name="variants[${n}][weight]" class="form-control border" required placeholder="e.g. 500ml">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1" style="font-size:0.8rem;">MRP (₹)</label>
                        <input type="number" step="0.01" name="variants[${n}][mrp]" class="form-control border" placeholder="999">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1" style="font-size:0.8rem;">Sale Price (₹)</label>
                        <input type="number" step="0.01" name="variants[${n}][sale_price]" class="form-control border" placeholder="799">
                    </div>
                </div>
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1" style="font-size:0.8rem;">Stock</label>
                        <input type="number" name="variants[${n}][stock]" class="form-control border" value="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1" style="font-size:0.8rem;">Max Cart Qty</label>
                        <input type="number" name="variants[${n}][max_cart_qty]" class="form-control border" placeholder="No limit">
                    </div>
                    <div class="col-md-4 d-flex justify-content-md-end">
                        <button type="button" class="btn btn-sm btn-outline-danger border rounded-pill px-3 btn-remove-variant"><i class="bi bi-trash me-1"></i>Remove</button>
                    </div>
                </div>
            `,d.appendChild(e),n++,e.querySelector(`.btn-remove-variant`).addEventListener(`click`,()=>e.remove())}));let f=document.getElementById(`galleryContainer`);f&&typeof Sortable<`u`&&Sortable.create(f,{animation:180,ghostClass:`sortable-ghost`,chosenClass:`sortable-chosen`,handle:`.card`,onEnd:p});function p(){f&&f.querySelectorAll(`.gallery-item-card`).forEach((e,t)=>{let n=e.querySelector(`.gallery-image-path`),r=e.querySelector(`.gallery-video-path`),i=e.querySelector(`.gallery-sort-order`);n&&(n.name=`gallery[${t}][image_path]`),r&&(r.name=`gallery[${t}][video_path]`),i&&(i.name=`gallery[${t}][sort_order]`,i.value=t+1)})}let m=document.getElementById(`gallerySelectorInput`),h=document.getElementById(`btnOpenGalleryPicker`);m&&m.addEventListener(`change`,function(){let e=this.value;e&&(g(e),this.value=``)}),h&&h.addEventListener(`click`,()=>{let e=document.getElementById(`gallerySelectorInput`)?.closest(`.media-picker-group`);if(e){let t=e.querySelector(`button`);t&&t.click()}});function g(e,n=``){if(!f)return;let r=document.getElementById(`galleryEmpty`);r&&r.remove();let i=document.createElement(`div`);i.className=`col-6 col-md-4 gallery-item-card`,i.innerHTML=`
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
        `,f.appendChild(i),t++,i.querySelector(`.btn-remove-gallery-item`).addEventListener(`click`,()=>{i.remove(),p()})}f&&f.querySelectorAll(`.btn-remove-gallery-item`).forEach(e=>{e.addEventListener(`click`,function(){this.closest(`.gallery-item-card`).remove(),p()})});let _=parseInt(e.dataset.faqCount||`0`),v=document.getElementById(`btnAddFaq`),y=document.getElementById(`faqsContainer`);function b(){y&&y.querySelectorAll(`.faq-item-card`).forEach((e,t)=>{let n=e.querySelector(`.faq-question-input`),r=e.querySelector(`.faq-answer-input`),i=e.querySelector(`.faq-sort-order`);n&&(n.name=`faqs[${t}][question]`),r&&(r.name=`faqs[${t}][answer]`),i&&(i.name=`faqs[${t}][sort_order]`,i.value=t)})}function x(e=``,t=``){if(!y)return;document.getElementById(`faqsEmpty`)?.remove();let n=document.createElement(`div`);n.className=`faq-item-card d-flex align-items-start gap-3 border rounded-3 p-3 bg-light`,n.innerHTML=`
            <div class="faq-drag-handle text-muted pt-2" style="cursor: grab;"><i class="bi bi-grip-vertical fs-5"></i></div>
            <div class="flex-grow-1">
                <input type="text" name="faqs[${_}][question]" class="form-control border mb-2 faq-question-input" placeholder="Question" required value="${e}">
                <textarea name="faqs[${_}][answer]" class="form-control border faq-answer-input" rows="2" placeholder="Answer" required>${t}</textarea>
                <input type="hidden" class="faq-sort-order" name="faqs[${_}][sort_order]" value="${_}">
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger border rounded-pill btn-remove-faq"><i class="bi bi-trash"></i></button>
        `,y.appendChild(n),_++,n.querySelector(`.btn-remove-faq`).addEventListener(`click`,()=>{n.remove(),b()})}v&&v.addEventListener(`click`,()=>x()),y&&y.querySelectorAll(`.btn-remove-faq`).forEach(e=>{e.addEventListener(`click`,function(){this.closest(`.faq-item-card`).remove(),b()})}),y&&typeof Sortable<`u`&&Sortable.create(y,{animation:180,ghostClass:`sortable-ghost`,chosenClass:`sortable-chosen`,handle:`.faq-drag-handle`,onEnd:b});let S=document.querySelector(`input[name="name"]`),C=document.querySelector(`input[name="slug"]`);if(S&&C){let e=C.value===``;S.addEventListener(`input`,function(){e&&(C.value=w(this.value))}),C.addEventListener(`input`,function(){e=this.value.trim()===``,this.value=w(this.value)})}function w(e){return e.toString().toLowerCase().replace(/\s+/g,`-`).replace(/[^\w\-]+/g,``).replace(/\-\-+/g,`-`).replace(/^-+/,``).replace(/-+$/,``)}});