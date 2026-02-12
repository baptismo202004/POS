@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-base">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="page-header">
                        <h3 class="m-0">Supplier Details</h3>
                        <p class="text-muted mb-0">View supplier information</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('superadmin.suppliers.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Suppliers
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="editSupplier({{ $supplier->id }})">
                            <i class="fas fa-edit me-2"></i> Edit Supplier
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="supplier-info">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Supplier Name:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $supplier->supplier_name }}
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Contact Person:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $supplier->contact_person ?? '-' }}
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Phone Number:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $supplier->phone ?? '-' }}
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Email:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        @if($supplier->email)
                                            <a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Address:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $supplier->address ?? '-' }}
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Status:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        <span class="badge {{ $supplier->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Created:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $supplier->created_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <strong>Last Updated:</strong>
                                    </div>
                                    <div class="col-sm-9">
                                        {{ $supplier->updated_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('superadmin.purchases.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart me-2"></i> Create Purchase
                                        </a>
                                        @if($supplier->email)
                                            <a href="mailto:{{ $supplier->email }}" class="btn btn-outline-primary">
                                                <i class="fas fa-envelope me-2"></i> Send Email
                                            </a>
                                        @endif
                                        @if($supplier->phone)
                                            <a href="tel:{{ $supplier->phone }}" class="btn btn-outline-success">
                                                <i class="fas fa-phone me-2"></i> Call Supplier
                                            </a>
                                        @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Edit Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ $supplier->supplier_name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ $supplier->contact_person ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $supplier->email ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ $supplier->phone ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ $supplier->address ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" {{ $supplier->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $supplier->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function editSupplier(id) {
    document.getElementById('supplierForm').action = `/superadmin/suppliers/${id}`;
}

// Form submission
document.getElementById('supplierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Supplier updated successfully!',
                confirmButtonColor: '#0D47A1',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('supplierModal')).hide();
                location.reload();
            });
        } else {
            let errorMessage = 'Error updating supplier';
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join(', ');
            }
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
                confirmButtonColor: '#0D47A1'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error updating supplier',
            confirmButtonColor: '#0D47A1'
        });
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});
</script>
@endsection
