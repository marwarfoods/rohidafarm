<!-- Direct URL Modal on Media Manager Page -->
<div class="modal fade" id="directUrlPageModal" tabindex="-1" aria-labelledby="directUrlPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="directUrlPageModalLabel">
                    <i class="bi bi-globe2 text-primary me-2"></i>Add Direct URL Media
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="directUrlPageForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small py-2 px-3 mb-3">
                        <i class="bi bi-cloud-check me-1"></i><strong>Zero Local Storage:</strong> Saves an external direct URL (Cloudinary, Imgur, CDN, etc.). Serves directly from the remote URL with zero server disk usage.
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">External File URL <span class="text-danger">*</span></label>
                        <input type="url" name="url" id="directUrlPageInput" class="form-control bg-light border p-2 shadow-none font-monospace" placeholder="https://res.cloudinary.com/... or https://example.com/image.jpg" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Display Name (Optional)</label>
                        <input type="text" name="filename" id="directUrlPageNameInput" class="form-control bg-light border p-2 shadow-none" placeholder="e.g. Rohida Ghee Banner (Leave blank to use filename from URL)">
                    </div>

                    <div id="directUrlPagePreviewContainer" class="mt-3 text-center d-none p-3 border rounded-3 bg-light">
                        <div class="mb-2 small text-muted fw-semibold"><i class="bi bi-eye me-1"></i>Preview:</div>
                        <img id="directUrlPagePreviewImage" src="" class="img-fluid rounded-3 mx-auto border shadow-sm" style="max-height: 180px; object-fit: contain; background: #fff;" onerror="this.classList.add('d-none');" onload="this.classList.remove('d-none');">
                        <video id="directUrlPagePreviewVideo" src="" class="w-100 rounded-3 mx-auto border shadow-sm d-none" style="max-height: 180px;" controls></video>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSubmitDirectUrlPage" class="btn btn-primary rounded-pill px-4 font-heading fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Save Direct URL
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
