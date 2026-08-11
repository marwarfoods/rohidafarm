<!-- ── The Traditional Journey Of Our Ghee (2-Column: Left BG visible, Right Steps) ── -->
<section class="py-3 py-md-4 bilona-kasutam-section position-relative overflow-hidden" id="bilona-process"
    style="background: url('{{ asset('images/Vedic Craftsmanship.png') }}') left bottom / cover no-repeat; min-height: 480px;">

    {{-- Full-width overlay — image shows through uniformly on both left & right --}}
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(250, 247, 238, 0.82); z-index: 0; pointer-events: none;"></div>

    <div class="container-fluid px-0 py-md-2 position-relative" style="z-index: 1;">
        <div class="row g-0 align-items-stretch" style="min-height: 460px;">

            {{-- LEFT COLUMN — empty, shows BG image (40%) --}}
            <div class="col-lg-5 d-none d-lg-block"></div>

            {{-- RIGHT COLUMN — heading + all 5 steps (60%) --}}
            <div class="col-12 col-lg-7">
                <div class="py-3 py-md-4 px-3 px-lg-5">

                    <!-- ── Section Header ── -->
                    <div class="mb-2" data-aos="fade-up">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill font-heading fw-bold text-uppercase mb-3"
                             style="font-size: 0.78rem; letter-spacing: 2px; color: #5C3D2E; background: rgba(196, 154, 69, 0.18); border: 1px solid rgba(196, 154, 69, 0.4);">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L14.5 9H22L16 13.5L18.5 20.5L12 16L5.5 20.5L8 13.5L2 9H9.5L12 2Z" fill="#C49A45"/>
                            </svg>
                            <span>Vedic Craftsmanship</span>
                        </div>
                        <h2 class="display-6 font-heading fw-bold mb-2" style="color: #5C3D2E;">The Traditional Journey Of Our Ghee</h2>
                        <p class="fs-6 mb-0" style="color: #6E5B4F; max-width: 580px; line-height: 1.7;">
                            Crafted strictly according to Shastras using 100% A2 Tharparkar Cow milk, bi-directional wooden churners, and earthenware pots.
                        </p>
                    </div>

                    <!-- ── Steps Timeline ── -->
                    <div class="kasutam-timeline-wrapper ps-1 ps-sm-2 mt-2">

                        <div class="kasutam-dashed-line"></div>

                        <!-- Step 1 -->
                        <div class="kasutam-step-item d-flex align-items-center gap-3 gap-md-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="kasutam-num-badge">1</div>
                            <div class="kasutam-card">
                                <div class="kasutam-scalloped-frame">
                                    <svg viewBox="0 0 100 100" class="kasutam-scalloped-svg" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 50,4 C 53,4 55,7 58,8 C 61,9 64,7 67,9 C 70,11 71,14 74,17 C 77,20 80,21 82,25 C 84,29 83,32 85,36 C 87,40 90,42 90,46 C 90,50 87,52 85,56 C 83,60 84,63 82,67 C 80,71 77,72 74,75 C 71,78 70,81 67,83 C 64,85 61,83 58,84 C 55,85 53,88 50,88 C 47,88 45,85 42,84 C 39,83 36,85 33,83 C 30,81 29,78 26,75 C 23,72 20,71 18,67 C 16,63 17,60 15,56 C 13,52 10,50 10,46 C 10,42 13,40 15,36 C 17,32 16,29 18,25 C 20,21 23,20 26,17 C 29,14 30,11 33,9 C 36,7 39,9 42,8 C 45,7 47,4 50,4 Z" fill="none" stroke="#C49A45" stroke-width="3"/>
                                    </svg>
                                    <img src="{{ file_exists(public_path('images/bilona-step-1.jpg')) ? asset('images/bilona-step-1.jpg') : 'https://images.unsplash.com/photo-1546445317-29f4545f9d52?q=80&w=400&auto=format&fit=crop' }}"
                                         alt="Milking with care" class="kasutam-step-img">
                                </div>
                                <div>
                                    <h3 class="kasutam-card-title">Milking with care</h3>
                                    <p class="kasutam-card-desc">A2 Desi Cows are Milked by Hand, Honoring Ancient Ayurvedic traditions for purity and authenticity.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="kasutam-step-item d-flex align-items-center gap-3 gap-md-4" data-aos="fade-up" data-aos-delay="200">
                            <div class="kasutam-num-badge">2</div>
                            <div class="kasutam-card">
                                <div class="kasutam-scalloped-frame">
                                    <svg viewBox="0 0 100 100" class="kasutam-scalloped-svg" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 50,4 C 53,4 55,7 58,8 C 61,9 64,7 67,9 C 70,11 71,14 74,17 C 77,20 80,21 82,25 C 84,29 83,32 85,36 C 87,40 90,42 90,46 C 90,50 87,52 85,56 C 83,60 84,63 82,67 C 80,71 77,72 74,75 C 71,78 70,81 67,83 C 64,85 61,83 58,84 C 55,85 53,88 50,88 C 47,88 45,85 42,84 C 39,83 36,85 33,83 C 30,81 29,78 26,75 C 23,72 20,71 18,67 C 16,63 17,60 15,56 C 13,52 10,50 10,46 C 10,42 13,40 15,36 C 17,32 16,29 18,25 C 20,21 23,20 26,17 C 29,14 30,11 33,9 C 36,7 39,9 42,8 C 45,7 47,4 50,4 Z" fill="none" stroke="#C49A45" stroke-width="3"/>
                                    </svg>
                                    <img src="{{ file_exists(public_path('images/bilona-step-2.jpg')) ? asset('images/bilona-step-2.jpg') : 'https://images.unsplash.com/photo-1574316071802-0d684efa7bf5?q=80&w=400&auto=format&fit=crop' }}"
                                         alt="Heating Milk and Preparing Curd" class="kasutam-step-img">
                                </div>
                                <div>
                                    <h3 class="kasutam-card-title">Heating Milk and Preparing Curd</h3>
                                    <p class="kasutam-card-desc">Heat A2 milk in a clay pot, add curd culture, and let it ferment into rich curd.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 — uses stpe-3.png -->
                        <div class="kasutam-step-item d-flex align-items-center gap-3 gap-md-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="kasutam-num-badge">3</div>
                            <div class="kasutam-card">
                                <div class="kasutam-scalloped-frame">
                                    <svg viewBox="0 0 100 100" class="kasutam-scalloped-svg" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 50,4 C 53,4 55,7 58,8 C 61,9 64,7 67,9 C 70,11 71,14 74,17 C 77,20 80,21 82,25 C 84,29 83,32 85,36 C 87,40 90,42 90,46 C 90,50 87,52 85,56 C 83,60 84,63 82,67 C 80,71 77,72 74,75 C 71,78 70,81 67,83 C 64,85 61,83 58,84 C 55,85 53,88 50,88 C 47,88 45,85 42,84 C 39,83 36,85 33,83 C 30,81 29,78 26,75 C 23,72 20,71 18,67 C 16,63 17,60 15,56 C 13,52 10,50 10,46 C 10,42 13,40 15,36 C 17,32 16,29 18,25 C 20,21 23,20 26,17 C 29,14 30,11 33,9 C 36,7 39,9 42,8 C 45,7 47,4 50,4 Z" fill="none" stroke="#C49A45" stroke-width="3"/>
                                    </svg>
                                    <img src="{{ asset('images/steps/stpe-3.png') }}"
                                         alt="Traditionally wood churned" class="kasutam-step-img">
                                </div>
                                <div>
                                    <h3 class="kasutam-card-title">Traditionally wood churned</h3>
                                    <p class="kasutam-card-desc">The curd is churned using a wooden bilona, extracting rich and wholesome butter.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 — uses stpe-4.png -->
                        <div class="kasutam-step-item d-flex align-items-center gap-3 gap-md-4" data-aos="fade-up" data-aos-delay="400">
                            <div class="kasutam-num-badge">4</div>
                            <div class="kasutam-card">
                                <div class="kasutam-scalloped-frame">
                                    <svg viewBox="0 0 100 100" class="kasutam-scalloped-svg" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 50,4 C 53,4 55,7 58,8 C 61,9 64,7 67,9 C 70,11 71,14 74,17 C 77,20 80,21 82,25 C 84,29 83,32 85,36 C 87,40 90,42 90,46 C 90,50 87,52 85,56 C 83,60 84,63 82,67 C 80,71 77,72 74,75 C 71,78 70,81 67,83 C 64,85 61,83 58,84 C 55,85 53,88 50,88 C 47,88 45,85 42,84 C 39,83 36,85 33,83 C 30,81 29,78 26,75 C 23,72 20,71 18,67 C 16,63 17,60 15,56 C 13,52 10,50 10,46 C 10,42 13,40 15,36 C 17,32 16,29 18,25 C 20,21 23,20 26,17 C 29,14 30,11 33,9 C 36,7 39,9 42,8 C 45,7 47,4 50,4 Z" fill="none" stroke="#C49A45" stroke-width="3"/>
                                    </svg>
                                    <img src="{{ asset('images/steps/stpe-4.png') }}"
                                         alt="Slow Cooking the Butter" class="kasutam-step-img">
                                </div>
                                <div>
                                    <h3 class="kasutam-card-title">Slow Cooking the Butter</h3>
                                    <p class="kasutam-card-desc">This process evaporates water content and converts the butter into aromatic golden ghee.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5 — uses stpe-5.png -->
                        <div class="kasutam-step-item d-flex align-items-center gap-3 gap-md-4" data-aos="fade-up" data-aos-delay="500">
                            <div class="kasutam-num-badge">5</div>
                            <div class="kasutam-card">
                                <div class="kasutam-scalloped-frame">
                                    <svg viewBox="0 0 100 100" class="kasutam-scalloped-svg" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M 50,4 C 53,4 55,7 58,8 C 61,9 64,7 67,9 C 70,11 71,14 74,17 C 77,20 80,21 82,25 C 84,29 83,32 85,36 C 87,40 90,42 90,46 C 90,50 87,52 85,56 C 83,60 84,63 82,67 C 80,71 77,72 74,75 C 71,78 70,81 67,83 C 64,85 61,83 58,84 C 55,85 53,88 50,88 C 47,88 45,85 42,84 C 39,83 36,85 33,83 C 30,81 29,78 26,75 C 23,72 20,71 18,67 C 16,63 17,60 15,56 C 13,52 10,50 10,46 C 10,42 13,40 15,36 C 17,32 16,29 18,25 C 20,21 23,20 26,17 C 29,14 30,11 33,9 C 36,7 39,9 42,8 C 45,7 47,4 50,4 Z" fill="none" stroke="#C49A45" stroke-width="3"/>
                                    </svg>
                                    <img src="{{ asset('images/steps/stpe-5.png') }}"
                                         alt="Filtering and Packaging" class="kasutam-step-img">
                                </div>
                                <div>
                                    <h3 class="kasutam-card-title">Filtering and Packaging</h3>
                                    <p class="kasutam-card-desc">Once cooled, the pure Bilona A2 Ghee is carefully packaged to preserve its freshness and rich aroma.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
