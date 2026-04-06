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
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.12);
            --text:    #1a2744;
            --muted:   #6b84aa;
        }

        .products-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        .products-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .products-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .products-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .products-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .products-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .products-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .products-page .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .products-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .products-page .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
        }
        .products-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .products-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }
        .products-page .ph-actions { display: flex; align-items: center; gap: 9px; flex-wrap: wrap; }

        .products-page .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .products-page .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .products-page .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }
        .products-page .btn-outline {
            background: var(--card);
            color: var(--navy);
            border: 1.5px solid var(--border);
        }
        .products-page .btn-outline:hover { border-color: var(--blue-lt); background: rgba(66,165,245,0.06); }
        .products-page .btn-danger-outline {
            background: transparent;
            color: var(--red);
            border: 1.5px solid rgba(239,68,68,0.28);
        }
        .products-page .btn-danger-outline:hover { background: rgba(239,68,68,0.07); border-color: var(--red); }

        .products-page .search-wrapper { position: relative; }
        .products-page .search-wrapper .fas.fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
            z-index: 2;
        }
        .products-page .search-wrapper input {
            padding: 9px 14px 9px 36px !important;
            border-radius: 11px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--card);
            color: var(--text);
            outline: none;
            width: 230px;
            transition: border-color .18s, box-shadow .18s;
        }
        .products-page .search-wrapper input:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }
        .products-page .search-wrapper input::placeholder { color: #b0c0d8; }

        .products-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 24px rgba(13,71,161,0.08);
            overflow: hidden;
        }
        .products-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .products-page .c-head::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top:-90px;
            right:-50px;
            pointer-events:none;
        }
        .products-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 85% 50%, rgba(0,229,255,0.14), transparent);
            pointer-events:none;
        }
        .products-page .c-head-title {
            font-family:'Nunito',sans-serif;
            font-size:14.5px;
            font-weight:800;
            color:#fff;
            display:flex;
            align-items:center;
            gap:8px;
            position:relative;
            z-index:1;
        }
        .products-page .c-head-title i { color:rgba(0,229,255,.85); }
        .products-page .c-badge {
            position:relative;
            z-index:1;
            background:rgba(255,255,255,0.15);
            border:1px solid rgba(255,255,255,0.25);
            color:#fff;
            font-size:11px;
            font-weight:700;
            padding:3px 10px;
            border-radius:20px;
            font-family:'Nunito',sans-serif;
        }

        .products-page .table-responsive { overflow-x: auto; }
        .products-page .table-responsive::-webkit-scrollbar { height: 4px; }
        .products-page .table-responsive::-webkit-scrollbar-thumb { background: rgba(13,71,161,0.15); border-radius: 4px; }

        .products-page table.table { width: 100%; border-collapse: collapse; margin: 0; }
        .products-page table.table thead th {
            padding: 12px 16px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: nowrap;
        }
        .products-page table.table thead th a {
            color: rgba(255,255,255,0.92);
            text-decoration: none;
            display:inline-flex;
            align-items:center;
            gap:4px;
        }
        .products-page table.table thead th a:hover { color: #fff; }
        .products-page table.table tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
        }
        .products-page table.table tbody tr:nth-child(odd) { background: #fff; }
        .products-page table.table tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .products-page table.table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }
        .products-page table.table td { padding: 13px 16px; font-size: 13px; vertical-align: middle; }
        .products-page table.table th:first-child,
        .products-page table.table td:first-child { width: 44px; text-align: center; }
        .products-page input.form-check-input,
        .products-page input.product-select {
            width: 16px;
            height: 16px;
            accent-color: var(--navy);
            cursor: pointer;
            border-radius: 4px;
        }

        .products-page .product-name { font-weight: 700; font-size: 13.5px; color: var(--navy); }
        .products-page .badge-unit {
            display:inline-flex;
            align-items:center;
            padding: 3px 11px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            white-space: nowrap;
            background:rgba(13,71,161,0.08);
            color:var(--navy);
            border:1px solid var(--border);
        }
        .products-page .badge-status-active,
        .products-page .badge-status-inactive {
            display:inline-flex;
            align-items:center;
            gap: 6px;
            padding: 4px 12px 4px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            white-space: nowrap;
            border: 1px solid transparent;
        }
        .products-page .badge-status-active { background:rgba(16,185,129,0.11); color:#047857; border-color:rgba(16,185,129,0.2); }
        .products-page .badge-status-inactive { background:rgba(239,68,68,0.10); color:#b91c1c; border-color:rgba(239,68,68,0.18); }

        .products-page .tbl-action {
            display:inline-flex;align-items:center;justify-content:center;
            width:30px;height:30px;border-radius:8px;
            text-decoration:none;
            border:none;transition:all .18s ease;
        }
        .products-page .tbl-view { color:var(--navy);background:rgba(13,71,161,0.08); }
        .products-page .tbl-view:hover { background:rgba(13,71,161,0.16); }

        .products-page .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-block;
            position: relative;
        }

        .products-page .status-dot.active-dot {
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16,185,129,0.6);
            animation: dot-float 2.4s ease-in-out infinite, dot-pulse 2s ease-in-out infinite;
        }

        .products-page .status-dot.inactive-dot {
            background: #ef4444;
        }

        @keyframes dot-float {
            0%,  100% { transform: translateY(0px); }
            25%        { transform: translateY(-2.5px); }
            50%        { transform: translateY(0px); }
            75%        { transform: translateY(2px); }
        }

        @keyframes dot-pulse {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.65); }
            50%  { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }

        @media (prefers-reduced-motion: reduce) {
            .products-page .status-dot.active-dot {
                animation-duration: 2.4s, 2s !important;
                animation-iteration-count: infinite, infinite !important;
            }
        }

        .products-page .pagination-wrap {
            padding: 14px 22px;
            background: rgba(13,71,161,0.03);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .products-page .pagination { margin: 0; gap: 6px; }
        .products-page .page-link {
            border-radius: 10px !important;
            border: 1.5px solid var(--border) !important;
            color: var(--navy) !important;
            background: #fff !important;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            min-width: 36px;
            text-align: center;
            box-shadow: none !important;
        }
        .products-page .page-item.active .page-link {
            background: var(--navy) !important;
            border-color: var(--navy) !important;
            color: #fff !important;
        }
        .products-page .page-item.disabled .page-link {
            opacity: .55;
        }
    </style>
@endpush

@section('content')
<div class="products-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-box"></i></div>
                <div>
                    <div class="ph-title">Products</div>
                    <div class="ph-sub">Manage your product catalog</div>
                </div>
            </div>

            <div class="ph-actions">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" id="product-search-input" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                </div>

                <a href="{{ route('cashier.products.create') }}" class="btn btn-primary" id="addProductBtn">
                    <i class="fas fa-plus"></i>
                    Add New Product
                </a>

                <button type="button" id="editSelectedBtn" class="btn btn-outline">
                    <i class="fas fa-pen"></i>
                    Edit Selected
                </button>

                <button type="button" id="deleteSelectedBtn" class="btn btn-danger-outline">
                    <i class="fas fa-trash"></i>
                    Delete Selected
                </button>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Product List</div>
                <span class="c-badge">{{ $products->total() }} product{{ $products->total() == 1 ? '' : 's' }}</span>
            </div>

            <div class="table-responsive">
                <table class="table">
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
                            <th>Image</th>
                            <th>Barcode</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        @include('cashier.products._product_table', ['products' => $products])
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $products->links() }}
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
        const isViewOnlyProducts = {{ !empty($isViewOnlyProducts) && $isViewOnlyProducts ? 'true' : 'false' }};
        const canCreateProducts = {{ !empty($canCreateProducts) && $canCreateProducts ? 'true' : 'false' }};
        const canEditProducts = {{ !empty($canEditProducts) && $canEditProducts ? 'true' : 'false' }};
        const canDeleteProducts = {{ !empty($canDeleteProducts) && $canDeleteProducts ? 'true' : 'false' }};

        const noPermissionAllMessage = "You don't have permission to add, edit, or delete products.";
        const noPermissionEditDeleteMessage = "You don't have permission to edit and delete products.";
        const noPermissionDeleteMessage = "You don't have permission to delete products.";

        function showNoPermission() {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: noPermissionAllMessage,
                confirmButtonColor: '#2196F3'
            });
        }

        function showNoEditDeletePermission(messageOverride) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: messageOverride || noPermissionEditDeleteMessage,
                confirmButtonColor: '#2196F3'
            });
        }

        function showNoDeletePermission(messageOverride) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: messageOverride || noPermissionDeleteMessage,
                confirmButtonColor: '#2196F3'
            });
        }

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
            if (!canEditProducts) {
                if (isViewOnlyProducts && !canCreateProducts) {
                    showNoPermission();
                } else {
                    showNoEditDeletePermission();
                }
                return;
            }
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
            if (!canDeleteProducts) {
                if (isViewOnlyProducts && !canCreateProducts) {
                    showNoPermission();
                } else if (canEditProducts) {
                    showNoDeletePermission();
                } else {
                    showNoEditDeletePermission();
                }
                return;
            }
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
            let failedIds = [];
            
            for (const id of ids){
                try{
                    const res = await fetch('/cashier/products/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({ _method: 'DELETE', _token: token })
                    });
                    if (res.status === 403) {
                        let msg = null;
                        try {
                            const data = await res.json();
                            msg = data?.message ?? null;
                        } catch (e) {
                            msg = null;
                        }
                        if (isViewOnlyProducts && !canCreateProducts) {
                            showNoPermission();
                        } else if (canEditProducts) {
                            showNoDeletePermission(msg);
                        } else {
                            showNoEditDeletePermission(msg);
                        }
                        return;
                    }
                    if (!res.ok) {
                        let msg = null;
                        try {
                            const data = await res.json();
                            msg = data?.message ?? null;
                        } catch (e) {
                            msg = null;
                        }
                        failedIds.push(msg ? `${id} (${msg})` : String(id));
                        continue;
                    }
                    deletedCount++;
                }catch(e){ 
                    console.error('Error deleting product:', e);
                    failedIds.push(String(id));
                }
            }
            
            if (failedIds.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to delete',
                    text: `Failed to delete ${failedIds.length} product(s): ${failedIds.join(', ')}`,
                    confirmButtonColor: '#2196F3'
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: `${deletedCount} product(s) have been deleted.`,
                    confirmButtonColor: '#2196F3'
                }).then(() => {
                    window.location.reload();
                });
            }
        });

        $('#addProductBtn').on('click', function(e){
            if (!canCreateProducts) {
                e.preventDefault();
                showNoPermission();
            }
        });
    });

    // Use standard CashierSidebar from layouts
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: @json(session('success')),
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
                text: @json(session('error')),
                confirmButtonColor: '#2196F3'
            });
        @endif
</script>
@endpush
