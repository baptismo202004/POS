@extends('layouts.app')
@section('title', 'New Sale')

@push('stylesDashboard')
<style>
    /* Full-width POS for cashier */
    aside.sidebar-fixed {
        display: none !important;
    }
    main.main-content {
        margin-left: 0 !important;
    }

    :root {
        --navy:      #0D47A1;
        --navy-mid:  #1565C0;
        --blue:      #1976D2;
        --blue-lt:   #42A5F5;
        --cyan:      #00E5FF;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --bg:        #f0f6ff;
        --card:      #ffffff;
        --border:    rgba(25,118,210,0.12);
        --text:      #1a2744;
        --text-muted:#6b84aa;
        --radius: 18px;
        --shadow: 0 4px 28px rgba(13,71,161,0.10);
        --shadow-hover: 0 8px 32px rgba(13,71,161,0.18);

        --primary-color: var(--navy-mid);
        --secondary-color: var(--text-muted);
        --light-bg: var(--bg);
        --card-shadow: var(--shadow);
        --card-hover-shadow: var(--shadow-hover);
    }

    body {
        background: var(--bg);
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--text);
    }

    .pos-page-bg {
        position: fixed;
        inset: 0;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .pos-page-bg::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
    }
    .pos-bg-circle {
        position: absolute;
        border-radius: 50%;
        filter: blur(60px);
        opacity: .12;
        pointer-events: none;
    }
    .pos-bg-c1 {
        width: 400px;
        height: 400px;
        background: #1976D2;
        top: -120px;
        left: -120px;
        animation: bf1 9s ease-in-out infinite;
    }
    .pos-bg-c2 {
        width: 300px;
        height: 300px;
        background: #00B0FF;
        bottom: -80px;
        right: -80px;
        animation: bf2 11s ease-in-out infinite;
    }
    @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(30px,20px)} }
    @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-25px)} }

    .main-container {
        position: relative;
        z-index: 1;
        background: transparent;
        margin: 0;
        padding: 28px 24px 48px;
        min-height: 100vh;
        max-width: none;
        width: calc(100% - 24px);
        margin-left: auto;
        margin-right: auto;
    }

    .header-title {
        color: var(--navy);
        font-size: 28px;
        font-weight: 900;
        margin-bottom: 10px;
        font-family: 'Nunito', sans-serif;
    }

    .header-title i {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--navy), var(--blue-lt));
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        color: #fff;
        box-shadow: 0 6px 18px rgba(13,71,161,0.30);
        margin-right: 12px !important;
    }

    .main-container .text-muted {
        color: var(--text-muted) !important;
    }

    .main-container .btn-outline-primary {
        border: 1.5px solid var(--border) !important;
        background: var(--card) !important;
        color: var(--navy) !important;
        border-radius: 11px !important;
        padding: 9px 18px !important;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all .2s ease;
        font-family: 'Nunito', sans-serif;
    }
    .main-container .btn-outline-primary:hover {
        background: var(--navy) !important;
        border-color: var(--navy) !important;
        color: #fff !important;
        transform: translateX(-3px);
    }

    /* Custom SweetAlert animations */
    @keyframes slideInUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.3);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .swal2-icon-success {
        animation: bounceIn 0.6s ease-out;
    }

    .swal2-icon-success i {
        font-size: 4rem;
        color: #10b981;
    }

    .search-section {
        padding: 20px 22px;
        border-radius: var(--radius);
        background: rgba(13,71,161,0.03);
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
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
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(66,165,245,0.35);
    }

    .search-btn {
        background: linear-gradient(135deg, var(--navy-mid), var(--blue));
        border: none;
        border-radius: 0 12px 12px 0;
        padding: 13px 22px;
        color: #fff;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        font-size: 13px;
        letter-spacing: 0.2px;
        box-shadow: 0 3px 12px rgba(13,71,161,0.22);
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
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(13,71,161,0.30);
        background: linear-gradient(135deg, var(--blue), var(--blue-lt));
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
        border: 1.5px solid var(--border);
        background: #fff;
        padding: 13px 18px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        outline: none;
        transition: all 0.3s ease;
        flex: 1;
        position: relative;
        border-radius: 12px 0 0 12px;
    }

    /* POS two-column layout */
    .main-container > .row {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 18px;
        align-items: start;
        margin: 0;
    }
    .main-container > .row > [class*="col-"] {
        width: auto;
        max-width: none;
        padding: 0;
    }
    @media (max-width: 900px) {
        .main-container > .row {
            grid-template-columns: 1fr;
        }
    }

    /* Card shell + gradient header */
    .main-container .card {
        border-radius: 20px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 24px rgba(13,71,161,0.08);
        overflow: hidden;
        background: var(--card);
    }

    .main-container .card-header {
        padding: 15px 22px;
        background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
        border-bottom: 0;
        position: relative;
        overflow: hidden;
        color: #fff;
        font-family: 'Nunito', sans-serif;
        font-weight: 800;
    }
    .main-container .card-header::after {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
        top: -80px;
        right: -50px;
        pointer-events: none;
    }
    .main-container .card-header span,
    .main-container .card-header h5 {
        position: relative;
        z-index: 1;
        color: #fff;
        margin: 0;
    }
    .main-container .card-header i {
        color: rgba(0,229,255,.85);
    }

    .main-container .badge.bg-primary {
        background: rgba(255,255,255,0.15) !important;
        border: 1px solid rgba(255,255,255,0.25) !important;
        color: #fff !important;
        font-weight: 800;
        border-radius: 20px;
        padding: 3px 10px;
    }

    /* Table */
    .table-container {
        max-height: 420px;
        overflow-y: auto;
        border: 0;
        border-radius: 0;
    }
    .table-container::-webkit-scrollbar { width: 3px; }
    .table-container::-webkit-scrollbar-thumb { background: rgba(13,71,161,0.15); border-radius: 4px; }

    #results-table thead th {
        padding: 11px 18px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--text-muted);
        background: rgba(13,71,161,0.03);
        border-bottom: 1.5px solid var(--border);
        position: sticky;
        top: 0;
        z-index: 2;
        white-space: nowrap;
    }
    #results-table tbody td {
        padding: 13px 18px;
        font-size: 13px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(13,71,161,0.05);
    }
    #results-table tbody tr:nth-child(odd)  { background: #fff; }
    #results-table tbody tr:nth-child(even) { background: rgba(240,246,255,0.6); }
    #results-table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }

    /* Order summary */
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

    /* Payment toggle */
    .btn-group.w-100 {
        gap: 8px;
    }
    .btn-group.w-100 .btn {
        border-radius: 11px !important;
        border: 1.5px solid var(--border) !important;
        background: var(--bg) !important;
        color: var(--text-muted) !important;
        font-size: 12.5px;
        font-weight: 800;
        font-family: 'Nunito', sans-serif;
        padding: 10px 10px;
        transition: all .2s ease;
    }
    .btn-check:checked + .btn {
        background: linear-gradient(135deg, var(--navy), var(--blue)) !important;
        color: #fff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 14px rgba(13,71,161,0.28);
    }

    /* Credit details box */
    #credit-details .card {
        background: rgba(245,158,11,0.06);
        border: 1.5px solid rgba(245,158,11,0.22);
        border-radius: 13px;
        box-shadow: none;
    }
    #credit-details .card-body {
        background: transparent !important;
    }

    /* Total + action buttons */
    #total-amount {
        font-family: 'Nunito', sans-serif;
        font-size: 28px;
        font-weight: 900;
        color: var(--navy) !important;
    }

    .order-summary .btn-success.btn-lg {
        width: 100%;
        padding: 13px;
        background: linear-gradient(135deg, #059669, #10b981) !important;
        border: none !important;
        border-radius: 13px !important;
        font-size: 15px;
        font-weight: 900;
        font-family: 'Nunito', sans-serif;
        box-shadow: 0 4px 16px rgba(16,185,129,0.30);
        transition: all .22s cubic-bezier(.34,1.56,.64,1);
    }
    .order-summary .btn-success.btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(16,185,129,0.38);
    }

    .order-summary .btn-outline-danger {
        width: 100%;
        padding: 10px;
        background: transparent !important;
        border: 1.5px solid rgba(239,68,68,0.25) !important;
        color: var(--danger-color) !important;
        border-radius: 13px !important;
        font-weight: 800;
        font-family: 'Nunito', sans-serif;
        transition: all .2s ease;
    }
    .order-summary .btn-outline-danger:hover {
        background: rgba(239,68,68,0.06) !important;
        border-color: var(--danger-color) !important;
    }

    .search-input:focus {
        border-color: var(--blue-lt);
        box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        animation: none;
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
        color: var(--navy);
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
        background: linear-gradient(135deg, var(--navy-mid), var(--blue));
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-add-product:hover {
        background: linear-gradient(135deg, var(--blue), var(--blue-lt));
        transform: scale(1.06);
    }

    .barcode-input {
        font-size: 18px;
        border: 2px solid var(--primary-color);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
    }

    .sale-items {
        background: var(--card);
        border-radius: var(--radius);
        padding: 20px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .summary-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 20px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
        position: sticky;
        top: 20px;
    }

    .total-amount {
        font-size: 24px;
        font-weight: bold;
        color: var(--navy);
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
            padding: 16px 12px 32px;
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
@endpush

@section('content')
<div class="pos-page-bg">
    <div class="pos-bg-circle pos-bg-c1"></div>
    <div class="pos-bg-circle pos-bg-c2"></div>
</div>
<div class="main-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="header-title mb-2">
                <i class="fas fa-cash-register me-3"></i>Cashier POS
            </h1>
            <p class="text-muted mb-0">Fast sales processing with barcode scanning support</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('cashier.pos.electronics') }}" class="btn btn-outline-primary">
                <i class="fas fa-plug me-2"></i>Electronic Devices
            </a>
            <a href="{{ route('cashier.sales.index') }}" class="btn btn-outline-primary">
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
                                            <label class="form-label">Credit Date:</label>
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

function getProductPrimaryBranch(product) {
    if (!product) return null;
    if (Array.isArray(product.branches) && product.branches.length > 0) return product.branches[0];
    return null;
}

function getSelectedUnitForProduct(productId) {
    const select = document.getElementById(`unit-type-${productId}`);
    const unitTypeId = select ? parseInt(select.value || '0', 10) : 0;
    if (!unitTypeId) return null;

    const product = products.find(p => p.id === productId);
    const branch = getProductPrimaryBranch(product);
    const units = branch && Array.isArray(branch.stock_units) ? branch.stock_units : [];
    return units.find(u => parseInt(u.unit_type_id, 10) === unitTypeId) || null;
}

function getCartKey(productId, unitTypeId) {
    return `${productId}:${unitTypeId}`;
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const unit = getSelectedUnitForProduct(productId);
    if (!unit || !unit.unit_type_id) {
        alert('Please select unit type first');
        return;
    }

    const unitTypeId = parseInt(unit.unit_type_id, 10);
    const unitPrice = parseFloat(unit.price || 0);
    const unitStock = parseFloat(unit.stock || 0);

    if (unitStock <= 0) {
        alert('Out of stock for selected unit type');
        return;
    }

    const key = getCartKey(productId, unitTypeId);
    const existingItem = saleData.items.find(item => item.key === key);

    if (existingItem) {
        existingItem.quantity++;
    } else {
        saleData.items.push({
            key: key,
            id: product.id,
            unit_type_id: unitTypeId,
            unit_name: unit.unit_name || '',
            name: product.product_name,
            price: unitPrice,
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
                <small class="text-muted">${item.unit_name ? `(${item.unit_name}) ` : ''}₱${item.price.toFixed(2)} × ${item.quantity}</small>
            </div>
            <div class="d-flex align-items-center">
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity('${item.key}', ${item.quantity - 1})">-</button>
                <span class="mx-2">${item.quantity}</span>
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="updateQuantity('${item.key}', ${item.quantity + 1})">+</button>
                <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('${item.key}')">
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

function updateQuantity(itemKey, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(itemKey);
        return;
    }
    
    const item = saleData.items.find(item => item.key === itemKey);
    if (item) {
        item.quantity = newQuantity;
        updateOrderDisplay();
    }
}

function removeFromCart(itemKey) {
    saleData.items = saleData.items.filter(item => item.key !== itemKey);
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
        formData.append(`products[${index}][unit_type_id]`, item.unit_type_id);
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
    fetch('{{ route("cashier.pos.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Capture payment details before clearing cart
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
            const customerName = document.getElementById('customer_name').value;
            const creditDueDate = document.getElementById('credit_due_date').value;
            const totalAmount = document.getElementById('total-amount').textContent;
            
            // Clear cart
            saleData.items = [];
            updateOrderDisplay();
            
            // Show success and redirect to receipt if cash payment
            if (data.auto_receipt && data.receipt_url) {
                // Redirect to receipt
                window.location.href = data.receipt_url;
            } else {
                // For credit payments, show beautiful success message
                if (selectedPayment === 'credit') {
                    Swal.fire({
                        title: '<div class="swal2-icon-success"><i class="fas fa-check-circle"></i></div>',
                        html: `
                            <div class="text-center">
                                <h3 class="mb-3" style="color: #10b981; font-weight: 600;">Credit Sale Completed Successfully!</h3>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Customer:</span>
                                        <strong>${customerName || 'Guest Customer'}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Total Amount:</span>
                                        <strong style="color: #2563eb; font-size: 1.2em;">${totalAmount}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Credit Date:</span>
                                        <strong>${new Date(creditDueDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Payment Method:</span>
                                        <span class="badge bg-primary fs-6">Credit</span>
                                    </div>
                                </div>
                                <div class="alert alert-info mb-3" style="border-radius: 10px;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Credit Reference:</strong> A credit record has been created for this sale. Use this reference when recording payments for this credit.
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-success btn-lg me-2" onclick="window.location.reload()">
                                        <i class="fas fa-plus-circle me-2"></i>Okay
                                    </button>
                                    <button class="btn btn-outline-secondary btn-lg" onclick="window.location.href='/cashier/sales'">
                                        <i class="fas fa-list me-2"></i>View Sales
                                    </button>
                                </div>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCloseButton: true,
                        width: '600px',
                        padding: '2em',
                        backdrop: 'rgba(0,0,123,0.1)',
                        didOpen: () => {
                            // Add custom animations
                            const swalContainer = document.querySelector('.swal2-container');
                            swalContainer.style.animation = 'slideInUp 0.5s ease-out';
                        }
                    });
                } else {
                    // For other payment methods, show simple success
                    Swal.fire({
                        icon: 'success',
                        title: 'Sale Completed!',
                        text: 'The sale has been processed successfully.',
                        confirmButtonColor: '#10b981',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                }
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'An error occurred while processing the sale.',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Checkout error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Processing Error',
            text: 'An error occurred while processing the sale. Please try again.',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        });
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

    tableBody.innerHTML = productsList.map(product => {
        const branch = getProductPrimaryBranch(product);
        const units = branch && Array.isArray(branch.stock_units) ? branch.stock_units : [];
        const defaultUnit = units.length > 0 ? units[0] : null;
        const defaultUnitId = defaultUnit ? parseInt(defaultUnit.unit_type_id || '0', 10) : 0;
        const defaultStock = defaultUnit ? parseFloat(defaultUnit.stock || 0) : parseFloat(product.total_stock ?? 0);
        const defaultPrice = defaultUnit ? parseFloat(defaultUnit.price || 0) : parseFloat(product.selling_price || 0);

        const unitOptions = units.map(u => {
            const id = parseInt(u.unit_type_id || '0', 10);
            const name = u.unit_name || '';
            const selected = id === defaultUnitId ? 'selected' : '';
            return `<option value="${id}" ${selected}>${name}</option>`;
        }).join('');

        const unitSelect = units.length > 0
            ? `<select class="form-select form-select-sm mt-1" id="unit-type-${product.id}" onchange="onUnitTypeChange(${product.id})">${unitOptions}</select>`
            : '';

        return `
        <tr class="animate-in">
            <td>
                <div class="d-flex align-items-center">
                    ${product.image ? `<img src="/storage/${product.image}" alt="${product.product_name}" class="me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">` : '<i class="fas fa-box me-2 text-muted"></i>'}
                    <div>
                        <div class="fw-bold">${product.product_name}</div>
                        ${unitSelect}
                    </div>
                </div>
            </td>
            <td>${product.barcode || 'N/A'}</td>
            <td class="text-end">
                <span id="stock-${product.id}" class="badge ${(defaultStock > 10) ? 'bg-success' : 'bg-warning'}">
                    ${Number.isFinite(defaultStock) ? defaultStock.toFixed(2) : '0.00'}
                </span>
            </td>
            <td class="text-end">
                <span id="price-${product.id}" class="price-display">₱${defaultPrice.toFixed(2)}</span>
            </td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="addToCart(${product.id})">
                    <i class="fas fa-plus me-1"></i>Add
                </button>
            </td>
        </tr>
        `;
    }).join('');
}

function onUnitTypeChange(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const unit = getSelectedUnitForProduct(productId);
    if (!unit) return;

    const stockEl = document.getElementById(`stock-${productId}`);
    const priceEl = document.getElementById(`price-${productId}`);

    const stock = parseFloat(unit.stock || 0);
    const price = parseFloat(unit.price || 0);

    if (stockEl) {
        stockEl.textContent = Number.isFinite(stock) ? stock.toFixed(2) : '0.00';
        stockEl.classList.remove('bg-success', 'bg-warning');
        stockEl.classList.add(stock > 10 ? 'bg-success' : 'bg-warning');
    }

    if (priceEl) {
        priceEl.textContent = `₱${price.toFixed(2)}`;
    }
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
                const response = await fetch('{{ route("cashier.pos.lookup") }}', {
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
            const response = await fetch('{{ route("cashier.pos.lookup") }}', {
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

    // Payment method change listeners
    paymentCash.addEventListener('change', function() {
        if (this.checked) {
            creditDetails.style.display = 'none';
        }
    });

    paymentCredit.addEventListener('change', function() {
        if (this.checked) {
            creditDetails.style.display = 'block';
        }
    });

    // Load initial products
    search('list');
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
