@extends('layouts.app')
@section('title', 'Sales History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Sales History</h4>
                    <a href="{{ route('cashier.sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Sale
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search sales..." 
                                   value="{{ request('search') }}" id="searchInput">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from" 
                                   value="{{ request('date_from') }}" placeholder="From">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_to" 
                                   value="{{ request('date_to') }}" placeholder="To">
                        </div>
                          <div class="col-md-2">
                            <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
                        </div>
                    </div>
                    <!-- Sales Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ route('cashier.sales.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}">
                                            Receipt # 
                                            @if ($sortBy == 'id')
                                                <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Date & Time</th>
                                    <th>Customer</th>
                                    <th>
                                        <a href="{{ route('cashier.sales.index', ['sort_by' => 'total_amount', 'sort_direction' => ($sortBy == 'total_amount' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}">
                                            Total Amount
                                            @if ($sortBy == 'total_amount')
                                                <i class="fas fa-sort-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr>
                                    <td>
                                        <strong>#{{ $sale->id }}</strong>
                                        @if($sale->receipt_group_id)
                                            <br><small class="text-muted"><i class="fas fa-link"></i> Group: {{ $sale->receipt_group_id }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $sale->customer_name ?: 'Walk-in' }}</td>
                                    <td class="text-end">
                                        ₱{{ number_format($sale->total_amount, 2) }}
                                        @if($sale->receipt_group_id)
                                            <br><small class="text-muted">Branch {{ $sale->branch->branch_name ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : ($sale->payment_method == 'card' ? 'info' : 'warning') }}">
                                            {{ ucfirst($sale->payment_method) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status == 'completed' ? 'success' : ($sale->status == 'voided' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('cashier.pos.receipt', $sale) }}" class="btn btn-outline-success" title="Receipt">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                            @if(strtolower($sale->payment_method) !== 'credit')
                                                <button
                                                    type="button"
                                                    class="btn btn-outline-warning refund-btn"
                                                    title="Refund / Return"
                                                    data-sale-id="{{ $sale->id }}"
                                                    data-payment-method="{{ strtolower($sale->payment_method) }}"
                                                    data-sale-date="{{ $sale->created_at->format('Y-m-d') }}"
                                                    data-products='@json($sale->saleItems->map(function($item){ return ["name" => $item->product->product_name ?? "N/A", "qty" => $item->quantity]; }))'
                                                    onclick="openRefundModal(this)">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @endif
                                            @if($sale->status !== 'voided')
                                                <button class="btn btn-outline-danger" onclick="voidSale({{ $sale->id }})" title="Void">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5>No sales found</h5>
                                        <p class="text-muted">Start by creating your first sale.</p>
                                        
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($sales->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} entries
                            </div>
                            {{ $sales->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openRefundModal(button) {
    const saleId = button.getAttribute('data-sale-id');
    const paymentMethod = button.getAttribute('data-payment-method');
    const saleDateStr = button.getAttribute('data-sale-date');
    const productsJson = button.getAttribute('data-products') || '[]';

    if (paymentMethod === 'credit') {
        Swal.fire({
            icon: 'error',
            title: 'Refund Not Allowed',
            text: 'Refund/Return is not allowed for credit sales.',
            confirmButtonColor: '#ef4444',
        });
        return;
    }

    // Enforce 2-3 day window
    const saleDate = new Date(saleDateStr + 'T00:00:00');
    const today = new Date();
    const diffMs = today - saleDate;
    const diffDays = diffMs / (1000 * 60 * 60 * 24);

    if (diffDays > 3) {
        Swal.fire({
            icon: 'error',
            title: 'Refund Window Expired',
            text: 'Refund/Return is only allowed within 2-3 days from the purchase date.',
            confirmButtonColor: '#ef4444',
        });
        return;
    }

    let products = [];
    try {
        products = JSON.parse(productsJson);
    } catch (e) {
        products = [];
    }

    const productsHtml = products.length
        ? '<ul class="mb-2">' + products.map(p => `<li>${p.name} <span class="text-muted">(x${p.qty})</span></li>`).join('') + '</ul>'
        : '<p class="text-muted mb-2">No product details available.</p>';

    Swal.fire({
        title: 'Refund / Return',
        html: `
            <div class="text-start">
                <p class="mb-1"><strong>Products in this sale:</strong></p>
                ${productsHtml}
                <div class="mb-2">
                    <label class="form-label">Reason for refund/return</label>
                    <textarea id="refund-reason" class="swal2-textarea" placeholder="Enter reason..."></textarea>
                </div>
                <p class="text-muted mb-0">Refund/Return is only allowed within 2-3 days from the purchase date.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Continue to Refund',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#f59e0b',
        preConfirm: () => {
            const reason = document.getElementById('refund-reason').value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please enter a reason for the refund/return.');
                return false;
            }
            return { reason };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Call quick refund endpoint to perform full refund via AJAX
            fetch(`/cashier/sales/${saleId}/quick-refund`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: result.value.reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Refund Processed',
                        html: `<p>The sale has been fully refunded.</p><p class="mb-0"><strong>Status:</strong> ${data.status?.toUpperCase?.() || 'REFUNDED'}</p>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Refund Failed',
                        text: data.message || 'An error occurred while processing the refund.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Quick refund error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'An error occurred while processing the refund. Please try again.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function clearFilters() {
    window.location.href = '{{ route('cashier.sales.index') }}';
}

function voidSale(saleId) {
    if(confirm('Are you sure you want to void this sale? This action cannot be undone.')) {
        fetch(`/cashier/sales/${saleId}/void`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Sale voided successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while voiding the sale');
        });
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if(e.key === 'Enter') {
        const params = new URLSearchParams(window.location.search);
        if(this.value) {
            params.set('search', this.value);
        } else {
            params.delete('search');
        }
        window.location.href = '{{ route('cashier.sales.index') }}?' + params.toString();
    }
});

// Filter change handlers
document.querySelectorAll('select[name], input[name]').forEach(input => {
    if(input.type !== 'text') {
        input.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            if(this.value) {
                params.set(this.name, this.value);
            } else {
                params.delete(this.name);
            }
            window.location.href = '{{ route('cashier.sales.index') }}?' + params.toString();
        });
    }
});
</script>
@endsection
