@extends('layouts.app')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<style>
.modal-body {
    position: relative;
    min-height: 200px;
}

#revenueLoading, #itemsLoading, #monthlyLoading {
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

#revenueContent, #itemsContent, #monthlyContent {
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
                                    <h2 class="m-0">Sales Dashboard</h2>
                                    <p class="mb-0 text-muted">
                                        @if(isset($selectedDate))
                                            Sales for {{ $selectedDate->format('F d, Y') }}
                                            <a href="{{ route('admin.sales.index') }}" class="text-primary text-decoration-none">
                                                <small>(Show Today)</small>
                                            </a>
                                        @else
                                            Overview of your sales performance
                                        @endif
                                    </p>
                                    @if($filter !== 'all')
                                        <div class="alert alert-info" role="alert">
                                            <strong>Filter Applied:</strong> Showing {{ ucfirst(str_replace('-', ' ', $filter)) }} sales only
                                        </div>
                                    @endif
                                </div>
                                
                            </div>
                        <div class="row">
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2 hover-card" onclick="showTodaysRevenueModal()">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
    {{ isset($selectedDate) ? $selectedDate->format('M d, Y') . ' Sales' : 'Today\'s Sales' }}
</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todaySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2 hover-card" onclick="showTodaysRevenueModal()">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Revenue</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todaySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-info shadow h-100 py-2 hover-card" onclick="showItemsSoldTodayModal()">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Items Sold Today</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayItems ?? 0 }} Items</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-box fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2 hover-card" onclick="showThisMonthSalesModal()">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month's Sales</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Graph -->
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="m-0 font-weight-bold text-white">Sales Trend - Last 2 Weeks</h6>
                            <small class="text-white-50">Daily sales revenue and order count for the past 14 days</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-light" onclick="refreshGraph()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-check-circle"></i>
                            <strong>TEST MESSAGE:</strong> If you can see this message, the view changes are working!
                        </div>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Graph Data:</strong> This chart displays daily sales revenue (₱) and total orders for the last 14 days. 
                            Blue line shows sales revenue, cyan line shows order count. Hover over data points for detailed values.
                        </div>
                        <div id="graphLoading" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 10; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div id="graphContent" style="position: relative; z-index: 1;">
                            <canvas id="salesGraph" width="400" height="150"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Sales Table -->
                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-white">Recent Sales</h6>
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
                                        <th>Reference #</th>
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
                                        <tr class="{{ $filter !== 'all' ? 'table-warning' : '' }}">
                                            <td>{{ $sale->reference_number ?: 'PR-' . $sale->created_at->format('Ymd') . $sale->id }}</td>
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
                                                @if($hasRefunds)
                                                    <button class="btn btn-sm btn-secondary" disabled title="Already refunded">
                                                        <i class="fas fa-undo"></i> Refunded
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-warning" onclick="openRefundModal({{ $sale->id }})">
                                                        <i class="fas fa-undo"></i> Refund
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No sales found for today.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th colspan="3" class="text-right">Grand Total:</th>
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
                    </div>
                </div>
            </div>

    <!-- Refund Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="refundForm">
                    <div class="modal-body">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="sale_id" id="sale_id">
                        
                        <div class="mb-3">
                            <label for="sale_item_id" class="form-label">Select Product</label>
                            <select class="form-select" name="sale_item_id" id="sale_item_id" required>
                                <option value="">Choose a product...</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity_refunded" class="form-label">Quantity to Refund</label>
                            <input type="number" class="form-control" name="quantity_refunded" id="quantity_refunded" min="1" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="refund_amount" class="form-label">Refund Amount (₱)</label>
                            <input type="number" class="form-control" name="refund_amount" id="refund_amount" step="0.01" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Refund</label>
                            <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Optional reason for refund"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Process Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Sales Graph -->
    <script src="{{ asset('js/sales-graph.js') }}"></script>
    
    <script>
        let currentSale = null;
        
        // Function to refresh sale items for a sale
        function refreshSaleItems(saleId) {
            fetch(`/admin/sales/${saleId}/items`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Refreshed sale items data:', data);
                    const select = document.getElementById('sale_item_id');
                    const currentValue = select.value;
                    
                    select.innerHTML = '<option value="">Choose a product...</option>';
                    
                    // Display updated sale total information
                    if (data.sale_info) {
                        const originalTotal = parseFloat(data.sale_info.original_total) || 0;
                        const totalRefunded = parseFloat(data.sale_info.total_refunded) || 0;
                        const currentTotal = parseFloat(data.sale_info.current_total) || 0;
                        
                        const saleInfoDiv = document.createElement('div');
                        saleInfoDiv.className = 'alert alert-info mb-3';
                        saleInfoDiv.innerHTML = `
                            <h6 class="mb-2">Sale Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small>Original Total:</small><br>
                                    <strong>₱${originalTotal.toFixed(2)}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small>Total Refunded:</small><br>
                                    <strong class="text-danger">-₱${totalRefunded.toFixed(2)}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small>Current Total:</small><br>
                                    <strong class="text-success">₱${currentTotal.toFixed(2)}</strong>
                                </div>
                            </div>
                        `;
                        
                        // Remove existing sale info if present
                        const existingInfo = document.querySelector('.sale-info-display');
                        if (existingInfo) {
                            existingInfo.remove();
                        }
                        
                        // Add sale info before the select
                        saleInfoDiv.className = 'alert alert-info mb-3 sale-info-display';
                        select.parentNode.insertBefore(saleInfoDiv, select);
                    }
                    
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            console.log('Processing refreshed item:', item);
                            if (item.available_for_refund > 0) { // Only show items with available quantity
                                const option = document.createElement('option');
                                option.value = item.id;
                                option.textContent = `${item.product_name} (Available: ${item.available_for_refund})`;
                                option.dataset.price = item.unit_price;
                                option.dataset.maxQuantity = item.available_for_refund;
                                option.dataset.productId = item.product_id;
                                select.appendChild(option);
                            }
                        });
                        
                        // Restore previous selection if still available
                        if (currentValue) {
                            select.value = currentValue;
                            if (!select.value) {
                                // Previous selection no longer available
                                document.getElementById('quantity_refunded').value = '';
                                document.getElementById('refund_amount').value = '';
                            }
                        }
                    } else {
                        console.log('No items available for refund after refresh');
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No items available for refund';
                        option.disabled = true;
                        select.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error refreshing sale items:', error);
                });
        }
        
        function openRefundModal(saleId) {
            currentSale = saleId;
            document.getElementById('sale_id').value = saleId;
            
            // Load sale items for this sale
            fetch(`/admin/sales/${saleId}/items`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Sale items data:', data);
                    const select = document.getElementById('sale_item_id');
                    select.innerHTML = '<option value="">Choose a product...</option>';
                    
                    // Display sale total information
                    if (data.sale_info) {
                        const originalTotal = parseFloat(data.sale_info.original_total) || 0;
                        const totalRefunded = parseFloat(data.sale_info.total_refunded) || 0;
                        const currentTotal = parseFloat(data.sale_info.current_total) || 0;
                        
                        const saleInfoDiv = document.createElement('div');
                        saleInfoDiv.className = 'alert alert-info mb-3';
                        saleInfoDiv.innerHTML = `
                            <h6 class="mb-2">Sale Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <small>Original Total:</small><br>
                                    <strong>₱${originalTotal.toFixed(2)}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small>Total Refunded:</small><br>
                                    <strong class="text-danger">-₱${totalRefunded.toFixed(2)}</strong>
                                </div>
                                <div class="col-md-4">
                                    <small>Current Total:</small><br>
                                    <strong class="text-success">₱${currentTotal.toFixed(2)}</strong>
                                </div>
                            </div>
                        `;
                        
                        // Remove existing sale info if present
                        const existingInfo = document.querySelector('.sale-info-display');
                        if (existingInfo) {
                            existingInfo.remove();
                        }
                        
                        // Add sale info before the select
                        saleInfoDiv.className = 'alert alert-info mb-3 sale-info-display';
                        select.parentNode.insertBefore(saleInfoDiv, select);
                    }
                    
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            console.log('Processing item:', item);
                            const option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = `${item.product_name} (Available: ${item.available_for_refund})`;
                            option.dataset.price = item.unit_price;
                            option.dataset.maxQuantity = item.available_for_refund;
                            option.dataset.productId = item.product_id;
                            select.appendChild(option);
                        });
                    } else {
                        console.log('No items found for this sale');
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No items available for refund';
                        option.disabled = true;
                        select.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error loading sale items:', error);
                    const select = document.getElementById('sale_item_id');
                    select.innerHTML = '<option value="">Error loading products</option>';
                });
            
            const modal = new bootstrap.Modal(document.getElementById('refundModal'));
            modal.show();
        }
        
        function voidSale(saleId) {
            if (confirm('Are you sure you want to void this sale? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/superadmin/sales/${saleId}/void`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        document.getElementById('sale_item_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const maxQuantity = parseInt(selectedOption.dataset.maxQuantity) || 0;
            const price = parseFloat(selectedOption.dataset.price) || 0;
            
            document.getElementById('quantity_refunded').max = maxQuantity;
            document.getElementById('quantity_refunded').value = Math.min(1, maxQuantity);
            document.getElementById('refund_amount').value = (price * Math.min(1, maxQuantity)).toFixed(2);
        });
        
        document.getElementById('quantity_refunded').addEventListener('input', function() {
            const select = document.getElementById('sale_item_id');
            const selectedOption = select.options[select.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const quantity = parseInt(this.value) || 0;
            
            document.getElementById('refund_amount').value = (price * quantity).toFixed(2);
        });
        
        document.getElementById('refundForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate that a product is selected
            const saleItemSelect = document.getElementById('sale_item_id');
            if (!saleItemSelect.value || saleItemSelect.value === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a product to refund.'
                });
                return;
            }
            
            // Validate quantity
            const quantityInput = document.getElementById('quantity_refunded');
            if (!quantityInput.value || parseInt(quantityInput.value) < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter a valid quantity to refund.'
                });
                return;
            }
            
            const formData = new FormData(this);
            const selectedOption = saleItemSelect.options[saleItemSelect.selectedIndex];
            
            // Add product_id to form data
            formData.append('product_id', selectedOption.dataset.productId || '');
            
            Swal.fire({
                title: 'Processing Refund...',
                html: 'Please wait while we process your refund.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/admin/refunds', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Close the modal
                        bootstrap.Modal.getInstance(document.getElementById('refundModal')).hide();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Refund Processed!',
                            text: `Item has been refunded and recorded in the system.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Refresh the sale items to update available quantities
                        setTimeout(() => {
                            refreshSaleItems(currentSale);
                        }, 1000);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred while processing the refund.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing the refund.'
                });
            });
        });
        
        function showTodaysRevenueModal() {
            const modal = new bootstrap.Modal(document.getElementById('todaysRevenueModal'));
            modal.show();
            
            // Reset content
            document.getElementById('revenueLoading').style.display = 'block';
            document.getElementById('revenueContent').style.display = 'none';
            
            // Fetch today's revenue data
            const revenueUrl = "{{ route('admin.sales.todays-revenue') }}?t=" + Date.now();
            console.log('Revenue URL:', revenueUrl);
            fetch(revenueUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('revenueLoading').style.display = 'none';
                    document.getElementById('revenueContent').style.display = 'block';
                    
                    // Populate table
                    const tbody = document.getElementById('revenueTableBody');
                    tbody.innerHTML = '';
                    
                    if (data.sales && data.sales.length > 0) {
                        data.sales.forEach(sale => {
                            const row = `
                                <tr>
                                    <td>${new Date(sale.created_at).toLocaleTimeString()}</td>
                                    <td>${sale.id}</td>
                                    <td>₱${parseFloat(sale.total_amount).toFixed(2)}</td>
                                    <td>${sale.branch_name || 'N/A'}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No sales today.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching revenue data:', error);
                    document.getElementById('revenueLoading').style.display = 'none';
                    document.getElementById('revenueContent').style.display = 'block';
                    document.getElementById('revenueTableBody').innerHTML = 
                        '<tr><td colspan="4" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
                });
        }
        
        function showThisMonthSalesModal() {
            const modal = new bootstrap.Modal(document.getElementById('thisMonthSalesModal'));
            modal.show();
            
            // Reset content
            document.getElementById('monthlyLoading').style.display = 'block';
            document.getElementById('monthlyContent').style.display = 'none';
            
            // Fetch this month's sales data
            fetch("{{ route('admin.sales.this-month-sales') }}?t=" + Date.now())
                .then(response => response.json())
                .then(data => {
                    document.getElementById('monthlyLoading').style.display = 'none';
                    document.getElementById('monthlyContent').style.display = 'block';
                    
                    // Populate table
                    const tbody = document.getElementById('monthlyTableBody');
                    tbody.innerHTML = '';
                    
                    if (data.daily_sales && data.daily_sales.length > 0) {
                        data.daily_sales.forEach(day => {
                            const row = `
                                <tr>
                                    <td>${new Date(day.date).toLocaleDateString()}</td>
                                    <td>₱${parseFloat(day.total_sales).toFixed(2)}</td>
                                    <td>${day.transactions}</td>
                                    <td>₱${parseFloat(day.average).toFixed(2)}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No sales this month.</td></tr>';
                    }
                })
                .catch(error => {
                    document.getElementById('monthlyLoading').style.display = 'none';
                    document.getElementById('monthlyContent').style.display = 'block';
                    document.getElementById('monthlyTableBody').innerHTML = 
                        '<tr><td colspan="4" class="text-center text-danger">Error loading monthly data. Please try again.</td></tr>';
                });
        }
        
        function viewTodaysSales() {
            window.location.href = "{{ route('admin.reports.index', ['date' => now()->format('Y-m-d')]) }}";
        }
        
        function viewTodaysRevenueReports() {
            window.location.href = "{{ route('admin.reports.index', ['date' => now()->format('Y-m-d')]) }}";
        }
        
        function filterSalesByDate() {
            const dateFilter = document.getElementById('salesDateFilter').value;
            if (dateFilter) {
                window.location.href = "{{ route('admin.sales.index') }}?date=" + dateFilter;
            } else {
                window.location.href = "{{ route('admin.sales.index') }}";
            }
        }
        
        function searchSales() {
            const searchInput = document.getElementById('salesSearchInput').value.toLowerCase();
            const tableRows = document.querySelectorAll('#salesTableBody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchInput)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</div>

<!-- Items Sold Today Modal -->
<div class="modal fade" id="itemsSoldTodayModal" tabindex="-1" aria-labelledby="itemsSoldTodayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="itemsSoldTodayModalLabel">Items Sold Today - {{ now()->format('F d, Y') }}</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary btn-sm me-2" onclick="viewTodaysSales()">
                        <i class="fas fa-list me-1"></i>View More
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            
            <div class="modal-body">
                <div class="text-center" id="itemsLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading items sold today...</p>
                </div>
                <div id="itemsContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Branch</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Today's Revenue Modal -->
<div class="modal fade" id="todaysRevenueModal" tabindex="-1" aria-labelledby="todaysRevenueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="todaysRevenueModalLabel">Today's Revenue - {{ now()->format('F d, Y') }}</h5>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-primary btn-sm me-2" onclick="viewTodaysRevenueReports()">
                        <i class="fas fa-chart-bar me-1"></i>View More
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center" id="revenueLoading">
                    <div class="spinner-border text-success" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading today's revenue details...</p>
                </div>
                <div id="revenueContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Branch</th>
                                </tr>
                            </thead>
                            <tbody id="revenueTableBody">
                                <!-- Sales data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- This Month's Sales Modal -->
<div class="modal fade" id="thisMonthSalesModal" tabindex="-1" aria-labelledby="thisMonthSalesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="thisMonthSalesModalLabel">This Month's Sales - {{ now()->format('F Y') }}</h5>
                <div class="d-flex align-items-center">                  
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="text-center" id="monthlyLoading">
                    <div class="spinner-border text-warning" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading this month's sales details...</p>
                </div>
                <div id="monthlyContent" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Sales</th>
                                    <th>Transactions</th>
                                    <th>Average</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyTableBody">
                                <!-- Monthly data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script>
        function showItemsSoldTodayModal() {
            const modal = new bootstrap.Modal(document.getElementById('itemsSoldTodayModal'));
            modal.show();
            
            // Reset content
            document.getElementById('itemsLoading').style.display = 'block';
            document.getElementById('itemsContent').style.display = 'none';
            
            // Fetch today's items
            fetch("{{ route('admin.sales.items-today') }}?t=" + Date.now())
                .then(response => response.json())
                .then(data => {
                    document.getElementById('itemsLoading').style.display = 'none';
                    document.getElementById('itemsContent').style.display = 'block';
                    
                    const tbody = document.getElementById('itemsTableBody');
                    tbody.innerHTML = '';
                    
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            const row = `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.quantity}</td>
                                    <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                    <td>₱${parseFloat(item.total).toFixed(2)}</td>
                                    <td>${item.branch_name || 'N/A'}</td>
                                    <td>${new Date(item.created_at).toLocaleTimeString()}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No items sold today.</td></tr>';
                    }
                })
                .catch(error => {
                    document.getElementById('itemsLoading').style.display = 'none';
                    document.getElementById('itemsContent').style.display = 'block';
                    document.getElementById('itemsTableBody').innerHTML = 
                        '<tr><td colspan="6" class="text-center text-danger">Error loading items. Please try again.</td></tr>';
                });
        }
    </script>
    
    <style>
        .hover-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            background-color: #f8f9fa;
        }
        
        .hover-card:hover .text-gray-800 {
            color: #2563eb !important;
        }
        
        .hover-card:hover .fa-box {
            color: #2563eb !important;
        }
        
        .hover-card:hover .fa-dollar-sign {
            color: #10b981 !important;
        }
        
        .hover-card:hover .fa-calendar {
            color: #f59e0b !important;
        }
    </style>
@endsection
