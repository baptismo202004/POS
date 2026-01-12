@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Unit Types</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#unitTypeModal" onclick="openUnitTypeModal()">
                        <i class="fas fa-plus"></i> Add Unit Type
                    </button>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unitTypes as $unitType)
                                <tr>
                                    <td>{{ $unitType->id }}</td>
                                    <td>{{ $unitType->unit_name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#unitTypeModal" onclick="editUnitType({{ $unitType->id }}, '{{ $unitType->unit_name }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('superadmin.unit-types.destroy', $unitType) }}" class="d-inline">
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
                                    <td colspan="3">No unit types found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unit Type Modal -->
<div class="modal fade" id="unitTypeModal" tabindex="-1" aria-labelledby="unitTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unitTypeModalLabel">Add Unit Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="unitTypeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="unit_name" class="form-label">Unit Type Name</label>
                        <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Unit Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openUnitTypeModal() {
    document.getElementById('unitTypeForm').action = '{{ route("superadmin.unit-types.store") }}';
    document.getElementById('unitTypeForm').querySelector('input[name="_method"]')?.remove();
    document.getElementById('unitTypeModalLabel').textContent = 'Add Unit Type';
    document.getElementById('unit_name').value = '';
}

function editUnitType(id, name) {
    document.getElementById('unitTypeForm').action = '/superadmin/unit-types/' + id;
    if (!document.getElementById('unitTypeForm').querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('unitTypeForm').appendChild(methodInput);
    }
    document.getElementById('unitTypeModalLabel').textContent = 'Edit Unit Type';
    document.getElementById('unit_name').value = name;
}
</script>
@endsection