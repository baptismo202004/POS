@extends('layouts.app')
@section('title', 'Branch Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Branch Details</h2>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $branch->branch_name }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Branch Information</h6>
                            <p><strong>Address:</strong> {{ $branch->address ?? 'Not specified' }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $branch->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($branch->status) }}
                                </span>
                            </p>
                            @if($branch->assignedUser)
                                <p><strong>Assigned to:</strong> {{ $branch->assignedUser->name }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Performance Statistics</h6>
                            <p><strong>Today's Sales:</strong> ₱{{ number_format($todaySales, 2) }}</p>
                            <p><strong>Monthly Sales:</strong> ₱{{ number_format($monthlySales, 2) }}</p>
                            <p><strong>Total Transactions:</strong> {{ number_format($totalTransactions) }}</p>
                        </div>
                    </div>
                    
                    <!-- All Sales Table (Last 30 Days) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted mb-3">All Sales (Last 30 Days)</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Cashier</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($allSalesData->isNotEmpty())
                                            @foreach ($allSalesData as $sale)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y') }}</td>
                                                    <td>{{ $sale->cashier_name }}</td>
                                                    <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No sales in the last 30 days</td>
                                            </tr>
                                        @endif
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

<script>
</script>
@endsection
