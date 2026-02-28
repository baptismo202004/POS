@extends('layouts.app')
@section('title', 'Add Expense')

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

        .alert-warning {
            background: linear-gradient(135deg, #fef3c7, #fbbf24);
            border: none;
            border-radius: 10px;
            padding: 15px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .category-btn {
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .category-btn:hover {
            border-color: var(--primary-color);
            background: rgba(37, 99, 235, 0.05);
        }

        .category-btn.selected {
            border-color: var(--primary-color);
            background: var(--primary-color);
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
                <h2 class="mb-1">Add Expense</h2>
                <p class="text-muted mb-0">Record a new branch expense</p>
            </div>
            
        </div>

        <!-- Expense Form -->
        <div class="row">
            <div class="col-12">
                <div class="card form-card">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Expense Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        title: '<div class="swal2-icon-success"><i class="fas fa-check-circle"></i></div>',
                                        html: `
                                            <div class="text-center">
                                                <h3 class="mb-3" style="color: #10b981; font-weight: 600;">Expense Created Successfully!</h3>
                                                <p class="text-muted mb-0">{{ session('success') }}</p>
                                            </div>
                                        `,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Okay',
                                        confirmButtonColor: '#10b981',
                                        width: '500px',
                                        padding: '1.5em',
                                        backdrop: 'rgba(0,0,0,0.35)'
                                    });
                                });
                            </script>
                        @endif

                        <form action="{{ route('cashier.expenses.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Section A: Expense Information -->
                                <div class="col-md-6 mb-4">
                                    <h5 class="mb-3">Expense Information</h5>

                                    <div class="mb-3">
                                        <label for="expense_category_id" class="form-label">Expense Category *</label>
                                        <select id="expense_category_id" name="expense_category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="expense_date" class="form-label">Expense Date *</label>
                                        <input type="date" id="expense_date" name="expense_date" class="form-control" 
                                               value="{{ now()->format('Y-m-d') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="number" id="amount" name="amount" class="form-control" 
                                                   step="0.01" min="0" required placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method *</label>
                                        <select id="payment_method" name="payment_method" class="form-select" required>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="gcash">GCash</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Section B: Additional Details -->
                                <div class="col-md-6 mb-4">
                                    <h5 class="mb-3">Additional Details</h5>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea id="description" name="description" class="form-control" rows="3" 
                                                  required placeholder="Enter expense description..."></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="supplier_id" class="form-label">Supplier (optional)</label>
                                        <select id="supplier_id" name="supplier_id" class="form-select">
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section C: Receipt Attachment -->
                            <hr class="my-4">
                            <h5 class="mb-3">Receipt Attachment</h5>
                            <div class="mb-3">
                                <label for="receipt" class="form-label">Upload Receipt (Image or PDF) (optional)</label>
                                <input type="file" id="receipt" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Important:</strong> Please ensure all expense details are accurate. 
                                Attach receipts if available for proper documentation.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Record Expense
                                </button>
                                <a href="{{ route('cashier.expenses.index') }}" class="btn btn-outline-secondary">
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
