@extends('layouts.app')

@section('content')
    {{-- Breadcrumb Navigation (hidden on product page — PC & mobile) --}}
    {{-- @include('frontend.shop.partials.breadcrumb') --}}

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
