@extends('layouts.app')
@section('title', 'Inventory')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="card card-rounded shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Inventory</h4>
                        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search products..." value="{{ request('search') }}">
                    </div>
                    <div class="card-body">
                        @if(request('filter') == 'out-of-stock')
                            <div class="alert alert-info" role="alert">
                                <strong>Filter Applied:</strong> Showing only out of stock items (≤ 15 units)
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">Product</a></th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">Current Stock</a></th>
                                        <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'total_sold', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">Total Sold</a></th>
                                        <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'total_revenue', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}">Total Revenue</a></th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $product)
                                        <tr class="{{ request('filter') == 'out-of-stock' && $product->current_stock <= 15 ? 'table-danger' : '' }}">
                                            <td><a href="{{ route('superadmin.products.show', $product->id) }}">{{ $product->product_name }}</a></td>
                                            <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                            <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                            <td class="{{ request('filter') == 'out-of-stock' && $product->current_stock <= 15 ? 'text-danger font-weight-bold' : '' }}">{{ $product->current_stock }}</td>
                                            <td>{{ $product->total_sold }}</td>
                                            <td>{{ number_format($product->total_revenue, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.stockin.index') }}" class="btn btn-sm btn-primary">Manage Stock</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No products found.</td>
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
        </main>
    </div>

    
    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to update dashboard alerts
            function updateDashboardAlerts(outOfStockCount) {
                try {
                    console.log('Updating dashboard alerts with count:', outOfStockCount);
                    
                    // Try multiple selectors for the alerts count element
                    const totalAlertsElement = document.getElementById('totalAlertsCount') || 
                                          document.querySelector('[id="totalAlertsCount"]') ||
                                          document.querySelector('.widget-badge.alert-count');
                    
                    console.log('Found alerts element:', totalAlertsElement);
                    
                    if (totalAlertsElement) {
                        // Get current alerts from dashboard if available
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
                            console.log('Dashboard data received:', data);
                            if (data.alerts) {
                                const totalAlerts = data.alerts.outOfStock + data.alerts.negativeProfit + data.alerts.voidedSales + data.alerts.belowCostSales + data.alerts.highDiscountUsage;
                                totalAlertsElement.textContent = totalAlerts;
                                console.log('Updated total alerts to:', totalAlerts);
                                
                                // Try to update alerts list with multiple selectors
                                const alertsList = document.getElementById('alertsList') || 
                                                   document.querySelector('[id="alertsList"]') ||
                                                   document.querySelector('.alerts-list');
                                
                                console.log('Found alerts list element:', alertsList);
                                
                                if (alertsList) {
                                    const alertItems = [];
                                    
                                    if (data.alerts.outOfStock > 0) {
                                        alertItems.push(`<div class="alert-item critical clickable" onclick="window.location.href='/superadmin/inventory?filter=out-of-stock'"><i class="fas fa-exclamation-triangle alert-icon" style="color:#E91E63"></i><div class="alert-content"><div class="alert-title">${data.alerts.outOfStock} items out of stock</div><div class="alert-description">Restock needed</div></div></div>`);
                                    }
                                    
                                    alertsList.innerHTML = alertItems.length > 0 ? alertItems.join('') : '<div class="alert-item" style="border-left-color:#43A047;background:rgba(67,160,71,0.05)"><i class="fas fa-check-circle alert-icon" style="color:#43A047"></i><div class="alert-content"><div class="alert-title">No alerts</div><div class="alert-description">All systems normal</div></div></div>';
                                    console.log('Updated alerts list');
                                } else {
                                    console.log('Alerts list element not found');
                                }
                            } else {
                                console.log('No alerts data in response');
                            }
                        })
                        .catch(error => {
                            console.error('Error updating dashboard alerts:', error);
                        });
                    } else {
                        console.log('Alerts count element not found - trying alternative approach');
                        
                        // Alternative: Try to update via parent window if in iframe
                        if (window.parent && window.parent.document) {
                            const parentAlertsElement = window.parent.document.getElementById('totalAlertsCount');
                            if (parentAlertsElement) {
                                console.log('Found alerts element in parent window');
                                parentAlertsElement.textContent = outOfStockCount;
                            }
                        }
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
    </script>
@endsection
