<!-- Reusable Media Picker Modal -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="mediaPickerModalLabel">
                    <i class="bi bi-images text-success me-2"></i>Select Media
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs border-bottom mb-3" id="mediaPickerTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="upload-tab" data-bs-toggle="tab" data-bs-target="#tab-upload" type="button" role="tab" aria-controls="tab-upload" aria-selected="true">
                            <i class="bi bi-upload me-1"></i>Upload File
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#tab-gallery" type="button" role="tab" aria-controls="tab-gallery" aria-selected="false">
                            <i class="bi bi-grid-3x3-gap me-1"></i>Choose from Gallery
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="url-tab" data-bs-toggle="tab" data-bs-target="#tab-url" type="button" role="tab" aria-controls="tab-url" aria-selected="false">
                            <i class="bi bi-link-45deg me-1"></i>Insert from URL
                        </button>
                    </li>
                </ul>

                <!-- Tabs Content -->
                <div class="tab-content" id="mediaPickerTabsContent">
                    <!-- Local Upload -->
                    <div class="tab-pane fade show active py-3" id="tab-upload" role="tabpanel" aria-labelledby="upload-tab">
                        <div class="border border-2 border-dashed rounded-4 p-5 text-center bg-white" style="border-color: #ECE7DD !important;">
                            <i class="bi bi-cloud-arrow-up text-success display-4 mb-3"></i>
                            <h5 class="fw-bold">Drag & Drop file here</h5>
                            <p class="text-muted mb-4" style="font-size: 0.85rem;">or click to browse from your computer (Images or Videos max 20MB)</p>
                            <input type="file" id="pickerLocalFileInput" class="d-none">
                            <button type="button" class="btn btn-premium px-4 py-2 rounded-pill" onclick="document.getElementById('pickerLocalFileInput').click()">Browse Files</button>
                            <div class="progress mt-3 d-none" id="uploadProgressBarContainer" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="uploadProgressBar" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery List -->
                    <div class="tab-pane fade py-3" id="tab-gallery" role="tabpanel" aria-labelledby="gallery-tab">
                        <div class="row row-cols-3 row-cols-md-4 g-3 bg-white p-3 rounded-4 border overflow-y-auto" id="galleryGridContainer" style="max-height: 380px; border-color: #ECE7DD !important;">
                            <!-- items loaded dynamically -->
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-sm btn-outline-success px-4 py-2 rounded-pill d-none" id="btnLoadMoreMedia">Load More</button>
                        </div>
                    </div>

                    <!-- Insert From URL -->
                    <div class="tab-pane fade py-3" id="tab-url" role="tabpanel" aria-labelledby="url-tab">
                        <div class="bg-white p-4 rounded-4 border" style="border-color: #ECE7DD !important;">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Paste Direct File URL</label>
                                <input type="url" id="pickerUrlInput" class="form-control bg-light border p-2 shadow-none" placeholder="https://example.com/image.jpg" required>
                                <div class="form-text text-muted mb-2" style="font-size: 0.75rem;"><i class="bi bi-info-circle me-1"></i>Must be a direct link to an image or video file. System will download and save it.</div>
                                <div id="urlPreviewContainer" class="mt-2 text-center d-none p-2 border rounded-3 bg-white">
                                    <img id="urlPreviewImage" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; object-fit: contain;">
                                </div>
                            </div>
                            <button type="button" id="btnImportFromUrl" class="btn btn-premium px-4 py-2 rounded-pill">Import Asset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('admin_styles')
    <link rel="stylesheet" href="{{ asset('admin/css/media-picker.css') }}">
@endpush

@push('admin_scripts')
    <script src="{{ asset('admin/js/media-picker.js') }}"></script>
@endpush
