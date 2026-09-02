<!-- Promo Banners Section (Ghee, Oil, Honey) -->
<section class="py-5 bg-white border-bottom">
    <div class="container overflow-hidden">
        
        <!-- Section Heading Title -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="display-6 font-heading fw-bold mt-1 mb-1">Our Specialties</h2>
            <span class="text-uppercase fw-bold text-success" style="font-size: 0.72rem; letter-spacing: 2px;">Fresh & Traditional Specialties</span>
        </div>

        <div class="swiper promo-three-slider overflow-visible">
            <div class="swiper-wrapper">
                <!-- Banner 1: Ghee -->
                <div class="swiper-slide h-auto">
                    <div class="p-4 rounded-4 h-100 d-flex align-items-center justify-content-between position-relative overflow-hidden shadow-sm" style="background-color: #FFF9F2; border: 1px solid #ECE7DD; min-height: 190px;">
                        <div style="z-index: 2; max-width: 55%;">
                            <h4 class="fw-bold font-heading text-dark mb-1 fs-5">Pure Cow Ghee</h4>
                            <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.3;">Made the Traditional Bilona Way</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-premium btn-sm px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.7rem; letter-spacing: 0.5px;">Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        <img src="{{ url('uploads/products/1784099286_cow-ghee-1.png') }}" alt="Cow Ghee Product" width="150" height="150" loading="lazy" decoding="async" class="position-absolute end-0 bottom-0" style="height: 150px; object-fit: contain; transform: translateY(10px) translateX(10px); z-index: 1; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                    </div>
                </div>

                <!-- Banner 2: Cold Pressed Oil -->
                <div class="swiper-slide h-auto">
                    <div class="p-4 rounded-4 h-100 d-flex align-items-center justify-content-between position-relative overflow-hidden shadow-sm" style="background-color: #F4F7F4; border: 1px solid #ECE7DD; min-height: 190px;">
                        <div style="z-index: 2; max-width: 55%;">
                            <h4 class="fw-bold font-heading text-dark mb-1 fs-5">Cold Pressed Oils</h4>
                            <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.3;">100% Pure, Wood-Pressed & Natural</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-premium btn-sm px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.7rem; letter-spacing: 0.5px;">Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        <img src="{{ url('uploads/products/1784100896_oil-2.png') }}" alt="Cold Pressed Oil Product" width="150" height="150" loading="lazy" decoding="async" class="position-absolute end-0 bottom-0" style="height: 150px; object-fit: contain; transform: translateY(10px) translateX(10px); z-index: 1; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                    </div>
                </div>

                <!-- Banner 3: Combo Pack -->
                <div class="swiper-slide h-auto">
                    <div class="p-4 rounded-4 h-100 d-flex align-items-center justify-content-between position-relative overflow-hidden shadow-sm" style="background-color: #FFFDF9; border: 1px solid #ECE7DD; min-height: 190px;">
                        <div style="z-index: 2; max-width: 55%;">
                            <h4 class="fw-bold font-heading text-dark mb-1 fs-5">Combo Pack</h4>
                            <p class="text-muted mb-3" style="font-size: 0.8rem; line-height: 1.3;">Ghee & Cold Pressed Oil</p>
                            <a href="{{ route('shop.index') }}" class="btn btn-premium btn-sm px-4 py-2 rounded-pill text-uppercase font-heading" style="font-size: 0.7rem; letter-spacing: 0.5px;">Shop Now <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        <img src="{{ asset('images/combo.png') }}" alt="Combo Pack" width="150" height="150" loading="lazy" decoding="async" class="position-absolute end-0 bottom-0" style="height: 150px; object-fit: contain; transform: translateY(10px) translateX(10px); z-index: 1; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                    </div>
                </div>
            </div>
            <!-- Pagination on Mobile only -->
            <div class="swiper-pagination d-block d-md-none mt-4"></div>
        </div>
    </div>
</section>
