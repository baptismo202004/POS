@extends('layouts.app')
@section('title', 'Add New Purchase')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .purchases-create-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .purchases-create-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .purchases-create-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .purchases-create-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .purchases-create-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .purchases-create-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .purchases-create-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .purchases-create-page .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .purchases-create-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .purchases-create-page .ph-icon {
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
        .purchases-create-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .purchases-create-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .purchases-create-page .action-bar {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .purchases-create-page .btn {
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
        .purchases-create-page .btn:disabled { opacity: .5; cursor: not-allowed; transform: none !important; }
        .purchases-create-page .btn-primary,
        .purchases-create-page .theme-bg {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .purchases-create-page .btn-primary:hover,
        .purchases-create-page .theme-bg:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(13,71,161,0.34);
        }
        .purchases-create-page .btn-secondary {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
            border: 1px solid rgba(25,118,210,0.20);
            box-shadow: none;
        }
        .purchases-create-page .btn-secondary:hover { background: rgba(25,118,210,0.14); transform: translateY(-1px); }
        .purchases-create-page .btn-danger {
            background: linear-gradient(135deg, #dc2626, var(--red));
            color: #fff !important;
            border: none;
            box-shadow: 0 4px 14px rgba(239,68,68,0.20);
        }
        .purchases-create-page .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 7px 18px rgba(239,68,68,0.30); }

        .purchases-create-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .purchases-create-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .purchases-create-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .purchases-create-page .c-head::after {
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
        .purchases-create-page .c-head-title {
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
        .purchases-create-page .c-head-title i { color:rgba(0,229,255,.85); }
        .purchases-create-page .c-badge {
            position:relative;
            z-index:1;
            background:rgba(255,255,255,0.15);
            border:1px solid rgba(255,255,255,0.25);
            color:#fff;
            font-size:11px;
            font-weight:700;
            padding:3px 10px;
            border-radius:20px;
            font-family:'Nunito',sans-serif;
        }

        .purchases-create-page .card-body-pad { padding: 18px 22px 22px; }

        .purchases-create-page .section-title {
            font-family:'Nunito',sans-serif;
            font-weight: 900;
            color: var(--navy);
            font-size: 14px;
            margin: 16px 0 10px;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .purchases-create-page .section-title i { color: rgba(25,118,210,0.9); }

        .purchases-create-page label.form-label {
            font-size: 11px;
            letter-spacing: .10em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
        }
        .purchases-create-page .form-control,
        .purchases-create-page .form-select {
            border-radius: 12px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            padding: 10px 12px;
            background: #fff;
            color: var(--text);
            transition: border-color .18s, box-shadow .18s;
        }
        .purchases-create-page .form-control:focus,
        .purchases-create-page .form-select:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }

        .purchases-create-page #grand-total {
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            color: var(--navy);
            background: rgba(240,246,255,0.55);
        }

        .purchases-create-page .item-row {
            border: 1px solid rgba(13,71,161,0.09) !important;
            border-radius: 16px !important;
            background: rgba(240,246,255,0.55);
            box-shadow: 0 10px 26px rgba(13,71,161,0.06);
        }

        .purchases-modal .modal-content {
            border-radius: 18px;
            border: 1px solid rgba(25,118,210,0.18);
            box-shadow: 0 18px 50px rgba(13,71,161,0.18);
        }
        .purchases-modal .modal-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            color: #fff;
            border-bottom: none;
        }
        .purchases-modal .modal-header h5,
        .purchases-modal .modal-header .modal-title { color: #fff; font-family:'Nunito',sans-serif; font-weight: 900; }
        .purchases-modal .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            border: none;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
            border-radius: 12px;
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
        }
        .purchases-modal .btn-secondary {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
            border: 1px solid rgba(25,118,210,0.20);
            border-radius: 12px;
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
        }

        .purchases-create-page .select2-container--default .select2-selection--single {
            height: 42px;
            border-radius: 12px;
            border: 1.5px solid var(--border);
            display: flex;
            align-items: center;
        }
        .purchases-create-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 12px;
            color: var(--text);
            font-size: 13px;
        }
        .purchases-create-page .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }
        .select2-dropdown {
            border-radius: 14px;
            border: 1px solid rgba(25,118,210,0.22);
            overflow: hidden;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: linear-gradient(135deg, rgba(13,71,161,0.12), rgba(25,118,210,0.12));
            color: var(--text);
        }
        .select2-results__option.select2-add-supplier {
            font-weight: 900;
            font-family: 'Nunito', sans-serif;
            color: var(--navy);
        }
    </style>
@endpush

@section('content')
<div class="purchases-create-page purchase-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="ph-title">Add New Purchase</div>
                    <div class="ph-sub">Create a new purchase and add items</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-pen-to-square"></i> Purchase Form</div>
                <span class="c-badge">Step 1: Details</span>
            </div>

            <div class="card-body-pad">
                <form method="POST" action="{{ route('cashier.purchases.store') }}">
                    @csrf

                    <div class="row g-3 mb-2">
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

                    <div class="section-title"><i class="fas fa-boxes-stacked"></i> Purchase Items</div>
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
                        <button type="button" id="add-item-btn" class="btn btn-secondary">
                            <i class="fas fa-plus"></i>
                            Add Item
                        </button>
                        <button type="submit" class="btn theme-bg text-white">
                            <i class="fas fa-save"></i>
                            Save Purchase
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
<div class="modal fade purchases-modal" id="addSupplierModal" tabindex="-1">
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
