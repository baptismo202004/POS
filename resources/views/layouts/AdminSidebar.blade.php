<aside class="sidebar shadow-sm p-4 d-flex flex-column justify-content-between" id="sidebar">
    <style>
        /* Mobile responsive sidebar - handled by main layout */
        @media (max-width: 767.98px) {
            .sidebar {
                width: 280px;
            }
            
            .sidebar.show {
                /* Handled by main layout */
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Sidebar styles - Electric Modern Palette */
        .sidebar { 
            width: 200px; 
            min-height: 100vh; 
            background: linear-gradient(180deg, #0D47A1 0%, #1565C0 100%);
            border-radius: 0 16px 16px 0;
            padding: 1rem;
            color: #FFFFFF;
            position: relative;
            overflow: hidden;
            overflow-x: hidden;
            font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-weight: 500;
            letter-spacing: -0.01em;
        }
        
        /* Subtle radial glow effect */
        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 100% 0%, rgba(0, 229, 255, 0.15), transparent 60%);
            pointer-events: none;
        }
        
        .sidebar .sidebar-icon { 
            width: 20px; 
            height: 20px; 
            display: block; 
            color: #FFFFFF; 
        }
        
        .icon { 
            width: 20px; 
            height: 20px; 
            color: #FFFFFF; 
        }

        /* Make stroke and fill use currentColor so icons inherit color */
        .sidebar .sidebar-icon path,
        .sidebar .sidebar-icon rect,
        .sidebar .sidebar-icon circle,
        .icon path,
        .icon rect,
        .icon circle { 
            fill: currentColor !important; 
            stroke: none !important; 
        }

        /* Icon badge */
        .icon-badge { 
            width: 32px; 
            height: 32px; 
            border-radius: 8px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: transparent;
            box-shadow: none; 
            padding: 0 !important; 
            border: none;
            transition: all 0.18s ease;
            flex-shrink: 0;
            margin: 0 !important;
        }
        
        /* User dropdown sits at bottom with visual separation */
        .sidebar .dropend { 
            margin-top: auto; 
            margin-bottom: 12px; 
            border-top: 1px solid rgba(255, 255, 255, 0.1); /* Subtle top divider */
            padding-top: 12px; /* Spacing above divider */
            background: rgba(13, 71, 161, 0.05); /* Subtle background change */
        }
        
        /* User dropdown button styling */
        .sidebar .dropdown-toggle {
            background: transparent !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: rgba(255, 255, 255, 0.9) !important;
            transition: all 0.18s ease !important;
        }
        
        .sidebar .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        /* Clear dropdown indicator visibility */
        .sidebar .dropdown-toggle::after {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        /* Sidebar navigation links - visually dominant */
        .sidebar nav a { 
            font-size: 14px; 
            font-weight: 500;
            font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            letter-spacing: -0.01em;
            color: rgba(255, 255, 255, 0.9);
            border-radius: 8px; 
            min-height: 44px;
            padding: 0 10px;
            transition: all 0.18s ease;
            position: relative;
            z-index: 1;
            white-space: normal;
            overflow: visible;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            margin: 0 !important;
        }
        
        /* Force override any Bootstrap margins */
        .sidebar nav a.d-flex {
            margin: 0 !important;
            padding: 0 10px !important;
            gap: 12px !important;
        }
        
        /* Ensure icon badges are consistent */
        .sidebar nav a .icon-badge {
            margin: 0 !important;
            padding: 0 !important;
            flex-shrink: 0;
            width: 32px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Ensure submenu containers don't affect alignment */
        .sidebar nav .submenu {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Override any potential Bootstrap spacing classes */
        .sidebar nav [class*="gap-"] {
            gap: 10px !important;
        }
        
        .sidebar nav [class*="align-items-"] {
            align-items: center !important;
        }
        
        /* Navigation container with improved spacing */
        .sidebar nav {
            padding-bottom: 16px; /* Space before user panel */
        }
        
        .sidebar nav.d-flex.flex-column {
            gap: 4px; /* Reduced gap between menu items */
        }
        
        /* Section spacing - increased vertical spacing */
        .sidebar nav .section-label + .d-flex,
        .sidebar nav .section-label + a {
            margin-top: 8px;
        }
        
        /* Collapsed sidebar readiness */
        .sidebar.collapsed {
            width: 60px; /* Icons only width */
        }
        
        .sidebar.collapsed .section-label {
            display: none; /* Hide section headers in collapsed mode */
        }
        
        .sidebar.collapsed .sidebar nav a span:not(.icon-badge):not(.submenu-indicator) {
            display: none; /* Hide text labels in collapsed mode */
        }
        
        .sidebar.collapsed .submenu {
            display: none; /* Hide submenus in collapsed mode */
        }
        
        /* Tooltip readiness for collapsed mode */
        .sidebar nav a {
            position: relative;
        }
        
        .sidebar nav a::before {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 8px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.18s ease;
            z-index: 1000;
        }
        
        .sidebar.collapsed nav a:hover::before {
            opacity: 1;
        }
        
        /* Sidebar icons - smaller for text focus */
        .sidebar .sidebar-icon {
            width: 18px; /* Reduced from 20px */
            height: 18px; /* Reduced from 20px */
            display: block;
            color: #FFFFFF;
            transition: all 0.18s ease;
        }
        
        /* Submenu indicators - smaller */
        .submenu-indicator {
            transition: transform 0.3s ease;
            opacity: 0.6;
            width: 14px; /* Reduced from 16px */
            height: 14px; /* Reduced from 16px */
            flex-shrink: 0;
        }
        
        .sidebar nav a span { 
            line-height: 1.2; 
            flex: 1;
            white-space: normal;
            overflow: visible;
        }
        
        .sidebar nav a .fw-semibold { 
            font-weight: 700;
        }

        /* Hover state - Background fade with icon color shift */
        .sidebar nav a:hover { 
            color: #FFFFFF; 
            background: rgba(0, 229, 255, 0.15);
            transform: none;
        }
        
        .sidebar nav a:hover .icon-badge {
            background: transparent;
            transform: none;
        }
        
        .sidebar nav a:hover .sidebar-icon {
            color: #00E5FF;
            transform: none;
        }
        
        /* Active state - Immediately identifiable with all indicators */
        .sidebar nav a.active { 
            background: rgba(0, 229, 255, 0.15) !important; /* Soft background highlight */
            color: #00E5FF !important; /* Icon color change */
            border-left: 4px solid #00E5FF !important; /* Left border accent */
            font-weight: 600 !important; /* Increased text weight */
            border-radius: 0 8px 8px 0; /* Adjusted for left border */
            transition: none; /* Instant change - no delay */
        }
        
        .sidebar nav a.active .icon-badge {
            background: transparent;
            border-color: transparent;
        }
        
        .sidebar nav a.active .sidebar-icon {
            color: #00E5FF !important; /* Icon color change */
        }
        
        .sidebar nav a.active span {
            font-weight: 600 !important; /* Increased text weight */
        }
        
        .sidebar nav a.active .submenu-indicator {
            opacity: 1;
            color: #00E5FF !important;
        }

        /* Section label styling - smaller, lighter, uppercase, non-clickable */
        .section-label { 
            font-size: 9px; /* Smaller than menu items */
            font-weight: 600;
            font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            letter-spacing: 0.15em;
            text-transform: uppercase; 
            color: rgba(255, 255, 255, 0.4); /* Lighter color */
            margin: 16px 8px 8px; /* Increased vertical spacing */
            padding: 4px 8px;
            pointer-events: none; /* Non-clickable */
            user-select: none;
        }

        /* User dropdown menu */
        .user-dropdown-menu { 
            min-width: 210px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(13, 71, 161, 0.15); 
            padding: 6px; 
            list-style: none;
            background: #FFFFFF;
            z-index: 1050;
            position: relative;
        }
        
        /* Ensure dropdown button is clickable */
        #sidebarUserDropdown {
            pointer-events: auto !important;
            cursor: pointer !important;
            position: relative;
            z-index: 1051;
        }
        
        #sidebarUserDropdown:hover {
            background: rgba(0, 229, 255, 0.1) !important;
        }
        
        .dropdown-item svg { 
            opacity: 0.95; 
            width: 18px; 
            height: 18px; 
            color: #0D47A1;
        }
        
        .dropdown-item { 
            border-radius: 8px; 
            padding: 8px 12px;
            color: #263238;
        }
        
        .dropdown-item:hover { 
            background: rgba(0, 229, 255, 0.1);
            color: #0D47A1;
        }
        
        .dropdown-toggle .username { 
            font-weight: 700;
            color: #FFFFFF; 
        }
        
        .dropdown-toggle .role { 
            font-size: 12px; 
            color: rgba(255, 255, 255, 0.8);
            margin-left: 2px; 
            opacity: 0.8; 
        }
        
        .dropdown-menu { 
            list-style: none; 
        }
        
        .dropdown-menu li { 
            list-style: none; 
        }

        /* Dropdown toggle button on sidebar */
        .sidebar .dropdown-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
        }
        
        .sidebar .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Submenu styles */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: transparent;
            border-radius: 8px;
            margin: 4px 0;
        }
        
        .submenu.show {
            max-height: 500px;
        }
        
        .submenu a {
            font-size: 13px;
            font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-weight: 500;
            letter-spacing: -0.01em;
            white-space: normal;
            overflow: visible;
            display: flex;
            align-items: center;
            padding: 8px 12px;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.15s ease;
        }
        
        .submenu a:hover {
            color: #FFFFFF;
            background: rgba(0, 229, 255, 0.1);
        }
        
        .submenu .small {
            font-size: 12px;
            font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-weight: 500;
            letter-spacing: -0.01em;
            white-space: normal;
            overflow: visible;
            flex: 1;
            line-height: 1.2;
        }
        
        .submenu-indicator {
            transition: transform 0.3s ease;
            opacity: 0.6;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }
        
        .submenu-indicator.rotated {
            transform: rotate(45deg);
            opacity: 1;
        }
        
        .sidebar nav a:hover .submenu-indicator {
            opacity: 1;
        }

        /* Submenu styling - already defined above */

        /* Mobile menu toggle button */
        .mobile-menu-toggle {
            background: linear-gradient(135deg, #2196F3, #00E5FF) !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4) !important;
        }
        
        .mobile-menu-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.5) !important;
        }

        /* Dropdown divider */
        .dropdown-divider {
            border-color: rgba(13, 71, 161, 0.15);
        }

        /* Text color utilities for dropdown */
        .text-muted {
            color: #6B7280 !important;
        }
    </style>
    
    <div class="d-flex align-items-center justify-content-center mb-3" style="overflow: hidden;">
        <img src="/images/BGH LOGO.png" alt="BGH logo" style="width:180px;height:70px;object-fit:contain;border-radius:8px;max-width:100%;">
    </div>

    <div class="grow" style="overflow-y: auto; overflow-x: hidden;">
        <nav class="d-flex flex-column gap-1" style="width: 100%; overflow: hidden;">
            @php
                $user = auth()->user();
                $isCashier = $user && $user->userType && $user->userType->name === 'Cashier';
                $dashboardRoute = $isCashier ? route('cashier.dashboard') : route('dashboard');
                $dashboardActive = $isCashier ? request()->routeIs('cashier.dashboard') : request()->routeIs('dashboard');
            @endphp
            <a href="{{ $dashboardRoute }}" class="{{ $dashboardActive ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </span>
                <span class="fw-semibold">Dashboard</span>
            </a>
            
            <div class="section-label">OPERATIONS</div>
            
            @canAccess('products','view')
            @php
                $isProductsActive = request()->routeIs('superadmin.products.*') || request()->routeIs('superadmin.categories.*');
            @endphp
            <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isProductsActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('productsMenu', event); return false;">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </span>
                <span>Products</span>
                <svg class="icon ms-auto submenu-indicator" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
            <div class="submenu {{ $isProductsActive ? 'show' : '' }}" id="productsMenu">
                <div class="d-flex flex-column ms-4 mt-1">
                    <a href="{{ route('superadmin.products.index') }}" class="{{ request()->routeIs('superadmin.products.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">All Products</span>
                    </a>
                    <a href="{{ route('superadmin.categories.index') }}" class="{{ request()->routeIs('superadmin.categories.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Product Category</span>
                    </a>
                </div>
            </div>
            @endcanAccess
            
            @canAccess('purchases','view')
            <a href="{{ route('superadmin.purchases.index') }}" class="{{ request()->routeIs('superadmin.purchases.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </span>
                <span>Purchase</span>
            </a>
            @endcanAccess
            
            @canAccess('inventory','view')
            @php
                $isInventoryActive = request()->routeIs('superadmin.inventory.*') || request()->routeIs('superadmin.stockin.*') || request()->routeIs('superadmin.stocktransfer.*');
            @endphp
            <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isInventoryActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('inventoryMenu', event); return false;">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </span>
                <span>Inventory</span>
                <svg class="icon ms-auto submenu-indicator" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
            <div class="submenu {{ $isInventoryActive ? 'show' : '' }}" id="inventoryMenu">
                <div class="d-flex flex-column ms-4 mt-1">
                    <a href="{{ route('superadmin.inventory.index') }}" class="{{ request()->routeIs('superadmin.inventory.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Report</span>
                    </a>
                    <a href="{{ route('superadmin.stockin.index') }}" class="{{ request()->routeIs('superadmin.stockin.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Stock In</span>
                    </a>
                    <a href="{{ route('superadmin.stocktransfer.index') }}" class="{{ request()->routeIs('superadmin.stocktransfer.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Stock Transfer (Branch to Branch)</span>
                    </a>
                </div>
            </div>
            @endcanAccess
            
            @canAccess('sales','view')
            @php
                $isSalesActive = request()->routeIs('superadmin.admin.sales.*');
            @endphp
            <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isSalesActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('salesMenu', event); return false;">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </span>
                <span>Sales</span>
                <svg class="icon ms-auto submenu-indicator" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
            <div class="submenu {{ $isSalesActive ? 'show' : '' }}" id="salesMenu">
                <div class="d-flex flex-column ms-4 mt-1">
                    <a href="{{ route('superadmin.admin.sales.index') }}" class="{{ request()->routeIs('superadmin.admin.sales.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Sales</span>
                    </a>
                    <a href="{{ route('superadmin.admin.refunds.index') }}" class="{{ request()->routeIs('superadmin.admin.refunds.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Refund/Return</span>
                    </a>
                    <a href="{{ route('superadmin.admin.credits.index') }}" class="{{ request()->routeIs('superadmin.admin.credits.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Credit</span>
                    </a>
                   
                </div>
            </div>
            @endcanAccess

            <div class="section-label">MANAGEMENT</div>
            
            {{-- Expenses Link --}}
            <a href="{{ route('superadmin.admin.expenses.index') }}" class="{{ request()->routeIs('superadmin.admin.expenses.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 0h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </span>
                <span>Expenses</span>
            </a>

            @php $isCustomerActive = false; @endphp
            <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isCustomerActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('customerMenu', event); return false;">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </span>
                <span>Customers</span>
                <svg class="icon ms-auto submenu-indicator" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
            <div class="submenu {{ $isCustomerActive ? 'show' : '' }}" id="customerMenu">
                <div class="d-flex flex-column ms-4 mt-1">
                    <a href="{{ route('superadmin.admin.customers.index') }}" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Customers</span></a>
                    <a href="{{ route('superadmin.admin.customers.payment-history') }}" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Payment History</span></a>
                </div>
            </div>

            <div class="section-label">ANALYTICS</div>
            
            <a href="{{ route('superadmin.admin.reports.index') }}" class="{{ request()->routeIs('superadmin.admin.reports.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m0 0V8a2 2 0 012-2h4a2 2 0 012 2v6m0 0V8a2 2 0 012-2h4a2 2 0 012 2v6"></path>
                    </svg>
                </span>
                <span>Reports</span>
            </a>

            <div class="section-label">ADMINISTRATION</div>
            
            @canAccess('user_management','view')
            @php
                $isUserMgmtActive = request()->routeIs('superadmin.admin.users.*') || request()->routeIs('superadmin.admin.access.*');
            @endphp
            <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isUserMgmtActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('userMgmtMenu', event); return false;">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </span>
                <span>Users & Roles</span>
                <svg class="icon ms-auto submenu-indicator" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
            <div class="submenu {{ $isUserMgmtActive ? 'show' : '' }}" id="userMgmtMenu">
                <div class="d-flex flex-column ms-4 mt-1">
                    <a href="{{ route('superadmin.admin.access.index') }}" class="{{ request()->routeIs('superadmin.admin.access.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Access Permission</span>
                    </a>
                    <a href="{{ route('superadmin.admin.users.create') }}" class="{{ request()->routeIs('superadmin.admin.users.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Create Account</span>
                    </a>
                    <a href="{{ route('superadmin.admin.access.logs') }}" class="{{ request()->routeIs('superadmin.admin.access.logs') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Access Logs</span>
                    </a>
                </div>
            </div>
            @endcanAccess

            <div>
                @php
                    $isSettingsActive = request()->routeIs('superadmin.brands.*') || request()->routeIs('superadmin.categories.*') || request()->routeIs('superadmin.unit-types.*') || request()->routeIs('superadmin.branches.*');
                @endphp
                <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isSettingsActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('settingsMenu', event); return false;" id="settingsToggle">
                    <span class="bg-white rounded d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 15c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83l-.01.01a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2h-.02a2 2 0 0 1-2-2v-.09a1.65 1.65 0 0 0-1-1.51"/></svg>
                    </span>
                    <span>Settings</span>
                    <svg class="icon ms-auto submenu-arrow" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="submenu {{ $isSettingsActive ? 'show' : '' }}" id="settingsMenu">
                    <div class="d-flex flex-column ms-4 mt-1">
                        <a href="{{ route('superadmin.branches.index') }}" class="{{ request()->routeIs('superadmin.branches.*') ? 'd-flex align-items-center py-2 text-decoration-none active' : 'd-flex align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Branch</span>
                        </a>
                        <a href="{{ route('superadmin.brands.index') }}" class="{{ request()->routeIs('superadmin.brands.*') ? 'd-flex align-items-center py-2 text-decoration-none active' : 'd-flex align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Brands</span>
                        </a>
                        <a href="{{ route('superadmin.unit-types.index') }}" class="{{ request()->routeIs('superadmin.unit-types.*') ? 'd-flex align-items-center py-2 text-decoration-none active' : 'd-flex align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Unit Types</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    @php
        $sidebarUser = auth()->user();
        $sidebarAvatarUrl = null;
        if ($sidebarUser && !empty($sidebarUser->profile_picture)) {
            $av = $sidebarUser->profile_picture;
            if (\Illuminate\Support\Str::startsWith($av, ['http://', 'https://', '//'])) {
                $sidebarAvatarUrl = $av;
            } elseif (\Illuminate\Support\Str::startsWith($av, 'storage/')) {
                $sidebarAvatarUrl = asset($av);
            } else {
                $candidate = ltrim($av, '/');
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                    $sidebarAvatarUrl = asset('storage/' . $candidate);
                } elseif (file_exists(public_path($candidate))) {
                    $sidebarAvatarUrl = asset($candidate);
                } else {
                    $sidebarAvatarUrl = asset($candidate);
                }
            }
        }
    @endphp

    <!-- HR line above account -->
    <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 0; margin-bottom: 12px;">
    
    <div class="dropdown">
        <button class="d-flex align-items-center gap-2 w-100 text-start p-2" type="button" id="sidebarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.9);">
            @if(!empty($sidebarAvatarUrl))
                <img src="{{ $sidebarAvatarUrl }}" alt="{{ $sidebarUser->name ?? 'User' }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg, #2196F3, #00E5FF);color:#0D47A1;font-weight:700">{{ $sidebarUser ? strtoupper(substr($sidebarUser->name,0,1)) : 'U' }}</div>
            @endif
            <div class="ms-2 text-start">
                <div class="fw-semibold username">{{ $sidebarUser->name ?? 'User' }}</div>
            </div>
        </button>

        <ul class="dropdown-menu user-dropdown-menu" aria-labelledby="sidebarUserDropdown">
            <li class="px-3 py-2">
                <div class="d-flex align-items-center gap-2">
                    @if(!empty($sidebarAvatarUrl))
                        <img src="{{ $sidebarAvatarUrl }}" alt="{{ $sidebarUser->name ?? 'User' }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:linear-gradient(135deg, #2196F3, #00E5FF);color:#0D47A1;font-weight:700">{{ $sidebarUser ? strtoupper(substr($sidebarUser->name,0,1)) : 'U' }}</div>
                    @endif
                    <div class="ms-2">
                        <div class="small text-muted">{{ $sidebarUser->userType->name ?? ($sidebarUser->role ?? 'User') }}</div>
                        <div class="small text-muted">{{ $sidebarUser->email ?? '' }}</div>
                    </div>
                </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}">
                    <svg class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 20v-1c0-2.21 3.58-4 8-4s8 1.79 8 4v1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.edit') }}#password">
                    <svg class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="16" r="1" fill="currentColor"/></svg>
                    <span>Change Password</span>
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                        <svg class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 17l5-5-5-5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 12H9" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 19V5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>

<script>
// Initialize submenu arrows for active menus
document.addEventListener('DOMContentLoaded', function() {
    // Set initial arrow states for active submenus
    document.querySelectorAll('.submenu.show').forEach(submenu => {
        const arrow = submenu.previousElementSibling?.querySelector('.submenu-indicator');
        if (arrow) arrow.classList.add('rotated');
    });
    
    // Initialize Bootstrap dropdowns with explicit configuration
    const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
    dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl, {
            boundary: 'viewport',
            reference: 'toggle',
            display: 'dynamic'
        });
    });
    
    // Specific initialization for user dropdown
    const userDropdown = document.getElementById('sidebarUserDropdown');
    if (userDropdown) {
        new bootstrap.Dropdown(userDropdown, {
            boundary: 'viewport',
            reference: 'toggle',
            display: 'dynamic'
        });
    }
    
    // Initialize mobile sidebar
    initMobileSidebar();
});

// AJAX submenu toggle function
function toggleSubmenu(menuId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const submenu = document.getElementById(menuId);
    const arrow = event.currentTarget.querySelector('.submenu-indicator');
    
    if (submenu.classList.contains('show')) {
        // Close submenu
        submenu.classList.remove('show');
        if (arrow) arrow.classList.remove('rotated');
    } else {
        // Close all other submenus first
        document.querySelectorAll('.submenu').forEach(menu => {
            if (menu.id !== menuId) {
                menu.classList.remove('show');
            }
        });
        
        // Reset all arrows
        document.querySelectorAll('.submenu-indicator').forEach(arr => {
            arr.classList.remove('rotated');
        });
        
        // Open clicked submenu
        submenu.classList.add('show');
        if (arrow) arrow.classList.add('rotated');
    }
}

// Mobile sidebar functionality
function initMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.createElement('div');
    sidebarOverlay.className = 'sidebar-overlay';
    sidebarOverlay.id = 'sidebarOverlay';
    document.body.appendChild(sidebarOverlay);
    
    // Create mobile menu toggle button if it doesn't exist
    if (!document.getElementById('mobileMenuToggle')) {
        const mobileToggle = document.createElement('button');
        mobileToggle.className = 'btn btn-primary d-lg-none mobile-menu-toggle';
        mobileToggle.id = 'mobileMenuToggle';
        mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
        mobileToggle.style.cssText = `
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1060;
            border-radius: 8px;
            padding: 10px 14px;
            border: none;
        `;
        document.body.appendChild(mobileToggle);
        
        // Add toggle functionality
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        });
    }
    
    // Close sidebar when overlay is clicked
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
    });
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    });
}
</script>