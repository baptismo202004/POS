@extends('layouts.app')

@section('content')
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
                <button class="btn btn-info" onclick="showCreditLimits()">
                    <i class="fas fa-chart-bar"></i> Credit Limits
                </button>
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
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Credit Amount</th>
                            <th>Paid Amount</th>
                            <th>Remaining Balance</th>
                            <th>Credit Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($credits as $credit)
                            <tr>
                                <td>{{ $credit->created_at->format('M d, Y') }}</td>
                                <td>{{ $credit->customer_name ?? 'Walk-in Customer' }}</td>
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
    // Force refresh to avoid caching issues
    console.log('Credits index script loaded - v2');
    
    function viewCredit(creditId) {
        console.log('Viewing credit:', creditId);
        window.open(`/superadmin/admin/credits/${creditId}`, '_blank');
    }
    
    function makePayment(creditId, remainingBalance) {
        document.getElementById('payment_credit_id').value = creditId;
        document.getElementById('remaining_balance_display').textContent = remainingBalance.toFixed(2);
        document.getElementById('payment_amount').max = remainingBalance;
        document.getElementById('payment_amount').value = remainingBalance.toFixed(2); // Auto-populate payment amount
        
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
        
        fetch(`/superadmin/admin/credits/${creditId}/payment`, {
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

    function showCreditLimits() {
        fetch('/superadmin/admin/credits/credit-limits-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCreditLimitsModal(data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load credit limits data.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while loading credit limits.'
                });
            });
    }

    function displayCreditLimitsModal(data) {
        let customerHtml = '';
        if (data.creditsByCustomer && data.creditsByCustomer.length > 0) {
            data.creditsByCustomer.forEach((customer, index) => {
                const progressPercentage = customer.total_credit_limit > 0 
                    ? (customer.total_paid / customer.total_credit_limit) * 100 
                    : 0;
                const statusClass = customer.total_remaining <= 0 ? 'success' : 
                    (progressPercentage >= 80 ? 'warning' : 'danger');
                const statusText = customer.total_remaining <= 0 ? 'Fully Paid' : 
                    (progressPercentage >= 80 ? 'Good Standing' : 'Outstanding');
                
                const maxCreditLimit = customer.max_credit_limit || 0;
                const canStillCredit = maxCreditLimit > 0 && (customer.total_credit_limit < maxCreditLimit);
                const limitStatus = maxCreditLimit > 0 ? 
                    (customer.total_credit_limit >= maxCreditLimit ? 'Limit Reached' : 'Available') : 
                    'No Limit Set';
                
                customerHtml += `
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input customer-checkbox" 
                                   id="customer_${index}" data-customer="${customer.customer_name}" 
                                   data-current-limit="${maxCreditLimit}">
                        </td>
                        <td><strong>${customer.customer_name}</strong></td>
                        <td>${customer.total_credits}</td>
                        <td>₱${parseFloat(customer.total_credit_limit).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control form-control-sm limit-input" 
                                       id="limit_input_${index}" 
                                       value="${maxCreditLimit}" 
                                       min="0" 
                                       step="100"
                                       data-customer="${customer.customer_name}"
                                       style="width: 120px;">
                            </div>
                        </td>
                        <td>₱${parseFloat(customer.total_paid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>₱${parseFloat(customer.total_remaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-${progressPercentage >= 80 ? 'success' : (progressPercentage >= 50 ? 'warning' : 'danger')}" 
                                     style="width: ${progressPercentage}%"></div>
                            </div>
                            <small>${progressPercentage.toFixed(1)}% paid</small>
                        </td>
                        <td>
                            <span class="badge bg-${statusClass}">${statusText}</span>
                            ${maxCreditLimit > 0 ? `<br><small class="text-muted">${limitStatus}</small>` : ''}
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" 
                                    onclick="editCustomerLimit('${customer.customer_name}', ${maxCreditLimit}, ${index})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            customerHtml = `
                <tr>
                    <td colspan="10" class="text-center py-3">
                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">No credit records found</p>
                    </td>
                </tr>
            `;
        }

        const modalHtml = `
            <div class="modal fade" id="creditLimitsModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-chart-bar me-2"></i>Credit Limits Management
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Statistics Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary">${data.totalCustomers}</h5>
                                            <small class="text-muted">Total Customers</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-success">
                                        <div class="card-body text-center">
                                            <h5 class="text-success">₱${parseFloat(data.totalCreditLimit).toLocaleString('en-PH', {minimumFractionDigits: 2})}</h5>
                                            <small class="text-muted">Total Credit Limit</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-warning">
                                        <div class="card-body text-center">
                                            <h5 class="text-warning">₱${parseFloat(data.totalPaid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</h5>
                                            <small class="text-muted">Total Paid</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-danger">
                                        <div class="card-body text-center">
                                            <h5 class="text-danger">₱${parseFloat(data.totalRemaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}</h5>
                                            <small class="text-muted">Total Remaining</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bulk Actions -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <button class="btn btn-sm btn-success" id="updateSelectedBtn" disabled>
                                        <i class="fas fa-save"></i> Update Selected
                                    </button>
                                    <button class="btn btn-sm btn-secondary" id="resetSelectedBtn" disabled>
                                        <i class="fas fa-undo"></i> Reset Selected
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <span id="selectedCount">0</span> customers selected
                                </small>
                            </div>
                            
                            <!-- Customer Credit Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">Select</th>
                                            <th>Customer Name</th>
                                            <th>Total Credits</th>
                                            <th>Credit Limit</th>
                                            <th>Max Credit Limit</th>
                                            <th>Paid Amount</th>
                                            <th>Remaining</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${customerHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present
        const existingModal = document.getElementById('creditLimitsModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add modal to body and show it
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('creditLimitsModal'));
        modal.show();
        
        // Initialize event listeners
        initializeCreditLimitsModal();
        
        // Properly cleanup modal when hidden
        const modalElement = document.getElementById('creditLimitsModal');
        modalElement.addEventListener('hidden.bs.modal', function () {
            modalElement.remove();
        });
    }

    function initializeCreditLimitsModal() {
        // Individual checkbox functionality only
        const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
        const updateSelectedBtn = document.getElementById('updateSelectedBtn');
        const resetSelectedBtn = document.getElementById('resetSelectedBtn');
        const selectedCount = document.getElementById('selectedCount');
        
        // Individual checkbox changes
        customerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedButtons();
            });
        });
        
        // Update selected buttons
        function updateSelectedButtons() {
            const selectedCustomers = document.querySelectorAll('.customer-checkbox:checked');
            const hasSelection = selectedCustomers.length > 0;
            
            if (updateSelectedBtn) updateSelectedBtn.disabled = !hasSelection;
            if (resetSelectedBtn) resetSelectedBtn.disabled = !hasSelection;
            if (selectedCount) selectedCount.textContent = selectedCustomers.length;
        }
        
        // Update selected button click
        if (updateSelectedBtn) {
            updateSelectedBtn.addEventListener('click', function() {
                updateSelectedLimits();
            });
        }
        
        // Reset selected button click
        if (resetSelectedBtn) {
            resetSelectedBtn.addEventListener('click', function() {
                resetSelectedLimits();
            });
        }
        
        // Initialize buttons state
        updateSelectedButtons();
    }

    function editCustomerLimit(customerName, currentLimit, index) {
        const input = document.getElementById(`limit_input_${index}`);
        if (input) {
            input.focus();
            input.select();
        }
    }

    function updateSelectedLimits() {
        const selectedCustomers = document.querySelectorAll('.customer-checkbox:checked');
        const updates = [];
        
        selectedCustomers.forEach(checkbox => {
            const customerName = checkbox.dataset.customer;
            const inputId = `limit_input_${checkbox.id.replace('customer_', '')}`;
            const input = document.getElementById(inputId);
            
            if (input) {
                const newLimit = parseFloat(input.value) || 0;
                if (newLimit >= 0) {
                    updates.push({ customerName, newLimit });
                }
            }
        });
        
        if (updates.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Valid Updates',
                text: 'Please enter valid credit limits for selected customers.'
            });
            return;
        }
        
        // Update each customer
        let completed = 0;
        let errors = 0;
        
        updates.forEach(update => {
            updateCreditLimit(update.customerName, update.newLimit, function(success) {
                completed++;
                if (!success) errors++;
                
                if (completed === updates.length) {
                    if (errors === 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: `Updated ${updates.length} customer credit limits.`,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            showCreditLimits(); // Refresh the modal
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Partial Success',
                            text: `Updated ${updates.length - errors} of ${updates.length} customer credit limits.`
                        });
                    }
                }
            });
        });
    }

    function resetSelectedLimits() {
        const selectedCustomers = document.querySelectorAll('.customer-checkbox:checked');
        
        selectedCustomers.forEach(checkbox => {
            const currentLimit = parseFloat(checkbox.dataset.currentLimit) || 0;
            const inputId = `limit_input_${checkbox.id.replace('customer_', '')}`;
            const input = document.getElementById(inputId);
            
            if (input) {
                input.value = currentLimit;
            }
        });
        
        Swal.fire({
            icon: 'info',
            title: 'Reset Complete',
            text: `Reset ${selectedCustomers.length} customer limits to original values.`,
            timer: 1500,
            showConfirmButton: false
        });
    }

    function updateCreditLimit(customerName, newLimit, callback) {
        const formData = new FormData();
        formData.append('customer_name', customerName);
        formData.append('max_credit_limit', newLimit);

        fetch('/superadmin/admin/credits/update-credit-limit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (typeof callback === 'function') {
                callback(data.success);
            } else if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    showCreditLimits();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to update credit limit.'
                });
            }
        })
        .catch(error => {
            if (typeof callback === 'function') {
                callback(false);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the credit limit.'
                });
            }
        });
    }
</script>
@endsection
