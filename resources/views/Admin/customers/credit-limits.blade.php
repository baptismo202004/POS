<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credit Limits - SuperAdmin</title>

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
                            <h4 class="m-0">Credit Limits</h4>
                            <p class="mb-0 text-muted">Overview of customer credit limits and usage</p>
                        </div>
                        <a href="{{ route('superadmin.admin.sales.index') }}" class="btn btn-primary">Go to Sales</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Credit Limit</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalCreditLimit, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Used</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalUsed, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Available Credit</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalCreditLimit - $totalUsed, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Customer Name</th>
                                        <th>Total Credit</th>
                                        <th>Used Credit</th>
                                        <th>Available Credit</th>
                                        <th>Usage %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <?php
                                        $totalCredit = $customer->credits->sum('credit_amount');
                                        $usedCredit = $customer->credits->sum('paid_amount');
                                        $availableCredit = $totalCredit - $usedCredit;
                                        $usagePercentage = $totalCredit > 0 ? ($usedCredit / $totalCredit) * 100 : 0;
                                        ?>
                                        <tr>
                                            <td>{{ $customer->full_name }}</td>
                                            <td>₱{{ number_format($totalCredit, 2) }}</td>
                                            <td>₱{{ number_format($usedCredit, 2) }}</td>
                                            <td>₱{{ number_format($availableCredit, 2) }}</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar {{ $usagePercentage > 80 ? 'bg-danger' : ($usagePercentage > 60 ? 'bg-warning' : 'bg-success') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $usagePercentage }}%">
                                                        {{ number_format($usagePercentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No customers with credit found.</td>
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
</body>
</html>
