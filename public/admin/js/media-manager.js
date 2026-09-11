/**
 * RohidaFarm - Media Manager Page Script
 * Separated cleanly from admin/media/index.blade.php
 */
document.addEventListener('DOMContentLoaded', function () {
    const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ── Batch File Upload with Real XHR Progress ──
    const fileInput = document.getElementById('galleryPageFileInput');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const files = Array.from(this.files || []);
            if (files.length === 0) return;

            const progressContainer = document.getElementById('galleryPageProgressContainer');
            const progressList = document.getElementById('galleryPageProgressList');
            if (!progressContainer || !progressList) return;

            progressContainer.classList.remove('d-none');
            progressList.innerHTML = '';

            const rows = files.map((file) => {
                const row = document.createElement('div');
                row.className = 'upload-progress-row';
                row.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-truncate text-dark fw-semibold" style="font-size: 0.8rem; max-width: 70%;">${file.name}</span>
                        <span class="text-muted status-text" style="font-size: 0.75rem;">Queued</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                    </div>
                `;
                progressList.appendChild(row);
                return {
                    bar: row.querySelector('.progress-bar'),
                    status: row.querySelector('.status-text'),
                };
            });

            let uploadedCount = 0;
            let failedCount = 0;

            function uploadNext(index) {
                if (index >= files.length) {
                    if (failedCount > 0) {
                        alert(`${uploadedCount} file(s) uploaded, ${failedCount} failed. Check file size (max 200MB) and formats.`);
                    }
                    if (uploadedCount > 0) {
                        window.location.reload();
                    } else {
                        progressContainer.classList.add('d-none');
                    }
                    return;
                }

                const { bar, status } = rows[index];
                if (status) status.textContent = 'Uploading...';

                const formData = new FormData();
                formData.append('file', files[index]);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/admin/media/store', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', getCsrfToken());
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable && bar && status) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        bar.style.width = percent + '%';
                        status.textContent = percent + '%';
                    }
                };

                xhr.onload = function () {
                    if (xhr.status === 200) {
                        uploadedCount++;
                        if (bar) {
                            bar.style.width = '100%';
                            bar.className = 'progress-bar bg-success';
                        }
                        if (status) {
                            status.textContent = 'Done';
                            status.className = 'text-success fw-bold';
                        }
                    } else {
                        failedCount++;
                        if (bar) {
                            bar.style.width = '100%';
                            bar.className = 'progress-bar bg-danger';
                        }
                        if (status) {
                            status.textContent = 'Failed';
                            status.className = 'text-danger fw-bold';
                        }
                    }
                    uploadNext(index + 1);
                };

                xhr.onerror = function () {
                    failedCount++;
                    if (bar) {
                        bar.style.width = '100%';
                        bar.className = 'progress-bar bg-danger';
                    }
                    if (status) {
                        status.textContent = 'Failed';
                        status.className = 'text-danger fw-bold';
                    }
                    uploadNext(index + 1);
                };

                xhr.send(formData);
            }

            uploadNext(0);
        });
    }

    // ── Single Item Compression Handler ──
    window.compressSingleMedia = function (btn, id) {
        if (!btn || btn.disabled) return;

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="width: 0.75rem; height: 0.75rem;"></span> Compressing...';
        btn.classList.remove('btn-warning');
        btn.classList.add('btn-secondary');

        fetch(`/admin/media/${id}/compress`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Failed to compress');
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const sizeLabel = document.getElementById(`mediaCardSize_${id}`);
                    if (sizeLabel) {
                        sizeLabel.innerHTML = `${data.new_size} <span class="badge bg-success bg-opacity-10 text-success ms-1">-${data.saved_percent}%</span>`;
                    }

                    btn.innerHTML = `<i class="bi bi-check-circle-fill"></i> Saved ${data.saved_percent}%`;
                    btn.className = 'btn-compress-single btn btn-success text-white px-3 py-1 rounded fw-bold border-0';

                    showToast('Success!', `Compressed by ${data.saved_percent}% (${data.saved_formatted} saved)`, 'success');
                } else {
                    btn.innerHTML = '<i class="bi bi-info-circle-fill"></i> Optimized';
                    btn.className = 'btn-compress-single btn btn-info px-3 py-1 rounded fw-bold border-0';
                    showToast('Info', data.message || 'Image is already optimized.', 'info');
                }
            })
            .catch(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                btn.className = 'btn-compress-single btn btn-warning px-3 py-1 rounded fw-bold text-dark border-0';
                showToast('Error', 'An error occurred during compression.', 'danger');
            });
    };

    // Helper toast function
    function showToast(title, body, type = 'success') {
        const toastContainer = document.getElementById('admin-toast-container');
        if (toastContainer) {
            const toast = document.createElement('div');
            toast.className = 'admin-toast';
            toast.innerHTML = `
                <div class="toast-icon bg-${type} text-white">
                    <i class="bi bi-${type === 'success' ? 'check-lg' : (type === 'danger' ? 'x-lg' : 'info-circle')}"></i>
                </div>
                <div class="toast-body">
                    <strong>${title}</strong> ${body}
                </div>
            `;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        } else {
            alert(`${title} ${body}`);
        }
    }

    // ── Direct URL Modal on Media Manager Page ──
    const directUrlPageInput = document.getElementById('directUrlPageInput');
    const directUrlPagePreviewContainer = document.getElementById('directUrlPagePreviewContainer');
    const directUrlPagePreviewImage = document.getElementById('directUrlPagePreviewImage');
    const directUrlPagePreviewVideo = document.getElementById('directUrlPagePreviewVideo');
    const directUrlPageForm = document.getElementById('directUrlPageForm');
    const btnSubmitDirectUrlPage = document.getElementById('btnSubmitDirectUrlPage');

    if (directUrlPageInput) {
        directUrlPageInput.addEventListener('input', function () {
            const url = this.value.trim();
            if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                const isVideo = /\.(mp4|webm|mov|avi|mkv)(\?.*)?$/i.test(url);
                if (isVideo) {
                    if (directUrlPagePreviewVideo) {
                        directUrlPagePreviewVideo.src = url;
                        directUrlPagePreviewVideo.classList.remove('d-none');
                    }
                    if (directUrlPagePreviewImage) directUrlPagePreviewImage.classList.add('d-none');
                } else {
                    if (directUrlPagePreviewImage) {
                        directUrlPagePreviewImage.src = url;
                        directUrlPagePreviewImage.classList.remove('d-none');
                    }
                    if (directUrlPagePreviewVideo) directUrlPagePreviewVideo.classList.add('d-none');
                }
                if (directUrlPagePreviewContainer) directUrlPagePreviewContainer.classList.remove('d-none');
            } else {
                if (directUrlPagePreviewContainer) directUrlPagePreviewContainer.classList.add('d-none');
                if (directUrlPagePreviewImage) directUrlPagePreviewImage.src = '';
                if (directUrlPagePreviewVideo) directUrlPagePreviewVideo.src = '';
            }
        });
    }

    if (directUrlPageForm) {
        directUrlPageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const url = directUrlPageInput ? directUrlPageInput.value.trim() : '';
            const filenameInput = document.getElementById('directUrlPageNameInput');
            const filename = filenameInput ? filenameInput.value.trim() : '';
            if (!url) return;

            if (btnSubmitDirectUrlPage) {
                btnSubmitDirectUrlPage.disabled = true;
                btnSubmitDirectUrlPage.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
            }

            fetch('/admin/media/direct-url', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ url: url, filename: filename })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to save direct URL.');
                        if (btnSubmitDirectUrlPage) {
                            btnSubmitDirectUrlPage.disabled = false;
                            btnSubmitDirectUrlPage.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Save Direct URL';
                        }
                    }
                })
                .catch(() => {
                    alert('Connection error occurred.');
                    if (btnSubmitDirectUrlPage) {
                        btnSubmitDirectUrlPage.disabled = false;
                        btnSubmitDirectUrlPage.innerHTML = '<i class="bi bi-check2-circle me-1"></i>Save Direct URL';
                    }
                });
        });
    }
});
