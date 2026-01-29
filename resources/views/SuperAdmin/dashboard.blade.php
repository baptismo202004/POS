@extends('layouts.app')
@section('title', 'Dashboard')

@push('stylesDashboard')
    <style>
        body { font-family: 'Inter', sans-serif;}
        .sidebar { width: 220px; }
        .dash-header { font-size: 28px; font-weight:700; }
        .search-input { border-radius: 999px; padding-left: 44px; padding-right: 1rem; border: 1px solid #e2e8f0; transition: all 0.3s ease; }
        .search-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); outline: none; }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9aa6b2; }
        .stat-card { border-radius: 14px; padding: 20px; display:flex; flex-direction: column; align-items:flex-start; justify-content:space-between; transition: all 0.3s ease; cursor: pointer; background-color: #fff; border: 1px solid #e2e8f0; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .stat-icon { width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.7); transition: all 0.3s ease; align-self: flex-end; }
        .stat-card:hover .stat-icon { transform: scale(1.1); background: rgba(255,255,255,0.9); }
        .card-soft { background:#eef5ff; border-radius:12px; }
        .panel { background-color: #fff; border: 1px solid #e2e8f0; border-radius:12px; padding:24px; transition: all 0.3s ease; }
        .panel:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
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
        .dropdown-item { border-radius:8px; padding:8px 12px; transition: all 0.2s ease; }
        .dropdown-item:hover { background:#f8fafc; transform: translateX(2px); }
        .dropdown-toggle .username { font-weight:600;color:#111827; }
        .dropdown-toggle .role { font-size:12px;color:var(--icon-muted);margin-left:2px; }
        .caret-icon { opacity:0.75; color:var(--icon-muted); }
        /* (dark mode removed) */
    </style>
@endpush
    @section('content')
    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="position-relative d-flex align-items-center">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 21l-4.35-4.35" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="11" cy="11" r="6" stroke="#94a3b8" stroke-width="1.5"/></svg>
                <input class="form-control search-input" placeholder="Search..." />
            </div>
            <div>
                <select id="sales-range-select" class="form-select form-select-sm" style="width:160px">
                    <option>Last 7 days</option>
                    <option>Last 30 days</option>
                </select>
            </div>
        </div>
        <div class="row gy-4">
            <div class="col-12">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="flex-fill stat-card">
                            <div class="text-muted small">Sales</div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="fw-bold display-6">78</div>
                                <div class="stat-icon" style="background-color: #f3e8ff;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="8" width="4" height="13" rx="1" fill="#5B21B6"/><rect x="9" y="4" width="4" height="17" rx="1" fill="#7C3AED"/><rect x="15" y="11" width="4" height="10" rx="1" fill="#C084FC"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="flex-fill stat-card">
                            <div class="text-muted small">Expenses</div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="fw-bold display-6">78</div>
                                <div class="stat-icon" style="background-color: #dcfce7;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="9" width="4" height="12" rx="1" fill="#059669"/><rect x="9" y="5" width="4" height="16" rx="1" fill="#10B981"/><rect x="15" y="11" width="4" height="10" rx="1" fill="#34D399"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="flex-fill stat-card">
                            <div class="text-muted small">Revenue</div>
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="fw-bold display-6">80%</div>
                                <div class="stat-icon" style="background-color: #f1f5f9;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="13" width="4" height="8" rx="1" fill="#6B7280"/><rect x="9" y="9" width="4" height="12" rx="1" fill="#9CA3AF"/><rect x="15" y="5" width="4" height="16" rx="1" fill="#6B7280"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="fw-semibold fs-5">Branch Performance</div>
                        <div class="text-muted small">Updated 2 hours ago</div>
                    </div>
                    <div class="d-flex gap-4 justify-content-center">
                        <div class="text-center">
                            <div class="ring" style="--pct:70deg"><div class="ring-inner">70%</div></div>
                            <div class="small text-muted mt-2">MCS</div>
                            <div class="small fw-semibold text-success">+12%</div>
                        </div>
                        <div class="text-center">
                            <div class="ring" style="--pct:324deg"><div class="ring-inner">90%</div></div>
                            <div class="small text-muted mt-2">RK</div>
                            <div class="small fw-semibold text-success">+8%</div>
                        </div>
                        <div class="text-center">
                            <div class="ring" style="--pct:180deg"><div class="ring-inner">50%</div></div>
                            <div class="small text-muted mt-2">Branch 1</div>
                            <div class="small fw-semibold text-warning">-3%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="fw-semibold fs-5">Sales Overview</div>
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
                    <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                        <div class="small text-muted">Total Sales: <span class="fw-semibold text-dark">₱125,430</span></div>
                        <div class="small text-muted">Average: <span class="fw-semibold text-dark">₱17,918</span></div>
                    </div>
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
      options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } } }
    });
  }
</script>
@if(session('success') && request()->query('from') !== 'login')
@push('scripts')
<script>
  Swal.fire({ toast:true, position:'top-end', icon:'success', title:@json(session('success')), showConfirmButton:false, timer:2200 });
</script>
@endpush
@endif
@endpush

</body>
</html>
