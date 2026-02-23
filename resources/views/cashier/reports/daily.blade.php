@extends('layouts.app')
@section('title', 'Daily Sales Report')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 30px;
            min-height: calc(100vh - 40px);
        }

        .stats-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stats-icon.sales { background: linear-gradient(135deg, var(--primary-color), #1e40af); }
        .stats-icon.revenue { background: linear-gradient(135deg, var(--success-color), #059669); }
        .stats-icon.cash { background: linear-gradient(135deg, var(--warning-color), #d97706); }
        .stats-icon.credit { background: linear-gradient(135deg, var(--secondary-color), #475569); }

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

        .header-title {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 28px;
        }

        .badge-payment {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-cash {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .badge-credit {
            background: linear-gradient(135deg, var(--secondary-color), #475569);
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="main-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="header-title mb-2">
                <i class="fas fa-chart-bar me-3"></i>Daily Sales Report
            </h1>
            <p class="text-muted mb-0">Sales performance for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
        </div>
        <div>
            <form method="GET" class="d-flex gap-2">
                <input type="date" name="date" value="{{ $date }}" class="form-control" max="{{ now()->format('Y-m-d') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon sales me-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Sales</h6>
                            <h4 class="mb-0">{{ $totalSales }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon revenue me-3">
                            <i class="fas fa-peso-sign"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h4 class="mb-0">₱{{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon cash me-3">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Cash Sales</h6>
                            <h4 class="mb-0">₱{{ number_format($cashSales, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon credit me-3">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Credit Sales</h6>
                            <h4 class="mb-0">₱{{ number_format($creditSales, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Sales Transactions
            </h5>
        </div>
        <div class="card-body p-0">
            @if($sales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Customer</th>
                                <th>Payment Method</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-end">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr>
                                    <td>
                                        <strong>#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    </td>
                                    <td>{{ $sale->customer_name ?? 'Walk-in Customer' }}</td>
                                    <td>
                                        <span class="badge-payment badge-{{ $sale->payment_method }}">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">₱{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="text-end">{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No sales found for this date</h5>
                    <p class="text-muted">Try selecting a different date or check back later.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
