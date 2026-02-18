<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aging Reports - SuperAdmin</title>

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
                            <h4 class="m-0">Aging Reports</h4>
                            <p class="mb-0 text-muted">Overdue accounts and aging analysis</p>
                        </div>
                        <a href="{{ route('admin.sales.management.index') }}" class="btn btn-primary">Go to Sales</a>
                    </div>
                    <div class="card-body">
                        @forelse($agingReport as $category => $data)
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $category }}</h5>
                                    <div>
                                        <span class="badge bg-primary">{{ $data['count'] }} accounts</span>
                                        <span class="badge bg-danger ms-2">₱{{ number_format($data['total_amount'], 2) }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Customer Name</th>
                                                    <th>Overdue Amount</th>
                                                    <th>Days Overdue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['customers'] as $customer)
                                                    <tr>
                                                        <td>{{ $customer['name'] }}</td>
                                                        <td>₱{{ number_format($customer['amount'], 2) }}</td>
                                                        <td>{{ $customer['days_overdue'] }} days</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>No Overdue Accounts</h5>
                                <p class="text-muted">All customer accounts are up to date!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
