<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports - Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
        .table th {
            font-weight: 600;
            color: #475569;
            background-color: #f8fafc;
        }
        .stat-card {
            transition: transform 0.2s ease-in-out;
            border-radius: 12px;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .nav-tabs .nav-link {
            border-radius: 8px 8px 0 0;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
            color: white;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Business Reports</h2>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Quick Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Today's Sales</h6>
                                        <h4 class="mb-0">₱{{ number_format(\App\Models\Sale::whereDate('created_at', today())->sum('total_amount'), 2) }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-danger text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Today's Expenses</h6>
                                        <h4 class="mb-0">₱{{ number_format(\App\Models\Expense::whereDate('created_at', today())->sum('amount'), 2) }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-money-bill-wave fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Net Profit Today</h6>
                                        <h4 class="mb-0">₱{{ number_format(\App\Models\Sale::whereDate('created_at', today())->sum('total_amount') - \App\Models\Expense::whereDate('created_at', today())->sum('amount'), 2) }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-chart-line fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Transactions</h6>
                                        <h4 class="mb-0">{{ \App\Models\Sale::whereDate('created_at', today())->count() }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-receipt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combined Reports Table -->
                <div class="card card-rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Business Transactions</h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>User</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Get recent sales
                                        $recentSales = \App\Models\Sale::with('user')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(10)
                                            ->get();
                                        
                                        // Get recent expenses
                                        $recentExpenses = \App\Models\Expense::with('category')
                                            ->orderBy('created_at', 'desc')
                                            ->limit(10)
                                            ->get();
                                        
                                        // Combine and sort by date
                                        $allTransactions = $recentSales->concat($recentExpenses)
                                            ->sortByDesc('created_at')
                                            ->take(15);
                                    @endphp
                                    
                                    @forelse($allTransactions as $transaction)
                                        @if($transaction instanceof \App\Models\Sale)
                                            <tr>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                <td><span class="badge bg-primary">SALE</span></td>
                                                <td>
                                                    <div>
                                                        <strong>Sale Transaction</strong><br>
                                                        <small class="text-muted">{{ $transaction->saleItems->count() }} items sold</small>
                                                    </div>
                                                </td>
                                                <td class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</td>
                                                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                                <td><span class="badge bg-success">Completed</span></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                <td><span class="badge bg-danger">EXPENSE</span></td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $transaction->description }}</strong><br>
                                                        <small class="text-muted">
                                                            @if($transaction->category)
                                                                Category: {{ $transaction->category->name }}
                                                            @else
                                                                Uncategorized
                                                            @endif
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="fw-bold text-danger">₱{{ number_format($transaction->amount, 2) }}</td>
                                                <td>System</td>
                                                <td><span class="badge bg-warning">Processed</span></td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                    <p>No transactions found.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Summary Row -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted">Total Sales</h6>
                                    <h5 class="text-success">₱{{ number_format($recentSales->sum('total_amount'), 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted">Total Expenses</h6>
                                    <h5 class="text-danger">₱{{ number_format($recentExpenses->sum('amount'), 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted">Net Total</h6>
                                    <h5 class="text-primary">₱{{ number_format($recentSales->sum('total_amount') - $recentExpenses->sum('amount'), 2) }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success me-2" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>Export Report
                            </button>
                            <button type="button" class="btn btn-info" onclick="refreshData()">
                                <i class="fas fa-sync-alt me-2"></i>Refresh Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Reports</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="applyFilter()">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function exportReport() {
        const fromDate = document.getElementById('from_date').value;
        const toDate = document.getElementById('to_date').value;
        
        // Create form and submit for download
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("superadmin.admin.reports.export") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add dates
        const fromInput = document.createElement('input');
        fromInput.type = 'hidden';
        fromInput.name = 'from_date';
        fromInput.value = fromDate;
        form.appendChild(fromInput);
        
        const toInput = document.createElement('input');
        toInput.type = 'hidden';
        toInput.name = 'to_date';
        toInput.value = toDate;
        form.appendChild(toInput);
        
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    function refreshData() {
        location.reload();
    }

    function applyFilter() {
        const fromDate = document.getElementById('from_date').value;
        const toDate = document.getElementById('to_date').value;
        
        if (!fromDate || !toDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Dates',
                text: 'Please select both from and to dates.',
            });
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching filtered data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Make AJAX request
        fetch('{{ route("superadmin.admin.reports.filter") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                from_date: fromDate,
                to_date: toDate
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error,
                });
                return;
            }
            
            // Update the table with filtered data
            updateTable(data.transactions);
            
            // Update summary cards
            updateSummaryCards(data.summaries);
            
            // Update product breakdown if available
            if (data.sales_by_product) {
                updateProductBreakdown(data.sales_by_product);
            }
            
            // Close modal
            $('#filterModal').modal('hide');
            
            Swal.fire({
                icon: 'success',
                title: 'Filter Applied',
                text: `Showing data from ${fromDate} to ${toDate}`,
                timer: 2000,
                showConfirmButton: false
            });
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to fetch filtered data. Please try again.',
            });
        });
    }
    
    function updateTable(transactions) {
        const tbody = document.querySelector('table tbody');
        tbody.innerHTML = '';
        
        if (transactions.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No transactions found for the selected date range.</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        transactions.forEach(transaction => {
            const row = document.createElement('tr');
            
            if (transaction.cashier_id) { // It's a sale
                row.innerHTML = `
                    <td>${new Date(transaction.created_at).toLocaleString()}</td>
                    <td><span class="badge bg-primary">SALE</span></td>
                    <td>
                        <div>
                            <strong>Sale Transaction</strong><br>
                            <small class="text-muted">${transaction.sale_items ? transaction.sale_items.length : 0} items sold</small>
                        </div>
                    </td>
                    <td class="fw-bold text-success">₱${parseFloat(transaction.total_amount).toFixed(2)}</td>
                    <td>${transaction.user ? transaction.user.name : 'N/A'}</td>
                    <td><span class="badge bg-success">Completed</span></td>
                `;
            } else { // It's an expense
                row.innerHTML = `
                    <td>${new Date(transaction.created_at).toLocaleString()}</td>
                    <td><span class="badge bg-danger">EXPENSE</span></td>
                    <td>
                        <div>
                            <strong>${transaction.description}</strong><br>
                            <small class="text-muted">
                                ${transaction.category ? transaction.category.name : 'Uncategorized'}
                            </small>
                        </div>
                    </td>
                    <td class="fw-bold text-danger">₱${parseFloat(transaction.amount).toFixed(2)}</td>
                    <td>System</td>
                    <td><span class="badge bg-warning">Processed</span></td>
                `;
            }
            
            tbody.appendChild(row);
        });
    }
    
    function updateSummaryCards(summaries) {
        // Update today's sales card
        document.querySelector('.bg-primary h4').textContent = `₱${parseFloat(summaries.total_sales).toFixed(2)}`;
        
        // Update today's expenses card
        document.querySelector('.bg-danger h4').textContent = `₱${parseFloat(summaries.total_expenses).toFixed(2)}`;
        
        // Update net profit card
        document.querySelector('.bg-success h4').textContent = `₱${parseFloat(summaries.net_total).toFixed(2)}`;
        
        // Update transactions card
        document.querySelector('.bg-info h4').textContent = summaries.sales_count + summaries.expense_count;
        
        // Update summary row
        document.querySelector('.text-success h5').textContent = `₱${parseFloat(summaries.total_sales).toFixed(2)}`;
        document.querySelector('.text-danger h5').textContent = `₱${parseFloat(summaries.total_expenses).toFixed(2)}`;
        document.querySelector('.text-primary h5').textContent = `₱${parseFloat(summaries.net_total).toFixed(2)}`;
    }
    
    function updateProductBreakdown(salesByProduct) {
        // This could be used to show a product breakdown modal or section
        console.log('Sales by product:', salesByProduct);
    }
    </script>
</body>
</html>
