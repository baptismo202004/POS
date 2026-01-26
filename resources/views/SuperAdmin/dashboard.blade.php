@extends('layouts.app')
@section('title', 'Dashboard')

@push('stylesDashboard')
    <style>
        body { font-family: 'Inter', sans-serif;}
        .sidebar { width: 220px; }
        .dash-header { font-size: 28px; font-weight:700; }
        .search-input { border-radius: 999px; padding-left: 44px; padding-right: 1rem; }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9aa6b2; }
        .stat-card { border-radius: 14px; padding: 18px; display:flex; align-items:center; justify-content:space-between; }
        .stat-icon { width:56px;height:56px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.6); }
        .card-soft { background:#eef5ff; border-radius:12px; }
        .panel { background:#f1f5f9; border-radius:12px; padding:18px; }
        .small-circle { width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#e3f2ff;color:#1e3a8a;font-weight:600 }
        .bottom-avatar { position:fixed; left:32px; bottom:24px; display:flex; flex-direction:column; align-items:center; gap:6px }
        .ring { width:72px;height:72px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#2b8af9 var(--pct), rgba(0,0,0,0.06) 0); }
        .ring-inner { width:56px;height:56px;border-radius:50%;background:#fff;display:grid;place-items:center }

        /* User dropdown aesthetics */
        :root { --icon-color: #2563eb; --icon-muted: #60a5fa; --icon-stroke: 1.6; }
        .icon { width:20px; height:20px; color:var(--icon-color); opacity:0.98; }
        .icon path, .icon rect { stroke:currentColor; fill:none; stroke-width:var(--icon-stroke); stroke-linecap:round; stroke-linejoin:round; }
        .icon circle { fill: currentColor; }
        .icon-badge { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 8px 20px rgba(15,23,42,0.06); }
        .user-avatar { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3b82f6,#7c3aed);color:#fff;font-weight:700;box-shadow:0 6px 14px rgba(59,130,246,0.12); }
        .user-dropdown-menu { min-width:210px;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,0.08);padding:6px; }
        .dropdown-item svg { opacity:0.95; width:18px;height:18px; }
        .dropdown-item { border-radius:8px; padding:8px 12px; }
        .dropdown-item:hover { background:#f8fafc; }
        .dropdown-toggle .username { font-weight:600;color:#111827; }
        .dropdown-toggle .role { font-size:12px;color:var(--icon-muted);margin-left:2px; }
        .caret-icon { opacity:0.75; color:var(--icon-muted); }
        /* (dark mode removed) */
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <!-- Topbar -->
    <div class="bg-white border rounded-3 p-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <div class="position-relative d-flex align-items-center">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-4.35-4.35" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="11" cy="11" r="6" stroke="#94a3b8" stroke-width="1.5"/></svg>
                <input class="form-control search-input" placeholder="Search..." />
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <select class="form-select form-select-sm" style="margin-right:0.50rem;">
                <option>Last 7 days</option>
                <option>Last 30 days</option>
            </select>
        </div>
    </div>

    <div class="row gx-4 gy-4">
        <div class="col-lg-8">
            <div class="d-flex gap-3 mb-3">
                <div class="flex-fill stat-card" style="background:linear-gradient(135deg,#f5e5ff,#f9f0ff);">
                    <div>
                        <div class="text-muted small">Sales</div>
                        <div class="fw-bold display-6">78</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="8" width="4" height="13" rx="1" fill="#5B21B6"/><rect x="9" y="4" width="4" height="17" rx="1" fill="#7C3AED"/><rect x="15" y="11" width="4" height="10" rx="1" fill="#C084FC"/></svg>
                    </div>
                </div>
                <div class="flex-fill stat-card" style="background:linear-gradient(135deg,#e6fff2,#e8ffef);">
                    <div>
                        <div class="text-muted small">Expenses</div>
                        <div class="fw-bold display-6">78</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="9" width="4" height="12" rx="1" fill="#059669"/><rect x="9" y="5" width="4" height="16" rx="1" fill="#10B981"/><rect x="15" y="11" width="4" height="10" rx="1" fill="#34D399"/></svg>
                    </div>
                </div>
                <div class="flex-fill stat-card" style="background:linear-gradient(135deg,#f3f4f6,#ffffff);">
                    <div>
                        <div class="text-muted small">Revenue</div>
                        <div class="fw-bold display-6">80%</div>
                    </div>
                    <div class="stat-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="13" width="4" height="8" rx="1" fill="#6B7280"/><rect x="9" y="9" width="4" height="12" rx="1" fill="#9CA3AF"/><rect x="15" y="5" width="4" height="16" rx="1" fill="#6B7280"/></svg>
                    </div>
                </div>
            </div>
            <div class="card mb-3 p-3 panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="fw-semibold">Branch Performance</div>
                </div>
                <div class="d-flex gap-4">
                    <div class="text-center">
                        <div class="ring" style="--pct:70deg"><div class="ring-inner">70%</div></div>
                        <div class="small text-muted mt-2">MCS</div>
                    </div>
                    <div class="text-center">
                        <div class="ring" style="--pct:324deg"><div class="ring-inner">90%</div></div>
                        <div class="small text-muted mt-2">RK</div>
                    </div>
                    <div class="text-center">
                        <div class="ring" style="--pct:180deg"><div class="ring-inner">50%</div></div>
                        <div class="small text-muted mt-2">Branch 1</div>
                    </div>
                </div>
            </div>

            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold">Sales Overview</div>
                    <div>
                        <select id="sales-range-select" class="form-select form-select-sm" style="width:160px">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                        </select>
                    </div>
                </div>
                <div style="height:240px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3 p-3 panel">
                <div class="fw-semibold mb-2">Top Selling Products</div>
                <ul class="list-unstyled">
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>Mighty Red</div>
                        <div class="text-success">99.99%</div>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>NVR</div>
                        <div class="text-success">50%</div>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>Memory</div>
                        <div class="text-success">32%</div>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-2">
                        <div>Database</div>
                        <div class="text-success">10%</div>
                    </li>
                </ul>
            </div>

            <div class="card p-3" style="min-height:220px">
                <div class="fw-semibold mb-3">Recent Sales</div>
                <div class="text-muted">No recent sales to show</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                datasets: [
                    { label: 'Store A', data:[12,19,7,14,18,10,16], borderColor:'#ef4444', tension:0.4, fill:false },
                    { label: 'Store B', data:[10,14,22,12,11,24,18], borderColor:'#3b82f6', tension:0.4, fill:false },
                    { label: 'Store C', data:[8,6,5,18,9,12,10], borderColor:'#f59e0b', tension:0.4, fill:false },
                ]
            },
            options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}} }
        });
    }
</script>

@if(session('success') && request()->query('from') !== 'login')
<script>
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 2200
    });
</script>
@endif
@endpush