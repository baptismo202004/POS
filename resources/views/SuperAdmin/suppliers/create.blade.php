@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-base">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="page-header">
                        <h3 class="m-0">Add New Supplier</h3>
                        <p class="text-muted mb-0">Create a new supplier record</p>
                    </div>
                    <a href="{{ route('superadmin.suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Suppliers
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.suppliers.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
                                    @error('supplier_name')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_person" class="form-label">Contact Person</label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                                    @error('contact_person')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('superadmin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Form submission with SweetAlert
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Debug: Log form submission start
    console.log('=== SUPPLIER FORM SUBMISSION DEBUG ===');
    console.log('Form action:', this.action);
    console.log('Form method:', this.method);
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Debug: Log form data
    console.log('Form data submitted:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
    
    console.log('Sending request to:', this.action);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        console.log('Success status:', data.success);
        
        if (data.success) {
            console.log('✅ Supplier created successfully!');
            console.log('Supplier data:', data.supplier);
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Supplier created successfully!',
                confirmButtonColor: '#0D47A1',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                console.log('Redirecting to suppliers index...');
                window.location.href = '{{ route("superadmin.suppliers.index") }}';
            });
        } else {
            console.log('❌ Error creating supplier');
            console.log('Error details:', data.errors);
            
            let errorMessage = 'Error creating supplier';
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join(', ');
                console.log('Formatted error message:', errorMessage);
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
        console.error('❌ Fetch error occurred:');
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error creating supplier',
            confirmButtonColor: '#0D47A1'
        });
    })
    .finally(() => {
        console.log('=== FORM SUBMISSION COMPLETED ===');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endsection
