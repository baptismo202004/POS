@extends('layouts.app')
@section('title', 'Refunds')

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
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.13);
            --text:    #1a2744;
            --muted:   #6b84aa;
            --green:   #10b981;
            --red:     #ef4444;
            --amber:   #f59e0b;
            --shadow:  0 4px 28px rgba(13,71,161,0.09);
        }

        .refunds-theme {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .refunds-theme .bg-layer { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; }
        .refunds-theme .bg-layer::before {
            content: ''; position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .refunds-theme .bg-blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: .11; pointer-events: none; }
        .refunds-theme .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .refunds-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .refunds-theme .wrap { position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; padding: 28px 24px 56px; }

        .refunds-theme .page-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; flex-wrap: wrap; gap: 14px;
            animation: up .4s ease both;
        }
        .refunds-theme .ph-left { display: flex; align-items: center; gap: 13px; }
        .refunds-theme .ph-icon {
            width: 46px; height: 46px; border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
        }
        .refunds-theme .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .refunds-theme .ph-sub   { font-size:12px; color:var(--muted); margin-top:2px; }

        .refunds-theme .stats-row {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr));
            gap: 16px; margin-bottom: 22px;
        }
        .refunds-theme .stat-card {
            background: var(--card); border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 3px 18px rgba(13,71,161,0.07);
            padding: 20px 22px;
            display: flex; align-items: center; justify-content: space-between;
            gap: 14px; overflow: hidden; position: relative;
            animation: up .4s ease both;
        }
        .refunds-theme .stat-card:nth-child(2){animation-delay:.07s}
        .refunds-theme .stat-card:nth-child(3){animation-delay:.14s}
        .refunds-theme .stat-card::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0;
            width: 4px; border-radius: 18px 0 0 18px;
        }
        .refunds-theme .stat-card.s-blue::before  { background: linear-gradient(180deg, var(--navy), var(--blue-lt)); }
        .refunds-theme .stat-card.s-green::before { background: linear-gradient(180deg, #059669, var(--green)); }
        .refunds-theme .stat-card.s-amber::before { background: linear-gradient(180deg, #d97706, var(--amber)); }
        .refunds-theme .stat-label {
            font-size: 10px; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; margin-bottom: 6px;
        }
        .refunds-theme .s-blue  .stat-label { color: var(--navy); }
        .refunds-theme .s-green .stat-label { color: #047857; }
        .refunds-theme .s-amber .stat-label { color: #b45309; }
        .refunds-theme .stat-value { font-family: 'Nunito', sans-serif; font-size: 22px; font-weight: 900; color: var(--text); }
        .refunds-theme .stat-ico { font-size: 28px; opacity: .13; flex-shrink: 0; color: var(--navy); }
        .refunds-theme .s-green .stat-ico { color: var(--green); }
        .refunds-theme .s-amber .stat-ico { color: var(--amber); }

        .refunds-theme .main-card {
            background: var(--card); border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden; animation: up .5s ease both;
        }
        .refunds-theme .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex; align-items: center; justify-content: space-between;
            position: relative; overflow: hidden;
        }
        .refunds-theme .c-head::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .refunds-theme .c-head::after {
            content: ''; position: absolute; width:220px; height:220px; border-radius:50%;
            background: rgba(255,255,255,0.05); top:-90px; right:-50px; pointer-events:none;
        }
        .refunds-theme .c-head-title {
            font-family:'Nunito',sans-serif; font-size:14.5px; font-weight:800; color:#fff;
            display:flex; align-items:center; gap:8px; position:relative; z-index:1;
        }
        .refunds-theme .c-head-title i { color:rgba(0,229,255,.85); }
        .refunds-theme .c-badge {
            position:relative; z-index:1;
            background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25);
            color:#fff; font-size:11px; font-weight:700;
            padding:3px 10px; border-radius:20px; font-family:'Nunito',sans-serif;
        }

        .refunds-theme .t-wrap { overflow-x: auto; }
        .refunds-theme table { width: 100%; border-collapse: collapse; }
        .refunds-theme thead th {
            padding: 12px 16px;
            font-size: 10px; font-weight: 700; letter-spacing:.12em; text-transform: uppercase;
            color: var(--muted); background: rgba(13,71,161,0.03);
            border-bottom: 1.5px solid var(--border); white-space: nowrap;
        }
        .refunds-theme tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
            animation: up .3s ease both;
        }
        .refunds-theme tbody tr:nth-child(odd)  { background: #fff; }
        .refunds-theme tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .refunds-theme tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }
        .refunds-theme td { padding: 13px 16px; font-size: 13.5px; vertical-align: middle; }

        .refunds-theme .td-date { font-size: 12.5px; color: var(--muted); font-weight: 600; white-space: nowrap; }
        .refunds-theme .td-date i { color: var(--blue-lt); margin-right: 4px; font-size: 11px; }
        .refunds-theme .td-product { font-weight: 700; color: var(--navy); }
        .refunds-theme .td-qty { display: inline-flex; align-items: center; gap: 4px; font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 14px; color: var(--navy); }
        .refunds-theme .td-qty i { font-size: 10px; color: var(--blue-lt); }
        .refunds-theme .td-amount { font-family: 'Nunito', sans-serif; font-weight: 900; font-size: 14px; color: var(--navy); }
        .refunds-theme .td-reason { font-size: 12.5px; color: var(--muted); max-width: 180px; }
        .refunds-theme .td-cashier { font-size: 12.5px; font-weight: 600; color: var(--text); }

        .refunds-theme .bgh-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 700; font-family:'Nunito',sans-serif;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .refunds-theme .b-approved { background:rgba(16,185,129,0.11); color:#047857; border-color:rgba(16,185,129,0.22); }
        .refunds-theme .b-rejected { background:rgba(239,68,68,0.10); color:#b91c1c; border-color:rgba(239,68,68,0.18); }
        .refunds-theme .b-pending { background:rgba(245,158,11,0.11); color:#b45309; border-color:rgba(245,158,11,0.22); }
        .refunds-theme .bgh-badge i { font-size: 9px; }

        .refunds-theme .empty-row td { padding: 52px 24px !important; text-align: center; }
        .refunds-theme .empty-ico {
            width:58px; height:58px; border-radius:16px;
            background:rgba(13,71,161,0.06);
            display:flex; align-items:center; justify-content:center;
            font-size:22px; color:var(--blue-lt); margin:0 auto 14px;
        }
        .refunds-theme .empty-row h5 { font-family:'Nunito',sans-serif; font-size:15px; font-weight:700; color:var(--navy); margin-bottom:5px; }
        .refunds-theme .empty-row p  { font-size:13px; color:var(--muted); }

        .refunds-theme .pag-bar {
            padding: 14px 22px;
            background: rgba(13,71,161,0.03);
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
        }
        .refunds-theme .pag-bar .pagination { margin-bottom: 0; }
        .refunds-theme .pag-bar .page-link {
            border-radius: 10px !important;
            border: 1.5px solid var(--border) !important;
            color: var(--navy) !important;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
        }
        .refunds-theme .pag-bar .page-item.active .page-link {
            background: var(--navy) !important;
            border-color: var(--navy) !important;
            color: #fff !important;
        }
        .refunds-theme .pag-bar .page-link:focus { box-shadow: 0 0 0 3px rgba(66,165,245,0.14) !important; }

        @keyframes up {
            from { opacity:0; transform:translateY(13px); }
            to   { opacity:1; transform:translateY(0); }
        }
    </style>
@endpush

@section('content')
<div class="refunds-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-undo"></i></div>
                <div>
                    <div class="ph-title">Refunds &amp; Returns</div>
                    <div class="ph-sub">Overview of your refund and return transactions</div>
                </div>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card s-blue">
                <div class="stat-info">
                    <div class="stat-label">Today's Refunds</div>
                    <div class="stat-value">₱{{ number_format($todayRefunds->total_refund_amount ?? 0, 2) }}</div>
                </div>
                <i class="fas fa-undo stat-ico"></i>
            </div>

            <div class="stat-card s-green">
                <div class="stat-info">
                    <div class="stat-label">Items Refunded Today</div>
                    <div class="stat-value">{{ $todayRefunds->total_items ?? 0 }} items</div>
                </div>
                <i class="fas fa-box stat-ico"></i>
            </div>

            <div class="stat-card s-amber">
                <div class="stat-info">
                    <div class="stat-label">This Month's Refunds</div>
                    <div class="stat-value">₱{{ number_format($monthlyRefunds->total_refund_amount ?? 0, 2) }}</div>
                </div>
                <i class="fas fa-calendar stat-ico"></i>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Recent Refunds</div>
                <span class="c-badge">{{ $refunds->total() }} records</span>
            </div>

            <div class="t-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                            <tr>
                                <td>
                                    <span class="td-date"><i class="fas fa-calendar-alt"></i> {{ $refund->created_at->format('M d, Y h:i A') }}</span>
                                </td>
                                <td><span class="td-product">{{ $refund->product->product_name ?? 'N/A' }}</span></td>
                                <td><span class="td-qty"><i class="fas fa-box"></i> {{ $refund->quantity_refunded }}</span></td>
                                <td><span class="td-amount">₱{{ number_format($refund->refund_amount, 2) }}</span></td>
                                <td><span class="td-reason">{{ $refund->reason }}</span></td>
                                <td>
                                    <span class="bgh-badge {{ $refund->status == 'approved' ? 'b-approved' : ($refund->status == 'rejected' ? 'b-rejected' : 'b-pending') }}">
                                        @if($refund->status == 'approved')
                                            <i class="fas fa-check"></i>
                                        @elseif($refund->status == 'rejected')
                                            <i class="fas fa-times"></i>
                                        @else
                                            <i class="fas fa-clock"></i>
                                        @endif
                                        {{ ucfirst($refund->status) }}
                                    </span>
                                </td>
                                <td><span class="td-cashier">{{ $refund->cashier->name ?? 'Unknown' }}</span></td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="7">
                                    <div class="empty-ico"><i class="fas fa-inbox"></i></div>
                                    <h5>No refunds found</h5>
                                    <p>There are no refund transactions to display.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($refunds->hasPages())
                <div class="pag-bar">
                    {{ $refunds->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
