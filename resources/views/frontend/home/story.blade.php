<!-- ── Rohida Farm Story Section ── -->
<section class="py-5 overflow-hidden" id="our-story" style="background-color: var(--cream-bg);">
    <div class="container py-md-4">
        <div class="row align-items-center g-5">
            <!-- Left Image Column with Stacked Cards -->
            <div class="col-lg-6 position-relative" data-aos="fade-right">
                <div class="position-relative mx-auto" style="max-width: 520px;">
                    <div class="rounded-4 overflow-hidden shadow-lg border" style="border-color: var(--border-color) !important; aspect-ratio: 4/3;">
                        <img src="{{ asset('images/baner-1.png') }}" class="w-100 h-100 object-fit-cover" alt="Rohida Farm Tharparkar Cows & Heritage" loading="lazy" decoding="async">
                    </div>
                    <!-- Floating Heritage Card -->
                    <div class="position-absolute bottom-0 start-0 translate-middle-y bg-white p-3 p-md-4 rounded-4 shadow-lg border ms-2 mb-n4" style="border-color: var(--border-color) !important; max-width: 260px; z-index: 2;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center text-white" style="width: 50px; height: 50px; background-color: var(--primary-brown); flex-shrink: 0;">
                                <i class="bi bi-heart-fill fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold font-heading m-0 text-dark" style="font-size: 0.9rem;">Cruelty Free</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">Calves fed first before milking</small>
                            </div>
                        </div>
                    </div>
                    <!-- Decorative Badge top right -->
                    <div class="position-absolute top-0 end-0 translate-middle-y bg-warning text-dark p-3 rounded-circle shadow-md me-n3 mt-3 d-flex flex-column align-items-center justify-content-center" style="width: 90px; height: 90px; border: 3px solid #FFF; font-weight: 700; line-height: 1.1;">
                        <span style="font-size: 1.1rem; font-family: var(--font-heading);">100%</span>
                        <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;">Pure A2</span>
                    </div>
                </div>
            </div>

            <!-- Right Content Column -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="ps-lg-3">
                    <span class="text-uppercase fw-bold font-heading text-success mb-2 d-block" style="font-size: 0.8rem; letter-spacing: 2px; color: var(--primary-brown) !important;">
                        <i class="bi bi-flower2 me-1"></i> Our Roots & Heritage
                    </span>
                    <h2 class="display-5 font-heading fw-bold text-dark mb-3" style="line-height: 1.2;">
                        The Rohida Farm Journey & Traditional Values
                    </h2>
                    <p class="text-muted fs-6 mb-4" style="line-height: 1.8;">
                        Born from a passion to revive ancient Vedic agriculture, <strong>Rohida Farm</strong> is dedicated to protecting pure indigenous <em>Tharparkar cows</em>. Unlike commercial dairy operations, our farm nurtures cows in natural open pastures, feeding them organic green fodder and medicinal herbs.
                    </p>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 border shadow-sm" style="border-color: var(--border-color) !important;">
                                <i class="bi bi-check-circle-fill text-success fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold font-heading m-0 text-dark" style="font-size: 0.9rem;">A2 Vedic Churning</h6>
                                    <small class="text-muted" style="font-size: 0.78rem;">Handmade using wooden bilona in small batches.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-start gap-3 p-3 bg-white rounded-3 border shadow-sm" style="border-color: var(--border-color) !important;">
                                <i class="bi bi-shield-heart-fill text-success fs-4 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold font-heading m-0 text-dark" style="font-size: 0.9rem;">Hormone-Free Purity</h6>
                                    <small class="text-muted" style="font-size: 0.78rem;">Zero oxytocin, antibiotics, or synthetic chemicals.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('about') }}" class="btn btn-premium px-4 py-2.5 rounded-pill font-heading" style="font-size: 0.88rem;">
                            Discover Our Story <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#labReportsModal" class="btn btn-outline-success px-4 py-2.5 rounded-pill font-heading" style="font-size: 0.88rem; border-color: var(--dark-green); color: var(--dark-green);">
                            <i class="bi bi-file-earmark-check me-1"></i> Lab Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
