<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cashier POS - Electronic Devices</title>
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

        .order-summary .card {
            position: sticky;
            top: 20px;
        }

        #order-items {
            max-height: 260px !important;
            overflow-y: auto;
            padding: 14px 18px;
        }
        #order-items::-webkit-scrollbar { width: 3px; }
        #order-items::-webkit-scrollbar-thumb { background: rgba(13,71,161,0.15); border-radius: 4px; }

        #total-amount {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            font-weight: 900;
            color: var(--primary-color) !important;
        }

        .search-section {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            border-radius: 15px;
            padding: 30px;
            color: white;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }

        .search-input {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            background: white;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
            outline: none;
        }

        .search-btn {
            background: var(--success-color);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
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

        .sidebar-panel .add-btn {
            padding: 8px 16px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="header-title mb-2">
                    <i class="fas fa-plug me-3"></i>Cashier POS - Electronic Devices
                </h1>
                <p class="text-muted mb-0">POS with warranty and serial number capture</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="toggleSidebar()" id="header-sidebar-toggle">
                    <i class="fas fa-chevron-left me-2" id="header-toggle-icon"></i>Toggle Sidebar
                </button>
                <a href="{{ route('cashier.sales.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-cash-register me-2"></i>Standard POS
                </a>
                <a href="{{ route('cashier.sales.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
        </div>

        <div class="alert alert-info">
            <strong>Electronic Devices Rules:</strong>
            Each item requires a <strong>Serial Number</strong> and uses <strong>quantity = 1</strong>.
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
                        <input id="search-input" type="text" class="form-control search-input" 
                               placeholder="🔍 Search by product name, barcode, or model..." />
                        <button id="search-btn" class="btn search-btn">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                    <small class="mt-2 d-block opacity-75">
                        <i class="fas fa-barcode me-1"></i>Use barcode scanner or click search to find products
                    </small>
                </div>

                <!-- Products Table -->
                <div class="card products-card">
                    <div class="card-header card-header-custom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-box me-2"></i>Available Products
                            </span>
                            <span class="badge bg-primary" id="results-count">0 results</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive table-container">
                            <table class="table table-hover table-custom mb-0" id="results-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Barcode</th>
                                        <th class="text-end">Stock</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6" class="empty-state">
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
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Summary</h5>
                        </div>
                        <div class="card-body">
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Notes (optional):</label>
                                            <textarea class="form-control" id="order_notes" rows="3" placeholder="Add notes..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Payment Method:</label>
                                            <select class="form-select" id="payment_method">
                                                <option value="cash" selected>Cash</option>
                                                <option value="credit">Credit</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Order Status:</label>
                                            <select class="form-select" id="order_status">
                                                <option value="completed" selected>Completed</option>
                                                <option value="pending">Pending (Quotation)</option>
                                            </select>
                                        </div>
                                        <div id="credit-details" class="mb-3" style="display:none;">
                                            <div class="card border-warning">
                                                <div class="card-body bg-light">
                                                    <h6 class="card-title text-warning mb-3">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Credit Details
                                                    </h6>
                                                    <div class="mb-2">
                                                        <label class="form-label">Date:</label>
                                                        <input type="date" id="credit_due_date" class="form-control" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label">Notes:</label>
                                                        <textarea id="credit_notes" class="form-control" rows="2" placeholder="Add credit notes..."></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-top pt-3">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Merchandise Total:</label>
                                            <h4 class="text-primary mb-0" id="merchandise-total">₱0.00</h4>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Discount:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="text" class="form-control" id="discount-amount" placeholder="0.00 or 10%" value="0.00">
                                            </div>
                                            <small class="text-muted">Enter amount (e.g., 100.00) or percentage (e.g., 10%)</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Tax:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="text" class="form-control" id="tax-amount" placeholder="0.00 or 12%" value="0.00">
                                            </div>
                                            <small class="text-muted">Enter amount (e.g., 100.00) or percentage (e.g., 12%)</small>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3 mt-3" id="simple-summary-display">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">Merchandise:</span>
                                            <span class="fw-bold" id="display-merchandise">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span id="display-discount-label">Discount:</span>
                                            <span class="fw-bold text-danger" id="display-discount">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span id="display-tax-label">Tax:</span>
                                            <span class="fw-bold text-info" id="display-tax">₱0.00</span>
                                        </div>
                                    </div>

                                    <div class="border-top pt-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Total:</h5>
                                            <h4 class="mb-0 text-primary" id="total-amount">₱0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-3">
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function(){
        document.addEventListener('DOMContentLoaded', function(){
            const cashierBranchId = @json(optional(Auth::user())->branch_id);
            const input = document.getElementById('search-input');
            const btn = document.getElementById('search-btn');
            const tableBody = document.querySelector('#results-table tbody');
            const resultsCount = document.getElementById('results-count');

            async function search(mode='list'){
                const keyword = input.value.trim();

                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>`;
                resultsCount.textContent = keyword ? 'Searching...' : 'Loading...';

                const url = "{{ route('cashier.pos.lookup') }}";
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
                } else if (data && data.success && Array.isArray(data.products)) {
                    items = data.products;
                } else if (data && Array.isArray(data.products)) {
                    items = data.products;
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
                            <td colspan="6" class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <p class="mb-0">No products found${keyword ? ' for "' + keyword + '"' : ''}</p>
                            </td>
                        </tr>`;
                    return;
                }

                // Keep latest loaded items so Add button can resolve branch/unit without relying on hidden DOM inputs
                window.__electronicsItemsById = window.__electronicsItemsById || {};
                items.forEach(it => {
                    const safeProductId = (it && (it.product_id != null ? it.product_id : it.id)) || 0;
                    if (safeProductId) {
                        // Filter branches to only include cashier's branch
                        const branches = Array.isArray(it.branches) ? it.branches : [];
                        it.branches = branches.filter(b => parseInt(b && b.branch_id) === parseInt(cashierBranchId));
                        window.__electronicsItemsById[safeProductId] = it;
                    }
                });

                tableBody.innerHTML = items.map(it => {
                    const safeProductId = (it && (it.product_id != null ? it.product_id : it.id)) || 0;
                    const displayName = (it && (it.name || it.product_name || (it.product && it.product.product_name))) || 'N/A';
                    const displayBarcode = (it && (it.barcode || (it.product && it.product.barcode))) || 'N/A';
                    const displayPrice = (it && (it.price || it.selling_price)) || 0;

                    const canBeAdded = it.branches && it.branches.length > 0;

                    return `
                    <tr>
                        <td><div class="fw-semibold">${displayName}</div></td>
                        <td><code>${displayBarcode}</code></td>
                        <td class="text-end"><span class="badge ${it.total_stock > 10 ? 'bg-success' : (it.total_stock > 0 ? 'bg-warning' : 'bg-secondary')}">${it.total_stock ?? 0}</span></td>
                        <td class="text-end price-display" data-product-id="${safeProductId}">₱${Number(displayPrice || 0).toFixed(2)}</td>
                        <td class="text-end">
                            <button class="btn add-btn" onclick="addToOrder(this, ${safeProductId}, '${String(displayName).replace(/'/g, "\\'")}')" ${!canBeAdded ? 'disabled' : ''}>
                                <i class="fas fa-plus me-1"></i>Add
                            </button>
                        </td>
                    </tr>`;
                }).join('');

                // Ensure initial price display uses latest per-unit price if available
                items.forEach(it => {
                    const safeProductId = (it && (it.product_id != null ? it.product_id : it.id)) || 0;
                    if (safeProductId) {
                        updateProductPriceDisplay(safeProductId);
                    }
                });

            } catch (error) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
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

            if (!sidebar || !mainContent) return;

            isSidebarCollapsed = !isSidebarCollapsed;

            if (isSidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('full-width');
                if (sidebarIcon) sidebarIcon.className = 'fas fa-chevron-right';
                if (headerIcon) headerIcon.className = 'fas fa-chevron-right me-2';
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('full-width');
                if (sidebarIcon) sidebarIcon.className = 'fas fa-chevron-left';
                if (headerIcon) headerIcon.className = 'fas fa-chevron-left me-2';
            }
        };

        function updateProductPriceDisplay(productId) {
            const priceCell = document.querySelector(`td.price-display[data-product-id="${productId}"]`);

            if (!priceCell) return;

            const it = (window.__electronicsItemsById && window.__electronicsItemsById[productId]) ? window.__electronicsItemsById[productId] : null;
            const branches = it && Array.isArray(it.branches) ? it.branches : [];
            const firstBranch = branches.find(b => (parseFloat(b && b.stock) || 0) > 0) || branches[0];
            const units = firstBranch && Array.isArray(firstBranch.stock_units) ? firstBranch.stock_units : [];
            const firstUnit = units.find(u => (parseFloat(u && u.stock) || 0) > 0) || units[0];

            if (firstUnit && firstUnit.price != null) {
                const unitPrice = parseFloat(firstUnit.price || '0');
                priceCell.textContent = `₱${unitPrice.toFixed(2)}`;
                return;
            }

            if (firstBranch && firstBranch.price != null) {
                const branchPrice = parseFloat(firstBranch.price || '0');
                priceCell.textContent = `₱${branchPrice.toFixed(2)}`;
                return;
            }
        }

        window.addToOrder = function(button, productId, name) {
            const it = (window.__electronicsItemsById && window.__electronicsItemsById[productId]) ? window.__electronicsItemsById[productId] : null;
            const branches = it && Array.isArray(it.branches) ? it.branches : [];
            const firstBranch = branches.find(b => (parseFloat(b && b.stock) || 0) > 0) || branches[0];
            if (!firstBranch) {
                Swal.fire('Error', 'Product has no branch information.', 'error');
                return;
            }

            const branchId = parseInt(cashierBranchId || firstBranch.branch_id || firstBranch.id || 0);
            const branchName = firstBranch.branch_name || ('Branch #' + branchId);

            const units = Array.isArray(firstBranch.stock_units) ? firstBranch.stock_units : [];
            const firstUnit = units.find(u => (parseFloat(u && u.stock) || 0) > 0) || units[0];

            let unitTypeId = null;
            let unitName = null;
            let totalStock = parseFloat(firstBranch.stock || '0');
            let price = parseFloat(firstBranch.price || '0');

            if (firstUnit) {
                unitTypeId = parseInt(firstUnit.unit_type_id || 0) || null;
                unitName = firstUnit.unit_name || null;
                totalStock = parseFloat(firstUnit.stock || '0');
                price = parseFloat(firstUnit.price || '0');
            }

            const sameGroup = cart.find(i =>
                parseInt(i.product_id) === parseInt(productId)
                && parseInt(i.branch_id) === parseInt(branchId)
                && parseInt(i.unit_type_id || 0) === parseInt(unitTypeId || 0)
            );

            // How many units already in cart for this product/branch/unit
            const alreadyInCart = sameGroup ? (sameGroup.entries || []).length : 0;
            // This new entry gets stock if there's still remaining stock
            const inStock = (alreadyInCart < totalStock);

            const newEntry = {
                serial_number: '',
                warranty_months: 0,
                in_stock: inStock,
            };

            if (sameGroup) {
                sameGroup.entries = Array.isArray(sameGroup.entries) ? sameGroup.entries : [];
                sameGroup.entries.push(newEntry);
                sameGroup.quantity = sameGroup.entries.length;
            } else {
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
                    stock: totalStock,
                    entries: [newEntry],
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
            clearCustomerDetails();
            updateCartDisplay();
        };

        function clearCustomerDetails() {
            ['customer_name','customer_phone','customer_email','customer_company_school',
             'customer_facebook','customer_address','order_notes','credit_notes'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            const creditDetails = document.getElementById('credit-details');
            if (creditDetails) creditDetails.style.display = 'none';
            const paymentMethod = document.getElementById('payment_method');
            if (paymentMethod) paymentMethod.value = 'cash';
            const orderStatus = document.getElementById('order_status');
            if (orderStatus) orderStatus.value = 'completed';
        }

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

            if (item.entries.length === 0) {
                removeFromCart(cartIdentifier);
            } else {
                updateCartDisplay();
            }
        };

        window.checkout = function() {
            const orderStatus = document.getElementById('order_status') ? document.getElementById('order_status').value : 'completed';
            const isPending = orderStatus === 'pending';

            // --- COMMON CHECKS (all modes) ---
            if (cart.length === 0) {
                Swal.fire({ icon: 'info', title: 'Cart is Empty', text: 'Please add items to your cart before checkout.' });
                return;
            }

            for (const item of cart) {
                if (!item.entries || item.entries.length === 0) {
                    Swal.fire({ icon: 'error', title: 'Invalid Quantity', text: `"${item.name}" must have at least 1 unit.` });
                    return;
                }
                if (item.price < 0) {
                    Swal.fire({ icon: 'error', title: 'Invalid Price', text: `"${item.name}" has an invalid price.` });
                    return;
                }
            }

            const customerName = document.getElementById('customer_name') ? document.getElementById('customer_name').value.trim() : '';
            if (!customerName) {
                Swal.fire({ icon: 'warning', title: 'Missing Customer', text: 'Please enter a customer name before checkout.' });
                document.getElementById('customer_name').focus();
                return;
            }

            // --- PENDING MODE: skip all stock/serial checks ---
            if (isPending) {
                processOrder();
                return;
            }

            // --- COMPLETED MODE: validate serials only for fulfillable qty ---
            for (const item of cart) {
                const entries = item.entries || [];
                // Only entries marked in_stock need a serial
                const fulfillableEntries = entries.filter(e => e.in_stock);
                for (let i = 0; i < fulfillableEntries.length; i++) {
                    if (!fulfillableEntries[i].serial_number) {
                        Swal.fire({ icon: 'warning', title: 'Missing Serial Number', text: `Please enter serial number for "${item.name}" - Unit ${i + 1} (in-stock unit).` });
                        return;
                    }
                }
            }

            processOrder();
        };

        function processOrder() {
            Swal.fire({ title: 'Processing Order...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            fetch('{{ route("cashier.pos.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    products: cart.flatMap(item =>
                        (item.entries || []).map(entry => ({
                            id: item.product_id || item.id,
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
                    total: cart.reduce((sum, item) => sum + (item.price * ((item.entries || []).length || 0)), 0),
                    payment_method: document.getElementById('payment_method') ? document.getElementById('payment_method').value : 'cash',
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
                    const receiptUrl = data.receipt_url;
                    const receiptPdfUrl = data.receipt_pdf_url;
                    const total = cart.reduce((sum, item) => sum + (item.price * ((item.entries || []).length || 0)), 0);

                    if (receiptPdfUrl) {
                        cart = [];
                        clearCustomerDetails();
                        updateCartDisplay();
                        search('list');
                        Swal.fire({ icon: 'success', title: 'Order Saved!', text: 'Opening receipt PDF...', timer: 1500, showConfirmButton: false })
                            .then(() => window.open(receiptPdfUrl, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes'));
                        return;
                    }

                    if (data.auto_receipt && receiptUrl) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Confirmation',
                            html: `
                                <div class="text-start">
                                    <div class="mb-2"><strong>Total:</strong> ₱${Number(total || 0).toFixed(2)}</div>
                                    <label class="form-label">Amount Paid</label>
                                    <input id="amount_paid" type="number" min="0" step="0.01" class="form-control" placeholder="Enter amount paid">
                                    <div class="mt-2" id="change_display" style="font-weight:700; color:#2563eb;">Change: ₱0.00</div>
                                </div>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Confirm & Open Receipt',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#6b7280',
                            didOpen: () => {
                                const input = document.getElementById('amount_paid');
                                const changeEl = document.getElementById('change_display');
                                const compute = () => {
                                    const paid = parseFloat(input.value || '0') || 0;
                                    const change = paid - (parseFloat(total || 0) || 0);
                                    changeEl.textContent = `Change: ₱${(change > 0 ? change : 0).toFixed(2)}`;
                                };
                                input.addEventListener('input', compute);
                                input.focus();
                                compute();
                            },
                            preConfirm: () => {
                                const paid = parseFloat(document.getElementById('amount_paid').value || '0') || 0;
                                if (paid < (parseFloat(total || 0) || 0)) {
                                    Swal.showValidationMessage('Amount paid is less than total.');
                                    return false;
                                }
                                return { paid };
                            }
                        }).then((result) => {
                            if (!result.isConfirmed) return;
                            cart = [];
                            clearCustomerDetails();
                            updateCartDisplay();
                            search('list');
                            window.open(receiptUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
                        });
                    } else {
                        cart = [];
                        clearCustomerDetails();
                        updateCartDisplay();
                        search('list');
                        Swal.fire({ icon: 'success', title: 'Order Completed!', text: 'Order has been processed successfully.'});
                    }
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
            const merchandiseTotal = document.getElementById('merchandise-total');
            const discountInput = document.getElementById('discount-amount');
            const taxInput = document.getElementById('tax-amount');

            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="text-muted text-center py-3">No items in cart</div>';
                totalAmount.textContent = '₱0.00';
                if (merchandiseTotal) merchandiseTotal.textContent = '₱0.00';
                if (discountInput) discountInput.value = '0.00';
                if (taxInput) taxInput.value = '0.00';
                const displayMerchandise = document.getElementById('display-merchandise');
                const displayDiscount = document.getElementById('display-discount');
                const displayTax = document.getElementById('display-tax');
                const displayDiscountLabel = document.getElementById('display-discount-label');
                const displayTaxLabel = document.getElementById('display-tax-label');
                if (displayMerchandise) displayMerchandise.textContent = '₱0.00';
                if (displayDiscount) displayDiscount.textContent = '₱0.00';
                if (displayTax) displayTax.textContent = '₱0.00';
                if (displayDiscountLabel) displayDiscountLabel.textContent = 'Discount:';
                if (displayTaxLabel) displayTaxLabel.textContent = 'Tax:';
                return;
            }

            let total = 0;
            cartItems.innerHTML = cart.map(item => {
                const entries = Array.isArray(item.entries) ? item.entries : [];
                const itemTotal = item.price * entries.length;
                total += itemTotal;

                const entriesHtml = entries.map((entry, index) => `
                    <div class="entry-item border rounded p-2 mb-2 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">Unit ${index + 1}</span>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeEntry('${item.cartIdentifier}', ${index})" title="Remove this unit">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label mb-1 small">Serial Number</label>
                                <input type="text" class="form-control form-control-sm" placeholder="${entry.in_stock ? 'Enter serial' : 'Not required (out of stock)'}"
                                    value="${entry.serial_number || ''}"
                                    onchange="setSerial('${item.cartIdentifier}', ${index}, this.value)"
                                    ${entry.in_stock ? '' : 'disabled'}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label mb-1 small">Warranty (months)</label>
                                <input type="number" min="0" class="form-control form-control-sm" placeholder="0"
                                    value="${entry.warranty_months || 0}"
                                    onchange="setWarrantyMonths('${item.cartIdentifier}', ${index}, this.value)">
                            </div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="warranty_activate_${item.cartIdentifier}_${index}"
                                        onchange="toggleWarrantyActivation('${item.cartIdentifier}', ${index}, this.checked)"
                                        ${entry.warranty_activated ? 'checked' : ''}>
                                    <label class="form-check-label small" for="warranty_activate_${item.cartIdentifier}_${index}">
                                        <i class="fas fa-shield-alt me-1"></i>Activate Warranty Coverage
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <label class="form-label mb-1 small fw-bold">Subtotal:</label>
                                <h5 class="text-primary mb-0">₱${item.price.toFixed(2)}</h5>
                            </div>
                        </div>
                    </div>
                `).join('');

                return `
                    <div class="cart-item mb-3 p-3 border rounded bg-white">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="fw-bold">${item.name}</div>
                                <div class="text-muted small">${item.branchName} - ₱${item.price.toFixed(2)} × ${entries.length}</div>
                            </div>
                            <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart('${item.cartIdentifier}')" title="Remove product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        ${entriesHtml}
                        <div class="border-top pt-2 text-end">
                            <strong>Subtotal: ₱${itemTotal.toFixed(2)}</strong>
                        </div>
                    </div>
                `;
            }).join('');

            const discountValue = discountInput ? discountInput.value.trim() : '0';
            const taxValue = taxInput ? taxInput.value.trim() : '0';
            const discount = parseAmountOrPercentage(discountValue, total);
            const tax = parseAmountOrPercentage(taxValue, total);
            const finalTotal = total - discount + tax;

            if (merchandiseTotal) merchandiseTotal.textContent = `₱${total.toFixed(2)}`;
            totalAmount.textContent = `₱${finalTotal.toFixed(2)}`;

            const displayMerchandise = document.getElementById('display-merchandise');
            const displayDiscountLabel = document.getElementById('display-discount-label');
            const displayDiscount = document.getElementById('display-discount');
            const displayTaxLabel = document.getElementById('display-tax-label');
            const displayTax = document.getElementById('display-tax');

            if (displayMerchandise) displayMerchandise.textContent = `₱${total.toFixed(2)}`;
            if (displayDiscountLabel) displayDiscountLabel.textContent = discountValue.includes('%') ? `Discount (${parseFloat(discountValue)}%):` : 'Discount:';
            if (displayDiscount) displayDiscount.textContent = `₱${discount.toFixed(2)}`;
            if (displayTaxLabel) displayTaxLabel.textContent = taxValue.includes('%') ? `Tax (${parseFloat(taxValue)}%):` : 'Tax:';
            if (displayTax) displayTax.textContent = `₱${tax.toFixed(2)}`;
        }

        function parseAmountOrPercentage(value, baseAmount) {
            if (!value) return 0;
            if (String(value).includes('%')) {
                return baseAmount * ((parseFloat(value) || 0) / 100);
            }
            return parseFloat(value) || 0;
        }

        window.toggleWarrantyActivation = function(cartIdentifier, entryIndex, isChecked) {
            const item = cart.find(i => i.cartIdentifier === cartIdentifier);
            if (!item || !item.entries || !item.entries[entryIndex]) return;
            item.entries[entryIndex].warranty_activated = isChecked;
            if (isChecked && !item.entries[entryIndex].warranty_months) {
                item.entries[entryIndex].warranty_months = 12;
                updateCartDisplay();
            }
        };

        function initPaymentMethodUI() {
            const paymentSelect = document.getElementById('payment_method');
            const creditDetails = document.getElementById('credit-details');
            const dueDateInput = document.getElementById('credit_due_date');

            if (dueDateInput && !dueDateInput.value) {
                dueDateInput.value = new Date().toISOString().split('T')[0];
                dueDateInput.min = new Date().toISOString().split('T')[0];
            }

            if (paymentSelect) {
                paymentSelect.addEventListener('change', function() {
                    if (!creditDetails) return;
                    creditDetails.style.display = this.value === 'credit' ? 'block' : 'none';
                });
            }

            const discountInput = document.getElementById('discount-amount');
            const taxInput = document.getElementById('tax-amount');
            if (discountInput) discountInput.addEventListener('input', updateCartDisplay);
            if (taxInput) taxInput.addEventListener('input', updateCartDisplay);
        }

        // Initialize
        search('list');

        btn.addEventListener('click', () => search('list'));
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                search('list');
            }
        });

        initPaymentMethodUI();
        });
    })();
    </script>
</body>
</html>
