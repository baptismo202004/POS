@extends('layouts.app')

@section('title', 'Stock Management')

@section('content')
<div class="d-flex">
    <!-- Main Content Area -->
    <div class="flex-fill">
        <div class="container-fluid">
            <!-- Minimal Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1 fw-bold">
                        Stock Management
                    </h5>
                    <p class="text-muted mb-0 small">Intelligent inventory monitoring</p>
                </div>
                
                <!-- Search Bar -->
                <div class="position-relative">
                    <input type="text" class="form-control form-control-sm" id="searchFilterHeader" 
                           placeholder="Search..." style="width: 250px;">
                    <i class="fas fa-search position-absolute" style="right: 10px; top: 8px; color: #6c757d; font-size: 12px;"></i>
                </div>
            </div>
    
    <!-- Include Advanced Filters Component -->
    @include('superadmin.inventory.stock-filters')
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Product {{ request('sort_by') == 'product_name' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Branch</th>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Current Stock {{ request('sort_by') == 'current_stock' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'unit_price', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Unit Price {{ request('sort_by') == 'unit_price' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>Total Value</th>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'last_updated', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Last Updated {{ request('sort_by') == 'last_updated' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="{{ $product->current_stock <= 15 ? 'table-warning' : ($product->current_stock <= 0 ? 'table-danger' : '') }}">
                                    <td>
                                        <a href="{{ route('superadmin.products.show', $product->id) }}" class="text-decoration-none">
                                            {{ $product->product_name }}
                                        </a>
                                        @if($product->current_stock <= 0)
                                            <span class="badge bg-danger ms-2">Out of Stock</span>
                                        @elseif($product->current_stock <= 15)
                                            <span class="badge bg-warning ms-2">Low Stock</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->brand_name ?? 'N/A' }}</td>
                                    <td>{{ $product->category_name ?? 'N/A' }}</td>
                                    <td>{{ $product->branch_name ?? 'Main Branch' }}</td>
                                    <td class="{{ $product->current_stock <= 0 ? 'text-danger font-weight-bold' : ($product->current_stock <= 15 ? 'text-warning font-weight-bold' : '') }}">
                                        <span class="badge {{ $product->current_stock <= 0 ? 'bg-danger' : ($product->current_stock <= 15 ? 'bg-warning' : 'bg-success') }}">
                                            {{ $product->current_stock }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($product->unit_price ?? 0, 2) }}</td>
                                    <td>{{ number_format(($product->unit_price ?? 0) * $product->current_stock, 2) }}</td>
                                    <td>{{ $product->last_stock_update ? \Carbon\Carbon::parse($product->last_stock_update)->format('M d, Y H:i') : 'Never' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" data-current-stock="{{ $product->current_stock }}" title="Adjust Stock">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-success stock-in-btn" data-bs-toggle="modal" data-bs-target="#stockInModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" title="Stock In">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-info history-btn" data-bs-toggle="modal" data-bs-target="#stockHistoryModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" title="History">
                                                <i class="fas fa-history"></i>
                                            </button>
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
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
                                    @csrf
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
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
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
@endsection
