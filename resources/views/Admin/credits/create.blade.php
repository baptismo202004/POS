@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Create New Credit</h2>
            <a href="{{ route('superadmin.admin.credits.index') }}" class="btn btn-outline-primary">Back to Credits</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="creditForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select class="form-select" name="customer_id" id="customer_id">
                                <option value="">Select Customer (Optional)</option>
                                <option value="">Walk-in Customer</option>
                                {{-- You can add customers dynamically here --}}
                            </select>
                            <small class="text-muted">Leave blank for walk-in customer</small>
                        </div>

                        <div class="col-md-6">
                            <label for="sale_id" class="form-label">Related Sale (Optional)</label>
                            <select class="form-select" name="sale_id" id="sale_id">
                                <option value="">Select Sale</option>
                                {{-- You can add sales dynamically here --}}
                            </select>
                            <small class="text-muted">Link to an existing sale if applicable</small>
                        </div>

                        <div class="col-md-6">
                            <label for="credit_amount" class="form-label">Credit Amount (₱)</label>
                            <input type="number" class="form-control" name="credit_amount" id="credit_amount" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" name="due_date" id="due_date" required>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Add any additional notes about this credit..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Credit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Set minimum due date to tomorrow
    document.addEventListener('DOMContentLoaded', function() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('due_date').min = tomorrow.toISOString().split('T')[0];
        
        // Set default due date to 30 days from now
        const defaultDueDate = new Date();
        defaultDueDate.setDate(defaultDueDate.getDate() + 30);
        document.getElementById('due_date').value = defaultDueDate.toISOString().split('T')[0];
    });

    document.getElementById('creditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Creating Credit...',
            html: 'Please wait while we create the credit.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('/admin/credits', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/admin/credits';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred while creating the credit.',
                    confirmButtonColor: '#2563eb'
                });
                
                // Show validation errors if any
                if (data.errors) {
                    let errorMessages = '';
                    for (let field in data.errors) {
                        errorMessages += data.errors[field].join('\n') + '\n';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessages,
                        confirmButtonColor: '#2563eb'
                    });
                }
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while creating the credit.',
                confirmButtonColor: '#2563eb'
            });
        });
    });
</script>
@endsection
