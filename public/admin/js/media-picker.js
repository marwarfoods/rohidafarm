(function() {
    let activeTargetInput = null;
    let activePreviewContainer = null;
    let nextMediaPageUrl = null;
    let currentMediaTypeFilter = 'image'; // default filter
    let currentFolder = 'all';
    let currentSearch = '';
    let searchDebounceTimer = null;

    // Define function globally immediately so page inline scripts don't hit race conditions
    window.initMediaPicker = function(inputSelector, previewSelector = null, type = 'image') {
        const inputs = document.querySelectorAll(inputSelector);
        inputs.forEach(input => {
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
                activeTargetInput = input;
                activePreviewContainer = previewSelector ? document.querySelector(previewSelector) : null;
                currentMediaTypeFilter = type;
                currentFolder = 'all';
                currentSearch = '';

                const searchInput = document.getElementById('mediaPickerSearchInput');
                if (searchInput) searchInput.value = '';

                // The Videos folder only makes sense when the picker isn't locked to images
                const videoFolderLi = document.querySelector('.media-picker-folder-video-only');
                if (videoFolderLi) videoFolderLi.style.display = (type === 'video') ? '' : 'none';

                setActiveFolderUI('all');
                resetAndLoadGallery();

                const modal = new bootstrap.Modal(document.getElementById('mediaPickerModal'));
                modal.show();
            });
            wrapper.appendChild(btn);
        });
    };

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
                            previewHtml = `<img src="${item.full_url}" class="rounded-3 img-fluid object-fit-cover w-100" style="height: 100px; border: 1px solid #ECE7DD;">`;
                        }

                        col.innerHTML = `
                            <div class="gallery-picker-item cursor-pointer p-1 rounded-3 position-relative" data-path="${item.file_path}" data-full-url="${item.full_url}" style="transition: all 0.2s ease;">
                                ${previewHtml}
                                <div class="small text-truncate mt-1 text-muted text-center" style="font-size: 0.7rem;">${item.filename}</div>
                            </div>
                        `;

                        col.querySelector('.gallery-picker-item').addEventListener('click', function() {
                            selectMedia(this.getAttribute('data-path'), this.getAttribute('data-full-url'));
                        });

                        container.appendChild(col);
                    });
                }
            });
    }

    // Selection Action — filePath is relative (stored in DB), fullUrl is absolute (for img src preview)
    function selectMedia(filePath, fullUrl) {
        // DB records are inconsistent — some have a leading slash, some don't. Normalize
        // to root-relative ("/uploads/...") so any consumer of the input's raw value
        // (including pages' own duplicate preview listeners) resolves it against the
        // domain root instead of the current admin page path (which was producing
        // broken "/admin/uploads/..." 404s).
        const normalizedPath = filePath.startsWith('http') || filePath.startsWith('/')
            ? filePath
            : '/' + filePath;

        // Fallback: if no fullUrl given, construct an absolute URL from the origin.
        const previewSrc = fullUrl || (normalizedPath.startsWith('http') ? normalizedPath : window.location.origin + normalizedPath);

        if (activeTargetInput) {
            activeTargetInput.value = normalizedPath;

            // Trigger change event dynamically (some pages listen for this to run their own preview/validation)
            activeTargetInput.dispatchEvent(new Event('change'));

            // Set our own preview LAST so it wins over any page-level 'change' listener
            // that might set a broken relative src.
            if (activePreviewContainer) {
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
        const modalEl = document.getElementById('mediaPickerModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
    }

    // Attach listeners on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        const loadMore = document.getElementById('btnLoadMoreMedia');
        if (loadMore) {
            loadMore.addEventListener('click', () => {
                loadGallery(true);
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
                        // Build full_url from relative file_path
                        const fullUrl = data.media.url && data.media.url.startsWith('http')
                            ? data.media.url
                            : window.location.origin + '/' + data.media.file_path.replace(/^\//, '');
                        notify('success', 'Image imported successfully.');
                        selectMedia(data.media.file_path, fullUrl);
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
                            selectMedia(response.media.file_path, fullUrl);
                        } else {
                            notify('error', response.message || 'Upload failed.');
                        }
                    } else {
                        notify('error', 'Upload failed. Check file type and size.');
                    }
                    // Reset the file input so choosing the same file again re-triggers 'change'
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
