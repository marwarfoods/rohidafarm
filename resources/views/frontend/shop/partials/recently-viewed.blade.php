<!-- ── Recently Viewed Slider ── -->
@if(!empty($recentlyViewed) && count($recentlyViewed) > 0)
    <div class="mb-5 position-relative">
        <h3 class="font-heading fw-bold mb-4 text-center text-md-start">Recently Viewed Products</h3>
        <div class="products-slider-wrapper recently-viewed-slider-wrapper position-relative px-2">
            <div class="swiper related-products-slider" style="overflow:hidden;padding:10px 4px;">
                <div class="swiper-wrapper">
                    @foreach($recentlyViewed as $recent)
                        <div class="swiper-slide h-auto"><x-product-card :product="$recent" /></div>
                    @endforeach
                </div>
            </div>
            <div class="related-viewed-nav">
                <div class="swiper-button-prev" style="--swiper-navigation-size:14px;"></div>
                <div class="swiper-button-next" style="--swiper-navigation-size:14px;"></div>
            </div>
        </div>
    </div>
@endif
