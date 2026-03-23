@extends('layouts.app')
@section('title', 'Purchases')

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

        .purchases-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .purchases-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .purchases-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .purchases-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .purchases-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .purchases-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .purchases-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .purchases-page .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .purchases-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .purchases-page .ph-icon {
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
        .purchases-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .purchases-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .purchases-page .action-bar {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .purchases-page .btn {
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

        .purchases-page .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .purchases-page .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }

        .purchases-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .purchases-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .purchases-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .purchases-page .c-head::after {
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
        .purchases-page .c-head-title {
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
        .purchases-page .c-head-title i { color:rgba(0,229,255,.85); }
        .purchases-page .c-badge {
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

        .purchases-page .table-responsive { overflow-x: hidden; }
        .purchases-page table.table { width: 100%; border-collapse: collapse; margin: 0; table-layout: fixed; }
        .purchases-page table.table thead th {
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
        .purchases-page table.table tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
        }
        .purchases-page table.table tbody tr:nth-child(odd) { background: #fff; }
        .purchases-page table.table tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .purchases-page table.table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }
        .purchases-page table.table td {
            padding: 13px 18px;
            font-size: 13.5px;
            vertical-align: middle;
            color: var(--text);
            white-space: normal;
            word-break: break-word;
        }

        .purchases-page a { color: var(--navy); text-decoration: none; font-weight: 700; }
        .purchases-page a:hover { text-decoration: underline; }

        .purchases-page .badge.bg-success,
        .purchases-page .badge.bg-warning {
            display:inline-flex;
            align-items:center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            border: 1px solid transparent;
        }
        .purchases-page .badge.bg-success { background:rgba(16,185,129,0.11) !important; color:#047857 !important; border-color:rgba(16,185,129,0.22) !important; }
        .purchases-page .badge.bg-warning { background:rgba(245,158,11,0.14) !important; color:#92400e !important; border-color:rgba(245,158,11,0.24) !important; }
        .purchases-page .badge.bg-warning.text-dark { color:#92400e !important; }

        .purchases-page .pagination-wrap {
            padding: 14px 22px;
            background: rgba(13,71,161,0.03);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .purchases-page .pagination { margin: 0; gap: 6px; }
        .purchases-page .page-link {
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
        .purchases-page .page-item.active .page-link {
            background: var(--navy) !important;
            border-color: var(--navy) !important;
            color: #fff !important;
        }
        .purchases-page .page-item.disabled .page-link { opacity: .55; }

        .purchases-page .empty-row {
            padding: 52px 24px;
            text-align: center;
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
<div class="purchases-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="ph-title">Purchases</div>
                    <div class="ph-sub">Track and review purchase records</div>
                </div>
            </div>

            <div class="action-bar">
                @if(!empty($canCreatePurchases) && $canCreatePurchases)
                    <a href="{{ route('cashier.purchases.create') }}" class="btn btn-primary" id="addPurchaseBtn">
                        <i class="fas fa-plus"></i>
                        Add New Purchase
                    </a>
                @endif
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-table"></i> Purchase List</div>
                <span class="c-badge">{{ $purchases->total() }} purchase{{ $purchases->total() == 1 ? '' : 's' }}</span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reference Number</th>
                            <th>Purchase Date</th>
                            <th>Payment Status</th>
                            <th>Items</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->reference_number ?: 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('cashier.purchases.show', $purchase->id) }}">
                                        {{ optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($purchase->payment_status) }}
                                    </span>
                                </td>
                                <td>{{ $purchase->items_count ?? 0 }} item(s)</td>
                                <td><strong>₱{{ number_format($purchase->total_cost, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">No purchases found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isViewOnlyPurchases = {{ !empty($isViewOnlyPurchases) && $isViewOnlyPurchases ? 'true' : 'false' }};
    const canCreatePurchases = {{ !empty($canCreatePurchases) && $canCreatePurchases ? 'true' : 'false' }};

    const noPermissionCreateMessage = "You don't have permission to add purchases.";

    const addPurchaseBtn = document.getElementById('addPurchaseBtn');
    if (addPurchaseBtn) {
        addPurchaseBtn.addEventListener('click', function(e) {
            if (!canCreatePurchases) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Permission',
                        text: noPermissionCreateMessage,
                        confirmButtonColor: 'var(--theme-color)',
                    });
                }
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: @json(session('success')),
            icon: 'success',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'No Permission',
            text: @json(session('error')),
            icon: 'warning',
            confirmButtonColor: 'var(--theme-color)',
        });
    @endif
});
</script>
@endpush
