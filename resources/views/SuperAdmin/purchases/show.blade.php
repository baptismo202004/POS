@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Purchase Details</h2>
            <a href="{{ route('superadmin.purchases.index') }}" class="btn btn-outline-primary">Back to Purchases</a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Purchase Date:</strong> {{ optional($purchase->purchase_date)->format('M d, Y') }}</p>
                <p><strong>Total Cost:</strong> ₱{{ number_format($purchase->total_cost, 2) }}</p>
                <p><strong>Payment Status:</strong> <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($purchase->payment_status) }}</span></p>
            </div>
        </div>

        <h5 class="mt-4">Purchased Items</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Reference No.</th>
                        <th>Quantity</th>
                        <th>Unit Type</th>
                        <th>Unit Cost</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchase->items as $item)
                        <tr>
                            <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                            <td>{{ $item->reference_number ?: 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unitType->unit_name ?? 'N/A' }}</td>
                            <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                            <td>₱{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No items found for this purchase.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
