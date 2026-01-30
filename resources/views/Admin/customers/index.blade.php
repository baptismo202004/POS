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
                            <button class="btn btn-warning me-2" onclick="editSelectedCustomer()">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <a href="{{ route('superadmin.admin.sales.index') }}" class="btn btn-primary">Go to Sales</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                        </th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Total Credit</th>
                                        <th>Paid Amount</th>
                                        <th>Outstanding Balance</th>
                                        <th>Credit Giver</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Registered Customers -->
                                    @forelse($customers as $customer)
                                        <?php
                                        $totalCredits = $customer->credits->sum('credit_amount');
                                        $totalPaid = $customer->credits->sum('paid_amount');
                                        $outstandingBalance = $customer->credits->sum('remaining_balance');
                                        $rowNumber = $loop->index + 1; // Start from 1
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="customer-checkbox" value="{{ $customer->id }}" data-customer='{{ json_encode($customer) }}'>
                                            </td>
                                            <td>{{ $rowNumber }}</td>
                                            <td>{{ $customer->full_name }}</td>
                                            <td>{{ $customer->phone ?? 'N/A' }}</td>
                                            <td>{{ $customer->email ?? 'N/A' }}</td>
                                            <td>{{ $customer->address ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($totalCredits, 2) }}</td>
                                            <td>₱{{ number_format($totalPaid, 2) }}</td>
                                            <td class="fw-bold {{ $outstandingBalance > 0 ? 'text-danger' : 'text-success' }}">
                                                ₱{{ number_format($outstandingBalance, 2) }}
                                            </td>
                                            <td>
                                                @forelse($customer->credits as $credit)
                                                    {{ $credit->cashier->name ?? 'Unknown' }}
                                                    @if(!$loop->last), @endif
                                                @empty
                                                    No credits
                                                @endforelse
                                            </td>
                                        </tr>
                                    @empty
                                        @if($walkInCredits->count() == 0)
                                            <tr>
                                                <td colspan="10" class="text-center">No customers found.</td>
                                            </tr>
                                        @endif
                                    @endforelse
                                    
                                    <!-- All Credits (for debugging) -->
                                    @forelse($walkInCredits as $credit)
                                        <tr class="{{ $credit->customer_id ? 'table-info' : 'table-warning' }}">
                                            <td>
                                                <input type="checkbox" class="walk-in-checkbox" value="{{ $credit->id }}" data-credit='{{ json_encode($credit) }}'>
                                            </td>
                                            <td>{{ $credit->customer_id ? 'R-' . $loop->index + 1 : 'W-' . $loop->index + 1 }}</td>
                                            <td>{{ $credit->customer_name ?? ($credit->customer->name ?? 'Walk-in Customer') }}</td>
                                            <td>{{ $credit->phone ?? 'N/A' }}</td>
                                            <td>{{ $credit->email ?? 'N/A' }}</td>
                                            <td>{{ $credit->address ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($credit->credit_amount, 2) }}</td>
                                            <td>₱{{ number_format($credit->paid_amount, 2) }}</td>
                                            <td class="fw-bold {{ $credit->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                ₱{{ number_format($credit->remaining_balance, 2) }}
                                            </td>
                                            <td>{{ $credit->cashier->name ?? 'Unknown' }}</td>
                                        </tr>
                                    @empty
                                        @if($customers->count() == 0)
                                            <tr>
                                                <td colspan="10" class="text-center">No customers or credits found.</td>
                                            </tr>
                                        @endif
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
function toggleAllCheckboxes() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function editSelectedCustomer() {
    const selectedCustomer = document.querySelector('.customer-checkbox:checked');
    const selectedWalkIn = document.querySelector('.walk-in-checkbox:checked');
    
    if (!selectedCustomer && !selectedWalkIn) {
        Swal.fire({
            icon: 'warning',
            title: 'No Customer Selected',
            text: 'Please select at least one customer to edit.',
            confirmButtonColor: '#2563eb'
        });
        return;
    }
    
    if (selectedCustomer && selectedWalkIn) {
        Swal.fire({
            icon: 'warning',
            title: 'Multiple Customers Selected',
            text: 'Please select only one customer to edit.',
            confirmButtonColor: '#2563eb'
        });
        return;
    }
    
    if (selectedWalkIn) {
        // Handle walk-in customer editing
        const creditData = JSON.parse(selectedWalkIn.dataset.credit);
        
        Swal.fire({
            title: 'Edit Walk-in Customer',
            html: `
                <div class="text-start">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Credit ID:</label>
                                <input type="text" class="form-control" value="${creditData.id}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer Name:</label>
                                <input type="text" class="form-control" id="edit_customer_name" value="${creditData.customer_name || ''}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone:</label>
                                <input type="text" class="form-control" id="edit_phone" placeholder="Enter phone number">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email:</label>
                                <input type="email" class="form-control" id="edit_email" placeholder="Enter email address">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Address:</label>
                                <textarea class="form-control" id="edit_address" rows="4" placeholder="Enter address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Credit Amount:</label>
                                <input type="text" class="form-control" value="₱${creditData.credit_amount}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Outstanding Balance:</label>
                                <input type="text" class="form-control" value="₱${creditData.remaining_balance}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Update Customer',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2563eb'
        }).then((result) => {
            if (result.isConfirmed) {
                // Update walk-in customer with all fields
                updateWalkInCustomer(creditData.id, {
                    customer_name: document.getElementById('edit_customer_name').value,
                    phone: document.getElementById('edit_phone').value,
                    email: document.getElementById('edit_email').value,
                    address: document.getElementById('edit_address').value
                });
            }
        });
    } else {
        // Handle regular customer editing
        const customerData = JSON.parse(selectedCustomer.dataset.customer);
        
        Swal.fire({
            title: 'Edit Customer',
            html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label">Customer ID:</label>
                        <input type="text" class="form-control" id="edit_customer_id" value="${customerData.id}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name:</label>
                        <input type="text" class="form-control" id="edit_full_name" value="${customerData.full_name || ''}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone:</label>
                        <input type="text" class="form-control" id="edit_phone" value="${customerData.phone || ''}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" class="form-control" id="edit_email" value="${customerData.email || ''}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address:</label>
                        <textarea class="form-control" id="edit_address" rows="2">${customerData.address || ''}</textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save Changes',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2563eb',
            preConfirm: () => {
                return {
                    id: document.getElementById('edit_customer_id').value,
                    full_name: document.getElementById('edit_full_name').value,
                    phone: document.getElementById('edit_phone').value,
                    email: document.getElementById('edit_email').value,
                    address: document.getElementById('edit_address').value
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Send AJAX request to update the customer
                const customerData = result.value;
            
            fetch(`{{ route('superadmin.admin.customers.update', ':customer') }}`.replace(':customer', customerData.id), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(customerData)
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, return error with response text
                    return response.text().then(text => {
                        throw new Error(`Server returned non-JSON response: ${text.substring(0, 100)}...`);
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Updated',
                        text: 'Customer information has been updated successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        // Reload the page to show updated data
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update customer.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the customer.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
    }
}

function updateWalkInCustomerName(creditId, newName) {
    fetch(`/superadmin/admin/credits/${creditId}/update-name`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            customer_name: newName
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Customer Name Updated',
                text: 'Walk-in customer name has been updated successfully.',
                confirmButtonColor: '#2563eb'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data.message || 'Failed to update customer name.',
                confirmButtonColor: '#2563eb'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while updating the customer name.',
            confirmButtonColor: '#2563eb'
        });
    });
}

function updateWalkInCustomer(creditId, customerData) {
    fetch(`/superadmin/admin/credits/${creditId}/update-customer`, {
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
                title: 'Customer Updated',
                text: 'Walk-in customer information has been updated successfully.',
                confirmButtonColor: '#2563eb'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: data.message || 'Failed to update customer information.',
                confirmButtonColor: '#2563eb'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while updating the customer information.',
            confirmButtonColor: '#2563eb'
        });
    });
}
</script>
