@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
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
                <div class="card stat-card bg-primary text-white shadow-sm hover-card" onclick="showMonthlySalesModal()">
                    <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Monthly Sales</h6>
                                        <h4 class="mb-0">₱{{ number_format(\App\Models\Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'), 2) }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-danger text-white shadow-sm hover-card" onclick="showTodaysExpensesModal()">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">Today's Expenses</h6>
                                        <h4 class="mb-0">₱{{ number_format(\App\Models\Expense::whereDate('expense_date', today())->sum('amount'), 2) }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-money-bill-wave fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-success text-white shadow-sm hover-card" onclick="showThisMonthExpensesModal()">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50 mb-2">This Month's Expenses</h6>
                                        <h4 class="mb-0">₱{{ number_format(\App\Models\Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'), 2) }}</h4>
                                    </div>
                                    <div class="text-white-50">
                                        <i class="fas fa-money-bill-wave fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card bg-info text-white shadow-sm hover-card" onclick="showTransactionsDetails()">
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
    
    // Functions for clickable stat cards
    function showMonthlySalesModal() {
        const modal = new bootstrap.Modal(document.getElementById('monthlySalesModal'));
        modal.show();
        
        // Reset content
        document.getElementById('monthlySalesLoading').style.display = 'block';
        document.getElementById('monthlySalesContent').style.display = 'none';
        
        // Fetch monthly sales data
        fetch('/superadmin/admin/sales/this-month-sales')
            .then(response => response.json())
            .then(data => {
                document.getElementById('monthlySalesLoading').style.display = 'none';
                document.getElementById('monthlySalesContent').style.display = 'block';
                
                // Populate table
                const tbody = document.getElementById('monthlySalesTableBody');
                tbody.innerHTML = '';
                
                if (data.daily_sales && data.daily_sales.length > 0) {
                    data.daily_sales.forEach(day => {
                        const row = `
                            <tr>
                                <td>${new Date(day.date).toLocaleDateString()}</td>
                                <td>₱${parseFloat(day.total_sales).toFixed(2)}</td>
                                <td>${day.transactions}</td>
                                <td>₱${parseFloat(day.average).toFixed(2)}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No sales this month.</td></tr>';
                }
            })
            .catch(error => {
                document.getElementById('monthlySalesLoading').style.display = 'none';
                document.getElementById('monthlySalesContent').style.display = 'block';
                document.getElementById('monthlySalesTableBody').innerHTML = 
                    '<tr><td colspan="4" class="text-center text-danger">Error loading monthly data. Please try again.</td></tr>';
            });
    }
    
    function viewMonthlySalesDetails() {
        // Navigate to sales page with current month filter
        const currentDate = new Date();
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        
        // Show monthly sales in the current month
        window.location.href = "/superadmin/admin/sales?from_date=" + firstDay.toISOString().split('T')[0] + "&to_date=" + lastDay.toISOString().split('T')[0];
    }
    
    function showTodaysExpensesDetails() {
        // Navigate to expenses page with today's date
        window.location.href = "/superadmin/admin/expenses?date={{ now()->format('Y-m-d') }}";
    }
    
    function showTodaysExpensesModal() {
        const modal = new bootstrap.Modal(document.getElementById('todaysExpensesModal'));
        modal.show();
        
        // Reset content
        document.getElementById('todaysExpensesLoading').style.display = 'block';
        document.getElementById('todaysExpensesContent').style.display = 'none';
        
        // Fetch today's expenses data
        fetch('{{ url('/superadmin/admin/expenses/todays-expenses') }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('todaysExpensesLoading').style.display = 'none';
                document.getElementById('todaysExpensesContent').style.display = 'block';
                
                // Populate table
                const tbody = document.getElementById('todaysExpensesTableBody');
                tbody.innerHTML = '';
                
                if (data.expenses && data.expenses.length > 0) {
                    data.expenses.forEach(expense => {
                        const row = `
                            <tr>
                                <td>${new Date(expense.created_at).toLocaleTimeString()}</td>
                                <td>${expense.description || 'N/A'}</td>
                                <td>₱${parseFloat(expense.amount).toFixed(2)}</td>
                                <td>${expense.category || 'N/A'}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No expenses today.</td></tr>';
                }
            })
            .catch(error => {
                document.getElementById('todaysExpensesLoading').style.display = 'none';
                document.getElementById('todaysExpensesContent').style.display = 'block';
                document.getElementById('todaysExpensesTableBody').innerHTML = 
                    '<tr><td colspan="4" class="text-center text-danger">Error loading expenses data. Please try again.</td></tr>';
            });
    }
    
    function showThisMonthExpensesModal() {
        const modal = new bootstrap.Modal(document.getElementById('thisMonthExpensesModal'));
        modal.show();
        
        // Reset content
        document.getElementById('thisMonthExpensesLoading').style.display = 'block';
        document.getElementById('thisMonthExpensesContent').style.display = 'none';
        
        // Fetch this month's expenses data
        fetch('{{ url('/superadmin/admin/expenses/this-month-expenses') }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                document.getElementById('thisMonthExpensesLoading').style.display = 'none';
                document.getElementById('thisMonthExpensesContent').style.display = 'block';
                
                // Populate table
                const tbody = document.getElementById('thisMonthExpensesTableBody');
                tbody.innerHTML = '';
                
                if (data.expenses && data.expenses.length > 0) {
                    data.expenses.forEach(expense => {
                        const row = `
                            <tr>
                                <td>${new Date(expense.created_at).toLocaleDateString()}</td>
                                <td>${expense.description || 'N/A'}</td>
                                <td>₱${parseFloat(expense.amount).toFixed(2)}</td>
                                <td>${expense.category || 'N/A'}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No expenses this month.</td></tr>';
                }
            })
            .catch(error => {
                document.getElementById('thisMonthExpensesLoading').style.display = 'none';
                document.getElementById('thisMonthExpensesContent').style.display = 'block';
                document.getElementById('thisMonthExpensesTableBody').innerHTML = 
                    '<tr><td colspan="4" class="text-center text-danger">Error loading expenses data. Please try again.</td></tr>';
            });
    }
    
    function showNetProfitDetails() {
        // Show a modal or navigate to detailed profit analysis
        alert('Net Profit Details: This would show a detailed profit breakdown with charts and analysis.');
    }
    
    function showTransactionsDetails() {
        // Navigate to sales page with today's date to see all transactions
        window.location.href = "/superadmin/admin/sales?date={{ now()->format('Y-m-d') }}";
    }
</script>

<!-- Today's Expenses Modal -->
<div class="modal fade" id="todaysExpensesModal" tabindex="-1" aria-labelledby="todaysExpensesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="todaysExpensesModalLabel">Today's Expenses - {{ now()->format('F d, Y') }}</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary btn-sm me-2" onclick="showTodaysExpensesDetails()">
                        <i class="fas fa-list me-1"></i>View More
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center" id="todaysExpensesLoading">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading today's expenses details...</p>
                </div>
                <div id="todaysExpensesContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody id="todaysExpensesTableBody">
                                <!-- Today's expenses data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- This Month's Expenses Modal -->
<div class="modal fade" id="thisMonthExpensesModal" tabindex="-1" aria-labelledby="thisMonthExpensesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="thisMonthExpensesModalLabel">This Month's Expenses - {{ now()->format('F Y') }}</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary btn-sm me-2" onclick="showTodaysExpensesDetails()">
                        <i class="fas fa-list me-1"></i>View More
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center" id="thisMonthExpensesLoading">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading this month's expenses details...</p>
                </div>
                <div id="thisMonthExpensesContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody id="thisMonthExpensesTableBody">
                                <!-- This month's expenses data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .hover-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        opacity: 0.9;
    }
    
    .hover-card:hover .fa-shopping-cart {
        color: #fff !important;
        transform: scale(1.1);
    }
    
    .hover-card:hover .fa-money-bill-wave {
        color: #fff !important;
        transform: scale(1.1);
    }
    
    .hover-card:hover .fa-chart-line {
        color: #fff !important;
        transform: scale(1.1);
    }
    
    .hover-card:hover .fa-receipt {
        color: #fff !important;
        transform: scale(1.1);
    }
</style>

<!-- Monthly Sales Modal -->
<div class="modal fade" id="monthlySalesModal" tabindex="-1" aria-labelledby="monthlySalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="monthlySalesModalLabel">Monthly Sales - {{ now()->format('F Y') }}</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center" id="monthlySalesLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading monthly sales details...</p>
                </div>
                <div id="monthlySalesContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Sales</th>
                                    <th>Transactions</th>
                                    <th>Average</th>
                                </tr>
                            </thead>
                            <tbody id="monthlySalesTableBody">
                                <!-- Monthly sales data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
