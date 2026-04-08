@php
    // Expected variables from controller:
    // $products (paginated)
@endphp

@extends('layouts.app')
@section('title', 'Products')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
        :root {
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --cyan:    #00E5FF;
            --green:   #10b981;
            --red:     #ef4444;
            --amber:   #f59e0b;
            --bg:      #EBF3FB;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.12);
            --text:    #1a2744;
            --muted:   #6b84aa;
        }

        .products-page { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Background */
        .sp-bg { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; background: var(--bg); }
        .sp-bg::before {
            content: ''; position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%, transparent 55%);
        }
        .sp-blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: .11; }
        .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
        .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
        @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
        @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

        /* Wrap */
        .sp-wrap { position: relative; z-index: 1; padding: 28px 24px 56px; }

        /* Page header */
        .sp-page-head {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 22px; flex-wrap: wrap; gap: 14px;
            animation: spUp .4s ease both;
        }
        .sp-ph-left { display: flex; align-items: center; gap: 13px; }
        .sp-ph-icon {
            width: 46px; height: 46px; border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex; align-items: center; justify-content: center;
            font-size: 19px; color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
        }
        .sp-ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .sp-ph-sub   { font-size:12px; color:var(--muted); margin-top:2px; }
        .sp-ph-actions { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }

        /* Buttons */
        .sp-btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 16px; border-radius: 11px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none; transition: all .2s ease; text-decoration: none; white-space: nowrap;
        }
        .sp-btn-primary { background: linear-gradient(135deg, var(--navy), var(--blue)); color: #fff; box-shadow: 0 4px 14px rgba(13,71,161,0.26); }
        .sp-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }
        .sp-btn-amber { background: linear-gradient(135deg, #d97706, #f59e0b); color: #fff; box-shadow: 0 4px 14px rgba(245,158,11,0.28); }
        .sp-btn-amber:hover { transform: translateY(-2px); }
        .sp-btn-danger { background: transparent; color: var(--red); border: 1.5px solid rgba(239,68,68,0.30); }
        .sp-btn-danger:hover { background: rgba(239,68,68,0.07); border-color: var(--red); }

        /* Search */
        .sp-search-wrap { position: relative; }
        .sp-search-wrap i { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;z-index:2; }
        .sp-search-input {
            padding: 9px 14px 9px 36px;
            border-radius: 11px; border: 1.5px solid var(--border);
            font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--card); color: var(--text); outline: none;
            width: 230px; transition: border-color .18s, box-shadow .18s;
        }
        .sp-search-input:focus { border-color: var(--blue-lt); box-shadow: 0 0 0 3px rgba(66,165,245,0.12); }
        .sp-search-input::placeholder { color: #b0c0d8; }

        /* Card */
        .sp-card {
            background: var(--card); border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(13,71,161,0.08);
            overflow: hidden; animation: spUp .45s ease both;
        }

        /* Card header */
        .sp-card-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex; align-items: center; justify-content: space-between;
            position: relative; overflow: hidden;
        }
        .sp-card-head::before {
            content:'';position:absolute;inset:0;
            background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);
            pointer-events:none;
        }
        .sp-card-head::after {
            content:'';position:absolute;width:220px;height:220px;border-radius:50%;
            background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;
        }
        .sp-card-head-title {
            font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;
            display:flex;align-items:center;gap:8px;position:relative;z-index:1;
        }
        .sp-card-head-title i { color:rgba(0,229,255,.85); }
        .sp-c-badge {
            position:relative;z-index:1;
            background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);
            color:#fff;font-size:11px;font-weight:700;
            padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif;
        }

        /* Table */
        .sp-table-wrap { overflow-x:auto; overflow-y:auto; max-height:420px; }
        .sp-table-wrap::-webkit-scrollbar{height:5px;width:5px;}
        .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}

        .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
        .sp-table thead th {
            position:sticky;top:0;z-index:3;
            background:rgba(13,71,161,0.03);
            padding:11px 14px;
            font-size:11px;font-weight:700;
            color:var(--navy);letter-spacing:.06em;text-transform:uppercase;
            border-bottom:1px solid var(--border);white-space:nowrap;
        }
        .sp-table tbody td {
            padding:11px 14px;font-size:13px;color:var(--text);
            border-bottom:1px solid rgba(25,118,210,0.06);
            vertical-align:middle;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
        }
        .sp-table tbody tr:nth-child(even) td { background:rgba(240,246,255,0.55); }
        .sp-table tbody tr:hover td { background:rgba(21,101,192,0.05); }

        /* Badges */
        .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
        .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }
        .sp-badge-red   { background:rgba(239,68,68,0.10);color:#b91c1c; }
        .sp-badge-blue  { background:rgba(13,71,161,0.10);color:var(--navy); }
        .sp-badge-amber { background:rgba(245,158,11,0.12);color:#92400e; }

        /* Action buttons in table */
        .sp-tbl-action {
            display:inline-flex;align-items:center;justify-content:center;
            width:30px;height:30px;border-radius:8px;
            font-size:12px;text-decoration:none;cursor:pointer;
            border:none;transition:all .18s ease;
        }
        .sp-tbl-edit { color:var(--blue);background:rgba(13,71,161,0.08); }
        .sp-tbl-edit:hover { background:rgba(13,71,161,0.16); }
        .sp-tbl-del  { color:var(--red);background:rgba(239,68,68,0.08); }
        .sp-tbl-del:hover { background:rgba(239,68,68,0.16); }

        /* Pagination */
        .sp-pagination {
            padding:14px 22px;background:rgba(13,71,161,0.03);
            border-top:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .sp-pag-info { font-size:12px;color:var(--muted);font-family:'Nunito',sans-serif; }
        .sp-pagination .pagination { margin: 0; }
        .sp-pagination .page-link {
            border-radius: 8px;
            border: 1.5px solid var(--border);
            color: var(--navy);
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            min-width: 32px;
            text-align: center;
        }
        .sp-pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            border-color: var(--navy);
            color: #fff;
        }
        .sp-pagination .page-link:hover { background:rgba(13,71,161,0.08); border-color:var(--blue-lt); }

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}

        /* Checkbox */
        input[type="checkbox"] { cursor:pointer;accent-color:var(--navy);width:15px;height:15px; }

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
    min-width: 180px; /* Further reduced from 200px */
    flex-shrink: 0;
}

.search-wrapper .input-group {
    position: relative;
}

.search-wrapper .input-group-text {
    background: transparent;
    border-right: none;
    padding: 0.375rem 0.75rem;
}

.search-wrapper input {
    min-width: 130px; /* Further reduced from 150px */
    border-left: none;
}

.search-wrapper .input-group:focus-within .input-group-text {
    border-color: #86b7fe;
}

.btn {
    white-space: nowrap;
    flex-shrink: 0;
}

.table-responsive {
    max-height: 500px;
    overflow: auto;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
}

.products-table {
    border-collapse: separate;
    border-spacing: 0;
}

.products-table thead th {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 3;
    box-shadow: 0 1px 0 rgba(0,0,0,0.08);
}

/* Sticky checkbox column (works with horizontal scroll) */
.products-table thead th:first-child,
.products-table tbody td:first-child {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 4;
}

/* Ensure sticky header + sticky first column intersection stays on top */
.products-table thead th:first-child {
    z-index: 5;
}

/* Better row hover and focus */
.products-table tbody tr {
    transition: background-color 120ms ease-in-out;
}

.products-table tbody tr:hover {
    background-color: #f6f9ff;
}

.products-table tbody tr:hover td:first-child {
    background-color: #f6f9ff;
}

.products-table tbody tr:focus-within {
    background-color: #eef5ff;
}

.products-table tbody tr:focus-within td:first-child {
    background-color: #eef5ff;
}

/* Ensure proper alignment at all screen sizes */
@media (min-width: 1200px) {
    .products-header-card .d-flex:first-child {
        gap: 2rem !important;
    }
    
    .search-wrapper {
        min-width: 200px; /* Slightly larger for desktop */
    }
    
    .search-wrapper input {
        min-width: 150px; /* Slightly larger for desktop */
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
        <div class="sp-bg">
            <div class="sp-blob sp-blob-1"></div>
            <div class="sp-blob sp-blob-2"></div>
        </div>
        <main class="flex-fill p-4">
            <div class="sp-wrap">
                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-box"></i></div>
                        <div>
                            <div class="sp-ph-title">Products</div>
                            <div class="sp-ph-sub">Manage your product catalog</div>
                        </div>
                    </div>
                    <div class="sp-ph-actions">
                        <div class="sp-search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" id="product-search-input" class="sp-search-input" placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                        <a href="{{ route('superadmin.products.create') }}" class="sp-btn sp-btn-primary">
                            <i class="fas fa-plus"></i>
                            Add New Product
                        </a>
                        <button type="button" id="editSelectedBtn" class="sp-btn sp-btn-amber">
                            <i class="fas fa-pen"></i>
                            Edit Selected
                        </button>
                        <button type="button" id="deleteSelectedBtn" class="sp-btn sp-btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete Selected
                        </button>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-list"></i> Product List</div>
                        <span class="sp-c-badge">{{ $products->total() }} products</span>
                    </div>

                    <div class="sp-table-wrap">
                        <table class="sp-table products-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th style="width:60px;">
                                        <a href="{{ route('superadmin.products.index', ['sort_by' => 'id', 'sort_direction' => ($sortBy == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" style="color: inherit; text-decoration: none;">
                                            ID
                                            @if ($sortBy == 'id')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; opacity: .6;">
                                                    @if ($sortDirection == 'asc')
                                                        <polyline points="18 15 12 9 6 15"></polyline>
                                                    @else
                                                        <polyline points="6 9 12 15 18 9"></polyline>
                                                    @endif
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th style="width:55px;text-align:center;">Image</th>
                                    <th style="width:250px;">Product Name</th>
                                    <th style="width:100px;">Brand</th>
                                    <th style="width:110px;">Category</th>
                                    <th style="width:110px;">Product Type</th>
                                    <th style="width:120px;">Unit Type</th>
                                    <th style="width:80px;">Status</th>
                                    <th style="width:100px;">Selling Price (₱)</th>
                                    <th style="width:100px;">Purchase Price (₱)</th>
                                    <th style="width:90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="product-table-body">
                                @include('SuperAdmin.products._product_table', ['products' => $products])
                            </tbody>
                        </table>
                    </div>

                    <div class="sp-pagination">
                        <div class="sp-pag-info">
                            @if($products->total() > 0)
                                Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                            @else
                                Showing 0 of 0 products
                            @endif
                        </div>
                        <div>
                            {{ $products->links() }}
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
            let failedCount  = 0;

            for (const id of ids){
                try{
                    const res = await fetch('/superadmin/products/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({ _method: 'DELETE', _token: token })
                    });
                    if (res.ok) {
                        deletedCount++;
                    } else {
                        failedCount++;
                        console.error('Failed to delete product ' + id + ': HTTP ' + res.status);
                    }
                }catch(e){
                    failedCount++;
                    console.error('Error deleting product:', e);
                }
            }

            if (deletedCount > 0) {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: `${deletedCount} product(s) deleted successfully.` + (failedCount > 0 ? ` ${failedCount} failed.` : ''),
                    confirmButtonColor: '#2196F3'
                }).then(() => { window.location.reload(); });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not delete the selected product(s). You may not have permission.',
                    confirmButtonColor: '#2196F3'
                });
            }
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