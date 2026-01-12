@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Brands</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="openBrandModal()">
                        <i class="fas fa-plus"></i> Add Brand
                    </button>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                                <tr>
                                    <td>{{ $brand->id }}</td>
                                    <td>{{ $brand->brand_name }}</td>
                                    <td>
                                        <span class="badge {{ $brand->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($brand->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="editBrand({{ $brand->id }}, '{{ $brand->brand_name }}', '{{ $brand->status }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('superadmin.brands.destroy', $brand) }}" class="d-inline">
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
                                    <td colspan="4">No brands found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Brand Modal -->
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brandModalLabel">Add Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="brandForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openBrandModal() {
    document.getElementById('brandForm').action = '{{ route("superadmin.brands.store") }}';
    document.getElementById('brandForm').querySelector('input[name="_method"]')?.remove();
    document.getElementById('brandModalLabel').textContent = 'Add Brand';
    document.getElementById('brand_name').value = '';
    document.getElementById('status').value = 'active';
}

function editBrand(id, name, status) {
    document.getElementById('brandForm').action = '/superadmin/brands/' + id;
    if (!document.getElementById('brandForm').querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('brandForm').appendChild(methodInput);
    }
    document.getElementById('brandModalLabel').textContent = 'Edit Brand';
    document.getElementById('brand_name').value = name;
    document.getElementById('status').value = status;
}
</script>
@endsection