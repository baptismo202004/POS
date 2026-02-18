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
</main>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="productName"></span> - <span id="branchName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Current Stock Card -->
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Current Stock Record from <span id="branchName"></span></h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="text-primary" id="currentStockDisplay">0</h4>
                                <p class="text-muted mb-0">Units available</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-outline-primary" id="viewSalesBtn">
                                    <i class="fas fa-chart-line me-1"></i> View Sales
                                </button>
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
        let originalModalContent = null; // Store original modal content

        adjustStockModal.addEventListener('shown.bs.modal', function (event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const currentStock = button.getAttribute('data-current-stock');
            const branchId = button.getAttribute('data-branch-id');
            const branchName = button.getAttribute('data-branch-name');

            // Store original modal content when first opened
            if (!originalModalContent) {
                originalModalContent = adjustStockModal.querySelector('.modal-body').innerHTML;
            }

            currentProductData = {
                id: productId,
                name: productName,
                currentStock: parseInt(currentStock),
                branchId: branchId,
                branchName: branchName
            };

            // Update modal header
            document.getElementById('productName').textContent = productName;
            document.getElementById('branchName').textContent = branchName;
            document.getElementById('currentStockDisplay').textContent = currentStock;

            // Load other branches stock data
            loadOtherBranchesStock(productId, branchId);
            
            // Load purchase options for this product
            loadPurchaseOptions(productId);
            
            // Load branch options for transfer
            loadBranchOptions(branchId);
        });

        // Load other branches stock
        function loadOtherBranchesStock(productId, currentBranchId) {
            fetch(`/superadmin/inventory/product-stock/${productId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const container = document.getElementById('otherBranchesStock');
                    if (!container) {
                        console.error('Container not found: otherBranchesStock');
                        return;
                    }
                    container.innerHTML = '';
                    
                    if (Array.isArray(data)) {
                        data.forEach(branch => {
                            if (branch.branch_id != currentBranchId) {
                                const col = document.createElement('div');
                                col.className = 'col-md-4 mb-2';
                                col.innerHTML = `
                                    <div class="p-2 border rounded">
                                        <small class="text-muted">${branch.branch_name}</small>
                                        <div class="fw-bold">${branch.current_stock} units</div>
                                    </div>
                                `;
                                container.appendChild(col);
                            }
                        });
                    } else {
                        console.error('Invalid response format:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading other branches stock:', error);
                    const container = document.getElementById('otherBranchesStock');
                    if (container) {
                        container.innerHTML = '<div class="alert alert-danger">Error loading stock data</div>';
                    }
                });
        }

        // Load purchase options
        function loadPurchaseOptions(productId) {
            fetch(`/superadmin/purchases/by-product/${productId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const select = document.getElementById('purchase_id');
                    if (!select) {
                        console.error('Element not found: purchase_id');
                        return;
                    }
                    select.innerHTML = '<option value="">-- Select Purchase --</option>';
                    
                    if (Array.isArray(data)) {
                        data.forEach(purchase => {
                            const option = document.createElement('option');
                            option.value = purchase.id;
                            option.textContent = `Purchase #${purchase.id} - ${purchase.quantity} units`;
                            select.appendChild(option);
                        });
                    } else {
                        console.error('Invalid response format:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading purchase options:', error);
                    const select = document.getElementById('purchase_id');
                    if (select) {
                        select.innerHTML = '<option value="">-- Error loading purchases --</option>';
                    }
                });
        }

        // Load branch options for transfer
        function loadBranchOptions(currentBranchId) {
            fetch('/superadmin/api/branches')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const select = document.getElementById('fromBranch');
                    if (!select) {
                        console.error('Element not found: fromBranch');
                        return;
                    }
                    select.innerHTML = '<option value="">-- Select Branch --</option>';
                    
                    if (Array.isArray(data)) {
                        data.forEach(branch => {
                            if (branch.id != currentBranchId) {
                                const option = document.createElement('option');
                                option.value = branch.id;
                                option.textContent = branch.branch_name;
                                select.appendChild(option);
                            }
                        });
                    } else {
                        console.error('Invalid response format:', data);
                    }
                })
                .catch(error => {
                    console.error('Error loading branches:', error);
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

        // Handle View Sales button
        document.getElementById('viewSalesBtn').addEventListener('click', function() {
            if (!currentProductData) return;
            
            // Check if sales graph already exists
            if (document.getElementById('salesChart')) {
                // Scroll to existing sales graph
                document.querySelector('.card:has(#salesChart)').scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            // Load sales data
            fetch(`/superadmin/inventory/product-sales/${currentProductData.id}`)
                .then(response => response.json())
                .then(data => {
                    salesData = data;
                    displaySalesGraph();
                })
                .catch(error => {
                    console.error('Error loading sales data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load sales data'
                    });
                })
                .finally(() => {
                    // Restore button state
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-chart-line"></i> View Sales';
                });
        });

        // Display sales graph
        function displaySalesGraph() {
            const modalBody = adjustStockModal.querySelector('.modal-body');
            
            // Check if there's sales data
            if (!salesData || Object.keys(salesData).length === 0 || (typeof salesData === 'object' && !Array.isArray(salesData) && Object.keys(salesData).every(key => salesData[key].length === 0))) {
                // Display no sales data message
                const salesGraphSection = document.createElement('div');
                salesGraphSection.className = 'mt-3';
                salesGraphSection.innerHTML = `
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Sales Trend - ${currentProductData.name}</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideSalesGraph()">
                                <i class="fas fa-times"></i> Close
                            </button>
                        </div>
                        <div class="card-body text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No sales data available for this product in the last 30 days</p>
                        </div>
                    </div>
                `;
                
                modalBody.appendChild(salesGraphSection);
                salesGraphSection.scrollIntoView({ behavior: 'smooth' });
                return;
            }
            
            // Create sales graph section and append to modal
            const salesGraphSection = document.createElement('div');
            salesGraphSection.className = 'mt-3';
            salesGraphSection.innerHTML = `
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Sales Trend - ${currentProductData.name}</h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="hideSalesGraph()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                        <div class="mt-2" id="legendContainer"></div>
                    </div>
                </div>
            `;
            
            // Append to modal body
            modalBody.appendChild(salesGraphSection);
            
            // Scroll to the sales graph
            salesGraphSection.scrollIntoView({ behavior: 'smooth' });
            
            // Create line chart
            const canvas = document.getElementById('salesChart');
            const ctx = canvas.getContext('2d');
            
            // Prepare data for line graph
            const branches = Object.keys(salesData);
            const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14'];
            
            // Get all unique dates
            const allDates = new Set();
            branches.forEach(branch => {
                salesData[branch].forEach(item => {
                    allDates.add(item.date);
                });
            });
            const sortedDates = Array.from(allDates).sort();
            
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Set up chart dimensions
            const padding = 40;
            const chartWidth = canvas.width - 2 * padding;
            const chartHeight = canvas.height - 2 * padding;
            
            // Find max value for scaling
            let maxValue = 0;
            branches.forEach(branch => {
                salesData[branch].forEach(item => {
                    maxValue = Math.max(maxValue, item.quantity);
                });
            });
            maxValue = Math.ceil(maxValue * 1.1); // Add 10% padding
            
            // Draw axes
            ctx.strokeStyle = '#ddd';
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(padding, padding);
            ctx.lineTo(padding, canvas.height - padding);
            ctx.lineTo(canvas.width - padding, canvas.height - padding);
            ctx.stroke();
            
            // Draw grid lines and labels
            ctx.fillStyle = '#666';
            ctx.font = '10px Arial';
            ctx.textAlign = 'right';
            
            // Y-axis labels
            for (let i = 0; i <= 5; i++) {
                const y = padding + (chartHeight / 5) * i;
                const value = Math.round(maxValue - (maxValue / 5) * i);
                ctx.fillText(value, padding - 5, y + 3);
                
                // Grid lines
                ctx.strokeStyle = '#f0f0f0';
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(canvas.width - padding, y);
                ctx.stroke();
            }
            
            // X-axis labels (dates)
            const dateStep = Math.max(1, Math.floor(sortedDates.length / 10)); // Show max 10 dates
            sortedDates.forEach((date, index) => {
                if (index % dateStep === 0) {
                    const x = padding + (chartWidth / (sortedDates.length - 1)) * index;
                    ctx.save();
                    ctx.translate(x, canvas.height - padding + 15);
                    ctx.rotate(-45);
                    ctx.textAlign = 'right';
                    ctx.fillText(date, 0, 0);
                    ctx.restore();
                }
            });
            
            // Draw lines for each branch
            const legendContainer = document.getElementById('legendContainer');
            legendContainer.innerHTML = '<div class="d-flex flex-wrap gap-2">';
            
            branches.forEach((branch, branchIndex) => {
                const color = colors[branchIndex % colors.length];
                const branchData = salesData[branch];
                
                // Draw line
                ctx.strokeStyle = color;
                ctx.lineWidth = 2;
                ctx.beginPath();
                
                let firstPoint = true;
                sortedDates.forEach((date, dateIndex) => {
                    const dataPoint = branchData.find(item => item.date === date);
                    if (dataPoint) {
                        const x = padding + (chartWidth / (sortedDates.length - 1)) * dateIndex;
                        const y = padding + chartHeight - (dataPoint.quantity / maxValue) * chartHeight;
                        
                        if (firstPoint) {
                            ctx.moveTo(x, y);
                            firstPoint = false;
                        } else {
                            ctx.lineTo(x, y);
                        }
                        
                        // Draw point
                        ctx.fillStyle = color;
                        ctx.beginPath();
                        ctx.arc(x, y, 3, 0, 2 * Math.PI);
                        ctx.fill();
                    }
                });
                
                ctx.stroke();
                
                // Add to legend
                legendContainer.innerHTML += `
                    <div class="d-flex align-items-center gap-1">
                        <div style="width: 12px; height: 12px; background-color: ${color}; border-radius: 2px;"></div>
                        <small>${branch}</small>
                    </div>
                `;
            });
            
            legendContainer.innerHTML += '</div>';
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
