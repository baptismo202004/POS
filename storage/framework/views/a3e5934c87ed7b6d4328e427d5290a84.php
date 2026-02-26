<?php $__env->startSection('title', 'Customer Details'); ?>

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
        
        .customer-header {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border-radius: 12px;
        }
        
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }
        
        .status-active {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .status-blocked {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Customer Details</h2>
                <p class="text-muted mb-0">View customer information and history</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('cashier.customers.edit', $customer->id)); ?>" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit
                </a>
                <a href="<?php echo e(route('cashier.customers.index')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="customer-header card mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px;">
                            <?php echo e(strtoupper(substr($customer->full_name, 0, 1))); ?>

                        </div>
                    </div>
                    <div class="col-md-10">
                        <h3 class="mb-2"><?php echo e($customer->full_name); ?></h3>
                        <div class="d-flex align-items-center gap-3">
                            <span class="status-badge <?php echo e($customer->status == 'active' ? 'status-active' : 'status-blocked'); ?>">
                                <?php echo e(ucfirst($customer->status)); ?>

                            </span>
                            <div class="text-white">
                                <i class="fas fa-credit-card me-1"></i>
                                Credit Limit: ₱<?php echo e(number_format($customer->max_credit_limit, 2)); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-card card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-address-card me-2 text-primary"></i>Contact Information
                        </h5>
                        <div class="mt-3">
                            <?php if($customer->phone): ?>
                                <p class="mb-2">
                                    <strong>Phone:</strong> <?php echo e($customer->phone); ?>

                                </p>
                            <?php endif; ?>
                            <?php if($customer->email): ?>
                                <p class="mb-2">
                                    <strong>Email:</strong> <?php echo e($customer->email); ?>

                                </p>
                            <?php endif; ?>
                            <?php if($customer->address): ?>
                                <p class="mb-0">
                                    <strong>Address:</strong> <?php echo e($customer->address); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-info-circle me-2 text-primary"></i>Account Information
                        </h5>
                        <div class="mt-3">
                            <p class="mb-2">
                                <strong>Customer ID:</strong> #<?php echo e(str_pad($customer->id, 6, '0', STR_PAD_LEFT)); ?>

                            </p>
                            <p class="mb-2">
                                <strong>Status:</strong> 
                                <span class="status-badge <?php echo e($customer->status == 'active' ? 'status-active' : 'status-blocked'); ?>">
                                    <?php echo e(ucfirst($customer->status)); ?>

                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>Credit Limit:</strong> ₱<?php echo e(number_format($customer->max_credit_limit, 2)); ?>

                            </p>
                            <p class="mb-0">
                                <strong>Created:</strong> <?php echo e($customer->created_at->format('M d, Y H:i:s')); ?>

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity (Placeholder for future implementation) -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-history me-2 text-primary"></i>Recent Activity
                </h5>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-clock fa-2x mb-3"></i>
                    <p>Customer activity tracking will be available soon.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/customers/show.blade.php ENDPATH**/ ?>