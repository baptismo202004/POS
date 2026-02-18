@extends('layouts.app')

@section('content')
    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">Expenses</h2>
                                <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">Add New Expense</a>
                            </div>

                            <!-- Search and Filter Section -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" id="expenseSearch" class="form-control" placeholder="Search expenses..." onkeyup="searchExpenses()">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="categoryFilter" class="form-select" onchange="filterExpenses()">
                                        <option value="">All Categories</option>
                                        @foreach ($categories ?? [] as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="paymentMethodFilter" class="form-select" onchange="filterExpenses()">
                                        <option value="">All Payment Methods</option>
                                        <option value="cash">Cash</option>
                                        <option value="purchase_order">Purchase Order</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateFilter" class="form-control" onchange="filterExpenses()">
                                </div>
                            </div>

                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card border-left-primary shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Expenses</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayExpenses ?? 0, 2) }}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">This Month</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlyExpenses ?? 0, 2) }}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-left-info shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Expenses</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalExpenses ?? 0, 2) }}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Expenses Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered">
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
