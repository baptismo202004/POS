@extends('layouts.app')
@section('title', 'Add Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v={{ rand(1000, 9999) }}"></script>
    <style>
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
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-6">
        <div class="col-12">
            <div class="p-4 card-rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="m-0">Add Stock In</h2>
                </div>

                <form action="{{ route('admin.stockin.store') }}" method="POST">
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
                                                    <div class="small text-muted">Available to Stock In</div>
                                                    <div class="fw-semibold" id="purchase-remaining-label">-</div>
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
                                                    <th>Remaining to Stock</th>
                                                    <th>Stock Price and Quantity</th>
                                                    <th>Branch</th>
                                                    <th>Original Price</th>
                                                    <th style="width: 80px;">Actions</th>
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

                var unitIdInput = row.querySelector('input.js-unit-type-id');
                var unitId = unitIdInput ? String(unitIdInput.value || '') : '';
                var factorEl = unitId ? row.querySelector('.js-unit-factor[data-unit-id="' + unitId + '"]') : null;
                var factor = factorEl ? (parseFloat(factorEl.dataset.factor || '1') || 1) : 1;

                // Purchase price is stored as base unit cost (per base unit).
                // When selling/stocking by a larger unit (kg/box/case/etc.), the minimum allowed is base_price * factor.
                var minAllowed = original * factor;

                if (val < minAllowed) {
                    var msg = 'Please select an item in the list.';
                    hidden.setCustomValidity(msg);
                    var priceInput = unitId ? row.querySelector('input.js-new-price-unit[data-unit-id="' + unitId + '"]') : null;
                    if (priceInput) {
                        priceInput.setCustomValidity(msg);
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
                    var remainingBaseQty = parseFloat(item.base_remaining_quantity != null ? item.base_remaining_quantity : (item.quantity || 0)) || 0;
                    var purchasedQty = parseFloat(item.primary_remaining_quantity != null ? item.primary_remaining_quantity : 0) || 0;
                    var purchasedQtyDisplay = String(purchasedQty);
                    var purchasedUnitLabel = (item.unit_type && item.unit_type.unit_name) ? item.unit_type.unit_name : '';
                    if (purchasedUnitLabel) {
                        purchasedQtyDisplay += ' ' + purchasedUnitLabel;
                    }

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
                                escapeHtml(productName) +
                                '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">' +
                            '</td>' +
                            '<td>' + escapeHtml(purchasedQtyDisplay) + '</td>' +
                            '<td>' +
                                '<span class="fw-semibold js-remaining-to-stock" data-product-id="' + item.product_id + '" data-original-remaining="' + remainingBaseQty + '">' + escapeHtml(String(remainingBaseQty)) + '</span>' +
                                '<span class="text-muted"> </span>' +
                            '</td>' +
                            '<td>' +
                                '<input type="hidden" class="js-stockin-qty-base" data-product-id="' + item.product_id + '" name="items[' + idx + '][quantity]" value="0">' +
                                unitTypesHtml +
                                '<input type="hidden" class="js-selected-new-price" data-row-idx="' + idx + '" name="items[' + idx + '][new_price]" value="0.00">' +
                            '</td>' +
                            '<td>' +
                                '<select class="form-select" name="items[' + idx + '][branch_id]" required>' +
                                    '<option value="">-- Select Branch --</option>' +
                                    branchSelectOptions +
                                '</select>' +
                            '</td>' +
                            '<td>' +
                                '<input type="number" class="form-control js-original-price" name="items[' + idx + '][original_price]" min="0" step="0.01" value="' + (item.unit_price || '0.00') + '" readonly>' +
                            '</td>' +
                            '<td>' +
                                '<button type="button" class="btn btn-sm btn-outline-secondary add-branch" data-product-id="' + item.product_id + '">Add Branch</button>' +
                                '<button type="button" class="btn btn-sm btn-outline-danger mt-1 js-remove-row">Remove</button>' +
                            '</td>' +
                        '</tr>';

                    tableBody.insertAdjacentHTML('beforeend', rowHtml);

                    var insertedRow = tableBody.querySelector('tr:last-child');
                    if (insertedRow) {
                        insertedRow.dataset.primaryRemaining = String(parseFloat(item.primary_remaining_quantity || 0) || 0);
                        insertedRow.dataset.unitUnitName = item.unit_type && item.unit_type.unit_name ? item.unit_type.unit_name : '';
                        insertedRow.dataset.baseUnitName = item.base_unit_type && item.base_unit_type.unit_name ? item.base_unit_type.unit_name : (purchasedUnitLabel || '');
                        var remainingEl = insertedRow.querySelector('.js-remaining-to-stock');
                        if (remainingEl) {
                            remainingEl.dataset.originalRemaining = String(remainingBaseQty);
                            remainingEl.dataset.remainingBase = String(remainingBaseQty);
                        }
                    }
                });

            container.style.display = '';

            updateRemainingToStockAll();
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
                    var res = await fetch('{{ url('admin/stockin/products-by-purchase') }}/' + purchaseId, {
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
                if (e.target.classList.contains('js-new-price-unit')) {
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
                    return;
                }
            });
        }

        // Unit type selection radio buttons were removed; no change handler needed.

        if (form) {
            form.addEventListener('submit', async function(e) {
                syncStockInQtyToBaseAll();
                updateRemainingToStockAll();
                updateNewPriceValidityAll();

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
                    var response = await fetch('{{ route('admin.stockin.store') }}', {
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
