<?php $__env->startSection('title', 'Stock In'); ?>

<?php $__env->startPush('stylesDashboard'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex min-vh-100">
    <div class="container-fluid stockin-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2 class="m-0">Stock In</h2>
                                <a href="<?php echo e(route('cashier.stockin.create')); ?>" class="btn" style="background-color:var(--theme-color); color:white">Add Stock In</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><a href="<?php echo e(route('cashier.stockin.index', ['sort' => 'product', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])); ?>">Product</a></th>
                                            <th>Purchase Ref</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $stockIns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($stock->product->product_name ?? 'N/A'); ?></td>
                                                <td><?php echo e($stock->purchase->reference_number ?? 'N/A'); ?></td>
                                                <td><?php echo e($stock->quantity); ?></td>
                                                <td><?php echo e(number_format($stock->price, 2)); ?></td>
                                                <td><?php echo e(optional($stock->created_at)->format('M d, Y h:i A')); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No stock records found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <?php echo e($stockIns->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Use standard CashierSidebar from layouts
    <?php if(session('success')): ?>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo e(session('success')); ?>',
            icon: 'success',
            confirmButtonColor: 'var(--theme-color)',
        });
    <?php endif; ?>

    <?php if(session('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Stock-in Error',
            html: '<?php echo session('error'); ?>',
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)',
        });
    <?php endif; ?>
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/stockin/index.blade.php ENDPATH**/ ?>