@extends('layouts.app')
@section('title', 'Credit Management')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Use CashierSidebar */
        .main-content {
            margin-left: 280px !important;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --card-hover-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }

        .credit-card {
            border-radius: 15px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .credit-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-5px);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--light-bg), #e2e8f0);
            border-bottom: 2px solid var(--primary-color);
            padding: 20px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .table-custom {
            border-radius: 10px;
            overflow: hidden;
        }

        .table-custom thead {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
        }

        .table-custom th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-custom tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: var(--warning-color);
            color: white;
        }

        .status-paid {
            background: var(--success-color);
            color: white;
        }

        .status-overdue {
            background: var(--danger-color);
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Credit Management</h2>
                <p class="text-muted mb-0">Manage customer credit accounts</p>
            </div>
            <div class="d-flex gap-2">
                @canAccess('credit_limits','view')
                <button class="btn btn-info" onclick="showCreditLimits()">
                    <i class="fas fa-chart-bar me-2"></i>Credit Limits
                </button>
                @endcanAccess
                <a href="{{ route('cashier.credit.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Credit
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $credits->total() }}</div>
                    <div class="stats-label">Total Credits</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">{{ $credits->where('status', 'pending')->count() }}</div>
                    <div class="stats-label">Pending Credits</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">₱{{ number_format($credits->where('status', 'pending')->sum('amount'), 2) }}</div>
                    <div class="stats-label">Pending Amount</div>
                </div>
            </div>
        </div>

        <!-- Credits Table -->
        <div class="card credit-card">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Credit Accounts
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($credits as $credit)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle me-2 text-muted"></i>
                                            <div>
                                                <strong>{{ $credit->customer_name ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $credit->customer_email ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold">₱{{ number_format($credit->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($credit->due_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="status-badge status-{{ $credit->status }}">
                                            {{ $credit->status }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($credit->created_at)->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('cashier.credit.edit', $credit->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('cashier.credit.destroy', $credit->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this credit?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fas fa-credit-card fa-3x mb-3"></i>
                                        <h5>No Credit Accounts</h5>
                                        <p>No credit accounts found. Create a new credit account to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($credits->hasPages())
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Showing {{ $credits->firstItem() }} to {{ $credits->lastItem() }} of {{ $credits->total() }} credits</span>
                        {{ $credits->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showCreditLimits() {
    fetch('/cashier/credit/credit-limits-data')
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
                               data-customer-id="${customer.customer_id}"
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
                                   data-customer-id="${customer.customer_id}"
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
                        <small class="text-muted">${statusText}</small>
                    </td>
                </tr>
            `;
        });
    } else {
        customerHtml = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No customers with credit accounts found.</p>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button class="btn btn-primary btn-sm" onclick="updateSelectedLimits()">
                                    <i class="fas fa-save me-1"></i> Update Selected
                                </button>
                                <button class="btn btn-secondary btn-sm ms-2" onclick="resetAllLimits()">
                                    <i class="fas fa-undo me-1"></i> Reset All
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Set credit limits for customers. Leave at 0 for no limit.
                                </small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAllCustomers" onchange="toggleAllCustomers()">
                                        </th>
                                        <th>Customer</th>
                                        <th>Total Credits</th>
                                        <th>Current Limit</th>
                                        <th>Max Credit Limit</th>
                                        <th>Total Paid</th>
                                        <th>Remaining</th>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal if any
    const existingModal = document.getElementById('creditLimitsModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('creditLimitsModal'));
    modal.show();
}

function toggleAllCustomers() {
    const selectAll = document.getElementById('selectAllCustomers');
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function updateSelectedLimits() {
    const selectedCustomers = [];
    const checkboxes = document.querySelectorAll('.customer-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        const customerId = checkbox.dataset.customerId;
        const limitInput = document.querySelector(`#limit_input_${checkbox.id.replace('customer_', '')}`);
        selectedCustomers.push({
            customer_id: customerId,
            max_credit_limit: parseFloat(limitInput.value) || 0
        });
    });

    if (selectedCustomers.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one customer to update.'
        });
        return;
    }

    Swal.fire({
        title: 'Update Credit Limits',
        text: `Update credit limits for ${selectedCustomers.length} customer(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/cashier/credit/update-credit-limits', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    customers: selectedCustomers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Credit limits updated successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        showCreditLimits(); // Refresh the modal
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to update credit limits.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating credit limits.'
                });
            });
        }
    });
}

function resetAllLimits() {
    const inputs = document.querySelectorAll('.limit-input');
    inputs.forEach(input => {
        input.value = input.dataset.originalValue;
    });
}
</script>
@endpush
