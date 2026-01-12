<aside class="sidebar bg-white shadow-sm p-4 d-none d-lg-flex flex-column justify-content-between">
    <style>
        /* Ensure sidebar SVG icons render correctly even if global CSS isn't loaded */
        :root { --sidebar-icon-color: #2563eb; --sidebar-icon-stroke: 1.6; }
        .sidebar { width:220px; min-height:100vh; }
        .sidebar .sidebar-icon { width: 24px; height: 24px; display: block; color: var(--sidebar-icon-color); }
        .icon { width:20px; height:20px; color: var(--sidebar-icon-color); }

        /* Make stroke and fill use currentColor so icons inherit the blue color */
        .sidebar .sidebar-icon path,
        .sidebar .sidebar-icon rect,
        .sidebar .sidebar-icon circle,
        .icon path,
        .icon rect,
        .icon circle { fill: currentColor !important; stroke: none !important; }

        /* Icon badge size and appearance (match dashboard) */
        .icon-badge { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 8px 20px rgba(15,23,42,0.06); padding:0.45rem !important; }

        /* Ensure the user dropdown sits at the bottom with some spacing */
        .sidebar .dropend { margin-top: auto; margin-bottom: 16px; }

        /* Normalize sidebar nav text size and weight to match dashboard */
        .sidebar nav a { font-size: 16px; font-weight:500; color: #374151; }
        .sidebar nav a span { line-height: 1; }
        .sidebar nav a .fw-semibold { font-weight:600; }

        /* Match dashboard dropdown styles so sidebar dropdown isn't oversized */
        .user-dropdown-menu { min-width:210px; border-radius:12px; box-shadow:0 10px 30px rgba(15,23,42,0.08); padding:6px; }
        .dropdown-item svg { opacity:0.95; width:18px; height:18px; }
        .dropdown-item { border-radius:8px; padding:8px 12px; }
        .dropdown-item:hover { background:#f8fafc; }
        .dropdown-toggle .username { font-weight:600; color:#111827; }
        .dropdown-toggle .role { font-size:12px; color:#60a5fa; margin-left:2px; }

        /* Nested dropdown styles */
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
            -webkit-border-radius: 6px 0 6px 6px;
            -moz-border-radius: 6px 0 6px 6px;
            border-radius: 6px 0 6px 6px;
        }
    </style>
    <div>
        <div class="d-flex align-items-center gap-3 mb-5">
            <img src="/images/BGH LOGO.png" alt="BGH logo" style="width:200px;height:80px;object-fit:contain;border-radius:8px;max-width:100%;">
        </div>

        <nav class="d-flex flex-column gap-2">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-dark bg-indigo-50' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9.5L12 3l9 6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 22V12h6v10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="fw-semibold">Home</span>
            </a>
            
            </a>
            <a href="{{ route('superadmin.products.index') }}" class="{{ request()->routeIs('superadmin.products.*') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-dark bg-indigo-50' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 16V8a2 2 0 0 0-1-1.732L12 3 4 6.268A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.732L12 21l8-3.268A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Products</span>
            </a>
            <a href="{{ route('superadmin.purchases.index') }}" class="{{ request()->routeIs('superadmin.purchases.*') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-dark bg-indigo-50' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 6h15l-1.5 9h-12L6 6z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="19" r="1" fill="currentColor"/><circle cx="18" cy="19" r="1" fill="currentColor"/></svg>
                </span>
                <span>Purchase</span>
            </a>
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 16V8a2 2 0 0 0-1-1.732L12 3 4 6.268A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.732L12 21l8-3.268A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Inventory</span>
            </a>
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83l-.01.01a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2h-.02a2 2 0 0 1-2-2v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0l-.01-.01a2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2v-.02a2 2 0 0 1 2-2h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82L4.8 5.5a2 2 0 0 1 0-2.83L4.81 2.7a2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H10a1.65 1.65 0 0 0 1 1.51V5a2 2 0 0 1 2 2v.09c0 .44.16.86.46 1.18a1.65 1.65 0 0 0 .96 1.23c.53.28 1.13.28 1.66 0 .36-.2.68-.46.96-.77.36-.41.56-.88.56-1.38V7a2 2 0 0 1 2-2h.02a2 2 0 0 1 2 2v.09c0 .5.2.98.56 1.38.28.31.6.57.96.77.53.28 1.13.28 1.66 0 .39-.22.7-.58.96-.96.23-.34.46-.68.7-1.01z" stroke="currentColor" stroke-width="1.0" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Settings</span>
            </a>
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

    <div class="dropend">
        <button class="btn btn-sm btn-white d-flex align-items-center gap-2 p-0" id="sidebarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background:transparent;border:none">
            @if(!empty($sidebarAvatarUrl))
                <img src="{{ $sidebarAvatarUrl }}" alt="{{ $sidebarUser->name ?? 'User' }}" class="rounded-circle" style="width:56px;height:56px;object-fit:cover">
            @else
                <div class="rounded-circle bg-blue-600 text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px">{{ $sidebarUser ? strtoupper(substr($sidebarUser->name,0,1)) : 'U' }}</div>
            @endif
            <div class="ms-2 text-start">
                <div class="fw-semibold">{{ $sidebarUser->name ?? 'User' }}</div>
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
<!-- dark mode removed -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle submenu positioning to stay within viewport
    const submenus = document.querySelectorAll('.dropdown-submenu .dropdown-menu');
    
    submenus.forEach(function(submenu) {
        const parent = submenu.parentElement;
        
        parent.addEventListener('mouseenter', function() {
            const rect = submenu.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const windowWidth = window.innerWidth;
            
            // Reset any previous adjustments
            submenu.style.maxHeight = '80vh';
            submenu.style.overflowY = 'auto';
            
            // Check if submenu goes beyond right edge
            if (rect.right > windowWidth) {
                submenu.style.left = '-100%';
                submenu.style.marginLeft = '10px';
            } else {
                submenu.style.left = '100%';
                submenu.style.marginLeft = '-1px';
            }
            
            // Check if submenu goes beyond bottom edge
            if (rect.bottom > windowHeight) {
                submenu.style.top = 'auto';
                submenu.style.bottom = '0';
                submenu.style.maxHeight = (windowHeight - rect.top) + 'px';
            } else {
                submenu.style.top = '0';
                submenu.style.bottom = 'auto';
            }
        });
    });
});
</script>