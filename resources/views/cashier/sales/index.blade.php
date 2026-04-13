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
                                            <a href="{{ route('cashier.sales.show', $sale) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
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

<!-- Refund Modal -->
<div class="modal fade" id="cashierRefundModal" tabindex="-1" aria-labelledby="cashierRefundModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashierRefundModalLabel"><i class="fas fa-undo-alt me-2"></i>Return / Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="crRefundLoading" class="text-center py-4">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2">Loading sale items...</p>
                </div>
                <div id="crRefundContent" style="display:none;">
                    <div id="crRefundSaleInfo" class="mb-3 p-3 rounded" style="background:rgba(13,71,161,0.05);border:1px solid rgba(25,118,210,0.12);font-size:13px;"></div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#6b84aa;">Return Type</label>
                        <div class="d-flex gap-2">
                            <button type="button" id="crTypeCashBtn" onclick="crSetRefundType('cash')" class="btn btn-primary btn-sm">
                                <i class="fas fa-money-bill-wave me-1"></i> Cash Refund
                            </button>
                            <button type="button" id="crTypeReplaceBtn" onclick="crSetRefundType('replacement')" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-exchange-alt me-1"></i> Item Replacement
                            </button>
                        </div>
                    </div>

                    <h6 class="mb-2" style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#6b84aa;">Select Item to Return</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" id="crRefundItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Sold Qty</th>
                                    <th>Already Refunded</th>
                                    <th>Available</th>
                                    <th>Unit Price</th>
                                    <th>Return Qty</th>
                                    <th id="crRefundAmountHeader">Refund Amount</th>
                                </tr>
                            </thead>
                            <tbody id="crRefundItemsBody"></tbody>
                        </table>
                    </div>

                    <div id="crReplacementItemRow" class="mt-3 p-3 rounded" style="background:rgba(16,185,129,0.06);border:1px solid rgba(16,185,129,0.2);display:none;">
                        <label class="form-label fw-bold" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#6b84aa;">Replacement Item Description</label>
                        <input type="text" id="crReplacementItem" class="form-control" placeholder="e.g. Same product (new unit), Different color..." maxlength="255">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i>No money is returned — item is swapped for a replacement.</small>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-bold" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#6b84aa;">Reason</label>
                        <input type="text" id="crRefundReason" class="form-control" placeholder="e.g. Defective, Wrong item..." maxlength="255">
                    </div>
                    <div class="mt-2">
                        <label class="form-label fw-bold" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#6b84aa;">Notes (optional)</label>
                        <textarea id="crRefundNotes" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                    </div>
                    <div id="crRefundTotalRow" class="mt-3 p-3 rounded text-end" style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.15);display:none;">
                        <strong>Total Refund Amount: <span id="crRefundTotalDisplay" style="color:#ef4444;">₱0.00</span></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="crConfirmRefundBtn" onclick="crSubmitRefund()" disabled>
                    <i class="fas fa-undo-alt me-1"></i> <span id="crConfirmRefundBtnLabel">Process Refund</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let crRefundSaleId = null;
let crRefundItems = [];
let crRefundType = 'cash';

function crSetRefundType(type) {
    crRefundType = type;
    const cashBtn = document.getElementById('crTypeCashBtn');
    const replaceBtn = document.getElementById('crTypeReplaceBtn');
    const replacementRow = document.getElementById('crReplacementItemRow');
    const amountHeader = document.getElementById('crRefundAmountHeader');
    const confirmLabel = document.getElementById('crConfirmRefundBtnLabel');

    if (type === 'replacement') {
        cashBtn.className = 'btn btn-outline-secondary btn-sm';
        replaceBtn.className = 'btn btn-primary btn-sm';
        replacementRow.style.display = 'block';
        amountHeader.textContent = 'Value';
        confirmLabel.textContent = 'Process Replacement';
    } else {
        cashBtn.className = 'btn btn-primary btn-sm';
        replaceBtn.className = 'btn btn-outline-secondary btn-sm';
        replacementRow.style.display = 'none';
        amountHeader.textContent = 'Refund Amount';
        confirmLabel.textContent = 'Process Refund';
    }
    crUpdateRefundTotal();
}

function openRefundModal(button) {
    const saleId = button.getAttribute('data-sale-id');
    const paymentMethod = button.getAttribute('data-payment-method');
    const saleDateStr = button.getAttribute('data-sale-date');

    if (paymentMethod === 'credit') {
        Swal.fire({ icon: 'error', title: 'Refund Not Allowed', text: 'Refund/Return is not allowed for credit sales.', confirmButtonColor: '#ef4444' });
        return;
    }

    const saleDate = new Date(saleDateStr + 'T00:00:00');
    const diffDays = (new Date() - saleDate) / (1000 * 60 * 60 * 24);
    if (diffDays > 3) {
        Swal.fire({ icon: 'error', title: 'Refund Window Expired', text: 'Refund/Return is only allowed within 3 days from the purchase date.', confirmButtonColor: '#ef4444' });
        return;
    }

    crRefundSaleId = saleId;
    crRefundItems = [];
    crRefundType = 'cash';

    document.getElementById('crRefundLoading').style.display = 'block';
    document.getElementById('crRefundContent').style.display = 'none';
    document.getElementById('crConfirmRefundBtn').disabled = true;
    document.getElementById('crRefundTotalRow').style.display = 'none';
    document.getElementById('crRefundReason').value = '';
    document.getElementById('crRefundNotes').value = '';
    document.getElementById('crReplacementItem').value = '';
    document.getElementById('crReplacementItemRow').style.display = 'none';
    document.getElementById('crTypeCashBtn').className = 'btn btn-primary btn-sm';
    document.getElementById('crTypeReplaceBtn').className = 'btn btn-outline-secondary btn-sm';
    document.getElementById('crConfirmRefundBtnLabel').textContent = 'Process Refund';
    document.getElementById('crRefundAmountHeader').textContent = 'Refund Amount';

    const modal = new bootstrap.Modal(document.getElementById('cashierRefundModal'));
    modal.show();

    fetch(`/cashier/sales/${saleId}/items`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('crRefundLoading').style.display = 'none';
        document.getElementById('crRefundContent').style.display = 'block';

        const sale = data.sale || {};
        document.getElementById('crRefundSaleInfo').innerHTML =
            `<strong>Sale #${sale.id}</strong> &nbsp;·&nbsp; Ref: ${sale.reference_number || '—'} &nbsp;·&nbsp; Total: ₱${parseFloat(sale.total_amount || 0).toFixed(2)} &nbsp;·&nbsp; ${sale.cashier_name || ''}`;

        const tbody = document.getElementById('crRefundItemsBody');
        tbody.innerHTML = '';
        crRefundItems = data.items || [];

        if (crRefundItems.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No items found.</td></tr>';
            return;
        }

        crRefundItems.forEach((item, idx) => {
            const avail = item.available_for_refund || 0;
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>${item.refunded || 0}</td>
                <td><strong>${avail}</strong></td>
                <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm cr-refund-qty-input"
                        data-idx="${idx}" min="0" max="${avail}" value="0"
                        style="width:75px;" ${avail <= 0 ? 'disabled' : ''}>
                </td>
                <td class="cr-refund-item-amount" data-idx="${idx}">₱0.00</td>
            `;
            tbody.appendChild(row);
        });

        tbody.addEventListener('input', function(e) {
            if (e.target.classList.contains('cr-refund-qty-input')) {
                crUpdateRefundTotal();
            }
        });
    })
    .catch(() => {
        document.getElementById('crRefundLoading').style.display = 'none';
        document.getElementById('crRefundContent').innerHTML = '<div class="alert alert-danger">Error loading sale items.</div>';
        document.getElementById('crRefundContent').style.display = 'block';
    });
}

function crUpdateRefundTotal() {
    let total = 0;
    document.querySelectorAll('.cr-refund-qty-input').forEach(input => {
        const idx = parseInt(input.dataset.idx);
        const maxVal = parseInt(input.max);
        const qty = Math.min(Math.max(0, parseInt(input.value) || 0), isNaN(maxVal) ? Infinity : maxVal);
        input.value = qty;
        const amount = qty * parseFloat(crRefundItems[idx].unit_price);
        total += amount;
        const amountCell = document.querySelector(`.cr-refund-item-amount[data-idx="${idx}"]`);
        if (amountCell) {
            amountCell.textContent = crRefundType === 'replacement' ? qty + ' unit(s)' : '₱' + amount.toFixed(2);
        }
    });

    const totalRow = document.getElementById('crRefundTotalRow');
    const confirmBtn = document.getElementById('crConfirmRefundBtn');
    const hasQty = [...document.querySelectorAll('.cr-refund-qty-input')].some(i => parseInt(i.value) > 0);

    const display = document.getElementById('crRefundTotalDisplay');
    if (crRefundType === 'replacement') {
        display.textContent = 'Item swap — no cash returned';
        display.style.color = '#10b981';
        totalRow.style.background = 'rgba(16,185,129,0.06)';
        totalRow.style.border = '1px solid rgba(16,185,129,0.2)';
    } else {
        display.textContent = '₱' + total.toFixed(2);
        display.style.color = '#ef4444';
        totalRow.style.background = 'rgba(239,68,68,0.06)';
        totalRow.style.border = '1px solid rgba(239,68,68,0.15)';
    }

    totalRow.style.display = hasQty ? 'block' : 'none';
    confirmBtn.disabled = !hasQty;
}

function crSubmitRefund() {
    const reason = document.getElementById('crRefundReason').value.trim();
    const notes = document.getElementById('crRefundNotes').value.trim();
    const replacementItem = document.getElementById('crReplacementItem').value.trim();

    if (crRefundType === 'replacement' && !replacementItem) {
        Swal.fire('Missing Info', 'Please describe the replacement item.', 'warning');
        return;
    }

    const refundLines = [];
    document.querySelectorAll('.cr-refund-qty-input').forEach(input => {
        const qty = parseInt(input.value) || 0;
        if (qty > 0) {
            const idx = parseInt(input.dataset.idx);
            const item = crRefundItems[idx];
            refundLines.push({
                sale_item_id:      item.id,
                product_id:        item.product_id,
                quantity_refunded: qty,
                refund_amount:     crRefundType === 'replacement' ? '0.00' : (qty * parseFloat(item.unit_price)).toFixed(2),
            });
        }
    });

    if (refundLines.length === 0) {
        Swal.fire('No items selected', 'Please enter a quantity to return.', 'warning');
        return;
    }

    const confirmBtn = document.getElementById('crConfirmRefundBtn');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

    const notesWithType = crRefundType === 'replacement'
        ? `[REPLACEMENT] Replacement: ${replacementItem}${notes ? ' | ' + notes : ''}`
        : notes;

    const csrfToken = '{{ csrf_token() }}';

    Promise.all(refundLines.map(line =>
        fetch('{{ route("cashier.refunds.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({
                sale_id:           crRefundSaleId,
                sale_item_id:      line.sale_item_id,
                product_id:        line.product_id,
                quantity_refunded: line.quantity_refunded,
                refund_amount:     line.refund_amount,
                refund_type:       crRefundType,
                reason:            reason || null,
                notes:             notesWithType || null,
            }),
        }).then(r => r.json())
    )).then(results => {
        const failed = results.filter(r => !r.success);
        bootstrap.Modal.getInstance(document.getElementById('cashierRefundModal'))?.hide();

        if (failed.length === 0) {
            Swal.fire({ icon: 'success', title: 'Refund Processed', text: 'The return/refund has been processed successfully.', confirmButtonColor: '#10b981' })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Some Refunds Failed', html: failed.map(r => r.message).join('<br>'), confirmButtonColor: '#ef4444' });
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-undo-alt me-1"></i> <span id="crConfirmRefundBtnLabel">Process Refund</span>';
        }
    }).catch(() => {
        Swal.fire({ icon: 'error', title: 'Network Error', text: 'Failed to process refund. Please try again.', confirmButtonColor: '#ef4444' });
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="fas fa-undo-alt me-1"></i> <span id="crConfirmRefundBtnLabel">Process Refund</span>';
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
