// ── Premium Media Gallery Client Logic ──

document.addEventListener('DOMContentLoaded', function() {
    // 1. Grid vs List View Toggle
    const gridToggle = document.getElementById('btnGridView');
    const listToggle = document.getElementById('btnListView');
    const gridContainer = document.getElementById('mediaGridContainer');
    const listContainer = document.getElementById('mediaListContainer');

    if (gridToggle && listToggle) {
        // Load preference
        const currentView = localStorage.getItem('media_view') || 'grid';
        setView(currentView);

        gridToggle.addEventListener('click', () => setView('grid'));
        listToggle.addEventListener('click', () => setView('list'));
    }

    function setView(view) {
        localStorage.setItem('media_view', view);
        if (view === 'grid') {
            gridToggle.classList.add('active');
            listToggle.classList.remove('active');
            gridContainer.classList.remove('d-none');
            listContainer.classList.add('d-none');
        } else {
            listToggle.classList.add('active');
            gridToggle.classList.remove('active');
            listContainer.classList.remove('d-none');
            gridContainer.classList.add('d-none');
        }
    }

    // 2. Selection & Checkbox Event Listeners
    const bulkBar = document.getElementById('mediaBulkActionsBar');
    const countBadge = document.getElementById('selectedCountBadge');
    const masterCheckbox = document.getElementById('masterSelectCheckbox');
    
    // Select all individual checkboxes (for both grid and list)
    const selectCheckboxes = document.querySelectorAll('.media-select-checkbox');

    selectCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.media-card') || this.closest('tr');
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
            updateBulkBar();
        });
    });

    if (masterCheckbox) {
        masterCheckbox.addEventListener('change', function() {
            selectCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                const card = checkbox.closest('.media-card') || checkbox.closest('tr');
                if (this.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
            updateBulkBar();
        });
    }

    // Update floating actions bar display
    function updateBulkBar() {
        const checkedBoxes = Array.from(selectCheckboxes).filter(cb => cb.checked);
        const count = checkedBoxes.length;

        if (count > 0) {
            countBadge.textContent = count + ' selected';
            bulkBar.classList.add('show');
        } else {
            bulkBar.classList.remove('show');
        }
    }

    // 3. Clear/Cancel Selection
    const btnCancel = document.getElementById('btnCancelSelection');
    if (btnCancel) {
        btnCancel.addEventListener('click', () => {
            selectCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
                const card = checkbox.closest('.media-card') || checkbox.closest('tr');
                card.classList.remove('selected');
            });
            if (masterCheckbox) masterCheckbox.checked = false;
            updateBulkBar();
        });
    }

    // 4. Bulk Delete AJAX Call
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function() {
            const selectedIds = Array.from(selectCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.getAttribute('data-id'));

            if (selectedIds.length === 0) return;

            if (!confirm(`Are you sure you want to delete these ${selectedIds.length} media items?`)) return;

            this.disabled = true;
            this.textContent = 'Deleting...';

            fetch('/admin/media/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error occurred during deletion.');
                    this.disabled = false;
                    this.textContent = 'Delete';
                }
            })
            .catch(() => {
                alert('Connection error occurred.');
                this.disabled = false;
                this.textContent = 'Delete';
            });
        });
    }

    // 5. Bulk Compress AJAX Call
    const btnBulkCompress = document.getElementById('btnBulkCompress');
    if (btnBulkCompress) {
        btnBulkCompress.addEventListener('click', function() {
            const selectedIds = Array.from(selectCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.getAttribute('data-id'));

            if (selectedIds.length === 0) return;

            this.disabled = true;
            this.textContent = 'Compressing...';

            fetch('/admin/media/bulk-compress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: selectedIds })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Error occurred during compression.');
                    this.disabled = false;
                    this.textContent = 'Compress';
                }
            })
            .catch(() => {
                alert('Connection error occurred.');
                this.disabled = false;
                this.textContent = 'Compress';
            });
        });
    }

    // 6. Import from Unsplash External URL Dialog
    const btnImportUrl = document.getElementById('btnImportUrl');
    if (btnImportUrl) {
        btnImportUrl.addEventListener('click', function() {
            const url = prompt('Enter the absolute URL of the image or video to import:');
            if (!url) return;

            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Importing...';

            fetch('/admin/media/url-import', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url: url })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Import failed. Check file types.');
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            })
            .catch(() => {
                alert('Connection error occurred.');
                this.disabled = false;
                this.innerHTML = originalText;
            });
        });
    }
});
