<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System - Electronic Devices</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --light-bg: #f8fafc;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            --card-hover-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px;
            padding: 30px;
            min-height: calc(100vh - 40px);
        }

        .table-container {
            max-height: calc(100vh - 350px);
            overflow-y: auto;
        }

        .order-summary {
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .search-section {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            border-radius: 15px;
            padding: 14px;
            color: white;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }

        .search-section h4 {
            font-size: 16px;
            margin-bottom: 10px !important;
        }

        .search-input {
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            background: white;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
            outline: none;
        }

        .search-btn {
            background: var(--success-color);
            border: none;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        #results-table {
            table-layout: fixed;
            width: 100%;
        }

        #results-table th:nth-child(1),
        #results-table td:nth-child(1) {
            width: 34%;
            word-break: break-word;
        }

        #results-table th:nth-child(2),
        #results-table td:nth-child(2) {
            width: 36%;
            word-break: break-word;
        }

        #results-table th:nth-child(3),
        #results-table td:nth-child(3) {
            width: 15%;
        }

        #results-table th:nth-child(4),
        #results-table td:nth-child(4) {
            width: 15%;
        }

        .search-btn:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .products-card {
            border-radius: 15px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .products-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-5px);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--light-bg), #e2e8f0);
            border-bottom: 2px solid var(--primary-color);
            padding: 20px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .table-custom {
            border-radius: 10px;
            overflow: hidden;
        }

        .table-custom thead {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
        }

        .table-custom th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .table-custom tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-custom tbody tr:hover {
            background: #f1f5f9;
            transform: scale(1.01);
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
        }

        .add-btn {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .order-card {
            border-radius: 15px;
            border: none;
            box-shadow: var(--card-shadow);
            background: linear-gradient(135deg, #ffffff, #f8fafc);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .price-display {
            font-weight: 700;
            color: var(--success-color);
            font-size: 16px;
        }

        .header-title {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 28px;
        }

        /* Sidebar Styles */
        .sidebar-panel {
            position: absolute;
            left: 0;
            top: 0;
            width: 600px;
            height: 100%;
            background: white;
            border-radius: 15px 0 0 15px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease-in-out;
            z-index: 10;
            overflow: hidden;
        }

        .sidebar-panel.collapsed {
            transform: translateX(-100%);
        }

        .main-content-panel {
            margin-left: 600px;
            transition: margin-left 0.3s ease-in-out;
            min-height: 600px;
            width: calc(100% - 600px);
        }

        .main-content-panel.full-width {
            margin-left: 0;
            width: 100%;
        }

        .sidebar-panel .search-section {
            border-radius: 15px 15px 0 0;
            padding: 14px;
        }

        .sidebar-panel .products-card {
            border-radius: 0;
            box-shadow: none;
            border: none;
            border-left: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
        }

        .sidebar-panel .card-header-custom {
            border-left: none;
            border-right: none;
        }

        /* Product details padding adjustments */
        .sidebar-panel .products-card .card-body {
            padding: 20px;
        }

        .sidebar-panel .table-custom td {
            padding: 15px;
            vertical-align: middle;
        }

        .sidebar-panel .table-custom th {
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .sidebar-panel .form-select {
            padding: 8px 12px;
            min-height: 38px;
        }

        .sidebar-panel .add-btn {
            padding: 8px 16px;
            font-weight: 600;
        }

        /* Enhanced padding for collapsed state */
        .sidebar-panel.collapsed .products-card .card-body {
            padding: 20px;
        }

        .sidebar-panel.collapsed .table-custom td {
            padding: 18px 15px;
        }

        .sidebar-panel.collapsed .table-custom th {
            padding: 18px 15px;
        }

        .sidebar-panel.collapsed .form-select {
            padding: 10px 14px;
            min-height: 42px;
        }

        .sidebar-panel.collapsed .add-btn {
            padding: 10px 18px;
        }

        /* Hide scrollbars */
        .product-list-container::-webkit-scrollbar,
        .order-info-container::-webkit-scrollbar,
        .order-items-container::-webkit-scrollbar {
            display: none;
        }

        .product-list-container,
        .order-info-container,
        .order-items-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="header-title mb-2">
                    <i class="fas fa-plug me-3"></i>BGH POS - Electronic Devices
                </h1>
                <p class="text-muted mb-0">POS with warranty and serial number capture</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="toggleSidebar()" id="header-sidebar-toggle">
                    <i class="fas fa-chevron-left me-2" id="header-toggle-icon"></i>Toggle Sidebar
                </button>
                <a href="{{ route('pos.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-cash-register me-2"></i>Standard POS
                </a>
                <a href="{{ route('admin.sales.management.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
        </div>

        <div class="position-relative">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar-panel">
                <div class="search-section">
                    <h4 class="mb-3">
                        <i class="fas fa-search me-2"></i>Product Search
                        <button class="btn btn-sm btn-outline-light float-end" onclick="toggleSidebar()" id="sidebar-toggle-btn">
                            <i class="fas fa-chevron-left" id="sidebar-toggle-icon"></i>
                        </button>
                    </h4>
                    <div class="input-group input-group-lg">
                        <input id="search-input" type="text" class="form-control search-input" placeholder="🔍 Search by product name, barcode, or model..." />
                        <button id="search-btn" class="btn search-btn">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>

                <div class="products-card" id="products-section">
                    <div class="card-header card-header-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-box me-2"></i>Available Products
                            </span>
                            <span class="badge bg-primary" id="results-count">0 results</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive product-list-container" style="max-height: calc(100vh - 350px); overflow-y: auto;">
                            <table class="table table-hover table-custom mb-0" id="results-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Branches</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="empty-state">
                                            <i class="fas fa-search"></i>
                                            <p class="mb-0">Start searching to see products...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div id="main-content" class="main-content-panel">
                <div class="order-summary">
                    <div class="card order-card">
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Summary</h5>
                                </div>
                                <div class="card-body order-info-container" style="max-height: calc(100vh - 200px); overflow-y: auto;">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Customer Details</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label">Customer Name</label>
                                                        <input type="text" class="form-control" id="customer_name" placeholder="Enter customer name">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Phone Number</label>
                                                        <input type="text" class="form-control" id="customer_phone" placeholder="Enter phone number">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Email (optional)</label>
                                                        <input type="email" class="form-control" id="customer_email" placeholder="Enter email">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-2">
                                                        <label class="form-label">Company/School Name (optional)</label>
                                                        <input type="text" class="form-control" id="customer_company_school" placeholder="Enter company/school name">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Facebook (optional)</label>
                                                        <input type="text" class="form-control" id="customer_facebook" placeholder="Enter Facebook name/link">
                                                    </div>
                                                    <div class="mb-0">
                                                        <label class="form-label">Address</label>
                                                        <input type="text" class="form-control" id="customer_address" placeholder="Enter address">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <strong>Order Items</strong>
                                        </div>
                                        <div class="card-body p-0">
                                            <div id="order-items" class="order-items-container">
                                                <div class="text-muted text-center py-3">No items in cart</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Payment Method:</label>
                                            <div class="btn-group w-100" role="group">
                                                <input type="radio" class="btn-check" name="payment_method" id="payment_cash" value="cash" checked>
                                                <label class="btn btn-outline-success" for="payment_cash">
                                                    <i class="fas fa-money-bill-wave me-2"></i>Cash
                                                </label>

                                                <input type="radio" class="btn-check" name="payment_method" id="payment_credit" value="credit">
                                                <label class="btn btn-outline-primary" for="payment_credit">
                                                    <i class="fas fa-credit-card me-2"></i>Credit
                                                </label>
                                            </div>
                                        </div>

                                        <div id="credit-details" class="mb-3" style="display: none;">
                                            <div class="card border-warning">
                                                <div class="card-body bg-light">
                                                    <h6 class="card-title text-warning mb-3">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Credit Details
                                                    </h6>
                                                    <div class="mb-2">
                                                        <label class="form-label">Date:</label>
                                                        <input type="date" class="form-control" id="credit_due_date" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Notes:</label>
                                                        <textarea class="form-control" id="credit_notes" rows="2" placeholder="Add credit notes..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Notes (optional):</label>
                                            <textarea class="form-control" id="order_notes" rows="2" placeholder="Add notes..."></textarea>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Total:</h5>
                                            <h4 class="mb-0 text-primary" id="total-amount">₱0.00</h4>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Order Status:</label>
                                            <select class="form-select" id="order_status">
                                                <option value="completed" selected>Completed</option>
                                                <option value="pending">Pending (Quotation)</option>
                                            </select>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success btn-lg" onclick="checkout()">
                                                <i class="fas fa-credit-card me-2"></i>Checkout
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="clearCart()">
                                                <i class="fas fa-trash me-2"></i>Clear Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function(){
        const input = document.getElementById('search-input');
        const btn = document.getElementById('search-btn');
        const tableBody = document.querySelector('#results-table tbody');
        const resultsCount = document.getElementById('results-count');

        async function search(mode='list'){
            const keyword = input.value.trim();

            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>`;
            resultsCount.textContent = keyword ? 'Searching...' : 'Loading...';

            const url = "{{ route('pos.lookup') }}";
            const form = new FormData();
            form.set('barcode', keyword);
            form.set('mode', mode);
            form.set('electronics_only', '1');

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: form
                });
                const data = await res.json();

                let items = [];
                if (Array.isArray(data)) {
                    items = data;
                } else if (data && Array.isArray(data.items)) {
                    items = data.items;
                } else if (data && data.items && typeof data.items === 'object') {
                    items = data.items;
                } else if (data && data.error) {
                    items = [];
                } else if (data) {
                    items = [data];
                }
                resultsCount.textContent = `${items.length} products`;

                if(items.length === 0){
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p class="mb-0">No products found${keyword ? ' for "' + keyword + '"' : ''}</p>
                            </td>
                        </tr>`;
                    return;
                }

                tableBody.innerHTML = items.map(it => {
                    const displayName = (it && (it.name || it.product_name || (it.product && it.product.product_name))) || 'N/A';
                    const displayBarcode = (it && (it.barcode || it.product_barcode || (it.product && it.product.barcode))) || 'N/A';
                    const displayPrice = (it && (it.price != null ? it.price : it.selling_price)) || 0;

                    const branchesHtml = (it.branches && it.branches.length > 0)
                        ? (() => {
                            let firstStockBranchSelected = false;
                            let hasAnyStock = false;
                            
                            // First pass: check if any branch has stock
                            it.branches.forEach(b => {
                                const units = Array.isArray(b.stock_units) ? b.stock_units : [];
                                const isSingleUnit = units.length === 1;
                                const singleUnit = isSingleUnit ? units[0] : null;
                                const unitStock = isSingleUnit ? (singleUnit?.stock ?? b.stock ?? 0) : (b.stock ?? 0);
                                if (Number(unitStock || 0) > 0) hasAnyStock = true;
                            });
                            
                            const optionsHtml = it.branches.map((b, index) => {
                                const units = Array.isArray(b.stock_units) ? b.stock_units : [];
                                const isSingleUnit = units.length === 1;
                                const singleUnit = isSingleUnit ? units[0] : null;
                                const optionPrice = isSingleUnit ? (singleUnit?.price || b.price || 0) : (b.price || 0);
                                const unitTypeId = isSingleUnit ? (singleUnit?.unit_type_id ?? '') : '';
                                const unitName = isSingleUnit ? (singleUnit?.unit_name ?? '') : '';
                                const unitStock = isSingleUnit ? (singleUnit?.stock ?? b.stock ?? 0) : (b.stock ?? 0);
                                const hasStock = Number(unitStock || 0) > 0;
                                
                                // Select first branch with stock, or first branch if no stock exists
                                const shouldBeSelected = (hasStock && !firstStockBranchSelected) || (!hasAnyStock && index === 0);
                                if (hasStock && !firstStockBranchSelected) firstStockBranchSelected = true;

                                return `
                                    <option value="${b.branch_id}"
                                        data-branch-name="${b.branch_name || ('Branch #' + b.branch_id)}"
                                        data-stock="${Number(unitStock || 0)}"
                                        data-price="${Number(optionPrice || 0).toFixed(2)}"
                                        data-unit-type-id="${unitTypeId}"
                                        data-unit-name="${unitName}"
                                        data-units='${JSON.stringify(units || [])}'
                                        ${shouldBeSelected ? 'selected' : ''}>
                                        ${b.branch_name || ('Branch #' + b.branch_id)} (${Number(b.stock ?? 0)})
                                    </option>
                                `;
                            }).join('');

                            return `
                                <div>
                                    <select class="form-select form-select-sm js-branch-select" data-product-id="${it.product_id}">
                                        ${optionsHtml}
                                    </select>
                                    <div class="mt-1 js-unit-container" data-product-id="${it.product_id}"></div>
                                </div>
                            `;
                        })()
                        : '<span class="text-muted">No stock</span>';

                    const canBeAdded = it.branches && it.branches.length > 0;

                    return `
                    <tr>
                        <td>
                            <div class="fw-semibold">${displayName}</div>
                            <div class="text-muted small">${displayBarcode}</div>
                        </td>
                        <td>${branchesHtml}</td>
                        <td class="text-end price-display" data-product-id="${it.product_id}"><span class="text-muted">Select branch</span></td>
                        <td class="text-end">
                            <button class="btn add-btn" onclick="addToOrder(this, ${it.product_id}, '${String(displayName).replace(/'/g, "\\'")}', ${it.warranty_coverage_months || 0})" ${!canBeAdded ? 'disabled' : ''}>
                                <i class="fas fa-plus me-1"></i>Add
                            </button>
                        </td>
                    </tr>`;
                }).join('');

                items.forEach(it => {
                    const branchSel = document.querySelector(`select.js-branch-select[data-product-id="${it.product_id}"]`);
                    if (!branchSel) return;

                    branchSel.addEventListener('change', () => {
                        renderUnitSelectForProduct(it.product_id);
                        updateProductPriceDisplay(it.product_id);
                    });

                    renderUnitSelectForProduct(it.product_id);
                    updateProductPriceDisplay(it.product_id);
                });

            } catch (error) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p class="mb-0">Search failed. Please try again.</p>
                        </td>
                    </tr>`;
            }
        }

        let cart = [];
        let isSidebarCollapsed = false;

        window.toggleSidebar = function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarIcon = document.getElementById('sidebar-toggle-icon');
            const headerIcon = document.getElementById('header-toggle-icon');
            
            isSidebarCollapsed = !isSidebarCollapsed;
            
            if (isSidebarCollapsed) {
                // Hide sidebar to the left
                sidebar.classList.add('collapsed');
                mainContent.classList.add('full-width');
                sidebarIcon.className = 'fas fa-chevron-right';
                headerIcon.className = 'fas fa-chevron-right me-2';
            } else {
                // Show sidebar from the left
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('full-width');
                sidebarIcon.className = 'fas fa-chevron-left';
                headerIcon.className = 'fas fa-chevron-left me-2';
            }
        };

        function renderUnitSelectForProduct(productId) {
            const branchSel = document.querySelector(`select.js-branch-select[data-product-id="${productId}"]`);
            const unitContainer = document.querySelector(`.js-unit-container[data-product-id="${productId}"]`);
            if (!branchSel || !unitContainer) return;

            const branchOpt = branchSel.options[branchSel.selectedIndex];
            const unitsRaw = branchOpt ? (branchOpt.dataset.units || '[]') : '[]';
            let units = [];
            try {
                units = JSON.parse(unitsRaw);
            } catch (e) {
                units = [];
            }

            if (!Array.isArray(units) || units.length <= 1) {
                unitContainer.innerHTML = '';
                return;
            }

            unitContainer.innerHTML = `
                <select class="form-select form-select-sm js-unit-select" data-product-id="${productId}">
                    ${units.map((u, uidx) => {
                        const unitName = (u && u.unit_name) ? u.unit_name : '';
                        const unitStock = (u && u.stock != null) ? u.stock : 0;
                        const unitPrice = (u && u.price != null) ? u.price : 0;
                        const unitTypeId = (u && u.unit_type_id != null) ? u.unit_type_id : '';
                        return `<option value="${unitTypeId}" data-stock="${unitStock}" data-price="${Number(unitPrice).toFixed(2)}" data-unit-name="${unitName}" ${uidx === 0 ? 'selected' : ''}>${unitName || 'Unit'} - ₱${Number(unitPrice).toFixed(2)}</option>`;
                    }).join('')}
                </select>
            `;

            const sel = unitContainer.querySelector('select.js-unit-select');
            if (sel) {
                sel.addEventListener('change', () => updateProductPriceDisplay(productId));
            }
        }

        function updateProductPriceDisplay(productId) {
            const branchSel = document.querySelector(`select.js-branch-select[data-product-id="${productId}"]`);
            const priceCell = document.querySelector(`td.price-display[data-product-id="${productId}"]`);
            if (!priceCell || !branchSel) return;

            const branchOpt = branchSel.options[branchSel.selectedIndex];
            if (!branchOpt) return;

            const unitSelect = document.querySelector(`select.js-unit-select[data-product-id="${productId}"]`);
            if (unitSelect && unitSelect.value) {
                const opt = unitSelect.options[unitSelect.selectedIndex];
                const unitPrice = parseFloat(opt.dataset.price || '0');
                priceCell.textContent = `₱${unitPrice.toFixed(2)}`;
                return;
            }

            const branchPrice = parseFloat(branchOpt.dataset.price || '0');
            priceCell.textContent = `₱${branchPrice.toFixed(2)}`;
        }

        window.addToOrder = function(button, productId, name, warrantyCoverageMonths) {
            const branchSel = document.querySelector(`select.js-branch-select[data-product-id="${productId}"]`);
            if (!branchSel || !branchSel.value) {
                Swal.fire('Error', 'Please select a branch.', 'error');
                return;
            }

            const branchId = parseInt(branchSel.value);
            const branchOpt = branchSel.options[branchSel.selectedIndex];
            const branchName = (branchOpt && branchOpt.dataset.branchName) ? branchOpt.dataset.branchName : ('Branch #' + branchId);

            const unitSelect = document.querySelector(`select.js-unit-select[data-product-id="${productId}"]`);
            let unitTypeId = null;
            let unitName = null;
            let stock = parseFloat((branchOpt && branchOpt.dataset.stock) ? branchOpt.dataset.stock : '0');
            let price = parseFloat((branchOpt && branchOpt.dataset.price) ? branchOpt.dataset.price : '0');

            if (unitSelect && unitSelect.value) {
                const opt = unitSelect.options[unitSelect.selectedIndex];
                unitTypeId = parseInt(unitSelect.value);
                unitName = opt.dataset.unitName || opt.textContent;
                stock = parseFloat(opt.dataset.stock || '0');
                price = parseFloat(opt.dataset.price || '0');
            } else {
                const dsUnitTypeId = parseInt((branchOpt && branchOpt.dataset.unitTypeId) ? branchOpt.dataset.unitTypeId : '0');
                if (dsUnitTypeId > 0) {
                    unitTypeId = dsUnitTypeId;
                    unitName = (branchOpt && branchOpt.dataset.unitName) ? branchOpt.dataset.unitName : null;
                }
            }

            // Check if product already exists in cart
            const existingProductKey = `${productId}-${branchId}-${unitTypeId || 0}`;
            const existingProduct = cart.find(item => 
                item.product_id === productId && 
                item.branch_id === branchId && 
                item.unit_type_id === unitTypeId
            );

            if (existingProduct) {
                // Add new serial/warranty entry to existing product
                const newEntry = {
                    serial_number: '',
                    warranty_months: warrantyCoverageMonths || 0,
                    in_stock: stock > 0
                };
                existingProduct.entries.push(newEntry);
                existingProduct.quantity = existingProduct.entries.length;
            } else {
                // Create new product entry
                const cartIdentifier = `${productId}-${branchId}-${unitTypeId || 0}-${Date.now()}`;
                cart.push({
                    cartIdentifier,
                    product_id: productId,
                    branch_id: branchId,
                    unit_type_id: unitTypeId,
                    unit_name: unitName,
                    name,
                    branchName,
                    price,
                    quantity: 1,
                    stock,
                    entries: [{
                        serial_number: '',
                        warranty_months: warrantyCoverageMonths || 0,
                        in_stock: stock > 0
                    }]
                });
            }

            updateCartDisplay();
        };

        window.removeFromCart = function(cartIdentifier) {
            const idx = cart.findIndex(i => i.cartIdentifier === cartIdentifier);
            if (idx > -1) {
                cart.splice(idx, 1);
                updateCartDisplay();
            }
        };

        window.clearCart = function() {
            if (cart.length === 0) return;
            cart = [];
            updateCartDisplay();
        };

        window.setSerial = function(cartIdentifier, entryIndex, value) {
            const item = cart.find(i => i.cartIdentifier === cartIdentifier);
            if (!item || !item.entries || !item.entries[entryIndex]) return;
            item.entries[entryIndex].serial_number = String(value || '').trim();
        };

        window.setWarrantyMonths = function(cartIdentifier, entryIndex, value) {
            const item = cart.find(i => i.cartIdentifier === cartIdentifier);
            if (!item || !item.entries || !item.entries[entryIndex]) return;
            const n = parseInt(value, 10);
            item.entries[entryIndex].warranty_months = isFinite(n) ? n : 0;
        };

        window.removeEntry = function(cartIdentifier, entryIndex) {
            const item = cart.find(i => i.cartIdentifier === cartIdentifier);
            if (!item || !item.entries || !item.entries[entryIndex]) return;
            
            item.entries.splice(entryIndex, 1);
            item.quantity = item.entries.length;
            
            // Remove product if no entries left
            if (item.entries.length === 0) {
                removeFromCart(cartIdentifier);
            } else {
                updateCartDisplay();
            }
        };

        window.checkout = function() {
            if (cart.length === 0) {
                Swal.fire({ icon: 'info', title: 'Cart is Empty', text: 'Please add items to your cart before checkout.'});
                return;
            }

            // Check for missing serial numbers in any entry
            const missingEntry = cart.find(item => 
                item.entries && item.entries.some(entry => entry.in_stock && !entry.serial_number)
            );
            if (missingEntry) {
                Swal.fire({ icon: 'warning', title: 'Missing Serial Number', text: 'Please enter serial number for all items before checkout.'});
                return;
            }

            processOrder();
        };

        function processOrder() {
            Swal.fire({ title: 'Processing Order...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('{{ route("pos.electronics.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: cart.flatMap(item => 
                        item.entries.map(entry => ({
                            product_id: item.product_id,
                            branch_id: item.branch_id,
                            unit_type_id: item.unit_type_id,
                            unit_name: item.unit_name,
                            name: item.name,
                            quantity: 1,
                            price: item.price,
                            serial_number: entry.serial_number,
                            warranty_months: entry.warranty_months,
                        }))
                    ),
                    total: cart.reduce((sum, item) => sum + (item.price * item.entries.length), 0),
                    payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                    order_status: document.getElementById('order_status') ? document.getElementById('order_status').value : 'completed',
                    notes: document.getElementById('order_notes') ? document.getElementById('order_notes').value : null,
                    customer_name: document.getElementById('customer_name') ? document.getElementById('customer_name').value : null,
                    customer_company_school_name: document.getElementById('customer_company_school') ? document.getElementById('customer_company_school').value : null,
                    customer_phone: document.getElementById('customer_phone') ? document.getElementById('customer_phone').value : null,
                    customer_email: document.getElementById('customer_email') ? document.getElementById('customer_email').value : null,
                    customer_facebook: document.getElementById('customer_facebook') ? document.getElementById('customer_facebook').value : null,
                    customer_address: document.getElementById('customer_address') ? document.getElementById('customer_address').value : null,
                    credit_due_date: document.getElementById('credit_due_date') ? document.getElementById('credit_due_date').value : null,
                    credit_notes: document.getElementById('credit_notes') ? document.getElementById('credit_notes').value : null,
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    cart = [];
                    updateCartDisplay();
                    if (data.receipt_pdf_url) {
                        Swal.fire({ icon: 'success', title: 'Order Saved!', text: 'Opening receipt PDF...', timer: 1500, showConfirmButton: false })
                            .then(() => window.open(data.receipt_pdf_url, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes'));
                    } else if (data.auto_receipt && data.receipt_url) {
                        Swal.fire({ icon: 'success', title: 'Order Saved!', text: 'Opening receipt...', timer: 1500, showConfirmButton: false })
                            .then(() => window.open(data.receipt_url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes'));
                    } else {
                        Swal.fire({ icon: 'success', title: 'Order Saved!', text: data.message || 'Order has been processed successfully.'});
                    }
                    search('list');
                } else {
                    Swal.fire({ icon: 'error', title: 'Order Failed', text: data.message || 'There was an error processing your order.'});
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Order Failed', text: 'Network error. Please try again.'});
            });
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('order-items');
            const totalAmount = document.getElementById('total-amount');

            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="text-muted text-center py-3">No items in cart</div>';
                totalAmount.textContent = '₱0.00';
                return;
            }

            let total = 0;
            cartItems.innerHTML = cart.map(item => {
                const itemTotal = item.price * item.entries.length;
                total += itemTotal;

                const entriesHtml = item.entries.map((entry, index) => `
                    <div class="entry-item border rounded p-2 mb-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">Unit ${index + 1}</span>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeEntry('${item.cartIdentifier}', ${index})" title="Remove this unit">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="form-label mb-1 small">Serial Number</label>
                                <input type="text" class="form-control form-control-sm" placeholder="${entry.in_stock ? 'Enter serial' : 'Not required (out of stock)'}" 
                                    value="${entry.serial_number || ''}" 
                                    onchange="setSerial('${item.cartIdentifier}', ${index}, this.value)" 
                                    ${entry.in_stock ? '' : 'disabled'}>
                            </div>
                            <div class="col-12">
                                <label class="form-label mb-1 small">Warranty (months)</label>
                                <input type="number" min="0" class="form-control form-control-sm" placeholder="0" 
                                    value="${entry.warranty_months || 0}" 
                                    onchange="setWarrantyMonths('${item.cartIdentifier}', ${index}, this.value)">
                            </div>
                        </div>
                    </div>
                `).join('');

                return `
                    <div class="cart-item mb-3 p-3 border rounded bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">${item.name}</div>
                                <div class="text-muted small">${item.branchName} - ₱${item.price.toFixed(2)} x ${item.entries.length}</div>
                            </div>
                            <div class="ms-2">
                                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.cartIdentifier}')" title="Remove all units">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="entries-container">
                            ${entriesHtml}
                        </div>

                        <div class="mt-3 pt-2 border-top text-end">
                            <strong>Subtotal: ₱${itemTotal.toFixed(2)}</strong>
                        </div>
                    </div>
                `;
            }).join('');

            totalAmount.textContent = `₱${total.toFixed(2)}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const creditDetails = document.getElementById('credit-details');
            const dueDateInput = document.getElementById('credit_due_date');

            if (dueDateInput) {
                dueDateInput.value = new Date().toISOString().split('T')[0];
                dueDateInput.min = new Date().toISOString().split('T')[0];
            }

            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (!creditDetails) return;
                    creditDetails.style.display = this.value === 'credit' ? 'block' : 'none';
                });
            });
        });

        btn.addEventListener('click', () => search('list'));
        window.addEventListener('load', () => search('list'));
        input.addEventListener('input', () => search('list'));
        input.focus();

    })();
    </script>
</body>
</html>
