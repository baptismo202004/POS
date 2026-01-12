@php
    // Expected variables from controller:
    // $brands, $categories, $productTypes, $unitTypes, $branches
    // For edit: $product (with serials loaded)
    $isEdit = isset($product);
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product List - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery + Select2 for searchable creatable selects -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Tailwind Play CDN (for utility classes) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{ --theme-color: #2563eb; }
        .theme-bg{ background-color: var(--theme-color) !important; }
        .theme-border{ border-color: var(--theme-color) !important; }
        .theme-text{ color: var(--theme-color) !important; }
        /* small helper to mix Bootstrap and tailwind spacing */
        .card-rounded{ border-radius: 12px; }
    </style>
</head>
<body class="bg-white">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">{{ $isEdit ? 'Edit Product' : 'Add Product' }}</h2>
                                <a href="{{ route('superadmin.products.index') }}" class="btn btn-outline" style="border-color:var(--theme-color); color:var(--theme-color)">Back to products</a>
                            </div>

                            <form method="POST" action="{{ $isEdit ? route('superadmin.products.update', $product) : route('superadmin.products.store') }}" enctype="multipart/form-data" id="productForm">
                                @if($isEdit) @method('PUT') @endif
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="product_name" class="form-control" required value="{{ $isEdit ? $product->product_name : '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Barcode</label>
                                        <input type="text" name="barcode" class="form-control" required value="{{ $isEdit ? $product->barcode : '' }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Brand</label>
                                        <select name="brand_id" id="brandSelect" class="form-select select2-tags" style="width:100%">
                                            <option value="">-- Select Brand --</option>
                                            @foreach($brands ?? [] as $brand)
                                                <option value="{{ $brand->id }}" {{ $isEdit && $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" id="categorySelect" class="form-select select2-tags" style="width:100%">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}" {{ $isEdit && $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Product Type</label>
                                        <select name="product_type_id" id="productType" class="form-select select2-tags" style="width:100%">
                                            <option value="">-- Select Type --</option>
                                            <option value="1" data-electronic="1" {{ $isEdit && $product->product_type_id == 1 ? 'selected' : '' }}>Electronic</option>
                                            <option value="2" data-electronic="0" {{ $isEdit && $product->product_type_id == 2 ? 'selected' : '' }}>Non-Electronic</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Unit Type</label>
                                        <select name="unit_type_id" id="unitTypeSelect" class="form-select select2-tags" style="width:100%">
                                            <option value="">-- Select Unit --</option>
                                            @foreach($unitTypes ?? [] as $ut)
                                                <option value="{{ $ut->id }}" {{ $isEdit && $product->unit_type_id == $ut->id ? 'selected' : '' }}>{{ $ut->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Model Number</label>
                                        <input type="text" name="model_number" class="form-control" value="{{ $isEdit ? $product->model_number : '' }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Image</label>
                                        <input type="file" name="image" class="form-control">
                                        @if($isEdit && $product->image)
                                            <small class="text-muted">Current image: {{ basename($product->image) }}</small>
                                        @endif
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Tracking Type</label>
                                        <select name="tracking_type" class="form-select">
                                            <option value="none" {{ $isEdit && $product->tracking_type == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="serial" {{ $isEdit && $product->tracking_type == 'serial' ? 'selected' : '' }}>Serial</option>
                                            <option value="imei" {{ $isEdit && $product->tracking_type == 'imei' ? 'selected' : '' }}>IMEI</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Warranty Type</label>
                                        <select name="warranty_type" class="form-select">
                                            <option value="none" {{ $isEdit && $product->warranty_type == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="shop" {{ $isEdit && $product->warranty_type == 'shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="manufacturer" {{ $isEdit && $product->warranty_type == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Warranty Coverage (months)</label>
                                        <input type="number" name="warranty_coverage_months" min="0" class="form-control" value="{{ $isEdit ? $product->warranty_coverage_months : '' }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Voltage Specs</label>
                                        <input type="text" name="voltage_specs" class="form-control" value="{{ $isEdit ? $product->voltage_specs : '' }}" placeholder="e.g. 110-220V">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ $isEdit && $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $isEdit && $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                </div>

                                {{-- Electronic-only area: will be toggled by JS when product type is 'electronic' --}}
                                <div id="electronicArea" class="mt-4 p-3 bg-white card-rounded border theme-border d-none">
                                    <h5 class="mb-3 theme-text">Electronic product details & serials</h5>

                                    <p class="text-muted">Add product serials / IMEIs. You can add multiple entries.</p>

                                    <div id="serialsContainer">
                                        <!-- dynamic serial rows will be appended here -->
                                        @if($isEdit && $product->serials)
                                            @foreach($product->serials as $serial)
                                                <div class="serial-row p-3 mb-3 bg-gray-50 border rounded d-flex gap-3 align-items-start">
                                                    <div class="flex-1 w-100">
                                                        <div class="row g-2">
                                                            <div class="col-md-4">
                                                                <label class="form-label">Branch</label>
                                                                <select name="serials[][branch_id]" class="form-select">
                                                                    <option value="">-- Select Branch --</option>
                                                                    @foreach($branches ?? [] as $b)
                                                                        <option value="{{ $b->id }}" {{ $serial->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <label class="form-label">Serial / IMEI</label>
                                                                <input type="text" name="serials[][serial_number]" class="form-control" required value="{{ $serial->serial_number }}">
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label class="form-label">Status</label>
                                                                <select name="serials[][status]" class="form-select">
                                                                    <option value="in_stock" {{ $serial->status == 'in_stock' ? 'selected' : '' }}>In stock</option>
                                                                    <option value="sold" {{ $serial->status == 'sold' ? 'selected' : '' }}>Sold</option>
                                                                    <option value="returned" {{ $serial->status == 'returned' ? 'selected' : '' }}>Returned</option>
                                                                    <option value="defective" {{ $serial->status == 'defective' ? 'selected' : '' }}>Defective</option>
                                                                    <option value="lost" {{ $serial->status == 'lost' ? 'selected' : '' }}>Lost</option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-2">
                                                                <label class="form-label">Warranty expiry</label>
                                                                <input type="date" name="serials[][warranty_expiry_date]" class="form-control" value="{{ $serial->warranty_expiry_date ? $serial->warranty_expiry_date->format('Y-m-d') : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="ps-2">
                                                        <button type="button" class="btn btn-outline-danger btn-sm remove-serial">Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="d-flex gap-2 mt-3">
                                        <button type="button" id="addSerialBtn" class="btn btn-sm" style="background-color:var(--theme-color); color:white">Add serial</button>
                                        <small class="align-self-center text-muted">Each serial links to a branch and may have a warranty expiry date.</small>
                                    </div>

                                </div>

                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn" style="background-color:var(--theme-color); color:white">{{ $isEdit ? 'Update Product' : 'Save Product' }}</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    {{-- Templates and scripts --}}
    <template id="serialRowTemplate">
        <div class="serial-row p-3 mb-3 bg-gray-50 border rounded d-flex gap-3 align-items-start">
            <div class="flex-1 w-100">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Branch</label>
                        <select name="serials[][branch_id]" class="form-select">
                            <option value="">-- Select Branch --</option>
                            @foreach($branches ?? [] as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Serial / IMEI</label>
                        <input type="text" name="serials[][serial_number]" class="form-control" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="serials[][status]" class="form-select">
                            <option value="in_stock">In stock</option>
                            <option value="sold">Sold</option>
                            <option value="returned">Returned</option>
                            <option value="defective">Defective</option>
                            <option value="lost">Lost</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Warranty expiry</label>
                        <input type="date" name="serials[][warranty_expiry_date]" class="form-control">
                    </div>
                </div>
            </div>

            <div class="ps-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-serial">Remove</button>
            </div>
        </div>
    </template>

    <script>
        // Tiny helper to determine if selected product type triggers electronic UI
        function isSelectedTypeElectronic(selectEl){
            const opt = selectEl.selectedOptions[0];
            return opt && opt.dataset && opt.dataset.electronic === '1';
        }

        document.addEventListener('DOMContentLoaded', function(){
            // init select2 on non-creatable selects
            if (window.jQuery && $.fn.select2) {
                $('#brandSelect, #categorySelect').select2({
                    tags: true,
                    placeholder: '-- Select or create --',
                    allowClear: true,
                    width: 'resolve'
                });

                $('#productType, #unitTypeSelect').select2({
                    placeholder: '-- Select --',
                    allowClear: true,
                    width: 'resolve',
                    minimumResultsForSearch: Infinity // Hide search box for these
                });
            }

            const productType = document.getElementById('productType');
            const electronicArea = document.getElementById('electronicArea');

            function toggleElectronicArea(){
                const selectedOption = productType.options[productType.selectedIndex];
                if (selectedOption && selectedOption.text === 'Electronic') {
                    electronicArea.classList.remove('d-none');
                } else {
                    electronicArea.classList.add('d-none');
                }
            }

            productType.addEventListener('change', toggleElectronicArea);
            toggleElectronicArea(); // initial call to set visibility based on default selection

            addSerialBtn.addEventListener('click', function(){
                const node = document.importNode(serialTemplate.content, true);
                serialsContainer.appendChild(node);
            });

            // delegated remove
            serialsContainer.addEventListener('click', function(e){
                if(e.target.classList.contains('remove-serial')){
                    const row = e.target.closest('.serial-row');
                    if(row) row.remove();
                }
            });

            // Show new entry field based on selection
            $('.form-select').on('change', function(){
                const selectedValue = $(this).val();
                if(selectedValue === 'new'){
                    $(this).next('input').removeClass('d-none').focus();
                } else {
                    $(this).next('input').addClass('d-none');
                }
            });


        });
    </script>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
