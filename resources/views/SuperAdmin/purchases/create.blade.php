@php
    // Expected variables: $branches, $products, $brands, $categories, $product_types, $unit_types
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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tesseract.js -->
    <script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>

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
                    <div>
                        <a href="{{ route('superadmin.purchases.index') }}" class="btn btn-outline-primary">
                            Back to Purchases
                        </a>
                        <button type="button" id="ocr-button" class="btn btn-primary">
                            OCR
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('superadmin.purchases.store') }}">
                    @csrf

                    <div class="row g-3 mb-4">

                        <div class="col-md-4">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" required
                                   value="{{ date('Y-m-d') }}">
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
                         <button type="button" id="add-item-btn" class="btn btn-secondary">
                            Add Item
                        </button>
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
        <div class="col-md-3">
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
            <input type="number" name="items[][quantity]" class="form-control" min="1" required>
        </div>

        <div class="col-md-2">
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

        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="number" class="form-control item-total" readonly>
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-danger remove-item-btn">Remove</button>
        </div>
    </div>
</template>

<!-- ADD SUPPLIER MODAL -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Add New Supplier</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm" action="{{ route('superadmin.suppliers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
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

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Brand</label>
                            <select name="brand_id" class="form-select">
                                <option value="">-- Select Brand --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Type</label>
                            <select name="product_type_id" class="form-select">
                                <option value="">-- Select Product Type --</option>
                                                                @foreach($product_types as $type)
                                    <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit Type</label>
                            <select name="unit_type_id" class="form-select">
                                <option value="">-- Select Unit Type --</option>
                                                                @foreach($unit_types as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                @endforeach
                            </select>
                        </div>
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
    const productModal = new bootstrap.Modal(document.getElementById('addProductModal'));
    const supplierModal = new bootstrap.Modal(document.getElementById('addSupplierModal'));

    let lastSelect = null;
    let itemIndex = 0;

    // ----------------- PRODUCT SELECT2 & ADD NEW PRODUCT -----------------
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

                if (!results || document.querySelector('.select2-add-supplier')) return;

                const li = document.createElement('li');
                li.className = 'select2-results__option select2-add-supplier';
                li.innerHTML = `➕ Add new supplier`;

                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    select.select2('close');
                    $('#addProductForm [name="product_name"]').val(term);
                    productModal.show();
                });

                results.appendChild(li);
            }, 0);
        });
    }

    function addItem(productData = null) {
        const node = template.content.cloneNode(true);

        // Update name attributes with current index
        node.querySelectorAll('[name^="items[]"]').forEach(el => {
            const name = el.getAttribute('name').replace('[]', `[${itemIndex}]`);
            el.setAttribute('name', name);
        });

        container.appendChild(node);
        const newRow = container.querySelector('.item-row:last-child');
        initProductSelect(newRow);

        if (productData) {
            $(newRow).find('.product-select').val(productData.id).trigger('change');
            $(newRow).find('input[name$="[quantity]"]').val(productData.quantity);
            $(newRow).find('input[name$="[cost]"]').val(productData.cost);
        }

        itemIndex++;
        updateTotals();
    }

    addItemBtn.addEventListener('click', addItem);

        function updateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('input[name$="[quantity]"]').value) || 0;
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

    // ----------------- SUPPLIER SELECT2 & ADD NEW SUPPLIER -----------------
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
            li.className = 'select2-results__option select2-add-product';
            li.innerHTML = `➕ Add new supplier`;

            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $supplierSelect.select2('close');
                $supplierNameInput.val(term);
                supplierModal.show();
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
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.success) {
                    const newOption = new Option(response.supplier.supplier_name, response.supplier.id, true, true);
                    $supplierSelect.append(newOption).trigger('change');
                    supplierModal.hide();
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
                } else {
                    let errorMessages = 'An unknown error occurred.';
                    if (response.errors) {
                        errorMessages = Object.values(response.errors).map(e => e[0]).join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorMessages,
                        didOpen: () => {
                            const supplierModal = document.getElementById('addSupplierModal');
                            if (supplierModal) {
                                supplierModal.setAttribute('aria-hidden', 'true');
                            }
                        },
                        didClose: () => {
                            const supplierModal = document.getElementById('addSupplierModal');
                            if (supplierModal) {
                                supplierModal.removeAttribute('aria-hidden');
                            }
                        }
                    });
                }
            },
            error: function (xhr, status, error) {
                let errorMessages = 'An unexpected error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    html: errorMessages,
                    didOpen: () => {
                        const supplierModal = document.getElementById('addSupplierModal');
                        if (supplierModal) {
                            supplierModal.setAttribute('aria-hidden', 'true');
                        }
                    },
                    didClose: () => {
                        const supplierModal = document.getElementById('addSupplierModal');
                        if (supplierModal) {
                            supplierModal.removeAttribute('aria-hidden');
                        }
                    }
                });
            }
        });
    });

    $('#saveProductBtn').on('click', function () {
        const form = $('#addProductForm');
        $.ajax({
            url: '{{ route("superadmin.products.store") }}',
            method: 'POST',
            data: form.serialize(),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                if (response.success) {
                    const newOption = new Option(response.product.product_name, response.product.id, true, true);
                    lastSelect.append(newOption).trigger('change');
                    productModal.hide();
                    form[0].reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Product saved successfully!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    let errorMessages = 'An unknown error occurred.';
                    if (response.errors) {
                        errorMessages = Object.values(response.errors).map(e => e[0]).join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessages });
                }
            },
            error: function (xhr) {
                let errorMessages = 'An unexpected error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessages = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>');
                }
                Swal.fire({ icon: 'error', title: 'Save Failed', html: errorMessages });
            }
        });
    });

    // ----------------- MODAL FOCUS FIX -----------------
    const addSupplierModalEl = document.getElementById('addSupplierModal');
    const addProductModalEl = document.getElementById('addProductModal');

    // When a new modal is shown, hide the one behind it
    $(addProductModalEl).on('show.bs.modal', function () {
        $(addSupplierModalEl).attr('aria-hidden', 'true');
    });

    // When the new modal is hidden, show the original one again
    $(addProductModalEl).on('hidden.bs.modal', function () {
        $(addSupplierModalEl).removeAttr('aria-hidden');
    });

    // ----------------- OCR FUNCTIONALITY -----------------
    const ocrButton = document.getElementById('ocr-button');
    const ocrFileInput = document.createElement('input');
    ocrFileInput.type = 'file';
    ocrFileInput.accept = 'image/*';

    ocrButton.addEventListener('click', () => ocrFileInput.click());

    ocrFileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        Swal.fire({
            title: 'Processing OCR',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        Tesseract.recognize(file, 'eng', { logger: m => console.log(m) })
            .then(({ data: { text } }) => {
                $.ajax({
                    url: '{{ route("superadmin.purchases.ocr-product-match") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', text: text },
                    success: function(response) {
                        Swal.close();
                        if (response.reference_number) $('input[name="reference_number"]').val(response.reference_number);
                        $('#items-container').empty();
                        itemIndex = 0;
                        if (response.products && response.products.length > 0) {
                            response.products.forEach(product => addItem(product));
                            Swal.fire('Success', 'Products added from OCR scan.', 'success');
                        } else {
                            Swal.fire('No Products Found', 'Could not match any products from the scanned text.', 'warning');
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire('Error', 'An error occurred while matching products.', 'error');
                    }
                });
            })
            .catch(err => {
                Swal.close();
                console.error(err);
                Swal.fire({ title: 'OCR Error', text: 'Could not recognize text from the image.', icon: 'error' });
            });
    });

});
</script>


</body>
</html>
