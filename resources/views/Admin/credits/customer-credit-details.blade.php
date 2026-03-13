<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Credit Details - {{ $customer->full_name }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
            --green:#10b981;--red:#ef4444;--amber:#f59e0b;
            --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
            --text:#1a2744;--muted:#6b84aa;
        }

        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
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

        .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
        .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
        .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
        .sp-btn-outline{background:var(--card);color:var(--navy);border:1.5px solid var(--border);}
        .sp-btn-outline:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

        .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .45s ease both;}
        .sp-card-head{padding:18px 26px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);position:relative;overflow:hidden;}
        .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
        .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
        .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:16px;font-weight:900;color:#fff;display:flex;align-items:center;gap:9px;position:relative;z-index:1;}
        .sp-card-head-title i{color:rgba(0,229,255,.85);}
        .sp-card-head-sub{font-size:12px;color:rgba(255,255,255,0.65);margin-top:4px;position:relative;z-index:1;}
        .sp-card-body{padding:20px 26px;}

        .sp-stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
        @media(max-width:992px){.sp-stats-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:576px){.sp-stats-grid{grid-template-columns:1fr}}
        .sp-stat{background:rgba(255,255,255,0.7);border:1px solid var(--border);border-radius:16px;padding:14px 16px;}
        .sp-stat-k{font-size:10px;font-weight:900;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);font-family:'Nunito',sans-serif;}
        .sp-stat-v{font-family:'Nunito',sans-serif;font-size:18px;font-weight:900;color:var(--navy);margin-top:6px;}

        .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:800;font-family:'Nunito',sans-serif;}
        .sp-badge-good{background:rgba(16,185,129,0.12);color:#047857;}
        .sp-badge-warn{background:rgba(245,158,11,0.14);color:#92400e;}
        .sp-badge-paid{background:rgba(16,185,129,0.12);color:#047857;}
        .sp-badge-active{background:rgba(245,158,11,0.14);color:#92400e;}
        .sp-badge-muted{background:rgba(107,132,170,0.10);color:var(--muted);}

        .sp-date-group{background:rgba(13,71,161,0.03);border:1px solid var(--border);border-radius:16px;padding:14px 16px;margin-bottom:14px;}
        .sp-date-header{display:flex;align-items:center;gap:10px;font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);margin-bottom:10px;}
        .sp-credit-card{border:1px solid rgba(25,118,210,0.10);border-radius:16px;overflow:hidden;background:#fff;box-shadow:0 3px 16px rgba(13,71,161,0.06);}
        .sp-credit-card + .sp-credit-card{margin-top:10px;}
        .sp-credit-card-body{padding:14px 16px;}
        .sp-payment-item{border-left:3px solid #10b981;margin-bottom:10px;padding-left:12px;}
        .sp-muted{color:var(--muted);}

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
</head>
<body>

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>

        <main class="flex-fill p-4" style="position:relative;z-index:1;">
            <div class="sp-wrap">

                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Finance / Credits</div>
                            <div class="sp-ph-title">{{ $customer->full_name }}</div>
                            <div class="sp-ph-sub">Active credits and recent payments</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.credits.index') }}" class="sp-btn sp-btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Credit Management
                    </a>
                </div>

                <!-- SECTION 1: Header (Strict Operational Scope) -->
                <div class="sp-card" style="margin-bottom:16px;">
                    <div class="sp-card-head">
                        <div>
                            <div class="sp-card-head-title"><i class="fas fa-credit-card"></i> Customer Credit Snapshot</div>
                            <div class="sp-card-head-sub">Operational View - Active Credits Only</div>
                        </div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-stats-grid" style="margin-bottom:12px;">
                            <div class="sp-stat">
                                <div class="sp-stat-k">Active Credits</div>
                                <div class="sp-stat-v">{{ $activeSummary->active_credits }}</div>
                            </div>
                            <div class="sp-stat">
                                <div class="sp-stat-k">Active Credit Amount</div>
                                <div class="sp-stat-v">₱{{ number_format($activeSummary->active_credit_amount, 2) }}</div>
                            </div>
                            <div class="sp-stat">
                                <div class="sp-stat-k">Total Paid (Active)</div>
                                <div class="sp-stat-v">₱{{ number_format($activeSummary->total_paid_active, 2) }}</div>
                            </div>
                            <div class="sp-stat">
                                <div class="sp-stat-k">Outstanding Balance</div>
                                <div class="sp-stat-v">₱{{ number_format($activeSummary->outstanding_balance, 2) }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center" style="gap:10px;flex-wrap:wrap;">
                            <span class="sp-badge {{ $status == 'Good Standing' ? 'sp-badge-good' : 'sp-badge-warn' }}">
                                <i class="fas fa-circle" style="font-size:7px;"></i> Status: {{ $status }}
                            </span>
                            <span class="sp-muted" style="font-size:12px;">Customer ID: #{{ $customer->id }}</span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Active Credits (Primary Operational View) -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="sp-card">
                            <div class="sp-card-head">
                                <div>
                                    <div class="sp-card-head-title"><i class="fas fa-exclamation-triangle"></i> Active Credits (Actionable)</div>
                                    <div class="sp-card-head-sub">Credits requiring attention or payment</div>
                                </div>
                            </div>
                            <div class="sp-card-body">
                                @forelse($activeCredits as $date => $dateCredits)
                                    <div class="sp-date-group">
                                        <div class="sp-date-header">
                                            <i class="fas fa-calendar-day" style="opacity:.7;"></i>
                                            <span>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
                                        </div>
                                        
                                        @foreach($dateCredits as $credit)
                                            <div class="sp-credit-card">
                                                <div class="sp-credit-card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-8">
                                                            <h6 class="mb-2">Credit #{{ $credit->id }}</h6>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Created by:</strong> {{ $credit->cashier->name ?? 'Admin' }}</p>
                                                                    <p class="mb-1"><strong>Credit Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</p>
                                                                    <p class="mb-1"><strong>Total Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong>Remaining:</strong> 
                                                                        <span class="{{ $credit->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                            ₱{{ number_format($credit->remaining_balance, 2) }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="mb-1"><strong>Status:</strong> 
                                                                        <span class="sp-badge {{ $credit->status == 'paid' ? 'sp-badge-paid' : ($credit->status == 'active' ? 'sp-badge-active' : 'sp-badge-muted') }}">
                                                                            <i class="fas fa-circle" style="font-size:7px;"></i> {{ ucfirst($credit->status) }}
                                                                        </span>
                                                                    </p>
                                                                    <p class="mb-1"><strong>Created:</strong> {{ \Carbon\Carbon::parse($credit->created_at)->format('M d, Y h:i A') }}</p>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Payments (Expandable) -->
                                                            @if($credit->payments->count() > 0)
                                                                <div class="mt-3">
                                                                    <button class="sp-btn sp-btn-outline" style="padding:6px 12px;font-size:12px;" onclick="togglePayments({{ $credit->id }})">
                                                                        <i class="fas fa-eye me-1"></i> View Payments ({{ $credit->payments->count() }})
                                                                    </button>
                                                                    
                                                                    <div id="payments-{{ $credit->id }}" class="mt-3" style="display: none;">
                                                                        <div class="p-3" style="background:rgba(13,71,161,0.03);border:1px solid var(--border);border-radius:14px;">
                                                                            <h6 class="mb-3">Payments for Credit #{{ $credit->id }}</h6>
                                                                            @foreach($credit->payments as $payment)
                                                                                <div class="sp-payment-item">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <strong>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}</strong>
                                                                                            <span class="text-muted ms-2">by {{ $payment->cashier->name ?? 'Admin' }}</span>
                                                                                            @if($payment->notes)
                                                                                                <br><small class="text-muted">{{ $payment->notes }}</small>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div>
                                                                                            <strong>₱{{ number_format($payment->payment_amount, 2) }}</strong>
                                                                                            <br><small class="text-muted">{{ $payment->payment_method }}</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                            
                                                                            <hr>
                                                                            <div class="d-flex justify-content-between">
                                                                                <strong>Total Paid:</strong>
                                                                                <strong>₱{{ number_format($credit->paid_amount, 2) }}</strong>
                                                                            </div>
                                                                            <div class="d-flex justify-content-between">
                                                                                <span>Remaining:</span>
                                                                                <strong class="{{ $credit->remaining_balance > 0 ? 'text-danger' : 'text-success' }}">
                                                                                    ₱{{ number_format($credit->remaining_balance, 2) }}
                                                                                </strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="mt-3">
                                                                    <span class="text-muted">No payments yet</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="col-md-4 text-end">
                                                            <div class="btn-group" role="group">
                                                                @if($credit->remaining_balance > 0)
                                                                    <button class="sp-btn sp-btn-primary" style="padding:6px 12px;font-size:12px;" onclick="addPayment({{ $credit->id }}, {{ $credit->remaining_balance }})">
                                                                        <i class="fas fa-plus me-1"></i> Add Payment
                                                                    </button>
                                                                @endif
                                                                @if($credit->payments->count() > 0)
                                                                    <button class="sp-btn sp-btn-outline" style="padding:6px 12px;font-size:12px;" onclick="togglePayments({{ $credit->id }})">
                                                                        <i class="fas fa-list me-1"></i> View Payments
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <h5 class="text-muted">No Active Credits</h5>
                                        <p class="text-muted">This customer has no outstanding credits requiring attention.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Recently Paid Credits (Secondary Context) -->
                @if($recentlyPaidCredits->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="sp-card">
                                <div class="sp-card-head">
                                    <div>
                                        <div class="sp-card-head-title"><i class="fas fa-check-circle"></i> Recently Paid Credits (Context Only)</div>
                                        <div class="sp-card-head-sub">Last 3 paid credits - shown for reference only</div>
                                    </div>
                                </div>
                                <div class="sp-card-body">
                                    @foreach($recentlyPaidCredits as $date => $dateCredits)
                                        <div class="sp-date-group">
                                            <div class="sp-date-header">
                                                <i class="fas fa-calendar-day" style="opacity:.7;"></i>
                                                <span>{{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
                                            </div>
                                            
                                            @foreach($dateCredits as $credit)
                                                <div class="sp-credit-card" style="opacity:.78;">
                                                    <div class="sp-credit-card-body">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-8">
                                                                <h6 class="mb-2">Credit #{{ $credit->id }}</h6>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Created by:</strong> {{ $credit->cashier->name ?? 'Admin' }}</p>
                                                                        <p class="mb-1"><strong>Credit Amount:</strong> ₱{{ number_format($credit->credit_amount, 2) }}</p>
                                                                        <p class="mb-1"><strong>Total Paid:</strong> ₱{{ number_format($credit->paid_amount, 2) }}</p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Status:</strong> 
                                                                            <span class="sp-badge sp-badge-paid"><i class="fas fa-circle" style="font-size:7px;"></i> Paid</span>
                                                                        </p>
                                                                        <p class="mb-1"><strong>Paid Date:</strong> {{ \Carbon\Carbon::parse($credit->updated_at)->format('M d, Y') }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-md-4 text-end">
                                                                <button class="sp-btn sp-btn-outline" style="padding:6px 12px;font-size:12px;" onclick="viewCreditDetails({{ $credit->id }})"><i class="fas fa-eye"></i> View Details</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- SECTION 4: Full History Access -->
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button class="sp-btn sp-btn-primary" style="padding:12px 22px;font-size:14px;" onclick="viewFullHistory({{ $customer->id }})">
                            <i class="fas fa-history me-2"></i> View Full Credit History
                        </button>
                        <p class="text-muted mt-2">Access complete credit and payment history with filtering options</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function togglePayments(creditId) {
    const paymentsDiv = document.getElementById(`payments-${creditId}`);
    if (paymentsDiv.style.display === 'none') {
        paymentsDiv.style.display = 'block';
    } else {
        paymentsDiv.style.display = 'none';
    }
}

function addPayment(creditId, maxAmount) {
    Swal.fire({
        title: 'Add Payment',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Payment Amount (Max: ₱${maxAmount.toFixed(2)})</label>
                    <input type="number" class="form-control" id="payment_amount" step="0.01" min="0.01" max="${maxAmount}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" id="payment_method">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="payment_notes" rows="2" placeholder="Add any notes about this payment"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Add Payment',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const amount = parseFloat(document.getElementById('payment_amount').value);
            if (!amount || amount <= 0 || amount > maxAmount) {
                Swal.showValidationMessage(`Please enter a valid amount between ₱0.01 and ₱${maxAmount.toFixed(2)}`);
                return false;
            }
            
            return {
                payment_amount: amount,
                payment_method: document.getElementById('payment_method').value,
                notes: document.getElementById('payment_notes').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const paymentData = result.value;
            
            fetch(`/admin/credits/${creditId}/make-payment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paymentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Added',
                        text: 'Payment has been recorded successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'Failed to add payment.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding payment.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function viewCreditDetails(creditId) {
    window.location.href = `/admin/credits/${creditId}`;
}

function viewFullHistory(customerId) {
    window.location.href = `/admin/credits/customer/${customerId}/full-history`;
}
</script>

</html>
