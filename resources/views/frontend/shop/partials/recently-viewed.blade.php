<!-- ── Recently Viewed Slider ── -->
@if(!empty($recentlyViewed) && count($recentlyViewed) > 0)
    <div class="mb-5 position-relative">
        <h3 class="font-heading fw-bold mb-4 text-center text-md-start">Recently Viewed Products</h3>
        <div class="swiper related-products-slider" style="overflow:hidden;padding:10px 4px;">
            <div class="swiper-wrapper">
                @foreach($recentlyViewed as $recent)
                    <div class="swiper-slide h-auto"><x-product-card :product="$recent" /></div>
                @endforeach
            </div>
            <div class="swiper-button-next d-none d-md-flex" style="--swiper-navigation-size:16px;"></div>
            <div class="swiper-button-prev d-none d-md-flex" style="--swiper-navigation-size:16px;"></div>
        </div>
    </div>
@endif
