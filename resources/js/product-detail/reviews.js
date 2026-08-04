// =================================================
// Reviews Module – Review Card Modal, Review Image
//                 Lightbox & Multiple Modal Fix
// resources/js/product-detail/reviews.js
// =================================================

export function initReviews() {

    // ── Interactive Star Rating Input ─────────────────
    const starIcons   = document.querySelectorAll('#interactiveRatingStars .star-input-icon');
    const ratingInput = document.getElementById('ratingInputValue');
    if (starIcons.length > 0 && ratingInput) {
        starIcons.forEach(icon => {
            icon.addEventListener('click', function () {
                const val = parseInt(this.getAttribute('data-val'));
                ratingInput.value = val;
                starIcons.forEach(s => {
                    const sVal = parseInt(s.getAttribute('data-val'));
                    if (sVal <= val) {
                        s.classList.remove('bi-star');
                        s.classList.add('bi-star-fill');
                    } else {
                        s.classList.remove('bi-star-fill');
                        s.classList.add('bi-star');
                    }
                });
            });
        });
    }


    // ── Review Image Lightbox State ───────────────────
    let _lbImages = [];
    let _lbIndex  = 0;
    const lbModal   = document.getElementById('reviewImgLightboxFrontend');
    const lbImg     = document.getElementById('reviewLightboxImg');
    const lbCounter = document.getElementById('reviewLightboxCounter');
    const lbPrev    = document.getElementById('reviewLightboxPrev');
    const lbNext    = document.getElementById('reviewLightboxNext');

    function openReviewLightbox(images, startIndex) {
        _lbImages = images;
        _lbIndex  = startIndex;
        updateLightboxSlide();
        let lb = bootstrap.Modal.getInstance(lbModal);
        if (!lb) {
            lb = new bootstrap.Modal(lbModal, { backdrop: true });
        }
        lb.show();
    }

    function updateLightboxSlide() {
        if (!lbImg || !_lbImages.length) return;
        lbImg.style.opacity = '0';
        lbImg.style.transform = 'scale(0.96)';
        setTimeout(() => {
            lbImg.src = _lbImages[_lbIndex];
            lbImg.style.transition = 'opacity 0.22s,transform 0.22s';
            lbImg.style.opacity = '1';
            lbImg.style.transform = 'scale(1)';
        }, 120);
        if (lbCounter) lbCounter.textContent = (_lbIndex + 1) + ' / ' + _lbImages.length;
        if (lbPrev)    lbPrev.style.display = _lbImages.length > 1 ? 'flex' : 'none';
        if (lbNext)    lbNext.style.display = _lbImages.length > 1 ? 'flex' : 'none';
    }

    if (lbPrev) lbPrev.addEventListener('click', () => {
        _lbIndex = (_lbIndex - 1 + _lbImages.length) % _lbImages.length;
        updateLightboxSlide();
    });
    if (lbNext) lbNext.addEventListener('click', () => {
        _lbIndex = (_lbIndex + 1) % _lbImages.length;
        updateLightboxSlide();
    });

    // Keyboard navigation for review lightbox
    document.addEventListener('keydown', (e) => {
        if (!lbModal || !lbModal.classList.contains('show')) return;
        if (e.key === 'ArrowLeft')  { _lbIndex = (_lbIndex - 1 + _lbImages.length) % _lbImages.length; updateLightboxSlide(); }
        if (e.key === 'ArrowRight') { _lbIndex = (_lbIndex + 1) % _lbImages.length; updateLightboxSlide(); }
        if (e.key === 'Escape')     { bootstrap.Modal.getInstance(lbModal)?.hide(); }
    });


    // ── Review Card Click → Detail Modal ─────────────
    document.querySelectorAll('.review-card-click-trigger').forEach(card => {
        card.addEventListener('click', function (e) {
            if (e.target.classList.contains('review-img-lightbox')) return;

            const rating = this.getAttribute('data-rating');
            const title  = this.getAttribute('data-title');
            const review = this.getAttribute('data-review');
            const user   = this.getAttribute('data-user');
            const date   = this.getAttribute('data-date');
            let images   = [];
            try { images = JSON.parse(this.getAttribute('data-images') || '[]'); } catch (err) {}

            // Populate modal fields
            const ratingEl = document.getElementById('modalReviewRating');
            if (ratingEl) ratingEl.innerHTML = `${rating} <i class="bi bi-star-fill text-white" style="font-size:0.65rem;"></i>`;
            const titleEl = document.getElementById('modalReviewTitle');
            if (titleEl) titleEl.textContent = title;
            const textEl = document.getElementById('modalReviewText');
            if (textEl) textEl.textContent = review;
            const userEl = document.getElementById('modalReviewUser');
            if (userEl) userEl.textContent = user;
            const dateEl = document.getElementById('modalReviewDate');
            if (dateEl) dateEl.textContent = date;

            // Inject clickable image thumbnails into detail modal
            const imgContainer = document.getElementById('modalReviewImages');
            if (imgContainer) {
                imgContainer.innerHTML = '';
                images.forEach((src, idx) => {
                    const wrapper = document.createElement('div');
                    wrapper.style.cssText = 'position:relative;cursor:pointer;';

                    const img = document.createElement('img');
                    img.src = src;
                    img.className = 'rounded-3 border';
                    img.style.cssText = 'width:72px;height:72px;object-fit:cover;transition:transform 0.18s,box-shadow 0.18s;';
                    img.alt = 'Review photo ' + (idx + 1);
                    img.title = 'Click to enlarge';

                    img.addEventListener('mouseenter', () => {
                        img.style.transform = 'scale(1.08)';
                        img.style.boxShadow = '0 4px 16px rgba(0,0,0,0.22)';
                    });
                    img.addEventListener('mouseleave', () => {
                        img.style.transform = 'scale(1)';
                        img.style.boxShadow = '';
                    });
                    img.addEventListener('click', (e) => {
                        e.stopPropagation();
                        openReviewLightbox(images, idx);
                    });

                    // Magnify icon overlay
                    const overlay = document.createElement('div');
                    overlay.style.cssText = 'position:absolute;bottom:3px;right:3px;background:rgba(0,0,0,0.45);border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;pointer-events:none;';
                    overlay.innerHTML = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';

                    wrapper.appendChild(img);
                    wrapper.appendChild(overlay);
                    imgContainer.appendChild(wrapper);
                });
            }

            // Open review detail modal
            const modalEl = document.getElementById('reviewDetailModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
    });


    // ── Multiple Modals Backdrop / Scroll-Lock Fix ────
    document.addEventListener('hidden.bs.modal', function () {
        if (document.querySelector('.modal.show')) {
            document.body.classList.add('modal-open');
        } else {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
    });
}
