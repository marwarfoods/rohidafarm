@extends('layouts.admin')

@section('admin_content')
<div class="mb-4 pb-2 border-bottom">
    <h1 class="display-6 font-heading fw-bold m-0"><i class="bi bi-shield-lock text-primary me-2"></i>Roles & Permissions</h1>
    <p class="text-muted mt-1 m-0">Define dynamic administrative roles, configure access rules, and assign them to your staff.</p>
</div>

<div class="row">
    <!-- Tabs Nav -->
    <div class="col-12 mb-4">
        <ul class="nav nav-pills gap-2" id="rolesTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-primary active px-4" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles-pane" type="button" role="tab" aria-controls="roles-pane" aria-selected="true">
                    <i class="bi bi-shield-check me-2"></i>Roles & Permissions
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="btn btn-outline-primary px-4" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff-pane" type="button" role="tab" aria-controls="staff-pane" aria-selected="false">
                    <i class="bi bi-people me-2"></i>Administrative Staff & Assignment
                </button>
            </li>
        </ul>
    </div>

    <!-- Tabs Content -->
    <div class="col-12">
        <div class="tab-content" id="rolesTabContent">
            <!-- Roles Pane -->
            <div class="tab-pane fade show active" id="roles-pane" role="tabpanel" aria-labelledby="roles-tab" tabindex="0">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h4 class="font-heading fw-bold text-dark m-0"><i class="bi bi-shield-check me-2 text-primary"></i>Roles Registry</h4>
                    <button class="btn btn-primary rounded-3 px-4 py-2 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bi bi-plus-circle me-2"></i>Create New Role
                    </button>
                </div>

                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-0 rounded-4 shadow-sm h-100 bg-white p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-bold text-dark m-0">{{ $role->display_name }}</h5>
                                            <code class="small text-muted">{{ $role->name }}</code>
                                        </div>
                                        @if($role->name === 'admin')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5 rounded-3">Full Access</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-3">Staff Role</span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-4" style="min-height: 40px; font-size: 0.84rem;">{{ $role->description ?? 'No description provided.' }}</p>
                                    
                                    @if($role->name !== 'admin')
                                        <div class="mb-4">
                                            <span class="fw-semibold text-muted small d-block mb-2 text-uppercase" style="letter-spacing: .5px; font-size: 0.72rem;">Permissions Assigned:</span>
                                            <div class="d-flex flex-wrap gap-1.5 overflow-y-auto pr-1" style="max-height: 120px;">
                                                @php
                                                    $allowedModules = [];
                                                    foreach($role->permissions as $p) {
                                                        $parts = explode('-', $p->name);
                                                        $mod = $parts[0];
                                                        $act = $parts[1] ?? 'view';
                                                        if(!isset($allowedModules[$mod])) {
                                                            $allowedModules[$mod] = [];
                                                        }
                                                        $allowedModules[$mod][] = $act;
                                                    }
                                                @endphp
                                                @forelse($allowedModules as $mod => $actions)
                                                    <span class="badge bg-light text-dark border border-light-subtle px-2.5 py-1.5 rounded-3 text-start" style="font-size:0.75rem; font-weight: 500;">
                                                        <span class="text-success fw-bold me-1">#</span>{{ ucfirst($mod) }}: 
                                                        <span class="text-muted" style="font-size: 0.7rem;">{{ implode(', ', array_map('ucfirst', $actions)) }}</span>
                                                    </span>
                                                @empty
                                                    <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>No permissions assigned.</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-auto">
                                    @if($role->name !== 'admin')
                                        <button class="btn btn-sm btn-outline-primary px-3 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $role->id }}">
                                            <i class="bi bi-pencil-square me-1"></i>Configure Permissions
                                        </button>
                                        <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-3 py-2 rounded-3">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small py-1.5 fw-semibold"><i class="bi bi-lock-fill me-1 text-danger"></i>System Protected</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Edit Role Modal (Granular CRUD Checklist Grid) -->
                        @if($role->name !== 'admin')
                        <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content rounded-4 p-4 border-0">
                                    <div class="modal-header border-0 p-0 mb-3">
                                        <h5 class="modal-title font-heading fw-bold text-dark"><i class="bi bi-shield-lock text-primary me-2"></i>Configure Permissions: {{ $role->display_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-0">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold text-muted small">Role Code (No spaces)</label>
                                                    <input type="text" name="name" value="{{ $role->name }}" class="form-control shadow-none rounded-3" required readonly>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-semibold text-muted small">Display Name</label>
                                                    <input type="text" name="display_name" value="{{ $role->display_name }}" class="form-control shadow-none rounded-3" required>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label fw-semibold text-muted small">Description</label>
                                                    <textarea name="description" class="form-control shadow-none rounded-3" rows="1">{{ $role->description }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold text-dark small d-block mb-3 border-bottom pb-2">Actions Permission matrix (CRUD Access)</label>
                                                <div class="table-responsive rounded-3 border bg-light p-3" style="max-height: 400px; overflow-y: auto;">
                                                    <table class="table table-sm align-middle table-borderless m-0 bg-transparent">
                                                        <thead>
                                                            <tr class="text-muted small border-bottom">
                                                                <th style="width: 40%">Module</th>
                                                                <th class="text-center" style="width: 15%">View</th>
                                                                <th class="text-center" style="width: 15%">Add</th>
                                                                <th class="text-center" style="width: 15%">Edit</th>
                                                                <th class="text-center" style="width: 15%">Delete</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $modulesList = [
                                                                    'orders' => 'Orders',
                                                                    'customers' => 'Customers',
                                                                    'products' => 'Products',
                                                                    'categories' => 'Categories',
                                                                    'coupons' => 'Coupons',
                                                                    'stock' => 'Stock',
                                                                    'reviews' => 'Reviews',
                                                                    'video-reviews' => 'Video Reviews',
                                                                    'blogs' => 'Blogs',
                                                                    'pages' => 'Pages',
                                                                    'media' => 'Media & Layout',
                                                                    'settings' => 'Settings',
                                                                    'logs' => 'Audit Logs',
                                                                    'roles' => 'Roles & Staff'
                                                                ];
                                                            @endphp
                                                            @foreach($modulesList as $modKey => $modName)
                                                                <tr class="border-bottom-dashed">
                                                                    <td class="small fw-bold text-dark">{{ $modName }}</td>
                                                                    @foreach(['view', 'create', 'edit', 'delete'] as $actKey)
                                                                        @php
                                                                            $permName = "{$modKey}-{$actKey}";
                                                                            $permObj = $permissions->firstWhere('name', $permName);
                                                                        @endphp
                                                                        <td class="text-center">
                                                                            @if($permObj)
                                                                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permObj->id }}" id="perm_edit_{{ $role->id }}_{{ $permObj->id }}"
                                                                                       {{ $role->permissions->contains('id', $permObj->id) ? 'checked' : '' }}>
                                                                            @else
                                                                                <span class="text-muted">—</span>
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 p-0 mt-4 d-flex gap-2">
                                            <button type="button" class="btn btn-outline-secondary w-50 rounded-3" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary w-50 rounded-3">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Create New Role Modal -->
            <div class="modal fade" id="createRoleModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content rounded-4 p-4 border-0">
                        <div class="modal-header border-0 p-0 mb-3">
                            <h5 class="modal-title font-heading fw-bold text-dark"><i class="bi bg-plus-circle me-2 text-primary"></i>Create New Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.roles.store') }}" method="POST">
                            @csrf
                            <div class="modal-body p-0">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-muted small">Role Code (English, no spaces)</label>
                                        <input type="text" name="name" class="form-control shadow-none rounded-3" placeholder="e.g. editor, manager" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold text-muted small">Display Name</label>
                                        <input type="text" name="display_name" class="form-control shadow-none rounded-3" placeholder="e.g. Store Editor" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold text-muted small">Description</label>
                                        <textarea name="description" class="form-control shadow-none rounded-3" rows="1" placeholder="Brief details about what this role handles"></textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark small d-block mb-3 border-bottom pb-2">Actions Permission matrix (CRUD Access)</label>
                                    <div class="table-responsive rounded-3 border bg-light p-3" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm align-middle table-borderless m-0 bg-transparent">
                                            <thead>
                                                <tr class="text-muted small border-bottom">
                                                    <th style="width: 40%">Module</th>
                                                    <th class="text-center" style="width: 15%">View</th>
                                                    <th class="text-center" style="width: 15%">Add</th>
                                                    <th class="text-center" style="width: 15%">Edit</th>
                                                    <th class="text-center" style="width: 15%">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $modulesList = [
                                                        'orders' => 'Orders',
                                                        'customers' => 'Customers',
                                                        'products' => 'Products',
                                                        'categories' => 'Categories',
                                                        'coupons' => 'Coupons',
                                                        'stock' => 'Stock',
                                                        'reviews' => 'Reviews',
                                                        'video-reviews' => 'Video Reviews',
                                                        'blogs' => 'Blogs',
                                                        'pages' => 'Pages',
                                                        'media' => 'Media & Layout',
                                                        'settings' => 'Settings',
                                                        'logs' => 'Audit Logs',
                                                        'roles' => 'Roles & Staff'
                                                    ];
                                                @endphp
                                                @foreach($modulesList as $modKey => $modName)
                                                    <tr class="border-bottom-dashed">
                                                        <td class="small fw-bold text-dark">{{ $modName }}</td>
                                                        @foreach(['view', 'create', 'edit', 'delete'] as $actKey)
                                                            @php
                                                                $permName = "{$modKey}-{$actKey}";
                                                                $permObj = $permissions->firstWhere('name', $permName);
                                                            @endphp
                                                            <td class="text-center">
                                                                @if($permObj)
                                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permObj->id }}" id="perm_create_{{ $permObj->id }}">
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-0 mt-4 d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary w-50 rounded-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary w-50 rounded-3">Create Role</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Staff Pane -->
            <div class="tab-pane fade" id="staff-pane" role="tabpanel" aria-labelledby="staff-tab" tabindex="0">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 rounded-4 shadow-sm bg-white p-4">
                            <h4 class="font-heading fw-bold text-dark mb-3"><i class="bi bi-person-plus me-2 text-primary"></i>Assign Staff Role</h4>
                            
                            <form action="{{ route('admin.roles.index') }}" method="GET" class="mb-4">
                                <label class="form-label fw-semibold text-muted small">Search User to Assign Roles</label>
                                <div class="d-flex gap-2">
                                    <input type="text" name="search_user" class="form-control shadow-none" placeholder="Search by name/email..." value="{{ request('search_user') }}">
                                    <button type="submit" class="btn btn-secondary"><i class="bi bi-search"></i></button>
                                </div>
                            </form>

                            @if(request('search_user') && count($users) > 0)
                                <div class="list-group border rounded-3 overflow-hidden shadow-sm mb-4">
                                    @foreach($users as $user)
                                        <div class="list-group-item bg-white">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="fw-bold text-dark d-block" style="font-size:0.85rem;">{{ $user->name }}</span>
                                                    <span class="text-muted small">{{ $user->email }}</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#assignCollapse{{ $user->id }}">
                                                    Assign
                                                </button>
                                            </div>

                                            <div class="collapse mt-3" id="assignCollapse{{ $user->id }}">
                                                <form action="{{ route('admin.roles.assign') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold text-muted small">Choose Roles</label>
                                                        @foreach($roles as $role)
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="user_role_{{ $user->id }}_{{ $role->id }}"
                                                                       {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}>
                                                                <label class="form-check-label text-dark fw-medium small" for="user_role_{{ $user->id }}_{{ $role->id }}">
                                                                    {{ $role->display_name }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-2">Save Assignment</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(request('search_user'))
                                <p class="text-muted small">No users found matching query.</p>
                            @endif

                            <p class="text-muted small">To assign a role, search for any registered customer/staff user above and select their roles.</p>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card border-0 rounded-4 shadow-sm bg-white p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4 gap-3">
                                <h4 class="font-heading fw-bold text-dark m-0"><i class="bi bi-people me-2 text-primary"></i>Administrative Staff Registry</h4>
                                
                                <form action="{{ route('admin.roles.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="tab" value="staff">
                                    <input type="text" name="search_staff" class="form-control form-control-sm border shadow-none" placeholder="Search staff..." value="{{ request('search_staff') }}">
                                    <button type="submit" class="btn btn-sm btn-secondary"><i class="bi bi-search"></i></button>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Staff Member</th>
                                            <th>Roles Assigned</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($staff as $member)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $member->name }}</span>
                                                    <span class="d-block text-muted small">{{ $member->email }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($member->roles as $role)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-2 d-flex align-items-center gap-1" style="font-size:0.75rem;">
                                                                {{ $role->display_name }}
                                                                <form action="{{ route('admin.roles.remove-user-role', [$member->id, $role->id]) }}" method="POST" onsubmit="return confirm('Remove this role from staff member?');" class="m-0 d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="border-0 bg-transparent text-success p-0" style="line-height:1;"><i class="bi bi-x-circle-fill"></i></button>
                                                                </form>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary px-3 py-1 rounded-3" data-bs-toggle="collapse" data-bs-target="#editStaffCollapse{{ $member->id }}">
                                                        Modify
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Inline Modify Collapse -->
                                            <tr class="collapse" id="editStaffCollapse{{ $member->id }}">
                                                <td colspan="3" class="bg-light p-4">
                                                    <form action="{{ route('admin.roles.assign') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-dark small">Modify Assigned Roles for {{ $member->name }}</label>
                                                            <div class="d-flex gap-4">
                                                                @foreach($roles as $role)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="staff_role_{{ $member->id }}_{{ $role->id }}"
                                                                               {{ $member->roles->contains('id', $role->id) ? 'checked' : '' }}>
                                                                        <label class="form-check-label text-dark fw-medium small" for="staff_role_{{ $member->id }}_{{ $role->id }}">
                                                                            {{ $role->display_name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-toggle="collapse" data-bs-target="#editStaffCollapse{{ $member->id }}">Cancel</button>
                                                            <button type="submit" class="btn btn-sm btn-primary px-4">Update Assignment</button>
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">No staff members configured.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            @if(method_exists($staff, 'links') && $staff->hasPages())
                                <div class="mt-4">
                                    {{ $staff->appends(['search_staff' => request('search_staff')])->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle persistent active tab
        const urlParams = new URLSearchParams(window.location.search);
        const searchUser = urlParams.get('search_user');
        const tabParam = urlParams.get('tab');
        
        if (searchUser || tabParam === 'staff') {
            const staffTab = document.getElementById('staff-tab');
            if (staffTab) {
                bootstrap.Tab.getInstance(staffTab)?.show() || new bootstrap.Tab(staffTab).show();
            }
        }
    });
</script>
@endpush
