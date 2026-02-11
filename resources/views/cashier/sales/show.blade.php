@extends('layouts.app')
@section('title', 'Sale Details #{{ $sale->id }}')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Sale Details #{{ $sale->id }}</h4>
                    <div>
                        <a href="{{ route('cashier.sales.receipt', $sale) }}" class="btn btn-success">
                            <i class="fas fa-receipt"></i> Receipt
                        </a>
                        <a href="{{ route('cashier.sales.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sales
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Sale Information -->
                        <div class="col-md-6">
                            <h5>Sale Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Receipt #:</strong></td>
                                    <td>{{ $sale->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date & Time:</strong></td>
                                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status == 'completed' ? 'success' : ($sale->status == 'voided' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Payment Method:</strong></td>
                                    <td>{{ ucfirst($sale->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cashier:</strong></td>
                                    <td>{{ $sale->cashier->name ?? 'System' }}</td>
                                </tr>
                                @if($sale->voided_at)
                                <tr>
                                    <td><strong>Voided At:</strong></td>
                                    <td>{{ $sale->voided_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <table class="table table-borderless">
                                @if($sale->customer_name)
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $sale->customer_name }}</td>
                                </tr>
                                @endif
                                @if($sale->customer_contact)
                                <tr>
                                    <td><strong>Contact:</strong></td>
                                    <td>{{ $sale->customer_contact }}</td>
                                </tr>
                                @endif
                                @if(!$sale->customer_name && !$sale->customer_contact)
                                <tr>
                                    <td colspan="2" class="text-muted">No customer information</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Sale Items -->
                    <h5>Sale Items</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Type</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $item->unitType->name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Total Summary -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Payment Summary</h5>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>₱{{ number_format($sale->subtotal, 2) }}</span>
                                    </div>
                                    @if($sale->discount_amount > 0)
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Discount:</span>
                                        <span class="text-danger">-₱{{ number_format($sale->discount_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <h5>Total Amount:</h5>
                                        <h5>₱{{ number_format($sale->total_amount, 2) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Credit Information -->
                        @if($sale->credit)
                        <div class="col-md-6">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-credit-card"></i> Credit Information
                                    </h5>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Reference:</strong></td>
                                            <td>{{ $sale->credit->reference_number }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Credit Amount:</strong></td>
                                            <td>₱{{ number_format($sale->credit->credit_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paid Amount:</strong></td>
                                            <td>₱{{ number_format($sale->credit->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining Balance:</strong></td>
                                            <td class="text-danger">₱{{ number_format($sale->credit->remaining_balance, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $sale->credit->status == 'paid' ? 'success' : ($sale->credit->status == 'partial' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($sale->credit->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($sale->credit->notes)
                                        <tr>
                                            <td><strong>Notes:</strong></td>
                                            <td><em>{{ $sale->credit->notes }}</em></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-{{ $sale->credit ? '12' : '6' }} d-flex align-items-center justify-content-end mt-3">
                            @if($sale->status !== 'voided')
                                <button class="btn btn-danger" onclick="voidSale({{ $sale->id }})">
                                    <i class="fas fa-times"></i> Void Sale
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function voidSale(saleId) {
    if(confirm('Are you sure you want to void this sale? This action cannot be undone.')) {
        fetch(`/cashier/sales/${saleId}/void`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Sale voided successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while voiding the sale');
        });
    }
}
</script>
@endsection
