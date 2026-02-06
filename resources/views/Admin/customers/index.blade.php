<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customers - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="card card-rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="m-0">Customers</h4>
                        </div>
                        <div>
                            <button class="btn btn-success me-2" onclick="addNewCustomer()">
                                <i class="fas fa-plus me-1"></i>Add Customer
                            </button>
                            <a href="{{ route('superadmin.admin.sales.index') }}" class="btn btn-primary">Go to Sales</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Created At</th>
                                        <th>Created By</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->customer_id }}</td>
                                            <td>
                                                <strong>{{ $customer->full_name }}</strong>
                                                @if($customer->outstanding_balance > 0)
                                                    <span class="badge bg-warning ms-1">Has Balance</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</td>
                                            <td>{{ $customer->created_by }}</td>
                                            <td>Main Branch</td>
                                            <td>
                                                <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($customer->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('superadmin.admin.customers.show', $customer->customer_id) }}" class="btn btn-outline-primary" title="View Customer">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($customer->status == 'active')
                                                        <button type="button" class="btn btn-outline-danger" onclick="toggleCustomerStatus({{ $customer->customer_id }}, '{{ $customer->status }}')" title="Block Customer">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-success" onclick="toggleCustomerStatus({{ $customer->customer_id }}, '{{ $customer->status }}')" title="Unblock Customer">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No customers found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($customers->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $customers->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Add event listeners for single selection
document.addEventListener('DOMContentLoaded', function() {
    const customerCheckboxes = document.querySelectorAll('.customer-checkbox, .walk-in-checkbox');
    
    customerCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck all other checkboxes
                customerCheckboxes.forEach(otherCheckbox => {
                    if (otherCheckbox !== this) {
                        otherCheckbox.checked = false;
                    }
                });
            }
        });
    });
});

function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function addNewCustomer() {
    Swal.fire({
        title: 'Add New Customer',
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="new_full_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="new_phone" placeholder="Enter phone number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="new_email" placeholder="Enter email address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="new_address" rows="3" placeholder="Enter address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Credit Limit</label>
                            <input type="number" class="form-control" id="new_max_credit_limit" step="0.01" min="0" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Status</label>
                            <select class="form-control" id="new_status">
                                <option value="active">Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Add Customer',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const fullName = document.getElementById('new_full_name').value;
            if (!fullName.trim()) {
                Swal.showValidationMessage('Full name is required');
                return false;
            }
            
            return {
                full_name: fullName,
                phone: document.getElementById('new_phone').value,
                email: document.getElementById('new_email').value,
                address: document.getElementById('new_address').value,
                max_credit_limit: parseFloat(document.getElementById('new_max_credit_limit').value) || 0,
                status: document.getElementById('new_status').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Send AJAX request to create the customer
            const customerData = result.value;
        
            fetch('{{ route("superadmin.admin.customers.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(customerData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Added',
                        text: 'New customer has been added successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Add Failed',
                        text: data.message || 'Failed to add customer.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding the customer.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function viewCustomerDetails(customerId) {
    // This would typically open a modal or navigate to a detail page
    Swal.fire({
        title: 'Customer Details',
        text: 'Detailed customer view coming soon!',
        icon: 'info',
        confirmButtonColor: '#2563eb'
    });
}

function toggleCustomerStatus(customerId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'blocked' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'block';
    
    Swal.fire({
        title: `Confirm ${action} customer?`,
        text: `Are you sure you want to ${action} this customer?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/superadmin/admin/customers/${customerId}/toggle-status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: `Customer has been ${action}d successfully.`,
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update customer status.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating customer status.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

</script>
