@extends('layouts.app')

@section('content')
<style>
.void-badge {
    background-color: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="fas fa-ban text-danger"></i> Voided Sales
                </h3>
            </div>

            <!-- Date Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('superadmin.sales.voided') }}">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">From Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="{{ $startDate->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-danger">
                        <div class="card-body text-center">
                            <h5 class="card-title text-danger">Total Voided Amount</h5>
                            <h2 class="mb-0">₱{{ number_format($totalVoidedAmount, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h5 class="card-title text-warning">Total Voided Sales</h5>
                            <h2 class="mb-0">{{ $totalVoidedCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Voided Sales Table -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> Voided Sales List
                    </h5>
                </div>
                <div class="card-body">
                    @if($voidedSales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Products</th>
                                        <th>Cashier</th>
                                        <th>Branch</th>
                                        <th>Items Count</th>
                                        <th>Original Items</th>
                                        <th>Total Amount</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        \Log::info("View processing: " . $voidedSales->count() . " sales");
                                    @endphp
                                    @foreach($voidedSales as $sale)
                                        @php
                                            \Log::info("View rendering Sale ID: " . $sale->id . " with " . $sale->saleItems->count() . " items");
                                        @endphp
                                        <tr>
                                            <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                @php
                                                    $productNames = $sale->saleItems->map(function($item) {
                                                        return $item->product ? $item->product->product_name : 'Unknown Product';
                                                    })->filter()->take(3);
                                                    
                                                    $display = $productNames->join(', ');
                                                    if ($sale->saleItems->count() > 3) {
                                                        $display .= '... (+' . ($sale->saleItems->count() - 3) . ' more)';
                                                    }
                                                @endphp
                                                {{ $display ?: 'No products' }}
                                            </td>
                                            <td>{{ $sale->cashier ? $sale->cashier->name : 'N/A' }}</td>
                                            <td>{{ $sale->branch ? $sale->branch->branch_name : 'N/A' }}</td>
                                            <td>{{ $sale->saleItems->sum('quantity') }}</td>
                                            <td>{{ $sale->original_items_count ?? 0 }}</td>
                                            <td class="text-danger fw-bold">₱{{ number_format($sale->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($sale->payment_method) }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('superadmin.sales.receipt', $sale->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-receipt"></i> Receipt
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-danger">
                                        <th colspan="6" class="text-right">Total:</th>
                                        <th class="text-danger fw-bold">₱{{ number_format($totalVoidedAmount, 2) }}</th>
                                        <th colspan="2">{{ $totalVoidedCount }} Sales</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $voidedSales->firstItem() }} to {{ $voidedSales->lastItem() }} 
                                of {{ $voidedSales->total() }} entries
                            </div>
                            {{ $voidedSales->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">No Voided Sales Found</h4>
                            <p class="text-muted">Great! No sales have been voided in the selected period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sale Details Modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showSaleDetails(saleId) {
    const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
    const content = document.getElementById('saleDetailsContent');
    
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    fetch(`/superadmin/sales/${saleId}`)
        .then(response => response.json())
        .then(data => {
            let itemsHtml = '';
            if (data.sale_items && data.sale_items.length > 0) {
                itemsHtml = data.sale_items.map(item => `
                    <tr>
                        <td>${item.product ? item.product.product_name : 'Unknown Product'}</td>
                        <td>${item.quantity}</td>
                        <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td>₱${(item.quantity * item.unit_price).toFixed(2)}</td>
                    </tr>
                `).join('');
            } else {
                itemsHtml = '<tr><td colspan="4" class="text-center">No items found</td></tr>';
            }
            
            content.innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Sale ID:</strong> #${data.id}<br>
                        <strong>Date:</strong> ${new Date(data.created_at).toLocaleString()}<br>
                        <strong>Cashier:</strong> ${data.cashier ? data.cashier.name : 'N/A'}<br>
                        <strong>Branch:</strong> ${data.branch ? data.branch.branch_name : 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong> ${data.payment_method}<br>
                        <strong>Status:</strong> <span class="badge bg-danger">VOIDED</span><br>
                        <strong>Total Amount:</strong> <span class="text-danger fw-bold">₱${parseFloat(data.total_amount).toFixed(2)}</span>
                    </div>
                </div>
                
                <h6 class="fw-bold">Sale Items:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>
                </div>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error loading sale details. Please try again.
                </div>
            `;
        });
}
</script>
@endsection
