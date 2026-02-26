@extends('layouts.app')
@section('title', 'Create Product')

@php
    $isCashierContext = request()->is('cashier/*');
    $isEdit = false;
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Use CashierSidebar */
        .main-content {
            margin-left: 280px !important;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --card-hover-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }

        .card-rounded {
            border-radius: 12px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush

@section('content')
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="p-4 card-rounded shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="m-0">Add Product</h2>
                       </div>

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
                                <label class="form-label">Product Type</label>
                                <select name="product_type_id" id="productType" class="form-control select2-tags" style="width:100%">
                                    <option value="">-- Select Type --</option>
                                    <option value="electronic" data-electronic="1">Electronic</option>
                                    <option value="non-electronic" data-electronic="0" selected>Non-Electronic</option>
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
                                    <option value="none">None</option>
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

                        <div class="mt-4 d-flex justify-content-end">
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
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        console.log('jQuery and Select2 loaded successfully');
        
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

        // --- Conditional Visibility Logic ---
        const productType = $('#productType');
        const electronicFields = $('.electronic-field');
        const nonElectronicFields = $('.non-electronic-field');

        function toggleElectronicFields() {
            const selectedOption = productType.find('option:selected');
            const isElectronic = selectedOption.data('electronic') === 1 || selectedOption.val() === 'electronic';
            
            console.log('Product type changed:', {
                value: productType.val(),
                isElectronic: isElectronic,
                selectedOption: selectedOption.text()
            });
            
            // Show electronic fields for electronic products
            electronicFields.toggleClass('d-none', !isElectronic);
            
            // Show non-electronic fields (Branches) for non-electronic products only
            nonElectronicFields.toggleClass('d-none', isElectronic);
            
            // Log field visibility for debugging
            electronicFields.each(function() {
                console.log('Electronic field visibility:', $(this).find('label').text(), $(this).hasClass('d-none') ? 'hidden' : 'visible');
            });
            nonElectronicFields.each(function() {
                console.log('Non-electronic field visibility:', $(this).find('label').text(), $(this).hasClass('d-none') ? 'hidden' : 'visible');
            });
        }

        // Attach event listener to select2's change event
        productType.on('change', function() {
            console.log('Select2 change event triggered');
            toggleElectronicFields();
        });

        // Also listen for select2:select event
        productType.on('select2:select', function(e) {
            console.log('Select2 select event triggered:', e.params.data);
            toggleElectronicFields();
        });

        // Initial call to set the correct visibility on page load
        setTimeout(function() {
            toggleElectronicFields();
        }, 100);
    });
</script>
@endsection
