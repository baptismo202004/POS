<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Full Credit History - {{ $customer->full_name }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
        .header-section {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px 12px 0 0;
        }
        .lifetime-summary {
            background: #e9ecef;
            border-left: 4px solid #6c757d;
            border-radius: 8px;
            padding: 1rem;
        }
        .credit-card {
            border-left: 4px solid var(--theme-color);
            margin-bottom: 1.5rem;
        }
        .payment-item {
            border-left: 3px solid #28a745;
            margin-bottom: 0.5rem;
            padding-left: 1rem;
        }
        .date-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .date-header {
            border-bottom: 2px solid var(--theme-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
            color: var(--theme-color);
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .status-active { border-left-color: #ffc107; }
        .status-partial { border-left-color: #17a2b8; }
        .status-paid { border-left-color: #28a745; }
        
        /* GCash-style credit items */
        .credit-item {
            border-bottom: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }
        .credit-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }
        .credit-item button {
            border: none !important;
            box-shadow: none !important;
        }
        .credit-item button:hover {
            background-color: #f8f9fa !important;
        }
        .credit-item .collapse {
            border-left: 3px solid #2563eb;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ route('superadmin.admin.credits.customer', $customer->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Active Credits
                    </a>
                </div>

                <!-- SECTION 1: Header (Context Lock) -->
                <div class="card card-rounded shadow-sm mb-4">
                    <div class="header-section">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <h2 class="mb-2">Customer: {{ $customer->full_name }}</h2>
                                <h4 class="mb-3">Credit History (All Time)</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Customer ID:</strong> #{{ $customer->id }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Account Status:</strong> 
                                            <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($customer->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Total Credits in System:</strong> {{ $allCredits->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Lifetime Summary Bar (READ-ONLY) -->
                <div class="card card-rounded shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fas fa-chart-line me-2"></i>Lifetime Summary (All Time)
                        </h5>
                        <div class="lifetime-summary">
                            <div class="row">
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Total Credits (All Time):</strong> {{ $lifetimeSummary->total_credits_all_time }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Lifetime Credit Amount:</strong> ₱{{ number_format($lifetimeSummary->lifetime_credit_amount, 2) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Lifetime Paid Amount:</strong> ₱{{ number_format($lifetimeSummary->lifetime_paid_amount, 2) }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="mb-1"><strong>Lifetime Outstanding Balance:</strong> ₱{{ number_format($lifetimeSummary->lifetime_outstanding_balance, 2) }}</p>
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
                <div class="card card-rounded shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="fas fa-filter me-2"></i>Filters
                        </h5>
                        <form method="GET" action="{{ route('superadmin.admin.credits.full-history', $customer->id) }}">
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('superadmin.admin.credits.full-history', $customer->id) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Clear Filters
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- SECTION 4: Credit History List -->
                <div class="card card-rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Credit History
                        </h5>
                        <div>
                            <button class="btn btn-outline-primary btn-sm" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="exportToCSV()">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($groupedCredits as $date => $dateCredits)
                            <div class="mb-3">
                                <div class="text-muted small mb-2">
                                    📅 {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
                                </div>
                                
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
                                            <div class="p-3 border rounded bg-light">
                                                <div class="small text-muted mb-2">
                                                    Created by: {{ $credit->cashier->name ?? 'Unknown' }}
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
                                                                <div class="d-flex justify-content-between align-items-start mb-2 p-2 bg-white rounded border-start border-3 border-success">
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
    
    // Prepare data for export
    const credits = [];
    document.querySelectorAll('.credit-item').forEach(item => {
        const creditId = item.querySelector('button').getAttribute('data-bs-target').replace('#credit-', '');
        const expanded = item.querySelector('.collapse');
        
        if (expanded) {
            const amount = expanded.querySelector('strong:nth-child(1)').textContent.trim();
            const status = expanded.querySelector('.badge').textContent.trim();
            const creditNum = expanded.querySelector('.text-muted small').textContent.trim();
            const balance = expanded.querySelector('strong:nth-child(3)').textContent.trim();
            const createdBy = expanded.querySelector('.text-muted').textContent.replace('Created by: ', '').trim();
            
            credits.push({
                id: creditNum,
                amount: amount,
                status: status,
                balance: balance,
                createdBy: createdBy
            });
        }
    });
    
    // Create PDF
    const doc = new jsPDF();
    
    // Add title
    doc.setFontSize(16);
    doc.text('Credit History Report', 14, 20);
    doc.setFontSize(10);
    doc.text(`Customer: {{ $customer->full_name }}`, 14, 30);
    doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 40);
    
    // Add table headers
    let yPosition = 60;
    doc.text('Credit ID | Amount | Status | Balance | Created By', 14, yPosition);
    doc.line(14, yPosition + 2, 196, yPosition + 2);
    yPosition += 10;
    
    // Add credit data
    credits.forEach(credit => {
        const line = `${credit.id} | ${credit.amount} | ${credit.status} | ${credit.balance} | ${credit.createdBy}`;
        doc.text(line, 14, yPosition);
        yPosition += 8;
    });
    
    // Save PDF
    doc.save(`credit-history-${{{
 $customer->id }}-${Date.now()}.pdf`);
}

function exportToCSV() {
    // Prepare data for export
    const credits = [];
    document.querySelectorAll('.credit-item').forEach(item => {
        const creditId = item.querySelector('button').getAttribute('data-bs-target').replace('#credit-', '');
        const expanded = item.querySelector('.collapse');
        
        if (expanded) {
            const amount = expanded.querySelector('strong:nth-child(1)').textContent.trim();
            const status = expanded.querySelector('.badge').textContent.trim();
            const creditNum = expanded.querySelector('.text-muted small').textContent.trim();
            const balance = expanded.querySelector('strong:nth-child(3)').textContent.trim();
            const createdBy = expanded.querySelector('.text-muted').textContent.replace('Created by: ', '').trim();
            
            credits.push({
                id: creditNum,
                amount: amount,
                status: status,
                balance: balance,
                createdBy: createdBy
            });
        }
    });
    
    // Create CSV content
    let csvContent = 'Credit ID,Amount,Status,Balance,Created By\n';
    
    credits.forEach(credit => {
        csvContent += `"${credit.id}","${credit.amount}","${credit.status}","${credit.createdBy}"\n`;
    });
    
    // Create download link
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `credit-history-${{{
 $customer->id }}-${Date.now()}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

</html>
