@extends('layouts.app')
@section('title', 'Add Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v={{ time() }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v={{ rand(1000, 9999) }}"></script>
    <style>
        .card-rounded{ border-radius: 12px; }
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-6">
        <div class="col-12">
            <div class="p-4 card-rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="m-0">Add Stock In</h2>
                    <a href="{{ route('admin.stockin.index') }}" class="btn btn-outline-primary">Back to Stock List</a>
                </div>

                <form action="{{ route('admin.stockin.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_id" class="form-label">Purchase Reference</label>
                                        <select name="purchase_id" id="purchase_id" class="form-select" required>
                                            <option value="">-- Select Purchase Reference --</option>
                                            @foreach($purchases as $purchase)
                                                <option value="{{ $purchase->id }}">{{ $purchase->reference_number ?: 'N/A' }} - {{ optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                 
                                </div>

                                <div id="purchase-items-form-container" class="mb-3" style="display: none;">
                                    <h5 class="mt-4">Items to Stock In</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Unit Type</th>
                                                    <th>Purchased Qty</th>
                                                    <th>Stock-In Qty</th>
                                                    <th>Original Price</th>
                                                    <th>New Price</th>
                                                    <th>Branch</th>
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
                </div>
            </div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Success!',
                    text: {!! json_encode(session('success')) !!},
                    icon: 'success',
                    confirmButtonColor: 'var(--theme-color)',
                });
            }
        @endif

        var purchaseSelect = document.getElementById('purchase_id');
        var tableBody = document.getElementById('purchase-items-table-body');
        var container = document.getElementById('purchase-items-form-container');
        var form = document.querySelector('form[action*="stockin"]');

        function escapeHtml(s) {
            return String(s || '').replace(/[&<>"']/g, function (c) {
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'':'&#39;'}[c];
            });
        }

        if (purchaseSelect) {
            purchaseSelect.addEventListener('change', async function () {
                var purchaseId = purchaseSelect.value;
                tableBody.innerHTML = '';

                if (!purchaseId) {
                    container.style.display = 'none';
                    return;
                }

                try {
                    var res = await fetch('{{ url('admin/stockin/products-by-purchase') }}/' + purchaseId, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    
                    if (!res.ok) {
                        throw new Error('HTTP error! status: ' + res.status);
                    }
                    
                    var data = await res.json();

                    if (data.items && data.items.length > 0) {
                        var idx = 0;
                        var branchOptions = @json($branches->map(function($branch) {
                            return ['id' => $branch->id, 'name' => $branch->branch_name];
                        }));
                        
                        data.items.forEach(function(item) {
                            var productName = item.product ? item.product.product_name : 'N/A';
                            var purchasedQty = item.quantity || 0;
                            
                            var unitTypeOptions = '<option value="">No unit types</option>';
                            if (item.unit_types && item.unit_types.length > 0) {
                                unitTypeOptions = item.unit_types.map(function(ut) {
                                    return '<option value="' + ut.id + '">' + ut.unit_name + '</option>';
                                }).join('');
                            }

                            var branchSelectOptions = '';
                            branchOptions.forEach(function(branch) {
                                branchSelectOptions += '<option value="' + branch.id + '">' + branch.name + '</option>';
                            });

                            var rowHtml = 
                                '<tr>' +
                                    '<td>' + 
                                        escapeHtml(productName) +
                                        '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.product_id + '">' +
                                    '</td>' +
                                    '<td>' +
                                        '<select class="form-select" name="items[' + idx + '][unit_type_id]">' +
                                            unitTypeOptions +
                                        '</select>' +
                                    '</td>' +
                                    '<td>' + purchasedQty + '</td>' +
                                    '<td>' +
                                        '<input type="number" class="form-control" name="items[' + idx + '][quantity]" min="0" value="0">' +
                                    '</td>' +
                                    '<td>' +
                                        '<input type="number" class="form-control" name="items[' + idx + '][original_price]" min="0" step="0.01" value="' + (item.unit_price || '0.00') + '" readonly>' +
                                    '</td>' +
                                    '<td>' +
                                        '<input type="number" class="form-control" name="items[' + idx + '][new_price]" min="0" step="0.01" value="0.00" required>' +
                                    '</td>' +
                                    '<td>' +
                                        '<select class="form-select" name="items[' + idx + '][branch_id]" required>' +
                                            '<option value="">-- Select Branch --</option>' +
                                            branchSelectOptions +
                                        '</select>' +
                                    '</td>' +
                                '</tr>';
                            
                            tableBody.insertAdjacentHTML('beforeend', rowHtml);
                            idx++;
                        });

                        container.style.display = '';
                    } else {
                        container.style.display = 'none';
                    }
                } catch (e) {
                    console.error('Fetch error:', e);
                    container.style.display = 'none';
                }
            });
        }

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                var formData = new FormData(form);
                
                try {
                    var response = await fetch('{{ route('admin.stockin.store') }}', {
                        method: 'POST',
                        body: formData
                    });
                    
                    var result = await response.json();
                    
                    if (result.success) {
                        var inputs = form.querySelectorAll('input[name$="quantity"]');
                        inputs.forEach(function(input) {
                            var currentValue = parseInt(input.value) || 0;
                            if (currentValue > 0) {
                                input.value = currentValue - parseInt(input.value);
                            }
                        });
                        
                        form.reset();
                        container.style.display = 'none';
                        tableBody.innerHTML = '';
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: result.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Success: ' + result.message);
                        }
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: result.message || 'Something went wrong',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            alert('Error: ' + (result.message || 'Something went wrong'));
                        }
                    }
                } catch (error) {
                    console.error('Submit error:', error);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Error: Something went wrong. Please try again.');
                    }
                }
            });
        }
    });
})();
</script>
@endpush
