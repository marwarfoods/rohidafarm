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
                        Tradition You Can <span class="highlight-text">Taste</span><br>
                        Purity You Can Trust
                    </h1>

                    <p class="hero-subtitle">
                        At <strong>Rohida Farm</strong>, we believe that true nutrition begins with honesty — honest farming, honest ingredients, and honest methods, rooted in the wisdom of Rajasthan.
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

        <!-- Trust Icon Row -->
        <div class="hero-trust-row row g-2 mt-3 justify-content-center" data-aos="fade-up">
            <div class="col-4 col-md-3 text-center">
                <div class="trust-icon">
                    <img src="{{ asset('images/svgs/Rohida Farm Web Vector 04.svg') }}" alt="Purity">
                </div>
                <h6>Purity</h6>
            </div>
            <div class="col-4 col-md-3 text-center">
                <div class="trust-icon">
                    <img src="{{ asset('images/svgs/Rohida Farm Web Vector 03.svg') }}" alt="Tradition">
                </div>
                <h6>Tradition</h6>
            </div>
            <div class="col-4 col-md-3 text-center">
                <div class="trust-icon">
                    <img src="{{ asset('images/svgs/Rohida Farm Web Vector 07.svg') }}" alt="No Adulteration">
                </div>
                <h6>No Adulteration</h6>
            </div>
        </div>
    </div>
</section>
