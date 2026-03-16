@extends('layouts.app')
@section('title', 'Sales Reports')

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

        .sales-reports-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .sales-reports-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .sales-reports-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .sales-reports-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .sales-reports-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .sales-reports-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .sales-reports-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .sales-reports-page .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .sales-reports-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .sales-reports-page .ph-icon {
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
        .sales-reports-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .sales-reports-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .sales-reports-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .sales-reports-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .sales-reports-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .sales-reports-page .c-head::after {
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
        .sales-reports-page .c-head-title {
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
        .sales-reports-page .c-head-title i { color:rgba(0,229,255,.85); }

        .sales-reports-page .card-body-pad { padding: 18px 22px 22px; }

        .sales-reports-page .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }
        @media (max-width: 980px) {
            .sales-reports-page .stat-grid { grid-template-columns: 1fr; }
        }
        .sales-reports-page .stat {
            border-radius: 16px;
            border: 1px solid rgba(13,71,161,0.08);
            background: rgba(240,246,255,0.55);
            padding: 14px 14px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            justify-content: space-between;
        }
        .sales-reports-page .stat .label {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
            margin-bottom: 6px;
        }
        .sales-reports-page .stat .value {
            font-family: 'Nunito', sans-serif;
            font-size: 22px;
            font-weight: 900;
            color: var(--navy);
            line-height: 1.1;
        }
        .sales-reports-page .stat .icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: rgba(25,118,210,0.12);
            color: var(--navy);
            flex-shrink: 0;
        }

        .sales-reports-page .table-responsive { overflow-x: hidden; }
        .sales-reports-page table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .sales-reports-page table.table thead th {
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
        .sales-reports-page table.table td {
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
<div class="sales-reports-page">
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
                    <div class="ph-sub">Branch-based summary (this month)</div>
                </div>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-gauge-high"></i> Overview</div>
            </div>

            <div class="card-body-pad">
                <div class="stat-grid">
                    <div class="stat">
                        <div>
                            <div class="label">Today's Sales</div>
                            <div class="value">₱{{ number_format((float) ($todaySales ?? 0), 2) }}</div>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                    <div class="stat">
                        <div>
                            <div class="label">This Month's Sales</div>
                            <div class="value">₱{{ number_format((float) ($monthlySales ?? 0), 2) }}</div>
                        </div>
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                    </div>
                    <div class="stat">
                        <div>
                            <div class="label">Top Products Listed</div>
                            <div class="value">{{ $topProducts?->count() ?? 0 }}</div>
                        </div>
                        <div class="icon"><i class="fas fa-boxes-stacked"></i></div>
                    </div>
                </div>

                <div class="main-card" style="border-radius: 16px; overflow: hidden;">
                    <div class="c-head" style="border-radius: 0;">
                        <div class="c-head-title"><i class="fas fa-star"></i> Top Products (This Month)</div>
                    </div>
                    <div class="card-body-pad" style="padding: 0;">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 52px;">#</th>
                                        <th>Product</th>
                                        <th class="text-end">Units Sold</th>
                                        <th class="text-end">Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $index => $row)
                                        <tr>
                                            <td style="color: var(--muted); font-weight: 800; font-family: 'Nunito', sans-serif;">{{ $index + 1 }}</td>
                                            <td style="font-weight: 900; font-family: 'Nunito', sans-serif;">{{ $row->product_name }}</td>
                                            <td class="text-end">{{ number_format((float) $row->total_sold) }}</td>
                                            <td class="text-end"><strong>₱{{ number_format((float) $row->revenue, 2) }}</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="empty-row" style="padding: 32px 18px; text-align:center; color: var(--muted);">
                                                No sales data for this month yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

