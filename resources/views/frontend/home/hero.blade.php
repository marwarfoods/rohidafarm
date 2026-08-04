<!-- ── Hero Banner Section (Dynamic Slider & Premium Tharparkar/Bilona Fallback) ── -->
@push('preload')
    @if(isset($sliders) && $sliders->isNotEmpty())
        @php $firstSlide = $sliders->first(); @endphp
        <link rel="preload" as="image" href="{{ asset($firstSlide->image_path) }}" fetchpriority="high">
        @if($firstSlide->mobile_image_path)
            <link rel="preload" as="image" href="{{ asset($firstSlide->mobile_image_path) }}" media="(max-width: 767px)" fetchpriority="high">
        @endif
    @endif
@endpush

<section class="hero-slider-section position-relative overflow-hidden" style="border-bottom: 1px solid var(--border-color); background-color: var(--cream-bg); padding: 0 !important;">
    <!-- Section Skeleton Overlay -->
    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none;">
        <div class="skeleton-block w-100 h-100" style="border-radius: 0;"></div>
    </div>

    @if(isset($sliders) && $sliders->isNotEmpty())
        <!-- Swiper Dynamic Slider -->
        <div class="swiper hero-slider" style="height: auto;">
            <div class="swiper-wrapper">
                @foreach($sliders as $slide)
                    <div class="swiper-slide h-auto">
                        @if($slide->button_url)
                            <a href="{{ $slide->button_url }}" class="d-block w-100">
                                <picture>
                                    @if($slide->mobile_image_path)
                                        <source media="(max-width: 767px)" srcset="{{ asset($slide->mobile_image_path) }}">
                                    @endif
                                    <img src="{{ asset($slide->image_path) }}" class="w-100 d-block"
                                         style="height: auto; max-height: 580px; object-fit: cover;"
                                         width="1920" height="580"
                                         alt="{{ $slide->title ?? 'RohidaFarm Premium Banner' }}"
                                         fetchpriority="high"
                                         loading="eager"
                                         decoding="async">
                                </picture>
                            </a>
                        @else
                            <div class="w-100">
                                <picture>
                                    @if($slide->mobile_image_path)
                                        <source media="(max-width: 767px)" srcset="{{ asset($slide->mobile_image_path) }}">
                                    @endif
                                    <img src="{{ asset($slide->image_path) }}" class="w-100 d-block"
                                         style="height: auto; max-height: 580px; object-fit: cover;"
                                         alt="{{ $slide->title ?? 'RohidaFarm Premium Banner' }}"
                                         fetchpriority="high"
                                         loading="eager"
                                         decoding="async">
                                </picture>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    @else
        <!-- Premium Fallback Hero Card: Tharparkar Cows & Bilona Process -->
        <div class="py-5 py-lg-6 position-relative" style="background: linear-gradient(135deg, #1B5E20 0%, #122112 50%, #3D220F 100%); color: var(--white); min-height: 520px; display: flex; align-items: center;">
            <div class="position-absolute top-0 end-0 w-50 h-100 opacity-25 d-none d-lg-block" style="background-image: url('{{ asset('images/baner-1.png') }}'); background-size: cover; background-position: center; mask-image: linear-gradient(to left, rgba(0,0,0,1), rgba(0,0,0,0)); -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1), rgba(0,0,0,0));"></div>
            
            <div class="container position-relative" style="z-index: 2;">
                <div class="row align-items-center">
                    <div class="col-lg-7 text-center text-lg-start">
                        <!-- Badges pill -->
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(212, 160, 23, 0.2); border: 1px solid var(--gold-accent);">
                            <i class="bi bi-star-fill text-warning fs-6"></i>
                            <span class="text-white font-heading fw-bold" style="font-size: 0.82rem; letter-spacing: 1px; text-transform: uppercase;">
                                Authentic A2 Tharparkar Cow Ghee & Bilona Method
                            </span>
                        </div>

                        <h1 class="display-4 font-heading fw-bold text-white mb-3" style="line-height: 1.15;">
                            Pure Traditional A2 Bilona Ghee <br>
                            <span style="color: var(--gold-accent);">Direct From Rohida Farm</span>
                        </h1>

                        <p class="fs-5 mb-4 text-white-50 mx-auto mx-lg-0" style="max-width: 580px; line-height: 1.6;">
                            Handcrafted using the ancient 5-stage Bilona process from grass-fed Tharparkar cows. 100% natural, lab-tested, granular, and free from preservatives.
                        </p>

                        <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3 mb-4">
                            <a href="{{ route('shop.index') }}" class="btn btn-gold btn-lg px-5 py-3 rounded-pill font-heading text-uppercase shadow-lg" style="font-size: 0.95rem; letter-spacing: 0.5px;">
                                Shop Now <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                            <a href="#bilona-process" class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill font-heading" style="font-size: 0.95rem;">
                                <i class="bi bi-play-circle me-2"></i> Bilona Process
                            </a>
                        </div>

                        <!-- Mini Trust Row -->
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start gap-4 pt-2 text-white-50" style="font-size: 0.85rem;">
                            <span class="d-flex align-items-center gap-1.5"><i class="bi bi-patch-check-fill text-warning"></i> FSSAI Certified</span>
                            <span class="d-flex align-items-center gap-1.5"><i class="bi bi-shield-check text-success"></i> NABL Lab Tested</span>
                            <span class="d-flex align-items-center gap-1.5"><i class="bi bi-truck text-warning"></i> Free Delivery Across India</span>
                        </div>
                    </div>

                    <!-- Right Column: Hero Visual Product & Cow Highlight Card -->
                    <div class="col-lg-5 mt-5 mt-lg-0 text-center">
                        <div class="position-relative d-inline-block">
                            <div class="rounded-circle mx-auto p-3 shadow-lg" style="background: radial-gradient(circle, rgba(212,160,23,0.3) 0%, rgba(27,94,32,0) 70%);">
                                <img src="{{ asset('images/baner-1.png') }}" class="img-fluid rounded-4 shadow-lg border border-warning" style="max-height: 420px; object-fit: cover;" alt="Rohida Farm A2 Ghee & Tharparkar Cow">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
