<div class="row g-4">
    <!-- Add New Native Ingredient -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold font-heading text-dark mb-3">Add Native Ingredient</h5>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Select image and optional title/link.</p>

            <form action="{{ route('admin.homepage.native.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Image <span class="text-danger">*</span></label>
                    <input type="text" name="image_path" id="nativeImageInput" class="form-control" placeholder="Choose image..." readonly required>
                    <div class="mt-2 border rounded-3 p-2 bg-light text-center" style="min-height: 100px;">
                        <img id="nativeImagePreview" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; display: none; object-fit: contain;">
                        <small id="nativeImagePlaceholder" class="text-muted d-block py-4">No image selected</small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Alt Title (Optional)</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Native Geographies">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Link URL (Optional)</label>
                    <input type="text" name="link" class="form-control" placeholder="e.g. /shop">
                </div>
                <button type="submit" class="btn btn-warning w-100 py-2.5 rounded-3 fw-bold text-uppercase text-dark" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="bi bi-plus-circle me-1"></i> Add Ingredient
                </button>
            </form>
        </div>
    </div>

    <!-- List Native Ingredients -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold font-heading text-dark border-bottom pb-3 mb-4">Manage Native Ingredients</h5>
            <div class="table-responsive">
                <table class="table align-middle" id="native-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Image</th>
                            <th>Details</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nativeIngredients as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center cursor-move text-muted"><i class="bi bi-grip-vertical fs-5"></i></td>
                                <td>
                                    <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset($item->image_path) }}" class="rounded shadow-sm" style="height: 60px; object-fit: contain;">
                                </td>
                                <td>
                                    <strong>{{ $item->title ?? 'No title' }}</strong><br>
                                    <small class="text-muted">{{ $item->link ?? 'No link' }}</small>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editNativeModal{{ $item->id }}">Edit</button>
                                    <form action="{{ route('admin.homepage.native.delete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this native ingredient?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($nativeIngredients->isEmpty())
                            <tr><td colspan="4" class="text-center text-muted py-4">No native ingredients found.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{--
    Edit modals live HERE, outside the <table>. <tbody> can only legally contain <tr>
    elements — rendering them inside the @foreach above caused the browser to
    foster-parent that invalid content out of the table, which silently detached the
    image_path <input> from its <form> (same bug confirmed on the Bilona steps tab).
--}}
@foreach($nativeIngredients as $item)
    <div class="modal fade" id="editNativeModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="{{ route('admin.homepage.native.update', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title font-heading fw-bold">Edit Native Ingredient</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Image <span class="text-danger">*</span></label>
                            <input type="text" name="image_path" id="nativeImageInputEdit{{ $item->id }}" class="form-control" value="{{ $item->image_path }}" readonly required>
                            <div class="mt-2 text-center p-2 border rounded">
                                <img id="nativeImagePreviewEdit{{ $item->id }}" src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset($item->image_path) }}" style="max-height: 80px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Alt Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $item->title }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Link URL</label>
                            <input type="text" name="link" class="form-control" value="{{ $item->link }}">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="submit" class="btn btn-warning px-4 py-2 rounded-3 w-100 fw-bold text-dark">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
