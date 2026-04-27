<aside class="sidebar shadow-sm p-4 d-flex flex-column justify-content-between" id="sidebar">
    <style>
        /* Mobile responsive sidebar - handled by main layout */
        @media (max-width: 767.98px) {
            .sidebar {
                width: 320px;
                padding: 1rem 0.5rem 1rem 0.5rem; /* minimal padding */
                box-sizing: border-box;
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

        /* Sidebar styles - match CashierSidebar blue mesh/blob design */
        .sidebar { 
            width: 270px;
            min-height: 100vh;
            background: #0a1628;
            color: white;
            position: relative;
            z-index: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border-top-right-radius: 28px;
            border-bottom-right-radius: 28px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            font-family: 'Inter', 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-weight: 500;
            letter-spacing: -0.01em;
            box-sizing: border-box;
        }

        /* Animated mesh background */
        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 160% 120% at 10% 0%, #1a3a6e 0%, transparent 55%),
                radial-gradient(ellipse 120% 100% at 90% 100%, #0d2b5e 0%, transparent 60%),
                radial-gradient(ellipse 80% 60% at 50% 50%, #0f2044 0%, transparent 70%);
            z-index: -2;
        }

        /* Extended background container */
        .sidebar-bg-extension {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: transparent;
            z-index: -3;
        }

        /* Decorative blob shapes */
        .sidebar-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.18;
            pointer-events: none;
            z-index: -1;
        }
        .sidebar-blob-1 {
            width: 200px;
            height: 200px;
            background: #00c6ff;
            top: -60px;
            left: -60px;
            animation: blobFloat1 8s ease-in-out infinite;
        }
        .sidebar-blob-2 {
            width: 160px;
            height: 160px;
            background: #7b2ff7;
            bottom: 120px;
            right: -50px;
            animation: blobFloat2 10s ease-in-out infinite;
        }
        .sidebar-blob-3 {
            width: 120px;
            height: 120px;
            background: #00e5ff;
            top: 45%;
            left: 20%;
            animation: blobFloat3 12s ease-in-out infinite;
        }
        @keyframes blobFloat1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(20px, 30px) scale(1.1); }
        }
        @keyframes blobFloat2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-15px, -25px) scale(1.08); }
        }
        @keyframes blobFloat3 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(10px, -15px) scale(0.95); }
            66% { transform: translate(-8px, 10px) scale(1.05); }
        }

        /* Diagonal accent stripe */
        .sidebar-stripe {
            position: absolute;
            width: 400px;
            height: 8px;
            background: linear-gradient(90deg, transparent, rgba(0, 229, 255, 0.25), transparent);
            top: 155px;
            left: -60px;
            transform: rotate(-8deg);
            pointer-events: none;
            z-index: 0;
        }

        /* Logo area */
        .sidebar-logo-wrap {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 16px 0 8px;
        }

        /* Logo bottom separator */
        .sidebar-wave-divider {
            display: block;
            width: 100%;
            line-height: 0;
            position: relative;
            z-index: 2;
        }
        .sidebar-wave-divider svg {
            width: 100%;
            height: 18px;
            display: block;
        }

        /* Scroll container */
        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 4px 12px 12px;
            position: relative;
            z-index: 2;
            scrollbar-width: thin;
            scrollbar-color: rgba(0,229,255,0.2) transparent;
        }
        .sidebar-scroll::-webkit-scrollbar { width: 3px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(0,229,255,0.25); border-radius: 4px; }
        
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
            width: 30px;
            height: 30px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.07);
            transition: all 0.22s ease;
            margin: 0 !important;
            padding: 0 !important;
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

        /* Sidebar navigation links */
        .sidebar nav a { 
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            text-decoration: none;
            color: rgba(255,255,255,0.72);
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'Nunito', sans-serif;
            position: relative;
            transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
            border: 1px solid transparent;
            min-height: 44px;
            width: 100%;
            box-sizing: border-box;
            margin: 0 !important;
        }

        .sidebar nav a::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255,255,255,0.06) 50%, transparent 70%);
            transform: translateX(-100%);
            transition: transform 0.4s ease;
            border-radius: inherit;
        }
        .sidebar nav a:hover::before { transform: translateX(100%); }
        
        /* Force override any Bootstrap margins */
        .sidebar nav a.d-flex {
            margin: 0 !important;
            padding: 10px 12px !important;
            gap: 10px !important;
            box-sizing: border-box !important;
            width: 100% !important;
            overflow: hidden !important;
        }
        
        /* Ensure icon badges are consistent */
        .sidebar nav a .icon-badge {
            margin: 0 !important;
            padding: 0 !important;
            flex-shrink: 0;
            width: 30px;
            height: 30px;
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

        /* Hover state */
        .sidebar nav a:hover { 
            color: #ffffff;
            background: rgba(0, 198, 255, 0.12);
            border-color: rgba(0, 229, 255, 0.15);
            transform: translateX(4px);
        }
        .sidebar nav a:hover .icon-badge { background: rgba(0, 229, 255, 0.15); }
        .sidebar nav a:hover .sidebar-icon { color: #00e5ff; }
        
        /* Active state */
        .sidebar nav a.active { 
            background: linear-gradient(135deg, rgba(0,198,255,0.22), rgba(0,100,200,0.18)) !important;
            color: #00e5ff !important;
            border-color: rgba(0, 229, 255, 0.35) !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 20px rgba(0, 198, 255, 0.15), inset 0 0 0 1px rgba(0,229,255,0.1);
            transform: translateX(3px);
            border-left: none !important;
            border-radius: 14px;
        }

        .sidebar nav a.active::after {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, #00c6ff, #00e5ff, #7b2ff7);
            border-radius: 0 3px 3px 0;
            box-shadow: 0 0 10px rgba(0,229,255,0.8);
        }
        
        .sidebar nav a.active .icon-badge {
            background: rgba(0, 229, 255, 0.2);
            box-shadow: 0 0 12px rgba(0, 229, 255, 0.3);
            border-color: transparent;
        }
        
        .sidebar nav a.active .sidebar-icon {
            color: #00E5FF !important; /* Icon color change */
        }
        
        .sidebar nav a.active span {
            font-weight: 700 !important;
        }
        
        .sidebar nav a.active .submenu-indicator {
            opacity: 1;
            color: #00E5FF !important;
        }

        /* User dropdown menu */
        .user-dropdown-menu { 
            min-width: 280px; 
            max-width: 320px;
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(13, 71, 161, 0.15); 
            background: #FFFFFF;
            z-index: 99999 !important;
            position: fixed !important;
            padding: 8px;
            border: 1px solid rgba(0,0,0,0.08);
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

        /* Section label */
        .section-label { 
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(0, 229, 255, 0.45);
            margin: 18px 4px 6px;
            padding: 0 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: none;
            user-select: none;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(0,229,255,0.3), transparent);
            border-radius: 2px;
        }

        /* Bottom wave */
        .sidebar-bottom-wave {
            position: relative;
            z-index: 2;
            line-height: 0;
        }
        .sidebar-bottom-wave svg { width: 100%; height: 20px; display: block; }

        /* User panel */
        .sidebar-user-panel {
            position: relative;
            z-index: 2;
            padding: 6px 12px 16px;
        }

        /* Keep existing settings submenu arrow aligned to the Cashier submenu indicators */
        .submenu-arrow {
            transition: transform 0.3s ease;
            opacity: 0.6;
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        /* Suppress all transitions on initial page load to prevent submenu flash */
        .sidebar-no-transition *,
        .sidebar-no-transition *::before,
        .sidebar-no-transition *::after {
            transition: none !important;
            animation: none !important;
        }
    </style>

    <div class="sidebar-bg-extension"></div>
    <div class="sidebar-blob sidebar-blob-1"></div>
    <div class="sidebar-blob sidebar-blob-2"></div>
    <div class="sidebar-blob sidebar-blob-3"></div>
    <div class="sidebar-stripe"></div>

    <!-- Logo/Brand Section -->
    <div class="sidebar-logo-wrap">
        <div class="d-flex align-items-center justify-content-center" style="overflow: hidden;">
            <img src="/images/BGH LOGO.png" alt="BGH logo" style="width:180px;height:70px;object-fit:contain;border-radius:8px;max-width:100%;">
        </div>
    </div>

    <div class="sidebar-wave-divider">
        <svg viewBox="0 0 270 18" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,9 C45,18 90,0 135,9 C180,18 225,0 270,9 L270,18 L0,18 Z" fill="rgba(0,229,255,0.07)"/>
            <path d="M0,12 C45,20 90,4 135,12 C180,20 225,4 270,12" fill="none" stroke="rgba(0,229,255,0.18)" stroke-width="1"/>
        </svg>
    </div>

    <div class="sidebar-scroll">
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
                $isProductsActive = request()->routeIs('superadmin.products.*');
            @endphp
            <a href="{{ route('superadmin.products.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none {{ $isProductsActive ? 'active' : '' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </span>
                <span>Products</span>
            </a>
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
            <a href="{{ route('superadmin.stockin.index') }}" class="{{ request()->routeIs('superadmin.stockin.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11 16V7.85l-2.6 2.6L7 9l5-5 5 5-1.4 1.45-2.6-2.6V16h-2zm-7 4v-5h2v3h12v-3h2v5H4z"/>
                    </svg>
                </span>
                <span>Stock In</span>
            </a>
            @endcanAccess
            
            @canAccess('inventory','view')
            <a href="{{ route('superadmin.inventory.stock-management') }}" class="{{ request()->routeIs('superadmin.inventory.stock-management') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </span>
                <span>Stock Management</span>
            </a>
            @endcanAccess
            
            @canAccess('sales','view')
            @php
                $isSalesActive = request()->routeIs('admin.sales.*') || request()->routeIs('admin.main.sales.*') || request()->routeIs('admin.refunds.*') || request()->routeIs('admin.credits.*');
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
                    <a href="{{ route('admin.sales.management.index') }}" class="{{ request()->routeIs('admin.sales.management.*') || request()->routeIs('admin.main.sales.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Sales</span>
                    </a>
                    <a href="{{ route('admin.refunds.index') }}" class="{{ request()->routeIs('admin.refunds.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Refund/Return</span>
                    </a>
                    <a href="{{ route('admin.credits.index') }}" class="{{ request()->routeIs('admin.credits.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Credit</span>
                    </a>
                   
                </div>
            </div>
            @endcanAccess

            <div class="section-label">MANAGEMENT</div>
            
            {{-- Expenses Link --}}
            <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 0h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
                    </svg>
                </span>
                <span>Expenses</span>
            </a>

            {{-- Suppliers Link --}}
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </span>
                <span>Suppliers</span>
            </a>

            @php
                $isCustomerActive = str_starts_with(request()->route()?->getName() ?? '', 'admin.customers.');
            @endphp
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
                    <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.index') || request()->routeIs('admin.customers.show') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}"><span class="small">Customers</span></a>
                    <a href="{{ route('admin.customers.payment-history') }}" class="{{ request()->routeIs('admin.customers.payment-history') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}"><span class="small">Payment History</span></a>
                </div>
            </div>

            <div class="section-label">ANALYTICS</div>
            
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v6m0 0V8a2 2 0 012-2h4a2 2 0 012 2v6m0 0V8a2 2 0 012-2h4a2 2 0 012 2v6"></path>
                    </svg>
                </span>
                <span>Reports</span>
            </a>

            <a href="{{ route('superadmin.inventory.index') }}" class="{{ request()->routeIs('superadmin.inventory.index') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </span>
                <span>Inventory Overview</span>
            </a>

            @php $isSuperAdmin = in_array(optional(auth()->user()->userType)->name, config('rbac.super_roles', [])); @endphp
            @if($isSuperAdmin)

            <div class="section-label">ADMINISTRATION</div>

            @php
                $isUserMgmtActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.access.*') || request()->routeIs('admin.roles.*');
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
                    <a href="{{ route('admin.access.index') }}" class="{{ request()->routeIs('admin.access.index') || request()->routeIs('admin.access.store') || request()->routeIs('admin.access.users.*') || request()->routeIs('admin.access.permissions.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Access Permission</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Create Account</span>
                    </a>
                    <a href="{{ route('admin.access.logs') }}" class="{{ request()->routeIs('admin.access.logs') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Access Logs</span>
                    </a>
                </div>
            </div>
            @endif

            @php
                $isSettingsActive = request()->routeIs('superadmin.brands.*') || request()->routeIs('superadmin.categories.*') || request()->routeIs('superadmin.unit-types.*') || request()->routeIs('superadmin.branches.*');
            @endphp
            <a class="d-flex align-items-center rounded-lg text-decoration-none {{ $isSettingsActive ? 'active' : '' }}" href="#" onclick="toggleSubmenu('settingsMenu', event); return false;">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 15.5A3.5 3.5 0 0 1 8.5 12 3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5 3.5 3.5 0 0 1-3.5 3.5m7.43-2.92c.04-.34.07-.68.07-1.08s-.03-.74-.07-1.08l2.32-1.82c.21-.16.27-.46.13-.7l-2.2-3.82c-.13-.24-.42-.32-.66-.24l-2.74 1.1c-.57-.44-1.18-.8-1.86-1.08L14.5 2.42C14.46 2.18 14.24 2 14 2h-4c-.24 0-.46.18-.49.42L9.13 5.36C8.45 5.64 7.84 6 7.27 6.44L4.53 5.34c-.24-.09-.53 0-.66.24L1.67 9.4c-.14.23-.08.54.13.7l2.32 1.82C4.08 12.26 4.05 12.6 4.05 13s.03.74.07 1.08L1.8 15.9c-.21.16-.27.46-.13.7l2.2 3.82c.13.24.42.32.66.24l2.74-1.1c.57.44 1.18.8 1.86 1.08l.38 2.91c.03.24.25.42.49.42h4c.24 0 .46-.18.49-.42l.38-2.91c.68-.28 1.29-.64 1.86-1.08l2.74 1.1c.24.09.53 0 .66-.24l2.2-3.82c.14-.24.08-.54-.13-.7l-2.32-1.82z"/>
                    </svg>
                </span>
                <span>Settings</span>
                <svg class="icon ms-auto submenu-indicator" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
            <div class="submenu {{ $isSettingsActive ? 'show' : '' }}" id="settingsMenu">
                <div class="d-flex flex-column ms-4 mt-1">
                    <a href="{{ route('superadmin.brands.index') }}" class="{{ request()->routeIs('superadmin.brands.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Brands</span>
                    </a>
                    <a href="{{ route('superadmin.branches.index') }}" class="{{ request()->routeIs('superadmin.branches.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Branch</span>
                    </a>
                    <a href="{{ route('superadmin.unit-types.index') }}" class="{{ request()->routeIs('superadmin.unit-types.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Unit Types</span>
                    </a>
                    <a href="{{ route('superadmin.categories.index') }}" class="{{ request()->routeIs('superadmin.categories.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                        <span class="small">Product Category</span>
                    </a>
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

    <div class="sidebar-bottom-wave">
        <svg viewBox="0 0 270 20" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,10 C45,0 90,20 135,10 C180,0 225,20 270,10 L270,0 L0,0 Z" fill="rgba(0,229,255,0.06)"/>
            <path d="M0,10 C45,0 90,20 135,10 C180,0 225,20 270,10" fill="none" stroke="rgba(0,229,255,0.18)" stroke-width="1"/>
        </svg>
    </div>

    <div class="sidebar-user-panel">
    <div class="dropdown dropup">
        <button class="d-flex align-items-center gap-2 w-100 text-start p-2" type="button" id="sidebarUserDropdown" aria-expanded="false" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.9);">
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
    </div>
</aside>

<script>
// Initialize submenu arrows for active menus
document.addEventListener('DOMContentLoaded', function() {
    // Suppress transitions on initial load so active submenus open instantly (no flash/animation on load)
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.add('sidebar-no-transition');
        // Re-enable transitions after first paint
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                sidebar.classList.remove('sidebar-no-transition');
            });
        });
    }

    // Set initial arrow states for active submenus
    document.querySelectorAll('.submenu.show').forEach(submenu => {
        // Walk backwards through siblings to find the nearest <a> toggle
        let sibling = submenu.previousElementSibling;
        while (sibling) {
            const arrow = sibling.querySelector('.submenu-indicator');
            if (arrow) { arrow.classList.add('rotated'); break; }
            if (sibling.tagName === 'A') break;
            sibling = sibling.previousElementSibling;
        }
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
    
    // Specific initialization for user dropdown — portal approach so no parent overflow clips it
    const userDropdown = document.getElementById('sidebarUserDropdown');
    if (userDropdown) {
        const dropdownMenu = userDropdown.closest('.dropdown').querySelector('.user-dropdown-menu');

        // Move the dropdown menu to document.body so it escapes all overflow contexts
        if (dropdownMenu) {
            document.body.appendChild(dropdownMenu);
            dropdownMenu.style.display = 'none';
        }

        userDropdown.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = dropdownMenu.classList.contains('dropdown-open');

            if (isOpen) {
                dropdownMenu.classList.remove('dropdown-open');
                dropdownMenu.style.display = 'none';
            } else {
                // Position relative to the button
                const rect = userDropdown.getBoundingClientRect();
                dropdownMenu.style.position  = 'fixed';
                dropdownMenu.style.zIndex    = '99999';
                dropdownMenu.style.display   = 'block';
                dropdownMenu.style.left      = rect.left + 'px';
                // Open upward
                dropdownMenu.style.bottom    = (window.innerHeight - rect.top + 4) + 'px';
                dropdownMenu.style.top       = 'auto';
                dropdownMenu.classList.add('dropdown-open');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!userDropdown.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('dropdown-open');
                dropdownMenu.style.display = 'none';
            }
        });

        // Reposition on scroll/resize
        window.addEventListener('resize', function () {
            if (dropdownMenu.classList.contains('dropdown-open')) {
                const rect = userDropdown.getBoundingClientRect();
                dropdownMenu.style.left   = rect.left + 'px';
                dropdownMenu.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
            }
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