@extends('layouts.app')
@section('title', 'Add New Purchase')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="d-flex min-vh-100">
    <div class="container-fluid purchase-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2>Add New Purchase</h2>
                            </div>

                            <form method="POST" action="{{ route('cashier.purchases.store') }}">
                                @csrf

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Purchase Date</label>
                                        <input type="date" name="purchase_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Supplier</label>
                                        <select name="supplier_id" class="form-select supplier-select">
                                            <option value="">-- Select Supplier --</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Reference No.</label>
                                        <input type="text" name="reference_number" class="form-control">
                                    </div>
                                </div>

                                <h5>Purchase Items</h5>
                                <div id="items-container"></div>

                                <div class="row mt-4 align-items-end">
                                    <div class="col-md-auto ms-auto">
                                        <label for="payment_status" class="form-label">Payment Status</label>
                                        <select name="payment_status" id="payment_status" class="form-select">
                                            <option value="pending" selected>Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                    <div class="col-md-auto">
                                        <label class="form-label">Grand Total</label>
                                        <input type="text" id="grand-total" class="form-control fs-5 fw-bold" readonly value="0.00">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" id="add-item-btn" class="btn btn-secondary">Add Item</button>
                                    <button type="submit" class="btn theme-bg text-white">Save Purchase</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<template id="item-template">
    <div class="item-row border rounded p-3 mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Product</label>
                <select name="items[][product_id]" class="form-select product-select" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Product Type</label>
                <select name="items[][product_type]" class="form-select product-type-select">
                    <option value="non-electronic" selected>Non-Electronic</option>
                    <option value="electronic">Electronic</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Qty - Unit</label>
                <div class="input-group">
                    <input type="number" name="items[][primary_quantity]" class="form-control primary-qty-input" min="1" value="1" required>
                    <select name="items[][unit_type_id]" class="form-select unit-type-select" required>
                        <option value="">-- Select Unit --</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label">× Base</label>
                <div class="input-group">
                    <input type="number" name="items[][multiplier]" class="form-control multiplier-input" min="1" value="1" step="0.0001" required>
                    <select class="form-select base-unit-select" name="items[][base_unit_type_id]">
                        <option value="">Base</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="items[][base_quantity]" class="base-qty-input" value="1">

            <div class="col-md-2">
                <label class="form-label">Cost</label>
                <input type="number" name="items[][cost]" class="form-control item-cost" step="0.01" required>
            </div>

            <div class="col-md-1">
                <label class="form-label invisible">Remove</label>
                <button type="button" class="btn btn-danger remove-item-btn w-100">Remove</button>
            </div>
        </div>
    </div>
</template>

<!-- ADD SUPPLIER MODAL -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Add New Supplier</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm" action="{{ route('cashier.suppliers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSupplierBtn">Save Supplier</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebarHTML = sessionStorage.getItem('cashierSidebarHTML') || localStorage.getItem('cashierSidebarHTML');
    if (sidebarHTML) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = sidebarHTML;
        const appendedSidebar = wrapper.firstElementChild;
        if (appendedSidebar) {
            document.body.appendChild(appendedSidebar);
        }

        const sidebar = appendedSidebar || document.querySelector('body > div[style*="position: fixed"][style*="left: 0"]');
        if (sidebar) {
            sidebar.style.transform = 'translateX(0)';
            sidebar.style.zIndex = '2000';
            const navItems = sidebar.querySelectorAll('.nav-card');
            navItems.forEach(item => {
                item.style.transform = 'translateX(0)';
                item.style.opacity = '1';
            });

            const logoImg = sidebar.querySelector('img[src*="BGH LOGO.png"]');
            if (logoImg) {
                logoImg.addEventListener('click', () => {
                    window.location.href = '{{ route('cashier.dashboard') }}';
                });
            }

            const expandedWidth = 220;
            sidebar.style.width = expandedWidth + 'px';
            sidebar.style.padding = '20px 10px';
            sidebar.style.overflowX = 'hidden';

            const page = document.querySelector('.purchase-page');
            if (page) {
                page.style.transition = 'margin-left 0.2s ease';
                page.style.marginLeft = expandedWidth + 'px';
            }

            navItems.forEach(item => {
                item.style.justifyContent = 'flex-start';
                item.style.gap = '16px';
                item.style.paddingLeft = '20px';
                item.style.paddingRight = '20px';

                const icon = item.querySelector('.nav-icon');
                if (icon) icon.style.margin = '0';

                const content = item.querySelector('.nav-content');
                if (content) {
                    content.style.opacity = '1';
                    content.style.pointerEvents = 'auto';
                }
            });

            const itemsArr = Array.from(navItems);
            const invItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Inventory');
            const stockInItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Stock In');
            if (invItem && stockInItem) {
                stockInItem.style.marginLeft = '18px';
                stockInItem.style.paddingLeft = '20px';
                invItem.insertAdjacentElement('afterend', stockInItem);
            }

            const productsItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Products');
            const categoryItem = itemsArr.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Product Category');
            if (productsItem && categoryItem) {
                categoryItem.style.marginLeft = '18px';
                categoryItem.style.paddingLeft = '20px';
                productsItem.insertAdjacentElement('afterend', categoryItem);
            }
        }
    } else {
        console.warn('Cashier sidebar not found in sessionStorage/localStorage (cashierSidebarHTML).');
    }

    const itemsContainer = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const addBtn = document.getElementById('add-item-btn');
    const grandTotal = document.getElementById('grand-total');
    let itemIndex = 0;

    function recalcBaseQuantity(row) {
        if (!row) return;
        const primaryInput = row.querySelector('.primary-qty-input');
        const multiplierInput = row.querySelector('.multiplier-input');
        const baseInput = row.querySelector('.base-qty-input');
        if (!primaryInput || !multiplierInput || !baseInput) return;
        const primaryVal = parseFloat(primaryInput.value) || 0;
        const multVal = parseFloat(multiplierInput.value) || 1;
        baseInput.value = primaryVal * multVal;
    }

    function updateTotals() {
        let total = 0;
        itemsContainer.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.primary-qty-input')?.value || '0');
            const cost = parseFloat(row.querySelector('.item-cost')?.value || '0');
            total += (qty * cost);
        });
        grandTotal.value = total.toFixed(2);
    }

    function loadProductUnits(row, productId) {
        const unitSelect = row.querySelector('.unit-type-select');
        const baseUnitSelect = row.querySelector('.base-unit-select');
        const multiplierInput = row.querySelector('.multiplier-input');

        if (!unitSelect || !baseUnitSelect || !multiplierInput) return;

        unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
        baseUnitSelect.innerHTML = '<option value="">Base</option>';
        multiplierInput.value = 1;
        recalcBaseQuantity(row);

        if (!productId) return;

        $.getJSON(`/cashier/products/${productId}/unit-types`, function (response) {
            const units = response.units || [];

            units.forEach(function (unit) {
                const option = document.createElement('option');
                option.value = unit.id;
                option.textContent = unit.name;
                option.dataset.conversionFactor = unit.conversion_factor || 1;
                unitSelect.appendChild(option);

                const baseOpt = document.createElement('option');
                baseOpt.value = unit.id;
                baseOpt.textContent = unit.name;
                baseUnitSelect.appendChild(baseOpt);
            });

            const baseUnit = units.find(u => u.is_base);
            if (baseUnit) {
                baseUnitSelect.value = String(baseUnit.id);
            }

            if (units.length === 1) {
                unitSelect.value = units[0].id;
                const conv = units[0].conversion_factor || 1;
                multiplierInput.value = conv;
                recalcBaseQuantity(row);
                updateTotals();
            }
        }).fail(function () {
        });
    }

    function wireRow(row) {
        const removeBtn = row.querySelector('.remove-item-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
                updateTotals();
            });
        }
    }

    function addRow() {
        const node = template.content.cloneNode(true);
        node.querySelectorAll('[name^="items[]"]').forEach(el => {
            const name = el.getAttribute('name').replace('[]', `[${itemIndex}]`);
            el.setAttribute('name', name);
        });
        itemsContainer.appendChild(node);
        const row = itemsContainer.querySelector('.item-row:last-child');
        if (!row) return;
        wireRow(row);
        recalcBaseQuantity(row);
        itemIndex++;
        updateTotals();
    }

    if (addBtn) {
        addBtn.addEventListener('click', addRow);
    }

    addRow();

    // ----------------- SUPPLIER SELECT2 FUNCTIONALITY -----------------
    const $supplierSelect = $('.supplier-select');
    const $supplierForm = $('#addSupplierForm');
    const $supplierNameInput = $supplierForm.find('[name="supplier_name"]');
    const $saveSupplierBtn = $('#saveSupplierBtn');

    $supplierSelect.select2({ width: '100%' }).on('select2:open', function () {
        setTimeout(() => {
            const results = document.querySelector('.select2-results__options');
            if (!results || document.querySelector('.select2-add-supplier')) return;

            const search = document.querySelector('.select2-search__field');
            const term = search ? search.value : '';

            const li = document.createElement('li');
            li.className = 'select2-results__option select2-add-supplier';
            li.innerHTML = `➕ Add new supplier`;

            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $supplierSelect.select2('close');
                $supplierNameInput.val(term);
                $('#addSupplierModal').modal('show');
            });

            results.appendChild(li);
        }, 0);
    });

    $saveSupplierBtn.on('click', function () {
        const name = $supplierNameInput.val().trim();
        if (!name) {
            Swal.fire('Error', 'Supplier name is required.', 'error');
            return;
        }

        $.ajax({
            url: $supplierForm.attr('action'),
            method: 'POST',
            data: $supplierForm.serialize(),
            success: function (response) {
                const newOption = new Option(response.supplier_name, response.id, true, true);
                $supplierSelect.append(newOption).trigger('change');
                $('#addSupplierModal').modal('hide');
                $supplierForm[0].reset();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Supplier saved successfully!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            },
            error: function (xhr) {
                let errorMessages = 'An error occurred while saving the supplier.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessages = Object.values(errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessages = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    html: errorMessages
                });
            }
        });
    });

    if (itemsContainer) {
        itemsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const row = e.target.closest('.item-row');
                const productId = e.target.value;
                loadProductUnits(row, productId);
            }

            if (e.target.classList.contains('unit-type-select')) {
                const row = e.target.closest('.item-row');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const conv = selectedOption ? parseFloat(selectedOption.dataset.conversionFactor || '1') : 1;
                const multiplierInput = row.querySelector('.multiplier-input');
                if (multiplierInput) {
                    multiplierInput.value = conv;
                }
                recalcBaseQuantity(row);
                updateTotals();
            }
        });

        itemsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('primary-qty-input') || e.target.classList.contains('multiplier-input') || e.target.classList.contains('item-cost')) {
                const row = e.target.closest('.item-row');
                recalcBaseQuantity(row);
                updateTotals();
            }
        });
    }
});
</script>
@endpush
