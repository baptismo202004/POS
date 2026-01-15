@php
    /**
     * Variables:
     * - $modules: array key => label
     * - $roles: collection of UserType
     * - $existing: nested collection [user_type_id][module] => RolePermission
     */
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Control - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .list-group-item.active {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        .list-group-item.active a {
            color: white;
        }
        .roles-table .role-row.table-active {
            background-color: #e9f5ff;
            font-weight: 600;
        }
        .form-check-label {
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .roles-table tr[data-parent-id] {
            /* Hide child roles by default */
        }
        .toggle-role i, .toggle-module i {
            transition: transform 0.2s ease-in-out;
        }
        .toggle-role.expanded i, .toggle-module.expanded i {
            transform: rotate(90deg);
        }
    </style>
</head>
<body class="bg-white">
    <div class="d-flex min-vh-100">
        @include('layouts.AdminSidebar')
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-3">
                    <div class="col-12">
                        <h3 class="mb-0">User Management</h3>
                        <div class="text-muted">Configure per-role access for each module.</div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-transparent border-0 pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 text-muted">ROLES</h6>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">Add User Type</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover roles-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            @include('admin.access.partials.role_row', ['role' => $role, 'level' => 0])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-transparent border-0 pt-3">
                                <h6 class="mb-0 text-muted" id="permission-title">PERMISSIONS</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.access.store') }}">
                                    @csrf
                                    @foreach($roles as $role)
                                        <div class="permission-group" id="role-{{ $role->id }}-permissions" style="display: none;">
                                            <table class="table permissions-table">
                                                <thead>
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($modules as $moduleKey => $moduleData)
                                                        <tr data-module="{{ $moduleKey }}">
                                                            <td>
                                                                @php
                                                                    $subPermissions = array_filter($moduleData['permissions'], fn($p) => $p !== 'full');
                                                                @endphp
                                                                @if(count($subPermissions) > 0)
                                                                    <a href="#" class="toggle-module"><i class="fas fa-chevron-right me-2"></i></a>
                                                                @else
                                                                    <span class="me-2" style="width: 1em; display: inline-block;"></span>
                                                                @endif
                                                                {{ $moduleData['label'] }}
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        @php
                                                            $rolePermissions = $permissions[$role->id] ?? [];
                                                            $modulePermissions = $rolePermissions[$moduleKey] ?? [];
                                                        @endphp
                                                        @foreach($moduleData['permissions'] as $permission)
                                                            <tr class="sub-permission d-none" data-parent-module="{{ $moduleKey }}">
                                                                <td class="ps-4">{{ $permission === 'full' ? 'Full Access' : ucfirst($permission) }}</td>
                                                                <td>
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox" name="abilities[{{ $role->id }}][{{ $moduleKey }}][{{ $permission }}]" value="1" {{ in_array($permission, $modulePermissions) ? 'checked' : '' }}>
                                                                        <label class="form-check-label">Allow</label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 small text-muted">
                    
                </div>
            </div>
        </main>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoleForm" method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="roleDescription" class="form-label">Description</label>
                            <input type="text" class="form-control" id="roleDescription" name="description">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addRoleForm" class="btn btn-primary">Save Role</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editRoleForm" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="editRoleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="editRoleName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRoleDescription" class="form-label">Description</label>
                            <input type="text" class="form-control" id="editRoleDescription" name="description">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="editRoleForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
                document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('addRoleForm').addEventListener('submit', function(e) {
                e.preventDefault();

                let form = e.target;
                let formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        let errorMessages = Object.values(data.errors).join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessages,
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                });
            });

            document.getElementById('editRoleForm').addEventListener('submit', function(e) {
                e.preventDefault();

                let form = e.target;
                let formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { 
                            throw new Error(text || 'Server responded with an error'); 
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        let errorMessages = Object.values(data.errors).join('<br>');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessages,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! Please check the browser console for more details.',
                    });
                });
            });

            // Edit Role Modal Logic
            const editRoleModal = document.getElementById('editRoleModal');
            editRoleModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const roleId = button.dataset.roleId;
                const roleName = button.dataset.roleName;
                const roleDescription = button.dataset.roleDescription;

                const form = editRoleModal.querySelector('#editRoleForm');
                const nameInput = editRoleModal.querySelector('#editRoleName');
                const descriptionInput = editRoleModal.querySelector('#editRoleDescription');

                const baseUrl = '/superadmin/admin/admin/roles';
                form.action = `${baseUrl}/${roleId}`;
                nameInput.value = roleName;
                descriptionInput.value = roleDescription;
            });

            // Role selection logic
            const roleRows = document.querySelectorAll('.role-row');
            const permissionGroups = document.querySelectorAll('.permission-group');
            const permissionTitle = document.getElementById('permission-title');

            roleRows.forEach(row => {
                row.addEventListener('click', function (e) {
                    if (e.target.closest('.toggle-role')) {
                        return; // Don't select row if toggling
                    }
                    roleRows.forEach(r => r.classList.remove('table-active'));
                    this.classList.add('table-active');
                    const roleId = this.dataset.roleId;
                    const roleName = this.querySelector('td').textContent.trim();
                    permissionTitle.textContent = `PERMISSION OF ${roleName.toUpperCase()}`;
                    permissionGroups.forEach(group => {
                        group.style.display = group.id === `role-${roleId}-permissions` ? 'block' : 'none';
                    });
                });
            });

            if (roleRows.length > 0) {
                roleRows[0].click();
            }

            // Role hierarchy toggle logic
            document.querySelectorAll('.toggle-role').forEach(toggle => {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.toggle('expanded');
                    const roleId = this.closest('.role-row').dataset.roleId;
                    document.querySelectorAll(`.role-row[data-parent-id='${roleId}']`).forEach(childRow => {
                        childRow.classList.toggle('d-none');
                    });
                });
            });

            // Permission module toggle logic
            document.querySelectorAll('.toggle-module').forEach(toggle => {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.classList.toggle('expanded');
                    const moduleKey = this.closest('tr').dataset.module;
                    document.querySelectorAll(`.sub-permission[data-parent-module='${moduleKey}']`).forEach(subRow => {
                        subRow.classList.toggle('d-none');
                    });
                });
            });
        });
    </script>
</body>
</html>
