@extends('layouts.app')
@section('title', 'Create Refund')

@section('content')
<div class="container-fluid">
    <div class="card card-rounded shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0">Create Refund</h4>
                <p class="mb-0 text-muted">Lookup a sale, select an item, then process a refund</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.refunds.index') }}" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to Refunds
                </a>
                <a href="{{ route('admin.sales.management.index') }}" class="btn btn-primary">
                    <i class="fas fa-receipt me-2"></i>Go to Sales
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(!empty($lookupError))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $lookupError }}
                </div>
            @endif

            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-white">Sale Lookup</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.refunds.create') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="sale_id_lookup" class="form-label">Sale / Receipt ID</label>
                            <input type="number" min="1" class="form-control" id="sale_id_lookup" name="sale_id" value="{{ request('sale_id') }}" placeholder="Enter Sale ID">
                        </div>
                        <div class="col-md-8 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Lookup Sale
                            </button>
                            <a href="{{ route('admin.refunds.create') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-rotate-left me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if(!empty($sale))
                <div class="card shadow-sm mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Sale #{{ $sale->id }}</strong>
                            <div class="small opacity-75">{{ $sale->created_at?->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="small opacity-75">Total</div>
                            <div class="fw-bold">₱{{ number_format((float) $sale->total_amount, 2) }}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="text-muted small">Customer</div>
                                <div class="fw-semibold">{{ $sale->customer_name ?: 'Walk-in' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Payment</div>
                                <div class="fw-semibold text-capitalize">{{ $sale->payment_method }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">Cashier</div>
                                <div class="fw-semibold">{{ $sale->cashier?->name ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Subtotal</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->saleItems as $item)
                                        <tr>
                                            <td class="fw-semibold">{{ $item->product?->product_name ?? 'N/A' }}</td>
                                            <td class="text-end">{{ number_format((float) $item->quantity) }}</td>
                                            <td class="text-end">₱{{ number_format((float) $item->unit_price, 2) }}</td>
                                            <td class="text-end">₱{{ number_format((float) $item->subtotal, 2) }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-sm btn-outline-primary"
                                                   href="{{ route('admin.refunds.create', ['sale_id' => $sale->id, 'sale_item_id' => $item->id]) }}">
                                                    <i class="fas fa-hand-pointer me-1"></i>Select
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="text-muted small">
                            Select an item to refund.
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($sale) && !empty($saleItem))
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-white">Refund Item</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.refunds.store') }}">
                            @csrf
                            <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                            <input type="hidden" name="sale_item_id" value="{{ $saleItem->id }}">
                            <input type="hidden" name="product_id" value="{{ $saleItem->product_id }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1">Product</div>
                                    <div class="fw-semibold">{{ $saleItem->product?->product_name ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Sold Qty</div>
                                    <div class="fw-semibold">{{ number_format((float) $saleItem->quantity) }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-muted small mb-1">Unit Price</div>
                                    <div class="fw-semibold">₱{{ number_format((float) $saleItem->unit_price, 2) }}</div>
                                </div>

                                <div class="col-md-3">
                                    <label for="quantity_refunded" class="form-label">Refund Quantity</label>
                                    <input type="number"
                                           class="form-control"
                                           id="quantity_refunded"
                                           name="quantity_refunded"
                                           min="1"
                                           max="{{ (int) $saleItem->quantity }}"
                                           value="1"
                                           required>
                                </div>
                                <div class="col-md-3">
                                    <label for="refund_amount" class="form-label">Refund Amount</label>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           class="form-control"
                                           id="refund_amount"
                                           name="refund_amount"
                                           value="{{ number_format((float) $saleItem->unit_price, 2, '.', '') }}"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label for="reason" class="form-label">Reason (optional)</label>
                                    <input type="text" class="form-control" id="reason" name="reason" maxlength="255" placeholder="e.g. Wrong item / Defective / Customer return">
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notes (optional)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Additional details..."></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-undo me-2"></i>Process Refund
                                        </button>
                                        <a href="{{ route('admin.refunds.create', ['sale_id' => $sale->id]) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Back to Items
                                        </a>
                                    </div>
                                    <div class="text-muted small mt-2">
                                        This will add stock back and deduct the refund amount from the sale total.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('quantity_refunded');
    const amountInput = document.getElementById('refund_amount');

    if (!qtyInput || !amountInput) {
        return;
    }

    const unitPrice = {{ !empty($saleItem) ? (float) $saleItem->unit_price : 0 }};

    function syncAmountFromQty() {
        const qty = parseInt(qtyInput.value || '1', 10);
        if (!Number.isFinite(qty) || qty < 1) {
            return;
        }
        amountInput.value = (unitPrice * qty).toFixed(2);
    }

    qtyInput.addEventListener('input', syncAmountFromQty);
});
</script>
@endpush

