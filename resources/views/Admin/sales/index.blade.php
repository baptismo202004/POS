@extends('layouts.app')

@section('content')
    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h2 class="m-0">Sales Dashboard</h2>
                                    <p class="mb-0 text-muted">Overview of your sales performance</p>
                                </div>
                                <a href="{{ route('pos.index') }}" class="btn btn-primary">Go to POS</a>
                            </div>
                        <div class="row">
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Sales</div>
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
                                <div class="card border-left-success shadow h-100 py-2">
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
                                <div class="card border-left-info shadow h-100 py-2">
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
                                <div class="card border-left-warning shadow h-100 py-2">
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

                <!-- Recent Sales Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Sales</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Cashier</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Date</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSales as $sale)
                                        <tr>
                                            <td>{{ $sale->product_names ?: 'No products' }}</td>
                                            <td>{{ $sale->cashier->name ?? 'Unknown' }}</td>
                                            <td>{{ $sale->saleItems->sum('quantity') }} items</td>
                                            <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                            <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                            <td><span class="badge bg-success">{{ ucfirst($sale->payment_method) }}</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" onclick="openRefundModal({{ $sale->id }})">
                                                    <i class="fas fa-undo"></i> Refund
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No sales found for today.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
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
                            <textarea class="form-control" name="reason" id="reason" rows="3" required></textarea>
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
    
    <script>
        let currentSale = null;
        
        function openRefundModal(saleId) {
            currentSale = saleId;
            document.getElementById('sale_id').value = saleId;
            
            // Load sale items for this sale
            fetch(`/admin/sales/${saleId}/items`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('sale_item_id');
                    select.innerHTML = '<option value="">Choose a product...</option>';
                    
                    data.items.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = `${item.product_name} (Available: ${item.available_for_refund})`;
                        option.dataset.price = item.unit_price;
                        option.dataset.maxQuantity = item.available_for_refund;
                        option.dataset.productId = item.product_id;
                        select.appendChild(option);
                    });
                });
            
            const modal = new bootstrap.Modal(document.getElementById('refundModal'));
            modal.show();
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
            
            const formData = new FormData(this);
            const select = document.getElementById('sale_item_id');
            const selectedOption = select.options[select.selectedIndex];
            
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
                        bootstrap.Modal.getInstance(document.getElementById('refundModal')).hide();
                        location.reload();
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
    </script>
@endsection
