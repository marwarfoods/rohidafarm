<!-- ── Customer Reviews ── -->
@php
    $reviewsCollection = $product->reviews;
    $totalReviews = $reviewsCollection->count();
    $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    foreach ($reviewsCollection as $rev) {
        $r = (int) round($rev->rating);
        if (isset($ratingCounts[$r])) $ratingCounts[$r]++;
    }
    $reviewPhotos = $reviewsCollection->flatMap(fn($rev) => $rev->images ?: [])->take(8);
@endphp
<div id="ratings-reviews" class="bg-white p-4 p-md-5 rounded-4 shadow-sm border mb-5" style="border-color:#f6f3eb !important;">

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

    <h3 class="font-heading fw-bold mb-4 text-dark text-center display-6">Product Reviews</h3>

    <!-- Rating Summary (centered) -->
    <div class="text-center mb-4">
        <div class="d-flex align-items-baseline justify-content-center gap-2">
            <span class="display-5 fw-bold text-dark font-heading">{{ number_format($product->rating, 1) }}</span>
            <span class="text-muted" style="font-size: 0.9rem;">{{ $product->reviews_count ?? $totalReviews }} reviews</span>
        </div>
    </div>

    <!-- Rating Breakdown (thin bars, real percentages) -->
    <div class="d-flex flex-column gap-2 mx-auto mb-4" style="max-width: 420px;">
        @for($i = 5; $i >= 1; $i--)
            @php $pct = $totalReviews > 0 ? round(($ratingCounts[$i] / $totalReviews) * 100) : 0; @endphp
            <div class="d-flex align-items-center gap-3" style="font-size: 0.82rem;">
                <span class="text-dark fw-semibold d-flex align-items-center gap-1" style="width: 34px;">{{ $i }} <i class="bi bi-star-fill text-warning" style="font-size: 0.7rem;"></i></span>
                <div class="flex-grow-1" style="height: 1px; background: #e8e5df;">
                    <div style="height: 1px; width: {{ $pct }}%; background: #248443;"></div>
                </div>
                <span class="text-muted" style="width: 40px; text-align: right;">{{ $ratingCounts[$i] }}</span>
            </div>
        @endfor
    </div>

    <!-- Photo strip + Write Review -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-4 border-bottom" style="border-color:#f6f3eb !important;">
        <div class="d-flex gap-2 flex-wrap">
            @foreach($reviewPhotos as $photo)
                <img src="{{ asset($photo) }}" class="rounded-3 border cursor-pointer review-img-lightbox" style="width:52px;height:52px;object-fit:cover;" data-bs-toggle="modal" data-bs-target="#imageLightboxModal">
            @endforeach
        </div>
        <button class="btn btn-premium rounded-pill px-4" type="button" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
            <i class="bi bi-pencil-square me-2"></i> Write a Review
        </button>
    </div>

    @if($totalReviews > 0)
    <!-- Filter / Sort -->
    <div class="d-flex justify-content-end align-items-center gap-2 mb-4">
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3" title="Filter" disabled>
            <i class="bi bi-funnel"></i>
        </button>
        <select id="reviewSortSelect" class="form-select form-select-sm rounded-3 border" style="width: auto; font-size: 0.85rem;">
            <option value="newest">Newest first</option>
            <option value="highest">Highest rating</option>
            <option value="lowest">Lowest rating</option>
            <option value="pictures">Pictures first</option>
        </select>
    </div>
    @endif

    <!-- Reviews List -->
    <div id="reviewsList" class="d-flex flex-column gap-4">
        @forelse($reviewsCollection as $rev)
            <div class="review-row pb-4 border-bottom review-card-click-trigger cursor-pointer"
                 style="border-color: #f6f3eb !important;"
                 data-rating="{{ $rev->rating }}"
                 data-title="{{ $rev->title }}"
                 data-review="{{ $rev->review }}"
                 data-user="{{ $rev->user->name ?? $rev->customer_name ?? 'Anonymous' }}"
                 data-date="{{ $rev->created_at->format('d M, Y') }}"
                 data-date-ts="{{ $rev->created_at->timestamp }}"
                 data-has-images="{{ !empty($rev->images) ? 1 : 0 }}"
                 data-images="{{ json_encode($rev->images ?: []) }}">

                <div class="text-warning mb-2" style="font-size: 0.85rem;">
                    @for($s = 1; $s <= 5; $s++)
                        <i class="bi {{ $s <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                </div>

                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                        {{ strtoupper(substr($rev->user->name ?? $rev->customer_name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $rev->user->name ?? $rev->customer_name ?? 'Anonymous' }}</span>
                    @if($rev->user_id)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.68rem;"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                    @endif
                </div>
                <div class="text-muted mb-2" style="font-size: 0.75rem;">{{ $rev->created_at->format('d/m/Y') }}</div>

                @if($rev->title)
                    <h6 class="fw-bold font-heading text-dark mb-1" style="font-size: 0.92rem;">{{ $rev->title }}</h6>
                @endif

                <p class="text-muted mb-2 review-text-clamp" style="font-size: 0.88rem; line-height: 1.65; display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">{{ $rev->review }}</p>
                <span class="text-success fw-semibold text-decoration-underline d-inline-block mb-2" style="font-size: 0.8rem;">More</span>

                @if(!empty($rev->images))
                    <div class="d-flex gap-2 flex-wrap mt-1">
                        @foreach($rev->images as $img)
                            <img src="{{ asset($img) }}" class="rounded-3 border cursor-pointer review-img-lightbox" style="width:64px;height:64px;object-fit:cover;" data-bs-toggle="modal" data-bs-target="#imageLightboxModal">
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-4 text-muted">No reviews yet. Be the first!</div>
        @endforelse
    </div>
</div>

@if($totalReviews > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sortSelect = document.getElementById('reviewSortSelect');
    const list = document.getElementById('reviewsList');
    if (!sortSelect || !list) return;

    sortSelect.addEventListener('change', function () {
        const rows = Array.from(list.querySelectorAll('.review-row'));
        const mode = this.value;

        rows.sort((a, b) => {
            if (mode === 'newest') {
                return parseInt(b.dataset.dateTs) - parseInt(a.dataset.dateTs);
            }
            if (mode === 'highest') {
                return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
            }
            if (mode === 'lowest') {
                return parseFloat(a.dataset.rating) - parseFloat(b.dataset.rating);
            }
            if (mode === 'pictures') {
                return parseInt(b.dataset.hasImages) - parseInt(a.dataset.hasImages);
            }
            return 0;
        });

        rows.forEach(row => list.appendChild(row));
    });
});
</script>
@endif
