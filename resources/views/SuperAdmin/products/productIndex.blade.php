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

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tailwind Play CDN (for utility classes) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root{ --theme-color: #2563eb; }
        .theme-bg{ background-color: var(--theme-color) !important; }
        .theme-border{ border-color: var(--theme-color) !important; }
        .theme-text{ color: var(--theme-color) !important; }
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
                                <div class="d-flex align-items-center gap-3">
                                    <h2 class="m-0">Product List</h2>
                                    <form action="{{ route('superadmin.products.index') }}" method="GET" class="d-flex">
                                        <input type="text" name="search" id="product-search-input" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary ms-2">Search</button>
                                    </form>
                                </div>
                                <a href="{{ route('superadmin.products.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add New Product</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a href="{{ route('superadmin.products.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                                    ID
                                                    @if ($sortBy == 'id')
                                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
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
                                    <tbody id="product-table-body">
                                        @include('SuperAdmin.products._product_table', ['products' => $products])
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            let searchTimeout;

            $('#product-search-input').on('keyup', function () {
                clearTimeout(searchTimeout);
                const query = $(this).val();

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('superadmin.products.index') }}",
                        type: "GET",
                        data: { 'search': query },
                        success: function(data) {
                            $('#product-table-body').html(data);
                        }
                    });
                }, 300); // 300ms delay
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}'
                });
            @endif
        });
    </script>
</body>
</html>