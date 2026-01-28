@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card card-rounded shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0">Refunds & Returns</h4>
                <p class="mb-0 text-muted">Overview of your refund and return transactions</p>
            </div>
            <a href="{{ route('superadmin.admin.sales.index') }}" class="btn btn-primary">Go to Sales</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Refunds</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayRefunds->total_refund_amount ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-undo fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items Refunded Today</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayRefunds->total_items ?? 0 }} items</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month's Refunds</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlyRefunds->total_refund_amount ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Refunds Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Refunds</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                            <tr>
                                <td>{{ $refund->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $refund->product->product_name ?? 'N/A' }}</td>
                                <td>{{ $refund->quantity_refunded }}</td>
                                <td>₱{{ number_format($refund->refund_amount, 2) }}</td>
                                <td>{{ $refund->reason }}</td>
                                <td>
                                    <span class="badge bg-{{ $refund->status == 'approved' ? 'success' : ($refund->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($refund->status) }}
                                    </span>
                                </td>
                                <td>{{ $refund->cashier->name ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No refunds found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($refunds->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $refunds->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
