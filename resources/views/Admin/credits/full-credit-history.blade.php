<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Full Credit History - {{ $customer->full_name }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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

        .sp-card-head .sp-badge{background:rgba(255,255,255,0.14);border:1px solid rgba(255,255,255,0.25);color:#fff;}
        .sp-card-head .sp-badge-good{background:rgba(16,185,129,0.18);border:1px solid rgba(16,185,129,0.35);color:#a7f3d0;}
        .sp-card-head .sp-badge-warn{background:rgba(245,158,11,0.18);border:1px solid rgba(245,158,11,0.35);color:#fde68a;}

        .sp-form .form-label{font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;font-family:'Nunito',sans-serif;display:block;}
        .sp-form .form-control,.sp-form select.form-control{border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;}
        .sp-form .form-control:focus,.sp-form select.form-control:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}

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
        .credit-item .badge{font-family:'Nunito',sans-serif;font-weight:800;}

        .sp-soft{background:rgba(13,71,161,0.03);border:1px solid var(--border);border-radius:14px;}
        .sp-pay{border-left:3px solid #10b981;}

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
                        <div class="sp-ph-icon"><i class="fas fa-history"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Finance / Credits</div>
                            <div class="sp-ph-title">Full Credit History</div>
                            <div class="sp-ph-sub">All-time credits and payment records for {{ $customer->full_name }}</div>
                        </div>
                    </div>
                    <div class="sp-ph-actions">
                        <a href="{{ route('admin.credits.customer', $customer->id) }}" class="sp-btn sp-btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>

                <!-- SECTION 1: Header (Context Lock) -->
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

                <!-- SECTION 2: Lifetime Summary Bar (READ-ONLY) -->
                <div class="sp-card mb-4">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-chart-line"></i> Lifetime Summary (All Time)</div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-soft" style="padding:14px 16px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Total Credits (All Time):</strong> <span data-summary="total-credits">{{ $lifetimeSummary->total_credits_all_time }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Lifetime Credit Amount:</strong> <span data-summary="lifetime-credit-amount">₱{{ number_format($lifetimeSummary->lifetime_credit_amount, 2) }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Lifetime Paid Amount:</strong> <span data-summary="lifetime-paid-amount">₱{{ number_format($lifetimeSummary->lifetime_paid_amount, 2) }}</span></p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Lifetime Outstanding Balance:</strong> <span data-summary="lifetime-outstanding">₱{{ number_format($lifetimeSummary->lifetime_outstanding_balance, 2) }}</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                These totals include ALL credits - active, partially paid, and fully paid.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Filters -->
                <div class="sp-card mb-4">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-filter"></i> Filters</div>
                    </div>
                    <div class="sp-card-body sp-form">
                        <form method="GET" action="{{ route('admin.credits.full-history', $customer->id) }}">
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
                                            <option value="{{ $cashier }}" {{ ($filters['created_by'] ?? '') == $cashier ? 'selected' : '' }}>
                                                {{ $cashier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="sp-btn sp-btn-primary"><i class="fas fa-search"></i> Apply Filters</button>
                                    <a href="{{ route('admin.credits.full-history', $customer->id) }}" class="sp-btn sp-btn-outline"><i class="fas fa-times"></i> Clear Filters</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- SECTION 4: Credit History List -->
                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-history"></i> Credit History</div>
                        <div style="position:relative;z-index:1;display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
                            <button class="sp-btn sp-btn-outline" style="padding:7px 12px;font-size:12px;" onclick="exportToPDF()"><i class="fas fa-file-pdf"></i> Export PDF</button>
                            <button class="sp-btn sp-btn-teal" style="padding:7px 12px;font-size:12px;" onclick="exportToCSV()"><i class="fas fa-file-csv"></i> Export CSV</button>
                        </div>
                    </div>
                    <div class="sp-card-body">
                        @forelse($groupedCredits as $date => $dateCredits)
                            <div class="mb-3">
                                <div class="sp-date mb-2"><i class="fas fa-calendar-day" style="opacity:.6;"></i> {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</div>
                                
                                @foreach($dateCredits as $credit)
                                    <div class="credit-item mb-2">
                                        <button class="w-100 text-start p-3 border rounded bg-white"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#credit-{{ $credit->id }}">
                                            
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>₱{{ number_format($credit->credit_amount, 2) }}</strong>
                                                    <div class="text-muted small">Credit #{{ $credit->id }}</div>
                                                    <div class="small">Balance: ₱{{ number_format($credit->remaining_balance, 2) }}</div>
                                                </div>

                                                <span class="badge 
                                                    {{ $credit->status == 'paid' ? 'bg-success' : ($credit->status == 'partial' ? 'bg-info' : 'bg-warning') }}">
                                                    {{ ucfirst($credit->status) }}
                                                </span>
                                            </div>
                                        </button>

                                        <div id="credit-{{ $credit->id }}" class="collapse mt-2">
                                            <div class="p-3 sp-soft">
                                                <div class="small text-muted mb-2">
                                                    Created by: {{ $credit->cashier->name ?? 'Admin' }}
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-6">
                                                        <strong>Credit Amount:</strong>
                                                        ₱{{ number_format($credit->credit_amount, 2) }}
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Total Paid:</strong>
                                                        ₱{{ number_format($credit->paid_amount, 2) }}
                                                    </div>
                                                </div>

                                                <div class="mb-2">
                                                    <strong>Remaining:</strong>
                                                    ₱{{ number_format($credit->remaining_balance, 2) }}
                                                </div>

                                                @if($credit->payments->count() > 0)
                                                    <div class="small">
                                                        <strong>Payments:</strong>
                                                        <div class="mt-2">
                                                            @foreach($credit->payments as $payment)
                                                                <div class="d-flex justify-content-between align-items-start mb-2 p-2 bg-white rounded sp-pay">
                                                                    <div class="grow">
                                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                                            <strong>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y h:i A') }}</strong>
                                                                            <span class="badge bg-secondary">{{ $payment->payment_method }}</span>
                                                                        </div>
                                                                        <div class="text-muted small">
                                                                            by {{ $payment->cashier->name ?? 'Unknown' }}
                                                                            @if($payment->notes)
                                                                                <br><span class="text-primary">"{{ $payment->notes }}"</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="ms-3 text-end">
                                                                        <strong class="text-success">₱{{ number_format($payment->payment_amount, 2) }}</strong>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        
                                                        <div class="mt-2 pt-2 border-top">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="text-muted"><strong>Total Paid:</strong></span>
                                                                <strong class="text-success">₱{{ number_format($credit->paid_amount, 2) }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="small text-muted">
                                                        No payments recorded
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <h5 class="text-muted">No credits found</h5>
                                <p class="text-muted">No credits match the current filter criteria.</p>
                            </div>
                        @endforelse
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
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    
    // Get lifetime summary data
    const totalCredits = document.querySelector('[data-summary="total-credits"]')?.textContent?.trim() || '0';
    const lifetimeCreditAmount = document.querySelector('[data-summary="lifetime-credit-amount"]')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0.00';
    const lifetimePaidAmount = document.querySelector('[data-summary="lifetime-paid-amount"]')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0.00';
    const lifetimeOutstanding = document.querySelector('[data-summary="lifetime-outstanding"]')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0.00';
    
    // Get filter information
    const activeFilters = getActiveFilters();
    
    // Prepare detailed credit data with complete information
    const credits = [];
    document.querySelectorAll('.credit-item').forEach(item => {
        const creditTarget = item.querySelector('button').getAttribute('data-bs-target');
        const creditId = creditTarget.replace('#credit-', '');
        const expandedContent = document.querySelector(creditTarget);
        
        // Get basic credit information
        const amountElement = item.querySelector('strong');
        const amount = amountElement ? amountElement.textContent.replace(/[\u20b1\u00b1]/g, '').trim() : '0';
        
        const statusElement = item.querySelector('.badge');
        const status = statusElement ? statusElement.textContent.trim() : 'Unknown';
        
        const creditNumElement = item.querySelector('.text-muted.small');
        const creditNum = creditNumElement ? creditNumElement.textContent.replace('Credit #', '').trim() : 'Unknown';
        
        const balanceElement = item.querySelector('.small');
        let balance = '0';
        if (balanceElement) {
            const balanceText = balanceElement.textContent;
            const balanceMatch = balanceText.match(/Balance:\s*[\u20b1\u00b1]?\s*([\d.,]+)/);
            if (balanceMatch) {
                balance = balanceMatch[1].replace(/,/g, '');
            } else {
                const altMatch = balanceText.match(/Balance:\s*([\d.,]+)/);
                if (altMatch) {
                    balance = altMatch[1].replace(/,/g, '');
                } else {
                    const allNumbers = balanceText.match(/([\d.,]+)/g);
                    if (allNumbers && allNumbers.length > 0) {
                        balance = allNumbers[allNumbers.length - 1].replace(/,/g, '');
                    }
                }
            }
        }
        
        // Get complete information from expanded content
        let createdBy = 'Unknown';
        let creditType = 'Unknown';
        let dueDate = 'Unknown';
        let notes = '';
        let createdDate = 'Unknown';
        let payments = [];
        
        if (expandedContent) {
            // Get creator info
            const creatorElement = expandedContent.querySelector('.small.text-muted');
            if (creatorElement) {
                createdBy = creatorElement.textContent.replace('Created by: ', '').trim();
            }
            
            // Get credit creation date from the date group
            const dateGroup = item.closest('.mb-3');
            if (dateGroup) {
                const dateElement = dateGroup.querySelector('.text-muted.small');
                if (dateElement) {
                    createdDate = dateElement.textContent.replace('📅 ', '').trim();
                }
            }
            
            // Extract complete payment details
            const paymentElements = expandedContent.querySelectorAll('.border-start.border-success');
            let runningBalance = parseFloat(balance.replace(/[^0-9.]/g, ''));
            
            paymentElements.forEach((paymentEl, index) => {
                const paymentDate = paymentEl.querySelector('strong')?.textContent?.trim() || '';
                const paymentMethod = paymentEl.querySelector('.badge')?.textContent?.trim() || '';
                const paymentAmount = paymentEl.querySelector('.text-success')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0';
                const paymentNotes = paymentEl.querySelector('.text-primary')?.textContent?.replace(/"/g, '').trim() || '';
                const paymentBy = paymentEl.querySelector('.text-muted')?.textContent?.split('by ')[1]?.split('\n')[0]?.trim() || '';
                
                const amount = parseFloat(paymentAmount.replace(/[^0-9.]/g, ''));
                const balanceAfter = runningBalance - amount;
                
                payments.push({
                    date: paymentDate,
                    method: paymentMethod,
                    amount: amount,
                    notes: paymentNotes,
                    by: paymentBy,
                    balanceAfter: balanceAfter,
                    paymentNumber: index + 1
                });
                
                runningBalance = balanceAfter;
            });
        }
        
        credits.push({
            id: creditNum,
            amount: amount.replace(/[^0-9.]/g, ''),
            status: status,
            balance: balance.replace(/[^0-9.]/g, ''),
            createdBy: createdBy,
            creditType: creditType,
            dueDate: dueDate,
            notes: notes,
            createdDate: createdDate,
            payments: payments
        });
    });
    
    // Create PDF with landscape orientation
    const doc = new jsPDF({
        orientation: 'landscape',
        unit: 'mm',
        format: 'a4'
    });
    
    // Helper function to add new page if needed (adjusted for landscape with proper pagination)
    function checkPageBreak(yPosition, requiredSpace = 20) {
        if (yPosition > 190 - requiredSpace) { // Landscape A4 height is ~190mm
            doc.addPage();
            // Add simplified header on new page and get the new position
            const newPosition = addPageHeader();
            return newPosition; // Return position after header (logo + table header)
        }
        return yPosition;
    }
    
    // Function to add simplified header on each page (pagination rules)
    function addPageHeader() {
        // Document header (logo & business information)
        let yPosition = 15;
        
        // Try to add logo synchronously
        try {
            const logoPath = '{{ asset("images/BGH LOGO.png") }}';
            doc.addImage(logoPath, 'PNG', 14, 15, 30, 20);
        } catch (error) {
            console.log('Logo loading failed, using text-only header');
        }
        
        // Company Header
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('BGH IT SOLUTIONS', 50, 25);
        
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(12);
        doc.text('Credit History Report', 50, 32);
        
        // Contact Information (Right side) - adjusted for landscape
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.text('Phone: 09457410264', 270, 22, { align: 'right' });
        doc.text('Email: bgh.it.solutions@gmail.com', 270, 27, { align: 'right' });
        doc.text('Facebook: http://web.facebook.com/bgh.iis.1', 270, 32, { align: 'right' });
        
        yPosition = 45;
        
        // Draw a line under the header (adjusted for landscape)
        doc.setDrawColor(0, 0, 0);
        doc.setLineWidth(0.5);
        doc.line(14, yPosition, 276, yPosition); // Landscape width is ~276mm
        yPosition += 8;
        
        // Table header - MUST be printed before any data (consistent font)
        doc.setFont('helvetica', 'normal'); // Changed from bold to normal
        doc.setFontSize(9);
        doc.text('Credit Transaction Details', 14, yPosition);
        yPosition += 8;
        
        // Draw transaction table headers
        const transactionColWidths = [28, 28, 25, 28, 28, 26, 24, 28, 24, 27]; // Total: 236mm
        const transactionHeaders = ['Category', 'Ref No.', 'Type', 'Credit Amt', 'Rem. Balance', 'Pay Amount', 'Method', 'Transaction', 'Cashier', 'Date'];
        drawTableRow(14, yPosition, transactionColWidths, transactionHeaders, true);
        
        // Return the position after table header (no data should appear above this)
        return yPosition + 8; // Position after table header
    }
    
    // Helper function to draw bordered cell with improved text positioning
    function drawBorderedCell(x, y, width, height, text, fillColor = null, textColor = [0, 0, 0]) {
        if (fillColor) {
            doc.setFillColor(fillColor[0], fillColor[1], fillColor[2]);
            doc.rect(x, y, width, height, 'FD');
        } else {
            doc.rect(x, y, width, height);
        }
        doc.setTextColor(textColor[0], textColor[1], textColor[2]);
        
        // Improved text positioning and wrapping
        let textX = x + 2;
        let textY = y + height - 2; // Better vertical positioning
        
        // Handle special characters - simplified for payment rows
        if (text.includes('PAY')) {
            textX = x + 4; // Simple indentation for payment rows
        }
        
        // Split text into lines if too long
        const maxWidth = width - 4;
        const lines = doc.splitTextToSize(text, maxWidth);
        
        // Adjust starting position for multiple lines
        if (lines.length > 1) {
            textY = y + 4; // Start higher for wrapped text
        }
        
        // Draw each line with proper spacing
        lines.forEach((line, index) => {
            if (index > 0) {
                textY += 3; // Tighter line spacing for better fit
            }
            doc.text(line, textX, textY);
        });
    }
    
    // Helper function to draw table row
    function drawTableRow(x, y, colWidths, data, isHeader = false, bgColor = null) {
        let currentX = x;
        const rowHeight = 8;
        const textColor = isHeader ? [60, 60, 60] : [0, 0, 0];
        
        data.forEach((text, index) => {
            if (isHeader) {
                drawBorderedCell(currentX, y, colWidths[index], rowHeight, text, [240, 240, 240], textColor);
            } else {
                drawBorderedCell(currentX, y, colWidths[index], rowHeight, text, bgColor, textColor);
            }
            currentX += colWidths[index];
        });
        
        return y + rowHeight;
    }
    
    // Company Header with Logo - Synchronous approach (adjusted for landscape)
    let yPosition = 15;
    
    // Try to add logo synchronously
    try {
        // Create a simple approach - add logo directly without async loading
        const logoPath = '{{ asset("images/BGH LOGO.png") }}';
        doc.addImage(logoPath, 'PNG', 14, 15, 30, 20);
    } catch (error) {
        console.log('Logo loading failed, using text-only header');
    }
    
    // Company Header
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(16);
    doc.text('BGH IT SOLUTIONS', 50, 25);
    
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(12);
    doc.text('Credit History Report', 50, 32);
    
    // Contact Information (Right side) - adjusted for landscape
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.text('Phone: 09457410264', 270, 22, { align: 'right' });
    doc.text('Email: bgh.it.solutions@gmail.com', 270, 27, { align: 'right' });
    doc.text('Facebook: http://web.facebook.com/bgh.iis.1', 270, 32, { align: 'right' });
    
    yPosition = 45;
    
    // Draw a line under the header (adjusted for landscape)
    doc.setDrawColor(0, 0, 0);
    doc.setLineWidth(0.5);
    doc.line(14, yPosition, 276, yPosition); // Landscape width is ~276mm
    yPosition += 8;
    
    // Customer Information Box (adjusted for landscape)
    const customerBoxX = 14;
    const customerBoxY = yPosition;
    const customerBoxWidth = 262; // Adjusted for landscape
    const customerBoxHeight = 35;
    
    // Draw customer info box border
    doc.rect(customerBoxX, customerBoxY, customerBoxWidth, customerBoxHeight);
    
    // Customer information labels and values
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(9);
    
    // Left column labels
    doc.text('Customer Name:', customerBoxX + 4, customerBoxY + 8);
    doc.text('Customer ID:', customerBoxX + 4, customerBoxY + 15);
    doc.text('Phone:', customerBoxX + 4, customerBoxY + 22);
    doc.text('Email:', customerBoxX + 94, customerBoxY + 8);
    doc.text('Address:', customerBoxX + 94, customerBoxY + 15);
    doc.text('Status:', customerBoxX + 94, customerBoxY + 22);
    
    // Right column values
    doc.setFont('helvetica', 'normal');
    doc.text('{{ $customer->full_name }}', customerBoxX + 35, customerBoxY + 8);
    doc.text('#{{ $customer->id }}', customerBoxX + 35, customerBoxY + 15);
    doc.text('{{ $customer->phone ?? "N/A" }}', customerBoxX + 35, customerBoxY + 22);
    doc.text('{{ $customer->email ?? "N/A" }}', customerBoxX + 120, customerBoxY + 8);
    doc.text('{{ $customer->address ?? "N/A" }}', customerBoxX + 120, customerBoxY + 15);
    
    // Status with color
    const customerStatus = '{{ $customer->status ?? "active" }}';
    if (customerStatus.toLowerCase() === 'active') {
        doc.setTextColor(0, 128, 0);
        doc.text('Active', customerBoxX + 120, customerBoxY + 22);
    } else {
        doc.setTextColor(255, 128, 0);
        doc.text('Inactive', customerBoxX + 120, customerBoxY + 22);
    }
    doc.setTextColor(0, 0, 0);
    
    yPosition = customerBoxY + customerBoxHeight + 8;
    
    // Active Filters (if any)
    const filters = getActiveFilters();
    if (filters.length > 0) {
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('Active Filters:', 14, yPosition);
        yPosition += 6;
        
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        filters.forEach(filter => {
            doc.text(`• ${filter}`, 20, yPosition);
            yPosition += 5;
        });
        yPosition += 5;
    }
    
    // Lifetime Credit Summary Table - Adjusted for landscape
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.text('Lifetime Credit Summary', 14, yPosition);
    yPosition += 8;
    
    const summaryColWidths = [65, 65, 65, 67]; // Adjusted for landscape
    const summaryHeaders = ['Total Credits', 'Lifetime Credit Amount', 'Total Paid', 'Outstanding Balance'];
    const summaryData = [
        totalCredits,
        `PHP ${parseFloat(lifetimeCreditAmount).toLocaleString()}`,
        `PHP ${parseFloat(lifetimePaidAmount).toLocaleString()}`,
        `PHP ${parseFloat(lifetimeOutstanding).toLocaleString()}`
    ];
    
    // Draw summary table
    yPosition = drawTableRow(14, yPosition, summaryColWidths, summaryHeaders, true);
    yPosition = drawTableRow(14, yPosition, summaryColWidths, summaryData, false);
    
    yPosition += 15;
    
    // Credit Transaction Details Table - Adjusted for bond paper
    doc.setFont('helvetica', 'normal'); // Changed from bold to normal
    doc.setFontSize(9);
    doc.text('Credit Transaction Details', 14, yPosition);
    yPosition += 8;
    
    // Optimized table column widths for bond paper
    const transactionColWidths = [28, 28, 25, 28, 28, 26, 24, 28, 24, 27]; // Total: 236mm
    const transactionHeaders = ['Category', 'Ref No.', 'Type', 'Credit Amt', 'Rem. Balance', 'Pay Amount', 'Method', 'Transaction', 'Cashier', 'Date'];
    
    // Draw transaction table headers
    yPosition = drawTableRow(14, yPosition, transactionColWidths, transactionHeaders, true);
    
    // Draw transaction data rows with new structure
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8); // Reduced font size for better fit
    
    credits.forEach((credit, index) => {
        yPosition = checkPageBreak(yPosition, 25);
        
        // Get credit details
        const creditType = credit.creditType || 'Cash';
        const creditDate = credit.createdDate || 'Unknown';
        const creditAmount = parseFloat(credit.amount).toLocaleString();
        const remainingBalance = parseFloat(credit.balance).toLocaleString();
        const cashierId = credit.createdBy === 'Admin User' ? 'EMP-001' : credit.createdBy;
        
        // Credit row
        const creditData = [
            'CREDIT',
            `CR-${credit.id.padStart(4, '0')}`,
            creditType,
            `PHP ${creditAmount}`,
            `PHP ${remainingBalance}`,
            '—',
            '—',
            '—',
            cashierId,
            creditDate // Include full date with time
        ];
        
        // Draw credit row
        yPosition = drawTableRow(14, yPosition, transactionColWidths, creditData, false);
        
        // Payment rows (simplified without arrow)
        if (credit.payments && credit.payments.length > 0) {
            credit.payments.forEach(payment => {
                yPosition = checkPageBreak(yPosition, 15);
                
                const paymentAmount = parseFloat(payment.amount).toLocaleString();
                const paymentDate = payment.date || 'Unknown'; // Include full date with time
                const paymentCashier = payment.by === 'Admin User' ? 'EMP-001' : payment.by;
                
                const paymentData = [
                    'Payment', // Simple text instead of arrow
                    '',
                    '',
                    '',
                    '',
                    `PHP ${paymentAmount}`,
                    payment.method || 'Cash',
                    `OR-${Math.floor(Math.random() * 90000) + 10000}`, // Generate OR number
                    paymentCashier,
                    paymentDate // Include full date with time
                ];
                
                // Draw payment row
                yPosition = drawTableRow(14, yPosition, transactionColWidths, paymentData, false);
            });
        }
        
        // Remove separator line - all rows are now connected continuously
        // No separator lines between credits for continuous flow
    });
    
    // Add footer (adjusted for landscape)
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(128, 128, 128);
        doc.text(`Page ${i} of ${pageCount}`, 276 - 20, 190, { align: 'right' }); // Landscape dimensions
        doc.text('Generated by POS System', 14, 190);
        doc.setTextColor(0, 0, 0);
    }
    
    // Save PDF
    const filename = filters.length > 0 
        ? `credit-history-filtered-{{ $customer->id }}-${Date.now()}.pdf`
        : `credit-history-full-{{ $customer->id }}-${Date.now()}.pdf`;
    doc.save(filename);
}

function getActiveFilters() {
    const filters = [];
    
    // Get date filters
    const dateFrom = document.querySelector('input[name="date_from"]')?.value;
    const dateTo = document.querySelector('input[name="date_to"]')?.value;
    if (dateFrom) filters.push(`Date from: ${dateFrom}`);
    if (dateTo) filters.push(`Date to: ${dateTo}`);
    
    // Get status filter
    const status = document.querySelector('select[name="status"]')?.value;
    if (status) filters.push(`Status: ${status}`);
    
    // Get credit ID filter
    const creditId = document.querySelector('input[name="credit_id"]')?.value;
    if (creditId) filters.push(`Credit ID: ${creditId}`);
    
    // Get created by filter
    const createdBy = document.querySelector('select[name="created_by"]')?.value;
    if (createdBy) filters.push(`Created by: ${createdBy}`);
    
    return filters;
}

function exportToCSV() {
    // Get lifetime summary data
    const totalCredits = document.querySelector('[data-summary="total-credits"]')?.textContent?.trim() || '0';
    const lifetimeCreditAmount = document.querySelector('[data-summary="lifetime-credit-amount"]')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0.00';
    const lifetimePaidAmount = document.querySelector('[data-summary="lifetime-paid-amount"]')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0.00';
    const lifetimeOutstanding = document.querySelector('[data-summary="lifetime-outstanding"]')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0.00';
    
    // Prepare detailed credit data with complete information
    const credits = [];
    document.querySelectorAll('.credit-item').forEach(item => {
        const creditTarget = item.querySelector('button').getAttribute('data-bs-target');
        const creditId = creditTarget.replace('#credit-', '');
        const expandedContent = document.querySelector(creditTarget);
        
        // Get basic credit information
        const amountElement = item.querySelector('strong');
        const amount = amountElement ? amountElement.textContent.replace(/[\u20b1\u00b1]/g, '').trim() : '0';
        
        const statusElement = item.querySelector('.badge');
        const status = statusElement ? statusElement.textContent.trim() : 'Unknown';
        
        const creditNumElement = item.querySelector('.text-muted.small');
        const creditNum = creditNumElement ? creditNumElement.textContent.replace('Credit #', '').trim() : 'Unknown';
        
        const balanceElement = item.querySelector('.small');
        let balance = '0';
        if (balanceElement) {
            const balanceText = balanceElement.textContent;
            const balanceMatch = balanceText.match(/Balance:\s*[\u20b1\u00b1]?\s*([\d.,]+)/);
            if (balanceMatch) {
                balance = balanceMatch[1].replace(/,/g, '');
            } else {
                const altMatch = balanceText.match(/Balance:\s*([\d.,]+)/);
                if (altMatch) {
                    balance = altMatch[1].replace(/,/g, '');
                } else {
                    const allNumbers = balanceText.match(/([\d.,]+)/g);
                    if (allNumbers && allNumbers.length > 0) {
                        balance = allNumbers[allNumbers.length - 1].replace(/,/g, '');
                    }
                }
            }
        }
        
        // Get complete information from expanded content
        let createdBy = 'Unknown';
        let creditType = 'Cash';
        let createdDate = 'Unknown';
        let payments = [];
        
        if (expandedContent) {
            // Get creator info
            const creatorElement = expandedContent.querySelector('.small.text-muted');
            if (creatorElement) {
                createdBy = creatorElement.textContent.replace('Created by: ', '').trim();
            }
            
            // Get credit creation date from date group
            const dateGroup = item.closest('.mb-3');
            if (dateGroup) {
                const dateElement = dateGroup.querySelector('.text-muted.small');
                if (dateElement) {
                    createdDate = dateElement.textContent.replace('📅 ', '').trim();
                }
            }
            
            // Extract complete payment details
            const paymentElements = expandedContent.querySelectorAll('.border-start.border-success');
            let runningBalance = parseFloat(balance.replace(/[^0-9.]/g, ''));
            
            paymentElements.forEach((paymentEl, index) => {
                const paymentDate = paymentEl.querySelector('strong')?.textContent?.trim() || '';
                const paymentMethod = paymentEl.querySelector('.badge')?.textContent?.trim() || '';
                const paymentAmount = paymentEl.querySelector('.text-success')?.textContent?.replace(/[\u20b1\u00b1]/g, '').trim() || '0';
                const paymentNotes = paymentEl.querySelector('.text-primary')?.textContent?.replace(/"/g, '').trim() || '';
                const paymentBy = paymentEl.querySelector('.text-muted')?.textContent?.split('by ')[1]?.split('\n')[0]?.trim() || '';
                
                const amount = parseFloat(paymentAmount.replace(/[^0-9.]/g, ''));
                const balanceAfter = runningBalance - amount;
                
                payments.push({
                    date: paymentDate,
                    method: paymentMethod,
                    amount: amount,
                    notes: paymentNotes,
                    by: paymentBy,
                    balanceAfter: balanceAfter,
                    paymentNumber: index + 1
                });
                
                runningBalance = balanceAfter;
            });
        }
        
        credits.push({
            id: creditNum,
            amount: amount.replace(/[^0-9.]/g, ''),
            status: status,
            balance: balance.replace(/[^0-9.]/g, ''),
            createdBy: createdBy,
            creditType: creditType,
            createdDate: createdDate,
            payments: payments
        });
    });
    
    // Create enhanced CSV content with better structure
    let csvContent = '='.repeat(80) + '\n';
    csvContent += 'BGH IT SOLUTIONS - CREDIT HISTORY REPORT\n';
    csvContent += '='.repeat(80) + '\n';
    csvContent += `Customer: {{ $customer->full_name }} (ID: #{{ $customer->id }})\n`;
    csvContent += `Generated: ${new Date().toLocaleString()}\n`;
    csvContent += '='.repeat(80) + '\n\n';
    
    // Add customer information with better formatting
    csvContent += 'CUSTOMER INFORMATION\n';
    csvContent += '-'.repeat(40) + '\n';
    csvContent += `Name,{{ $customer->full_name }}\n`;
    csvContent += `ID,#{{ $customer->id }}\n`;
    csvContent += `Phone,{{ $customer->phone ?? "N/A" }}\n`;
    csvContent += `Email,{{ $customer->email ?? "N/A" }}\n`;
    csvContent += `Address,{{ $customer->address ?? "N/A" }}\n`;
    csvContent += `Status,{{ $customer->status ?? "active" }}\n\n`;
    
    // Add lifetime credit summary with emphasis
    csvContent += 'LIFETIME CREDIT SUMMARY\n';
    csvContent += '-'.repeat(40) + '\n';
    csvContent += `Total Credits,${totalCredits}\n`;
    csvContent += `Lifetime Credit Amount,${lifetimeCreditAmount}\n`;
    csvContent += `Total Paid,${lifetimePaidAmount}\n`;
    csvContent += `Outstanding Balance,${lifetimeOutstanding}\n\n`;
    
    // Add transaction details with clear headers
    csvContent += 'CREDIT TRANSACTION DETAILS\n';
    csvContent += '-'.repeat(100) + '\n';
    csvContent += 'CATEGORY,REF NO.,TYPE,CREDIT AMT,REM. BALANCE,PAY AMOUNT,METHOD,TRANSACTION,CASHIER,DATE\n';
    csvContent += '-'.repeat(100) + '\n';
    
    // Add transaction data with better formatting
    credits.forEach((credit, index) => {
        // Credit row
        const creditType = credit.creditType || 'Cash';
        const creditDate = credit.createdDate || 'Unknown';
        const creditAmount = parseFloat(credit.amount).toLocaleString();
        const remainingBalance = parseFloat(credit.balance).toLocaleString();
        const cashierId = credit.createdBy === 'Admin User' ? 'EMP-001' : credit.createdBy;
        
        csvContent += `CREDIT,CR-${credit.id.padStart(4, '0')},${creditType},PHP ${creditAmount},PHP ${remainingBalance},---,---,---,${cashierId},${creditDate}\n`;
        
        // Payment rows with clear indication
        if (credit.payments && credit.payments.length > 0) {
            credit.payments.forEach(payment => {
                const paymentAmount = parseFloat(payment.amount).toLocaleString();
                const paymentDate = payment.date || 'Unknown';
                const paymentCashier = payment.by === 'Admin User' ? 'EMP-001' : payment.by;
                
                csvContent += `PAYMENT,,,,,,PHP ${paymentAmount},${payment.method || 'Cash'},OR-${Math.floor(Math.random() * 90000) + 10000},${paymentCashier},${paymentDate}\n`;
            });
        }
        
        // Add separator between credits (except last one)
        if (index < credits.length - 1) {
            csvContent += '-'.repeat(100) + '\n';
        }
    });
    
    csvContent += '='.repeat(100) + '\n';
    csvContent += 'END OF REPORT\n';
    csvContent += '='.repeat(100) + '\n';
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    const filters = getActiveFilters();
    const filename = filters.length > 0 
        ? `credit-history-filtered-{{ $customer->id }}-${Date.now()}.csv`
        : `credit-history-full-{{ $customer->id }}-${Date.now()}.csv`;
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

</html>
