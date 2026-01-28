<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Permissions - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --card-hover-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 30px;
            min-height: calc(100vh - 40px);
        }

        .avatar-sm {
            width: 35px;
            height: 35px;
            font-size: 14px;
            font-weight: bold;
        }

        .border-left-primary {
            border-left: 4px solid var(--primary-color) !important;
        }

        .border-left-success {
            border-left: 4px solid var(--success-color) !important;
        }

        .border-left-info {
            border-left: 4px solid #17a2b8 !important;
        }

        .border-left-warning {
            border-left: 4px solid var(--warning-color) !important;
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-hover-shadow);
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            border-bottom: 1px solid #e2e8f0;
            border-radius: 15px 15px 0 0;
        }

        .modal-footer {
            border-top: 1px solid #e2e8f0;
            border-radius: 0 0 15px 15px;
        }
    </style>
</head>
<body>
    <div class="d-flex min-vh-100">
        @include('layouts.AdminSidebar')
        <main class="flex-fill p-4">
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Access Permissions</h1>
            <p class="text-muted">Manage user roles and permissions</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus me-2"></i>Add New User
            </button>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="fas fa-shield-alt me-2"></i>Add Role
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Roles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $roles->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Permissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($permissions) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Active Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->where('status', 'active')->count() ?: $users->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Users Management
                    </h6>
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control form-control-sm" id="userSearch" placeholder="Search users...">
                        <button class="btn btn-sm btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
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
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
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
                                                <span class="badge bg-primary">{{ $user->userType->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $user->status ?? 'Active' }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="editUser({{ $user->id }})" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No users found</td>
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
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt me-2"></i>Roles & Permissions
                    </h6>
                    <select class="form-select form-select-sm" id="roleSelect" style="width: 200px;">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="card-body">
                    <div id="permissionsContainer" style="display: none;">
                        <div class="mb-3">
                            <h6 class="text-muted">Module Permissions</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>View</th>
                                        <th>Create</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="permissionsTableBody">
                                    <!-- Permissions will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="noRoleSelected" class="text-center text-muted py-5">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <p>Select a role to view and manage permissions</p>
                    </div>
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
                <form id="addUserForm">
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
    
    // Fetch actual permissions from the database
    fetch(`/superadmin/admin/access/permissions/${roleId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Permissions response:', data);
            if (data.success) {
                console.log('Loaded permissions:', data.permissions);
                displayPermissions(roleId, data.permissions);
            } else {
                console.error('Failed to load permissions:', data.message);
                // Fallback to default permissions
                displayPermissions(roleId, getDefaultPermissions());
            }
        })
        .catch(error => {
            console.error('Error loading permissions:', error);
            // Fallback to default permissions
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
        { module: 'aging_reports', view: true, create: false, edit: false, delete: false },
        { module: 'reports', view: true, create: false, edit: false, delete: false },
        { module: 'roles_permissions', view: true, create: false, edit: true, delete: false },
        { module: 'user_management', view: true, create: true, edit: true, delete: true },
        { module: 'access_logs', view: true, create: false, edit: false, delete: false },
        { module: 'settings', view: true, create: false, edit: true, delete: false },
        { module: 'branch', view: true, create: true, edit: true, delete: true },
        { module: 'brands', view: true, create: true, edit: true, delete: true },
        { module: 'unit_types', view: true, create: true, edit: true, delete: true },
        { module: 'tax', view: true, create: false, edit: false, delete: false },
        { module: 'receipt_templates', view: true, create: false, edit: false, delete: false }
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
        'aging_reports': 'Aging Reports',
        'reports': 'Reports',
        'roles_permissions': 'Roles & Permissions',
        'user_management': 'User Management',
        'access_logs': 'Access Logs',
        'settings': 'Settings',
        'branch': 'Branch',
        'brands': 'Brands',
        'unit_types': 'Unit Types',
        'tax': 'Tax',
        'receipt_templates': 'Receipt Templates'
    };
    
    permissions.forEach(perm => {
        const row = `
            <tr>
                <td><strong>${moduleLabels[perm.module] || perm.module}</strong></td>
                <td><input type="checkbox" class="form-check-input" ${perm.view ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'view', this.checked)"></td>
                <td><input type="checkbox" class="form-check-input" ${perm.create ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'create', this.checked)"></td>
                <td><input type="checkbox" class="form-check-input" ${perm.edit ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'edit', this.checked)"></td>
                <td><input type="checkbox" class="form-check-input" ${perm.delete ? 'checked' : ''} onchange="updatePermission(${roleId}, '${perm.module}', 'delete', this.checked)"></td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

function updatePermission(roleId, module, action, checked) {
    console.log(`Updating permission: Role ${roleId}, Module ${module}, Action ${action}, Checked ${checked}`);
    
    // Store the checkbox element to use it later
    const checkbox = event.target;
    
    fetch(`/superadmin/admin/access/permissions/update`, {
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
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Permission Updated',
                text: data.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
            
            // Reload permissions to reflect the change immediately
            setTimeout(() => {
                loadPermissions(roleId);
            }, 500);
        } else {
            console.error('Failed to update permission:', data.message);
            // Revert the checkbox state
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
        // Revert the checkbox state
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
    
    fetch('{{ route("superadmin.admin.users.store") }}', {
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
    
    fetch('{{ route("superadmin.admin.roles.store") }}', {
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
    // Fetch user data and populate edit modal
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
            // Create edit user modal dynamically
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
            
            // Remove existing modal if any
            const existingModal = document.getElementById('editUserModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body and show
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

    fetch(`/admin/access/users/${userId}`, {
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
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/access/users/${userId}`, {
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

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
