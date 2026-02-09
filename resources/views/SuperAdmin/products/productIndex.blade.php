@php
    // Expected variables from controller:
    // $products (paginated)
@endphp

@extends('layouts.app')
@section('title', 'Products')

@push('styles')
<style>
/* CSS-only fix without !important */
.product-image {
    width: 25px;
    height: 25px;
    max-width: none; /* Remove Bootstrap's constraint */
    max-height: none; /* Remove Bootstrap's constraint */
    aspect-ratio: 1/1; /* Force square aspect ratio */
    object-fit: cover;
    display: block;
    margin: auto;
    border-radius: 2px;
    border: 1px solid #e0e0e0;
}


.product-image-placeholder {
    width: 25px;
    height: 25px;
    background-color: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image-placeholder svg {
    width: 8px;
    height: 8px;
}

.image-container {
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

/* HTML wrapper solution - Bootstrap immune */
.image-wrapper {
    width: 25px;
    height: 25px;
    overflow: hidden;
    border-radius: 2px;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.image-wrapper .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    max-width: none; /* Allow image to fill wrapper */
    max-height: none;
}

/* Stable header layout that resists zoom repositioning */
.products-header-card {
    min-height: 80px;
    overflow: visible; /* Allow content to be visible */
}

.products-header-card > div {
    min-width: 100%; /* Prevent shrinking during zoom */
    flex-wrap: nowrap !important; /* Prevent wrapping during zoom */
}

.products-header-card .d-flex:first-child {
    gap: 1rem !important; /* Reduced gap for more space */
    flex: 1; /* Allow title section to grow */
    min-width: 0; /* Allow shrinking if needed */
}

.products-header-card .d-flex:last-child {
    gap: 0.5rem !important; /* Fixed gap for buttons */
    flex-shrink: 0; /* Prevent button container from shrinking */
    min-width: fit-content; /* Ensure buttons don't get compressed */
}

.products-header-card h2 {
    font-size: 1.5rem !important;
    line-height: 1.2 !important;
    white-space: nowrap;
    flex-shrink: 0;
}

.products-header-card p {
    font-size: 0.875rem !important;
    line-height: 1.2 !important;
    white-space: nowrap;
    flex-shrink: 0;
}

.search-wrapper {
    min-width: 200px; /* Reduced from 250px */
    flex-shrink: 0;
}

.search-wrapper input {
    min-width: 150px; /* Reduced from 200px */
}

.btn {
    white-space: nowrap;
    flex-shrink: 0;
}

/* Ensure proper alignment at all screen sizes */
@media (min-width: 1200px) {
    .products-header-card .d-flex:first-child {
        gap: 2rem !important;
    }
    
    .search-wrapper {
        min-width: 250px;
    }
    
    .search-wrapper input {
        min-width: 200px;
    }
}

/* Bootstrap-safe override with higher specificity */
.table .product-image {
    width: 15px !important;
    height: 15px !important;
    max-width: 15px !important;
    max-height: 15px !important;
    min-width: 15px !important;
    min-height: 15px !important;
    object-fit: cover;
    display: block;
    border-radius: 2px;
    border: 1px solid #e0e0e0;
}

.products-table {
    table-layout: fixed;
    width: 100%;
}

.products-table td {
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.products-table th:nth-child(1) { width: 40px; } /* Checkbox */
.products-table th:nth-child(2) { width: 60px; } /* ID */
.products-table td:nth-child(3),
.products-table th:nth-child(3) { width: 50px; text-align: center; } /* Image */
.products-table th:nth-child(4) { width: 250px; } /* Product Name */
.products-table th:nth-child(5) { width: 100px; } /* Brand */
.products-table th:nth-child(6) { width: 100px; } /* Category */
.products-table th:nth-child(7) { width: 100px; } /* Product Type */
.products-table th:nth-child(8) { width: 120px; } /* Unit Type */
.products-table th:nth-child(9) { width: 80px; } /* Status */
</style>
@endpush

@include('layouts.theme-base')

@section('content')
    <div class="d-flex min-vh-100 products-page">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="row mb-6">
                    <div class="col-12">
                        <div class="products-header-card">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-4">
                                    <h2 class="m-0">Product List</h2>
                                    <p class="mb-0 text-muted">Manage your product inventory</p>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="search-wrapper">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <path d="m21 21-4.35-4.35"></path>
                                        </svg>
                                        <input type="text" name="search" id="product-search-input" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                                    </div>
                                    <a href="{{ route('superadmin.products.create') }}" class="btn btn-add-product d-flex align-items-center gap-2">
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
                                                    <a href="{{ route('superadmin.products.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}">
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
                                                <th>Image</th>
                                                <th>Product Name</th>
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
                    url: "{{ route('superadmin.products.index') }}",
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
            window.location.href = '/superadmin/products/' + ids[0] + '/edit';
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
                    await fetch('/superadmin/products/' + id, {
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

    document.addEventListener('DOMContentLoaded', function () {
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
    });

    // ----------------- OCR FUNCTIONALITY -----------------
    const ocrButton = document.getElementById('ocr-button');
    
    if (ocrButton) {
        const ocrFileInput = document.createElement('input');
        ocrFileInput.type = 'file';
        ocrFileInput.accept = 'image/*';

        ocrButton.addEventListener('click', () => ocrFileInput.click());

        ocrFileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (!file) return;

            Swal.fire({
                title: 'Processing OCR',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            Tesseract.recognize(file, 'eng', { logger: m => console.log(m) })
                .then(({ data: { text } }) => {
                    Swal.fire({
                        icon: 'success',
                        title: 'OCR Complete',
                        html: '<pre style="text-align: left; max-height: 300px; overflow-y: auto;">' + text + '</pre>',
                        confirmButtonText: 'Process Data',
                        showCancelButton: true,
                        confirmButtonColor: '#2196F3'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Here you can process the OCR text
                            // For example, parse product information and fill forms
                            processOCRText(text);
                        }
                    });
                })
                .catch(error => {
                    console.error('OCR Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'OCR Failed',
                        text: 'Failed to process image. Please try again.',
                        confirmButtonColor: '#2196F3'
                    });
                });
        });
    }

    function processOCRText(text) {
        // Function to process OCR text and extract product information
        // This is a placeholder - you can customize based on your needs
        const lines = text.split('\n').filter(line => line.trim());
        
        if (lines.length > 0) {
            // Example: redirect to create product page with pre-filled data
            const productName = lines[0].trim();
            window.location.href = `/superadmin/products/create?name=${encodeURIComponent(productName)}`;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'No Text Found',
                text: 'No readable text found in the image.',
                confirmButtonColor: '#2196F3'
            });
        }
    }
</script>
@endpush