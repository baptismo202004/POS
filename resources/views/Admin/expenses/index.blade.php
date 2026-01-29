@extends('layouts.app')
@section('title', 'Create User')

@section('content')
    <div class="container-fluid">
        <div class="card card-rounded shadow-sm">
                    <div class="card-header">
                        <h4 class="m-0">Expenses</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.admin.expenses.index') }}" method="GET" class="mb-4">
                            <div class="row gy-2 gx-3 align-items-center">
                                <div class="col-auto">
                                    <a href="{{ route('superadmin.admin.expenses.create') }}" class="btn btn-primary">Add Expense</a>
                                </div>
                                <div class="col-auto">
                                    <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-auto">
                                    <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-auto">
                                    <select class="form-select" name="category_filter">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_filter') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3 ms-auto">
                                    <input type="text" class="form-control" name="search_input" placeholder="Search..." value="{{ request('search_input') }}">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-secondary">Filter</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Payment Method</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-center">Receipt</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                                            <td><span class="badge bg-secondary">{{ $expense->category->name }}</span></td>
                                            <td>
                                                @if($expense->purchase_id)
                                                    <i class="fas fa-lock me-1"></i>
                                                @endif
                                                {{ $expense->description }}
                                            </td>
                                            <td>{{ $expense->supplier->name ?? 'N/A' }}</td>
                                            <td>{{ $expense->payment_method }}</td>
                                            <td class="text-end">₱{{ number_format($expense->amount, 2) }}</td>
                                            <td class="text-center">
                                                @if($expense->receipt_path)
                                                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank"><i class="fas fa-paperclip"></i></a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('superadmin.admin.expenses.show', $expense) }}" class="btn btn-sm btn-info">View</a>
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
                                            <td colspan="8" class="text-center py-5">
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
