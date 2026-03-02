<?php $__env->startSection('title', 'Below Cost Sales'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Items Sold Below Purchase Price</h1>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="<?php echo e(url('/superadmin/sales/below-cost')); ?>" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo e(request('start_date', optional($startDate)->toDateString())); ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo e(request('end_date', optional($endDate)->toDateString())); ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="<?php echo e(url('/superadmin/sales/below-cost')); ?>" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference #</th>
                        <th>Branch</th>
                        <th>Cashier</th>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Purchase Price</th>
                        <th class="text-end">Sold Price</th>
                        <th class="text-end">Purchase Total</th>
                        <th class="text-end">Sold Total</th>
                        <th class="text-end">Loss</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $loss = ($item->purchase_total ?? 0) - ($item->sold_total ?? 0);
                        ?>
                        <tr>
                            <td><?php echo e(optional($item->created_at)->format('Y-m-d H:i')); ?></td>
                            <td>
                                <?php if($item->sale_id): ?>
                                    <a href="<?php echo e(route('superadmin.sales.show', $item->sale_id)); ?>">#<?php echo e($item->sale_id); ?></a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item->branch_name ?? 'N/A'); ?></td>
                            <td><?php echo e($item->cashier_name ?? 'N/A'); ?></td>
                            <td><?php echo e($item->product_name); ?></td>
                            <td class="text-end"><?php echo e($item->quantity); ?></td>
                            <td class="text-end">₱<?php echo e(number_format($item->purchase_price ?? 0, 2)); ?></td>
                            <td class="text-end">₱<?php echo e(number_format($item->sold_unit_price ?? 0, 2)); ?></td>
                            <td class="text-end">₱<?php echo e(number_format($item->purchase_total ?? 0, 2)); ?></td>
                            <td class="text-end">₱<?php echo e(number_format($item->sold_total ?? 0, 2)); ?></td>
                            <td class="text-end text-danger">₱<?php echo e(number_format($loss, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">No items were sold below purchase price for the selected period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="mt-3">
                <?php echo e($items->withQueryString()->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/Admin/sales/below-cost.blade.php ENDPATH**/ ?>