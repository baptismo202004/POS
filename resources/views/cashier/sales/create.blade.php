@extends('layouts.app')
@section('title', 'New Sale')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">New Sale</h4>
                    <a href="{{ route('cashier.sales.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sales
                    </a>
                </div>
                <div class="card-body">
                    <form id="saleForm">
                        <!-- Customer Information -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Enter customer name" id="customerName">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="customer_phone" class="form-control" placeholder="Phone number" id="customerPhone">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="customer_email" class="form-control" placeholder="Email address" id="customerEmail">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Payment Method</label>
                                <select name="payment_method" class="form-select" required id="paymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="gcash">GCash</option>
                                    <option value="other">Other</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Address (Optional)</label>
                                <input type="text" name="customer_address" class="form-control" placeholder="Customer address" id="customerAddress">
                            </div>
                        </div>

                        <!-- Customer Info Display -->
                        <div id="customerInfoDisplay" class="alert alert-info d-none mb-4">
                            <h6><i class="fas fa-user"></i> Customer Information</h6>
                            <div id="customerDetails"></div>
                        </div>

                        <!-- Product Search -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Add Product</label>
                                <div class="input-group">
                                    <input type="text" id="productSearch" class="form-control" 
                                           placeholder="Search by product name or barcode...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="searchResults" class="position-absolute w-100 bg-white border rounded mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"></div>
                            </div>
                        </div>

                        <!-- Sale Items -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Sale Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Subtotal</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTableBody">
                                            <!-- Items will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="emptyState" class="text-center py-4 text-muted">
                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                    <h5>No items added</h5>
                                    <p>Search and add products to start the sale</p>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Discount Amount (₱)</label>
                                <input type="number" name="discount_amount" class="form-control" 
                                       placeholder="0.00" step="0.01" min="0" id="discountAmount">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Discount Percentage (%)</label>
                                <input type="number" name="discount_percentage" class="form-control" 
                                       placeholder="0" step="0.01" min="0" max="100" id="discountPercentage">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Discount Type</label>
                                <select class="form-select" id="discountType">
                                    <option value="none">No Discount</option>
                                    <option value="amount">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>

                        <!-- Total Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Order Summary</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">₱0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Discount:</span>
                                            <span id="discountDisplay">₱0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total:</strong>
                                            <strong id="totalAmount">₱0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center justify-content-end">
                                <div>
                                    <button type="button" class="btn btn-outline-danger me-2" onclick="clearSale()">
                                        <i class="fas fa-trash"></i> Clear Sale
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg" id="completeSaleBtn">
                                        <i class="fas fa-check"></i> Complete Sale
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Product</label>
                    <input type="text" class="form-control" id="modalProductName" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Type</label>
                    <select class="form-select" id="modalUnitType"></select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Price (₱)</label>
                    <input type="number" class="form-control" id="modalPrice" step="0.01" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="modalQuantity" min="1" value="1">
                    <small class="text-muted">Available stock: <span id="availableStock">0</span></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addProductToSale()">Add to Sale</button>
            </div>
        </div>
    </div>
</div>

<script>
let saleItems = [];
let selectedProduct = null;

// Payment method change handler
document.getElementById('paymentMethod').addEventListener('change', function() {
    const isCredit = this.value === 'credit';
    const customerName = document.getElementById('customerName');
    const customerPhone = document.getElementById('customerPhone');
    
    if (isCredit) {
        customerName.setAttribute('required', 'required');
        customerPhone.setAttribute('required', 'required');
        customerName.placeholder = 'Customer name (required for credit)';
        customerPhone.placeholder = 'Phone number (required for credit)';
        
        // Show warning if customer fields are empty
        if (!customerName.value || !customerPhone.value) {
            showCustomerWarning();
        }
    } else {
        customerName.removeAttribute('required');
        customerPhone.removeAttribute('required');
        customerName.placeholder = 'Enter customer name';
        customerPhone.placeholder = 'Phone number';
        hideCustomerWarning();
    }
});

// Customer lookup functionality
let customerLookupTimeout;
document.getElementById('customerPhone').addEventListener('input', function() {
    clearTimeout(customerLookupTimeout);
    const phone = this.value.trim();
    
    if (phone.length >= 8) {
        customerLookupTimeout = setTimeout(() => lookupCustomer(phone), 500);
    } else {
        hideCustomerInfo();
    }
});

document.getElementById('customerName').addEventListener('input', function() {
    const name = this.value.trim();
    const phone = document.getElementById('customerPhone').value.trim();
    
    if (name.length >= 3 && !phone) {
        customerLookupTimeout = setTimeout(() => lookupCustomerByName(name), 500);
    }
});

function lookupCustomer(phone) {
    fetch(`/cashier/customers/lookup?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.customer) {
                showCustomerInfo(data.customer);
            } else {
                hideCustomerInfo();
            }
        })
        .catch(error => {
            console.error('Error looking up customer:', error);
        });
}

function lookupCustomerByName(name) {
    fetch(`/cashier/customers/lookup?name=${encodeURIComponent(name)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.customer) {
                showCustomerInfo(data.customer);
            } else {
                hideCustomerInfo();
            }
        })
        .catch(error => {
            console.error('Error looking up customer:', error);
        });
}

function showCustomerInfo(customer) {
    const display = document.getElementById('customerInfoDisplay');
    const details = document.getElementById('customerDetails');
    
    details.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <strong>Name:</strong> ${customer.full_name}<br>
                <strong>Phone:</strong> ${customer.phone || 'N/A'}<br>
                <strong>Email:</strong> ${customer.email || 'N/A'}
            </div>
            <div class="col-md-6">
                <strong>Address:</strong> ${customer.address || 'N/A'}<br>
                <strong>Credit Limit:</strong> ₱${number_format(customer.max_credit_limit, 2)}<br>
                <strong>Status:</strong> <span class="badge bg-${customer.status === 'active' ? 'success' : 'danger'}">${customer.status}</span>
            </div>
        </div>
    `;
    
    display.classList.remove('d-none');
    
    // Auto-fill the form fields
    document.getElementById('customerName').value = customer.full_name;
    document.getElementById('customerPhone').value = customer.phone || '';
    document.getElementById('customerEmail').value = customer.email || '';
    document.getElementById('customerAddress').value = customer.address || '';
}

function hideCustomerInfo() {
    document.getElementById('customerInfoDisplay').classList.add('d-none');
}

function showCustomerWarning() {
    const paymentMethod = document.getElementById('paymentMethod').value;
    if (paymentMethod === 'credit') {
        const customerName = document.getElementById('customerName').value;
        const customerPhone = document.getElementById('customerPhone').value;
        
        if (!customerName || !customerPhone) {
            if (!document.getElementById('customerWarning')) {
                const warning = document.createElement('div');
                warning.id = 'customerWarning';
                warning.className = 'alert alert-warning mt-2';
                warning.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <strong>Customer Information Required:</strong> For credit payments, please provide customer name and phone number.';
                document.getElementById('customerName').parentNode.appendChild(warning);
            }
        }
    }
}

function hideCustomerWarning() {
    const warning = document.getElementById('customerWarning');
    if (warning) {
        warning.remove();
    }
}

// Product search
document.getElementById('productSearch').addEventListener('input', function() {
    const query = this.value.trim();
    if (query.length < 2) {
        document.getElementById('searchResults').style.display = 'none';
        return;
    }

    fetch(`/cashier/products/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(products => {
            const resultsDiv = document.getElementById('searchResults');
            resultsDiv.innerHTML = '';
            
            if (products.length === 0) {
                resultsDiv.innerHTML = '<div class="p-3 text-muted">No products found</div>';
            } else {
                products.forEach(product => {
                    const item = document.createElement('div');
                    item.className = 'p-3 border-bottom hover-bg-light cursor-pointer';
                    item.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>${product.product_name}</strong>
                                <br><small class="text-muted">Stock: ${product.current_stock}</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Barcode:</small><br>
                                <small>${product.barcode || 'N/A'}</small>
                            </div>
                        </div>
                    `;
                    item.onclick = () => selectProduct(product);
                    resultsDiv.appendChild(item);
                });
            }
            resultsDiv.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
});

function selectProduct(product) {
    selectedProduct = product;
    document.getElementById('modalProductName').value = product.product_name;
    document.getElementById('availableStock').textContent = product.current_stock;
    
    // Populate unit types
    const unitSelect = document.getElementById('modalUnitType');
    unitSelect.innerHTML = '';
    product.unit_types.forEach(unit => {
        const option = document.createElement('option');
        option.value = unit.id;
        option.textContent = unit.name;
        unitSelect.appendChild(option);
    });
    
    // Get price for first unit type
    if (product.unit_types.length > 0) {
        getProductPrice(product.id, product.unit_types[0].id);
    }
    
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('productSearch').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

function getProductPrice(productId, unitTypeId) {
    fetch(`/cashier/products/${productId}/price?unit_type_id=${unitTypeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalPrice').value = data.price;
            }
        });
}

document.getElementById('modalUnitType').addEventListener('change', function() {
    if (selectedProduct) {
        getProductPrice(selectedProduct.id, this.value);
    }
});

function addProductToSale() {
    const quantity = parseInt(document.getElementById('modalQuantity').value);
    const availableStock = parseInt(document.getElementById('availableStock').textContent);
    
    if (quantity > availableStock) {
        alert('Insufficient stock available!');
        return;
    }
    
    const item = {
        product_id: selectedProduct.id,
        product_name: selectedProduct.product_name,
        unit_type_id: parseInt(document.getElementById('modalUnitType').value),
        unit_type_name: document.getElementById('modalUnitType').options[document.getElementById('modalUnitType').selectedIndex].text,
        unit_price: parseFloat(document.getElementById('modalPrice').value),
        quantity: quantity,
        subtotal: quantity * parseFloat(document.getElementById('modalPrice').value)
    };
    
    // Check if product already exists
    const existingIndex = saleItems.findIndex(i => i.product_id === item.product_id && i.unit_type_id === item.unit_type_id);
    if (existingIndex !== -1) {
        saleItems[existingIndex].quantity += quantity;
        saleItems[existingIndex].subtotal = saleItems[existingIndex].quantity * saleItems[existingIndex].unit_price;
    } else {
        saleItems.push(item);
    }
    
    updateItemsTable();
    updateTotals();
    
    bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
}

function updateItemsTable() {
    const tbody = document.getElementById('itemsTableBody');
    const emptyState = document.getElementById('emptyState');
    
    if (saleItems.length === 0) {
        tbody.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    tbody.innerHTML = saleItems.map((item, index) => `
        <tr>
            <td>${item.product_name}</td>
            <td>${item.unit_type_name}</td>
            <td>₱${item.unit_price.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm" value="${item.quantity}" 
                       min="1" onchange="updateQuantity(${index}, this.value)" style="width: 80px;">
            </td>
            <td>₱${item.subtotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function updateQuantity(index, newQuantity) {
    const quantity = parseInt(newQuantity);
    if (quantity < 1) return;
    
    saleItems[index].quantity = quantity;
    saleItems[index].subtotal = quantity * saleItems[index].unit_price;
    
    updateItemsTable();
    updateTotals();
}

function removeItem(index) {
    saleItems.splice(index, 1);
    updateItemsTable();
    updateTotals();
}

function updateTotals() {
    const subtotal = saleItems.reduce((sum, item) => sum + item.subtotal, 0);
    const discountType = document.getElementById('discountType').value;
    let discount = 0;
    
    if (discountType === 'amount') {
        discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    } else if (discountType === 'percentage') {
        const percentage = parseFloat(document.getElementById('discountPercentage').value) || 0;
        discount = subtotal * (percentage / 100);
    }
    
    const total = Math.max(0, subtotal - discount);
    
    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('discountDisplay').textContent = `₱${discount.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `₱${total.toFixed(2)}`;
}

// Discount handling
document.getElementById('discountType').addEventListener('change', function() {
    const amountInput = document.getElementById('discountAmount');
    const percentageInput = document.getElementById('discountPercentage');
    
    amountInput.disabled = this.value !== 'amount';
    percentageInput.disabled = this.value !== 'percentage';
    
    if (this.value === 'none') {
        amountInput.value = '';
        percentageInput.value = '';
    }
    
    updateTotals();
});

document.getElementById('discountAmount').addEventListener('input', updateTotals);
document.getElementById('discountPercentage').addEventListener('input', updateTotals);

// Form submission
document.getElementById('saleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate credit payment requirements
    const paymentMethod = document.getElementById('paymentMethod').value;
    if (paymentMethod === 'credit') {
        const customerName = document.getElementById('customerName').value.trim();
        const customerPhone = document.getElementById('customerPhone').value.trim();
        
        if (!customerName || !customerPhone) {
            alert('Customer name and phone number are required for credit payments!');
            return;
        }
    }
    
    if (saleItems.length === 0) {
        alert('Please add at least one item to the sale');
        return;
    }
    
    const formData = new FormData(this);
    formData.append('items', JSON.stringify(saleItems));
    
    const submitBtn = document.getElementById('completeSaleBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    fetch('/cashier/sales', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sale completed successfully!');
            window.location.href = `/cashier/sales/${data.sale_id}/receipt`;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the sale');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Complete Sale';
    });
});

function clearSearch() {
    document.getElementById('productSearch').value = '';
    document.getElementById('searchResults').style.display = 'none';
}

function clearSale() {
    if (confirm('Are you sure you want to clear this sale?')) {
        saleItems = [];
        updateItemsTable();
        updateTotals();
        document.getElementById('saleForm').reset();
        hideCustomerInfo();
        hideCustomerWarning();
    }
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#productSearch') && !e.target.closest('#searchResults')) {
        document.getElementById('searchResults').style.display = 'none';
    }
});
</script>
@endsection
