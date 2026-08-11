@extends('layouts.app')

@section('content')
    {{-- -- 1. Hero (LCP critical - no lazy) -- --}}
    @include('frontend.home.hero')

    {{-- -- 2. Featured Products Slider (Bestseller & Categories - FIRST after Hero!) -- --}}
    <div class="lazy-section">
        @include('frontend.home.featured')
    </div>

    {{-- -- 3. Our Roots & Heritage (Rohida Farm Story) -- --}}
    <div class="lazy-section">
        @include('frontend.home.story')
    </div>

    {{-- Decorative divider between Our Roots & Heritage and Vedic Craftsmanship --}}
    <div class="w-100" style="line-height: 0;" aria-hidden="true">
        <svg viewBox="0 0 100 5" preserveAspectRatio="none" width="100%" height="5" style="display: block;">
            <line x1="0" y1="2.5" x2="100" y2="2.5" stroke="#C49A45" stroke-width="4" />
        </svg>
    </div>

    {{-- -- 4. The Traditional Journey Of Our Ghee (Vedic Bilona Process) -- --}}
    <div class="lazy-section">
        @include('frontend.home.bilona-process')
    </div>

    {{-- -- 5. Promo Banners -- --}}
    <div class="lazy-section">
        @include('frontend.home.banners')
    </div>

    {{-- -- 6. All Products Grid -- --}}
    <div class="lazy-section">
        @include('frontend.home.all-products-grid')
    </div>

    {{-- -- 7. Native Ingredients -- --}}
    <div class="lazy-section">
        @include('frontend.home.native-ingredients')
    </div>

    {{-- -- 8. Certifications & Quality Standards -- --}}
    <div class="lazy-section">
        @include('frontend.home.certifications')
    </div>

    {{-- -- 9. Customer Love & Video Reviews -- --}}
    <div class="lazy-section">
        @include('frontend.home.videos')
    </div>

    {{-- -- 10. Latest Blogs -- --}}
    <div class="lazy-section">
        @include('frontend.home.blogs')
    </div>

    {{-- -- 11. Instagram Feed Gallery (Bottom Section Before Footer) -- --}}
    <div class="lazy-section">
        @include('frontend.home.instagram-feed')
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/home.js', 'resources/sass/specialties.scss', 'resources/js/specialties.js'])
@endpush
