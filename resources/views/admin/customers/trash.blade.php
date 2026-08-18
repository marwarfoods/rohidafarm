@extends('layouts.admin')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom gap-2">
    <div>
        <h1 class="display-6 font-heading fw-bold m-0 text-danger d-flex align-items-center">
            <i class="bi bi-trash3-fill me-2"></i>Trashed Customers
        </h1>
        <p class="text-muted mb-0 mt-1" style="font-size:0.88rem;">
            Review soft-deleted customer accounts. You can restore accounts or permanently delete them with cascade options.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back to Active Customers
        </a>
    </div>
</div>

{{-- Search & Filter Card --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.customers.trash') }}" class="row g-2 align-items-center">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text"
                           name="search"
                           class="form-control border-start-0 ps-0"
                           placeholder="Search trashed customers by name, email, or phone..."
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / page</option>
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-danger w-100 rounded-3 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(request('search') || request('per_page'))
                    <a href="{{ route('admin.customers.trash') }}" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Trashed Customers Table Card --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    @if($customers->isEmpty())
        <div class="text-center py-5">
            <div class="text-muted mb-3" style="font-size:3rem;">
                <i class="bi bi-trash-check text-success"></i>
            </div>
            <h5 class="fw-bold text-dark">Trash is Empty</h5>
            <p class="text-muted mb-3" style="font-size:0.9rem;">
                No trashed or deactivated customers found.
            </p>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-success rounded-3 px-4">
                View Active Customers
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.6px;color:#666;">
                        <th class="ps-4">Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Orders</th>
                        <th>Deleted On</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center fw-bold"
                                         style="width:40px;height:40px;font-size:1rem;flex-shrink:0;">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $customer->name }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">ID: {{ $customer->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:0.88rem;">{{ $customer->email }}</td>
                            <td style="font-size:0.88rem;">{{ $customer->phone ?? '—' }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info fw-semibold">
                                    {{ $customer->orders_count }} orders
                                </span>
                            </td>
                            <td style="font-size:0.82rem;color:#666;">
                                <div class="fw-semibold text-danger">{{ $customer->deleted_at->format('d M Y') }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $customer->deleted_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-2 justify-content-center">
                                    {{-- Restore Form --}}
                                    <form method="POST" action="{{ route('admin.customers.restore', $customer->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-3 px-3 fw-semibold" title="Restore Customer">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore
                                        </button>
                                    </form>

                                    {{-- Permanent Delete Trigger --}}
                                    <button type="button"
                                            class="btn btn-sm btn-danger rounded-3 px-3 fw-semibold"
                                            onclick="openForceDeleteModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', {{ $customer->orders_count }})">
                                        <i class="bi bi-trash3-fill me-1"></i> Permanent Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-3 border-top">
                {{ $customers->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Permanent Delete Modal with Cascade / Detach Options --}}
<div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-2"></i> Permanent Delete Confirmation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="forceDeleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body py-3">
                    <p class="text-dark mb-3">
                        You are about to permanently delete customer <strong id="forceCustomerName" class="text-danger"></strong>.
                        This action <strong>cannot be undone</strong>.
                    </p>

                    <div class="alert alert-warning border-0 rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.85rem;">
                        <i class="bi bi-info-circle-fill text-warning fs-5"></i>
                        <span>This customer has <strong id="forceCustomerOrdersCount">0</strong> total orders recorded in the system.</span>
                    </div>

                    <label class="form-label fw-bold text-dark mb-2" style="font-size:0.9rem;">Choose Deletion Method:</label>

                    {{-- Option 1: Preserve History (Detach) --}}
                    <div class="card border rounded-3 p-3 mb-2 option-card border-success bg-light" id="cardOptionPreserve" style="cursor:pointer;" onclick="selectOption('preserve')">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="wipe_mode" id="wipeModePreserve" value="preserve" checked>
                            <label class="form-check-label fw-bold text-dark" for="wipeModePreserve">
                                <i class="bi bi-shield-check text-success me-1"></i> Preserve Sales & Financial History (Recommended)
                            </label>
                        </div>
                        <small class="text-muted ms-4 mt-1 d-block" style="font-size:0.82rem;">
                            Customer account is deleted. All orders, invoices, and payment records remain intact in your sales analytics and reports (customer ID set to unlinked).
                        </small>
                    </div>

                    {{-- Option 2: Complete Wipeout (Cascade) --}}
                    <div class="card border rounded-3 p-3 option-card" id="cardOptionCascade" style="cursor:pointer;" onclick="selectOption('cascade')">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="wipe_mode" id="wipeModeCascade" value="cascade">
                            <label class="form-check-label fw-bold text-danger" for="wipeModeCascade">
                                <i class="bi bi-radioactive text-danger me-1"></i> Complete Wipeout / Cascade Delete
                            </label>
                        </div>
                        <small class="text-muted ms-4 mt-1 d-block" style="font-size:0.82rem;">
                            Wipes the customer and completely deletes all their associated orders, items, addresses, reviews, and wheel entries from database.
                        </small>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4 fw-bold">
                        <i class="bi bi-trash3-fill me-1"></i> Permanently Purge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('admin_scripts')
<script>
function openForceDeleteModal(id, name, ordersCount) {
    document.getElementById('forceCustomerName').textContent = name;
    document.getElementById('forceCustomerOrdersCount').textContent = ordersCount;
    document.getElementById('forceDeleteForm').action = '/admin/customers/' + id + '/force-delete';
    
    // Default to preserve option
    selectOption('preserve');

    new bootstrap.Modal(document.getElementById('forceDeleteModal')).show();
}

function selectOption(mode) {
    const radioPreserve = document.getElementById('wipeModePreserve');
    const radioCascade = document.getElementById('wipeModeCascade');
    const cardPreserve = document.getElementById('cardOptionPreserve');
    const cardCascade = document.getElementById('cardOptionCascade');

    if (mode === 'preserve') {
        radioPreserve.checked = true;
        cardPreserve.classList.add('border-success', 'bg-light');
        cardCascade.classList.remove('border-danger', 'bg-light');
    } else {
        radioCascade.checked = true;
        cardCascade.classList.add('border-danger', 'bg-light');
        cardPreserve.classList.remove('border-success', 'bg-light');
    }
}
</script>
@endpush
