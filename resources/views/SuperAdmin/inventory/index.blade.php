@extends('layouts.app')
@section('title', 'Inventory')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card card-rounded shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Inventory</h4>
                        <input type="text" id="searchInput" class="form-control w-25" placeholder="Search products..." value="{{ request('search') }}">
                    </div>
                    <div class="card-body">
                        @if(request('filter') == 'out-of-stock')
                            <div class="alert alert-info" role="alert">
                                <strong>Filter Applied:</strong> Showing only out of stock items (≤ 10 units)
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
                                        <tr class="{{ request('filter') == 'out-of-stock' && $product->current_stock <= 10 ? 'table-danger' : '' }}">
                                            <td><a href="{{ route('superadmin.products.show', $product->id) }}">{{ $product->product_name }}</a></td>
                                            <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                            <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                            <td class="{{ request('filter') == 'out-of-stock' && $product->current_stock <= 10 ? 'text-danger font-weight-bold' : '' }}">{{ $product->current_stock }}</td>
                                            <td>{{ $product->total_sold }}</td>
                                            <td>{{ number_format($product->total_revenue, 2) }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" data-current-stock="{{ $product->current_stock }}">Adjust Stock</button>
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
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
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

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
@endsection
