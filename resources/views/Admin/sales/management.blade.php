@extends('layouts.app')

@section('content')
<style>
.modal-body {
    position: relative;
    min-height: 200px;
}

#detailsLoading, #editLoading, #historyLoading, #deleteLoading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.9);
    z-index: 10;
}

#detailsContent, #editContent, #historyContent, #deleteContent {
    position: relative;
    z-index: 1;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<div class="d-flex min-vh-100">
    <main class="flex-fill p-4">
        <div class="container-fluid">
            <div class="row mb-6">
                <div class="col-12">
                    <div class="p-4 card-rounded shadow-sm bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h2 class="m-0">Admin Sales Management</h2>
                                <p class="mb-0 text-muted">
                                    @if(isset($selectedDate))
                                        Sales for {{ $selectedDate->format('F d, Y') }}
                                        <a href="{{ route('admin.sales.management.index') }}" class="text-primary text-decoration-none">
                                            <small>(Show Today)</small>
                                        </a>
                                    @else
                                        Manage and monitor all sales transactions
                                    @endif
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('pos.index') }}" class="btn btn-primary">Go to POS</a>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row">
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    {{ isset($selectedDate) ? $selectedDate->format('M d, Y') : 'Today\'s Sales' }}
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todaySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">All Branches Today</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($allBranchesTodaySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-store fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Management Table -->
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Sales Management</h6>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="salesSearchInput" class="form-control form-control-sm" placeholder="Search sales..." onkeyup="searchSales()">
                        <input type="date" id="salesDateFilter" class="form-control form-control-sm" value="{{ isset($selectedDate) ? $selectedDate->format('Y-m-d') : '' }}" placeholder="Filter by date" onchange="filterSalesByDate()">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Product Name</th>
                                    <th>Cashier</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Date</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>#{{ $sale->id }}</td>
                                        <td>{{ $sale->product_names ?: 'No products' }}</td>
                                        <td>{{ $sale->cashier->name ?? 'Unknown' }}</td>
                                        <td>{{ $sale->saleItems->sum('quantity') }} items</td>
                                        <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @php
                                                $hasRefunds = $sale->refunds()->where('status', 'approved')->exists();
                                                $paymentDisplay = $hasRefunds ? 'Refunded' : ucfirst($sale->payment_method);
                                                $badgeClass = $hasRefunds ? 'bg-warning' : 'bg-success';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $paymentDisplay }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-info" onclick="showSaleDetails({{ $sale->id }})" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-secondary" onclick="showSaleHistory({{ $sale->id }})" title="View History">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="showDeleteSale({{ $sale->id }})" title="Delete Sale">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="4" class="text-right">Grand Total:</th>
                                    <th>₱{{ number_format($recentSales->sum('total_amount'), 2) }}</th>
                                    <th colspan="2">{{ $recentSales->count() }} Sales</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal 1: Sale Details Modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleDetailsModalLabel">Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailsLoading" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sale details...</p>
                </div>
                <div id="detailsContent">
                    <!-- Sale details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printSaleReceipt()">Print Receipt</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Sale History Modal -->
<div class="modal fade" id="saleHistoryModal" tabindex="-1" aria-labelledby="saleHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleHistoryModalLabel">Sale History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyLoading" style="display: none;">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sale history...</p>
                </div>
                <div id="historyContent">
                    <!-- Sale history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Delete Sale Modal -->
<div class="modal fade" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSaleModalLabel">Delete Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deleteLoading" style="display: none;">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Processing deletion...</p>
                </div>
                <div id="deleteContent">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete this sale? This will also remove all associated sale items and update inventory accordingly.</p>
                    <div id="deleteSaleInfo">
                        <!-- Sale info for deletion will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteSale()">Delete Sale</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentSaleId = null;

// Search functionality
function searchSales() {
    const searchTerm = document.getElementById('salesSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#salesTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Date filter functionality
function filterSalesByDate() {
    const selectedDate = document.getElementById('salesDateFilter').value;
    if (selectedDate) {
        window.location.href = `{{ route('admin.sales.management.index') }}?date=${selectedDate}`;
    } else {
        window.location.href = '{{ route('admin.sales.management.index') }}';
    }
}

// Modal 1: Show Sale Details
function showSaleDetails(saleId) {
    currentSaleId = saleId;
    const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
    modal.show();
    
    // Show loading
    document.getElementById('detailsLoading').style.display = 'block';
    document.getElementById('detailsContent').style.display = 'none';
    
    // Fetch sale details
    fetch(`/admin/sales/${saleId}/items`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detailsLoading').style.display = 'none';
            document.getElementById('detailsContent').style.display = 'block';
            
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Sale Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Sale ID:</strong></td><td>#${saleId}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>${new Date().toLocaleDateString()}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge bg-success">Completed</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Payment Method:</strong></td><td>Cash</td></tr>
                            <tr><td><strong>Total Amount:</strong></td><td>₱${data.sale_info ? parseFloat(data.sale_info.current_total).toFixed(2) : '0.00'}</td></tr>
                        </table>
                    </div>
                </div>
                <h6 class="mt-3">Items Sold</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    content += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.quantity}</td>
                            <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>₱${(item.quantity * parseFloat(item.unit_price)).toFixed(2)}</td>
                        </tr>
                    `;
                });
            } else {
                content += '<tr><td colspan="4" class="text-center">No items found</td></tr>';
            }
            
            content += `
                        </tbody>
                    </table>
                </div>
            `;
            
            document.getElementById('detailsContent').innerHTML = content;
        })
        .catch(error => {
            console.error('Error loading sale details:', error);
            document.getElementById('detailsLoading').style.display = 'none';
            document.getElementById('detailsContent').innerHTML = '<div class="alert alert-danger">Error loading sale details</div>';
        });
}

// Modal 3: Show Sale History
function showSaleHistory(saleId) {
    currentSaleId = saleId;
    const modal = new bootstrap.Modal(document.getElementById('saleHistoryModal'));
    modal.show();
    
    // Show loading
    document.getElementById('historyLoading').style.display = 'block';
    document.getElementById('historyContent').style.display = 'none';
    
    // Simulate loading history (in real app, this would fetch from API)
    setTimeout(() => {
        document.getElementById('historyLoading').style.display = 'none';
        document.getElementById('historyContent').style.display = 'block';
        
        const content = `
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Sale Created</h6>
                        <p class="text-muted mb-1">${new Date().toLocaleString()}</p>
                        <p>Sale was created and processed successfully.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Payment Processed</h6>
                        <p class="text-muted mb-1">${new Date().toLocaleString()}</p>
                        <p>Payment was received and confirmed.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Inventory Updated</h6>
                        <p class="text-muted mb-1">${new Date().toLocaleString()}</p>
                        <p>Stock levels were automatically updated.</p>
                    </div>
                </div>
            </div>
            <style>
                .timeline { position: relative; padding-left: 30px; }
                .timeline-item { position: relative; padding-bottom: 20px; }
                .timeline-marker { position: absolute; left: -30px; top: 5px; width: 12px; height: 12px; border-radius: 50%; }
                .timeline-content { border-left: 2px solid #e9ecef; padding-left: 20px; }
            </style>
        `;
        
        document.getElementById('historyContent').innerHTML = content;
    }, 1000);
}

// Modal 4: Show Delete Sale
function showDeleteSale(saleId) {
    currentSaleId = saleId;
    const modal = new bootstrap.Modal(document.getElementById('deleteSaleModal'));
    modal.show();
    
    // Show loading
    document.getElementById('deleteLoading').style.display = 'none';
    document.getElementById('deleteContent').style.display = 'block';
    
    // Fetch sale info for deletion confirmation
    fetch(`/admin/sales/${saleId}/items`)
        .then(response => response.json())
        .then(data => {
            const info = `
                <div class="alert alert-info">
                    <strong>Sale #${saleId}</strong><br>
                    Total Amount: ₱${data.sale_info ? parseFloat(data.sale_info.current_total).toFixed(2) : '0.00'}<br>
                    Items: ${data.items ? data.items.length : 0} products<br>
                    Date: ${new Date().toLocaleDateString()}
                </div>
            `;
            document.getElementById('deleteSaleInfo').innerHTML = info;
        })
        .catch(error => {
            console.error('Error loading sale info:', error);
            document.getElementById('deleteSaleInfo').innerHTML = '<div class="alert alert-danger">Error loading sale information</div>';
        });
}

// Confirm Delete Sale
function confirmDeleteSale() {
    if (!currentSaleId) return;
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            document.getElementById('deleteLoading').style.display = 'block';
            document.getElementById('deleteContent').style.display = 'none';
            
            // Simulate deletion (in real app, this would make an API call)
            setTimeout(() => {
                document.getElementById('deleteLoading').style.display = 'none';
                
                Swal.fire(
                    'Deleted!',
                    'Sale has been deleted.',
                    'success'
                ).then(() => {
                    // Close modal and refresh page
                    bootstrap.Modal.getInstance(document.getElementById('deleteSaleModal')).hide();
                    location.reload();
                });
            }, 1500);
        }
    });
}

// Print Receipt
function printSaleReceipt() {
    if (!currentSaleId) return;
    
    // Open print window (in real app, this would open a printable receipt view)
    const printWindow = window.open(`/admin/sales/${currentSaleId}/receipt`, '_blank');
    if (!printWindow) {
        Swal.fire({
            icon: 'error',
            title: 'Print Failed',
            text: 'Please allow pop-ups for this site to print receipts.'
        });
    }
}

</script>
@endsection
