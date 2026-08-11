document.addEventListener('DOMContentLoaded', function () {
    const productGridArea = document.getElementById('product-grid-area');

    // Sort Sync
    function handleSort(selectId) {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', function () {
                const url = new URL(window.location.href);
                url.searchParams.set('sort', this.value);
                fetchFilteredProducts(url.searchParams);
            });
        }
    }
    handleSort('sortSelect');
    handleSort('mobileSortSelect');

    // Desktop Search — AJAX
    const desktopSearchForm = document.getElementById('desktopSearchForm');
    if (desktopSearchForm) {
        desktopSearchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const url = new URL(window.location.href);
            const input = document.getElementById('desktopSearchInput');
            url.searchParams.set('search', input ? input.value : '');
            fetchFilteredProducts(url.searchParams);
        });
    }

    // Mobile Search — AJAX
    function submitMobileSearch() {
        const url = new URL(window.location.href);
        const input = document.getElementById('mobileSearchInput');
        url.searchParams.set('search', input ? input.value : '');
        fetchFilteredProducts(url.searchParams);
    }
    const mobileSearchBtn = document.getElementById('mobileSearchBtn');
    if (mobileSearchBtn) {
        mobileSearchBtn.addEventListener('click', submitMobileSearch);
    }
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitMobileSearch();
            }
        });
    }

    // Category Capsule Tabs AJAX
    const capsuleTabs = document.querySelectorAll('.capsule-tab');
    capsuleTabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            fetchFilteredProducts(url.searchParams, url.pathname);

            // Update active state manually since they are outside grid
            capsuleTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Skeleton Loader HTML
    const skeletonHtml = `
        <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-3 g-md-4 mb-5">
            ${Array(10).fill().map(() => `
                <div class="col">
                    <div class="skeleton-card border">
                        <div class="skeleton-img"></div>
                        <div class="skeleton-body">
                            <div class="skeleton-text"></div>
                            <div class="skeleton-text short"></div>
                            <div class="skeleton-button"></div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    function fetchFilteredProducts(searchParams, customPathname = null) {
        if (!productGridArea) return;

        // Ensure sort is included if not in form
        const currentUrl = new URL(window.location.href);
        if (!searchParams.has('sort') && currentUrl.searchParams.has('sort')) {
            searchParams.set('sort', currentUrl.searchParams.get('sort'));
        }

        const targetPathname = customPathname || window.location.pathname;
        const url = `${targetPathname}?${searchParams.toString()}`;

        // Update URL bar
        window.history.pushState({}, '', url);

        // Show skeleton loader
        const originalContent = productGridArea.innerHTML;
        productGridArea.innerHTML = skeletonHtml;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newGridArea = doc.getElementById('product-grid-area');

            if (newGridArea) {
                productGridArea.innerHTML = newGridArea.innerHTML;
            } else {
                productGridArea.innerHTML = originalContent;
            }
        })
        .catch(err => {
            console.error('Filter error:', err);
            productGridArea.innerHTML = originalContent; // Revert on error
        });
    }

    // Handle back/forward buttons
    window.addEventListener('popstate', function () {
        const url = new URL(window.location.href);
        fetchFilteredProducts(url.searchParams, url.pathname);
    });
});
