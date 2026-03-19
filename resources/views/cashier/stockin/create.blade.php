@extends('layouts.app')
@section('title', 'Add Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v={{ rand(1000, 9999) }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.13);
            --text:    #1a2744;
            --muted:   #6b84aa;
            --red:     #ef4444;
            --green:   #10b981;
            --amber:   #f59e0b;
            --shadow:  0 4px 28px rgba(13,71,161,0.09);
            --theme-color: var(--navy);
        }

        .stockin-create-theme {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .stockin-create-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .stockin-create-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .stockin-create-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .stockin-create-theme .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .stockin-create-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .stockin-create-theme .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .stockin-create-theme .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .stockin-create-theme .ph-left { display: flex; align-items: center; gap: 13px; }
        .stockin-create-theme .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
            flex-shrink: 0;
        }
        .stockin-create-theme .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .stockin-create-theme .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .stockin-create-theme .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .stockin-create-theme .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .stockin-create-theme .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }
        .stockin-create-theme .btn-outline-secondary {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
            border: 1px solid rgba(25,118,210,0.20);
        }

        .stockin-create-theme .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .stockin-create-theme .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .stockin-create-theme .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .stockin-create-theme .c-head::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -90px;
            right: -50px;
            pointer-events: none;
        }
        .stockin-create-theme .c-head-title {
            font-family:'Nunito',sans-serif;
            font-size:14.5px;
            font-weight:800;
            color:#fff;
            display:flex;
            align-items:center;
            gap:8px;
            position:relative;
            z-index:1;
        }
        .stockin-create-theme .c-head-title i { color:rgba(0,229,255,.85); }
        .stockin-create-theme .card-body-pad { padding: 18px 22px 22px; }

        .stockin-create-theme label.form-label {
            font-size: 11px;
            letter-spacing: .10em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
        }
        .stockin-create-theme .form-control,
        .stockin-create-theme .form-select {
            border-radius: 12px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            padding: 10px 12px;
            background: #fff;
            color: var(--text);
            transition: border-color .18s, box-shadow .18s;
        }
        .stockin-create-theme .form-control:focus,
        .stockin-create-theme .form-select:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }

        .stockin-create-theme .table-responsive { overflow-x: hidden; }
        .stockin-create-theme table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .stockin-create-theme table.table thead th {
            padding: 12px 14px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: normal;
            word-break: break-word;
        }
        .stockin-create-theme table.table thead th:last-child,
        .stockin-create-theme table.table td:last-child {
            width: 190px;
            white-space: nowrap;
        }
        .stockin-create-theme table.table td {
            padding: 12px 14px;
            font-size: 13px;
            vertical-align: middle;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }

        .stockin-create-theme .add-branch {
            white-space: nowrap;
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            font-family: 'Nunito', sans-serif;
        }

        .stockin-create-theme .remove-branch {
            white-space: nowrap;
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            font-family: 'Nunito', sans-serif;
        }

        .stockin-create-theme .actions-cell {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-start;
            flex-wrap: nowrap;
        }

        .stockin-create-theme #purchase-products-panel {
            border: 1px solid rgba(25,118,210,0.18) !important;
            border-radius: 16px !important;
            box-shadow: 0 18px 44px rgba(13,71,161,0.16) !important;
        }

        .card-rounded{ border-radius: 12px; }
    </style>
@endpush

@section('content')
<div class="stockin-create-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-boxes-stacked"></i></div>
                <div>
                    <div class="ph-title">Add Stock In</div>
                    <div class="ph-sub">Select purchase and stock items into branches</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.stockin.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-pen-to-square"></i> Stock In Form</div>
            </div>

            <div class="card-body-pad">
                <form action="{{ route('cashier.stockin.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_id" class="form-label">Purchase Reference</label>
                                        <select name="purchase_id" id="purchase_id" class="form-select" required>
                                            <option value="">-- Select Purchase Reference --</option>
                                            @foreach($purchases as $purchase)
                                                <option value="{{ $purchase->id }}" data-supplier-name="{{ $purchase->supplier_name ?? '' }}" data-reference-number="{{ $purchase->reference_number ?? '' }}" data-purchase-date="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('m/d/Y') : '' }}" data-remaining-quantity="{{ $purchase->remaining_quantity ?? '' }}">
                                                    {{ $purchase->supplier_name ?? 'N/A' }}
                                                    | {{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('m/d/Y') : 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3 d-flex align-items-end">
                                        <div class="w-100">
                                            <div class="d-flex gap-3 flex-wrap">
                                                <div>
                                                    <div class="small text-muted">Supplier</div>
                                                    <div class="fw-semibold" id="purchase-supplier-label">-</div>
                                                </div>
                                                <div>
                                                    <div class="small text-muted">Date</div>
                                                    <div class="fw-semibold" id="purchase-date-label">-</div>
                                                </div>
                                                <div>
                                                    <div class="small text-muted">Reference #</div>
                                                    <div class="fw-semibold" id="purchase-ref-label">-</div>
                                                </div>
                                                <div>
                                                </div>
                                                <div class="ms-auto">
                                                    <div class="position-relative">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" id="purchase-products-dropdown-btn" disabled>
                                                            Purchased Products
                                                        </button>
                                                        <div id="purchase-products-panel" class="border rounded bg-white shadow-sm p-3" style="display:none; position:absolute; right:0; top: calc(100% + 6px); min-width: 320px; max-height: 320px; overflow:auto; z-index: 1100;">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div class="fw-semibold">Select Products</div>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" id="select-all-products-btn">Select All</button>
                                                            </div>
                                                            <div id="purchase-products-checkboxes" class="d-flex flex-column gap-2"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                 
                                </div>

                                <div id="purchase-items-form-container" class="mb-3" style="display: none;">
                                    <h5 class="mt-4">Items to Stock In</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Purchased Qty</th>
                                                    <th>Remaining to Stock</th>
                                                    <th>Unit Type</th>
                                                    <th>Branch</th>
                                                    <th>Stock-In Qty</th>
                                                    <th>Original Price</th>
                                                    <th>New Price</th>
                                                    <th style="width: 190px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase-items-table-body"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Save Stock</button>
                            </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Success!',
                    text: {!! json_encode(session('success')) !!},
                    icon: 'success',
                    confirmButtonColor: 'var(--theme-color)',
                });
            }
        @endif

        var cashierBranchId = @json(optional(Auth::user())->branch_id);

        var purchaseSelect = document.getElementById('purchase_id');
        var tableBody = document.getElementById('purchase-items-table-body');
        var container = document.getElementById('purchase-items-form-container');
        var form = document.querySelector('form[action*="stockin"]');
        var stockInRowIndex = 0; // global index for items[] rows

        var supplierLabel = document.getElementById('purchase-supplier-label');
        var dateLabel = document.getElementById('purchase-date-label');
        var refLabel = document.getElementById('purchase-ref-label');
        var remainingLabel = document.getElementById('purchase-remaining-label');
        var productsDropdownBtn = document.getElementById('purchase-products-dropdown-btn');
        var productsPanel = document.getElementById('purchase-products-panel');
        var productsCheckboxes = document.getElementById('purchase-products-checkboxes');
        var selectAllBtn = document.getElementById('select-all-products-btn');

        function setProductsPanelOpen(open) {
            if (!productsPanel) return;
            productsPanel.style.display = open ? 'block' : 'none';
        }

        function isProductsPanelOpen() {
            if (!productsPanel) return false;
            return productsPanel.style.display !== 'none';
        }

        var latestPurchaseItems = [];

        function escapeHtml(s) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(s == null ? '' : String(s)));
            return div.innerHTML;
        }

        function setPurchaseLabelsFromOption(opt) {
            // Update labels only if their elements exist (remaining may be removed).
            if (!opt) {
                if (supplierLabel) supplierLabel.textContent = '-';
                if (dateLabel) dateLabel.textContent = '-';
                if (refLabel) refLabel.textContent = '-';
                if (remainingLabel) remainingLabel.textContent = '-';
                return;
            }

            if (supplierLabel) supplierLabel.textContent = opt.dataset.supplierName || '-';
            if (dateLabel) dateLabel.textContent = opt.dataset.purchaseDate || '-';
            if (refLabel) refLabel.textContent = opt.dataset.referenceNumber || '-';
            if (remainingLabel) {
                remainingLabel.textContent = (opt.dataset.remainingQuantity ? (opt.dataset.remainingQuantity + ' remaining') : '-');
            }
        }

        function renderProductCheckboxes(items) {
            if (!productsCheckboxes) return;
            productsCheckboxes.innerHTML = '';

            if (!items || items.length === 0) {
                productsCheckboxes.innerHTML = '<div class="text-muted small">No products found.</div>';
                return;
            }

            items.forEach(function(item) {
                var name = item.product ? item.product.product_name : 'N/A';
                var id = item.product_id;
                var checkboxId = 'purchase-product-checkbox-' + id;
                var remaining = parseFloat(item.quantity || 0);
                var disabled = remaining <= 0;
                var row = document.createElement('div');
                row.className = 'form-check';
                row.innerHTML =
                    '<input class="form-check-input purchase-product-checkbox" type="checkbox" value="' + escapeHtml(id) + '" id="' + escapeHtml(checkboxId) + '" ' + (disabled ? 'disabled' : '') + '>' +
                    '<label class="form-check-label" for="' + escapeHtml(checkboxId) + '">' + escapeHtml(name) + (disabled ? ' (Fully Stocked)' : '') + '</label>';
                productsCheckboxes.appendChild(row);
            });
        }

        function getSelectedProductIds() {
            if (!productsCheckboxes) return [];
            var checked = productsCheckboxes.querySelectorAll('.purchase-product-checkbox:checked');
            return Array.prototype.slice.call(checked).map(function(cb) { return String(cb.value); });
        }

        function renderStockInTableFromSelection() {
            tableBody.innerHTML = '';
            stockInRowIndex = 0;

            var selected = getSelectedProductIds();
            if (!selected || selected.length === 0) {
                container.style.display = 'none';
                return;
            }

            var cashierBranchName = @json($cashierBranchName ?? '');

            latestPurchaseItems
                .filter(function(item) { return selected.indexOf(String(item.product_id)) !== -1; })
                .forEach(function(item) {
                    var productName = item.product ? item.product.product_name : 'N/A';
                    var purchasedQty = item.quantity || 0;
                    var purchasedQtyDisplay = purchasedQty;
                    if (item.unit_type && item.unit_type.unit_name) {
                        purchasedQtyDisplay += ' ' + item.unit_type.unit_name;
                    }

                    var unitTypeOptions = '<option value="">No unit types</option>';
                    if (item.unit_types && item.unit_types.length > 0) {
                        unitTypeOptions = item.unit_types.map(function(ut) {
                            return '<option value="' + ut.id + '">' + ut.unit_name + '</option>';
                        }).join('');
                    }

                    var idx = stockInRowIndex++;
                    var rowHtml =
                        '<tr>' +
                            '<td>' +
                                escapeHtml(productName) +
                                '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">' +
                            '</td>' +
                            '<td>' + escapeHtml(purchasedQtyDisplay) + '</td>' +
                            '<td>' +
                                '<span class="fw-semibold js-remaining-to-stock" data-product-id="' + item.product_id + '" data-original-remaining="' + purchasedQty + '">' + escapeHtml(String(purchasedQty)) + '</span>' +
                            '</td>' +
                            '<td>' +
                                '<select class="form-select" name="items[' + idx + '][unit_type_id]">' +
                                    unitTypeOptions +
                                '</select>' +
                            '</td>' +
                            '<td>' +
                                '<span class="form-control" style="background: rgba(240,246,255,0.55);">' + escapeHtml(cashierBranchName || 'Branch') + '</span>' +
                                '<input type="hidden" name="items[' + idx + '][branch_id]" value="' + (cashierBranchId ? String(cashierBranchId) : '') + '">' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" class="form-control js-stockin-qty" data-product-id="' + item.product_id + '" data-original-remaining="' + purchasedQty + '" name="items[' + idx + '][quantity]" min="0" value="0">' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" class="form-control" name="items[' + idx + '][original_price]" min="0" step="0.01" value="' + (item.unit_price || '0.00') + '" readonly>' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" class="form-control" name="items[' + idx + '][new_price]" min="0" step="0.01" value="0.00" required>' +
                            '</td>' +
                            '<td>' +
                                '<div class="actions-cell">' +
                                    '<button type="button" class="btn btn-sm btn-outline-secondary add-branch" data-product-id="' + item.product_id + '">Add Stock</button>' +
                                    '<button type="button" class="btn btn-sm btn-outline-danger remove-branch" data-product-id="' + item.product_id + '">Remove</button>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';

                    tableBody.insertAdjacentHTML('beforeend', rowHtml);
                });

            container.style.display = '';

            updateRemainingToStockAll();
        }

        function updateRemainingToStockAll() {
            // Keep the remaining display fixed to the original remaining value.
            // Validation/capping is handled in the qty input handler.
            if (!tableBody) return;
            var remainingEls = tableBody.querySelectorAll('.js-remaining-to-stock');
            remainingEls.forEach(function(el) {
                var original = parseFloat(el.dataset.originalRemaining || '0') || 0;
                el.textContent = String(Number.isFinite(original) ? original : 0);
                el.classList.remove('text-danger');
            });
        }

        if (purchaseSelect) {
            purchaseSelect.addEventListener('change', async function () {
                var purchaseId = purchaseSelect.value;
                tableBody.innerHTML = '';

                latestPurchaseItems = [];
                if (productsCheckboxes) productsCheckboxes.innerHTML = '';
                if (productsDropdownBtn) {
                    productsDropdownBtn.disabled = true;
                }

                var selectedOpt = purchaseSelect.options[purchaseSelect.selectedIndex];
                setPurchaseLabelsFromOption(selectedOpt && selectedOpt.value ? selectedOpt : null);

                if (!purchaseId) {
                    container.style.display = 'none';
                    return;
                }

                try {
                    var res = await fetch('{{ url('cashier/stockin/products-by-purchase') }}/' + purchaseId, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    if (!res.ok) {
                        throw new Error('HTTP error! status: ' + res.status);
                    }
                    
                    var data = await res.json();

                    if (data.items && data.items.length > 0) {
                        latestPurchaseItems = data.items;
                        renderProductCheckboxes(latestPurchaseItems);
                        if (productsDropdownBtn) {
                            productsDropdownBtn.disabled = false;
                        }

                        if (productsCheckboxes) {
                            var allCbs = productsCheckboxes.querySelectorAll('.purchase-product-checkbox');
                            allCbs.forEach(function(cb) { if (!cb.disabled) cb.checked = true; });
                        }
                        renderStockInTableFromSelection();
                    } else {
                        container.style.display = 'none';
                    }
                } catch (e) {
                    console.error('Fetch error:', e);
                    container.style.display = 'none';
                }
            });
        }

        if (productsDropdownBtn) {
            productsDropdownBtn.addEventListener('click', function(e) {
                if (productsDropdownBtn.disabled) {
                    e.preventDefault();
                    return;
                }

                e.preventDefault();
                setProductsPanelOpen(!isProductsPanelOpen());
            });
        }

        document.addEventListener('click', function(e) {
            if (!productsPanel || !productsDropdownBtn) return;
            if (!isProductsPanelOpen()) return;
            if (!productsPanel.contains(e.target) && !productsDropdownBtn.contains(e.target)) {
                setProductsPanelOpen(false);
            }
        });

        if (productsCheckboxes) {
            productsCheckboxes.addEventListener('change', function(e) {
                if (!e.target.classList.contains('purchase-product-checkbox')) return;
                renderStockInTableFromSelection();
            });
        }

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                if (!productsCheckboxes) return;
                var cbs = productsCheckboxes.querySelectorAll('.purchase-product-checkbox');
                cbs.forEach(function(cb) { if (!cb.disabled) cb.checked = true; });
                renderStockInTableFromSelection();
            });
        }

        // Delegated handler for Add Branch button
        if (tableBody) {
            tableBody.addEventListener('click', function(e) {
                if (!e.target.classList.contains('add-branch')) return;

                var currentRow = e.target.closest('tr');
                if (!currentRow) return;

                var newIdx = stockInRowIndex++;
                var clone = currentRow.cloneNode(true);

                // Update all name attributes in the cloned row to use the new index
                var inputs = clone.querySelectorAll('input[name^="items["], select[name^="items["]');
                inputs.forEach(function(el) {
                    var name = el.getAttribute('name');
                    if (!name) return;
                    name = name.replace(/items\[(\d+)\]/, 'items[' + newIdx + ']');
                    el.setAttribute('name', name);

                    // Reset values for branch, quantity, and new_price
                    if (name.endsWith('[branch_id]')) {
                        el.value = cashierBranchId ? String(cashierBranchId) : '';
                    }
                    if (name.endsWith('[quantity]')) {
                        el.value = 0;
                    }
                    if (name.endsWith('[new_price]')) {
                        el.value = '0.00';
                    }
                });

                // Insert cloned row after the current one
                currentRow.parentNode.insertBefore(clone, currentRow.nextSibling);

                updateRemainingToStockAll();
            });
        }

        // Delegated handler for Remove button
        if (tableBody) {
            tableBody.addEventListener('click', function(e) {
                if (!e.target.classList.contains('remove-branch')) return;
                var currentRow = e.target.closest('tr');
                if (!currentRow) return;
                currentRow.remove();
                updateRemainingToStockAll();
            });
        }

        if (tableBody) {
            tableBody.addEventListener('input', function(e) {
                if (!e.target.classList.contains('js-stockin-qty')) return;
                var current = e.target;
                var pid = String(current.dataset.productId || '');
                var original = parseFloat(current.dataset.originalRemaining || '0') || 0;

                // Sum other rows for same product
                var othersTotal = 0;
                var allForProduct = tableBody.querySelectorAll('input.js-stockin-qty[data-product-id="' + pid + '"]');
                allForProduct.forEach(function(input) {
                    if (input === current) return;
                    othersTotal += (parseFloat(input.value || '0') || 0);
                });

                var allowable = original - othersTotal;
                if (allowable < 0) allowable = 0;

                var val = parseFloat(current.value || '0') || 0;
                if (val > allowable) {
                    current.value = allowable;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock-in quantity too high',
                            text: 'You can only stock in up to ' + allowable + ' for this product based on remaining quantity.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }

                updateRemainingToStockAll();
            });
        }

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                var formData = new FormData(form);
                
                try {
                    var response = await fetch('{{ route('cashier.stockin.store') }}', {
                        method: 'POST',
                        body: formData
                    });
                    
                    var result = await response.json();
                    
                    if (result.success) {
                        var inputs = form.querySelectorAll('input[name$="quantity"]');
                        inputs.forEach(function(input) {
                            var currentValue = parseInt(input.value) || 0;
                            if (currentValue > 0) {
                                input.value = currentValue - parseInt(input.value);
                            }
                        });
                        
                        form.reset();
                        container.style.display = 'none';
                        tableBody.innerHTML = '';
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Success: ' + result.message);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Something went wrong',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Error: ' + (result.message || 'Something went wrong'));
                        }
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Error: Something went wrong. Please try again.');
                    }
                }
            });
        }
    });
})();
</script>
@endpush
