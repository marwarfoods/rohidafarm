<!-- ── Customer Reviews (Flipkart Style) ── -->
<div id="ratings-reviews" class="bg-white p-4 rounded-4 shadow-sm border mb-5" style="border-color:#f6f3eb !important;">
    <h3 class="font-heading fw-bold mb-4 text-dark fs-4">Ratings &amp; Reviews</h3>

    {{-- Customer review submission flash --}}
    @if(session('success'))
        <div class="alert rounded-3 d-flex align-items-center gap-3 mb-4" style="background:#f0fdf4;border:1px solid #bbf7d0;">
            <i class="bi bi-hourglass-split text-success fs-4"></i>
            <div>
                <div class="fw-bold text-success">Review Submitted Successfully!</div>
                <div class="text-muted" style="font-size:0.85rem;">Your review is pending admin approval and will appear here once approved. Thank you!</div>
            </div>
        </div>
    @endif
    
    <!-- Rating Summary Row -->
    <div class="row g-4 mb-4 pb-4 border-bottom align-items-center" style="border-color: #f6f3eb !important;">
        <div class="col-md-4 text-center border-md-end" style="border-color: #f6f3eb !important;">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="display-5 fw-bold text-dark font-heading">{{ number_format($product->rating, 1) }}</span>
                <span class="badge bg-success font-heading py-2 px-2.5 rounded-3 fs-5 text-white" style="color: #fff !important;"><i class="bi bi-star-fill me-1"></i></span>
            </div>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">{{ $product->reviews_count ?: 5 }} Ratings &amp; {{ $product->reviews_count ?: 5 }} Reviews</p>
        </div>
        <div class="col-md-8">
            <!-- Rating Bars -->
            <div class="d-flex flex-column gap-2" style="max-width: 450px; margin: 0 auto;">
                <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                    <span class="text-dark fw-bold" style="width: 25px;">5 <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                        <div class="progress-bar bg-success" style="width: 80%;"></div>
                    </div>
                    <span class="text-muted" style="width: 35px; text-align: right;">80%</span>
                </div>
                <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                    <span class="text-dark fw-bold" style="width: 25px;">4 <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                        <div class="progress-bar bg-success" style="width: 20%; background-color: #a3d9a5 !important;"></div>
                    </div>
                    <span class="text-muted" style="width: 35px; text-align: right;">20%</span>
                </div>
                <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                    <span class="text-dark fw-bold" style="width: 25px;">3 <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                        <div class="progress-bar bg-warning" style="width: 0%;"></div>
                    </div>
                    <span class="text-muted" style="width: 35px; text-align: right;">0%</span>
                </div>
                <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                    <span class="text-dark fw-bold" style="width: 25px;">2 <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                        <div class="progress-bar bg-warning" style="width: 0%;"></div>
                    </div>
                    <span class="text-muted" style="width: 35px; text-align: right;">0%</span>
                </div>
                <div class="d-flex align-items-center gap-3" style="font-size: 0.8rem;">
                    <span class="text-dark fw-bold" style="width: 25px;">1 <i class="bi bi-star-fill text-warning"></i></span>
                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                        <div class="progress-bar bg-danger" style="width: 0%;"></div>
                    </div>
                    <span class="text-muted" style="width: 35px; text-align: right;">0%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Swiper Slider -->
    <div class="position-relative px-md-4">
        <div class="swiper reviews-slider overflow-hidden" style="padding: 10px 4px 30px;">
            <div class="swiper-wrapper">
                @forelse($product->reviews as $rev)
                    <div class="swiper-slide h-auto">
                        <div class="card border rounded-4 p-3 bg-white h-100 shadow-sm d-flex flex-column justify-content-between review-card-click-trigger cursor-pointer" 
                             style="border-color: #f6f3eb !important;"
                             data-rating="{{ $rev->rating }}"
                             data-title="{{ $rev->title }}"
                             data-review="{{ $rev->review }}"
                             data-user="{{ $rev->user->name }}"
                             data-date="{{ $rev->created_at->format('d M, Y') }}"
                             data-images="{{ json_encode($rev->images ?: []) }}">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-success py-1 px-2 rounded d-inline-flex align-items-center gap-1 text-white fw-bold" style="font-size: 0.75rem; color: #fff !important;">
                                        {{ $rev->rating }} <i class="bi bi-star-fill text-white" style="font-size: 0.65rem;"></i>
                                    </span>
                                    <h6 class="fw-bold font-heading text-dark m-0 text-truncate" style="font-size: 0.85rem;">{{ $rev->title }}</h6>
                                </div>
                                <p class="text-muted mb-2 text-line-clamp-3" style="font-size: 0.8rem; line-height: 1.6; display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">{{ $rev->review }}</p>
                                <span class="text-success fw-semibold d-block mb-3" style="font-size: 0.75rem;">Read More</span>
                                
                                <!-- Photos attachment if any (small size) -->
                                <div class="d-flex gap-1 mb-3 flex-wrap">
                                    @if(!empty($rev->images))
                                        @foreach($rev->images as $img)
                                            <img src="{{ asset($img) }}" class="rounded border cursor-pointer review-img-lightbox" style="width:36px;height:36px;object-fit:cover;" data-bs-toggle="modal" data-bs-target="#imageLightboxModal">
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between border-top pt-2" style="border-color: #f8f6f0 !important; font-size: 0.72rem;">
                                <div class="d-flex align-items-center gap-1 text-muted">
                                    <span class="fw-bold text-dark text-truncate" style="max-width:80px;">{{ $rev->user->name }}</span>
                                    <span class="text-success text-nowrap"><i class="bi bi-patch-check-fill"></i> Verified</span>
                                </div>
                                <span class="text-muted text-nowrap">{{ $rev->created_at->format('d M') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="swiper-slide w-100 text-center py-4 text-muted">No reviews yet. Be the first!</div>
                @endforelse
            </div>
            <!-- Swiper Dots Pagination -->
            <div class="reviews-slider-pagination text-center mt-3 swiper-pagination-clickable"></div>
        </div>
        <!-- Navigation Arrows -->
        <div class="reviews-swiper-button-prev swiper-button-prev d-flex" style="--swiper-navigation-size:16px;"></div>
        <div class="reviews-swiper-button-next swiper-button-next d-flex" style="--swiper-navigation-size:16px;"></div>
    </div>

    <!-- Write a Review Button Section -->
    <div class="mt-4 pt-4 border-top" style="border-color: #f6f3eb !important;">
        <button class="btn btn-premium rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
            <i class="bi bi-pencil-square me-2"></i> Write a Product Review
        </button>
    </div>
</div>
