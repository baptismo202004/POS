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
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Sidebar styles - Electric Modern Palette */
        .sidebar { 
            width: 260px; 
            min-height: 100vh; 
            background: linear-gradient(180deg, #0D47A1 0%, #1565C0 100%);
            color: white;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        /* Subtle radial glow effect */
        .sidebar::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            z-index: -2;
        }
        
        /* Extended background container */
        .sidebar-bg-extension {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, #0D47A1 0%, #1565C0 100%);
            z-index: -3;
        }

        .sidebar .sidebar-icon { 
            width: 20px; 
            height: 20px; 
            display: block; 
            flex-shrink: 0;
        }

        /* Make stroke and fill use currentColor so icons inherit color */
        .sidebar .sidebar-icon path,
        .sidebar .sidebar-icon rect,
        .sidebar .sidebar-icon circle,
        .icon path,
        .icon rect,
        .icon circle { 
            stroke: currentColor; 
            fill: currentColor; 
        }

        .sidebar .sidebar-icon svg { 
            width: 100%; 
            height: 100%; 
        }

        /* User dropdown sits at bottom with visual separation */
        .sidebar .dropend { 
            margin-top: auto; 
            margin-bottom: 12px; 
            border-top: 1px solid rgba(255, 255, 255, 0.1); /* Subtle top divider */
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
            padding: 0 6px; /* minimal padding */
            transition: all 0.18s ease;
            position: relative;
            z-index: 1;
            white-space: nowrap; /* prevent text wrapping */
            overflow: hidden; /* contain text within background */
            display: flex;
            align-items: center;
            gap: 6px; /* minimal gap */
            cursor: pointer;
            margin: 0 !important;
            box-sizing: border-box; /* ensure padding doesn't affect total width */
            width: 100%; /* use full available width */
        }
        
        /* Force override any Bootstrap margins */
        .sidebar nav a.d-flex {
            margin: 0 !important;
            padding: 0 6px !important;
            gap: 6px !important;
            box-sizing: border-box !important;
            width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
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
        
        /* Click animation state */
        .sidebar nav a.clicking {
            animation: navClickAnimation 0.6s ease-out;
            position: relative;
            z-index: 10;
        }
        
        @keyframes navClickAnimation {
            0% {
                transform: scale(1) translateX(0);
                background: rgba(0, 229, 255, 0.15);
                box-shadow: 0 0 0 rgba(0, 229, 255, 0);
            }
            20% {
                transform: scale(0.92) translateX(-2px);
                background: rgba(0, 229, 255, 0.4);
                box-shadow: 0 0 20px rgba(0, 229, 255, 0.3);
            }
            40% {
                transform: scale(0.95) translateX(2px);
                background: rgba(0, 229, 255, 0.3);
                box-shadow: 0 0 30px rgba(0, 229, 255, 0.4);
            }
            60% {
                transform: scale(0.98) translateX(-1px);
                background: rgba(0, 229, 255, 0.25);
                box-shadow: 0 0 25px rgba(0, 229, 255, 0.2);
            }
            80% {
                transform: scale(0.99) translateX(0);
                background: rgba(0, 229, 255, 0.2);
                box-shadow: 0 0 15px rgba(0, 229, 255, 0.1);
            }
            100% {
                transform: scale(1) translateX(0);
                background: rgba(0, 229, 255, 0.15);
                box-shadow: 0 0 0 rgba(0, 229, 255, 0);
            }
        }
        
        /* Ripple effect */
        .sidebar nav a::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
            pointer-events: none;
            z-index: 0;
        }
        
        .sidebar nav a.clicking::after {
            width: 100%;
            height: 100%;
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

        /* User dropdown menu styling to match Admin sidebar */
        .user-dropdown-menu { 
            min-width: 280px; 
            max-width: 320px;
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(13, 71, 161, 0.15); 
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
    </style>

    <div class="sidebar-bg-extension"></div>

    <!-- Logo/Brand Section -->
    <div class="d-flex align-items-center justify-content-center mb-3" style="overflow: hidden;">
        <img src="/images/BGH LOGO.png" alt="BGH logo" style="width:180px;height:70px;object-fit:contain;border-radius:8px;max-width:100%;">
    </div>

    <!-- Navigation -->
    <div class="grow" style="overflow-y: auto; overflow-x: hidden;">
        <nav class="d-flex flex-column gap-1" style="width: 100%; overflow: hidden;">
            <!-- Dashboard Section -->
            <a href="{{ route('cashier.dashboard') }}" class="{{ request()->routeIs('cashier.dashboard') ? 'd-flex align-items-center rounded-lg text-decoration-none active' : 'd-flex align-items-center rounded-lg text-decoration-none' }}">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-tachometer-alt sidebar-icon"></i>
                </span>
                <span class="fw-semibold">Dashboard</span>
            </a>

            <!-- Products Section -->
            @canAccess('products','view')
            <div class="section-label">PRODUCTS</div>
            
            <a href="{{ route('cashier.products.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-box sidebar-icon"></i>
                </span>
                <span>Products</span>
            </a>
            
            @canAccess('product_category','edit')
            <a href="{{ route('cashier.categories.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-tags sidebar-icon"></i>
                </span>
                <span>Product Category</span>
            </a>
            @endcanAccess
            @endcanAccess

            <!-- Inventory Section -->
            @canAccess('inventory','view')
            <div class="section-label">INVENTORY</div>
            
            @canAccess('inventory','edit')
            <a href="{{ route('cashier.purchases.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-shopping-bag sidebar-icon"></i>
                </span>
                <span>Purchase</span>
            </a>
            
            <a href="{{ route('cashier.stockin.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-sign-in-alt sidebar-icon"></i>
                </span>
                <span>Stock In</span>
            </a>
            @endcanAccess
            
            <a href="{{ route('cashier.inventory.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-box sidebar-icon"></i>
                </span>
                <span>Inventory</span>
            </a>

            @canAccess('stock_management','view')
            <a href="{{ route('cashier.stock-management.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-warehouse sidebar-icon"></i>
                </span>
                <span>Stock Management</span>
            </a>
            @endcanAccess
            
            @canAccess('stock_transfer','view')
            <a href="#" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-exchange-alt sidebar-icon"></i>
                </span>
                <span>Stock Transfer</span>
            </a>
            @endcanAccess
            @endcanAccess

            <!-- Sales Section -->
            @canAccess('sales','view')
            <div class="section-label">SALES</div>
            
            <a href="{{ route('cashier.sales.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-shopping-cart sidebar-icon"></i>
                </span>
                <span>Sales</span>
            </a>
            
            @canAccess('sales_report','view')
            <a href="{{ route('cashier.sales.reports') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-file-invoice-dollar sidebar-icon"></i>
                </span>
                <span>Sales Reports</span>
            </a>
            @endcanAccess
            
            @canAccess('sales','edit')
            <a href="{{ route('cashier.refunds.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-undo sidebar-icon"></i>
                </span>
                <span>Refund/Return</span>
            </a>
            @endcanAccess
            @endcanAccess

            <!-- Finance Section -->
            @canAccess('credit','view')
            <div class="section-label">FINANCE</div>
            
            <a href="{{ route('cashier.credit.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-credit-card sidebar-icon"></i>
                </span>
                <span>Credit</span>
            </a>
            
            @canAccess('expenses','view')
            <a href="{{ route('cashier.expenses.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-file-invoice sidebar-icon"></i>
                </span>
                <span>Expenses</span>
            </a>
            @endcanAccess
            @endcanAccess

            <!-- Customer Section -->
            @canAccess('customer','view')
            <div class="section-label">CUSTOMER</div>
            
            <a href="{{ route('cashier.customers.index') }}" class="d-flex align-items-center rounded-lg text-decoration-none">
                <span class="bg-transparent rounded d-flex align-items-center justify-content-center icon-badge">
                    <i class="fas fa-users sidebar-icon"></i>
                </span>
                <span>Customers</span>
            </a>
            @endcanAccess

                    </nav>
    </div>

    @php
        $cashierUser = auth()->user();
        $cashierAvatarUrl = null;
        if ($cashierUser && !empty($cashierUser->profile_picture)) {
            $av = $cashierUser->profile_picture;
            if (\Illuminate\Support\Str::startsWith($av, ['http://', 'https://', '//'])) {
                $cashierAvatarUrl = $av;
            } elseif (\Illuminate\Support\Str::startsWith($av, 'storage/')) {
                $cashierAvatarUrl = asset($av);
            } else {
                $candidate = ltrim($av, '/');
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                    $cashierAvatarUrl = asset('storage/' . $candidate);
                } elseif (file_exists(public_path($candidate))) {
                    $cashierAvatarUrl = asset($candidate);
                } else {
                    $cashierAvatarUrl = asset($candidate);
                }
            }
        }
    @endphp

    <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 0; margin-bottom: 12px;">

    <div class="dropdown">
        <button class="d-flex align-items-center gap-2 w-100 text-start p-2" type="button" id="sidebarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.9);">
            @if(!empty($cashierAvatarUrl))
                <img src="{{ $cashierAvatarUrl }}" alt="{{ $cashierUser->name ?? 'Cashier' }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg, #2196F3, #00E5FF);color:#0D47A1;font-weight:700">
                    {{ $cashierUser ? strtoupper(substr($cashierUser->name,0,1)) : 'C' }}
                </div>
            @endif
            <div class="ms-2 text-start">
                <div class="fw-semibold username">{{ $cashierUser->name ?? 'Cashier' }}</div>
            </div>
        </button>

        <ul class="dropdown-menu user-dropdown-menu" aria-labelledby="sidebarUserDropdown">
            <li class="px-3 py-2">
                <div class="d-flex align-items-center gap-2">
                    @if(!empty($cashierAvatarUrl))
                        <img src="{{ $cashierAvatarUrl }}" alt="{{ $cashierUser->name ?? 'Cashier' }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;background:linear-gradient(135deg, #2196F3, #00E5FF);color:#0D47A1;font-weight:700">
                            {{ $cashierUser ? strtoupper(substr($cashierUser->name,0,1)) : 'C' }}
                        </div>
                    @endif
                    <div class="ms-2">
                        <div class="small text-muted">{{ $cashierUser->userType->name ?? ($cashierUser->role ?? 'Cashier') }}</div>
                        <div class="small text-muted">{{ $cashierUser->email ?? '' }}</div>
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
                <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile.password') }}">
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
document.addEventListener('DOMContentLoaded', function() {
    // Add click animations to all navigation links
    const navLinks = document.querySelectorAll('.sidebar nav a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't animate if it's already active or has no href
            if (this.classList.contains('active') || !this.getAttribute('href') || this.getAttribute('href') === '#') {
                return;
            }
            
            // Prevent immediate navigation
            e.preventDefault();
            
            // Store the original href
            const href = this.getAttribute('href');
            
            // Add clicking class for animation
            this.classList.add('clicking');
            
            // Add a subtle loading state before navigation
            const icon = this.querySelector('.sidebar-icon');
            if (icon) {
                icon.style.opacity = '0.7';
                // Add spinning animation to icon
                icon.style.animation = 'spin 0.8s linear';
            }
            
            // Add pulse effect to the text
            const textSpan = this.querySelector('span:not(.icon-badge)');
            if (textSpan) {
                textSpan.style.animation = 'pulse 0.6s ease-in-out';
            }
            
            // Navigate after animation completes (600ms delay)
            setTimeout(() => {
                window.location.href = href;
            }, 600);
        });
    });
    
    // Add spin and pulse animation keyframes if not already present
    if (!document.querySelector('#navAnimations')) {
        const style = document.createElement('style');
        style.id = 'navAnimations';
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.6; transform: scale(1.05); }
                100% { opacity: 1; transform: scale(1); }
            }
        `;
        document.head.appendChild(style);
    }

    // Initialize user account dropdown explicitly (Bootstrap)
    const userDropdown = document.getElementById('sidebarUserDropdown');
    if (userDropdown && typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
        new bootstrap.Dropdown(userDropdown, {
            boundary: 'viewport',
            reference: 'toggle',
            display: 'dynamic'
        });
    }
});
</script>
