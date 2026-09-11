@php
    $customerReviews = $customerReviews ?? collect();
    $displayReviews = $customerReviews;
    if ($customerReviews->count() > 1 && $customerReviews->count() < 6) {
        $multiplier = (int) ceil(6 / $customerReviews->count());
        $extended = collect();
        for ($i = 0; $i < $multiplier; $i++) {
            $extended = $extended->concat($customerReviews);
        }
        $displayReviews = $extended;
    }
@endphp

@if($customerReviews->isNotEmpty())
<!-- ── Customer Reviews (Photo one side · Review other side) ── -->
<section class="py-4 py-md-5 customer-reviews-section position-relative overflow-hidden" id="customer-reviews">
    <div class="container position-relative">

        <div class="text-center mb-4" data-aos="fade-up">
            <span class="text-uppercase fw-bold d-block mb-2" style="color: #8B5A2B; font-size: 0.8rem; letter-spacing: 2px;">Customer Love</span>
            <h2 class="font-heading fw-bold m-0" style="color: #362518;">What Our Customers Say</h2>
        </div>

        <div class="swiper customer-reviews-slider" data-aos="fade-up">
            <div class="swiper-wrapper">
                @foreach($displayReviews as $cr)
                    @php
                        $crImage = $cr->image_path ? asset($cr->image_path) : asset('images/baner-1.png');
                        $productUrl = $cr->product ? route('shop.show', $cr->product->slug) : null;
                    @endphp
                    <div class="swiper-slide">
                        <div class="cr-card">
                            <div class="cr-media">
                                <img src="{{ $crImage }}" alt="{{ $cr->customer_name }}" loading="lazy" decoding="async">
                            </div>
                            <div class="cr-body">
                                <h3 class="cr-title font-heading">{{ $cr->title }}</h3>
                                <div class="cr-stars" aria-label="{{ $cr->rating }} out of 5 stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $cr->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <p class="cr-text">{{ $cr->review }}</p>
                                <div class="cr-meta">
                                    <span class="cr-name">{{ $cr->customer_name }}</span>
                                    @if($productUrl)
                                        <a href="{{ $productUrl }}" class="cr-product">{{ $cr->product->name }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-3 mt-4">
            <button class="cr-nav customer-reviews-prev" aria-label="Previous review"><i class="bi bi-chevron-left"></i></button>
            <div class="customer-reviews-pagination swiper-pagination position-static"></div>
            <button class="cr-nav customer-reviews-next" aria-label="Next review"><i class="bi bi-chevron-right"></i></button>
        </div>

    </div>
</section>

<style>
.customer-reviews-section {
    background: #FAF7EE;
}
.customer-reviews-slider {
    overflow: hidden;
}
.customer-reviews-slider .swiper-slide {
    height: auto;
}
.cr-card {
    display: flex;
    flex-direction: column;
    background: #FFFFFF;
    border: 1px solid #ECE3D2;
    border-radius: 1.25rem;
    overflow: hidden;
    height: 100%;
    box-shadow: 0 10px 30px rgba(92, 61, 46, 0.06);
}
.cr-media {
    flex-shrink: 0;
    background: #F1EADB;
}
.cr-media img {
    display: block;
    width: 100%;
    height: 260px;
    object-fit: cover;
}
.cr-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.cr-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #362518;
    margin: 0;
    line-height: 1.35;
}
.cr-stars {
    color: #E0A82E;
    font-size: 0.95rem;
    letter-spacing: 2px;
}
.cr-text {
    color: #6C5B4C;
    font-size: 0.95rem;
    line-height: 1.7;
    margin: 0;
}
.cr-meta {
    margin-top: 0.4rem;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}
.cr-name {
    font-weight: 700;
    color: #362518;
    font-size: 0.95rem;
}
.cr-product {
    color: #8B5A2B;
    font-size: 0.88rem;
    text-decoration: underline;
    text-underline-offset: 2px;
}
.cr-product:hover {
    color: #6E4622;
}
.cr-nav {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1px solid #C9B79A;
    background: #FFFFFF;
    color: #5C3D2E;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s ease, color 0.2s ease;
    flex-shrink: 0;
}
.cr-nav:hover {
    background: #5C3D2E;
    color: #FFFFFF;
}
.cr-nav.swiper-button-disabled {
    opacity: 0.4;
    cursor: default;
}
.customer-reviews-pagination.swiper-pagination .swiper-pagination-bullet {
    background: #8B5A2B;
}

/* Desktop: photo on one side, review on the other */
@media (min-width: 768px) {
    .cr-card {
        flex-direction: row;
        align-items: stretch;
    }
    .cr-media {
        width: 42%;
        max-width: 340px;
    }
    .cr-media img {
        height: 100%;
        min-height: 320px;
    }
    .cr-body {
        width: 58%;
        flex: 1;
        justify-content: center;
        padding: 2rem 2.25rem;
    }
    .cr-title {
        font-size: 1.35rem;
    }
}
</style>
@endif
