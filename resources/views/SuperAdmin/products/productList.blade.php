@php
    // Expected variables from controller:
    // $brands, $categories, $productTypes, $unitTypes, $branches
    // For edit: $product (with serials loaded)
    $isEdit = isset($product);
@endphp

@extends('layouts.app')
@section('title', 'Products')

@include('layouts.theme-base')

@section('content')

    <div class="d-flex min-vh-100">

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
                                            <option value="2" data-electronic="0" {{ ($isEdit && $product->product_type_id == 2) || !$isEdit ? 'selected' : '' }}>Non-Electronic</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Unit Types</label>
                                        <select name="unit_type_ids[]" id="unitTypeSelect" class="form-select" style="width:100%" multiple>
                                            @php
                                                $selectedUnitTypes = old('unit_type_ids', $isEdit ? $product->unitTypes->pluck('id')->all() : []);
                                            @endphp
                                            @foreach($unitTypes ?? [] as $ut)
                                                <option value="{{ $ut->id }}" {{ in_array($ut->id, $selectedUnitTypes) ? 'selected' : '' }}>{{ $ut->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 electronic-field d-none">
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

                                    <div class="col-md-4 electronic-field d-none">
                                        <label class="form-label">Tracking Type</label>
                                        <select name="tracking_type" class="form-select">
                                            <option value="none" {{ $isEdit && $product->tracking_type == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="serial" {{ $isEdit && $product->tracking_type == 'serial' ? 'selected' : '' }}>Serial</option>
                                            <option value="imei" {{ $isEdit && $product->tracking_type == 'imei' ? 'selected' : '' }}>IMEI</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Warranty Type</label>
                                        <select name="warranty_type" class="form-select">
                                            <option value="none" {{ $isEdit && $product->warranty_type == 'none' ? 'selected' : '' }}>None</option>
                                            <option value="shop" {{ $isEdit && $product->warranty_type == 'shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="manufacturer" {{ $isEdit && $product->warranty_type == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Warranty Coverage (months)</label>
                                        <input type="number" name="warranty_coverage_months" min="0" class="form-control" value="{{ $isEdit ? $product->warranty_coverage_months : '' }}">
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Voltage Specs</label>
                                        <input type="text" name="voltage_specs" class="form-control" value="{{ $isEdit ? $product->voltage_specs : '' }}" placeholder="e.g. 110-220V">
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Serial Number</label>
                                        <input type="text" name="serial_number" class="form-control" value="{{ $isEdit ? ($product->serial_number ?? '') : '' }}" placeholder="Enter serial number">
                                    </div>

                                    <div class="col-md-3 electronic-field">
                                        <label class="form-label">Branch</label>
                                        <select name="branch_id" id="branchSelect" class="form-select">
                                            <option value="">-- Select Branch --</option>
                                            @foreach($branches ?? [] as $branch)
                                                <option value="{{ $branch->id }}" {{ $isEdit && isset($product->branch_id) && $product->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        {{-- Debug: Show branch count --}}
                                        @if(isset($branches))
                                            <small class="text-muted">Branches found: {{ count($branches) }}</small>
                                        @else
                                            <small class="text-danger">No branches variable found</small>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="active" {{ $isEdit && $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $isEdit && $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
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

    {{-- Scripts --}}

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

            $('#unitTypeSelect').select2({
                placeholder: '-- Select unit types --',
                allowClear: true,
                width: 'resolve'
            });

            // --- Conditional Visibility Logic ---
            const productType = $('#productType');
            const electronicFields = $('.electronic-field');

            function toggleElectronicFields() {
                const selectedOption = productType.find('option:selected');
                const isElectronic = selectedOption.data('electronic') === 1 || selectedOption.val() === '1';
                
                console.log('Product type changed:', {
                    value: productType.val(),
                    isElectronic: isElectronic,
                    selectedOption: selectedOption.text()
                });
                
                electronicFields.toggleClass('d-none', !isElectronic);
                
                // Re-initialize branch select2 when electronic fields are shown
                if (isElectronic) {
                    // Only initialize select2 if not already initialized
                    if (!$('#branchSelect').hasClass('select2-hidden-accessible')) {
                        console.log('Initializing branch select2 with HTML:', $('#branchSelect').html());
                        $('#branchSelect').select2({
                            placeholder: '-- Select Branch --',
                            allowClear: true,
                            width: 'resolve'
                        });
                    }
                } else {
                    // If electronic fields are hidden, destroy select2 for branchSelect
                    if ($('#branchSelect').hasClass('select2-hidden-accessible')) {
                        $('#branchSelect').select2('destroy');
                    }
                }
                
                // Log field visibility for debugging
                electronicFields.each(function() {
                    console.log('Field visibility:', $(this).find('label').text(), $(this).hasClass('d-none') ? 'hidden' : 'visible');
                });
            }

            // Attach the event listener to select2's change event
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productForm = document.getElementById('productForm');
            if (productForm) {
                productForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const form = this;
                    const formData = new FormData(form);
                    const url = form.action;

                    // Clear previous errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '{{ $isEdit ? "Product updated successfully." : "Product created successfully." }}',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route("superadmin.products.index") }}';
                            });
                        } else if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const field = document.querySelector(`[name="${key}"]`);
                                if (field) {
                                    field.classList.add('is-invalid');
                                    const error = document.createElement('div');
                                    error.className = 'invalid-feedback';
                                    error.innerText = data.errors[key][0];
                                    field.parentNode.appendChild(error);
                                }
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please check the form for errors.',
                            });
                        } else {
                            throw new Error(data.message || 'An unknown error occurred.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        let errorMessage = 'Something went wrong. Please try again.';
                        if (error.message) {
                            errorMessage = error.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'An Error Occurred',
                            text: errorMessage,
                        });
                    });
                });
            }
        });
    </script>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection

