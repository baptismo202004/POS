@extends('layouts.app')

@section('content')
<style>
    .select2-container--open .select2-dropdown {
        z-index: 99999;
    }
    .select2-container {
        z-index: 99998;
    }
    /* Force standard select to be visible */
    select.form-control {
        display: block !important;
        width: 100% !important;
        height: auto !important;
        padding: 0.375rem 0.75rem !important;
        font-size: 1rem !important;
        line-height: 1.5 !important;
        color: #212529 !important;
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }
</style>
<div class="container-fluid p-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">Inventory</h2>
        <div class="d-flex">
            <input type="text" id="searchInput" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
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
                    <tr>
                        <td><a href="{{ route('superadmin.products.show', $product->id) }}">{{ $product->product_name }}</a></td>
                        <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                        <td class="{{ $product->current_stock < 10 ? 'text-danger' : '' }}">{{ $product->current_stock }}</td>
                        <td>{{ $product->total_sold }}</td>
                        <td>{{ number_format($product->total_revenue, 2) }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" data-current-stock="{{ $product->current_stock }}">Adjust Stock</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="productName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select class="form-control" id="branch_id" name="branch_id" required>
                            <option value="">-- Select Branch --</option>
                        </select>
                        {{-- Debug: {{ $branches->count() }} branches found --}}
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">New Stock Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" min="0" required>
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
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

            // Prevent Select2 from being applied to branch dropdown
            const $branchSelect = $('#adjustStockModal #branch_id');
            $branchSelect.removeClass('select2-hidden-accessible');
            
            // Destroy any existing Select2 instances
            if ($branchSelect.data('select2')) {
                $branchSelect.select2('destroy');
            }
            
            // Remove Select2 containers
            $branchSelect.siblings('.select2').remove();
            
            // Make sure the select is visible
            $branchSelect.show();
            
            // Populate branch dropdown
            try {
                const branches = {!! $branchesJson !!};
                console.log('Branches in modal:', branches);
                console.log('Type of branches:', typeof branches);
                console.log('Is array?', Array.isArray(branches));
                
                $branchSelect.empty().append('<option value="">-- Select Branch --</option>');
                
                if (Array.isArray(branches) && branches.length > 0) {
                    branches.forEach((branch, index) => {
                        console.log(`Branch ${index}:`, branch);
                        console.log(`Branch ID: ${branch.id}, Name: ${branch.branch_name}`);
                        $branchSelect.append(`<option value="${branch.id}">${branch.branch_name}</option>`);
                    });
                    console.log('Branches added to modal dropdown');
                } else {
                    console.error('Branches data is not an array or is empty');
                    // Try to parse as string if it's not an array
                    if (typeof branches === 'string') {
                        try {
                            const parsed = JSON.parse(branches);
                            console.log('Parsed branches from string:', parsed);
                        } catch (e) {
                            console.error('Failed to parse branches string:', e);
                        }
                    }
                }
            } catch (error) {
                console.error('Error parsing branches JSON:', error);
            }
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
</script>
@endpush
