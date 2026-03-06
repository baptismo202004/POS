@extends('layouts.app')

@section('content')
    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">Purchase Details</h2>
                                <div class="d-flex gap-2">
                                    @if($purchase->payment_status === 'pending')
                                        <form method="POST" action="{{ route('superadmin.purchases.mark-paid', $purchase) }}" class="d-inline" data-confirm-mark-paid>
                                            @csrf
                                            <button type="submit" class="btn btn-success">Mark as Paid</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('superadmin.purchases.index') }}" class="btn btn-outline-primary">Back to Purchases</a>
                                </div>
                            </div>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Reference Number</p>
                                <p class="fw-semibold">{{ $purchase->reference_number ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Purchase Date</p>
                                <p class="fw-semibold">{{ optional($purchase->purchase_date)->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="text-muted mb-1">Payment Status</p>
                                <p><span class="badge fs-6 {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($purchase->payment_status) }}</span></p>
                            </div>
                        </div>

                        <h5 class="mt-4">Purchased Items</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Type</th>
                                        <th>Unit Cost</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchase->items as $item)
                                        <tr>
                                            <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->unitType->unit_name ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                                            <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">No items found for this purchase.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($purchase->items->isNotEmpty())
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end fw-semibold">Total Amount</td>
                                            <td class="fw-semibold">₱{{ number_format($purchase->items->sum('subtotal'), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[data-confirm-mark-paid]');
            if (!form || typeof Swal === 'undefined') return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Mark as Paid?'
                    , text: 'This will set the payment status to Paid.'
                    , icon: 'question'
                    , showCancelButton: true
                    , confirmButtonText: 'Yes, mark as paid'
                    , cancelButtonText: 'Cancel'
                    , confirmButtonColor: '#198754'
                    , cancelButtonColor: '#6c757d'
                    , reverseButtons: true
                    , background: '#ffffff'
                    , customClass: {
                        popup: 'shadow-lg rounded-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
