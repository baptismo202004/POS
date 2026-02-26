@extends('layouts.app')
@section('title', 'Access Permissions')

@include('layouts.theme-base')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h1>Access Permissions</h1>
            <p class="text-muted mb-0">Manage user roles and permissions</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-2"></i>Add New User
            </button>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-primary">Back to Expenses</a>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="fas fa-shield-alt me-2"></i>Add Role
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="grow">
                            <div class="stats-label">Total Users</div>
                            <div class="stats-value">{{ $users->count() }}</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="grow">
                            <div class="stats-label">Total Roles</div>
                            <div class="stats-value">{{ $roles->count() }}</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="grow">
                            <div class="stats-label">Permissions</div>
                            <div class="stats-value">{{ count($permissions) }}</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-key"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="grow">
                            <div class="stats-label">Active Today</div>
                            <div class="stats-value">{{ $users->where('status', 'active')->count() ?: $users->count() }}</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Users Section -->
        <div class="col-lg-6">
            <div class="card content-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>
                            <i class="fas fa-users me-2"></i>Users Management
                        </h6>
                        <div class="search-input-group" style="width: 250px; position: relative;">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control form-control-sm" id="userSearch" placeholder="Search users...">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table" id="usersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->userType)
                                                <span class="badge badge-primary">{{ $user->userType->name }}</span>
                                            @else
                                                <span class="badge badge-secondary">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-success">{{ $user->status ?? 'Active' }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-primary btn-sm" onclick="editUser({{ $user->id }})" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" onclick="deleteUser({{ $user->id }})" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="fas fa-users"></i>
                                                <p>No users found</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles & Permissions Section -->
        <div class="col-lg-6">
            <div class="card content-card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>
                            <i class="fas fa-shield-alt me-2"></i>Roles & Permissions
                        </h6>
                        <select class="form-select form-select-sm" id="roleSelect" style="width: 200px;">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div id="permissionsContainer" style="display: none;">
                        <div class="mb-3">
                            <h6 class="text-muted fw-bold">Module Permissions</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm permissions-table">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th class="text-center">View</th>
                                        <th class="text-center">Create</th>
                                        <th class="text-center">Edit</th>
                                        <th class="text-center">Delete</th>
                                        <th class="text-center">Allow All</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionsTableBody">
                                    <!-- Permissions will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="noRoleSelected" class="empty-state">
                        <i class="fas fa-shield-alt"></i>
                        <p>Select a role to view and manage permissions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Save User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoleForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Role Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveRole()">Save Role</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// User search functionality
document.getElementById('userSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Role selection and permissions loading
document.getElementById('roleSelect').addEventListener('change', function() {
    const roleId = this.value;
    
    if (roleId) {
        loadPermissions(roleId);
        document.getElementById('permissionsContainer').style.display = 'block';
        document.getElementById('noRoleSelected').style.display = 'none';
    } else {
        document.getElementById('permissionsContainer').style.display = 'none';
        document.getElementById('noRoleSelected').style.display = 'block';
    }
});

function loadPermissions(roleId) {
    console.log(`Loading permissions for role ID: ${roleId}`);
    
    fetch(`/admin/access/permissions/${roleId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Permissions response:', data);
            if (data.success) {
                console.log('Loaded permissions:', data.permissions);
                displayPermissions(roleId, data.permissions);
            } else {
                console.error('Failed to load permissions:', data.message);
                displayPermissions(roleId, getDefaultPermissions());
            }
        })
        .catch(error => {
            console.error('Error loading permissions:', error);
            displayPermissions(roleId, getDefaultPermissions());
        });
}

function getDefaultPermissions() {
    return [
        { module: 'dashboard', view: true, create: false, edit: false, delete: false },
        { module: 'products', view: true, create: true, edit: true, delete: true },
        { module: 'product_category', view: true, create: true, edit: true, delete: true },
        { module: 'purchases', view: true, create: true, edit: true, delete: false },
        { module: 'inventory', view: true, create: false, edit: true, delete: false },
        { module: 'stock_in', view: true, create: true, edit: false, delete: false },
        { module: 'stock_transfer', view: true, create: true, edit: true, delete: false },
        { module: 'sales', view: true, create: false, edit: false, delete: false },
        { module: 'sales_report', view: true, create: false, edit: false, delete: false },
        { module: 'refund_return', view: true, create: false, edit: false, delete: false },
        { module: 'credit', view: true, create: false, edit: false, delete: false },
        { module: 'expenses', view: true, create: true, edit: true, delete: true },
        { module: 'customer', view: true, create: true, edit: true, delete: false },
        { module: 'credit_limits', view: true, create: true, edit: true, delete: false },
        { module: 'payment_history', view: true, create: false, edit: false, delete: false },
        { module: 'reports', view: true, create: false, edit: false, delete: false },
        { module: 'roles_permissions', view: true, create: false, edit: true, delete: false },
        { module: 'user_management', view: true, create: true, edit: true, delete: true },
        { module: 'access_logs', view: true, create: false, edit: false, delete: false },
        { module: 'settings', view: true, create: false, edit: true, delete: false },
        { module: 'branch', view: true, create: true, edit: true, delete: true },
        { module: 'brands', view: true, create: true, edit: true, delete: true },
        { module: 'unit_types', view: true, create: true, edit: true, delete: true }
    ];
}

function displayPermissions(roleId, permissions) {
    const tbody = document.getElementById('permissionsTableBody');
    tbody.innerHTML = '';
    
    const moduleLabels = {
        'dashboard': 'Dashboard',
        'products': 'Products',
        'product_category': 'Product Category',
        'purchases': 'Purchase',
        'inventory': 'Inventory',
        'stock_in': 'Stock In',
        'stock_transfer': 'Stock Transfer',
        'sales': 'Sales',
        'sales_report': 'Sales Report',
        'refund_return': 'Refund/Return',
        'credit': 'Credit',
        'expenses': 'Expenses',
        'customer': 'Customer',
        'credit_limits': 'Credit Limits',
        'payment_history': 'Payment History',
        'reports': 'Reports',
        'roles_permissions': 'Roles & Permissions',
        'user_management': 'User Management',
        'access_logs': 'Access Logs',
        'settings': 'Settings',
        'branch': 'Branch',
        'brands': 'Brands',
        'unit_types': 'Unit Types'
    };
    
    permissions.forEach(perm => {
        const row = `
            <tr>
                <td class="module-name">${moduleLabels[perm.module] || perm.module}</td>
                <td class="text-center">
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" class="form-check-input" ${perm.view ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'view', this.checked)">
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" class="form-check-input" ${perm.create ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'create', this.checked)">
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" class="form-check-input" ${perm.edit ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'edit', this.checked)">
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" class="form-check-input" ${perm.delete ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'delete', this.checked)">
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" class="form-check-input" onchange="toggleAllPermissions(${roleId}, '${perm.module}', this.checked)">
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function toggleAllPermissions(roleId, module, checked) {
    console.log(`Toggling all permissions for Role ${roleId}, Module ${module}, Checked ${checked}`);
    
    const row = event.target.closest('tr');
    const checkboxes = row.querySelectorAll('input[type="checkbox"]:not([onchange*="toggleAllPermissions"])');
    
    // Define the permission actions in order
    const actions = ['view', 'create', 'edit', 'delete'];
    let updateCount = 0;
    const totalUpdates = actions.length;
    
    // Update all permission checkboxes
    checkboxes.forEach((checkbox, index) => {
        checkbox.checked = checked;
        const action = actions[index];
        
        // Call a silent version of updatePermission that doesn't show toast
        updatePermissionSilent(roleId, module, action, checked, () => {
            updateCount++;
            // Show success message only after all updates are complete
            if (updateCount === totalUpdates) {
                Swal.fire({
                    icon: 'success',
                    title: 'Permissions Updated',
                    text: `All permissions for ${module} have been ${checked ? 'granted' : 'revoked'}`,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    });
}

function updatePermissionSilent(roleId, module, action, checked, callback) {
    console.log(`Updating permission: Role ${roleId}, Module ${module}, Action ${action}, Checked ${checked}`);
    
<<<<<<< HEAD
    fetch('{{ route("admin.access.permissions.update") }}', {
=======
    fetch(`/admin/access/permissions/update`, {
>>>>>>> e35526bd90d5d38b2a18503553947162576ff8b1
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            role_id: roleId,
            module: module,
            action: action,
            checked: checked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Permission updated successfully:', data.message);
            if (callback) callback();
        } else {
            console.error('Failed to update permission:', data.message);
            // If any update fails, show an error message
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    })
    .catch(error => {
        console.error('Error updating permission:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update permission',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
}

function updatePermission(roleId, module, action, checked) {
    console.log(`Updating permission: Role ${roleId}, Module ${module}, Action ${action}, Checked ${checked}`);
    
    const checkbox = event.target;
    
<<<<<<< HEAD
    fetch('{{ route("admin.access.permissions.update") }}', {
=======
    fetch(`/admin/access/permissions/update`, {
>>>>>>> e35526bd90d5d38b2a18503553947162576ff8b1
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            role_id: roleId,
            module: module,
            action: action,
            checked: checked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Permission updated successfully:', data.message);
            Swal.fire({
                icon: 'success',
                title: 'Permission Updated',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            
            setTimeout(() => {
                loadPermissions(roleId);
            }, 500);
        } else {
            console.error('Failed to update permission:', data.message);
            checkbox.checked = !checked;
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    })
    .catch(error => {
        console.error('Error updating permission:', error);
        checkbox.checked = !checked;
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update permission',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
}

function saveUser() {
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.users.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Something went wrong'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Network error occurred'
        });
    });
}

function saveRole() {
    const form = document.getElementById('addRoleForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.roles.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Something went wrong'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Network error occurred'
        });
    });
}

function editUser(userId) {
    fetch(`/admin/access/users/${userId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const modalHtml = `
                <div class="modal fade" id="editUserModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editUserForm">
                                    <input type="hidden" name="user_id" value="${data.user.id}">
                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" value="${data.user.name}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="${data.user.email}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select class="form-select" name="role_id" required>
                                            <option value="">Select Role</option>
                                            ${getRoleOptions(data.user.user_type_id)}
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="updateUser()">Update User</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            const existingModal = document.getElementById('editUserModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to fetch user data'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to fetch user data: ' + error.message
        });
    });
}

function updateUser() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const userId = formData.get('user_id');
    formData.append('_method', 'PUT');

    fetch(`/superadmin/admin/access/users/${userId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: data.message
            }).then(() => {
                location.reload();
            });
        } else {
            let errorText = data.message || 'Something went wrong';
            if (data.errors) {
                errorText = Object.values(data.errors).flat().join('<br>');
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: errorText
            });
        }
    })
    .catch(error => {
        let errorText = 'An unexpected error occurred.';
        if (error.message) {
            errorText = error.message;
        }
        if (error.errors) {
            errorText = Object.values(error.errors).flat().join('<br>');
        }
        Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            html: errorText
        });
    });
}

function getRoleOptions(selectedRoleId) {
    const roles = @json($roles);
    return roles.map(role => 
        `<option value="${role.id}" ${role.id == selectedRoleId ? 'selected' : ''}>${role.name}</option>`
    ).join('');
}

function deleteUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E91E63',
        cancelButtonColor: '#2196F3',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/superadmin/admin/access/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete user'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network error occurred'
                });
            });
        }
    });
}
</script>
@endpush