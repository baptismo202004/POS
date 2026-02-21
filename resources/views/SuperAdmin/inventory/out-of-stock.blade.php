@extends('layouts.app')
@section('title', 'Out of Stock Products')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
.branch-list {
    max-height: 80px;
    overflow-y: auto;
}

.branch-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 2px 0;
    border-bottom: 1px solid #f0f0f0;
}

.branch-item:last-child {
    border-bottom: none;
}

.branch-name {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6c757d;
    min-width: 80px;
}

.stock-info {
    display: flex;
    align-items: center;
    gap: 4px;
}

.current {
    font-weight: 600;
    font-size: 0.8rem;
}

.needed {
    font-size: 0.7rem;
    color: #dc3545;
    font-weight: 600;
    background: #f8d7da;
    padding: 1px 4px;
    border-radius: 3px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="card card-rounded shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h4 class="m-0 me-3">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                        Out of Stock Products
                    </h4>
                    <span class="badge bg-danger">{{ $products->total() }} items</span>
                </div>
                <div class="d-flex align-items-center">
                    <a href="{{ route('superadmin.inventory.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to Inventory
                    </a>
                    <select id="branchFilter" class="form-select me-2" onchange="filterByBranch(this.value)">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                    <button id="exportPdfBtn" class="btn btn-success me-2" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </button>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Stock Alert:</strong> Showing products with 15 units or less in stock. These items need immediate restocking.
                </div>
                
                @if($products->total() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-success">Great News!</h5>
                        <p class="text-muted">All products have sufficient stock levels (more than 15 units).</p>
                        <a href="{{ route('superadmin.inventory.index') }}" class="btn btn-success">
                            <i class="fas fa-box me-1"></i> View Full Inventory
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ route('superadmin.inventory.out-of-stock', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            Product {{ request('sort_by') == 'product_name' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    </th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Branch</th>
                                    <th>
                                        <a href="{{ route('superadmin.inventory.out-of-stock', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            Current Stock {{ request('sort_by') == 'current_stock' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('superadmin.inventory.out-of-stock', ['sort_by' => 'total_sold', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            Total Sold {{ request('sort_by') == 'total_sold' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('superadmin.inventory.out-of-stock', ['sort_by' => 'total_revenue', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                            Total Revenue {{ request('sort_by') == 'total_revenue' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    </th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr class="{{ $product->current_stock <= 5 ? 'table-danger' : 'table-warning' }}">
                                        <td>
                                            <a href="{{ route('superadmin.products.show', $product->id) }}" class="text-decoration-none">
                                                {{ $product->product_name }}
                                            </a>
                                        </td>
                                        <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                        <td>{{ $product->branch_name ?? 'Multiple Branches' }}</td>
                                        <td>
                                            <span class="badge {{ $product->current_stock <= 5 ? 'bg-danger' : 'bg-warning' }} fs-6">
                                                {{ $product->current_stock }} units
                                            </span>
                                        </td>
                                        <td>{{ $product->total_sold }}</td>
                                        <td>{{ number_format($product->total_revenue, 2) }}</td>
                                        <td>
                                            @if($product->current_stock == 0)
                                                <span class="badge bg-danger">Out of Stock</span>
                                            @elseif($product->current_stock <= 5)
                                                <span class="badge bg-danger">Critical</span>
                                            @else
                                                <span class="badge bg-warning">Low Stock</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" data-current-stock="{{ $product->current_stock }}" data-branch-id="{{ $product->branch_id }}" data-branch-name="{{ $product->branch_name }}" title="Adjust Stock">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route('superadmin.products.show', $product->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No products found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $products->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="productName"></span> - <span id="branchName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Stock Adjustment Form -->
                    <div class="col-md-6" id="stockAdjustmentFormColumn">
                        <!-- Current Stock Card -->
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Current Stock Record from <span id="branchName"></span></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-primary" id="currentStockDisplay">0</h4>
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
                                    @csrf
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
                                            <small class="form-text text-muted">Select a purchase that contains this product</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="purchaseQuantity" class="form-label">Quantity to Add</label>
                                            <input type="number" name="purchase_quantity" id="purchaseQuantity" class="form-control" min="1" required>
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

<!-- Bootstrap JS bundle -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Function to update dashboard alerts
        function updateDashboardAlerts(outOfStockCount) {
            try {
                console.log('Updating dashboard alerts with count:', outOfStockCount);
                
                const totalAlertsElement = document.getElementById('totalAlertsCount') || 
                                      document.querySelector('[id="totalAlertsCount"]') ||
                                      document.querySelector('.widget-badge.alert-count');
                
                if (totalAlertsElement) {
                    fetch('/dashboard/widgets', {
                        method: 'GET',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest', 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.alerts) {
                            const totalAlerts = data.alerts.outOfStock + data.alerts.negativeProfit + data.alerts.voidedSales + data.alerts.belowCostSales + data.alerts.highDiscountUsage;
                            totalAlertsElement.textContent = totalAlerts;
                            
                            const alertsList = document.getElementById('alertsList') || 
                                               document.querySelector('[id="alertsList"]') ||
                                               document.querySelector('.alerts-list');
                            
                            if (alertsList) {
                                const alertItems = [];
                                
                                if (data.alerts.outOfStock > 0) {
                                    alertItems.push(`<div class="alert-item critical clickable" onclick="window.location.href='/superadmin/inventory/out-of-stock'"><i class="fas fa-exclamation-triangle alert-icon" style="color:#E91E63"></i><div class="alert-content"><div class="alert-title">${data.alerts.outOfStock} items out of stock</div><div class="alert-description">Restock needed</div></div></div>`);
                                }
                                
                                alertsList.innerHTML = alertItems.length > 0 ? alertItems.join('') : '<div class="alert-item" style="border-left-color:#43A047;background:rgba(67,160,71,0.05)"><i class="fas fa-check-circle alert-icon" style="color:#43A047"></i><div class="alert-content"><div class="alert-title">No alerts</div><div class="alert-description">All systems normal</div></div></div>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error updating dashboard alerts:', error);
                    });
                }
            } catch (error) {
                console.error('Error in updateDashboardAlerts:', error);
            }
        }

        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            showConfirmButton: true,
            confirmButtonText: 'Great!',
            confirmButtonColor: '#02C39A',
            backdrop: `
                rgba(2, 195, 154, 0.1)
                left top
                no-repeat
            `,
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            },
            position: 'top-center',
            toast: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        @endif

        const adjustStockModal = document.getElementById('adjustStockModal');
        let currentProductData = null;
        let salesData = null;

        adjustStockModal.addEventListener('show.bs.modal', function (event) {
            console.log('� [MODAL] Modal opened');
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const currentStock = button.getAttribute('data-current-stock');
            const branchId = button.getAttribute('data-branch-id');
            const branchName = button.getAttribute('data-branch-name');

            // Set product data
            currentProductData = {
                id: productId,
                name: productName,
                currentStock: currentStock,
                branchId: branchId,
                branchName: branchName
            };

            // Update modal content
            document.getElementById('productName').textContent = productName;
            document.getElementById('branchName').textContent = branchName;
            document.getElementById('currentStockDisplay').textContent = currentStock;

            console.log('🎨 [UI] Modal header updated with product information');

            // Load other branches stock data
            console.log('🔄 [API] Starting to load other branches stock data...');
            loadOtherBranchesStock(productId, branchId);
            
            // Load purchase options for this product
            console.log('🔄 [API] Starting to load purchase options...');
            loadPurchaseOptions(productId);
            
            // Load branch options for transfer
            console.log('🔄 [API] Starting to load branch options for transfer...');
            loadBranchOptions(branchId);
            
            // Load and display sales data automatically
            loadAndDisplaySalesData(productId);
        });

        // Load other branches stock data
        function loadOtherBranchesStock(productId, currentBranchId) {
            console.log('📊 [BRANCH_STOCK] Loading stock data for product:', {
                productId: productId,
                currentBranchId: currentBranchId,
                endpoint: `/superadmin/inventory/product-stock/${productId}`
            });
            
            fetch(`/superadmin/inventory/product-stock/${productId}`)
                .then(response => {
                    console.log('📡 [API] Branch stock response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ [BRANCH_STOCK] Stock data loaded successfully:', {
                        dataType: typeof data,
                        isArray: Array.isArray(data),
                        length: data ? data.length : 0,
                        rawData: data
                    });
                    
                    // Debug: Log the actual data structure
                    console.log('🔍 [DEBUG] Raw branch data:', data);
                    console.log('🔍 [DEBUG] Current branch ID:', currentBranchId);
                    console.log('🔍 [DEBUG] Filtered branches:', data.filter(branch => branch.branch_id != currentBranchId));
                    
                    // Display other branches stock
                    const otherBranchesContainer = document.getElementById('otherBranchesStock');
                    if (otherBranchesContainer) {
                        if (Array.isArray(data) && data.length > 0) {
                            // Show all branches including current branch, but exclude current from display
                            const displayBranches = data.filter(branch => branch.branch_id != currentBranchId);
                            
                            console.log('📋 [BRANCH_STOCK] Processing branch data:', displayBranches);
                            
                            if (displayBranches.length > 0) {
                                let html = '';
                                let totalBranches = 0;
                                let processedBranches = 0;
                                
                                displayBranches.forEach(branch => {
                                    totalBranches++;
                                    
                                    console.log(`🔍 [FIELD_DEBUG] Branch object keys:`, Object.keys(branch));
                                    console.log(`🔍 [FIELD_DEBUG] Branch object:`, branch);
                                    
                                    const branchName = branch.branch_name || branch.name || 'Unknown Branch';
                                    const totalUnits = branch.current_stock || 0;
                                    
                                    console.log(`🏪 [BRANCH] Processing branch:`, {
                                        branchId: branch.branch_id,
                                        branchName: branchName,
                                        totalUnits: totalUnits,
                                        current_stock: branch.current_stock,
                                        isEligibleForTransfer: branch.branch_id != currentBranchId,
                                        rawBranch: branch
                                    });
                                    
                                    html += `
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-light">
                                                <div class="card-body">
                                                    <h6 class="text-dark">${branchName}</h6>
                                                    <h4 class="text-dark">${totalUnits}</h4>
                                                    <small class="text-muted">Total Units</small>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    processedBranches++;
                                });
                                
                                otherBranchesContainer.innerHTML = html;
                                
                                console.log(`✅ [BRANCH_STOCK] Branch stock display completed:`, {
                                    totalBranches: totalBranches,
                                    processedBranches: processedBranches,
                                    excludedCurrentBranch: currentBranchId
                                });
                            } else {
                                // Check if we have any data at all (including current branch)
                                console.log('📭 [BRANCH_STOCK] No other branches found - checking if we have current branch data');
                                console.log('🔍 [DEBUG] All branch data:', data);
                                
                                // If we have data but it's only current branch, still show current branch for reference
                                if (data.length > 0) {
                                    const currentBranchData = data.find(branch => branch.branch_id == currentBranchId);
                                    if (currentBranchData) {
                                        console.log('� [BRANCH_STOCK] Showing current branch as reference:', currentBranchData);
                                        const branchName = currentBranchData.branch_name || currentBranchData.name || 'Current Branch';
                                        const totalUnits = currentBranchData.total_units || currentBranchData.quantity || 0;
                                        
                                        otherBranchesContainer.innerHTML = `
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-light">
                                                    <div class="card-body">
                                                        <h6 class="text-dark">${branchName}</h6>
                                                        <h4 class="text-dark">${totalUnits}</h4>
                                                        <small class="text-muted">Total Units</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-light">
                                                    <div class="card-body text-center">
                                                        <h6 class="text-muted">No Other Branches</h6>
                                                        <small class="text-muted">This product only exists in current branch</small>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        
                                        console.log('📭 [BRANCH_STOCK] Current branch displayed with no other branches message');
                                    } else {
                                        // Fallback to no data message
                                        showNoDataMessage('No Stock Data Available', 'This product has no stock records in any branches');
                                    }
                                } else {
                                    // No data at all
                                    showNoDataMessage('No Stock Data Available', 'This product has no stock records in any branches');
                                }
                            }
                        } else {
                            // No data at all
                            showNoDataMessage('No Stock Data Available', 'This product has no stock records in any branches');
                        }
                    }
                    
                    function showNoDataMessage(title, message) {
                        console.log('📭 [BRANCH_STOCK] Showing no data message:', title);
                        otherBranchesContainer.innerHTML = `
                            <div class="col-12">
                                <div class="card border-light">
                                    <div class="card-body text-center py-4">
                                        <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted mb-2">${title}</h6>
                                        <small class="text-muted">${message}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('❌ [BRANCH_STOCK] Error loading branch stock:', error);
                    const otherBranchesContainer = document.getElementById('otherBranchesStock');
                    if (otherBranchesContainer) {
                        otherBranchesContainer.innerHTML = '<p class="text-danger">Error loading branch stock data</p>';
                    }
                });
        }

        // Load purchase options
        function loadPurchaseOptions(productId) {
            console.log('🛒 [PURCHASE] Loading purchase options for product:', {
                productId: productId,
                endpoint: `/superadmin/purchases/by-product/${productId}`
            });
            
            fetch(`/superadmin/purchases/by-product/${productId}`)
                .then(response => {
                    console.log('📡 [API] Purchase options response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ [PURCHASE] Purchase options loaded successfully:', {
                        dataType: typeof data,
                        isArray: Array.isArray(data),
                        length: Array.isArray(data) ? data.length : 'N/A',
                        rawData: data
                    });
                    
                    const select = document.getElementById('purchase_id');
                    if (!select) {
                        console.error('❌ [PURCHASE] Element not found: purchase_id');
                        return;
                    }
                    select.innerHTML = '<option value="">-- Select Purchase --</option>';
                    
                    if (Array.isArray(data)) {
                        console.log('📋 [PURCHASE] Processing purchase data:', data);
                        
                        let processedCount = 0;
                        data.forEach(purchase => {
                            console.log('📦 [PURCHASE] Processing purchase option:', {
                                purchaseId: purchase.id,
                                quantity: purchase.quantity,
                                displayText: `Purchase #${purchase.id} - ${purchase.quantity} units`
                            });
                            
                            const option = document.createElement('option');
                            option.value = purchase.id;
                            option.textContent = `Purchase #${purchase.id} - ${purchase.quantity} units`;
                            select.appendChild(option);
                            processedCount++;
                        });
                        
                        console.log('✅ [PURCHASE] Purchase options populated:', {
                            totalPurchases: data.length,
                            processedPurchases: processedCount
                        });
                    } else {
                        console.error('❌ [PURCHASE] Invalid response format:', data);
                    }
                })
                .catch(error => {
                    console.error('❌ [PURCHASE] Error loading purchase options:', {
                        error: error.message,
                        stack: error.stack,
                        productId: productId
                    });
                    const select = document.getElementById('purchase_id');
                    if (select) {
                        select.innerHTML = '<option value="">-- Error loading purchases --</option>';
                    }
                });
        }

        // Load branch options for transfer
        function loadBranchOptions(currentBranchId) {
            console.log('🏢 [BRANCH] Loading branch options for transfer:', {
                currentBranchId: currentBranchId,
                endpoint: '/superadmin/api/branches'
            });
            
            fetch('/superadmin/api/branches')
                .then(response => {
                    console.log('📡 [API] Branch options response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ [BRANCH] Branch options loaded successfully:', {
                        dataType: typeof data,
                        isArray: Array.isArray(data),
                        length: Array.isArray(data) ? data.length : 'N/A',
                        rawData: data
                    });
                    
                    const select = document.getElementById('fromBranch');
                    if (!select) {
                        console.error('❌ [BRANCH] Element not found: fromBranch');
                        return;
                    }
                    select.innerHTML = '<option value="">-- Select Branch --</option>';
                    
                    if (Array.isArray(data)) {
                        console.log('📋 [BRANCH] Processing branch data:', data);
                        
                        let processedCount = 0;
                        let excludedCount = 0;
                        
                        data.forEach(branch => {
                            if (branch.id != currentBranchId) {
                                console.log('🏪 [BRANCH] Processing branch option:', {
                                    branchId: branch.id,
                                    branchName: branch.branch_name,
                                    isEligibleForTransfer: true
                                });
                                
                                const option = document.createElement('option');
                                option.value = branch.id;
                                option.textContent = branch.branch_name;
                                select.appendChild(option);
                                processedCount++;
                            } else {
                                console.log('🚫 [BRANCH] Excluding current branch from transfer options:', {
                                    branchId: branch.id,
                                    branchName: branch.branch_name,
                                    reason: 'Current branch - cannot transfer from self'
                                });
                                excludedCount++;
                            }
                        });
                        
                        console.log('✅ [BRANCH] Branch options populated:', {
                            totalBranches: data.length,
                            processedBranches: processedCount,
                            excludedCurrentBranch: excludedCount
                        });
                    } else {
                        console.error('❌ [BRANCH] Invalid response format:', data);
                    }
                })
                .catch(error => {
                    console.error('❌ [BRANCH] Error loading branches:', {
                        error: error.message,
                        stack: error.stack,
                        currentBranchId: currentBranchId
                    });
                    const select = document.getElementById('fromBranch');
                    if (select) {
                        select.innerHTML = '<option value="">-- Error loading branches --</option>';
                    }
                });
        }

        // Handle adjustment type change
        document.getElementById('adjustmentType').addEventListener('change', function() {
            const type = this.value;
            const purchaseOption = document.getElementById('purchaseOption');
            const transferOption = document.getElementById('transferOption');
            
            if (type === 'purchase') {
                purchaseOption.style.display = 'block';
                transferOption.style.display = 'none';
            } else if (type === 'transfer') {
                purchaseOption.style.display = 'none';
                transferOption.style.display = 'block';
            } else {
                purchaseOption.style.display = 'none';
                transferOption.style.display = 'none';
            }
        });

        // Load and display sales data automatically
        function loadAndDisplaySalesData(productId) {
            console.log('📈 [SALES] Auto-loading sales data for product:', productId);
            
            // Show loading state
            const salesGraphColumn = document.getElementById('salesGraphColumn');
            if (salesGraphColumn) {
                salesGraphColumn.innerHTML = `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Loading Sales Data...</h6>
                        </div>
                        <div class="card-body text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Fetching sales information...</p>
                        </div>
                    </div>
                `;
            }
            
            // Fetch sales data
            fetch(`/superadmin/inventory/product-sales/${productId}`)
                .then(response => {
                    console.log('📡 [API] Sales data response received:', {
                        status: response.status,
                        statusText: response.statusText,
                        ok: response.ok,
                        endpoint: `/superadmin/inventory/product-sales/${productId}`
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ [SALES] Sales data loaded successfully:', {
                        dataType: typeof data,
                        isObject: typeof data === 'object',
                        isArray: Array.isArray(data),
                        keys: Object.keys(data),
                        rawData: data
                    });
                    
                    // Store sales data globally
                    salesData = data;
                    console.log('💾 [SALES] Sales data stored in global variable');
                    
                    // Display the graph
                    displaySalesGraph();
                })
                .catch(error => {
                    console.error('❌ [SALES] Error loading sales data:', error);
                    
                    if (salesGraphColumn) {
                        salesGraphColumn.innerHTML = `
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Sales Trend - ${currentProductData.name}</h6>
                                </div>
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <p class="text-muted mb-0">Failed to load sales data</p>
                                </div>
                            </div>
                        `;
                    }
                });
        }

        // Display sales graph
        function displaySalesGraph() {
            console.log('🎨 [CHART] Display sales graph function started');
            
            const salesGraphColumn = document.getElementById('salesGraphColumn');
            if (!salesGraphColumn) {
                console.error('❌ [CHART] Sales graph column not found');
                return;
            }
            
            console.log('📊 [CHART] Analyzing sales data structure:', {
                salesDataExists: !!salesData,
                salesDataType: typeof salesData,
                salesDataKeys: salesData ? Object.keys(salesData) : 'N/A',
                isArray: Array.isArray(salesData),
                isEmpty: salesData ? Object.keys(salesData).length === 0 : true
            });
            
            // Check if there's sales data
            if (!salesData || Object.keys(salesData).length === 0 || (typeof salesData === 'object' && !Array.isArray(salesData) && Object.keys(salesData).every(key => salesData[key].length === 0))) {
                console.log('📭 [CHART] No sales data available, showing no data message');
                
                // Display no sales data message
                const noDataContent = `
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Sales Trend - ${currentProductData.name}</h6>
                        </div>
                        <div class="card-body text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No sales data available for this product in the last 30 days</p>
                        </div>
                    </div>
                `;
                
                salesGraphColumn.innerHTML = noDataContent;
                console.log('✅ [CHART] No data message displayed');
                return;
            }
            
            console.log('📈 [CHART] Sales data found, creating Chart.js configuration...');
            
            // Transform data for Chart.js
            const chartData = transformSalesDataForChart(salesData);
            console.log('🔄 [DATA] Transformed data for Chart.js:', chartData);
            
            // Create sales graph content
            const salesGraphContent = `
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Sales Trend - ${currentProductData.name}</h6>
                    </div>
                    <div class="card-body p-0">
                        <div style="height: 350px; position: relative;">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            `;
            
            // Set the content
            salesGraphColumn.innerHTML = salesGraphContent;
            console.log('✅ [CHART] Chart container created in right column');
            
            // Initialize Chart.js
            setTimeout(() => {
                initializeChart(chartData);
            }, 100);
        }

        // Back to stock adjustment
        function backToStockAdjustment() {
            const salesGraphColumn = document.getElementById('salesGraphColumn');
            const stockAdjustmentColumn = document.getElementById('stockAdjustmentFormColumn');
            
            if (salesGraphColumn && stockAdjustmentColumn) {
                salesGraphColumn.style.display = 'none';
                stockAdjustmentColumn.style.display = 'block';
                console.log('🔄 [LAYOUT] Switched back to stock adjustment view');
            }
        }

        // Transform sales data for Chart.js
        function transformSalesDataForChart(salesData) {
            console.log('🔄 [TRANSFORM] Starting data transformation...');
            
            const branches = Object.keys(salesData);
            const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14'];
            
            // Get all unique dates and sort them
            const allDates = new Set();
            branches.forEach(branch => {
                salesData[branch].forEach(item => {
                    allDates.add(item.date);
                });
            });
            const sortedDates = Array.from(allDates).sort();
            
            console.log('📅 [TRANSFORM] Date processing:', {
                uniqueDates: sortedDates,
                totalDates: sortedDates.length
            });
            
            // Create datasets for each branch
            const datasets = branches.map((branch, index) => {
                const branchData = salesData[branch];
                const color = colors[index % colors.length];
                
                // Create data array aligned with sortedDates
                const data = sortedDates.map(date => {
                    const dataPoint = branchData.find(item => item.date === date);
                    return dataPoint ? dataPoint.quantity : null; // Use null for missing dates
                });
                
                console.log(`📊 [TRANSFORM] Created dataset for ${branch}:`, {
                    branch: branch,
                    color: color,
                    dataPoints: data,
                    hasNulls: data.some(val => val === null)
                });
                
                return {
                    label: branch,
                    data: data,
                    borderColor: color,
                    backgroundColor: color + '20', // Add transparency
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: color,
                    pointBorderWidth: 2,
                    tension: 0.1, // Smooth lines
                    spanGaps: true // Connect across null values
                };
            });
            
            const result = {
                labels: sortedDates,
                datasets: datasets
            };
            
            console.log('✅ [TRANSFORM] Data transformation completed:', {
                labels: result.labels,
                datasetCount: result.datasets.length,
                datasets: result.datasets.map(ds => ({ label: ds.label, dataPoints: ds.data.length }))
            });
            
            return result;
        }

        // Initialize Chart.js chart
        function initializeChart(chartData) {
            console.log('🎨 [CHART] Initializing Chart.js...');
            
            const canvas = document.getElementById('salesChart');
            if (!canvas) {
                console.error('❌ [CHART] Canvas element not found');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            if (!ctx) {
                console.error('❌ [CHART] Could not get 2D context from canvas');
                return;
            }
            
            // Destroy existing chart if it exists
            if (window.salesChartInstance) {
                window.salesChartInstance.destroy();
                console.log('🗑️ [CHART] Destroyed existing chart instance');
            }
            
            // Create new chart
            window.salesChartInstance = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#ffffff',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true,
                            callbacks: {
                                title: function(context) {
                                    return context[0].dataset.label;
                                },
                                label: function(context) {
                                    return `Date: ${context.label}, Sales: ${context.parsed.y} units`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: true,
                                color: '#e0e0e0'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                },
                                maxRotation: 45,
                                minRotation: 45
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Sales (units)',
                                font: {
                                    size: 12
                                }
                            },
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: '#e0e0e0'
                            },
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 10,
                            right: 10,
                            bottom: 10,
                            left: 10
                        }
                    }
                }
            });
            
            console.log('✅ [CHART] Chart.js initialization completed:', {
                chartType: 'line',
                datasets: chartData.datasets.length,
                labels: chartData.labels.length
            });
        }

        // Hide sales graph
        function hideSalesGraph() {
            const salesGraphSection = document.querySelector('.card:has(#salesChart)');
            if (salesGraphSection) {
                salesGraphSection.remove();
            }
        }

        // Back to stock adjustment
        function backToStockAdjustment() {
            // Restore the original modal content
            const modalBody = adjustStockModal.querySelector('.modal-body');
            modalBody.innerHTML = originalModalContent;
            
            // Re-attach event listeners
            reattachEventListeners();
        }

        // Re-attach event listeners after restoring modal content
        function reattachEventListeners() {
            // Re-attach adjustment type change listener
            const adjustmentTypeSelect = document.getElementById('adjustmentType');
            if (adjustmentTypeSelect) {
                adjustmentTypeSelect.addEventListener('change', function() {
                    const type = this.value;
                    const purchaseOption = document.getElementById('purchaseOption');
                    const transferOption = document.getElementById('transferOption');
                    
                    if (type === 'purchase') {
                        purchaseOption.style.display = 'block';
                        transferOption.style.display = 'none';
                    } else if (type === 'transfer') {
                        purchaseOption.style.display = 'none';
                        transferOption.style.display = 'block';
                    } else {
                        purchaseOption.style.display = 'none';
                        transferOption.style.display = 'none';
                    }
                });
            }
            
            // Re-attach view sales button listener
            const viewSalesBtn = document.getElementById('viewSalesBtn');
            if (viewSalesBtn) {
                viewSalesBtn.addEventListener('click', function() {
                    if (!currentProductData) return;
                    
                    // Extend modal to show sales graph
                    const modalBody = adjustStockModal.querySelector('.modal-body');
                    const originalContent = modalBody.innerHTML;
                    
                    // Show loading
                    modalBody.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-3x"></i></div>';
                    
                    // Load sales data
                    fetch(`/superadmin/inventory/product-sales/${currentProductData.id}`)
                        .then(response => response.json())
                        .then(data => {
                            salesData = data;
                            displaySalesGraph();
                        })
                        .catch(error => {
                            console.error('Error loading sales data:', error);
                            modalBody.innerHTML = originalContent;
                        });
                });
            }
        }

        // Handle save button
        document.getElementById('saveAdjustmentBtn').addEventListener('click', function() {
            const adjustmentType = document.getElementById('adjustmentType').value;
            
            if (!adjustmentType) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select an adjustment type.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            if (adjustmentType === 'purchase') {
                handlePurchaseAdjustment();
            } else if (adjustmentType === 'transfer') {
                handleTransferAdjustment();
            }
        });

        // Handle purchase adjustment
        function handlePurchaseAdjustment() {
            const purchaseId = document.getElementById('purchase_id').value;
            const quantity = parseInt(document.getElementById('purchaseQuantity').value);
            
            if (!purchaseId || !quantity) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Information',
                    text: 'Please select a purchase and enter quantity.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            const newStock = currentProductData.currentStock + quantity;
            
            Swal.fire({
                title: 'Confirm Stock Adjustment',
                html: `Adjust stock from <strong>${currentProductData.currentStock}</strong> to <strong>${newStock}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, adjust stock!'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAdjustment('purchase', {
                        purchase_id: purchaseId,
                        purchase_quantity: quantity,
                        new_stock: newStock
                    });
                }
            });
        }

        // Handle transfer adjustment
        function handleTransferAdjustment() {
            const fromBranch = document.getElementById('fromBranch').value;
            const quantity = parseInt(document.getElementById('transferQuantity').value);
            
            if (!fromBranch || !quantity) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Information',
                    text: 'Please select a branch and enter quantity.',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            // Validate transfer amount
            if (quantity > currentProductData.currentStock) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Transfer Amount',
                    text: 'Cannot transfer more units than currently available.',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
            
            Swal.fire({
                title: 'Confirm Stock Transfer',
                html: `Transfer <strong>${quantity}</strong> units from selected branch to <strong>${currentProductData.branchName}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, transfer stock!'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitAdjustment('transfer', {
                        from_branch: fromBranch,
                        transfer_quantity: quantity
                    });
                }
            });
        }

        // Submit adjustment
        function submitAdjustment(type, data) {
            const submitBtn = document.getElementById('saveAdjustmentBtn');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';

            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('adjustment_type', type);
            
            Object.keys(data).forEach(key => {
                formData.append(key, data[key]);
            });

            fetch(`/superadmin/inventory/${currentProductData.id}/adjust`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        const modal = bootstrap.Modal.getInstance(adjustStockModal);
                        modal.hide();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    });
                } else {
                    throw new Error(data.message || 'Adjustment failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to process adjustment. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        }

        
        const searchInput = document.getElementById('searchInput');
        let debounceTimer;

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = searchInput.value;
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                window.location.href = url.toString();
            }, 500);
        });
    });

    // Make exportToPDF function globally accessible
    window.exportToPDF = function() {
        const exportBtn = document.getElementById('exportPdfBtn');
        const originalText = exportBtn.innerHTML;
        exportBtn.disabled = true;
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating PDF...';
        
        try {
            // Get current filter parameters
            const url = new URL(window.location.href);
            const branchId = url.searchParams.get('branch_id');
            const search = url.searchParams.get('search');
            const sortBy = url.searchParams.get('sort_by');
            const sortDirection = url.searchParams.get('sort_direction');
            
            // Build export URL with current filters
            const exportUrl = new URL('{{ route("superadmin.inventory.out-of-stock.export") }}');
            if (branchId) exportUrl.searchParams.set('branch_id', branchId);
            if (search) exportUrl.searchParams.set('search', search);
            if (sortBy) exportUrl.searchParams.set('sort_by', sortBy);
            if (sortDirection) exportUrl.searchParams.set('sort_direction', sortDirection);
            
            // Create hidden link and trigger download
            const link = document.createElement('a');
            link.href = exportUrl.toString();
            link.download = `out-of-stock-${new Date().toISOString().split('T')[0]}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Export Failed',
                text: 'Failed to generate PDF. Please try again.',
                confirmButtonColor: '#dc3545'
            });
        } finally {
            // Restore button
            exportBtn.disabled = false;
            exportBtn.innerHTML = originalText;
        }
    };
</script>
@endsection
