// =================================================
// Swipers Module – Offers, Reviews & Related Products
// resources/js/product-detail/swipers.js
// =================================================

export function initSwipers() {

    // ── Related Products Slider ──────────────────────
    if (document.querySelector('.related-products-slider')) {
        new Swiper('.related-products-slider', {
            slidesPerView: 1.5,
            spaceBetween: 16,
            navigation: {
                nextEl: '.related-products-slider .swiper-button-next',
                prevEl: '.related-products-slider .swiper-button-prev',
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
