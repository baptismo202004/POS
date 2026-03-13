@extends('layouts.app')
@section('title', 'Create Refund')

@section('content')
@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--bg:#EBF3FB;--card:#fff;
        --border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;
    }
    .sp-page{background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--text);}
    .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
    .sp-bg::before{content:'';position:absolute;inset:0;background:
        radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),
        radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);
    }
    .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
    .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;}
    .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;}
    .sp-wrap{position:relative;z-index:1;padding:18px 24px 44px;}
    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:12px;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
    .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}
    .sp-ph-actions{display:flex;align-items:center;gap:9px;flex-wrap:wrap;}
    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
    .sp-btn-outline{background:var(--card);color:var(--navy);border:1.5px solid var(--border);}
    .sp-btn-outline:hover{background:var(--navy);color:#fff;border-color:var(--navy);}
    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;}
    .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;}
    .sp-card-body{padding:18px 22px;}
    .sp-search{border-radius:11px !important;border:1.5px solid var(--border) !important;padding:9px 14px !important;box-shadow:none !important;}
    .sp-search:focus{border-color:var(--blue-lt) !important;box-shadow:0 0 0 3px rgba(66,165,245,0.12) !important;}
    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
    .sp-table thead th{background:linear-gradient(135deg, rgba(13,71,161,0.92), rgba(25,118,210,0.92));padding:11px 16px;font-size:11px;font-weight:800;color:#fff;letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:13px 16px;font-size:13.5px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
</style>
@endpush

<div class="sp-page">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <div class="d-flex min-vh-100">
        <main class="flex-fill p-4" style="position:relative;z-index:1;">
            <div class="sp-wrap">
                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-undo"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Refunds</div>
                            <div class="sp-ph-title">Create Refund</div>
                            <div class="sp-ph-sub">Lookup a sale, select an item, then process a refund</div>
                        </div>
                    </div>
                    <div class="sp-ph-actions">
                        <a href="{{ route('admin.refunds.index') }}" class="sp-btn sp-btn-outline">
                            <i class="fas fa-arrow-left"></i> Back to Refunds
                        </a>
                        <a href="{{ route('admin.sales.management.index') }}" class="sp-btn sp-btn-primary">
                            <i class="fas fa-receipt"></i> Go to Sales
                        </a>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-magnifying-glass"></i> Refund Processing</div>
                    </div>

                    <div class="sp-card-body">
            @if(!empty($lookupError))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $lookupError }}
                </div>
            @endif

            <div class="sp-card" style="margin-bottom:16px;">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-search"></i> Sale Lookup</div>
                </div>
                <div class="sp-card-body">
                    <form method="GET" action="{{ route('admin.refunds.create') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="sale_id_lookup" class="form-label">Sale / Receipt ID</label>
                            <input type="number" min="1" class="form-control sp-search" id="sale_id_lookup" name="sale_id" value="{{ request('sale_id') }}" placeholder="Enter Sale ID">
                        </div>
                        <div class="col-md-8 d-flex gap-2">
                            <button type="submit" class="sp-btn sp-btn-primary">
                                <i class="fas fa-search me-2"></i>Lookup Sale
                            </button>
                            <a href="{{ route('admin.refunds.create') }}" class="sp-btn sp-btn-outline">
                                <i class="fas fa-rotate-left me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if(!empty($sale))
                <div class="sp-card" style="margin-bottom:16px;">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-receipt"></i> Sale #{{ $sale->id }}</div>
                        <div>
                            <div style="color:rgba(255,255,255,0.9);font-weight:700;">{{ $sale->created_at?->format('M d, Y h:i A') }}</div>
                        </div>
                        <div class="text-end">
                            <div style="color:rgba(255,255,255,0.85);font-size:12px;">Total</div>
                            <div style="color:#fff;font-weight:900;">₱{{ number_format((float) $sale->total_amount, 2) }}</div>
                        </div>
                    </div>
                    <div class="sp-card-body">
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
                            <table class="sp-table table table-bordered table-striped table-hover align-middle" width="100%" cellspacing="0">
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
                                                <a class="sp-btn sp-btn-outline" style="padding:6px 12px;font-size:12px;"
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
                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-rotate-left"></i> Refund Item</div>
                    </div>
                    <div class="sp-card-body">
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
                                           class="form-control sp-search"
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
                                           class="form-control sp-search"
                                           id="refund_amount"
                                           name="refund_amount"
                                           value="{{ number_format((float) $saleItem->unit_price, 2, '.', '') }}"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label for="reason" class="form-label">Reason (optional)</label>
                                    <input type="text" class="form-control sp-search" id="reason" name="reason" maxlength="255" placeholder="e.g. Wrong item / Defective / Customer return">
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notes (optional)</label>
                                    <textarea class="form-control sp-search" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Additional details..."></textarea>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="sp-btn sp-btn-primary">
                                            <i class="fas fa-undo me-2"></i>Process Refund
                                        </button>
                                        <a href="{{ route('admin.refunds.create', ['sale_id' => $sale->id]) }}" class="sp-btn sp-btn-outline">
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
        </main>
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

