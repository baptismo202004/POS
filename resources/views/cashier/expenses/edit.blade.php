@extends('layouts.app')
@section('title', 'Edit Expense')

@php
    $isCashierContext = request()->is('cashier/*');
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

        .expense-info {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: none;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .category-badge {
            background: var(--warning-color);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .expenses-theme {
            position: relative;
            min-height: 100vh;
            background: #f0f6ff;
            color: #1a2744;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }
        .expenses-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .expenses-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .expenses-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .expenses-theme .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .expenses-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .expenses-theme h2 { font-family: 'Nunito', sans-serif; font-weight: 900; letter-spacing: .2px; }

        .expenses-theme .form-card {
            border-radius: 20px;
            border: 1px solid rgba(25,118,210,0.13);
            box-shadow: 0 4px 28px rgba(13,71,161,0.09);
        }
        .expenses-theme .form-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 36px rgba(13,71,161,0.12);
        }
        .expenses-theme .card-header-custom {
            background: linear-gradient(135deg, rgba(13,71,161,0.08), rgba(0,176,255,0.08));
            border-bottom: 1px solid rgba(25,118,210,0.18);
            color: #0D47A1;
        }
        .expenses-theme .btn-primary {
            background: linear-gradient(135deg, #0D47A1, #1976D2);
            border: none;
            box-shadow: 0 10px 20px rgba(13,71,161,0.16);
        }
        .expenses-theme .btn-primary:hover {
            filter: brightness(1.03);
            transform: translateY(-1px);
        }
        .expenses-theme .form-control, .expenses-theme .form-select {
            border: 1px solid rgba(25,118,210,0.20);
            box-shadow: none;
        }
        .expenses-theme .form-control:focus, .expenses-theme .form-select:focus {
            border-color: rgba(25,118,210,0.55);
            box-shadow: 0 0 0 4px rgba(66,165,245,0.22);
        }
    </style>
@endpush

@section('content')
<div class="expenses-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="p-3 p-lg-4" style="position: relative; z-index: 1;">
        <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Edit Expense</h2>
                <p class="text-muted mb-0">Update expense information</p>
            </div>
            <div>
                <a href="{{ route('cashier.expenses.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Expenses
                </a>
            </div>
        </div>

        <!-- Current Expense Info -->
        @if($expense)
            <div class="expense-info">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Expense ID:</strong> #{{ $expense->id }}
                    </div>
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Amount:</strong> ₱{{ number_format($expense->amount, 2) }}
                    </div>
                    <div class="col-md-3">
                        <strong>Category:</strong> 
                        <span class="category-badge">{{ $expense->category?->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Expense Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card form-card">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Update Expense Information
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('cashier.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Description *</label>
                                    <textarea id="description" name="description" class="form-control" rows="3" 
                                              required placeholder="Enter expense description...">{{ $expense->description }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">Amount *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">₱</span>
                                        <input type="number" id="amount" name="amount" class="form-control" 
                                               step="0.01" min="0" required value="{{ $expense->amount }}" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expense_date" class="form-label">Expense Date *</label>
                                    <input type="date" id="expense_date" name="expense_date" class="form-control" 
                                           required value="{{ $expense->expense_date }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expense_category_id" class="form-label">Category *</label>
                                    <select id="expense_category_id" name="expense_category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @selected((int) $expense->expense_category_id === (int) $category->id)>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplier_id" class="form-label">Supplier (optional)</label>
                                    <select id="supplier_id" name="supplier_id" class="form-select">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected((int) $expense->supplier_id === (int) $supplier->id)>
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label">Payment Method *</label>
                                    <select id="payment_method" name="payment_method" class="form-select" required>
                                        <option value="cash" @selected($expense->payment_method === 'cash')>Cash</option>
                                        <option value="card" @selected($expense->payment_method === 'card')>Card</option>
                                        <option value="gcash" @selected($expense->payment_method === 'gcash')>GCash</option>
                                        <option value="other" @selected($expense->payment_method === 'other')>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="receipt" class="form-label">Receipt (optional)</label>
                                    <input type="file" id="receipt" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    @if($expense->receipt_path)
                                        <div class="small mt-2">
                                            Current: <a href="{{ asset('storage/'.$expense->receipt_path) }}" target="_blank" rel="noopener">View receipt</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Expense
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
</div>
@endsection
