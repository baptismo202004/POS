@extends('layouts.app')
@section('title', 'Add Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="d-flex min-vh-100">
    <div class="container-fluid stockin-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h2 class="m-0">Add Stock In</h2>
                            </div>

                            <form action="{{ route('cashier.stockin.store') }}" method="POST">
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
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Stock-in Error',
        html: '{!! session('error') !!}',
        confirmButtonText: 'Okay',
        confirmButtonColor: 'var(--theme-color)',
    });
@endif

const purchaseSelect = document.getElementById('purchase_id');
const tableBody = document.getElementById('purchase-items-table-body');
const container = document.getElementById('purchase-items-form-container');

function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function (c) {
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]);
    });
}

if (purchaseSelect) {
    purchaseSelect.addEventListener('change', async function () {
        const purchaseId = purchaseSelect.value;
        tableBody.innerHTML = '';

        if (!purchaseId) {
            container.style.display = 'none';
            return;
        }

        try {
            const res = await fetch('{{ url('cashier/stockin/products-by-purchase') }}/' + purchaseId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!res.ok) throw new Error('Request failed');

            const items = await res.json();
            if (!Array.isArray(items) || items.length === 0) {
                container.style.display = 'none';
                return;
            }

            items.forEach((item, idx) => {
                const productName = item.product?.product_name || 'N/A';
                const purchasedQty = item.quantity || 0;

                let unitTypeOptions = '<option value="">-- Select Unit --</option>';
                const unitTypes = item.product?.unit_types || item.product?.unitTypes || [];
                if (Array.isArray(unitTypes)) {
                    unitTypes.forEach(u => {
                        unitTypeOptions += `<option value="${u.id}">${escapeHtml(u.unit_name)}</option>`;
                    });
                }

                tableBody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td>
                            ${escapeHtml(productName)}
                            <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                        </td>
                        <td>
                            <select class="form-select" name="items[${idx}][unit_type_id]">
                                ${unitTypeOptions}
                            </select>
                        </td>
                        <td>${purchasedQty}</td>
                        <td>
                            <input type="number" class="form-control" name="items[${idx}][quantity]" min="0" value="0">
                        </td>
                        <td>
                            <input type="number" class="form-control" name="items[${idx}][price]" min="0" step="0.01" value="0.00" required>
                        </td>
                    </tr>
                `);
            });

            container.style.display = '';
        } catch (e) {
            container.style.display = 'none';
        }
    });
}
</script>
@endpush
