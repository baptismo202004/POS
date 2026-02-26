<!-- Minimal Stock Management Filters -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-header bg-white border-0 p-2">
        <div class="d-flex justify-content-between align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdownBtn" data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-1"></i>
                    Filters
                    <span class="badge bg-secondary ms-1" id="activeFiltersCount">0</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 280px;">
                <!-- Stock Level Filters -->
                <li><h6 class="dropdown-header small">Stock Level</h6></li>
                <li>
                    <div class="px-3 py-2">
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" id="filterOutOfStock" value="out_of_stock">
                            <label class="form-check-label small" for="filterOutOfStock">
                                Out of Stock
                            </label>
                        </div>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" id="filterLowStock" value="low_stock">
                            <label class="form-check-label small" for="filterLowStock">
                                Low Stock
                            </label>
                        </div>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" id="filterCriticalStock" value="critical_stock">
                            <label class="form-check-label small" for="filterCriticalStock">
                                Critical Stock
                            </label>
                        </div>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" id="filterInStock" value="in_stock">
                            <label class="form-check-label small" for="filterInStock">
                                In Stock
                            </label>
                        </div>
                        <div class="form-check form-check-sm">
                            <input class="form-check-input" type="checkbox" id="filterOverstock" value="overstock">
                            <label class="form-check-label small" for="filterOverstock">
                                Overstock
                            </label>
                        </div>
                    </div>
                </li>
                
                <li><hr class="dropdown-divider"></li></li>
                
                <!-- Category Filter -->
                <li><h6 class="dropdown-header small">Category</h6></li>
                <li>
                    <div class="px-3 py-2">
                        <select class="form-select form-select-sm" id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="Beverages">Beverages</option>
                            <option value="Snacks">Snacks</option>
                            <option value="Grocery">Grocery</option>
                            <option value="Dairy">Dairy</option>
                            <option value="Bakery">Bakery</option>
                            <option value="Frozen">Frozen</option>
                            <option value="Canned">Canned Goods</option>
                            <option value="Personal Care">Personal Care</option>
                            <option value="Household">Household</option>
                        </select>
                    </div>
                </li>
                
                <li><hr class="dropdown-divider"></li></li>
                
                <!-- Supplier Filter -->
                <li><h6 class="dropdown-header small">Supplier</h6></li>
                <li>
                    <div class="px-3 py-2">
                        <select class="form-select form-select-sm" id="supplierFilter">
                            <option value="">All Suppliers</option>
                            <!-- Suppliers will be loaded dynamically -->
                        </select>
                    </div>
                </li>
                
                <li><hr class="dropdown-divider"></li></li>
                
                <!-- Date-Based Filters -->
                <li><h6 class="dropdown-header small">Date Filters</h6></li>
                <li>
                    <div class="px-3 py-2">
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
                    </div>
                </li>
                
                <li><hr class="dropdown-divider"></li></li>
                
                <!-- Sort Only -->
                <li><h6 class="dropdown-header small">Sort By</h6></li>
                <li>
                    <div class="px-3 py-2">
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
                
                <li><hr class="dropdown-divider"></li></li>
                
                <!-- Action Buttons -->
                <li>
                    <div class="px-3 py-2 d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm flex-fill" id="applyFiltersBtn">
                            Apply
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" id="clearFiltersBtn">
                            Clear
                        </button>
                    </div>
                </li>
            </ul>
        </div>
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
    const activeFiltersCount = document.getElementById('activeFiltersCount');
    const activeFiltersDiv = document.getElementById('activeFilters');
    const filterSummary = document.getElementById('filterSummary');
    const resultCount = document.getElementById('resultCount');
    
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
        // Collect all active filters
        currentFilters.stockLevels = Object.entries(stockLevelFilters)
            .filter(([key, checkbox]) => checkbox.checked)
            .map(([key]) => key);
        
        currentFilters.category = categoryFilter.value;
        currentFilters.supplier = supplierFilter.value;
        currentFilters.dateRange = dateRangeFilter.value;
        currentFilters.movement = movementFilter.value;
        currentFilters.search = searchFilter.value.trim();
        currentFilters.sortBy = sortByFilter.value;
        
        // Update active filters display
        updateActiveFiltersDisplay();
        
        // Apply filters to the table (this would be implemented based on your table structure)
        applyFiltersToTable();
        
        // Update summary
        updateFilterSummary();
    }
    
    function clearAllFilters() {
        // Clear all checkboxes
        Object.values(stockLevelFilters).forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Clear all selects
        categoryFilter.value = '';
        supplierFilter.value = '';
        dateRangeFilter.value = '';
        movementFilter.value = '';
        searchFilter.value = '';
        sortByFilter.value = 'name_asc';
        
        // Reset state
        currentFilters = {
            stockLevels: [],
            category: '',
            supplier: '',
            dateRange: '',
            movement: '',
            search: '',
            sortBy: 'name_asc'
        };
        
        // Update display
        updateActiveFiltersCount();
        updateActiveFiltersDisplay();
        applyFiltersToTable();
        updateFilterSummary();
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
        // Load suppliers from the correct SuperAdmin API route
        fetch('/superadmin/api/suppliers')
            .then(response => response.json())
            .then(data => {
                supplierFilter.innerHTML = '<option value="">All Suppliers</option>';
                data.forEach(supplier => {
                    supplierFilter.innerHTML += `<option value="${supplier.id}">${supplier.name}</option>`;
                });
            })
            .catch(error => console.error('Error loading suppliers:', error));
    }
    
    function debounceSearch() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    }
    
    function applyFiltersToTable() {
        // This function would apply filters to your stock table
        // Implementation depends on your table structure
        console.log('Applying filters:', currentFilters);
        
        // Example: Trigger table reload with filters
        const event = new CustomEvent('applyStockFilters', { detail: currentFilters });
        document.dispatchEvent(event);
    }
});
</script>
