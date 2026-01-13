@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Add Stock In</h2>
            <a href="{{ route('superadmin.stockin.index') }}" class="btn btn-outline-primary">Back to Stock List</a>
        </div>

        <form action="{{ route('superadmin.stockin.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="purchase_id" class="form-label">Purchase Reference</label>
                    <select name="purchase_id" id="purchase_id" class="form-select">
                        <option value="">-- Select Purchase Reference --</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}">{{ $purchase->purchase_date->format('M d, Y') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select name="branch_id" id="branch_id" class="form-select" required>
                        <option value="">-- Select Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="purchase-items-form-container" class="col-md-12 mb-3" style="display: none;">
                    <h5 class="mt-4">Items to Stock In</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Reference No.</th>
                                <th>Purchased Qty</th>
                                <th>Stock-In Qty</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody id="purchase-items-table-body"></tbody>
                    </table>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Stock</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Stock-in Error',
            html: '{!! session('error') !!}',
            confirmButtonText: 'Okay',
            confirmButtonColor: '#3085d6',
        });
        @endif

        $('#purchase_id').on('change', function() {
            var purchaseId = $(this).val();
            var tableBody = $('#purchase-items-table-body');
            var container = $('#purchase-items-form-container');

            if (purchaseId) {
                $.ajax({
                    url: '{{ url("superadmin/stockin/products-by-purchase") }}/' + purchaseId,
                    type: "GET",
                    dataType: "json",
                    success:function(data) {
                        tableBody.empty();

                        if(data.length > 0) {
                            $.each(data, function(index, item) {
                                var row = `<tr>
                                    <td>
                                        <input type="hidden" name="items[${index}][product_id]" value="${item.product.id}">
                                        ${item.product.product_name}
                                    </td>
                                    <td>${item.reference_number || 'N/A'}</td>
                                    <td>${item.quantity}</td>
                                    <td><input type="number" name="items[${index}][quantity]" class="form-control" min="0" max="${item.quantity}"></td>
                                    <td><input type="number" name="items[${index}][price]" class="form-control" step="0.01" value="${item.unit_cost}"></td>
                                </tr>`;
                                tableBody.append(row);
                            });
                            container.show();
                        } else {
                            container.hide();
                        }
                    }
                });
            } else {
                tableBody.empty();
                container.hide();
            }
        });
    });
</script>
@endpush
