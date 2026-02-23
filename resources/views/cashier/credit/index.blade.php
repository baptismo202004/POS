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
            <div>
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
