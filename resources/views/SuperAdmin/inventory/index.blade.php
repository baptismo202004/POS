@extends('layouts.app')
@section('title', 'Inventory')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
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
    .sp-wrap { position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif; }

    /* Page header */
    .sp-page-head {
        display:flex;align-items:center;justify-content:space-between;
        margin-bottom:22px;flex-wrap:wrap;gap:14px;
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

    /* Alert */
    .sp-alert {
        margin:18px 22px 0;
        border-radius:14px;
        border:1px solid rgba(13,71,161,0.15);
        background:rgba(13,71,161,0.04);
        color:var(--text);
        padding:12px 14px;
        font-size:13px;
    }

    /* Table */
    .sp-table-wrap { overflow-x:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;width:5px;}
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}
    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th {
        background:rgba(13,71,161,0.03);
        padding:11px 16px;
        font-size:11px;font-weight:700;color:var(--navy);
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
                    <div class="sp-ph-icon"><i class="fas fa-warehouse"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Inventory</div>
                        <div class="sp-ph-title">Inventory</div>
                        <div class="sp-ph-sub">Monitor stock, sales, and revenue</div>
                    </div>
                </div>
                <div class="sp-ph-actions">
                    <div class="sp-search-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="sp-search-input" placeholder="Search products..." value="{{ request('search') }}">
                    </div>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Stock Overview</div>
                    <span class="sp-c-badge">{{ $products->total() }} records</span>
                </div>

                @if(request('filter') == 'out-of-stock')
                    <div class="sp-alert" role="alert">
                        <strong>Filter Applied:</strong> Showing only out of stock items (≤ 15 units)
                    </div>
                @endif

                <div class="sp-table-wrap">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color:inherit;text-decoration:none;">Product</a></th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color:inherit;text-decoration:none;">Current Stock</a></th>
                                <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'total_sold', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color:inherit;text-decoration:none;">Total Sold</a></th>
                                <th><a href="{{ route('superadmin.inventory.index', ['sort_by' => 'total_revenue', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" style="color:inherit;text-decoration:none;">Total Revenue</a></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr class="{{ request('filter') == 'out-of-stock' && $product->current_stock <= 15 ? 'table-danger' : '' }}">
                                    <td style="font-weight:600;"><a href="{{ route('superadmin.products.show', $product->id) }}" style="color:inherit;text-decoration:none;">{{ $product->product_name }}</a></td>
                                    <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                                    <td class="{{ request('filter') == 'out-of-stock' && $product->current_stock <= 15 ? 'text-danger font-weight-bold' : '' }}">{{ $product->current_stock }}</td>
                                    <td>{{ $product->total_sold }}</td>
                                    <td>{{ number_format($product->total_revenue, 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.stockin.index') }}" class="btn btn-sm btn-primary">Manage Stock</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center" style="color:var(--muted);padding:34px;">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="sp-pagination">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </main>
</div>

    
    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to update dashboard alerts
            function updateDashboardAlerts(outOfStockCount) {
                try {
                    console.log('Updating dashboard alerts with count:', outOfStockCount);
                    
                    // Try multiple selectors for the alerts count element
                    const totalAlertsElement = document.getElementById('totalAlertsCount') || 
                                          document.querySelector('[id="totalAlertsCount"]') ||
                                          document.querySelector('.widget-badge.alert-count');
                    
                    console.log('Found alerts element:', totalAlertsElement);
                    
                    if (totalAlertsElement) {
                        // Get current alerts from dashboard if available
                        fetch('/dashboard/widgets', {
                            method: 'GET',
                            headers: { 
                                'X-Requested-With': 'XMLHttpRequest', 
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Dashboard data received:', data);
                            if (data.alerts) {
                                const totalAlerts = data.alerts.outOfStock + data.alerts.negativeProfit + data.alerts.voidedSales + data.alerts.belowCostSales + data.alerts.highDiscountUsage;
                                totalAlertsElement.textContent = totalAlerts;
                                console.log('Updated total alerts to:', totalAlerts);
                                
                                // Try to update alerts list with multiple selectors
                                const alertsList = document.getElementById('alertsList') || 
                                                   document.querySelector('[id="alertsList"]') ||
                                                   document.querySelector('.alerts-list');
                                
                                console.log('Found alerts list element:', alertsList);
                                
                                if (alertsList) {
                                    const alertItems = [];
                                    
                                    if (data.alerts.outOfStock > 0) {
                                        alertItems.push(`<div class="alert-item critical clickable" onclick="window.location.href='/superadmin/inventory?filter=out-of-stock'"><i class="fas fa-exclamation-triangle alert-icon" style="color:#E91E63"></i><div class="alert-content"><div class="alert-title">${data.alerts.outOfStock} items out of stock</div><div class="alert-description">Restock needed</div></div></div>`);
                                    }
                                    
                                    alertsList.innerHTML = alertItems.length > 0 ? alertItems.join('') : '<div class="alert-item" style="border-left-color:#43A047;background:rgba(67,160,71,0.05)"><i class="fas fa-check-circle alert-icon" style="color:#43A047"></i><div class="alert-content"><div class="alert-title">No alerts</div><div class="alert-description">All systems normal</div></div></div>';
                                    console.log('Updated alerts list');
                                } else {
                                    console.log('Alerts list element not found');
                                }
                            } else {
                                console.log('No alerts data in response');
                            }
                        })
                        .catch(error => {
                            console.error('Error updating dashboard alerts:', error);
                        });
                    } else {
                        console.log('Alerts count element not found - trying alternative approach');
                        
                        // Alternative: Try to update via parent window if in iframe
                        if (window.parent && window.parent.document) {
                            const parentAlertsElement = window.parent.document.getElementById('totalAlertsCount');
                            if (parentAlertsElement) {
                                console.log('Found alerts element in parent window');
                                parentAlertsElement.textContent = outOfStockCount;
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error in updateDashboardAlerts:', error);
                }
            }

            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true,
                confirmButtonText: 'Great!',
                confirmButtonColor: '#02C39A',
                backdrop: `
                    rgba(2, 195, 154, 0.1)
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

            
            const searchInput = document.getElementById('searchInput');
            let debounceTimer;

            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = searchInput.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', query);
                    window.location.href = url.toString();
                }, 500);
            });
        });
    </script>
@endsection
