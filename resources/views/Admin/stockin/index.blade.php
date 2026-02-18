@extends('layouts.app')
@section('title', 'Stock In')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="container-fluid p-4">
    <div class="row mb-6">
        <div class="col-12">
            <div class="p-4 card-rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="m-0">Stock In</h2>
                    <a href="{{ route('admin.stockin.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add Stock In</a>
                </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><a href="{{ route('admin.stockin.index', ['sort' => 'product', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Product</a></th>
                                            <th>Purchase Ref</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stockIns as $stock)
                                            <tr>
                                                <td>{{ $stock->product->product_name ?? 'N/A' }}</td>
                                                <td>{{ $stock->purchase->reference_number ?? 'N/A' }}</td>
                                                <td>{{ $stock->quantity }}</td>
                                                <td>{{ number_format($stock->price, 2) }}</td>
                                                <td>{{ optional($stock->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No stock records found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $stockIns->links() }}
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

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Stock-in Error',
            html: '{!! session('error') !!}',
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif
});
</script>
@endpush
