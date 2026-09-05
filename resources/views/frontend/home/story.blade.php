<!-- ── Our Roots & Heritage (The Rohida Farm Journey & Traditional Values) ── -->
@php
    $storyVideo = \App\Models\Setting::get('home_story_video') ?: asset('images/videos/about-us.mp4');
@endphp

<section class="py-4 py-md-5 story-kasutam-section position-relative overflow-hidden" id="our-story"
    style="background: url('{{ asset('images/Our Roots & Heritage-bg.jpg') }}') center center / cover no-repeat;">

    {{-- Warm soft cream overlay for high contrast & elegance --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(250, 247, 238, 0.90); z-index: 0; pointer-events: none;"></div>

    <div class="container-fluid px-3 px-sm-4 px-md-5 position-relative" style="z-index: 1;">

        <!-- ── Top Section Header ── -->
        <div class="text-center mb-3 mb-md-4" data-aos="fade-up">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill font-heading fw-bold text-uppercase mb-2"
                 style="font-size: 0.78rem; letter-spacing: 2px; color: #5C3D2E; background: rgba(196, 154, 69, 0.18); border: 1px solid rgba(196, 154, 69, 0.4);">
                <i class="fa-solid fa-seedling" style="color: #C49A45;"></i>
                <span>Our Roots &amp; Heritage</span>
            </div>
            <h2 class="display-5 font-heading fw-bold mb-2 text-dark" style="color: #5C3D2E !important;">
                The Rohida Farm Journey
            </h2>
            <p class="fs-6 mx-auto mb-0" style="max-width: 680px; color: #6E5B4F; line-height: 1.65;">
                Born from a passion to revive ancient Vedic agriculture, <strong>Rohida Farm</strong> is dedicated to nurturing pure indigenous <em>Tharparkar cows</em> grazing freely in open organic pastures under natural sunshine.
            </p>
        </div>

        <!-- ── Center Video Frame (Full Width Container, Auto Scroll-Play, Click To Toggle) ── -->
        <div class="story-video-container mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="story-video-card position-relative overflow-hidden" 
                 id="storyVideoCard"
                 title="Click to play / pause">
                
                <video id="storyVideoElement" 
                       class="w-100 h-100 story-video-el" 
                       playsinline 
                       muted 
                       loop 
                       preload="auto"
                       style="object-fit: cover; display: block; width: 100%; min-height: 280px; max-height: 560px;">
                    <source src="{{ $storyVideo }}" type="video/mp4">
                    Your browser does not support HTML5 video.
                </video>

            </div>
        </div>

        <!-- ── Bottom Row: Left Value Card | Center Button | Right Value Card ── -->
        <div class="row g-3 align-items-stretch" data-aos="fade-up" data-aos-delay="200">
            
            <!-- Left Feature Card: A2 Vedic Churning -->
            <div class="col-12 col-md-4 d-flex">
                <div class="story-feature-card w-100 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden" 
                         style="width: 48px; height: 48px; background: rgba(196, 154, 69, 0.14); border: 1px solid rgba(196, 154, 69, 0.35); padding: 6px;">
                        <img src="{{ asset('images/svgs/Rohida Farm Web Vector 03.svg') }}" alt="A2 Vedic Churning" width="34" height="34" style="object-fit: contain;">
                    </div>
                    <div>
                        <h6 class="fw-bold font-heading m-0 text-dark" style="color: #5C3D2E !important; font-size: 0.95rem;">
                            A2 Vedic Churning
                        </h6>
                        <small style="color: #705849; font-size: 0.8rem; line-height: 1.4; display: block; margin-top: 2px;">
                            Handmade in earthenware pots using bi-directional wooden bilona in small batches.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Center Button: Discover Our Story -->
            <div class="col-12 col-md-4 d-flex align-items-center justify-content-center">
                <a href="{{ route('about') }}" 
                   class="btn btn-story-primary w-100 w-md-auto px-4 py-2.5 rounded-pill font-heading fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2" 
                   style="font-size: 0.92rem; min-height: 48px;">
                    <i class="fa-solid fa-book-open"></i>
                    <span>Discover Our Story</span>
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>

            <!-- Right Feature Card: Ethical Grass-Fed Dairy -->
            <div class="col-12 col-md-4 d-flex">
                <div class="story-feature-card w-100 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden" 
                         style="width: 48px; height: 48px; background: rgba(196, 154, 69, 0.14); border: 1px solid rgba(196, 154, 69, 0.35); padding: 6px;">
                        <img src="{{ asset('images/svgs/Rohida Farm Web Vector 07.svg') }}" alt="Ethical Grass-Fed Dairy" width="34" height="34" style="object-fit: contain;">
                    </div>
                    <div>
                        <h6 class="fw-bold font-heading m-0 text-dark" style="color: #5C3D2E !important; font-size: 0.95rem;">
                            Ethical Grass-Fed Dairy
                        </h6>
                        <small style="color: #705849; font-size: 0.8rem; line-height: 1.4; display: block; margin-top: 2px;">
                            Pure indigenous Tharparkar cows roaming freely in open organic pastures.
                        </small>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
/* ── Story Video Card ── */
.story-video-card {
    background: #000000;
    border: 2px solid #5C3D2E;
    border-radius: 2.2rem;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(92, 61, 46, 0.08);
    position: relative;
    cursor: pointer;
    transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s ease;
    aspect-ratio: 16 / 9;
    max-height: 540px;
    width: 100%;
}

.story-video-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 40px rgba(92, 61, 46, 0.14);
}

.story-feature-card {
    background: #FFFFFF;
    border: 2px solid #5C3D2E;
    border-radius: 1.5rem;
    padding: 1rem 1.2rem;
    box-shadow: 0 4px 14px rgba(92, 61, 46, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.story-feature-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(92, 61, 46, 0.09);
}

.btn-story-primary {
    background-color: #5C3D2E !important;
    border: 2px solid #5C3D2E !important;
    color: #FFFFFF !important;
    transition: all 0.25s ease;
}

.btn-story-primary:hover {
    background-color: #4A2E1B !important;
    border-color: #4A2E1B !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(92, 61, 46, 0.2) !important;
}

@media (max-width: 767.98px) {
    .story-video-card {
        border-radius: 1.5rem;
        aspect-ratio: 4 / 3;
        max-height: 380px;
    }
    .story-feature-card {
        border-radius: 1.25rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoCard = document.getElementById('storyVideoCard');
    const videoEl = document.getElementById('storyVideoElement');

    if (!videoCard || !videoEl) return;

    videoEl.muted = true;

    // ── Auto Play Video on Scroll into View (Muted) ──
    const videoObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                videoEl.muted = true;
                const playPromise = videoEl.play();
                if (playPromise !== undefined) {
                    playPromise.catch(function() {
                        // Autoplay prevented, fail silently
                    });
                }
            } else {
                videoEl.pause();
            }
        });
    }, {
        root: null,
        rootMargin: '0px',
        threshold: 0.25
    });

    videoObserver.observe(videoEl);

    // ── Click anywhere on video card to toggle Play / Pause in-place ──
    videoCard.addEventListener('click', function(e) {
        if (videoEl.paused) {
            videoEl.play();
        } else {
            videoEl.pause();
        }
    });
});
</script>

