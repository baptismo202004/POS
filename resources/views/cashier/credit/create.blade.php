@extends('layouts.app')
@section('title', 'Add Credit')

@php
    $isCashierContext = request()->is('cashier/*');
    $returnTo = request('return_to') ?: route('cashier.credit.index');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        .form-card {
            border-radius: 15px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .form-card:hover {
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

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            padding: 12px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-outline-secondary {
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .alert-info {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            border: none;
            border-radius: 10px;
            padding: 15px;
        }

        .credit-theme {
            position: relative;
            min-height: 100vh;
            background: #f0f6ff;
            color: #1a2744;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .credit-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .credit-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .credit-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .credit-theme .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .credit-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .credit-theme .form-card {
            border-radius: 20px;
            border: 1px solid rgba(25,118,210,0.13);
            box-shadow: 0 4px 28px rgba(13,71,161,0.09);
        }
        .credit-theme .card-header-custom {
            background: linear-gradient(135deg, #0D47A1 0%, #1976D2 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            color: #fff;
        }
        .credit-theme .card-header-custom h5 {
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
        }
        .credit-theme .card-header-custom i { color: rgba(0,229,255,.85); }

        .credit-theme .form-control,
        .credit-theme .form-select {
            border-radius: 11px;
            border: 1.5px solid rgba(25,118,210,0.18);
            font-size: 13px;
            padding: 10px 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .credit-theme .form-control:focus,
        .credit-theme .form-select:focus {
            border-color: #42A5F5;
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }

        .credit-theme .btn-primary {
            background: linear-gradient(135deg, #0D47A1, #1976D2);
            border: none;
            border-radius: 11px;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .credit-theme .btn-outline-secondary {
            border-radius: 11px;
        }

        .credit-theme .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
@endpush

@section('content')
<div class="credit-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="p-3 p-lg-4" style="position: relative; z-index: 1;">
        <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Create New Credit</h2>
                <p class="text-muted mb-0">Create a new credit for this branch</p>
            </div>
            <div>
                <a href="{{ $returnTo }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Credits
                </a>
            </div>
        </div>

        <!-- Credit Form -->
        <div class="row">
            <div class="col-12">
                <div class="card form-card">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Credit Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="credit-create-form" action="{{ route('cashier.credit.store') }}" method="POST">
                            @csrf
                            <div class="row align-items-end mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="branch_id" class="form-label">Branch</label>
                                    <input type="text" class="form-control" value="{{ $userBranch?->branch_name ?? '' }}" readonly>
                                    <input type="hidden" id="branch_id" name="branch_id" value="{{ old('branch_id', $userBranch?->id) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_id" class="form-label">Customer</label>
                                    <select id="customer_id" name="customer_id" class="form-select" required style="width: 100%">
                                        <option value="">Search or add new customer...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->full_name ?? $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="credit_amount" class="form-label">Credit Amount (₱)</label>
                                    <input type="number" id="credit_amount" name="credit_amount" class="form-control" 
                                           step="0.01" min="0" required placeholder="Enter credit amount">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="due_date" class="form-label">Credit Date</label>
                                    <input type="date" id="due_date" name="due_date" class="form-control" 
                                           value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="credit_type" class="form-label">Credit Type</label>
                                    <select id="credit_type" name="credit_type" class="form-select" required>
                                        <option value="">Select Credit Type</option>
                                        <option value="cash">Cash</option>
                                        <option value="grocery">Grocery (Requires Sale ID)</option>
                                        <option value="electronics">Electronics (Requires Sale ID)</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3" id="sale_id_field" style="display: none;">
                                    <label for="sale_id" class="form-label">Sale ID</label>
                                    <input type="number" id="sale_id" name="sale_id" class="form-control" placeholder="Enter Sale ID">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Notes (Optional)</label>
                                <textarea id="description" name="description" class="form-control" rows="3" 
                                          placeholder="Add any additional notes about this credit..."></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ $returnTo }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Create Credit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('credit-create-form');
    const successReturnTo = @json(request('return_to') ?: route('cashier.customers.index'));
    window.__creditReturnTo = successReturnTo;

    const creditTypeEl = document.getElementById('credit_type');
    const saleIdField = document.getElementById('sale_id_field');
    const saleIdInput = document.getElementById('sale_id');

    function toggleSaleIdField() {
        const t = creditTypeEl ? String(creditTypeEl.value || '') : '';
        const requiresSale = t === 'grocery' || t === 'electronics';

        if (saleIdField) {
            saleIdField.style.display = requiresSale ? '' : 'none';
        }
        if (saleIdInput) {
            if (requiresSale) {
                saleIdInput.setAttribute('required', 'required');
            } else {
                saleIdInput.removeAttribute('required');
                saleIdInput.value = '';
            }
        }
    }

    if (creditTypeEl) {
        creditTypeEl.addEventListener('change', toggleSaleIdField);
        toggleSaleIdField();
    }

    // Initialize Select2 on customer dropdown for search + type new
    const $customerSelect = $('#customer_id');
    if ($customerSelect.length) {
        $customerSelect.select2({
            tags: true,
            placeholder: 'Select or type customer name',
            allowClear: true,
            width: 'resolve'
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
            submitBtn.disabled = true;
            
            fetch(form.action, {
                method: 'POST', // Always use POST for Laravel forms
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Get form values for success message
                    const customerName = $('#customer_id').find('option:selected').text() || 'Unknown Customer';
                    const creditLimits = document.getElementById('credit_amount').value;
                    const phoneInput = document.getElementById('phone_number');
                    const phoneNumber = phoneInput ? phoneInput.value : '';
                    const dueDate = document.getElementById('due_date').value;
                    const description = document.getElementById('description').value;
                    
                    Swal.fire({
                        title: '<div class="swal2-icon-success"><i class="fas fa-check-circle"></i></div>',
                        html: `
                            <div class="text-center">
                                <h3 class="mb-3" style="color: #10b981; font-weight: 600;">Credit Created Successfully!</h3>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Customer:</span>
                                        <strong>${customerName}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Credit Limits:</span>
                                        <strong style="color: #2563eb; font-size: 1.2em;">₱${parseFloat(creditLimits).toLocaleString('en-PH', {minimumFractionDigits: 2})}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Phone Number:</span>
                                        <strong>${phoneNumber || 'Not provided'}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Due Date:</span>
                                        <strong>${new Date(dueDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Description:</span>
                                        <strong>${description || 'No description'}</strong>
                                    </div>
                                </div>
                                <div class="alert alert-success mb-3" style="border-radius: 10px;">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Credit Created:</strong> The credit account has been successfully created for the customer.
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-success btn-lg me-2" onclick="window.location.href='/cashier/credit'">
                                        <i class="fas fa-list me-2"></i>View Credits
                                    </button>
                                    <button class="btn btn-outline-secondary btn-lg" onclick="window.location.href=window.__creditReturnTo">
                                        <i class="fas fa-check me-2"></i>Okay
                                    </button>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: '600px',
                        padding: '2em',
                        backdrop: 'rgba(0,0,123,0.1)',
                        didOpen: () => {
                            // Add custom animations
                            const swalContainer = document.querySelector('.swal2-container');
                            swalContainer.style.animation = 'slideInUp 0.5s ease-out';
                        }
                    }).then((result) => {
                        if (result.isDismissed || result.dismiss === Swal.DismissReason.close) {
                            window.location.href = window.__creditReturnTo;
                        }
                    });
                } else {
                    const limitMsg = (data && data.errors && data.errors.credit_amount && data.errors.credit_amount.length)
                        ? data.errors.credit_amount[0]
                        : null;

                    Swal.fire({
                        icon: 'error',
                        title: 'Creation Failed',
                        text: limitMsg || data.message || "You can't add more credit because the customer has exceeded the credit limit.",
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Create error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'An error occurred while creating credit. Please try again.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Restore button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});
</script>
@push('stylesDashboard')
<style>
    /* Custom SweetAlert animations */
    @keyframes slideInUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .swal2-icon-success {
        animation: bounceIn 0.6s ease-out;
    }

    .swal2-icon-success i {
        font-size: 4rem;
        color: #10b981;
    }
</style>
@endpush
