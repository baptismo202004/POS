<?php $__env->startSection('title', 'Stock Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Minimal Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 fw-bold">
                Stock Management
            </h5>
            <p class="text-muted mb-0 small">Intelligent inventory monitoring</p>
        </div>
        
        <!-- Right Side Controls -->
        <div class="d-flex align-items-center gap-2">
            <!-- Search Bar -->
            <div class="position-relative">
                <input type="text" class="form-control form-control-sm" id="searchFilterHeader" 
                       placeholder="Search..." style="width: 250px;" value="<?php echo e(request('search')); ?>">
                <i class="fas fa-search position-absolute" style="right: 10px; top: 8px; color: #6c757d; font-size: 12px;"></i>
            </div>
            
            <!-- Filter Button -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdownBtn" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i>
                    Filters
                    <span class="badge bg-secondary ms-1" id="activeFiltersCount">0</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                    <!-- Filter content will be included here -->
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Include Advanced Filters Component -->
    <?php echo $__env->make('superadmin.inventory.stock-filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="<?php echo e(route('superadmin.inventory.stock-management', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])); ?>" class="text-decoration-none">
                                        Product <?php echo e(request('sort_by') == 'product_name' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : ''); ?>

                                    </a>
                                </th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Branch</th>
                                <th>
                                    <a href="<?php echo e(route('superadmin.inventory.stock-management', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])); ?>" class="text-decoration-none">
                                        Current Stock <?php echo e(request('sort_by') == 'current_stock' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : ''); ?>

                                    </a>
                                </th>
                                <th>
                                    <a href="<?php echo e(route('superadmin.inventory.stock-management', ['sort_by' => 'unit_price', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])); ?>" class="text-decoration-none">
                                        Unit Price <?php echo e(request('sort_by') == 'unit_price' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : ''); ?>

                                    </a>
                                </th>
                                <th>Total Value</th>
                                <th>
                                    <a href="<?php echo e(route('superadmin.inventory.stock-management', ['sort_by' => 'last_updated', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])); ?>" class="text-decoration-none">
                                        Last Updated <?php echo e(request('sort_by') == 'last_updated' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : ''); ?>

                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $currentStock = (int) ($product->current_stock ?? 0);
                                    $minStockLevel = (int) ($product->min_stock_level ?? 0);
                                    $maxStockLevel = (int) ($product->max_stock_level ?? 0);
                                    $lowStockThreshold = (int) ($product->low_stock_threshold ?? 0);

                                    $effectiveMin = $minStockLevel > 0 ? $minStockLevel : 10;
                                    $effectiveMax = $maxStockLevel > 0 ? $maxStockLevel : 100;
                                    $effectiveLow = $lowStockThreshold > 0 ? max($effectiveMin, $lowStockThreshold) : $effectiveMin;

                                    if ($currentStock <= 0) {
                                        $stockLevel = 'out_of_stock';
                                    } elseif ($currentStock <= $effectiveMin) {
                                        $stockLevel = 'critical_stock';
                                    } elseif ($currentStock <= $effectiveLow) {
                                        $stockLevel = 'low_stock';
                                    } elseif ($currentStock <= $effectiveMax) {
                                        $stockLevel = 'in_stock';
                                    } else {
                                        $stockLevel = 'overstock';
                                    }

                                    $rowClass = match ($stockLevel) {
                                        'out_of_stock' => 'table-danger',
                                        'critical_stock' => 'table-danger',
                                        'low_stock' => 'table-warning',
                                        'overstock' => 'table-info',
                                        default => '',
                                    };

                                    $badgeClass = match ($stockLevel) {
                                        'out_of_stock' => 'bg-danger',
                                        'critical_stock' => 'bg-danger',
                                        'low_stock' => 'bg-warning text-dark',
                                        'in_stock' => 'bg-success',
                                        'overstock' => 'bg-info text-dark',
                                    };

                                    $stockLabel = match ($stockLevel) {
                                        'out_of_stock' => 'Out of Stock',
                                        'critical_stock' => 'Critical',
                                        'low_stock' => 'Low',
                                        'in_stock' => 'In Stock',
                                        'overstock' => 'Overstock',
                                    };
                                ?>
                                <tr class="<?php echo e($rowClass); ?>">
                                    <td>
                                        <a href="<?php echo e(route('superadmin.products.show', $product->id)); ?>" class="text-decoration-none">
                                            <?php echo e($product->product_name); ?>

                                        </a>
                                        <span class="badge <?php echo e($badgeClass); ?> ms-2"><?php echo e($stockLabel); ?></span>
                                    </td>
                                    <td><?php echo e($product->brand_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($product->category_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($product->branch_name ?? 'Main Branch'); ?></td>
                                    <td>
                                        <span class="badge <?php echo e($badgeClass); ?>">
                                            <?php echo e($currentStock); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e(number_format($product->unit_price ?? 0, 2)); ?></td>
                                    <td><?php echo e(number_format(($product->unit_price ?? 0) * $currentStock, 2)); ?></td>
                                    <td><?php echo e($product->last_stock_update ? \Carbon\Carbon::parse($product->last_stock_update)->format('M d, Y H:i') : 'Never'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="<?php echo e($product->id); ?>" data-product-name="<?php echo e($product->product_name); ?>" data-current-stock="<?php echo e($currentStock); ?>" data-branch-id="<?php echo e($product->branch_id ?? request('branch_id')); ?>" data-branch-name="<?php echo e($product->branch_name ?? ''); ?>" title="Adjust Stock">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-info history-btn" data-bs-toggle="modal" data-bs-target="#stockHistoryModal" data-product-id="<?php echo e($product->id); ?>" data-product-name="<?php echo e($product->product_name); ?>" title="History">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center">No products found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($products->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="adjustProductName"></span> - <span id="adjustBranchName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Stock Adjustment Form -->
                    <div class="col-md-6" id="stockAdjustmentFormColumn">
                        <!-- Current Stock Card -->
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Current Stock Record from <span id="adjustBranchName2"></span></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-primary" id="adjustCurrentStock">0</h4>
                                        <p class="text-muted mb-0">Units available</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Overview Card -->
                        <div class="card mb-3 border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">General Overview</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="otherBranchesStock">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Stock Adjustment Options -->
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Stock Adjustment Options</h6>
                            </div>
                            <div class="card-body">
                                <form id="adjustStockForm" method="POST" action="">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" id="adjustProductId" name="product_id">
                                    <div class="mb-3">
                                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                                        <select class="form-control" id="adjustmentType" name="adjustment_type" required>
                                            <option value="">-- Select Adjustment Type --</option>
                                            <option value="purchase">Add from Purchase</option>
                                            <option value="transfer">Create Stock Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Purchase Option -->
                                    <div id="purchaseOption" style="display: none;">
                                        <div class="mb-3">
                                            <label for="purchase_id" class="form-label">Purchase ID</label>
                                            <select class="form-control" id="purchase_id" name="purchase_id">
                                                <option value="">-- Select Purchase --</option>
                                                <!-- Will be populated dynamically -->
                                            </select>
                                            <small class="form-text text-muted">
                                                Select a purchase that contains this product. Availability is based on
                                                Purchased − Stocked − Sold per purchase batch.
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="purchaseQuantity" class="form-label">Quantity to Add</label>
                                            <input type="number" name="purchase_quantity" id="purchaseQuantity" class="form-control" min="1" required>
                                            <small id="purchaseStats" class="form-text text-muted d-block"></small>
                                            <small id="purchaseWarning" class="form-text text-danger d-none"></small>
                                        </div>
                                    </div>

                                    <!-- Transfer Option -->
                                    <div id="transferOption" style="display: none;">
                                        <div class="mb-3">
                                            <label for="fromBranch" class="form-label">From Branch</label>
                                            <select class="form-control" id="fromBranch" name="from_branch">
                                                <option value="">-- Select Branch --</option>
                                                <!-- Will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="transferQuantity" class="form-label">Quantity to Transfer</label>
                                            <input type="number" name="transfer_quantity" id="transferQuantity" class="form-control" min="1" required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Sales Graph -->
                    <div class="col-md-6" id="salesGraphColumn">
                        <!-- Sales graph will be rendered here automatically -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveAdjustmentBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-labelledby="stockInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockInModalLabel">Stock In - <span id="stockInProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stockInForm">
                    <input type="hidden" id="stockInProductId" name="product_id">
                    <div class="mb-3">
                        <label for="stockInQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="stockInQuantity" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="stockInPrice" class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="stockInPrice" name="price" min="0" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stockInBranch" class="form-label">Branch</label>
                        <select class="form-select" id="stockInBranch" name="branch_id" required>
                            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($branch->id); ?>"><?php echo e($branch->branch_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stockInNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="stockInNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStockIn()">Save Stock In</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock History Modal -->
<div class="modal fade" id="stockHistoryModal" tabindex="-1" aria-labelledby="stockHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockHistoryModalLabel">Stock History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="stockHistoryContent">
                    <!-- Stock history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="/js/stock-management.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/superadmin/inventory/stock-management.blade.php ENDPATH**/ ?>