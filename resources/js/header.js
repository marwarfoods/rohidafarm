document.addEventListener('DOMContentLoaded', function() {
    const sliderEl = document.querySelector('.mega-menu-slider');
    if(sliderEl) {
        const slidesCount = sliderEl.querySelectorAll('.swiper-slide').length;
        new Swiper('.mega-menu-slider', {
            slidesPerView: 2,
            spaceBetween: 10,
            loop: slidesCount > 6,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.mega-menu-next',
                prevEl: '.mega-menu-prev',
            },
            breakpoints: {
                640: { slidesPerView: 3, spaceBetween: 20 },
                768: { slidesPerView: 4, spaceBetween: 30 },
                1024: { slidesPerView: 6, spaceBetween: 30 },
            }
        });
    }
});
