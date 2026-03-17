@extends('layouts.app')
@section('title', 'Full Credit History')

@push('stylesDashboard')
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Use CashierSidebar */
        .main-content { margin-left: 280px !important; }

        :root {
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

        .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
        .sp-ph-left{display:flex;align-items:center;gap:13px;}
        .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
        .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
        .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
        .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}
        .sp-ph-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap;}

        .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
        .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
        .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
        .sp-btn-teal{background:linear-gradient(135deg,#0e7490,#06b6d4);color:#fff;box-shadow:0 4px 14px rgba(6,182,212,0.26);}
        .sp-btn-teal:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(6,182,212,0.36);color:#fff;}
        .sp-btn-outline{background:var(--card);color:var(--navy);border:1.5px solid var(--border);}
        .sp-btn-outline:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

        .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .45s ease both;}
        .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:flex-start;justify-content:space-between;gap:12px;position:relative;overflow:hidden;}
        .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
        .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
        .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
        .sp-card-head-title i{color:rgba(0,229,255,.85);}
        .sp-card-body{padding:18px 22px;}

        .sp-form .form-label{font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;font-family:'Nunito',sans-serif;display:block;}
        .sp-form .form-control{border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;}
        .sp-form .form-control:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}

        .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:800;font-family:'Nunito',sans-serif;}
        .sp-badge-good{background:rgba(16,185,129,0.12);color:#047857;}
        .sp-badge-warn{background:rgba(245,158,11,0.14);color:#92400e;}
        .sp-badge-muted{background:rgba(107,132,170,0.10);color:var(--muted);}

        .sp-date{font-size:11.5px;font-weight:800;color:var(--muted);letter-spacing:.08em;text-transform:uppercase;font-family:'Nunito',sans-serif;display:flex;align-items:center;gap:8px;}
        .credit-item{border-bottom:1px solid rgba(25,118,210,0.08);transition:background .15s,transform .15s;}
        .credit-item:hover{background:rgba(13,71,161,0.03);transform:translateY(-1px);}
        .credit-item button{border:none !important;box-shadow:none !important;border-radius:14px !important;}
        .credit-item button:hover{background:transparent !important;}
        .credit-item .collapse{border-left:3px solid rgba(25,118,210,0.28);}

        .sp-soft{background:rgba(13,71,161,0.03);border:1px solid var(--border);border-radius:14px;}
        .sp-pay{border-left:3px solid #10b981;}

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
@endpush

@section('content')
<div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>

<div class="sp-wrap">
    <div class="sp-page-head">
        <div class="sp-ph-left">
            <div class="sp-ph-icon"><i class="fas fa-history"></i></div>
            <div>
                <div class="sp-ph-crumb">Cashier / Credits</div>
                <div class="sp-ph-title">Full Credit History</div>
                <div class="sp-ph-sub">All-time credits and payment records for {{ $customer->full_name }}</div>
            </div>
        </div>
        <div class="sp-ph-actions">
            <a href="{{ $returnTo ?: route('cashier.customers.show', $customer->id) }}" class="sp-btn sp-btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="sp-card mb-4">
        <div class="sp-card-head">
            <div>
                <div class="sp-card-head-title"><i class="fas fa-user"></i> {{ $customer->full_name }}</div>
                <div class="sp-ph-sub" style="color:rgba(255,255,255,0.72);margin:6px 0 0;position:relative;z-index:1;">Credit History (All Time)</div>
            </div>
            <div style="position:relative;z-index:1;display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
                <span class="sp-badge sp-badge-muted">Customer ID: #{{ $customer->id }}</span>
                <span class="sp-badge {{ $customer->status == 'active' ? 'sp-badge-good' : 'sp-badge-warn' }}"><i class="fas fa-circle" style="font-size:7px;"></i> {{ ucfirst($customer->status) }}</span>
                <span class="sp-badge sp-badge-muted">Credits: {{ $allCredits->count() }}</span>
            </div>
        </div>
    </div>

    <div class="sp-card mb-4">
        <div class="sp-card-head">
            <div class="sp-card-head-title"><i class="fas fa-chart-line"></i> Lifetime Summary (All Time)</div>
        </div>
        <div class="sp-card-body">
            <div class="sp-soft" style="padding:14px 16px;">
                <div class="row">
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Total Credits (All Time):</strong> {{ $lifetimeSummary->total_credits_all_time ?? 0 }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Lifetime Credit Amount:</strong> ₱{{ number_format($lifetimeSummary->lifetime_credit_amount ?? 0, 2) }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Lifetime Paid Amount:</strong> ₱{{ number_format($lifetimeSummary->lifetime_paid_amount ?? 0, 2) }}</p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>Lifetime Outstanding Balance:</strong> ₱{{ number_format($lifetimeSummary->lifetime_outstanding_balance ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sp-card mb-4">
        <div class="sp-card-head">
            <div class="sp-card-head-title"><i class="fas fa-filter"></i> Filters</div>
        </div>
        <div class="sp-card-body sp-form">
            <form method="GET" action="{{ route('cashier.credit.full-history', $customer->id) }}">
                @if($returnTo)
                    <input type="hidden" name="return_to" value="{{ $returnTo }}">
                @endif
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Credit Status</label>
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="partial" {{ ($filters['status'] ?? '') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ ($filters['status'] ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Credit ID</label>
                        <input type="text" class="form-control" name="credit_id" value="{{ $filters['credit_id'] ?? '' }}" placeholder="Credit #">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Created By</label>
                        <select class="form-control" name="created_by">
                            <option value="">All</option>
                            @foreach($cashiers as $cashier)
                                <option value="{{ $cashier }}" {{ ($filters['created_by'] ?? '') == $cashier ? 'selected' : '' }}>{{ $cashier }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="submit" class="sp-btn sp-btn-primary"><i class="fas fa-search"></i> Apply Filters</button>
                        <a href="{{ route('cashier.credit.full-history', $customer->id) . ($returnTo ? ('?return_to=' . urlencode($returnTo)) : '') }}" class="sp-btn sp-btn-outline"><i class="fas fa-times"></i> Clear Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="sp-card">
        <div class="sp-card-head">
            <div class="sp-card-head-title"><i class="fas fa-history"></i> Credit History</div>
            <div style="position:relative;z-index:1;display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                <button type="button" class="sp-btn sp-btn-outline" style="padding:7px 12px;font-size:12px;" onclick="exportToPDF()"><i class="fas fa-file-pdf"></i> Export PDF</button>
                <button type="button" class="sp-btn sp-btn-teal" style="padding:7px 12px;font-size:12px;" onclick="exportToCSV()"><i class="fas fa-file-csv"></i> Export CSV</button>
            </div>
        </div>
        <div class="sp-card-body">
            @forelse($groupedCredits as $date => $dateCredits)
                <div class="mb-3">
                    <div class="sp-date mb-2"><i class="fas fa-calendar-day" style="opacity:.6;"></i> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</div>

                    @foreach($dateCredits as $credit)
                        <div class="credit-item mb-2">
                            <button type="button" class="w-100 text-start p-3 border rounded bg-white" data-bs-toggle="collapse" data-bs-target="#credit-{{ $credit->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>₱{{ number_format($credit->credit_amount, 2) }}</strong>
                                        <div class="text-muted small">Credit #{{ $credit->id }}</div>
                                        <div class="small">Balance: ₱{{ number_format($credit->remaining_balance, 2) }}</div>
                                    </div>
                                    <span class="badge {{ $credit->status == 'paid' ? 'bg-success' : ($credit->status == 'partial' ? 'bg-info' : 'bg-warning') }}">
                                        {{ ucfirst($credit->status) }}
                                    </span>
                                </div>
                            </button>

                            <div id="credit-{{ $credit->id }}" class="collapse mt-2">
                                <div class="p-3 sp-soft">
                                    <div class="small text-muted mb-2">Created by: {{ $credit->cashier->name ?? 'Cashier' }}</div>

                                    <div class="row mb-2">
                                        <div class="col-md-6"><strong>Credit Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</div>
                                        <div class="col-md-6"><strong>Total Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-6"><strong>Remaining Balance:</strong> ₱{{ number_format($credit->remaining_balance, 2) }}</div>
                                        <div class="col-md-6"><strong>Credit Date:</strong> {{ \Carbon\Carbon::parse($credit->date)->format('M d, Y') }}</div>
                                    </div>

                                    <div class="mt-3">
                                        <strong class="d-block mb-2">Payment History</strong>
                                        @if($credit->payments && $credit->payments->count() > 0)
                                            @foreach($credit->payments as $payment)
                                                <div class="sp-soft sp-pay p-2 mb-2">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <div><strong>₱{{ number_format($payment->payment_amount, 2) }}</strong></div>
                                                            <div class="text-muted small">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="small">{{ $payment->payment_method }}</div>
                                                            @if($payment->notes)
                                                                <div class="text-muted small">{{ $payment->notes }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-muted small">No payments recorded.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-muted text-center">No credit history found.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function exportToCSV() {
    const rows = [];
    rows.push(['Credit ID','Credit Date','Amount','Paid','Balance','Status','Created By']);

    const grouped = @json($groupedCredits);
    Object.keys(grouped || {}).forEach(date => {
        (grouped[date] || []).forEach(c => {
            rows.push([
                c.id,
                c.date,
                c.credit_amount,
                c.paid_amount,
                c.remaining_balance,
                c.status,
                (c.cashier && c.cashier.name) ? c.cashier.name : 'Cashier'
            ]);
        });
    });

    const csv = rows.map(r => r.map(v => `"${String(v ?? '').replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'credit-history-{{ $customer->id }}.csv';
    a.click();
    URL.revokeObjectURL(url);
}

function exportToPDF() {
    window.print();
}
</script>
@endpush
