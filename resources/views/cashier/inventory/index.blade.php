@extends('layouts.app')
@section('title', 'Inventory')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{--navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--green:#10b981;--red:#ef4444;--amber:#f59e0b;--bg:#EBF3FB;--card:#fff;--border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;}
.bi-wrap{padding:24px 22px 60px;}
.bi-header{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px;}
.bi-title{font-size:26px;font-weight:900;color:var(--navy);margin:0;line-height:1.1;}
.bi-sub{font-size:12px;color:var(--muted);margin-top:3px;}
.bi-crumb{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--blue);opacity:.7;margin-bottom:4px;}
.bi-alerts{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;}
.bi-alert{display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;font-size:12.5px;font-weight:600;}
.bi-alert-warning{background:rgba(245,158,11,0.1);color:#b45309;border:1px solid rgba(245,158,11,0.2);}
.bi-alert-info{background:rgba(25,118,210,0.1);color:#1565c0;border:1px solid rgba(25,118,210,0.2);}
.bi-kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-bottom:22px;}
.bi-kpi{background:var(--card);border-radius:14px;border:1px solid var(--border);padding:14px 16px;box-shadow:0 2px 10px rgba(13,71,161,0.06);position:relative;overflow:hidden;}
.bi-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
.bi-kpi.green::before{background:var(--green);}
.bi-kpi.red::before{background:var(--red);}
.bi-kpi.blue::before{background:var(--blue);}
.bi-kpi.amber::before{background:var(--amber);}
.bi-kpi.navy::before{background:var(--navy);}
.bi-kpi-label{font-size:10.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:5px;}
.bi-kpi-value{font-size:19px;font-weight:900;color:var(--navy);line-height:1;}
.bi-kpi-sub{font-size:11px;color:var(--muted);margin-top:4px;}
.bi-kpi-icon{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:26px;opacity:.08;}
.bi-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
@media(max-width:900px){.bi-row{grid-template-columns:1fr;}}
.bi-card{background:var(--card);border-radius:16px;border:1px solid var(--border);box-shadow:0 2px 14px rgba(13,71,161,0.07);overflow:hidden;}
.bi-card-head{padding:12px 18px;background:linear-gradient(135deg,var(--navy),var(--blue));display:flex;align-items:center;justify-content:space-between;}
.bi-card-title{font-size:13px;font-weight:800;color:#fff;display:flex;align-items:center;gap:7px;}
.bi-card-title i{color:rgba(0,229,255,.85);}
.bi-card-badge{background:rgba(255,255,255,0.18);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;}
.bi-mini-tbl{width:100%;border-collapse:collapse;font-size:13px;}
.bi-mini-tbl th{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);padding:6px 10px;border-bottom:1px solid var(--border);}
.bi-mini-tbl td{padding:9px 10px;border-bottom:1px solid rgba(25,118,210,0.05);color:var(--text);vertical-align:middle;}
.bi-mini-tbl tr:last-child td{border-bottom:none;}
.bi-mini-tbl tr:hover td{background:rgba(21,101,192,0.04);}
.bi-bar-wrap{background:rgba(13,71,161,0.07);border-radius:6px;height:6px;overflow:hidden;margin-top:4px;}
.bi-bar{height:100%;border-radius:6px;background:linear-gradient(90deg,var(--blue),var(--blue-lt));}
.bi-row-3{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px;}
@media(max-width:900px){.bi-row-3{grid-template-columns:1fr;}}
.bi-chart-wrap{padding:16px 18px 10px;}
.bi-card-body{padding:16px 18px;}
.bi-feed{list-style:none;margin:0;padding:0;}
.bi-feed li{display:flex;align-items:flex-start;gap:10px;padding:9px 0;border-bottom:1px solid rgba(25,118,210,0.06);}
.bi-feed li:last-child{border-bottom:none;}
.bi-feed-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;color:#fff;flex-shrink:0;margin-top:1px;}
.bi-feed-text{font-size:12.5px;color:var(--text);font-weight:500;line-height:1.3;}
.bi-feed-time{font-size:11px;color:var(--muted);margin-top:2px;}
.bi-tabs{display:flex;gap:3px;flex-wrap:wrap;border-bottom:2px solid var(--border);margin-bottom:0;}
.bi-tab{padding:8px 16px;font-size:12.5px;font-weight:700;border-radius:9px 9px 0 0;border:1px solid transparent;border-bottom:none;color:var(--muted);background:transparent;text-decoration:none;transition:all .15s;}
.bi-tab:hover{background:rgba(13,71,161,0.05);color:var(--navy);}
.bi-tab.active{background:var(--card);border-color:var(--border);color:var(--navy);margin-bottom:-2px;border-bottom:2px solid var(--card);}
.bi-panel{background:var(--card);border-radius:0 16px 16px 16px;border:1px solid var(--border);box-shadow:0 2px 14px rgba(13,71,161,0.07);overflow:hidden;}
.bi-panel-head{padding:12px 18px;background:linear-gradient(135deg,var(--navy),var(--blue));display:flex;align-items:center;justify-content:space-between;}
.bi-search-bar{padding:10px 18px;border-bottom:1px solid var(--border);background:rgba(13,71,161,0.02);}
.bi-search-bar form{display:flex;align-items:center;gap:8px;}
.bi-search-bar input{padding:7px 12px 7px 32px;border-radius:9px;border:1.5px solid var(--border);font-size:13px;background:var(--card);color:var(--text);outline:none;width:240px;}
.bi-search-bar input:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.1);}
.bi-search-wrap{position:relative;display:inline-block;}
.bi-search-wrap i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px;}
.bi-tbl-wrap{overflow-x:auto;}
.bi-tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;}
.bi-tbl thead th{background:rgba(13,71,161,0.03);padding:9px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
.bi-tbl thead th a{color:var(--navy);text-decoration:none;}
.bi-tbl thead th a:hover{text-decoration:underline;}
.bi-tbl tbody td{padding:11px 14px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.05);vertical-align:middle;}
.bi-tbl tbody tr:nth-child(even) td{background:rgba(240,246,255,0.45);}
.bi-tbl tbody tr:hover td{background:rgba(21,101,192,0.05);}
.bi-tbl tbody tr:last-child td{border-bottom:none;}
.bp{display:inline-block;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;}
.bp-green{background:rgba(16,185,129,0.12);color:#059669;}
.bp-red{background:rgba(239,68,68,0.12);color:#dc2626;}
.money-g{font-weight:700;color:var(--green);}
.money-r{font-weight:700;color:var(--red);}
.bi-pager{padding:11px 18px;background:rgba(13,71,161,0.02);border-top:1px solid var(--border);display:flex;justify-content:center;}
.bi-pager .pagination{margin:0;}
.bi-pager .page-link{border-radius:7px!important;margin:0 2px;border:1.5px solid var(--border);color:var(--navy);font-weight:700;font-size:12px;}
.bi-pager .page-item.active .page-link{background:linear-gradient(135deg,var(--navy),var(--blue));border-color:var(--navy);color:#fff;}
</style>
@endpush

@section('content')
<div class="bi-wrap">

<div class="bi-header">
    <div>
        <div class="bi-crumb">Cashier · Inventory</div>
        <h1 class="bi-title"><i class="fas fa-warehouse me-2" style="color:var(--blue);"></i>Inventory Overview</h1>
        <div class="bi-sub">Branch stock levels · stock-ins · movements</div>
    </div>
    <div style="font-size:12px;color:var(--muted);text-align:right;padding-top:6px;">
        <i class="fas fa-clock me-1"></i>{{ now()->format('M d, Y H:i') }}
    </div>
</div>

@if($alerts->isNotEmpty())
<div class="bi-alerts">
    @foreach($alerts as $a)
    <div class="bi-alert bi-alert-{{ $a['type'] }}">
        <i class="fas {{ $a['icon'] }}"></i> {{ $a['text'] }}
    </div>
    @endforeach
</div>
@endif

<div class="bi-kpi-grid">
    <div class="bi-kpi green">
        <div class="bi-kpi-label">Today's Sales</div>
        <div class="bi-kpi-value money-g">₱{{ number_format($todaySales, 2) }}</div>
        <div class="bi-kpi-sub">completed sales today</div>
        <i class="fas fa-cash-register bi-kpi-icon"></i>
    </div>
    <div class="bi-kpi blue">
        <div class="bi-kpi-label">Total Products</div>
        <div class="bi-kpi-value">{{ $sortedProducts->count() }}</div>
        <div class="bi-kpi-sub">in this branch</div>
        <i class="fas fa-boxes bi-kpi-icon"></i>
    </div>
    <div class="bi-kpi navy">
        <div class="bi-kpi-label">Total Stock Units</div>
        <div class="bi-kpi-value">{{ number_format($totalStockValue, 0) }}</div>
        <div class="bi-kpi-sub">base units on hand</div>
        <i class="fas fa-layer-group bi-kpi-icon"></i>
    </div>
    <div class="bi-kpi red">
        <div class="bi-kpi-label">Low Stock</div>
        <div class="bi-kpi-value money-r">{{ $lowStockCount }}</div>
        <div class="bi-kpi-sub">products below 10 units</div>
        <i class="fas fa-exclamation-triangle bi-kpi-icon"></i>
    </div>
    <div class="bi-kpi amber">
        <div class="bi-kpi-label">Stock Ins</div>
        <div class="bi-kpi-value">{{ $totalStockIns }}</div>
        <div class="bi-kpi-sub">total records</div>
        <i class="fas fa-arrow-down bi-kpi-icon"></i>
    </div>
    <div class="bi-kpi blue">
        <div class="bi-kpi-label">Movements</div>
        <div class="bi-kpi-value">{{ $totalMovements }}</div>
        <div class="bi-kpi-sub">total records</div>
        <i class="fas fa-exchange-alt bi-kpi-icon"></i>
    </div>
</div>

<div class="bi-row-3" style="margin-bottom:16px;">
        <div class="bi-card">
            <div class="bi-card-head">
                <div class="bi-card-title"><i class="fas fa-chart-area"></i> 30-Day Financial Trend</div>
                <div style="display:flex;gap:12px;font-size:11px;color:rgba(255,255,255,0.75);">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#10b981;margin-right:4px;"></span>Sales</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;margin-right:4px;"></span>Purchases</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#42A5F5;margin-right:4px;"></span>Stock Ins</span>
                </div>
            </div>
            <div class="bi-chart-wrap">
                <canvas id="trendChart" height="130"></canvas>
            </div>
        </div>
        <div class="bi-card">
            <div class="bi-card-head">
                <div class="bi-card-title"><i class="fas fa-bolt"></i> Live Activity Feed</div>
                <span class="bi-card-badge">Last 20</span>
            </div>
            <div class="bi-card-body" style="max-height:280px;overflow-y:auto;padding:10px 14px;">
                <ul class="bi-feed">
                    @foreach($feed as $f)
                    <li>
                        <div class="bi-feed-dot" style="background:{{ $f['color'] }}">
                            <i class="fas {{ $f['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="bi-feed-text">{{ $f['text'] }}</div>
                            <div class="bi-feed-time">{{ $f['time']?->diffForHumans() }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="bi-row" style="margin-bottom:20px;">
    <div class="bi-card">
        <div class="bi-card-head">
            <div class="bi-card-title"><i class="fas fa-trophy"></i> Top 5 Products by Units Sold</div>
        </div>
        <div style="padding:0 4px;">
            @php $maxSold = $topProducts->max('total_sold') ?: 1; @endphp
            <table class="bi-mini-tbl">
                <thead><tr><th>#</th><th>Product</th><th>Category</th><th class="text-end">Sold</th><th class="text-end">Stock</th></tr></thead>
                <tbody>
                    @forelse($topProducts as $i => $p)
                    <tr>
                        <td style="color:var(--muted);font-size:12px;font-weight:700;">{{ $i + 1 }}</td>
                        <td>
                            <div style="font-weight:600;font-size:13px;">{{ $p->product_name }}</div>
                            <div class="bi-bar-wrap" style="width:100px;">
                                <div class="bi-bar" style="width:{{ min(100, round($p->total_sold / $maxSold * 100)) }}%;background:linear-gradient(90deg,var(--green),#34d399);"></div>
                            </div>
                        </td>
                        <td style="color:var(--muted);font-size:12px;">{{ $p->category }}</td>
                        <td class="text-end money-g">{{ number_format($p->total_sold, 0) }}</td>
                        <td class="text-end {{ $p->current_stock < 10 ? 'money-r' : '' }}">{{ number_format($p->current_stock, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center" style="padding:20px;color:var(--muted);">No data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bi-card">
        <div class="bi-card-head">
            <div class="bi-card-title"><i class="fas fa-exclamation-triangle"></i> Low Stock Products</div>
            <span class="bi-card-badge">{{ $lowStockCount }}</span>
        </div>
        <div style="padding:0 4px;max-height:260px;overflow-y:auto;">
            <table class="bi-mini-tbl">
                <thead><tr><th>Product</th><th class="text-end">Stock</th></tr></thead>
                <tbody>
                    @forelse($sortedProducts->filter(fn($p) => $p->current_stock < 10)->take(20) as $p)
                    <tr>
                        <td style="font-weight:600;">{{ $p->product_name }}</td>
                        <td class="text-end money-r">{{ number_format($p->current_stock, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center" style="padding:20px;color:var(--muted);">All products are well-stocked.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="bi-tabs">
    @foreach([
        'stock'     => ['fa-boxes',        'Stock',      $sortedProducts->count()],
        'stockins'  => ['fa-arrow-down',   'Stock Ins',  $stockIns->total()],
        'movements' => ['fa-exchange-alt', 'Movements',  $movements->total()],
    ] as $key => [$icon, $label, $count])
    <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
       class="bi-tab {{ $tab === $key ? 'active' : '' }}">
        <i class="fas {{ $icon }} me-1"></i>{{ $label }}
        <span style="font-size:10px;opacity:.65;margin-left:3px;">({{ $count }})</span>
    </a>
    @endforeach
</div>

<div class="bi-panel">
    <div class="bi-panel-head">
        <div class="bi-card-title">
            @php
                $meta = ['stock' => ['fa-boxes','Stock'], 'stockins' => ['fa-arrow-down','Stock Ins'], 'movements' => ['fa-exchange-alt','Stock Movements']][$tab] ?? ['fa-list','Records'];
            @endphp
            <i class="fas {{ $meta[0] }}"></i> {{ $meta[1] }}
        </div>
        <span class="bi-card-badge">
            {{ $tab === 'stock' ? $sortedProducts->count() : ($tab === 'stockins' ? $stockIns->total() : $movements->total()) }} records
        </span>
    </div>

    <div class="bi-search-bar">
        <form method="GET" action="{{ route('cashier.inventory.index') }}">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="bi-search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search products...">
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
            @if($search)
                <a href="{{ route('cashier.inventory.index', ['tab' => $tab]) }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>

    <div class="bi-tbl-wrap">
    @if($tab === 'stock')
    <table class="bi-tbl">
        <thead>
            <tr>
                <th><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'product_name', 'sort_direction' => $sortDirection === 'asc' ? 'desc' : 'asc', 'tab' => 'stock']) }}">Product</a></th>
                <th>Brand</th>
                <th>Category</th>
                <th class="text-end"><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'current_stock', 'sort_direction' => $sortDirection === 'asc' ? 'desc' : 'asc', 'tab' => 'stock']) }}">Current Stock</a></th>
                <th class="text-end"><a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_sold', 'sort_direction' => $sortDirection === 'asc' ? 'desc' : 'asc', 'tab' => 'stock']) }}">Total Sold</a></th>
            </tr>
        </thead>
        <tbody>
            @forelse($sortedProducts as $p)
            <tr>
                <td style="font-weight:600;">{{ $p->product_name }}</td>
                <td style="color:var(--muted);">{{ $p->brand }}</td>
                <td style="color:var(--muted);">{{ $p->category }}</td>
                <td class="text-end {{ $p->current_stock < 10 ? 'money-r' : '' }}">{{ number_format($p->current_stock, 0) }}</td>
                <td class="text-end">{{ number_format($p->total_sold, 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center" style="padding:28px;color:var(--muted);">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @elseif($tab === 'stockins')
    <table class="bi-tbl">
        <thead><tr><th>#</th><th>Product</th><th class="text-end">Qty In</th><th class="text-end">Sold</th><th class="text-end">Remaining</th><th>Reason</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($stockIns as $si)
            <tr>
                <td style="color:var(--muted);font-size:11px;">{{ $si->id }}</td>
                <td style="font-weight:600;">{{ $si->product->product_name ?? '—' }}</td>
                <td class="text-end">{{ number_format($si->quantity, 0) }}</td>
                <td class="text-end" style="color:var(--muted);">{{ number_format($si->sold, 0) }}</td>
                <td class="text-end money-g">{{ number_format($si->quantity - $si->sold, 0) }}</td>
                <td style="color:var(--muted);font-size:11px;">{{ $si->reason ?? '—' }}</td>
                <td style="color:var(--muted);font-size:11px;white-space:nowrap;">{{ $si->created_at?->format('M d, Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center" style="padding:28px;color:var(--muted);">No stock-in records.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="bi-pager">{{ $stockIns->withQueryString()->links() }}</div>

    @elseif($tab === 'movements')
    <table class="bi-tbl">
        <thead><tr><th>#</th><th>Product</th><th>Type</th><th>Source</th><th class="text-end">Qty (base)</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($movements as $m)
            <tr>
                <td style="color:var(--muted);font-size:11px;">{{ $m->id }}</td>
                <td style="font-weight:600;">{{ $m->product->product_name ?? '—' }}</td>
                <td><span class="bp {{ str_contains($m->movement_type ?? '', 'in') ? 'bp-green' : 'bp-red' }}">{{ $m->movement_type ?? '—' }}</span></td>
                <td style="color:var(--muted);font-size:11px;">{{ $m->source_type ?? '—' }} #{{ $m->source_id ?? '' }}</td>
                <td class="text-end {{ str_contains($m->movement_type ?? '', 'in') ? 'money-g' : 'money-r' }}">
                    {{ str_contains($m->movement_type ?? '', 'in') ? '+' : '-' }}{{ number_format(abs($m->quantity_base), 2) }}
                </td>
                <td style="color:var(--muted);font-size:11px;white-space:nowrap;">{{ $m->created_at?->format('M d, Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center" style="padding:28px;color:var(--muted);">No movements found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="bi-pager">{{ $movements->withQueryString()->links() }}</div>
    @endif
    </div>
</div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels    = @json($chartLabels);
    const sales     = @json($chartSales);
    const purchases = @json($chartPurchases);
    const stockins  = @json($chartStockIns);

    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label:'Sales',     data:sales,     borderColor:'#10b981', backgroundColor:'rgba(16,185,129,0.08)', borderWidth:2, pointRadius:2, fill:true, tension:0.4 },
                { label:'Purchases', data:purchases, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,0.06)',  borderWidth:2, pointRadius:2, fill:true, tension:0.4 },
                { label:'Stock Ins', data:stockins,  borderColor:'#42A5F5', backgroundColor:'rgba(66,165,245,0.06)', borderWidth:2, pointRadius:2, fill:true, tension:0.4 },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode:'index', intersect:false },
            plugins: {
                legend: { display:false },
                tooltip: { callbacks: { label: ctx => ' ₱' + Number(ctx.parsed.y).toLocaleString('en-PH', { minimumFractionDigits:2 }) } },
            },
            scales: {
                x: { grid:{ display:false }, ticks:{ font:{ size:10 }, maxTicksLimit:10 } },
                y: { grid:{ color:'rgba(13,71,161,0.06)' }, ticks:{ font:{ size:10 }, callback: v => '₱'+Number(v).toLocaleString() } },
            },
        },
    });
})();
</script>
@endpush
