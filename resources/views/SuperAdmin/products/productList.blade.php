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
                            @if(session('notice'))
                                <div class="alert alert-info d-flex align-items-start" role="alert">
                                    <div>
                                        {{ session('notice') }} If you intended to create a different one, please choose a different name or adjust the value.
                                    </div>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">{{ $isEdit ? 'Edit Product' : 'Add Product' }}</h2>
                                <a href="{{ route('superadmin.products.index') }}" class="btn btn-outline" style="border-color:var(--theme-color); color:var(--theme-color)">Back to products</a>
                            </div>

                            <form method="POST" action="{{ $isEdit ? route('superadmin.products.update', $product) : route('superadmin.products.store') }}" enctype="multipart/form-data" id="productForm" data-check-name="{{ url('/superadmin/products/checkName') }}" data-check-barcode="{{ url('/superadmin/products/checkBarcode') }}">
                                @if($isEdit) @method('PUT') @endif
                                @csrf

                                <div class="row g-3">
                                    <div class="col-12"><h5 class="mb-1">Product Identity</h5><small class="text-muted">Basic identifiers and branding</small><hr class="mt-2 mb-3"></div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="productName">Product Name</label>
                                        <input type="text" id="productName" name="product_name" class="form-control" required value="{{ $isEdit ? $product->product_name : '' }}" autocomplete="off">
                                        <div class="form-text" id="nameHelp"></div>
                                    </div>
                                

                                    <div class="col-md-6">
                                        <label class="form-label" for="barcodeInput">Barcode</label>
                                        <div class="position-relative">
                                            <input type="text" id="barcodeInput" name="barcode" class="form-control" required value="{{ $isEdit ? $product->barcode : '' }}" autocomplete="off">
                                            <div class="invalid-feedback" id="barcodeError">This barcode already exists. Please enter a unique barcode.</div>
                                            <div class="position-absolute top-50 end-0 translate-middle-y me-3 d-none" id="barcodeLoading" aria-hidden="true">
                                                <span class="spinner-border spinner-border-sm text-secondary"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2"><h5 class="mb-1">Classification</h5><small class="text-muted">Brand, category, type and units</small><hr class="mt-2 mb-3"></div>
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
                                        <select id="isElectronic" class="form-select" style="width:100%">
                                            <option value="0" {{ $isEdit && optional($product->category)->is_electronic ? '' : 'selected' }}>Non-Electronic</option>
                                            <option value="1" {{ $isEdit && optional($product->category)->is_electronic ? 'selected' : '' }}>Electronic</option>
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

                                    <div class="col-12 mt-2"><h5 class="mb-1">Measurement</h5><small class="text-muted">Model, units and media</small><hr class="mt-2 mb-3"></div>
                                    <div class="col-md-4 electronic-only" id="modelNumberGroup">
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

                                    <div class="col-12 mt-2"><h5 class="mb-1">Electronics Details</h5><small class="text-muted">Shown only for electronic products</small><hr class="mt-2 mb-3"></div>
                                    <div class="col-md-4 electronic-only">
                                        <label class="form-label">Tracking Type</label>
                                        <select name="tracking_type" id="trackingType" class="form-select">
                                            <option value="none" {{ $isEdit && $product->tracking_type == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="serial" {{ $isEdit && $product->tracking_type == 'serial' ? 'selected' : '' }}>Serial</option>
                                            <option value="imei" {{ $isEdit && $product->tracking_type == 'imei' ? 'selected' : '' }}>IMEI</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 electronic-only">
                                        <label class="form-label">Warranty Type</label>
                                        <select name="warranty_type" id="warrantyType" class="form-select">
                                            <option value="none" {{ $isEdit && $product->warranty_type == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="shop" {{ $isEdit && $product->warranty_type == 'shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="manufacturer" {{ $isEdit && $product->warranty_type == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 electronic-only">
                                        <label class="form-label">Warranty Coverage (months)</label>
                                        <input type="number" id="warrantyMonths" name="warranty_coverage_months" min="0" class="form-control" value="{{ $isEdit ? $product->warranty_coverage_months : '' }}">
                                    </div>

                                    <div class="col-md-3 electronic-only">
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

                                {{-- Electronic-only area (serials). Hidden on create; available on edit only. --}}
                                @if($isEdit)
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

                              @endif
                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="submit" id="saveBtn" class="btn" style="background-color:var(--theme-color); color:white">{{ $isEdit ? 'Update Product' : 'Save Product' }}</button>
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
                // Creatable selects with inline typing in the selection area
                $('#brandSelect, #categorySelect, #unitTypeSelect').select2({
                    tags: true,
                    placeholder: '-- Select or create --',
                    allowClear: true,
                    width: 'resolve'
                });
            }

            const isElectronicSel = document.getElementById('isElectronic');
            // Hidden input to submit is_electronic to server
            let isElectronicHidden = document.querySelector('input[name="is_electronic"]');
            if (!isElectronicHidden){
                isElectronicHidden = document.createElement('input');
                isElectronicHidden.type = 'hidden';
                isElectronicHidden.name = 'is_electronic';
                document.getElementById('productForm').appendChild(isElectronicHidden);
            }
            const electronicArea = document.getElementById('electronicArea');
            const serialTemplate = document.getElementById('serialRowTemplate');
            const serialsContainer = document.getElementById('serialsContainer');
            const addSerialBtn = document.getElementById('addSerialBtn');
            const productNameEl = document.getElementById('productName');
            const nameHelpEl = document.getElementById('nameHelp');
            const barcodeEl = document.getElementById('barcodeInput');
            const barcodeErrEl = document.getElementById('barcodeError');
            const barcodeLoading = document.getElementById('barcodeLoading');
            const saveBtn = document.getElementById('saveBtn');
            const formEl = document.getElementById('productForm');
            const checkNameUrlBase = formEl?.dataset?.checkName || (window.location.origin + '/superadmin/products/checkName');
            const checkBarcodeUrlBase = formEl?.dataset?.checkBarcode || (window.location.origin + '/superadmin/products/checkBarcode');
            const warrantyType = document.getElementById('warrantyType');
            const warrantyMonths = document.getElementById('warrantyMonths');

            function toggleElectronicArea(){
                const isElectronic = isElectronicSel && isElectronicSel.value === '1';
                // sync hidden flag
                if (isElectronicHidden) isElectronicHidden.value = isElectronic ? '1' : '0';
                // toggle electronics-only fields
                document.querySelectorAll('.electronic-only').forEach(el => {
                    if (isElectronic) el.classList.remove('d-none');
                    else el.classList.add('d-none');
                });
                // toggle serials/edit-only container if present
                if (electronicArea){
                    if (isElectronic) electronicArea.classList.remove('d-none');
                    else electronicArea.classList.add('d-none');
                }
            }

            if (isElectronicSel){
                isElectronicSel.addEventListener('change', toggleElectronicArea);
            }
            toggleElectronicArea(); // initial call defaults to Non-Electronic

            if (addSerialBtn && serialTemplate && serialsContainer){
                addSerialBtn.addEventListener('click', function(){
                    const node = document.importNode(serialTemplate.content, true);
                    serialsContainer.appendChild(node);
                });
            }

            // delegated remove
            if (serialsContainer){
                serialsContainer.addEventListener('click', function(e){
                    if(e.target.classList.contains('remove-serial')){
                        const row = e.target.closest('.serial-row');
                        if(row) row.remove();
                    }
                });
            }

            // Warranty coverage enable/disable by warranty type
            function updateWarrantyMonthsState(){
                if (!warrantyType) return;
                const disabled = warrantyType.value === 'none';
                warrantyMonths.disabled = disabled;
                if (disabled) {
                    warrantyMonths.value = '';
                }
            }
            if (warrantyType && warrantyMonths){
                warrantyType.addEventListener('change', updateWarrantyMonthsState);
                updateWarrantyMonthsState();
            }

            // Debounce helper
            function debounce(fn, wait){
                let t; return function(...args){
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            // Product name duplicate suggestion (warning)
            const runNameCheck = debounce(async function(){
                if (!productNameEl) return;
                const val = productNameEl.value.trim();
                if (!val){ nameHelpEl.textContent = ''; nameHelpEl.className = 'form-text'; return; }
                try{
                    const url = new URL(checkNameUrlBase, window.location.origin);
                    url.searchParams.set('name', val);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (data && data.ok){
                        if (data.exists || (data.suggestions && data.suggestions.length)){
                            nameHelpEl.className = 'form-text text-warning';
                            const sugg = (data.suggestions || []).filter(s=>s && s.toLowerCase()!==val.toLowerCase()).slice(0,5);
                            nameHelpEl.textContent = data.exists ? 'Similar product exists. Please verify to avoid duplicates.' : (sugg.length ? 'Similar names: ' + sugg.join(', ') : '');
                        } else {
                            nameHelpEl.className = 'form-text text-success';
                            nameHelpEl.textContent = 'Looks good. No similar product found.';
                        }
                    }
                }catch(e){ /* ignore */ }
            }, 500);
            if (productNameEl){
                productNameEl.addEventListener('input', runNameCheck);
                productNameEl.addEventListener('blur', runNameCheck);
            }

            // Barcode uniqueness (error + block save)
            let barcodeAbort;
            const runBarcodeCheck = debounce(async function(){
                if (!barcodeEl) return;
                const val = barcodeEl.value.trim();
                barcodeEl.classList.remove('is-invalid');
                barcodeErrEl.style.display = '';
                if (!val){ if (saveBtn) saveBtn.disabled = false; return; }
                try{
                    if (barcodeLoading) barcodeLoading.classList.remove('d-none');
                    const url = new URL(checkBarcodeUrlBase, window.location.origin);
                    url.searchParams.set('barcode', val);
                    const ignoreId = document.querySelector('form#productForm')?.action.match(/products\/(\d+)/)?.[1];
                    if (ignoreId) url.searchParams.set('ignore_id', ignoreId);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    const dup = !!(data && data.duplicate);
                    if (dup){
                        barcodeEl.classList.add('is-invalid');
                        if (saveBtn) saveBtn.disabled = true;
                    } else {
                        barcodeEl.classList.remove('is-invalid');
                        if (saveBtn) saveBtn.disabled = false;
                    }
                }catch(e){ /* ignore */ }
                finally{
                    if (barcodeLoading) barcodeLoading.classList.add('d-none');
                }
            }, 500);
            if (barcodeEl){
                barcodeEl.addEventListener('input', runBarcodeCheck);
                barcodeEl.addEventListener('blur', runBarcodeCheck);
            }

            // Inline AJAX creation for dropdowns: brand, category, product type, unit type
            const csrfToken = document.querySelector('form#productForm input[name=_token]')?.value;
            async function createResource(url, payload){
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(payload)
                });
                if (!res.ok) throw new Error('Request failed');
                return await res.json();
            }

            function isNumericId(val){ return /^\d+$/.test(String(val)); }

            function wireCreatableSelect(selectId, config){
                const el = document.getElementById(selectId);
                if (!el) return;
                $(el).on('select2:select', async function(e){
                    const selected = e.params.data;
                    const raw = selected && selected.id;
                    if (!raw || isNumericId(raw)) return; // existing id
                    const text = selected.text?.trim();
                    if (!text) return;
                    try {
                        $(el).prop('disabled', true);
                        const data = await createResource(config.url, config.payload(text));
                        // Expect JSON with created resource. Try to infer id/name.
                        const id = data.id || data.data?.id || data[config.key]?.id;
                        const name = data.name || data[config.key]?.name || text;
                        if (id){
                            // replace option with numeric id
                            const newOpt = new Option(name, id, true, true);
                            if (config.dataElectronic != null){
                                newOpt.dataset.electronic = String(config.dataElectronic);
                            }
                            $(el).append(newOpt).trigger('change');
                            // remove the tag option
                            $(el).find('option').each(function(){ if (this.value === raw) this.remove(); });
                        }
                    } catch(err){
                        // Silent fail; keep the tag as-is so submit fallback can create
                    } finally {
                        $(el).prop('disabled', false);
                    }
                });
            }

            // Wire up creatable selects
            wireCreatableSelect('brandSelect', {
                url: '/superadmin/brands',
                key: 'brand',
                payload: (text)=>({ brand_name: text, status: 'active' })
            });
            wireCreatableSelect('categorySelect', {
                url: '/superadmin/categories',
                key: 'category',
                payload: (text)=>({ category_name: text, status: 'active', is_electronic: (isElectronicSel && isElectronicSel.value === '1') ? 1 : 0 })
            });
            wireCreatableSelect('unitTypeSelect', {
                url: '/superadmin/unit-types',
                key: 'unit_type',
                payload: (text)=>({ unit_name: text })
            });
            // Note: Product Type model removed; use category.is_electronic instead.


        });
    </script>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
