@extends('layouts.app')

@section('content')
    {{-- Schema.org Product Structured Data (Google Rich Snippets, Merchant Center & Ads) --}}
    @php
        $primaryImg = $product->primaryImage ? asset($product->primaryImage->image_path) : ($product->images->isNotEmpty() ? asset($product->images->first()->image_path) : asset('favicon-512x512.png'));
        $inStock = $product->inStock();
        $prodPrice = $product->sale_price ?? $product->price ?? 0;
        $reviewCount = $product->reviews ? $product->reviews->count() : 0;
        $avgRating = $product->reviews && $reviewCount > 0 ? $product->reviews->avg('rating') : 5.0;
    @endphp
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org/",
        "@@type": "Product",
        "name": "{{ addslashes($product->name) }}",
        "image": [
            "{{ $primaryImg }}"
        ],
        "description": "{{ addslashes(strip_tags($product->short_description ?: $product->description ?: $product->name)) }}",
        "sku": "{{ $product->sku ?: 'RF-' . $product->id }}",
        "brand": {
            "@@type": "Brand",
            "name": "Rohida Farm"
        },
        "offers": {
            "@@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "INR",
            "price": "{{ number_format($prodPrice, 2, '.', '') }}",
            "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
            "itemCondition": "https://schema.org/NewCondition",
            "availability": "{{ $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}",
            "seller": {
                "@@type": "Organization",
                "name": "RohidaFarm"
            }
        }
        @if($reviewCount > 0)
        ,
        "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "{{ number_format($avgRating ?: 5.0, 1) }}",
            "reviewCount": "{{ $reviewCount }}"
        }
        @endif
    }
    </script>

    {{-- Main Product Detail Container --}}
    <section class="py-4 py-lg-5" style="background: url('{{ asset('images/vectors/bg11.png') }}') top left repeat, var(--cream-bg);">
        <div class="product-detail-container">
            {{-- Skeleton & Product Main Info (Gallery & Description) --}}
            @include('frontend.shop.partials.product-main')

            {{-- Detail Specification Tabs (Description, Benefits, Ingredients) --}}
            @include('frontend.shop.partials.tabs')

            {{-- Product Story / Infographic Banners (Stacked Banners) --}}
            @include('frontend.shop.partials.infographics')

            {{-- Ratings & Reviews (Flipkart Style review sliders) --}}
            @include('frontend.shop.partials.reviews')

            {{-- Recently Viewed Products Slider --}}
            @include('frontend.shop.partials.recently-viewed')
        </div>
    </section>

    {{-- Sticky Add To Cart CTA for Mobile --}}
    @include('frontend.shop.partials.product-mobile-cta')

    {{-- Lightboxes, Reviews detail, and review write Form Modals --}}
    @include('frontend.shop.partials.modals')
@endsection

@push('styles')
    @vite(['resources/sass/product-detail.scss'])
@endpush

@push('page-scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    @vite(['resources/js/product-detail.js'])
@endpush
