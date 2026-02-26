<?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr>
        <td>
            <input type="checkbox" class="form-check-input product-select" name="selected_ids[]" value="<?php echo e($product->id); ?>">
        </td>
        <td>
            <span style="color: #7f8c8d; font-family: monospace; font-weight: 600;">#<?php echo e($product->id); ?></span>
        </td>
        <td>
            <div class="image-wrapper">
                <?php if($product->image): ?>
                    <img src="<?php echo e(asset('storage/' . $product->image)); ?>" 
                         alt="<?php echo e($product->product_name); ?>" 
                         class="product-image"
                         style="width: 25px !important; height: 25px !important; max-width: none !important; max-height: none !important; object-fit: cover !important;">
                <?php else: ?>
                    <div class="product-image-placeholder">
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#95a5a6" stroke-width="2">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
        </td>
        <td>
            <div>
                <div class="product-name"><?php echo e($product->product_name); ?></div>
                <small style="color: #7f8c8d; font-size: 0.75rem;"><?php echo e($product->barcode); ?></small>
            </div>
        </td>
        <td style="color: #34495e;">
            <?php echo e($product->brand->name ?? 'N/A'); ?>

        </td>
        <td style="color: #34495e;">
            <?php echo e($product->category->name ?? 'N/A'); ?>

        </td>
        <td style="color: #34495e;">
            <?php echo e($product->product_type_id ?? 'N/A'); ?>

        </td>
        <td>
            <?php if($product->unitTypes->isNotEmpty()): ?>
                <?php $__currentLoopData = $product->unitTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unitType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge-unit me-1"><?php echo e($unitType->name); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <span style="color: #95a5a6;">N/A</span>
            <?php endif; ?>
        </td>
        <td>
            <span class="<?php echo e($product->status === 'active' ? 'badge-status-active' : 'badge-status-inactive'); ?>">
                <?php echo e(ucfirst($product->status)); ?>

            </span>
        </td>
        <td>
            <a href="<?php echo e(route('superadmin.products.show', $product->id)); ?>" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                View
            </a>
        </td>
    </tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="9" class="text-center py-5">
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#95a5a6" stroke-width="1.5">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <div class="fw-semibold" style="font-size: 1.125rem; margin-top: 1rem; color: #34495e;">No products found</div>
                <small style="color: #7f8c8d;">Start by adding your first product</small>
            </div>
        </td>
    </tr>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/SuperAdmin/products/_product_table.blade.php ENDPATH**/ ?>