<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Details - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
        .table th {
            font-weight: 600;
            color: #475569;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-light">

    <div class="d-flex min-vh-100">
        {{-- Sidebar --}}
        @include('layouts.AdminSidebar')

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="card card-rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Purchase Details</h4>
                        <a href="{{ route('superadmin.purchases.index') }}" class="btn btn-outline-primary">Back to Purchases</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Reference Number</p>
                                <p class="fw-semibold">{{ $purchase->reference_number ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Purchase Date</p>
                                <p class="fw-semibold">{{ optional($purchase->purchase_date)->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Payment Status</p>
                                <p><span class="badge fs-6 {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($purchase->payment_status) }}</span></p>
                            </div>
                        </div>

                        <h5 class="mt-4">Purchased Items</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Type</th>
                                        <th>Unit Cost</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchase->items as $item)
                                        <tr>
                                            <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->unitType->unit_name ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                                            <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No items found for this purchase.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
