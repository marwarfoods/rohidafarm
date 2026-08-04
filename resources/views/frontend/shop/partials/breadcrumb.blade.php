<!-- Page Breadcrumb -->
<section class="py-2 bg-light border-bottom">
    <div class="product-detail-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 flex-wrap" style="font-size: 0.82rem; row-gap: 0; column-gap: 0;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-success text-decoration-none">Shop</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.category', ['category' => $product->category->slug]) }}" class="text-success text-decoration-none">{{ $product->category->name }}</a></li>
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page" style="word-break: break-word;">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</section>
