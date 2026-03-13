@extends('layouts.app')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
        --green:#10b981;--red:#ef4444;--amber:#f59e0b;
        --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
        --text:#1a2744;--muted:#6b84aa;
    }

    .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
    .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
    .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
    .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
    .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif;}

    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
    .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-outline{background:var(--card);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-outline:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .45s ease both;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
    .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-card-body{padding:18px 22px;}

    .sp-kv{width:100%;border-collapse:separate;border-spacing:0 8px;}
    .sp-kv td{padding:0;font-size:13px;color:var(--text);vertical-align:top;}
    .sp-kv td:first-child{width:40%;color:var(--muted);font-weight:800;font-family:'Nunito',sans-serif;letter-spacing:.02em;}
    .sp-kv td:last-child{font-weight:700;}

    .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:800;font-family:'Nunito',sans-serif;}
    .sp-badge-green{background:rgba(16,185,129,0.12);color:#047857;}
    .sp-badge-amber{background:rgba(245,158,11,0.14);color:#92400e;}
    .sp-badge-red{background:rgba(239,68,68,0.10);color:#b91c1c;}
    .sp-badge-blue{background:rgba(13,71,161,0.10);color:var(--navy);}

    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}

    .sp-alert{border:1px solid var(--border);border-radius:14px;padding:14px 16px;display:flex;align-items:flex-start;gap:10px;}
    .sp-alert-success{background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.18);}
    .sp-alert-info{background:rgba(13,71,161,0.05);}
    .sp-alert i{margin-top:2px;}

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@section('content')
    <div class="d-flex min-vh-100">

        <div class="sp-bg">
            <div class="sp-blob sp-blob-1"></div>
            <div class="sp-blob sp-blob-2"></div>
        </div>

        <main class="flex-fill p-4" style="position:relative;z-index:1;">
            <div class="sp-wrap">

                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-receipt"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Finance / Credits</div>
                            <div class="sp-ph-title">Credit Details</div>
                            <div class="sp-ph-sub">Reference #{{ $credit->id }} and payment history</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.credits.index') }}" class="sp-btn sp-btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Credits
                    </a>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-circle-info"></i> Details</div>
                    </div>
                    <div class="sp-card-body">

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-2" style="font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);">Credit Information</div>
                                <table class="sp-kv">
                                        <tr>
                                            <td><strong>Credit ID:</strong></td>
                                            <td>#{{ $credit->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Customer:</strong></td>
                                            <td>{{ $credit->customer->name ?? $credit->customer_name ?? 'Walk-in Customer' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Credit Amount:</strong></td>
                                            <td>₱{{ number_format($credit->credit_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paid Amount:</strong></td>
                                            <td>₱{{ number_format($credit->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining Balance:</strong></td>
                                            <td class="fw-bold text-{{ $credit->remaining_balance > 0 ? 'danger' : 'success' }}">
                                                ₱{{ number_format($credit->remaining_balance, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ $credit->date->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="sp-badge {{ $credit->status == 'active' ? 'sp-badge-blue' : ($credit->status == 'paid' ? 'sp-badge-green' : 'sp-badge-red') }}">
                                                    <i class="fas fa-circle" style="font-size:7px;"></i> {{ ucfirst($credit->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($credit->notes)
                                        <tr>
                                            <td><strong>Notes:</strong></td>
                                            <td>{{ $credit->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-2" style="font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);">Payment History</div>
                                    @if($credit->payments->count() > 0)
                                        <div class="sp-table-wrap">
                                            <table class="sp-table">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Method</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($credit->payments as $payment)
                                                        <tr>
                                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                                            <td>₱{{ number_format($payment->payment_amount ?? $payment->amount ?? 0, 2) }}</td>
                                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted">No payments recorded yet.</p>
                                    @endif
                                </div>
                            </div>

                            @if($credit->remaining_balance > 0)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="sp-alert sp-alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <div>
                                                <strong>Outstanding Balance:</strong> ₱{{ number_format($credit->remaining_balance, 2) }}
                                                <br>
                                                <small>Return to the credits list to make a payment.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-12">
                                        <div class="sp-alert sp-alert-success">
                                            <i class="fas fa-check-circle"></i>
                                            <div>
                                                <strong>Fully Paid:</strong> This credit has been completely settled.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
