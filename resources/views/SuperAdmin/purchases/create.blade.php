@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
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
                </div>
            </div>
        </main>
    </div>

    <!-- ITEM TEMPLATE -->
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
                    <select name="items[][product_type]" class="form-select product-type-select" required>
                        <option value="non-electronic" selected>Non-Electronic</option>
                        <option value="electronic">Electronic</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="items[][quantity]" class="form-control quantity-input" min="1" value="1" required>
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

                <div class="col-md-1">
                    <label class="form-label invisible">Remove</label>
                    <button type="button" class="btn btn-danger remove-item-btn w-100">Remove</button>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-12 serials-container d-none">
                    <div class="border rounded p-3">
                        <h6>Electronic product details & serials</h6>
                        <p class="text-muted small">Add product serials / IMEIs. You can add multiple entries.</p>
                        <div class="serial-entries-container"></div>
                        <button type="button" class="btn btn-primary btn-sm add-serial-btn mt-2">Add serial</button>
                        <span class="text-muted small ms-2">Each serial links to a branch and may have a warranty expiry date.</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- UNMATCHED ITEM TEMPLATE -->
    <template id="unmatched-item-template">
        <div class="item-row border rounded p-3 mb-3 bg-light">
            <input type="hidden" name="items[][is_new]" value="1">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">New Product Name</label>
                    <input type="text" name="items[][product_name]" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Product Type</label>
                    <select name="items[][product_type]" class="form-select product-type-select" required>
                        <option value="non-electronic" selected>Non-Electronic</option>
                        <option value="electronic">Electronic</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Quantity</label>
                    <input type="number" name="items[][quantity]" class="form-control quantity-input" min="1" value="1" required>
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
                <div class="col-md-1">
                    <label class="form-label invisible">Remove</label>
                    <button type="button" class="btn btn-danger remove-item-btn w-100">Remove</button>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-12 serials-container d-none">
                    <div class="border rounded p-3">
                        <h6>Electronic product details & serials</h6>
                        <p class="text-muted small">Add product serials / IMEIs. You can add multiple entries.</p>
                        <div class="serial-entries-container"></div>
                        <button type="button" class="btn btn-primary btn-sm add-serial-btn mt-2">Add serial</button>
                        <span class="text-muted small ms-2">Each serial links to a branch and may have a warranty expiry date.</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- SERIAL ENTRY TEMPLATE -->
    <template id="serial-entry-template">
        <div class="row g-3 align-items-center serial-entry-row mb-2">
            <div class="col-md-4">
                <label class="form-label">Serial Number / IMEI</label>
                <input type="text" name="serial_number" class="form-control" placeholder="Enter serial number" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Branch</label>
                <select name="branch_id" class="form-select branch-select" required>
                    <option value="">-- Select Branch --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Warranty Expiry</label>
                <input type="date" name="warranty_expiry" class="form-control">
            </div>
            <div class="col-md-1">
                <label class="form-label invisible">Remove</label>
                <button type="button" class="btn btn-sm btn-outline-danger remove-serial-btn w-100">Remove</button>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }
        
        .branch-select {
            width: 100% !important;
        }
        
        .select2-container {
            width: 100% !important;
        }
    </style>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    console.log('Purchases create script loaded successfully!');
    
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM loaded, initializing purchases...');

        const branches = @json($branches);
        console.log('Branches data loaded:', branches);

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

        addItemBtn.addEventListener('click', () => addItem());

        function addUnmatchedItem(productData = null) {
            const unmatchedTemplate = document.getElementById('unmatched-item-template');
            const node = unmatchedTemplate.content.cloneNode(true);

            node.querySelectorAll('[name^="items[]"]').forEach(el => {
                const name = el.getAttribute('name').replace('[]', `[${itemIndex}]`);
                el.setAttribute('name', name);
            });

            container.appendChild(node);
            const newRow = container.querySelector('.item-row:last-child');

            if (productData) {
                $(newRow).find('input[name$="[product_name]"]').val(productData.name);
                $(newRow).find('input[name$="[quantity]"]').val(productData.quantity);
                $(newRow).find('input[name$="[cost]"]').val(productData.cost);
            }

            itemIndex++;
            updateTotals();
        }

        // --- SERIALS VISIBILITY ---
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-type-select')) {
                const row = e.target.closest('.item-row');
                const serialsContainer = row.querySelector('.serials-container');
                if (e.target.value === 'electronic') {
                    serialsContainer.classList.remove('d-none');
                } else {
                    serialsContainer.classList.add('d-none');
                    const serialEntriesContainer = serialsContainer.querySelector('.serial-entries-container');
                    if (serialEntriesContainer) {
                        serialEntriesContainer.innerHTML = '';
                    }
                }
            }
        });

        function updateTotals() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('input[name$="[quantity]"]').value) || 0;
                const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
                grandTotal += quantity * cost;
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

            // --- ADD / REMOVE SERIALS ---
            if (e.target.classList.contains('add-serial-btn')) {
                console.log('Add serial button clicked!');
                
                const itemRow = e.target.closest('.item-row');
                const serialsContainer = itemRow.querySelector('.serial-entries-container');
                const serialTemplate = document.getElementById('serial-entry-template');
                const itemIndex = Array.from(container.children).indexOf(itemRow);
                const serialIndex = serialsContainer.children.length;

                console.log('Creating serial entry for item:', itemIndex, 'serial:', serialIndex);

                const node = serialTemplate.content.cloneNode(true);

                node.querySelector('[name="serial_number"]').name = `items[${itemIndex}][serials][${serialIndex}][serial_number]`;
                node.querySelector('[name="branch_id"]').name = `items[${itemIndex}][serials][${serialIndex}][branch_id]`;
                const warrantyInput = node.querySelector('[name="warranty_expiry"]');
                warrantyInput.name = `items[${itemIndex}][serials][${serialIndex}][warranty_expiry]`;
                warrantyInput.value = new Date().toISOString().slice(0, 10);

                serialsContainer.appendChild(node);

                const branchSelect = serialsContainer.querySelector('.serial-entry-row:last-child .branch-select');
                
                console.log('Adding branch dropdown:', branchSelect);
                console.log('Available branches:', branches);
                
                if (branchSelect && branches) {
                    console.log('Branches data type:', typeof branches);
                    console.log('Branches data:', branches);
                    
                    // Clear and add branch options
                    branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
                    
                    // Check if branches is an array of objects or a string
                    if (Array.isArray(branches)) {
                        branches.forEach(branch => {
                            console.log('Processing branch:', branch);
                            const branchName = branch.name || branch.branch_name || 'Unknown Branch';
                            const branchId = branch.id || branch.branch_id || '';
                            const option = new Option(branchName, branchId);
                            branchSelect.add(option);
                        });
                    } else {
                        console.log('Branches is not an array, trying to parse...');
                        // If it's a string or object, try to extract branch info
                        try {
                            if (typeof branches === 'object') {
                                Object.keys(branches).forEach(key => {
                                    const branch = branches[key];
                                    const branchName = branch.name || branch.branch_name || `Branch ${key}`;
                                    const branchId = branch.id || branch.branch_id || key;
                                    const option = new Option(branchName, branchId);
                                    branchSelect.add(option);
                                });
                            }
                        } catch (e) {
                            console.error('Error parsing branches:', e);
                        }
                    }
                    
                    console.log('Branch options added:', branches.length, 'branches');
                    
                    // Try to initialize Select2, but fallback to normal select if it fails
                    try {
                        $(branchSelect).select2({
                            theme: 'bootstrap-5',
                            placeholder: '-- Select Branch --',
                            width: '100%',
                            dropdownParent: $(branchSelect).parent()
                        });
                        console.log('Select2 initialized successfully');
                        
                        // Force show the Select2 container
                        setTimeout(() => {
                            const select2Container = $(branchSelect).next('.select2-container');
                            if (select2Container.length > 0) {
                                select2Container.show();
                                console.log('Select2 container forced to show');
                            }
                        }, 50);
                        
                    } catch (error) {
                        console.log('Select2 failed, using normal select:', error);
                        // Make the normal select visible
                        branchSelect.style.display = 'block';
                        branchSelect.style.width = '100%';
                        branchSelect.classList.remove('select2-hidden-accessible');
                    }
                } else {
                    console.error('Branch select or branches data missing');
                    console.log('Branch select:', branchSelect);
                    console.log('Branches:', branches);
                }
            }

            if (e.target.classList.contains('remove-serial-btn')) {
                e.target.closest('.serial-entry-row').remove();
            }
        });

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
                    // Filter the raw text to show only product items and reference number
                    const lines = text.split('\n');
                    const filteredLines = [];
                    let referenceNumber = null;
                    
                    lines.forEach(line => {
                        line = line.trim();
                        if (!line) return;
                        
                        // Check for reference number
                        if (/(?:REFERENCE NO|REF NO|REFERENCE):\s*([A-Z0-9-]+)/i.test(line)) {
                            referenceNumber = line;
                            filteredLines.push(line);
                            return;
                        }
                        
                        // Skip common receipt headers and footers
                        if (/^(TOTAL|SUBTOTAL|CASH|CHANGE|VAT|DISCOUNT|PAYMENT|AMOUNT|QTY|PRICE|ITEM|DESCRIPTION|ORDER|INVOICE|RECEIPT|THANK YOU|SHOPPING AT|OFFICIAL RECEIPT)/i.test(line)) {
                            return;
                        }
                        
                        // Skip lines with dates and times
                        if (/\d{1,2}\/\d{1,2}\/\d{4}|\d{1,2}:\d{2}\s*(AM|PM)/i.test(line)) {
                            return;
                        }
                        
                        // Skip lines with addresses
                        if (/\d+\s+.*\s+(Road|St|Ave|City|Cebu)/i.test(line)) {
                            return;
                        }
                        
                        // Skip lines that are just numbers or reference numbers (except when part of product)
                        if (/^\d+$/.test(line) || /^[A-Z0-9-]{6,}$/.test(line)) {
                            return;
                        }
                        
                        // Include lines that look like product items
                        if (/^(\d+)\s+(.+?)\s+(\d+\.\d{2})\s+(\d+\.\d{2})$/.test(line) || // Qty + Name + Price + Total
                            /^(\d+)\s+(.+?)\s+(\d+\.\d{2})$/.test(line) || // Qty + Name + Price
                            /^(.+?)\s+(\d+\.\d{2})$/.test(line) || // Name + Price
                            (line.length >= 3 && line.length <= 50 && /[a-zA-Z]/.test(line) && !/\d{2,}/.test(line))) { // Name only
                            filteredLines.push(line);
                        }
                    });
                    
                    const filteredText = filteredLines.join('\n');
                    
                    // Show the filtered OCR text
                    Swal.fire({
                        title: 'OCR Text Extracted',
                        html: '<div style="text-align: left;"><strong>Product Items Found:</strong><pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto; font-size: 12px;">' + filteredText + '</pre></div>',
                        confirmButtonText: 'Process Products',
                        showCancelButton: true,
                        confirmButtonColor: '#2196F3',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Process the original full text through backend
                            $.ajax({
                                url: '{{ route("superadmin.purchases.ocr-product-match") }}',
                                method: 'POST',
                                data: { _token: '{{ csrf_token() }}', text: text },
                                success: function(response) {
                                    console.log('OCR Response:', response);
                                    Swal.close();
                                    let message = '';
                                    let icon = 'success';

                                    // Auto-fill reference number if found
                                    if (response.reference_number) {
                                        $('input[name="reference_number"]').val(response.reference_number);
                                        console.log('Reference number filled:', response.reference_number);
                                    }

                                    if (response.products && response.products.length > 0) {
                                        message = '<div style="text-align: left;"><strong>Found Products:</strong><ul>';
                                        response.products.forEach(function(product) {
                                            message += `<li>${product.name} (Qty: ${product.quantity}, Cost: ${product.cost})</li>`;
                                        });
                                        message += '</ul></div>';

                                        // Add found products to the items list
                                        response.products.forEach(function(product) {
                                            console.log('Adding found product:', product);
                                            addItem({
                                                id: product.id,
                                                quantity: product.quantity,
                                                cost: product.cost
                                            });
                                        });
                                    }

                                    if (response.unmatched_products && response.unmatched_products.length > 0) {
                                        if (message) message += '<br>';
                                        message += '<div style="text-align: left;"><strong>New Products Added:</strong><ul>';
                                        response.unmatched_products.forEach(function(product) {
                                            message += `<li>${product.name} (Qty: ${product.quantity}, Cost: ${product.cost})</li>`;
                                        });
                                        message += '</ul></div>';

                                        // Add unmatched products as new items with OCR data
                                        response.unmatched_products.forEach(function(product) {
                                            console.log('Adding unmatched product:', product);
                                            addUnmatchedItem({
                                                name: product.name,
                                                quantity: product.quantity,
                                                cost: product.cost
                                            });
                                        });
                                    }

                                    if (!message) {
                                        message = 'Could not find any products from the scanned text.';
                                        icon = 'warning';
                                    } else {
                                        message += '<br><small>Products have been added to the purchase list below. You can edit their details before saving.</small>';
                                    }

                                    console.log('Final message:', message);
                                    
                                    // Add a small delay to ensure items are added before showing the message
                                    setTimeout(() => {
                                        Swal.fire({ 
                                            title: 'OCR Scan Complete', 
                                            html: message, 
                                            icon: icon,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }, 500);
                                },
                                error: function() {
                                    Swal.close();
                                    Swal.fire('Error', 'An error occurred while matching products.', 'error');
                                }
                            });
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
@endsection
