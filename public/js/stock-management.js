// Stock Management JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Function to update dashboard alerts
    function updateDashboardAlerts(outOfStockCount) {
        try {
            console.log('Updating dashboard alerts with count:', outOfStockCount);
            
            const totalAlertsElement = document.getElementById('totalAlertsCount') || 
                                  document.querySelector('[id="totalAlertsCount"]') ||
                                  document.querySelector('.widget-badge.alert-count');
            
            if (totalAlertsElement) {
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
                    if (data.alerts) {
                        const totalAlerts = data.alerts.outOfStock + data.alerts.negativeProfit + data.alerts.voidedSales + data.alerts.belowCostSales + data.alerts.highDiscountUsage;
                        totalAlertsElement.textContent = totalAlerts;
                    }
                })
                .catch(error => {
                    console.error('Error updating dashboard alerts:', error);
                });
            }
        } catch (error) {
            console.error('Error in updateDashboardAlerts:', error);
        }
    }

    // Show success messages if any
    const successMessage = document.querySelector('[data-success-message]');
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage.getAttribute('data-success-message'),
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
    }

    const adjustStockModal = document.getElementById('adjustStockModal');
    const stockInModal = document.getElementById('stockInModal');
    const stockHistoryModal = document.getElementById('stockHistoryModal');
    let currentProductData = null;
    let salesData = null;

    // Stock Adjustment Modal
    if (adjustStockModal) {
        adjustStockModal.addEventListener('show.bs.modal', function (event) {
            console.log('📋 [MODAL] Stock Adjustment Modal opened');
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');
            const currentStock = button.getAttribute('data-current-stock');
            const branchId = button.getAttribute('data-branch-id');
            const branchName = button.getAttribute('data-branch-name') || 'Main Branch';

            // Set product data
            currentProductData = {
                id: productId,
                name: productName,
                currentStock: currentStock,
                branchId: branchId,
                branchName: branchName
            };

            // Update modal content
            document.getElementById('adjustProductName').textContent = productName;
            document.getElementById('adjustBranchName').textContent = branchName;
            document.getElementById('adjustBranchName2').textContent = branchName;
            document.getElementById('adjustCurrentStock').textContent = currentStock;
            document.getElementById('adjustProductId').value = productId;

            // Reset form
            document.getElementById('adjustmentType').value = '';
            document.getElementById('purchase_id').value = '';
            document.getElementById('purchaseQuantity').value = '';
            document.getElementById('fromBranch').value = '';
            document.getElementById('transferQuantity').value = '';
            
            // Hide all adjustment options initially
            document.getElementById('purchaseOption').style.display = 'none';
            document.getElementById('transferOption').style.display = 'none';
            
            // Load other branches stock data
            console.log('🔄 [API] Starting to load other branches stock data...');
            loadOtherBranchesStock(productId, branchId);
            
            // Load purchase options for this product
            console.log('🔄 [API] Starting to load purchase options...');
            loadPurchaseOptions(productId);
            
            // Load branch options for transfer
            console.log('🔄 [API] Starting to load branch options for transfer...');
            loadBranchOptions(branchId);
            
            // Load and display sales data automatically
            loadAndDisplaySalesData(productId);
        });

        // Handle adjustment type change
        document.getElementById('adjustmentType').addEventListener('change', function(e) {
            const adjustmentType = e.target.value;
            
            // Hide all options first
            document.getElementById('purchaseOption').style.display = 'none';
            document.getElementById('transferOption').style.display = 'none';
            
            if (adjustmentType === 'purchase') {
                document.getElementById('purchaseOption').style.display = 'block';
            } else if (adjustmentType === 'transfer') {
                document.getElementById('transferOption').style.display = 'block';
            }
        });

        // Save adjustment button
        document.getElementById('saveAdjustmentBtn').addEventListener('click', function() {
            const adjustmentType = document.getElementById('adjustmentType').value;
            
            if (!adjustmentType) {
                Swal.fire('Error', 'Please select an adjustment type', 'error');
                return;
            }
            
            if (adjustmentType === 'purchase') {
                savePurchaseAdjustment();
            } else if (adjustmentType === 'transfer') {
                saveTransferAdjustment();
            }
        });
    }

    // Stock In Modal
    if (stockInModal) {
        stockInModal.addEventListener('show.bs.modal', function (event) {
            console.log('📥 [MODAL] Stock In Modal opened');
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');

            // Update modal content
            document.getElementById('stockInProductName').textContent = productName;
            document.getElementById('stockInProductId').value = productId;
            
            // Reset form
            document.getElementById('stockInQuantity').value = '';
            document.getElementById('stockInPrice').value = '';
            document.getElementById('stockInNotes').value = '';
        });
    }

    // Stock History Modal
    if (stockHistoryModal) {
        stockHistoryModal.addEventListener('show.bs.modal', function (event) {
            console.log('📊 [MODAL] Stock History Modal opened');
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            const productName = button.getAttribute('data-product-name');

            // Update modal title
            document.getElementById('stockHistoryModalLabel').textContent = `Stock History - ${productName}`;
            
            // Load stock history
            loadStockHistory(productId);
        });
    }

    // Load other branches stock data
    function loadOtherBranchesStock(productId, currentBranchId) {
        console.log('📊 [BRANCH_STOCK] Loading stock data for product:', {
            productId: productId,
            currentBranchId: currentBranchId,
            endpoint: `/inventory/product-stock/${productId}`
        });
        
        fetch(`/inventory/product-stock/${productId}`)
            .then(response => {
                console.log('📡 [API] Branch stock response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ [BRANCH_STOCK] Stock data loaded successfully:', {
                    dataType: typeof data,
                    isArray: Array.isArray(data),
                    length: data ? data.length : 0,
                    rawData: data
                });
                
                // Display other branches stock
                const otherBranchesContainer = document.getElementById('otherBranchesStock');
                if (otherBranchesContainer) {
                    if (Array.isArray(data) && data.length > 0) {
                        // Show all branches including current branch, but exclude current from display
                        const displayBranches = data.filter(branch => branch.branch_id != currentBranchId);
                        
                        console.log('📋 [BRANCH_STOCK] Processing branch data:', displayBranches);
                        
                        if (displayBranches.length > 0) {
                            let html = '';
                            let totalBranches = 0;
                            let processedBranches = 0;
                            
                            displayBranches.forEach(branch => {
                                totalBranches++;
                                
                                console.log(`🔍 [FIELD_DEBUG] Branch object keys:`, Object.keys(branch));
                                console.log(`🔍 [FIELD_DEBUG] Branch object:`, branch);
                                
                                const branchName = branch.branch_name || branch.name || 'Unknown Branch';
                                const totalUnits = branch.current_stock || 0;
                                
                                console.log(`🏪 [BRANCH] Processing branch:`, {
                                    branchId: branch.branch_id,
                                    branchName: branchName,
                                    totalUnits: totalUnits,
                                    current_stock: branch.current_stock,
                                    isEligibleForTransfer: branch.branch_id != currentBranchId,
                                    rawBranch: branch
                                });
                                
                                html += `
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-light">
                                            <div class="card-body">
                                                <h6 class="text-dark">${branchName}</h6>
                                                <h4 class="text-dark">${totalUnits}</h4>
                                                <small class="text-muted">Total Units</small>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                processedBranches++;
                            });
                            
                            otherBranchesContainer.innerHTML = html;
                            
                            console.log(`✅ [BRANCH_STOCK] Branch stock display completed:`, {
                                totalBranches: totalBranches,
                                processedBranches: processedBranches,
                                excludedCurrentBranch: currentBranchId
                            });
                        } else {
                            // Check if we have any data at all (including current branch)
                            console.log('📭 [BRANCH_STOCK] No other branches found - checking if we have current branch data');
                            console.log('🔍 [DEBUG] All branch data:', data);
                            
                            // If we have data but it's only current branch, still show current branch for reference
                            if (data.length > 0) {
                                const currentBranchData = data.find(branch => branch.branch_id == currentBranchId);
                                if (currentBranchData) {
                                    console.log('📭 [BRANCH_STOCK] Showing current branch as reference:', currentBranchData);
                                    const branchName = currentBranchData.branch_name || currentBranchData.name || 'Current Branch';
                                    const totalUnits = currentBranchData.total_units || currentBranchData.quantity || 0;
                                    
                                    otherBranchesContainer.innerHTML = `
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-light">
                                                <div class="card-body">
                                                    <h6 class="text-dark">${branchName}</h6>
                                                    <h4 class="text-dark">${totalUnits}</h4>
                                                    <small class="text-muted">Total Units</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-light">
                                                <div class="card-body text-center">
                                                    <h6 class="text-muted">No Other Branches</h6>
                                                    <small class="text-muted">This product only exists in current branch</small>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    
                                    console.log('📭 [BRANCH_STOCK] Current branch displayed with no other branches message');
                                } else {
                                    // Fallback to no data message
                                    showNoDataMessage('No Stock Data Available', 'This product has no stock records in any branches');
                                }
                            } else {
                                // No data at all
                                showNoDataMessage('No Stock Data Available', 'This product has no stock records in any branches');
                            }
                        }
                    } else {
                        // No data at all
                        showNoDataMessage('No Stock Data Available', 'This product has no stock records in any branches');
                    }
                }
                
                function showNoDataMessage(title, message) {
                    console.log('📭 [BRANCH_STOCK] Showing no data message:', title);
                    otherBranchesContainer.innerHTML = `
                        <div class="col-12">
                            <div class="card border-light">
                                <div class="card-body text-center py-4">
                                    <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted mb-2">${title}</h6>
                                    <small class="text-muted">${message}</small>
                                </div>
                            </div>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('❌ [BRANCH_STOCK] Error loading branch stock:', error);
                const otherBranchesContainer = document.getElementById('otherBranchesStock');
                if (otherBranchesContainer) {
                    otherBranchesContainer.innerHTML = '<p class="text-danger">Error loading branch stock data</p>';
                }
            });
    }

    // Load purchase options
    function loadPurchaseOptions(productId) {
        console.log('🛒 [PURCHASE] Loading purchase options for product:', {
            productId: productId,
            endpoint: `/purchases/by-product/${productId}`
        });
        
        fetch(`/purchases/by-product/${productId}`)
            .then(response => {
                console.log('📡 [API] Purchase options response received:', {
                    status: response.status,
                    statusText: response.statusText,
                    ok: response.ok
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ [PURCHASE] Purchase options loaded:', data);
                
                const purchaseSelect = document.getElementById('purchase_id');
                if (purchaseSelect) {
                    purchaseSelect.innerHTML = '<option value="">-- Select Purchase --</option>';
                    
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(purchase => {
                            const option = document.createElement('option');
                            option.value = purchase.id;
                            option.textContent = `Purchase #${purchase.id} - ${purchase.date} (${purchase.quantity} units)`;
                            purchaseSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No purchases found for this product';
                        option.disabled = true;
                        purchaseSelect.appendChild(option);
                    }
                }
            })
            .catch(error => {
                console.error('❌ [PURCHASE] Error loading purchase options:', error);
                const purchaseSelect = document.getElementById('purchase_id');
                if (purchaseSelect) {
                    purchaseSelect.innerHTML = '<option value="">Error loading purchases</option>';
                }
            });
    }

    // Load branch options for transfer
    function loadBranchOptions(currentBranchId) {
        console.log('🏢 [BRANCH] Loading branch options for transfer, excluding:', currentBranchId);
        
        fetch('/api/branches')
            .then(response => response.json())
            .then(data => {
                console.log('✅ [BRANCH] Branch options loaded:', data);
                
                const branchSelect = document.getElementById('fromBranch');
                if (branchSelect) {
                    branchSelect.innerHTML = '<option value="">-- Select Branch --</option>';
                    
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(branch => {
                            // Exclude current branch from transfer options
                            if (branch.id != currentBranchId) {
                                const option = document.createElement('option');
                                option.value = branch.id;
                                option.textContent = branch.branch_name;
                                branchSelect.appendChild(option);
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('❌ [BRANCH] Error loading branch options:', error);
                const branchSelect = document.getElementById('fromBranch');
                if (branchSelect) {
                    branchSelect.innerHTML = '<option value="">Error loading branches</option>';
                }
            });
    }

    // Load and display sales data
    function loadAndDisplaySalesData(productId) {
        console.log('📈 [SALES] Loading sales data for product:', productId);
        
        const salesGraphColumn = document.getElementById('salesGraphColumn');
        if (salesGraphColumn) {
            // Show loading state
            salesGraphColumn.innerHTML = `
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Sales Trend</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2">Loading sales data...</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Load sales data
            fetch(`/inventory/product-sales/${productId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('✅ [SALES] Sales data loaded:', data);
                    
                    if (data && data.length > 0) {
                        // Create sales graph
                        const salesHtml = `
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">Sales Trend</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        `;
                        salesGraphColumn.innerHTML = salesHtml;
                        
                        // Initialize chart
                        setTimeout(() => {
                            const ctx = document.getElementById('salesChart');
                            if (ctx) {
                                new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: data.map(item => item.date),
                                        datasets: [{
                                            label: 'Sales',
                                            data: data.map(item => item.quantity),
                                            borderColor: 'rgb(75, 192, 192)',
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                            tension: 0.1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            title: {
                                                display: true,
                                                text: currentProductData ? currentProductData.name : 'Product Sales'
                                            }
                                        }
                                    }
                                });
                            }
                        }, 100);
                    } else {
                        // Show no data message
                        salesGraphColumn.innerHTML = `
                            <div class="card border-secondary">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">Sales Trend</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted mb-2">No Sales Data</h6>
                                        <small class="text-muted">No sales records found for this product</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('❌ [SALES] Error loading sales data:', error);
                    salesGraphColumn.innerHTML = `
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">Sales Trend</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Error loading sales data
                                </div>
                            </div>
                        </div>
                    `;
                });
        }
    }

    // Save purchase adjustment
    function savePurchaseAdjustment() {
        const productId = document.getElementById('adjustProductId').value;
        const purchaseId = document.getElementById('purchase_id').value;
        const quantity = document.getElementById('purchaseQuantity').value;
        
        if (!purchaseId || !quantity) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Adding stock from purchase...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/inventory/${productId}/stock-in`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                purchase_id: purchaseId,
                quantity: quantity,
                reason: 'Stock from purchase'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', 'Stock added successfully from purchase', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to add stock', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to add stock', 'error');
        });
    }

    // Save transfer adjustment
    function saveTransferAdjustment() {
        const productId = document.getElementById('adjustProductId').value;
        const fromBranch = document.getElementById('fromBranch').value;
        const quantity = document.getElementById('transferQuantity').value;
        
        if (!fromBranch || !quantity) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Creating stock transfer...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/inventory/${productId}/adjust`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                from_branch: fromBranch,
                transfer_quantity: quantity,
                reason: 'Stock transfer',
                adjustment_type: 'transfer'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', 'Stock transfer created successfully', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to create transfer', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to create transfer', 'error');
        });
    }
    function loadStockHistory(productId) {
        console.log('📊 [HISTORY] Loading stock history for product:', productId);
        
        fetch(`/inventory/product-stock-history/${productId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ [HISTORY] Stock history loaded:', data);
                
                const historyContent = document.getElementById('stockHistoryContent');
                if (historyContent) {
                    if (data.history && data.history.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Date</th><th>Type</th><th>Quantity</th><th>Price</th><th>Branch</th><th>Reason</th><th>Notes</th></tr></thead><tbody>';
                        
                        data.history.forEach(record => {
                            const date = new Date(record.created_at).toLocaleString();
                            const typeBadge = `<span class="badge bg-${record.type === 'in' ? 'success' : 'warning'}">${record.type.toUpperCase()}</span>`;
                            const price = record.price ? '₱' + parseFloat(record.price).toFixed(2) : 'N/A';
                            const branch = record.branch_name || 'N/A';
                            const reason = record.reason || '-';
                            const notes = record.notes || '-';
                            
                            html += `
                                <tr>
                                    <td>${date}</td>
                                    <td>${typeBadge}</td>
                                    <td>${record.quantity}</td>
                                    <td>${price}</td>
                                    <td>${branch}</td>
                                    <td>${reason}</td>
                                    <td>${notes}</td>
                                </tr>
                            `;
                        });
                        
                        html += '</tbody></table></div>';
                        historyContent.innerHTML = html;
                    } else {
                        historyContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-history fa-3x text-muted mb-3"></i><h6 class="text-muted">No Stock History</h6><small class="text-muted">No stock movements found for this product</small></div>';
                    }
                }
            })
            .catch(error => {
                console.error('❌ [HISTORY] Error loading stock history:', error);
                const historyContent = document.getElementById('stockHistoryContent');
                if (historyContent) {
                    historyContent.innerHTML = '<div class="alert alert-danger">Error loading stock history</div>';
                }
            });
    }

    // Save stock adjustment
    window.saveStockAdjustment = function() {
        const productId = document.getElementById('adjustProductId').value;
        const adjustmentType = document.getElementById('adjustmentType').value;
        const newStock = document.getElementById('newStock').value;
        const reason = document.getElementById('adjustmentReason').value;
        
        if (!adjustmentType || !newStock || !reason) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        const currentStock = parseInt(document.getElementById('adjustCurrentStock').textContent);
        let finalStock = parseInt(newStock);
        
        if (adjustmentType === 'add') {
            finalStock = currentStock + parseInt(newStock);
        } else if (adjustmentType === 'subtract') {
            finalStock = currentStock - parseInt(newStock);
            if (finalStock < 0) {
                Swal.fire('Error', 'Stock cannot be negative', 'error');
                return;
            }
        }
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Adjusting stock...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/inventory/${productId}/adjust`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                new_stock: finalStock,
                reason: reason,
                adjustment_type: adjustmentType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to adjust stock', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to adjust stock', 'error');
        });
    };

    // Save stock in
    window.saveStockIn = function() {
        const productId = document.getElementById('stockInProductId').value;
        const quantity = document.getElementById('stockInQuantity').value;
        const price = document.getElementById('stockInPrice').value;
        const branchId = document.getElementById('stockInBranch').value;
        const notes = document.getElementById('stockInNotes').value;
        
        if (!quantity || !price || !branchId) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Adding stock...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(`/inventory/${productId}/stock-in`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                quantity: quantity,
                price: price,
                branch_id: branchId,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', 'Stock added successfully', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to add stock', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to add stock', 'error');
        });
    };

    // Bulk stock adjustment
    window.openBulkStockAdjustment = function() {
        Swal.fire({
            title: 'Bulk Stock Adjustment',
            html: `
                <div class="mb-3">
                    <label class="form-label">Adjustment Type</label>
                    <select id="bulkAdjustmentType" class="form-select">
                        <option value="">-- Select Type --</option>
                        <option value="add">Add Stock to All Products</option>
                        <option value="subtract">Remove Stock from All Products</option>
                        <option value="set">Set Stock Level for All Products</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Value</label>
                    <input type="number" id="bulkAdjustmentValue" class="form-control" min="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea id="bulkAdjustmentReason" class="form-control" rows="3" required></textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Apply Bulk Adjustment',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const type = document.getElementById('bulkAdjustmentType').value;
                const value = document.getElementById('bulkAdjustmentValue').value;
                const reason = document.getElementById('bulkAdjustmentReason').value;
                
                if (!type || !value || !reason) {
                    Swal.showValidationMessage('Please fill in all fields');
                    return false;
                }
                
                return fetch('/inventory/bulk-adjust', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        adjustment_type: type,
                        value: value,
                        reason: reason
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Bulk adjustment failed');
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(error.message);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('Success', result.value.message, 'success').then(() => {
                    location.reload();
                });
            }
        });
    };

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const searchValue = e.target.value;
                const url = new URL(window.location);
                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            }
        });
    }

    // Branch filter
    const branchFilter = document.getElementById('branchFilter');
    if (branchFilter) {
        branchFilter.addEventListener('change', function(e) {
            const branchId = e.target.value;
            const url = new URL(window.location);
            if (branchId) {
                url.searchParams.set('branch_id', branchId);
            } else {
                url.searchParams.delete('branch_id');
            }
            window.location.href = url.toString();
        });
    }
});
