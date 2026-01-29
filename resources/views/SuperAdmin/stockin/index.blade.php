@extends('layouts.app')
@section('title', 'Purchase')

@section('content')
    <div class="p-3 p-lg-4">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="card card-rounded shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Stock In</h4>
                        <a href="{{ route('superadmin.stockin.create') }}" class="btn btn-primary">Add Stock In</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
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
                                            <td>
                                                @if($stock->product)
                                                    <a href="{{ route('superadmin.products.show', $stock->product->id) }}">{{ $stock->product->product_name }}</a>
                                                @else
                                                    <span class="text-muted">Product not found</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($stock->branch)
                                                    {{ $stock->branch->branch_name }}
                                                @else
                                                    <span class="text-muted">Branch not found</span>
                                                @endif
                                            </td>
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
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
@endsection
