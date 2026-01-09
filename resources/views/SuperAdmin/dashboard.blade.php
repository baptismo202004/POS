<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CDN (dev only) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">

<div class="d-flex min-vh-100">

    <!-- Sidebar -->
    <aside class="bg-white border-end w-64 p-4 d-none d-lg-block">
        <div class="d-flex align-items-center gap-2 mb-4">
            <div class="rounded-circle bg-indigo-600 text-white d-flex align-items-center justify-content-center"
                 style="width:40px;height:40px;">P</div>
            <span class="fw-semibold text-lg">BGH POS</span>
        </div>

        <p class="text-muted text-uppercase small mb-2">Menu</p>

        <nav class="d-flex flex-column gap-1">
            <a href="#" class="rounded px-3 py-2 text-decoration-none text-indigo-600 bg-indigo-50 fw-medium">
                📦 Products
            </a>
            <a href="#" class="rounded px-3 py-2 text-decoration-none text-gray-700 hover:bg-gray-100">
                🧾 Orders
            </a>
            <a href="#" class="rounded px-3 py-2 text-decoration-none text-gray-700 hover:bg-gray-100">
                👥 Customers
            </a>
            <a href="#" class="rounded px-3 py-2 text-decoration-none text-gray-700 hover:bg-gray-100">
                📊 Reports
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-fill p-4">

        <!-- Topbar -->
        <div class="bg-white border rounded-3 p-3 mb-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Dashboard</h5>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-indigo-600 px-3 py-2">Admin</span>
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-gray-200 d-flex align-items-center justify-content-center"
                         style="width:36px;height:36px;">A</div>
                    <span class="fw-medium d-none d-md-inline">Admin User</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Total Sales</p>
                        <h4 class="fw-bold">₱45,320</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Orders</p>
                        <h4 class="fw-bold">128</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Products</p>
                        <h4 class="fw-bold">54</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-1">Customers</p>
                        <h4 class="fw-bold">92</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold">
                Recent Transactions
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Juan Dela Cruz</td>
                            <td>Aircon Filter</td>
                            <td>Jan 6, 2026</td>
                            <td class="text-end fw-semibold">₱1,200</td>
                        </tr>
                        <tr>
                            <td>Maria Santos</td>
                            <td>Cleaning Service</td>
                            <td>Jan 5, 2026</td>
                            <td class="text-end fw-semibold">₱2,500</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success') && request()->query('from') !== 'login')
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 2200
    });
</script>
@endif

</body>
</html>
