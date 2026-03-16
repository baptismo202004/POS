@extends('layouts.app')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
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

    .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif;}

    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:26px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
    .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}

    .sp-stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;}
    @media(max-width:992px){.sp-stats-grid{grid-template-columns:1fr;}}
    .sp-stat-card{background:var(--card);border-radius:18px;border:1px solid var(--border);box-shadow:0 4px 20px rgba(13,71,161,0.07);padding:18px 20px;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
    .sp-stat-card:nth-child(1)::before{background:linear-gradient(90deg,var(--navy),var(--blue-lt));}
    .sp-stat-card:nth-child(2)::before{background:linear-gradient(90deg,#059669,#10b981);}
    .sp-stat-card:nth-child(3)::before{background:linear-gradient(90deg,#0e7490,#06b6d4);}
    .sp-stat-label{font-size:10.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:7px;font-family:'Nunito',sans-serif;}
    .sp-stat-value{font-family:'Nunito',sans-serif;font-size:20px;font-weight:900;color:var(--navy);line-height:1;}
    .sp-stat-icon{width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;background:rgba(13,71,161,0.10);color:var(--navy);}
    .sp-stat-card:nth-child(2) .sp-stat-icon{background:rgba(16,185,129,0.10);color:#059669;}
    .sp-stat-card:nth-child(3) .sp-stat-icon{background:rgba(6,182,212,0.10);color:#0e7490;}

    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .55s ease both;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
    .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-card-body{padding:18px 22px;}

    .sp-filter-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:12px;}
    @media(max-width:992px){.sp-filter-grid{grid-template-columns:1fr;}}
    .sp-input{border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;font-size:13.5px;color:var(--text);background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;transition:border-color .18s,box-shadow .18s;outline:none;box-shadow:none;width:100%;}
    .sp-input:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12);background:#fff;}
    .sp-input-group{position:relative;}
    .sp-input-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:rgba(13,71,161,0.55);}

    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@section('content')
    <div class="d-flex min-vh-100" style="background:var(--bg);">
        <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
        <main class="flex-fill p-4" style="position:relative;z-index:1;">
            <div class="sp-wrap">

                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-receipt"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Finance</div>
                            <div class="sp-ph-title">Expenses</div>
                            <div class="sp-ph-sub">Track and review operational expenses</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.expenses.create') }}" class="sp-btn sp-btn-primary"><i class="fas fa-plus"></i> Add New Expense</a>
                </div>

                <div class="sp-stats-grid">
                    <div class="sp-stat-card">
                        <div>
                            <div class="sp-stat-label">Today's Expenses</div>
                            <div class="sp-stat-value">₱{{ number_format($todayExpenses ?? 0, 2) }}</div>
                        </div>
                        <div class="sp-stat-icon"><i class="fas fa-calendar-day"></i></div>
                    </div>
                    <div class="sp-stat-card">
                        <div>
                            <div class="sp-stat-label">This Month</div>
                            <div class="sp-stat-value">₱{{ number_format($monthlyExpenses ?? 0, 2) }}</div>
                        </div>
                        <div class="sp-stat-icon"><i class="fas fa-calendar"></i></div>
                    </div>
                    <div class="sp-stat-card">
                        <div>
                            <div class="sp-stat-label">Total Expenses</div>
                            <div class="sp-stat-value">₱{{ number_format($totalExpenses ?? 0, 2) }}</div>
                        </div>
                        <div class="sp-stat-icon"><i class="fas fa-layer-group"></i></div>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-list"></i> Expense Records</div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-filter-grid" style="margin-bottom:14px;">
                            <div class="sp-input-group">
                                <input type="text" id="expenseSearch" class="sp-input" placeholder="Search expenses..." onkeyup="searchExpenses()">
                                <span class="sp-input-icon"><i class="fas fa-search"></i></span>
                            </div>
                            <select id="categoryFilter" class="sp-input" onchange="filterExpenses()">
                                <option value="">All Categories</option>
                                @foreach ($categories ?? [] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <select id="paymentMethodFilter" class="sp-input" onchange="filterExpenses()">
                                <option value="">All Payment Methods</option>
                                <option value="cash">Cash</option>
                                <option value="purchase_order">Purchase Order</option>
                            </select>
                            <input type="date" id="dateFilter" class="sp-input" onchange="filterExpenses()">
                        </div>

                        <div class="sp-table-wrap">
                            <table class="sp-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->id }}</td>
                                        <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>₱{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ $expense->payment_method }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <h5 class="mb-0">No expenses recorded yet</h5>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection

<script>
function searchExpenses() {
    const searchTerm = document.getElementById('expenseSearch').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const description = row.cells[3]?.textContent.toLowerCase() || '';
        const category = row.cells[2]?.textContent.toLowerCase() || '';
        const paymentMethod = row.cells[5]?.textContent.toLowerCase() || '';
        const amount = row.cells[4]?.textContent.toLowerCase() || '';
        
        const matchesSearch = description.includes(searchTerm) || 
                           category.includes(searchTerm) || 
                           paymentMethod.includes(searchTerm) || 
                           amount.includes(searchTerm);
        
        row.style.display = matchesSearch ? '' : 'none';
    });
}

function filterExpenses() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const paymentMethodFilter = document.getElementById('paymentMethodFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        let showRow = true;
        
        // Category filter
        if (categoryFilter && row.cells[2]?.textContent.trim() !== '') {
            const categoryText = row.cells[2].textContent.trim();
            const categoryOption = document.querySelector(`#categoryFilter option[value="${categoryFilter}"]`);
            if (categoryOption && categoryText !== categoryOption.textContent) {
                showRow = false;
            }
        }
        
        // Payment method filter
        if (paymentMethodFilter && row.cells[5]?.textContent.trim() !== '') {
            const paymentMethodText = row.cells[5].textContent.trim().toLowerCase().replace(' ', '_');
            if (paymentMethodText !== paymentMethodFilter) {
                showRow = false;
            }
        }
        
        // Date filter
        if (dateFilter && row.cells[1]?.textContent.trim() !== '') {
            const rowDate = row.cells[1].textContent.trim();
            const formattedDate = new Date(rowDate).toISOString().split('T')[0];
            if (formattedDate !== dateFilter) {
                showRow = false;
            }
        }
        
        row.style.display = showRow ? '' : 'none';
    });
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Apply any existing filters
    filterExpenses();
});
</script>
