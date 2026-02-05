<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment History - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        :root{ --theme-color: #2563eb; }
        .card-rounded{ border-radius: 12px; }
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
                        <div>
                            <h4 class="m-0">Payment History</h4>
                            <p class="mb-0 text-muted">Track all customer payments and credit transactions</p>
                        </div>
                        <a href="{{ route('superadmin.admin.sales.index') }}" class="btn btn-primary">Go to Sales</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Credit Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Remaining Balance</th>
                                        <th>Status</th>
                                        <th>Cashier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                            <td>{{ $payment->customer_name ?? 'Walk-in Customer' }}</td>
                                            <td>₱{{ number_format($payment->credit_amount, 2) }}</td>
                                            <td>₱{{ number_format($payment->paid_amount, 2) }}</td>
                                            <td>₱{{ number_format($payment->remaining_balance, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status == 'active' ? 'primary' : ($payment->status == 'paid' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->cashier->name ?? 'Unknown' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No payment history found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if($payments->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $payments->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
