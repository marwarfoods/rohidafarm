@extends('layouts.admin')

@section('admin_content')

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h1 class="display-6 font-heading fw-bold m-0">
            <i class="bi bi-people-fill text-success me-2"></i>Customers
        </h1>
        <p class="text-muted mb-0 mt-1" style="font-size:0.88rem;">
            Manage all registered customers, view their orders and details.
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.customers.trash') }}" class="btn btn-outline-danger rounded-3 px-3 py-2 fw-semibold position-relative" title="View Trashed / Deactivated Customers">
            <i class="bi bi-trash3 me-1"></i> Trash Bin
            @php
                $trashedCount = \App\Models\User::onlyTrashed()->where('role', 'customer')->count();
            @endphp
            @if($trashedCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm">
                    {{ $trashedCount }}
                </span>
            @endif
        </a>
        @if(auth()->user()->hasPermission('customers-create'))
        <a href="{{ route('admin.customers.create') }}" class="btn btn-success rounded-3 px-4 py-2 fw-semibold">
            <i class="bi bi-person-plus-fill me-2"></i>Add Customer
        </a>
        @endif
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 rounded-4 p-3 bg-white shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.3rem;">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Total Customers</div>
                    <div class="fw-bold fs-4">{{ $customers->total() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 rounded-4 p-3 bg-white shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.3rem;">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Showing This Page</div>
                    <div class="fw-bold fs-4">{{ $customers->count() }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 rounded-4 p-3 bg-white shadow-sm">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.3rem;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:0.75rem;text-transform:uppercase;letter-spacing:.5px;font-weight:600;">Page</div>
                    <div class="fw-bold fs-4">{{ $customers->currentPage() }} / {{ $customers->lastPage() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toolbar --}}
<div class="card border-0 rounded-4 mb-4 shadow-sm">
    <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                class="form-control rounded-3"
                placeholder="Search by name, email or phone..."
                style="max-width:300px;"
            >
            <button type="submit" class="btn btn-success rounded-3 px-3">
                <i class="bi bi-search"></i>
            </button>
            @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary rounded-3 px-3">
                    Clear
                </a>
            @endif
        </form>

        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="m-0 d-flex align-items-center gap-2">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <label class="text-muted fw-semibold" style="font-size:0.85rem; white-space:nowrap;">Per Page:</label>
                <select name="per_page" class="form-select form-select-sm rounded-3" style="width:80px;" onchange="this.form.submit()">
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </form>
            
            <div class="dropdown">
                <button class="btn btn-primary rounded-3 px-3 fw-semibold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><button type="button" class="dropdown-item" onclick="submitExport('pdf')"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Export to PDF</button></li>
                    <li><button type="button" class="dropdown-item" onclick="submitExport('csv')"><i class="bi bi-file-earmark-excel text-success me-2"></i>Export to Excel (CSV)</button></li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Customers Table --}}
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="fw-bold m-0">
            <i class="bi bi-table me-2 text-success"></i>All Customers
            @if(request('search'))
                <span class="badge bg-success-subtle text-success ms-2">Filtered</span>
            @endif
        </h6>
        <span class="text-muted" style="font-size:0.82rem;">{{ $customers->total() }} total</span>
    </div>

    @if($customers->isEmpty())
        <div class="card-body text-center py-5">
            <i class="bi bi-people text-muted" style="font-size:3rem;"></i>
            <p class="text-muted mt-3 mb-0">
                {{ request('search') ? 'No customers matched your search.' : 'No customers found.' }}
            </p>
            @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary mt-3 rounded-3">Clear Search</a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCustomers">
                        </th>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Joined</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $i => $customer)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input customer-checkbox" value="{{ $customer->id }}">
                            </td>
                            <td class="text-muted" style="font-size:0.82rem;">
                                {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold"
                                         style="width:38px;height:38px;font-size:0.9rem;flex-shrink:0;">
                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold d-flex align-items-center gap-1.5 flex-wrap" style="font-size:0.9rem;">
                                            <span>{{ $customer->name }}</span>
                                            @if($customer->roles->count() > 0)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-1.5 py-0.5 rounded-2" style="font-size:0.68rem; font-weight: 500;">{{ $customer->roles->first()->display_name }}</span>
                                            @endif
                                        </div>
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
                                {{ $customer->created_at->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    {{-- View --}}
                                    @if(auth()->user()->hasPermission('customers-view'))
                                    <a href="{{ route('admin.customers.show', $customer->id) }}"
                                       class="btn btn-sm btn-outline-success rounded-2"
                                       title="View Customer">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                    {{-- Edit --}}
                                    @if(auth()->user()->hasPermission('customers-edit'))
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-2"
                                       title="Edit Customer">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                    {{-- Delete --}}
                                    @if(auth()->user()->hasPermission('customers-delete'))
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger rounded-2"
                                            title="Delete Customer"
                                            onclick="confirmDelete({{ $customer->id }}, '{{ addslashes($customer->name) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </small>
                {{ $customers->links() }}
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
                    <i class="bi bi-trash3-fill me-2"></i>Move Customer to Trash?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-0 text-muted">
                    Are you sure you want to deactivate and move
                    <strong id="deleteCustomerName" class="text-dark"></strong> to the <strong>Trash Bin</strong>?
                    <br><small class="text-secondary mt-1 d-block">The customer will no longer be able to log in. You can restore them or permanently purge them at any time from the Trash Bin.</small>
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3 px-4">
                        <i class="bi bi-trash3 me-1"></i>Move to Trash
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

document.getElementById('selectAllCustomers')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

function submitExport(type) {
    const selectedIds = Array.from(document.querySelectorAll('.customer-checkbox:checked')).map(cb => cb.value);
    
    // Create a form dynamically
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = type === 'pdf' ? '{{ route("admin.customers.export.pdf") }}' : '{{ route("admin.customers.export.csv") }}';
    
    // CSRF token
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    // Search param
    const search = document.createElement('input');
    search.type = 'hidden';
    search.name = 'search';
    search.value = '{{ request("search") }}';
    form.appendChild(search);
    
    // Selected IDs
    if (selectedIds.length > 0) {
        const ids = document.createElement('input');
        ids.type = 'hidden';
        ids.name = 'selected_ids';
        ids.value = selectedIds.join(',');
        form.appendChild(ids);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush
