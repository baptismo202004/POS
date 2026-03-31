@extends('layouts.app')

@section('title', 'Stock Management')

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

    /* Background */
    .sp-bg { position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg); }
    .sp-bg::before {
        content:'';position:absolute;inset:0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%,transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%,transparent 55%);
    }
    .sp-blob { position:absolute;border-radius:50%;filter:blur(60px);opacity:.11; }
    .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
    .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    /* Wrap */
    .sp-wrap { position:relative;z-index:1;padding:18px 24px 44px;font-family:'Plus Jakarta Sans',sans-serif; }

    /* Page header */
    .sp-page-head {
        display:flex;align-items:center;justify-content:space-between;
        margin-bottom:12px;flex-wrap:wrap;gap:12px;
        animation:spUp .4s ease both;
    }
    .sp-ph-left { display:flex;align-items:center;gap:13px; }
    .sp-ph-icon {
        width:48px;height:48px;border-radius:14px;
        background:linear-gradient(135deg,var(--navy),var(--blue-lt));
        display:flex;align-items:center;justify-content:center;
        font-size:20px;color:#fff;
        box-shadow:0 6px 20px rgba(13,71,161,0.28);
    }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
    .sp-ph-actions { display:flex;align-items:center;gap:9px;flex-wrap:wrap; }

    /* Search */
    .sp-search-wrap { position:relative; }
    .sp-search-wrap i { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px;z-index:2; }
    .sp-search-input {
        padding:9px 14px 9px 36px;
        border-radius:11px;border:1.5px solid var(--border);
        font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;
        background:var(--card);color:var(--text);outline:none;
        width:260px;transition:border-color .18s, box-shadow .18s;
    }
    .sp-search-input:focus { border-color:var(--blue-lt); box-shadow:0 0 0 3px rgba(66,165,245,0.12); }

    /* Filter button */
    .sp-filter-btn {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 16px;border-radius:11px;
        font-size:13px;font-weight:700;cursor:pointer;
        font-family:'Nunito',sans-serif;
        border:1.5px solid var(--border);
        background:var(--card);color:var(--navy);
        transition:all .2s ease;
    }
    .sp-filter-btn:hover { background:rgba(13,71,161,0.06); border-color:var(--blue-lt); }
    .sp-filter-btn .badge { border-radius:999px; font-weight:800; }

    /* Filter dropdown panel */
    .sp-filter-menu{
        padding:14px 14px;
        border-radius:16px;
        border:1.5px solid var(--border);
        background:linear-gradient(180deg, rgba(255,255,255,0.98), rgba(250,252,255,0.98));
        box-shadow:0 18px 48px rgba(13,71,161,0.18);
        backdrop-filter: blur(10px);
    }
    .sp-filter-menu .dropdown-header{
        padding:0;
        color:var(--navy);
        font-size:11px;
        font-weight:900;
        letter-spacing:.08em;
        text-transform:uppercase;
        font-family:'Nunito',sans-serif;
        opacity:.9;
    }
    .sp-filter-menu .form-label{
        font-size:11px;
        font-weight:800;
        color:var(--navy);
        letter-spacing:.06em;
        text-transform:uppercase;
        margin-bottom:6px;
        font-family:'Nunito',sans-serif;
    }
    .sp-filter-menu .form-select{
        border-radius:11px;
        border:1.5px solid var(--border);
        padding:9px 12px;
        font-size:13px;
        background:#ffffff;
        color:var(--text);
        font-family:'Plus Jakarta Sans',sans-serif;
        box-shadow:none;
    }
    .sp-filter-menu .form-select:focus{
        border-color:var(--blue-lt);
        box-shadow:0 0 0 3px rgba(66,165,245,0.12);
    }
    .sp-filter-menu .form-check-input{
        border-color:rgba(13,71,161,0.25);
    }
    .sp-filter-menu .form-check-input:checked{
        background-color:var(--navy);
        border-color:var(--navy);
    }
    .sp-filter-menu .form-check-label{
        font-size:13px;
        color:var(--text);
        font-family:'Plus Jakarta Sans',sans-serif;
    }

    /* Buttons (for modals/actions) */
    .sp-btn {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 18px;border-radius:11px;
        font-size:13px;font-weight:700;cursor:pointer;
        font-family:'Nunito',sans-serif;
        border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;
    }
    .sp-btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue)); color:#fff; box-shadow:0 4px 14px rgba(13,71,161,0.26); }
    .sp-btn-primary:hover { transform:translateY(-2px); box-shadow:0 7px 20px rgba(13,71,161,0.36); color:#fff; }
    .sp-btn-outline { background:var(--card); color:var(--navy); border:1.5px solid var(--border); }
    .sp-btn-outline:hover { background:var(--navy); color:#fff; border-color:var(--navy); }

    /* Card */
    .sp-card {
        background:var(--card);border-radius:20px;
        border:1px solid var(--border);
        box-shadow:0 4px 28px rgba(13,71,161,0.09);
        overflow:hidden;animation:spUp .45s ease both;
    }
    .sp-card-head {
        padding:15px 22px;
        background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
        display:flex;align-items:center;justify-content:space-between;
        position:relative;overflow:hidden;
    }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-c-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif; }

    .sp-card-body { padding: 18px 22px; }

    /* Reduce space from the included stock-filters wrapper */
    .sp-wrap .card.mb-3.border-0.shadow-sm { margin-bottom: 10px; box-shadow: none; background: transparent; }

    /* Table */
    .sp-table-wrap { overflow-x:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;width:5px;}
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}
    .sp-table-scroll{max-height:560px;overflow-y:auto;}

    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th {
        position:sticky;top:0;z-index:3;
        background:linear-gradient(135deg, rgba(13,71,161,0.92), rgba(25,118,210,0.92));
        padding:11px 16px;
        font-size:11px;font-weight:800;color:#fff;
        letter-spacing:.06em;text-transform:uppercase;
        border-bottom:1px solid var(--border);white-space:nowrap;
    }
    .sp-table thead th a { color:#fff; text-decoration:none; }
    .sp-table thead th a:hover { color:rgba(255,255,255,0.92); text-decoration:underline; }
    .sp-table tbody td {
        padding:13px 16px;font-size:13.5px;color:var(--text);
        border-bottom:1px solid rgba(25,118,210,0.06);
        vertical-align:middle;
    }

    /* Pagination */
    .sp-pagination {
        padding:14px 22px;
        background:rgba(13,71,161,0.03);
        border-top:1px solid var(--border);
        display:flex;align-items:center;justify-content:center;
    }
    .sp-pagination .pagination { margin:0; }
    .sp-pagination .page-link {
        border-radius:8px !important;margin:0 2px;
        border:1.5px solid var(--border);
        color:var(--navy);font-weight:700;font-size:13px;
        font-family:'Nunito',sans-serif;transition:all .18s ease;
    }
    .sp-pagination .page-link:hover { background:rgba(13,71,161,0.08);border-color:var(--blue-lt); }
    .sp-pagination .page-item.active .page-link {
        background:linear-gradient(135deg,var(--navy),var(--blue));
        border-color:var(--navy);color:#fff;
    }

    /* Modal styling (Adjust Stock) */
    .sp-modal .modal-content {
        border:none;
        border-radius:18px;
        box-shadow:0 16px 50px rgba(13,71,161,0.18);
        overflow:hidden;
    }
    .sp-modal .modal-header {
        padding:18px 24px;
        background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
        border:none;position:relative;overflow:hidden;
    }
    .sp-modal .modal-header::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-modal .modal-header::after  { content:'';position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.05);top:-70px;right:-40px;pointer-events:none; }
    .sp-modal .modal-title {
        font-family:'Nunito',sans-serif;
        font-size:16px;
        font-weight:800;
        color:#fff;
        position:relative;
        z-index:1;
    }
    .sp-modal .btn-close { filter:brightness(0) invert(1); opacity:.75; position:relative; z-index:1; }
    .sp-modal .btn-close:hover { opacity:1; }

    .sp-modal .modal-body { padding:22px 24px; }
    .sp-modal .modal-footer {
        border-top:1px solid var(--border);
        padding:16px 24px;
        background:rgba(13,71,161,0.02);
    }

    .sp-modal .form-label {
        font-size:11.5px;
        font-weight:700;
        color:var(--navy);
        letter-spacing:.05em;
        text-transform:uppercase;
        margin-bottom:6px;
        font-family:'Nunito',sans-serif;
    }
    .sp-modal .form-control,
    .sp-modal .form-select {
        border-radius:11px;
        border:1.5px solid var(--border);
        padding:10px 14px;
        font-size:13.5px;
        background:#fafcff;
        color:var(--text);
        font-family:'Plus Jakarta Sans',sans-serif;
        box-shadow:none;
        transition:border-color .18s, box-shadow .18s;
    }
    .sp-modal .form-control:focus,
    .sp-modal .form-select:focus {
        border-color:var(--blue-lt);
        box-shadow:0 0 0 3px rgba(66,165,245,0.12);
        background:#fff;
    }

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@section('content')
<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <main class="flex-fill p-4" style="position:relative;z-index:1;">
        <div class="sp-wrap">
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-boxes-stacked"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Inventory</div>
                        <div class="sp-ph-title">Stock Management</div>
                        <div class="sp-ph-sub">Intelligent inventory monitoring</div>
                    </div>
                </div>

                <div class="sp-ph-actions">
                    <a href="{{ route('superadmin.inventory.procurement') }}" class="btn btn-warning btn-sm fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-truck-loading"></i>
                        Procurement Needs
                        @php
                            $procCount = \App\Models\SaleItem::where('is_for_procurement', true)->where('pending_qty', '>', 0)->distinct('product_id')->count('product_id');
                        @endphp
                        @if($procCount > 0)
                            <span class="badge bg-danger rounded-pill">{{ $procCount }}</span>
                        @endif
                    </a>
                    <div class="sp-search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" class="sp-search-input" id="searchFilterHeader" placeholder="Search..." value="{{ request('search') }}">
                    </div>

                    <div class="dropdown" style="position:relative;">
                        <button class="sp-filter-btn dropdown-toggle" type="button" id="filterDropdownBtn" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i>
                            Filters
                            <span class="badge bg-secondary ms-1" id="activeFiltersCount">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                            <!-- Filter content will be included here -->
                        </ul>
                    </div>
                </div>
            </div>

            @include('SuperAdmin.inventory.stock-filters')

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Stock Overview</div>
                    <span class="sp-c-badge">{{ $products->total() }} records</span>
                </div>

                <div class="sp-card-body">
                    <div class="sp-table-wrap sp-table-scroll">
                        <table class="sp-table table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Product {{ request('sort_by') == 'product_name' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Branch</th>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Current Stock {{ request('sort_by') == 'current_stock' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'unit_price', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Unit Price {{ request('sort_by') == 'unit_price' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>Total Value</th>
                                <th>
                                    <a href="{{ route('superadmin.inventory.stock-management', ['sort_by' => 'last_updated', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                        Last Updated {{ request('sort_by') == 'last_updated' ? (request('sort_direction') == 'asc' ? '↑' : '↓') : '' }}
                                    </a>
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $currentStock = (int) ($product->current_stock ?? 0);
                                    $minStockLevel = (int) ($product->min_stock_level ?? 0);
                                    $maxStockLevel = (int) ($product->max_stock_level ?? 0);
                                    $lowStockThreshold = (int) ($product->low_stock_threshold ?? 0);

                                    $effectiveMin = $minStockLevel > 0 ? $minStockLevel : 10;
                                    $effectiveMax = $maxStockLevel > 0 ? $maxStockLevel : 100;
                                    $effectiveLow = $lowStockThreshold > 0 ? max($effectiveMin, $lowStockThreshold) : $effectiveMin;

                                    if ($currentStock <= 0) {
                                        $stockLevel = 'out_of_stock';
                                    } elseif ($currentStock <= $effectiveMin) {
                                        $stockLevel = 'critical_stock';
                                    } elseif ($currentStock <= $effectiveLow) {
                                        $stockLevel = 'low_stock';
                                    } elseif ($currentStock <= $effectiveMax) {
                                        $stockLevel = 'in_stock';
                                    } else {
                                        $stockLevel = 'overstock';
                                    }

                                    $rowClass = match ($stockLevel) {
                                        'out_of_stock' => 'table-danger',
                                        'critical_stock' => 'table-danger',
                                        'low_stock' => 'table-warning',
                                        'overstock' => 'table-info',
                                        default => '',
                                    };

                                    $badgeClass = match ($stockLevel) {
                                        'out_of_stock' => 'bg-danger',
                                        'critical_stock' => 'bg-danger',
                                        'low_stock' => 'bg-warning text-dark',
                                        'in_stock' => 'bg-success',
                                        'overstock' => 'bg-info text-dark',
                                    };

                                    $stockLabel = match ($stockLevel) {
                                        'out_of_stock' => 'Out of Stock',
                                        'critical_stock' => 'Critical',
                                        'low_stock' => 'Low',
                                        'in_stock' => 'In Stock',
                                        'overstock' => 'Overstock',
                                    };
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td>
                                        <a href="{{ route('superadmin.products.show', $product->id) }}" class="text-decoration-none">
                                            {{ $product->product_name }}
                                        </a>
                                        <span class="badge {{ $badgeClass }} ms-2">{{ $stockLabel }}</span>
                                    </td>
                                    <td>{{ $product->brand_name ?? 'N/A' }}</td>
                                    <td>{{ $product->category_name ?? 'N/A' }}</td>
                                    <td>{{ $product->branch_name ?? 'Main Branch' }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $currentStock }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($product->unit_price ?? 0, 2) }}</td>
                                    <td>{{ number_format(($product->unit_price ?? 0) * $currentStock, 2) }}</td>
                                    <td>{{ $product->last_stock_update ? \Carbon\Carbon::parse($product->last_stock_update)->format('M d, Y H:i') : 'Never' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-primary adjust-stock-btn" data-bs-toggle="modal" data-bs-target="#adjustStockModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" data-current-stock="{{ $currentStock }}" data-branch-id="{{ $product->branch_id ?? request('branch_id') }}" data-branch-name="{{ $product->branch_name ?? '' }}" title="Adjust Stock">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-info history-btn" data-bs-toggle="modal" data-bs-target="#stockHistoryModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}" title="History">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="sp-pagination">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade sp-modal" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">Adjust Stock for <span id="adjustProductName"></span> - <span id="adjustBranchName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Left Column: Stock Adjustment Form -->
                    <div class="col-md-6" id="stockAdjustmentFormColumn">
                        <!-- Current Stock Card -->
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">Current Stock Record from <span id="adjustBranchName2"></span></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4 class="text-primary" id="adjustCurrentStock">0</h4>
                                        <p class="text-muted mb-0">Units available</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- General Overview Card -->
                        <div class="card mb-3 border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">General Overview</h6>
                            </div>
                            <div class="card-body">
                                <div class="row" id="otherBranchesStock">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>
                        </div>

                        <!-- Stock Adjustment Options -->
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Stock Adjustment Options</h6>
                            </div>
                            <div class="card-body">
                                <form id="adjustStockForm" method="POST" action="">
                                    @csrf
                                    <input type="hidden" id="adjustProductId" name="product_id">
                                    <div class="mb-3">
                                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                                        <select class="form-control" id="adjustmentType" name="adjustment_type" required>
                                            <option value="">-- Select Adjustment Type --</option>
                                            <option value="purchase">Add from Purchase</option>
                                            <option value="transfer">Create Stock Transfer</option>
                                        </select>
                                    </div>

                                    <!-- Purchase Option -->
                                    <div id="purchaseOption" style="display: none;">
                                        <div class="mb-3">
                                            <label for="purchase_id" class="form-label">Purchase ID</label>
                                            <select class="form-control" id="purchase_id" name="purchase_id">
                                                <option value="">-- Select Purchase --</option>
                                                <!-- Will be populated dynamically -->
                                            </select>
                                            <small class="form-text text-muted">
                                                Select a purchase that contains this product. Availability is based on
                                                Purchased − Stocked − Sold per purchase batch.
                                            </small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="purchaseQuantity" class="form-label">Quantity to Add</label>
                                            <input type="number" name="purchase_quantity" id="purchaseQuantity" class="form-control" min="1" required>
                                            <small id="purchaseStats" class="form-text text-muted d-block"></small>
                                            <small id="purchaseWarning" class="form-text text-danger d-none"></small>
                                        </div>
                                    </div>

                                    <!-- Transfer Option -->
                                    <div id="transferOption" style="display: none;">
                                        <div class="mb-3">
                                            <label for="fromBranch" class="form-label">From Branch</label>
                                            <select class="form-control" id="fromBranch" name="from_branch">
                                                <option value="">-- Select Branch --</option>
                                                <!-- Will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="toBranch" class="form-label">To Branch</label>
                                            <select class="form-control" id="toBranch" name="to_branch">
                                                <option value="">-- Select Destination Branch --</option>
                                                <!-- Will be populated dynamically -->
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="transferPrice" class="form-label">Price</label>
                                            <input type="number" name="transfer_price" id="transferPrice" class="form-control" min="0" step="0.01" placeholder="Enter price">
                                            <small id="currentRkPriceLabel" class="form-text text-muted">Current price from RK: ₱0.00</small>
                                        </div>
                                        <div class="mb-3">
                                            <label for="transferQuantity" class="form-label">Quantity to Transfer</label>
                                            <input type="number" name="transfer_quantity" id="transferQuantity" class="form-control" min="1" required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Sales Graph -->
                    <div class="col-md-6" id="salesGraphColumn">
                        <!-- Sales graph will be rendered here automatically -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sp-btn sp-btn-outline" data-bs-dismiss="modal">Close</button>
                <button type="button" class="sp-btn sp-btn-primary" id="saveAdjustmentBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock In Modal -->
<div class="modal fade" id="stockInModal" tabindex="-1" aria-labelledby="stockInModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockInModalLabel">Stock In - <span id="stockInProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stockInForm">
                    <input type="hidden" id="stockInProductId" name="product_id">
                    <div class="mb-3">
                        <label for="stockInQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="stockInQuantity" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="stockInBranch" class="form-label">Branch</label>
                        <select class="form-select" id="stockInBranch" name="branch_id" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="stockInNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="stockInNotes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStockIn()">Save Stock In</button>
            </div>
        </div>
    </div>
</div>

<!-- Stock History Modal -->
<div class="modal fade sp-modal" id="stockHistoryModal" tabindex="-1" aria-labelledby="stockHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockHistoryModalLabel">Stock History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Minimal filters for history (kept compact for modal) -->
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" id="historySearchInput" class="form-control" placeholder="Search branch, reason or notes">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="historyDateInput" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <select id="historyTypeFilter" class="form-select">
                            <option value="">All Types</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                </div>

                <div id="stockHistoryContent">
                    <!-- Stock history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sp-btn sp-btn-outline" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script src="/js/stock-management.js"></script>
@endsection
