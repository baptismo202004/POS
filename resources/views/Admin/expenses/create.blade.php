@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h1 class="h3 mb-0 text-gray-800">Add New Expense</h1>
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
                                    <input type="date" class="form-control" name="expense_date" id="expense_date" required>
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
                                    <label for="supplier" class="form-label">Supplier</label>
                                    <label for="supplier_id" class="form-label">Supplier</label>
                                    <select class="form-select" name="supplier_id" id="supplier_id">
                                        <option value="" selected>Select a supplier (optional)</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Link this expense to a supplier.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="reference_number" class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="reference_number" id="reference_number" placeholder="e.g., Invoice #, OR #">
                                    <div class="form-text">Optional reference for tracking.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section C: Receipt Attachment -->
                        <hr class="my-4">
                        <h5>Receipt Attachment</h5>
                        <div class="mb-3">
                            <label for="receipt" class="form-label">Upload Receipt (Image or PDF)</label>
                            <input class="form-control" type="file" name="receipt" id="receipt" accept="image/*,.pdf">
                            <div class="form-text">You can drag and drop a file here.</div>
                            {{-- Preview thumbnail would be dynamically generated here --}}
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.expenses.index') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Expense</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#expense_category_id').select2({
        placeholder: 'Select or create a category',
        tags: true,
        ajax: {
            url: '{{ route("admin.expense-categories.search") }}',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // add additional parameters
            }
        }
    }).on('select2:select', function (e) {
        var data = e.params.data;
        if (data.newTag) {
            $.ajax({
                url: '{{ route("admin.expense-categories.store") }}',
                method: 'POST',
                data: {
                    name: data.text,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    var newOption = new Option(response.text, response.id, true, true);
                    $('#expense_category_id').append(newOption).trigger('change');
                }
            });
        }
    });
});
</script>
@endpush
