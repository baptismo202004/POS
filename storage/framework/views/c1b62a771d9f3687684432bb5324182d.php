<?php $__env->startSection('title', 'New Sale'); ?>

<?php $__env->startPush('stylesDashboard'); ?>
<style>
    /* Full-width POS for cashier */
    aside.sidebar-fixed {
        display: none !important;
    }
    main.main-content {
        margin-left: 0 !important;
    }

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

    .header-title {
        background: linear-gradient(135deg, var(--primary-color), #1e40af);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .search-section {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(255,255,255,0.1);
        transition: all 0.3s ease;
    }

    .search-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: searchPulse 3s ease-in-out infinite;
    }

    @keyframes searchPulse {
        0%, 100% {
            opacity: 0.1;
            transform: scale(1);
        }
        50% {
            opacity: 0.3;
            transform: scale(1.1);
        }
    }

    .search-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        border-color: rgba(255,255,255,0.2);
    }

    .search-btn {
        background: linear-gradient(135deg, #0f8504ff 0%, #16c606ff 100%);
        border: none;
        border-radius: 0 var(--border-radius-lg) var(--border-radius-lg) 0 0;
        padding: 15px 25px;
        color: var(--white);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
    }

    .search-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3) 50%, transparent);
        transition: all 0.5s ease;
    }

    .search-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
        background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
    }

    .search-btn:hover::before {
        left: 100%;
    }

    .search-btn::after {
        content: '';
        position: absolute;
        top: 50%;
        right: -50%;
        width: 6px;
        height: 6px;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        transform: translateY(-50%);
        transition: all 0.3s ease;
    }

    .search-btn:active {
        transform: scale(0.95);
        box-shadow: 0 2px 8px rgba(79, 172, 254, 0.5);
    }

    .search-input {
        border: 2px solid rgba(255,255,255,0.3);
        background: rgba(255,255,255,0.9);
        padding: 15px 50px 15px 20px;
        font-size: 16px;
        font-weight: 500;
        color: #333;
        outline: none;
        transition: all 0.3s ease;
        flex: 1;
        position: relative;
        backdrop-filter: blur(10px);
    }

    .search-input:focus {
        background: rgba(255,255,255,0.95);
        box-shadow: 0 0 20px rgba(255,255,255,0.5);
        border-color: rgba(255,255,255,0.8);
        animation: searchGlow 0.3s ease-in-out infinite alternate;
    }

    @keyframes searchGlow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(255,255,255,0.5);
        }
        50% {
            box-shadow: 0 0 30px rgba(255,255,255,0.8);
        }
    }

    .product-card {
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-hover-shadow);
    }

    .product-image {
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }

    .product-name {
        font-weight: 600;
        color: var(--primary-color);
        margin: 10px 0 5px 0;
    }

    .product-price {
        font-size: 18px;
        font-weight: bold;
        color: var(--success-color);
    }

    .product-stock {
        font-size: 14px;
        color: var(--secondary-color);
    }

    .btn-add-product {
        background: var(--primary-color);
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-add-product:hover {
        background: #1d4ed8;
        transform: scale(1.05);
    }

    .barcode-input {
        font-size: 18px;
        border: 2px solid var(--primary-color);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }

    .sale-items {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: var(--card-shadow);
    }

    .summary-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: var(--card-shadow);
        position: sticky;
        top: 20px;
    }

    .total-amount {
        font-size: 24px;
        font-weight: bold;
        color: var(--primary-color);
    }

    .btn-process {
        background: var(--success-color);
        border: none;
        border-radius: 10px;
        padding: 15px 30px;
        font-size: 16px;
        font-weight: bold;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-process:hover {
        background: #059669;
        transform: scale(1.02);
    }

        border-radius: 10px;
        padding: 15px 30px;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-clear:hover {
        background: #475569;
    }

    .table-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }

    .table-container thead {
        position: sticky;
        top: 0;
        background: white;
        z-index: 10;
    }

    .table-container thead th {
        border-bottom: 2px solid #e5e7eb;
        background: #f8f9fa;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .search-result-item {
        padding: 10px 15px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .item-row {
        transition: background-color 0.2s ease;
    }

    .item-row:hover {
        background-color: #f8f9fa;
    }

    @media (max-width: 768px) {
        .main-container {
            margin: 10px;
            padding: 15px;
        }

        .header-title {
            font-size: 24px;
        }

        .product-image {
            height: 80px;
        }

        .btn-process, .btn-clear {
            padding: 12px 20px;
            font-size: 14px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="header-title mb-2">
                <i class="fas fa-cash-register me-3"></i>Cashier POS
            </h1>
            <p class="text-muted mb-0">Fast sales processing with barcode scanning support</p>
        </div>
        <div>
            <a href="<?php echo e(route('cashier.sales.index')); ?>" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Sales
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
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
                                    <th class="text-end">Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="empty-state">
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

        <div class="col-lg-4">
            <div class="order-summary">
                <div class="card order-card">
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
                                            <label class="form-label">Customer Name:</label>
                                            <input type="text" class="form-control" id="customer_name" placeholder="Enter customer name (optional)">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Date:</label>
                                            <input type="date" class="form-control" id="credit_due_date" value="<?php echo e(date('Y-m-d')); ?>">
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
                                <button type="button" class="btn btn-success btn-lg" onclick="checkout()">
                                    <i class="fas fa-credit-card me-2"></i>Checkout
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="clearCart()">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<script>
// Global variables and functions
let saleData = {
    items: [],
    subtotal: 0,
    vat: 0,
    total: 0
};

let products = [];

// Global DOM element references
let input, btn, tableBody, resultsCount, orderItems, totalAmount, creditDetails, paymentCash, paymentCredit;

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = saleData.items.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        saleData.items.push({
            id: product.id,
            name: product.product_name,
            price: parseFloat(product.selling_price),
            quantity: 1
        });
    }
    
    updateOrderDisplay();
}

function updateOrderDisplay() {
    if (saleData.items.length === 0) {
        orderItems.innerHTML = '<div class="text-muted text-center py-3">No items in cart</div>';
        totalAmount.textContent = '₱0.00';
        return;
    }

    orderItems.innerHTML = saleData.items.map(item => `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="fw-bold">${item.name}</div>
                <small class="text-muted">₱${item.price.toFixed(2)} × ${item.quantity}</small>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                <span class="mx-2">${item.quantity}</span>
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');

    const subtotal = saleData.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const total = subtotal; // No VAT added
    
    saleData.subtotal = subtotal;
    saleData.vat = 0;
    saleData.total = total;
    
    totalAmount.textContent = `₱${total.toFixed(2)}`;
}

function updateQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    const item = saleData.items.find(item => item.id === productId);
    if (item) {
        item.quantity = newQuantity;
        updateOrderDisplay();
    }
}

function removeFromCart(productId) {
    saleData.items = saleData.items.filter(item => item.id !== productId);
    updateOrderDisplay();
}

function clearCart() {
    saleData.items = [];
    updateOrderDisplay();
}

function checkout() {
    if (saleData.items.length === 0) {
        alert('Please add items to cart first');
        return;
    }

    // Show loading state
    const checkoutBtn = document.querySelector('button[onclick="checkout()"]');
    const originalText = checkoutBtn.innerHTML;
    checkoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    checkoutBtn.disabled = true;

    const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
    
    // Create form data
    const formData = new FormData();
    
    // Add payment method
    formData.append('payment_method', selectedPayment);
    
    // Add credit details if selected
    if (selectedPayment === 'credit') {
        formData.append('customer_name', document.getElementById('customer_name').value);
        formData.append('credit_due_date', document.getElementById('credit_due_date').value);
        formData.append('credit_notes', document.getElementById('credit_notes').value);
    }

    // Add items
    saleData.items.forEach((item, index) => {
        formData.append(`products[${index}][id]`, item.id);
        formData.append(`products[${index}][quantity]`, item.quantity);
        formData.append(`products[${index}][price]`, item.price);
    });

    // Calculate totals
    const subtotal = saleData.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const total = subtotal; // No VAT added
    
    console.log('Checkout calculation:', {
        subtotal: subtotal,
        total: total,
        items: saleData.items
    });
    
    formData.append('subtotal', subtotal);
    formData.append('vat', 0);
    formData.append('total', total);

    // Submit via fetch for better control
    fetch('<?php echo e(route("cashier.pos.store")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear cart
            saleData.items = [];
            updateOrderDisplay();
            
            // Show success and redirect to receipt if cash payment
            if (data.auto_receipt && data.receipt_url) {
                // Redirect to receipt
                window.location.href = data.receipt_url;
            } else {
                // For credit payments, show success and reload
                alert('Sale completed successfully!');
                window.location.reload();
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Checkout error:', error);
        alert('An error occurred while processing the sale. Please try again.');
    })
    .finally(() => {
        // Restore button state
        checkoutBtn.innerHTML = originalText;
        checkoutBtn.disabled = false;
    });
}

function displayProducts(productsList) {
    if (productsList.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-search mb-2"></i>
                        <p class="mb-0">No products found</p>
                        <small>Try adjusting your search criteria</small>
                    </div>
                </td>
            </tr>`;
        return;
    }

    tableBody.innerHTML = productsList.map(product => `
        <tr class="animate-in">
            <td>
                <div class="d-flex align-items-center">
                    ${product.image ? `<img src="/storage/${product.image}" alt="${product.product_name}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">` : '<i class="fas fa-box me-2 text-muted"></i>'}
                    <div>
                        <div class="fw-bold">${product.product_name}</div>
                    </div>
                </div>
            </td>
            <td>${product.barcode || 'N/A'}</td>
            <td class="text-end">
                <span class="badge ${product.total_stock > 10 ? 'bg-success' : 'bg-warning'}">
                    ${product.total_stock ?? 0}
                </span>
            </td>
            <td class="text-end">
                <span class="price-display">₱${parseFloat(product.selling_price).toFixed(2)}</span>
            </td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="addToCart(${product.id})">
                    <i class="fas fa-plus me-1"></i>Add
                </button>
            </td>
        </tr>
    `).join('');
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM element references
    input = document.getElementById('search-input');
    btn = document.getElementById('search-btn');
    tableBody = document.querySelector('#results-table tbody');
    resultsCount = document.getElementById('results-count');
    orderItems = document.getElementById('order-items');
    totalAmount = document.getElementById('total-amount');
    creditDetails = document.getElementById('credit-details');
    paymentCash = document.getElementById('payment_cash');
    paymentCredit = document.getElementById('payment_credit');

    if (!input || !btn || !tableBody || !resultsCount) {
        console.error('Required elements not found');
        return;
    }

    async function search(mode = 'search', keyword = '') {
        if (mode === 'list') {
            // Show loading state
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading all products...</span>
                        </div>
                    </td>
                </tr>`;
            resultsCount.textContent = 'Loading...';
            
            // Load all products
            try {
                const response = await fetch('<?php echo e(route("cashier.pos.lookup")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ mode: 'list' })
                });

                const data = await response.json();
                
                if (data.success) {
                    products = data.products;
                    displayProducts(products);
                    resultsCount.textContent = `${products.length} results`;
                } else {
                    throw new Error(data.message || 'Failed to load products');
                }
            } catch (error) {
                console.error('Search error:', error);
                displayProducts([]);
                resultsCount.textContent = '0 results';
            }
            return;
        }

        // Show loading state for search
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                </td>
            </tr>`;
        resultsCount.textContent = 'Searching...';

        try {
            const response = await fetch('<?php echo e(route("cashier.pos.lookup")); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ mode: 'search', keyword: keyword })
            });

            const data = await response.json();
            
            if (data.success) {
                displayProducts(data.products);
                resultsCount.textContent = `${data.products.length} results`;
            } else {
                throw new Error(data.message || 'Search failed');
            }
        } catch (error) {
            console.error('Search error:', error);
            displayProducts([]);
            resultsCount.textContent = '0 results';
        }
    }

    // Event listeners
    btn.addEventListener('click', () => search('list'));
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            search('list');
        }
    });

    // Load initial products
    search('list');
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\POS\resources\views/cashier/sales/create.blade.php ENDPATH**/ ?>