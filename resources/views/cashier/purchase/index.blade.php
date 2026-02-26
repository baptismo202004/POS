@extends('layouts.app')
@section('title', 'Purchases')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 card-rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="m-0">Purchases</h2>
                    <a href="{{ route('cashier.purchases.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add New Purchase</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Reference Number</th>
                                <th>Purchase Date</th>
                                <th>Payment Status</th>
                                <th>Items</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->reference_number ?: 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('cashier.purchases.show', $purchase) }}">
                                            {{ optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($purchase->payment_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $purchase->items->count() }} item(s)</td>
                                    <td><strong>₱{{ number_format($purchase->total_cost, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No purchases found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif
});
</script>
@endpush
