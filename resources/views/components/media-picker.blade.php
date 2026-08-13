@php
    // Folder counts for the picker's sidebar badges — mirrors the counts shown on
    // the full Media Gallery page (admin.media.index) so both stay in sync.
    $pickerCounts = [
        'all' => \App\Models\MediaItem::count(),
        'product-images' => \App\Models\MediaItem::where('file_path', 'like', '%uploads/products/%')->where('file_type', 'image')->count(),
        'sliders' => \App\Models\MediaItem::where('file_path', 'like', '%uploads/sliders/%')->where('file_type', 'image')->count(),
        'reviews' => \App\Models\MediaItem::where('file_path', 'like', '%uploads/reviews/%')->where('file_type', 'image')->count(),
        'settings' => \App\Models\MediaItem::where('file_path', 'like', '%uploads/settings/%')->where('file_type', 'image')->count(),
        'videos' => \App\Models\MediaItem::where('file_type', 'video')->count(),
        'unsplash' => \App\Models\MediaItem::whereNotNull('url')->count(),
    ];
@endphp

<!-- Reusable Media Picker Modal -->
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-labelledby="mediaPickerModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered media-picker-dialog">
        <div class="modal-content rounded-4 border-0 shadow media-picker-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="mediaPickerModalLabel">
                    <i class="bi bi-images text-success me-2"></i>Select Media
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light d-flex flex-column">
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
                <div class="tab-content flex-grow-1 d-flex" id="mediaPickerTabsContent" style="min-height: 0;">
                    <!-- Local Upload -->
                    <div class="tab-pane fade show active py-3 w-100" id="tab-upload" role="tabpanel" aria-labelledby="upload-tab">
                        <div class="border border-2 border-dashed rounded-4 p-5 text-center bg-white" style="border-color: #ECE7DD !important;">
                            <i class="bi bi-cloud-arrow-up text-success display-4 mb-3"></i>
                            <h5 class="fw-bold">Drag & Drop file here</h5>
                            <p class="text-muted mb-4" style="font-size: 0.85rem;">or click to browse from your computer (Images or Videos max 200MB)</p>
                            <input type="file" id="pickerLocalFileInput" class="d-none">
                            <button type="button" class="btn btn-premium px-4 py-2 rounded-pill" onclick="document.getElementById('pickerLocalFileInput').click()">Browse Files</button>
                            <div class="progress mt-3 d-none" id="uploadProgressBarContainer" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="uploadProgressBar" role="progressbar" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery: Sidebar Folders + Search + Grid -->
                    <div class="tab-pane fade py-3 w-100 media-picker-gallery-pane" id="tab-gallery" role="tabpanel" aria-labelledby="gallery-tab">
                        <div class="d-flex gap-3 h-100">
                            <!-- Folder Sidebar -->
                            <div class="media-picker-sidebar bg-white rounded-4 border p-2" style="border-color: #ECE7DD !important;">
                                <ul class="list-unstyled m-0" id="mediaPickerFolderList">
                                    <li>
                                        <button type="button" class="media-picker-folder-link active w-100 text-start" data-folder="all">
                                            <i class="bi bi-images me-2"></i>All Media
                                            <span class="float-end text-muted small">{{ $pickerCounts['all'] }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="product-images">
                                            <i class="bi bi-folder-fill me-2"></i>Product Images
                                            <span class="float-end text-muted small">{{ $pickerCounts['product-images'] }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="sliders">
                                            <i class="bi bi-folder-fill me-2"></i>Sliders
                                            <span class="float-end text-muted small">{{ $pickerCounts['sliders'] }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="reviews">
                                            <i class="bi bi-folder-fill me-2"></i>Reviews
                                            <span class="float-end text-muted small">{{ $pickerCounts['reviews'] }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="settings">
                                            <i class="bi bi-folder-fill me-2"></i>Settings
                                            <span class="float-end text-muted small">{{ $pickerCounts['settings'] }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="unsplash">
                                            <i class="bi bi-folder-fill me-2"></i>From Unsplash
                                            <span class="float-end text-muted small">{{ $pickerCounts['unsplash'] }}</span>
                                        </button>
                                    </li>
                                    <li class="media-picker-folder-video-only">
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="videos">
                                            <i class="bi bi-folder-fill me-2"></i>Videos
                                            <span class="float-end text-muted small">{{ $pickerCounts['videos'] }}</span>
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="media-picker-folder-link w-100 text-start" data-folder="built-in">
                                            <i class="bi bi-box-seam me-2"></i>Built-in Images
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Right: Search + Grid -->
                            <div class="d-flex flex-column flex-grow-1" style="min-width: 0;">
                                <div class="position-relative mb-3">
                                    <i class="bi bi-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%);"></i>
                                    <input type="text" id="mediaPickerSearchInput" class="form-control ps-5 bg-white border shadow-none" placeholder="Search files by name..." style="border-color: #ECE7DD !important;">
                                </div>
                                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 bg-white p-3 rounded-4 border overflow-y-auto flex-grow-1" id="galleryGridContainer" style="border-color: #ECE7DD !important;">
                                    <!-- items loaded dynamically -->
                                </div>
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-success px-4 py-2 rounded-pill d-none" id="btnLoadMoreMedia">Load More</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Insert From URL -->
                    <div class="tab-pane fade py-3 w-100" id="tab-url" role="tabpanel" aria-labelledby="url-tab">
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

{{--
    NOTE: This component's CSS/JS are included directly in layouts/admin.blade.php's
    <head>, NOT via @push('admin_styles') here. @stack('admin_styles') in the layout's
    <head> renders BEFORE this component (placed in <body>) ever executes, so anything
    pushed from here is always too late to be captured — the stack has already printed.
--}}
