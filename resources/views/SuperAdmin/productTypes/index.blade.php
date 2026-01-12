@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Product Types</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productTypeModal" onclick="openProductTypeModal()">
                        <i class="fas fa-plus"></i> Add Product Type
                    </button>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Is Electronic</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productTypes as $productType)
                                <tr>
                                    <td>{{ $productType->id }}</td>
                                    <td>{{ $productType->type_name }}</td>
                                    <td>{{ $productType->is_electronic ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#productTypeModal" onclick="editProductType({{ $productType->id }}, '{{ $productType->type_name }}', {{ $productType->is_electronic ? 'true' : 'false' }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form method="POST" action="{{ route('superadmin.product-types.destroy', $productType) }}" class="d-inline">
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
                                    <td colspan="4">No product types found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Type Modal -->
<div class="modal fade" id="productTypeModal" tabindex="-1" aria-labelledby="productTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productTypeModalLabel">Add Product Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productTypeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type_name" class="form-label">Product Type Name</label>
                        <input type="text" class="form-control" id="type_name" name="type_name" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_electronic" name="is_electronic" value="1">
                            <label class="form-check-label" for="is_electronic">Is Electronic</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openProductTypeModal() {
    document.getElementById('productTypeForm').action = '{{ route("superadmin.product-types.store") }}';
    document.getElementById('productTypeForm').querySelector('input[name="_method"]')?.remove();
    document.getElementById('productTypeModalLabel').textContent = 'Add Product Type';
    document.getElementById('type_name').value = '';
    document.getElementById('is_electronic').checked = false;
}

function editProductType(id, name, isElectronic) {
    document.getElementById('productTypeForm').action = '/superadmin/product-types/' + id;
    if (!document.getElementById('productTypeForm').querySelector('input[name="_method"]')) {
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        document.getElementById('productTypeForm').appendChild(methodInput);
    }
    document.getElementById('productTypeModalLabel').textContent = 'Edit Product Type';
    document.getElementById('type_name').value = name;
    document.getElementById('is_electronic').checked = isElectronic;
}
</script>
@endsection