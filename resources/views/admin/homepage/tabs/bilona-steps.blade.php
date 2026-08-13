<div class="row g-4">
    <!-- Add New Process Step -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold font-heading text-dark mb-3">Add Process Step</h5>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Add an image, heading, and a short 2-line description for this step.</p>

            <form action="{{ route('admin.homepage.bilona.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Step Image <span class="text-danger">*</span></label>
                    <input type="text" name="image_path" id="bilonaImageInput" class="form-control" placeholder="Choose image..." readonly required>
                    <div class="mt-2 border rounded-3 p-2 bg-light text-center" style="min-height: 100px;">
                        <img id="bilonaImagePreview" src="" class="img-fluid rounded-2 mx-auto" style="max-height: 120px; display: none; object-fit: contain;">
                        <small id="bilonaImagePlaceholder" class="text-muted d-block py-4">No image selected</small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Step Heading <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Milking with care" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 0.85rem;">Description (1-2 lines)</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Short description of this step..."></textarea>
                </div>
                <button type="submit" class="btn btn-warning w-100 py-2.5 rounded-3 fw-bold text-uppercase text-dark" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="bi bi-plus-circle me-1"></i> Add Step
                </button>
            </form>
        </div>
    </div>

    <!-- List Process Steps -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold font-heading text-dark border-bottom pb-3 mb-4">Manage Process Steps (Vedic Craftsmanship)</h5>
            <p class="text-muted mb-3" style="font-size: 0.8rem;"><i class="bi bi-grip-vertical me-1"></i>Drag the handle to reorder steps — the step number and its image/text move together.</p>
            <div class="table-responsive">
                <table class="table align-middle" id="bilona-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th style="width: 60px;">#</th>
                            <th>Image</th>
                            <th>Details</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bilonaSteps as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center cursor-move text-muted"><i class="bi bi-grip-vertical fs-5"></i></td>
                                <td class="fw-bold text-success">{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset($item->image_path) }}" class="rounded shadow-sm" style="height: 60px; width: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $item->title }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($item->description, 70) }}</small>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editBilonaModal{{ $item->id }}">Edit</button>
                                    <form action="{{ route('admin.homepage.bilona.delete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this process step?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($bilonaSteps->isEmpty())
                            <tr><td colspan="5" class="text-center text-muted py-4">No process steps found.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{--
    Edit modals live HERE, outside the <table>. <tbody> can only legally contain <tr>
    elements — when they were rendered inside the @foreach above (as siblings of each
    <tr>), the browser's HTML parser "foster-parented" that invalid content out of the
    table, which silently detached the image_path <input> from its <form> so it never
    made it into the submitted FormData (confirmed via debug logging: "not inside a form").
--}}
@foreach($bilonaSteps as $item)
    <div class="modal fade" id="editBilonaModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <form action="{{ route('admin.homepage.bilona.update', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title font-heading fw-bold">Edit Process Step</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Step Image <span class="text-danger">*</span></label>
                            <input type="text" name="image_path" id="bilonaImageInputEdit{{ $item->id }}" class="form-control" value="{{ $item->image_path }}" readonly required>
                            <div class="mt-2 text-center p-2 border rounded">
                                <img id="bilonaImagePreviewEdit{{ $item->id }}" src="{{ Str::startsWith($item->image_path, 'http') ? $item->image_path : asset($item->image_path) }}" style="max-height: 80px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Step Heading</label>
                            <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea>
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
