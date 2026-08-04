/* Icon Picker global library */
let activeTargetInput = null;
let activePreviewContainer = null;
let activeDropdownDisplay = null;
let iconPickerModalInstance = null;

// Global initializer function
window.initIconPicker = function(inputSelector, previewSelector = null, dropdownDisplaySelector = null) {
    const inputs = document.querySelectorAll(inputSelector);
    inputs.forEach(input => {
        // Avoid binding twice
        if (input.parentNode.classList.contains('icon-picker-group')) return;

        const parent = input.parentNode;
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group icon-picker-group';
        parent.replaceChild(wrapper, input);
        wrapper.appendChild(input);

        // Add selector button
        const btn = document.createElement('button');
        btn.className = 'btn btn-outline-success font-heading';
        btn.type = 'button';
        btn.innerHTML = '<i class="bi bi-tags-fill"></i> Icon Library';
        btn.addEventListener('click', () => {
            activeTargetInput = input;
            activePreviewContainer = previewSelector ? document.querySelector(previewSelector) : null;
            activeDropdownDisplay = dropdownDisplaySelector ? document.querySelector(dropdownDisplaySelector) : null;

            // Mark current value as active
            const currentVal = input.value || 'bi-tags';
            document.querySelectorAll('.icon-grid-item').forEach(el => {
                if (el.getAttribute('data-icon') === currentVal) {
                    el.classList.add('active-icon');
                } else {
                    el.classList.remove('active-icon');
                }
            });

            // Initialize bootstrap modal instance on demand if not done yet
            if (!iconPickerModalInstance) {
                const modalEl = document.getElementById('iconPickerModal');
                if (modalEl) {
                    iconPickerModalInstance = new bootstrap.Modal(modalEl);
                }
            }
            if (iconPickerModalInstance) {
                iconPickerModalInstance.show();
            } else {
                console.error("Icon picker modal element #iconPickerModal not found in DOM.");
            }
        });
        wrapper.appendChild(btn);
    });
};

document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('iconPickerModal');
    if (!modalEl) return;
    
    iconPickerModalInstance = new bootstrap.Modal(modalEl);

    // Grid selection handling
    const gridItems = document.querySelectorAll('.icon-grid-item');
    gridItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const selectedIcon = this.getAttribute('data-icon');
            const selectedLabel = this.getAttribute('data-label');

            if (activeTargetInput) {
                activeTargetInput.value = selectedIcon;
                
                // Dispatch change event
                activeTargetInput.dispatchEvent(new Event('change'));
            }

            if (activePreviewContainer) {
                const iTag = activePreviewContainer.querySelector('i');
                if (iTag) {
                    iTag.className = `bi ${selectedIcon} text-success me-2 fs-5`;
                } else {
                    activePreviewContainer.innerHTML = `<i class="bi ${selectedIcon} text-success me-2 fs-5"></i> ${selectedLabel}`;
                }
            }

            if (activeDropdownDisplay) {
                activeDropdownDisplay.innerHTML = `<i class="bi ${selectedIcon} text-success me-2 fs-5"></i> ${selectedLabel}`;
            }

            if (iconPickerModalInstance) {
                iconPickerModalInstance.hide();
            }
        });
    });

    // Category Sidebar filter logic
    const catButtons = document.querySelectorAll('#iconCategoryList button');
    catButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            catButtons.forEach(b => {
                b.classList.remove('active-cat-tab', 'text-success', 'bg-success-subtle');
                b.classList.add('text-muted', 'bg-transparent');
            });
            this.classList.add('active-cat-tab', 'text-success', 'bg-success-subtle');
            this.classList.remove('text-muted', 'bg-transparent');

            const selectedCat = this.getAttribute('data-cat');
            filterIcons(selectedCat, document.getElementById('iconSearchInput').value.toLowerCase());
        });
    });

    // Search Input filter logic
    const searchInput = document.getElementById('iconSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const activeTab = document.querySelector('#iconCategoryList .active-cat-tab');
            const activeCat = activeTab ? activeTab.getAttribute('data-cat') : 'all';
            filterIcons(activeCat, this.value.toLowerCase());
        });
    }

    function filterIcons(category, searchTxt) {
        const items = document.querySelectorAll('.icon-item-card');
        items.forEach(item => {
            const itemGroup = item.getAttribute('data-group');
            const itemLabel = item.getAttribute('data-label');
            const itemClass = item.getAttribute('data-class');

            const matchesCategory = (category === 'all' || itemGroup === category);
            const matchesSearch = (!searchTxt || itemLabel.includes(searchTxt) || itemClass.includes(searchTxt));

            if (matchesCategory && matchesSearch) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    }
});
