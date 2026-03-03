@extends('layouts.app')
@section('title', 'Add Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v={{ rand(1000, 9999) }}"></script>
    <style>
        .card-rounded{ border-radius: 12px; }
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

                    var branchSelectOptions = '';
                    if (branchOptions && branchOptions.length > 0) {
                        branchOptions.forEach(function(branch) {
                            branchSelectOptions += '<option value="' + branch.id + '">' + branch.name + '</option>';
                        });
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
                                '<select class="form-select" name="items[' + idx + '][branch_id]" required>' +
                                    '<option value="">-- Select Branch --</option>' +
                                    branchSelectOptions +
                                '</select>' +
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
                                '<button type="button" class="btn btn-sm btn-outline-secondary add-branch" data-product-id="' + item.product_id + '">Add Branch</button>' +
                            '</td>' +
                        '</tr>';

                    tableBody.insertAdjacentHTML('beforeend', rowHtml);
                });

            container.style.display = '';

            updateRemainingToStockAll();
        }

        function updateRemainingToStockAll() {
            if (!tableBody) return;
            var inputs = tableBody.querySelectorAll('input.js-stockin-qty');
            var totals = {};

            inputs.forEach(function(input) {
                var pid = String(input.dataset.productId || '');
                if (!pid) return;
                var qty = parseFloat(input.value || '0') || 0;
                totals[pid] = (totals[pid] || 0) + qty;
            });

            var remainingEls = tableBody.querySelectorAll('.js-remaining-to-stock');
            remainingEls.forEach(function(el) {
                var pid = String(el.dataset.productId || '');
                var original = parseFloat(el.dataset.originalRemaining || '0') || 0;
                var used = totals[pid] || 0;
                var remaining = original - used;
                if (remaining < 0) remaining = 0;
                el.textContent = String(remaining);
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
                    }
                });

                // Insert cloned row after the current one
                currentRow.parentNode.insertBefore(clone, currentRow.nextSibling);

                updateRemainingToStockAll();
            });
        }

        if (tableBody) {
            tableBody.addEventListener('input', function(e) {
                if (!e.target.classList.contains('js-stockin-qty')) return;
                updateRemainingToStockAll();
            });
        }

        if (form) {
            form.addEventListener('submit', async function(e) {
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
