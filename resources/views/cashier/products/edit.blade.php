@extends('layouts.app')
@section('title', 'Edit Product')

@php
    $isCashierContext = request()->is('cashier/*');
    $isEdit = true;
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Use CashierSidebar */
        .main-content {
            margin-left: 280px !important;
        }

        :root {
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --cyan:    #00E5FF;
            --green:   #10b981;
            --red:     #ef4444;
            --amber:   #f59e0b;
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.12);
            --text:    #1a2744;
            --muted:   #6b84aa;
            --shadow:  0 4px 24px rgba(13,71,161,0.08);
        }

        .products-edit-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .products-edit-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .products-edit-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .products-edit-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .products-edit-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .products-edit-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .products-edit-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .products-edit-page .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .products-edit-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .products-edit-page .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
        }
        .products-edit-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .products-edit-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .products-edit-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .products-edit-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .products-edit-page .c-head::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top:-90px;
            right:-50px;
            pointer-events:none;
        }
        .products-edit-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 85% 50%, rgba(0,229,255,0.14), transparent);
            pointer-events:none;
        }
        .products-edit-page .c-head-title {
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
        .products-edit-page .c-head-title i { color:rgba(0,229,255,.85); }

        .products-edit-page .card-body-pad { padding: 18px 22px 22px; }

        .products-edit-page .form-label {
            font-weight: 700;
            color: var(--navy);
            font-family: 'Nunito', sans-serif;
            font-size: 13px;
        }

        .products-edit-page .form-control {
            border-radius: 11px;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--text);
            font-size: 13px;
            padding: 10px 12px;
        }
        .products-edit-page .form-control:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }

        .products-edit-page .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .products-edit-page .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .products-edit-page .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }

        .products-edit-page .btn-secondary {
            background: #fff;
            color: var(--navy);
            border: 1.5px solid var(--border);
        }
        .products-edit-page .btn-secondary:hover { border-color: var(--blue-lt); background: rgba(66,165,245,0.06); }

        .products-edit-page .alert {
            border-radius: 14px;
            border: 1px solid transparent;
        }
        .products-edit-page .alert-success { border-color: rgba(16,185,129,0.24); }
        .products-edit-page .alert-danger { border-color: rgba(239,68,68,0.20); }

        .products-edit-page .text-muted { color: var(--muted) !important; }

        .products-edit-page .actions-row {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .select2-container--default .select2-selection--single {
            border: 1.5px solid var(--border);
            border-radius: 11px;
            min-height: 40px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            color: var(--text);
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1.5px solid var(--border);
            border-radius: 11px;
            min-height: 40px;
            padding: 4px 8px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: rgba(13,71,161,0.08);
            border: 1px solid var(--border);
            color: var(--navy);
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 12px;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: var(--navy);
            margin-right: 6px;
        }
        .select2-dropdown {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        .select2-results__option { font-size: 13px; }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: rgba(25,118,210,0.10);
            color: var(--navy);
        }
    </style>
@endpush

@section('content')
<div class="products-edit-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-box"></i></div>
                <div>
                    <div class="ph-title">Edit Product</div>
                    <div class="ph-sub">Update product information</div>
                </div>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-pen"></i> Product Details</div>
            </div>

            <div class="card-body-pad">
                    @if (session('success'))
                        <div class="alert alert-success mb-3" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger mb-3" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('cashier.products.update', $product->id) }}" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="product_name" class="form-control" value="{{ old('product_name', $product->product_name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" id="brandSelect" class="form-control select2-tags" style="width:100%">
                                    <option value="">-- Select Brand --</option>
                                    @foreach($brands ?? [] as $brand)
                                        <option value="{{ $brand->id }}" @selected((string)old('brand_id', $product->brand_id) === (string)$brand->id)>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" id="categorySelect" class="form-control select2-tags" style="width:100%">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}" @selected((string)old('category_id', $product->category_id) === (string)$cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 product-type-field">
                                <label class="form-label">Product Type</label>
                                <select name="product_type_id" id="productType" class="form-control select2-tags" style="width:100%" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="electronic" data-electronic="1" @selected(old('product_type_id', $product->product_type_id) === 'electronic')>Electronic</option>
                                    <option value="non-electronic" data-electronic="0" @selected(old('product_type_id', $product->product_type_id) === 'non-electronic')>Non-Electronic</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Unit Types</label>
                                <select name="unit_type_ids[]" id="unitTypeSelect" class="form-control" style="width:100%" multiple>
                                    @php
                                        $selectedUnitTypes = collect(old('unit_type_ids', $product->unitTypes->pluck('id')->all() ?? []))->map(fn($v) => (string)$v)->all();
                                    @endphp
                                    @foreach($unitTypes ?? [] as $ut)
                                        <option value="{{ $ut->id }}" @selected(in_array((string)$ut->id, $selectedUnitTypes, true))>{{ $ut->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 electronic-field d-none">
                                <label class="form-label">Model Number</label>
                                <input type="text" name="model_number" class="form-control" value="{{ old('model_number', $product->model_number) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control">
                                <small class="text-muted">Allowed: JPG, PNG, GIF (Max 2MB)</small>
                            </div>

                            <div class="col-md-4 electronic-field d-none">
                                <label class="form-label">Warranty Type</label>
                                <select name="warranty_type" class="form-control">
                                    <option value="none" @selected(old('warranty_type', $product->warranty_type) === 'none')>None</option>
                                    <option value="shop" @selected(old('warranty_type', $product->warranty_type) === 'shop')>Shop</option>
                                    <option value="manufacturer" @selected(old('warranty_type', $product->warranty_type) === 'manufacturer')>Manufacturer</option>
                                </select>
                            </div>

                            <div class="col-md-3 electronic-field d-none">
                                <label class="form-label">Warranty Coverage (months)</label>
                                <input type="number" name="warranty_coverage_months" min="0" class="form-control" placeholder="e.g. 12" value="{{ old('warranty_coverage_months', $product->warranty_coverage_months) }}">
                            </div>

                            <div class="col-md-3 electronic-field d-none">
                                <label class="form-label">Voltage Specs</label>
                                <input type="text" name="voltage_specs" class="form-control" placeholder="e.g. 110-220V" value="{{ old('voltage_specs', $product->voltage_specs) }}">
                            </div>

                            <div class="col-md-3 non-electronic-field">
                                <label class="form-label">Branches</label>
                                @php
                                    $defaultBranches = $product->branches->pluck('id')->all();
                                    if (empty($defaultBranches) && !empty($product->branch_id)) {
                                        $defaultBranches = [$product->branch_id];
                                    }
                                    if (empty($defaultBranches) && isset($userBranch) && $userBranch) {
                                        $defaultBranches = [$userBranch->id];
                                    }
                                    $selectedBranches = collect(old('branch_ids', $defaultBranches ?? []))->map(fn($v) => (string)$v)->all();
                                @endphp
                                @if(isset($userBranch) && $userBranch)
                                    <input type="text" class="form-control" value="{{ $userBranch->branch_name }}" readonly>
                                    <input type="hidden" name="branch_ids[]" value="{{ $userBranch->id }}">
                                @else
                                    <select name="branch_ids[]" id="branchSelect" class="form-control" style="width:100%" multiple>
                                        @foreach($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}" @selected(in_array((string)$branch->id, $selectedBranches, true))>{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" @selected(old('status', $product->status) === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status', $product->status) === 'inactive')>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 actions-row">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Product
                            </button>
                            <a href="{{ route('cashier.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize all select2 dropdowns
        $('#brandSelect, #categorySelect').select2({
            tags: true,
            placeholder: '-- Select or create --',
            allowClear: true,
            width: 'resolve'
        });

        $('#productType').select2({
            placeholder: '-- Select --',
            allowClear: true,
            width: 'resolve',
            minimumResultsForSearch: Infinity
        });

        $('#unitTypeSelect, #branchSelect').select2({
            placeholder: '-- Select items --',
            allowClear: true,
            width: 'resolve'
        });

        const productType = $('#productType');
        const electronicFields = $('.electronic-field');
        const nonElectronicFields = $('.non-electronic-field');
        const categorySelect = $('#categorySelect');

        function requiresElectronicCategory(categoryName) {
            if (!categoryName) return false;
            const n = String(categoryName).trim().toLowerCase();
            return n === 'electronics' || n === 'computers' || n === 'appliances';
        }

        function toggleProductTypeField() {
            const categoryName = categorySelect.find('option:selected').text();
            const required = requiresElectronicCategory(categoryName);

            if (required) {
                productType.val('electronic').trigger('change');
            } else {
                productType.val('non-electronic').trigger('change');
            }
        }

        function toggleElectronicFields() {
            const selectedOption = productType.find('option:selected');
            const isElectronic = selectedOption.data('electronic') === 1 || selectedOption.val() === 'electronic';

            electronicFields.toggleClass('d-none', !isElectronic);
            nonElectronicFields.toggleClass('d-none', isElectronic);
        }

        productType.on('change', function() {
            toggleElectronicFields();
        });

        setTimeout(function() {
            toggleProductTypeField();
            toggleElectronicFields();
        }, 100);

        categorySelect.on('change', function() {
            toggleProductTypeField();
            toggleElectronicFields();
        });
    });
</script>
@endsection
