<div class="row g-4">
    <!-- Add New Slider Banner -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold font-heading text-dark mb-3">Add New Slide Banner</h5>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Select banner images using media gallery. Desktop image is required. Mobile image is optional and optimized for narrow mobile screens.</p>

            <form action="{{ route('admin.homepage.slider.store') }}" method="POST">
                @csrf
                
                <!-- Desktop Banner Input (using Media Picker) -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Desktop Banner Image <span class="text-danger">*</span></label>
                    <input type="text" name="image_path" id="sliderImageInput" class="form-control" placeholder="Choose desktop banner image..." readonly required>
                    <div class="mt-2 border rounded-3 p-2 bg-light text-center" style="min-height: 100px;">
                        <img id="sliderImagePreview" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; display: none; object-fit: contain;">
                        <small id="sliderImagePlaceholder" class="text-muted d-block py-4">No image selected</small>
                    </div>
                    <small class="text-muted d-block mt-1">Recommended size: 1600x600px.</small>
                </div>

                <!-- Mobile Banner Input (using Media Picker) -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Mobile Banner Image <span class="text-muted">(Optional)</span></label>
                    <input type="text" name="mobile_image_path" id="sliderMobileImageInput" class="form-control" placeholder="Choose mobile banner image (optional)..." readonly>
                    <div class="mt-2 border rounded-3 p-2 bg-light text-center" style="min-height: 100px;">
                        <img id="sliderMobileImagePreview" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; display: none; object-fit: contain;">
                        <small id="sliderMobileImagePlaceholder" class="text-muted d-block py-4">Same as desktop banner (fallback)</small>
                    </div>
                    <small class="text-muted d-block mt-1">Optimized for vertical phones. Recommended size: 600x800px.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Button / Redirect Link URL (Optional)</label>
                    <input type="text" name="button_url" class="form-control" placeholder="e.g. /shop?category=ghee">
                    <small class="text-muted d-block mt-1">Clicking this slide will redirect users to this URL.</small>
                </div>

                <button type="submit" class="btn btn-warning w-100 py-2.5 rounded-3 fw-bold text-uppercase text-dark" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="bi bi-plus-circle me-1"></i> Add Slide Banner
                </button>
            </form>
        </div>
    </div>

    <!-- Existing Slider List -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold font-heading text-dark m-0">Active Banner Slides</h5>
                <span class="badge bg-success-subtle text-success py-1 px-2.5 rounded-pill" style="font-size: 0.7rem;"><i class="bi bi-info-circle me-1"></i> Drag to Reorder</span>
            </div>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">These slides are currently displayed in a looping slider at the top of the frontend homepage. Drag slides up or down to rearrange their order.</p>

            @if($sliders->isEmpty())
                <div class="text-center py-5 border rounded-4 bg-light" style="border-style: dashed !important; border-color: #ECE7DD !important;">
                    <i class="bi bi-images text-muted display-4 d-block mb-3"></i>
                    <h6 class="fw-bold text-dark m-0">No banners uploaded yet</h6>
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Use the left panel to upload your first banner slide.</p>
                </div>
            @else
                <div id="sortable-slider-list" class="list-group rounded-4 overflow-hidden border" style="border-color: #ECE7DD !important;">
                    @foreach($sliders as $slide)
                        <div class="list-group-item list-group-item-action slider-item p-3 border-bottom d-flex align-items-center justify-content-between" data-id="{{ $slide->id }}" style="cursor: grab; border-color: #ECE7DD !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="drag-handle text-muted" style="font-size: 1.25rem;">
                                    <i class="bi bi-grip-vertical"></i>
                                </div>
                                <div class="slide-preview border rounded overflow-hidden" style="width: 140px; height: 60px; background-image: url('{{ $slide->image_path }}'); background-size: cover; background-position: center; flex-shrink: 0;"></div>
                                @if($slide->mobile_image_path)
                                    <div class="slide-preview border rounded overflow-hidden" style="width: 45px; height: 60px; background-image: url('{{ $slide->mobile_image_path }}'); background-size: cover; background-position: center; flex-shrink: 0;" title="Mobile optimized creative banner"></div>
                                @endif
                                <div>
                                    <h6 class="fw-bold text-dark m-0" style="font-size: 0.9rem;">{{ $slide->title }}</h6>
                                    @if($slide->button_url)
                                        <small class="text-success d-block" style="font-size: 0.75rem;"><i class="bi bi-link-45deg me-0.5"></i> {{ $slide->button_url }}</small>
                                    @else
                                        <small class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-eye-slash me-0.5"></i> No Redirect URL</small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success edit-slider-btn"
                                        data-id="{{ $slide->id }}"
                                        data-image="{{ $slide->image_path }}"
                                        data-mobile-image="{{ $slide->mobile_image_path }}"
                                        data-url="{{ $slide->button_url }}"
                                        title="Edit Slide">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                
                                <form action="{{ route('admin.homepage.slider.delete', $slide->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this banner slide?');" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Slide">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
