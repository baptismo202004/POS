@extends('layouts.app')
@section('title', 'Customer Details')

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
            --navy: #0D47A1;
            --blue: #1976D2;
            --blue-lt: #42A5F5;
            --bg: #f0f6ff;
            --card: #ffffff;
            --border: rgba(25,118,210,0.13);
            --text: #1a2744;
            --muted: #6b84aa;
            --shadow: 0 4px 28px rgba(13,71,161,0.09);
        }

        .customers-theme {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }
        .customers-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .customers-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .customers-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .customers-theme .bb1 { width:420px; height:420px; background: var(--blue); top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .customers-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .customers-theme h2 { font-family: 'Nunito', sans-serif; font-weight: 900; letter-spacing: .2px; }
        .customers-theme .text-muted { color: var(--muted) !important; }

        .customers-theme .card {
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        .customer-header {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: white;
            border-radius: 12px;
        }
        
        .info-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
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
<div class="customers-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="p-3 p-lg-4" style="position: relative; z-index: 1;">
        <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Customer Details</h2>
                <p class="text-muted mb-0">View customer information and history</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('cashier.customers.edit', $customer->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <a href="{{ route('cashier.customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-header card mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px;">
                            {{ strtoupper(substr($customer->full_name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col-md-10">
                        <h3 class="mb-2">{{ $customer->full_name }}</h3>
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-badge {{ $customer->status == 'active' ? 'status-active' : 'status-blocked' }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                            <div class="text-white">
                                <i class="fas fa-credit-card me-1"></i>
                                Credit Limit: ₱{{ number_format($customer->max_credit_limit, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-card card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-address-card me-2 text-primary"></i>Contact Information
                        </h5>
                        <div class="mt-3">
                            @if($customer->phone)
                                <p class="mb-2">
                                    <strong>Phone:</strong> {{ $customer->phone }}
                                </p>
                            @endif
                            @if($customer->email)
                                <p class="mb-2">
                                    <strong>Email:</strong> {{ $customer->email }}
                                </p>
                            @endif
                            @if($customer->address)
                                <p class="mb-0">
                                    <strong>Address:</strong> {{ $customer->address }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Account Information
                        </h5>
                        <div class="mt-3">
                            <p class="mb-2">
                                <strong>Customer ID:</strong> #{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                            <p class="mb-2">
                                <strong>Status:</strong> 
                                <span class="status-badge {{ $customer->status == 'active' ? 'status-active' : 'status-blocked' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>Credit Limit:</strong> ₱{{ number_format($customer->max_credit_limit, 2) }}
                            </p>
                            <p class="mb-0">
                                <strong>Created:</strong> {{ $customer->created_at->format('M d, Y H:i:s') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity (Placeholder for future implementation) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-history me-2 text-primary"></i>Recent Activity
                </h5>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-clock fa-2x mb-3"></i>
                    <p>Customer activity tracking will be available soon.</p>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
