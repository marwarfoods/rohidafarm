// =================================================
// Gallery Module – Skeleton, Swiper, Thumbnails,
//                 Zoom, Image Lightbox
// resources/js/product-detail/gallery.js
// =================================================

export function initGallery() {

    // ── Skeleton Loader ──────────────────────────────
    const skel = document.getElementById('productSkeleton');
    const hideSkeleton = () => {
        if (skel) {
            skel.style.opacity = '0';
            skel.style.pointerEvents = 'none';
            setTimeout(() => skel.remove(), 500);
        }
    };
    window.addEventListener('load', hideSkeleton);
    setTimeout(hideSkeleton, 1400);


    // ── Main Product Image Swiper ────────────────────
    const mainSwiper = new Swiper('.main-product-slider', {
        loop: false,
        pagination: { el: '.main-product-slider .swiper-pagination', clickable: true },
    });

    // Collect all product image URLs for lightbox
    let allImages = Array.from(document.querySelectorAll('.img-main-slide'))
        .map(img => img.getAttribute('data-full') || img.src);
    const defaultImages = [...allImages];

    const thumbContainer = document.querySelector('.thumb-slider-col');

    // ── Set container height = exactly 5 thumbs ───────
    function setThumbContainerHeight() {
        if (!thumbContainer) return;
        const thumbs = thumbContainer.querySelectorAll('.thumb-img-wrapper');
        if (!thumbs.length) return;
        const firstThumb = thumbs[0];
        const thumbWidth = firstThumb.getBoundingClientRect().width;
        if (thumbWidth <= 0) return;
        // Each thumb is square: height = width. Gap between each = 6px. Show 5 items.
        const containerHeight = (thumbWidth * Math.min(thumbs.length, 5)) + (6 * Math.max(0, Math.min(thumbs.length, 5) - 1));
        thumbContainer.style.height   = containerHeight + 'px';
        thumbContainer.style.maxHeight = containerHeight + 'px';
    }

    window.addEventListener('load', setThumbContainerHeight);
    window.addEventListener('resize', setThumbContainerHeight);
    setTimeout(setThumbContainerHeight, 50);
    setTimeout(setThumbContainerHeight, 300);
    setTimeout(setThumbContainerHeight, 800);


    // ── Thumbnail Click ──────────────────────────────
    function wireThumbnailClicks() {
        document.querySelectorAll('.thumb-img-wrapper').forEach(thumb => {
            thumb.addEventListener('click', function () {
                const index = parseInt(this.getAttribute('data-slide-index'));
                mainSwiper.slideTo(index);
                document.querySelectorAll('.thumb-img-wrapper').forEach(t => t.classList.remove('active'));
                document.querySelectorAll(`.thumb-img-wrapper[data-slide-index="${index}"]`).forEach(t => t.classList.add('active'));
            });
        });
    }
    wireThumbnailClicks();


    // ── Thumbnail Scroll Up/Down Buttons ────────────
    if (thumbContainer) {
        const upBtn   = document.querySelector('.thumb-scroll-btn.scroll-up');
        const downBtn = document.querySelector('.thumb-scroll-btn.scroll-down');

        upBtn?.addEventListener('click', () => {
            let prevIndex = mainSwiper.activeIndex - 1;
            if (prevIndex < 0) prevIndex = allImages.length - 1;
            mainSwiper.slideTo(prevIndex);
        });
        
        downBtn?.addEventListener('click', () => {
            let nextIndex = mainSwiper.activeIndex + 1;
            if (nextIndex >= allImages.length) nextIndex = 0;
            mainSwiper.slideTo(nextIndex);
        });
    }


    // ── Swipe → Sync Thumbnails ──────────────────────
    mainSwiper.on('slideChange', function () {
        const index = mainSwiper.activeIndex;
        document.querySelectorAll('.thumb-img-wrapper').forEach(t => t.classList.remove('active'));
        document.querySelectorAll(`.thumb-img-wrapper[data-slide-index="${index}"]`).forEach(t => t.classList.add('active'));

        // Auto scroll thumbnail column to keep active item visible
        const activeThumb = document.querySelector(`.thumb-img-wrapper[data-slide-index="${index}"]`);
        if (activeThumb && thumbContainer) {
            const containerRect = thumbContainer.getBoundingClientRect();
            const thumbRect     = activeThumb.getBoundingClientRect();
            if (thumbRect.top < containerRect.top) {
                thumbContainer.scrollBy({ top: thumbRect.top - containerRect.top, behavior: 'smooth' });
            } else if (thumbRect.bottom > containerRect.bottom) {
                thumbContainer.scrollBy({ top: thumbRect.bottom - containerRect.bottom, behavior: 'smooth' });
            }
        }
    });


    // ── Lightbox Swiper ──────────────────────────────
    let lightboxSwiper = null;

    function buildLightboxSlides(startIndex) {
        const wrapper = document.getElementById('lightboxSwiperWrapper');
        if (!wrapper) return;
        wrapper.innerHTML = '';
        allImages.forEach(src => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';
            slide.innerHTML = `<img src="${src}" class="img-fluid d-block mx-auto" style="max-height:88vh;object-fit:contain;border-radius:12px;">`;
            wrapper.appendChild(slide);
        });

        if (lightboxSwiper) { lightboxSwiper.destroy(true, true); }
        lightboxSwiper = new Swiper('#lightboxSwiper', {
            initialSlide: startIndex,
            navigation: { nextEl: '#lightboxSwiper .swiper-button-next', prevEl: '#lightboxSwiper .swiper-button-prev' },
            pagination: { el: '#lightboxSwiper .swiper-pagination', clickable: true },
            loop: allImages.length > 1,
        });
    }

    function wireMainImageZoomAndLightbox() {
        // Open lightbox on main image click
        document.querySelectorAll('.img-main-slide').forEach((img, idx) => {
            img.style.cursor = 'zoom-in';
            img.onclick = function () {
                buildLightboxSlides(idx);
                const modal = new bootstrap.Modal(document.getElementById('imageLightboxModal'));
                modal.show();
            };
        });

        // Zoom on Desktop
        document.querySelectorAll('.main-image-zoom-frame').forEach(frame => {
            const img = frame.querySelector('img');
            if (!img) return;
            frame.onmousemove = function (e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                img.style.transformOrigin = `${x}px ${y}px`;
                img.style.transform = 'scale(1.7)';
                img.style.transition = 'transform 0.1s ease';
            };
            frame.onmouseleave = function () {
                img.style.transformOrigin = 'center center';
                img.style.transform = 'scale(1)';
            };
        });
    }
    wireMainImageZoomAndLightbox();

    // Open lightbox on review photo click
    document.querySelectorAll('.review-img-lightbox').forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function () {
            const wrapper = document.getElementById('lightboxSwiperWrapper');
            wrapper.innerHTML = `<div class="swiper-slide"><img src="${this.src}" class="img-fluid d-block mx-auto" style="max-height:88vh;object-fit:contain;border-radius:12px;"></div>`;
            if (lightboxSwiper) { lightboxSwiper.destroy(true, true); }
            lightboxSwiper = new Swiper('#lightboxSwiper', {
                pagination: { el: '#lightboxSwiper .swiper-pagination', clickable: true }
            });
            const modal = new bootstrap.Modal(document.getElementById('imageLightboxModal'));
            modal.show();
        });
    });

    // ── Global Variant Image & Gallery Switcher ────────
    window.updateProductGalleryImages = function(imageUrls) {
        const imgs = (imageUrls && imageUrls.length > 0) ? imageUrls : defaultImages;
        allImages = imgs;

        // 1. Update Swiper slides
        const swiperWrapper = document.querySelector('.main-product-slider .swiper-wrapper');
        if (swiperWrapper) {
            swiperWrapper.innerHTML = '';
            imgs.forEach((src, idx) => {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide main-image-zoom-frame';
                slide.style.cursor = 'zoom-in';
                slide.innerHTML = `<img src="${src}" alt="Product variant image" class="w-100 h-100 object-fit-cover img-main-slide" data-full="${src}" loading="eager">`;
                swiperWrapper.appendChild(slide);
            });
            mainSwiper.update();
            mainSwiper.slideTo(0, 300);
        }

        // 2. Update Thumbnails
        if (thumbContainer) {
            thumbContainer.innerHTML = '';
            imgs.forEach((src, idx) => {
                const thumb = document.createElement('div');
                thumb.className = `thumb-img-wrapper ${idx === 0 ? 'active' : ''}`;
                thumb.setAttribute('data-slide-index', idx);
                thumb.innerHTML = `<img src="${src}" alt="Thumbnail ${idx + 1}">`;
                thumbContainer.appendChild(thumb);
            });
            wireThumbnailClicks();
            setThumbContainerHeight();
        }

        // 3. Re-wire Zoom & Lightbox on new DOM elements
        wireMainImageZoomAndLightbox();
    };
}
