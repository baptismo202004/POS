@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<style>
    :root{
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
        --green:#10b981;--red:#ef4444;--amber:#f59e0b;
        --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
        --text:#1a2744;--muted:#6b84aa;
    }

    .sp-page{position:relative;}
    .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
    .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
    .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
    .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
    .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    .sp-wrap{position:relative;z-index:1;padding:18px 10px 42px;}
    @media (min-width: 992px){.sp-wrap{padding:24px 18px 54px;}}

    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:14px;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:800;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;}
    .sp-ph-title{font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:800;cursor:pointer;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
    .sp-btn-ghost{background:rgba(13,71,161,0.04);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-ghost:hover{background:var(--navy);color:#fff;border-color:var(--navy);}

    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
    .sp-card-head-title{font-size:14.5px;font-weight:900;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-card-body{padding:18px 22px;}

    .sp-stat{display:flex;align-items:stretch;gap:14px;padding:16px 16px;border-radius:18px;color:#fff;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.12);box-shadow:0 10px 26px rgba(13,71,161,0.12);cursor:pointer;transition:transform .2s ease,box-shadow .2s ease,opacity .2s ease;min-height:92px;}
    .sp-stat:hover{transform:translateY(-3px);box-shadow:0 16px 34px rgba(13,71,161,0.18);opacity:.98;}
    .sp-stat::after{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 70% 120% at 85% 50%,rgba(255,255,255,0.18),transparent);pointer-events:none;}
    .sp-stat .sp-stat-meta{position:relative;z-index:1;flex:1;min-width:0;}
    .sp-stat .sp-stat-k{font-size:11px;letter-spacing:.13em;text-transform:uppercase;font-weight:900;opacity:.88;margin:0 0 6px;}
    .sp-stat .sp-stat-v{font-size:18px;font-weight:900;margin:0;line-height:1.15;}
    .sp-stat .sp-stat-ic{position:relative;z-index:1;width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.16);flex:0 0 auto;}
    .sp-stat-blue{background:linear-gradient(135deg,#0D47A1,#1976D2);}
    .sp-stat-red{background:linear-gradient(135deg,#b91c1c,#ef4444);}
    .sp-stat-green{background:linear-gradient(135deg,#047857,#10b981);}
    .sp-stat-cyan{background:linear-gradient(135deg,#0ea5e9,#22d3ee);}

    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:900;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}

    .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:900;letter-spacing:.02em;border:1px solid transparent;}
    .sp-badge-sale{background:rgba(25,118,210,0.10);color:var(--navy);border-color:rgba(25,118,210,0.18);}
    .sp-badge-expense{background:rgba(239,68,68,0.10);color:#b91c1c;border-color:rgba(239,68,68,0.18);}
    .sp-badge-done{background:rgba(16,185,129,0.12);color:#047857;border-color:rgba(16,185,129,0.22);}
    .sp-badge-proc{background:rgba(245,158,11,0.12);color:#b45309;border-color:rgba(245,158,11,0.22);}

    .sp-sum{display:grid;grid-template-columns:repeat(1,minmax(0,1fr));gap:12px;margin-top:18px;padding-top:16px;border-top:1px solid rgba(25,118,210,0.10);}
    @media (min-width: 768px){.sp-sum{grid-template-columns:repeat(3,minmax(0,1fr));}}
    .sp-sum-item{background:rgba(235,243,251,0.60);border:1px solid rgba(25,118,210,0.10);border-radius:16px;padding:14px 14px;text-align:center;}
    .sp-sum-item .k{font-size:11px;font-weight:900;letter-spacing:.13em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;}
    .sp-sum-item .v{font-size:16px;font-weight:900;margin:0;}
</style>

<div class="sp-page">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <div class="container-fluid">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Admin</div>
                        <div class="sp-ph-title">Reports</div>
                        <div class="sp-ph-sub">Business reports and transaction insights</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="sp-btn sp-btn-primary" onclick="exportReport()"><i class="fas fa-download"></i> Export Report</button>
                    <button type="button" class="sp-btn sp-btn-ghost" onclick="refreshData()"><i class="fas fa-sync-alt"></i> Refresh</button>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="sp-stat sp-stat-blue hover-card" onclick="showMonthlySalesModal()">
                        <div class="sp-stat-meta">
                            <div class="sp-stat-k">Monthly Sales</div>
                            <div class="sp-stat-v" id="stat-card-sales">₱{{ number_format(\App\Models\Sale::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'), 2) }}</div>
                        </div>
                        <div class="sp-stat-ic"><i class="fas fa-shopping-cart"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sp-stat sp-stat-red hover-card" onclick="showTodaysExpensesModal()">
                        <div class="sp-stat-meta">
                            <div class="sp-stat-k">Today's Expenses</div>
                            <div class="sp-stat-v" id="stat-card-today-expenses">₱{{ number_format(\App\Models\Expense::whereDate('expense_date', today())->sum('amount'), 2) }}</div>
                        </div>
                        <div class="sp-stat-ic"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sp-stat sp-stat-green">
                        <div class="sp-stat-meta">
                            <div class="sp-stat-k">This Month's Expenses</div>
                            <div class="sp-stat-v" id="stat-card-month-expenses">₱{{ number_format(\App\Models\Expense::whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'), 2) }}</div>
                        </div>
                        <div class="sp-stat-ic"><i class="fas fa-money-bill-wave"></i></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="sp-stat sp-stat-cyan hover-card" onclick="showTransactionsDetails()">
                        <div class="sp-stat-meta">
                            <div class="sp-stat-k">Transactions</div>
                            <div class="sp-stat-v" id="stat-card-transactions">{{ \App\Models\Sale::whereDate('created_at', today())->count() }}</div>
                        </div>
                        <div class="sp-stat-ic"><i class="fas fa-receipt"></i></div>
                    </div>
                </div>
            </div>

                <!-- Combined Reports Table -->
                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-list"></i> Business Transactions</div>
                        <div class="d-flex gap-2" style="position:relative;z-index:1;"></div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-table-wrap">
                            <table class="sp-table" width="100%" cellspacing="0">
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
                                                <td><span class="sp-badge sp-badge-sale">SALE</span></td>
                                                <td>
                                                    <div>
                                                        <strong>Sale Transaction</strong><br>
                                                        <small class="text-muted">{{ $transaction->saleItems->count() }} items sold</small>
                                                    </div>
                                                </td>
                                                <td class="fw-bold text-success">₱{{ number_format($transaction->total_amount, 2) }}</td>
                                                <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                                                <td><span class="sp-badge sp-badge-done">Completed</span></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                <td><span class="sp-badge sp-badge-expense">EXPENSE</span></td>
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
                                                <td><span class="sp-badge sp-badge-proc">Processed</span></td>
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
                        <div class="sp-sum">
                            <div class="sp-sum-item">
                                <div class="k">Total Sales</div>
                                <p class="v text-success" id="summary-row-sales">₱{{ number_format($recentSales->sum('total_amount'), 2) }}</p>
                            </div>
                            <div class="sp-sum-item">
                                <div class="k">Total Expenses</div>
                                <p class="v text-danger" id="summary-row-expenses">₱{{ number_format($recentExpenses->sum('amount'), 2) }}</p>
                            </div>
                            <div class="sp-sum-item">
                                <div class="k">Net Total</div>
                                <p class="v text-primary" id="summary-row-net">₱{{ number_format($recentSales->sum('total_amount') - $recentExpenses->sum('amount'), 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function exportReport() {
        const fromEl = document.getElementById('from_date');
        const toEl = document.getElementById('to_date');
        const today = new Date();
        const fromFallback = new Date(today);
        fromFallback.setDate(fromFallback.getDate() - 30);

        const fromDate = fromEl && fromEl.value ? fromEl.value : fromFallback.toISOString().split('T')[0];
        const toDate = toEl && toEl.value ? toEl.value : today.toISOString().split('T')[0];
        
        // Create form and submit for download
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.reports.export") }}';
        
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
            
            const transactionDate = new Date(transaction.transaction_date).toLocaleString();

            if (transaction.cashier_id) { // It's a sale
                row.innerHTML = `
                    <td>${transactionDate}</td>
                    <td><span class="sp-badge sp-badge-sale">SALE</span></td>
                    <td>
                        <div>
                            <strong>Sale Transaction</strong><br>
                            <small class="text-muted">${transaction.sale_items ? transaction.sale_items.length : 0} items sold</small>
                        </div>
                    </td>
                    <td class="fw-bold text-success">₱${parseFloat(transaction.total_amount).toFixed(2)}</td>
                    <td>${transaction.user ? transaction.user.name : 'N/A'}</td>
                    <td><span class="sp-badge sp-badge-done">Completed</span></td>
                `;
            } else { // It's an expense
                row.innerHTML = `
                    <td>${transactionDate}</td>
                    <td><span class="sp-badge sp-badge-expense">EXPENSE</span></td>
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
                    <td><span class="sp-badge sp-badge-proc">Processed</span></td>
                `;
            }
            
            tbody.appendChild(row);
        });
    }
    
    function updateSummaryCards(summaries) {
        // Update stat card titles for filtered view
        document.querySelector('#stat-card-sales').parentElement.querySelector('h6').textContent = 'Total Sales';
        document.querySelector('#stat-card-today-expenses').parentElement.querySelector('h6').textContent = 'Total Expenses';
        document.querySelector('#stat-card-month-expenses').parentElement.querySelector('h6').textContent = 'Net Total';
        document.querySelector('#stat-card-transactions').parentElement.querySelector('h6').textContent = 'Total Transactions';

        // Update stat card values
        document.getElementById('stat-card-sales').textContent = `₱${parseFloat(summaries.total_sales).toFixed(2)}`;
        document.getElementById('stat-card-today-expenses').textContent = `₱${parseFloat(summaries.total_expenses).toFixed(2)}`;
        document.getElementById('stat-card-month-expenses').textContent = `₱${parseFloat(summaries.net_total).toFixed(2)}`;
        document.getElementById('stat-card-transactions').textContent = summaries.sales_count + summaries.expense_count;
        
        // Update summary row
        document.getElementById('summary-row-sales').textContent = `₱${parseFloat(summaries.total_sales).toFixed(2)}`;
        document.getElementById('summary-row-expenses').textContent = `₱${parseFloat(summaries.total_expenses).toFixed(2)}`;
        document.getElementById('summary-row-net').textContent = `₱${parseFloat(summaries.net_total).toFixed(2)}`;
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
