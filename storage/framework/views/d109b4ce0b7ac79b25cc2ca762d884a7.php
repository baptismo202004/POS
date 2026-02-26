<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card card-rounded shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0">Refunds & Returns</h4>
                <p class="mb-0 text-muted">Overview of your refund and return transactions</p>
            </div>
            <a href="<?php echo e(route('admin.sales.management.index')); ?>" class="btn btn-primary">Go to Sales</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Refunds</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo e(number_format($todayRefunds->total_refund_amount ?? 0, 2)); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-undo fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items Refunded Today</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($todayRefunds->total_items ?? 0); ?> items</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month's Refunds</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?php echo e(number_format($monthlyRefunds->total_refund_amount ?? 0, 2)); ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Refunds Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Refunds</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($refund->created_at->format('M d, Y h:i A')); ?></td>
                                <td><?php echo e($refund->product->product_name ?? 'N/A'); ?></td>
                                <td><?php echo e($refund->quantity_refunded); ?></td>
                                <td>₱<?php echo e(number_format($refund->refund_amount, 2)); ?></td>
                                <td><?php echo e($refund->reason); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($refund->status == 'approved' ? 'success' : ($refund->status == 'rejected' ? 'danger' : 'warning')); ?>">
                                        <?php echo e(ucfirst($refund->status)); ?>

                                    </span>
                                </td>
                                <td><?php echo e($refund->cashier->name ?? 'Unknown'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center">No refunds found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($refunds->hasPages()): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php echo e($refunds->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/Admin/refunds/index.blade.php ENDPATH**/ ?>