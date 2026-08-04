@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-trash3 text-danger me-2"></i>Deleted Orders</h1>
    
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<form id="bulkActionForm" method="POST">
    @csrf
    <input type="hidden" name="action_type" id="actionType" value="">
    <x-admin-table 
        :headers="['<input type=\'checkbox\' id=\'selectAll\'>', 'Order Number', 'Customer Name', 'Total Amount', 'Payment Mode', 'Deleted At', 'Actions']" 
        :items="$orders" 
        title="Trashed Orders" 
        description="View softly deleted orders. You can restore them or delete them permanently.">
        
        <x-slot name="actions">
            <!-- Search -->
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-success btn-sm d-none me-2 bulk-action-btn" data-action="{{ route('admin.orders.bulk-restore') }}">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                </button>
                <button type="button" class="btn btn-danger btn-sm d-none me-2 bulk-action-btn" data-action="{{ route('admin.orders.bulk-force-delete') }}">
                    <i class="bi bi-x-circle"></i> Permanent Delete
                </button>
                <form action="{{ route('admin.orders.trash') }}" method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control form-control-sm border shadow-none" placeholder="Search order/name..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-secondary ms-1"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </x-slot>

        @forelse($orders as $ord)
            <tr style="font-size: 0.85rem;">
                <td class="px-4 py-3">
                    <input type="checkbox" name="ids[]" value="{{ $ord->id }}" class="row-checkbox">
                </td>
                <td class="fw-bold px-4 py-3">#{{ $ord->order_number }}</td>
                <td class="px-4 py-3">{{ $ord->user->name ?? 'Guest/Deleted' }}</td>
                <td class="fw-bold text-success px-4 py-3">₹{{ number_format($ord->total, 2) }}</td>
                <td class="text-uppercase px-4 py-3">{{ $ord->payment_method }}</td>
                <td class="px-4 py-3">{{ $ord->deleted_at->format('d M, Y h:i A') }}</td>
                <td class="text-end px-4 py-3">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-sm btn-outline-success px-3 py-1 rounded-3" form="restoreForm{{ $ord->id }}" title="Restore">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1 rounded-3" form="forceDeleteForm{{ $ord->id }}" title="Permanent Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">No deleted orders found.</td>
            </tr>
        @endforelse
    </x-admin-table>
</form>

@foreach($orders as $ord)
    <form id="restoreForm{{ $ord->id }}" action="{{ route('admin.orders.restore', $ord->id) }}" method="POST" class="d-none">
        @csrf
    </form>
    <form id="forceDeleteForm{{ $ord->id }}" action="{{ route('admin.orders.force-delete', $ord->id) }}" method="POST" class="d-none" onsubmit="return confirm('WARNING: This will permanently delete this order and cannot be undone. Proceed?');">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkActionBtns = document.querySelectorAll('.bulk-action-btn');
        const bulkForm = document.getElementById('bulkActionForm');

        function toggleBulkBtns() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            bulkActionBtns.forEach(btn => {
                if(anyChecked) {
                    btn.classList.remove('d-none');
                } else {
                    btn.classList.add('d-none');
                }
            });
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkBtns();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkBtns);
        });

        bulkActionBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const actionUrl = this.getAttribute('data-action');
                if (actionUrl.includes('force-delete')) {
                    if (!confirm("Are you sure you want to permanently delete these orders? This action cannot be undone.")) {
                        return;
                    }
                }
                bulkForm.action = actionUrl;
                bulkForm.submit();
            });
        });
    });
</script>
@endpush
