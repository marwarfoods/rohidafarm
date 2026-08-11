<!-- Featured Videos Section -->
<section class="py-5 position-relative overflow-hidden"
    style="background: url('{{ asset('images/vectors/bg7.png') }}') center center / cover no-repeat;">
    <div class="position-absolute top-0 start-0 w-100 h-100"
         style="background: rgba(255, 255, 255, 0.55); z-index: 0; pointer-events: none;"></div>
    <div class="container overflow-hidden position-relative" style="z-index: 1;">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-up">
            <div>
                <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 2px;">Customer Love</span>
                <h2 class="display-5 font-heading fw-bold mt-1 mb-0">Featured Videos</h2>
            </div>
            <!-- Swiper Navigation Arrows -->
            <div class="d-none d-md-flex gap-2">
                <button class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center video-reviews-prev" style="width: 44px; height: 44px; transition: all 0.2s;">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center video-reviews-next" style="width: 44px; height: 44px; transition: all 0.2s;">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="swiper video-reviews-slider overflow-visible" data-aos="fade-up">
            <div class="swiper-wrapper">
                @forelse($videoReviews as $vid)
                    @php
                        $avatar = $vid->product && $vid->product->primaryImage ? asset($vid->product->primaryImage->image_path) : asset('images/baner-1.png');
                        $pPrice = $vid->product ? $vid->product->sale_price : 0;
                        $pMrp = $vid->product ? $vid->product->mrp : 0;
                        $discount = $pMrp > $pPrice ? round((($pMrp - $pPrice) / $pMrp) * 100) : 0;
                    @endphp
                    <div class="swiper-slide h-auto" style="width: 280px;">
                        <div class="video-card-wrapper position-relative rounded-4 overflow-hidden border shadow-sm h-100 bg-light d-flex flex-column" 
                             style="border-color: var(--border-color) !important; cursor: pointer;"
                             data-index="{{ $loop->index }}"
                             data-video="{{ asset($vid->video_path) }}"
                             data-reviewer="{{ $vid->reviewer_name }}"
                             data-product="{{ $vid->product ? $vid->product->name : 'Verified Buyer' }}"
                             data-buy-url="{{ $vid->product ? route('shop.show', $vid->product->slug) : '' }}">
                            <div class="position-relative" style="height: 380px;">
                                <video src="{{ asset($vid->video_path) }}" class="w-100 h-100 object-fit-cover rounded-top-4" muted playsinline preload="metadata" style="background-color: #000; pointer-events: none;" aria-label="Customer video review"></video>
                                <div class="play-overlay-btn position-absolute top-50 start-50 translate-middle bg-white bg-opacity-75 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; pointer-events: none; transition: opacity 0.3s ease; z-index: 2;">
                                    <i class="bi bi-play-fill text-dark fs-2" style="margin-left: 3px;"></i>
                                </div>
                            </div>
                            <div class="p-3 bg-white d-flex flex-column justify-content-between rounded-bottom-4 flex-grow-1" style="min-height: 140px;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <img src="{{ $avatar }}" class="rounded-circle border" width="44" height="44" style="object-fit: cover; flex-shrink: 0;" loading="lazy" decoding="async" alt="{{ $vid->reviewer_name }}">
                                    <div style="line-height: 1.25; overflow: hidden;">
                                        <h6 class="fw-bold font-heading text-dark m-0 fs-6 text-truncate" title="{{ $vid->product ? $vid->product->name : $vid->reviewer_name }}" style="font-size: 0.85rem !important;">{{ $vid->product ? $vid->product->name : $vid->reviewer_name }}</h6>
                                        @if($vid->product)
                                            <div class="mt-1">
                                                <span class="fw-bold text-success" style="font-size: 0.85rem;">₹{{ number_format($pPrice, 0) }}</span>
                                                @if($discount > 0)
                                                    <small class="text-decoration-line-through text-muted ms-1" style="font-size: 0.75rem;">₹{{ number_format($pMrp, 0) }}</small>
                                                    <span class="badge bg-danger-subtle text-danger py-0 px-1 rounded-pill" style="font-size: 0.6rem;">-{{ $discount }}%</span>
                                                @endif
                                            </div>
                                        @else
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $vid->reviewer_name }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    @if($vid->product)
                                        <a href="{{ route('shop.show', $vid->product->slug) }}" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase btn-video-buy" style="font-size: 0.75rem; letter-spacing: 0.5px; background-color: var(--primary-green) !important; border-color: var(--primary-green) !important; color: white !important;">Buy Now</a>
                                    @else
                                        <button class="btn btn-secondary w-100 py-2 rounded-3 text-uppercase btn-video-buy" disabled style="font-size: 0.75rem;">Buy Now</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- Placeholder Videos -->
                    <div class="swiper-slide h-auto" style="width: 280px;">
                        <div class="video-card-wrapper position-relative rounded-4 overflow-hidden border shadow-sm h-100 bg-light d-flex flex-column" 
                             style="border-color: var(--border-color) !important; cursor: pointer;"
                             data-index="0"
                             data-video="https://assets.mixkit.co/videos/preview/mixkit-pouring-honey-from-a-wooden-spoon-42358-large.mp4"
                             data-reviewer="Sita Devi"
                             data-product="Pure Gir Ghee"
                             data-buy-url="">
                            <div class="position-relative" style="height: 380px;">
                                <video src="https://assets.mixkit.co/videos/preview/mixkit-pouring-honey-from-a-wooden-spoon-42358-large.mp4" class="w-100 h-100 object-fit-cover rounded-top-4" muted playsinline preload="metadata" style="background-color: #000; pointer-events: none;" aria-label="Customer video review"></video>
                                <div class="play-overlay-btn position-absolute top-50 start-50 translate-middle bg-white bg-opacity-75 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; pointer-events: none; transition: opacity 0.3s ease; z-index: 2;">
                                    <i class="bi bi-play-fill text-dark fs-2" style="margin-left: 3px;"></i>
                                </div>
                            </div>
                            <div class="p-3 bg-white rounded-bottom-4 flex-grow-1 d-flex flex-column justify-content-between" style="min-height: 120px;">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <img src="{{ asset('images/baner-1.png') }}" class="rounded-circle border" width="44" height="44" style="object-fit: cover; flex-shrink: 0;" loading="lazy" decoding="async" alt="Sita Devi">
                                    <div>
                                        <h6 class="fw-bold font-heading text-dark m-0 fs-6">Sita Devi</h6>
                                        <small class="text-muted d-block">Pure Gir Ghee User</small>
                                    </div>
                                </div>
                                <button class="btn btn-secondary w-100 py-2 rounded-3 text-uppercase btn-video-buy" disabled style="font-size: 0.75rem;">Buy Now</button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="swiper-pagination d-block mt-4"></div>
        </div>
    </div>
</section>

<!-- Video Review Lightbox Modal Markup -->
<div id="videoLightbox" class="video-lightbox-overlay d-none">
    <div class="lightbox-backdrop"></div>
    
    <!-- Navigation Arrows (Desktop) -->
    <button type="button" class="lightbox-arrow arrow-left" aria-label="Previous video">
        <i class="bi bi-chevron-left" aria-hidden="true"></i>
    </button>

    <div class="lightbox-container">
        <!-- Close Button -->
        <button type="button" class="lightbox-close" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>
        
        <div class="lightbox-content">
            <div class="lightbox-video-wrapper">
                <video id="lightboxVideo" src="" playsinline controls autoplay style="background: #000;"></video>
            </div>
            <div class="lightbox-info p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="m-0 font-heading fw-bold text-white" id="lightboxReviewer"></h5>
                        <p class="m-0 text-white-50" style="font-size: 0.85rem;" id="lightboxProduct"></p>
                    </div>
                    <a href="" id="lightboxBuyBtn" class="btn btn-premium btn-sm px-4 rounded-pill d-none">Buy Now</a>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Controls (Reels-style indicators) -->
        <div class="lightbox-mobile-nav d-md-none">
            <button type="button" class="mobile-nav-btn btn-prev mb-2" aria-label="Previous video">
                <i class="bi bi-chevron-up fs-4" aria-hidden="true"></i>
            </button>
            <button type="button" class="mobile-nav-btn btn-next" aria-label="Next video">
                <i class="bi bi-chevron-down fs-4" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <button type="button" class="lightbox-arrow arrow-right" aria-label="Next video">
        <i class="bi bi-chevron-right" aria-hidden="true"></i>
    </button>
</div>
