<!-- Our Specialties Minimalistic Section -->
<section class="py-5" id="specialtiesSection" style="background-color: var(--cream-bg, #FAF9F6);">
    <div class="container py-5">
        
        <!-- Header -->
        <div class="text-center mb-5 pb-3" data-aos="fade-up">
            <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">The RohidaFarm Difference</span>
            <h2 class="display-5 font-heading fw-bold mt-2 text-dark">Our Core Values</h2>
            <p class="text-muted mx-auto mt-3" style="max-width: 600px; font-size: 1.05rem;">Discover what makes our products unique, pure, and naturally healthy for your family.</p>
        </div>
        
        <div class="position-relative">
            <!-- Section Skeleton Overlay -->
            <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100" style="z-index: 10; pointer-events: none; background-color: var(--cream-bg, #FAF9F6);">
                <div class="row g-4 g-lg-5 justify-content-center">
                    @for($i = 0; $i < 4; $i++)
                        <div class="col-lg-3 col-md-6 col-6 text-center">
                            <div class="skeleton-block mx-auto mb-3" style="width: 70px; height: 70px; border-radius: 50%;"></div>
                            <div class="skeleton-block mx-auto mb-2" style="height: 16px; width: 60%;"></div>
                            <div class="skeleton-block mx-auto d-none d-md-block mb-1" style="height: 10px; width: 80%;"></div>
                            <div class="skeleton-block mx-auto d-none d-md-block" style="height: 10px; width: 65%;"></div>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="row g-4 g-lg-5 justify-content-center">
                <!-- Value 1 -->
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 bg-transparent text-center px-xl-2 feature-card-minimal">
                        <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; background-color: #fff; color: var(--primary-green);">
                            <i class="bi bi-flower1" style="font-size: 1.8rem;"></i>
                        </div>
                        <h4 class="fw-bold font-heading mb-2 fs-5 text-dark">100% Organic</h4>
                        <p class="text-muted d-none d-md-block" style="font-size: 0.9rem; line-height: 1.5;">Sourced directly from our own organic pastures, completely free from pesticides.</p>
                    </div>
                </div>

                <!-- Value 2 -->
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 bg-transparent text-center px-xl-2 feature-card-minimal">
                        <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; background-color: #fff; color: var(--primary-green);">
                            <i class="bi bi-arrow-repeat" style="font-size: 1.8rem;"></i>
                        </div>
                        <h4 class="fw-bold font-heading mb-2 fs-5 text-dark">Traditional Bilona</h4>
                        <p class="text-muted d-none d-md-block" style="font-size: 0.9rem; line-height: 1.5;">Cultured from curd and churned bidirectionally with wooden tools.</p>
                    </div>
                </div>

                <!-- Value 3 -->
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="card h-100 border-0 bg-transparent text-center px-xl-2 feature-card-minimal">
                        <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; background-color: #fff; color: var(--primary-green);">
                            <i class="bi bi-shield-check" style="font-size: 1.8rem;"></i>
                        </div>
                        <h4 class="fw-bold font-heading mb-2 fs-5 text-dark">Lab Tested</h4>
                        <p class="text-muted d-none d-md-block" style="font-size: 0.9rem; line-height: 1.5;">Every single batch undergoes strict safety checks to guarantee premium quality.</p>
                    </div>
                </div>

                <!-- Value 4 -->
                <div class="col-lg-3 col-md-6 col-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="card h-100 border-0 bg-transparent text-center px-xl-2 feature-card-minimal">
                        <div class="icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 70px; height: 70px; background-color: #fff; color: var(--primary-green);">
                            <i class="bi bi-heart" style="font-size: 1.8rem;"></i>
                        </div>
                        <h4 class="fw-bold font-heading mb-2 fs-5 text-dark">Ethically Sourced</h4>
                        <p class="text-muted d-none d-md-block" style="font-size: 0.9rem; line-height: 1.5;">Cruelty-free practices where calves are fed first. Happy cows, healthy products.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Banner Images (Responsive Full Width Edge-to-Edge) -->
@php
    $pcImg = \App\Models\Setting::get('home_section_image_pc') ?: 'images/home-image-pc.png';
    $mobImg = \App\Models\Setting::get('home_section_image_mobile') ?: 'images/home-image-mobile.png';
    $link = \App\Models\Setting::get('home_section_image_link');
@endphp
<div class="container-fluid p-0 position-relative" data-aos="fade-up" data-aos-offset="0">
    <!-- Section Skeleton Overlay -->
    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none;">
        <div class="skeleton-block w-100 h-100" style="border-radius: 0; min-height: 250px;"></div>
    </div>
    
    @if($link)
        <a href="{{ $link }}" class="d-block w-100">
    @endif
    
    <!-- Desktop Image -->
    <img src="{{ asset($pcImg) }}" class="img-fluid w-100 d-none d-md-block" alt="Rohida Farm Desktop Banner" loading="lazy" decoding="async">
    <!-- Mobile Image -->
    <img src="{{ asset($mobImg) }}" class="img-fluid w-100 d-block d-md-none" alt="Rohida Farm Mobile Banner" loading="lazy" decoding="async">
    
    @if($link)
        </a>
    @endif
</div>
