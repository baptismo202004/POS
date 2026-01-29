<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credits Management - SuperAdmin</title>

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
                <h4 class="m-0">Credits Management</h4>
                <p class="mb-0 text-muted">Overview of your credit transactions and outstanding balances</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('superadmin.admin.credits.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Credit
                </a>
                <a href="{{ route('superadmin.admin.sales.index') }}" class="btn btn-outline-primary">Go to Sales</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Credits</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayCredits->total_credit_amount ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Outstanding Balance</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayCredits->total_outstanding ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month's Credits</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlyCredits->total_credit_amount ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue Credits</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($overdueCredits->total_overdue_amount ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Credits Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Recent Credits</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-success" onclick="openPaymentModal()">
                    <i class="fas fa-money-bill-wave"></i> Record Payment
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Created Date</th>
                            <th>Customer</th>
                            <th>Credit Amount</th>
                            <th>Paid Amount</th>
                            <th>Remaining Balance</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credits as $credit)
                            <tr>
                                <td>{{ $credit->created_at->format('M d, Y') }}</td>
                                <td>{{ $credit->customer->name ?? 'Walk-in Customer' }}</td>
                                <td>₱{{ number_format($credit->credit_amount, 2) }}</td>
                                <td>₱{{ number_format($credit->paid_amount, 2) }}</td>
                                <td>₱{{ number_format($credit->remaining_balance, 2) }}</td>
                                <td>{{ $credit->date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $credit->status == 'active' ? 'primary' : ($credit->status == 'paid' ? 'success' : 'danger') }}">
                                        {{ ucfirst($credit->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewCredit({{ $credit->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($credit->remaining_balance > 0)
                                            <button class="btn btn-outline-success" onclick="makePayment({{ $credit->id }}, {{ $credit->remaining_balance }})">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No credits found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($credits->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $credits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Credit Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="credit_id" id="payment_credit_id">
                    
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount (₱)</label>
                        <input type="number" class="form-control" name="payment_amount" id="payment_amount" step="0.01" min="0.01" required>
                        <small class="text-muted">Remaining balance: ₱<span id="remaining_balance_display">0.00</span></small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="">Select payment method...</option>
                            <option value="cash">Cash</option>
                          
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function viewCredit(creditId) {
        window.open(`/admin/credits/${creditId}`, '_blank');
    }
    
    function makePayment(creditId, remainingBalance) {
        document.getElementById('payment_credit_id').value = creditId;
        document.getElementById('remaining_balance_display').textContent = remainingBalance.toFixed(2);
        document.getElementById('payment_amount').max = remainingBalance;
        document.getElementById('payment_amount').value = remainingBalance.toFixed(2);
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }
    
    function openPaymentModal() {
        // This would open a modal to select a credit first
        Swal.fire({
            icon: 'info',
            title: 'Select Credit',
            text: 'Please select a credit from the table to record a payment.',
            confirmButtonColor: '#2563eb'
        });
    }
    
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const creditId = document.getElementById('payment_credit_id').value;
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Processing Payment...',
            html: 'Please wait while we record your payment.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/admin/credits/${creditId}/payment`, {
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
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred while processing the payment.'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while processing the payment.'
            });
        });
    });
</script>
            </div>
        </main>
    </div>
</body>
</html>
