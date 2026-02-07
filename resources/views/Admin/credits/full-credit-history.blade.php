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
    
    // Create PDF
    const doc = new jsPDF();
    
    // Helper function to add new page if needed
    function checkPageBreak(yPosition, requiredSpace = 20) {
        if (yPosition > 270 - requiredSpace) {
            doc.addPage();
            return 20;
        }
        return yPosition;
    }
    
    // Helper function to draw bordered cell
    function drawBorderedCell(x, y, width, height, text, fillColor = null, textColor = [0, 0, 0]) {
        if (fillColor) {
            doc.setFillColor(fillColor[0], fillColor[1], fillColor[2]);
            doc.rect(x, y, width, height, 'FD');
        } else {
            doc.rect(x, y, width, height);
        }
        doc.setTextColor(textColor[0], textColor[1], textColor[2]);
        doc.text(text, x + 2, y + height - 2);
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
    
    // Company Header with Logo - Synchronous approach
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
    
    // Contact Information (right aligned)
    doc.setFontSize(9);
    doc.text('📞 0997-384-9783', 140, 20);
    doc.text('✒ bghitsolutiona@gmail.com', 140, 26);
    doc.text('http://web.facebook.com/bgh.iis.1', 140, 32);
    
    yPosition = 45;
    
    // Customer Information Box
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.text('Customer Information:', 14, yPosition);
    yPosition += 8;
    
    const customerInfoY = yPosition;
    const customerInfoHeight = 25;
    
    // Customer info labels and values
    drawBorderedCell(14, customerInfoY, 60, customerInfoHeight, 'Customer Name:', null, [60, 60, 60]);
    drawBorderedCell(74, customerInfoY, 120, customerInfoHeight, '{{ $customer->full_name }}', null, [0, 0, 0]);
    
    drawBorderedCell(14, customerInfoY + 8, 60, customerInfoHeight, 'Customer ID:', null, [60, 60, 60]);
    drawBorderedCell(74, customerInfoY + 8, 120, customerInfoHeight, '#{{ $customer->id }}', null, [0, 0, 0]);
    
    drawBorderedCell(14, customerInfoY + 16, 60, customerInfoHeight, 'Generated On:', null, [60, 60, 60]);
    drawBorderedCell(74, customerInfoY + 16, 120, customerInfoHeight, new Date().toLocaleString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    }).replace(',', ' -'), null, [0, 0, 0]);
    
    yPosition = customerInfoY + 45;
    
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
    
    // Lifetime Credit Summary Table
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.text('Lifetime Credit Summary', 14, yPosition);
    yPosition += 8;
    
    const summaryColWidths = [50, 50, 50, 50];
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
    
    // Credit Transaction Details Table
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.text('Credit Transaction Details', 14, yPosition);
    yPosition += 8;
    
    const transactionColWidths = [25, 35, 35, 40, 35, 35, 30];
    const transactionHeaders = ['Ref #', 'Type', 'Date', 'Credit Amount', 'Paid', 'Balance', 'Status'];
    
    // Draw transaction table headers
    yPosition = drawTableRow(14, yPosition, transactionColWidths, transactionHeaders, true);
    
    // Draw transaction data rows
    doc.setFont('helvetica', 'normal');
    credits.forEach((credit, index) => {
        yPosition = checkPageBreak(yPosition, 15);
        
        // Get credit type from the data or default to 'Unknown'
        const creditType = credit.creditType || 'Unknown';
        const creditDate = credit.createdDate || 'Unknown';
        const paidAmount = (parseFloat(credit.amount) - parseFloat(credit.balance)).toLocaleString();
        
        const transactionData = [
            `CR-${credit.id.padStart(4, '0')}`,
            creditType,
            creditDate,
            `PHP ${parseFloat(credit.amount).toLocaleString()}`,
            `PHP ${paidAmount}`,
            `PHP ${parseFloat(credit.balance).toLocaleString()}`,
            credit.status.toUpperCase()
        ];
        
        // Alternate row colors for better readability
        const bgColor = index % 2 === 0 ? [250, 250, 250] : null;
        yPosition = drawTableRow(14, yPosition, transactionColWidths, transactionData, false, bgColor);
    });
    
    // Add footer
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(128, 128, 128);
        doc.text(`Page ${i} of ${pageCount}`, 196 - 20, 287, { align: 'right' });
        doc.text('Generated by POS System', 14, 287);
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
    // Prepare data for export
    const credits = [];
    document.querySelectorAll('.credit-item').forEach(item => {
        const creditId = item.querySelector('button').getAttribute('data-bs-target').replace('#credit-', '');
        const expanded = item.querySelector('.collapse');
        
        if (expanded) {
            const amount = expanded.querySelector('strong:nth-child(1)')?.textContent?.trim() || '0';
            const status = expanded.querySelector('.badge')?.textContent?.trim() || 'Unknown';
            const creditNum = expanded.querySelector('.text-muted small')?.textContent?.trim() || 'Unknown';
            const balance = expanded.querySelector('strong:nth-child(3)')?.textContent?.trim() || '0';
            const createdByElement = expanded.querySelector('.small.text-muted');
            const createdBy = createdByElement ? createdByElement.textContent.replace('Created by: ', '').trim() : 'Unknown';
            
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
    link.setAttribute('download', 'credit-history-{{ $customer->id }}-' + Date.now() + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

</html>
