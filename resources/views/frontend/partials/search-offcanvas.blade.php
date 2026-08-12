<div class="offcanvas offcanvas-end" tabindex="-1" id="searchOffcanvas" aria-labelledby="searchOffcanvasLabel" style="width: 420px; border-left: 1px solid var(--border-color); z-index: 1055;">
    <div class="offcanvas-header border-bottom py-3">
        <h5 class="offcanvas-title font-heading fw-bold text-dark d-flex align-items-center" id="searchOffcanvasLabel">
            <i class="bi bi-search text-success me-2 fs-5"></i>Search Products
        </h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4 bg-light">
        <!-- Search Form -->
        <form action="{{ route('shop.index') }}" method="GET" class="mb-4" id="ajaxSearchForm">
            <div class="input-group border rounded-3 overflow-hidden bg-white shadow-sm p-1">
                <input type="text" name="search" id="ajaxSearchInput" class="form-control border-0 shadow-none bg-transparent py-2 px-3" placeholder="Search ghee, oils, honey..." value="{{ request('search') }}" style="font-size: 0.9rem;" autocomplete="off">
                <button type="submit" class="btn btn-premium px-3 border-0"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <!-- Real-time Search Results Container -->
        <div id="ajaxSearchResultsContainer" class="mb-4 d-none">
            <!-- Search Skeleton -->
            <div id="ajaxSearchSkeleton" class="d-none">
                @for($i = 0; $i < 4; $i++)
                    <div class="d-flex gap-3 mb-3 p-3 bg-white rounded-4 border shadow-sm align-items-center position-relative overflow-hidden">
                        <div class="skeleton-block" style="width: 55px; height: 55px; border-radius: 8px; flex-shrink: 0;"></div>
                        <div class="flex-grow-1">
                            <div class="skeleton-block mb-2 w-75" style="height: 14px; border-radius: 4px;"></div>
                            <div class="skeleton-block w-40" style="height: 10px; border-radius: 4px;"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Real Results List -->
            <div id="ajaxSearchResultsList" class="d-flex flex-column gap-3"></div>
        </div>

        <!-- Shop By Category (real, dynamic categories from the database) -->
        @php
            $searchOffcanvasCategories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();
        @endphp
        <div id="trendingSearchesCard" class="trending-searches card border-0 rounded-4 shadow-sm p-4 bg-white">
            <h6 class="fw-bold text-dark text-uppercase font-heading mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Shop By Category</h6>
            <div class="d-flex flex-column gap-2" style="font-size: 0.9rem;">
                @forelse($searchOffcanvasCategories as $cat)
                    <a href="{{ route('shop.category', ['category' => $cat->slug]) }}" class="text-muted hover-gold text-decoration-none py-2 {{ $loop->last ? '' : 'border-bottom' }} d-flex align-items-center justify-content-between">
                        <span><i class="bi {{ $cat->icon ?? 'bi-tags' }} text-success me-2"></i>{{ $cat->name }}</span>
                        <i class="bi bi-chevron-right text-success" style="font-size: 0.75rem;"></i>
                    </a>
                @empty
                    <span class="text-muted">No categories available.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ajaxSearchInput');
    const resultsContainer = document.getElementById('ajaxSearchResultsContainer');
    const skeleton = document.getElementById('ajaxSearchSkeleton');
    const resultsList = document.getElementById('ajaxSearchResultsList');
    const trendingCard = document.getElementById('trendingSearchesCard');
    let debounceTimer;

    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        clearTimeout(debounceTimer);

        if (query.length === 0) {
            resultsContainer.classList.add('d-none');
            trendingCard.classList.remove('d-none');
            resultsList.innerHTML = '';
            return;
        }

        // Show loading state
        trendingCard.classList.add('d-none');
        resultsContainer.classList.remove('d-none');
        skeleton.classList.remove('d-none');
        resultsList.classList.add('d-none');

        debounceTimer = setTimeout(() => {
            fetch(`/shop/ajax-search?search=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    skeleton.classList.add('d-none');
                    resultsList.classList.remove('d-none');
                    resultsList.innerHTML = '';

                    if (data.length === 0) {
                        resultsList.innerHTML = `
                            <div class="text-center py-4 text-muted bg-white rounded-4 border shadow-sm">
                                <i class="bi bi-search-heart display-6 d-block mb-2 text-success"></i>
                                No matching products found.
                            </div>
                        `;
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement('a');
                        div.href = `/product/${item.slug}`;
                        div.className = 'd-flex gap-3 p-2.5 bg-white rounded-3 border shadow-2xs align-items-center text-decoration-none hover-gold transition-smooth';
                        div.style.transition = 'all 0.2s ease';
                        div.style.borderColor = '#E8E5DF';
                        div.innerHTML = `
                            <img src="${item.image}" onerror="this.onerror=null;this.src='/assets/images/products/placeholder.jpg';" class="rounded-2 object-fit-cover bg-light border" style="width: 55px; height: 55px; flex-shrink: 0;" alt="${item.name}">
                            <div class="flex-grow-1 overflow-hidden">
                                <span class="badge rounded-1 px-1.5 py-0.5 mb-1 d-inline-block" style="background-color: #E0F2F1; color: #004D40; font-size: 0.65rem; font-weight: 600;">${item.category_name}</span>
                                <h6 class="fw-semibold text-dark m-0 text-truncate" style="font-family: var(--font-body); font-size: 0.85rem;">${item.name}</h6>
                                <div class="mt-1" style="font-size: 0.85rem;">
                                    <span class="fw-bold" style="color: #174C38;">₹${item.sale_price}</span>
                                    ${parseFloat(item.mrp) > parseFloat(item.sale_price) ? `<span class="text-muted text-decoration-line-through ms-1 text-xs">₹${item.mrp}</span>` : ''}
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-success pe-2"></i>
                        `;
                        resultsList.appendChild(div);
                    });
                })
                .catch(err => {
                    skeleton.classList.add('d-none');
                    resultsList.classList.remove('d-none');
                    resultsList.innerHTML = `
                        <div class="text-center py-3 text-danger bg-white rounded-4 border shadow-sm">
                            Error fetching results.
                        </div>
                    `;
                });
        }, 250);
    });
});
</script>
