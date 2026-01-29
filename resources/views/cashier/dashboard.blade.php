@extends('layouts.cashier')
@section('title', 'Cashier Dashboard')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ========================================
           ELECTRIC MODERN PALETTE - CASHIER DASHBOARD
           Minimal, branch-scoped interface
           ======================================== */
        
        :root {
            /* Electric Modern Palette */
            --electric-blue: #0D47A1;
            --neon-blue: #2196F3;
            --cyan-bright: #00E5FF;
            --magenta: #E91E63;
            --violet: #9C27B0;
            --lime-electric: #C6FF00;
            --slate-bg: #ECEFF1;
            --ice-white: #FAFBFC;
            
            /* Dashboard Color Mapping */
            --app-bg: linear-gradient(135deg, #ECEFF1 0%, #E8EAF6 100%);
            --card-bg: #FAFBFC;
            --card-border: rgba(13, 71, 161, 0.15);
            --page-header: #263238;
            
            /* KPI Colors - Electric Theme */
            --text-primary: #263238;
            --text-secondary: #546E7A;
            --success-teal: #43A047;
            --danger-red: #E53935;
            --warning-yellow: #C6FF00;
            --attention-orange: #E91E63;
            --info-blue: #2196F3;
            --hover-blue: #00E5FF;
            --inactive-text: #78909C;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--slate-bg) 0%, #E8EAF6 100%);
        }
        
        .sidebar { width: 220px; }
        
        .dash-header { 
            font-size: 24px; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--electric-blue), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }
        
        .branch-badge {
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Minimal KPI Cards */
        .kpi-card { 
            border-radius: 12px; 
            padding: 16px; 
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(13, 71, 161, 0.08);
            transition: all 0.3s;
            height: 100%;
        }
        
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15);
            border-color: var(--cyan-bright);
        }
        
        .kpi-icon { 
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(0, 229, 255, 0.15));
            color: var(--neon-blue);
            margin-bottom: 12px;
        }
        
        .kpi-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--electric-blue);
            margin-bottom: 4px;
            font-family: 'Inter', monospace;
        }
        
        .kpi-label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        
        /* Navigation Cards */
        .nav-card {
            border-radius: 12px;
            padding: 20px;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .nav-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(33, 150, 243, 0.15);
            border-color: var(--cyan-bright);
            color: var(--text-primary);
            text-decoration: none;
        }
        
        .nav-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            font-size: 20px;
        }
        
        .nav-content h5 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--electric-blue);
        }
        
        .nav-content p {
            margin: 0;
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        /* Chart Container */
        .chart-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            height: 300px;
            border: 2px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(13, 71, 161, 0.08);
        }
        
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--electric-blue);
            margin-bottom: 16px;
        }
        
        /* Recent Sales List */
        .sales-list {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .sales-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--card-border);
        }
        
        .sales-item:last-child {
            border-bottom: none;
        }
        
        .sale-amount {
            font-weight: 600;
            color: var(--neon-blue);
            font-family: 'Inter', monospace;
        }
        
        .sale-time {
            font-size: 11px;
            color: var(--text-secondary);
        }
        
        /* Top Products */
        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--card-border);
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-name {
            font-size: 13px;
            color: var(--text-primary);
            flex: 1;
            margin-right: 8px;
        }
        
        .product-revenue {
            font-weight: 600;
            color: var(--neon-blue);
            font-family: 'Inter', monospace;
            font-size: 12px;
        }
        
        /* Alert Badge */
        .alert-badge {
            background: linear-gradient(135deg, var(--magenta), var(--violet));
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        /* Responsive */
        @media (max-width: 767.98px) {
            .kpi-card { margin-bottom: 12px; }
            .nav-card { margin-bottom: 12px; }
            .chart-container { margin-bottom: 16px; }
        }
    </style>
@endpush

@section('content')
<div class="p-3 p-lg-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-start justify-content-between mb-4">
        <div>
            <div class="dash-header mb-2">Cashier Dashboard</div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Welcome back, {{ auth()->user()->name }}</span>
                @if($branch)
                    <span class="branch-badge">{{ $branch->branch_name }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-secondary btn-sm">🔄 Refresh</button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div class="kpi-value">₱{{ number_format($todaySales, 2) }}</div>
                <div class="kpi-label">Today's Sales</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="kpi-value">{{ $todayTransactions }}</div>
                <div class="kpi-label">Transactions</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="kpi-value">₱{{ number_format($todayExpenses, 2) }}</div>
                <div class="kpi-label">Today's Expenses</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="kpi-value">
                    {{ $lowStockCount }}
                    @if($lowStockCount > 0)
                        <span class="alert-badge">Alert</span>
                    @endif
                </div>
                <div class="kpi-label">Low Stock Items</div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('pos.index') }}" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="nav-content">
                    <h5>Point of Sale</h5>
                    <p>Process sales transactions</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="#" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="nav-content">
                    <h5>Sales</h5>
                    <p>View sales history</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="{{ route('profile.edit') }}" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="nav-content">
                    <h5>Profile</h5>
                    <p>Manage your account</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="#" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="nav-content">
                    <h5>Customers</h5>
                    <p>Customer management</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="#" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="nav-content">
                    <h5>Credit</h5>
                    <p>Credit accounts</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="#" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="nav-content">
                    <h5>Inventory</h5>
                    <p>Branch stock levels</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="#" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <div class="nav-content">
                    <h5>Returns/Refunds</h5>
                    <p>Process returns</p>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <a href="#" class="nav-card">
                <div class="nav-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="nav-content">
                    <h5>Expenses</h5>
                    <p>Track expenses</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts and Lists -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <div class="chart-title">Sales Trend (Last 7 Days)</div>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <div class="chart-title">Recent Sales</div>
                <div class="sales-list">
                    @if($recentSales->count() > 0)
                        @foreach($recentSales as $sale)
                            <div class="sales-item">
                                <div>
                                    <div class="sale-amount">₱{{ number_format($sale->total_amount, 2) }}</div>
                                    <div class="sale-time">{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</div>
                                </div>
                                <div class="text-muted small">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">No sales today</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    @if($topProducts->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <div class="chart-title">Top Products Today</div>
                    <div class="row">
                        @foreach($topProducts as $product)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="product-item">
                                    <div class="product-name">{{ $product->product_name }}</div>
                                    <div class="product-revenue">₱{{ number_format($product->revenue, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Sales',
                    data: @json($salesData),
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            callback: function(value) { 
                                return '₱' + value.toLocaleString(); 
                            },
                            color: '#546E7A'
                        },
                        grid: { 
                            color: 'rgba(13, 71, 161, 0.1)' 
                        }
                    },
                    x: {
                        ticks: { 
                            color: '#546E7A' 
                        },
                        grid: { 
                            color: 'rgba(13, 71, 161, 0.1)' 
                        }
                    }
                }
            }
        });
    }

    // Auto-refresh every 60 seconds
    setInterval(() => {
        window.location.reload();
    }, 60000);
});

function peso(n) {
    return new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'}).format(n || 0);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
