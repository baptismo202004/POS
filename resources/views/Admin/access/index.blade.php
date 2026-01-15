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
    <title>Access Control - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                                <h6 class="mb-0 text-muted">ROLES</h6>
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
                                                                <a href="#" class="toggle-module"><i class="fas fa-chevron-right me-2"></i></a>
                                                                {{ $moduleData['label'] }}
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $rolePermissions = $permissions[$role->id] ?? [];
                                                                    $modulePermissions = $rolePermissions[$moduleKey] ?? [];
                                                                    $isAllowed = in_array('full', $modulePermissions);
                                                                @endphp
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" name="abilities[{{ $role->id }}][{{ $moduleKey }}][full]" value="1" {{ $isAllowed ? 'checked' : '' }}>
                                                                    <label class="form-check-label">Allow</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @foreach($moduleData['permissions'] as $permission)
                                                            @if($permission !== 'full')
                                                                <tr class="sub-permission d-none" data-parent-module="{{ $moduleKey }}">
                                                                    <td class="ps-4">{{ ucfirst($permission) }}</td>
                                                                    <td>
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" type="checkbox" name="abilities[{{ $role->id }}][{{ $moduleKey }}][{{ $permission }}]" value="1" {{ in_array($permission, $modulePermissions) ? 'checked' : '' }}>
                                                                            <label class="form-check-label">Allow</label>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endif
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
                    <!-- Maintenance notes: Keep this module list in sync with routes and sidebar.
                         Consider extracting modules to a config/rbac.php if growing further. -->
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
