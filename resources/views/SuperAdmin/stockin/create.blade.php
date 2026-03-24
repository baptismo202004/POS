@extends('layouts.app')
@section('title', 'Add Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v={{ time() }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v={{ rand(1000, 9999) }}"></script>
    <style>
        :root {
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --cyan:    #00E5FF;
            --green:   #10b981;
            --red:     #ef4444;
            --amber:   #f59e0b;
            --bg:      #EBF3FB;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.12);
            --text:    #1a2744;
            --muted:   #6b84aa;
        }

        /* Background */
        .sp-bg { position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg); }
        .sp-bg::before {
            content:'';position:absolute;inset:0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%,transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%,transparent 55%);
        }
        .sp-blob { position:absolute;border-radius:50%;filter:blur(60px);opacity:.11; }
        .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
        .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
        @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
        @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

        /* Wrap */
        .sp-wrap { position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif; }

        /* Page header */
        .sp-page-head {
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:22px;flex-wrap:wrap;gap:14px;
            animation:spUp .4s ease both;
        }
        .sp-ph-left { display:flex;align-items:center;gap:13px; }
        .sp-ph-icon {
            width:48px;height:48px;border-radius:14px;
            background:linear-gradient(135deg,var(--navy),var(--blue-lt));
            display:flex;align-items:center;justify-content:center;
            font-size:20px;color:#fff;
            box-shadow:0 6px 20px rgba(13,71,161,0.28);
        }
        .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
        .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
        .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
        .sp-ph-actions { display:flex;align-items:center;gap:9px;flex-wrap:wrap; }

        /* Buttons */
        .sp-btn {
            display:inline-flex;align-items:center;gap:7px;
            padding:9px 18px;border-radius:11px;
            font-size:13px;font-weight:700;cursor:pointer;
            font-family:'Nunito',sans-serif;
            border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;
        }
        .sp-btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26); }
        .sp-btn-primary:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }
        .sp-btn-outline { background:var(--card);color:var(--navy);border:1.5px solid var(--border); }
        .sp-btn-outline:hover { background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px); }
        .sp-btn-soft { background:rgba(13,71,161,0.06);color:var(--navy);border:1.5px solid var(--border); }
        .sp-btn-soft:hover { background:rgba(13,71,161,0.12);color:var(--navy); }

        /* Card */
        .sp-card {
            background:var(--card);border-radius:20px;
            border:1px solid var(--border);
            box-shadow:0 4px 28px rgba(13,71,161,0.09);
            overflow:hidden;animation:spUp .45s ease both;
        }
        .sp-card-head {
            padding:15px 22px;
            background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
            display:flex;align-items:center;justify-content:space-between;
            position:relative;overflow:hidden;
        }
        .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
        .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
        .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
        .sp-card-head-title i { color:rgba(0,229,255,.85); }
        .sp-card-body { padding: 22px; }

        /* Form */
        .sp-form .form-label { font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;font-family:'Nunito',sans-serif; }
        .sp-form .form-control,
        .sp-form .form-select {
            border-radius:11px;
            border:1.5px solid var(--border);
            padding:10px 14px;
            font-size:13.5px;
            background:#fafcff;
            color:var(--text);
            font-family:'Plus Jakarta Sans',sans-serif;
            box-shadow:none;
            transition:border-color .18s, box-shadow .18s;
        }
        .sp-form .form-control:focus,
        .sp-form .form-select:focus {
            border-color:var(--blue-lt);
            box-shadow:0 0 0 3px rgba(66,165,245,0.12);
            background:#fff;
        }

        /* Small info blocks (purchase labels) */
        .sp-mini-label { font-size:11px;color:var(--muted);font-weight:700;font-family:'Nunito',sans-serif;text-transform:uppercase;letter-spacing:.06em; }
        .sp-mini-value { font-size:13px;color:var(--text);font-weight:800;font-family:'Nunito',sans-serif; }

        /* Purchased products panel */
        #purchase-products-panel { border:1px solid var(--border) !important; border-radius:14px !important; background:rgba(255,255,255,0.98) !important; }

        /* Stockin table container */
        .stockin-table-scroll { border-radius:16px; border:1px solid var(--border); }

        /* Keep existing rounded helper */
        .card-rounded{ border-radius: 12px; }

        .stockin-active-row {
            background-color: #fff8e1 !important;
        }

        .stockin-active-row > td {
            background-color: transparent !important;
        }

        .stockin-table-scroll {
            max-height: 60vh;
            overflow: auto;
        }

        .stockin-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: #fff;
        }

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
@endpush

@section('content')
<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <main class="flex-fill p-4" style="position:relative;z-index:1;">
        <div class="sp-wrap">
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-boxes-stacked"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Inventory</div>
                        <div class="sp-ph-title">Add Stock In</div>
                        <div class="sp-ph-sub">Stock items from purchases into branches</div>
                    </div>
                </div>
                <div class="sp-ph-actions">
                    <a href="{{ route('superadmin.stockin.index') }}" class="sp-btn sp-btn-outline">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-receipt"></i> Stock In Form</div>
                </div>

                <div class="sp-card-body sp-form">

                <form action="{{ route('superadmin.stockin.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_id" class="form-label">Purchase Reference</label>
                                        <select name="purchase_id" id="purchase_id" class="form-select" required>
                                            <option value="">-- Select Purchase Reference --</option>
                                            @foreach($purchases as $purchase)
                                                <option value="{{ $purchase->id }}" data-supplier-name="{{ $purchase->supplier_name ?? '' }}" data-reference-number="{{ $purchase->reference_number ?? '' }}" data-purchase-date="{{ $purchase->purchase_date ? \Carbon\Carbon::parse($purchase->purchase_date)->format('m/d/Y') : '' }}" data-remaining-quantity="{{ $purchase->remaining_quantity }}">
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
                                                    <div class="sp-mini-label">Supplier</div>
                                                    <div class="sp-mini-value" id="purchase-supplier-label">-</div>
                                                </div>
                                                <div>
                                                    <div class="sp-mini-label">Date</div>
                                                    <div class="sp-mini-value" id="purchase-date-label">-</div>
                                                </div>
                                                <div>
                                                    <div class="sp-mini-label">Reference #</div>
                                                    <div class="sp-mini-value" id="purchase-ref-label">-</div>
                                                </div>
                                                <div>
                                                    <div class="sp-mini-label">Available to Stock In</div>
                                                    <div class="sp-mini-value" id="purchase-remaining-label">-</div>
                                                </div>
                                                <div class="ms-auto">
                                                    <div class="position-relative">
                                                        <button class="sp-btn sp-btn-soft" type="button" id="purchase-products-dropdown-btn" disabled>
                                                            Purchased Products
                                                        </button>
                                                        <div id="purchase-products-panel" class="border rounded bg-white shadow-sm p-3" style="display:none; position:absolute; right:0; top: calc(100% + 6px); min-width: 320px; max-height: 320px; overflow:auto; z-index: 1100;">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <div class="fw-semibold">Select Products</div>
                                                                <button type="button" class="sp-btn sp-btn-outline" id="select-all-products-btn">Select All</button>
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
                                    <div class="table-responsive stockin-table-scroll">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Purchased Qty</th>
                                                    <th>Original Price</th>
                                                    <th>Branch</th>
                                                    <th>Stock Price and Quantity</th>
                                                    <th style="width: 80px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase-items-table-body"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <button type="submit" class="sp-btn sp-btn-primary">
                                    <i class="fas fa-save"></i> Save Stock
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
        </div>
    </main>
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

        var purchaseSelect = document.getElementById('purchase_id');
        var tableBody = document.getElementById('purchase-items-table-body') || document.getElementById('stockin-items-tbody');
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

        function updateNewPriceValidityAll() {
            if (!tableBody) return;
            var rows = tableBody.querySelectorAll('tr');
            rows.forEach(function(row) {
                var hidden = row.querySelector('input.js-selected-new-price');
                var originalInput = row.querySelector('input.js-original-price');
                if (!hidden || !originalInput) return;
                var original = parseFloat(originalInput.value || '0') || 0;
                var val = parseFloat(hidden.value || '0') || 0;
                hidden.setCustomValidity('');

                // Clear all per-unit inputs validity first
                var unitPriceInputs = row.querySelectorAll('input.js-new-price-unit');
                unitPriceInputs.forEach(function(inp) {
                    inp.setCustomValidity('');
                });

                // Determine the purchase unit conversion factor for this product row.
                // `original` is the purchase unit cost (purchase_items.unit_cost) in Admin stock-in payload.
                // Convert it to base-unit cost by dividing by the purchase factor.
                var remainingMeta = row.querySelector('.js-remaining-display');
                var purchaseFactor = remainingMeta ? (parseFloat(remainingMeta.dataset.purchaseFactor || '1') || 1) : 1;
                if (!isFinite(purchaseFactor) || purchaseFactor <= 0) purchaseFactor = 1;

                var basePurchaseCost = original / purchaseFactor;
                if (!isFinite(basePurchaseCost) || basePurchaseCost < 0) basePurchaseCost = 0;

                // Validate every unit price input (pc, box, etc.) against purchase baseline.
                unitPriceInputs.forEach(function(inp) {
                    var uId = String(inp.dataset.unitId || '');
                    var factorEl = uId ? row.querySelector('.js-unit-factor[data-unit-id="' + uId + '"]') : null;
                    var factor = factorEl ? (parseFloat(factorEl.dataset.factor || '1') || 1) : 1;
                    if (!isFinite(factor) || factor <= 0) factor = 1;

                    var minAllowed = basePurchaseCost * factor;
                    if (!isFinite(minAllowed) || minAllowed < 0) minAllowed = 0;

                    var entered = parseFloat(inp.value || '0') || 0;
                    if (entered < minAllowed) {
                        var msg = 'New price must be at least ' + minAllowed.toFixed(2) + '.';
                        inp.setCustomValidity(msg);
                    }
                });

                // Apply the same validation to the currently selected unit price (hidden field used by backend).
                var unitIdInput = row.querySelector('input.js-unit-type-id');
                var unitId = unitIdInput ? String(unitIdInput.value || '') : '';
                var selectedPriceInput = unitId ? row.querySelector('input.js-new-price-unit[data-unit-id="' + unitId + '"]') : null;
                if (selectedPriceInput) {
                    var selectedErr = selectedPriceInput.validationMessage;
                    hidden.setCustomValidity(selectedErr || '');
                } else {
                    hidden.setCustomValidity('');
                }
            });
        }

        if (tableBody) {
            tableBody.addEventListener('change', async function(e) {
                if (e.target && e.target.tagName === 'SELECT' && e.target.name && e.target.name.indexOf('[branch_id]') !== -1) {
                    var row = e.target.closest('tr');
                    if (!row) return;
                    var productIdEl = row.querySelector('input[name$="[product_id]"]');
                    var productId = productIdEl ? productIdEl.value : null;
                    var branchId = e.target.value;
                    if (!productId || !branchId) return;

                    // Always reset unit price inputs when switching branches so latest saved prices can apply.
                    resetUnitPriceInputsForBranchChange(row);

                    var data = await fetchLatestUnitPrices(productId, branchId);
                    if (data) {
                        applyLatestPricesToRowAsPlaceholder(row, data);
                    }
                }
            });
        }

        function syncSelectedUnitPricesToHidden() {
            if (!tableBody) return;
            var rows = tableBody.querySelectorAll('tr');
            rows.forEach(function(row) {
                var hidden = row.querySelector('input.js-selected-new-price');
                if (!hidden) return;

                var unitIdInput = row.querySelector('input.js-unit-type-id');
                if (!unitIdInput) return;

                var unitId = String(unitIdInput.value || '');
                var priceInput = row.querySelector('input.js-new-price-unit[data-unit-id="' + unitId + '"]');
                hidden.value = priceInput ? (priceInput.value || '0.00') : '0.00';
            });
        }

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
            if (!supplierLabel || !dateLabel || !refLabel || !remainingLabel) return;
            if (!opt) {
                supplierLabel.textContent = '-';
                dateLabel.textContent = '-';
                refLabel.textContent = '-';
                remainingLabel.textContent = '-';
                return;
            }
            supplierLabel.textContent = opt.dataset.supplierName || '-';
            dateLabel.textContent = opt.dataset.purchaseDate || '-';
            refLabel.textContent = opt.dataset.referenceNumber || '-';
            remainingLabel.textContent = (opt.dataset.remainingQuantity ? (opt.dataset.remainingQuantity + ' remaining') : '-');
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

            function fmtNumber(n) {
                var num = Number(n);
                if (!isFinite(num)) return '0';
                var fixed = num.toFixed(6);
                return fixed.replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1');
            }

            var selected = getSelectedProductIds();
            if (!selected || selected.length === 0) {
                container.style.display = 'none';
                return;
            }

            var branchOptions = @json($branches ? $branches->map(function($branch) {
                return ['id' => $branch->id, 'name' => $branch->branch_name];
            }) : []);

            latestPurchaseItems
                .filter(function(item) { return selected.indexOf(String(item.product_id)) !== -1; })
                .forEach(function(item) {
                    var idx = stockInRowIndex++;
                    var productName = item.product ? item.product.product_name : 'N/A';
                    var purchaseUnitName = item.purchase_unit_name || ((item.unit_type && item.unit_type.unit_name) ? item.unit_type.unit_name : '');
                    var baseUnitName = item.base_unit_name || '';
                    var purchaseFactor = parseFloat(item.purchase_factor || 1) || 1;

                    var purchasedQty = parseFloat(item.purchased_qty != null ? item.purchased_qty : 0) || 0;
                    var purchasedBase = parseFloat(item.purchased_base != null ? item.purchased_base : 0) || 0;
                    var remainingBaseQty = parseFloat(item.remaining_base != null ? item.remaining_base : (item.quantity || 0)) || 0;

                    var purchasedQtyDisplay = fmtNumber(purchasedQty) + (purchaseUnitName ? (' ' + purchaseUnitName) : '');
                    if (baseUnitName) {
                        purchasedQtyDisplay = fmtNumber(purchasedQty) + (purchaseUnitName ? (' ' + purchaseUnitName) : '') + ' / ' + fmtNumber(purchasedBase) + ' ' + baseUnitName;
                    }

                    var remainingPurchaseUnits = Math.floor(remainingBaseQty / (purchaseFactor || 1));
                    if (!isFinite(remainingPurchaseUnits) || remainingPurchaseUnits < 0) remainingPurchaseUnits = 0;
                    var remainingRemainderBase = remainingBaseQty - (remainingPurchaseUnits * (purchaseFactor || 1));
                    if (!isFinite(remainingRemainderBase) || remainingRemainderBase < 0) remainingRemainderBase = 0;

                    var remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '');
                    if (baseUnitName) {
                        remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '') + ' / ' + fmtNumber(remainingBaseQty) + ' ' + baseUnitName;
                    }

                    var conversionSummary = item.conversion_summary || '';

                    var categoryType = item.category_type || 'non_electronic';
                    var showSerialPicker = categoryType === 'electronic_with_serial';

                    var baseUnit = null;
                    var unitTypes = Array.isArray(item.unit_types) ? item.unit_types : [];
                    unitTypes.forEach(function(ut) {
                        if (ut && ut.is_base) baseUnit = ut;
                    });
                    if (!baseUnit && unitTypes.length > 0) {
                        baseUnit = unitTypes[0];
                    }

                    var unitTypesHtml = '<div class="text-muted small">No unit types</div>';
                    if (unitTypes.length > 0) {
                        var defaultUnitId = baseUnit ? baseUnit.id : unitTypes[0].id;

                        unitTypesHtml =
                            '<input type="hidden" class="js-unit-type-id" name="items[' + idx + '][unit_type_id]" value="' + defaultUnitId + '">' +
                            unitTypes.map(function(ut) {
                                    var utName = ut.unit_name || 'Unit';
                                    var factor = parseFloat(ut.conversion_factor || '1') || 1;
                                    return (
                                        '<div class="border rounded p-2 mb-2">' +
                                            '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                                '<div class="fw-semibold">' + escapeHtml(utName) + '</div>' +
                                                '<span class="js-unit-factor d-none" data-unit-id="' + ut.id + '" data-factor="' + factor + '"></span>' +
                                            '</div>' +
                                            '<div class="row g-2">' +
                                                '<div class="col-6">' +
                                                    '<label class="small text-muted mb-1">New Price</label>' +
                                                    '<input type="number" class="form-control form-control-sm js-new-price-unit" data-row-idx="' + idx + '" data-unit-id="' + ut.id + '" data-original-price="' + (item.unit_price || '0.00') + '" name="items[' + idx + '][unit_prices][' + ut.id + ']" min="0" step="0.01" value="0.00" required>' +
                                                '</div>' +
                                                '<div class="col-6">' +
                                                    '<label class="small text-muted mb-1">Stock In</label>' +
                                                    '<input type="number" class="form-control form-control-sm js-stockin-qty-unit" data-product-id="' + item.product_id + '" data-row-idx="' + idx + '" data-unit-id="' + ut.id + '" name="items[' + idx + '][unit_quantities][' + ut.id + ']" min="0" value="0">' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>'
                                    );
                                }).join('') 
                            ;

                        // Hidden field required by backend (new_price). We keep it inside Unit Types column.
                        unitTypesHtml += '<input type="hidden" class="js-selected-new-price" data-row-idx="' + idx + '" name="items[' + idx + '][new_price]" value="0.00">';
                    }

                    var branchSelectOptions = '';
                    if (branchOptions && branchOptions.length > 0) {
                        branchOptions.forEach(function(branch) {
                            branchSelectOptions += '<option value="' + branch.id + '">' + branch.name + '</option>';
                        });
                    }

                    var rowHtml =
                        '<tr>' +
                            '<td>' +
                                '<div class="d-flex align-items-center gap-2">' +
                                    '<div>' + escapeHtml(productName) + '</div>' +
                                    '<span class="js-price-alert text-warning" style="display:none;" title="">' +
                                        '<i class="fas fa-triangle-exclamation"></i>' +
                                    '</span>' +
                                '</div>' +
                                (conversionSummary ? ('<div class="text-muted" style="font-size: 12px;">' + escapeHtml(conversionSummary) + '</div>') : '') +
                                '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">' +
                            '</td>' +
                            '<td>' +
                                '<div>' + escapeHtml(purchasedQtyDisplay) + '</div>' +
                                '<div class="text-muted" style="font-size: 12px;">' +
                                    '<span class="js-remaining-display"'
                                        + ' data-product-id="' + item.product_id + '"'
                                        + ' data-original-remaining="' + remainingBaseQty + '"'
                                        + ' data-purchase-factor="' + purchaseFactor + '"'
                                        + ' data-purchase-unit-name="' + escapeHtml(purchaseUnitName) + '"'
                                        + ' data-base-unit-name="' + escapeHtml(baseUnitName) + '"'
                                    + '>Remaining: ' + escapeHtml(remainingDisplay) + '</span>' +
                                '</div>' +
                                '<span class="fw-semibold js-remaining-to-stock d-none" data-product-id="' + item.product_id + '" data-original-remaining="' + remainingBaseQty + '">' + escapeHtml(String(remainingBaseQty)) + '</span>' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" class="form-control js-original-price" name="items[' + idx + '][original_price]" min="0" step="0.01" value="' + (item.unit_price || '0.00') + '" readonly>' +
                            '</td>' +
                            '<td>' +
                                '<select class="form-select" name="items[' + idx + '][branch_id]" required>' +
                                    '<option value="">-- Select Branch --</option>' +
                                    branchSelectOptions +
                                '</select>' +
                            '</td>' +
                            '<td>' +
                                '<input type="hidden" class="js-stockin-qty-base" data-product-id="' + item.product_id + '" name="items[' + idx + '][quantity]" value="0">' +
                                unitTypesHtml +

                                (showSerialPicker
                                    ? (
                                        '<div class="mt-2">' +
                                            '<button type="button" class="btn btn-sm btn-outline-secondary js-open-serials" data-row-idx="' + idx + '" data-product-id="' + item.product_id + '">Search Serials</button>' +
                                            '<div class="border rounded p-2 mt-2 js-serials-panel" data-loaded="0" data-row-idx="' + idx + '" data-product-id="' + item.product_id + '" style="display:none;">' +
                                                '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                                    '<div class="small">Selected <span class="js-serial-selected-count">0</span> / <span class="js-serial-required-count">0</span> required</div>' +
                                                    '<div class="d-flex gap-2">' +
                                                        '<button type="button" class="btn btn-sm btn-outline-secondary js-serial-select-all">Select All</button>' +
                                                        '<button type="button" class="btn btn-sm btn-outline-secondary js-close-serials">Close</button>' +
                                                    '</div>' +
                                                '</div>' +

                                                '<div class="d-flex gap-2 align-items-center mb-2">' +
                                                    '<input type="text" class="form-control form-control-sm js-serial-search" placeholder="Search serial..." style="max-width: 220px;">' +
                                                '</div>' +
                                                '<div class="small text-muted js-serials-loading">Loading...</div>' +
                                                '<div class="js-serials-list" style="max-height: 180px; overflow:auto;"></div>' +
                                                '<div class="js-serials-hidden-inputs"></div>' +
                                            '</div>' +
                                        '</div>'
                                    )
                                    : ''
                                ) +
                            '</td>' +
                            '<td>' +
                                '<button type="button" class="btn btn-sm btn-outline-secondary add-branch" data-product-id="' + item.product_id + '">Add Branch</button>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger mt-1 js-remove-row">Remove</button>' +
                            '</td>' +
                        '</tr>';

                    tableBody.insertAdjacentHTML('beforeend', rowHtml);

                    var insertedRow = tableBody.querySelector('tr:last-child');
                    if (insertedRow) {
                        insertedRow.dataset.primaryRemaining = String(parseFloat(item.purchased_qty || 0) || 0);
                        insertedRow.dataset.unitUnitName = purchaseUnitName;
                        insertedRow.dataset.baseUnitName = baseUnitName;
                        var remainingEl = insertedRow.querySelector('.js-remaining-to-stock');
                        if (remainingEl) {
                            remainingEl.dataset.originalRemaining = String(remainingBaseQty);
                            remainingEl.dataset.remainingBase = String(remainingBaseQty);
                        }
                    }
                });

            container.style.display = 'block';

            updateRemainingToStockAll();
            syncSelectedUnitPricesToHidden();
            updateNewPriceValidityAll();
        }

        async function fetchPurchaseProductSerials(purchaseId, productId) {
            var baseUrl = '{{ route('superadmin.stockin.purchase-product-serials', ['purchase' => ':purchase', 'product' => ':product']) }}';
            var url = baseUrl.replace(':purchase', purchaseId).replace(':product', productId);
            var res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) return null;
            return await res.json();
        }

        function renderSerialsIntoPanel(panel, idx, serials) {
            var list = panel.querySelector('.js-serials-list');
            var hiddenWrap = panel.querySelector('.js-serials-hidden-inputs');
            var loading = panel.querySelector('.js-serials-loading');
            if (!list || !hiddenWrap) return;

            if (loading) loading.style.display = 'none';
            list.innerHTML = '';
            hiddenWrap.innerHTML = '';

            if (!serials || serials.length === 0) {
                list.innerHTML = '<div class="text-muted small">No available serials for this purchase/product.</div>';
                return;
            }

            serials.forEach(function(s) {
                var row = document.createElement('div');
                row.className = 'form-check';
                row.innerHTML =
                    '<input class="form-check-input js-serial-checkbox" type="checkbox" value="' + escapeHtml(s.id) + '" id="serial-' + idx + '-' + escapeHtml(s.id) + '">' +
                    '<label class="form-check-label" for="serial-' + idx + '-' + escapeHtml(s.id) + '">' + escapeHtml(s.serial_number) + '</label>';
                list.appendChild(row);
            });
        }

        function syncSerialHiddenInputs(panel, idx) {
            var hiddenWrap = panel.querySelector('.js-serials-hidden-inputs');
            if (!hiddenWrap) return;
            hiddenWrap.innerHTML = '';

            var checked = panel.querySelectorAll('.js-serial-checkbox:checked');
            Array.prototype.slice.call(checked).forEach(function(cb) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'items[' + idx + '][serial_ids][]';
                inp.value = cb.value;
                hiddenWrap.appendChild(inp);
            });
        }

        function requiredSerialCountForRow(row) {
            var baseInput = row ? row.querySelector('input.js-stockin-qty-base') : null;
            var baseQty = baseInput ? (parseFloat(baseInput.value || '0') || 0) : 0;
            var required = Math.round(baseQty);
            if (!isFinite(required) || required < 0) required = 0;
            return required;
        }

        function updateSerialIndicatorForRow(row) {
            if (!row) return;
            var panel = row.querySelector('.js-serials-panel');
            if (!panel) return;

            var requiredEl = panel.querySelector('.js-serial-required-count');
            var selectedEl = panel.querySelector('.js-serial-selected-count');
            if (!requiredEl || !selectedEl) return;

            var required = requiredSerialCountForRow(row);
            var selected = panel.querySelectorAll('.js-serial-checkbox:checked').length;

            requiredEl.textContent = String(required);
            selectedEl.textContent = String(selected);

            var baseInput = row.querySelector('input.js-stockin-qty-base');
            if (baseInput) {
                baseInput.setCustomValidity('');
                if (required > 0 && selected !== required) {
                    baseInput.setCustomValidity('Selected serials must match stock-in quantity.');
                }
            }
        }

        if (tableBody) {
            tableBody.addEventListener('click', async function(e) {
                var openBtn = e.target && e.target.closest ? e.target.closest('.js-open-serials') : null;
                if (openBtn) {
                    var idx = openBtn.dataset.rowIdx;
                    var productId = openBtn.dataset.productId;
                    var purchaseId = purchaseSelect ? purchaseSelect.value : '';
                    if (!idx || !productId || !purchaseId) return;

                    var row = openBtn.closest('tr');
                    var panel = tableBody.querySelector('.js-serials-panel[data-row-idx="' + idx + '"][data-product-id="' + productId + '"]');
                    if (!panel) return;

                    panel.style.display = 'block';

                    if (panel.dataset.loaded !== '1') {
                        var payload = await fetchPurchaseProductSerials(purchaseId, productId);
                        var serials = payload && payload.serials ? payload.serials : [];
                        renderSerialsIntoPanel(panel, idx, serials);
                        panel.dataset.loaded = '1';
                    }

                    updateSerialIndicatorForRow(row);

                    return;
                }

                var selectAllBtn = e.target && e.target.closest ? e.target.closest('.js-serial-select-all') : null;
                if (selectAllBtn) {
                    var panel = selectAllBtn.closest('.js-serials-panel');
                    var row = selectAllBtn.closest('tr');
                    if (!panel || !row) return;

                    var idx = panel.dataset.rowIdx;
                    var required = requiredSerialCountForRow(row);
                    var currently = panel.querySelectorAll('.js-serial-checkbox:checked').length;
                    var toSelect = required > 0 ? (required - currently) : 0;
                    if (toSelect <= 0) {
                        updateSerialIndicatorForRow(row);
                        return;
                    }

                    var checkboxes = panel.querySelectorAll('.js-serial-checkbox');
                    for (var i = 0; i < checkboxes.length && toSelect > 0; i++) {
                        var cb = checkboxes[i];
                        if (cb.checked) continue;

                        var wrapper = cb.closest('.form-check');
                        if (wrapper && wrapper.style && wrapper.style.display === 'none') continue;

                        cb.checked = true;
                        toSelect--;
                    }

                    syncSerialHiddenInputs(panel, idx);
                    updateSerialIndicatorForRow(row);
                    return;
                }

                var closeBtn = e.target && e.target.closest ? e.target.closest('.js-close-serials') : null;
                if (closeBtn) {
                    var panelToClose = closeBtn.closest('.js-serials-panel');
                    if (panelToClose) panelToClose.style.display = 'none';
                    return;
                }
            });

            tableBody.addEventListener('input', function(e) {
                if (!e.target) return;
                if (e.target.classList && e.target.classList.contains('js-serial-search')) {
                    var panel = e.target.closest('.js-serials-panel');
                    if (!panel) return;
                    var q = (e.target.value || '').toLowerCase();
                    var rows = panel.querySelectorAll('.form-check');
                    rows.forEach(function(r) {
                        var text = (r.textContent || '').toLowerCase();
                        r.style.display = text.indexOf(q) !== -1 ? '' : 'none';
                    });
                }
            });

            tableBody.addEventListener('change', function(e) {
                if (!e.target) return;
                if (e.target.classList && e.target.classList.contains('js-serial-checkbox')) {
                    var panel = e.target.closest('.js-serials-panel');
                    if (!panel) return;
                    var idx = panel.dataset.rowIdx;
                    syncSerialHiddenInputs(panel, idx);

                    var row = e.target.closest('tr');
                    updateSerialIndicatorForRow(row);
                }
            });
        }

        var latestUnitPricesCache = {};

        async function fetchLatestUnitPrices(productId, branchId) {
            var key = String(productId) + ':' + String(branchId);
            if (latestUnitPricesCache[key]) {
                return latestUnitPricesCache[key];
            }

            try {
                var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    return null;
                }

                console.log('Fetching latest unit prices for product:', productId, 'branch:', branchId);
                console.log('CSRF token:', csrfToken.substring(0, 20) + '...');

                var response = await fetch('{{ route('superadmin.stockin.latest-unit-prices') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        branch_id: branchId
                    })
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    console.error('Response not OK:', response.statusText);
                    throw new Error('HTTP error! status: ' + response.status);
                }
                var data = await response.json();
                console.log('Received data:', data);
                latestUnitPricesCache[key] = data;
                return data;
            } catch (e) {
                console.error('Fetch latest-unit-prices error:', e);
                return null;
            }
        }

        function resetUnitPriceInputsForBranchChange(row) {
            if (!row) return;
            var inputs = row.querySelectorAll('input.js-new-price-unit');
            inputs.forEach(function(inp) {
                inp.dataset.manual = '';
                inp.dataset.dbSaved = '';
                inp.placeholder = '';
                inp.dataset.autofill = '1';
                inp.value = '0.00';
                inp.dataset.autofill = '';
            });

            syncSelectedUnitPricesToHidden();
            updateNewPriceValidityAll();
        }

        function applyLatestPricesToRowAsPlaceholder(row, pricesData) {
            if (!row || !pricesData || !pricesData.unit_prices) return;
            var unitPrices = pricesData.unit_prices || {};
            var inputs = row.querySelectorAll('input.js-new-price-unit');
            inputs.forEach(function(inp) {
                var unitId = String(inp.dataset.unitId || '');
                if (!unitId) return;

                var hasDb = Object.prototype.hasOwnProperty.call(unitPrices, unitId);
                inp.dataset.dbSaved = hasDb ? '1' : '0';

                if (hasDb) {
                    var p = unitPrices[unitId];
                    inp.placeholder = formatPrice(p);

                    // Prefill visible value only if untouched.
                    var isManual = String(inp.dataset.manual || '') === '1';
                    var currentVal = String(inp.value || '').trim();
                    var currentNum = parseFloat(currentVal);
                    var isDefaultZero = currentVal === '' || !isFinite(currentNum) || currentNum === 0;
                    if (!isManual && isDefaultZero) {
                        inp.dataset.autofill = '1';
                        inp.value = formatPrice(p);
                        inp.dataset.autofill = '';
                    }
                    return;
                }

                // No saved DB price for this unit type: show 0.00
                inp.placeholder = '0.00';
                var isManualMissing = String(inp.dataset.manual || '') === '1';
                var curValMissing = String(inp.value || '').trim();
                var curNumMissing = parseFloat(curValMissing);
                var isDefaultZeroMissing = curValMissing === '' || !isFinite(curNumMissing) || curNumMissing === 0;
                if (!isManualMissing && isDefaultZeroMissing) {
                    inp.dataset.autofill = '1';
                    inp.value = '0.00';
                    inp.dataset.autofill = '';
                }
            });

            updateDbPriceAlert(row, unitPrices);
            syncSelectedUnitPricesToHidden();
            updateNewPriceValidityAll();
        }

        function getUnitFactor(row, unitId) {
            var factorEl = row.querySelector('.js-unit-factor[data-unit-id="' + unitId + '"]');
            var factor = factorEl ? (parseFloat(factorEl.dataset.factor || '1') || 1) : 1;
            if (!isFinite(factor) || factor <= 0) factor = 1;
            return factor;
        }

        function updateDbPriceAlert(row, unitPrices) {
            if (!row) return;
            var alertEl = row.querySelector('.js-price-alert');
            if (!alertEl) return;

            var originalInput = row.querySelector('input.js-original-price');
            var original = originalInput ? (parseFloat(originalInput.value || '0') || 0) : 0;

            var remainingMeta = row.querySelector('.js-remaining-display');
            var purchaseFactor = remainingMeta ? (parseFloat(remainingMeta.dataset.purchaseFactor || '1') || 1) : 1;
            if (!isFinite(purchaseFactor) || purchaseFactor <= 0) purchaseFactor = 1;

            var basePurchaseCost = original / purchaseFactor;
            if (!isFinite(basePurchaseCost) || basePurchaseCost < 0) basePurchaseCost = 0;

            var messages = [];
            Object.keys(unitPrices || {}).forEach(function(unitId) {
                var p = parseFloat(unitPrices[unitId] || '0') || 0;
                if (!(p > 0)) return;
                var factor = getUnitFactor(row, unitId);
                var minAllowed = basePurchaseCost * factor;
                if (p < minAllowed) {
                    var unitNameEl = row.querySelector('input.js-new-price-unit[data-unit-id="' + unitId + '"]');
                    var unitName = unitNameEl ? (unitNameEl.closest('.border') ? unitNameEl.closest('.border').querySelector('.fw-semibold')?.textContent : '') : '';
                    unitName = unitName ? unitName.trim() : ('Unit ' + unitId);
                    messages.push(unitName + ' saved price ' + formatPrice(p) + ' is below purchase min ' + formatPrice(minAllowed));
                }
            });

            if (messages.length > 0) {
                alertEl.style.display = '';
                alertEl.title = messages.join(' | ');
            } else {
                alertEl.style.display = 'none';
                alertEl.title = '';
            }
        }

        function deriveBaseFromUnitPrice(row, unitId, unitPrice) {
            var factorEl = row.querySelector('.js-unit-factor[data-unit-id="' + unitId + '"]');
            var factor = factorEl ? (parseFloat(factorEl.dataset.factor || '1') || 1) : 1;
            if (!isFinite(factor) || factor <= 0) factor = 1;
            var p = parseFloat(unitPrice || '0') || 0;
            if (p <= 0) return 0;
            return p / factor;
        }

        function formatPrice(n) {
            var num = parseFloat(n);
            if (!isFinite(num) || num < 0) num = 0;

            // Keep enough precision for small-unit prices (e.g., grams).
            var fixed = num.toFixed(6);
            fixed = fixed.replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1');
            return fixed === '' ? '0' : fixed;
        }

        function syncOtherUnitPricesFromDriver(row, driverUnitId, driverPrice) {
            if (!row) return;
            var base = deriveBaseFromUnitPrice(row, driverUnitId, driverPrice);
            if (!(base > 0)) return;

            var driverFactor = getUnitFactor(row, driverUnitId);

            var inputs = row.querySelectorAll('input.js-new-price-unit');
            inputs.forEach(function(inp) {
                var unitId = String(inp.dataset.unitId || '');
                if (!unitId || unitId === String(driverUnitId)) return;

                // Do not let smaller unit edits affect larger units.
                var targetFactor = getUnitFactor(row, unitId);
                if (driverFactor < targetFactor) return;

                // Auto-calc only for units with no saved DB price.
                if (String(inp.dataset.dbSaved || '') === '1') return;

                // Never overwrite manually edited target inputs.
                if (String(inp.dataset.manual || '') === '1') return;

                var computed = base * targetFactor;

                // Put computed result in placeholder, and set value only if the field is still default/0.
                inp.placeholder = formatPrice(computed);
                var currentVal = String(inp.value || '').trim();
                var currentNum = parseFloat(currentVal);
                var isDefaultZero = currentVal === '' || !isFinite(currentNum) || currentNum === 0;
                if (isDefaultZero) {
                    inp.dataset.autofill = '1';
                    inp.value = formatPrice(computed);
                    inp.dataset.autofill = '';
                }
            });

            syncSelectedUnitPricesToHidden();
            updateNewPriceValidityAll();
        }

        function updateRemainingToStockAll() {
            if (!tableBody) return;
            var baseInputs = tableBody.querySelectorAll('input.js-stockin-qty-base');
            var totals = {};

            var originalByProduct = {};

            baseInputs.forEach(function(input) {
                var pid = String(input.dataset.productId || '');
                if (!pid) return;
                var qty = parseFloat(input.value || '0') || 0;
                totals[pid] = (totals[pid] || 0) + qty;
            });

            var remainingEls = tableBody.querySelectorAll('.js-remaining-to-stock');
            remainingEls.forEach(function(el) {
                var pid = String(el.dataset.productId || '');
                var original = parseFloat(el.dataset.originalRemaining || '0') || 0;
                if (originalByProduct[pid] == null) {
                    originalByProduct[pid] = original;
                }
            });

            remainingEls.forEach(function(el) {
                var pid = String(el.dataset.productId || '');
                var original = parseFloat(el.dataset.originalRemaining || '0') || 0;
                var used = totals[pid] || 0;
                var remainingBase = original - used;
                if (remainingBase < 0) remainingBase = 0;
                el.dataset.remainingBase = String(remainingBase);
            });

            function fmtNumber(n) {
                var num = Number(n);
                if (!isFinite(num)) return '0';
                var fixed = num.toFixed(6);
                return fixed.replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1');
            }

            var remainingDisplays = tableBody.querySelectorAll('.js-remaining-display');
            remainingDisplays.forEach(function(el) {
                var pid = String(el.dataset.productId || '');
                if (!pid) return;

                var original = parseFloat(el.dataset.originalRemaining || '0') || 0;
                var used = totals[pid] || 0;
                var remainingBase = original - used;
                if (remainingBase < 0) remainingBase = 0;

                var purchaseFactor = parseFloat(el.dataset.purchaseFactor || '1') || 1;
                if (purchaseFactor <= 0) purchaseFactor = 1;
                var purchaseUnitName = el.dataset.purchaseUnitName || '';
                var baseUnitName = el.dataset.baseUnitName || '';

                var remainingPurchaseUnits = Math.floor(remainingBase / purchaseFactor);
                if (!isFinite(remainingPurchaseUnits) || remainingPurchaseUnits < 0) remainingPurchaseUnits = 0;
                var remainingRemainderBase = remainingBase - (remainingPurchaseUnits * purchaseFactor);
                if (!isFinite(remainingRemainderBase) || remainingRemainderBase < 0) remainingRemainderBase = 0;

                var remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '');
                if (baseUnitName) {
                    remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '') + ' / ' + fmtNumber(remainingBase) + ' ' + baseUnitName;
                }

                el.textContent = 'Remaining: ' + remainingDisplay;
            });

            baseInputs.forEach(function(input) {
                var pid = String(input.dataset.productId || '');
                if (!pid) return;
                var original = originalByProduct[pid] || 0;
                var total = totals[pid] || 0;
                input.setCustomValidity('');
                if (total > original) {
                    input.setCustomValidity('Total Stock-In Qty for this product exceeds remaining to stock.');
                }
            });

            updateRemainingDisplayInSelectedUnit();
        }

        function updateRemainingDisplayInSelectedUnit() {
            // Intentionally kept as a no-op now that we display Remaining to Stock in base units only.
        }

        function syncStockInQtyToBaseAll() {
            if (!tableBody) return;
            var rows = tableBody.querySelectorAll('tr');
            rows.forEach(function(row) {
                var baseInput = row.querySelector('input.js-stockin-qty-base');
                if (!baseInput) return;

                var totalBase = 0;
                var qtyInputs = row.querySelectorAll('input.js-stockin-qty-unit');
                qtyInputs.forEach(function(inp) {
                    var entered = parseFloat(inp.value || '0') || 0;
                    var unitId = String(inp.dataset.unitId || '');
                    var factorEl = unitId ? row.querySelector('.js-unit-factor[data-unit-id="' + unitId + '"]') : null;
                    var factor = factorEl ? (parseFloat(factorEl.dataset.factor || '1') || 1) : 1;
                    totalBase += (entered * factor);
                });

                baseInput.value = String(totalBase);
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
                    var baseUrl = '{{ route('superadmin.stockin.products-by-purchase', ['purchase' => ':purchase']) }}';
                    var url = baseUrl.replace(':purchase', purchaseId);
                    var res = await fetch(url, {
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

        // Active row highlight (helps user see the row they're currently editing)
        if (tableBody) {
            var setActiveRow = function(row) {
                if (!row) return;
                tableBody.querySelectorAll('tr.stockin-active-row').forEach(function(r) {
                    r.classList.remove('stockin-active-row');
                });
                row.classList.add('stockin-active-row');
            };

            tableBody.addEventListener('focusin', function(e) {
                var row = e.target && e.target.closest ? e.target.closest('tr') : null;
                if (!row) return;
                setActiveRow(row);
            });

            tableBody.addEventListener('click', function(e) {
                var row = e.target && e.target.closest ? e.target.closest('tr') : null;
                if (!row) return;
                setActiveRow(row);
            });

            document.addEventListener('click', function(e) {
                if (!e.target || !e.target.closest) return;
                if (e.target.closest('#purchase-items-table-body')) return;
                tableBody.querySelectorAll('tr.stockin-active-row').forEach(function(r) {
                    r.classList.remove('stockin-active-row');
                });
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
                        el.value = '';
                    }
                    if (name.endsWith('[quantity]')) {
                        el.value = 0;
                    }
                    if (name.endsWith('[new_price]')) {
                        el.value = '0.00';
                        if (el.classList && el.classList.contains('js-new-price')) {
                            el.setCustomValidity('New Price must be greater than Original Price.');
                        }
                    }
                });

                // Insert cloned row after the current one
                currentRow.parentNode.insertBefore(clone, currentRow.nextSibling);

                updateRemainingToStockAll();
                updateNewPriceValidityAll();
            });
        }

        if (tableBody) {
            tableBody.addEventListener('click', function(e) {
                if (!e.target.classList.contains('js-remove-row')) return;
                var row = e.target.closest('tr');
                if (!row) return;
                row.remove();
                updateRemainingToStockAll();
                syncSelectedUnitPricesToHidden();
                updateNewPriceValidityAll();
            });
        }

        if (tableBody) {
            tableBody.addEventListener('input', function(e) {
                if (!e.target) return;
                if (e.target.classList.contains('js-new-price-unit')) {
                    if (e.target.dataset.autofill === '1') {
                        return;
                    }

                    var row = e.target.closest('tr');
                    if (row) {
                        var driverUnitId = String(e.target.dataset.unitId || '');
                        // Last edited unit becomes the driver: clear manual markers on siblings.
                        row.querySelectorAll('input.js-new-price-unit').forEach(function(inp) {
                            inp.dataset.manual = '';
                        });
                        e.target.dataset.manual = '1';
                        syncOtherUnitPricesFromDriver(row, driverUnitId, e.target.value);
                    }

                    syncSelectedUnitPricesToHidden();
                    updateNewPriceValidityAll();
                    if (!form.checkValidity()) {
                        form.reportValidity();
                    }

                    if (e.target && typeof e.target.reportValidity === 'function') {
                        e.target.reportValidity();
                    }
                    return;
                }

                if (e.target.classList.contains('js-stockin-qty-unit')) {
                    syncStockInQtyToBaseAll();
                    updateRemainingToStockAll();
                    var row = e.target.closest('tr');
                    updateSerialIndicatorForRow(row);
                    return;
                }
            });
        }

        // Unit type selection radio buttons were removed; no change handler needed.

        if (form) {
            form.addEventListener('submit', async function(e) {
                syncStockInQtyToBaseAll();
                if (tableBody) {
                    tableBody.querySelectorAll('tr').forEach(function(row) {
                        updateSerialIndicatorForRow(row);
                    });
                }
                syncSelectedUnitPricesToHidden();
                updateNewPriceValidityAll();

                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
                    return;
                }

                var qtyInputs = form.querySelectorAll('input.js-stockin-qty-base');
                var totalAll = 0;
                qtyInputs.forEach(function(inp) {
                    totalAll += (parseFloat(inp.value || '0') || 0);
                });

                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
                    return;
                }

                if (totalAll <= 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Stock-In Quantity',
                            text: 'Please enter Stock-In Qty for at least one item.',
                            confirmButtonColor: 'var(--theme-color)'
                        });
                    } else {
                        alert('Please enter Stock-In Qty for at least one item.');
                    }
                    return;
                }

                e.preventDefault();
                
                var formData = new FormData(form);
                
                try {
                    var response = await fetch('{{ route('superadmin.stockin.store') }}', {
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
                            }).then(function() {
                                if (result.redirect_url) {
                                    window.location.href = result.redirect_url;
                                }
                            });
                        } else {
                            alert('Success: ' + result.message);
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            }
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
