<!-- Hero Section -->
<section class="about-hero-section">
    <div class="container py-4">
        <div class="row g-5 align-items-center">

            <!-- Left Column: Big Heading -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content-wrapper">
                    <span class="hero-badge">
                        <i class="fa-solid fa-leaf me-2"></i>About Rohida Farm
                    </span>

                    <h1 class="hero-title">
                        Everything Has A Story.<br>
                        <span class="highlight-text">Here’s Ours.</span>
                    </h1>

                    <p class="hero-subtitle">
                        Before the sun was fully up, Dadi would already be sitting on the aangan, a matka of curd between her knees, the madani spinning between her palms — the same rhythm, the same hands, that had been doing this for as long as anyone in the family could remember.
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
                        <a href="#our-story" class="btn btn-hero-primary px-4 py-3 rounded-pill font-heading">
                            Our Story <i class="fa-solid fa-arrow-down ms-2 fs-6"></i>
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn btn-hero-outline px-4 py-3 rounded-pill font-heading">
                            Explore Products <i class="fa-solid fa-arrow-right ms-2 fs-6"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Column: SVG Backdrop -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="hero-visual-container position-relative">
                    <img src="{{ asset('images/svgs/Rohida Farm Web Vector 01.svg') }}" alt="" class="hero-bg-vector" aria-hidden="true">
                </div>
            </div>

        </div>

        <!-- Traditional Vedic Bilona Process Illustration Banner -->
        <div class="hero-trust-banner text-center mt-3 pt-3" data-aos="fade-up">
            <img src="{{ asset('images/proses.png') }}" 
                 alt="Traditional Vedic Bilona Process" 
                 class="img-fluid hero-trust-vector-img" 
                 loading="lazy">
        </div>
    </div>
</section>
