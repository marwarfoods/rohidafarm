@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-tags text-success me-2"></i>Blog Categories</h1>
</div>

<div class="row g-4">
    <!-- Forms Column (Left) -->
    <div class="col-md-5">
        <!-- Create Category Form -->
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
            <h4 class="font-heading fw-bold text-success border-bottom pb-2 mb-3">Add New Category</h4>
            <form action="{{ route('admin.blog-categories.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Name *</label>
                    <input type="text" name="name" class="form-control bg-light border p-2" placeholder="e.g. Health Tips" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Description</label>
                    <textarea name="description" class="form-control bg-light border p-2" rows="3" placeholder="Brief details..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-premium w-100 py-2 rounded-3 text-uppercase font-heading" style="font-size: 0.8rem;">Save Category</button>
            </form>
        </div>
    </div>

    <!-- List Column (Right) -->
    <div class="col-md-7">
        <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
            <h4 class="font-heading fw-bold text-dark border-bottom pb-2 mb-3">All Categories</h4>
            
            <div class="list-group list-group-flush border-0">
                @forelse($categories as $cat)
                    <div class="list-group-item bg-white border-bottom py-3 px-0 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-tag-fill text-success fs-5 me-2"></i>
                            <div>
                                <span class="fw-bold text-dark fs-5 d-block font-heading" style="line-height: 1.2;">{{ $cat->name }}</span>
                                <span class="text-muted" style="font-size: 0.8rem;">{{ $cat->description }}</span>
                                <div class="mt-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                                        {{ $cat->blogs_count }} {{ Str::plural('Blog', $cat->blogs_count) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success border-0 edit-cat-btn" 
                                    data-id="{{ $cat->id }}" 
                                    data-name="{{ $cat->name }}" 
                                    data-description="{{ $cat->description }}" 
                                    title="Edit Category">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('admin.blog-categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Delete Category">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-5 bg-white rounded-4 border">
                        <i class="bi bi-tags display-4 text-muted mb-2 d-block"></i>
                        No categories exist yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ── Edit Category Modal ── -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="editCategoryModalLabel"><i class="bi bi-pencil-square text-success me-2"></i>Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Category Name *</label>
                        <input type="text" name="name" id="editCatName" class="form-control bg-light border p-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark" style="font-size: 0.85rem;">Description</label>
                        <textarea name="description" id="editCatDescription" class="form-control bg-light border p-2" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4 bg-success text-white border-0">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('admin_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Edit Category Modal Logic ──
    const editCatButtons = document.querySelectorAll('.edit-cat-btn');
    const editCatForm = document.getElementById('editCategoryForm');
    const editCatName = document.getElementById('editCatName');
    const editCatDescription = document.getElementById('editCatDescription');
    
    // Only try to create modal if element exists
    const modalEl = document.getElementById('editCategoryModal');
    if(modalEl) {
        const editCatModal = new bootstrap.Modal(modalEl);

        editCatButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const desc = this.getAttribute('data-description');

                editCatForm.action = `/admin/blog-categories/${id}/update`;
                editCatName.value = name;
                editCatDescription.value = desc || '';

                editCatModal.show();
            });
        });
    }
});
</script>
@endpush
@endsection
