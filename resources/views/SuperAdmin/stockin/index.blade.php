@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Stock In</h2>
            <a href="{{ route('superadmin.stockin.create') }}" class="btn btn-primary">Add Stock In</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                                                <th><a href="{{ route('superadmin.stockin.index', ['sort' => 'product', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Product</a></th>
                        <th>Branch</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Sold</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockIns as $stock)
                        <tr>
                                                        <td><a href="{{ route('superadmin.products.show', $stock->product->id) }}">{{ $stock->product->product_name ?? 'N/A' }}</a></td>
                            <td>{{ $stock->branch->branch_name ?? 'N/A' }}</td>
                            <td>{{ $stock->quantity }}</td>
                            <td>{{ $stock->price }}</td>
                            <td>{{ $stock->sold }}</td>
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
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
        @endif
    });
</script>
@endpush
