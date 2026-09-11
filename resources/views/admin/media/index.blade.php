@extends('layouts.admin')

@push('admin_styles')
    <link rel="stylesheet" href="{{ asset('admin/css/media-manager.css') }}?v={{ @filemtime(public_path('admin/css/media-manager.css')) }}">
@endpush

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom flex-wrap gap-3">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-images text-success me-2"></i>Media Manager</h1>
    
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('admin.media.compress-all') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-success px-4 py-2 rounded-pill font-heading" style="border-width: 2px; font-weight: 600;">
                <i class="bi bi-file-earmark-zip me-2"></i>Resize & Compress All
            </button>
        </form>
        <button type="button" class="btn btn-outline-primary px-4 py-2 rounded-pill font-heading" data-bs-toggle="modal" data-bs-target="#directUrlPageModal" style="border-width: 2px; font-weight: 600;">
            <i class="bi bi-globe2 me-1"></i>Direct URL
        </button>
        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill font-heading" id="btnImportUrl" style="border-width: 2px; font-weight: 600;">
            <i class="bi bi-link-45deg me-1"></i>Import from URL
        </button>
        <button type="button" class="btn btn-premium px-4 py-2 rounded-pill font-heading" onclick="document.getElementById('galleryPageFileInput').click()">
            <i class="bi bi-upload me-2"></i>Upload File
        </button>
    </div>
    <input type="file" id="galleryPageFileInput" class="d-none" multiple>
</div>

<!-- Storage Usage Info Bar -->
<div class="alert border-0 rounded-4 d-flex align-items-center justify-content-between py-3 px-4 mb-4" style="background-color: #f3f8f5; border-left: 5px solid #1a6b36 !important; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-success bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="bi bi-hdd-fill text-success fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold text-dark m-0" style="font-size: 0.95rem;">Storage Used</h6>
            <span class="text-muted" style="font-size: 0.8rem;">Total weight of images and videos</span>
        </div>
    </div>
    <div class="text-end">
        <span class="fs-4 fw-bold text-success font-heading">{{ $totalSizeFormatted }}</span>
    </div>
</div>

<!-- Upload Progress: real per-file progress list -->
<div class="card border-0 rounded-4 shadow-sm p-3 mb-4 d-none" id="galleryPageProgressContainer">
    <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;"><i class="bi bi-cloud-arrow-up-fill text-success me-2"></i>Uploading Files</h6>
    <div id="galleryPageProgressList" class="d-flex flex-column gap-2"></div>
</div>

<div class="media-layout-wrapper">
    <!-- Left Sidebar: Folder tree -->
    @include('admin.media.partials.folder-sidebar')

    <!-- Right Pane: File manager list -->
    <div class="media-content">
        <!-- Path and Breadcrumb Navigation -->
        <div class="d-flex align-items-center gap-2 mb-3 text-muted" style="font-size: 0.85rem;">
            <span>Media Library</span>
            <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i>
            <span class="text-dark fw-bold text-capitalize">{{ $folder === 'built-in' ? 'Built-in Images' : str_replace('-', ' ', $folder) }}</span>
        </div>

        <!-- Search and Toggles Toolbar -->
        <div class="media-toolbar">
            <div class="toolbar-left">
                <form action="{{ route('admin.media.index') }}" method="GET" class="search-wrapper m-0">
                    <input type="hidden" name="folder" value="{{ $folder }}">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search files..." value="{{ $search }}">
                </form>
            </div>
            
            <div class="toolbar-right">
                <div class="view-toggle-group">
                    <button type="button" class="btn-toggle active" id="btnGridView" title="Grid View">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </button>
                    <button type="button" class="btn-toggle" id="btnListView" title="List View">
                        <i class="bi bi-list-task"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Image Grid Layout -->
        <div id="mediaGridContainer" class="media-items-grid">
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
                @forelse($mediaItems as $media)
                    @php
                        $isBuiltin = $media->is_builtin ?? false;
                        $isExternal = Str::startsWith($media->file_path, ['http://', 'https://']) || (!empty($media->url) && filter_var($media->url, FILTER_VALIDATE_URL));
                    @endphp
                    <div class="col">
                        <div class="card media-card position-relative" id="mediaGridCard_{{ $media->id }}">
                             <!-- Select Checkbox overlay (built-in images cannot be selected for bulk delete) -->
                             @unless($isBuiltin)
                             <input type="checkbox" class="form-check-input media-select-checkbox" data-id="{{ $media->id }}" style="position: absolute !important; top: 10px !important; left: 10px !important; z-index: 25 !important; width: 18px !important; height: 18px !important; opacity: 1 !important; cursor: pointer !important; border: 2px solid #248443 !important; background-color: #ffffff;">
                             @endunless

                            @if($isExternal)
                                <span class="badge bg-primary position-absolute" style="top: 10px; right: 10px; z-index: 25; font-size: 0.65rem;" title="External Direct URL — Zero local storage">
                                    <i class="bi bi-globe2 me-1"></i>Direct URL
                                </span>
                            @elseif($isBuiltin)
                                <span class="badge bg-dark position-absolute" style="top: 10px; right: 10px; z-index: 25; font-size: 0.65rem;" title="Bundled app image — cannot be deleted">
                                    <i class="bi bi-box-seam me-1"></i>Built-in
                                </span>
                            @endif

                            <div class="thumbnail-wrapper">
                                @if($media->file_type === 'video')
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark text-white">
                                        <i class="bi bi-play-btn fs-1 text-danger"></i>
                                        <span class="badge bg-danger position-absolute bottom-0 start-0 m-2">Video</span>
                                    </div>
                                @else
                                    <img src="{{ asset($media->file_path) }}" class="w-100 h-100 object-fit-cover" loading="lazy">
                                @endif

                                <!-- Hover Actions overlay -->
                                <div class="card-hover-overlay d-flex flex-column align-items-center justify-content-center gap-2" style="background: rgba(43,43,43,0.7) !important;">
                                    <a href="{{ asset($media->file_path) }}" target="_blank" class="btn-preview px-3 py-1 bg-white text-dark rounded fw-bold text-decoration-none" style="font-size: 0.75rem;"><i class="bi bi-eye"></i> Preview</a>
                                    @if($isExternal)
                                        <button type="button" class="btn-preview px-3 py-1 bg-white text-dark rounded fw-bold border-0" style="font-size: 0.75rem;" onclick="navigator.clipboard.writeText('{{ $media->file_path }}'); this.innerHTML='<i class=\'bi bi-check-lg\'></i> Copied!';">
                                            <i class="bi bi-clipboard"></i> Copy URL
                                        </button>
                                    @elseif($media->file_type === 'image' && !$isBuiltin)
                                        <button type="button" class="btn-compress-single btn btn-warning px-3 py-1 rounded fw-bold text-dark border-0" data-id="{{ $media->id }}" style="font-size: 0.75rem;" onclick="compressSingleMedia(this, {{ $media->id }})">
                                            <i class="bi bi-file-zip-fill"></i> Compress
                                        </button>
                                    @endif
                                    @if($isBuiltin)
                                        <button type="button" class="btn-preview px-3 py-1 bg-white text-dark rounded fw-bold border-0" style="font-size: 0.75rem;" onclick="navigator.clipboard.writeText('{{ $media->full_url }}'); this.innerHTML='<i class=\'bi bi-check-lg\'></i> Copied!';">
                                            <i class="bi bi-clipboard"></i> Copy Link
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="card-details">
                                <h6 class="card-name" title="{{ $media->filename }}">{{ $media->filename }}</h6>
                                <div class="card-size" id="mediaCardSize_{{ $media->id }}">
                                    @if($isExternal)
                                        <span class="text-primary fw-semibold" style="font-size: 0.72rem;"><i class="bi bi-cloud-check me-1"></i>Direct (0 KB)</span>
                                    @else
                                        {{ number_format($media->file_size / 1024, 1) }} KB
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="bi bi-image display-4"></i>
                        <h6 class="mt-3">No media items match this category filter.</h6>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Image List Layout (Table view) -->
        <div id="mediaListContainer" class="media-items-list d-none bg-white">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="masterSelectCheckbox">
                            </th>
                            <th style="width: 80px;">Preview</th>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Created At</th>
                            <th style="width: 80px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mediaItems as $media)
                            @php $isBuiltin = $media->is_builtin ?? false; @endphp
                            <tr id="mediaListRow_{{ $media->id }}">
                                <td>
                                    @unless($isBuiltin)
                                        <input type="checkbox" class="form-check-input media-select-checkbox" data-id="{{ $media->id }}">
                                    @endunless
                                </td>
                                <td>
                                    <div class="rounded overflow-hidden" style="width: 50px; height: 50px;">
                                        @if($media->file_type === 'video')
                                            <div class="w-100 h-100 bg-dark text-white d-flex align-items-center justify-content-center">
                                                <i class="bi bi-play-fill text-danger"></i>
                                            </div>
                                        @else
                                            <img src="{{ asset($media->file_path) }}" class="w-100 h-100 object-fit-cover">
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-bold text-dark text-truncate" style="max-width: 250px;">
                                    {{ $media->filename }}
                                    @if($isBuiltin)
                                        <span class="badge bg-dark-subtle text-dark ms-1" style="font-size: 0.65rem;">Built-in</span>
                                    @endif
                                </td>
                                <td class="text-muted">
                                    {{ number_format($media->file_size / 1024, 1) }} KB
                                </td>
                                <td class="text-capitalize text-muted">
                                    {{ $media->file_type }}
                                </td>
                                <td class="text-muted">
                                    {{ $media->created_at->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    @if($isBuiltin)
                                        <button type="button" class="btn btn-sm btn-outline-secondary border-0" title="Copy link" onclick="navigator.clipboard.writeText('{{ $media->full_url }}'); this.innerHTML='<i class=\'bi bi-check-lg\'></i>';">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.media.delete', $media->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this media?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No media items available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $mediaItems->links() }}
        </div>
    </div>
</div>

<!-- Bulk Actions Floating Control Bar -->
<div class="media-bulk-actions" id="mediaBulkActionsBar">
    <div class="selected-count" id="selectedCountBadge">0 selected</div>
    <div class="action-buttons">
        <button type="button" class="btn-bulk-compress" id="btnBulkCompress">
            <i class="bi bi-file-zip me-1"></i>Compress Selected
        </button>
        <button type="button" class="btn-bulk-delete" id="btnBulkDelete">
            <i class="bi bi-trash-fill me-1"></i>Delete Selected
        </button>
        <button type="button" class="btn-bulk-cancel" id="btnCancelSelection">Cancel</button>
    </div>
</div>

@include('admin.media.partials.direct-url-modal')

@push('admin_scripts')
    <script src="{{ asset('admin/js/media-manager.js') }}?v={{ @filemtime(public_path('admin/js/media-manager.js')) }}"></script>
@endpush
@endsection
