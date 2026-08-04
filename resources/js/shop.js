document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');
    const mobileFilterForm = document.getElementById('mobileFilterForm');
    const productGridArea = document.getElementById('product-grid-area');

    // Dual-Range Price Slider Initialization
    function initDualRangeSlider(prefix = '') {
        const minRange = document.getElementById(prefix + 'MinPriceRange');
        const maxRange = document.getElementById(prefix + 'MaxPriceRange');
        const minInput = document.getElementById(prefix + 'MinPriceInput');
        const maxInput = document.getElementById(prefix + 'MaxPriceInput');
        const trackFill = document.getElementById(prefix + 'PriceTrackFill');
        const form = document.getElementById(prefix === 'mobile' ? 'mobileFilterForm' : 'filterForm');

        if (!minRange || !maxRange || !trackFill) return;

        const maxValLimit = parseInt(maxRange.max, 10) || 5000;

        function updateFillAndInputs(e) {
            let minVal = parseInt(minRange.value, 10);
            let maxVal = parseInt(maxRange.value, 10);

            if (minVal > maxVal - 50) {
                if (e && e.target === minRange) {
                    minRange.value = maxVal - 50;
                    minVal = maxVal - 50;
                } else {
                    maxRange.value = minVal + 50;
                    maxVal = minVal + 50;
                }
            }

            if (minInput) minInput.value = minVal;
            if (maxInput) maxInput.value = maxVal;

            const leftPercent = (minVal / maxValLimit) * 100;
            const rightPercent = 100 - ((maxVal / maxValLimit) * 100);

            trackFill.style.left = leftPercent + '%';
            trackFill.style.right = rightPercent + '%';
        }

        [minRange, maxRange].forEach(slider => {
            slider.addEventListener('input', updateFillAndInputs);
            slider.addEventListener('change', function() {
                if (form) {
                    fetchFilteredProducts(new URLSearchParams(new FormData(form)));
                }
            });
        });

        [minInput, maxInput].forEach(numInput => {
            if (numInput) {
                numInput.addEventListener('change', function() {
                    let minV = parseInt(minInput.value, 10) || 0;
                    let maxV = parseInt(maxInput.value, 10) || 5000;
                    
                    minRange.value = minV;
                    maxRange.value = maxV;
                    updateFillAndInputs();

                    if (form) {
                        fetchFilteredProducts(new URLSearchParams(new FormData(form)));
                    }
                });
            }
        });

        updateFillAndInputs();
    }

    initDualRangeSlider('');
    initDualRangeSlider('mobile');

    // Auto submit on filter radio/checkbox changes
    const triggers = document.querySelectorAll('.filter-trigger');
    triggers.forEach(tr => {
        tr.addEventListener('change', function () {
            const form = this.closest('form');
            if(form) {
                fetchFilteredProducts(new URLSearchParams(new FormData(form)));
            }
        });
    });

    // Accordion interaction: if main category is clicked, don't submit if it has subcategories.
    // However, if the main category has NO subcategories, we want to allow it to be selected.
    // The radios for main categories should be triggered. 
    // We will handle this in HTML by placing the main category radio outside the accordion toggle, 
    // or just relying on standard label clicking. 
    
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

    // Form Submissions (for search, etc.)
    [filterForm, mobileFilterForm].forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetchFilteredProducts(new URLSearchParams(new FormData(form)));
            });
        }
    });

    // Category Capsule Tabs AJAX
    const capsuleTabs = document.querySelectorAll('.capsule-tab');
    capsuleTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
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
        <div class="row row-cols-2 row-cols-md-3 g-3 g-md-4 mb-5">
            ${Array(6).fill().map(() => `
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
        if(!searchParams.has('sort') && currentUrl.searchParams.has('sort')) {
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
            
            // Sync current values (like showing result counts, etc.) if they were inside the grid area
        })
        .catch(err => {
            console.error('Filter error:', err);
            productGridArea.innerHTML = originalContent; // Revert on error
        });
    }

    // Handle back/forward buttons
    window.addEventListener('popstate', function() {
        const url = new URL(window.location.href);
        fetchFilteredProducts(url.searchParams, url.pathname);
    });
});
