<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Credit Details - {{ $customer->full_name }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
        .header-section {
            background: linear-gradient(135deg, var(--theme-color) 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
        }
        .credit-card {
            border-left: 4px solid var(--theme-color);
            margin-bottom: 1.5rem;
        }
        .payment-item {
            border-left: 3px solid #28a745;
            margin-bottom: 0.5rem;
            padding-left: 1rem;
        }
        .sticky-summary {
            position: sticky;
            top: 20px;
            z-index: 100;
        }
        .date-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .date-header {
            border-bottom: 2px solid var(--theme-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
            color: var(--theme-color);
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
                    <a href="{{ route('superadmin.admin.credits.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Credit Management
                    </a>
                </div>

                <!-- SECTION 1: Header (Strict Operational Scope) -->
                <div class="card card-rounded shadow-sm mb-4">
                    <div class="header-section">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Customer: {{ $customer->full_name }}</h2>
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Active Credits:</strong> {{ $activeSummary->active_credits }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Active Credit Amount:</strong> ₱{{ number_format($activeSummary->active_credit_amount, 2) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Total Paid (Active):</strong> ₱{{ number_format($activeSummary->total_paid_active, 2) }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Outstanding Balance:</strong> ₱{{ number_format($activeSummary->outstanding_balance, 2) }}</p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-{{ $status == 'Good Standing' ? 'success' : 'warning' }} fs-6">
                                        Status: {{ $status }}
                                    </span>
                                    <small class="text-white-50 ms-2">Operational View - Active Credits Only</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="sticky-summary">
                                    <div class="alert alert-{{ $activeSummary->outstanding_balance > 0 ? 'danger' : 'success' }} mb-0">
                                        <h6 class="mb-1">Outstanding Balance</h6>
                                        <h4 class="mb-0">₱{{ number_format($activeSummary->outstanding_balance, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Active Credits (Primary Operational View) -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Active Credits (Actionable)
                                </h5>
                                <small class="text-white-50">Credits requiring attention or payment</small>
                            </div>
                            <div class="card-body">
                                @forelse($activeCredits as $date => $dateCredits)
                                    <div class="date-group">
                                        <div class="date-header">
                                            📅 {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                                        </div>
                                        
                                        @foreach($dateCredits as $credit)
                                            <div class="card credit-card">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-8">
                                                            <h6 class="mb-2">Credit #{{ $credit->id }}</h6>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Created by:</strong> {{ $credit->cashier->name ?? 'Admin' }}</p>
                                                                    <p class="mb-1"><strong>Credit Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</p>
                                                                    <p class="mb-1"><strong>Total Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Remaining:</strong> 
                                                                        <span class="{{ $credit->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                            ₱{{ number_format($credit->remaining_balance, 2) }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="mb-1"><strong>Status:</strong> 
                                                                        <span class="badge bg-{{ $credit->status == 'paid' ? 'success' : ($credit->status == 'active' ? 'warning' : 'secondary') }}">
                                                                            {{ ucfirst($credit->status) }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="mb-1"><strong>Created:</strong> {{ \Carbon\Carbon::parse($credit->created_at)->format('M d, Y h:i A') }}</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Payments (Expandable) -->
                                                            @if($credit->payments->count() > 0)
                                                                <div class="mt-3">
                                                                    <button class="btn btn-sm btn-outline-info" onclick="togglePayments({{ $credit->id }})">
                                                                        <i class="fas fa-eye me-1"></i> View Payments ({{ $credit->payments->count() }})
                                                                    </button>
                                                                    
                                                                    <div id="payments-{{ $credit->id }}" class="mt-3" style="display: none;">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <h6 class="mb-3">Payments for Credit #{{ $credit->id }}</h6>
                                                                            @foreach($credit->payments as $payment)
                                                                                <div class="payment-item">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <strong>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}</strong>
                                                                                            <span class="text-muted ms-2">by {{ $payment->cashier->name ?? 'Admin' }}</span>
                                                                                            @if($payment->notes)
                                                                                                <br><small class="text-muted">{{ $payment->notes }}</small>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div>
                                                                                            <strong>₱{{ number_format($payment->payment_amount, 2) }}</strong>
                                                                                            <br><small class="text-muted">{{ $payment->payment_method }}</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                            
                                                                            <hr>
                                                                            <div class="d-flex justify-content-between">
                                                                                <strong>Total Paid:</strong>
                                                                                <strong>₱{{ number_format($credit->paid_amount, 2) }}</strong>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between">
                                                                                <span>Remaining:</span>
                                                                                <strong class="{{ $credit->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                                    ₱{{ number_format($credit->remaining_balance, 2) }}
                                                                                </strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="mt-3">
                                                                    <span class="text-muted">No payments yet</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="col-md-4 text-end">
                                                            <div class="btn-group" role="group">
                                                                @if($credit->remaining_balance > 0)
                                                                    <button class="btn btn-sm btn-success" onclick="addPayment({{ $credit->id }}, {{ $credit->remaining_balance }})">
                                                                        <i class="fas fa-plus me-1"></i> Add Payment
                                                                    </button>
                                                                @endif
                                                                @if($credit->payments->count() > 0)
                                                                    <button class="btn btn-sm btn-outline-info" onclick="togglePayments({{ $credit->id }})">
                                                                        <i class="fas fa-list me-1"></i> View Payments
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <h5 class="text-muted">No Active Credits</h5>
                                        <p class="text-muted">This customer has no outstanding credits requiring attention.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Recently Paid Credits (Secondary Context) -->
                @if($recentlyPaidCredits->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-check-circle me-2"></i>Recently Paid Credits (Context Only)
                                    </h5>
                                    <small class="text-muted">Last 3 paid credits - shown for reference only</small>
                                </div>
                                <div class="card-body">
                                    @foreach($recentlyPaidCredits as $date => $dateCredits)
                                        <div class="date-group">
                                            <div class="date-header">
                                                📅 {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                                            </div>
                                            
                                            @foreach($dateCredits as $credit)
                                                <div class="card credit-card opacity-75">
                                                    <div class="card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-8">
                                                                <h6 class="mb-2">Credit #{{ $credit->id }}</h6>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Created by:</strong> {{ $credit->cashier->name ?? 'Admin' }}</p>
                                                                        <p class="mb-1"><strong>Credit Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</p>
                                                                        <p class="mb-1"><strong>Total Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Status:</strong> 
                                                                            <span class="badge bg-success">Paid</span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Paid Date:</strong> {{ \Carbon\Carbon::parse($credit->updated_at)->format('M d, Y') }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-md-4 text-end">
                                                                <button class="btn btn-sm btn-outline-secondary" onclick="viewCreditDetails({{ $credit->id }})">
                                                                    <i class="fas fa-eye me-1"></i> View Details
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- SECTION 4: Full History Access -->
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="btn btn-outline-primary btn-lg" onclick="viewFullHistory({{ $customer->id }})">
                            <i class="fas fa-history me-2"></i> View Full Credit History
                        </button>
                        <p class="text-muted mt-2">Access complete credit and payment history with filtering options</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function togglePayments(creditId) {
    const paymentsDiv = document.getElementById(`payments-${creditId}`);
    if (paymentsDiv.style.display === 'none') {
        paymentsDiv.style.display = 'block';
    } else {
        paymentsDiv.style.display = 'none';
    }
}

function addPayment(creditId, maxAmount) {
    Swal.fire({
        title: 'Add Payment',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Payment Amount (Max: ₱${maxAmount.toFixed(2)})</label>
                    <input type="number" class="form-control" id="payment_amount" step="0.01" min="0.01" max="${maxAmount}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="payment_notes" rows="2" placeholder="Add any notes about this payment"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Add Payment',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const amount = parseFloat(document.getElementById('payment_amount').value);
            if (!amount || amount <= 0 || amount > maxAmount) {
                Swal.showValidationMessage(`Please enter a valid amount between ₱0.01 and ₱${maxAmount.toFixed(2)}`);
                return false;
            }
            
            return {
                payment_amount: amount,
                payment_method: document.getElementById('payment_method').value,
                notes: document.getElementById('payment_notes').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const paymentData = result.value;
            
            fetch(`/superadmin/admin/credits/${creditId}/make-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Added',
                        text: 'Payment has been recorded successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'Failed to add payment.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding payment.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function viewCreditDetails(creditId) {
    window.location.href = `/superadmin/admin/credits/${creditId}`;
}

function viewFullHistory(customerId) {
    window.location.href = `/superadmin/admin/credits/customer/${customerId}/full-history`;
}
</script>

</html>
