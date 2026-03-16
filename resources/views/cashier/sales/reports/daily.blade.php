@extends('layouts.app')
@section('title', 'Daily Sales Report')

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

        .sales-report-daily {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .sales-report-daily .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .sales-report-daily .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .sales-report-daily .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .sales-report-daily .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .sales-report-daily .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .sales-report-daily .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .sales-report-daily .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .sales-report-daily .ph-left { display: flex; align-items: center; gap: 13px; }
        .sales-report-daily .ph-icon {
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
        .sales-report-daily .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .sales-report-daily .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .sales-report-daily .action-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .sales-report-daily .btn {
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
        .sales-report-daily .btn-secondary {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
            border: 1px solid rgba(25,118,210,0.20);
        }

        .sales-report-daily .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .sales-report-daily .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .sales-report-daily .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .sales-report-daily .c-head::after {
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
        .sales-report-daily .c-head-title {
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
        .sales-report-daily .c-head-title i { color:rgba(0,229,255,.85); }
        .sales-report-daily .card-body-pad { padding: 18px 22px 22px; }

        .sales-report-daily .stat {
            border-radius: 18px;
            border: 1px solid rgba(13,71,161,0.08);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            color: #fff;
            padding: 18px 18px;
            box-shadow: 0 8px 22px rgba(13,71,161,0.18);
        }
        .sales-report-daily .stat .value { font-family:'Nunito',sans-serif; font-size: 28px; font-weight: 900; }
        .sales-report-daily .stat .label { opacity: .92; font-weight: 700; }

        .sales-report-daily .receipt-badge {
            background: rgba(25,118,210,0.12);
            border: 1px solid rgba(25,118,210,0.20);
            color: var(--navy);
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
        }

        .sales-report-daily .table-responsive { overflow-x: hidden; }
        .sales-report-daily table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .sales-report-daily table.table thead th {
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
        .sales-report-daily table.table td {
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
<div class="sales-report-daily">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <div class="ph-title">Daily Sales Report</div>
                    <div class="ph-sub">Sales performance for {{ \Carbon\Carbon::today()->format('F d, Y') }}</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.sales.reports') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Reports
                </a>
            </div>
        </div>

        <div class="stat" style="margin-bottom: 18px;">
            <div class="value">₱{{ number_format($totalSales, 2) }}</div>
            <div class="label">Total Sales Today</div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-receipt"></i> Today's Sales Transactions</div>
            </div>

            <div class="card-body-pad" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table" style="margin: 0;">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Receipt #</th>
                                <th>Cashier</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salesData as $sale)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</td>
                                    <td><span class="receipt-badge">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span></td>
                                    <td>{{ $sale->cashier_name ?? 'N/A' }}</td>
                                    <td>{{ $sale->items_count ?? 0 }}</td>
                                    <td><strong>₱{{ number_format($sale->total_amount, 2) }}</strong></td>
                                    <td>
                                        <span class="badge bg-success">Completed</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-row" style="padding: 32px 18px; text-align:center; color: var(--muted);">No sales transactions recorded for today.</td>
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
