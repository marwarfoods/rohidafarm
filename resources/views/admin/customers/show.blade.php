@extends('layouts.admin')

@section('admin_content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}" class="text-success text-decoration-none">Customers</a></li>
        <li class="breadcrumb-item active fw-semibold">{{ $customer->name }}</li>
    </ol>
</nav>

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold shadow"
             style="width:56px;height:56px;font-size:1.4rem;flex-shrink:0;">
            {{ strtoupper(substr($customer->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="h4 font-heading fw-bold m-0">{{ $customer->name }}</h1>
            <p class="text-muted mb-0" style="font-size:0.85rem;">
                <i class="bi bi-envelope me-1"></i>{{ $customer->email }}
                @if($customer->phone)
                    &nbsp;·&nbsp;<i class="bi bi-telephone me-1"></i>{{ $customer->phone }}
                @endif
            </p>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->user()->hasPermission('customers-edit'))
        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-outline-primary rounded-3 px-3">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        @endif
        @if(auth()->user()->hasPermission('customers-delete'))
        <button type="button" class="btn btn-outline-danger rounded-3 px-3"
                onclick="confirmDelete({{ $customer->id }}, '{{ addslashes($customer->name) }}')">
            <i class="bi bi-trash me-1"></i>Delete
        </button>
        @endif
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

{{-- Customer Info Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 rounded-4 p-3 text-center">
            <div class="text-muted mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Total Orders</div>
            <div class="fw-bold text-dark" style="font-size:1.8rem;">{{ $orders->total() }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 p-3 text-center">
            <div class="text-muted mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Total Spent</div>
            <div class="fw-bold text-success" style="font-size:1.4rem;">
                ₹{{ number_format($customer->orders->sum('total'), 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 p-3 text-center">
            <div class="text-muted mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Wallet Balance</div>
            <div class="fw-bold text-primary" style="font-size:1.4rem;">
                ₹{{ number_format($customer->wallet_balance ?? 0, 2) }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 rounded-4 p-3 text-center">
            <div class="text-muted mb-1" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Member Since</div>
            <div class="fw-semibold text-dark" style="font-size:1rem;">{{ $customer->created_at->format('d M Y') }}</div>
        </div>
    </div>
</div>

{{-- Customer Details + Addresses --}}
<div class="row g-4 mb-4">

    {{-- Personal Details --}}
    <div class="col-md-6">
        <div class="card border-0 rounded-4 h-100">
            <div class="card-header">
                <h6 class="fw-bold m-0"><i class="bi bi-person-circle me-2 text-success"></i>Personal Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0" style="font-size:0.88rem;">
                    <tr>
                        <td class="text-muted fw-semibold" style="width:40%;">Full Name</td>
                        <td class="fw-semibold">{{ $customer->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Email</td>
                        <td>{{ $customer->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Phone</td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Role</td>
                        <td><span class="badge bg-success-subtle text-success">{{ ucfirst($customer->role) }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Wallet Balance</td>
                        <td><strong>₹{{ number_format($customer->wallet_balance ?? 0, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold">Registered On</td>
                        <td>{{ $customer->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Saved Addresses --}}
    <div class="col-md-6">
        <div class="card border-0 rounded-4 h-100">
            <div class="card-header">
                <h6 class="fw-bold m-0"><i class="bi bi-geo-alt me-2 text-success"></i>Saved Addresses</h6>
            </div>
            <div class="card-body">
                @if($customer->addresses->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">No addresses saved.</p>
                @else
                    @foreach($customer->addresses as $addr)
                        <div class="mb-3 p-3 rounded-3 {{ $addr->is_default ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-light' }}">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong style="font-size:0.88rem;">{{ $addr->name }}</strong>
                                @if($addr->is_default)
                                    <span class="badge bg-success" style="font-size:0.65rem;">Default</span>
                                @endif
                            </div>
                            <p class="mb-0 text-muted" style="font-size:0.82rem;">
                                {{ $addr->address_line1 }}@if($addr->address_line2), {{ $addr->address_line2 }}@endif<br>
                                {{ $addr->city }}, {{ $addr->state }} - {{ $addr->postal_code }}<br>
                                {{ $addr->country }} &nbsp;·&nbsp; <i class="bi bi-telephone"></i> {{ $addr->phone }}
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Orders Table --}}
<div class="card border-0 rounded-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="fw-bold m-0"><i class="bi bi-cart-check me-2 text-success"></i>Order History</h6>
        <span class="text-muted" style="font-size:0.82rem;">{{ $orders->total() }} orders</span>
    </div>

    @if($orders->isEmpty())
        <div class="card-body text-center py-5">
            <i class="bi bi-cart text-muted" style="font-size:3rem;"></i>
            <p class="text-muted mt-3 mb-0">This customer has no orders yet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th class="text-center">View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark" style="font-size:0.88rem;">
                                    #{{ $order->order_number }}
                                </span>
                            </td>
                            <td style="font-size:0.82rem;color:#666;">
                                {{ $order->created_at->format('d M Y') }}<br>
                                <span style="font-size:0.75rem;">{{ $order->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    {{ $order->items->count() }} item(s)
                                </span>
                            </td>
                            <td class="fw-semibold">₹{{ number_format($order->total, 2) }}</td>
                            <td>
                                <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success-subtle text-success' : ($order->payment_status === 'failed' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                   class="btn btn-sm btn-outline-success rounded-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                </small>
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Customer?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-0 text-muted">
                    Are you sure you want to permanently delete
                    <strong id="deleteCustomerName" class="text-dark"></strong>?
                    This action <strong class="text-danger">cannot be undone</strong>.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-trash me-1"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('admin_scripts')
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteCustomerName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/customers/' + id + '/delete';
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
