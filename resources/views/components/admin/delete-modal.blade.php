{{--
  Admin Delete Confirmation Modal (Reusable)
  Usage: <x-admin.delete-modal />
  Then on delete buttons:
    data-bs-toggle="modal"
    data-bs-target="#adminDeleteModal"
    data-delete-url="{{ route('admin.xxx.delete', $item->id) }}"
    data-delete-name="{{ $item->name }}"
--}}
<div class="modal fade" id="adminDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:40px;height:40px;background:#fef2f2;flex-shrink:0;">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    </div>
                    <h5 class="modal-title fw-bold m-0" style="font-size:1rem;">Confirm Delete</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 pb-3">
                <p class="text-muted mb-0" style="font-size:0.875rem;">
                    Are you sure you want to permanently delete
                    <strong id="adminDeleteName" class="text-dark"></strong>?
                    <br><span class="text-danger fw-semibold">This action cannot be undone.</span>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">
                    Cancel
                </button>
                <form id="adminDeleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3 px-4 no-spinner">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
