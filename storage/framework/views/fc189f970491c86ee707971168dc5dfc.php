<?php $__env->startSection('title', 'Inventory'); ?>

<?php $__env->startPush('stylesDashboard'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex min-vh-100">
    <div class="container-fluid inventory-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2 class="m-0">Inventory</h2>
                                <input type="text" id="searchInput" class="form-control" style="max-width: 320px" placeholder="Search products..." value="<?php echo e(request('search')); ?>">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><a href="<?php echo e(route('cashier.inventory.index', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page']))); ?>">Product</a></th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th><a href="<?php echo e(route('cashier.inventory.index', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page']))); ?>">Current Stock</a></th>
                                            <th><a href="<?php echo e(route('cashier.inventory.index', ['sort_by' => 'total_sold', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page']))); ?>">Total Sold</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $sortedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><?php echo e($product->product_name); ?></td>
                                                <td><?php echo e($product->brand->brand_name ?? 'N/A'); ?></td>
                                                <td><?php echo e($product->category->category_name ?? 'N/A'); ?></td>
                                                <td class="<?php echo e(($product->current_stock ?? 0) < 10 ? 'text-danger' : ''); ?>"><?php echo e($product->current_stock ?? 0); ?></td>
                                                <td><?php echo e($product->total_sold ?? 0); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No products found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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

    const searchInput = document.getElementById('searchInput');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = searchInput.value;
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                window.location.href = url.toString();
            }, 400);
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/inventory/index.blade.php ENDPATH**/ ?>