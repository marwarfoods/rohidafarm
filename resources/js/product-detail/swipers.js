// =================================================
// Swipers Module – Offers, Reviews & Related Products
// resources/js/product-detail/swipers.js
// =================================================

export function initSwipers() {

    // ── Related Products Slider ──────────────────────
    const relatedSliderEl = document.querySelector('.related-products-slider');
    if (relatedSliderEl) {
        const relatedWrapper = relatedSliderEl.closest('.products-slider-wrapper');
        new Swiper(relatedSliderEl, {
            slidesPerView: 1.5,
            spaceBetween: 16,
            watchOverflow: true,
            // Let taps on the variant <select> inside a slide open the native
            // picker normally on touch devices instead of Swiper's own touch/drag
            // handling swallowing the tap — this is why the weight dropdown's
            // "change" (and the price update that depends on it) never fired on
            // mobile even though it worked fine with a mouse on desktop.
            noSwipingSelector: 'select',
            navigation: {
                nextEl: relatedWrapper ? relatedWrapper.querySelector('.swiper-button-next') : null,
                prevEl: relatedWrapper ? relatedWrapper.querySelector('.swiper-button-prev') : null,
            },
            breakpoints: {
                576: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                992: { slidesPerView: 4 }
            }
        });
    }


    // ── Offers Slider ────────────────────────────────
    if (document.querySelector('.offers-slider')) {
        new Swiper('.offers-slider', {
            slidesPerView: 1,
            spaceBetween: 12,
            grabCursor: true,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.offers-slider-pagination',
                clickable: true,
            },
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 16 }
            }
        });
    }


    // ── Reviews Slider ───────────────────────────────
    if (document.querySelector('.reviews-slider')) {
        new Swiper('.reviews-slider', {
            slidesPerView: 1.1,
            spaceBetween: 12,
            grabCursor: true,
            pagination: {
                el: '.reviews-slider-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.reviews-swiper-button-next',
                prevEl: '.reviews-swiper-button-prev',
            },
            breakpoints: {
                576: { slidesPerView: 2, spaceBetween: 16 },
                992: { slidesPerView: 3, spaceBetween: 16 }
            }
        });
    }
}
