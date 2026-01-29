@php
    // Expected variables from controller:
    // $products (paginated)
@endphp

@extends('layouts.app')
@section('title', 'Products')

@section('content')
    <div class="d-flex min-vh-100">

        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="p-4 card-rounded shadow-sm bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h2 class="m-0">Product List</h2>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="search-wrapper">
                                        <i class="bi bi-search search-icon"></i>
                                        <input type="text" name="search" id="product-search-input" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                                    </div>
                                    <a href="{{ route('superadmin.products.create') }}" class="btn" style="background-color:var(--theme-color); color:white">Add New Product</a>
                                    <button type="button" id="editSelectedBtn" class="btn btn-outline-secondary">Edit Selected</button>
                                    <button type="button" id="deleteSelectedBtn" class="btn btn-outline-danger">Delete Selected</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" class="form-check-input" id="selectAll">
                                            </th>
                                            <th>
                                                <a href="{{ route('superadmin.products.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                                    ID
                                                    @if ($sortBy == 'id')
                                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>Product Name</th>
                                            <th>Barcode</th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th>Product Type</th>
                                            <th>Unit Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-table-body">
                                        @include('SuperAdmin.products._product_table', ['products' => $products])
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            let searchTimeout;

            $('#product-search-input').on('keyup', function () {
                clearTimeout(searchTimeout);
                const query = $(this).val();

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('superadmin.products.index') }}",
                        type: "GET",
                        data: { 'search': query },
                        success: function(data) {
                            $('#product-table-body').html(data);
                        }
                    });
                }, 300); // 300ms delay
            });

            // Select all toggle
            $(document).on('change', '#selectAll', function(){
                const checked = $(this).is(':checked');
                $('#product-table-body input.product-select').prop('checked', checked);
            });

            // Sync header checkbox if rows are toggled individually
            $(document).on('change', '#product-table-body input.product-select', function(){
                const total = $('#product-table-body input.product-select').length;
                const selected = $('#product-table-body input.product-select:checked').length;
                $('#selectAll').prop('checked', total > 0 && selected === total);
            });

            // Edit selected: exactly one
            $('#editSelectedBtn').on('click', function(){
                const ids = $('#product-table-body input.product-select:checked').map(function(){ return this.value; }).get();
                if (ids.length !== 1){
                    alert('Please select exactly one product to edit.');
                    return;
                }
                window.location.href = '/superadmin/products/' + ids[0] + '/edit';
            });

            // Delete selected: one or many
            $('#deleteSelectedBtn').on('click', async function(){
                const ids = $('#product-table-body input.product-select:checked').map(function(){ return this.value; }).get();
                if (ids.length === 0){
                    alert('Please select at least one product to delete.');
                    return;
                }
                if (!confirm('Are you sure you want to delete the selected product(s)?')) return;

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                for (const id of ids){
                    try{
                        await fetch('/superadmin/products/' + id, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'text/html,application/json'
                            },
                            body: new URLSearchParams({ _method: 'DELETE', _token: token })
                        });
                    }catch(e){ /* ignore and continue */ }
                }
                // Reload to reflect deletions
                window.location.reload();
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}'
                });
            @endif
        });
    </script>
@endsection
