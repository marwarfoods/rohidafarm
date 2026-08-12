@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-cart text-success me-2"></i>Order Management</h1>
    
    @if(auth()->user()->hasPermission('orders-delete'))
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.orders.trash') }}" class="btn btn-outline-danger d-inline-flex align-items-center gap-2">
            <i class="bi bi-trash3-fill"></i> Trash Bin
            @if($trashedCount > 0)
                <span class="badge bg-danger rounded-pill">{{ $trashedCount }}</span>
            @endif
        </a>
    </div>
    @endif
</div>

<form id="bulkDeleteForm" action="{{ route('admin.orders.bulk-delete') }}" method="POST">
    @csrf
    <x-admin-table 
        :headers="['<input type=\'checkbox\' id=\'selectAll\'>', 'Order Number', 'Customer Name', 'Total Amount', 'Payment Mode', 'Payment Status', 'Order Status', 'Delhivery', 'Actions']"
        :items="$orders" 
        title="Customer Sales Orders" 
        description="Track customer purchases, update fulfillment status, and review payments.">
        
        <x-slot name="actions">
            <!-- Filter status select -->
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->hasPermission('orders-edit'))
                <div class="d-none align-items-center gap-2" id="bulkStatusGroup">
                    <select class="form-select form-select-sm border shadow-none bg-light" id="bulkStatusSelect" style="font-size: 0.8rem; width: 150px;">
                        <option value="">Bulk Action...</option>
                        <option value="pending">Set: Pending</option>
                        <option value="processing">Set: Processing</option>
                        <option value="shipped">Set: Shipped</option>
                        <option value="delivered">Set: Delivered</option>
                        <option value="cancelled">Set: Cancelled</option>
                        <option value="refunded">Set: Refunded</option>
                    </select>
                    <button type="button" class="btn btn-success btn-sm" id="bulkStatusApplyBtn">
                        <i class="bi bi-check2-circle"></i> Apply
                    </button>

                    {{-- Real, item-by-item progress — advances only as each order's own status-update request finishes --}}
                    <div class="d-none align-items-center gap-2" id="bulkStatusProgressWrap" style="min-width: 180px;">
                        <div class="progress flex-grow-1" style="height: 8px; width: 120px;">
                            <div class="progress-bar bg-success" id="bulkStatusProgressBar" style="width: 0%;"></div>
                        </div>
                        <span class="text-muted fw-semibold" id="bulkStatusProgressText" style="font-size: 0.78rem; white-space: nowrap;">0 / 0</span>
                    </div>
                </div>
                @endif
                @if(auth()->user()->hasPermission('orders-delete'))
                <button type="submit" class="btn btn-danger btn-sm d-none me-2" id="bulkDeleteBtn">
                    <i class="bi bi-trash"></i> Delete Selected
                </button>
                @endif
                <label for="statusFilter" class="text-muted fw-semibold" style="font-size: 0.85rem; white-space: nowrap;">Filter Status:</label>
                <select class="form-select border shadow-none bg-light p-1.5" id="statusFilter" style="font-size: 0.85rem; width: 160px;">
                    <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Orders</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
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
                <td class="px-4 py-3">
                    <span class="badge {{ $ord->payment_status === 'paid' ? 'bg-success text-white' : 'bg-warning text-dark' }}">{{ ucfirst($ord->payment_status) }}</span>
                </td>
                <td class="px-4 py-3">
                    <span class="badge {{ $ord->status_badge_class }}">{{ ucfirst($ord->status) }}</span>
                </td>
                <td class="px-4 py-3">
                    @if($ord->shipment)
                        <div class="d-flex flex-column gap-1">
                            <span class="badge bg-success" style="font-size: 0.7rem;"><i class="bi bi-check-circle me-1"></i>Synced</span>
                            @if($ord->tracking_number)
                                <span class="text-muted" style="font-size: 0.72rem;">AWB: {{ $ord->tracking_number }}</span>
                            @endif
                        </div>
                    @elseif(!in_array($ord->status, ['cancelled', 'delivered']))
                        @if(auth()->user()->hasPermission('orders-edit'))
                        <button type="button" form="syncForm{{ $ord->id }}" class="btn btn-sm text-white px-2 py-1 rounded-2 fw-bold"
                                style="font-size: 0.72rem; background: linear-gradient(135deg,#7c3aed,#4f46e5);" 
                                title="Push to Delhivery">
                            <i class="bi bi-send-fill me-1"></i>Sync
                        </button>
                        @else
                        <span class="text-muted" style="font-size: 0.75rem;">Not Synced</span>
                        @endif
                    @else
                        <span class="text-muted" style="font-size: 0.75rem;">—</span>
                    @endif
                </td>
                <td class="text-end px-4 py-3">
                    <div class="d-flex gap-2 justify-content-end">
                        @if(auth()->user()->hasPermission('orders-view'))
                        <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn btn-sm btn-premium-outline px-3 py-1 rounded-3">Manage</a>
                        @endif
                        @if(auth()->user()->hasPermission('orders-delete'))
                        <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-1 rounded-3" form="deleteForm{{ $ord->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">No orders matching criteria are available.</td>
            </tr>
        @endforelse
    </x-admin-table>
</form>

@foreach($orders as $ord)
    @if(!in_array($ord->status, ['cancelled', 'delivered']) && !$ord->shipment)
    <form id="syncForm{{ $ord->id }}" action="{{ route('admin.orders.sync-delhivery', $ord->id) }}" method="POST" class="d-none" onsubmit="this.querySelector('button').disabled=true;">
        @csrf
    </form>
    @endif
    <form id="deleteForm{{ $ord->id }}" action="{{ route('admin.orders.delete', $ord->id) }}" method="POST" class="d-none" onsubmit="return confirm('Move this order to trash?');">
        @csrf
        @method('DELETE')
    </form>
@endforeach
@endsection

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filter = document.getElementById('statusFilter');
        if (filter) {
            filter.addEventListener('change', function () {
                window.location.href = `/admin/orders?status=${this.value}`;
            });
        }

        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkStatusGroup = document.getElementById('bulkStatusGroup');

        function toggleBulkBtn() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            if (bulkDeleteBtn) bulkDeleteBtn.classList.toggle('d-none', !anyChecked);
            if (bulkStatusGroup) {
                bulkStatusGroup.classList.toggle('d-none', !anyChecked);
                bulkStatusGroup.classList.toggle('d-flex', anyChecked);
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                toggleBulkBtn();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkBtn);
        });

        // Bulk Status Apply — reuses the same checked row checkboxes as bulk delete.
        // Sends one AJAX request per selected order (sequentially) against the same
        // single-order status route used by the "Update Order Status" form, so the
        // progress bar reflects REAL completed/total counts, not a simulated fill.
        const bulkStatusSelect = document.getElementById('bulkStatusSelect');
        const bulkStatusApplyBtn = document.getElementById('bulkStatusApplyBtn');
        const bulkProgressWrap = document.getElementById('bulkStatusProgressWrap');
        const bulkProgressBar = document.getElementById('bulkStatusProgressBar');
        const bulkProgressText = document.getElementById('bulkStatusProgressText');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        if (bulkStatusApplyBtn) {
            bulkStatusApplyBtn.addEventListener('click', function () {
                const status = bulkStatusSelect.value;
                if (!status) {
                    alert('Please choose a status to apply.');
                    return;
                }

                const checkedIds = Array.from(rowCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
                if (checkedIds.length === 0) {
                    alert('Please select at least one order.');
                    return;
                }

                if (!confirm(`Set status to "${status}" for ${checkedIds.length} selected order(s)?`)) return;

                bulkStatusApplyBtn.disabled = true;
                bulkStatusSelect.disabled = true;
                bulkProgressWrap.classList.remove('d-none');
                bulkProgressWrap.classList.add('d-flex');
                bulkProgressBar.style.width = '0%';
                bulkProgressText.textContent = `0 / ${checkedIds.length}`;

                let completed = 0;
                let failed = 0;

                function updateNext(index) {
                    if (index >= checkedIds.length) {
                        bulkStatusApplyBtn.disabled = false;
                        bulkStatusSelect.disabled = false;
                        if (failed > 0) {
                            alert(`${completed} order(s) updated, ${failed} failed.`);
                        }
                        if (completed > 0) {
                            window.location.reload();
                        }
                        return;
                    }

                    const orderId = checkedIds[index];

                    fetch(`/admin/orders/${orderId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    })
                    .then(res => { if (!res.ok) throw new Error('failed'); completed++; })
                    .catch(() => { failed++; })
                    .finally(() => {
                        // Real progress — only moves once THIS order's own request has actually finished.
                        const done = completed + failed;
                        const percent = Math.round((done / checkedIds.length) * 100);
                        bulkProgressBar.style.width = percent + '%';
                        bulkProgressText.textContent = `${done} / ${checkedIds.length}`;
                        updateNext(index + 1);
                    });
                }

                updateNext(0);
            });
        }
    });
</script>
@endpush
