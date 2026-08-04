<!-- Reusable Icon Picker Modal -->
<div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-heading fw-bold text-dark" id="iconPickerModalLabel">
                    <i class="bi bi-tags text-success me-2"></i>Icon Library
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light overflow-hidden" style="height: 520px;">
                <div class="d-flex h-100">
                    <!-- Left Sidebar - Categories -->
                    <div class="bg-white border-end p-3 flex-shrink-0" style="width: 220px; overflow-y: auto;">
                        <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.72rem; letter-spacing: 0.5px;">Categories</h6>
                        <ul class="nav flex-column gap-1" id="iconCategoryList">
                            <li>
                                <button type="button" class="nav-link w-100 text-start active-cat-tab px-3 py-2 rounded-3 border-0 fw-semibold text-success bg-success-subtle" data-cat="all" style="font-size: 0.85rem; transition: all 0.2s;">
                                    All Icons
                                </button>
                            </li>
                            @php
                                $iconGroups = [
                                    'general' => 'General & Trust',
                                    'agriculture' => 'Agriculture & Organic',
                                    'dairy' => 'Dairy, Ghee & Oils',
                                    'dryfruits' => 'Dry Fruits & Nuts',
                                    'honey' => 'Honey & Sweeteners',
                                    'pickles' => 'Pickles & Spices',
                                    'shipping' => 'Shopping & Shipping',
                                    'wellness' => 'Wellness & Health',
                                    'website' => 'General Website'
                                ];
                            @endphp
                            @foreach($iconGroups as $key => $label)
                                <li>
                                    <button type="button" class="nav-link w-100 text-start px-3 py-2 rounded-3 border-0 text-muted bg-transparent" data-cat="{{ $key }}" style="font-size: 0.85rem; transition: all 0.2s;">
                                        {{ $label }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Right Panel - Grid & Search -->
                    <div class="d-flex flex-column flex-grow-1 h-100 p-4">
                        <!-- Search Box -->
                        <div class="mb-3">
                            <div class="input-group bg-white rounded-3 border">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="iconSearchInput" class="form-control border-0 bg-transparent py-2 shadow-none" placeholder="Search icon by name...">
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="flex-grow-1 overflow-y-auto bg-white rounded-4 border p-3" id="iconGridContainer" style="border-color: #ECE7DD !important;">
                            @php
                                $groupedIcons = [
                                    'general' => include(resource_path('views/components/icons/general.php')),
                                    'agriculture' => include(resource_path('views/components/icons/agriculture.php')),
                                    'dairy' => include(resource_path('views/components/icons/dairy.php')),
                                    'dryfruits' => include(resource_path('views/components/icons/dryfruits.php')),
                                    'honey' => include(resource_path('views/components/icons/honey.php')),
                                    'pickles' => include(resource_path('views/components/icons/pickles.php')),
                                    'shipping' => include(resource_path('views/components/icons/shipping.php')),
                                    'wellness' => include(resource_path('views/components/icons/wellness.php')),
                                    'website' => include(resource_path('views/components/icons/website.php')),
                                ];
                            @endphp

                            <div class="row row-cols-3 row-cols-sm-4 row-cols-md-5 g-2">
                                @foreach($groupedIcons as $group => $icons)
                                    @foreach($icons as $class => $label)
                                        <div class="col icon-item-card" data-group="{{ $group }}" data-label="{{ strtolower($label) }}" data-class="{{ $class }}">
                                            <button type="button" class="btn btn-light w-100 h-100 py-3 d-flex flex-column align-items-center justify-content-center border-0 icon-grid-item" data-icon="{{ $class }}" data-label="{{ $label }}" style="border-radius: 12px; transition: all 0.15s;">
                                                <i class="bi {{ $class }} text-success fs-3 mb-1"></i>
                                                <span class="text-muted text-center text-truncate w-100" style="font-size: 0.62rem; font-weight: 500;">{{ $label }}</span>
                                            </button>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
