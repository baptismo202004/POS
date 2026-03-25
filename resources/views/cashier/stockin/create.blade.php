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

        .stockin-create-theme .table-responsive { overflow-x: auto; }
        .stockin-create-theme table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: auto; }
        .stockin-create-theme table.table thead th {
            padding: 12px 14px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: nowrap;
        }
        .stockin-create-theme table.table thead th:nth-child(1) { min-width: 120px; }
        .stockin-create-theme table.table thead th:nth-child(2) { min-width: 160px; }
        .stockin-create-theme table.table thead th:nth-child(3) { min-width: 140px; }
        .stockin-create-theme table.table thead th:nth-child(4) { min-width: 150px; }
        .stockin-create-theme table.table thead th:nth-child(5) { min-width: 260px; }
        .stockin-create-theme table.table thead th:nth-child(6) { min-width: 120px; white-space: nowrap; }
        .stockin-create-theme table.table td {
            padding: 12px 14px;
            font-size: 13px;
            vertical-align: top;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }
        /* Serial panel header fix */
        .js-serials-panel .d-flex.justify-content-between { flex-wrap: nowrap; gap: 8px; }
        .js-serials-panel .small { white-space: nowrap; }

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

        .stockin-table-scroll {
            border-radius: 16px;
            border: 1px solid var(--border);
            max-height: 60vh;
            overflow: auto;
        }
        .stockin-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background: var(--navy);
        }
        .stockin-active-row { background-color: #fff8e1 !important; }
        .stockin-active-row > td { background-color: transparent !important; }
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
                                    <div class="table-responsive stockin-table-scroll">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Purchased Qty</th>
                                                    <th>Original Price</th>
                                                    <th>Branch</th>
                                                    <th>Stock Price and Quantity</th>
                                                    <th style="width: 110px;">Actions</th>
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
            if (!opt) {
                if (supplierLabel) supplierLabel.textContent = '-';
                if (dateLabel) dateLabel.textContent = '-';
                if (refLabel) refLabel.textContent = '-';
                return;
            }
            if (supplierLabel) supplierLabel.textContent = opt.dataset.supplierName || '-';
            if (dateLabel) dateLabel.textContent = opt.dataset.purchaseDate || '-';
            if (refLabel) refLabel.textContent = opt.dataset.referenceNumber || '-';
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
                    var idx = stockInRowIndex++;
                    var productName = item.product ? item.product.product_name : 'N/A';
                    var purchaseUnitName = (item.unit_type && item.unit_type.unit_name) ? item.unit_type.unit_name : '';
                    var baseUnitName = item.base_unit_name || '';
                    var purchaseFactor = parseFloat(item.purchase_factor || 1) || 1;

                    var purchasedQty = parseFloat(item.purchased_qty != null ? item.purchased_qty : (item.quantity || 0)) || 0;
                    var purchasedBase = parseFloat(item.purchased_base != null ? item.purchased_base : 0) || 0;
                    var remainingBaseQty = parseFloat(item.remaining_base != null ? item.remaining_base : (item.quantity || 0)) || 0;

                    function fmtNumber(n) {
                        var num = Number(n);
                        if (!isFinite(num)) return '0';
                        var fixed = num.toFixed(6);
                        return fixed.replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1');
                    }

                    var purchasedQtyDisplay = fmtNumber(purchasedQty) + (purchaseUnitName ? (' ' + purchaseUnitName) : '');
                    if (baseUnitName) {
                        purchasedQtyDisplay = fmtNumber(purchasedQty) + (purchaseUnitName ? (' ' + purchaseUnitName) : '') + ' / ' + fmtNumber(purchasedBase) + ' ' + baseUnitName;
                    }

                    var remainingPurchaseUnits = Math.floor(remainingBaseQty / (purchaseFactor || 1));
                    if (!isFinite(remainingPurchaseUnits) || remainingPurchaseUnits < 0) remainingPurchaseUnits = 0;

                    var remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '');
                    if (baseUnitName) {
                        remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '') + ' / ' + fmtNumber(remainingBaseQty) + ' ' + baseUnitName;
                    }

                    var categoryType = item.category_type || 'non_electronic';
                    var showSerialPicker = categoryType === 'electronic_with_serial';

                    var unitTypes = Array.isArray(item.unit_types) ? item.unit_types : [];
                    var baseUnit = null;
                    unitTypes.forEach(function(ut) { if (ut && ut.is_base) baseUnit = ut; });
                    if (!baseUnit && unitTypes.length > 0) baseUnit = unitTypes[0];

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
                                                '<input type="number" class="form-control form-control-sm js-new-price-unit" data-row-idx="' + idx + '" data-unit-id="' + ut.id + '" name="items[' + idx + '][unit_prices][' + ut.id + ']" min="0" step="0.01" value="0.00" required>' +
                                            '</div>' +
                                            '<div class="col-6">' +
                                                '<label class="small text-muted mb-1">Stock In</label>' +
                                                '<input type="number" class="form-control form-control-sm js-stockin-qty-unit" data-product-id="' + item.product_id + '" data-row-idx="' + idx + '" data-unit-id="' + ut.id + '" name="items[' + idx + '][unit_quantities][' + ut.id + ']" min="0" value="0">' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>'
                                );
                            }).join('');
                    }

                    var serialPickerHtml = showSerialPicker ? (
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
                                '<input type="text" class="form-control form-control-sm js-serial-search mb-2" placeholder="Search serial..." style="max-width:220px;">' +
                                '<div class="small text-muted js-serials-loading">Loading...</div>' +
                                '<div class="js-serials-list" style="max-height:180px;overflow:auto;"></div>' +
                                '<div class="js-serials-hidden-inputs"></div>' +
                            '</div>' +
                        '</div>'
                    ) : '';

                    var rowHtml =
                        '<tr>' +
                            '<td>' +
                                escapeHtml(productName) +
                                '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">' +
                            '</td>' +
                            '<td>' +
                                '<div>' + escapeHtml(purchasedQtyDisplay) + '</div>' +
                                '<div class="text-muted" style="font-size:12px;">' +
                                    '<span class="js-remaining-display"' +
                                        ' data-product-id="' + item.product_id + '"' +
                                        ' data-original-remaining="' + remainingBaseQty + '"' +
                                        ' data-purchase-factor="' + purchaseFactor + '"' +
                                        ' data-purchase-unit-name="' + escapeHtml(purchaseUnitName) + '"' +
                                        ' data-base-unit-name="' + escapeHtml(baseUnitName) + '"' +
                                    '>Remaining: ' + escapeHtml(remainingDisplay) + '</span>' +
                                '</div>' +
                                '<span class="fw-semibold js-remaining-to-stock d-none" data-product-id="' + item.product_id + '" data-original-remaining="' + remainingBaseQty + '">' + escapeHtml(String(remainingBaseQty)) + '</span>' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" class="form-control js-original-price" name="items[' + idx + '][original_price]" min="0" step="0.01" value="' + (item.unit_price || '0.00') + '" readonly>' +
                            '</td>' +
                            '<td>' +
                                '<span class="form-control" style="background:rgba(240,246,255,0.55);">' + escapeHtml(cashierBranchName || 'Branch') + '</span>' +
                                '<input type="hidden" name="items[' + idx + '][branch_id]" value="' + (cashierBranchId ? String(cashierBranchId) : '') + '">' +
                            '</td>' +
                            '<td>' +
                                '<input type="hidden" class="js-stockin-qty-base" data-product-id="' + item.product_id + '" name="items[' + idx + '][quantity]" value="0">' +
                                unitTypesHtml +
                                serialPickerHtml +
                            '</td>' +
                            '<td>' +
                                '<button type="button" class="btn btn-sm btn-outline-secondary add-branch" data-product-id="' + item.product_id + '">Add Branch</button>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger mt-1 js-remove-row">Remove</button>' +
                            '</td>' +
                        '</tr>';

                    tableBody.insertAdjacentHTML('beforeend', rowHtml);

                    var insertedRow = tableBody.querySelector('tr:last-child');
                    if (insertedRow) {
                        var remainingEl = insertedRow.querySelector('.js-remaining-to-stock');
                        if (remainingEl) {
                            remainingEl.dataset.originalRemaining = String(remainingBaseQty);
                            remainingEl.dataset.remainingBase = String(remainingBaseQty);
                        }
                    }
                });

            container.style.display = 'block';
            updateRemainingToStockAll();
            syncStockInQtyToBaseAll();
        }

        function syncStockInQtyToBaseAll() {
            if (!tableBody) return;
            tableBody.querySelectorAll('tr').forEach(function(row) {
                var baseInput = row.querySelector('input.js-stockin-qty-base');
                if (!baseInput) return;
                var totalBase = 0;
                row.querySelectorAll('input.js-stockin-qty-unit').forEach(function(inp) {
                    var entered = parseFloat(inp.value || '0') || 0;
                    var unitId = String(inp.dataset.unitId || '');
                    var factorEl = unitId ? row.querySelector('.js-unit-factor[data-unit-id="' + unitId + '"]') : null;
                    var factor = factorEl ? (parseFloat(factorEl.dataset.factor || '1') || 1) : 1;
                    totalBase += (entered * factor);
                });
                baseInput.value = String(totalBase);
            });
        }

        function updateRemainingToStockAll() {
            if (!tableBody) return;
            var totals = {};
            tableBody.querySelectorAll('input.js-stockin-qty-base').forEach(function(input) {
                var pid = String(input.dataset.productId || '');
                if (!pid) return;
                totals[pid] = (totals[pid] || 0) + (parseFloat(input.value || '0') || 0);
            });

            function fmtNumber(n) {
                var num = Number(n);
                if (!isFinite(num)) return '0';
                var fixed = num.toFixed(6);
                return fixed.replace(/\.0+$/, '').replace(/(\.\d*?)0+$/, '$1');
            }

            tableBody.querySelectorAll('.js-remaining-display').forEach(function(el) {
                var pid = String(el.dataset.productId || '');
                var original = parseFloat(el.dataset.originalRemaining || '0') || 0;
                var used = totals[pid] || 0;
                var remainingBase = Math.max(0, original - used);

                var purchaseFactor = parseFloat(el.dataset.purchaseFactor || '1') || 1;
                if (purchaseFactor <= 0) purchaseFactor = 1;
                var purchaseUnitName = el.dataset.purchaseUnitName || '';
                var baseUnitName = el.dataset.baseUnitName || '';

                var remainingPurchaseUnits = Math.floor(remainingBase / purchaseFactor);
                if (!isFinite(remainingPurchaseUnits) || remainingPurchaseUnits < 0) remainingPurchaseUnits = 0;

                var remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '');
                if (baseUnitName) {
                    remainingDisplay = fmtNumber(remainingPurchaseUnits) + (purchaseUnitName ? (' ' + purchaseUnitName) : '') + ' / ' + fmtNumber(remainingBase) + ' ' + baseUnitName;
                }
                el.textContent = 'Remaining: ' + remainingDisplay;
            });
        }

        // Serial picker support
        function fetchPurchaseProductSerials(purchaseId, productId) {
            var url = '{{ url('cashier/stockin/products-by-purchase') }}/' + purchaseId + '/serials/' + productId;
            return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.ok ? r.json() : null; })
                .catch(function() { return null; });
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
            panel.querySelectorAll('.js-serial-checkbox:checked').forEach(function(cb) {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'items[' + idx + '][serial_ids][]';
                inp.value = cb.value;
                hiddenWrap.appendChild(inp);
            });
        }

        function requiredSerialCountForRow(row) {
            var baseInput = row ? row.querySelector('input.js-stockin-qty-base') : null;
            var qty = baseInput ? (parseFloat(baseInput.value || '0') || 0) : 0;
            return Math.max(0, Math.round(qty));
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
        }

        if (tableBody) {
            tableBody.addEventListener('input', function(e) {
                if (!e.target) return;
                if (e.target.classList.contains('js-stockin-qty-unit')) {
                    syncStockInQtyToBaseAll();
                    updateRemainingToStockAll();
                    updateSerialIndicatorForRow(e.target.closest('tr'));
                }
                if (e.target.classList.contains('js-serial-search')) {
                    var panel = e.target.closest('.js-serials-panel');
                    if (!panel) return;
                    var q = (e.target.value || '').toLowerCase();
                    panel.querySelectorAll('.form-check').forEach(function(r) {
                        r.style.display = (r.textContent || '').toLowerCase().indexOf(q) !== -1 ? '' : 'none';
                    });
                }
            });

            tableBody.addEventListener('change', function(e) {
                if (!e.target) return;
                if (e.target.classList.contains('js-serial-checkbox')) {
                    var panel = e.target.closest('.js-serials-panel');
                    if (!panel) return;
                    syncSerialHiddenInputs(panel, panel.dataset.rowIdx);
                    updateSerialIndicatorForRow(e.target.closest('tr'));
                }
            });

            tableBody.addEventListener('click', async function(e) {
                var openBtn = e.target.closest ? e.target.closest('.js-open-serials') : null;
                if (openBtn) {
                    var idx = openBtn.dataset.rowIdx;
                    var productId = openBtn.dataset.productId;
                    var purchaseId = purchaseSelect ? purchaseSelect.value : '';
                    if (!idx || !productId || !purchaseId) return;
                    var panel = tableBody.querySelector('.js-serials-panel[data-row-idx="' + idx + '"]');
                    if (!panel) return;
                    panel.style.display = 'block';
                    if (panel.dataset.loaded !== '1') {
                        var payload = await fetchPurchaseProductSerials(purchaseId, productId);
                        renderSerialsIntoPanel(panel, idx, payload && payload.serials ? payload.serials : []);
                        panel.dataset.loaded = '1';
                    }
                    updateSerialIndicatorForRow(openBtn.closest('tr'));
                    return;
                }

                var selectAllSerialBtn = e.target.closest ? e.target.closest('.js-serial-select-all') : null;
                if (selectAllSerialBtn) {
                    var panel = selectAllSerialBtn.closest('.js-serials-panel');
                    var row = selectAllSerialBtn.closest('tr');
                    if (!panel || !row) return;
                    var required = requiredSerialCountForRow(row);
                    var toSelect = required - panel.querySelectorAll('.js-serial-checkbox:checked').length;
                    var checkboxes = panel.querySelectorAll('.js-serial-checkbox');
                    for (var i = 0; i < checkboxes.length && toSelect > 0; i++) {
                        if (!checkboxes[i].checked && checkboxes[i].closest('.form-check').style.display !== 'none') {
                            checkboxes[i].checked = true;
                            toSelect--;
                        }
                    }
                    syncSerialHiddenInputs(panel, panel.dataset.rowIdx);
                    updateSerialIndicatorForRow(row);
                    return;
                }

                var closeBtn = e.target.closest ? e.target.closest('.js-close-serials') : null;
                if (closeBtn) {
                    var p = closeBtn.closest('.js-serials-panel');
                    if (p) p.style.display = 'none';
                    return;
                }

                if (e.target.classList.contains('js-remove-row')) {
                    var row = e.target.closest('tr');
                    if (row) { row.remove(); updateRemainingToStockAll(); }
                    return;
                }

                if (e.target.classList.contains('add-branch')) {
                    var currentRow = e.target.closest('tr');
                    if (!currentRow) return;
                    var newIdx = stockInRowIndex++;
                    var clone = currentRow.cloneNode(true);
                    clone.querySelectorAll('input[name^="items["], select[name^="items["]').forEach(function(el) {
                        var name = el.getAttribute('name');
                        if (!name) return;
                        el.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + newIdx + ']'));
                        if (name.indexOf('[quantity]') !== -1 || name.indexOf('[unit_quantities]') !== -1) el.value = '0';
                        if (name.indexOf('[unit_prices]') !== -1) el.value = '0.00';
                    });
                    clone.querySelectorAll('.js-serials-panel').forEach(function(p) {
                        p.dataset.loaded = '0';
                        p.style.display = 'none';
                        p.dataset.rowIdx = newIdx;
                        var list = p.querySelector('.js-serials-list');
                        var hidden = p.querySelector('.js-serials-hidden-inputs');
                        var loading = p.querySelector('.js-serials-loading');
                        if (list) list.innerHTML = '';
                        if (hidden) hidden.innerHTML = '';
                        if (loading) { loading.style.display = ''; loading.textContent = 'Loading...'; }
                    });
                    currentRow.parentNode.insertBefore(clone, currentRow.nextSibling);
                    updateRemainingToStockAll();
                    return;
                }
            });

            // Active row highlight
            tableBody.addEventListener('focusin', function(e) {
                var row = e.target.closest ? e.target.closest('tr') : null;
                if (!row) return;
                tableBody.querySelectorAll('tr.stockin-active-row').forEach(function(r) { r.classList.remove('stockin-active-row'); });
                row.classList.add('stockin-active-row');
            });
        }

        if (purchaseSelect) {
            purchaseSelect.addEventListener('change', async function () {
                var purchaseId = purchaseSelect.value;
                tableBody.innerHTML = '';
                latestPurchaseItems = [];
                if (productsCheckboxes) productsCheckboxes.innerHTML = '';
                if (productsDropdownBtn) productsDropdownBtn.disabled = true;

                var selectedOpt = purchaseSelect.options[purchaseSelect.selectedIndex];
                setPurchaseLabelsFromOption(selectedOpt && selectedOpt.value ? selectedOpt : null);

                if (!purchaseId) { container.style.display = 'none'; return; }

                try {
                    var res = await fetch('{{ url('cashier/stockin/products-by-purchase') }}/' + purchaseId, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) throw new Error('HTTP error! status: ' + res.status);
                    var data = await res.json();
                    if (data.items && data.items.length > 0) {
                        latestPurchaseItems = data.items;
                        renderProductCheckboxes(latestPurchaseItems);
                        if (productsDropdownBtn) productsDropdownBtn.disabled = false;
                        if (productsCheckboxes) {
                            productsCheckboxes.querySelectorAll('.purchase-product-checkbox').forEach(function(cb) {
                                if (!cb.disabled) cb.checked = true;
                            });
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
                if (productsDropdownBtn.disabled) { e.preventDefault(); return; }
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
                productsCheckboxes.querySelectorAll('.purchase-product-checkbox').forEach(function(cb) {
                    if (!cb.disabled) cb.checked = true;
                });
                renderStockInTableFromSelection();
            });
        }

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                try {
                    var response = await fetch('{{ route('cashier.stockin.store') }}', {
                        method: 'POST',
                        body: new FormData(form)
                    });
                    var result = await response.json();
                    if (result.success) {
                        form.reset();
                        container.style.display = 'none';
                        tableBody.innerHTML = '';
                        Swal.fire({ icon: 'success', title: 'Success!', text: result.message, timer: 2000, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error!', text: result.message || 'Something went wrong', timer: 3000, showConfirmButton: false });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Error!', text: 'Something went wrong. Please try again.', timer: 3000, showConfirmButton: false });
                }
            });
        }
    });
})();
</script>
@endpush
