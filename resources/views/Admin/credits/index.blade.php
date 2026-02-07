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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Credits</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Customer Name</th>
                            <th>Total Credits</th>
                            <th>Total Credit Amount</th>
                            <th>Total Paid Amount</th>
                            <th>Remaining Balance</th>
                            <th>Last Credit Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>{{ $customer->branch_name ?? 'N/A' }}</td>
                                <td><strong>{{ $customer->full_name }}</strong></td>
                                <td>{{ $customer->credit_giver_total }}</td>
                                <td>₱{{ number_format($customer->total_credit, 2) }}</td>
                                <td>₱{{ number_format($customer->total_paid, 2) }}</td>
                                <td>₱{{ number_format($customer->outstanding_balance, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($customer->last_credit_date)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $customer->status == 'Fully Paid' ? 'success' : ($customer->status == 'Good Standing' ? 'warning' : 'danger') }}">
                                        {{ $customer->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="viewCustomerCredits({{ $customer->customer_id }})">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                        @if($customer->outstanding_balance > 0)
                                            <button class="btn btn-outline-success" onclick="makeCustomerPayment({{ $customer->customer_id }}, {{ $customer->outstanding_balance }})">
                                                <i class="fas fa-money-bill-wave"></i> Payment
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No customers with credits found.</td>
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

    // Add CSS for highlighting changed fields
    const style = document.createElement('style');
    style.textContent = `
        .limit-input.changed {
            border-color: #28a745;
            background-color: #f8f9fa;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            font-weight: 500;
        }
        .limit-input.changed:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .swal2-popup {
            font-size: 14px;
        }
        .swal2-popup strong {
            color: #333;
        }
    `;
    document.head.appendChild(style);

    // Global modal focus management to fix aria-hidden accessibility issues
    document.addEventListener('DOMContentLoaded', function() {
        // Handle all modal dismissals to prevent aria-hidden focus conflicts
        document.addEventListener('click', function(e) {
            if (e.target.hasAttribute('data-bs-dismiss') && e.target.getAttribute('data-bs-dismiss') === 'modal') {
                // Remove focus from the button before dismissing modal
                e.target.blur();
                // Move focus to body to prevent focus trapping
                document.body.focus();
            }
        });

        // Handle Bootstrap modal hide events
        document.addEventListener('hide.bs.modal', function(e) {
            const modal = e.target;
            const activeElement = document.activeElement;
            
            // If active element is within the modal, remove focus before hiding
            if (activeElement && modal.contains(activeElement)) {
                activeElement.blur();
                document.body.focus();
            }
        });
    });
    
    function viewCredit(creditId) {
        console.log('Viewing credit:', creditId);
        window.location.href = `/superadmin/admin/credits/${creditId}`;
    }
    
    function viewCustomerCredits(customerId) {
        console.log('Viewing customer credits:', customerId);
        window.location.href = `/superadmin/admin/credits/customer/${customerId}`;
    }
    
    function makeCustomerPayment(customerId, remainingBalance) {
        console.log('Making payment for customer:', customerId, 'Remaining:', remainingBalance);
        
        // Fetch customer's credits to show in modal
        fetch(`/superadmin/admin/credits/customer/${customerId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCreditPaymentModal(customerId, data.credits);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to fetch customer credits'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Unable to fetch customer credits'
                });
            });
    }
    
    function makePayment(creditId, remainingBalance) {
        console.log('Making payment for credit:', creditId, 'Remaining balance:', remainingBalance);
        
        // Ensure remainingBalance is a number
        const paymentAmount = parseFloat(remainingBalance);
        console.log('Parsed payment amount:', paymentAmount);
        
        // Set credit ID
        document.getElementById('payment_credit_id').value = creditId;
        
        // Set remaining balance display
        document.getElementById('remaining_balance_display').textContent = paymentAmount.toFixed(2);
        
        // Set payment amount input properties
        const paymentInput = document.getElementById('payment_amount');
        paymentInput.min = 0.01;
        paymentInput.value = paymentAmount.toFixed(2); // Auto-populate payment amount
        
        // Remove max validation to allow any payment amount
        paymentInput.removeAttribute('max');
        paymentInput.removeAttribute('max'); // Double remove
        
        console.log('Payment input value set to:', paymentInput.value);
        console.log('Payment input max removed');
        console.log('Remaining balance display:', document.getElementById('remaining_balance_display').textContent);
        
        // Update modal title to indicate individual payment
        document.getElementById('paymentModalLabel').textContent = 'Record Credit Payment';
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }
    
    function showCreditPaymentModal(customerId, credits) {
        console.log('=== SHOW CREDIT PAYMENT MODAL DEBUG ===');
        console.log('Customer ID:', customerId);
        console.log('Credits received:', credits);
        
        let creditsHtml = '';
        let totalOutstanding = 0;
        
        if (credits.length === 0) {
            creditsHtml = '<p class="text-muted">No active credits found for this customer.</p>';
        } else {
            credits.forEach(credit => {
                console.log('Processing credit:', credit);
                console.log('Credit remaining balance:', credit.remaining_balance);
                console.log('Type of remaining_balance:', typeof credit.remaining_balance);
                
                if (credit.remaining_balance > 0) {
                    const balance = parseFloat(credit.remaining_balance);
                    totalOutstanding += balance;
                    console.log('Added to total:', balance, 'New total:', totalOutstanding);
                    
                    creditsHtml += `
                        <div class="card mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">Credit #${credit.id}</h6>
                                        <small class="text-muted">Amount: ₱${credit.credit_amount}</small><br>
                                        <small class="text-muted">Date: ${new Date(credit.date).toLocaleDateString()}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-danger">₱${credit.remaining_balance}</strong><br>
                                        <small class="text-muted">Outstanding</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
        }
        
        console.log('Final total outstanding:', totalOutstanding);
        console.log('Type of totalOutstanding:', typeof totalOutstanding);
        
        const modalHtml = `
            <div style="max-height: 400px; overflow-y: auto;">
                ${creditsHtml || '<p class="text-muted">No outstanding credits found.</p>'}
                ${totalOutstanding > 0 ? `
                    <div class="alert alert-info mt-3">
                        <strong>Total Outstanding Balance: ₱${totalOutstanding.toFixed(2)}</strong>
                        <br><small>Payment will be automatically distributed across all outstanding credits.</small>
                    </div>
                ` : ''}
            </div>
        `;
        
        console.log('=== END SHOW CREDIT PAYMENT MODAL DEBUG ===');
        
        Swal.fire({
            title: 'Customer Credits Summary',
            html: modalHtml,
            showConfirmButton: totalOutstanding > 0,
            showCancelButton: true,
            confirmButtonText: totalOutstanding > 0 ? `Pay Total (₱${totalOutstanding.toFixed(2)})` : 'Close',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            width: '600px'
        }).then((result) => {
            if (result.isConfirmed && totalOutstanding > 0) {
                // Open payment modal with total outstanding balance
                makeTotalPayment(customerId, totalOutstanding, credits);
            }
        });
    }
    
    function makeTotalPayment(customerId, totalAmount, credits) {
        // Create a summary for the payment
        const creditIds = credits.filter(c => c.remaining_balance > 0).map(c => c.id);
        
        console.log('=== MAKE TOTAL PAYMENT DEBUG ===');
        console.log('Customer ID:', customerId);
        console.log('Total Amount:', totalAmount);
        console.log('Credits to pay:', creditIds);
        console.log('Type of totalAmount:', typeof totalAmount);
        
        // Ensure totalAmount is a number
        const paymentAmount = parseFloat(totalAmount);
        console.log('Parsed payment amount:', paymentAmount);
        
        // Set up payment modal for total payment
        const creditIdInput = document.getElementById('payment_credit_id');
        const remainingBalanceDisplay = document.getElementById('remaining_balance_display');
        const paymentInput = document.getElementById('payment_amount');
        
        creditIdInput.value = creditIds.join(','); // Multiple credit IDs
        remainingBalanceDisplay.textContent = paymentAmount.toFixed(2);
        
        // Set payment amount without max validation
        paymentInput.min = 0.01;
        paymentInput.value = paymentAmount.toFixed(2); // Auto-populate with total amount
        
        // Remove max validation to allow any payment amount
        paymentInput.removeAttribute('max');
        paymentInput.removeAttribute('max'); // Double remove
        
        console.log('Payment input value set to:', paymentInput.value);
        console.log('Payment input max removed');
        console.log('Remaining balance display:', remainingBalanceDisplay.textContent);
        
        // Update modal title to indicate total payment
        document.getElementById('paymentModalLabel').textContent = 'Record Total Payment';
        
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
        
        console.log('=== END MAKE TOTAL PAYMENT DEBUG ===');
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
        const paymentAmount = document.getElementById('payment_amount').value;
        const remainingBalanceDisplay = document.getElementById('remaining_balance_display').textContent;
        
        console.log('Submitting payment:');
        console.log('Credit ID:', creditId);
        console.log('Payment Amount:', paymentAmount);
        console.log('Remaining Balance Display:', remainingBalanceDisplay);
        
        const formData = new FormData(this);
        
        // Check if this is a multi-credit payment (credit IDs are comma-separated)
        const isMultiCredit = creditId.includes(',');
        
        // Log form data
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
        
        Swal.fire({
            title: 'Processing Payment...',
            html: 'Please wait while we record your payment.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Choose the correct endpoint based on payment type
        const endpoint = isMultiCredit ? '/superadmin/admin/credits/multi-payment' : `/superadmin/admin/credits/${creditId}/payment`;
        
        // For multi-credit payments, add the credit IDs to form data
        if (isMultiCredit) {
            formData.set('credit_ids', creditId);
            // Remove credit_id field for multi-credit payments
            formData.delete('credit_id');
        }
        
        fetch(endpoint, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('Payment response:', data);
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
            console.error('Payment error:', error);
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
                const maxCreditLimit = customer.max_credit_limit || 0;
                
                // Recalculate status based on max credit limit (or 0 if none)
                const effectiveLimit = maxCreditLimit > 0 ? maxCreditLimit : customer.total_credit_limit;
                const progressPercentage = effectiveLimit > 0 
                    ? (customer.total_paid / effectiveLimit) * 100 
                    : 0;
                
                // Enhanced status calculation considering max credit limit
                let statusClass, statusText;
                
                if (customer.total_remaining <= 0) {
                    statusClass = 'success';
                    statusText = 'Fully Paid';
                } else if (maxCreditLimit > 0) {
                    // If max credit limit is set, use it for status calculation
                    const maxLimitProgress = (customer.total_credit_limit / maxCreditLimit) * 100;
                    if (maxLimitProgress >= 100) {
                        statusClass = 'danger';
                        statusText = 'Limit Reached';
                    } else if (maxLimitProgress >= 80) {
                        statusClass = 'warning';
                        statusText = 'Near Limit';
                    } else {
                        statusClass = 'success';
                        statusText = 'Available';
                    }
                } else {
                    // If no max limit set, use traditional calculation
                    statusClass = progressPercentage >= 80 ? 'warning' : 'danger';
                    statusText = progressPercentage >= 80 ? 'Good Standing' : 'Outstanding';
                }
                
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
                                       data-original-value="${maxCreditLimit}"
                                       data-customer="${customer.customer_name}"
                                       style="width: 120px;">
                            </div>
                        </td>
                        <td>₱${parseFloat(customer.total_paid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>₱${parseFloat(customer.total_remaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-${statusClass}" 
                                     style="width: ${progressPercentage}%"></div>
                            </div>
                            <small>${progressPercentage.toFixed(1)}% of ${maxCreditLimit > 0 ? 'max limit' : 'total limit'}</small>
                        </td>
                        <td>
                            <span class="badge bg-${statusClass}">${statusText}</span>
                            ${maxCreditLimit > 0 ? `<br><small class="text-muted">${limitStatus}</small>` : ''}
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${customerHtml}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <div>
                                    <button class="btn btn-sm btn-success" id="updateSelectedBtn" disabled>
                                        <i class="fas fa-save"></i> Update
                                    </button>
                                    <button class="btn btn-sm btn-secondary" id="resetSelectedBtn" disabled>
                                        <i class="fas fa-undo"></i> Reset Selected
                                    </button>
                                    <small class="text-muted ms-3">
                                        <span id="selectedCount">0</span> customers selected
                                    </small>
                                </div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
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
        
        // Handle focus management before modal hides
        modalElement.addEventListener('hide.bs.modal', function () {
            // Remove focus from any focused element within the modal before hiding
            const activeElement = document.activeElement;
            if (activeElement && modalElement.contains(activeElement)) {
                activeElement.blur();
                // Move focus to body or a safe element
                document.body.focus();
            }
        });
        
        // Handle all close buttons within this modal
        const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Remove focus before dismissing
                this.blur();
                document.body.focus();
            });
        });
        
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
            
            // Check if there are any changed inputs
            const changedInputs = document.querySelectorAll('.limit-input.changed');
            const hasChanges = changedInputs.length > 0;
            
            // Update button: enabled if there are any changes (regardless of checkbox selection)
            if (updateSelectedBtn) updateSelectedBtn.disabled = !hasChanges;
            
            // Reset button: still requires checkbox selection
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
        
        // Add change detection to all limit inputs
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('limit-input')) {
                const originalValue = parseFloat(e.target.dataset.originalValue) || 0;
                const currentValue = parseFloat(e.target.value) || 0;
                
                if (currentValue !== originalValue) {
                    e.target.classList.add('changed');
                } else {
                    e.target.classList.remove('changed');
                }
                
                // Recalculate status in real-time based on new limit
                recalculateCustomerStatus(e.target);
                
                // Update button states
                updateSelectedButtons();
            }
        });
    }

    function recalculateCustomerStatus(inputElement) {
        const customerName = inputElement.dataset.customer;
        const newMaxLimit = parseFloat(inputElement.value) || 0;
        
        // Find the row for this customer
        const row = inputElement.closest('tr');
        if (!row) return;
        
        // Get customer data from the row (we need to extract this from the existing data)
        const customerData = findCustomerData(customerName);
        if (!customerData) return;
        
        // Recalculate status based on new max limit
        const effectiveLimit = newMaxLimit > 0 ? newMaxLimit : customerData.total_credit_limit;
        const progressPercentage = effectiveLimit > 0 
            ? (customerData.total_paid / effectiveLimit) * 100 
            : 0;
        
        let statusClass, statusText;
        
        if (customerData.total_remaining <= 0) {
            statusClass = 'success';
            statusText = 'Fully Paid';
        } else if (newMaxLimit > 0) {
            // If max credit limit is set, use it for status calculation
            const maxLimitProgress = (customerData.total_credit_limit / newMaxLimit) * 100;
            if (maxLimitProgress >= 100) {
                statusClass = 'danger';
                statusText = 'Limit Reached';
            } else if (maxLimitProgress >= 80) {
                statusClass = 'warning';
                statusText = 'Near Limit';
            } else {
                statusClass = 'success';
                statusText = 'Available';
            }
        } else {
            // If no max limit set, use traditional calculation
            statusClass = progressPercentage >= 80 ? 'warning' : 'danger';
            statusText = progressPercentage >= 80 ? 'Good Standing' : 'Outstanding';
        }
        
        // Update the status badge in the row
        const statusCell = row.querySelector('td:last-child .badge');
        if (statusCell) {
            statusCell.className = `badge bg-${statusClass}`;
            statusCell.textContent = statusText;
        }
        
        // Update the progress bar
        const progressBar = row.querySelector('.progress-bar');
        const progressText = row.querySelector('.progress small');
        if (progressBar) {
            progressBar.className = `progress-bar bg-${statusClass}`;
            progressBar.style.width = `${progressPercentage}%`;
        }
        if (progressText) {
            progressText.textContent = `${progressPercentage.toFixed(1)}% of ${newMaxLimit > 0 ? 'max limit' : 'total limit'}`;
        }
    }

    function findCustomerData(customerName) {
        // This function should find the customer data from the original data
        // For now, we'll extract it from the current row
        const row = document.querySelector(`[data-customer="${customerName}"]`).closest('tr');
        if (!row) return null;
        
        const cells = row.querySelectorAll('td');
        return {
            total_credits: parseInt(cells[2].textContent) || 0,
            total_credit_limit: parseFloat(cells[3].textContent.replace(/[₱,]/g, '')) || 0,
            total_paid: parseFloat(cells[5].textContent.replace(/[₱,]/g, '')) || 0,
            total_remaining: parseFloat(cells[6].textContent.replace(/[₱,]/g, '')) || 0
        };
    }

    function editCustomerLimit(customerName, currentLimit, index) {
        const input = document.getElementById(`limit_input_${index}`);
        if (input) {
            input.focus();
            input.select();
        }
    }

    function updateSelectedLimits() {
        // Get all changed inputs, not just selected ones
        const allInputs = document.querySelectorAll('.limit-input');
        const updates = [];
        const changes = [];
        
        allInputs.forEach(input => {
            const newLimit = parseFloat(input.value) || 0;
            const originalLimit = parseFloat(input.dataset.originalValue) || 0;
            const customerName = input.dataset.customer;
            
            if (newLimit >= 0) {
                updates.push({ customerName, newLimit, originalLimit });
                
                // Track if there's actually a change
                if (newLimit !== originalLimit) {
                    changes.push({
                        customerName,
                        oldValue: originalLimit,
                        newValue: newLimit,
                        difference: newLimit - originalLimit
                    });
                }
            }
        });
        
        if (updates.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Valid Updates',
                text: 'Please enter valid credit limits for customers.'
            });
            return;
        }
        
        // If no changes detected, show message
        if (changes.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Changes Detected',
                text: 'No changes were made to the credit limits.'
            });
            return;
        }
        
        // Show confirmation dialog with changes summary
        let changesSummary = 'The following changes will be made:<br><br>';
        changes.forEach(change => {
            const changeText = change.difference > 0 ? 'increased' : 'decreased';
            const changeColor = change.difference > 0 ? 'success' : 'danger';
            changesSummary += `<strong>${change.customerName}:</strong> ₱${change.oldValue.toLocaleString()} → ₱${change.newValue.toLocaleString()} 
                <span style="color: var(--bs-${changeColor});">(${changeText} by ₱${Math.abs(change.difference).toLocaleString()})</span><br>`;
        });
        
        Swal.fire({
            icon: 'question',
            title: 'Confirm Credit Limit Changes',
            html: changesSummary,
            showCancelButton: true,
            confirmButtonText: 'Yes, Update Limits',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            customClass: {
                popup: 'swal2-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
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
                input.classList.remove('changed'); // Remove changed styling
            }
        });
        
        // Update button states after reset
        updateSelectedButtons();
        
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
