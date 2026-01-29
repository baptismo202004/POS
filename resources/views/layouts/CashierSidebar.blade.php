<aside class="sidebar shadow-sm p-4 d-none d-lg-flex flex-column justify-content-between" id="sidebar">
    <style>
        /* Mobile responsive sidebar */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1050;
                border-radius: 0;
                transform: translateX(0);
            }
            
            .sidebar.show {
                transform: translateX(100%);
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }

        @media (min-width: 992px) {
            .sidebar {
                position: relative;
                transform: none !important;
            }
        }

        /* Sidebar styles - Electric Modern Palette */
        .sidebar { 
            width: 220px; 
            min-height: 100vh; 
            background: linear-gradient(180deg, #0D47A1 0%, #1565C0 100%);
            border-radius: 16px; 
            box-shadow: 8px 0 32px rgba(13, 71, 161, 0.2); 
            transition: transform 0.3s ease;
            position: relative;
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
            width: 24px; 
            height: 24px; 
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
            width: 44px; 
            height: 44px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 8px 20px rgba(0, 229, 255, 0.1); 
            padding: 0.45rem !important; 
            backdrop-filter: blur(6px); 
            -webkit-backdrop-filter: blur(6px); 
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* User dropdown sits at bottom */
        .sidebar .dropend { 
            margin-top: auto; 
            margin-bottom: 16px; 
        }

        /* Sidebar navigation links */
        .sidebar nav a { 
            font-size: 16px; 
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            border-radius: 12px; 
            min-height: 44px;
            padding: 12px;
            transition: all 0.3s;
            position: relative;
            z-index: 1;
        }
        
        .sidebar nav a span { 
            line-height: 1; 
        }
        
        .sidebar nav a .fw-semibold { 
            font-weight: 700;
        }

        /* Hover state - Cyan glow */
        .sidebar nav a:hover { 
            color: #FFFFFF; 
            background: rgba(0, 229, 255, 0.2);
            transform: translateX(6px);
        }
        
        /* Active state - Cyan/Neon Blue gradient */
        .sidebar nav a.active { 
            background: linear-gradient(135deg, #00E5FF, #2196F3) !important;
            color: #0D47A1 !important;
            box-shadow: 0 6px 20px rgba(0, 229, 255, 0.4);
        }
        
        .sidebar nav a.active .icon-badge { 
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Section label styling */
        .section-label { 
            font-size: 12px; 
            letter-spacing: 0.08em; 
            text-transform: uppercase; 
            color: rgba(255, 255, 255, 0.6);
            margin: 8px 8px 4px; 
            font-weight: 700;
        }

        /* User dropdown menu */
        .user-dropdown-menu { 
            min-width: 210px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(13, 71, 161, 0.15); 
            padding: 6px; 
            list-style: none;
            background: #FFFFFF;
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
    
    <div class="d-flex align-items-center gap-3 mb-4">
        <img src="/images/BGH LOGO.png" alt="BGH logo" style="width:200px;height:80px;object-fit:contain;border-radius:8px;max-width:100%;">
    </div>

    <div class="grow" style="overflow-y: auto;">
        <nav class="d-flex flex-column gap-2">
            <!-- Cashier Dashboard -->
            <a href="{{ route('cashier.dashboard') }}" class="{{ request()->routeIs('cashier.dashboard') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none active' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 9.5L12 3l9 6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 22V12h6v10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span class="fw-semibold">Dashboard</span>
            </a>
            
            <div class="section-label">Point of Sale</div>
            
            <!-- POS -->
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none active' : 'd-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none' }}">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M6 6h15l-1.5 9h-12L6 6z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="19" r="1" fill="currentColor"/><circle cx="18" cy="19" r="1" fill="currentColor"/></svg>
                </span>
                <span>Point of Sale</span>
            </a>
            
            <!-- Sales -->
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83l-.01.01a2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2h-.02a2 2 0 0 1-2-2v-.09a1.65 1.65 0 0 0-1-1.51"/></svg>
                </span>
                <span>Sales History</span>
            </a>
            
            <!-- Returns/Refunds -->
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M3 7v6h6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 17a9 9 0 00-9-9 9 9 0 00-6 2.3L3 13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Returns/Refunds</span>
            </a>
            
            <div class="section-label">Management</div>
            
            <!-- Customers -->
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10z"/><path d="M2 22a8 8 0 0 1 16 0"/></svg>
                </span>
                <span>Customers</span>
            </a>
            
            <!-- Credit -->
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 4H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 15h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Credit</span>
            </a>
            
            <!-- Branch Inventory -->
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16 2H8C6.9 2 6 2.9 6 4v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm-4 18c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H9v-2h6v2z"/></svg>
                </span>
                <span>Branch Inventory</span>
            </a>
            
            <!-- Expenses -->
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 18h.01" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 14h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Expenses</span>
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

    <div class="dropdown">
        <button class="btn dropdown-toggle d-flex align-items-center gap-2 w-100 text-start p-2 rounded-lg" type="button" id="sidebarUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            @if(!empty($sidebarAvatarUrl))
                <img src="{{ $sidebarAvatarUrl }}" alt="{{ $sidebarUser->name ?? 'User' }}" class="rounded-circle" style="width:32px;height:32px;object-fit:cover">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg, #2196F3, #00E5FF);color:#0D47A1;font-weight:700">{{ $sidebarUser ? strtoupper(substr($sidebarUser->name,0,1)) : 'U' }}</div>
            @endif
            <div class="ms-2 text-start">
                <div class="fw-semibold username">{{ $sidebarUser->name ?? 'User' }}</div>
            </div>
            <svg class="icon" width="16" height="16" viewBox="0 0 24 24" style="margin-left:6px"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
                        <div class="small text-muted">Cashier</div>
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
// Initialize mobile sidebar
document.addEventListener('DOMContentLoaded', function() {
    initMobileSidebar();
});

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
