@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-cart text-success me-2"></i>Order Management</h1>
    
    @if(auth()->user()->hasPermission('orders-delete'))
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.orders.trash') }}" class="btn btn-outline-danger">
            <i class="bi bi-trash3-fill me-1"></i> Trash Bin
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

        function toggleBulkBtn() {
            const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            if(anyChecked) {
                bulkDeleteBtn.classList.remove('d-none');
            } else {
                bulkDeleteBtn.classList.add('d-none');
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
    });
</script>
@endpush
