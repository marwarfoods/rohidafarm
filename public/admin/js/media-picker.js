(function() {
    let activeTargetInput = null;
    let activePreviewContainer = null;
    let activeCustomCallback = null;
    let isMultiSelect = false;
    let selectedMediaItems = []; // [{ path, fullUrl, filename }]
    let nextMediaPageUrl = null;
    let currentMediaTypeFilter = 'image'; // default filter
    let currentFolder = 'all';
    let currentSearch = '';
    let searchDebounceTimer = null;

    // Define function globally immediately so page inline scripts don't hit race conditions
    window.initMediaPicker = function(inputSelector, previewSelector = null, type = 'image', multiple = null) {
        let inputs = [];
        if (typeof inputSelector === 'string') {
            inputs = Array.from(document.querySelectorAll(inputSelector));
        } else if (inputSelector instanceof Element) {
            inputs = [inputSelector];
        } else if (inputSelector instanceof NodeList || Array.isArray(inputSelector)) {
            inputs = Array.from(inputSelector);
        }

        inputs.forEach(input => {
            if (!input || !input.parentNode) return;
            // Ensure double binding wrapper doesn't exist
            if (input.parentNode.classList.contains('media-picker-group')) return;

            const parent = input.parentNode;
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group media-picker-group';
            parent.replaceChild(wrapper, input);
            wrapper.appendChild(input);

            // Add selection button
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-success font-heading';
            btn.type = 'button';
            btn.innerHTML = '<i class="bi bi-folder2-open"></i> Choose';
            btn.addEventListener('click', () => {
                const isMulti = (multiple !== null) ? !!multiple : (input.hasAttribute('multiple') || input.dataset.multiple === 'true');
                openPickerModal({
                    targetInput: input,
                    previewSelector: previewSelector,
                    type: type,
                    isMultiSelect: isMulti
                });
            });
            wrapper.appendChild(btn);
        });
    };

    // Programmatic picker opener
    window.openMediaPicker = function(options = {}) {
        openPickerModal(options);
    };

    function openPickerModal(options = {}) {
        activeTargetInput = options.targetInput || null;
        activePreviewContainer = options.previewSelector 
            ? (typeof options.previewSelector === 'string' ? document.querySelector(options.previewSelector) : options.previewSelector) 
            : null;
        activeCustomCallback = (typeof options.onSelect === 'function') ? options.onSelect : null;
        
        isMultiSelect = !!options.isMultiSelect;
        selectedMediaItems = [];
        updateFooterSelectionUI();

        currentMediaTypeFilter = options.type || 'image';
        currentFolder = 'all';
        currentSearch = '';

        const searchInput = document.getElementById('mediaPickerSearchInput');
        if (searchInput) searchInput.value = '';

        const videoFolderLi = document.querySelector('.media-picker-folder-video-only');
        if (videoFolderLi) videoFolderLi.style.display = (currentMediaTypeFilter === 'video') ? '' : 'none';

        setActiveFolderUI('all');
        resetAndLoadGallery();

        // Switch to gallery tab by default when opening
        const galleryTabBtn = document.getElementById('gallery-tab');
        if (galleryTabBtn && typeof bootstrap !== 'undefined') {
            const tab = bootstrap.Tab.getOrCreateInstance(galleryTabBtn);
            tab.show();
        }

        const modalEl = document.getElementById('mediaPickerModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    function updateFooterSelectionUI() {
        const count = selectedMediaItems.length;
        const countBadge = document.getElementById('mediaPickerSelectedCount');
        const clearBtn = document.getElementById('mediaPickerClearSelectionBtn');
        const confirmBtn = document.getElementById('mediaPickerConfirmBtn');
        const confirmBtnText = document.getElementById('mediaPickerConfirmBtnText');

        if (countBadge) countBadge.textContent = count;
        if (clearBtn) {
            clearBtn.classList.toggle('d-none', count === 0);
        }

        if (confirmBtn) {
            confirmBtn.disabled = (count === 0);
        }

        if (confirmBtnText) {
            if (isMultiSelect) {
                confirmBtnText.textContent = count > 0 ? `Insert Selected (${count})` : 'Insert Selected';
            } else {
                confirmBtnText.textContent = count > 0 ? 'Use Selected Image' : 'Select Item';
            }
        }
    }

    function toggleItemSelection(item, cardEl) {
        const normPath = item.path.startsWith('http') || item.path.startsWith('/')
            ? item.path
            : '/' + item.path;

        const existingIdx = selectedMediaItems.findIndex(i => i.path === normPath);

        if (isMultiSelect) {
            if (existingIdx > -1) {
                // Deselect
                selectedMediaItems.splice(existingIdx, 1);
                cardEl.classList.remove('selected');
            } else {
                // Select
                selectedMediaItems.push({
                    path: normPath,
                    fullUrl: item.fullUrl,
                    filename: item.filename
                });
                cardEl.classList.add('selected');
            }
        } else {
            // Single Select
            selectedMediaItems = [{
                path: normPath,
                fullUrl: item.fullUrl,
                filename: item.filename
            }];
            document.querySelectorAll('#galleryGridContainer .gallery-picker-item').forEach(el => el.classList.remove('selected'));
            cardEl.classList.add('selected');
        }

        updateFooterSelectionUI();
    }

    function confirmAndApplySelection() {
        if (!selectedMediaItems.length) return;

        // 1. If custom onSelect callback provided
        if (activeCustomCallback) {
            activeCustomCallback(selectedMediaItems);
        }

        // 2. If target input exists
        if (activeTargetInput) {
            if (isMultiSelect) {
                activeTargetInput.value = selectedMediaItems.map(i => i.path).join(',');
            } else {
                activeTargetInput.value = selectedMediaItems[0].path;
            }

            // Dispatch custom event with full array payload
            activeTargetInput.dispatchEvent(new CustomEvent('media-picker:selected', {
                bubbles: true,
                detail: {
                    items: selectedMediaItems,
                    paths: selectedMediaItems.map(i => i.path),
                    isMulti: isMultiSelect
                }
            }));

            // Dispatch standard change event for backward compatibility
            activeTargetInput.dispatchEvent(new Event('change', { bubbles: true }));

            // Update single preview container if present
            if (activePreviewContainer && selectedMediaItems.length > 0) {
                const previewSrc = selectedMediaItems[0].fullUrl || (selectedMediaItems[0].path.startsWith('http') ? selectedMediaItems[0].path : window.location.origin + selectedMediaItems[0].path);
                if (activePreviewContainer.tagName === 'IMG') {
                    activePreviewContainer.src = previewSrc;
                    activePreviewContainer.style.display = 'block';
                } else if (currentMediaTypeFilter === 'video') {
                    activePreviewContainer.innerHTML = `<video src="${previewSrc}" class="w-100 rounded-3 mt-2" style="max-height: 150px;" controls></video>`;
                } else {
                    activePreviewContainer.innerHTML = `<img src="${previewSrc}" class="rounded-3 mt-2 img-fluid border" style="max-height: 120px; object-fit: cover;">`;
                }
            }
        }

        // Close modal
        const modalEl = document.getElementById('mediaPickerModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    }

    // Shows a toast if the shared AdminToast helper (resources/js/admin/tables.js) is
    // loaded on this page; falls back to a plain alert otherwise so feedback is never silent.
    function notify(type, message) {
        if (typeof window.AdminToast === 'function') {
            window.AdminToast(type, message);
        } else if (type === 'error') {
            alert(message);
        }
    }

    function buildGalleryUrl(page = 1) {
        const params = new URLSearchParams({
            type: currentMediaTypeFilter,
            folder: currentFolder,
            page: page
        });
        if (currentSearch) params.set('search', currentSearch);
        return `/admin/media/ajax-list?${params.toString()}`;
    }

    function resetAndLoadGallery() {
        nextMediaPageUrl = buildGalleryUrl(1);
        const container = document.getElementById('galleryGridContainer');
        if (container) container.innerHTML = '';
        loadGallery();
    }

    function setActiveFolderUI(folder) {
        document.querySelectorAll('.media-picker-folder-link').forEach(link => {
            link.classList.toggle('active', link.getAttribute('data-folder') === folder);
        });
    }

    // Load Gallery Function
    function loadGallery(append = false) {
        if (!nextMediaPageUrl) return;
        const container = document.getElementById('galleryGridContainer');
        if (!container) return;

        fetch(nextMediaPageUrl)
            .then(res => res.json())
            .then(response => {
                if (response.status === 'success') {
                    const items = response.data;
                    nextMediaPageUrl = response.next_page_url;

                    if (items.length === 0 && !append) {
                        container.innerHTML = '<div class="col-12 text-center py-4 text-muted">No files found in gallery.</div>';
                        return;
                    }

                    const loadMoreBtn = document.getElementById('btnLoadMoreMedia');
                    if (loadMoreBtn) {
                        if (nextMediaPageUrl) {
                            loadMoreBtn.classList.remove('d-none');
                        } else {
                            loadMoreBtn.classList.add('d-none');
                        }
                    }

                    items.forEach(item => {
                        const col = document.createElement('div');
                        col.className = 'col';
                        
                        let previewHtml = '';
                        if (item.file_type === 'video') {
                            previewHtml = `<div class="bg-dark rounded-3 d-flex align-items-center justify-content-center text-white" style="height: 100px;"><i class="bi bi-play-btn fs-2"></i></div>`;
                        } else {
                            previewHtml = `<img src="${item.full_url}" class="rounded-3 img-fluid object-fit-cover w-100" style="height: 100px; border: 1px solid #ECE7DD;" loading="lazy">`;
                        }

                        const normPath = item.file_path.startsWith('http') || item.file_path.startsWith('/')
                            ? item.file_path
                            : '/' + item.file_path;

                        const isAlreadySelected = selectedMediaItems.some(i => i.path === normPath);

                        col.innerHTML = `
                            <div class="gallery-picker-item cursor-pointer p-1 rounded-3 position-relative ${isAlreadySelected ? 'selected' : ''}" data-path="${item.file_path}" data-full-url="${item.full_url}" data-filename="${item.filename}">
                                <div class="selection-check-badge">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                ${previewHtml}
                                <div class="small text-truncate mt-1 text-muted text-center" style="font-size: 0.7rem;">${item.filename}</div>
                            </div>
                        `;

                        const card = col.querySelector('.gallery-picker-item');
                        card.addEventListener('click', function() {
                            toggleItemSelection({
                                path: this.getAttribute('data-path'),
                                fullUrl: this.getAttribute('data-full-url'),
                                filename: this.getAttribute('data-filename')
                            }, this);
                        });

                        // Double click shortcut for instant single selection
                        card.addEventListener('dblclick', function() {
                            if (!isMultiSelect) {
                                confirmAndApplySelection();
                            }
                        });

                        container.appendChild(col);
                    });
                }
            });
    }

    // Attach listeners on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        const loadMore = document.getElementById('btnLoadMoreMedia');
        if (loadMore) {
            loadMore.addEventListener('click', () => {
                loadGallery(true);
            });
        }

        // Confirm button
        const confirmBtn = document.getElementById('mediaPickerConfirmBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', confirmAndApplySelection);
        }

        // Clear selection button
        const clearBtn = document.getElementById('mediaPickerClearSelectionBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                selectedMediaItems = [];
                document.querySelectorAll('#galleryGridContainer .gallery-picker-item').forEach(el => el.classList.remove('selected'));
                updateFooterSelectionUI();
            });
        }

        // Folder sidebar switching
        document.querySelectorAll('.media-picker-folder-link').forEach(link => {
            link.addEventListener('click', function() {
                const folder = this.getAttribute('data-folder');
                if (folder === currentFolder) return;
                currentFolder = folder;
                setActiveFolderUI(folder);
                resetAndLoadGallery();
            });
        });

        // Search box (debounced)
        const searchInput = document.getElementById('mediaPickerSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimer);
                const value = this.value.trim();
                searchDebounceTimer = setTimeout(() => {
                    currentSearch = value;
                    resetAndLoadGallery();
                }, 350);
            });
        }

        // URL Import
        const importBtn = document.getElementById('btnImportFromUrl');
        const urlInput = document.getElementById('pickerUrlInput');
        const urlPreviewContainer = document.getElementById('urlPreviewContainer');
        const urlPreviewImage = document.getElementById('urlPreviewImage');

        if (urlInput && urlPreviewContainer && urlPreviewImage) {
            urlInput.addEventListener('input', function() {
                const url = this.value.trim();
                if (url && url.startsWith('http')) {
                    urlPreviewImage.src = url;
                    urlPreviewContainer.classList.remove('d-none');
                } else {
                    urlPreviewContainer.classList.add('d-none');
                    urlPreviewImage.src = '';
                }
            });
        }

        if (importBtn) {
            importBtn.addEventListener('click', function() {
                if (!urlInput) return;
                const url = urlInput.value;
                if (!url) return;

                const btn = this;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Importing...';
                btn.disabled = true;

                fetch('/admin/media/url-import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ url: url })
                })
                .then(res => res.json())
                .then(data => {
                    btn.innerHTML = 'Import Asset';
                    btn.disabled = false;

                    if (data.status === 'success') {
                        urlInput.value = '';
                        const fullUrl = data.media.url && data.media.url.startsWith('http')
                            ? data.media.url
                            : window.location.origin + '/' + data.media.file_path.replace(/^\//, '');
                        notify('success', 'Image imported successfully.');
                        
                        selectedMediaItems = [{
                            path: data.media.file_path,
                            fullUrl: fullUrl,
                            filename: data.media.file_name || 'Imported Asset'
                        }];
                        confirmAndApplySelection();
                    } else {
                        notify('error', data.message || 'Import failed.');
                    }
                })
                .catch(err => {
                    btn.innerHTML = 'Import Asset';
                    btn.disabled = false;
                    notify('error', 'Connection error occurred.');
                });
            });
        }

        // Local Upload
        const localFileInput = document.getElementById('pickerLocalFileInput');
        if (localFileInput) {
            localFileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                const progressContainer = document.getElementById('uploadProgressBarContainer');
                const progressBar = document.getElementById('uploadProgressBar');
                
                if (progressContainer) progressContainer.classList.remove('d-none');
                if (progressBar) progressBar.style.width = '0%';

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/admin/media/store', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable && progressBar) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                    }
                };

                xhr.onload = function() {
                    if (progressContainer) progressContainer.classList.add('d-none');
                    if (xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            const fullUrl = window.location.origin + '/' + response.media.file_path.replace(/^\//, '');
                            notify('success', 'Image uploaded successfully.');
                            selectedMediaItems = [{
                                path: response.media.file_path,
                                fullUrl: fullUrl,
                                filename: response.media.file_name || 'Uploaded Asset'
                            }];
                            confirmAndApplySelection();
                        } else {
                            notify('error', response.message || 'Upload failed.');
                        }
                    } else {
                        notify('error', 'Upload failed. Check file type and size.');
                    }
                    localFileInput.value = '';
                };

                xhr.onerror = function() {
                    if (progressContainer) progressContainer.classList.add('d-none');
                    notify('error', 'Upload network error occurred.');
                    localFileInput.value = '';
                };

                xhr.send(formData);
            });
        }
    });
})();
