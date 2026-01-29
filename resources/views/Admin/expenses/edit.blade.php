<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Expense - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
                        <h4 class="m-0">Edit Expense</h4>
                        <a href="{{ route('superadmin.admin.expenses.index') }}" class="btn btn-outline-primary">Back to Expenses</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.admin.expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <!-- Section A: Expense Information -->
                                <div class="col-md-6">
                                    <h5>Expense Information</h5>
                                    <div class="mb-3">
                                        <label for="expense_category_id" class="form-label">Expense Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="expense_category_id" id="expense_category_id" required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="expense_date" id="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="amount" id="amount" value="{{ $expense->amount }}" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_method" id="payment_method" required>
                                            <option value="Cash" {{ $expense->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="Bank" {{ $expense->payment_method == 'Bank' ? 'selected' : '' }}>Bank</option>
                                            <option value="GCash" {{ $expense->payment_method == 'GCash' ? 'selected' : '' }}>GCash</option>
                                            <option value="Others" {{ $expense->payment_method == 'Others' ? 'selected' : '' }}>Others</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Section B: Additional Details -->
                                <div class="col-md-6">
                                    <h5>Additional Details</h5>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="description" rows="3">{{ $expense->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="supplier_id" class="form-label">Supplier</label>
                                        <select class="form-select" name="supplier_id" id="supplier_id">
                                            <option value="">Select a supplier (optional)</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $expense->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" name="reference_number" id="reference_number" value="{{ $expense->reference_number }}" placeholder="e.g., Invoice #, OR #">
                                    </div>
                                </div>
                            </div>

                            <!-- Section C: Receipt Attachment -->
                            <hr class="my-4">
                            <h5>Receipt Attachment</h5>
                            <div class="mb-3">
                                <label for="receipt" class="form-label">Upload New Receipt (Image or PDF)</label>
                                <input class="form-control" type="file" name="receipt" id="receipt" accept="image/*,.pdf">
                                @if($expense->receipt_path)
                                    <div class="form-text">Current receipt: <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank">View Receipt</a></div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">Update Expense</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#expense_category_id, #supplier_id').select2();
    });
    </script>
</body>
</html>
