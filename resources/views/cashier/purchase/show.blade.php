@extends('layouts.app')
@section('title', 'Purchase Details')

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

        .purchase-details-page {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .purchase-details-page .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .purchase-details-page .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .purchase-details-page .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .purchase-details-page .bb1 { width:420px; height:420px; background:#1976D2; top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .purchase-details-page .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .purchase-details-page .wrap {
            position: relative;
            z-index: 1;
            max-width: 1380px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .purchase-details-page .page-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .purchase-details-page .ph-left { display: flex; align-items: center; gap: 13px; }
        .purchase-details-page .ph-icon {
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
        .purchase-details-page .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .purchase-details-page .ph-sub { font-size:12px; color:var(--muted); margin-top:2px; }

        .purchase-details-page .action-bar {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .purchase-details-page .btn {
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
        .purchase-details-page .btn-primary {
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            box-shadow: 0 4px 14px rgba(13,71,161,0.26);
        }
        .purchase-details-page .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(13,71,161,0.34); }

        .purchase-details-page .btn-success {
            background: linear-gradient(135deg, var(--green), #34d399);
            color: #fff;
            box-shadow: 0 4px 14px rgba(16,185,129,0.26);
        }
        .purchase-details-page .btn-success:hover { transform: translateY(-2px); box-shadow: 0 7px 20px rgba(16,185,129,0.34); }

        .purchase-details-page .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .purchase-details-page .c-head {
            padding: 15px 22px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .purchase-details-page .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .purchase-details-page .c-head::after {
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
        .purchase-details-page .c-head-title {
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
        .purchase-details-page .c-head-title i { color:rgba(0,229,255,.85); }
        .purchase-details-page .c-badge {
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

        .purchase-details-page .section { padding: 18px 22px 8px; }

        .purchase-details-page .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }
        @media (max-width: 980px) {
            .purchase-details-page .meta-grid { grid-template-columns: 1fr; }
        }

        .purchase-details-page .meta-item {
            background: rgba(240,246,255,0.55);
            border: 1px solid rgba(13,71,161,0.06);
            border-radius: 14px;
            padding: 12px 14px;
        }
        .purchase-details-page .meta-label {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 800;
            font-family:'Nunito',sans-serif;
            margin-bottom: 6px;
        }
        .purchase-details-page .meta-value {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--navy);
            font-family:'Nunito',sans-serif;
        }

        .purchase-details-page .badge.bg-success,
        .purchase-details-page .badge.bg-warning {
            display:inline-flex;
            align-items:center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            border: 1px solid transparent;
        }
        .purchase-details-page .badge.bg-success { background:rgba(16,185,129,0.11) !important; color:#047857 !important; border-color:rgba(16,185,129,0.22) !important; }
        .purchase-details-page .badge.bg-warning { background:rgba(245,158,11,0.14) !important; color:#92400e !important; border-color:rgba(245,158,11,0.24) !important; }
        .purchase-details-page .badge.bg-warning.text-dark { color:#92400e !important; }

        .purchase-details-page .table-title {
            padding: 10px 22px 0;
            font-family:'Nunito',sans-serif;
            font-size: 14px;
            font-weight: 900;
            color: var(--navy);
            display:flex;
            align-items:center;
            gap:8px;
        }
        .purchase-details-page .table-title i { color: rgba(25,118,210,0.9); }

        .purchase-details-page .table-responsive { overflow-x: visible; }
        .purchase-details-page table.table { width: 100%; border-collapse: collapse; margin: 0; }
        .purchase-details-page table.table thead th {
            padding: 12px 18px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing:.12em;
            text-transform:uppercase;
            color: rgba(255,255,255,0.92);
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.12);
            white-space: normal;
        }
        .purchase-details-page table.table tbody td { white-space: normal; word-break: break-word; }
        .purchase-details-page table.table tbody tr {
            border-bottom: 1px solid rgba(13,71,161,0.05);
            transition: background .15s, transform .15s;
        }
        .purchase-details-page table.table tbody tr:nth-child(odd) { background: #fff; }
        .purchase-details-page table.table tbody tr:nth-child(even) { background: rgba(240,246,255,0.55); }
        .purchase-details-page table.table tbody tr:hover { background: rgba(21,101,192,0.05) !important; transform: translateX(2px); }
        .purchase-details-page table.table td {
            padding: 13px 18px;
            font-size: 13.5px;
            vertical-align: middle;
            color: var(--text);
        }

        .purchase-details-page .empty-row {
            padding: 52px 24px;
            text-align: center;
            color: var(--muted);
        }
    </style>
@endpush

@section('content')
<div class="purchase-details-page">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="ph-title">Purchase Details</div>
                    <div class="ph-sub">Review purchase information and items</div>
                </div>
            </div>

            <div class="action-bar">
                <a href="{{ route('cashier.purchases.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    Back to Purchases
                </a>

                @if(($purchase->payment_status ?? '') === 'pending')
                    <form method="POST" action="{{ route('cashier.purchases.mark-paid', ['purchase' => $purchase->id]) }}" class="d-inline" data-confirm-mark-paid>
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i>
                            Mark as Paid
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-circle-info"></i> Purchase Summary</div>
                <span class="c-badge">{{ $purchase->reference_number ?: 'N/A' }}</span>
            </div>

            <div class="section">
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-label">Reference Number</div>
                        <div class="meta-value">{{ $purchase->reference_number ?: 'N/A' }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Purchase Date</div>
                        <div class="meta-value">{{ optional($purchase->purchase_date)->format('M d, Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Payment Status</div>
                        <div>
                            <span class="badge {{ $purchase->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-title">
                <i class="fas fa-table"></i>
                Purchased Items
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Type</th>
                            <th>Unit Cost</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->items as $item)
                            <tr>
                                <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $qtyRaw = $item->quantity ?? 0;
                                        $qty = is_numeric($qtyRaw) ? (float) $qtyRaw : 0;
                                        $qtyDisplay = (floor($qty) == $qty)
                                            ? (string) (int) $qty
                                            : rtrim(rtrim(number_format($qty, 6, '.', ''), '0'), '.');
                                    @endphp
                                    {{ $qtyDisplay }}
                                </td>
                                <td>{{ $item->unitType->unit_name ?? 'N/A' }}</td>
                                <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                                <td><strong>₱{{ number_format($item->subtotal, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-row">No items found for this purchase.</td>
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
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form[data-confirm-mark-paid]');
        if (!form || typeof Swal === 'undefined') return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Mark as Paid?'
                , text: 'This will set the payment status to Paid.'
                , icon: 'question'
                , showCancelButton: true
                , confirmButtonText: 'Yes, mark as paid'
                , cancelButtonText: 'Cancel'
                , confirmButtonColor: '#198754'
                , cancelButtonColor: '#6c757d'
                , reverseButtons: true
                , background: '#ffffff'
                , customClass: {
                    popup: 'shadow-lg rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush


