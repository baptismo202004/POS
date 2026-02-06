@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-base">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="page-header">
                        <h3 class="m-0">Brands</h3>
                        <p class="text-muted mb-0">Manage product brands and manufacturers</p>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="openBrandModal()">
                        <i class="fas fa-plus me-2"></i> Add Brand
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-base">
                        <table class="table">
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
                                        <td><span class="badge badge-secondary">#{{ $brand->id }}</span></td>
                                        <td>
                                            <div class="fw-semibold" style="color: var(--electric-blue);">{{ $brand->brand_name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $brand->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                                {{ ucfirst($brand->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="editBrand({{ $brand->id }}, '{{ $brand->brand_name }}', '{{ $brand->status }}')">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form method="POST" action="{{ route('superadmin.brands.destroy', $brand) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this brand?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-tag fa-3x mb-3"></i>
                                                <div class="fw-semibold">No brands found</div>
                                                <small>Start by adding your first brand</small>
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