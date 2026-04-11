<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment History - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
            --green:#10b981;--red:#ef4444;--amber:#f59e0b;
            --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
            --text:#1a2744;--muted:#6b84aa;
        }

        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

        .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
        .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
        .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
        .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
        .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
        @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
        @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

        .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;}

        .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
        .sp-ph-left{display:flex;align-items:center;gap:13px;}
        .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
        .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
        .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
        .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

        .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
        .sp-btn-secondary{background:var(--card);color:var(--navy);border:1.5px solid var(--border);box-shadow:0 2px 8px rgba(13,71,161,0.08);}
        .sp-btn-secondary:hover{transform:translateY(-2px);box-shadow:0 6px 16px rgba(13,71,161,0.14);color:var(--navy);}

        .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .55s ease both;}
        .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
        .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
        .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
        .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
        .sp-card-head-title i{color:rgba(0,229,255,.85);}
        .sp-card-body{padding:18px 22px;}

        .sp-table-wrap{overflow-x:auto;}
        .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
        .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
        .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
        .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
        .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}

        .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.02em;font-family:'Nunito',sans-serif;}
        .sp-badge-info{background:rgba(25,118,210,0.10);color:#1565C0;border:1px solid rgba(25,118,210,0.20);}
        .sp-badge-good{background:rgba(16,185,129,0.12);color:#047857;border:1px solid rgba(16,185,129,0.22);}
        .sp-badge-warn{background:rgba(245,158,11,0.12);color:#b45309;border:1px solid rgba(245,158,11,0.22);}

        .pagination{margin-bottom:0;}
        .page-link{border-radius:10px !important;border:1.5px solid var(--border) !important;color:var(--navy) !important;}
        .page-item.active .page-link{background:linear-gradient(135deg,var(--navy),var(--blue)) !important;border-color:transparent !important;color:#fff !important;}

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
</head>
<body>

<div class="d-flex min-vh-100">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>

    @include('layouts.AdminSidebar')

    <main class="flex-fill p-4">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Customers</div>
                        <div class="sp-ph-title">Payment History</div>
                        <div class="sp-ph-sub">Track all customer payments and credit transactions</div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.customers.index') }}" class="sp-btn sp-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Customers
                    </a>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-history"></i> Payment Records</div>
                    <div style="color:rgba(255,255,255,0.7);font-size:12px;font-family:'Nunito',sans-serif;position:relative;z-index:1;">
                        {{ $payments->total() }} record(s)
                    </div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-table-wrap">
                        <table class="sp-table">
                            <thead>
                                <tr>
                                    <th>Payment Date</th>
                                    <th>Reference #</th>
                                    <th>Customer</th>
                                    <th>Payment Amount</th>
                                    <th>Payment Method</th>
                                    <th>Remaining Balance</th>
                                    <th>Cashier</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <span style="font-family:'Nunito',sans-serif;font-weight:700;color:var(--navy);">
                                                {{ $payment->credit->reference_number ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->credit->customer_name ?? 'Walk-in Customer' }}</td>
                                        <td>
                                            <span style="font-weight:700;color:var(--green);">
                                                ₱{{ number_format($payment->payment_amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="sp-badge sp-badge-info">
                                                <i class="fas fa-credit-card"></i>
                                                {{ ucfirst($payment->payment_method) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight:600;color:{{ $payment->remaining_balance_after_payment > 0 ? 'var(--amber)' : 'var(--green)' }};">
                                                ₱{{ number_format($payment->remaining_balance_after_payment, 2) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->cashier->name ?? 'Unknown' }}</td>
                                        <td style="color:var(--muted);">{{ $payment->notes ?? '—' }}</td>
                                        <td>
                                            @if($payment->credit_id)
                                                <a href="{{ route('admin.credits.show', $payment->credit_id) }}"
                                                   class="sp-mini-btn sp-mini-primary"
                                                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:10px;border:1.5px solid rgba(25,118,210,0.22);background:#fff;color:var(--navy);font-size:12px;font-weight:800;font-family:'Nunito',sans-serif;text-decoration:none;transition:all .15s ease;"
                                                   onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 16px rgba(13,71,161,0.10)'"
                                                   onmouseout="this.style.transform='';this.style.boxShadow=''">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @else
                                                <span style="color:var(--muted);font-size:12px;">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5" style="color:var(--muted);">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.35;"></i>
                                            No payment history found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($payments->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
