@extends('layouts.app')
@section('title', 'Add New Purchase')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content {
            margin-left: 0 !important;
        }
    </style>
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
                                <a href="{{ route('cashier.purchases.index') }}" class="btn btn-outline-primary">Back to Purchases</a>
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
                                        <select name="supplier_id" class="form-select">
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
            <div class="col-md-5">
                <label class="form-label">Product</label>
                <select name="items[][product_id]" class="form-select product-select" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Quantity</label>
                <input type="number" name="items[][quantity]" class="form-control quantity-input" min="1" value="1" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Unit Type</label>
                <select name="items[][unit_type_id]" class="form-select" required>
                    <option value="">-- Select Unit --</option>
                    @foreach($unit_types as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Cost</label>
                <input type="number" name="items[][cost]" class="form-control item-cost" step="0.01" required>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
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

    function recalcTotal() {
        let total = 0;
        itemsContainer.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.quantity-input')?.value || '0');
            const cost = parseFloat(row.querySelector('.item-cost')?.value || '0');
            total += (qty * cost);
        });
        grandTotal.value = total.toFixed(2);
    }

    function wireRow(row) {
        row.querySelectorAll('.quantity-input, .item-cost').forEach(input => {
            input.addEventListener('input', recalcTotal);
        });
        const removeBtn = row.querySelector('.remove-item-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
                recalcTotal();
            });
        }
    }

    function addRow() {
        const node = template.content.cloneNode(true);
        const row = node.querySelector('.item-row');
        if (!row) return;
        itemsContainer.appendChild(row);
        wireRow(row);
        recalcTotal();
    }

    addBtn.addEventListener('click', addRow);

    // Add first row by default
    addRow();
});
</script>
@endpush
