// =================================================
// Reviews Module – Review Card Click → Combined Detail
//                 Modal (photo left + full text right)
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


    // ── Combined Review Detail Modal (image left + text right) ─
    const modalEl       = document.getElementById('reviewDetailModal');
    const imageCol       = document.getElementById('modalReviewImageCol');
    const textCol        = document.getElementById('modalReviewTextCol');
    const mainImg        = document.getElementById('modalReviewMainImg');
    const imgPrevBtn     = document.getElementById('modalReviewImgPrev');
    const imgNextBtn     = document.getElementById('modalReviewImgNext');
    const imgThumbsWrap  = document.getElementById('modalReviewImgThumbs');

    let _images = [];
    let _index  = 0;

    function renderModalImage() {
        if (!_images.length) return;
        mainImg.src = _images[_index];

        const showNav = _images.length > 1;
        if (imgPrevBtn) imgPrevBtn.style.display = showNav ? 'flex' : 'none';
        if (imgNextBtn) imgNextBtn.style.display = showNav ? 'flex' : 'none';

        if (imgThumbsWrap) {
            imgThumbsWrap.innerHTML = '';
            if (showNav) {
                _images.forEach((src, idx) => {
                    const thumb = document.createElement('img');
                    thumb.src = src;
                    thumb.style.cssText = `width:44px;height:44px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid ${idx === _index ? '#fff' : 'transparent'};opacity:${idx === _index ? '1' : '0.6'};`;
                    thumb.addEventListener('click', () => { _index = idx; renderModalImage(); });
                    imgThumbsWrap.appendChild(thumb);
                });
            }
        }
    }

    if (imgPrevBtn) imgPrevBtn.addEventListener('click', () => {
        _index = (_index - 1 + _images.length) % _images.length;
        renderModalImage();
    });
    if (imgNextBtn) imgNextBtn.addEventListener('click', () => {
        _index = (_index + 1) % _images.length;
        renderModalImage();
    });

    document.addEventListener('keydown', (e) => {
        if (!modalEl || !modalEl.classList.contains('show') || _images.length < 2) return;
        if (e.key === 'ArrowLeft')  { _index = (_index - 1 + _images.length) % _images.length; renderModalImage(); }
        if (e.key === 'ArrowRight') { _index = (_index + 1) % _images.length; renderModalImage(); }
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

            // Populate text column
            const starsEl = document.getElementById('modalReviewStars');
            if (starsEl) {
                starsEl.innerHTML = '';
                for (let s = 1; s <= 5; s++) {
                    starsEl.innerHTML += `<i class="bi ${s <= rating ? 'bi-star-fill' : 'bi-star'}"></i>`;
                }
            }
            const titleEl = document.getElementById('modalReviewTitle');
            if (titleEl) titleEl.textContent = title;
            const textEl = document.getElementById('modalReviewText');
            if (textEl) textEl.textContent = review;
            const userEl = document.getElementById('modalReviewUser');
            if (userEl) userEl.textContent = user;
            const dateEl = document.getElementById('modalReviewDate');
            if (dateEl) dateEl.textContent = date;
            const avatarEl = document.getElementById('modalReviewAvatar');
            if (avatarEl) avatarEl.textContent = (user || 'A').charAt(0).toUpperCase();

            // Populate / toggle photo column
            _images = images;
            _index  = 0;
            if (images.length > 0) {
                imageCol.classList.remove('d-none');
                textCol.classList.remove('col-md-12');
                textCol.classList.add('col-md-6');
                renderModalImage();
            } else {
                imageCol.classList.add('d-none');
                textCol.classList.remove('col-md-6');
                textCol.classList.add('col-md-12');
            }

            // Open review detail modal
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
