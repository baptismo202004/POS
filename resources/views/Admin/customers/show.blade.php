<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Profile - {{ $customerDetails->full_name }}</title>

    <!-- Bootstrap CSS -->
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
</head>
<body>

    <div class="d-flex min-vh-100">
        <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="sp-wrap">

                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Admin</div>
                            <div class="sp-ph-title">Customer Profile</div>
                            <div class="sp-ph-sub">View customer details and credit overview</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.customers.index') }}" class="sp-btn-outline-back"><i class="fas fa-arrow-left"></i> Back to Customers</a>
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
                                <button class="sp-btn sp-btn-soft" onclick="editCustomer()"><i class="fas fa-edit"></i> Edit</button>
                                @if($customerDetails->status == 'active')
                                    <button class="sp-btn sp-btn-danger" onclick="toggleCustomerStatus({{ $customerDetails->customer_id }}, '{{ $customerDetails->status }}')"><i class="fas fa-ban"></i> Block</button>
                                @else
                                    <button class="sp-btn sp-btn-good" onclick="toggleCustomerStatus({{ $customerDetails->customer_id }}, '{{ $customerDetails->status }}')"><i class="fas fa-check"></i> Unblock</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Customer Information -->
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

                        <!-- Recent Credits -->
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
                                                <h6 class="mb-1">Credit #{{ $credit->id }}</h6>
                                                <p class="text-muted mb-1">
                                                    <small>{{ \Carbon\Carbon::parse($credit->created_at)->format('M d, Y h:i A') }}</small>
                                                </p>
                                                <p class="mb-0">
                                                    <small>Cashier: {{ $credit->cashier->name ?? 'Unknown' }}</small>
                                                </p>
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

                    <!-- Quick Credit Information -->
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
                                        <small class="text-muted">
                                            Last Credit: {{ \Carbon\Carbon::parse($customerDetails->last_credit_date)->format('M d, Y') }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="sp-card">
                            <div class="sp-card-body">
                                <div class="d-flex align-items-center justify-content-between" style="gap:12px;flex-wrap:wrap;margin-bottom:12px;">
                                    <div>
                                        <div class="sp-section-title"><i class="fas fa-bolt"></i> Quick Actions</div>
                                        <div class="sp-section-sub">Use quick actions for common tasks. For detailed history, open Full Credit History.</div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="sp-btn sp-btn-primary" onclick="addCredit()"><i class="fas fa-plus"></i> Add Credit</button>
                                    @if($customerDetails->outstanding_balance > 0)
                                        <button class="sp-btn sp-btn-soft" onclick="makePayment()"><i class="fas fa-money-bill"></i> Make Payment</button>
                                    @else
                                        <button class="sp-btn sp-btn-soft" disabled title="No outstanding balance"><i class="fas fa-money-bill"></i> Make Payment</button>
                                        <div class="text-center text-muted small mt-1">No Outstanding Balance</div>
                                    @endif
                                    <button class="sp-btn sp-btn-soft" onclick="viewFullHistory()"><i class="fas fa-list"></i> Full Credit History</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function editCustomer() {
    Swal.fire({
        title: 'Edit Customer Information',
        customClass: {
            popup: 'sp-swal',
            confirmButton: 'sp-swal-confirm',
            cancelButton: 'sp-swal-cancel'
        },
        buttonsStyling: false,
        html: `
            <div class="text-start">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="sp-label">Full Name</label>
                            <input type="text" class="sp-input" id="edit_full_name" value="{{ $customerDetails->full_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Phone</label>
                            <input type="text" class="sp-input" id="edit_phone" value="{{ $customerDetails->phone ?? '' }}" placeholder="Enter phone number">
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Email</label>
                            <input type="email" class="sp-input" id="edit_email" value="{{ $customerDetails->email ?? '' }}" placeholder="Enter email address">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="sp-label">Address</label>
                            <textarea class="sp-textarea" id="edit_address" rows="3" placeholder="Enter address">{{ $customerDetails->address ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="sp-label">Max Credit Limit</label>
                            <input type="number" class="sp-input" id="edit_max_credit_limit" value="{{ $customerDetails->max_credit_limit ?? 0 }}" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Save Changes',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const fullName = document.getElementById('edit_full_name').value.trim();
            const phone = document.getElementById('edit_phone').value.trim();
            const email = document.getElementById('edit_email').value.trim();
            const address = document.getElementById('edit_address').value.trim();
            const maxCreditLimit = parseFloat(document.getElementById('edit_max_credit_limit').value) || 0;
            
            if (!fullName) {
                Swal.showValidationMessage('Full name is required');
                return false;
            }
            
            return {
                full_name: fullName,
                phone: phone || null,
                email: email || null,
                address: address || null,
                max_credit_limit: maxCreditLimit
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const updateUrl = `{{ route('admin.customers.update', $customerDetails->customer_id) }}`;
            console.log('Update URL:', updateUrl);
            
            fetch(updateUrl, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                // Check if response is ok and is JSON
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer Updated',
                        text: 'Customer information has been updated successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update customer information.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating customer information.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function toggleCustomerStatus(customerId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'blocked' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'block';
    
    Swal.fire({
        title: `Confirm ${action} customer?`,
        text: `Are you sure you want to ${action} this customer?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/superadmin/admin/customers/${customerId}/toggle-status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated',
                        text: `Customer has been ${action}d successfully.`,
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: data.message || 'Failed to update customer status.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating customer status.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function addCredit() {
    Swal.fire({
        title: 'Add Credit',
        html: `
            <div class="text-start">
                <div class="mb-3">
                    <label class="form-label">Credit Amount</label>
                    <input type="number" class="form-control" id="credit_amount" step="0.01" min="0.01" placeholder="Enter credit amount" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Credit Type</label>
                    <select class="form-control" id="credit_type" required>
                        <option value="">Select credit type</option>
                        <option value="cash">Cash</option>
                        <option value="grocery">Grocery</option>
                        <option value="electronics">Electronics</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="credit_date" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="credit_notes" rows="3" placeholder="Add any notes about this credit"></textarea>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Add Credit',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        preConfirm: () => {
            const amount = parseFloat(document.getElementById('credit_amount').value);
            const creditType = document.getElementById('credit_type').value;
            const creditDate = document.getElementById('credit_date').value;
            const notes = document.getElementById('credit_notes').value.trim();
            
            if (!amount || amount <= 0) {
                Swal.showValidationMessage('Please enter a valid amount');
                return false;
            }
            
            if (!creditType) {
                Swal.showValidationMessage('Please select a credit type');
                return false;
            }
            
            if (!creditDate) {
                Swal.showValidationMessage('Please select a due date');
                return false;
            }
            
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            if (creditDate < today) {
                Swal.showValidationMessage('Due date cannot be in the past');
                return false;
            }
            
            return {
                customer_id: '{{ $customerDetails->customer_id }}',
                credit_amount: amount,
                credit_type: creditType,
                date: creditDate,
                notes: notes || null,
                sale_id: null // Direct credits don't have sale_id
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/superadmin/admin/credits', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                // Log the response status and text for debugging
                console.log('Add Credit - Response status:', response.status);
                console.log('Add Credit - Request data:', result.value);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Add Credit - Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}`);
                    });
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Add Credit - Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Credit Added',
                        text: 'Credit has been added successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    let errorMessage = data.message || 'Failed to add credit.';
                    
                    // Show validation errors if they exist
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage = errorMessages.join('\n');
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Add Failed',
                        text: errorMessage,
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding credit.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function makePayment() {
    const outstandingBalance = {{ $customerDetails->outstanding_balance }};
    
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
                    <label class="sp-label">Payment Amount</label>
                    <input type="number" class="sp-input" id="payment_amount" step="0.01" min="0.01" max="${outstandingBalance}" value="${outstandingBalance.toFixed(2)}" placeholder="Enter payment amount" required>
                    <div class="sp-hint">Outstanding Balance: ₱${outstandingBalance.toFixed(2)}</div>
                </div>
                <div class="mb-3">
                    <label class="sp-label">Payment Method</label>
                    <select class="sp-select" id="payment_method" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="other">Other</option>
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
        preConfirm: () => {
            const amount = parseFloat(document.getElementById('payment_amount').value);
            const paymentMethod = document.getElementById('payment_method').value;
            const notes = document.getElementById('payment_notes').value.trim();
            
            if (!amount || amount <= 0) {
                Swal.showValidationMessage('Please enter a valid amount');
                return false;
            }
            
            if (amount > outstandingBalance) {
                Swal.showValidationMessage('Payment amount cannot exceed outstanding balance');
                return false;
            }
            
            if (!paymentMethod) {
                Swal.showValidationMessage('Please select a payment method');
                return false;
            }
            
            return {
                customer_id: {{ $customerDetails->customer_id }},
                payment_amount: amount,
                payment_method: paymentMethod,
                notes: notes || null
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route('admin.customers.make-payment') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(result.value)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response from server');
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Recorded',
                        text: 'Payment has been recorded successfully.',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Failed',
                        text: data.message || 'Failed to record payment.',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while recording payment.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
}

function viewFullHistory() {
    window.location.href = '{{ route('admin.credits.full-history', $customerDetails->customer_id) }}';
}
</script>

</html>
