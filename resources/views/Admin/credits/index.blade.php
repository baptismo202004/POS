@extends('layouts.app')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:    #0D47A1;
        --blue:    #1976D2;
        --blue-lt: #42A5F5;
        --cyan:    #00E5FF;
        --green:   #10b981;
        --red:     #ef4444;
        --amber:   #f59e0b;
        --bg:      #EBF3FB;
        --card:    #ffffff;
        --border:  rgba(25,118,210,0.12);
        --text:    #1a2744;
        --muted:   #6b84aa;
    }

    /* ── Background ── */
    .sp-bg { position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg); }
    .sp-bg::before {
        content:'';position:absolute;inset:0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%,transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%,transparent 55%);
    }
    .sp-blob { position:absolute;border-radius:50%;filter:blur(60px);opacity:.11; }
    .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
    .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    /* ── Wrap ── */
    .sp-wrap { position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif; }

    /* ── Page header ── */
    .sp-page-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:26px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both; }
    .sp-ph-left { display:flex;align-items:center;gap:13px; }
    .sp-ph-icon { width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28); }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
    .sp-ph-actions { display:flex;align-items:center;gap:9px;flex-wrap:wrap; }

    /* ── Buttons ── */
    .sp-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap; }
    .sp-btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26); }
    .sp-btn-primary:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }
    .sp-btn-teal { background:linear-gradient(135deg,#0e7490,#06b6d4);color:#fff;box-shadow:0 4px 14px rgba(6,182,212,0.26); }
    .sp-btn-teal:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(6,182,212,0.36);color:#fff; }
    .sp-btn-outline { background:var(--card);color:var(--navy);border:1.5px solid var(--border); }
    .sp-btn-outline:hover { background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-2px); }

    /* ── Stats grid ── */
    .sp-stats-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px; }
    @media(max-width:992px){ .sp-stats-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:576px){ .sp-stats-grid{grid-template-columns:1fr;} }

    .sp-stat-card {
        background:var(--card);border-radius:18px;border:1px solid var(--border);
        box-shadow:0 4px 20px rgba(13,71,161,0.07);
        padding:20px 22px;
        display:flex;align-items:center;justify-content:space-between;
        position:relative;overflow:hidden;
        transition:transform .2s ease,box-shadow .2s ease;
    }
    .sp-stat-card::before { content:'';position:absolute;top:0;left:0;right:0;height:3px; }
    .sp-stat-card:hover { transform:translateY(-3px);box-shadow:0 8px 28px rgba(13,71,161,0.13); }
    .sp-stat-card:nth-child(1) { animation:spUp .40s ease both; }
    .sp-stat-card:nth-child(2) { animation:spUp .46s ease both; }
    .sp-stat-card:nth-child(3) { animation:spUp .52s ease both; }
    .sp-stat-card:nth-child(4) { animation:spUp .58s ease both; }
    .sp-stat-card:nth-child(1)::before { background:linear-gradient(90deg,var(--navy),var(--blue-lt)); }
    .sp-stat-card:nth-child(2)::before { background:linear-gradient(90deg,#059669,#10b981); }
    .sp-stat-card:nth-child(3)::before { background:linear-gradient(90deg,#d97706,var(--amber)); }
    .sp-stat-card:nth-child(4)::before { background:linear-gradient(90deg,#dc2626,var(--red)); }

    .sp-stat-label { font-size:10.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:7px;font-family:'Nunito',sans-serif; }
    .sp-stat-value { font-family:'Nunito',sans-serif;font-size:21px;font-weight:900;color:var(--navy);line-height:1; }
    .sp-stat-icon { width:46px;height:46px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }
    .sp-stat-card:nth-child(1) .sp-stat-icon { background:rgba(13,71,161,0.10);color:var(--navy); }
    .sp-stat-card:nth-child(2) .sp-stat-icon { background:rgba(16,185,129,0.10);color:#059669; }
    .sp-stat-card:nth-child(3) .sp-stat-icon { background:rgba(245,158,11,0.10);color:#d97706; }
    .sp-stat-card:nth-child(4) .sp-stat-icon { background:rgba(239,68,68,0.10);color:#dc2626; }

    /* ── Main card ── */
    .sp-card { background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .55s ease both; }

    /* ── Card gradient header ── */
    .sp-card-head { padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden; }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-c-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif; }

    /* ── Table ── */
    .sp-table-wrap { overflow-x:auto;max-height:520px;overflow-y:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;width:5px;}
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}
    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th { position:sticky;top:0;z-index:3;background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap; }
    .sp-table tbody td { padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;transition:background .15s; }
    .sp-table tbody tr:nth-child(even) td { background:rgba(240,246,255,0.55); }
    .sp-table tbody tr:hover td { background:rgba(21,101,192,0.05); }
    .sp-table tbody tr { animation:spRow .3s ease both; }
    .sp-table tbody tr:nth-child(1){animation-delay:.03s}
    .sp-table tbody tr:nth-child(2){animation-delay:.06s}
    .sp-table tbody tr:nth-child(3){animation-delay:.09s}
    .sp-table tbody tr:nth-child(4){animation-delay:.12s}
    .sp-table tbody tr:nth-child(5){animation-delay:.15s}
    @keyframes spRow{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
    .sp-table td.empty-row { text-align:center;color:var(--muted);font-style:italic;padding:40px; }

    /* Badges */
    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green  { background:rgba(16,185,129,0.12);color:#047857; }
    .sp-badge-amber  { background:rgba(245,158,11,0.12);color:#92400e; }
    .sp-badge-red    { background:rgba(239,68,68,0.10);color:#b91c1c; }
    .sp-badge-teal   { background:rgba(6,182,212,0.10);color:#0e7490; }
    .sp-badge-muted  { background:rgba(107,132,170,0.10);color:var(--muted); }

    /* Amounts */
    .sp-amount        { font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:var(--navy); }
    .sp-amount-green  { font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:#059669; }
    .sp-amount-danger { font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:#dc2626; }

    /* Table action buttons */
    .sp-tbl-btn { display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:8px;border:none;cursor:pointer;font-size:12px;font-weight:700;font-family:'Nunito',sans-serif;text-decoration:none;transition:all .18s ease; }
    .sp-tbl-view { color:var(--blue);background:rgba(13,71,161,0.08); }
    .sp-tbl-view:hover { background:rgba(13,71,161,0.16);color:var(--navy); }
    .sp-tbl-pay  { color:#059669;background:rgba(16,185,129,0.10); }
    .sp-tbl-pay:hover  { background:rgba(16,185,129,0.18);color:#047857; }

    /* ── Pagination ── */
    .sp-pagination { padding:14px 22px;background:rgba(13,71,161,0.03);border-top:1px solid var(--border);display:flex;align-items:center;justify-content:center; }
    .sp-pagination .pagination { margin:0; }
    .sp-pagination .page-link { border-radius:8px !important;margin:0 2px;border:1.5px solid var(--border);color:var(--navy);font-weight:700;font-size:13px;font-family:'Nunito',sans-serif;transition:all .18s ease; }
    .sp-pagination .page-link:hover { background:rgba(13,71,161,0.08);border-color:var(--blue-lt); }
    .sp-pagination .page-item.active .page-link { background:linear-gradient(135deg,var(--navy),var(--blue));border-color:var(--navy);color:#fff; }

    /* ── Modal ── */
    .sp-modal .modal-content { border:none;border-radius:18px;box-shadow:0 16px 50px rgba(13,71,161,0.18);overflow:hidden; }
    .sp-modal .modal-header { padding:18px 24px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);border:none;position:relative;overflow:hidden; }
    .sp-modal .modal-header::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-modal .modal-header::after  { content:'';position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.05);top:-70px;right:-40px;pointer-events:none; }
    .sp-modal .modal-title { font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;color:#fff;position:relative;z-index:1;display:flex;align-items:center;gap:8px; }
    .sp-modal .modal-title i { color:rgba(0,229,255,.85); }
    .sp-modal .btn-close { filter:brightness(0) invert(1);opacity:.7;position:relative;z-index:1; }
    .sp-modal .btn-close:hover { opacity:1; }
    .sp-modal .modal-body { padding:24px; }
    .sp-modal .modal-footer { border-top:1px solid var(--border);padding:16px 24px;background:rgba(13,71,161,0.02); }
    .sp-modal .form-label { font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;font-family:'Nunito',sans-serif;display:block; }
    .sp-modal .form-control,
    .sp-modal .form-select { border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;width:100%; }
    .sp-modal .form-control:focus,
    .sp-modal .form-select:focus { border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff; }
    .sp-modal .text-muted { font-size:11.5px;color:var(--muted) !important; }
    .sp-modal-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:11px;border:none;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;transition:all .2s ease; }
    .sp-modal-btn-success { background:linear-gradient(135deg,#059669,#10b981);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,0.26); }
    .sp-modal-btn-success:hover { transform:translateY(-1px);box-shadow:0 7px 20px rgba(16,185,129,0.36);color:#fff; }
    .sp-modal-btn-cancel { background:transparent;color:var(--muted);border:1.5px solid var(--border); }
    .sp-modal-btn-cancel:hover { background:rgba(107,132,170,0.08);color:var(--text); }

    .sp-swal-popup{border-radius:18px !important;border:1px solid var(--border) !important;box-shadow:0 16px 50px rgba(13,71,161,0.18) !important;overflow:hidden !important;padding:0 !important;font-family:'Plus Jakarta Sans',sans-serif !important;}
    .sp-swal-title{margin:0 !important;padding:18px 24px !important;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%) !important;color:#fff !important;font-family:'Nunito',sans-serif !important;font-size:16px !important;font-weight:900 !important;text-align:left !important;position:relative !important;}
    .sp-swal-title::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-swal-body{margin:0 !important;padding:18px 24px !important;}
    .sp-swal-actions{margin:0 !important;padding:16px 24px !important;border-top:1px solid var(--border) !important;background:rgba(13,71,161,0.02) !important;display:flex !important;gap:10px !important;justify-content:center !important;}
    .sp-swal-confirm,.sp-swal-cancel{border-radius:11px !important;padding:10px 18px !important;font-size:13px !important;font-weight:800 !important;font-family:'Nunito',sans-serif !important;}
    .sp-swal-confirm{background:linear-gradient(135deg,#059669,#10b981) !important;color:#fff !important;border:none !important;box-shadow:0 4px 14px rgba(16,185,129,0.26) !important;}
    .sp-swal-cancel{background:transparent !important;color:var(--muted) !important;border:1.5px solid var(--border) !important;}
    .sp-swal-cancel:hover{background:rgba(107,132,170,0.08) !important;color:var(--text) !important;}

    .sp-swal-card{background:rgba(255,255,255,0.75);border:1px solid var(--border);border-radius:16px;padding:14px 16px;box-shadow:0 3px 14px rgba(13,71,161,0.06);}
    .sp-swal-card + .sp-swal-card{margin-top:10px;}
    .sp-swal-row{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;}
    .sp-swal-left h6{margin:0 0 6px;font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);}
    .sp-swal-left small{display:block;color:var(--muted);line-height:1.35;}
    .sp-swal-right{min-width:140px;text-align:right;}
    .sp-swal-right strong{font-family:'Nunito',sans-serif;font-weight:900;}
    .sp-swal-note{margin-top:12px;background:rgba(13,71,161,0.03);border:1px solid var(--border);border-radius:14px;padding:14px 16px;}
    .sp-swal-note strong{font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);}

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@section('content')

<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <main class="flex-fill p-4" style="position:relative;z-index:1;">
        <div class="sp-wrap">

            {{-- ── Page header ── --}}
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-credit-card"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Finance</div>
                        <div class="sp-ph-title">Credits Management</div>
                        <div class="sp-ph-sub">Overview of your credit transactions and outstanding balances</div>
                    </div>
                </div>
                <div class="sp-ph-actions">
                    <a href="{{ route('admin.credits.create') }}" class="sp-btn sp-btn-primary">
                        <i class="fas fa-plus"></i> New Credit
                    </a>
                    <button class="sp-btn sp-btn-teal" onclick="showCreditLimits()">
                        <i class="fas fa-chart-bar"></i> Credit Limits
                    </button>
                     </div>
            </div>

            {{-- ── Stats cards ── --}}
            <div class="sp-stats-grid">
                <div class="sp-stat-card">
                    <div>
                        <div class="sp-stat-label">Today's Credits</div>
                        <div class="sp-stat-value">₱{{ number_format($todayCredits->total_credit_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="sp-stat-icon"><i class="fas fa-credit-card"></i></div>
                </div>
                <div class="sp-stat-card">
                    <div>
                        <div class="sp-stat-label">Outstanding Balance</div>
                        <div class="sp-stat-value">₱{{ number_format($todayCredits->total_outstanding ?? 0, 2) }}</div>
                    </div>
                    <div class="sp-stat-icon"><i class="fas fa-hourglass-half"></i></div>
                </div>
                <div class="sp-stat-card">
                    <div>
                        <div class="sp-stat-label">This Month's Credits</div>
                        <div class="sp-stat-value">₱{{ number_format($monthlyCredits->total_credit_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="sp-stat-icon"><i class="fas fa-calendar"></i></div>
                </div>
                <div class="sp-stat-card">
                    <div>
                        <div class="sp-stat-label">Overdue Credits</div>
                        <div class="sp-stat-value">₱{{ number_format($overdueCredits->total_overdue_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="sp-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>

            {{-- ── Recent Credits table card ── --}}
            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-list"></i> Recent Credits
                    </div>
                    <span class="sp-c-badge">{{ $customers->total() }} records</span>
                </div>

                <div class="sp-table-wrap">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th>Branch</th>
                                <th>Customer Name</th>
                                <th>Source</th>
                                <th>Total Credits</th>
                                <th>Total Credit Amount</th>
                                <th>Total Paid Amount</th>
                                <th>Remaining Balance</th>
                                <th>Last Credit Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer->branch_name ?? 'N/A' }}</td>
                                    <td><strong>{{ $customer->full_name }}</strong></td>
                                    <td>
                                        @php
                                            $srcClass = $customer->credit_source == 'Sales' ? 'sp-badge-teal' :
                                                        ($customer->credit_source == 'Cash'  ? 'sp-badge-green' : 'sp-badge-muted');
                                        @endphp
                                        <span class="sp-badge {{ $srcClass }}">{{ $customer->credit_source }}</span>
                                    </td>
                                    <td style="font-weight:600;">{{ $customer->credit_giver_total }}</td>
                                    <td><span class="sp-amount">₱{{ number_format($customer->total_credit, 2) }}</span></td>
                                    <td><span class="sp-amount-green">₱{{ number_format($customer->total_paid, 2) }}</span></td>
                                    <td><span class="sp-amount-danger">₱{{ number_format($customer->outstanding_balance, 2) }}</span></td>
                                    <td style="color:var(--muted);font-size:12.5px;">
                                        <i class="fas fa-calendar-alt me-1" style="font-size:10px;opacity:.6;"></i>
                                        {{ \Carbon\Carbon::parse($customer->last_credit_date)->format('M d, Y') }}
                                    </td>
                                    <td>
                                        @php
                                            $stClass = $customer->status == 'Fully Paid'    ? 'sp-badge-green' :
                                                       ($customer->status == 'Good Standing' ? 'sp-badge-amber' : 'sp-badge-red');
                                        @endphp
                                        <span class="sp-badge {{ $stClass }}">
                                            <i class="fas fa-circle me-1" style="font-size:7px;"></i>{{ $customer->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <button class="sp-tbl-btn sp-tbl-view" onclick="viewCustomerCredits({{ $customer->customer_id }})">
                                                <i class="fas fa-eye"></i> View Details
                                            </button>
                                            @if($customer->outstanding_balance > 0)
                                                <button class="sp-tbl-btn sp-tbl-pay" onclick="makeCustomerPayment({{ $customer->customer_id }}, {{ $customer->outstanding_balance }})">
                                                    <i class="fas fa-money-bill-wave"></i> Payment
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="empty-row">
                                        <i class="fas fa-inbox me-2"></i>No customers with credits found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($customers->hasPages())
                    <div class="sp-pagination">
                        {{ $customers->links() }}
                    </div>
                @endif

            </div>{{-- end sp-card --}}
        </div>{{-- end sp-wrap --}}
    </main>
</div>

{{-- ══ Payment Modal ══ --}}
<div class="modal fade sp-modal" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fas fa-money-bill-wave"></i> Record Credit Payment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="credit_id" id="payment_credit_id">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount (₱)</label>
                        <input type="number" class="form-control" name="payment_amount" id="payment_amount" step="0.01" min="0.01" required>
                        <small class="text-muted mt-1 d-block">Remaining balance: ₱<span id="remaining_balance_display">0.00</span></small>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="">Select payment method...</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="sp-modal-btn sp-modal-btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="sp-modal-btn sp-modal-btn-success">
                        <i class="fas fa-check"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Force refresh to avoid caching issues
    console.log('Credits index script loaded - v2');

    // Add CSS for highlighting changed fields
    const style = document.createElement('style');
    style.textContent = `
        .limit-input.changed {
            border-color: #28a745;
            background-color: #f8f9fa;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            font-weight: 500;
        }
        .limit-input.changed:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .swal2-popup {
            font-size: 14px;
        }
        .swal2-popup strong {
            color: #333;
        }
    `;
    document.head.appendChild(style);

    // Global modal focus management to fix aria-hidden accessibility issues
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('click', function(e) {
            if (e.target.hasAttribute('data-bs-dismiss') && e.target.getAttribute('data-bs-dismiss') === 'modal') {
                e.target.blur();
                document.body.focus();
            }
        });

        document.addEventListener('hide.bs.modal', function(e) {
            const modal = e.target;
            const activeElement = document.activeElement;
            if (activeElement && modal.contains(activeElement)) {
                activeElement.blur();
                document.body.focus();
            }
        });
    });

    function viewCredit(creditId) {
        console.log('Viewing credit:', creditId);
        window.location.href = `/admin/credits/${creditId}`;
    }

    function viewCustomerCredits(customerId) {
        console.log('Viewing customer credits:', customerId);
        window.location.href = `/admin/credits/customer/${customerId}`;
    }

    function makeCustomerPayment(customerId, remainingBalance) {
        console.log('Making payment for customer:', customerId, 'Remaining:', remainingBalance);

        fetch(`/admin/credits/customer/${customerId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCreditPaymentModal(customerId, data.credits);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to fetch customer credits' });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to fetch customer credits' });
            });
    }

    function makePayment(creditId, remainingBalance) {
        console.log('Making payment for credit:', creditId, 'Remaining balance:', remainingBalance);
        const paymentAmount = parseFloat(remainingBalance);
        console.log('Parsed payment amount:', paymentAmount);
        document.getElementById('payment_credit_id').value = creditId;
        document.getElementById('remaining_balance_display').textContent = paymentAmount.toFixed(2);
        const paymentInput = document.getElementById('payment_amount');
        paymentInput.min = 0.01;
        paymentInput.value = paymentAmount.toFixed(2);
        paymentInput.removeAttribute('max');
        paymentInput.removeAttribute('max');
        console.log('Payment input value set to:', paymentInput.value);
        console.log('Payment input max removed');
        console.log('Remaining balance display:', document.getElementById('remaining_balance_display').textContent);
        document.getElementById('paymentModalLabel').textContent = 'Record Credit Payment';
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();
    }

    function showCreditPaymentModal(customerId, credits) {
        console.log('=== SHOW CREDIT PAYMENT MODAL DEBUG ===');
        console.log('Customer ID:', customerId);
        console.log('Credits received:', credits);

        let creditsHtml = '';
        let totalOutstanding = 0;

        if (credits.length === 0) {
            creditsHtml = '<p class="text-muted">No active credits found for this customer.</p>';
        } else {
            credits.forEach(credit => {
                console.log('Processing credit:', credit);
                console.log('Credit remaining balance:', credit.remaining_balance);
                console.log('Type of remaining_balance:', typeof credit.remaining_balance);

                if (credit.remaining_balance > 0) {
                    const balance = parseFloat(credit.remaining_balance);
                    totalOutstanding += balance;
                    console.log('Added to total:', balance, 'New total:', totalOutstanding);

                    creditsHtml += `
                        <div class="sp-swal-card">
                            <div class="sp-swal-row">
                                <div class="sp-swal-left">
                                    <h6>Credit #${credit.id}</h6>
                                    <small>Amount: ₱${credit.credit_amount}</small>
                                    <small>Date: ${new Date(credit.date).toLocaleDateString()}</small>
                                </div>
                                <div class="sp-swal-right">
                                    <strong style="color:var(--red);">₱${credit.remaining_balance}</strong>
                                    <small>Outstanding</small>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
        }

        console.log('Final total outstanding:', totalOutstanding);
        console.log('Type of totalOutstanding:', typeof totalOutstanding);

        const modalHtml = `
            <div style="max-height: 400px; overflow-y: auto;">
                ${creditsHtml || '<p class="text-muted">No outstanding credits found.</p>'}
                ${totalOutstanding > 0 ? `
                    <div class="sp-swal-note">
                        <strong>Total Outstanding Balance: ₱${totalOutstanding.toFixed(2)}</strong>
                        <br><small class="text-muted">Payment will be automatically distributed across all outstanding credits.</small>
                    </div>
                ` : ''}
            </div>
        `;

        console.log('=== END SHOW CREDIT PAYMENT MODAL DEBUG ===');

        Swal.fire({
            title: 'Customer Credits Summary',
            html: modalHtml,
            showConfirmButton: totalOutstanding > 0,
            showCancelButton: true,
            confirmButtonText: totalOutstanding > 0 ? `Record Payment (₱${totalOutstanding.toFixed(2)})` : 'Close',
            cancelButtonText: 'Cancel',
            width: '600px',
            customClass: {
                popup: 'sp-swal-popup',
                title: 'sp-swal-title',
                htmlContainer: 'sp-swal-body',
                actions: 'sp-swal-actions',
                confirmButton: 'sp-swal-confirm',
                cancelButton: 'sp-swal-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed && totalOutstanding > 0) {
                makeTotalPayment(customerId, totalOutstanding, credits);
            }
        });
    }

    function makeTotalPayment(customerId, totalAmount, credits) {
        const creditIds = credits.filter(c => c.remaining_balance > 0).map(c => c.id);

        console.log('=== MAKE TOTAL PAYMENT DEBUG ===');
        console.log('Customer ID:', customerId);
        console.log('Total Amount:', totalAmount);
        console.log('Credits to pay:', creditIds);
        console.log('Type of totalAmount:', typeof totalAmount);

        const paymentAmount = parseFloat(totalAmount);
        console.log('Parsed payment amount:', paymentAmount);

        const creditIdInput = document.getElementById('payment_credit_id');
        const remainingBalanceDisplay = document.getElementById('remaining_balance_display');
        const paymentInput = document.getElementById('payment_amount');

        creditIdInput.value = creditIds.join(',');
        remainingBalanceDisplay.textContent = paymentAmount.toFixed(2);
        paymentInput.min = 0.01;
        paymentInput.value = paymentAmount.toFixed(2);
        paymentInput.removeAttribute('max');
        paymentInput.removeAttribute('max');

        console.log('Payment input value set to:', paymentInput.value);
        console.log('Payment input max removed');
        console.log('Remaining balance display:', remainingBalanceDisplay.textContent);

        document.getElementById('paymentModalLabel').textContent = 'Record Total Payment';
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();

        console.log('=== END MAKE TOTAL PAYMENT DEBUG ===');
    }

    function openPaymentModal() {
        Swal.fire({ icon: 'info', title: 'Select Credit', text: 'Please select a credit from the table to record a payment.', confirmButtonColor: '#2563eb' });
    }

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const creditId = document.getElementById('payment_credit_id').value;
        const paymentAmount = document.getElementById('payment_amount').value;
        const remainingBalanceDisplay = document.getElementById('remaining_balance_display').textContent;

        console.log('Submitting payment:');
        console.log('Credit ID:', creditId);
        console.log('Payment Amount:', paymentAmount);
        console.log('Remaining Balance Display:', remainingBalanceDisplay);

        const formData = new FormData(this);
        const isMultiCredit = creditId.includes(',');

        for (let [key, value] of formData.entries()) { console.log(key + ':', value); }

        Swal.fire({ title: 'Processing Payment...', html: 'Please wait while we record your payment.', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

        const endpoint = isMultiCredit ? '/admin/credits/multi-payment' : `/admin/credits/${creditId}/payment`;

        if (isMultiCredit) {
            formData.set('credit_ids', creditId);
            formData.delete('credit_id');
        }

        fetch(endpoint, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            console.log('Payment response:', data);
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Success!', text: data.message, timer: 2000, showConfirmButton: false })
                .then(() => { bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide(); location.reload(); });
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'An error occurred while processing the payment.' });
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while processing the payment.' });
        });
    });

    function showCreditLimits() {
        fetch('/admin/credits/credit-limits-data')
            .then(response => response.json())
            .then(data => {
                if (data.success) { displayCreditLimitsModal(data); }
                else { Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load credit limits data.' }); }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while loading credit limits.' });
            });
    }

    function displayCreditLimitsModal(data) {
        let customerHtml = '';
        if (data.creditsByCustomer && data.creditsByCustomer.length > 0) {
            data.creditsByCustomer.forEach((customer, index) => {
                const maxCreditLimit = customer.max_credit_limit || 0;
                const effectiveLimit = maxCreditLimit > 0 ? maxCreditLimit : customer.total_credit_limit;
                const progressPercentage = effectiveLimit > 0 ? (customer.total_paid / effectiveLimit) * 100 : 0;
                let statusClass, statusText;
                if (customer.total_remaining <= 0) { statusClass = 'success'; statusText = 'Fully Paid'; }
                else if (maxCreditLimit > 0) {
                    const maxLimitProgress = (customer.total_credit_limit / maxCreditLimit) * 100;
                    if (maxLimitProgress >= 100) { statusClass = 'danger'; statusText = 'Limit Reached'; }
                    else if (maxLimitProgress >= 80) { statusClass = 'warning'; statusText = 'Near Limit'; }
                    else { statusClass = 'success'; statusText = 'Available'; }
                } else {
                    statusClass = progressPercentage >= 80 ? 'warning' : 'danger';
                    statusText = progressPercentage >= 80 ? 'Good Standing' : 'Outstanding';
                }
                const canStillCredit = maxCreditLimit > 0 && (customer.total_credit_limit < maxCreditLimit);
                const limitStatus = maxCreditLimit > 0 ? (customer.total_credit_limit >= maxCreditLimit ? 'Limit Reached' : 'Available') : 'No Limit Set';

                customerHtml += `
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input customer-checkbox"
                                   id="customer_${index}" data-customer="${customer.customer_name}"
                                   data-customer-id="${customer.customer_id}"
                                   data-current-limit="${maxCreditLimit}">
                        </td>
                        <td><strong>${customer.customer_name}</strong></td>
                        <td>${customer.total_credits}</td>
                        <td>₱${parseFloat(customer.total_credit_limit).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control form-control-sm limit-input"
                                       id="limit_input_${index}"
                                       value="${maxCreditLimit}"
                                       min="0"
                                       step="100"
                                       data-original-value="${maxCreditLimit}"
                                       data-customer="${customer.customer_name}"
                                       data-customer-id="${customer.customer_id}"
                                       style="width: 120px;">
                            </div>
                        </td>
                        <td>₱${parseFloat(customer.total_paid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>₱${parseFloat(customer.total_remaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                        <td>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-${statusClass}" style="width: ${progressPercentage}%"></div>
                            </div>
                            <small>${progressPercentage.toFixed(1)}% of ${maxCreditLimit > 0 ? 'max limit' : 'total limit'}</small>
                        </td>
                        <td>
                            <span class="badge bg-${statusClass}">${statusText}</span>
                            ${maxCreditLimit > 0 ? `<br><small class="text-muted">${limitStatus}</small>` : ''}
                        </td>
                    </tr>
                `;
            });
        } else {
            customerHtml = `<tr><td colspan="10" class="text-center py-3"><i class="fas fa-inbox fa-2x text-muted mb-2"></i><p class="text-muted mb-0">No credit records found</p></td></tr>`;
        }

        const modalHtml = `
            <div class="modal fade sp-modal" id="creditLimitsModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-chart-bar"></i> Credit Limits Management</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="sp-stats-grid" style="margin-bottom:16px;grid-template-columns:repeat(4,1fr);">
                                <div class="sp-stat-card" style="padding:16px 18px;">
                                    <div>
                                        <div class="sp-stat-label">Total Customers</div>
                                        <div class="sp-stat-value">${data.totalCustomers}</div>
                                    </div>
                                    <div class="sp-stat-icon"><i class="fas fa-users"></i></div>
                                </div>
                                <div class="sp-stat-card" style="padding:16px 18px;">
                                    <div>
                                        <div class="sp-stat-label">Total Credit Limit</div>
                                        <div class="sp-stat-value">₱${parseFloat(data.totalCreditLimit).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                                    </div>
                                    <div class="sp-stat-icon"><i class="fas fa-layer-group"></i></div>
                                </div>
                                <div class="sp-stat-card" style="padding:16px 18px;">
                                    <div>
                                        <div class="sp-stat-label">Total Paid</div>
                                        <div class="sp-stat-value">₱${parseFloat(data.totalPaid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                                    </div>
                                    <div class="sp-stat-icon"><i class="fas fa-circle-check"></i></div>
                                </div>
                                <div class="sp-stat-card" style="padding:16px 18px;">
                                    <div>
                                        <div class="sp-stat-label">Total Remaining</div>
                                        <div class="sp-stat-value">₱${parseFloat(data.totalRemaining).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                                    </div>
                                    <div class="sp-stat-icon"><i class="fas fa-triangle-exclamation"></i></div>
                                </div>
                            </div>

                            <div class="sp-table-wrap" style="max-height: 58vh;">
                                <table class="sp-table">
                                    <thead>
                                        <tr>
                                            <th width="40">Select</th>
                                            <th>Customer Name</th>
                                            <th>Total Credits</th>
                                            <th>Credit Limit</th>
                                            <th>Max Credit Limit</th>
                                            <th>Paid Amount</th>
                                            <th>Remaining</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>${customerHtml}</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex justify-content-between align-items-center w-100" style="gap: 12px;flex-wrap:wrap;">
                                <div class="d-flex align-items-center" style="gap:10px;flex-wrap:wrap;">
                                    <button class="sp-modal-btn sp-modal-btn-success" id="updateSelectedBtn" disabled><i class="fas fa-save"></i> Update</button>
                                    <button class="sp-modal-btn sp-modal-btn-cancel" id="resetSelectedBtn" disabled><i class="fas fa-undo"></i> Reset Selected</button>
                                    <small class="text-muted"><span id="selectedCount">0</span> customers selected</small>
                                </div>
                                <button type="button" class="sp-modal-btn sp-modal-btn-cancel" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const existingModal = document.getElementById('creditLimitsModal');
        if (existingModal) existingModal.remove();

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('creditLimitsModal'));
        modal.show();

        initializeCreditLimitsModal();

        const modalElement = document.getElementById('creditLimitsModal');
        modalElement.addEventListener('hide.bs.modal', function () {
            const activeElement = document.activeElement;
            if (activeElement && modalElement.contains(activeElement)) { activeElement.blur(); document.body.focus(); }
        });
        const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"]');
        closeButtons.forEach(button => { button.addEventListener('click', function(e) { this.blur(); document.body.focus(); }); });
        modalElement.addEventListener('hidden.bs.modal', function () { modalElement.remove(); });
    }

    function initializeCreditLimitsModal() {
        const customerCheckboxes = document.querySelectorAll('.customer-checkbox');
        const updateSelectedBtn = document.getElementById('updateSelectedBtn');
        const resetSelectedBtn  = document.getElementById('resetSelectedBtn');
        const selectedCount     = document.getElementById('selectedCount');

        customerCheckboxes.forEach(checkbox => { checkbox.addEventListener('change', function() { updateSelectedButtons(); }); });

        function updateSelectedButtons() {
            const selectedCustomers = document.querySelectorAll('.customer-checkbox:checked');
            const hasSelection = selectedCustomers.length > 0;
            const changedInputs = document.querySelectorAll('.limit-input.changed');
            const hasChanges = changedInputs.length > 0;
            if (updateSelectedBtn) updateSelectedBtn.disabled = !hasSelection;
            if (resetSelectedBtn)  resetSelectedBtn.disabled  = !hasSelection;
            if (selectedCount)     selectedCount.textContent  = selectedCustomers.length;
        }

        if (updateSelectedBtn) updateSelectedBtn.addEventListener('click', function() { updateSelectedLimits(); });
        if (resetSelectedBtn)  resetSelectedBtn.addEventListener('click',  function() { resetSelectedLimits(); });

        updateSelectedButtons();

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('limit-input')) {
                const originalValue = parseFloat(e.target.dataset.originalValue) || 0;
                const currentValue  = parseFloat(e.target.value) || 0;
                if (currentValue !== originalValue) e.target.classList.add('changed');
                else e.target.classList.remove('changed');
                recalculateCustomerStatus(e.target);
                updateSelectedButtons();
            }
        });
    }

    function recalculateCustomerStatus(inputElement) {
        const customerName = inputElement.dataset.customer;
        const newMaxLimit  = parseFloat(inputElement.value) || 0;
        const row = inputElement.closest('tr');
        if (!row) return;
        const customerData = findCustomerData(customerName);
        if (!customerData) return;
        const effectiveLimit = newMaxLimit > 0 ? newMaxLimit : customerData.total_credit_limit;
        const progressPercentage = effectiveLimit > 0 ? (customerData.total_paid / effectiveLimit) * 100 : 0;
        let statusClass, statusText;
        if (customerData.total_remaining <= 0) { statusClass = 'success'; statusText = 'Fully Paid'; }
        else if (newMaxLimit > 0) {
            const maxLimitProgress = (customerData.total_credit_limit / newMaxLimit) * 100;
            if (maxLimitProgress >= 100) { statusClass = 'danger'; statusText = 'Limit Reached'; }
            else if (maxLimitProgress >= 80) { statusClass = 'warning'; statusText = 'Near Limit'; }
            else { statusClass = 'success'; statusText = 'Available'; }
        } else {
            statusClass = progressPercentage >= 80 ? 'warning' : 'danger';
            statusText  = progressPercentage >= 80 ? 'Good Standing' : 'Outstanding';
        }
        const statusCell = row.querySelector('td:last-child .badge');
        if (statusCell) { statusCell.className = `badge bg-${statusClass}`; statusCell.textContent = statusText; }
        const progressBar  = row.querySelector('.progress-bar');
        const progressText = row.querySelector('.progress small');
        if (progressBar) { progressBar.className = `progress-bar bg-${statusClass}`; progressBar.style.width = `${progressPercentage}%`; }
        if (progressText) progressText.textContent = `${progressPercentage.toFixed(1)}% of ${newMaxLimit > 0 ? 'max limit' : 'total limit'}`;
    }

    function findCustomerData(customerName) {
        const row = document.querySelector(`[data-customer="${customerName}"]`).closest('tr');
        if (!row) return null;
        const cells = row.querySelectorAll('td');
        return {
            total_credits: parseInt(cells[2].textContent) || 0,
            total_credit_limit: parseFloat(cells[3].textContent.replace(/[₱,]/g, '')) || 0,
            total_paid: parseFloat(cells[5].textContent.replace(/[₱,]/g, '')) || 0,
            total_remaining: parseFloat(cells[6].textContent.replace(/[₱,]/g, '')) || 0
        };
    }

    function editCustomerLimit(customerName, currentLimit, index) {
        const input = document.getElementById(`limit_input_${index}`);
        if (input) { input.focus(); input.select(); }
    }

    function updateSelectedLimits() {
        const allInputs = document.querySelectorAll('.limit-input');
        const updates = []; const changes = [];
        allInputs.forEach(input => {
            const newLimit      = parseFloat(input.value) || 0;
            const originalLimit = parseFloat(input.dataset.originalValue) || 0;
            const customerName  = input.dataset.customer;
            if (newLimit >= 0) {
                updates.push({ customerName, newLimit, originalLimit });
                if (newLimit !== originalLimit) changes.push({ customerName, oldValue: originalLimit, newValue: newLimit, difference: newLimit - originalLimit });
            }
        });
        if (updates.length === 0) { Swal.fire({ icon: 'warning', title: 'No Valid Updates', text: 'Please enter valid credit limits for customers.' }); return; }
        if (changes.length === 0) { Swal.fire({ icon: 'info', title: 'No Changes Detected', text: 'No changes were made to the credit limits.' }); return; }
        let changesSummary = 'The following changes will be made:<br><br>';
        changes.forEach(change => {
            const changeText  = change.difference > 0 ? 'increased' : 'decreased';
            const changeColor = change.difference > 0 ? 'success' : 'danger';
            changesSummary += `<strong>${change.customerName}:</strong> ₱${change.oldValue.toLocaleString()} → ₱${change.newValue.toLocaleString()} <span style="color: var(--bs-${changeColor});">(${changeText} by ₱${Math.abs(change.difference).toLocaleString()})</span><br>`;
        });
        Swal.fire({
            icon: 'question', title: 'Confirm Credit Limit Changes', html: changesSummary,
            showCancelButton: true, confirmButtonText: 'Yes, Update Limits', cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', customClass: { popup: 'swal2-popup' }
        }).then((result) => {
            if (result.isConfirmed) {
                const updateBtn = document.getElementById('updateSelectedBtn');
                const originalBtnContent = updateBtn.innerHTML;
                updateBtn.disabled = true; updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                let completed = 0; let errors = 0;
                updates.forEach(update => {
                    updateCreditLimit(update.customerName, update.newLimit, function(success) {
                        completed++; if (!success) errors++;
                        if (completed === updates.length) {
                            updateBtn.disabled = false; updateBtn.innerHTML = originalBtnContent;
                            if (errors === 0) {
                                Swal.fire({ icon: 'success', title: 'Updated Successfully!', text: `Successfully updated ${updates.length} customer credit limits.`, timer: 2000, showConfirmButton: false })
                                .then(() => { showCreditLimits(); });
                            } else {
                                Swal.fire({ icon: 'warning', title: 'Partial Success', text: `Updated ${updates.length - errors} of ${updates.length} customer credit limits.` });
                            }
                        }
                    });
                });
            }
        });
    }

    function resetSelectedLimits() {
        const selectedCustomers = document.querySelectorAll('.customer-checkbox:checked');
        selectedCustomers.forEach(checkbox => {
            const currentLimit = parseFloat(checkbox.dataset.currentLimit) || 0;
            const inputId = `limit_input_${checkbox.id.replace('customer_', '')}`;
            const input   = document.getElementById(inputId);
            if (input) { input.value = currentLimit; input.classList.remove('changed'); }
        });
        updateSelectedButtons();
        Swal.fire({ icon: 'info', title: 'Reset Complete', text: `Reset ${selectedCustomers.length} customer limits to original values.`, timer: 1500, showConfirmButton: false });
    }

    function updateCreditLimit(customerName, newLimit, callback) {
        const checkbox   = document.querySelector(`[data-customer="${customerName}"]`);
        const customerId = checkbox ? checkbox.getAttribute('data-customer-id') : null;
        if (!customerId) { console.error('Customer ID not found for:', customerName); if (typeof callback === 'function') callback(false); return; }
        const formData = new FormData();
        formData.append('customer_id', customerId);
        formData.append('max_credit_limit', newLimit);
        fetch('/admin/credits/update-credit-limit', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (typeof callback === 'function') { callback(data.success); }
            else if (data.success) { Swal.fire({ icon: 'success', title: 'Success!', text: data.message, timer: 2000, showConfirmButton: false }).then(() => { showCreditLimits(); }); }
            else { Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to update credit limit.' }); }
        })
        .catch(error => {
            if (typeof callback === 'function') { callback(false); }
            else { Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while updating the credit limit.' }); }
        });
    }
</script>

@endsection