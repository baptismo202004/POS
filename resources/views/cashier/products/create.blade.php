@extends('layouts.app')
@section('title', 'Create Product')

@php
    $isCashierContext = request()->is('cashier/*');
    $isEdit = false;
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

        .products-create-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .products-create-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .products-create-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .products-create-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .products-create-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .products-create-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .products-create-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .products-create-page .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .products-create-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .products-create-page .ph-icon {
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
        .products-create-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .products-create-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .products-create-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .products-create-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .products-create-page .c-head::after {
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
        .products-create-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 85% 50%, rgba(0,229,255,0.14), transparent);
            pointer-events:none;
        }
        .products-create-page .c-head-title {
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
        .products-create-page .c-head-title i { color:rgba(0,229,255,.85); }

        .products-create-page .card-body-pad { padding: 18px 22px 22px; }

        .card-rounded {
            border-radius: 12px;
        }

        .products-create-page .form-label {
            font-weight: 700;
            color: var(--navy);
            font-family: 'Nunito', sans-serif;
            font-size: 13px;
        }

        .products-create-page .form-control {
            border-radius: 11px;
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--text);
            font-size: 13px;
            padding: 10px 12px;
        }
        .products-create-page .form-control:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }

        .products-create-page .btn {
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

        .products-create-page .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .products-create-page .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }

        .products-create-page .btn-secondary {
            background: #fff;
            color: var(--navy);
            border: 1.5px solid var(--border);
        }
        .products-create-page .btn-secondary:hover { border-color: var(--blue-lt); background: rgba(66,165,245,0.06); }

        .products-create-page .alert {
            border-radius: 14px;
            border: 1px solid transparent;
        }
        .products-create-page .alert-success { border-color: rgba(16,185,129,0.24); }
        .products-create-page .alert-danger { border-color: rgba(239,68,68,0.20); }

        .products-create-page .text-muted { color: var(--muted) !important; }

        .products-create-page .actions-row {
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
<div class="products-create-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-box"></i></div>
                <div>
                    <div class="ph-title">Add Product</div>
                    <div class="ph-sub">Create a new item in your catalog</div>
                </div>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-plus"></i> Product Details</div>
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

                    <form method="POST" action="{{ route('cashier.products.store') }}" enctype="multipart/form-data" id="productForm">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="product_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="barcode" class="form-control" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" id="brandSelect" class="form-control select2-tags" style="width:100%">
                                    <option value="">-- Select Brand --</option>
                                    @foreach($brands ?? [] as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" id="categorySelect" class="form-control select2-tags" style="width:100%">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Unit Types</label>
                                <select name="unit_type_ids[]" id="unitTypeSelect" class="form-control" style="width:100%" multiple>
                                    @foreach($unitTypes ?? [] as $ut)
                                        <option value="{{ $ut->id }}">{{ $ut->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 electronic-field d-none">
                                <label class="form-label">Model Number</label>
                                <input type="text" name="model_number" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control">
                                <small class="text-muted">Allowed: JPG, PNG, GIF (Max 2MB)</small>
                            </div>

                            <div class="col-md-4 electronic-field d-none">
                                <label class="form-label">Warranty Type</label>
                                <select name="warranty_type" class="form-control">
                                    <option value="shop">Shop</option>
                                    <option value="manufacturer">Manufacturer</option>
                                </select>
                            </div>

                            <div class="col-md-3 electronic-field d-none">
                                <label class="form-label">Warranty Coverage (months)</label>
                                <input type="number" name="warranty_coverage_months" min="0" class="form-control" placeholder="e.g. 12">
                            </div>

                            <div class="col-md-3 electronic-field d-none">
                                <label class="form-label">Voltage Specs</label>
                                <input type="text" name="voltage_specs" class="form-control" placeholder="e.g. 110-220V">
                            </div>

                            <div class="col-md-3 non-electronic-field">
                                <label class="form-label">Branches</label>
                                @if($userBranch)
                                    <input type="text" class="form-control" value="{{ $userBranch->branch_name }}" readonly>
                                    <input type="hidden" name="branch_ids[]" value="{{ $userBranch->id }}">
                                @else
                                    <select name="branch_ids[]" id="branchSelect" class="form-control" style="width:100%" multiple>
                                        @foreach($branches ?? [] as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 actions-row">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Product
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#brandSelect, #categorySelect').select2({
            tags: true,
            placeholder: '-- Select or create --',
            allowClear: true,
            width: 'resolve'
        });

        $('#unitTypeSelect, #branchSelect').select2({
            placeholder: '-- Select items --',
            allowClear: true,
            width: 'resolve'
        });

        const categoriesMeta = {};
        @foreach($categories ?? [] as $cat)
            categoriesMeta["{{ $cat->id }}"] = {
                category_type: "{{ $cat->category_type ?? 'non_electronic' }}"
            };
        @endforeach

        const electronicFields = $('.electronic-field');
        const nonElectronicFields = $('.non-electronic-field');
        const categorySelect = $('#categorySelect');

        function isElectronicCategoryType(categoryType) {
            return categoryType === 'electronic_with_serial' || categoryType === 'electronic_without_serial';
        }

        function toggleFieldsByCategory() {
            const raw = categorySelect.val();
            const meta = categoriesMeta[String(raw)] || { category_type: 'non_electronic' };
            const isElectronic = isElectronicCategoryType(meta.category_type);

            if (isElectronic) {
                electronicFields.removeClass('d-none');
                nonElectronicFields.addClass('d-none');
            } else {
                electronicFields.addClass('d-none');
                nonElectronicFields.removeClass('d-none');
            }
        }

        categorySelect.on('change', toggleFieldsByCategory);
        toggleFieldsByCategory();
    });
</script>
@endpush
@endsection
