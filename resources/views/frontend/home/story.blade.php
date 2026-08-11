<!-- ── Our Roots & Heritage (Rohida Farm Story Section) ── -->
<section class="py-5 story-kasutam-section position-relative overflow-hidden" id="our-story"
    style="background: url('{{ asset('images/Our Roots & Heritage-bg.jpg') }}') center center / cover no-repeat;">

    {{-- Full-width warm cream overlay so text stays readable --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(250, 247, 238, 0.88); z-index: 0; pointer-events: none;"></div>

    <div class="container py-md-4 position-relative" style="z-index: 1;">
        <div class="row align-items-center g-4 g-lg-5">

            
            <!-- ── Left Column: Main Heritage Image & Floating Badges ── -->
            <div class="col-lg-6 position-relative" data-aos="fade-right">
                <div class="position-relative mx-auto" style="max-width: 540px;">
                    
                    <div class="story-img-card">
                        <img src="{{ file_exists(public_path('images/our-roots-heritage.jpg')) ? asset('images/our-roots-heritage.jpg') : asset('images/baner-1.png') }}" 
                             alt="Rohida Farm Tharparkar Cows &amp; Pasture Heritage" 
                             loading="lazy" 
                             decoding="async">
                    </div>

                    <!-- Floating Cruelty-Free Badge (Bottom Left) -->
                    <div class="position-absolute bottom-0 start-0 translate-middle-y bg-white p-3 p-md-3.5 story-badge-floating ms-2 ms-sm-3 mb-n4" 
                         style="max-width: 270px; z-index: 3;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                                 style="width: 46px; height: 46px; background: linear-gradient(135deg, #5C3D2E 0%, #3E2723 100%);">
                                <i class="bi bi-heart-fill text-warning fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold font-heading m-0" style="color: #5C3D2E; font-size: 0.92rem;">Cruelty-Free Milking</h6>
                                <small style="color: #705849; font-size: 0.76rem; line-height: 1.3; display: block;">Calves fed first with love before milking</small>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Pure A2 Circle Badge (Top Right) -->
                    <div class="position-absolute top-0 end-0 translate-middle-y me-n2 me-sm-n3 mt-3 d-flex flex-column align-items-center justify-content-center text-center text-white rounded-circle shadow-lg" 
                         style="width: 94px; height: 94px; background: #5C3D2E; border: 3px solid #FAF7EE; z-index: 3;">
                        <span class="font-heading fw-bold text-warning" style="font-size: 1.2rem; line-height: 1;">100%</span>
                        <span class="font-heading fw-semibold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px; opacity: 0.95;">Pure A2</span>
                    </div>

                </div>
            </div>

            <!-- ── Right Column: Story Text & Feature Cards ── -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="ps-lg-2">
                    
                    <!-- Subtitle Pill Badge -->
                    <div class="d-inline-flex align-items-center gap-2 px-3.5 py-1.5 rounded-pill font-heading fw-bold text-uppercase mb-3" 
                         style="font-size: 0.8rem; letter-spacing: 2px; color: #5C3D2E; background: rgba(196, 154, 69, 0.15); border: 1px solid rgba(196, 154, 69, 0.35);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L14.5 9H22L16 13.5L18.5 20.5L12 16L5.5 20.5L8 13.5L2 9H9.5L12 2Z" fill="#C49A45"/>
                        </svg>
                        <span>Our Roots &amp; Heritage</span>
                    </div>

                    <!-- Section Title -->
                    <h2 class="display-5 font-heading fw-bold mb-3" style="color: #5C3D2E; line-height: 1.2;">
                        The Rohida Farm Journey &amp; Traditional Values
                    </h2>

                    <!-- Main Story Paragraph -->
                    <p class="fs-6 mb-4" style="color: #6E5B4F; line-height: 1.75;">
                        Born from a passion to revive ancient Vedic agriculture, <strong>Rohida Farm</strong> is dedicated to nurturing pure indigenous <em>Tharparkar cows</em>. Unlike commercial dairy operations, our cows roam freely in open organic pastures, grazing on green fodder and medicinal herbs under natural sunshine.
                    </p>

                    <!-- Feature Cards Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="story-feature-card d-flex align-items-start gap-3">
                                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 38px; height: 38px; background: rgba(196, 154, 69, 0.15); color: #C49A45;">
                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold font-heading m-0" style="color: #5C3D2E; font-size: 0.92rem;">A2 Vedic Churning</h6>
                                    <small style="color: #705849; font-size: 0.78rem;">Handmade using wooden bilona in small batches.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="story-feature-card d-flex align-items-start gap-3">
                                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 38px; height: 38px; background: rgba(196, 154, 69, 0.15); color: #C49A45;">
                                    <i class="bi bi-shield-heart-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold font-heading m-0" style="color: #5C3D2E; font-size: 0.92rem;">Hormone-Free Purity</h6>
                                    <small style="color: #705849; font-size: 0.78rem;">Zero oxytocin, antibiotics, or synthetic chemicals.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="{{ route('about') }}" class="btn btn-story-primary px-4 py-2.5 rounded-pill font-heading fw-bold" style="font-size: 0.88rem;">
                            Discover Our Story <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#labReportsModal" class="btn btn-story-outline px-4 py-2.5 rounded-pill font-heading fw-bold" style="font-size: 0.88rem;">
                            <i class="bi bi-file-earmark-check me-1"></i> Lab Reports
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

