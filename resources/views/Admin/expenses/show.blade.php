<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expense Details - SuperAdmin</title>

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

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="card card-rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Expense Details</h4>
                        <a href="{{ route('superadmin.admin.expenses.index') }}" class="btn btn-outline-primary">Back to Expenses</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Expense Details</h5>
                                @if($expense->purchase_id)
                                    <div class="alert alert-info" role="alert">
                                        <i class="fas fa-link me-1"></i>
                                        This expense is linked to Purchase #{{ $expense->purchase_id }}.
                                    </div>
                                @endif
                                <dl class="row">
                                    <dt class="col-sm-4">Category:</dt>
                                    <dd class="col-sm-8"><span class="badge bg-secondary">{{ $expense->category->name }}</span></dd>

                                    <dt class="col-sm-4">Date:</dt>
                                    <dd class="col-sm-8">{{ $expense->expense_date->format('F j, Y') }}</dd>

                                    <dt class="col-sm-4">Amount:</dt>
                                    <dd class="col-sm-8">₱{{ number_format($expense->amount, 2) }}</dd>

                                    <dt class="col-sm-4">Payment Method:</dt>
                                    <dd class="col-sm-8">{{ $expense->payment_method }}</dd>

                                    <dt class="col-sm-4">Description:</dt>
                                    <dd class="col-sm-8">{{ $expense->description ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Supplier:</dt>
                                    <dd class="col-sm-8">{{ $expense->supplier->name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Reference:</dt>
                                    <dd class="col-sm-8">{{ $expense->reference_number ?? 'N/A' }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Receipt</h5>
                                @if($expense->receipt_path)
                                    @php
                                        $extension = pathinfo($expense->receipt_path, PATHINFO_EXTENSION);
                                    @endphp
                                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $expense->receipt_path) }}" alt="Receipt Preview" class="img-fluid rounded border" style="max-height: 400px;">
                                        </a>
                                    @elseif($extension == 'pdf')
                                        <div class="alert alert-secondary">
                                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="btn btn-primary"><i class="fas fa-file-pdf me-2"></i>View PDF Receipt</a>
                                        </div>
                                    @else
                                        <div class="alert alert-secondary">
                                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank">Download Receipt</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        No receipt attached.
                                    </div>
                                @endif
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
