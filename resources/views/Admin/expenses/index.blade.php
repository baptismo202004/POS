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
                                <a href="{{ route('superadmin.admin.expenses.create') }}" class="btn btn-primary">Add New Expense</a>
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
                                            <th>Actions</th>
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
                                            <td>
                                                @can('update-expense', $expense)
                                                    <a href="{{ route('superadmin.admin.expenses.edit', $expense) }}" class="btn btn-sm btn-warning">Edit</a>
                                                @endcan
                                                @can('delete-expense', $expense)
                                                    <form action="{{ route('superadmin.admin.expenses.destroy', $expense) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
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
