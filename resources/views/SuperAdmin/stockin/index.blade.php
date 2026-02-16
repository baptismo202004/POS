@extends('layouts.app')
@section('title', 'Purchase')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush

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
                showConfirmButton: true,
                confirmButtonText: 'Great!',
                confirmButtonColor: '#10b981',
                backdrop: `
                    rgba(16, 185, 129, 0.1)
                    left top
                    no-repeat
                `,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                },
                position: 'top-center',
                toast: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            @endif
        });
    </script>
@endsection
