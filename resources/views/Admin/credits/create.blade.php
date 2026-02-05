@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                                <option value="">Select Customer </option>
                                
                                @if($customers->isNotEmpty())
                                    <optgroup label="Registered Customers">
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                
                                @if($walkInCustomers->isNotEmpty())
                                    <optgroup label="Walk-in Customers (Previous Credits)">
                                        @foreach($walkInCustomers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="credit_amount" class="form-label">Credit Amount (₱)</label>
                            <input type="number" class="form-control" name="credit_amount" id="credit_amount" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-6">
                            <label for="date" class="form-label">Credit Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize Select2 for customer dropdown
    $(document).ready(function() {
        $('#customer_id').select2({
            placeholder: "Search or add new customer...",
            allowClear: true,
            tags: true, // Allow adding new options
            createTag: function (params) {
                var term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: 'new-' + term, // Use 'new-' prefix to identify new customers
                    text: term + ' (New Customer)',
                    newOption: true
                }
            }
        });
    });
    // Set credit date to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        document.getElementById('date').min = today.toISOString().split('T')[0];
        document.getElementById('date').value = today.toISOString().split('T')[0];
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
        
        fetch('/superadmin/admin/credits', {
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
                    window.location.href = '/superadmin/admin/credits';
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
