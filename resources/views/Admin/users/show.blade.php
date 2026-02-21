@extends('layouts.app')
@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">User Details</h2>
                <a href="{{ request()->header('referer') ?: route('superadmin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Users
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $user->name }}</h5>
                    <div class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($user->status) }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">User Information</h6>
                            <p><strong>Email:</strong> {{ $user->email ?? 'Not specified' }}</p>
                            <p><strong>Phone:</strong> {{ $user->phone ?? 'Not specified' }}</p>
                            <p><strong>User Type:</strong> {{ $user->userType->name ?? 'Not specified' }}</p>
                            <p><strong>Branch:</strong> {{ $user->branch->branch_name ?? 'Not assigned' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </p>
                            <p><strong>Created At:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                            <p><strong>Last Login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Performance Summary</h6>
                            <p><strong>Total Sales:</strong> ₱{{ number_format($user->sales ? $user->sales->sum('total_amount') : 0, 2) }}</p>
                            <p><strong>Average Sale:</strong> ₱{{ number_format($user->sales ? $user->sales->avg('total_amount') : 0, 2) }}</p>
                            <p><strong>Total Transactions:</strong> {{ $user->sales ? $user->sales->count() : 0 }}</p>
                            <p><strong>Today's Sales:</strong> ₱{{ number_format($user->sales ? $user->sales->filter(function($sale) { return \Carbon\Carbon::parse($sale->created_at)->isToday(); })->sum('total_amount') : 0, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">Recent Sales</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt #</th>
                                            <th>Total Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($user->sales->take(10) as $sale)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y') }}</td>
                                                <td>{{ $sale->reference_number ?? 'N/A' }}</td>
                                                <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                                <td>
                                                    <a href="/superadmin/sales/{{ $sale->id }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No sales found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
