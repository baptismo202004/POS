<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS System - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;            --warning-color: #f59e0b;
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

        .products-section {
            /* Remove scroll from here */
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
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
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

        .order-summary {
            position: sticky;
            top: 20px;
        }

        .order-card {
            border-radius: 15px;
            border: none;
            box-shadow: var(--card-shadow);
            background: linear-gradient(135deg, #ffffff, #f8fafc);
        }

        .order-header {
            background: linear-gradient(135deg, var(--secondary-color), #475569);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
            font-weight: 600;
        }

        .order-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .order-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .total-section {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .checkout-btn {
            background: linear-gradient(135deg, var(--success-color), #059669);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .checkout-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }

        .clear-btn {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .clear-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.3);
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

        .badge-stock {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
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

        .remove-btn {
            background: none;
            border: none;
            color: var(--danger-color);
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="header-title mb-2">
                    <i class="fas fa-cash-register me-3"></i>BGH POS
                </h1>
                <p class="text-muted mb-0">Fast sales processing with barcode scanning support</p>
            </div>
            <div class="d-flex gap-2">
                @if(($branchType ?? 'grocery') !== 'grocery')
                <a href="{{ route('pos.electronics.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plug me-2"></i>Electronics POS
                </a>
                @endif
                <a href="{{ route('admin.sales.management.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Search and Products -->
            <div class="col-lg-8">
                <!-- Search Section -->
                <div class="search-section">
                    <h4 class="mb-3">
                        <i class="fas fa-search me-2"></i>Product Search
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
                                        <th>Branches</th>
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

            <!-- Right Column - Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary">
                    <div class="card order-card">
                        <div class="order-header">
                        </div>
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div id="order-items" class="mb-3" style="max-height: 400px; overflow-y: auto;">
                                        <div class="text-muted text-center py-3">No items in cart</div>
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
                                        
                                        <!-- Credit details (shown only when credit is selected) -->
                                        <div id="credit-details" class="mb-3" style="display: none;">
                                            <div class="card border-warning">
                                                <div class="card-body bg-light">
                                                    <h6 class="card-title text-warning mb-3">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Credit Details
                                                    </h6>
                                                    <div class="mb-2">
                                                        <label class="form-label">Customer Name: <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="customer_name" placeholder="Enter customer name" required>
                                                        <div class="invalid-feedback">Customer name is required for credit payments.</div>
                                                    </div>
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
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">Total:</h5>
                                            <h4 class="mb-0 text-primary" id="total-amount">₱0.00</h4>
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    (function(){
        const input = document.getElementById('search-input');
        const btn = document.getElementById('search-btn');
        const tableBody = document.querySelector('#results-table tbody');
        const resultsCount = document.getElementById('results-count');

        if (!input || !btn || !tableBody || !resultsCount) {
            console.error('Missing elements! Input: ' + !!input + ', Btn: ' + !!btn + ', Table: ' + !!tableBody + ', Count: ' + !!resultsCount);
            return;
        }

        console.log('All elements found!'); // Debug

        async function search(mode='list'){
            const keyword = input.value.trim();
            
            // Always show products, even if keyword is empty
            if(!keyword){ 
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading all products...</span>
                            </div>
                        </td>
                    </tr>`;
                resultsCount.textContent = 'Loading...';
            } else {
                // Show loading state for search
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Searching...</span>
                            </div>
                        </td>
                    </tr>`;
                resultsCount.textContent = 'Searching...';
            }

            const url = "{{ route('pos.lookup') }}";
            const form = new FormData();
            form.set('barcode', keyword);
            form.set('mode', mode);
            
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

                console.log('API Response:', data); // Debugging

                const items = data.items || (data.error ? [] : [data]);
                resultsCount.textContent = `${items.length} products`;
                
                console.log('Items:', items); // Debugging
                
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

                tableBody.innerHTML = items.map(it => {
                    const safeName = String((it && (it.name || it.product_name)) || '');
                    const safeProductId = (it && (it.product_id != null ? it.product_id : it.id)) || 0;
                    const branchesHtml = (it.branches && it.branches.length > 0) ? it.branches.map((b, index) => {
                        const units = Array.isArray(b.stock_units) ? b.stock_units : [];
                        const unitsBadgesHtml = units.length > 0
                            ? units.map(u => {
                                const unitName = (u && u.unit_name) ? u.unit_name : '';
                                const unitStock = (u && u.stock != null) ? u.stock : 0;
                                return `<span class="badge bg-light text-dark border me-1">${unitStock}${unitName ? ' ' + unitName : ''}</span>`;
                            }).join('')
                            : '';

                        const unitsSelectHtml = units.length > 0
                            ? `
                                <select class="form-select form-select-sm mt-1 js-unit-select" data-product-id="${it.product_id}" data-branch-id="${b.branch_id}">
                                    ${units.map((u, uidx) => {
                                        const unitName = (u && u.unit_name) ? u.unit_name : '';
                                        const unitStock = (u && u.stock != null) ? u.stock : 0;
                                        const unitPrice = (u && u.price != null) ? u.price : 0;
                                        const unitTypeId = (u && u.unit_type_id != null) ? u.unit_type_id : '';
                                        return `<option value="${unitTypeId}" data-stock="${unitStock}" data-price="${Number(unitPrice).toFixed(2)}" data-unit-name="${unitName}" ${uidx === 0 ? 'selected' : ''}>${unitName || 'Unit'} - ₱${Number(unitPrice).toFixed(2)}</option>`;
                                    }).join('')}
                                </select>
                            `
                            : '';

                        return `
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="branch_${it.product_id}" id="branch_${it.product_id}_${b.branch_id}" value="${b.branch_id}" data-stock="${b.stock}" data-branch-name="${b.branch_name || ('Branch #' + b.branch_id)}" data-price="${(b.price || 0).toFixed(2)}" ${index === 0 ? 'checked' : ''}>
                                <label class="form-check-label" for="branch_${it.product_id}_${b.branch_id}">
                                    ${b.branch_name || ('Branch #' + b.branch_id)} <span class="badge bg-secondary">${b.stock}</span>
                                    ${unitsBadgesHtml ? `<div class="mt-1">${unitsBadgesHtml}</div>` : ''}
                                    ${unitsSelectHtml ? `<div class="mt-1">${unitsSelectHtml}</div>` : ''}
                                </label>
                            </div>
                        `;
                    }).join('') : '<span class="text-muted">No stock</span>';

                    const canBeAdded = it.branches && it.branches.length > 0 && it.total_stock > 0;

                    return `
                    <tr class="animate-in">
                        <td>
                            <div class="fw-semibold">${safeName || it.name || ''}</div>
                            ${it.model_number ? `<small class="text-muted">${it.model_number}</small>` : ''}
                        </td>
                        <td><code>${it.barcode||'N/A'}</code></td>
                        <td class="text-end">
                            <span class="badge ${it.total_stock > 10 ? 'bg-success' : 'bg-warning'}">
                                ${it.total_stock ?? 0}
                            </span>
                        </td>
                        <td>${branchesHtml}</td>
                        <td class="text-end price-display" data-product-id="${safeProductId}">₱${(it.price||0).toFixed(2)}</td>
                        <td class="text-end">
                            <button class="btn add-btn" onclick="addToOrder(this, ${safeProductId}, '${safeName.replace(/'/g, "\\'")}')" ${!canBeAdded ? 'disabled' : ''}>
                                <i class="fas fa-plus me-1"></i>Add
                            </button>
                        </td>
                    </tr>`;
                }).join('');

                // After rendering rows, attach listeners to branch radios to update price display per product
                items.forEach(it => {
                    const safeProductId = (it && (it.product_id != null ? it.product_id : it.id)) || 0;
                    const radios = document.querySelectorAll(`input[name="branch_${safeProductId}"]`);
                    radios.forEach(r => {
                        r.addEventListener('change', () => {
                            updateProductPriceDisplay(safeProductId);
                        });
                    });

                    const unitSelects = document.querySelectorAll(`select.js-unit-select[data-product-id="${safeProductId}"]`);
                    unitSelects.forEach(sel => {
                        sel.addEventListener('change', () => {
                            updateProductPriceDisplay(safeProductId);
                        });
                    });

                    // Ensure initial price matches the initially checked branch
                    updateProductPriceDisplay(safeProductId);
                });

            } catch (error) {
                console.error('Search error:', error);
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p class="mb-0">Search failed. Please try again.</p>
                        </td>
                    </tr>`;
            }
        }

        // Shopping cart functionality
        let cart = [];

        function updateProductPriceDisplay(productId) {
            const selectedBranchRadio = document.querySelector(`input[name="branch_${productId}"]:checked`);
            const priceCell = document.querySelector(`td.price-display[data-product-id="${productId}"]`);

            if (!priceCell || !selectedBranchRadio) return;

            const branchId = selectedBranchRadio.value;
            const unitSelect = document.querySelector(`select.js-unit-select[data-product-id="${productId}"][data-branch-id="${branchId}"]`);

            if (unitSelect && unitSelect.value) {
                const opt = unitSelect.options[unitSelect.selectedIndex];
                const unitPrice = parseFloat(opt.dataset.price || '0');
                priceCell.textContent = `₱${unitPrice.toFixed(2)}`;
                return;
            }

            const branchPrice = parseFloat(selectedBranchRadio.dataset.price || '0');
            priceCell.textContent = `₱${branchPrice.toFixed(2)}`;
        }
        
        // Make functions globally accessible for onclick handlers
        window.addToOrder = function(button, productId, name) {
            const selectedBranchRadio = document.querySelector(`input[name="branch_${productId}"]:checked`);
            
            if (!selectedBranchRadio) {
                Swal.fire('Error', 'Please select a branch.', 'error');
                return;
            }
            
            const branchId = parseInt(selectedBranchRadio.value);
            const branchName = selectedBranchRadio.dataset.branchName || selectedBranchRadio.labels[0].innerText.trim();

            const unitSelect = document.querySelector(`select.js-unit-select[data-product-id="${productId}"][data-branch-id="${branchId}"]`);
            let unitTypeId = null;
            let unitName = null;
            let stock = parseFloat(selectedBranchRadio.dataset.stock || '0');
            let price = parseFloat(selectedBranchRadio.dataset.price || '0');

            if (unitSelect && unitSelect.value) {
                const opt = unitSelect.options[unitSelect.selectedIndex];
                unitTypeId = parseInt(unitSelect.value);
                unitName = opt.dataset.unitName || opt.dataset.unit_name || opt.textContent;
                stock = parseFloat(opt.dataset.stock || '0');
                price = parseFloat(opt.dataset.price || '0');
            }

            if (stock <= 0) {
                Swal.fire('Out of Stock', `This product is out of stock at ${branchName}.`, 'warning');
                return;
            }

            const cartIdentifier = `${productId}-${branchId}-${unitTypeId || 0}`;
            const existingItem = cart.find(item => item.cartIdentifier === cartIdentifier);
            
            if (existingItem) {
                if (existingItem.quantity >= stock) {
                    Swal.fire('Stock Limit', `Cannot add more. Only ${stock} available at ${branchName}.`, 'warning');
                    return;
                }
                existingItem.quantity++;
            } else {
                cart.push({
                    cartIdentifier: cartIdentifier,
                    product_id: productId,
                    branch_id: branchId,
                    unit_type_id: unitTypeId,
                    unit_name: unitName,
                    name: name,
                    branchName: branchName,
                    price: price,
                    quantity: 1,
                    stock: stock
                });
            }
            
            updateCartDisplay();
            showNotification(`${name} (${branchName}) added to cart!`, 'success');
        };
        
        function getQtyStepForItem(item) {
            const unitName = String(item && item.unit_name ? item.unit_name : '');
            if (/kilogram|\bkg\b/i.test(unitName)) {
                return 0.01;
            }
            return 1;
        }

        function normalizeQty(n) {
            const num = parseFloat(n);
            if (!isFinite(num)) return 0;
            return Math.round(num * 1000000) / 1000000;
        }

        function setCartItemQuantity(cartIdentifier, rawQty) {
            const item = cart.find(item => item.cartIdentifier === cartIdentifier);
            if (!item) return;

            const newQuantity = normalizeQty(rawQty);

            if (newQuantity <= 0) {
                removeFromCart(cartIdentifier);
                return;
            }

            if (newQuantity > item.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock Limit Reached',
                    text: `Cannot add more. Only ${item.stock} available in stock.`,
                    confirmButtonColor: '#2563eb',
                    timer: 3000,
                    timerProgressBar: true
                });
                updateCartDisplay();
                return;
            }

            item.quantity = newQuantity;
            updateCartDisplay();
        }

        window.setQuantity = function(cartIdentifier, qty) {
            return setCartItemQuantity(cartIdentifier, qty);
        };

        window.updateQuantity = function(cartIdentifier, change) {
            const item = cart.find(item => item.cartIdentifier === cartIdentifier);
            if (!item) return;

            const step = getQtyStepForItem(item);
            const current = normalizeQty(item.quantity);
            const newQuantity = normalizeQty(current + (step * change));

            return setCartItemQuantity(cartIdentifier, newQuantity);
        };
        
        window.removeFromCart = function(cartIdentifier) {
            const itemIndex = cart.findIndex(item => item.cartIdentifier === cartIdentifier);
            if (itemIndex > -1) {
                const item = cart[itemIndex];
                cart.splice(itemIndex, 1);
                updateCartDisplay();
                showNotification(`${item.name} removed from cart`, 'info');
            }
        };
        
        window.clearCart = function() {
            if (cart.length === 0) return;
            
            Swal.fire({
                title: 'Clear Entire Cart?',
                text: "Are you sure you want to remove all items from the cart?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    updateCartDisplay();
                    Swal.fire({
                        icon: 'success',
                        title: 'Cart Cleared',
                        text: 'All items have been removed from your cart.',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            });
        };
        
        window.checkout = function() {
            if (cart.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Cart is Empty',
                    text: 'Please add items to your cart before checkout.',
                    confirmButtonColor: '#2563eb',
                    timer: 3000,
                    timerProgressBar: true
                });
                return;
            }
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const itemsList = cart.map(item => `${item.name} x${item.quantity} = ₱${(item.price * item.quantity).toFixed(2)}`).join('\n');

            // Validate customer name for credit payments
            const paymentMethodSelected = document.querySelector('input[name="payment_method"]:checked').value;
            if (paymentMethodSelected === 'credit') {
                const customerNameVal = document.getElementById('customer_name').value.trim();
                if (!customerNameVal) {
                    const nameInput = document.getElementById('customer_name');
                    nameInput.classList.add('is-invalid');
                    nameInput.focus();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Customer Name Required',
                        text: 'Please enter the customer name for credit payments.',
                        confirmButtonColor: '#2563eb'
                    });
                    return;
                }
                document.getElementById('customer_name').classList.remove('is-invalid');
            }
            
            Swal.fire({
                title: 'Process Order?',
                html: `
                    <div style="text-align: left; font-family: monospace;">
                        ${itemsList.split('\n').map(item => `<div>${item}</div>`).join('')}
                        <hr>
                        <div style="font-weight: bold; color: #2563eb;">Total: ₱${total.toFixed(2)}</div>
                        <br>
                        <div style="color: #ef4444; font-size: 12px;">
                            <i class="fas fa-info-circle"></i> This will deduct stock and record the sale
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Process Order',
                cancelButtonText: 'Cancel',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
                    if (paymentMethod === 'cash') {
                        Swal.fire({
                            title: 'Cash Payment',
                            html: `
                                <div class="text-start">
                                    <div class="mb-2"><strong>Total:</strong> ₱${total.toFixed(2)}</div>
                                    <label class="form-label">Cash Tendered</label>
                                    <input id="amount_paid" type="number" min="${total.toFixed(2)}" step="0.01" class="form-control" placeholder="Enter cash amount">
                                    <div class="mt-2" id="change_display" style="font-weight:700; color:#2563eb;">Change: ₱0.00</div>
                                </div>
                            `,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Confirm & Process',
                            didOpen: () => {
                                const input = document.getElementById('amount_paid');
                                const changeEl = document.getElementById('change_display');
                                const compute = () => {
                                    const paid = parseFloat(input.value || '0') || 0;
                                    const change = paid - total;
                                    changeEl.textContent = `Change: ₱${(change > 0 ? change : 0).toFixed(2)}`;
                                };
                                input.addEventListener('input', compute);
                                input.focus();
                            },
                            preConfirm: () => {
                                const paid = parseFloat(document.getElementById('amount_paid').value || '0') || 0;
                                if (paid < total) {
                                    Swal.showValidationMessage('Cash tendered is less than total.');
                                    return false;
                                }
                                return { paid };
                            }
                        }).then((cashResult) => {
                            if (cashResult.isConfirmed) {
                                processOrder(cashResult.value.paid);
                            }
                        });
                    } else {
                        processOrder(null);
                    }
                }
            });
        };
        
        function processOrder(cashTendered) {
            // Show loading
            Swal.fire({
                title: 'Processing Order...',
                text: 'Please wait while we process your order.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send order to server
            fetch('{{ route("pos.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    items: cart.map(item => ({
                        product_id: item.product_id,
                        branch_id: item.branch_id,
                        unit_type_id: item.unit_type_id,
                        unit_name: item.unit_name,
                        name: item.name,
                        quantity: item.quantity,
                        price: item.price
                    })),
                    total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
                    payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                    customer_name: document.getElementById('customer_name').value,
                    credit_due_date: document.getElementById('credit_due_date').value,
                    credit_notes: document.getElementById('credit_notes').value,
                    cash_tendered: cashTendered,
                    change_due: cashTendered !== null ? Math.max(0, cashTendered - cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)) : null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const receiptUrl = data.receipt_url;
                    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

                    // Clear customer name field
                    document.getElementById('customer_name').value = '';
                    
                    // Check if this is a cash payment and auto-receipt is enabled
                    if (data.auto_receipt && receiptUrl) {
                        cart = [];
                        updateCartDisplay();
                        search('list');
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Completed!',
                            text: 'Opening receipt...',
                            confirmButtonColor: '#10b981',
                            timer: 1500,
                            timerProgressBar: true
                        });
                        window.open(receiptUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
                    } else if (data.redirect_url) {
                        // Credit payment — redirect to credits management
                        cart = [];
                        updateCartDisplay();
                        Swal.fire({
                            icon: 'success',
                            title: 'Credit Sale Recorded!',
                            text: data.message || 'Redirecting to credits...',
                            confirmButtonColor: '#10b981',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            window.location.href = data.redirect_url;
                        });
                    } else {
                        cart = [];
                        updateCartDisplay();

                        // Regular success message for credit payments
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Completed!',
                            text: 'Order has been processed successfully.',
                            confirmButtonColor: '#10b981',
                            timer: 3000,
                            timerProgressBar: true
                        });

                        // Refresh products to show updated stock
                        search('list');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Order Failed',
                        text: data.message || 'There was an error processing your order.',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                console.error('Order processing error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Order Failed',
                    text: 'Network error. Please try again.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
        
        function updateCartDisplay() {
            const cartItems = document.getElementById('order-items');
            const totalAmount = document.getElementById('total-amount');
            
            console.log('updateCartDisplay called, cart:', cart); // Debug
            console.log('Cart elements found:', !!cartItems, !!totalAmount); // Debug
            
            if (!cartItems || !totalAmount) {
                console.error('Cart elements not found!');
                return;
            }
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<div class="text-muted text-center py-3">No items in cart</div>';
                totalAmount.textContent = '₱0.00';
                return;
            }
            
            let total = 0;
            cartItems.innerHTML = cart.map(item => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;
                const unitLabel = item.unit_name ? ` ${item.unit_name}` : '';
                const step = getQtyStepForItem(item);
                const qtyVal = normalizeQty(item.quantity);
                
                return `
                    <div class="cart-item d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">${item.name}</div>
                            <div class="text-muted small">${item.branchName} - ₱${item.price.toFixed(2)} x ${item.quantity}${unitLabel}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity('${item.cartIdentifier}', -1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input
                                type="number"
                                class="form-control form-control-sm mx-2"
                                style="width: 90px;"
                                value="${qtyVal}"
                                min="0"
                                step="${step}"
                                inputmode="decimal"
                                onchange="setQuantity('${item.cartIdentifier}', this.value)"
                            >
                            <button class="btn btn-sm btn-outline-success me-2" onclick="updateQuantity('${item.cartIdentifier}', 1)">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.cartIdentifier}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            <div class="ms-3 text-end">
                                <strong>₱${itemTotal.toFixed(2)}</strong>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            totalAmount.textContent = `₱${total.toFixed(2)}`;
        }

        function updateQuantity(cartIdentifier, change) {
            return window.updateQuantity(cartIdentifier, change);
        }

        function removeFromCart(cartIdentifier) {
            return window.removeFromCart(cartIdentifier);
        }

        function clearCart() {
            return window.clearCart();
        }
        
        // Payment method toggle
        document.addEventListener('DOMContentLoaded', function() {
            const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
            const creditDetails = document.getElementById('credit-details');
            const dueDateInput = document.getElementById('credit_due_date');
            
            // Set default due date to today
            const defaultDueDate = new Date();
            dueDateInput.value = defaultDueDate.toISOString().split('T')[0];
            dueDateInput.min = new Date().toISOString().split('T')[0]; // Today
            
            paymentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'credit') {
                        creditDetails.style.display = 'block';
                    } else {
                        creditDetails.style.display = 'none';
                    }
                });
            });
        });
        
        
        
        function showNotification(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            const icon = type === 'success' ? 'success' : 'info';
            const color = type === 'success' ? '#10b981' : '#3b82f6';
            
            Toast.fire({
                icon: icon,
                title: message,
                background: '#fff',
                color: '#333'
            });
        }

        // Event listeners
        btn.addEventListener('click', () => {
            console.log('Search button clicked!'); // Debug
            search('list');
        });

        // Auto-load all products when page loads
        window.addEventListener('load', () => {
            console.log('Auto-loading all products...'); // Debug
            search('list');
        });

        // Also search when user types (live search)
        input.addEventListener('input', () => {
            search('list');
        });

        // Focus on search input when page loads
        input.focus();
    })();
    </script>
</body>
</html>