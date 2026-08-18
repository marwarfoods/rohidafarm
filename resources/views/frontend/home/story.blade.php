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

                </div>
            </div>

            <!-- ── Right Column: Story Text & Feature Cards ── -->
            <div class="col-lg-6" data-aos="fade-left">
                <div class="ps-lg-2">
                    
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
                                    <i class="fa-solid fa-circle-check fs-5"></i>
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
                                    <i class="fa-solid fa-shield-heart fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold font-heading m-0" style="color: #5C3D2E; font-size: 0.92rem;">Lactose-Free Goodness</h6>
                                    <small style="color: #705849; font-size: 0.78rem;">Naturally free of lactose and easy on digestion.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <a href="{{ route('about') }}" class="btn btn-story-primary px-4 py-2.5 rounded-pill font-heading fw-bold" style="font-size: 0.88rem;">
                            Discover Our Story <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

