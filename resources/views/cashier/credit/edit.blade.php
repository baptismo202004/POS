@extends('layouts.app')
@section('title', 'Edit Credit')

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

        .credit-info {
            background: linear-gradient(135deg, #fef3c7, #fbbf24);
            border: none;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Edit Credit Account</h2>
                <p class="text-muted mb-0">Update credit account information</p>
            </div>
            <div>
                <a href="{{ route('cashier.credit.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Credits
                </a>
            </div>
        </div>
       <!-- Credit Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card form-card">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Update Customer's Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="credit-update-form" action="{{ route('cashier.credit.update', $credit->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_id" class="form-label">Customer *</label>
                                    <select id="customer_id" name="customer_id" class="form-select" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ $credit->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->full_name ?? $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           placeholder="Enter email address..." value="{{ $credit->customer?->email ?? '' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control" 
                                           placeholder="Enter phone number..." value="{{ $credit->customer?->phone ?? '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" id="address" name="address" class="form-control" 
                                           placeholder="Enter address..." value="{{ $credit->customer?->address ?? '' }}">
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Customer Information
                                </button>
                                <a href="{{ route('cashier.credit.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('credit-update-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
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
                    // Get the updated values for success message
                    const customerSelect = document.getElementById('customer_id');
                    const customerName = customerSelect.options[customerSelect.selectedIndex]?.text || 'Unknown Customer';
                    const email = document.getElementById('email').value;
                    const phoneNumber = document.getElementById('phone_number').value;
                    const address = document.getElementById('address').value;
                    
                    Swal.fire({
                        title: '<div class="swal2-icon-success"><i class="fas fa-check-circle"></i></div>',
                        html: `
                            <div class="text-center">
                                <h3 class="mb-3" style="color: #10b981; font-weight: 600;">Customer Information Updated Successfully!</h3>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Customer:</span>
                                        <strong>${customerName}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Email:</span>
                                        <strong style="color: #2563eb;">${email || 'Not provided'}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Phone Number:</span>
                                        <strong>${phoneNumber || 'Not provided'}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Address:</span>
                                        <strong>${address || 'Not provided'}</strong>
                                    </div>
                                </div>
                                <div class="alert alert-success mb-3" style="border-radius: 10px;">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Customer Updated:</strong> The customer information has been successfully updated.
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-success btn-lg me-2" onclick="window.location.href='/cashier/credit'">
                                        <i class="fas fa-list me-2"></i>View Credits
                                    </button>
                                    <button class="btn btn-outline-secondary btn-lg" onclick="window.location.href='/cashier/credit'">
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
                            window.location.href = '/cashier/credit';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update credit. Please try again.',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Update error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'An error occurred while updating the credit. Please try again.',
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
