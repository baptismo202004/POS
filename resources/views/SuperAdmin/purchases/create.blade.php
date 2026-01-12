@php
    // Expected variables: $branches, $products, $brands, $categories
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Purchase - SuperAdmin</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery + Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root { --theme-color: #2563eb; }
        .theme-bg { background-color: var(--theme-color) !important; }
        .theme-border { border-color: var(--theme-color) !important; }
        .theme-text { color: var(--theme-color) !important; }
        .card-rounded { border-radius: 12px; }

        .select2-container .select2-selection--single { height: 38px; }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-add-product {
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            color: #2563eb;
        }
    </style>
</head>
<body class="bg-white">

<div class="d-flex min-vh-100">
    @include('layouts.AdminSidebar')

    <main class="flex-fill p-4">
        <div class="container-fluid">
            <div class="p-4 card-rounded shadow-sm bg-white">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Add New Purchase</h2>
                    <a href="{{ route('superadmin.purchases.index') }}" class="btn btn-outline-primary">
                        Back to Purchases
                    </a>
                </div>

                <form method="POST" action="{{ route('superadmin.purchases.store') }}">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" required
                                   value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select main-select2" required>
                                <option value="">-- Select Branch --</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h5>Purchase Items</h5>
                    <div id="items-container"></div>

                    <div class="row mt-3">
                        <div class="col-md-9 text-end">
                            <strong class="fs-5">Grand Total:</strong>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="grand-total" class="form-control fs-5 fw-bold" readonly value="0.00">
                        </div>
                    </div>

                    <button type="button" id="add-item-btn" class="btn btn-secondary mt-3">
                        Add Item
                    </button>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn theme-bg text-white">
                            Save Purchase
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>
</div>

<!-- ITEM TEMPLATE -->
<template id="item-template">
    <div class="row g-3 mb-3 item-row align-items-end">
        <div class="col-md-4">
            <label class="form-label">Product</label>
            <select name="items[][product_id]" class="form-select product-select" required>
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="items[][quantity]" class="form-control" min="1" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">Cost</label>
            <input type="number" name="items[][cost]" class="form-control item-cost" step="0.01" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="number" class="form-control item-total" readonly>
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
        </div>
    </div>
</template>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Add New Product</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="addProductForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Barcode</label>
                        <input type="text" name="barcode" class="form-control" required>
                    </div>

                    <input type="hidden" name="tracking_type" value="none">
                    <input type="hidden" name="warranty_type" value="none">
                    <input type="hidden" name="status" value="active">
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="saveProductBtn">Save</button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('items-container');
    const template = document.getElementById('item-template');
    const addItemBtn = document.getElementById('add-item-btn');
    const modal = new bootstrap.Modal(document.getElementById('addProductModal'));

    let lastSelect = null;
    let itemIndex = 0;

    function initProductSelect(wrapper) {
        const select = $(wrapper).find('.product-select');

        select.select2({
            placeholder: '-- Select Product --',
            width: '100%'
        });

        select.on('select2:open', function () {
            lastSelect = select;

            setTimeout(() => {
                const results = document.querySelector('.select2-results__options');
                const search = document.querySelector('.select2-search__field');
                const term = search ? search.value : '';

                if (!results || document.querySelector('.select2-add-product')) return;

                const li = document.createElement('li');
                li.className = 'select2-results__option select2-add-product';
                li.innerHTML = `➕ Add new product`;

                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    select.select2('close');
                    $('#addProductForm [name="product_name"]').val(term);
                    modal.show();
                });

                results.appendChild(li);
            }, 0);
        });
    }

    function addItem() {
        const node = template.content.cloneNode(true);

        // Update the name attributes with the current index
        node.querySelectorAll('[name^="items[]"]').forEach(el => {
            const name = el.getAttribute('name').replace('[]', `[${itemIndex}]`);
            el.setAttribute('name', name);
        });

        container.appendChild(node);
        initProductSelect(container.querySelector('.item-row:last-child'));
        itemIndex++;
    }

    addItemBtn.addEventListener('click', addItem);

    function updateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const quantityInput = row.querySelector('input[name^="items"][name$="[quantity]"]');
            const quantity = parseFloat(quantityInput.value) || 0;
            const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
            const total = quantity * cost;
            row.querySelector('.item-total').value = total.toFixed(2);
            grandTotal += total;
        });
        document.getElementById('grand-total').value = grandTotal.toFixed(2);
    }

    container.addEventListener('input', function(e) {
        if (e.target.matches('input[name^="items["]') || e.target.matches('.item-cost')) {
            updateTotals();
        }
    });

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
            e.target.closest('.item-row').remove();
            updateTotals();
        }
    });

    $('.main-select2').select2({ width: '100%' });

    $('#saveProductBtn').on('click', function () {
        const form = $('#addProductForm');
        $.ajax({
            url: '{{ route("superadmin.products.store") }}',
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    const newOption = new Option(response.product.product_name, response.product.id, true, true);
                    lastSelect.append(newOption).trigger('change');
                    modal.hide();
                    form[0].reset();
                } else {
                    // Handle errors if needed
                    alert('Error saving product.');
                }
            },
            error: function () {
                alert('An unexpected error occurred.');
            }
        });
    });

    addItem();
});
</script>

</body>
</html>
