document.addEventListener('DOMContentLoaded', function () {
    // 1. Handle Tab Hash Routing
    let currentHash = window.location.hash || '#site'; // default to site
    
    // Validate hash, fallback if invalid
    const validHashes = ['#site', '#smtp', '#seo', '#payments', '#shipping', '#integrations', '#auth'];
    if (!validHashes.includes(currentHash)) {
        currentHash = '#site';
    }

    // Function to activate tab and sidebar card
    function activateTab(hash) {
        // Deactivate all nav links and tab panes
        document.querySelectorAll('.settings-tab-link').forEach(el => {
            el.classList.remove('active');
            el.setAttribute('aria-selected', 'false');
        });
        document.querySelectorAll('.settings-tab-pane').forEach(el => {
            el.classList.remove('show', 'active');
        });
        document.querySelectorAll('.dynamic-sidebar-card').forEach(el => {
            el.classList.remove('active');
        });

        // Activate the targeted tab and corresponding sidebar
        const targetLink = document.querySelector(`.settings-tab-link[href="${hash}"]`);
        const targetPane = document.querySelector(hash);
        const targetSidebar = document.getElementById(`sidebar-${hash.replace('#', '')}`);

        if (targetLink && targetPane) {
            targetLink.classList.add('active');
            targetLink.setAttribute('aria-selected', 'true');
            targetPane.classList.add('show', 'active');
            if (targetSidebar) {
                targetSidebar.classList.add('active');
            }
        }
    }

    // Initial activation
    activateTab(currentHash);

    // Listen for tab clicks to update URL hash and sidebar
    document.querySelectorAll('.settings-tab-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const hash = this.getAttribute('href');
            window.history.pushState(null, null, hash);
            activateTab(hash);
        });
    });

    // 2. SEO Live Preview Logic
    const titleInput = document.getElementById('meta_title_input');
    const descInput = document.getElementById('meta_desc_input');
    
    const previewTitle = document.getElementById('seo_preview_title');
    const previewDesc = document.getElementById('seo_preview_desc');

    const defaultTitle = "RohidaFarm - Pure Organic Ghee";
    const defaultDesc = "Premium luxury organic ghee and farm fresh products directly from our farms to your doorstep.";

    function updateSeoPreview() {
        if (!titleInput || !descInput || !previewTitle || !previewDesc) return;

        let titleVal = titleInput.value.trim();
        let descVal = descInput.value.trim();

        if (titleVal === '') {
            previewTitle.innerHTML = `<span class="empty-seo-placeholder">Please provide a title tag</span>`;
        } else {
            previewTitle.textContent = titleVal;
        }

        if (descVal === '') {
            previewDesc.innerHTML = `<span class="empty-seo-placeholder">Please provide a meta description</span>`;
        } else {
            previewDesc.textContent = descVal;
        }
    }

    // Attach listeners for live update
    if (titleInput) titleInput.addEventListener('input', updateSeoPreview);
    if (descInput) descInput.addEventListener('input', updateSeoPreview);

    // Initial call to set preview on load
    updateSeoPreview();

    // 3. Initialize Media Pickers for Site Branding
    if (window.initMediaPicker) {
        initMediaPicker('#mainLogoInput', '#mainLogoPreview', 'image');
        initMediaPicker('#footerLogoInput', '#footerLogoPreview', 'image');
        initMediaPicker('#faviconInput', '#faviconPreview', 'image');
        
        // Hide placeholder text when an image is picked
        const handlePlaceholder = (inputId) => {
            const input = document.getElementById(inputId);
            if(input) {
                input.addEventListener('change', function() {
                    const parent = this.nextElementSibling;
                    const placeholder = parent.querySelector('.placeholder-text');
                    if (placeholder && this.value) {
                        placeholder.style.display = 'none';
                    }
                });
            }
        };
        
        handlePlaceholder('mainLogoInput');
        handlePlaceholder('footerLogoInput');
        handlePlaceholder('faviconInput');
    }
});
