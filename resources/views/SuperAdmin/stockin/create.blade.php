<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Stock In - SuperAdmin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

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
                        <h4 class="m-0">Add Stock In</h4>
                        <a href="{{ route('superadmin.stockin.index') }}" class="btn btn-outline-primary">Back to Stock List</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.stockin.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="purchase_id" class="form-label">Purchase Reference</label>
                                    <select name="purchase_id" id="purchase_id" class="form-select">
                                        <option value="">-- Select Purchase Reference --</option>
                                        @foreach($purchases as $purchase)
                                            <option value="{{ $purchase->id }}">{{ $purchase->reference_number }} - {{ $purchase->purchase_date ? $purchase->purchase_date->format('M d, Y') : 'Invalid Date' }}</option>
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
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Unit Type</th>
                                                    <th>Purchased Qty</th>
                                                    <th>Stock-In Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchase-items-table-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Stock</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Stock-in Error',
                html: '{!! session('error') !!}',
                confirmButtonText: 'Okay',
                confirmButtonColor: '#ef4444',
                backdrop: `
                    rgba(239, 68, 68, 0.1)
                    left top
                    no-repeat
                `,
                showClass: {
                    popup: 'animate__animated animate__shakeX'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                position: 'top-center'
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
                            console.log('AJAX success. Data received:', data);
                            tableBody.empty();

                            if(data.length > 0) {
                                var itemIndex = 0;
                                $.each(data, function(index, item) {
                                    var product = item.product;
                                    // Check if unit_types exists and is an array (JSON uses snake_case)
                                    var unitTypes = product && product.unit_types ? product.unit_types : [];
                                    var rowspan = unitTypes.length > 0 ? `rowspan="${unitTypes.length}"` : '';

                                    if (unitTypes.length > 0) {
                                        $.each(unitTypes, function(utIndex, unitType) {
                                            var row;
                                            var priceInput = `<input type="number" name="items[${itemIndex}][price]" class="form-control" step="0.01" value="${item.unit_cost}">`;
                                            if (utIndex === 0) {
                                                row = `<tr>
                                                    <td ${rowspan}>${product.product_name}</td>
                                                    <td>
                                                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${product.id}">
                                                        <input type="hidden" name="items[${itemIndex}][unit_type_id]" value="${unitType.id}">
                                                        ${unitType.unit_name}
                                                    </td>
                                                    <td ${rowspan}>${item.quantity}</td>
                                                    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="0"></td>
                                                    <td>${priceInput}</td>
                                                </tr>`;
                                            } else {
                                                row = `<tr>
                                                    <td>
                                                        <input type="hidden" name="items[${itemIndex}][product_id]" value="${product.id}">
                                                        <input type="hidden" name="items[${itemIndex}][unit_type_id]" value="${unitType.id}">
                                                        ${unitType.unit_name}
                                                    </td>
                                                    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="0"></td>
                                                    <td>${priceInput}</td>
                                                </tr>`;
                                            }
                                            tableBody.append(row);
                                            itemIndex++;
                                        });
                                    } else {
                                        var row = `<tr>
                                            <td>${product ? product.product_name : 'Product not found'}</td>
                                            <td>No unit type defined</td>
                                            <td>${item.quantity}</td>
                                            <td>
                                                <input type="hidden" name="items[${itemIndex}][product_id]" value="${product.id}">
                                                <input type="hidden" name="items[${itemIndex}][unit_type_id]" value="">
                                                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="0" value="${item.quantity}">
                                            </td>
                                            <td><input type="number" name="items[${itemIndex}][price]" class="form-control" step="0.01" value="${item.unit_cost}"></td>
                                        </tr>`;
                                        tableBody.append(row);
                                        itemIndex++;
                                    }
                                });
                                container.show();
                            } else {
                                container.hide();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX error:', textStatus, errorThrown);
                            console.error('Response Text:', jqXHR.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Fetching Items',
                                text: 'Could not retrieve items for the selected purchase. Please check the console for details.',
                                confirmButtonText: 'Understood',
                                confirmButtonColor: '#ef4444',
                                backdrop: `
                                    rgba(239, 68, 68, 0.1)
                                    left top
                                    no-repeat
                                `,
                                showClass: {
                                    popup: 'animate__animated animate__shakeX'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp'
                                },
                                position: 'top-center'
                            });
                        }
                    });
                } else {
                    tableBody.empty();
                    container.hide();
                }
            });
        });
    </script>
</body>
</html>
