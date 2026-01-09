<aside class="sidebar bg-white shadow-sm p-4 d-none d-lg-flex flex-column justify-content-between">
    <style>
        /* Ensure sidebar SVG icons render correctly even if global CSS isn't loaded */
        .sidebar .sidebar-icon { width: 24px; height: 24px; display: block; color: #2563eb; }
        .sidebar .sidebar-icon path,
        .sidebar .sidebar-icon rect,
        .sidebar .sidebar-icon circle { fill: currentColor !important; stroke: none !important; }
        .icon-badge { padding: 0.45rem !important; }
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
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100">
                <span class="bg-white rounded p-2 d-flex align-items-center justify-content-center icon-badge">
                    <svg class="icon sidebar-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 16V8a2 2 0 0 0-1-1.732L12 3 4 6.268A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.732L12 21l8-3.268A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span>Products</span>
            </a>
            <a href="#" class="d-flex gap-3 align-items-center p-3 rounded-lg text-decoration-none text-muted hover:bg-gray-100">
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

    <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-blue-600 text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px">N</div>
        <div>
            <div class="fw-semibold">Nida</div>
            <small class="text-muted">SuperAdmin</small>
        </div>
    </div>
</aside>