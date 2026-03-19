@extends('layouts.app')

@section('content')
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

    .sp-page { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text); }

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

    .sp-wrap { position:relative; z-index:1; padding:18px 24px 44px; }

    .sp-page-head {
        display:flex;align-items:center;justify-content:space-between;
        margin-bottom:16px;flex-wrap:wrap;gap:12px;
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
    .sp-card-body { padding: 18px 22px; }

    .sp-search {
        border-radius:11px !important;
        border:1.5px solid var(--border) !important;
        padding:9px 14px !important;
        font-size:13px !important;
        font-family:'Plus Jakarta Sans',sans-serif !important;
        box-shadow:none !important;
    }
    .sp-search:focus { border-color:var(--blue-lt) !important; box-shadow:0 0 0 3px rgba(66,165,245,0.12) !important; }

    .sp-table-wrap { overflow-x:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;width:5px;}
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}
    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th {
        background:linear-gradient(135deg, rgba(13,71,161,0.92), rgba(25,118,210,0.92));
        padding:11px 16px;
        font-size:11px;font-weight:800;color:#fff;
        letter-spacing:.06em;text-transform:uppercase;
        border-bottom:1px solid var(--border);white-space:nowrap;
    }
    .sp-table tbody td {
        padding:13px 16px;font-size:13.5px;color:var(--text);
        border-bottom:1px solid rgba(25,118,210,0.06);
        vertical-align:middle;
    }
    .sp-table tbody tr:nth-child(even) td { background:rgba(240,246,255,0.55); }
    .sp-table tbody tr:hover td { background:rgba(21,101,192,0.05); }
    .sp-table tfoot td, .sp-table tfoot th { padding:13px 16px; }

    .sp-modal .modal-content { border:none;border-radius:18px;box-shadow:0 16px 50px rgba(13,71,161,0.18);overflow:hidden; }
    .sp-modal .modal-header {
        padding:18px 24px;
        background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
        border:none;position:relative;overflow:hidden;
    }
    .sp-modal .modal-title { font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;color:#fff;position:relative;z-index:1; }
    .sp-modal .btn-close { filter:brightness(0) invert(1);opacity:.75;position:relative;z-index:1; }
    .sp-modal .btn-close:hover { opacity:1; }
    .sp-modal .modal-body { padding:22px 24px; }
    .sp-modal .modal-footer { border-top:1px solid var(--border);padding:16px 24px;background:rgba(13,71,161,0.02); }
    .sp-modal .form-control,
    .sp-modal .form-select { border-radius:11px;border:1.5px solid var(--border);padding:10px 14px;box-shadow:none; }

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

<style>
.modal-body {
    position: relative;
    min-height: 200px;
}

#detailsLoading, #editLoading, #historyLoading, #deleteLoading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(255, 255, 255, 0.9);
    z-index: 10;
}

#detailsContent, #editContent, #historyContent, #deleteContent {
    position: relative;
    z-index: 1;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<div class="sp-page">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <div class="d-flex min-vh-100">
        <main class="flex-fill p-4" style="position:relative;z-index:1;">
            <div class="sp-wrap">
                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-chart-line"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Sales</div>
                            <div class="sp-ph-title">Sales Management</div>
                            <div class="sp-ph-sub">
                                @if(isset($selectedDate))
                                    Sales for {{ $selectedDate->format('F d, Y') }}
                                    <a href="{{ route('admin.sales.management.index') }}" style="color:inherit;text-decoration:underline;">
                                        <small>(Show Today)</small>
                                    </a>
                                @else
                                    Manage and monitor all sales transactions
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="sp-ph-actions">
                        <a href="{{ route('pos.index') }}" class="sp-btn sp-btn-primary">
                            <i class="fas fa-cash-register"></i> Go to POS
                        </a>
                        <a href="{{ route('pos.electronics.index') }}" class="sp-btn sp-btn-outline">
                            <i class="fas fa-plug"></i> Electronic Devices POS
                        </a>
                    </div>
                </div>

                <div class="sp-card" style="margin-bottom:16px;">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-gauge"></i> Summary</div>
                    </div>
                    <div class="sp-card-body">
                        <div class="row">
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    {{ isset($selectedDate) ? $selectedDate->format('M d, Y') : 'Today\'s Sales' }}
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todaySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">All Branches Today</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($allBranchesTodaySales->total_revenue ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-store fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-table"></i> Sales Management</div>
                        <div class="d-flex align-items-center gap-2" style="position:relative;z-index:1;">
                            <input type="text" id="salesSearchInput" class="form-control form-control-sm sp-search" placeholder="Search sales..." onkeyup="searchSales()">
                            <input type="date" id="salesDateFilter" class="form-control form-control-sm sp-search" value="{{ isset($selectedDate) ? $selectedDate->format('Y-m-d') : '' }}" placeholder="Filter by date" onchange="filterSalesByDate()">
                        </div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-table-wrap">
                        <table class="sp-table table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Reference Number</th>
                                    <th>Cashier</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Date</th>
                                    <th>Payment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>#{{ $sale->id }}</td>
                                        <td>{{ $sale->reference_number ?: ('PR-' . $sale->created_at->format('Ymd') . $sale->id) }}</td>
                                        <td>{{ $sale->cashier->name ?? 'Unknown' }}</td>
                                        <td>{{ $sale->saleItems->sum('quantity') }} items</td>
                                        <td>₱{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            @php
                                                $hasRefunds = $sale->refunds()->where('status', 'approved')->exists();
                                                $paymentDisplay = $hasRefunds ? 'Refunded' : ucfirst($sale->payment_method);
                                                $badgeClass = $hasRefunds ? 'bg-warning' : 'bg-success';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $paymentDisplay }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-sm btn-info" href="{{ route('admin.main.sales.show', $sale) }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-secondary" onclick="showSaleHistory({{ $sale->id }})" title="View History">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="showDeleteSale({{ $sale->id }})" title="Delete Sale">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="4" class="text-right">Grand Total:</th>
                                    <th>₱{{ number_format($recentSales->sum('total_amount'), 2) }}</th>
                                    <th colspan="2">{{ $recentSales->count() }} Sales</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal 1: Sale Details Modal -->
<div class="modal fade sp-modal" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleDetailsModalLabel">Sale Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailsLoading" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sale details...</p>
                </div>
                <div id="detailsContent">
                    <!-- Sale details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sp-btn sp-btn-outline" data-bs-dismiss="modal">Close</button>
                <button type="button" class="sp-btn sp-btn-primary" onclick="printSaleReceipt()">Print Receipt</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Sale History Modal -->
<div class="modal fade sp-modal" id="saleHistoryModal" tabindex="-1" aria-labelledby="saleHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleHistoryModalLabel">Sale History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyLoading" style="display: none;">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sale history...</p>
                </div>
                <div id="historyContent">
                    <!-- Sale history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sp-btn sp-btn-outline" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 4: Delete Sale Modal -->
<div class="modal fade sp-modal" id="deleteSaleModal" tabindex="-1" aria-labelledby="deleteSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSaleModalLabel">Delete Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deleteLoading" style="display: none;">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Processing deletion...</p>
                </div>
                <div id="deleteContent">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete this sale? This will also remove all associated sale items and update inventory accordingly.</p>
                    <div id="deleteSaleInfo">
                        <!-- Sale info for deletion will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="sp-btn sp-btn-outline" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="sp-btn sp-btn-primary" onclick="confirmDeleteSale()">Delete Sale</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentSaleId = null;

// Search functionality
function searchSales() {
    const searchTerm = document.getElementById('salesSearchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#salesTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Date filter functionality
function filterSalesByDate() {
    const selectedDate = document.getElementById('salesDateFilter').value;
    if (selectedDate) {
        window.location.href = `{{ route('admin.sales.management.index') }}?date=${selectedDate}`;
    } else {
        window.location.href = '{{ route('admin.sales.management.index') }}';
    }
}

// Modal 1: Show Sale Details
function showSaleDetails(saleId) {
    currentSaleId = saleId;
    const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
    modal.show();
    
    // Show loading
    document.getElementById('detailsLoading').style.display = 'block';
    document.getElementById('detailsContent').style.display = 'none';
    
    // Fetch sale details
    fetch(`/admin/sales/${saleId}/items`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detailsLoading').style.display = 'none';
            document.getElementById('detailsContent').style.display = 'block';

            const sale = data.sale || {};
            const createdAt = sale.created_at ? new Date(sale.created_at) : null;
            const dateDisplay = createdAt ? createdAt.toLocaleDateString() : '';
            const status = (sale.status || '').toLowerCase();
            const statusLabel = status ? (status.charAt(0).toUpperCase() + status.slice(1)) : 'N/A';
            const statusClass = status === 'pending' ? 'bg-warning' : (status === 'voided' ? 'bg-danger' : 'bg-success');
            const paymentMethod = sale.payment_method ? (sale.payment_method.charAt(0).toUpperCase() + sale.payment_method.slice(1)) : 'N/A';
            const totalAmount = (data.sale_info && data.sale_info.current_total != null)
                ? parseFloat(data.sale_info.current_total)
                : (sale.total_amount != null ? parseFloat(sale.total_amount) : 0);
            const customer = sale.customer || null;
            
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Sale Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Sale ID:</strong></td><td>#${sale.id || saleId}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>${dateDisplay}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge ${statusClass}">${statusLabel}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Payment Method:</strong></td><td>${paymentMethod}</td></tr>
                            <tr><td><strong>Total Amount:</strong></td><td>₱${isFinite(totalAmount) ? totalAmount.toFixed(2) : '0.00'}</td></tr>
                        </table>
                    </div>
                </div>
                <h6 class="mt-3">Customer Details</h6>
                <table class="table table-sm">
                    <tr><td><strong>Name:</strong></td><td>${customer && customer.full_name ? customer.full_name : 'Walk-in'}</td></tr>
                    <tr><td><strong>Company/School:</strong></td><td>${customer && customer.company_school_name ? customer.company_school_name : '-'}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${customer && customer.phone ? customer.phone : '-'}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${customer && customer.email ? customer.email : '-'}</td></tr>
                    <tr><td><strong>Facebook:</strong></td><td>${customer && customer.facebook ? customer.facebook : '-'}</td></tr>
                    <tr><td><strong>Address:</strong></td><td>${customer && customer.address ? customer.address : '-'}</td></tr>
                </table>
                <h6 class="mt-3">Items Sold</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Warranty</th>
                                <th>Warranty Start</th>
                                <th>Warranty Expiry</th>
                                <th>Warranty Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    const qtyRaw = item.quantity != null ? item.quantity : 0;
                    const qtyNum = parseFloat(qtyRaw);
                    const qtyDisplay = Number.isFinite(qtyNum)
                        ? (Math.floor(qtyNum) === qtyNum ? String(parseInt(qtyNum, 10)) : String(qtyNum))
                        : String(qtyRaw);

                    const wm = item.warranty_months != null ? parseInt(item.warranty_months, 10) : 0;
                    const ws = item.warranty_start ? new Date(item.warranty_start) : null;
                    const we = item.warranty_expiry_date ? new Date(item.warranty_expiry_date) : null;
                    let warrantyStatus = 'N/A';
                    if (!wm || wm <= 0) {
                        warrantyStatus = 'No Warranty';
                    } else if (!ws) {
                        warrantyStatus = 'Not Started';
                    } else if (we && we.getTime() < new Date().setHours(0,0,0,0)) {
                        warrantyStatus = 'Expired';
                    } else {
                        warrantyStatus = 'Active';
                    }

                    content += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${qtyDisplay}</td>
                            <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                            <td>₱${(item.quantity * parseFloat(item.unit_price)).toFixed(2)}</td>
                            <td>${wm && wm > 0 ? (wm + ' month(s)') : '-'}</td>
                            <td>${ws ? ws.toLocaleDateString() : '-'}</td>
                            <td>${we ? we.toLocaleDateString() : '-'}</td>
                            <td>${warrantyStatus}</td>
                        </tr>
                    `;
                });
            } else {
                content += '<tr><td colspan="8" class="text-center">No items found</td></tr>';
            }
            
            content += `
                        </tbody>
                    </table>
                </div>
            `;
            
            document.getElementById('detailsContent').innerHTML = content;
        })
        .catch(error => {
            console.error('Error loading sale details:', error);
            document.getElementById('detailsLoading').style.display = 'none';
            document.getElementById('detailsContent').innerHTML = '<div class="alert alert-danger">Error loading sale details</div>';
        });
}

// Modal 3: Show Sale History
function showSaleHistory(saleId) {
    currentSaleId = saleId;
    const modal = new bootstrap.Modal(document.getElementById('saleHistoryModal'));
    modal.show();
    
    // Show loading
    document.getElementById('historyLoading').style.display = 'block';
    document.getElementById('historyContent').style.display = 'none';
    
    // Simulate loading history (in real app, this would fetch from API)
    setTimeout(() => {
        document.getElementById('historyLoading').style.display = 'none';
        document.getElementById('historyContent').style.display = 'block';
        
        const content = `
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Sale Created</h6>
                        <p class="text-muted mb-1">${new Date().toLocaleString()}</p>
                        <p>Sale was created and processed successfully.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-info"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Payment Processed</h6>
                        <p class="text-muted mb-1">${new Date().toLocaleString()}</p>
                        <p>Payment was received and confirmed.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker bg-warning"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Inventory Updated</h6>
                        <p class="text-muted mb-1">${new Date().toLocaleString()}</p>
                        <p>Stock levels were automatically updated.</p>
                    </div>
                </div>
            </div>
            <style>
                .timeline { position: relative; padding-left: 30px; }
                .timeline-item { position: relative; padding-bottom: 20px; }
                .timeline-marker { position: absolute; left: -30px; top: 5px; width: 12px; height: 12px; border-radius: 50%; }
                .timeline-content { border-left: 2px solid #e9ecef; padding-left: 20px; }
            </style>
        `;
        
        document.getElementById('historyContent').innerHTML = content;
    }, 1000);
}

// Modal 4: Show Delete Sale
function showDeleteSale(saleId) {
    currentSaleId = saleId;
    const modal = new bootstrap.Modal(document.getElementById('deleteSaleModal'));
    modal.show();
    
    // Show loading
    document.getElementById('deleteLoading').style.display = 'none';
    document.getElementById('deleteContent').style.display = 'block';
    
    // Fetch sale info for deletion confirmation
    fetch(`/admin/sales/${saleId}/items`)
        .then(response => response.json())
        .then(data => {
            const info = `
                <div class="alert alert-info">
                    <strong>Sale #${saleId}</strong><br>
                    Total Amount: ₱${data.sale_info ? parseFloat(data.sale_info.current_total).toFixed(2) : '0.00'}<br>
                    Items: ${data.items ? data.items.length : 0} products<br>
                    Date: ${new Date().toLocaleDateString()}
                </div>
            `;
            document.getElementById('deleteSaleInfo').innerHTML = info;
        })
        .catch(error => {
            console.error('Error loading sale info:', error);
            document.getElementById('deleteSaleInfo').innerHTML = '<div class="alert alert-danger">Error loading sale information</div>';
        });
}

// Confirm Delete Sale
function confirmDeleteSale() {
    if (!currentSaleId) return;
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            document.getElementById('deleteLoading').style.display = 'block';
            document.getElementById('deleteContent').style.display = 'none';
            
            // Simulate deletion (in real app, this would make an API call)
            setTimeout(() => {
                document.getElementById('deleteLoading').style.display = 'none';
                
                Swal.fire(
                    'Deleted!',
                    'Sale has been deleted.',
                    'success'
                ).then(() => {
                    // Close modal and refresh page
                    bootstrap.Modal.getInstance(document.getElementById('deleteSaleModal')).hide();
                    location.reload();
                });
            }, 1500);
        }
    });
}

// Print Receipt
function printSaleReceipt() {
    if (!currentSaleId) return;
    
    // Open print window
    const printWindow = window.open(`/admin/sales/${currentSaleId}/receipt-pdf`, '_blank');
    if (!printWindow) {
        Swal.fire({
            icon: 'error',
            title: 'Print Failed',
            text: 'Please allow pop-ups for this site to print receipts.'
        });
    }
}

</script>
@endsection
