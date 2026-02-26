<?php $__env->startSection('title', 'Sales History'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Sales History</h4>
                    <a href="<?php echo e(route('cashier.sales.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Sale
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search sales..." 
                                   value="<?php echo e(request('search')); ?>" id="searchInput">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from" 
                                   value="<?php echo e(request('date_from')); ?>" placeholder="From">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_to" 
                                   value="<?php echo e(request('date_to')); ?>" placeholder="To">
                        </div>
                          <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
                        </div>
                    </div>
                    <!-- Sales Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="<?php echo e(route('cashier.sales.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc'])); ?>">
                                            Receipt # 
                                            <?php if($sortBy == 'id'): ?>
                                                <i class="fas fa-sort-<?php echo e($sortDirection == 'asc' ? 'up' : 'down'); ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>Date & Time</th>
                                    <th>Customer</th>
                                    <th>
                                        <a href="<?php echo e(route('cashier.sales.index', ['sort_by' => 'total_amount', 'sort_direction' => ($sortBy == 'total_amount' && $sortDirection == 'asc') ? 'desc' : 'asc'])); ?>">
                                            Total Amount
                                            <?php if($sortBy == 'total_amount'): ?>
                                                <i class="fas fa-sort-<?php echo e($sortDirection == 'asc' ? 'up' : 'down'); ?>"></i>
                                            <?php endif; ?>
                                        </a>
                                    </th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo e($sale->id); ?></strong>
                                        <?php if($sale->receipt_group_id): ?>
                                            <br><small class="text-muted"><i class="fas fa-link"></i> Group: <?php echo e($sale->receipt_group_id); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($sale->created_at->format('M d, Y h:i A')); ?></td>
                                    <td><?php echo e($sale->customer_name ?: 'Walk-in'); ?></td>
                                    <td class="text-end">
                                        ₱<?php echo e(number_format($sale->total_amount, 2)); ?>

                                        <?php if($sale->receipt_group_id): ?>
                                            <br><small class="text-muted">Branch <?php echo e($sale->branch->branch_name ?? 'N/A'); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'info' : 'warning')); ?>">
                                            <?php echo e(ucfirst($sale->payment_method)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($sale->status == 'completed' ? 'success' : ($sale->status == 'voided' ? 'danger' : 'secondary')); ?>">
                                            <?php echo e(ucfirst($sale->status)); ?>

                                        </span>
                                    </td>
                                    <td>
    <div class="btn-group btn-group-sm">
        <a href="<?php echo e(route('cashier.pos.receipt', $sale)); ?>" class="btn btn-outline-success" title="Receipt">
            <i class="fas fa-receipt"></i>
        </a>
        <?php if($sale->status !== 'voided'): ?>
            <button class="btn btn-outline-danger" onclick="voidSale(<?php echo e($sale->id); ?>)" title="Void">
                <i class="fas fa-times"></i>
            </button>
        <?php endif; ?>
    </div>
</td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5>No sales found</h5>
                                        <p class="text-muted">Start by creating your first sale.</p>
                                        
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if($sales->hasPages()): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing <?php echo e($sales->firstItem()); ?> to <?php echo e($sales->lastItem()); ?> of <?php echo e($sales->total()); ?> entries
                            </div>
                            <?php echo e($sales->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearFilters() {
    window.location.href = '<?php echo e(route('cashier.sales.index')); ?>';
}

function voidSale(saleId) {
    if(confirm('Are you sure you want to void this sale? This action cannot be undone.')) {
        fetch(`/cashier/sales/${saleId}/void`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Sale voided successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while voiding the sale');
        });
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if(e.key === 'Enter') {
        const params = new URLSearchParams(window.location.search);
        if(this.value) {
            params.set('search', this.value);
        } else {
            params.delete('search');
        }
        window.location.href = '<?php echo e(route('cashier.sales.index')); ?>?' + params.toString();
    }
});

// Filter change handlers
document.querySelectorAll('select[name], input[name]').forEach(input => {
    if(input.type !== 'text') {
        input.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            if(this.value) {
                params.set(this.name, this.value);
            } else {
                params.delete(this.name);
            }
            window.location.href = '<?php echo e(route('cashier.sales.index')); ?>?' + params.toString();
        });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/sales/index.blade.php ENDPATH**/ ?>