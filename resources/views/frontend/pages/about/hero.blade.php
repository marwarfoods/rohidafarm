<!-- Hero Section (2-Column Layout) -->
<section class="about-hero-section">
    <div class="container py-4">
        <div class="row g-5 align-items-center">
            
            <!-- Left Column: Content -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content-wrapper">
                    <span class="hero-badge">
                        <i class="fa-solid fa-leaf me-2 text-success"></i>About Rohida Farm
                    </span>
                    
                    <h1 class="hero-title">
                        Rooted In Rajasthan.<br>
                        Crafted With <span class="highlight-text">Tradition</span>.<br>
                        Shared With Every Home.
                    </h1>
                    
                    <p class="hero-subtitle">
                        At <strong>Rohida Farm</strong>, we believe that true nutrition begins with honesty—honest farming, honest ingredients, and honest methods.
                    </p>

                    <!-- Feature Highlights Pills -->
                    <div class="hero-feature-tags d-flex flex-wrap gap-2 my-4">
                        <span class="feature-tag"><i class="fa-solid fa-check text-success me-1"></i> A2 Tharparkar Cows</span>
                        <span class="feature-tag"><i class="fa-solid fa-check text-success me-1"></i> Hand-Churned Bilona</span>
                        <span class="feature-tag"><i class="fa-solid fa-check text-success me-1"></i> 100% Pure & Natural</span>
                    </div>

                    <!-- Call To Action Buttons -->
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

            <!-- Right Column: Visual Art & Floating Badge -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="hero-visual-container position-relative">
                    <div class="hero-main-img-card shadow-lg rounded-4 overflow-hidden border">
                        <img src="{{ asset('assets/images/about/hero.jpg') }}" onerror="this.onerror=null;this.src='/assets/images/products/placeholder.jpg';" class="img-fluid w-100 object-fit-cover" style="min-height: 420px; max-height: 500px;" alt="Rohida Farm Pure Vedic Ghee">
                    </div>
                    
                    <!-- Floating Experience/Purity Badge -->
                    <div class="hero-floating-badge shadow-md bg-white p-3 rounded-4 d-flex align-items-center gap-3">
                        <div class="badge-icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                            <i class="fa-solid fa-award fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark font-heading">100% Vedic Bilona Process</h6>
                            <small class="text-muted">Zero Preservatives • Zero Chemicals</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
