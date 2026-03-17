@extends('layouts.app')
@section('title', 'Customer Details')

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

        :root{
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

        .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;}

        .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
        .sp-ph-left{display:flex;align-items:center;gap:13px;}
        .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
        .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
        .sp-ph-title{font-family:'Nunito',sans-serif;font-size:22px;font-weight:900;color:var(--navy);line-height:1.1;}
        .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

        .sp-btn-outline-back{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;color:var(--navy);background:var(--card);border:1.5px solid var(--border);text-decoration:none;transition:all .2s ease;white-space:nowrap;}
        .sp-btn-outline-back:hover{background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px);}

        .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:800;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
        .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
        .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
        .sp-btn-good{background:linear-gradient(135deg,#059669,#10b981);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,0.24);}
        .sp-btn-good:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(16,185,129,0.34);color:#fff;}
        .sp-btn-danger{background:linear-gradient(135deg,#b91c1c,#ef4444);color:#fff;box-shadow:0 4px 14px rgba(239,68,68,0.22);}
        .sp-btn-danger:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(239,68,68,0.30);color:#fff;}
        .sp-btn-soft{background:rgba(13,71,161,0.08);color:var(--navy);border:1.5px solid rgba(25,118,210,0.14);}
        .sp-btn-soft:hover{background:rgba(13,71,161,0.12);color:var(--navy);}

        .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .55s ease both;}
        .sp-card-head{padding:18px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);position:relative;overflow:hidden;}
        .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
        .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
        .sp-card-head-row{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;position:relative;z-index:1;flex-wrap:wrap;}
        .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:#fff;line-height:1.1;margin:0;}
        .sp-card-head-sub{display:flex;align-items:center;gap:10px;flex-wrap:wrap;color:rgba(255,255,255,0.82);font-size:12.5px;font-weight:700;}

        .sp-card-body{padding:18px 22px;}

        .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:900;letter-spacing:.02em;font-family:'Nunito',sans-serif;}
        .sp-badge-good{background:rgba(16,185,129,0.12);color:#047857;border:1px solid rgba(16,185,129,0.22);}
        .sp-badge-bad{background:rgba(239,68,68,0.12);color:#b91c1c;border:1px solid rgba(239,68,68,0.22);}
        .sp-badge-warn{background:rgba(245,158,11,0.12);color:#b45309;border:1px solid rgba(245,158,11,0.22);}
        .sp-badge-info{background:rgba(6,182,212,0.12);color:#0369a1;border:1px solid rgba(6,182,212,0.22);}

        .sp-card-head .sp-badge-good{background:rgba(16,185,129,0.22);color:#fff;border-color:rgba(16,185,129,0.32);}
        .sp-card-head .sp-badge-bad{background:rgba(239,68,68,0.22);color:#fff;border-color:rgba(239,68,68,0.32);}
        .sp-card-head .sp-badge-warn{background:rgba(245,158,11,0.24);color:#fff;border-color:rgba(245,158,11,0.34);}
        .sp-card-head .sp-badge-info{background:rgba(6,182,212,0.24);color:#fff;border-color:rgba(6,182,212,0.34);}

        .sp-subcard{background:rgba(235,243,251,0.65);border:1px solid rgba(25,118,210,0.12);border-radius:16px;padding:14px 14px;}
        .sp-subcard h6{font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);margin-bottom:10px;}
        .sp-subcard p{margin-bottom:6px;color:var(--text);font-size:13px;}
        .sp-subcard p:last-child{margin-bottom:0;}

        .sp-section-title{font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);font-size:14px;margin:0;display:flex;align-items:center;gap:8px;}
        .sp-section-sub{color:var(--muted);font-size:12px;font-weight:600;}

        .sp-swal.swal2-popup{border-radius:18px !important;border:1px solid var(--border) !important;box-shadow:0 22px 50px rgba(13,71,161,0.18) !important;padding:0 !important;overflow:hidden;}
        .sp-swal .swal2-title{margin:0 !important;padding:16px 20px !important;text-align:left !important;font-family:'Nunito',sans-serif !important;font-size:15px !important;font-weight:900 !important;color:#fff !important;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%) !important;}
        .sp-swal .swal2-html-container{margin:0 !important;padding:18px 20px 8px !important;text-align:left !important;color:var(--text) !important;font-family:'Plus Jakarta Sans',sans-serif !important;}
        .sp-swal .swal2-actions{margin:0 !important;padding:14px 20px 18px !important;gap:10px !important;justify-content:flex-end !important;background:rgba(13,71,161,0.02) !important;border-top:1px solid var(--border) !important;}

        .sp-swal .swal2-validation-message{margin:0 !important;border-radius:0 !important;background:rgba(239,68,68,0.08) !important;color:#b91c1c !important;font-family:'Plus Jakarta Sans',sans-serif !important;}

        .sp-swal .sp-label{display:block;font-size:11.5px;font-weight:800;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:7px;font-family:'Nunito',sans-serif;}
        .sp-swal .sp-hint{font-size:11.5px;color:var(--muted);margin-top:6px;}
        .sp-swal .sp-input,.sp-swal .sp-select,.sp-swal .sp-textarea{width:100%;border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;}
        .sp-swal .sp-input:focus,.sp-swal .sp-select:focus,.sp-swal .sp-textarea:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}
        .sp-swal .sp-textarea{resize:vertical;min-height:92px;}

        .sp-swal-confirm{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:11px;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:#fff;border:none;background:linear-gradient(135deg,var(--navy),var(--blue));box-shadow:0 4px 14px rgba(13,71,161,0.26);transition:all .2s ease;}
        .sp-swal-confirm:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);}
        .sp-swal-cancel{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:11px;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:800;color:var(--navy);border:1.5px solid var(--border);background:var(--card);transition:all .2s ease;}
        .sp-swal-cancel:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
@endpush

@section('content')
<div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>

<div class="sp-wrap">
    <div class="sp-page-head">
        <div class="sp-ph-left">
            <div class="sp-ph-icon"><i class="fas fa-user"></i></div>
            <div>
                <div class="sp-ph-crumb">Cashier</div>
                <div class="sp-ph-title">Customer Profile</div>
                <div class="sp-ph-sub">View customer details and credit overview</div>
            </div>
        </div>
        <a href="{{ route('cashier.customers.index') }}" class="sp-btn-outline-back"><i class="fas fa-arrow-left"></i> Back to Customers</a>
    </div>

    <div class="sp-card" style="margin-bottom:18px;">
        <div class="sp-card-head">
            <div class="sp-card-head-row">
                <div>
                    <h2 class="sp-card-head-title">{{ $customerDetails->full_name }}</h2>
                    <div class="sp-card-head-sub" style="margin-top:8px;">
                        @if($customerDetails->status == 'active')
                            <span class="sp-badge sp-badge-good"><i class="fas fa-check-circle"></i> Active</span>
                        @else
                            <span class="sp-badge sp-badge-bad"><i class="fas fa-ban"></i> {{ ucfirst($customerDetails->status) }}</span>
                        @endif
                        <span>Customer ID: #{{ $customerDetails->customer_id }}</span>
                    </div>
                </div>
                <div class="d-flex" style="gap:10px;flex-wrap:wrap;">
                    <a class="sp-btn sp-btn-soft" href="{{ route('cashier.customers.edit', $customer->id) }}"><i class="fas fa-edit"></i> Edit</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="sp-card" style="margin-bottom:18px;">
                <div class="sp-card-body">
                    <div class="d-flex align-items-center justify-content-between" style="gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div class="sp-section-title"><i class="fas fa-user"></i> Customer Information</div>
                            <div class="sp-section-sub">Contact + account details</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="sp-subcard" style="margin-bottom:12px;">
                                <h6>Contact Information</h6>
                                <p><strong>Phone:</strong> {{ $customerDetails->phone ?? 'N/A' }}</p>
                                <p><strong>Email:</strong> {{ $customerDetails->email ?? 'N/A' }}</p>
                                <p><strong>Address:</strong> {{ $customerDetails->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sp-subcard" style="margin-bottom:12px;">
                                <h6>Account Details</h6>
                                <p><strong>Max Credit Limit:</strong> ₱{{ number_format($customerDetails->max_credit_limit, 2) }}</p>
                                <p><strong>Created At:</strong> {{ \Carbon\Carbon::parse($customerDetails->created_at)->format('M d, Y h:i A') }}</p>
                                <p><strong>Created By:</strong> {{ $customerDetails->created_by }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-body">
                    <div class="d-flex align-items-center justify-content-between" style="gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div class="sp-section-title"><i class="fas fa-history"></i> Recent Credits</div>
                            <div class="sp-section-sub">Showing last 3 credits</div>
                        </div>
                    </div>
                    @forelse($recentCredits as $credit)
                        <div style="border-bottom:1px solid rgba(25,118,210,0.10);padding-bottom:14px;margin-bottom:14px;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-1">
                                        {{ \Carbon\Carbon::parse($credit->date)->format('M d, Y') }}
                                        @if(($credit->count ?? 1) > 1)
                                            <small class="text-muted">({{ (int) $credit->count }} credits)</small>
                                        @endif
                                    </h6>
                                    <p class="text-muted mb-1"><small>{{ \Carbon\Carbon::parse($credit->created_at)->format('M d, Y h:i A') }}</small></p>
                                    <p class="mb-0"><small>Cashier: {{ $credit->cashier_name ?? 'Unknown' }}</small></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-1"><strong>Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</p>
                                    <p class="mb-1"><strong>Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</p>
                                    <p class="mb-0">
                                        @if($credit->remaining_balance > 0)
                                            <span class="sp-badge sp-badge-warn"><i class="fas fa-exclamation-circle"></i> Balance: ₱{{ number_format($credit->remaining_balance, 2) }}</span>
                                        @else
                                            <span class="sp-badge sp-badge-good"><i class="fas fa-check-circle"></i> Balance: ₱{{ number_format($credit->remaining_balance, 2) }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No credit history found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="sp-card" style="margin-bottom:18px;">
                <div class="sp-card-head">
                    <div class="sp-card-head-row">
                        <div>
                            <div class="sp-card-head-title" style="font-size:16px;"><i class="fas fa-credit-card" style="color:rgba(0,229,255,.85);margin-right:8px;"></i>Quick Credit Information</div>
                        </div>
                    </div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-subcard" style="margin-bottom:12px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Credits:</span>
                            <strong>{{ $customerDetails->total_credits }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Amount:</span>
                            <strong>₱{{ number_format($customerDetails->total_credit, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Paid:</span>
                            <strong>₱{{ number_format($customerDetails->total_paid, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Outstanding Balance:</span>
                            @if($customerDetails->outstanding_balance > 0)
                                <span class="sp-badge sp-badge-warn"><i class="fas fa-exclamation-circle"></i> ₱{{ number_format($customerDetails->outstanding_balance, 2) }}</span>
                            @else
                                <span class="sp-badge sp-badge-good"><i class="fas fa-check-circle"></i> ₱{{ number_format($customerDetails->outstanding_balance, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-center">
                        @if($customerDetails->credit_status == 'Fully Paid')
                            <span class="sp-badge sp-badge-good"><i class="fas fa-check-circle"></i> {{ $customerDetails->credit_status }}</span>
                        @elseif($customerDetails->credit_status == 'Good Standing')
                            <span class="sp-badge sp-badge-info"><i class="fas fa-info-circle"></i> {{ $customerDetails->credit_status }}</span>
                        @else
                            <span class="sp-badge sp-badge-warn"><i class="fas fa-exclamation-circle"></i> {{ $customerDetails->credit_status }}</span>
                        @endif
                    </div>

                    @if($customerDetails->last_credit_date)
                        <div class="mt-3 text-center">
                            <small class="text-muted">Last Credit: {{ \Carbon\Carbon::parse($customerDetails->last_credit_date)->format('M d, Y') }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-body">
                    <div class="d-flex align-items-center justify-content-between" style="gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                        <div>
                            <div class="sp-section-title"><i class="fas fa-bolt"></i> Quick Actions</div>
                            <div class="sp-section-sub">Use quick actions for common tasks. For detailed history, open Credits page.</div>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <a class="sp-btn sp-btn-primary" href="{{ route('cashier.credit.create', ['return_to' => route('cashier.customers.show', $customer->id)]) }}"><i class="fas fa-plus"></i> Add Credit</a>
                        @if(($customerDetails->outstanding_balance ?? 0) > 0)
                            <button type="button" class="sp-btn sp-btn-soft" onclick="makePayment()"><i class="fas fa-money-bill"></i> Make Payment</button>
                        @else
                            <button type="button" class="sp-btn sp-btn-soft" disabled title="No outstanding balance"><i class="fas fa-money-bill"></i> Make Payment</button>
                            <div class="text-center text-muted small mt-1">No Outstanding Balance</div>
                        @endif
                        <a class="sp-btn sp-btn-soft" href="{{ route('cashier.credit.full-history', ['customer' => $customer->id, 'return_to' => route('cashier.customers.show', $customer->id)]) }}"><i class="fas fa-list"></i> Full Credit History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function makePayment() {
    const credits = @json(($activeCredits ?? collect())->values());

    if (!Array.isArray(credits) || credits.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'No Active Credits',
            text: 'No outstanding credit balance found for this customer.'
        });
        return;
    }

    const opts = credits.map(c => {
        const bal = parseFloat(c.remaining_balance || 0);
        return `<option value="${c.id}" data-balance="${bal}">Balance: ₱${bal.toFixed(2)}</option>`;
    }).join('');

    const firstBal = parseFloat(credits[0].remaining_balance || 0);

    Swal.fire({
        title: 'Make Payment',
        customClass: {
            popup: 'sp-swal',
            confirmButton: 'sp-swal-confirm',
            cancelButton: 'sp-swal-cancel'
        },
        
        buttonsStyling: false,
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="sp-label">Credit</label>
                    <select class="sp-select" id="payment_credit_id" required>
                        ${opts}
                    </select>
                </div>
                <div class="mb-3">
                    <label class="sp-label">Payment Amount</label>
                    <input type="number" class="sp-input" id="payment_amount" step="0.01" min="0.01" max="${firstBal}" value="${firstBal.toFixed(2)}" placeholder="Enter payment amount" required>
                    <div class="sp-hint">Outstanding Balance: ₱<span id="payment_balance_hint">${firstBal.toFixed(2)}</span></div>
                </div>
                <div class="mb-3">
                    <label class="sp-label">Payment Method</label>
                    <select class="sp-select" id="payment_method" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="sp-label">Notes (Optional)</label>
                    <textarea class="sp-textarea" id="payment_notes" rows="3" placeholder="Add any notes about this payment"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check-circle"></i> Make Payment',
        cancelButtonText: 'Cancel',
        didOpen: () => {
            const creditEl = document.getElementById('payment_credit_id');
            const amountEl = document.getElementById('payment_amount');
            const hintEl = document.getElementById('payment_balance_hint');

            function syncBalance() {
                const opt = creditEl.options[creditEl.selectedIndex];
                const bal = parseFloat(opt.getAttribute('data-balance') || '0') || 0;
                amountEl.max = String(bal);
                amountEl.value = bal.toFixed(2);
                hintEl.textContent = bal.toFixed(2);
            }

            if (creditEl) {
                creditEl.addEventListener('change', syncBalance);
                syncBalance();
            }
        },
        preConfirm: () => {
            const creditId = document.getElementById('payment_credit_id').value;
            const amount = parseFloat(document.getElementById('payment_amount').value);
            const method = document.getElementById('payment_method').value;
            const notes = document.getElementById('payment_notes').value.trim();
            const opt = document.getElementById('payment_credit_id').options[document.getElementById('payment_credit_id').selectedIndex];
            const maxAmount = parseFloat(opt.getAttribute('data-balance') || '0') || 0;

            if (!creditId) {
                Swal.showValidationMessage('Please select a credit.');
                return false;
            }
            if (!amount || amount <= 0 || amount > maxAmount) {
                Swal.showValidationMessage(`Please enter a valid amount between ₱0.01 and ₱${maxAmount.toFixed(2)}`);
                return false;
            }
            if (!method) {
                Swal.showValidationMessage('Please select a payment method.');
                return false;
            }

            return { creditId, amount, method, notes };
        }
    }).then(async (result) => {
        if (!result.isConfirmed) return;

        const payload = result.value;
        const url = `{{ url('/cashier/credit') }}/${payload.creditId}/payments`;

        try {
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    payment_amount: payload.amount,
                    payment_method: payload.method,
                    notes: payload.notes || null
                })
            });

            const data = await resp.json();
            if (!resp.ok || !data.success) {
                const msg = (data && data.message) ? data.message : 'Payment failed. Please try again.';
                throw new Error(msg);
            }

            await Swal.fire({
                icon: 'success',
                title: 'Payment recorded',
                text: 'Payment has been recorded successfully.'
            });

            window.location.reload();
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Payment failed',
                text: e && e.message ? e.message : 'Payment failed. Please try again.'
            });
        }
    });
}
</script>
@endpush
