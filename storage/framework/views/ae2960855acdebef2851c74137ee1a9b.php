<!-- Minimal Stock Management Filters -->
<div class="card mb-3 border-0 shadow-sm">
    <!-- Filter content only (no dropdown wrapper) -->
    <ul class="dropdown-menu dropdown-menu-end show" style="min-width: 320px; position: absolute; right: 0; top: 100%; display: none; background-color: #f8f9fa; border: 1px solid #dee2e6; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); padding: 10px;" id="filterDropdownContent">
                <!-- Two Column Layout -->
                <div class="row g-2">
                    <!-- Left Column -->
                    <div class="col-6">
                        <!-- Stock Level Filters -->
                        <li class="list-unstyled"><h6 class="dropdown-header small mb-2">Stock Level</h6></li>
                        <li class="list-unstyled">
                            <div class="form-check form-check-sm mb-1">
                                <input class="form-check-input" type="checkbox" id="filterOutOfStock" value="out_of_stock">
                                <label class="form-check-label small" for="filterOutOfStock">Out of Stock</label>
                            </div>
                            <div class="form-check form-check-sm mb-1">
                                <input class="form-check-input" type="checkbox" id="filterLowStock" value="low_stock">
                                <label class="form-check-label small" for="filterLowStock">Low Stock</label>
                            </div>
                            <div class="form-check form-check-sm mb-1">
                                <input class="form-check-input" type="checkbox" id="filterCriticalStock" value="critical_stock">
                                <label class="form-check-label small" for="filterCriticalStock">Critical Stock</label>
                            </div>
                            <div class="form-check form-check-sm mb-1">
                                <input class="form-check-input" type="checkbox" id="filterInStock" value="in_stock">
                                <label class="form-check-label small" for="filterInStock">In Stock</label>
                            </div>
                            <div class="form-check form-check-sm mb-1">
                                <input class="form-check-input" type="checkbox" id="filterOverstock" value="overstock">
                                <label class="form-check-label small" for="filterOverstock">Overstock</label>
                            </div>
                        </li>
                        
                        <!-- Date Filters -->
                        <li class="list-unstyled"><h6 class="dropdown-header small mb-2 mt-3">Date Filters</h6></li>
                        <li class="list-unstyled">
                            <div class="mb-2">
                                <label class="form-label small">Date Range</label>
                                <select class="form-select form-select-sm" id="dateRangeFilter">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="quarter">This Quarter</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Stock Movement</label>
                                <select class="form-select form-select-sm" id="movementFilter">
                                    <option value="">All Items</option>
                                    <option value="recently_restocked">Recently Restocked</option>
                                    <option value="no_movement">No Movement (30 days)</option>
                                    <option value="fast_moving">Fast Moving</option>
                                    <option value="slow_moving">Slow Moving</option>
                                </select>
                            </div>
                        </li>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-6">
                        <!-- Category Filter -->
                        <li class="list-unstyled"><h6 class="dropdown-header small mb-2">Category</h6></li>
                        <li class="list-unstyled">
                            <div class="mb-2">
                                <select class="form-select form-select-sm" id="categoryFilter">
                                    <option value="">All Categories</option>
                                    <?php $__currentLoopData = ($categories ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($id); ?>" <?php echo e((string) request('category_id') === (string) $id ? 'selected' : ''); ?>>
                                            <?php echo e($name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </li>
                        
                        <!-- Supplier Filter -->
                        <li class="list-unstyled"><h6 class="dropdown-header small mb-2 mt-3">Supplier</h6></li>
                        <li class="list-unstyled">
                            <div class="mb-2">
                                <select class="form-select form-select-sm" id="supplierFilter">
                                    <option value="">All Suppliers</option>
                                    <?php $__currentLoopData = ($suppliers ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($id); ?>" <?php echo e((string) request('supplier_id') === (string) $id ? 'selected' : ''); ?>>
                                            <?php echo e($name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </li>
                        
                        <!-- Sort Options -->
                        <li class="list-unstyled"><h6 class="dropdown-header small mb-2 mt-3">Sort By</h6></li>
                        <li class="list-unstyled">
                            <div class="mb-2">
                                <select class="form-select form-select-sm" id="sortByFilter">
                                    <option value="name_asc">Name (A-Z)</option>
                                    <option value="name_desc">Name (Z-A)</option>
                                    <option value="quantity_asc">Quantity (Low to High)</option>
                                    <option value="quantity_desc">Quantity (High to Low)</option>
                                    <option value="updated_desc">Recently Updated</option>
                                    <option value="status_asc">Stock Status</option>
                                </select>
                            </div>
                        </li>
                    </div>
                </div>
                
                <!-- Action Buttons (Full Width) -->
                <li class="list-unstyled mt-3">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-fill" id="applyFiltersBtn">Apply</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="clearFiltersBtn">Clear</button>
                    </div>
                </li>
            </ul>
    </div>
    
    <!-- Active Filters Display -->
    <div class="card-body p-2">
        <div id="activeFilters" class="d-flex flex-wrap gap-1 mb-2" style="display: none;">
            <!-- Active filter tags will be displayed here -->
        </div>
        
        <!-- Filter Summary -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted">
                    <span id="filterSummary">Showing all products</span>
                </small>
            </div>
            <div class="col-md-6 text-end">
                <small class="text-muted">
                    <span id="resultCount">0</span> products
                </small>
            </div>
        </div>
    </div>
</div>

<script>
// Stock Management Filters JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const filterDropdownBtn = document.getElementById('filterDropdownBtn');
    const filterDropdownContent = document.getElementById('filterDropdownContent');
    const activeFiltersCount = document.getElementById('activeFiltersCount');
    const activeFiltersDiv = document.getElementById('activeFilters');
    const filterSummary = document.getElementById('filterSummary');
    const resultCount = document.getElementById('resultCount');
    const headerSearchInput = document.getElementById('searchFilterHeader');
    
    // Filter elements
    const stockLevelFilters = {
        outOfStock: document.getElementById('filterOutOfStock'),
        lowStock: document.getElementById('filterLowStock'),
        criticalStock: document.getElementById('filterCriticalStock'),
        inStock: document.getElementById('filterInStock'),
        overstock: document.getElementById('filterOverstock')
    };
    
    const categoryFilter = document.getElementById('categoryFilter');
    const supplierFilter = document.getElementById('supplierFilter');
    const dateRangeFilter = document.getElementById('dateRangeFilter');
    const movementFilter = document.getElementById('movementFilter');
    const searchFilter = document.getElementById('searchFilterHeader'); // Updated to use header search
    const sortByFilter = document.getElementById('sortByFilter');
    
    // Buttons
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    
    // State
    let currentFilters = {
        stockLevels: [],
        category: '',
        supplier: '',
        dateRange: '',
        movement: '',
        search: '',
        sortBy: 'name_asc'
    };
    
    // Load suppliers dynamically
    loadSuppliers();
    
    // Dropdown toggle functionality
    if (filterDropdownBtn && filterDropdownContent) {
        filterDropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isVisible = filterDropdownContent.style.display !== 'none';
            filterDropdownContent.style.display = isVisible ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!filterDropdownBtn.contains(e.target) && !filterDropdownContent.contains(e.target)) {
                filterDropdownContent.style.display = 'none';
            }
        });
        
        // Position dropdown properly relative to button
        function updateDropdownPosition() {
            const buttonRect = filterDropdownBtn.getBoundingClientRect();
            filterDropdownContent.style.top = (buttonRect.bottom + 2) + 'px';
            filterDropdownContent.style.right = '0px';
        }
        
        // Update position when button is clicked
        filterDropdownBtn.addEventListener('click', updateDropdownPosition);
    }
    
    // Event listeners
    Object.values(stockLevelFilters).forEach(checkbox => {
        checkbox.addEventListener('change', updateActiveFiltersCount);
    });
    
    categoryFilter.addEventListener('change', updateActiveFiltersCount);
    supplierFilter.addEventListener('change', updateActiveFiltersCount);
    dateRangeFilter.addEventListener('change', updateActiveFiltersCount);
    movementFilter.addEventListener('change', updateActiveFiltersCount);
    searchFilter.addEventListener('input', debounceSearch);
    sortByFilter.addEventListener('change', updateActiveFiltersCount);
    
    // Add header search functionality
    if (headerSearchInput) {
        headerSearchInput.addEventListener('input', debounceSearch);
    }
    
    applyFiltersBtn.addEventListener('click', applyFilters);
    clearFiltersBtn.addEventListener('click', clearAllFilters);
    
    // Functions
    function updateActiveFiltersCount() {
        let count = 0;
        
        // Count stock level filters
        Object.entries(stockLevelFilters).forEach(([key, checkbox]) => {
            if (checkbox.checked) count++;
        });
        
        // Count other filters
        if (categoryFilter.value) count++;
        if (supplierFilter.value) count++;
        if (dateRangeFilter.value) count++;
        if (movementFilter.value) count++;
        if (searchFilter.value.trim()) count++;
        if (sortByFilter.value !== 'name_asc') count++;
        
        activeFiltersCount.textContent = count;
        activeFiltersCount.style.display = count > 0 ? 'inline' : 'none';
    }
    
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        // Search (header input)
        const searchValue = (headerSearchInput?.value || '').trim();
        if (searchValue) {
            params.set('search', searchValue);
        } else {
            params.delete('search');
        }

        // Category and supplier (IDs)
        if (categoryFilter.value) {
            params.set('category_id', categoryFilter.value);
        } else {
            params.delete('category_id');
        }

        if (supplierFilter.value) {
            params.set('supplier_id', supplierFilter.value);
        } else {
            params.delete('supplier_id');
        }

        // Date range & movement
        if (dateRangeFilter.value) {
            params.set('date_range', dateRangeFilter.value);
        } else {
            params.delete('date_range');
        }

        if (movementFilter.value === 'recently_restocked' || movementFilter.value === 'no_movement') {
            params.set('movement', movementFilter.value);
        } else {
            params.delete('movement');
        }

        // Stock levels (multi)
        Array.from(params.keys())
            .filter(k => k === 'stock_levels' || k.startsWith('stock_levels['))
            .forEach(k => params.delete(k));

        const selectedLevels = Object.values(stockLevelFilters)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        selectedLevels.forEach(level => {
            params.append('stock_levels[]', level);
        });

        // Sort mapping
        const sortValue = sortByFilter.value;
        let sortBy = null;
        let sortDirection = null;

        switch (sortValue) {
            case 'name_asc':
                sortBy = 'product_name';
                sortDirection = 'asc';
                break;
            case 'name_desc':
                sortBy = 'product_name';
                sortDirection = 'desc';
                break;
            case 'quantity_asc':
                sortBy = 'current_stock';
                sortDirection = 'asc';
                break;
            case 'quantity_desc':
                sortBy = 'current_stock';
                sortDirection = 'desc';
                break;
            case 'updated_desc':
                sortBy = 'last_updated';
                sortDirection = 'desc';
                break;
            case 'status_asc':
                sortBy = 'stock_level';
                sortDirection = 'asc';
                break;
        }

        if (sortBy && sortDirection) {
            params.set('sort_by', sortBy);
            params.set('sort_direction', sortDirection);
        } else {
            params.delete('sort_by');
            params.delete('sort_direction');
        }

        const baseUrl = window.stockManagementUrl || window.location.pathname;
        const query = params.toString();
        window.location = query ? `${baseUrl}?${query}` : baseUrl;
    }
    
    function clearAllFilters() {
        // Reset UI controls
        Object.values(stockLevelFilters).forEach(checkbox => {
            checkbox.checked = false;
        });
        categoryFilter.value = '';
        supplierFilter.value = '';
        dateRangeFilter.value = '';
        movementFilter.value = '';
        if (headerSearchInput) {
            headerSearchInput.value = '';
        }
        sortByFilter.value = 'name_asc';

        // Clear related query parameters and redirect
        const params = new URLSearchParams(window.location.search);

        ['search', 'category_id', 'supplier_id', 'date_range', 'movement', 'sort_by', 'sort_direction']
            .forEach(key => params.delete(key));

        Array.from(params.keys())
            .filter(k => k === 'stock_levels' || k.startsWith('stock_levels['))
            .forEach(k => params.delete(k));

        const baseUrl = window.stockManagementUrl || window.location.pathname;
        const query = params.toString();
        window.location = query ? `${baseUrl}?${query}` : baseUrl;
    }
    
    function updateActiveFiltersDisplay() {
        const tags = [];
        
        // Add stock level tags
        currentFilters.stockLevels.forEach(level => {
            const labels = {
                out_of_stock: 'Out of Stock',
                low_stock: 'Low Stock',
                critical_stock: 'Critical',
                in_stock: 'In Stock',
                overstock: 'Overstock'
            };
            
            if (labels[level]) {
                tags.push(`<span class="badge bg-secondary me-1">${labels[level]}</span>`);
            }
        });
        
        // Add other filter tags
        if (currentFilters.category) {
            tags.push(`<span class="badge bg-info me-1">Category: ${currentFilters.category}</span>`);
        }
        if (currentFilters.supplier) {
            tags.push(`<span class="badge bg-primary me-1">Supplier: ${currentFilters.supplier}</span>`);
        }
        if (currentFilters.search) {
            tags.push(`<span class="badge bg-dark me-1">Search: ${currentFilters.search}</span>`);
        }
        
        activeFiltersDiv.innerHTML = tags.join('');
        activeFiltersDiv.style.display = tags.length > 0 ? 'flex' : 'none';
    }
    
    function updateFilterSummary() {
        const activeCount = currentFilters.stockLevels.length + 
                          (currentFilters.category ? 1 : 0) + 
                          (currentFilters.supplier ? 1 : 0) + 
                          (currentFilters.search ? 1 : 0);
        
        if (activeCount === 0) {
            filterSummary.textContent = 'Showing all products';
        } else {
            filterSummary.textContent = `Filtered by ${activeCount} criteria`;
        }
    }
    
    function loadSuppliers() {
        // Options are rendered server-side in the Blade template; no dynamic loading needed here.
        return;
    }
    
    function debounceSearch() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => {
            // Update search from both inputs
            const searchValue = (searchFilter?.value || headerSearchInput?.value || '').trim();
            currentFilters.search = searchValue;
            applyFiltersToTable();
        }, 300);
    }
    
    function applyFiltersToTable() {
        console.log('Applying filters:', currentFilters);
        
        // Get all table rows
        const tableRows = document.querySelectorAll('table tbody tr');
        let visibleCount = 0;
        let hasAnyData = tableRows.length > 0;
        
        tableRows.forEach(row => {
            let showRow = true;
            
            // Get cell values for filtering
            const productName = row.cells[0]?.textContent.toLowerCase() || '';
            const categoryName = row.cells[1]?.textContent.toLowerCase() || '';
            const currentStock = parseInt(row.cells[2]?.textContent) || 0;
            
            // Apply search filter
            if (currentFilters.search) {
                const searchTerm = currentFilters.search.toLowerCase();
                if (!productName.includes(searchTerm) && !categoryName.includes(searchTerm)) {
                    showRow = false;
                }
            }
            
            // Apply category filter
            if (currentFilters.category && showRow) {
                if (categoryName !== currentFilters.category.toLowerCase()) {
                    showRow = false;
                }
            }
            
            // Apply stock level filters
            if (currentFilters.stockLevels.length > 0 && showRow) {
                const hasStockLevel = currentFilters.stockLevels.some(level => {
                    switch (level) {
                        case 'out_of_stock':
                            return currentStock <= 0;
                        case 'low_stock':
                            return currentStock > 0 && currentStock <= 5;
                        case 'critical_stock':
                            return currentStock > 0 && currentStock <= 3;
                        case 'in_stock':
                            return currentStock > 0;
                        case 'overstock':
                            return currentStock > 100;
                        default:
                            return false;
                    }
                });
                
                if (!hasStockLevel) {
                    showRow = false;
                }
            }
            
            // Show/hide row
            row.style.display = showRow ? '' : 'none';
            if (showRow) {
                visibleCount++;
            }
        });
        
        // Handle no data found / nothing follows messages
        const tbody = document.querySelector('table tbody');
        
        // Remove existing message rows
        const existingMessages = tbody.querySelectorAll('.no-data-message');
        existingMessages.forEach(msg => msg.remove());
        
        if (visibleCount === 0) {
            // Create message row
            const messageRow = document.createElement('tr');
            messageRow.className = 'no-data-message';
            
            const messageCell = document.createElement('td');
            messageCell.colSpan = '10'; // Span all columns
            messageCell.className = 'text-center py-4 text-muted';
            
            if (!hasAnyData) {
                messageCell.innerHTML = '<i class="fas fa-inbox me-2"></i>No data found';
            } else {
                messageCell.innerHTML = '<i class="fas fa-search me-2"></i>Nothing follows';
            }
            
            messageRow.appendChild(messageCell);
            tbody.appendChild(messageRow);
        }
        
        // Update result count
        if (resultCount) {
            resultCount.textContent = visibleCount;
        }
    }
});
</script>
<?php /**PATH C:\xampp\htdocs\POS\resources\views/superadmin/inventory/stock-filters.blade.php ENDPATH**/ ?>