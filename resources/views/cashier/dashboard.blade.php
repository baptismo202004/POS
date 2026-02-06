@extends('layouts.app')
@section('title', 'Cashier Dashboard')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-fixed {
            display: none;
        }
        .main-content {
            margin-left: 0 !important;
        }

        /* ========================================
           ELECTRIC MODERN PALETTE - CASHIER DASHBOARD
           Minimal, branch-scoped interface
           ======================================== */
        
        :root {
            /* Electric Modern Palette */
            --electric-blue: #0D47A1;
            --neon-blue: #2196F3;
            --cyan-bright: #00E5FF;
            --magenta: #E91E63;
            --violet: #9C27B0;
            --lime-electric: #C6FF00;
            --slate-bg: #ECEFF1;
            --ice-white: #FAFBFC;
            
            /* Dashboard Color Mapping */
            --app-bg: linear-gradient(135deg, #ECEFF1 0%, #E8EAF6 100%);
            --card-bg: #FAFBFC;
            --card-border: rgba(13, 71, 161, 0.15);
            --page-header: #263238;
            
            /* KPI Colors - Electric Theme */
            --text-primary: #263238;
            --text-secondary: #546E7A;
            --success-teal: #43A047;
            --danger-red: #E53935;
            --warning-yellow: #C6FF00;
            --attention-orange: #E91E63;
            --info-blue: #2196F3;
            --hover-blue: #00E5FF;
            --inactive-text: #78909C;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--slate-bg) 0%, #E8EAF6 100%);
        }
        
        .sidebar { width: 220px; }
        
        .dash-header { 
            font-size: 24px; 
            font-weight: 700; 
            background: linear-gradient(135deg, var(--electric-blue), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }
        
        .branch-badge {
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        /* Minimal KPI Cards */
        .kpi-card { 
            border-radius: 12px; 
            padding: 16px; 
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(13, 71, 161, 0.08);
            transition: all 0.3s;
            height: 100%;
        }
        
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.15);
            border-color: var(--cyan-bright);
        }
        
        .kpi-icon { 
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(0, 229, 255, 0.15));
            color: var(--neon-blue);
            margin-bottom: 12px;
        }
        
        .kpi-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--electric-blue);
            margin-bottom: 4px;
            font-family: 'Inter', monospace;
        }
        
        .kpi-label {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        
        /* Navigation Cards */
        .nav-card {
            border-radius: 12px;
            padding: 20px;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .nav-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(33, 150, 243, 0.15);
            border-color: var(--cyan-bright);
            color: var(--text-primary);
            text-decoration: none;
        }
        
        .nav-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            font-size: 20px;
        }
        
        .nav-content h5 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--electric-blue);
        }
        
        .nav-content p {
            margin: 0;
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        /* Chart Container */
        .chart-container {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 20px;
            height: 300px;
            border: 2px solid var(--card-border);
            box-shadow: 0 2px 8px rgba(13, 71, 161, 0.08);
        }
        
        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--electric-blue);
            margin-bottom: 16px;
        }
        
        /* Recent Sales List */
        .sales-list {
            max-height: 200px;
            overflow-y: auto;
        }
        
        .sales-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--card-border);
        }
        
        .sales-item:last-child {
            border-bottom: none;
        }
        
        .sale-amount {
            font-weight: 600;
            color: var(--neon-blue);
            font-family: 'Inter', monospace;
        }
        
        .sale-time {
            font-size: 11px;
            color: var(--text-secondary);
        }
        
        /* Top Products */
        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid var(--card-border);
        }
        
        .product-item:last-child {
            border-bottom: none;
        }
        
        .product-name {
            font-size: 13px;
            color: var(--text-primary);
            flex: 1;
            margin-right: 8px;
        }
        
        .product-revenue {
            font-weight: 600;
            color: var(--neon-blue);
            font-family: 'Inter', monospace;
            font-size: 12px;
        }
        
        /* Alert Badge */
        .alert-badge {
            background: linear-gradient(135deg, var(--magenta), var(--violet));
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        /* Low Stock Item Hover */
        .low-stock-item:hover {
            background-color: rgba(33, 150, 243, 0.1);
            transition: background-color 0.3s ease;
        }
        
        .low-stock-item .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 767.98px) {
            .kpi-card { margin-bottom: 12px; }
            .nav-card { margin-bottom: 12px; }
            .chart-container { margin-bottom: 16px; }
        }
    </style>
@endpush

@section('content')
<div class="p-3 p-lg-4">
    <!-- Header -->
    <div class="d-flex flex-wrap align-items-start justify-content-between mb-4">
        <div>
            <div class="dash-header mb-2">Cashier Dashboard</div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Welcome back, {{ auth()->user()->name }}</span>
                @if($branch)
                    <span class="branch-badge">{{ $branch->branch_name }}</span>
                @endif
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: white;">
                <i class="fas fa-user me-2"></i>
                {{ auth()->user()->name }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            Logout
                        </a>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div class="kpi-value">₱{{ number_format($todaySales, 2) }}</div>
                <div class="kpi-label">Today's Sales</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="kpi-value">{{ $todayTransactions }}</div>
                <div class="kpi-label">Transactions</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card">
                <div class="kpi-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="kpi-value">₱{{ number_format($todayExpenses, 2) }}</div>
                <div class="kpi-label">Today's Expenses</div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="kpi-card" onclick="showLowStockModal()" style="cursor: pointer;">
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="kpi-value">
                    {{ $lowStockCount }}
                    @if($lowStockCount > 0)
                        <span class="alert-badge">Alert</span>
                    @endif
                </div>
                <div class="kpi-label">Low Stock Items</div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row mb-4">
        @foreach ($modules as $moduleKey => $moduleData)
            @if (isset($permissions[$moduleKey]) && in_array('view', $permissions[$moduleKey]))
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3 {{ in_array($moduleKey, ['stock_in','product_category']) ? 'd-none' : '' }}">
                    <a href="{{ $moduleKey === 'products' ? route('cashier.products.index') : ($moduleKey === 'product_category' ? route('cashier.categories.index') : ($moduleKey === 'purchases' ? route('cashier.purchases.index') : ($moduleKey === 'inventory' ? route('cashier.inventory.index') : ($moduleKey === 'stock_in' ? route('cashier.stockin.index') : '#')))) }}" class="nav-card" @if($moduleKey === 'products') onclick="animateAndNavigate(event, 'products')" @elseif($moduleKey === 'product_category') onclick="animateAndNavigate(event, 'product_category')" @elseif($moduleKey === 'purchases') onclick="animateAndNavigate(event, 'purchases')" @elseif($moduleKey === 'inventory') onclick="animateAndNavigate(event, 'inventory')" @elseif($moduleKey === 'stock_in') onclick="animateAndNavigate(event, 'stock_in')" @endif>
                        <div class="nav-icon">
                            <i class="fas fa-{{ $moduleData['icon'] ?? 'cogs' }}"></i>
                        </div>
                        <div class="nav-content">
                            <h5>{{ $moduleData['label'] }}</h5>
                        </div>
                    </a>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Charts and Lists -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <div class="chart-title">Sales Trend (Last 7 Days)</div>    
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <div class="chart-title">Recent Sales</div>
                <div class="sales-list">
                    @if($recentSales->count() > 0)
                        @foreach($recentSales as $sale)
                            <div class="sales-item">
                                <div>
                                    <div class="sale-amount">₱{{ number_format($sale->total_amount, 2) }}</div>
                                    <div class="sale-time">{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</div>
                                </div>
                                <div class="text-muted small">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">No sales today</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    @if($topProducts->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="chart-container">
                    <div class="chart-title">Top Products Today</div>
                    <div class="row">
                        @foreach($topProducts as $product)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="product-item">
                                    <div class="product-name">{{ $product->product_name }}</div>
                                    <div class="product-revenue">₱{{ number_format($product->revenue, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/cashier-sidebar.js') }}"></script>
<script src="{{ asset('js/cashier-sidebar-inventory.js') }}"></script>
<script src="{{ asset('js/cashier-sidebar-products.js') }}"></script>
<script src="{{ asset('js/cashier-sidebar-purchase.js') }}"></script>
<script src="{{ asset('js/cashier-sidebar-stockin.js') }}"></script>
<script src="{{ asset('js/cashier-sidebar-categories.js') }}"></script>
<script src="{{ asset('js/cashier-sidebar-resize.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Sales',
                    data: @json($salesData),
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            callback: function(value) { 
                                return '₱' + value.toLocaleString(); 
                            },
                            color: '#546E7A'
                        },
                        grid: { 
                            color: 'rgba(13, 71, 161, 0.1)' 
                        }
                    },
                    x: {
                        ticks: { 
                            color: '#546E7A' 
                        },
                        grid: { 
                            color: 'rgba(13, 71, 161, 0.1)' 
                        }
                    }
                }
            }
        });
    }

});

function viewProductDetails(productName, branchName) {
    // Close the modal first
    const modal = bootstrap.Modal.getInstance(document.getElementById('lowStockModal'));
    if (modal) {
        modal.hide();
    }
    
    // Show info about the product with branch information
    Swal.fire({
        icon: 'info',
        title: 'Product Details',
        html: `
            <div class="text-start">
                <p><strong>Product:</strong> ${productName}</p>
                <p><strong>Branch:</strong> <span class="badge bg-primary">${branchName}</span></p>
                <p><strong>Status:</strong> <span class="badge bg-danger">Low Stock</span></p>
                <p class="text-muted">Click "Manage Stock" to update inventory levels for this branch.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Manage Stock',
        cancelButtonText: 'Close',
        confirmButtonColor: '#2196F3'
    }).then((result) => {
        if (result.isConfirmed) {
            // Navigate to inventory management (you can update this route)
            window.location.href = '{{ route('cashier.inventory.index') }}';
        }
    });

}

function showLowStockModal() {
    // Get low stock items via AJAX
    fetch('/cashier/dashboard/low-stock')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let modalHtml = `
                    <div class="modal fade" id="lowStockModal" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Low Stock Items
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="list-group">`;
                
                // Split items into two columns
                const halfLength = Math.ceil(data.lowStockItems.length / 2);
                const firstColumn = data.lowStockItems.slice(0, halfLength);
                const secondColumn = data.lowStockItems.slice(halfLength);
                
                // First column
                firstColumn.forEach(item => {
                    modalHtml += `
                                                <div class="list-group-item low-stock-item d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="viewProductDetails('${item.product_name}', '${item.branch_name}')">
                                                    <div>
                                                        <i class="fas fa-box text-warning me-2"></i>
                                                        <span class="fw-medium">${item.product_name}</span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-map-marker-alt me-1"></i>${item.branch_name}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-danger me-2">${item.current_stock}</span>
                                                        <small class="text-muted">${item.unit_name}</small>
                                                    </div>
                                                </div>`;
                });
                
                modalHtml += `
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="list-group">`;
                
                // Second column
                secondColumn.forEach(item => {
                    modalHtml += `
                                                <div class="list-group-item low-stock-item d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="viewProductDetails('${item.product_name}', '${item.branch_name}')">
                                                    <div>
                                                        <i class="fas fa-box text-warning me-2"></i>
                                                        <span class="fw-medium">${item.product_name}</span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-map-marker-alt me-1"></i>${item.branch_name}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-danger me-2">${item.current_stock}</span>
                                                        <small class="text-muted">${item.unit_name}</small>
                                                    </div>
                                                </div>`;
                });
                
                modalHtml += `
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                
                // Remove existing modal if any
                const existingModal = document.getElementById('lowStockModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Add modal to page
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('lowStockModal'));
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to load low stock items'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load low stock items'
            });
        });
}

function peso(n) {
    return new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'}).format(n || 0);
}

function animateAndNavigate(event, module) {
    event.preventDefault();
    const clickedCard = event.currentTarget;
    const allCards = document.querySelectorAll('.nav-card');
    const targetUrl = clickedCard.href;

    // Check if clicking on products or product_category
    const isProductsClick = module === 'products';
    const isCategoryClick = module === 'product_category';
    const isPurchasesClick = module === 'purchases';
    const isInventoryClick = module === 'inventory';
    const isStockInClick = module === 'stock_in';

    // Create a sidebar container
    const sidebar = document.createElement('div');
    sidebar.style.cssText = `
        position: fixed;
        left: 0;
        top: 0;
        width: 220px;
        height: 100vh;
        background: linear-gradient(180deg, #0D47A1 0%, #1565C0 100%);
        box-shadow: 2px 0 15px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        padding: 20px 10px;
        transform: translateX(-100%);
        transition: transform 0.5s ease-in-out;
    `;
    document.body.appendChild(sidebar);

    // Make sidebar resizable
    makeSidebarResizable(sidebar);

    // Add sidebar header with logo
    const sidebarHeader = document.createElement('div');
    sidebarHeader.style.cssText = `
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    `;
    
    // Create logo container
    const logoContainer = document.createElement('div');
    logoContainer.style.cssText = `
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    // Create logo image
    const logoImg = document.createElement('img');
    logoImg.src = '/images/BGH LOGO.png';
    logoImg.style.cssText = `
        height: 40px;
        width: auto;
        object-fit: contain;
        cursor: pointer;
        transition: transform 0.3s ease;
    `;
    
    // Add click event to logo
    logoImg.addEventListener('click', () => {
        // Close sidebar only if we're on the dashboard page
        if (window.location.pathname === '/cashier/dashboard') {
            const sidebar = document.querySelector('[style*="position: fixed"][style*="left: 0"]');
            if (sidebar) {
                sidebar.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    sidebar.remove();
                }, 300);
            }
        }
    });
    
    logoContainer.appendChild(logoImg);
    sidebarHeader.appendChild(logoContainer);
    sidebar.appendChild(sidebarHeader);

    // Animate cards into sidebar
    allCards.forEach((card, index) => {
        if (card !== clickedCard) {
            // Clone the card content
            const cardContent = card.cloneNode(true);
            // Inherit original classes but override for sidebar context
            cardContent.className = 'nav-card'; // Keep the original class
            cardContent.style.cssText = `
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 20px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                margin-bottom: 10px;
                cursor: pointer;
                text-decoration: none;
                color: white;
                transition: all 0.3s ease;
                transform: translateX(-100%);
                opacity: 0;
            `;

            const iconEl = cardContent.querySelector('.nav-icon');
            if (iconEl) {
                iconEl.style.cssText = `
                    width: 48px;
                    height: 48px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: linear-gradient(135deg, #2196F3, #00E5FF);
                    color: #0D47A1;
                    font-size: 20px;
                    flex-shrink: 0;
                `;
            }

            const contentEl = cardContent.querySelector('.nav-content');
            if (contentEl) {
                contentEl.style.cssText = `
                    display: block;
                    color: white;
                `;
            }

            const titleEl = cardContent.querySelector('.nav-content h5');
            if (titleEl) {
                titleEl.style.cssText = `
                    margin: 0 0 4px 0;
                    font-size: 16px;
                    font-weight: 600;
                    color: white;
                    text-decoration: none;
                `;
            }

            const descEl = cardContent.querySelector('.nav-content p');
            if (descEl) {
                descEl.style.cssText = `
                    margin: 0;
                    font-size: 12px;
                    color: rgba(255, 255, 255, 0.85);
                    text-decoration: none;
                `;
            }

            // Remove link underlines even if browser default styles apply
            cardContent.querySelectorAll('a, h5, p, span').forEach(el => {
                el.style.textDecoration = 'none';
            });
            
            // Remove onclick from cloned cards
            cardContent.removeAttribute('onclick');
            
            // Add hover effect
            cardContent.addEventListener('mouseenter', () => {
                cardContent.style.background = 'rgba(255,255,255,0.2)';
                cardContent.style.transform = 'translateX(5px)';
            });
            cardContent.addEventListener('mouseleave', () => {
                cardContent.style.background = 'rgba(255,255,255,0.1)';
                cardContent.style.transform = 'translateX(0)';
            });
            
            sidebar.appendChild(cardContent);
            
            // Hide the original card immediately
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            
            // Animate in the cloned card
            setTimeout(() => {
                cardContent.style.transform = 'translateX(0)';
                cardContent.style.opacity = '1';
            }, 100 + (index * 100));
            
            // Completely hide original card after animation
            setTimeout(() => {
                card.style.display = 'none';
            }, 500 + (index * 100));
        }
    });

    // Arrange submenu items in the generated sidebar
    const sidebarNavItems = Array.from(sidebar.querySelectorAll('.nav-card'));

    const invItem = sidebarNavItems.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Inventory');
    const stockInItem = sidebarNavItems.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Stock In');
    if (invItem && stockInItem) {
        stockInItem.style.marginLeft = '18px';
        stockInItem.style.paddingLeft = '20px';
        invItem.insertAdjacentElement('afterend', stockInItem);
    }

    const productsItem = sidebarNavItems.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Products');
    const categoryItem = sidebarNavItems.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Product Category');
    if (productsItem && categoryItem) {
        categoryItem.style.marginLeft = '18px';
        categoryItem.style.paddingLeft = '20px';
        productsItem.insertAdjacentElement('afterend', categoryItem);
    }

    // Slide in sidebar and save to session storage if navigating
    if (isProductsClick || isCategoryClick || isPurchasesClick || isInventoryClick || isStockInClick) {
        sidebar.style.transform = 'translateX(0)';
        // Store the sidebar HTML in session storage so it can be recreated on the next page
        sessionStorage.setItem('cashierSidebarHTML', sidebar.outerHTML);
        localStorage.setItem('cashierSidebarHTML', sidebar.outerHTML);
    }

    // Fade out and scale the clicked card
    clickedCard.style.transition = 'all 0.5s ease-in-out';
    clickedCard.style.transform = 'scale(0.95)';
    clickedCard.style.opacity = '0.3';

    // Navigate after animation
    setTimeout(() => {
        window.location.href = targetUrl;
    }, 1500);
}

</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
