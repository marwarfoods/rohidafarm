<!-- Why Choose Us Section -->
<style>
    .feature-card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .feature-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
</style>

<section class="pt-5" style="background-color: var(--cream-bg);">
    <div class="container mb-5 pb-4">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">The RohidaFarm Difference</span>
            <h2 class="display-5 font-heading fw-bold mt-1">Why Choose Us?</h2>
        </div>

        <div class="row g-4 align-items-center">
            <!-- Large Featured Card -->
            <div class="col-lg-6" data-aos="fade-right">
                <div class="p-5 rounded-4 h-100 position-relative overflow-hidden shadow-sm feature-card-hover" style="background: linear-gradient(135deg, var(--primary-green), #165c2b); color: white;">
                    <!-- Decorative background element -->
                    <div class="position-absolute" style="top: -20px; right: -20px; opacity: 0.1; transform: rotate(15deg);">
                        <i class="bi bi-flower1" style="font-size: 18rem;"></i>
                    </div>
                    
                    <div class="position-relative" style="z-index: 2;">
                        <div class="icon-box mb-4 bg-white text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <h3 class="fw-bold font-heading mb-3 display-6">100% Organic & Pure</h3>
                        <p class="mb-0 fs-5" style="opacity: 0.9; max-width: 95%;">Sourced directly from our own organic pastures, completely free from pesticides, antibiotics, and harmful chemical processing. Pure nature in every drop.</p>
                    </div>
                </div>
            </div>

            <!-- Stacked Smaller Cards -->
            <div class="col-lg-6">
                <div class="d-flex flex-column gap-4">
                    
                    <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-start gap-4 feature-card-hover" data-aos="fade-left" data-aos-delay="100" style="border-color: var(--border-color) !important;">
                        <div class="icon-box text-success flex-shrink-0" style="font-size: 3.5rem; line-height: 1;">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold font-heading fs-4 mb-2">Traditional Bilona Churning</h5>
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Cultured from curd and churned bidirectionally with wooden tools to preserve vital enzymes and original aroma.</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-4 bg-white border shadow-sm d-flex align-items-start gap-4 feature-card-hover" data-aos="fade-left" data-aos-delay="200" style="border-color: var(--border-color) !important;">
                        <div class="icon-box text-success flex-shrink-0" style="font-size: 3.5rem; line-height: 1;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold font-heading fs-4 mb-2">Lab Tested & Secure</h5>
                            <p class="text-muted mb-0" style="font-size: 0.95rem;">Every single batch undergoes strict safety and quality checks to guarantee premium nutrients before it reaches you.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Banner Images (Responsive Full Width Edge-to-Edge) -->
    <div class="container-fluid p-0 mt-3" data-aos="fade-up" data-aos-delay="300" data-aos-offset="0">
        <!-- Desktop Image -->
        <img src="{{ asset('images/home-image-pc.png') }}" class="img-fluid w-100 d-none d-md-block" width="1920" height="600" loading="lazy" decoding="async" alt="Rohida Farm - Pure Organic Ghee Desktop Banner">
        <!-- Mobile Image -->
        <img src="{{ asset('images/home-image-mobile.png') }}" class="img-fluid w-100 d-block d-md-none" width="640" height="480" loading="lazy" decoding="async" alt="Rohida Farm - Pure Organic Ghee Mobile Banner">
    </div>
</section>
