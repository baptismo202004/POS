@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Branches</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#branchModal" onclick="openBranchModal()">
                        <i class="fas fa-plus"></i> Add Branch
                    </button>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branches as $branch)
                                <tr>
                                    <td>{{ $branch->id }}</td>
                                    <td>{{ $branch->branch_name }}</td>
                                    <td>{{ $branch->address ?? 'N/A' }}</td>
                                    <td>{{ $branch->assignedUser ? $branch->assignedUser->name . ' (' . $branch->assignedUser->id . ')' : 'Not Assigned' }}</td>
                                    <td>
                                        <span class="badge {{ $branch->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($branch->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#branchModal" onclick="editBranch({{ $branch->id }}, '{{ $branch->branch_name }}', '{{ $branch->address ?? '' }}', '{{ $branch->assign_to ?? '' }}', '{{ $branch->status }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('superadmin.branches.destroy', $branch) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No branches found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Branch Modal -->
<div class="modal fade" id="branchModal" tabindex="-1" aria-labelledby="branchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="branchModalLabel">Add Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="branchForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="branch_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="mb-3">
                        <label for="assign_to" class="form-label">Assign To</label>
                        <select class="form-select" id="assign_to" name="assign_to">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Branch</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openBranchModal() {
    document.getElementById('branchForm').action = '{{ route("superadmin.branches.store") }}';
    document.getElementById('branchForm').querySelector('input[name="_method"]')?.remove();
    document.getElementById('branchModalLabel').textContent = 'Add Branch';
    document.getElementById('branch_name').value = '';
    document.getElementById('address').value = '';
    document.getElementById('assign_to').value = '';
    document.getElementById('status').value = 'active';
}

function editBranch(id, name, address, assignTo, status) {
    document.getElementById('branchForm').action = '/superadmin/branches/' + id;
    if (!document.getElementById('branchForm').querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('branchForm').appendChild(methodInput);
    }
    document.getElementById('branchModalLabel').textContent = 'Edit Branch';
    document.getElementById('branch_name').value = name;
    document.getElementById('address').value = address;
    document.getElementById('assign_to').value = assignTo;
    document.getElementById('status').value = status;
}
</script>
@endsection