<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Profile - {{ $customerDetails->full_name }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
        .profile-header {
            background: linear-gradient(135deg, var(--theme-color) 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
        }
        .info-card {
            border-left: 4px solid var(--theme-color);
        }
        .credit-summary {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Customers
                    </a>
                </div>

                <!-- Profile Header -->
                <div class="card card-rounded shadow-sm mb-4">
                    <div class="profile-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">{{ $customerDetails->full_name }}</h2>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $customerDetails->status == 'active' ? 'success' : 'danger' }} me-2">
                                        {{ ucfirst($customerDetails->status) }}
                                    </span>
                                    Customer ID: #{{ $customerDetails->customer_id }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-light" onclick="editCustomer()">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    @if($customerDetails->status == 'active')
                                        <button class="btn btn-danger" onclick="toggleCustomerStatus({{ $customerDetails->customer_id }}, '{{ $customerDetails->status }}')">
                                            <i class="fas fa-ban me-1"></i> Block
                                        </button>
                                    @else
                                        <button class="btn btn-success" onclick="toggleCustomerStatus({{ $customerDetails->customer_id }}, '{{ $customerDetails->status }}')">
                                            <i class="fas fa-check me-1"></i> Unblock
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Customer Information -->
                    <div class="col-md-8">
                        <div class="card card-rounded shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Customer Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-card card mb-3">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2">Contact Information</h6>
                                                <p class="mb-1"><strong>Phone:</strong> {{ $customerDetails->phone ?? 'N/A' }}</p>
                                                <p class="mb-1"><strong>Email:</strong> {{ $customerDetails->email ?? 'N/A' }}</p>
                                                <p class="mb-0"><strong>Address:</strong> {{ $customerDetails->address ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-card card mb-3">
                                            <div class="card-body">
                                                <h6 class="text-muted mb-2">Account Details</h6>
                                                <p class="mb-1"><strong>Max Credit Limit:</strong> ₱{{ number_format($customerDetails->max_credit_limit, 2) }}</p>
                                                <p class="mb-1"><strong>Created At:</strong> {{ \Carbon\Carbon::parse($customerDetails->created_at)->format('M d, Y h:i A') }}</p>
                                                <p class="mb-0"><strong>Created By:</strong> {{ $customerDetails->created_by }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Credits -->
                        <div class="card card-rounded shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Recent Credits
                                    <small class="text-muted ms-2">Showing last 3 credits</small>
                                </h5>
                            </div>
                            <div class="card-body">
                                @forelse($recentCredits as $credit)
                                    <div class="border-bottom pb-3 mb-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-1">Credit #{{ $credit->id }}</h6>
                                                <p class="text-muted mb-1">
                                                    <small>{{ \Carbon\Carbon::parse($credit->created_at)->format('M d, Y h:i A') }}</small>
                                                </p>
                                                <p class="mb-0">
                                                    <small>Cashier: {{ $credit->cashier->name ?? 'Unknown' }}</small>
                                                </p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <p class="mb-1"><strong>Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</p>
                                                <p class="mb-1"><strong>Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</p>
                                                <p class="mb-0">
                                                    <strong class="{{ $credit->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                        Balance: ₱{{ number_format($credit->remaining_balance, 2) }}
                                                    </strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center">No credit history found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Quick Credit Information -->
                    <div class="col-md-4">
                        <div class="card card-rounded shadow-sm mb-4 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Quick Credit Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="credit-summary mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Total Credits:</span>
                                        <strong>{{ $customerDetails->total_credits }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Total Amount:</span>
                                        <strong>₱{{ number_format($customerDetails->total_credit, 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>Total Paid:</span>
                                        <strong>₱{{ number_format($customerDetails->total_paid, 2) }}</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Outstanding Balance:</span>
                                        <strong class="{{ $customerDetails->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                                            ₱{{ number_format($customerDetails->outstanding_balance, 2) }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <span class="badge bg-{{ $customerDetails->credit_status == 'Fully Paid' ? 'success' : ($customerDetails->credit_status == 'Good Standing' ? 'info' : 'warning') }} fs-6">
                                        {{ $customerDetails->credit_status }}
                                    </span>
                                </div>

                                @if($customerDetails->last_credit_date)
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            Last Credit: {{ \Carbon\Carbon::parse($customerDetails->last_credit_date)->format('M d, Y') }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card card-rounded shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>Quick Actions
                                </h5>
                                <small class="text-muted d-block mt-1">Use quick actions for common tasks. For detailed history, open Full Credit History.</small>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" onclick="addCredit()">
                                        <i class="fas fa-plus me-1"></i> Add Credit
                                    </button>
                                    @if($customerDetails->outstanding_balance > 0)
                                        <button class="btn btn-info" onclick="makePayment()">
                                            <i class="fas fa-money-bill me-1"></i> Make Payment
                                        </button>
                                    @else
                                        <button class="btn btn-info" disabled title="No outstanding balance">
                                            <i class="fas fa-money-bill me-1"></i> Make Payment
                                        </button>
                                        <div class="text-center text-muted small mt-1">No Outstanding Balance</div>
                                    @endif
                                    <button class="btn btn-outline-secondary" onclick="viewFullHistory()">
                                        <i class="fas fa-list me-1"></i> Full Credit History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editCustomer() {
    Swal.fire({
        title: 'Edit Customer Information',
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_full_name" value="{{ $customerDetails->full_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit_phone" value="{{ $customerDetails->phone ?? '' }}" placeholder="Enter phone number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" value="{{ $customerDetails->email ?? '' }}" placeholder="Enter email address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" rows="3" placeholder="Enter address">{{ $customerDetails->address ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Max Credit Limit</label>
                            <input type="number" class="form-control" id="edit_max_credit_limit" value="{{ $customerDetails->max_credit_limit ?? 0 }}" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Save Changes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const fullName = document.getElementById('edit_full_name').value.trim();
            const phone = document.getElementById('edit_phone').value.trim();
            const email = document.getElementById('edit_email').value.trim();
            const address = document.getElementById('edit_address').value.trim();
            const maxCreditLimit = parseFloat(document.getElementById('edit_max_credit_limit').value) || 0;
            
            if (!fullName) {
                Swal.showValidationMessage('Full name is required');
                return false;
            }
            
            return {
                full_name: fullName,
                phone: phone || null,
                email: email || null,
                address: address || null,
                max_credit_limit: maxCreditLimit
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const updateUrl = `{{ route('superadmin.admin.customers.update', $customerDetails->customer_id) }}`;
            console.log('Update URL:', updateUrl);
            
            fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                // Check if response is ok and is JSON
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Updated',
                        text: 'Customer information has been updated successfully.',
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
                    text: 'An error occurred while updating customer information.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
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

function addCredit() {
    Swal.fire({
        title: 'Add Credit',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Credit Amount</label>
                    <input type="number" class="form-control" id="credit_amount" step="0.01" min="0.01" placeholder="Enter credit amount" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Credit Type</label>
                    <select class="form-control" id="credit_type" required>
                        <option value="">Select credit type</option>
                        <option value="cash">Cash</option>
                        <option value="grocery">Grocery</option>
                        <option value="electronics">Electronics</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="credit_date" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="credit_notes" rows="3" placeholder="Add any notes about this credit"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Add Credit',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const amount = parseFloat(document.getElementById('credit_amount').value);
            const creditType = document.getElementById('credit_type').value;
            const creditDate = document.getElementById('credit_date').value;
            const notes = document.getElementById('credit_notes').value.trim();
            
            if (!amount || amount <= 0) {
                Swal.showValidationMessage('Please enter a valid amount');
                return false;
            }
            
            if (!creditType) {
                Swal.showValidationMessage('Please select a credit type');
                return false;
            }
            
            if (!creditDate) {
                Swal.showValidationMessage('Please select a due date');
                return false;
            }
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            if (creditDate < today) {
                Swal.showValidationMessage('Due date cannot be in the past');
                return false;
            }
            
            return {
                customer_id: '{{ $customerDetails->customer_id }}',
                credit_amount: amount,
                credit_type: creditType,
                date: creditDate,
                notes: notes || null,
                sale_id: null // Direct credits don't have sale_id
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/superadmin/admin/credits', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                // Log the response status and text for debugging
                console.log('Add Credit - Response status:', response.status);
                console.log('Add Credit - Request data:', result.value);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Add Credit - Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Add Credit - Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Credit Added',
                        text: 'Credit has been added successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    let errorMessage = data.message || 'Failed to add credit.';
                    
                    // Show validation errors if they exist
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage = errorMessages.join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Add Failed',
                        text: errorMessage,
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding credit.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function makePayment() {
    const outstandingBalance = {{ $customerDetails->outstanding_balance }};
    
    Swal.fire({
        title: 'Make Payment',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Payment Amount</label>
                    <input type="number" class="form-control" id="payment_amount" step="0.01" min="0.01" max="${outstandingBalance}" placeholder="Enter payment amount" required>
                    <small class="text-muted">Outstanding Balance: ₱${outstandingBalance.toFixed(2)}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="payment_notes" rows="3" placeholder="Add any notes about this payment"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Make Payment',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const amount = parseFloat(document.getElementById('payment_amount').value);
            const paymentMethod = document.getElementById('payment_method').value;
            const notes = document.getElementById('payment_notes').value.trim();
            
            if (!amount || amount <= 0) {
                Swal.showValidationMessage('Please enter a valid amount');
                return false;
            }
            
            if (amount > outstandingBalance) {
                Swal.showValidationMessage('Payment amount cannot exceed outstanding balance');
                return false;
            }
            
            if (!paymentMethod) {
                Swal.showValidationMessage('Please select a payment method');
                return false;
            }
            
            return {
                customer_id: {{ $customerDetails->customer_id }},
                payment_amount: amount,
                payment_method: paymentMethod,
                notes: notes || null
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/superadmin/admin/customers/make-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Recorded',
                        text: 'Payment has been recorded successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'Failed to record payment.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while recording payment.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function viewFullHistory() {
    Swal.fire({
        title: 'Credit History',
        text: 'Full credit history view coming soon!',
        icon: 'info',
        confirmButtonColor: '#2563eb'
    });
}
</script>

</html>
