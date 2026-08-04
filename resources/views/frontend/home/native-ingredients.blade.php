@if(isset($nativeIngredients) && $nativeIngredients->count() > 0)
<!-- Native Ingredients Section -->
<section class="py-5" style="background-color: var(--cream-bg);">
    <div class="container text-center mb-5 pb-2" data-aos="fade-up">
        <h2 class="display-6 font-heading fw-bold" style="color: #9d6423;">Native Ingredients. No Substitutes.</h2>
    </div>

    <div class="container">
        <div class="swiper native-ingredients-slider pb-5 pb-lg-0" data-aos="fade-up" data-aos-delay="100">
            <div class="swiper-wrapper">
                @foreach($nativeIngredients as $ingredient)
                <div class="swiper-slide">
                    @if($ingredient->link)
                        <a href="{{ url($ingredient->link) }}" class="d-block">
                    @endif
                        <img src="{{ Str::startsWith($ingredient->image_path, 'http') ? $ingredient->image_path : asset($ingredient->image_path) }}" class="img-fluid rounded-4 w-100 shadow-sm" alt="{{ $ingredient->title ?? 'Native Ingredient' }}" loading="lazy" decoding="async">
                    @if($ingredient->link)
                        </a>
                    @endif
                </div>
                @endforeach
            </div>
            <!-- Pagination for mobile -->
            <div class="swiper-pagination d-lg-none" style="bottom: 0;"></div>
        </div>
    </div>
</section>
@endif
