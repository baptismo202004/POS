@extends('layouts.app')
@section('title', 'Inventory')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="d-flex min-vh-100">
    <div class="container-fluid inventory-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2 class="m-0">Inventory</h2>
                                <input type="text" id="searchInput" class="form-control" style="max-width: 320px" placeholder="Search products..." value="{{ request('search') }}">
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><a href="{{ route('cashier.inventory.index', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page'])) }}">Product</a></th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th><a href="{{ route('cashier.inventory.index', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page'])) }}">Current Stock</a></th>
                                            <th><a href="{{ route('cashier.inventory.index', ['sort_by' => 'total_sold', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page'])) }}">Total Sold</a></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sortedProducts as $product)
                                            <tr>
                                                <td>{{ $product->product_name }}</td>
                                                <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                                <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                                <td class="{{ ($product->current_stock ?? 0) < 10 ? 'text-danger' : '' }}">{{ $product->current_stock ?? 0 }}</td>
                                                <td>{{ $product->total_sold ?? 0 }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No products found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                                                    </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Use standard CashierSidebar from layouts

    const searchInput = document.getElementById('searchInput');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = searchInput.value;
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                window.location.href = url.toString();
            }, 400);
        });
    }
</script>
@endpush
