@extends('layouts.app')

@section('content')
    {{-- -- 1. Hero (LCP critical - no lazy) -- --}}
    @include('frontend.home.hero')

    {{-- -- 2. Featured Products Slider (Bestseller & Categories - FIRST after Hero!) -- --}}
    <div class="lazy-section">
        @include('frontend.home.featured')
    </div>

    {{-- -- 3. Trust Bar (6 Core Pillars) -- --}}
    <div class="lazy-section">
        @include('frontend.home.trust-bar')
    </div>

    {{-- -- 4. Our Roots & Heritage (Rohida Farm Story) -- --}}
    <div class="lazy-section">
        @include('frontend.home.story')
    </div>

    {{-- -- 5. The Traditional Journey Of Our Ghee (Step-by-Step Bilona Timeline) -- --}}
    <div class="lazy-section">
        @include('frontend.home.bilona-process')
    </div>

    {{-- -- 6. Why Rohida Farm & Why Tharparkar Cows -- --}}
    <div class="lazy-section">
        @include('frontend.home.why-tharparkar')
    </div>

    {{-- -- 7. Promo Banners -- --}}
    <div class="lazy-section">
        @include('frontend.home.banners')
    </div>

    {{-- -- 8. All Products Grid -- --}}
    <div class="lazy-section">
        @include('frontend.home.all-products-grid')
    </div>

    {{-- -- 9. Native Ingredients -- --}}
    <div class="lazy-section">
        @include('frontend.home.native-ingredients')
    </div>

    {{-- -- 10. Certifications & Quality Standards -- --}}
    <div class="lazy-section">
        @include('frontend.home.certifications')
    </div>

    {{-- -- 11. Customer Love & Video Reviews -- --}}
    <div class="lazy-section">
        @include('frontend.home.videos')
    </div>

    {{-- -- 12. Latest Blogs -- --}}
    <div class="lazy-section">
        @include('frontend.home.blogs')
    </div>

    {{-- -- 13. Instagram Feed Gallery (Bottom Section Before Footer) -- --}}
    <div class="lazy-section">
        @include('frontend.home.instagram-feed')
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/home.js', 'resources/sass/specialties.scss', 'resources/js/specialties.js'])
@endpush
