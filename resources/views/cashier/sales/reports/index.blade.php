@extends('layouts.app')
@section('title', 'Sales Reports')

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

        .sales-reports-index {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .sales-reports-index .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .sales-reports-index .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .sales-reports-index .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .sales-reports-index .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .sales-reports-index .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .sales-reports-index .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .sales-reports-index .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .sales-reports-index .ph-left { display: flex; align-items: center; gap: 13px; }
        .sales-reports-index .ph-icon {
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
        .sales-reports-index .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .sales-reports-index .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .sales-reports-index .action-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .sales-reports-index .btn {
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
        .sales-reports-index .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .sales-reports-index .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }
        .sales-reports-index .btn-secondary {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
            border: 1px solid rgba(25,118,210,0.20);
        }

        .sales-reports-index .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .sales-reports-index .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .sales-reports-index .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .sales-reports-index .c-head::after {
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
        .sales-reports-index .c-head-title {
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
        .sales-reports-index .c-head-title i { color:rgba(0,229,255,.85); }
        .sales-reports-index .card-body-pad { padding: 18px 22px 22px; }

        .sales-reports-index .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }
        @media (max-width: 980px) {
            .sales-reports-index .stat-grid { grid-template-columns: 1fr; }
        }
        .sales-reports-index .stat {
            border-radius: 16px;
            border: 1px solid rgba(13,71,161,0.08);
            background: rgba(240,246,255,0.55);
            padding: 14px 14px;
        }
        .sales-reports-index .stat .label {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 6px;
        }
        .sales-reports-index .stat .value {
            font-family: 'Nunito', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: var(--navy);
            line-height: 1.1;
        }

        .sales-reports-index .table-responsive { overflow-x: hidden; }
        .sales-reports-index table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .sales-reports-index table.table thead th {
            padding: 12px 18px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: normal;
            word-break: break-word;
        }
        .sales-reports-index table.table td {
            padding: 13px 18px;
            font-size: 13.5px;
            vertical-align: middle;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }
    </style>
@endpush

@section('content')
<div class="sales-reports-index">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="ph-title">Sales Reports</div>
                    <div class="ph-sub">View and analyze sales performance</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.sales.reports.daily') }}" class="btn btn-secondary">
                    <i class="fas fa-calendar-day"></i>
                    Daily Report
                </a>
                <a href="{{ route('cashier.sales.reports.monthly') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-alt"></i>
                    Monthly Report
                </a>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Sales History (Current Month)</div>
            </div>

            <div class="card-body-pad">
                <div class="stat-grid">
                    <div class="stat">
                        <div class="label">Total Sales (This Month)</div>
                        <div class="value">₱{{ number_format($salesData->sum('total'), 2) }}</div>
                    </div>
                    <div class="stat">
                        <div class="label">Total Transactions</div>
                        <div class="value">{{ $salesData->count() }}</div>
                    </div>
                    <div class="stat">
                        <div class="label">Average Sale</div>
                        <div class="value">₱{{ number_format($salesData->avg('total'), 2) }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Sales</th>
                                <th>Transactions</th>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesData as $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('M d, Y') }}</td>
                                    <td><strong>₱{{ number_format($data->total, 2) }}</strong></td>
                                    <td>{{ $data->transactions }}</td>
                                    <td>₱{{ number_format($data->total / $data->transactions, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-row" style="padding: 32px 18px; text-align:center; color: var(--muted);">No sales data available for the current month.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
