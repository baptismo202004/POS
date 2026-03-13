@extends('layouts.app')
@section('title', 'Stock In')

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
            --green:   #10b981;
            --amber:   #f59e0b;
            --shadow:  0 4px 28px rgba(13,71,161,0.09);
            --theme-color: var(--navy);
        }

        .stockin-theme {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .stockin-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .stockin-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .stockin-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .stockin-theme .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .stockin-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .stockin-theme .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .stockin-theme .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .stockin-theme .ph-left { display: flex; align-items: center; gap: 13px; }
        .stockin-theme .ph-icon {
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
        .stockin-theme .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .stockin-theme .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .stockin-theme .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            border: none;
            transition: all .2s ease;
            white-space: nowrap;
            text-decoration: none;
        }
        .stockin-theme .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .stockin-theme .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }

        .stockin-theme .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .stockin-theme .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .stockin-theme .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .stockin-theme .c-head::after {
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
        .stockin-theme .c-head-title {
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
        .stockin-theme .c-head-title i { color:rgba(0,229,255,.85); }
        .stockin-theme .c-badge {
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

        .stockin-theme .table-responsive { overflow-x: hidden; }
        .stockin-theme table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .stockin-theme table.table thead th {
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
        .stockin-theme table.table tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
        }
        .stockin-theme table.table tbody tr:nth-child(odd) { background: #fff; }
        .stockin-theme table.table tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .stockin-theme table.table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }
        .stockin-theme table.table td {
            padding: 13px 18px;
            font-size: 13.5px;
            vertical-align: middle;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }

        .stockin-theme .empty-row {
            padding: 52px 24px;
            text-align: center;
            color: var(--muted);
        }

        .stockin-theme .pagination-wrap {
            padding: 14px 22px;
            background: rgba(13,71,161,0.03);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stockin-theme .pagination { margin: 0; gap: 6px; }
        .stockin-theme .page-link {
            border-radius: 10px !important;
            border: 1.5px solid var(--border) !important;
            color: var(--navy) !important;
            background: #fff !important;
            font-weight: 700;
            font-family: 'Nunito', sans-serif;
            min-width: 36px;
            text-align: center;
            box-shadow: none !important;
        }
        .stockin-theme .page-item.active .page-link {
            background: var(--navy) !important;
            border-color: var(--navy) !important;
            color: #fff !important;
        }
        .stockin-theme .page-item.disabled .page-link { opacity: .55; }
    </style>
@endpush

@section('content')
<div class="stockin-theme stockin-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-boxes-stacked"></i></div>
                <div>
                    <div class="ph-title">Stock In</div>
                    <div class="ph-sub">Add and review stock-in records</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.stockin.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Stock In
                </a>
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Stock In List</div>
                <span class="c-badge">{{ $stockIns->total() }} record{{ $stockIns->total() == 1 ? '' : 's' }}</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Purchase Ref</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockIns as $stock)
                            <tr>
                                <td>{{ $stock->product->product_name ?? 'N/A' }}</td>
                                <td>{{ $stock->purchase->reference_number ?? 'N/A' }}</td>
                                <td>{{ $stock->quantity }}</td>
                                <td>{{ number_format($stock->price, 2) }}</td>
                                <td>{{ optional($stock->created_at)->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">No stock records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $stockIns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Use standard CashierSidebar from layouts
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Stock-in Error',
            html: '{!! session('error') !!}',
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif
</script>
@endpush
