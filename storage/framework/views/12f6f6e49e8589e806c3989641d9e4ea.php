<?php $__env->startSection('title', 'Expenses Management'); ?>

<?php
    $isCashierContext = request()->is('cashier/*');
?>

<?php $__env->startPush('stylesDashboard'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Use CashierSidebar */
        .main-content {
            margin-left: 280px !important;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --card-hover-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }

        .expense-card {
            border-radius: 15px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .expense-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-5px);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--light-bg), #e2e8f0);
            border-bottom: 2px solid var(--primary-color);
            padding: 20px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .table-custom {
            border-radius: 10px;
            overflow: hidden;
        }

        .table-custom thead {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
        }

        .table-custom th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-custom tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .category-badge {
            background: var(--warning-color);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .receipt-badge {
            background: var(--secondary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Expenses Management</h2>
                <p class="text-muted mb-0">Track and manage branch expenses</p>
            </div>
            <div>
                <a href="<?php echo e(route('cashier.expenses.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Expense
                </a>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">₱<?php echo e(number_format((float) ($todayExpenses->total_amount ?? 0), 2)); ?></div>
                    <div class="stats-label">Today's Expenses</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number"><?php echo e(number_format((float) ($monthlyExpenses->total_expenses ?? 0))); ?></div>
                    <div class="stats-label">This Month (Count)</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="stats-number">₱<?php echo e(number_format((float) ($monthlyExpenses->total_amount ?? 0), 2)); ?></div>
                    <div class="stats-label">This Month (Amount)</div>
                </div>
            </div>
        </div>

        <!-- Expenses Table -->
        <div class="card expense-card">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>Expense Records
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Supplier</th>
                                <th>Payment</th>
                                <th>Amount</th>
                                <th>Receipt</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($expense->expense_date?->format('M d, Y')); ?></td>
                                    <td>
                                        <div>
                                            <strong><?php echo e($expense->description); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge"><?php echo e($expense->category?->name ?? 'N/A'); ?></span>
                                    </td>
                                    <td><?php echo e($expense->supplier?->supplier_name ?? '—'); ?></td>
                                    <td class="text-capitalize"><?php echo e($expense->payment_method); ?></td>
                                    <td class="fw-bold text-danger">-₱<?php echo e(number_format($expense->amount, 2)); ?></td>
                                    <td>
                                        <?php if($expense->receipt_path): ?>
                                            <a class="receipt-badge text-decoration-none" href="<?php echo e(asset('storage/'.$expense->receipt_path)); ?>" target="_blank" rel="noopener">
                                                View
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No receipt</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(route('cashier.expenses.edit', $expense->id)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('cashier.expenses.destroy', $expense->id)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this expense?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <h5>No Expenses Recorded</h5>
                                        <p>No expenses have been recorded yet. Add your first expense to get started.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($expenses->hasPages()): ?>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Showing <?php echo e($expenses->firstItem()); ?> to <?php echo e($expenses->lastItem()); ?> of <?php echo e($expenses->total()); ?> expenses</span>
                        <?php echo e($expenses->links()); ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/expenses/index.blade.php ENDPATH**/ ?>