@extends('layouts.app')
@section('title', 'Create Expense')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0">Add New Expenses</h4>
            <a href="{{ route('admin.expenses.index') }}" class="btn btn-light border">Back to Expenses</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                                <!-- Section A: Expense Information -->
                                <div class="col-md-6">
                                    <h5>Expense Information</h5>
                                    <div class="mb-3">
                                        <label for="expense_category_id" class="form-label">Expense Category <span class="text-danger">*</span></label>
                                        <select class="form-select" name="expense_category_id" id="expense_category_id" required>
                                            <option value="" disabled selected>Select a category...</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="expense_date" id="expense_date" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="amount" id="amount" placeholder="0.00" step="0.01" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_method" id="payment_method" required>
                                            <option value="Cash">Cash</option>
                                            <option value="Bank">Bank</option>
                                            <option value="GCash">GCash</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Section B: Additional Details -->
                                <div class="col-md-6">
                                    <h5>Additional Details</h5>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter a short description..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="supplier_name" class="form-label">Supplier</label>
                                        <select class="form-select" name="supplier_name" id="supplier_name">
                                            <option value="">Select a supplier or type new name (optional)</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">Select existing supplier or type to create new one</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reference_number" class="form-label">Reference Number</label>
                                        <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder="e.g., Invoice #, OR #">
                                    </div>
                                </div>
                            </div>

                            <!-- Section C: Receipt Attachment -->
                            <hr class="my-4">
                            <h5>Receipt Attachment</h5>
                            <div class="mb-3">
                                <label for="receipt" class="form-label">Upload Receipt (Image or PDF)</label>
                                <input class="form-control" type="file" name="receipt" id="receipt" accept="image/*,.pdf">
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">Save Expense</button>
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
        $('#expense_category_id').select2();
        $('#supplier_name').select2({
            tags: true,
            createTag: function (params) {
                // Don't create empty tags
                if ($.trim(params.term) === '') {
                    return null;
                }
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                };
            }
        });
        
        // Auto-select payment method based on amount
        $('#amount').on('input change keyup paste', function() {
            const amount = parseFloat($(this).val()) || 0;
            const paymentMethod = $('#payment_method');
            
            console.log('Amount changed:', amount); // Debug log
            
            // Logic for automatic payment method selection
            if (amount <= 1000) {
                paymentMethod.val('Cash');
                console.log('Selected: Cash');
            } else if (amount <= 10000) {
                paymentMethod.val('GCash');
                console.log('Selected: GCash');
            } else if (amount <= 50000) {
                paymentMethod.val('Bank');
                console.log('Selected: Bank');
            } else {
                paymentMethod.val('Bank');
                console.log('Selected: Bank (large amount)');
            }
            
            // Add visual feedback
            paymentMethod.addClass('border-success');
            setTimeout(() => {
                paymentMethod.removeClass('border-success');
            }, 1000);
        });
        
        // Trigger once on page load in case there's a default value
        $('#amount').trigger('input');
    });
    </script>
@endsection
