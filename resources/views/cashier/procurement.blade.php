@extends('layouts.app')
@section('title', 'Procurement Needs')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{--navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--green:#10b981;--red:#ef4444;--amber:#f59e0b;--bg:#EBF3FB;--card:#fff;--border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;}
body{background:var(--bg);font-family:'Segoe UI',sans-serif;}
.sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
.sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
.sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
.sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;}
.sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;}
.sp-wrap{position:relative;z-index:1;padding:22px 24px 56px;}
.sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:12px;}
.sp-ph-left{display:flex;align-items:center;gap:13px;}
.sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#b45309,var(--amber));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(180,83,9,0.28);}
.sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;}
.sp-ph-title{font-size:22px;font-weight:900;color:var(--navy);line-height:1.1;}
.sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}
.proc-banner{background:linear-gradient(135deg,rgba(180,83,9,0.08),rgba(245,158,11,0.06));border:1.5px solid rgba(245,158,11,0.3);border-radius:14px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.proc-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:18px;}
.proc-kpi{background:var(--card);border-radius:13px;border:1px solid var(--border);padding:13px 15px;box-shadow:0 1px 8px rgba(13,71,161,0.05);position:relative;overflow:hidden;}
.proc-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
.proc-kpi.r::before{background:var(--red);}
.proc-kpi.a::before{background:var(--amber);}
.proc-kpi.b::before{background:var(--blue);}
.proc-kpi.g::before{background:var(--green);}
.proc-kpi-label{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;}
.proc-kpi-value{font-size:20px;font-weight:900;color:var(--navy);}
.proc-kpi-sub{font-size:10.5px;color:var(--muted);margin-top:2px;}
.proc-sa{display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .15s;}
.proc-sa:hover{transform:translateY(-1px);}
.proc-sa.red{background:rgba(239,68,68,0.1);color:#b91c1c;border:1px solid rgba(239,68,68,0.2);}
.proc-sa.amber{background:rgba(245,158,11,0.1);color:#b45309;border:1px solid rgba(245,158,11,0.2);}
.proc-sa.blue{background:rgba(25,118,210,0.1);color:#1565c0;border:1px solid rgba(25,118,210,0.2);}
.proc-sa.green{background:rgba(16,185,129,0.1);color:#065f46;border:1px solid rgba(16,185,129,0.2);}
.proc-sa-count{font-size:16px;font-weight:900;}
.sp-card{background:var(--card);border-radius:18px;border:1px solid var(--border);box-shadow:0 4px 24px rgba(13,71,161,0.08);overflow:hidden;}
.sp-card-head{padding:13px 20px;background:linear-gradient(135deg,var(--navy),var(--blue));display:flex;align-items:center;justify-content:space-between;}
.sp-card-head-title{font-size:14px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;}
.sp-card-head-title i{color:rgba(0,229,255,.85);}
.sp-c-badge{background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;}
.sp-search-wrap{position:relative;}
.sp-search-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;z-index:2;}
.sp-search-input{padding:8px 13px 8px 34px;border-radius:10px;border:1.5px solid var(--border);font-size:13px;background:var(--card);color:var(--text);outline:none;width:220px;}
.sp-table-wrap{overflow-x:auto;}
.sp-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;}
.sp-table thead th{background:rgba(13,71,161,0.03);padding:10px 15px;font-size:11px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
.sp-table tbody td{padding:12px 15px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
.sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.45);}
.sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}
.sp-table tbody tr:last-child td{border-bottom:none;}
.bp{display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.bp-red{background:rgba(239,68,68,0.12);color:#dc2626;}
.bp-amber{background:rgba(245,158,11,0.12);color:#d97706;}
.bp-green{background:rgba(16,185,129,0.12);color:#059669;}
.bp-blue{background:rgba(25,118,210,0.12);color:#1565c0;}
.proc-bar-wrap{height:6px;border-radius:4px;background:rgba(13,71,161,0.08);overflow:hidden;min-width:80px;}
.proc-bar{height:100%;border-radius:4px;}
</style>
@endpush

@section('content')
<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <main class="flex-fill p-4" style="position:relative;z-index:1;">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-truck-loading"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Inventory · My Branch</div>
                        <div class="sp-ph-title">Procurement Needs</div>
                        <div class="sp-ph-sub">Items ordered but not yet fulfilled — needs purchasing to complete</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                    <form method="GET" action="{{ route('cashier.procurement') }}" class="d-flex gap-2 align-items-center">
                        <div class="sp-search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="sp-search-input" placeholder="Search product, supplier..." value="{{ $search }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Search</button>
                        @if($search)
                            <a href="{{ route('cashier.procurement') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                        @endif
                    </form>
                    <a href="{{ route('cashier.stock-management.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-boxes-stacked me-1"></i> Stock Management
                    </a>
                    <a href="{{ route('cashier.purchases.create') }}" class="btn btn-warning btn-sm fw-bold">
                        <i class="fas fa-plus me-1"></i> New Purchase
                    </a>
                    <a href="{{ route('cashier.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Dashboard
                    </a>
                </div>
            </div>

            @if($totalStillNeeded > 0)
            <div class="proc-banner">
                <div style="font-size:22px;color:var(--amber);"><i class="fas fa-exclamation-triangle"></i></div>
                <div style="flex:1;font-size:13px;color:var(--text);">
                    <strong>{{ number_format($totalStillNeeded, 0) }} units</strong> across
                    <strong>{{ $fullyBlocked }}</strong> product(s) have zero stock and cannot be fulfilled.
                    @if($partiallyFulfillable > 0)
                        An additional <strong>{{ $partiallyFulfillable }}</strong> product(s) are partially fulfillable.
                    @endif
                </div>
                <a href="{{ route('cashier.purchases.create') }}" class="btn btn-warning btn-sm fw-bold">
                    <i class="fas fa-shopping-cart me-1"></i> Create Purchase
                </a>
            </div>
            @endif

            {{-- Stock alert strip --}}
            <div class="d-flex gap-2 flex-wrap mb-3">
                <a href="{{ route('cashier.stock-management.index', ['stock_levels[]' => 'out_of_stock']) }}" class="proc-sa red">
                    <i class="fas fa-times-circle"></i><span class="proc-sa-count">{{ $stockStats['out_of_stock'] ?? 0 }}</span><span>Out of Stock</span>
                </a>
                <a href="{{ route('cashier.stock-management.index', ['stock_levels[]' => 'critical_stock']) }}" class="proc-sa red">
                    <i class="fas fa-exclamation-circle"></i><span class="proc-sa-count">{{ $stockStats['critical_stock'] ?? 0 }}</span><span>Critical</span>
                </a>
                <a href="{{ route('cashier.stock-management.index', ['stock_levels[]' => 'low_stock']) }}" class="proc-sa amber">
                    <i class="fas fa-exclamation-triangle"></i><span class="proc-sa-count">{{ $stockStats['low_stock'] ?? 0 }}</span><span>Low Stock</span>
                </a>
                <a href="{{ route('cashier.stock-management.index', ['stock_levels[]' => 'in_stock']) }}" class="proc-sa green">
                    <i class="fas fa-check-circle"></i><span class="proc-sa-count">{{ $stockStats['in_stock'] ?? 0 }}</span><span>In Stock</span>
                </a>
            </div>

            {{-- KPIs --}}
            <div class="proc-kpis">
                <div class="proc-kpi r"><div class="proc-kpi-label">Fully Blocked</div><div class="proc-kpi-value" style="color:var(--red);">{{ $fullyBlocked }}</div><div class="proc-kpi-sub">zero stock</div></div>
                <div class="proc-kpi a"><div class="proc-kpi-label">Partial</div><div class="proc-kpi-value" style="color:var(--amber);">{{ $partiallyFulfillable }}</div><div class="proc-kpi-sub">some stock, still short</div></div>
                <div class="proc-kpi b"><div class="proc-kpi-label">Product Lines</div><div class="proc-kpi-value">{{ $totalPendingProducts }}</div><div class="proc-kpi-sub">needing procurement</div></div>
                <div class="proc-kpi a"><div class="proc-kpi-label">Pending Units</div><div class="proc-kpi-value">{{ number_format($totalPendingUnits, 0) }}</div><div class="proc-kpi-sub">ordered, not fulfilled</div></div>
                <div class="proc-kpi r"><div class="proc-kpi-label">Still Needed</div><div class="proc-kpi-value" style="color:var(--red);">{{ number_format($totalStillNeeded, 0) }}</div><div class="proc-kpi-sub">units to purchase</div></div>
                <div class="proc-kpi b"><div class="proc-kpi-label">Affected Orders</div><div class="proc-kpi-value">{{ number_format($totalOrders, 0) }}</div><div class="proc-kpi-sub">sales waiting</div></div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-truck-loading"></i> Items Needing Procurement</div>
                    <span class="sp-c-badge">{{ $pendingItems->count() }} items</span>
                </div>
                <div class="sp-table-wrap">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th>Product</th><th>Supplier</th>
                                <th class="text-center">Orders</th>
                                <th class="text-end">Ordered</th><th class="text-end">In Stock</th>
                                <th class="text-end">Still Needed</th><th>Fulfillment</th>
                                <th>Oldest Order</th><th>Status</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingItems as $row)
                            @php
                                $pct = $row->total_pending_qty > 0 ? min(100, round($row->can_fulfill / $row->total_pending_qty * 100)) : 0;
                                $barColor = $pct === 0 ? 'var(--red)' : ($pct < 100 ? 'var(--amber)' : 'var(--green)');
                                $statusLabel = $pct === 0 ? 'No Stock' : ($pct < 100 ? 'Partial' : 'Fulfillable');
                                $statusClass = $pct === 0 ? 'bp-red' : ($pct < 100 ? 'bp-amber' : 'bp-green');
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:700;">{{ $row->product_name }}</div>
                                    <div style="font-size:11px;color:var(--muted);">{{ $row->barcode ?? '' }} · {{ $row->category_name }}</div>
                                </td>
                                <td style="font-size:12.5px;">{{ $row->supplier_name }}</td>
                                <td class="text-center"><span class="bp bp-blue">{{ $row->order_count }}</span></td>
                                <td class="text-end" style="font-weight:700;">{{ number_format($row->total_pending_qty, 0) }}</td>
                                <td class="text-end" style="font-weight:700;color:{{ $row->current_stock > 0 ? 'var(--green)' : 'var(--red)' }};">{{ number_format($row->current_stock, 0) }}</td>
                                <td class="text-end" style="font-weight:800;color:var(--red);">{{ $row->still_needed > 0 ? number_format($row->still_needed, 0) : '—' }}</td>
                                <td style="min-width:90px;">
                                    <div class="proc-bar-wrap"><div class="proc-bar" style="width:{{ $pct }}%;background:{{ $barColor }};"></div></div>
                                    <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $pct }}%</div>
                                </td>
                                <td style="font-size:11px;color:var(--muted);white-space:nowrap;">
                                    {{ $row->oldest_order_date ? \Carbon\Carbon::parse($row->oldest_order_date)->format('M d, Y') : '—' }}
                                    @php $age = $row->oldest_order_date ? \Carbon\Carbon::parse($row->oldest_order_date)->diffInDays(now()) : 0; @endphp
                                    @if($age > 7)<span class="bp bp-red ms-1">{{ $age }}d</span>
                                    @elseif($age > 2)<span class="bp bp-amber ms-1">{{ $age }}d</span>@endif
                                </td>
                                <td><span class="bp {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>
                                    <a href="{{ route('cashier.purchases.create') }}" class="btn btn-warning btn-sm fw-bold">
                                        <i class="fas fa-shopping-cart me-1"></i>Purchase
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center" style="padding:48px;color:var(--muted);">
                                    <i class="fas fa-check-circle" style="font-size:36px;color:var(--green);opacity:.4;display:block;margin-bottom:10px;"></i>
                                    <div style="font-weight:700;">All orders are fulfilled</div>
                                    <div style="font-size:12px;margin-top:4px;">No items currently need procurement.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection
