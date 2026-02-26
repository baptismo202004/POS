<?php
    // Expected variables from controller:
    // $brands, $categories, $productTypes, $unitTypes, $branches
    // For edit: $product (with serials loaded)
    $isEdit = isset($product);
?>


<?php $__env->startSection('title', 'Products'); ?>

<?php
    $isCashierContext = request()->is('cashier/*');
?>

<?php if($isCashierContext): ?>
<?php $__env->startPush('stylesDashboard'); ?>
    <style>
        .sidebar-fixed {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<?php echo $__env->make('layouts.theme-base', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startSection('content'); ?>

    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0"><?php echo e($isEdit ? 'Edit Product' : 'Add Product'); ?></h2>
                                <a href="<?php echo e($isCashierContext ? route('cashier.products.index') : route('superadmin.products.index')); ?>" class="btn btn-outline" style="border-color:var(--theme-color); color:var(--theme-color)">Back to products</a>
                            </div>

                            <form method="POST" action="<?php echo e($isEdit ? ($isCashierContext ? route('cashier.products.update', $product) : route('superadmin.products.update', $product)) : ($isCashierContext ? route('cashier.products.store') : route('superadmin.products.store'))); ?>" enctype="multipart/form-data" id="productForm">
                                <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                                <?php echo csrf_field(); ?>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="product_name" class="form-control" required value="<?php echo e($isEdit ? $product->product_name : ''); ?>">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Barcode</label>
                                        <input type="text" name="barcode" class="form-control" required value="<?php echo e($isEdit ? $product->barcode : ''); ?>">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Brand</label>
                                        <select name="brand_id" id="brandSelect" class="form-control select2-tags" style="width:100%">
                                            <option value="">-- Select Brand --</option>
                                            <?php $__currentLoopData = $brands ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($brand->id); ?>" <?php echo e($isEdit && $product->brand_id == $brand->id ? 'selected' : ''); ?>><?php echo e($brand->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" id="categorySelect" class="form-control select2-tags" style="width:100%">
                                            <option value="">-- Select Category --</option>
                                            <?php $__currentLoopData = $categories ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($cat->id); ?>" <?php echo e($isEdit && $product->category_id == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Product Type</label>
                                        <select name="product_type_id" id="productType" class="form-control select2-tags" style="width:100%">
                                            <option value="">-- Select Type --</option>
                                            <option value="electronic" data-electronic="1" <?php echo e($isEdit && $product->product_type_id == 'electronic' ? 'selected' : ''); ?>>Electronic</option>
                                            <option value="non-electronic" data-electronic="0" <?php echo e(($isEdit && $product->product_type_id == 'non-electronic') || !$isEdit ? 'selected' : ''); ?>>Non-Electronic</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Unit Types</label>
                                        <select name="unit_type_ids[]" id="unitTypeSelect" class="form-control" style="width:100%" multiple>
                                            <?php
                                                $selectedUnitTypes = old('unit_type_ids', $isEdit ? $product->unitTypes->pluck('id')->all() : []);
                                            ?>
                                            <?php $__currentLoopData = $unitTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($ut->id); ?>" <?php echo e(in_array($ut->id, $selectedUnitTypes) ? 'selected' : ''); ?>><?php echo e($ut->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 electronic-field d-none">
                                        <label class="form-label">Model Number</label>
                                        <input type="text" name="model_number" class="form-control" value="<?php echo e($isEdit ? $product->model_number : ''); ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Image</label>
                                        <input type="file" name="image" class="form-control">
                                        <?php if($isEdit && $product->image): ?>
                                            <small class="text-muted">Current image: <?php echo e(basename($product->image)); ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-md-4 electronic-field d-none">
                                        <label class="form-label">Warranty Type</label>
                                        <select name="warranty_type" class="form-control">
                                            <option value="none" <?php echo e($isEdit && $product->warranty_type == 'none' ? 'selected' : ''); ?>>None</option>
                                            <option value="shop" <?php echo e($isEdit && $product->warranty_type == 'shop' ? 'selected' : ''); ?>>Shop</option>
                                            <option value="manufacturer" <?php echo e($isEdit && $product->warranty_type == 'manufacturer' ? 'selected' : ''); ?>>Manufacturer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Warranty Coverage (months)</label>
                                        <input type="number" name="warranty_coverage_months" min="0" class="form-control" value="<?php echo e($isEdit ? $product->warranty_coverage_months : ''); ?>">
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Voltage Specs</label>
                                        <input type="text" name="voltage_specs" class="form-control" value="<?php echo e($isEdit ? $product->voltage_specs : ''); ?>" placeholder="e.g. 110-220V">
                                    </div>

                                    <div class="col-md-3 non-electronic-field">
                                        <label class="form-label">Branches</label>
                                        <select name="branch_ids[]" id="branchSelect" class="form-control" style="width:100%" multiple>
                                            <?php
                                                $selectedBranches = old('branch_ids', $isEdit ? $product->branches->pluck('id')->all() : []);
                                            ?>
                                            <?php $__currentLoopData = $branches ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($branch->id); ?>" <?php echo e(in_array($branch->id, $selectedBranches) ? 'selected' : ''); ?>><?php echo e($branch->branch_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" <?php echo e($isEdit && $product->status == 'active' ? 'selected' : ''); ?>>Active</option>
                                            <option value="inactive" <?php echo e($isEdit && $product->status == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn" style="background-color:var(--theme-color); color:white"><?php echo e($isEdit ? 'Update Product' : 'Save Product'); ?></button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    

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
        // Make loading functions globally accessible
        function showLoading() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }
        }

        function hideLoading() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<?php echo e($isEdit ? 'Update Product' : 'Save Product'); ?>';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up form handler');
            const productForm = document.getElementById('productForm');
            if (productForm) {
                console.log('Product form found, attaching submit handler');
                productForm.addEventListener('submit', function(e) {
                    console.log('Form submit event triggered');
                    e.preventDefault();
                    e.stopPropagation();

                    // Show loading state immediately
                    showLoading();

                    const form = this;
                    const formData = new FormData(form);
                    const url = form.action;

                    // Log form data for debugging
                    console.log('=== FORM SUBMISSION DEBUG ===');
                    console.log('Form action URL:', url);
                    console.log('Form data entries:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}: ${value}`);
                    }
                    console.log('==============================');

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
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        return response.json().then(data => {
                            console.log('Response data:', data);
                            if (!response.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        console.log('Success response:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '<?php echo e($isEdit ? 'Product updated successfully.' : 'Product created successfully.'); ?>',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '<?php echo e(route('superadmin.products.index')); ?>';
                            });
                        } else if (data.errors) {
                            console.error('Validation errors:', data.errors);
                            Object.keys(data.errors).forEach(key => {
                                const field = document.querySelector(`[name="${key}"]`);
                                if (field) {
                                    field.classList.add('is-invalid');
                                    const error = document.createElement('div');
                                    error.className = 'invalid-feedback';
                                    error.innerText = data.errors[key][0];
                                    field.parentNode.appendChild(error);
                                    console.error(`Field error for ${key}:`, data.errors[key][0]);
                                } else {
                                    console.error(`Field not found for error key: ${key}`);
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
                        console.error('Fetch error:', error);
                        let errorMessage = 'Something went wrong. Please try again.';
                        if (error.message) {
                            errorMessage = error.message;
                        }
                        if (error.errors) {
                            console.error('Server validation errors:', error.errors);
                            errorMessage = 'Validation failed. Check console for details.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'An Error Occurred',
                            text: errorMessage,
                        });
                    })
                    .finally(() => {
                        // Always hide loading state
                        hideLoading();
                    });
                });
            }
        });
    </script>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/SuperAdmin/products/productList.blade.php ENDPATH**/ ?>