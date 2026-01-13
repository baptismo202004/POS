<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchases - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind Play CDN (for utility classes) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root{ --theme-color: #2563eb; }
        .theme-bg{ background-color: var(--theme-color) !important; }
        .theme-border{ border-color: var(--theme-color) !important; }
        .theme-text{ color: var(--theme-or) !important; }
        .card-rounded{ border-radius: 12px; }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                <h2 class="m-0">Purchases</h2>
                                <a href="{{ route('superadmin.purchases.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add New Purchase</a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Reference Numbers</th>
                                            <th>Purchase Date</th>
                                            <th>Branch</th>
                                            <th>Items</th>
                                            <th>Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($purchases as $purchase)
                                            <tr>
                                                <td>{{ $purchase->items->pluck('reference_number')->filter()->implode(', ') ?: 'N/A' }}</td>
                                                <td>{{ optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A' }}</td>
                                                <td>{{ $purchase->branch->branch_name ?? 'N/A' }}</td>
                                                <td>{{ $purchase->items->count() }} item(s)</td>
                                                <td><strong>₱{{ number_format($purchase->total_cost, 2) }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No purchases found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{ $purchases->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: 'var(--theme-color)',
                });
            @endif
        });
    </script>
</body>
</html>
