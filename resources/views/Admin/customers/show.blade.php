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
                    <a href="{{ route('superadmin.admin.customers.index') }}" class="btn btn-outline-secondary">
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
        title: 'Edit Customer',
        text: 'Customer editing functionality coming soon!',
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

function addCredit() {
    Swal.fire({
        title: 'Add Credit',
        text: 'Credit addition functionality coming soon!',
        icon: 'info',
        confirmButtonColor: '#2563eb'
    });
}

function makePayment() {
    Swal.fire({
        title: 'Make Payment',
        text: 'Payment functionality coming soon!',
        icon: 'info',
        confirmButtonColor: '#2563eb'
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
