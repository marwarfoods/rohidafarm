// ── Homepage Dynamic Scripts & Swiper Sliders ──

// Typewriter Class definition for dynamic typing headings
class Typewriter {
    constructor(el, words, wait = 2500) {
        this.el = el;
        this.words = words;
        this.txt = '';
        this.wordIndex = 0;
        this.wait = parseInt(wait, 10);
        this.isDeleting = false;
        this.type();
    }

    type() {
        const current = this.wordIndex % this.words.length;
        const fullTxt = this.words[current];

        if(this.isDeleting) {
            this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
            this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = `<span class="txt">${this.txt}</span>`;

        let typeSpeed = 120;

        if(this.isDeleting) {
            typeSpeed /= 2;
        }

        if(!this.isDeleting && this.txt === fullTxt) {
            typeSpeed = this.wait;
            this.isDeleting = true;
        } else if(this.isDeleting && this.txt === '') {
            this.isDeleting = false;
            this.wordIndex++;
            typeSpeed = 400;
        }

        setTimeout(() => this.type(), typeSpeed);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Swiper Hero Slider with slide transition
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        const slidesCount = heroSlider.querySelectorAll('.swiper-slide').length;
        new Swiper('.hero-slider', {
            loop: slidesCount > 1,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        }
        });
    }

    // Initialize Product Swiper Sliders (PC: 4 items, Mobile: 1.5 items, scroll 1-by-1, infinite loop)
    const productSliders = document.querySelectorAll('.products-slider');
    productSliders.forEach(slider => {
        const wrapper = slider.closest('.products-slider-wrapper');
        const slidesCount = slider.querySelectorAll('.swiper-slide').length;
        new Swiper(slider, {
            loop: slidesCount > 4,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            freeMode: false,
            mousewheel: false,
            slidesPerView: 1.5,
            slidesPerGroup: 1,
            spaceBetween: 16,
            grabCursor: true,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: wrapper ? wrapper.querySelector('.swiper-button-next') : null,
                prevEl: wrapper ? wrapper.querySelector('.swiper-button-prev') : null,
            },
            scrollbar: {
                el: slider.querySelector('.swiper-scrollbar'),
                draggable: true,
            },
            pagination: {
                el: slider.querySelector('.swiper-pagination'),
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                320: {
                    slidesPerView: 1.5,
                    slidesPerGroup: 1,
                    spaceBetween: 10,
                },
                576: {
                    slidesPerView: 2,
                    slidesPerGroup: 1,
                    spaceBetween: 12,
                },
                768: {
                    slidesPerView: 3,
                    slidesPerGroup: 1,
                    spaceBetween: 16,
                },
                992: {
                    slidesPerView: 4,
                    slidesPerGroup: 1,
                    spaceBetween: 20,
                },
                1200: {
                    slidesPerView: 4,
                    slidesPerGroup: 1,
                    spaceBetween: 24,
                }
            }
        });
    });

    // Initialize Promo Three Slider on Mobile
    new Swiper('.promo-three-slider', {
        slidesPerView: 1,
        spaceBetween: 20,
        grabCursor: true,
        pagination: {
            el: '.promo-three-slider .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 3,
                spaceBetween: 24,
                allowTouchMove: false, // disable swipe on desktop grid
            }
        }
    });

    // Initialize Native Ingredients Slider
    const nativeSlider = document.querySelector('.native-ingredients-slider');
    if (nativeSlider) {
        const slidesCount = nativeSlider.querySelectorAll('.swiper-slide').length;
        new Swiper('.native-ingredients-slider', {
            loop: slidesCount > 4,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        slidesPerView: 1.2,
        spaceBetween: 16,
        grabCursor: true,
        pagination: {
            el: '.native-ingredients-slider .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            576: { slidesPerView: 2.2, spaceBetween: 16 },
            768: { slidesPerView: 3, spaceBetween: 20 },
            992: {
                slidesPerView: 4,
                spaceBetween: 24,
                allowTouchMove: false,
            }
        }
        });
    }

    // Initialize Video Reviews Swiper Slider
    const videoSlider = document.querySelector('.video-reviews-slider');
    if (videoSlider) {
        const slidesCount = videoSlider.querySelectorAll('.swiper-slide').length;
        new Swiper('.video-reviews-slider', {
            loop: slidesCount > 6,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
        slidesPerView: 1.5, // 1.5 columns on mobile base
        spaceBetween: 16,
        grabCursor: true,
        navigation: {
            nextEl: '.video-reviews-next',
            prevEl: '.video-reviews-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2.5,
            },
            768: {
                slidesPerView: 3.5,
            },
            992: {
                slidesPerView: 4.5,
            },
            1200: {
                slidesPerView: 5.5, // 5.5 columns on desktop
                spaceBetween: 20
            }
        }
        });
    }

    // Lightbox modal logic for Video Reviews
    const videoCards = document.querySelectorAll('.video-card-wrapper');
    const lightbox = document.getElementById('videoLightbox');
    const lightboxVideo = document.getElementById('lightboxVideo');
    const lightboxReviewer = document.getElementById('lightboxReviewer');
    const lightboxProduct = document.getElementById('lightboxProduct');
    const lightboxBuyBtn = document.getElementById('lightboxBuyBtn');
    
    if (lightbox) {
        let currentVideoIndex = 0;
        let videoDataList = [];

        // Build array of metadata from DOM & add Hover-to-Play handlers
        videoCards.forEach((card, index) => {
            const cardVideo = card.querySelector('video');
            const playBtn = card.querySelector('.play-overlay-btn');

            if (cardVideo) {
                cardVideo.muted = true;

                card.addEventListener('mouseenter', () => {
                    const playPromise = cardVideo.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            if (playBtn) playBtn.style.opacity = '0';
                        }).catch(() => {});
                    }
                });

                card.addEventListener('mouseleave', () => {
                    cardVideo.pause();
                    if (playBtn) playBtn.style.opacity = '1';
                });
            }

            videoDataList.push({
                index: index,
                video: card.getAttribute('data-video'),
                reviewer: card.getAttribute('data-reviewer'),
                product: card.getAttribute('data-product'),
                buyUrl: card.getAttribute('data-buy-url')
            });
            
            // Add click listener
            card.addEventListener('click', (e) => {
                if (e.target.closest('.btn-video-buy')) return; // let product redirect work
                currentVideoIndex = parseInt(card.getAttribute('data-index'));
                openLightbox(currentVideoIndex);
            });
        });

        function openLightbox(index) {
            currentVideoIndex = index;
            const data = videoDataList[currentVideoIndex];
            if (!data) return;

            // Pause background hover videos
            videoCards.forEach(c => {
                const v = c.querySelector('video');
                const p = c.querySelector('.play-overlay-btn');
                if (v) v.pause();
                if (p) p.style.opacity = '1';
            });

            const container = lightbox.querySelector('.lightbox-container');
            if (container) container.classList.add('fading');

            setTimeout(() => {
                lightboxVideo.src = data.video;
                lightboxReviewer.textContent = data.reviewer;
                lightboxProduct.textContent = data.product;

                if (data.buyUrl) {
                    lightboxBuyBtn.href = data.buyUrl;
                    lightboxBuyBtn.classList.remove('d-none');
                } else {
                    lightboxBuyBtn.classList.add('d-none');
                }

                lightbox.classList.remove('d-none');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
                lightboxVideo.play().then(() => {
                    if (container) container.classList.remove('fading');
                }).catch(() => {
                    if (container) container.classList.remove('fading');
                });
            }, 150);
        }

        function closeLightbox() {
            lightbox.classList.add('d-none');
            document.body.style.overflow = ''; // Restore background scrolling
            lightboxVideo.pause();
            lightboxVideo.src = '';
        }

        // Close actions
        const closeBtn = lightbox.querySelector('.lightbox-close');
        if (closeBtn) closeBtn.addEventListener('click', closeLightbox);
        
        // Close on clicking backdrop or empty overlay area
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.classList.contains('lightbox-backdrop')) {
                closeLightbox();
            }
        });

        // Next/Prev Arrows Navigation
        function playNext() {
            let nextIndex = (currentVideoIndex + 1) % videoDataList.length;
            openLightbox(nextIndex);
        }

        function playPrev() {
            let prevIndex = (currentVideoIndex - 1 + videoDataList.length) % videoDataList.length;
            openLightbox(prevIndex);
        }

        const arrowLeft = lightbox.querySelector('.arrow-left');
        const arrowRight = lightbox.querySelector('.arrow-right');
        if (arrowLeft) arrowLeft.addEventListener('click', playPrev);
        if (arrowRight) arrowRight.addEventListener('click', playNext);
        
        // Mobile Navigation Controls
        const btnPrev = lightbox.querySelector('.btn-prev');
        const btnNext = lightbox.querySelector('.btn-next');
        if (btnPrev) btnPrev.addEventListener('click', playPrev);
        if (btnNext) btnNext.addEventListener('click', playNext);

        // Key Listeners
        window.addEventListener('keydown', (e) => {
            if (lightbox.classList.contains('d-none')) return;
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') playNext();
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') playPrev();
            if (e.key === 'Escape') closeLightbox();
        });

        // Mobile Reels Y-axis Swipe (up/down Y direction scroll)
        let touchStartY = 0;
        let touchEndY = 0;

        lightbox.addEventListener('touchstart', (e) => {
            touchStartY = e.changedTouches[0].clientY;
        }, { passive: true });

        lightbox.addEventListener('touchend', (e) => {
            touchEndY = e.changedTouches[0].clientY;
            handleSwipeY();
        }, { passive: true });

        function handleSwipeY() {
            const diffY = touchStartY - touchEndY;
            if (Math.abs(diffY) > 50) { // minimum Y threshold
                if (diffY > 0) {
                    playNext(); // Swiped Y-axis Up -> Next Reels video
                } else {
                    playPrev(); // Swiped Y-axis Down -> Previous Reels video
                }
            }
        }
    }

    // Dynamic tab skeleton loader switcher
    const tabElList = document.querySelectorAll('button[data-bs-toggle="tab"]');
    tabElList.forEach(tabEl => {
        tabEl.addEventListener('show.bs.tab', event => {
            const targetId = event.target.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            
            const loader = targetPane.querySelector('.skeleton-loader-container');
            const content = targetPane.querySelector('.tab-pane-content-wrapper');
            
            if (loader && content) {
                loader.classList.remove('d-none');
                content.classList.add('d-none');
                
                setTimeout(() => {
                    loader.classList.add('d-none');
                    content.classList.remove('d-none');
                    
                    // Update Swiper inside tab pane to prevent pagination glitches
                    const sliders = targetPane.querySelectorAll('.products-slider');
                    sliders.forEach(s => {
                        if (s.swiper) s.swiper.update();
                    });
                }, 400);
            }
        });
    });

    // Initialize dynamic typewriter headings
    const typewriterEl = document.querySelector('.text-typewriter');
    if (typewriterEl) {
        const words = JSON.parse(typewriterEl.getAttribute('data-words'));
        new Typewriter(typewriterEl, words);
    }

    // Initialize Home Blog Slider (PC: 4 items, Mobile: 1.5 items, scroll 1-by-1)
    const blogSlider = document.querySelector('.blogs-slider');
    if (blogSlider) {
        const slidesCount = blogSlider.querySelectorAll('.swiper-slide').length;
        new Swiper(blogSlider, {
            loop: slidesCount > 4,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            slidesPerView: 1.5,
            slidesPerGroup: 1,
            spaceBetween: 16,
            grabCursor: true,
            breakpoints: {
                320: {
                    slidesPerView: 1.5,
                    slidesPerGroup: 1,
                    spaceBetween: 10,
                },
                576: {
                    slidesPerView: 2.2,
                    slidesPerGroup: 1,
                    spaceBetween: 12,
                },
                768: {
                    slidesPerView: 3,
                    slidesPerGroup: 1,
                    spaceBetween: 16,
                },
                992: {
                    slidesPerView: 4,
                    slidesPerGroup: 1,
                    spaceBetween: 20,
                }
            }
        });
    }

    // Intersection Observer for Deferred Lazy Sections
    if ('IntersectionObserver' in window) {
        const lazySections = document.querySelectorAll('.lazy-section');
        const sectionObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('section-loaded');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '200px 0px 200px 0px',
            threshold: 0.01
        });

        lazySections.forEach(sec => sectionObserver.observe(sec));
    }
});
