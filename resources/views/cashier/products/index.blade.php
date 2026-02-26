@php
    // Expected variables from controller:
    // $products (paginated)
@endphp

@extends('layouts.app')
@section('title', 'Products')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .search-wrapper .fas.fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 2;
            font-size: 14px;
        }
        
        .search-wrapper input {
            padding-left: 40px !important;
            width: 250px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 card-rounded shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="m-0">Products</h2>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="search-wrapper">
                                        <i class="fas fa-search"></i>
                                        <input type="text" name="search" id="product-search-input" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                                    </div>
                                    <a href="{{ route('cashier.products.create') }}" class="btn btn-add-product d-flex align-items-center gap-2">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 5v14M5 12h14"/>
                                        </svg>
                                        Add New Product
                                    </a>
                                    <button type="button" id="editSelectedBtn" class="btn btn-edit-selected d-flex align-items-center gap-2">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit Selected
                                    </button>
                                    <button type="button" id="deleteSelectedBtn" class="btn btn-delete-selected d-flex align-items-center gap-2">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                        </svg>
                                        Delete Selected
                                    </button>
                                </div>
                            </div>

                            <div class="table-container">
                                <div class="table-responsive">
                                    <table class="table products-table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                                </th>
                                                <th>
                                                    <a href="{{ route('cashier.products.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}">
                                                        ID
                                                        @if ($sortBy == 'id')
                                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle;">
                                                                @if ($sortDirection == 'asc')
                                                                    <polyline points="18 15 12 9 6 15"></polyline>
                                                                @else
                                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                                @endif
                                                            </svg>
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
                                            @include('cashier.products._product_table', ['products' => $products])
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{ $products->links() }}
                            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        let searchTimeout;

        $('#product-search-input').on('keyup', function () {
            clearTimeout(searchTimeout);
            const query = $(this).val();

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('cashier.products.index') }}",
                    type: "GET",
                    data: { 'search': query },
                    success: function(data) {
                        $('#product-table-body').html(data);
                    }
                });
            }, 300);
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select exactly one product to edit.',
                    confirmButtonColor: '#2196F3'
                });
                return;
            }
            window.location.href = '/cashier/products/' + ids[0] + '/edit';
        });

        // Delete selected: one or many
        $('#deleteSelectedBtn').on('click', async function(){
            const ids = $('#product-table-body input.product-select:checked').map(function(){ return this.value; }).get();
            if (ids.length === 0){
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select at least one product to delete.',
                    confirmButtonColor: '#2196F3'
                });
                return;
            }
            
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete ${ids.length} product(s). This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E91E63',
                cancelButtonColor: '#2196F3',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            });
            
            if (!result.isConfirmed) return;

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let deletedCount = 0;
            
            for (const id of ids){
                try{
                    await fetch('/cashier/products/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'text/html,application/json'
                        },
                        body: new URLSearchParams({ _method: 'DELETE', _token: token })
                    });
                    deletedCount++;
                }catch(e){ 
                    console.error('Error deleting product:', e);
                }
            }
            
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: `${deletedCount} product(s) have been deleted.`,
                confirmButtonColor: '#2196F3'
            }).then(() => {
                window.location.reload();
            });
        });
    });

    // Use standard CashierSidebar from layouts
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#0D47A1',
                color: '#fff'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                confirmButtonColor: '#2196F3'
            });
        @endif
</script>
@endpush
