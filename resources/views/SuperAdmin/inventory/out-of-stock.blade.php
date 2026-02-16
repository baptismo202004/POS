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
                                                <button class="btn btn-sm btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" data-current-stock="{{ $product->current_stock }}" title="Adjust Stock">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="productName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustStockForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-control" id="branch_id" name="branch_id" required>
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">New Stock Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="0" required>
                        <small class="form-text text-muted">Set the new total stock quantity for this product.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
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
        adjustStockModal.addEventListener('shown.bs.modal', function (event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const currentStock = button.getAttribute('data-current-stock');

            const modalTitle = adjustStockModal.querySelector('.modal-title #productName');
            const form = adjustStockModal.querySelector('#adjustStockForm');
            const quantityInput = adjustStockModal.querySelector('#quantity');

            modalTitle.textContent = productName;
            form.action = `/superadmin/inventory/${productId}/adjust`;
            quantityInput.value = currentStock;
        });

        // Handle stock adjustment form submission via AJAX
        const adjustStockForm = document.getElementById('adjustStockForm');
        adjustStockForm.addEventListener('submit', function (e) {
            e.preventDefault();
            
            const submitBtn = adjustStockForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            const formData = new FormData(adjustStockForm);
            const action = adjustStockForm.action;

            fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('AJAX response received:', data);
                
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(adjustStockModal);
                    modal.hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Stock adjusted successfully.',
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
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        // Update dashboard alerts if we have count
                        if (data.outOfStockCount !== undefined) {
                            console.log('Updating dashboard alerts with new count:', data.outOfStockCount);
                            updateDashboardAlerts(data.outOfStockCount);
                        }
                        // Reload page to show updated inventory data
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    });
                } else {
                    throw new Error(data.message || 'Something went wrong');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to adjust stock. Please try again.',
                    confirmButtonText: 'Okay',
                    confirmButtonColor: '#E63946'
                });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });

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
