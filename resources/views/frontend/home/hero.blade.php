<!-- ── Hero Banner Section (Kasutam Rounded Frame & Responsive Banner Slider) ── -->
@push('preload')
    @if(isset($sliders) && $sliders->isNotEmpty())
        @php $firstSlide = $sliders->first(); @endphp
        <link rel="preload" as="image" href="{{ asset($firstSlide->image_path) }}" fetchpriority="high">
        @if($firstSlide->mobile_image_path)
            <link rel="preload" as="image" href="{{ asset($firstSlide->mobile_image_path) }}" media="(max-width: 767px)" fetchpriority="high">
        @endif
    @else
        <link rel="preload" as="image" href="{{ file_exists(public_path('images/home-image-pc.png')) ? asset('images/home-image-pc.png') : asset('images/baner-1.png') }}" fetchpriority="high">
        <link rel="preload" as="image" href="{{ file_exists(public_path('images/home-image-mobile.png')) ? asset('images/home-image-mobile.png') : asset('images/baner-1.png') }}" media="(max-width: 767px)" fetchpriority="high">
    @endif
@endpush

<section class="hero-banner-section py-3 py-md-4 position-relative overflow-hidden">
    <!-- Section Skeleton Overlay -->
    <div class="section-skeleton-overlay position-absolute top-0 start-0 w-100 h-100 bg-white" style="z-index: 10; pointer-events: none;">
        <div class="skeleton-block w-100 h-100" style="border-radius: 2rem;"></div>
    </div>

    <div class="container-fluid px-3 px-sm-4 px-md-5">
        <div class="hero-banner-card">
            
            @if(isset($sliders) && $sliders->isNotEmpty())
                <!-- Swiper Dynamic Slider inside Rounded Frame -->
                <div class="swiper hero-slider" style="height: auto;">
                    <div class="swiper-wrapper">
                        @foreach($sliders as $slide)
                            <div class="swiper-slide h-auto">
                                @if($slide->button_url)
                                    <a href="{{ $slide->button_url }}" class="d-block w-100">
                                        <picture>
                                            @if($slide->mobile_image_path)
                                                <source media="(max-width: 767px)" srcset="{{ asset($slide->mobile_image_path) }}">
                                            @endif
                                            <img src="{{ asset($slide->image_path) }}" 
                                                 class="w-100 d-block hero-banner-img"
                                                 width="1920" height="540"
                                                 alt="{{ $slide->title ?? 'RohidaFarm Premium Banner' }}"
                                                 fetchpriority="high"
                                                 loading="eager"
                                                 decoding="async">
                                        </picture>
                                    </a>
                                @else
                                    <div class="w-100">
                                        <picture>
                                            @if($slide->mobile_image_path)
                                                <source media="(max-width: 767px)" srcset="{{ asset($slide->mobile_image_path) }}">
                                            @endif
                                            <img src="{{ asset($slide->image_path) }}" 
                                                 class="w-100 d-block hero-banner-img"
                                                 alt="{{ $slide->title ?? 'RohidaFarm Premium Banner' }}"
                                                 fetchpriority="high"
                                                 loading="eager"
                                                 decoding="async">
                                        </picture>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination Dots -->
                    <div class="swiper-pagination"></div>
                </div>

            @else
                <!-- Responsive Default Banner inside Kasutam Rounded Border Card -->
                <div class="position-relative w-100">
                    <picture>
                        @if(file_exists(public_path('images/home-image-mobile.png')))
                            <source media="(max-width: 767px)" srcset="{{ asset('images/home-image-mobile.png') }}">
                        @endif
                        <img src="{{ file_exists(public_path('images/home-image-pc.png')) ? asset('images/home-image-pc.png') : asset('images/baner-1.png') }}" 
                             class="w-100 d-block hero-banner-img"
                             alt="RohidaFarm Pure Traditional Organic Ghee Banner"
                             fetchpriority="high"
                             loading="eager"
                             decoding="async">
                    </picture>
                </div>
            @endif

        </div>
    </div>
</section>

