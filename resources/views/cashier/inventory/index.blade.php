@extends('layouts.app')
@section('title', 'Inventory')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:    #0D47A1;
            --blue:    #1976D2;
            --blue-lt: #42A5F5;
            --bg:      #f0f6ff;
            --card:    #ffffff;
            --border:  rgba(25,118,210,0.13);
            --text:    #1a2744;
            --muted:   #6b84aa;
            --red:     #ef4444;
            --shadow:  0 4px 28px rgba(13,71,161,0.09);
        }

        .inventory-theme {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .inventory-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .inventory-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .inventory-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .inventory-theme .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .inventory-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .inventory-theme .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .inventory-theme .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .inventory-theme .ph-left { display: flex; align-items: center; gap: 13px; }
        .inventory-theme .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
            flex-shrink: 0;
        }
        .inventory-theme .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .inventory-theme .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .inventory-theme .action-bar {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .inventory-theme .search-wrap { position: relative; }
        .inventory-theme .search-wrap .fas.fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
            pointer-events: none;
            z-index: 2;
        }
        .inventory-theme #searchInput {
            padding: 9px 14px 9px 36px !important;
            border-radius: 11px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--card);
            color: var(--text);
            outline: none;
            width: 320px;
            max-width: 100%;
            transition: border-color .18s, box-shadow .18s;
        }
        .inventory-theme #searchInput:focus { border-color: var(--blue-lt); box-shadow: 0 0 0 3px rgba(66,165,245,0.12); }
        .inventory-theme #searchInput::placeholder { color: #b0c0d8; }

        .inventory-theme .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .inventory-theme .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .inventory-theme .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .inventory-theme .c-head::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -90px;
            right: -50px;
            pointer-events: none;
        }
        .inventory-theme .c-head-title {
            font-family:'Nunito',sans-serif;
            font-size:14.5px;
            font-weight:800;
            color:#fff;
            display:flex;
            align-items:center;
            gap:8px;
            position:relative;
            z-index:1;
        }
        .inventory-theme .c-head-title i { color:rgba(0,229,255,.85); }
        .inventory-theme .c-badge {
            position:relative;
            z-index:1;
            background:rgba(255,255,255,0.15);
            border:1px solid rgba(255,255,255,0.25);
            color:#fff;
            font-size:11px;
            font-weight:700;
            padding:3px 10px;
            border-radius:20px;
            font-family:'Nunito',sans-serif;
        }

        .inventory-theme .table-responsive { overflow-x: hidden; }
        .inventory-theme table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .inventory-theme table.table thead th {
            padding: 12px 18px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: normal;
            word-break: break-word;
        }
        .inventory-theme table.table thead th a { color: rgba(255,255,255,0.92); text-decoration: none; }
        .inventory-theme table.table thead th a:hover { color: #fff; text-decoration: underline; }
        .inventory-theme table.table tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
        }
        .inventory-theme table.table tbody tr:nth-child(odd) { background: #fff; }
        .inventory-theme table.table tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .inventory-theme table.table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }
        .inventory-theme table.table td {
            padding: 13px 18px;
            font-size: 13.5px;
            vertical-align: middle;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }
        .inventory-theme .text-danger { font-weight: 900; }

        .inventory-theme .empty-row {
            padding: 52px 24px;
            text-align: center;
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
<div class="inventory-theme inventory-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-warehouse"></i></div>
                <div>
                    <div class="ph-title">Inventory</div>
                    <div class="ph-sub">Monitor current stock and sales movement</div>
                </div>
            </div>

            <div class="action-bar">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search products..." value="{{ request('search') }}">
                </div>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Inventory List</div>
                <span class="c-badge">{{ $sortedProducts->count() }} item{{ $sortedProducts->count() == 1 ? '' : 's' }}</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><a href="{{ route('cashier.inventory.index', ['sort_by' => 'product_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page'])) }}">Product</a></th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th><a href="{{ route('cashier.inventory.index', ['sort_by' => 'current_stock', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page'])) }}">Current Stock</a></th>
                            <th><a href="{{ route('cashier.inventory.index', ['sort_by' => 'total_sold', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'] + request()->except(['page'])) }}">Total Sold</a></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sortedProducts as $product)
                            <tr>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->brand ?? 'N/A' }}</td>
                                <td>{{ $product->category ?? 'N/A' }}</td>
                                <td class="{{ ($product->current_stock ?? 0) < 10 ? 'text-danger' : '' }}">{{ $product->current_stock ?? 0 }}</td>
                                <td>{{ $product->total_sold ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Use standard CashierSidebar from layouts

    const searchInput = document.getElementById('searchInput');
    let debounceTimer;
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = searchInput.value;
                const url = new URL(window.location.href);
                url.searchParams.set('search', query);
                window.location.href = url.toString();
            }, 400);
        });
    }
</script>
@endpush
