@extends('layouts.app')
@section('title', 'Sales History')

@section('content')
<style>
    :root {
        --navy:      #0D47A1;
        --navy-mid:  #1565C0;
        --blue:      #1976D2;
        --blue-lt:   #42A5F5;
        --cyan:      #00B0FF;
        --cyan-glow: #00E5FF;
        --bg:        #EBF3FB;
        --card:      #ffffff;
        --border:    rgba(25,118,210,0.13);
        --text:      #1a2744;
        --text-muted:#5a7499;
        --shadow-card: 0 4px 28px rgba(13,71,161,0.10);
        --shadow-hover: 0 8px 32px rgba(13,71,161,0.16);
    }

    .sales-page {
        position: relative;
        padding: 0 10px 36px;
        color: var(--text);
        margin-top: -18px;
    }

    .sales-page-bg {
        position: fixed;
        inset: 0;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .sales-page-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 70% 50% at 5%  10%,  rgba(21,101,192,0.13) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 95% 80%,  rgba(0,176,255,0.10)  0%, transparent 55%),
            radial-gradient(ellipse 40% 35% at 60% 0%,   rgba(66,165,245,0.08) 0%, transparent 50%);
    }
    .sales-bg-circle {
        position: absolute;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(21,101,192,0.08), rgba(0,176,255,0.06));
        border: 1px solid rgba(21,101,192,0.07);
    }
    .sales-bg-circle-1 { width:520px; height:520px; top:-180px; left:-160px; }
    .sales-bg-circle-2 { width:380px; height:380px; bottom:-120px; right:-100px; }
    .sales-bg-circle-3 { width:200px; height:200px; top:38%; right:8%; opacity:.6; }
    .sales-bg-stripe {
        position: absolute;
        width: 200%;
        height: 6px;
        background: linear-gradient(90deg, transparent, rgba(0,229,255,0.12), transparent);
        top: 38%;
        left: -50%;
        transform: rotate(-4deg);
    }

    .sales-content {
        position: relative;
        z-index: 1;
        max-width: none;
        width: 100%;
        margin: 0;
    }

    .sales-page .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .sales-page .row {
        margin-top: 0;
    }

    .sales-page .card {
        margin-top: 0;
    }

    .sales-page .card {
        border-radius: 18px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        background: var(--card);
    }

    .sales-page .card-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
        position: relative;
        overflow: hidden;
        border-bottom: 0;
    }
    .sales-page .card-header::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 80% 100% at 90% 50%, rgba(0,229,255,0.18), transparent);
    }
    .sales-page .card-header::after {
        content: '';
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
        top: -100px;
        right: -60px;
    }
    .sales-page .card-header > * {
        position: relative;
        z-index: 1;
    }

    .sales-page .card-title {
        font-size: 18px;
        font-weight: 800;
        color: #fff;
        letter-spacing: 0.01em;
    }

    .sales-page .btn-primary {
        background: linear-gradient(135deg, var(--navy-mid), var(--blue));
        border: none;
        border-radius: 12px;
        padding: 11px 18px;
        font-weight: 800;
        box-shadow: 0 4px 16px rgba(13,71,161,0.3);
        transition: all 0.2s ease;
    }
    .sales-page .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(13,71,161,0.38);
    }

    .sales-page .card-body {
        padding: 0;
    }

    .sales-page .sales-filter-bar {
        padding: 16px 24px;
        background: rgba(13,71,161,0.03);
        border-bottom: 1px solid var(--border);
    }
    .sales-page .sales-filter-bar .form-control {
        border-radius: 10px;
        border: 1.5px solid var(--border);
        height: 40px;
        font-size: 13px;
    }
    .sales-page .sales-filter-bar .form-control:focus {
        border-color: var(--blue-lt);
        box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
    }
    .sales-page .sales-filter-bar .btn-outline-secondary {
        border-radius: 10px;
        border: 1.5px solid var(--border);
        color: var(--text-muted);
        font-weight: 700;
        background: #fff;
        height: 40px;
        transition: all 0.18s ease;
    }
    .sales-page .sales-filter-bar .btn-outline-secondary:hover {
        background: var(--bg);
        border-color: var(--blue-lt);
        color: var(--navy);
    }

    .sales-page .table-responsive {
        padding: 0 0;
        margin: 0;
        overflow-x: hidden;
    }

    .sales-page table.table {
        margin: 0;
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
    }
    .sales-page table.table thead th {
        padding: 13px 18px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.92);
        background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
        border-bottom: 1px solid rgba(255,255,255,0.12);
        white-space: normal;
        word-break: break-word;
    }
    .sales-page table.table tbody td {
        padding: 13px 18px;
        font-size: 13.5px;
        color: var(--text);
        vertical-align: middle;
        border-bottom: 1px solid rgba(13,71,161,0.06);
    }
    .sales-page table.table tbody tr:nth-child(odd) { background: #fff; }
    .sales-page table.table tbody tr:nth-child(even) { background: rgba(235,243,251,0.55); }
    .sales-page table.table tbody tr:hover {
        background: rgba(21,101,192,0.06) !important;
        transform: translateX(2px);
    }

    .sales-page .badge {
        border-radius: 20px;
        font-weight: 800;
        font-size: 11px;
        letter-spacing: 0.02em;
        padding: 5px 11px;
    }

    .sales-page .btn-group.btn-group-sm .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        transition: all 0.18s cubic-bezier(0.34,1.56,0.64,1);
        border-width: 1.5px;
    }
    .sales-page .btn-group.btn-group-sm .btn:hover { transform: scale(1.15); }

    .sales-page .pagination,
    .sales-page .pagination * {
        border-radius: 8px !important;
    }

    .sales-page .sales-pagination-bar {
        padding: 16px 24px;
        background: rgba(13,71,161,0.03);
        border-top: 1px solid var(--border);
    }
</style>

<div class="sales-page">
    <div class="sales-page-bg">
        <div class="sales-bg-circle sales-bg-circle-1"></div>
        <div class="sales-bg-circle sales-bg-circle-2"></div>
        <div class="sales-bg-circle sales-bg-circle-3"></div>
        <div class="sales-bg-stripe"></div>
    </div>

    <div class="sales-content">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Sales History</h4>
                    <a href="{{ route('cashier.sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Sale
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="sales-filter-bar">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from" 
                                   value="{{ request('date_from') }}" placeholder="Date">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
                        </div>
                    </div>
                    </div>
                    <!-- Sales Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        Receipt #
                                    </th>
                                    <th>Date & Time</th>
                                    <th>Customer</th>
                                    <th>
                                        Total Amount
                                    </th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr>
                                    <td>
                                        <strong>#{{ $sale->id }}</strong>
                                        @if($sale->receipt_group_id)
                                            <br><small class="text-muted"><i class="fas fa-link"></i> Group: {{ $sale->receipt_group_id }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $sale->customer_name ?: 'Walk-in' }}</td>
                                    <td class="text-end">
                                        ₱{{ number_format($sale->total_amount, 2) }}
                                        @if($sale->receipt_group_id)
                                            <br><small class="text-muted">Branch {{ $sale->branch->branch_name ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'info' : 'warning') }}">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            // Compute total sold quantity for this sale and total approved refunded quantity
                                            $totalSoldQty = $sale->saleItems->sum('quantity');
                                            $totalRefundedQty = $sale->refunds()->where('status', 'approved')->sum('quantity_refunded');

                                            if ($totalRefundedQty > 0 && $totalRefundedQty >= $totalSoldQty && $totalSoldQty > 0) {
                                                // All quantity refunded
                                                $statusLabel = 'Refunded';
                                                $statusClass = 'bg-warning';
                                            } elseif ($totalRefundedQty > 0 && $totalRefundedQty < $totalSoldQty) {
                                                // Some quantity refunded, but not all
                                                $statusLabel = 'Partially Refunded';
                                                $statusClass = 'bg-warning';
                                            } else {
                                                // No refunds; fall back to original status
                                                $statusLabel = ucfirst($sale->status);
                                                $statusClass = $sale->status == 'completed'
                                                    ? 'bg-success'
                                                    : ($sale->status == 'voided' ? 'bg-danger' : 'bg-secondary');
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('cashier.pos.receipt', $sale) }}" class="btn btn-outline-success" title="Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            @if(strtolower($sale->payment_method) !== 'credit')
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-warning refund-btn"
                                                    title="Refund / Return"
                                                    data-sale-id="{{ $sale->id }}"
                                                    data-payment-method="{{ strtolower($sale->payment_method) }}"
                                                    data-sale-date="{{ $sale->created_at->format('Y-m-d') }}"
                                                    data-products='@json($sale->saleItems->map(function($item){ return ["name" => $item->product->product_name ?? "N/A", "qty" => $item->quantity]; }))'
                                                    onclick="openRefundModal(this)">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5>No sales found</h5>
                                        <p class="text-muted">Start by creating your first sale.</p>
                                        
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($sales->hasPages())
                        <div class="sales-pagination-bar d-flex justify-content-between align-items-center">
                            <div>
                                Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} entries
                            </div>
                            {{ $sales->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openRefundModal(button) {
    const saleId = button.getAttribute('data-sale-id');
    const paymentMethod = button.getAttribute('data-payment-method');
    const saleDateStr = button.getAttribute('data-sale-date');
    const productsJson = button.getAttribute('data-products') || '[]';

    if (paymentMethod === 'credit') {
        Swal.fire({
            icon: 'error',
            title: 'Refund Not Allowed',
            text: 'Refund/Return is not allowed for credit sales.',
            confirmButtonColor: '#ef4444',
        });
        return;
    }

    // Enforce 2-3 day window
    const saleDate = new Date(saleDateStr + 'T00:00:00');
    const today = new Date();
    const diffMs = today - saleDate;
    const diffDays = diffMs / (1000 * 60 * 60 * 24);

    if (diffDays > 3) {
        Swal.fire({
            icon: 'error',
            title: 'Refund Window Expired',
            text: 'Refund/Return is only allowed within 2-3 days from the purchase date.',
            confirmButtonColor: '#ef4444',
        });
        return;
    }

    let products = [];
    try {
        products = JSON.parse(productsJson);
    } catch (e) {
        products = [];
    }

    const productsHtml = products.length
        ? '<ul class="mb-2">' + products.map(p => `<li>${p.name} <span class="text-muted">(x${p.qty})</span></li>`).join('') + '</ul>'
        : '<p class="text-muted mb-2">No product details available.</p>';

    // If there is exactly one product in the sale, allow specifying a quantity to refund
    let quantityInputHtml = '';
    if (products.length === 1) {
        const maxQty = parseInt(products[0].qty || 1, 10) || 1;
        quantityInputHtml = `
            <div class="mb-2">
                <label class="form-label">Quantity to refund (max ${maxQty})</label>
                <input id="refund-qty" type="number" class="swal2-input" min="1" max="${maxQty}" value="1" />
            </div>
        `;
    }

    Swal.fire({
        title: 'Refund / Return',
        html: `
            <div class="text-start">
                <p class="mb-1"><strong>Products in this sale:</strong></p>
                ${productsHtml}
                ${quantityInputHtml}
                <div class="mb-2">
                    <label class="form-label">Reason for refund/return</label>
                    <textarea id="refund-reason" class="swal2-textarea" placeholder="Enter reason..."></textarea>
                </div>
                <p class="text-muted mb-0">Refund/Return is only allowed within 2-3 days from the purchase date.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Continue to Refund',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
        preConfirm: () => {
            const reason = document.getElementById('refund-reason').value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please enter a reason for the refund/return.');
                return false;
            }

            let quantity = null;
            if (products.length === 1) {
                const qtyInput = document.getElementById('refund-qty');
                if (qtyInput) {
                    const maxQty = parseInt(products[0].qty || 1, 10) || 1;
                    const val = parseInt(qtyInput.value, 10);
                    if (isNaN(val) || val < 1 || val > maxQty) {
                        Swal.showValidationMessage(`Please enter a valid quantity between 1 and ${maxQty}.`);
                        return false;
                    }
                    quantity = val;
                }
            }

            return { reason, quantity };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Call quick refund endpoint to perform full refund via AJAX
            fetch(`/cashier/sales/${saleId}/quick-refund`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: result.value.reason, quantity: result.value.quantity ?? null })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Refund Processed',
                        html: `<p>The sale has been fully refunded.</p><p class="mb-0"><strong>Status:</strong> ${data.status?.toUpperCase?.() || 'REFUNDED'}</p>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Refund Failed',
                        text: data.message || 'An error occurred while processing the refund.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Quick refund error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'An error occurred while processing the refund. Please try again.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function clearFilters() {
    window.location.href = '{{ route('cashier.sales.index') }}';
}

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

// Filter change handlers
document.querySelectorAll('select[name], input[name]').forEach(input => {
    if(input.type !== 'text') {
        input.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            if(this.value) {
                params.set(this.name, this.value);
            } else {
                params.delete(this.name);
            }
            window.location.href = '{{ route('cashier.sales.index') }}?' + params.toString();
        });
    }
});
</script>
@endsection
