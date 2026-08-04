@extends('layouts.admin')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
    <div>
        <h1 class="fs-4 font-heading fw-bold m-0"><i class="bi bi-star-fill text-warning me-2"></i>Customer Reviews</h1>
        <p class="text-muted m-0 mt-1" style="font-size:0.85rem;">Moderate reviews — approve, edit, or delete per product.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">{{ $reviews->total() }} Total</span>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.reviews.index') }}" class="card border-0 rounded-3 shadow-sm bg-white p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold text-dark mb-1" style="font-size:0.8rem;">Product</label>
            <select name="product_id" class="form-select form-select-sm border">
                <option value="">All Products</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold text-dark mb-1" style="font-size:0.8rem;">Status</label>
            <select name="status" class="form-select form-select-sm border">
                <option value="">All Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="pending"  {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold text-dark mb-1" style="font-size:0.8rem;">Rating</label>
            <select name="rating" class="form-select form-select-sm border">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} ★</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 flex-fill">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Reset</a>
        </div>
    </div>
</form>

{{-- Bulk Delete Action Bar (appears when checkboxes selected) --}}
<div id="bulkActionBar" class="d-none mb-3">
    <form id="bulkDeleteForm" action="{{ route('admin.reviews.bulk-delete') }}" method="POST"
          onsubmit="return confirmBulkDelete()">
        @csrf
        <div id="bulkIdsContainer"></div>
        <div class="d-flex align-items-center gap-3 p-3 rounded-3 border border-danger-subtle bg-danger-subtle">
            <span id="selectedCount" class="fw-semibold text-danger" style="font-size:0.9rem;"></span>
            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4">
                <i class="bi bi-trash me-1"></i> Delete Selected Reviews
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="clearAllChecks()">
                Cancel
            </button>
        </div>
    </form>
</div>

{{-- Reviews Table --}}
<div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle m-0" style="font-size:0.85rem;">
            <thead class="bg-light text-muted" style="font-size:0.8rem;">
                <tr>
                    <th class="px-3 py-3 border-0" style="width:40px;">
                        <input type="checkbox" id="checkAll" class="form-check-input" title="Select all on this page">
                    </th>
                    <th class="px-4 py-3 border-0">#</th>
                    <th class="px-4 py-3 border-0">Reviewer</th>
                    <th class="px-4 py-3 border-0">Product</th>
                    <th class="px-4 py-3 border-0">Rating</th>
                    <th class="px-4 py-3 border-0">Review</th>
                    <th class="px-4 py-3 border-0">Status</th>
                    <th class="px-4 py-3 border-0 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $rev)
                    @php $ratingVal = min(5, max(1, (int) $rev->rating)); @endphp
                    <tr>
                        {{-- Checkbox --}}
                        <td class="px-3 py-3">
                            <input type="checkbox" class="form-check-input row-check" value="{{ $rev->id }}" onchange="updateBulkBar()">
                        </td>

                        <td class="px-4 py-3 text-muted fw-semibold">#{{ $rev->id }}</td>

                        {{-- Reviewer --}}
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:34px;height:34px;font-size:0.8rem;">
                                    {{ strtoupper(substr($rev->user->name ?? 'G', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.85rem;line-height:1.2;">{{ $rev->user->name ?? 'Guest' }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">{{ $rev->user->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Product --}}
                        <td class="px-4 py-3">
                            @if($rev->product)
                                <div class="fw-semibold text-dark" style="max-width:150px;font-size:0.85rem;line-height:1.2;">
                                    {{ Str::limit($rev->product->name, 26) }}
                                </div>
                                <a href="{{ route('admin.products.edit', $rev->product->id) }}" class="text-success" style="font-size:0.72rem;">
                                    <i class="bi bi-pencil"></i> Edit Product
                                </a>
                            @else
                                <span class="text-muted" style="font-size:0.85rem;">Deleted Product</span>
                            @endif
                        </td>

                        {{-- ── Fixed Rating Stars (max 5, proper display) ── --}}
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $ratingVal)
                                        <i class="bi bi-star-fill" style="color:#f59e0b;font-size:0.7rem;"></i>
                                    @else
                                        <i class="bi bi-star" style="color:#d1d5db;font-size:0.7rem;"></i>
                                    @endif
                                @endfor
                            </div>
                            <div class="fw-bold text-dark mt-1" style="font-size:0.8rem;">{{ $ratingVal }} / 5</div>
                        </td>

                        {{-- Review content --}}
                        <td class="px-4 py-3" style="max-width:260px;">
                            <div class="fw-semibold text-dark mb-1" style="font-size:0.85rem;">{{ Str::limit($rev->title, 40) }}</div>
                            <div class="text-muted" style="font-size:0.8rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $rev->review }}
                            </div>
                            @if(!empty($rev->images) && count($rev->images) > 0)
                                <div class="d-flex gap-1 mt-1 flex-wrap">
                                    @foreach($rev->images as $img)
                                        <img src="{{ asset($img) }}" class="rounded border" style="width:24px;height:24px;object-fit:cover;" title="{{ $img }}">
                                    @endforeach
                                    <span class="text-muted" style="font-size:0.7rem;line-height:24px;">{{ count($rev->images) }} photo(s)</span>
                                </div>
                            @endif
                            <div class="text-muted mt-1" style="font-size:0.7rem;">{{ $rev->created_at->format('d M Y, h:i A') }}</div>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @if($rev->is_approved)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill d-flex align-items-center gap-1" style="width:fit-content;">
                                    <i class="bi bi-check-circle-fill" style="font-size:0.65rem;"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 rounded-pill d-flex align-items-center gap-1" style="width:fit-content;">
                                    <i class="bi bi-hourglass-split" style="font-size:0.65rem;"></i> Pending
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex gap-1 align-items-center justify-content-end flex-wrap">
                                {{-- Approve (pending only) --}}
                                @if(!$rev->is_approved)
                                    <form action="{{ route('admin.reviews.approve', $rev->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-2" title="Approve" style="font-size:0.75rem;">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                @else
                                    <span class="text-success" style="font-size:0.72rem;"><i class="bi bi-check-circle-fill"></i> Live</span>
                                @endif

                                {{-- Edit --}}
                                <button type="button" class="btn btn-sm btn-outline-primary border rounded-pill px-2"
                                        title="Edit" style="font-size:0.75rem;"
                                        data-bs-toggle="modal" data-bs-target="#editReviewModal"
                                        data-review-id="{{ $rev->id }}"
                                        data-review-title="{{ $rev->title }}"
                                        data-review-body="{{ $rev->review }}"
                                        data-review-rating="{{ $ratingVal }}"
                                        data-review-approved="{{ $rev->is_approved ? '1' : '0' }}"
                                        data-review-images='@json($rev->images ?? [])'
                                        data-customer-name="{{ $rev->customer_name }}"
                                        data-customer-email="{{ $rev->customer_email }}"
                                        data-customer-phone="{{ $rev->customer_phone }}"
                                        data-update-url="{{ route('admin.reviews.update', $rev->id) }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                {{-- Delete --}}
                                <form action="{{ route('admin.reviews.delete', $rev->id) }}" method="POST"
                                      onsubmit="return confirm('Delete this review? Uploaded photos will also be removed.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border rounded-pill px-2" title="Delete" style="font-size:0.75rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-star d-block mb-2" style="font-size:2rem;opacity:0.3;"></i>
                            No reviews found matching the current filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Custom Pagination (no giant SVG arrows) --}}
    @if($reviews->hasPages())
        <div class="card-footer bg-white border-top px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="text-muted" style="font-size:0.82rem;">
                Showing {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
            </span>
            <nav>
                <ul class="pagination pagination-sm mb-0 gap-1">
                    {{-- Previous --}}
                    @if($reviews->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link rounded-pill border px-3" style="font-size:0.8rem;">‹ Prev</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link rounded-pill border px-3" href="{{ $reviews->previousPageUrl() }}" style="font-size:0.8rem;">‹ Prev</a>
                        </li>
                    @endif

                    {{-- Page numbers --}}
                    @for($p = 1; $p <= $reviews->lastPage(); $p++)
                        <li class="page-item {{ $reviews->currentPage() === $p ? 'active' : '' }}">
                            <a class="page-link rounded-pill border px-3" href="{{ $reviews->url($p) }}" style="font-size:0.8rem;">{{ $p }}</a>
                        </li>
                    @endfor

                    {{-- Next --}}
                    @if($reviews->hasMorePages())
                        <li class="page-item">
                            <a class="page-link rounded-pill border px-3" href="{{ $reviews->nextPageUrl() }}" style="font-size:0.8rem;">Next ›</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link rounded-pill border px-3" style="font-size:0.8rem;">Next ›</span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif
</div>

{{-- ══════════ EDIT REVIEW MODAL ══════════ --}}
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Edit Review
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editReviewForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Customer Name</label>
                            <input type="text" name="customer_name" id="editCustomerName" class="form-control border p-2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Customer Email</label>
                            <input type="email" name="customer_email" id="editCustomerEmail" class="form-control border p-2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Customer Phone</label>
                            <input type="text" name="customer_phone" id="editCustomerPhone" class="form-control border p-2">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Review Headline *</label>
                        <input type="text" name="title" id="editReviewTitle" class="form-control border p-2" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Review Body *</label>
                        <textarea name="review" id="editReviewBody" class="form-control border p-2" rows="5" required></textarea>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold text-dark">Rating (1–5) *</label>
                            <div class="d-flex gap-2 mt-1" id="editStarPicker">
                                @for($i=1;$i<=5;$i++)
                                    <i class="bi bi-star star-pick" data-val="{{ $i }}"
                                       style="font-size:1.6rem;cursor:pointer;color:#d1d5db;transition:color 0.15s;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="editReviewRating" value="5">
                        </div>
                        <div class="col-md-5 d-flex align-items-end pb-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_approved" id="editReviewApproved" value="1">
                                <label class="form-check-label fw-semibold" for="editReviewApproved">Mark as Approved (visible to customers)</label>
                            </div>
                        </div>
                    </div>

                    {{-- ── Review Photos ── --}}
                    <div id="editReviewImagesSection" class="mt-3" style="display:none;">
                        <label class="form-label fw-semibold text-dark mb-2">Review Photos</label>
                        <div id="editReviewImageGrid" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Image Lightbox Modal ── --}}
<div class="modal fade" id="reviewImgLightbox" tabindex="-1" aria-hidden="true" style="z-index:1080;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body text-center p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute"
                        style="top:10px;right:14px;z-index:10;background-color:rgba(0,0,0,0.5);border-radius:50%;padding:10px;"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="lightboxImg" src="" alt="Review photo"
                     class="img-fluid rounded-4 shadow-lg"
                     style="max-height:80vh;object-fit:contain;">
            </div>
        </div>
    </div>
</div>

@push('admin_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Edit Modal populate ──
    document.getElementById('editReviewModal').addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('editReviewTitle').value      = btn.dataset.reviewTitle;
        document.getElementById('editReviewBody').value       = btn.dataset.reviewBody;
        document.getElementById('editReviewApproved').checked = btn.dataset.reviewApproved === '1';
        document.getElementById('editCustomerName').value     = btn.dataset.customerName || '';
        document.getElementById('editCustomerEmail').value    = btn.dataset.customerEmail || '';
        document.getElementById('editCustomerPhone').value    = btn.dataset.customerPhone || '';
        document.getElementById('editReviewForm').action      = btn.dataset.updateUrl;

        const ratingVal = parseInt(btn.dataset.reviewRating) || 5;
        setEditStars(ratingVal);

        // ── Populate Review Images ──
        let images = [];
        try { images = JSON.parse(btn.dataset.reviewImages || '[]'); } catch(err) {}

        const section = document.getElementById('editReviewImagesSection');
        const grid    = document.getElementById('editReviewImageGrid');
        grid.innerHTML = '';

        if (images.length > 0) {
            section.style.display = 'block';
            images.forEach(src => {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.cssText = 'cursor:pointer;';

                const img = document.createElement('img');
                img.src = src.startsWith('http') ? src : (window.location.origin + '/' + src.replace(/^\//, ''));
                img.className = 'rounded-3 border';
                img.style.cssText = 'width:80px;height:80px;object-fit:cover;transition:transform 0.15s,box-shadow 0.15s;';
                img.title = 'Click to enlarge';

                // Hover effect
                img.addEventListener('mouseenter', () => { img.style.transform='scale(1.06)'; img.style.boxShadow='0 4px 16px rgba(0,0,0,0.18)'; });
                img.addEventListener('mouseleave', () => { img.style.transform='scale(1)'; img.style.boxShadow=''; });

                // Lightbox click
                img.addEventListener('click', () => openLightbox(img.src));

                wrapper.appendChild(img);
                grid.appendChild(wrapper);
            });
        } else {
            section.style.display = 'none';
        }
    });

    // ── Lightbox opener ──
    function openLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        // Open lightbox directly on top of edit modal without hiding it
        const lbEl = document.getElementById('reviewImgLightbox');
        let lb = bootstrap.Modal.getInstance(lbEl);
        if (!lb) {
            lb = new bootstrap.Modal(lbEl, { backdrop: true });
        }
        lb.show();
    }

    // ── Interactive Star Picker in Modal ──
    const starPicker = document.getElementById('editStarPicker');
    const ratingInput = document.getElementById('editReviewRating');

    function setEditStars(val) {
        ratingInput.value = val;
        starPicker.querySelectorAll('.star-pick').forEach(s => {
            const sv = parseInt(s.dataset.val);
            s.className = sv <= val ? 'bi bi-star-fill star-pick' : 'bi bi-star star-pick';
            s.style.color = sv <= val ? '#f59e0b' : '#d1d5db';
        });
    }

    starPicker.querySelectorAll('.star-pick').forEach(star => {
        star.addEventListener('click', () => setEditStars(parseInt(star.dataset.val)));
        star.addEventListener('mouseenter', () => {
            const v = parseInt(star.dataset.val);
            starPicker.querySelectorAll('.star-pick').forEach(s => {
                s.style.color = parseInt(s.dataset.val) <= v ? '#f59e0b' : '#d1d5db';
            });
        });
        star.addEventListener('mouseleave', () => setEditStars(parseInt(ratingInput.value)));
    });

    // ── Bulk Delete Checkbox Logic ──
    const checkAll = document.getElementById('checkAll');
    checkAll.addEventListener('change', function() {
        document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
        updateBulkBar();
    });

    // Handle multiple modals scroll/backdrop restore
    document.addEventListener('hidden.bs.modal', function () {
        if (document.querySelector('.modal.show')) {
            document.body.classList.add('modal-open');
        } else {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        }
    });
});

function updateBulkBar() {
    const checked = Array.from(document.querySelectorAll('.row-check:checked'));
    const bar = document.getElementById('bulkActionBar');
    const container = document.getElementById('bulkIdsContainer');
    const countEl = document.getElementById('selectedCount');

    container.innerHTML = '';
    checked.forEach(c => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'ids[]';
        inp.value = c.value;
        container.appendChild(inp);
    });

    if (checked.length > 0) {
        bar.classList.remove('d-none');
        countEl.textContent = checked.length + ' review(s) selected';
    } else {
        bar.classList.add('d-none');
        document.getElementById('checkAll').checked = false;
    }
}

function clearAllChecks() {
    document.querySelectorAll('.row-check, #checkAll').forEach(c => c.checked = false);
    document.getElementById('bulkActionBar').classList.add('d-none');
}

function confirmBulkDelete() {
    const count = document.querySelectorAll('.row-check:checked').length;
    return confirm(`Delete ${count} review(s)? All uploaded photos will also be permanently removed.`);
}
</script>

<style>
.page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}
.page-link { color: #198754; }
.page-link:hover { background-color: #f0fdf4; color: #198754; }
</style>
@endpush

@endsection
