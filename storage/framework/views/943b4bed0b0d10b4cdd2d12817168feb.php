<?php $__env->startSection('title', 'Sales Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">Sales Reports</h4>
                        <small class="opacity-75">Branch-based summary (this month)</small>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="text-muted small mb-2">Today&apos;s Sales</div>
                                            <div class="fs-4 fw-bold text-success">
                                                ₱<?php echo e(number_format((float) ($todaySales ?? 0), 2)); ?>

                                            </div>
                                        </div>
                                        <div class="text-success">
                                            <i class="fas fa-calendar-day fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="text-muted small mb-2">This Month&apos;s Sales</div>
                                            <div class="fs-4 fw-bold text-primary">
                                                ₱<?php echo e(number_format((float) ($monthlySales ?? 0), 2)); ?>

                                            </div>
                                        </div>
                                        <div class="text-primary">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="text-muted small mb-2">Top Products Listed</div>
                                            <div class="fs-4 fw-bold">
                                                <?php echo e($topProducts?->count() ?? 0); ?>

                                            </div>
                                        </div>
                                        <div class="text-warning">
                                            <i class="fas fa-boxes-stacked fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-star me-2"></i>Top Products (This Month)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 52px;">#</th>
                                            <th>Product</th>
                                            <th class="text-end">Units Sold</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td class="text-muted"><?php echo e($index + 1); ?></td>
                                                <td class="fw-semibold"><?php echo e($row->product_name); ?></td>
                                                <td class="text-end"><?php echo e(number_format((float) $row->total_sold)); ?></td>
                                                <td class="text-end">₱<?php echo e(number_format((float) $row->revenue, 2)); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>No sales data for this month yet.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/sales/reports.blade.php ENDPATH**/ ?>