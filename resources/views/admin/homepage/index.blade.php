@extends('layouts.admin')

@push('admin_styles')
<style>
    #homepageTabs .nav-link {
        border-radius: 8px 8px 0 0;
        transition: all 0.2s ease-in-out;
        margin-bottom: -1px;
    }
    #homepageTabs .nav-link:hover {
        background-color: #f3f8f5 !important;
    }
    #homepageTabs .nav-link.active {
        background-color: #e8f5e9 !important;
        border-bottom: 3px solid #1a6b36 !important;
    }
</style>
@endpush

@section('admin_content')
<div class="row">
    <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 m-0 text-dark font-heading fw-bold">Homepage Management</h1>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Manage sliders, dynamic banners, and top announcement tickers.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="col-12 mb-4">
        <ul class="nav nav-tabs border-bottom" id="homepageTabs" role="tablist" style="border-color: #ECE7DD !important;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold text-success border-0 px-4 py-3 position-relative" id="slider-tab" data-bs-toggle="tab" data-bs-target="#slider-content" type="button" role="tab" aria-controls="slider-content" aria-selected="true" style="font-size: 0.9rem; background: transparent;">
                    <i class="bi bi-images me-2"></i> Hero Banner Slider
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-muted border-0 px-4 py-3 position-relative" id="ticker-tab" data-bs-toggle="tab" data-bs-target="#ticker-content" type="button" role="tab" aria-controls="ticker-content" aria-selected="false" style="font-size: 0.9rem; background: transparent;">
                    <i class="bi bi-megaphone me-2"></i> Top Header Ticker
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-muted border-0 px-4 py-3 position-relative" id="promo-tab" data-bs-toggle="tab" data-bs-target="#promo-content" type="button" role="tab" aria-controls="promo-content" aria-selected="false" style="font-size: 0.9rem; background: transparent;">
                    <i class="bi bi-grid-3x3-gap me-2"></i> Promo Sections & Banners
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-muted border-0 px-4 py-3 position-relative" id="native-tab" data-bs-toggle="tab" data-bs-target="#native-content" type="button" role="tab" aria-controls="native-content" aria-selected="false" style="font-size: 0.9rem; background: transparent;">
                    <i class="bi bi-flower1 me-2"></i> Native Ingredients
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-muted border-0 px-4 py-3 position-relative" id="section-image-tab" data-bs-toggle="tab" data-bs-target="#section-image-content" type="button" role="tab" aria-controls="section-image-content" aria-selected="false" style="font-size: 0.9rem; background: transparent;">
                    <i class="bi bi-image me-2"></i> Section Image
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold text-muted border-0 px-4 py-3 position-relative" id="bilona-tab" data-bs-toggle="tab" data-bs-target="#bilona-content" type="button" role="tab" aria-controls="bilona-content" aria-selected="false" style="font-size: 0.9rem; background: transparent;">
                    <i class="bi bi-signpost-split me-2"></i> Vedic Craftsmanship Steps
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content w-100" id="homepageTabsContent">
        <!-- TAB 1: HERO BANNER SLIDER -->
        <div class="tab-pane fade show active col-12" id="slider-content" role="tabpanel" aria-labelledby="slider-tab">
            @include('admin.homepage.tabs.slider')
        </div>

        <!-- TAB 2: TOP HEADER TICKER -->
        <div class="tab-pane fade col-12" id="ticker-content" role="tabpanel" aria-labelledby="ticker-tab">
            @include('admin.homepage.tabs.ticker')
        </div>

        <!-- TAB 3: PROMO SECTIONS & BANNERS -->
        <div class="tab-pane fade col-12" id="promo-content" role="tabpanel" aria-labelledby="promo-tab">
            @include('admin.homepage.tabs.promo')
        </div>
        <!-- TAB 4: NATIVE INGREDIENTS -->
        <div class="tab-pane fade col-12" id="native-content" role="tabpanel" aria-labelledby="native-tab">
            @include('admin.homepage.tabs.native')
        </div>
        
        <!-- TAB 5: SECTION IMAGE -->
        <div class="tab-pane fade col-12" id="section-image-content" role="tabpanel" aria-labelledby="section-image-tab">
            @include('admin.homepage.tabs.section-image')
        </div>

        <!-- TAB 6: VEDIC CRAFTSMANSHIP (BILONA PROCESS) STEPS -->
        <div class="tab-pane fade col-12" id="bilona-content" role="tabpanel" aria-labelledby="bilona-tab">
            @include('admin.homepage.tabs.bilona-steps')
        </div>
    </div>
</div>

<!-- ═══ EDIT SLIDER MODAL ═══ -->
<div class="modal fade" id="editSliderModal" tabindex="-1" aria-labelledby="editSliderModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="editSliderModalLabel">
                    <i class="bi bi-pencil-square text-success me-2"></i>Edit Banner Slide
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSliderForm" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <!-- Desktop Banner Input -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Desktop Banner Image <span class="text-danger">*</span></label>
                        <input type="text" name="image_path" id="editSliderImageInput" class="form-control" placeholder="Choose desktop banner image..." readonly required>
                        <div class="mt-2 border rounded-3 p-2 bg-white text-center" style="min-height: 100px;">
                            <img id="editSliderImagePreview" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; object-fit: contain;">
                        </div>
                    </div>

                    <!-- Mobile Banner Input -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Mobile Banner Image <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="mobile_image_path" id="editSliderMobileImageInput" class="form-control" placeholder="Choose mobile banner image (optional)..." readonly>
                        <div class="mt-2 border rounded-3 p-2 bg-white text-center" style="min-height: 100px;">
                            <img id="editSliderMobileImagePreview" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; display: none; object-fit: contain;">
                            <small id="editSliderMobileImagePlaceholder" class="text-muted d-block py-4">Same as desktop banner (fallback)</small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Button / Redirect Link URL</label>
                        <input type="text" name="button_url" id="editSliderUrlInput" class="form-control" placeholder="e.g. /shop?category=ghee">
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-white">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning px-4 rounded-pill fw-bold text-uppercase text-dark" style="font-size: 0.8rem; font-weight: 700;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('admin_scripts')
<!-- Load SortableJS for drag and drop reordering -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab UI state management
        const tabTriggers = document.querySelectorAll('#homepageTabs button');
        tabTriggers.forEach(trigger => {
            trigger.addEventListener('shown.bs.tab', function(e) {
                // Remove green class from all tabs, add gray class
                tabTriggers.forEach(btn => {
                    btn.classList.remove('text-success');
                    btn.classList.add('text-muted');
                });
                // Add green class to active tab
                e.target.classList.remove('text-muted');
                e.target.classList.add('text-success');
                
                // Update URL Hash without scrolling
                const targetHash = e.target.getAttribute('data-bs-target');
                if (history.replaceState) {
                    history.replaceState(null, null, targetHash);
                } else {
                    window.location.hash = targetHash;
                }
            });
        });
        // Initialize Media Pickers for all slider banner fields
        if (window.initMediaPicker) {
            initMediaPicker('#sliderImageInput', '#sliderImagePreview', 'image');
            initMediaPicker('#sliderMobileImageInput', '#sliderMobileImagePreview', 'image');
            initMediaPicker('#editSliderImageInput', '#editSliderImagePreview', 'image');
            initMediaPicker('#editSliderMobileImageInput', '#editSliderMobileImagePreview', 'image');
            initMediaPicker('#sectionImagePcInput', '#sectionImagePcPreview', 'image');
            initMediaPicker('#sectionImageMobileInput', '#sectionImageMobilePreview', 'image');
        }

        // Live preview listeners for desktop banners
        const setupPreview = (inputId, previewId, placeholderId = null) => {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = placeholderId ? document.getElementById(placeholderId) : null;
            if (input && preview) {
                input.addEventListener('change', function() {
                    const val = input.value;
                    if (val) {
                        preview.src = val;
                        preview.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    } else {
                        preview.style.display = 'none';
                        if (placeholder) placeholder.style.display = 'block';
                    }
                });
            }
        };

        setupPreview('sliderImageInput', 'sliderImagePreview', 'sliderImagePlaceholder');
        setupPreview('sliderMobileImageInput', 'sliderMobileImagePreview', 'sliderMobileImagePlaceholder');
        setupPreview('editSliderImageInput', 'editSliderImagePreview');
        setupPreview('editSliderMobileImageInput', 'editSliderMobileImagePreview', 'editSliderMobileImagePlaceholder');
        setupPreview('sectionImagePcInput', 'sectionImagePcPreview', 'sectionImagePcPlaceholder');
        setupPreview('sectionImageMobileInput', 'sectionImageMobilePreview', 'sectionImageMobilePlaceholder');

        // Edit button triggers
        const editModalEl = document.getElementById('editSliderModal');
        const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
        
        document.querySelectorAll('.edit-slider-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const image = this.getAttribute('data-image');
                const mobileImage = this.getAttribute('data-mobile-image');
                const url = this.getAttribute('data-url');

                // Set form action action
                document.getElementById('editSliderForm').action = `/admin/homepage/slider/${id}/update`;

                // Set values
                const imgInput = document.getElementById('editSliderImageInput');
                imgInput.value = image;
                imgInput.dispatchEvent(new Event('change'));

                const mobInput = document.getElementById('editSliderMobileImageInput');
                mobInput.value = mobileImage || '';
                mobInput.dispatchEvent(new Event('change'));

                document.getElementById('editSliderUrlInput').value = url || '';

                if (editModal) editModal.show();
            });
        });

        // Initialize SortableJS for slider banner slides
        var sliderEl = document.getElementById('sortable-slider-list');
        if (sliderEl) {
            Sortable.create(sliderEl, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'bg-light-subtle',
                chosenClass: 'bg-light',
                onEnd: function (evt) {
                    var order = [];
                    document.querySelectorAll('#sortable-slider-list .slider-item').forEach(function(item) {
                        order.push(item.getAttribute('data-id'));
                    });

                    // Send AJAX Request to update sorting sequence
                    fetch('{{ route("admin.homepage.slider.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            console.log('Sliders successfully reordered!');
                        }
                    })
                    .catch(error => {
                        console.error('Reordering failed:', error);
                    });
                }
            });
        }

        // Initialize SortableJS for top header tickers
        var tickerEl = document.getElementById('sortable-ticker-list');
        if (tickerEl) {
            Sortable.create(tickerEl, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'bg-light-subtle',
                chosenClass: 'bg-light',
                onEnd: function (evt) {
                    var order = [];
                    document.querySelectorAll('#sortable-ticker-list .ticker-item').forEach(function(item) {
                        order.push(item.getAttribute('data-id'));
                    });

                    // Send AJAX Request to update sorting sequence
                    fetch('{{ route("admin.homepage.ticker.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            console.log('Tickers successfully reordered!');
                        }
                    })
                    .catch(error => {
                        console.error('Reordering failed:', error);
                    });
                }
            });
        }

        // Initialize SortableJS for Native Ingredients
        var nativeEl = document.getElementById('native-table').querySelector('tbody');
        if (nativeEl) {
            Sortable.create(nativeEl, {
                animation: 200,
                handle: '.cursor-move',
                ghostClass: 'bg-light-subtle',
                chosenClass: 'bg-light',
                onEnd: function (evt) {
                    var order = [];
                    nativeEl.querySelectorAll('tr[data-id]').forEach(function(item) {
                        order.push(item.getAttribute('data-id'));
                    });

                    fetch('{{ route("admin.homepage.native.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            console.log('Native ingredients successfully reordered!');
                        }
                    });
                }
            });
        }
        
        // Register Native Ingredients Media Pickers
        if (window.initMediaPicker) {
            initMediaPicker('#nativeImageInput', '#nativeImagePreview', 'image');
            setupPreview('nativeImageInput', 'nativeImagePreview', 'nativeImagePlaceholder');
            @foreach($nativeIngredients as $item)
                initMediaPicker('#nativeImageInputEdit{{ $item->id }}', '#nativeImagePreviewEdit{{ $item->id }}', 'image');
                setupPreview('nativeImageInputEdit{{ $item->id }}', 'nativeImagePreviewEdit{{ $item->id }}');
            @endforeach
        }

        // Initialize SortableJS for Vedic Craftsmanship (Bilona) process steps
        var bilonaTableEl = document.getElementById('bilona-table');
        var bilonaEl = bilonaTableEl ? bilonaTableEl.querySelector('tbody') : null;
        if (bilonaEl) {
            Sortable.create(bilonaEl, {
                animation: 200,
                handle: '.cursor-move',
                ghostClass: 'bg-light-subtle',
                chosenClass: 'bg-light',
                onEnd: function (evt) {
                    var order = [];
                    bilonaEl.querySelectorAll('tr[data-id]').forEach(function(item) {
                        order.push(item.getAttribute('data-id'));
                    });

                    fetch('{{ route("admin.homepage.bilona.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.reload();
                        }
                    });
                }
            });
        }

        // Register Vedic Craftsmanship Step Media Pickers
        if (window.initMediaPicker) {
            initMediaPicker('#bilonaImageInput', '#bilonaImagePreview', 'image');
            setupPreview('bilonaImageInput', 'bilonaImagePreview', 'bilonaImagePlaceholder');
            @foreach($bilonaSteps as $item)
                initMediaPicker('#bilonaImageInputEdit{{ $item->id }}', '#bilonaImagePreviewEdit{{ $item->id }}', 'image');
                setupPreview('bilonaImageInputEdit{{ $item->id }}', 'bilonaImagePreviewEdit{{ $item->id }}');
            @endforeach
        }


        // Trigger correct tab on load based on hash
        const activeHash = window.location.hash;
        if (activeHash) {
            const activeTabBtn = document.querySelector(`button[data-bs-target="${activeHash}"]`);
            if (activeTabBtn) {
                const tab = new bootstrap.Tab(activeTabBtn);
                tab.show();
            }
        }
    });
</script>
@endpush
