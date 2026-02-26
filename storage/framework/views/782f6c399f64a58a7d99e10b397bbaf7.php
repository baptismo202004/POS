<?php $__env->startSection('content'); ?>
    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">Purchases</h2>
                                <a href="<?php echo e(route('superadmin.purchases.create')); ?>" class="btn" style="background-color:var(--theme-color); color:white">Add New Purchase</a>
                            </div>

                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Reference Number</th>
                                                <th>Purchase Date</th>
                                                <th>Payment Status</th>
                                                <th>Items</th>
                                                <th>Total Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td><?php echo e($purchase->reference_number ?: 'N/A'); ?></td>
                                                    <td>
                                                        <a href="<?php echo e(route('superadmin.purchases.show', $purchase)); ?>">
                                                            <?php echo e(optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A'); ?>

                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?php echo e($purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark'); ?>">
                                                            <?php echo e(ucfirst($purchase->payment_status)); ?>

                                                        </span>
                                                    </td>
                                                    <td><?php echo e($purchase->items->count()); ?> item(s)</td>
                                                    <td><strong>₱<?php echo e(number_format($purchase->total_cost, 2)); ?></strong></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">No purchases found.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                
                                <div class="d-flex justify-content-center mt-4">
                                    <?php echo e($purchases->links()); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(session('success')): ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo e(session('success')); ?>',
                    icon: 'success',
                    confirmButtonColor: 'var(--theme-color)',
                });
            <?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/SuperAdmin/purchases/index.blade.php ENDPATH**/ ?>