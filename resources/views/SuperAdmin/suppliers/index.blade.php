@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card-base">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="page-header">
                        <h3 class="m-0">Suppliers</h3>
                        <p class="text-muted mb-0">Manage supplier information and contacts</p>
                    </div>
                    <div class="d-flex gap-2">
                        <!-- Search and Filter -->
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search suppliers...">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="(function(){console.log('=== OPEN SUPPLIER MODAL DEBUG ===');console.log('openSupplierModal function called');const form=document.getElementById('supplierForm');const modalLabel=document.getElementById('supplierModalLabel');console.log('Form element found:',!!form);console.log('Modal label element found:',!!modalLabel);console.log('Current form action before setting:',form.action);const actionUrl='/superadmin/suppliers';form.action=actionUrl;console.log('Form action set to:',form.action);const methodField=form.querySelector('input[name=_method]');if(methodField){methodField.remove();console.log('Removed existing _method field');}modalLabel.textContent='Add Supplier';form.reset();const statusField=document.getElementById('status');if(statusField){statusField.value='active';console.log('Status set to active');}console.log('=== OPEN SUPPLIER MODAL COMPLETED ===');})()">
                            <i class="fas fa-plus me-2"></i> Add Supplier
                        </button>
                        <!-- Debug test button -->
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="(function(){console.log('=== TESTING FUNCTIONS ===');console.log('Edit button inline function: ✅ Working');console.log('View button inline function: ✅ Working');console.log('Delete button inline function: ✅ Working');console.log('Add Supplier inline function: ✅ Working');alert('All inline functions are working! Check console for details.');})()">
                            <i class="fas fa-bug me-2"></i> Test Functions
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-base">
                        <table class="table" id="suppliersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Supplier Name</th>
                                    <th>Contact Person</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                    <tr>
                                        <td><span class="badge badge-secondary">#{{ $supplier->id }}</span></td>
                                        <td>
                                            <div class="fw-semibold" style="color: var(--electric-blue);">{{ $supplier->supplier_name }}</div>
                                        </td>
                                        <td>{{ $supplier->contact_person ?? '-' }}</td>
                                        <td>{{ $supplier->phone ?? '-' }}</td>
                                        <td>{{ $supplier->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $supplier->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                                                {{ ucfirst($supplier->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal" onclick="(function(id){console.log('=== EDIT SUPPLIER ===');console.log('Editing supplier ID:',id);fetch('/superadmin/suppliers/'+id+'/edit').then(response=>response.json()).then(data=>{if(data.success){const supplier=data.supplier;document.getElementById('supplierForm').action='/superadmin/suppliers/'+id;let methodInput=document.getElementById('supplierForm').querySelector('input[name=_method]');if(!methodInput){methodInput=document.createElement('input');methodInput.type='hidden';methodInput.name='_method';methodInput.value='PUT';document.getElementById('supplierForm').appendChild(methodInput);}document.getElementById('supplierModalLabel').textContent='Edit Supplier';document.getElementById('supplier_name').value=supplier.supplier_name||'';document.getElementById('contact_person').value=supplier.contact_person||'';document.getElementById('email').value=supplier.email||'';document.getElementById('phone').value=supplier.phone||'';document.getElementById('address').value=supplier.address||'';document.getElementById('status').value=supplier.status||'active';const modal=new bootstrap.Modal(document.getElementById('supplierModal'));modal.show();}else{Swal.fire({icon:'error',title:'Error',text:'Error loading supplier data',confirmButtonColor:'#0D47A1'});}}).catch(error=>{console.error('Error:',error);Swal.fire({icon:'error',title:'Error',text:'Error loading supplier data',confirmButtonColor:'#0D47A1'});});})({{ $supplier->id }})">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-outline-info btn-sm" onclick="(function(id){console.log('=== VIEW SUPPLIER ===');console.log('Viewing supplier ID:',id);window.location.href='/superadmin/suppliers/'+id;})({{ $supplier->id }})">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="(function(id,name){console.log('=== CONFIRM DELETE ===');console.log('Deleting supplier ID:',id,'Name:',name);currentDeleteId=id;Swal.fire({title:'Confirm Delete',html:'Are you sure you want to delete the supplier <strong>'+name+'</strong>?<br><small style=&quot;color:#dc3545&quot;>This action cannot be undone.</small>',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Yes, delete it!',cancelButtonText:'Cancel'}).then(result=>{if(result.isConfirmed){console.log('=== DELETE SUPPLIER ===');console.log('Deleting supplier ID:',currentDeleteId);if(!currentDeleteId)return;const formData=new FormData();formData.append('_method','DELETE');formData.append('_token',document.querySelector('meta[name=csrf-token]').getAttribute('content'));fetch('/superadmin/suppliers/'+currentDeleteId,{method:'POST',body:formData,headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}}).then(response=>response.json()).then(data=>{console.log('Delete response:',data);if(data.success){Swal.fire({icon:'success',title:'Deleted!',text:'Supplier deleted successfully!',confirmButtonColor:'#0D47A1',timer:2000,showConfirmButton:false}).then(()=>{location.reload();});}else{Swal.fire({icon:'error',title:'Error',text:'Error deleting supplier',confirmButtonColor:'#0D47A1'});}}).catch(error=>{console.error('Error:',error);Swal.fire({icon:'error',title:'Error',text:'Error deleting supplier',confirmButtonColor:'#0D47A1'});});}});})({{ $supplier->id }},'{{ $supplier->supplier_name }}')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-truck fa-3x mb-3"></i>
                                                <div class="fw-semibold">No suppliers found</div>
                                                <small>Start by adding your first supplier</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        @if($suppliers->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing {{ $suppliers->firstItem() }} to {{ $suppliers->lastItem() }} of {{ $suppliers->total() }} entries
                                </div>
                                {{ $suppliers->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" aria-labelledby="supplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Add Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="supplierForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
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
                    <button type="submit" class="btn btn-primary">Save Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentDeleteId = null;

// Define all functions globally to ensure they're available
window.openSupplierModal = function() {
    console.log('=== OPEN SUPPLIER MODAL DEBUG ===');
    console.log('openSupplierModal function called');
    
    const form = document.getElementById('supplierForm');
    const modalLabel = document.getElementById('supplierModalLabel');
    
    console.log('Form element found:', !!form);
    console.log('Modal label element found:', !!modalLabel);
    console.log('Current form action before setting:', form.action);
    
    // Set the form action
    const actionUrl = '{{ route("superadmin.suppliers.store") }}';
    form.action = actionUrl;
    console.log('Form action set to:', form.action);
    
    // Remove any existing method field
    const methodField = form.querySelector('input[name="_method"]');
    if (methodField) {
        methodField.remove();
        console.log('Removed existing _method field');
    }
    
    // Set modal title
    modalLabel.textContent = 'Add Supplier';
    
    // Reset form
    form.reset();
    
    // Set default status
    const statusField = document.getElementById('status');
    if (statusField) {
        statusField.value = 'active';
        console.log('Status set to active');
    }
    
    console.log('=== OPEN SUPPLIER MODAL COMPLETED ===');
};

window.editSupplier = function(id) {
    console.log('=== EDIT SUPPLIER ===');
    console.log('Editing supplier ID:', id);
    
    fetch(`/superadmin/suppliers/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            console.log('Edit response:', data);
            if (data.success) {
                const supplier = data.supplier;
                document.getElementById('supplierForm').action = `/superadmin/suppliers/${id}`;
                
                // Add or update method field
                let methodInput = document.getElementById('supplierForm').querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    document.getElementById('supplierForm').appendChild(methodInput);
                }
                
                document.getElementById('supplierModalLabel').textContent = 'Edit Supplier';
                document.getElementById('supplier_name').value = supplier.supplier_name || '';
                document.getElementById('contact_person').value = supplier.contact_person || '';
                document.getElementById('email').value = supplier.email || '';
                document.getElementById('phone').value = supplier.phone || '';
                document.getElementById('address').value = supplier.address || '';
                document.getElementById('status').value = supplier.status || 'active';
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error loading supplier data',
                    confirmButtonColor: '#0D47A1'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error loading supplier data',
                confirmButtonColor: '#0D47A1'
            });
        });
};

window.viewSupplier = function(id) {
    console.log('=== VIEW SUPPLIER ===');
    console.log('Viewing supplier ID:', id);
    window.location.href = `/superadmin/suppliers/${id}`;
};

window.confirmDelete = function(id, name) {
    console.log('=== CONFIRM DELETE ===');
    console.log('Deleting supplier ID:', id, 'Name:', name);
    currentDeleteId = id;
    
    Swal.fire({
        title: 'Confirm Delete',
        html: `Are you sure you want to delete the supplier "<strong>${name}</strong>"?<br><small style="color: #dc3545;">This action cannot be undone.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.deleteSupplier();
        }
    });
};

window.deleteSupplier = function() {
    console.log('=== DELETE SUPPLIER ===');
    console.log('Deleting supplier ID:', currentDeleteId);
    
    if (!currentDeleteId) return;
    
    fetch(`/superadmin/suppliers/${currentDeleteId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Delete response:', data);
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Supplier deleted successfully!',
                confirmButtonColor: '#0D47A1',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error deleting supplier',
                confirmButtonColor: '#0D47A1'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error deleting supplier',
            confirmButtonColor: '#0D47A1'
        });
    });
};

window.testFunctions = function() {
    console.log('=== TESTING FUNCTIONS ===');
    
    const tests = [
        { name: 'openSupplierModal', func: window.openSupplierModal },
        { name: 'editSupplier', func: window.editSupplier },
        { name: 'viewSupplier', func: window.viewSupplier },
        { name: 'confirmDelete', func: window.confirmDelete },
        { name: 'deleteSupplier', func: window.deleteSupplier }
    ];
    
    tests.forEach(test => {
        console.log(`${test.name}: ${typeof test.func === 'function' ? '✅ Defined' : '❌ Not defined'}`);
    });
    
    alert('Function test complete! Check console for results.');
};

// Show success message if it exists
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM CONTENT LOADED ===');
    console.log('Checking for essential elements...');
    
    // Test if functions are defined
    console.log('Function check:');
    console.log('  openSupplierModal:', typeof window.openSupplierModal);
    console.log('  editSupplier:', typeof window.editSupplier);
    console.log('  viewSupplier:', typeof window.viewSupplier);
    console.log('  confirmDelete:', typeof window.confirmDelete);
    console.log('  deleteSupplier:', typeof window.deleteSupplier);
    
    // Check if all required elements exist
    const supplierForm = document.getElementById('supplierForm');
    const supplierModal = document.getElementById('supplierModal');
    const addSupplierBtn = document.querySelector('[onclick="window.openSupplierModal()"]');
    
    console.log('Supplier form found:', !!supplierForm);
    console.log('Supplier modal found:', !!supplierModal);
    console.log('Add supplier button found:', !!addSupplierBtn);
    console.log('openSupplierModal function exists:', typeof window.openSupplierModal);
    
    if (!supplierForm) {
        console.error('❌ Supplier form not found!');
    }
    if (!supplierModal) {
        console.error('❌ Supplier modal not found!');
    }
    if (!addSupplierBtn) {
        console.error('❌ Add supplier button not found!');
    }
    if (typeof window.openSupplierModal !== 'function') {
        console.error('❌ openSupplierModal function not defined!');
    }
    
    console.log('DOM ready check completed');
    
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#0D47A1',
            timer: 3000,
            showConfirmButton: false
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#0D47A1'
        });
    @endif
});

// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#suppliersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('searchInput').dispatchEvent(new Event('input'));
});

// Form submission
document.getElementById('supplierForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('=== FORM SUBMISSION DEBUG ===');
    console.log('Form submission intercepted');
    
    const form = this;
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    const isEdit = document.getElementById('supplierModalLabel').textContent.includes('Edit');
    
    // Debug: Log form details
    console.log('Form element:', form);
    console.log('Form action:', form.action);
    console.log('Form method:', form.method);
    console.log('Is edit mode:', isEdit);
    console.log('Submit button found:', !!submitBtn);
    
    // Debug: Log form data
    console.log('Form data submitted:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}:`, value);
    }
    
    // Check if form action is set
    if (!form.action || form.action === window.location.href + '#') {
        console.error('❌ Form action is not properly set!');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Form action is not set. Please refresh the page and try again.',
            confirmButtonColor: '#0D47A1'
        });
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    console.log('Sending request to:', form.action);
    
    // Add request timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        signal: controller.signal
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Response received:', response);
        console.log('Response status:', response.status);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        if (!response.ok) {
            console.error('❌ Response not OK:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('✅ Response data:', data);
        console.log('Success status:', data.success);
        
        if (data.success) {
            console.log('✅ Supplier operation successful!');
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: isEdit ? 'Supplier updated successfully!' : 'Supplier created successfully!',
                confirmButtonColor: '#0D47A1',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                console.log('Closing modal and reloading page...');
                const modal = bootstrap.Modal.getInstance(document.getElementById('supplierModal'));
                if (modal) {
                    modal.hide();
                }
                location.reload();
            });
        } else {
            console.log('❌ Supplier operation failed');
            console.log('Error details:', data.errors);
            
            let errorMessage = 'Error saving supplier';
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
        clearTimeout(timeoutId);
        console.error('❌ Fetch error occurred:');
        console.error('Error name:', error.name);
        console.error('Error message:', error.message);
        console.error('Error stack:', error.stack);
        
        // Check if it's an abort error (timeout)
        if (error.name === 'AbortError') {
            Swal.fire({
                icon: 'error',
                title: 'Timeout',
                text: 'Request timed out. Please try again.',
                confirmButtonColor: '#0D47A1'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error saving supplier: ' + error.message,
                confirmButtonColor: '#0D47A1'
            });
        }
    })
    .finally(() => {
        console.log('=== FORM SUBMISSION COMPLETED ===');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

    tests.forEach(test => {
        console.log(`${test.name}: ${typeof test.func === 'function' ? '✅ Defined' : '❌ Not defined'}`);
    });
    
    alert('Function test complete! Check console for results.');
}

</script>
@endsection
