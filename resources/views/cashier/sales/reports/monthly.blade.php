@extends('layouts.app')
@section('title', 'Monthly Sales Report')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.13);
            --text:    #1a2744;
            --muted:   #6b84aa;
            --red:     #ef4444;
            --green:   #10b981;
            --amber:   #f59e0b;
            --shadow:  0 4px 28px rgba(13,71,161,0.09);
        }

        .sales-report-monthly {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .sales-report-monthly .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .sales-report-monthly .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .sales-report-monthly .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .sales-report-monthly .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .sales-report-monthly .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .sales-report-monthly .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .sales-report-monthly .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .sales-report-monthly .ph-left { display: flex; align-items: center; gap: 13px; }
        .sales-report-monthly .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
            flex-shrink: 0;
        }
        .sales-report-monthly .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .sales-report-monthly .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .sales-report-monthly .action-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .sales-report-monthly .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .sales-report-monthly .btn-secondary {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
            border: 1px solid rgba(25,118,210,0.20);
        }

        .sales-report-monthly .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }
        @media (max-width: 980px) {
            .sales-report-monthly .stat-grid { grid-template-columns: 1fr; }
        }

        .sales-report-monthly .stat {
            border-radius: 16px;
            border: 1px solid rgba(13,71,161,0.08);
            background: rgba(240,246,255,0.55);
            padding: 14px 14px;
        }
        .sales-report-monthly .stat .label {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 6px;
        }
        .sales-report-monthly .stat .value {
            font-family: 'Nunito', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: var(--navy);
            line-height: 1.1;
        }

        .sales-report-monthly .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .sales-report-monthly .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .sales-report-monthly .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .sales-report-monthly .c-head::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -90px;
            right: -50px;
            pointer-events: none;
        }
        .sales-report-monthly .c-head-title {
            font-family:'Nunito',sans-serif;
            font-size:14.5px;
            font-weight:800;
            color:#fff;
            display:flex;
            align-items:center;
            gap:8px;
            position:relative;
            z-index:1;
        }
        .sales-report-monthly .c-head-title i { color:rgba(0,229,255,.85); }
        .sales-report-monthly .card-body-pad { padding: 18px 22px 22px; }

        .sales-report-monthly .chart-shell { padding: 18px 22px 22px; }
        .sales-report-monthly .chart-shell canvas { width: 100% !important; }
    </style>
@endpush

@section('content')
<div class="sales-report-monthly">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="ph-title">Monthly Sales Report</div>
                    <div class="ph-sub">Sales performance for {{ \Carbon\Carbon::now()->format('F Y') }}</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.sales.reports') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Reports
                </a>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat">
                <div class="label">Total Sales</div>
                <div class="value">₱{{ number_format($monthlyData->total_sales ?? 0, 2) }}</div>
            </div>
            <div class="stat">
                <div class="label">Total Transactions</div>
                <div class="value">{{ $monthlyData->total_transactions ?? 0 }}</div>
            </div>
            <div class="stat">
                <div class="label">Average Transaction</div>
                <div class="value">₱{{ number_format(($monthlyData->total_sales ?? 0) / max($monthlyData->total_transactions ?? 1, 1), 2) }}</div>
            </div>
        </div>

        <div class="main-card" style="margin-bottom: 18px;">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-chart-bar"></i> Sales Trend This Month</div>
            </div>
            <div class="chart-shell">
                <canvas id="monthlySalesChart" height="100"></canvas>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-file-invoice-dollar"></i> Monthly Summary</div>
            </div>
            <div class="card-body-pad">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Performance Metrics</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Best Day:</strong>
                                <span class="text-success">To be calculated</span>
                            </li>
                            <li class="mb-2">
                                <strong>Peak Hours:</strong>
                                <span class="text-info">To be calculated</span>
                            </li>
                            <li class="mb-2">
                                <strong>Growth Rate:</strong>
                                <span class="text-warning">To be calculated</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Target Achievement</h6>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%">
                                75% of Monthly Target
                            </div>
                        </div>
                        <small class="text-muted">Monthly Target: ₱100,000</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Sales Chart
const ctx = document.getElementById('monthlySalesChart').getContext('2d');
const monthlySalesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [{
            label: 'Sales',
            data: [12000, 19000, 15000, 25000],
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
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
                    }
                }
            }
        }
    }
});
</script>
@endpush
