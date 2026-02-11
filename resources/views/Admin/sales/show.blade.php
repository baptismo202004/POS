@extends('layouts.app')
@section('title', 'Sale Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Sale Details</h2>
                <a href="{{ request()->referer() ?: route('superadmin.sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Receipt #{{ $sale->receipt_number ?? 'N/A' }}</h5>
                    <div class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : 'info' }}">
                        {{ ucfirst($sale->payment_method) }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Sale Information</h6>
                            <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}</p>
                            <p><strong>Branch:</strong> {{ $sale->branch->branch_name ?? 'N/A' }}</p>
                            <p><strong>Cashier:</strong> {{ $sale->cashier->name ?? 'N/A' }}</p>
                            <p><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
                            <p><strong>Total Amount:</strong> ₱{{ number_format($sale->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Summary</h6>
                            <p><strong>Total Items:</strong> {{ $totalQuantity }}</p>
                            <p><strong>Total Refunds:</strong> ₱{{ number_format($totalRefunds, 2) }}</p>
                            <p><strong>Net Amount:</strong> ₱{{ number_format($sale->total_amount - $totalRefunds, 2) }}</p>
                        </div>
                    </div>
                    
                    <h6 class="text-muted mb-3">Sale Items</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                    <th>Refunded</th>
                                    <th>Available for Refund</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->saleItems as $item)
                                    <tr>
                                        <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                        <td>{{ $item->refunds->where('status', 'approved')->sum('quantity_refunded') }}</td>
                                        <td>{{ $item->quantity - $item->refunds->where('status', 'approved')->sum('quantity_refunded') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
