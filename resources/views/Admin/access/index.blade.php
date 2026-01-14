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

                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Access Matrix</span>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-outline-primary">Create Account</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.access.store') }}">
                            @csrf

                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Role</th>
@foreach($modules as $key => $label)
                                            <th>{{ $label }}</th>
@endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
@foreach($roles as $role)
                                        <tr>
                                            <td class="fw-semibold">{{ $role->name }}</td>
@foreach($modules as $key => $label)
@php
    $perm = optional(optional($existing[$role->id] ?? collect())[$key] ?? collect()->first());
    $current = $perm->ability ?? 'view';
@endphp
                                            <td>
                                                <select name="abilities[{{ $role->id }}][{{ $key }}]" class="form-select form-select-sm">
                                                    <option value="none" {{ $current==='none' ? 'selected' : '' }}>None</option>
                                                    <option value="view" {{ $current==='view' ? 'selected' : '' }}>View</option>
                                                    <option value="edit" {{ $current==='edit' ? 'selected' : '' }}>Edit</option>
                                                    <option value="full" {{ $current==='full' ? 'selected' : '' }}>Full</option>
                                                </select>
                                                <!-- Notes: 
                                                    none  => hidden/blocked
                                                    view  => can view list/details
                                                    edit  => can create/update where applicable
                                                    full  => includes destructive actions like delete
                                                -->
                                            </td>
@endforeach
                                        </tr>
@endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
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
</body>
</html>
