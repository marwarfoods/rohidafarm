<section class="py-md-5 py-3 bg-white">
    <div class="container-fluid px-md-5 px-1" style="max-width: 1720px;">
        <div class="text-center mb-md-5 mb-4" data-aos="fade-up">
            <h2 class="display-5 font-heading fw-bold mt-1 mb-1">New Arrivals</h2>
            <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">Explore All Our Products</span>
        </div>

        <div class="row g-3 g-md-4 px-md-5 px-1" id="allProductsGridContainer" data-aos="fade-up">
            @foreach($allProducts as $index => $product)
                <div class="col-lg-3 col-md-4 col-6 all-product-item {{ $index >= 8 ? 'd-none' : '' }}">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>

        @if($allProducts->count() > 8)
            <div class="text-center mt-5" data-aos="fade-up">
                <button type="button" id="btnLoadMoreProducts" class="btn btn-premium px-5 py-3 rounded-pill text-uppercase font-heading" style="font-size: 0.9rem;">
                    Load More Products
                </button>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnLoadMore = document.getElementById('btnLoadMoreProducts');
    const items = document.querySelectorAll('.all-product-item');
    let currentVisible = 8;
    const itemsToLoad = 4;

    if (btnLoadMore) {
        btnLoadMore.addEventListener('click', function () {
            let newlyShown = 0;
            
            for (let i = currentVisible; i < items.length; i++) {
                if (newlyShown < itemsToLoad) {
                    items[i].classList.remove('d-none');
                    items[i].style.animation = 'fadeInUp 0.5s ease forwards';
                    newlyShown++;
                }
            }
            
            currentVisible += newlyShown;

            if (currentVisible >= items.length) {
                btnLoadMore.parentElement.style.display = 'none';
            }
        });
    }
});
</script>
<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
