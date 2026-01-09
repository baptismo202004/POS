@php
    // Expected variables from controller:
    // $products (paginated)
@endphp

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product List - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind Play CDN (for utility classes) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{ --theme-color: #2563eb; }
        .theme-bg{ background-color: var(--theme-color) !important; }
        .theme-border{ border-color: var(--theme-color) !important; }
        .theme-text{ color: var(--theme-color) !important; }
        /* small helper to mix Bootstrap and tailwind spacing */
        .card-rounded{ border-radius: 12px; }
    </style>
</head>
<body class="bg-white">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">Product List</h2>
                                <a href="{{ route('superadmin.products.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add New Product</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Barcode</th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th>Product Type</th>
                                            <th>Unit Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($products as $product)
                                            <tr>
                                                <td>{{ $product->id }}</td>
                                                <td>{{ $product->product_name }}</td>
                                                <td>{{ $product->barcode }}</td>
                                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                                <td>{{ $product->productType->name ?? 'N/A' }}</td>
                                                <td>{{ $product->unitType->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($product->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('superadmin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <form action="{{ route('superadmin.products.destroy', $product) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
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

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>