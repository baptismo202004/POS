<aside class="sidebar shadow-sm p-4 d-none d-lg-flex flex-column justify-content-between">
    <style>
        /* Ensure sidebar SVG icons render correctly even if global CSS isn't loaded */
        :root { --sidebar-icon-color: #dde4ed; --sidebar-icon-stroke: 1.6; --sidebar-bg: rgba(37,29,80,0.18); --sidebar-active-bg:#251d50; --sidebar-active-text:#dde4ed; --sidebar-link:#dde4ed; --sidebar-link-hover:#ffffff; }
        .sidebar { width:220px; min-height:100vh; background: var(--sidebar-bg); border-radius:16px; box-shadow: 0 10px 30px rgba(0,0,0,0.22); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.08); }
        .sidebar .sidebar-icon { width: 24px; height: 24px; display: block; color: inherit; }
        .icon { width:20px; height:20px; color: inherit; }

        /* Make stroke and fill use currentColor so icons inherit the blue color */
        .sidebar .sidebar-icon path,
        .sidebar .sidebar-icon rect,
        .sidebar .sidebar-icon circle,
        .icon path,
        .icon rect,
        .icon circle { fill: currentColor !important; stroke: none !important; }

        /* Icon badge size and appearance (match dashboard) */
        .icon-badge { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.08); box-shadow:0 8px 20px rgba(15,23,42,0.06); padding:0.45rem !important; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); border:1px solid rgba(255,255,255,0.06); }

        /* Ensure the user dropdown sits at the bottom with some spacing */
        .sidebar .dropend { margin-top: auto; margin-bottom: 16px; }

        /* Normalize sidebar nav text size and weight to match dashboard */
        .sidebar nav a { font-size: 16px; font-weight:500; color: var(--sidebar-link); border-radius:12px; }
        .sidebar nav a span { line-height: 1; }
        .sidebar nav a .fw-semibold { font-weight:600; }

        .sidebar nav a:hover { color: var(--sidebar-link-hover); background: rgba(255,255,255,0.04); }
        .sidebar nav a.active { background: var(--sidebar-active-bg) !important; color: var(--sidebar-active-text) !important; }
        .sidebar nav a.active .icon-badge { background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.18); }

        /* Match dashboard dropdown styles so sidebar dropdown isn't oversized */
        .user-dropdown-menu { min-width:210px; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.08); padding:6px; list-style:none; }
        .dropdown-item svg { opacity:0.95; width:18px; height:18px; }
        .dropdown-item { border-radius:8px; padding:8px 12px; }
        .dropdown-item:hover { background:rgba(255,255,255,0.06); }
        .dropdown-toggle .username { font-weight:600; color:#dde4ed; }
        .dropdown-toggle .role { font-size:12px; color:#dde4ed; margin-left:2px; opacity:0.8; }
        .dropdown-menu { list-style: none; }
        .dropdown-menu li { list-style: none; }

        .dropdown-submenu {
            position: relative;
        }
        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -6px;
            margin-left: -1px;
            -webkit-border-radius: 0 6px 6px 6px;
            -moz-border-radius: 0 6px 6px;
            border-radius: 0 6px 6px 6px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .dropdown-submenu:hover .dropdown-menu {
            display: block;
        }
        .dropdown-submenu a:after {
            display: block;
            content: " ";
            float: right;
            width: 0;
            height: 0;
            border-color: transparent;
            border-style: solid;
            border-width: 5px 0 5px 5px;
            border-left-color: #ccc;
            margin-top: 5px;
            margin-right: -10px;
        }
        .dropdown-submenu:hover a:after {
            border-left-color: #fff;
        }
        .dropdown-submenu.pull-left {
            float: none;
        }
        .dropdown-submenu.pull-left .dropdown-menu {
            left: -100%;
            margin-left: 10px;
            border-radius: 6px 0 6px 6px;
        }

    

    </style>
        <div class="d-flex align-items-center gap-3 mb-4">
            <img src="/images/BGH LOGO.png" alt="BGH logo" style="width:200px;height:80px;object-fit:contain;border-radius:8px;max-width:100%;">
        </div>

            <div class="flex-grow-1" style="overflow-y: auto;">
        <nav class="d-flex flex-column gap-2">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none active' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9.5L12 3l9 6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 22V12h6v10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="fw-semibold">Home</span>
            </a>
            
            @canAccess('products','view')
            <div>
                @php
                    $isProductsActive = request()->routeIs('superadmin.products.*') || request()->routeIs('superadmin.categories.*');
                @endphp
                <a class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none {{ $isProductsActive ? 'active' : '' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#productsMenu" aria-expanded="false" aria-controls="productsMenu">
                    <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.586 2.586a2 2 0 0 0-2.828 0L2 10.172V20h9.828l7.586-7.586a2 2 0 0 0 0-2.828l-7.414-7.414zM6 14a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>
                    </span>
                    <span>Products</span>
                    <svg class="icon ms-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="collapse {{ $isProductsActive ? 'show' : '' }}" id="productsMenu">
                    <div class="d-flex flex-column ms-4 mt-1">
                        <a href="{{ route('superadmin.products.index') }}" class="{{ request()->routeIs('superadmin.products.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                            <span class="small">All Products</span>
                        </a>
                        <a href="{{ route('superadmin.categories.index') }}" class="{{ request()->routeIs('superadmin.categories.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Product Category</span>
                        </a>
                    </div>
                </div>
            </div>
            @endcanAccess
            @canAccess('purchases','view')
            <a href="{{ route('superadmin.purchases.index') }}" class="{{ request()->routeIs('superadmin.purchases.*') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none active' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 6h15l-1.5 9h-12L6 6z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="19" r="1" fill="currentColor"/><circle cx="18" cy="19" r="1" fill="currentColor"/></svg>
                </span>
                <span>Purchase</span>
            </a>
            @endcanAccess
            @canAccess('inventory','view')
            <div>
                @php
                    $isInventoryActive = request()->routeIs('superadmin.inventory.*') || request()->routeIs('superadmin.stockin.*') || request()->routeIs('superadmin.stocktransfer.*');
                @endphp
                    <a class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none {{ $isInventoryActive ? 'active' : '' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#inventoryMenu" aria-expanded="false" aria-controls="inventoryMenu">
                    <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 2H8C6.9 2 6 2.9 6 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-4 18c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H9v-2h6v2z"/></svg>
                    </span>
                    <span>Inventory</span>
                    <svg class="icon ms-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="collapse {{ $isInventoryActive ? 'show' : '' }}" id="inventoryMenu">
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
            </div>
            @endcanAccess
            @canAccess('sales','view')
            <div>
                @php
                    $isSalesActive = request()->routeIs('admin.sales.*');
                @endphp
                <a class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none {{ $isSalesActive ? 'active' : '' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#salesMenu" aria-expanded="false" aria-controls="salesMenu">
                    <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83l-.01.01a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2h-.02a2 2 0 0 1-2-2v-.09a1.65 1.65 0 0 0-1-1.51"/></svg>
                    </span>
                    <span>Sales</span>
                    <svg class="icon ms-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="collapse {{ $isSalesActive ? 'show' : '' }}" id="salesMenu">
                    <div class="d-flex flex-column ms-4 mt-1">
                        <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Sales</span>
                        </a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Sales Report</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Refund/Return</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Credit</span></a>
                    </div>
                </div>
            </div>
            @endcanAccess

            {{-- Expenses Link --}}
            <a href="{{ route('admin.expenses.index') }}" class="{{ request()->routeIs('admin.expenses.*') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none active' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 18h.01" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 14h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Expenses</span>
            </a>

            <div>
                @php $isCustomerActive = false; @endphp
                <a class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none {{ $isCustomerActive ? 'active' : '' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#customerMenu" aria-expanded="false" aria-controls="customerMenu">
                    <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"/><path d="M2 22a8 8 0 0 1 16 0"/></svg>
                    </span>
                    <span>Customer</span>
                    <svg class="icon ms-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="collapse" id="customerMenu">
                    <div class="d-flex flex-column ms-4 mt-1">
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Customers</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Credit Limits</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Payment History</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Aging Reports</span></a>
                    </div>
                </div>
            </div>

            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 3h18v4H3z"/><path d="M3 9h18v12H3z"/></svg>
                </span>
                <span>Report</span>
            </a>

            @canAccess('user_management','view')
            <div>
                @php
                    $isUserMgmtActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.access.*');
                @endphp
                                <a class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none {{ $isUserMgmtActive ? 'active' : '' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#userMgmtMenu" aria-expanded="false" aria-controls="userMgmtMenu">
                    <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 22a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19 10v-2m-1 1h2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <span>User Management</span>
                    <svg class="icon ms-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="collapse {{ $isUserMgmtActive ? 'show' : '' }}" id="userMgmtMenu">
                    <div class="d-flex flex-column ms-4 mt-1">
                        <a href="{{ route('admin.access.index') }}" class="{{ request()->routeIs('admin.access.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Access Permission</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="{{ request()->routeIs('admin.users.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}">
                            <span class="small">Create Account</span>
                        </a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none">
                            <span class="small">Access Logs</span>
                        </a>
                    </div>
                </div>
            </div>
            @endcanAccess

            <div>
                @php
                    $isSettingsActive = request()->routeIs('superadmin.brands.*') || request()->routeIs('superadmin.categories.*') || request()->routeIs('superadmin.unit-types.*') || request()->routeIs('superadmin.branches.*');
                @endphp
                <a class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none {{ $isSettingsActive ? 'active' : '' }}" href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#settingsMenu" aria-expanded="false" aria-controls="settingsMenu">
                    <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                        <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 15c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83l-.01.01a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2h-.02a2 2 0 0 1-2-2v-.09a1.65 1.65 0 0 0-1-1.51"/></svg>
                    </span>
                    <span>Settings</span>
                    <svg class="icon ms-auto" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 9l6 6 6-6"/></svg>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="settingsMenu">
                    <div class="d-flex flex-column ms-4 mt-1">
                        <a href="{{ route('superadmin.branches.index') }}" class="{{ request()->routeIs('superadmin.branches.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}"><span class="small">Branch</span></a>
                        <a href="{{ route('superadmin.brands.index') }}" class="{{ request()->routeIs('superadmin.brands.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}"><span class="small">Brands</span></a>
                        <a href="{{ route('superadmin.unit-types.index') }}" class="{{ request()->routeIs('superadmin.unit-types.*') ? 'd-flex gap-2 align-items-center py-2 text-decoration-none active' : 'd-flex gap-2 align-items-center py-2 text-decoration-none' }}"><span class="small">Unit Types</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Tax</span></a>
                        <a href="#" class="d-flex gap-2 align-items-center py-2 text-decoration-none"><span class="small">Receipt Templates</span></a>
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

    <div class="dropend mt-auto">
        <button class="btn btn-sm btn-white d-flex align-items-center gap-2 p-0" id="sidebarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:transparent;border:none">
            @if(!empty($sidebarAvatarUrl))
                <img src="{{ $sidebarAvatarUrl }}" alt="{{ $sidebarUser->name ?? 'User' }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover">
            @else
                <div class="rounded-circle" style="width:56px;height:56px;background:#251d50;color:#dde4ed;display:flex;align-items:center;justify-content:center">{{ $sidebarUser ? strtoupper(substr($sidebarUser->name,0,1)) : 'U' }}</div>
            @endif
            <div class="ms-2 text-start">
                <div class="fw-semibold" style="color:#dde4ed">{{ $sidebarUser->name ?? 'User' }}</div>
            </div>
            <svg class="icon" width="16" height="16" viewBox="0 0 24 24" style="margin-left:6px"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <ul class="dropdown-menu user-dropdown-menu" aria-labelledby="sidebarUserDropdown">
            <li class="px-3 py-2">
                <div class="d-flex align-items-center gap-2">
                    @if(!empty($sidebarAvatarUrl))
                        <img src="{{ $sidebarAvatarUrl }}" alt="{{ $sidebarUser->name ?? 'User' }}" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
                    @else
                        <div class="rounded-circle bg-blue-600 text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px">{{ $sidebarUser ? strtoupper(substr($sidebarUser->name,0,1)) : 'U' }}</div>
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
            <li class="dropdown-submenu">
                <a class="dropdown-item d-flex align-items-center gap-2 dropdown-toggle" href="#" aria-expanded="false">
                    <svg class="icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 15c1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3 1.3 3 3 3z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83l-.01.01a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2h-.02a2 2 0 0 1-2-2v-.09a1.65 1.65 0 0 0-1-1.51" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Settings</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('superadmin.brands.index') }}">Brands</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.categories.index') }}">Categories</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.product-types.index') }}">Product Types</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.unit-types.index') }}">Unit Types</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.branches.index') }}">Branches</a></li>
                </ul>
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
