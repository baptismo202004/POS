@extends('layouts.app')
@section('title', 'Customers')

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
        
        .customer-card {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }
        
        .customer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .status-blocked {
            background: linear-gradient(135deg, #ef4444, #dc2626);
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
                <h2 class="mb-1">Customers</h2>
                <p class="text-muted mb-0">Manage customer accounts and credit limits</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cashier.customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add Customer
                </a>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('cashier.customers.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search customers..." value="{{ request('search') }}">
                    </div>
                     <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('cashier.customers.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customers List -->
        @forelse($customers as $customer)
            <div class="customer-card card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-2">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 20px;">
                                    {{ strtoupper(substr($customer->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ $customer->full_name }}</h5>
                                    <div class="text-muted small">
                                        @if($customer->phone)
                                            <i class="fas fa-phone me-1"></i>{{ $customer->phone }}
                                        @endif
                                        @if($customer->email)
                                            <span class="ms-3"><i class="fas fa-envelope me-1"></i>{{ $customer->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($customer->address)
                                <p class="text-muted mb-2"><i class="fas fa-map-marker-alt me-1"></i>{{ $customer->address }}</p>
                            @endif
                            <div class="d-flex align-items-center gap-3">
                                <span class="status-badge {{ $customer->status == 'active' ? 'status-active' : 'status-blocked' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                                <small class="text-muted">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Credit Limit: ₱{{ number_format($customer->max_credit_limit, 2) }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <a href="{{ route('cashier.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cashier.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCustomer({{ $customer->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-users fa-3x mb-3"></i>
                    <h4>No customers found</h4>
                    <p>Get started by adding your first customer.</p>
                    
                </div>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </div>
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCustomer(customerId) {
    Swal.fire({
        title: 'Delete Customer?',
        text: 'Are you sure you want to delete this customer? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cashier/customers/${customerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Customer has been deleted successfully.',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to delete customer.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the customer.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}
</script>
@endpush
